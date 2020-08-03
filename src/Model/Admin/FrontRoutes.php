<?php
declare(strict_types=1);

namespace Mzh\Admin\Model\Admin;

use Hyperf\Database\Model\Model;

/**
 * @property int $id
 * @property int $pid 父级ID
 * @property string $label label名称
 * @property string $module 模块
 * @property string $path 路径
 * @property string $view 非脚手架渲染是且path路径为正则时, vue文件路径
 * @property string $icon icon
 * @property int $open_type 打开方式 0 当前页面 2 新标签页
 * @property int $is_scaffold 是否脚手架渲染, 1是, 0否
 * @property int $is_menu 是否菜单 0 否 1 是
 * @property int $status 状态：0 禁用 1 启用
 * @property int $sort 排序，数字越大越在前面
 * @property \Carbon\Carbon $create_at
 * @property \Carbon\Carbon $update_at
 * @property string $permission 权限标识
 * @property int $http_method 请求方式; 0, Any; 1, GET; 2, POST; 3, PUT; 4, DELETE;
 * @property int $type 菜单类型 0 目录  1 菜单 2 其他
 * @property int $page_type 页面类型： 0 列表  1 表单
 * @property string $scaffold_action 脚手架预置权限
 */
class FrontRoutes extends Model
{
    const HTTP_METHOD_ANY = 0;
    const HTTP_METHOD_GET = 1;
    const HTTP_METHOD_POST = 2;
    const HTTP_METHOD_PUT = 3;
    const HTTP_METHOD_DELETE = 4;

    protected $connection = 'default';
    protected $table = 'front_routes';
    protected $database = 'admin';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pid',
        'label',
        'module',
        'path',
        'view',
        'icon',
        'open_type',
        'is_scaffold',
        'is_menu',
        'status',
        'sort',
        'permission',
        'http_method',
        'type',
        'page_type',
        'scaffold_action',
    ];

    protected $casts = [
        'id' => 'int',
        'pid' => 'int',
        'label' => 'string',
        'module' => 'string',
        'path' => 'string',
        'view' => 'string',
        'icon' => 'string',
        'open_type' => 'int',
        'is_scaffold' => 'int',
        'is_menu' => 'int',
        'status' => 'int',
        'sort' => 'int',
        'permission' => 'json',
        'http_method' => 'int',
        'type' => 'int',
        'page_type' => 'int',
        'scaffold_action' => 'json',
    ];

    public static $http_methods = [
        self::HTTP_METHOD_ANY => 'ANY',
        self::HTTP_METHOD_GET => 'GET',
        self::HTTP_METHOD_POST => 'POST',
        self::HTTP_METHOD_PUT => 'PUT',
        self::HTTP_METHOD_DELETE => 'DELETE',
    ];

//    public function getIsMenuAttribute()
//    {
//        return $this->attributes['is_menu'] == true ? '是' : '否';
//    }

    public function hasChildren()
    {
        return $this->newQuery()->where('pid', $this->id)->count() > 0;
    }
}
