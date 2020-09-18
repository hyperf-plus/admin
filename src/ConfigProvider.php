<?php
declare(strict_types=1);

namespace HPlus\Admin;

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
            ],
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
                ],
            ],
        ];
    }
}
