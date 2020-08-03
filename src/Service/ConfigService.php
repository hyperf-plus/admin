<?php
declare(strict_types=1);

namespace Mzh\Admin\Service;


use Mzh\Admin\Model\Config;

class ConfigService
{
    public static function getConfig($name)
    {
        return Config::query()->where('name', $name)->value('value');
    }
}