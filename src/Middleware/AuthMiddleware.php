<?php

declare(strict_types=1);

namespace HPlus\Admin\Middleware;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\AuthManager;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;

/**
 * Class AuthMiddleware.
 */
class AuthMiddleware implements MiddlewareInterface
{
    protected $guards = [null];

    /**
     * @var AuthManager
     */
    protected $auth;

    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        foreach ($this->guards as $name) {
            $guard = $this->auth->guard($name);
            if (!$guard->user() instanceof Authenticatable) {
                throw new UnauthorizedException("Without authorization from {$guard->getName()} guard", $guard);
            }
        }
        return $handler->handle($request);
    }
}
