<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Entity\UserInfo;
use App\Model\AuthGroup;
use App\Service\ActionLogService;
use Hyperf\Config\Annotation\Value;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Utils\Context;
use Mzh\JwtAuth\Exception\PermissionDeniedException;
use Mzh\JwtAuth\Jwt;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Redis;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @var ActionLogService
     */
    private $logService;
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Redis
     */
    protected $redis;

    /**
     * @var Jwt
     */
    public $jwt;

    /**
     * @Value("jwt.auth_prefix")
     * @var string
     */
    private $auth_prefix = 'auth:';

    /**
     * @Value("jwt.auth_log_prefix")
     * @var string
     */
    private $auth_log_prefix = 'auth_log:';

    public static $ignore = ['api/v1/admin/login', 'api/v1/admin/refresh_token', 'api/v1/admin/logout'];

    public function __construct(ContainerInterface $container)
    {
        $this->jwt = $container->get(Jwt::class);
        $this->redis = $container->get(Redis::class);
        $this->logService = $container->get(ActionLogService::class);
        $this->initRule();
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = Context::get(ResponseInterface::class);
        $response = $response->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Headers', 'DNT,Keep-Alive,User-Agent,Cache-Control,Content-Type,Authorization');
        Context::set(ResponseInterface::class, $response);
        if ($request->getMethod() == 'OPTIONS') {
            return $response;
        }
        $currUrl = strtolower(trim($request->getUri()->getPath(), '/'));
        if (in_array($currUrl, self::$ignore)) {
            $result = $handler->handle($request);
            # 记录日志
            $this->recordLog($request, $result);
            return $result;
        }

        $token = $request->getParsedBody()['token'] ?? '';
        $tokenData = $this->jwt->verifyToken($token);
        $user = $tokenData->getJwtData();
        $userInfo = new UserInfo();
        $userInfo->setUsername($user['u']);
        $userInfo->setUid($user['a']);
        $userInfo->setGroupId($user['g']);
        $userInfo->setType($user['t']);
        Context::set(UserInfo::class, $userInfo);

        if (!$this->check($userInfo->getGroupId(), $currUrl)) {
            throw new PermissionDeniedException('您无权限', 403);
        }
        $result = $handler->handle($request);
        # 记录日志  检测是否有权限写日志
        if ($this->redis->hExists($this->auth_log_prefix . $userInfo->getGroupId(), $currUrl)) {
            $this->recordLog($request, $result);
        };
        return $result;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $result
     */
    private function recordLog(ServerRequestInterface $request, ResponseInterface $result)
    {
        /** @var Dispatched $router */
        $router = $request->getAttribute(Dispatched::class);
        if ($router->isFound()) {
            $reqData = array_merge($request->getQueryParams(), $request->getParsedBody());
            $url = $router->handler->route;
            $class = $router->handler->callback[0];
            $meats = $router->handler->callback[1];
            if ($meats == 'list') {
                $resData = json_encode(['status' => 200, 'message' => '数据过大不做展示'], 256);
            } else {
                $resData = $result->getBody()->getContents();
            }
            $this->logService->recordLog(getUserInfo(), $url, $reqData, $resData, $class, $result->getStatusCode(), getClientIp());
        }
    }

    private function initRule()
    {
        $list = AuthGroup::getGroupAuthUrl();
        foreach ($list as $groupId => $item) {
            $this->redis->hMSet($this->auth_prefix . $groupId, $item['menu']);
            $this->redis->hMSet($this->auth_log_prefix . $groupId, $item['log']);
        }
    }

    private function check($groupId, $url)
    {
        return $this->redis->hExists($this->auth_prefix . $groupId, $url);
    }

}