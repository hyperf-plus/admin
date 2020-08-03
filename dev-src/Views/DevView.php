<?php

namespace Mzh\DevTools\Views;

use Hyperf\Utils\Str;

class DevView
{

    public function rules()
    {
        return [
            'pool|连接池' => [
                'type' => 'select',
                'rule' => 'required',
                'options' => function ($field, $biz) {
                    $pools = array_keys(config('databases'));
                    $options = [];
                    foreach ($pools as $pool) {
                        if (Str::startsWith($pool, '_')) {
                            continue;
                        }
                        $options[] = [
                            'value' => $pool,
                            'label' => $pool,
                        ];
                    }

                    return $options;
                },
                'col' => [
                    'span' => 8,
                ],
            ],
            'database|数据库' => [
                'type' => 'select',
                'rule' => 'required',
                'props' => [
                    'selectApi' => '/dev/dbAct?pool={pool}',
                ],
                'col' => [
                    'span' => 8,
                ],
            ],
            'make_type|模块名称' => [
                'type' => 'hidden',
                'rule' => 'required',
                'default' => 'maker'
            ],
            'table|表' => [
                'type' => 'select',
                'rule' => 'required',
                'props' => [
                    'selectApi' => '/dev/tableAct?pool={pool}&db={database}',
                ],
                'col' => [
                    'span' => 8,
                ],
            ],
            'model_path|Model路径' => [
                'rule' => 'required',
                'default' => 'app/Model',
                'col' => [
                    'span' => 12,
                ],
            ],
            'controller_path|Controller路径' => [
                'rule' => 'required',
                'default' => 'app/Controller/Api/V1',
                'col' => [
                    'span' => 12,
                ],
            ],
            'view_path|View配置路径' => [
                'rule' => 'required',
                'default' => 'app/View',
                'col' => [
                    'span' => 12,
                ],
            ],
            'validate_path|验证器路径' => [
                'rule' => 'required',
                'default' => 'app/Validate',
                'col' => [
                    'span' => 12,
                ],
            ],

            'base_init|UI初始化' => [
                'type' => 'checkbox',
                'options' => [
                    'createAble' => '创建按钮',
                    'exportAble' => '导出按钮',
                    'deleteAble' => '允许删除',
                    'defaultList' => '首屏列表默认查询',
                    'filterSyncToQuery' => '筛选同步URL',
                    'editButton' => '行编辑按钮',
                    'deleteButton' => '行删除按钮',
                ],
                'default' => [
                    'createAble',
                    'exportAble',
                    'deleteAble',
                    'defaultList',
                    'filterSyncToQuery',
                    'editButton',
                    'deleteButton',
                ],
            ],

            'api_init|接口初始化' => [
                'type' => 'checkbox',
                'options' => [
                    'GetApiUI' => '创建前端渲染接口',
                    'GetApiList' => '列表接口',
                    'GetApiCreate' => '新增接口',
                    'GetApiUpdate' => '修改接口',
                    'GetApiSort' => '排序接口',
                    'GetApiState' => '状态修改接口',
                    'GetApiDelete' => '删除接口',
                    'GetApiBatchDel' => '批量删除接口',
                    'GetApiRowChange' => '行修改接口',
                ],
                'info' => '选择UI界面则会生成对应UI界面相应接口',
                'default' => [
                    'GetApiUI',
                    'GetApiList',
                    'GetApiCreate',
                    'GetApiUpdate',
                    'GetApiSort',
                    'GetApiState',
                    'GetApiDelete',
                    'GetApiBatchDel',
                    'GetApiRowChange'
                ]
            ],

            'init_hooks|钩子函数初始化' => [
                'type' => 'checkbox',
                'options' => [
                    '_list_before' => '查询列表前',
                    '_list_after' => '查询列表后',
                    'meddleFormRule' => 'UI表单生成前',
                    'beforeFormResponse' => 'UI表单生成后',
                    '_update_before' => '更新前',
                    '_update_after' => '更新后',
                    '_create_before' => '创建前',
                    '_create_after' => '创建后',
                    '_sort_before' => '排序前',
                    '_delete_before' => '删除前',
                    '_delete_after' => '删除后',
                    '_state_before' => '状态更新前',
                    '_state_after' => '状态更新后',
                ],
                'props' => [
                    'multiple' => true,
                ],
                'info' => '钩子函数的具体含义请查看文档',
                'default' => [],
            ],
            'form|表单' => [
                'type' => 'sub-form',
                'rule' => 'required',
                'children' => [
                    'field|字段名' => [
                        'rule' => 'required',
                        'col' => [
                            'span' => 8,
                        ],
                    ],
                    'label|字段中文名' => [
                        'col' => [
                            'span' => 8,
                        ],
                    ],
                    'type|字段类型' => [
                        'type' => 'select',
                        'options' => [
                            'input' => 'input',
                            'hidden' => 'hidden',
                            'select' => 'select',
                            'number' => 'number',
                            'float' => 'float',
                            'radio' => 'radio',
                            'switch' => 'switch',
                            'date' => 'date',
                            'date_time' => 'date_time',
                            'date_range' => 'date_range',
                            'datetime_range' => 'datetime_range',
                            'image' => 'image',
                            'file' => 'file',
                        ],
                        'default' => 'input',
                        'col' => [
                            'span' => 8,
                        ],
                    ],
                    //'options' => [
                    //    'type' => 'json',
                    //    'depend' => [
                    //        'field' => 'type',
                    //        'value' => ['select', 'checkbox', 'radio']
                    //    ]
                    //],
                    'rule|校验规则' => [
                        'type' => 'select',
                        'props' => [
                            'multiple' => true,
                        ],
                        'options' => [
                            'require' => '必填',
                            'integer' => '只能是数字',
                            'alpha' => '只能是字母',
                            'chs' => '只能是汉字',
                            'alphaDash' => '只能是字母、数字和下划线_及破折号-',
                            'alphaNum' => '只能是字母和数字',
                            'array' => '数组',
                            'ip' => '必须是ip',
                            'url' => '必须是网址',
                            'intOrArrayInt' => '只允许为int类型或array(int,int)',
                        ],
                        'col' => [
                            'span' => 8,
                        ],
                    ],
                    'default|默认值' => [
                        'col' => [
                            'span' => 8,
                        ],
                    ],
                    'virtual_field|虚拟字段' => [
                        'type' => 'radio',
                        'options' => [
                            0 => '否',
                            1 => '是',
                        ],
                        'col' => [
                            'span' => 8,
                        ],
                        'default' => 0,
                    ],
                    'props' => [
                        'type' => 'json',
                    ],
                ],
                'repeat' => true,
                'props' => [
                    'sort' => true,
                ],
            ],
        ];
    }


    public function validate()
    {
        return [
            'pool|连接池' => [
                'type' => 'select',
                'rule' => '',
                'options' => function ($field, $biz) {
                    $pools = array_keys(config('databases'));
                    $options = [];
                    foreach ($pools as $pool) {
                        if (Str::startsWith($pool, '_')) {
                            continue;
                        }
                        $options[] = [
                            'value' => $pool,
                            'label' => $pool,
                        ];
                    }

                    return $options;
                },
                'col' => [
                    'span' => 8,
                ],
            ],
            'database|数据库' => [
                'type' => 'select',
                'rule' => '',
                'props' => [
                    'selectApi' => '/dev/dbAct?pool={pool}',
                ],
                'col' => [
                    'span' => 8,
                ],
            ],
            'table|表' => [
                'type' => 'select',
                'rule' => '',
                'props' => [
                    'selectApi' => '/dev/tableAct?pool={pool}&db={database}',
                ],
                'col' => [
                    'span' => 8,
                ],
            ],
            'validate_path|验证器路径' => [
                'rule' => 'required',
                'default' => 'app/Validate',
                'col' => [
                    'span' => 8,
                ],
            ],
            'validate_name|验证器名称' => [
                'rule' => 'required',
                'default' => '',
                'col' => [
                    'span' => 8,
                ],
            ],
            'make_type|模块名称' => [
                'type' => 'hidden',
                'rule' => 'required',
                'default' => 'validate'
            ],
            'form|验证字段' => [
                'type' => 'sub-form',
                'rule' => 'required',
                'children' => [
                    'field|字段名' => [
                        'rule' => 'required',
                        'col' => [
                            'span' => 8,
                        ],
                    ],
                    'label|字段中文名' => [
                        'col' => [
                            'span' => 8,
                        ],
                    ],
                    'type|字段类型' => [
                        'type' => 'select',
                        'options' => [
                            'string' => '字符串',
                            'integer' => '数字',
                            'alpha' => '字母',
                            'chs' => '汉字',
                            'array' => '数组',
                            'ip' => '数组',
                            'datetime' => '时间',
                            'file' => 'file',
                        ],
                        'default' => 'string',
                        'col' => [
                            'span' => 8,
                        ],
                    ],
                    //'options' => [
                    //    'type' => 'json',
                    //    'depend' => [
                    //        'field' => 'type',
                    //        'value' => ['select', 'checkbox', 'radio']
                    //    ]
                    //],
                    'rule|校验规则' => [
                        'type' => 'select',
                        'props' => [
                            'multiple' => true,
                        ],
                        'options' => [
                            'require' => '必填',
                            'integer' => '只能是数字',
                            'alpha' => '只能是字母',
                            'chs' => '只能是汉字',
                            'alphaDash' => '只能是字母、数字和下划线_及破折号-',
                            'alphaNum' => '只能是字母和数字',
                            'array' => '数组',
                            'ip' => '必须是ip',
                            'url' => '必须是网址',
                            'intOrArrayInt' => '只允许为int类型或array(int,int)',
                        ],
                        'col' => [
                            'span' => 8,
                        ],
                    ],
                    'diy_rule|自定义规则' => [
                        'col' => [
                            'span' => 16,
                        ],
                        "props" => [
                            "placeholder" => "例如： in:0,1|max:255 多个之间用｜分割"

                        ]
                    ]
                ],
                'repeat' => true,
                'props' => [
                    'sort' => true,
                ],
            ],
        ];
    }

    public function controller()
    {
        return [
            'controller_name|控制器名称' => [
                'rule' => '',
                'default' => '',
                'col' => [
                    'span' => 12,
                ]
            ],
            'controller_path|Controller路径' => [
                'rule' => 'required',
                'default' => 'app/Controller/Api',
                'col' => [
                    'span' => 12,
                ],
            ],
            'view_path|View配置路径' => [
                'rule' => '',
                'default' => 'app/View',
                'col' => [
                    'span' => 12,
                ],
                'info' => '如果只填写路径，默认会注入与控制器同名的View（如果存在的话）。',
            ],
            'validate_path|验证器路径' => [
                'rule' => '',
                'default' => 'app/Validate',
                'col' => [
                    'span' => 12,
                ],
                'info' => '如果只填写路径，默认会注入与控制器同名的验证器（如果存在的话）。',
                "depend" => [
                    "field" => "controller_name", // 依赖字段
                    "value" => '1' // 当 field_1 = 1 时 field_2 此项才会显示
                ]
            ],
            'swagger_name|模块名称' => [
                'rule' => '',
                'default' => '',
                'info' => 'Swagger注解显示模块名',
                'col' => [
                    'span' => 12,
                ],
            ],
            'make_type|模块名称' => [
                'type' => 'hidden',
                'rule' => 'required',
                'default' => 'controller'
            ],
            'api_init|接口初始化' => [
                'type' => 'checkbox',
                'options' => [
                    'GetApiUI' => '创建前端渲染接口',
                    'GetApiList' => '列表接口',
                    'GetApiCreate' => '新增接口',
                    'GetApiUpdate' => '修改接口',
                    'GetApiSort' => '排序接口',
                    'GetApiState' => '状态修改接口',
                    'GetApiDelete' => '删除接口',
                    'GetApiBatchDel' => '批量删除接口',
                    'GetApiRowChange' => '行修改接口',
                ],
                'info' => '选择UI界面则会生成对应UI界面相应接口',
                'default' => [
                    'GetApiUI',
                    'GetApiList',
                    'GetApiCreate',
                    'GetApiUpdate',
                    'GetApiSort',
                    'GetApiState',
                    'GetApiDelete',
                    'GetApiBatchDel',
                    'GetApiRowChange'
                ]
            ],
            'init_hooks|钩子函数初始化' => [
                'type' => 'checkbox',
                'options' => [
                    '_list_before' => '查询列表前',
                    '_list_after' => '查询列表后',
                    'meddleFormRule' => 'UI表单生成前',
                    'beforeFormResponse' => 'UI表单生成后',
                    '_update_before' => '更新前',
                    '_update_after' => '更新后',
                    '_create_before' => '创建前',
                    '_create_after' => '创建后',
                    '_sort_before' => '排序前',
                    '_delete_before' => '删除前',
                    '_delete_after' => '删除后',
                    '_state_before' => '状态更新前',
                    '_state_after' => '状态更新后',
                ],
                'props' => [
                    'multiple' => true,
                ],
                'info' => '钩子函数的具体含义请查看文档',
                'default' => [],
            ]
        ];
    }

}