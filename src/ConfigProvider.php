<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.plus
 *
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/hyperf-plus/admin/blob/master/LICENSE
 */
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
                        __DIR__,
                    ],
                ],
                'ignore_annotations' => [
                    'mixin',
                ],
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
