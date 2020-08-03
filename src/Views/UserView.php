<?php 
declare(strict_types = 1);
namespace Mzh\Admin\Views;

class UserView implements UiViewInterface
{
    public function scaffoldOptions()
    {
        return [
            'form' => [
                'id|#' => '',
                'username|用户名' => '',
                'realname' => '',
                'password' => '',
                'mobile' => '',
                'email' => '',
                'status' => '',
                'login_time' => '',
                'login_ip' => '',
                'is_admin|is admin' => '',
                'is_default_pass|是否初始密码1:是,0:否' => '',
                'qq|用户qq' => '',
                'roles' => '',
                'sign|签名' => '',
                'avatar' => '',
                'avatar_small' => '',
            ],
            'table' => [
                'rowActions' => [
                    [
                        'type' => 'jump',
                        'target' => '/user/{id}',
                        'text' => '编辑',
                    ],
                    [
                        'type' => 'api',
                        'target' => '/user/delete',
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
