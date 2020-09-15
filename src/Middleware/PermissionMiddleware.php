<?php

declare(strict_types=1);

namespace HPlus\Admin\Middleware;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\SessionInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Exception\NotFoundException;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Utils\Context;
use HPlus\Admin\Admin;
use HPlus\Admin\Exception\UserLoginException;
use HPlus\Admin\Contracts\AuthInterface;
use HPlus\Admin\Contracts\UserInfoInterface;
use HPlus\Admin\Library\Auth;
use HPlus\Admin\Model\Admin\OperationLog;
use HPlus\Helper\RunTimes as RunTimesAlias;
use HPlus\Helper\Session\Session;
use HPlus\JwtAuth\Exception\TokenValidException;
use HPlus\JwtAuth\Jwt;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SessionHandlerInterface;

class PermissionMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject()
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var Jwt
     */
    private $jwt;
    /**
     * @var ConfigInterface
     */
    private $config;
    /**
     * @var SessionHandlerInterface
     */
    private $handler;

    public function __construct(Jwt $jwt, AuthInterface $auth, ConfigInterface $config, ContainerInterface $container, SessionInterface $session)
    {
        $this->auth = $auth;
        $this->session = $session;
        $this->jwt = $jwt;
        $this->config = $config;
        $this->handler = $container->get($this->config->get('session.handler'));
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $time = new RunTimesAlias;
        $time->start();

        /** @var Dispatched $router */
        $router = $request->getAttribute(Dispatched::class);
        if (!$router->isFound()) {
            throw new NotFoundException('接口不存在');
        }
        $currUrl = $request->getMethod() . '::' . $router->handler->route;


      //  p($this->auth->hasPermission(1, 'admin', $currUrl));


        $security = FALSE; //$this->auth->isOpen($currUrl);
        #如果为开放资源直接处理
        if (!$security) {
            try {
                $response = $handler->handle($request);
                $this->log($time->spent());
            } finally {
                /** @var SessionInterface $session */
                $session = Context::get(SessionInterface::class);


                if (!empty($session)) {
                    $session->save();
                }
            }
            return $response;
        }
        $token = $request->getHeaderLine('x-token');
        $token = trim($token);
        #验证TOKEN
        try {
            $user = $this->jwt->verifyToken($token);
        } catch (TokenValidException $validException) {
            throw new UserLoginException(50012, $validException->getMessage());
        }
        $session = new Session($this->handler, $user->getIssuer() . ':' . (string)$user->getAudience());
        $session->start();
        #检测用户授权信息
        /** @var UserInfoInterface $userInfo */
        $userInfo = $session->get(UserInfoInterface::class);
        switch (true) {
            #如果为开放资源直接处理
            case $this->auth->isUserOpen($currUrl) && $userInfo instanceof UserInfoInterface:
                Context::set(SessionInterface::class, $session);
                try {
                    $response = $handler->handle($request);
                } finally {
                    /** @var SessionInterface $session */
                    $session = Context::get(SessionInterface::class);
                    if (!empty($session)) {
                        $session->save();
                    }
                }
                return $response;
                break;
            case  !$userInfo instanceof UserInfoInterface:
                throw new UserLoginException(50012, '请先登录！');
                break;
            case !$userInfo->isIsAdmin() && $security && !$this->auth->hasPermission($userInfo->getUserId(), $user->getIssuer(), $currUrl):
                throw new UserLoginException(1000, '您无权限');
                break;
            default:
                Context::set(SessionInterface::class, $session);
                try {
                    $response = $handler->handle($request);
                } finally {
                    $session->save();
                }
                break;
        }
        return $response;
    }

    /**
     * @todo
     * 暂未做性能优化
     */
    public function log($runtime = '')
    {
        $log = [
            'user_id' => 1,
            'runtime' => $runtime,
            'path' => substr($this->request->getUri()->getPath(), 0, 255),
            'method' => $this->request->getMethod(),
            'ip' => getClientIp(),
            'input' => json_encode($this->request->all(), 256),
        ];
        try {
            OperationLog::create($log);
        } catch (\Exception $exception) {
            // pass
        }
    }
}