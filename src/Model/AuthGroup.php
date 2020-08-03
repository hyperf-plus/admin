<?php

declare (strict_types=1);

namespace Mzh\Admin\Model;

use Hyperf\Database\Model\Model;

/**
 * @property int $group_id
 * @property string $name 名称
 * @property string $description 描述
 * @property string $module 所属模块
 * @property int $system 系统保留
 * @property int $sort 排序
 * @property int $status 0=禁用 1=启用
 */
class AuthGroup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'auth_group';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['group_id', 'name', 'description', 'module', 'system', 'sort', 'status'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['group_id' => 'integer', 'system' => 'integer', 'sort' => 'integer', 'status' => 'integer'];
}