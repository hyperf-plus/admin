<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Kernel\Helper;

use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use Hyperf\AsyncQueue\JobInterface;

class QueueHelper
{
    public static function push(JobInterface $job, $name = 'default')
    {
        $driver = self::getDriver($name);
        return $driver->push($job);
    }

    public static function delay(JobInterface $job, int $delay, $name = 'default')
    {
        $driver = self::getDriver($name);
        return $driver->delay($job, $delay);
    }

    private static function getDriver($name): DriverInterface
    {
        return di()->get(DriverFactory::class)->get($name);
    }
}
