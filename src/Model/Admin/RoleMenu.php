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
namespace HPlus\Admin\Model\Admin;

use HPlus\Admin\Model\Model;

/**
 * @property int $role_id
 * @property int $menu_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class RoleMenu extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_role_menu';

    protected $primaryKey = 'role_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['role_id' => 'integer', 'menu_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
