<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 日志中间件
 * Class LogMiddleware
 * @package App\Middleware
 */
class LogMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

//        var_dump($request->getAttributes());
//        var_dump($request->getHeaders());
//        var_dump($request->getMethod());
//        var_dump($request->getQueryParams());
//        var_dump($request->getUri()->getHost());
//        var_dump($request->getUri()->getAuthority());
//        var_dump($request->getUri()->getPath());
//        var_dump($request->getUri()->getScheme());
        return $handler->handle($request);
    }
}