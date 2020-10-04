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
namespace HPlus\Admin\Exception;

use Throwable;

class BusinessException extends \Exception
{
    public function __construct(int $code = 0, string $message = null, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
