<?php 
declare(strict_types = 1);
namespace Mzh\Admin\Views;

class FrontRoutesView implements UiViewInterface
{
    public function scaffoldOptions()
    {
        return [
            'form' => [
                'id|#' => '',
                'pid|父级ID' => 'integer',
                'label|label名称' => 'require',
                'module|模块' => 'require',
                'path|路径' => '',
                'view|非脚手架渲染是且path路径为正则时, vue文件路径' => '',
                'icon|icon' => '',
                'open_type|打开方式 0 当前页面 2 新标签页' => '',
                'is_scaffold|是否脚手架渲染, 1是, 0否' => 'integer',
                'is_menu|是否菜单 0 否 1 是' => 'integer',
                'status|状态：0 禁用 1 启用' => 'integer',
                'sort|排序，数字越大越在前面' => 'integer',
                'permission|权限标识' => '',
                'http_method|请求方式; 0, Any; 1, GET; 2, POST; 3, PUT; 4, DELETE;' => '',
                'type|菜单类型 0 目录  1 菜单 2 其他' => '',
                'page_type|页面类型： 0 列表  1 表单' => '',
                'scaffold_action|脚手架预置权限' => '',
            ],
            'table' => [
                'rowActions' => [
                    [
                        'type' => 'jump',
                        'target' => '/front_routes/{id}',
                        'text' => '编辑',
                    ],
                    [
                        'type' => 'api',
                        'target' => '/front_routes/delete',
                        'text' => '删除',
                        'props' => [
                            'type' => 'danger',
                        ],
                    ],
                ],
            ],
        ];
    }
}
