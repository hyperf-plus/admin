<?php
declare(strict_types=1);

/**
 * This file is part of Hyperf.plus
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/lphkxd/hyperf-plus/blob/master/LICENSE
 */

use HPlus\Admin\Library\Permission;

if (!function_exists('permission')) {
    /**
     * @return Permission
     */
    function permission()
    {
        return getContainer(Permission::class);
    }
}