<?php
declare(strict_types=1);

namespace HPlus\Admin\Middleware;

use HPlus\Admin\Contracts\PermissionInterface;
use HPlus\Admin\Library\Permission;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Hyperf\HttpServer\Router\Dispatched;
use Mzh\Helper\RunTimes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Qbhy\HyperfAuth\Annotation\Auth;
use HPlus\Admin\Exception\UserLoginException;

class PermissionMiddleware implements MiddlewareInterface
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
     * @var Permission
     */
    protected $permission;

    public function __construct(PermissionInterface $permission)
    {
        $this->permission = $permission;
    }

    #laravel 权限每次都需要查询数据库，影响性能，此权限插件基于redis实现 性能大幅度提升
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Dispatched $router */
        $router = $request->getAttribute(Dispatched::class);
        if (!$router->isFound()) {
            throw new NotFoundException('接口不存在');
        }
        $has = $this->permission->can($request->getMethod(), $router->handler->route);
        if (!$has) {
          //  throw new UserLoginException(401, '您无权限');
        }
        return $handler->handle($request);
    }
}