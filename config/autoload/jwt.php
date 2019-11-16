<?php

declare(strict_types=1);

return [
    # 登录方式，sso为单点登录，mpop为多点登录
    'login_type' => env('JWT_LOGIN_TYPE', 'sso'),

    # 单点登录自定义数据中必须存在uid的键值，这个key你可以自行定义，只要自定义数据中存在该键即可
    'sso_key' => 'uid',

    # 非对称加密使用字符串,请使用自己加密的字符串
    'secret' => env('JWT_SECRET', 'admin666'),

    /*
     * JWT 权限keys
     * 对称算法: HS256, HS384 & HS512 使用 `JWT_SECRET`.
     * 非对称算法: RS256, RS384 & RS512 / ES256, ES384 & ES512 使用下面的公钥私钥.
     */
    'keys' => [
        # 公钥，例如：'file://path/to/public/key'
        'public' => env('JWT_PUBLIC_KEY'),

        # 私钥，例如：'file://path/to/private/key'
        'private' => env('JWT_PRIVATE_KEY'),
    ],

    # token过期时间，单位为秒
    'ttl' => env('JWT_TTL', 7200),

    # jwt的hearder加密算法
    'alg' => env('JWT_ALG', 'HS256'),

    # 是否开启黑名单，单点登录和多点登录的注销、刷新使原token失效，必须要开启黑名单，目前黑名单缓存只支持hyperf缓存驱动
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),

    # 黑名单的宽限时间 单位为：秒，注意：如果使用单点登录，该宽限时间无效
    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),

    # 黑名单缓存token时间，注意：该时间一定要设置比token过期时间要大，默认为1天
    'blacklist_cache_ttl' => env('JWT_BLACKLIST_CACHE_TTL', 86400),
];
