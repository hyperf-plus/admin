<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Entity\Tenant;
use App\Service\TenantService;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TenantMiddleware implements MiddlewareInterface
{

    protected ContainerInterface $container;

    protected TenantService $TenantService;

    public function __construct(ContainerInterface $container, TenantService $service)
    {
        $this->container = $container;
        $this->TenantService = $service;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cookie = $request->getCookieParams();
        $tenant_id = intval(isset($cookie['corp_id']) ? $cookie['corp_id'] : 0);
        if ($tenant_id > 0) {
            $this->setTenantData($tenant_id);
            return $handler->handle($request);
        }
        $tenant_id = intval($request->getQueryParams()['corp_id'] ?? 0);
        if ($tenant_id > 0) {
            $this->setTenantData($tenant_id);
            return $handler->handle($request);
        }
        return $handler->handle($request);
    }

    private function setTenantData($tenant_id)
    {
        $Tenant = $this->TenantService->getInfo($tenant_id);
        return Context::set(Tenant::class, $Tenant);
    }
}