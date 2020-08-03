<?php
declare(strict_types=1);

namespace Mzh\Admin\Views;

use Hyperf\Di\Annotation\Inject;
use Mzh\Admin\Model\AuthRule;
use Mzh\Admin\Service\AuthService;

class AuthRuleView implements UiViewInterface
{
    /**
     * @Inject()
     * @var AuthService $authService
     */
    protected $authService;

    public function scaffoldOptions()
    {
        return [
            'createAble' => true,
            'deleteAble' => true,
            'importAble' => true,
            'filter' => ['name'],
            'where' => [
                'pid' => 0,
            ],
            'form' => [
                'id' => 'int',
                'name|名称' => [
                    'rule' => 'required|max:20',
                    'type' => 'input',
                    'props' => [
                        'size' => 'small',
                        'maxlength' => 20,
                    ],
                ],
                'pid|上级角色' => [
                    'rule' => 'int',
                    'type' => 'select',
                    'info' => '没有上级角色则为一级角色',
                    'default' => 0,
                    'props' => [
                        'multipleLimit' => 1,
                    ],
                    'options' => function ($field, &$data) {
                        $options = AuthRule::query()->where('pid', 0)->get(['id as value', 'name as label'])->toArray();
                        array_unshift($options, ['value' => 0, 'label' => '无']);
                        return $options;
                    },
                ],
                'sort|排序' => [
                    'rule' => 'int',
                    'type' => 'number',
                    'default' => 0,
                ],
                'menu_auth|权限设置' => [
                    'rule' => 'Array',
                    'type' => 'el-cascader-panel',
                    'virtual_field' => true,
                    'props' => [
                        'style' => 'height:500px;',
                        'props' => [
                            'multiple' => true,
                            'leaf' => 'leaf',
                            'checkStrictly' => false,
                        ],
                    ],
                    'render' => function ($field, &$data) {
                        $id = (int)request()->query('id', 0);
                        [
                            $data['value'],
                            $data['props']['options'],
                        ] = $this->authService->getPermissionOptions($id);
                    },
                ],
                'user_ids|授权用户' => [
                    'type' => 'select',
                    'props' => [
                        'multiple' => true,
                        'selectApi' => '/user/act',
                        'remote' => true,
                    ],
                    'virtual_field' => true,
                    'render' => function ($field, &$data) {
                        $id = (int)request()->query('id', 0);
                        //          $data['value'] = $this->permission_service->getRoleUserIds($id);
//                        if (!empty($data['value'])) {
//                            $data['options'] = select_options($data['props']['selectApi'], $data['value']);
//                        }
                    },
                ],
            ],
            'table' => [
                'columns' => [
                    ['field' => 'id', 'hidden' => true],
                    ['field' => 'pid', 'hidden' => true],
                    'name',
                    [
                        'field' => 'sort',
                        'edit' => true,
                    ],
                    [
                        'field' => 'module',
                        'title' => '所属模块'
                    ],
                    [
                        'field' => 'status',
                        'title' => '状态',
                        'sortable' => true,
                        'options' => [
                            0 => '已禁用',
                            1 => '启用',
                        ],
                        'enum' => [
                            0 => 'info',
                            1 => 'success',
                        ]
                    ]
                ],
                'rowActions' => [
                    [
                        'action' => '/auth_rule/form?id={id}',
                        'text' => '编辑',
                        "type" => "drawer"
                    ],
                    [
                        'action' => 'api',
                        'api' => 'delete:/auth_rule/{id}',
                        'text' => '删除',
                        'type' => 'danger',
                    ]
                ],
            ],
            'order_by' => 'pid asc, sort desc',
        ];
    }
}
