<?php
declare(strict_types=1);

namespace Mzh\Admin\Views;

use Hyperf\Di\Annotation\Inject;
use Mzh\Admin\Service\AuthService;
use Mzh\Admin\Service\ConfigService;
use Mzh\Admin\Service\MenuService;

class MenuView implements UiViewInterface
{
    /**
     * @Inject()
     * @var AuthService
     */
    protected $authService;

    public function scaffoldOptions()
    {
        return [
            'createAble' => true,
            'deleteAble' => true,
            'defaultList' => true,
            'where' => [
                'pid' => 0,
            ],
            'formUI' => [
                'form' => [
                    'size' => 'mini',
                ],
            ],
            'filter' => ['name%', 'id%'],
            'form' => [
                'id|#' => 'int',
                'module|#' => [
                    'type' => 'hidden',
                    'render' => function ($field, &$data) {
                        $data['value'] = request()->input('module', $data['value'] ?? 'system');
                    },
                ],
                'type|菜单类型' => [
                    'type' => 'radio',
                    'default' => 1,
                    'options' => ['目录', '菜单', '权限'],
                    'compute' => [
                        [
                            'when' => ['in', [0, 2]],
                            'set' => [
                                'is_scaffold' => [
                                    'type' => 'hidden',
                                ],
                                'other_menu' => [
                                    'type' => 'hidden',
                                ],
                                'path' => [
                                    'type' => 'hidden',
                                ],
                                'page_type' => [
                                    'type' => 'hidden',
                                ],
                            ],
                        ],
                        [
                            'when' => ['=', 1],
                            'set' => [
                                'path' => [
                                    'rule' => 'required',
                                ],
                                'label' => [
                                    'title' => '菜单标题',
                                    'col' => [
                                        'span' => 12,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'when' => ['=', 2],
                            'set' => [
                                'icon' => [
                                    'type' => 'hidden',
                                ],
                                'is_menu' => [
                                    'type' => 'hidden',
                                ],
                                'label' => [
                                    'title' => '权限名称',
                                    'col' => [
                                        'span' => 24,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'when' => ['=', 0],
                            'set' => [
                                'permission' => [
                                    'type' => 'hidden',
                                ],
                                'label' => [
                                    'title' => '菜单标题',
                                    'col' => [
                                        'span' => 12,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'icon|菜单图标' => [
                    'type' => 'icon-select',
                    'options' => [
                        'example' => 'example',
                    ],
                    'col' => [
                        'span' => 12,
                    ],
                ],
                'sort|菜单排序' => [
                    'type' => 'number',
                    'default' => 99,
                    'col' => [
                        'span' => 12,
                    ],
                ],
                'label|菜单标题' => [
                    'rule' => 'required|string|max:10',
                    'col' => [
                        'span' => 12,
                    ],
                ],
                'path|路由地址' => [
                    'rule' => 'string|max:100',
                    'default' => '',
                    'col' => [
                        'span' => 12,
                    ],
                ],
                'is_menu|菜单可见' => [
                    'type' => 'radio',
                    'options' => [
                        0 => '否',
                        1 => '是',
                    ],
                    'default' => 1,
                    'col' => [
                        'span' => 12,
                    ],
                ],
                'is_scaffold|渲染方式' => [
                    'type' => 'radio',
                    'options' => [
                        1 => '脚手架',
                        0 => '自定义',
                    ],
                    'default' => 1,
                    'col' => [
                        'span' => 12,
                    ],
                    'compute' => [
                        'when' => ['=', 0],
                        'set' => [
                            'view' => [
                                'rule' => 'required',
                            ],
                        ],
                    ],
                ],
                'view|组件路径' => [
                    'rule' => 'string|max:50',
                    'default' => '',
                    'depend' => [
                        'field' => 'is_scaffold',
                        'value' => 0,
                    ],
                ],
                'scaffold_action|预置权限' => [
                    'type' => 'checkbox',
                    'virtual_field' => true,
                    'options' => function ($field, $data) {
                        $scaffold_permissions = [
                            'create' => '新建',
                            'edit' => '编辑',
                            'rowchange' => '行编辑',
                            'delete' => '删除',
                            'import' => '导入',
                            'export' => '导出'
                        ];
                        $options = [];
                        foreach ($scaffold_permissions as $key => $value) {
                            $options[] = [
                                'value' => $key,
                                'label' => $value,
                            ];
                        }
                        return $options;
                    },
                    'info' => '新增和编辑会创建/form或/:id的前端路由',
                    'depend' => [
                        'field' => 'is_scaffold',
                        'value' => 1,
                    ],
                ],
                'permission|权限标识' => [
                    'type' => 'select',
                    'default' => [],
                    'props' => [
                        'multiple' => true,
                        'selectApi' => '/system/routes?module={module}'
                    ],
                ],
                'pid|上级类目' => [
                    'rule' => 'array',
                    'type' => 'cascader',
                    'default' => [],
                    'options' => function ($field, $data) {
                        $module = request()->input('module', $data['module'] ?? 'system');
                        return (new MenuService())->tree([
                            'module' => $module,
                            'type' => [0, 1],
                        ]);
                    },
                    'props' => [
                        'style' => 'width: 100%;',
                        'clearable' => true,
                        'props' => [
                            'checkStrictly' => true,
                        ],
                    ],
                ],
                'roles|分配角色' => [
                    'rule' => 'array',
                    'type' => 'cascader',
                    'virtual_field' => true,
                    'props' => [
                        'style' => 'width: 100%;',
                        'props' => [
                            'multiple' => true,
                            'leaf' => 'leaf',
                            'emitPath' => false,
                            'checkStrictly' => true,
                        ],
                    ],
                    'render' => function ($field, &$data) {
                        $id = (int)request()->query('id', 0);
                        // $data['value'] = $this->authService->getMenuRoleIds($id);
                        //$data['options'] = $this->authService->getRoleTree();
                    },
                ],
            ],
            'table' => [
                'is_tree' => true,
                'tabs' => function () {
                    $system_module = ConfigService::getConfig('namespace');
                    $tabs = [];
                    foreach ($system_module as $key => $value) $tabs[] = ['label' => $value, 'value' => $key, 'icon' => ''];
                    return $tabs;
                },
                'rowActions' => [
                    [
                        'text' => '编辑',
                        'type' => 'form',
                        'target' => '/menu/form?id={id}',
                        'formUi' => [
                            'form' => [
                                'labelWidth' => '80px',
                                'size' => 'mini',
                            ],
                        ],
                        'props' => [
                            'type' => 'primary',
                        ],
                    ],
                    [
                        'text' => '加子菜单',
                        'type' => 'form',
                        'target' => '/menu/form?pid[]={id}&module={module}',
                        'formUi' => [
                            'form' => [
                                'labelWidth' => '80px',
                                'size' => 'mini',
                            ],
                        ],
                        'props' => [
                            'type' => 'success',
                        ],
                    ],
                    [
                        'text' => '删除',
                        'type' => 'api',
                        'target' => 'delete:/menu/{id}',
                        'props' => [
                            'type' => 'danger',
                        ],
                    ],
                ],
                'topActions' => [
                    [
                        'text' => '清除权限缓存',
                        'type' => 'api',
                        'target' => '/menu/permission/clear',
                        'props' => [
                            'icon' => 'el-icon-delete',
                            'type' => 'warning',
                        ],
                    ],
                    [
                        'text' => '公共资源',
                        'type' => 'jump',
                        'target' => '/cconf/cconf_permissions',
                        'props' => [
                            'icon' => 'el-icon-setting',
                            'type' => 'primary',
                        ],
                    ],
                    [
                        'text' => '新建',
                        'type' => 'form',
                        'target' => '/menu/form?module={tab_id}',
                        'formUi' => [
                            'form' => [
                                'labelWidth' => '80px',
                                'size' => 'mini',
                            ],
                        ],
                        'props' => [
                            'icon' => 'el-icon-plus',
                            'type' => 'success',
                        ],
                    ],
                ],
                'columns' => [
                    ['field' => 'id', 'hidden' => true],
                    ['field' => 'pid', 'hidden' => true],
                    ['field' => 'module', 'hidden' => true],
                    [
                        'field' => 'label',
                        'width' => '250px',
                    ],
                    [
                        'field' => 'is_menu',
                        'enum' => [
                            0 => 'info',
                            1 => 'success',
                        ],
                        'width' => '80px;',
                    ],
                    [
                        'field' => 'icon',
                        'type' => 'icon',
                        'width' => '80px;',
                    ],
                    'path',
                    'permission',
                    [
                        'field' => 'sort',
                        'edit' => true,
                        'width' => '170px;',
                    ],
                ],
            ],
            'order_by' => 'sort desc',
        ];

    }
}
