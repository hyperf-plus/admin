<?php

declare (strict_types=1);
namespace Mzh\Admin\Model;

use Hyperf\Database\Model\Model;

/**
 * @property int $menu_id 
 * @property int $parent_id 父id
 * @property string $name 名称
 * @property string $alias 别名
 * @property string $icon 图标
 * @property string $remark 备注
 * @property string $module 所属模块
 * @property int $type 0=模块 1=外链
 * @property string $url 链接
 * @property string $params 参数
 * @property string $target _self _blank
 * @property int $is_navi 导航 0=否 1=是
 * @property int $sort 排序
 * @property int $status 0=禁用 1=启用
 */
class Menu extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menu';
    protected $primaryKey = 'menu_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['menu_id', 'parent_id', 'name', 'alias', 'icon', 'remark', 'module', 'type', 'url', 'params', 'target', 'is_navi', 'sort', 'status'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['menu_id' => 'integer', 'parent_id' => 'integer', 'type' => 'integer', 'is_navi' => 'integer', 'sort' => 'integer', 'status' => 'integer'];
}