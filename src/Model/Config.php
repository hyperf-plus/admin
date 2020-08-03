<?php

declare (strict_types=1);

namespace Mzh\Admin\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $namespace 命名空间, 字母
 * @property string $name 配置名, 字母
 * @property string $title 可读配置名
 * @property string $remark 备注
 * @property string $rules 配置规则描述
 * @property string $value 具体配置值 key:value
 * @property string $permissions 权限
 * @property string $create_at
 * @property string $update_at
 * @property int $is_need_form 是否启用表单：0，否；1，是
 */
class Config extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'config';
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'namespace', 'name', 'title', 'remark', 'rules', 'value', 'permissions', 'created_at', 'updated_at', 'is_need_form'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'is_need_form' => 'integer', 'value' => 'json', 'rules' => 'json'];
}