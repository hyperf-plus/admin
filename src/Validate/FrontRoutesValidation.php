<?php 
declare(strict_types = 1);
namespace Mzh\Admin\Validate;

use Mzh\Validate\Validate\Validate;

class FrontRoutesValidation extends Validate
{
    protected $rule = [
        'id' => 'require|integer|gt:0',
        'pid' => 'intOrArrayInt',
        'label' => 'require',
        'module' => 'require',
        'path' => 'max:255',
        'view' => 'max:255',
        'icon' => 'max:255',
        'open_type' => 'max:255',
        'is_scaffold' => 'integer',
        'is_menu' => 'integer',
        'status' => 'integer',
        'sort' => 'integer',
        'permission' => 'max:255',
        'http_method' => 'max:255',
        'type' => 'max:255',
        'page_type' => 'max:255',
        'scaffold_action' => 'max:255',
        'page' => 'integer',
        'limit' => 'integer|gt:0',
    ];

    protected $field = [
        'pid' => '父级ID',
        'label' => 'label名称',
        'module' => '模块',
        'path' => '路径',
        'view' => '非脚手架渲染是且path路径为正则时, vue文件路径',
        'icon' => 'icon',
        'open_type' => '打开方式 0 当前页面 2 新标签页',
        'is_scaffold' => '是否脚手架渲染, 1是, 0否',
        'is_menu' => '是否菜单 0 否 1 是',
        'status' => '状态：0 禁用 1 启用',
        'sort' => '排序，数字越大越在前面',
        'permission' => '权限标识',
        'http_method' => '请求方式; 0, Any; 1, GET; 2, POST; 3, PUT; 4, DELETE;',
        'type' => '菜单类型 0 目录  1 菜单 2 其他',
        'page_type' => '页面类型： 0 列表  1 表单',
        'scaffold_action' => '脚手架预置权限',
        'page' => '页码',
        'limit' => '每页条数',
    ];

    protected $scene = [
        'detail' => ['pid'],
        'update' => [
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
            'pid',
            'permission',
            'http_method',
            'type',
            'page_type',
            'scaffold_action',
        ],
        'delete' => ['pid'],
        'list' => ['limit', 'page'],
        'sort' => ['pid', 'sort'],
        'status' => [0 => 'pid', 'status' => 'require|in:0,1'],
        'create' => [
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
            'pid',
            'permission',
            'http_method',
            'type',
            'page_type',
            'scaffold_action',
        ],
    ];
}
