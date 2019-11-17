<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\Constants;
use App\Exception\LoginException;
use App\Model\Entity\User;
use App\Service\Auth;
use App\Util\Jwt\Jwt;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 权限验证中间件
 * Class AuthMiddleware
 * @package App\Middleware
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;

    /**
     * @Inject()
     * @var Jwt
     */
    protected $jwt;
    /**
     * @Inject()
     * @var Auth
     */
    protected $Auth;

    public function __construct(ContainerInterface $container, HttpResponse $response, RequestInterface $request)
    {
        $this->container = $container;
        $this->response = $response;
        $this->request = $request;
    }

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
        //检查TOKEN
        $cur_node = $this->request->getUri()->getPath();
        $method = $this->request->all()['method'] ?? '';
        if ($method == 'login.admin.user' && $cur_node == '/api/v1/admin') {
            return $handler->handle($request);//交给下个一个中间件处理
        }

        foreach ($this->Auth->ignores() as $ignore) {
            if ($ignore === $cur_node) {
                return $handler->handle($request);//交给下个一个中间件处理
            }
        }
        $token = $this->request->header('Authorization') ?? '';
        try {
            //todo 检查token
            $jwt = $this->jwt->verifyToken($token);
        } catch (LoginException $exception) {
            return $this->response->json(
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'data' => []
                ]
            );
        }
        if ($jwt['scope'] != Constants::SCOPE_ROLE) {
            return $this->response->json(
                [
                    'code' => 500,
                    'message' => 'token类型错误！',
                    'data' => []
                ]
            );
        }
        $admin = $jwt['data'] ?? [];
        $model = $jwt['iss'] ?? '';
        //todo 检查用户与节点权限
        $method = $this->request->all()['method'] ?? '';
        Context::set(User::class, new User(['userId' => $admin['id'],'groupId' => $admin['group'] ]));

        if (!$this->Auth->checkAuth($model, $admin['group'], $cur_node, $method)) {
            return $this->response->json(
                [
                    'code' => 500,
                    'message' => '您没有访问改节点的权限！',
                    'data' => []
                ]
            );
        }
        //Context::set(ServerRequestInterface::class, $request);
        return $handler->handle($request);//交给下个一个中间件处理
    }
}