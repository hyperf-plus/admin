<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\Constants;
use App\Exception\LoginException;
use App\Util\Auth;
use App\Util\Jwt\Jwt;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
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

    public function __construct(ContainerInterface $container, HttpResponse $response, RequestInterface $request)
    {
        $this->container = $container;
        $this->response = $response;
        $this->request = $request;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //检查节点
        //检查TOKEN
        $cur_node = $this->request->getUri()->getPath();
        $method = $this->request->all()['method'] ?? '';
        if ($method == 'login.admin.user' && $cur_node == '/api/v1/admin') {
            return $handler->handle($request);//交给下个一个中间件处理
        }

        foreach (Auth::ignores() as $ignore) {
            if ($ignore === $cur_node) {
                return $handler->handle($request);//交给下个一个中间件处理
            }
        }
        $token = $this->request->all()['token'] ?? '';
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
        //todo 检查用户与节点权限
        if (Auth::checkNode($admin['group'], $cur_node)) {
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