<?php
declare(strict_types=1);

namespace Mzh\Admin\Views;

use Mzh\Admin\Service\AuthService;

class UserView implements UiViewInterface
{
    public function scaffoldOptions()
    {
        return [
            'createAble' => true,
            'deleteAble' => true,
            'defaultList' => true,
            'filter' => ['realname%', 'username%', 'create_at'],
            'form' => [
                'id' => 'int',
                'username|登录账号' => [
                    'rule' => 'required',
                    'readonly' => true,
                ],
                'avatar|头像' => [
                    'type' => 'image',
                    'rule' => 'string',
                ],
                'realname|昵称' => '',
                'mobile|手机' => '',
                'email|邮箱' => 'email',
                'sign|签名' => '',
                'pwd|密码' => [
                    'virtual_field' => true,
                    'default' => '',
                    'props' => [
                        'size' => 'small',
                        'maxlength' => 20,
                    ],
                    'info' => '若设置, 将会更新用户密码'
                ],
                'status|状态' => [
                    'rule' => 'required',
                    'type' => 'radio',
                    'options' => [
                        0 => '禁用',
                        1 => '启用',
                    ],
                    'default' => 0,
                ],
                'is_admin|类型' => [
                    'rule' => 'int',
                    'type' => 'radio',
                    'options' => [
                        0 => '普通管理员',
                        1 => '超级管理员',
                    ],
                    'info' => '普通管理员需要分配角色才能访问角色对应的资源；超级管理员可以访问全部资源',
                    'default' => 0,
                    'render' => function ($field, &$rule) {
//                        if ($this->auth_service->isSupperAdmin()) {
//                            $rule['type'] = 'hidden';
//                        }
                    },
                ],
                'role_ids|角色' => [
                    'rule' => 'array',
                    'type' => 'el-cascader-panel',
                    'virtual_field' => true,
                    'props' => [
                        'props' => [
                            'multiple' => true,
                            'leaf' => 'leaf',
                            'emitPath' => false,
                            'checkStrictly' => true,
                        ],
                    ],
                    'render' => function ($field, &$data) {
                        //$id = (int)$this->request->route('id', 0);
                        $service = make(AuthService::class);
                       $data['value'] = $service->getUserRoleIds(getUserInfo()->getUserId());
                        $data['props']['options'] =$service->getRoleTree();
                    },
                ],
                'create_at|创建时间' => [
                    'form' => false,
                    'type' => 'date_range',
                ],
            ],
            'hasOne' => [
                'hyperf_admin.hyperf_admin.user_role:user_id,role_id',
            ],
            'table' => [
                'columns' => [
                    'id',
                    ['field' => 'avatar', 'render' => 'avatarRender'],
                    'realname',
                    'username',
                    [
                        'field' => 'mobile',
                        'render' => function ($field, $row) {
                            return data_desensitization($row['value'], 3, 4);
                        },
                    ],
                    'email',
                    [
                        'field' => 'status',
                        'enum' => [
                            0 => 'info',
                            1 => 'success',
                        ],
                    ],
                    [
                        'field' => 'role_id',
                        'title' => '权限',
                        'virtual_field' => true,
                    ],
                ],
                'rowActions' => [
                    ['action' => '/user/form?id={id}', 'text' => '编辑',],
                    [
                        'action' => 'api',
                        'api' => 'delete:/user/{id}',
                        'text' => '删除',
                        'type' => 'danger',
                    ]
                ],
            ],
        ];
    }
}
