<?php
declare(strict_types=1);

namespace HPlus\Admin;


use HPlus\Admin\Contracts\PermissionInterface;
use HPlus\Admin\Library\Permission;
use HPlus\Admin\Listener\PermissionListener;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__
                    ],
                ],
            ],
            'dependencies' => [
                PermissionInterface::class => Permission::class
            ],
//            'listeners' => [
//                PermissionListener::class,
//            ],
            'publish' => [
                [
                    'id' => 'admin',
                    'description' => 'hyperf-admin',
                    'source' => __DIR__ . '/../publish/admin.php',
                    'destination' => BASE_PATH . '/config/autoload/admin.php',
                ], [
                    'id' => 'auth',
                    'description' => 'hyperf-auth',
                    'source' => __DIR__ . '/../publish/auth.php',
                    'destination' => BASE_PATH . '/config/autoload/auth.php',
                ], [
                    'id' => 'file',
                    'description' => 'hyperf-file',
                    'source' => __DIR__ . '/../publish/file.php',
                    'destination' => BASE_PATH . '/config/autoload/file.php',
                ],
            ],
        ];
    }
}
