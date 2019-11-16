<?php


namespace App\Util;

use App\Constants\Constants;
use App\Exception\LoginException;
use App\Util\Jwt\Jwt;
use InvalidArgumentException;

class AccessToken
{
    private static $alg;

    private static $app_key;

    public function __construct()
    {
        self::$alg = 'HS256';
        self::$app_key = config('app_key');
    }

    public function encode(array $payload): string
    {
        return JWT::getToken($payload);
    }

    public function decode(string $jwt): array
    {
        try {
            $decode = JWT::verifyToken($jwt);
            return (array)$decode;
        } catch (ExpiredException $exception) {
            //过期token
            throw new LoginException('token过期！', 500);
        } catch (InvalidArgumentException $exception) {
            //参数错误
            throw new LoginException('token参数非法！', 500);
        } catch (\UnexpectedValueException $exception) {
            //token无效
            throw new LoginException('token无效！', 500);
        } catch (\Exception $exception) {
            throw new LoginException($exception->getMessage(), 500);
        }
    }

    /**
     * 创建token
     * @return string
     */
    public function createToken($data)
    {
        $token = $this->encode($data);
        return $token;
    }

    public function checkToken(string $token): Payload
    {
        if (empty($token)) {
            throw new LoginException('token不能为空！', 500);
        }

        $decode = $this->decode($token);

        if (is_null($decode)) {
            throw new LoginException('token无效！', 500);
        }

        $jwt = new Payload($decode);

        if (Constants::SCOPE_ROLE !== $jwt->scopes) {
            throw new LoginException('token参数非法！', 500);
        }

        return $jwt;

    }

    public function checkRefreshToken(string $refresh)
    {
        if (empty($refresh)) {
            throw new LoginException('token不能为空！', 500);
        }

        $decode = $this->decode($refresh);

        if (is_null($decode)) {
            throw new LoginException('token无效！', 500);
        }

        $jwt = new Payload($decode);

        if (Constants::SCOPE_REFRESH !== $jwt->scopes) {
            throw new LoginException('refresh-token参数非法！', 500);
        }

        return $jwt->toArray();
    }

    /**
     * 刷新token
     * @param $refresh
     * @return string
     */
    public function refreshToken($refresh): string
    {
        if (empty($refresh)) {
            throw new LoginException('参数有误！');
        }

        $jwt = $this->decode($refresh);

        if (is_null($jwt)) {
            throw new LoginException('refresh-token参数有误！', 500);
        }

        if (Constants::SCOPE_REFRESH !== $jwt['scopes']) {
            throw new LoginException('refresh-token参数非法！', 500);
        }

        $data = $jwt['data'];
        return '';
    }

}