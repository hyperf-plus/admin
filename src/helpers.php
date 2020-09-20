<?php
declare(strict_types=1);

/**
 * This file is part of Hyperf.plus
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/lphkxd/hyperf-plus/blob/master/LICENSE
 */


if (!function_exists('permission')) {
    /**
     * @return \HPlus\Admin\Contracts\PermissionInterface
     */
    function permission()
    {
        return getContainer(\HPlus\Admin\Contracts\PermissionInterface::class);
    }
}