<?php

use HPlus\Admin\Model\Admin\Administrator;
use Qbhy\HyperfAuth\Provider\EloquentProvider;
use Qbhy\SimpleJwt\Encoders\Base64UrlSafeEncoder;
use Qbhy\SimpleJwt\EncryptAdapters\PasswordHashEncrypter;

return [
    'default' => [
        'guard' => 'jwt',
        'provider' => 'admin',
    ],
    'guards' => [ // 开发者可以在这里添加自己的 guard ，guard Qbhy\HyperfAuth\AuthGuard 接口
        'jwt' => [
            'driver' => Qbhy\HyperfAuth\Guard\JwtGuard::class,
            'provider' => 'admin',
            'secret' => env('JWT_SECRET', 'hyperf.plus'),
            'ttl' => 60 * 60, // 单位秒
            'default' => PasswordHashEncrypter::class,
            'encoder' => new Base64UrlSafeEncoder(),
            'cache' => function () {
                return make(Qbhy\HyperfAuth\HyperfRedisCache::class);
            },
        ],
        'session' => [
            'driver' => Qbhy\HyperfAuth\Guard\SessionGuard::class,
            'provider' => 'users',
        ],
    ],
    'providers' => [
        'admin' => [
            'driver' => EloquentProvider::class, // user provider 需要实现 Qbhy\HyperfAuth\UserProvider 接口
            'model' => Administrator::class, //  需要实现 Qbhy\HyperfAuth\Authenticatable 接口
        ],
        'users' => [
            'driver' => EloquentProvider::class, // user provider 需要实现 Qbhy\HyperfAuth\UserProvider 接口
            'model' => App\Model\User::class, //  需要实现 Qbhy\HyperfAuth\Authenticatable 接口
        ],
    ]
];