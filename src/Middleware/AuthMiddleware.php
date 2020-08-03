<?php

declare(strict_types=1);

namespace Mzh\Admin\Middleware;

use App\Constants\ErrorCode;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\SessionInterface;
use Hyperf\Di\Exception\NotFoundException;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Utils\Context;
use Mzh\Admin\Exception\UserLoginException;
use Mzh\Admin\Library\Auth;
use Mzh\Helper\Session\Session;
use Mzh\JwtAuth\Exception\TokenValidException;
use Mzh\JwtAuth\Jwt;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SessionHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{

    /**
     * @var ContainerInterface
     */
    protected $container;

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

    public function __construct(Jwt $jwt, ConfigInterface $config, ContainerInterface $container, SessionInterface $session)
    {
        //$this->auth = $container->get(Auth::class);
        $this->session = $session;
        $this->jwt = $jwt;
        $this->config = $config;
        $this->handler = $container->get($this->config->get('session.handler'));
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
            // Headers 可以根据实际情况进行改写。
            ->withHeader('Access-Control-Allow-Methods', '*')
            ->withHeader('Access-Control-Allow-Headers', '*');
        Context::set(ResponseInterface::class, $response);

        if ($request->getMethod() == 'OPTIONS') {
            return $response;
        }
        /** @var Dispatched $router */
        $router = $request->getAttribute(Dispatched::class);
        if (!$router->isFound()) {
            throw new NotFoundException('接口不存在');
        }

        $url = $request->getMethod() . '::' . $router->handler->route;
        p($url);

        $response = $handler->handle($request);
        return $response;


        $currUrl = strtolower($router->handler->route);
        $security = $this->auth->isSecurity($currUrl);
        $token = $request->getHeaderLine('authorization');
        $token = trim($token);
        #如果不验证权限、token也为空，则直接处理
        if ($security == false && empty($token)) {
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
        }
        #验证TOKEN
        try {
            $user = $this->jwt->verifyToken($token);
        } catch (TokenValidException $validException) {
            throw new UserLoginException(ErrorCode::USER_TOKEN_INVALID);
        }
        $session = new Session($this->handler, (string)$user->getAudience());
        $session->start();
        #检测用户授权信息
        /** @var UserInfo $userInfo */
        $userInfo = $session->get(UserInfo::class);
        if (!$userInfo instanceof UserInfo) {
            throw new UserLoginException(ErrorCode::USER_NO_LOGIN);
        }
//       if (!$this->auth->check($userInfo->getGroupId(), $currUrl) && $security) {
//            throw new UserLoginException(ErrorCode::USER_NO_ACCESS);
//        }
        Context::set(SessionInterface::class, $session);
        try {
            $response = $handler->handle($request);
        } finally {
            $session->save();
        }
        return $response;
    }
}