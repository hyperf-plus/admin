<?php
declare(strict_types=1);
namespace HPlus\Admin;

use HPlus\Admin\Contracts\AuthInterface;
use HPlus\Admin\Library\Auth;

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
                AuthInterface::class => Auth::class,
            ],
            'publish' => [
                [
                    'id' => 'admin',
                    'description' => 'hyperf-admin',
                    'source' => __DIR__ . '/../publish/admin.php',
                    'destination' => BASE_PATH . '/config/autoload/admin.php',
                ],
            ],
        ];
    }
}
