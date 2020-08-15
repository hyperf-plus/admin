<?php

return [
    'enable' => true,
    'password' => [
        'salt' => '#$455admin!@#!2hlgy'
    ],
    'scaffold_permissions'=>[
        'list' => [
            'label' => '列表',
            'type' => 2,
            'permission' => 'GET::/*/info,GET::/*/list,GET::/*/childs/{id:\d+},GET::/*/act,GET::/*/notice',
        ],
        'create' =>[
            'label' => '新建',
            'path' => '/form',
            'type' => 1,
            'permission' =>'GET::/*/form,GET::/*/form.json,POST::/*/form',
        ],
        'edit' =>[
            'label' => '编辑',
            'path' => '/:id',
            'type' => 1,
            'permission' => 'GET::/*/{id:\d+},GET::/*/{id:\d+},PUT::/*/{id:\d+}',
        ],
        'rowchange' =>[
            'label' => '行编辑',
            'type' => 2,
            'permission' => 'POST::/*/rowchange/{id:\d+}'
        ],
        'delete' => [
            'label' => '删除',
            'type' => 2,
            'permission' => 'DELETE::/*/{id:\d+}',
        ],
        'import' => [
            'label' => '导入',
            'type' => 2,
            'permission' => 'POST::/*/import',
        ],
        'export' => [
            'label' => '导出',
            'type' => 2,
            'permission' => 'POST::/*/export',
        ],

    ]
];
