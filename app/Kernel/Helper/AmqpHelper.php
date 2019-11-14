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

use Hyperf\Amqp\Message\ProducerMessageInterface;
use Hyperf\Amqp\Producer;

class AmqpHelper
{
    public static function produce(ProducerMessageInterface $message, $retry = 2)
    {
        return retry($retry, function () use ($message) {
            return di()->get(Producer::class)->produce($message, true);
        });
    }
}
