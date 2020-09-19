<?php
declare(strict_types=1);

namespace HPlus\Admin\Middleware;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $time = new RunTimesAlias;
        $time->start();
        /** @var Dispatched $router */
        $router = $request->getAttribute(Dispatched::class);
        if (!$router->isFound()) {
            throw new NotFoundException('接口不存在');
        }
        return $handler->handle($request);
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