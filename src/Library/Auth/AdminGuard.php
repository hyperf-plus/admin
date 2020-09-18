<?php
declare(strict_types=1);

namespace HPlus\Admin\Library\Auth;

use Hyperf\Cache\Cache;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\Context;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\Exception\AuthException;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;
use Qbhy\HyperfAuth\Guard\AbstractAuthGuard;
use Qbhy\HyperfAuth\HyperfRedisCache;
use Qbhy\HyperfAuth\UserProvider;
use Qbhy\SimpleJwt\Exceptions\InvalidTokenException;
use Qbhy\SimpleJwt\Exceptions\JWTException;
use Qbhy\SimpleJwt\Exceptions\SignatureException;
use Qbhy\SimpleJwt\Exceptions\TokenExpiredException;
use Qbhy\SimpleJwt\JWTManager;
use Throwable;

class AdminGuard extends AbstractAuthGuard
{
    /**
     * @var JWTManager
     */
    protected $jwtManager;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var ResponseInterface
     */
    protected $response;

    static $SESSIONNAME = 'HYPERFPLUSID';

    /**
     * JwtGuardAbstract constructor.
     * @param array $config
     * @param string $name
     * @param UserProvider $userProvider
     * @param Cache $cache
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(
        array $config,
        string $name,
        UserProvider $userProvider,
        Cache $cache,
        RequestInterface $request,
        ResponseInterface $response
    )
    {
        parent::__construct($config, $name, $userProvider);
        $this->request = $request;
        $this->response = $response;
        $this->cache = $cache;
    }

    public function parseToken()
    {
        return $this->request->cookie(self::$SESSIONNAME, '');
    }

    public function login(Authenticatable $user)
    {
        return $this->jwtManager->make(['uid' => $user->getId()])->token();
    }

    public function resultKey($token)
    {
        return $this->name . '.auth.result.' . $token;
    }

    public function user(?string $token = null): ?Authenticatable
    {
        $token = $token ?? $this->parseToken();
        if (Context::has($key = $this->resultKey($token))) {
            $result = Context::get($key);
            if ($result instanceof Throwable) {
                throw $result;
            }
            return $result ?: null;
        }
        try {
            if ($token) {
                $uid = $this->cache->get($this->resultKey($token));
                $user = $uid ? $this->userProvider->retrieveByCredentials($uid) : null;
                Context::set($key, $user ?: 0);
                return $user;
            }
            return null;
        } catch (Throwable $exception) {
            $newException = $exception instanceof AuthException ? $exception : new UnauthorizedException(
                $exception->getMessage(),
                $this,
                $exception
            );
            Context::set($key, $newException);
            throw $newException;
        }
    }

    public function check(?string $token = null): bool
    {
        try {
            return $this->user($token) instanceof Authenticatable;
        } catch (AuthException $exception) {
            return false;
        }
    }

    public function guest(?string $token = null): bool
    {
        return !$this->check($token);
    }

    /**
     * 刷新 token，旧 token 会失效.
     * @throws InvalidTokenException
     * @throws JWTException
     * @throws SignatureException
     * @throws TokenExpiredException
     */
    public function refresh(?string $token = null): ?string
    {
        $token = $token ?? $this->parseToken();

        if ($token) {
            try {
                $jwt = $this->jwtManager->parse($token);
            } catch (TokenExpiredException $exception) {
                $jwt = $exception->getJwt();
            }

            $this->jwtManager->addBlacklist($jwt);

            return $this->jwtManager->refresh($jwt)->token();
        }

        return null;
    }

    public function logout($token = null)
    {
        if ($token = $token ?? $this->parseToken()) {
            Context::destroy($this->resultKey($token));
            $this->jwtManager->addBlacklist(
                $this->jwtManager->parse($token)
            );
            return true;
        }
        return false;
    }
}
