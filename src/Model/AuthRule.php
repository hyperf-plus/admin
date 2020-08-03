<?php

declare (strict_types=1);

namespace Mzh\Admin\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $module 所属模块
 * @property int $group_id 用户组Id
 * @property string $name 规则名称
 * @property string $menu_auth 菜单权限
 * @property string $log_auth 记录权限
 * @property int $sort 排序
 * @property int $status 0=禁用 1=启用
 * @property int $is_delete 0=为删除 1=已删除
 * @property int $update_time 创建时间
 * @property int $create_time 创建时间
 * @property int $pid
 */
class AuthRule extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'auth_rule';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'module', 'group_id', 'name', 'menu_auth','permissions', 'log_auth', 'sort', 'status', 'is_delete', 'update_time', 'create_time', 'pid'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'group_id' => 'integer', 'sort' => 'integer', 'status' => 'integer', 'menu_auth' => 'json', 'permissions' => 'json', 'log_auth' => 'json', 'is_delete' => 'integer', 'update_time' => 'integer', 'create_time' => 'integer', 'pid' => 'integer'];
}