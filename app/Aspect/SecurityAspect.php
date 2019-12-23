<?php

declare(strict_types=1);

namespace App\Aspect;

use App\Annotations\Authorize;
use App\Annotations\PreAuthorize;
use App\Exception\AccessDeniedException;
use App\Traits\GetSecurity;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Exception\Exception;
use Psr\Container\ContainerInterface;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @Aspect
 */
class SecurityAspect extends AbstractAspect
{
    protected ContainerInterface $container;
    protected ServerRequestInterface $request;
    use GetSecurity;

    // 要切入的类，可以多个，亦可通过 :: 标识到具体的某个方法，通过 * 可以模糊匹配
    public $annotations = [
    ];

    public function __construct(ContainerInterface $container ,ServerRequestInterface $Request)
    {
        $this->container = $container;
        $this->request = $this->container->get(ServerRequestInterface::class);
    }

    /**
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     * @throws AccessDeniedException
     * @throws Exception
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        if (!$this->isAuthenticated()) {
            throw new AccessDeniedException();
        }
        foreach ($proceedingJoinPoint->getAnnotationMetadata()->method as $Authorize) {
            $this->checkPermission($Authorize);
        }
        $principal = $this->getPrincipal();

        return $proceedingJoinPoint->process();
    }


    /**
     * @param Authorize $Authorize
     * @return bool
     * @throws AccessDeniedException
     */
    private function checkPermission($Authorize)
    {
        if ($Authorize->all) {
            return true;
        }
        if ($Authorize->deny) {
            throw new AccessDeniedException();
        }

        if ($Authorize->value && !$this->hasAnyPermission($Authorize->value)) {
            throw new AccessDeniedException();
        }
        $roles = $Authorize->roles;
        if ($roles && !$this->hasAnyRole($Authorize->roles)) {
            throw new AccessDeniedException();
        }
        $ips = $Authorize->ips;
        if ($ips && !$this->hasIpAddress($Authorize->ips)) {
            throw new AccessDeniedException();
        }
        return true;
    }
}
