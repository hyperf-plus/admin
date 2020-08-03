<?php
declare(strict_types=1);
namespace Mzh\DevTools;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'init_routes' => [
                __DIR__ . '/config/routes.php',
            ],
        ];
    }
}
