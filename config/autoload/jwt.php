<?php

declare(strict_types=1);

return [
    # 登录方式，sso为单点登录，mpop为多点登录
    'login_type' => env('JWT_LOGIN_TYPE', 'sso'),

    # 单点登录自定义数据中必须存在uid的键值，这个key你可以自行定义，只要自定义数据中存在该键即可
    'sso_key' => 'u',

    # 非对称加密使用字符串,请使用自己加密的字符串
    'secret' => env('JWT_SECRET', 'jwthyper'),
    /*
     * JWT 权限keys
     * 对称算法: HS256, HS384 & HS512 使用 `JWT_SECRET`.
     * 非对称算法: RS256, RS384 & RS512 / ES256, ES384 & ES512 使用下面的公钥私钥.
     */
    'keys' => [
        # 公钥密文
        'public' => env('JWT_PUBLIC_KEY'),
        # 私钥密文
        'private' => env('JWT_PRIVATE_KEY'),
    ],

    # token过期时间，单位为秒
    'ttl' => env('JWT_TTL', 7200),

    # refresh_token过期时间，单位为秒 默认15天
    'refresh_ttl' => env('JWT_TTL', 3600 * 24 * 15),

    # jwt的hearder加密算法  目前仅支持对称加密
    'alg' => env('JWT_ALG', 'HS256'),

    # Redis缓存前缀
    'cache_prefix' => env('JWT_AUTH_PREFIX', 'jwt:'),

    # Redis auth授权表缓存前缀
    'auth_prefix' => env('JWT_AUTH_PREFIX', 'auth:'),

    # Redis auth_log授权表缓存前缀
    'auth_log_prefix' => env('JWT_AUTH_LOG_PREFIX', 'auth_log:')

];
