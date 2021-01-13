<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.plus
 *
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/hyperf-plus/admin/blob/master/LICENSE
 */

namespace HPlus\Admin\Middleware;

use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\AuthGuard;
use Qbhy\HyperfAuth\AuthManager;
use \Hyperf\Utils\Context;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;

/**
 * Class AuthMiddleware.
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @var AuthGuard
     */
    protected $guard;

    public function __construct(AuthManager $auth, ConfigInterface $config)
    {
        $this->guard = $auth->guard($config->get('admin.auth.guard', 'jwt'));
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (strpos($request->getUri()->getQuery(), '_export_') !== false) {
            //因为导出数据是跳转的浏览器新窗口，所以头信息携带会丢失，这里需要用cookie来判断权限
            Context::override(ServerRequestInterface::class, function (ServerRequestInterface $request) {
                $token = $request->getCookieParams()[config('cookie_name', 'HPLUSSESSIONID')] ?? null;
                return $request->withQueryParams(array_merge($request->getQueryParams(),[
                    'token' => $token
                ]));
            });
        }

        if (!$this->guard->user() instanceof Authenticatable) {
            throw new UnauthorizedException("Without authorization from {$this->guard->getName()} guard", $guard);
        }
        return $handler->handle($request);
    }
}
