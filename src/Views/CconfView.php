<?php
declare(strict_types=1);

namespace Mzh\Admin\Views;

use Mzh\Admin\Model\Config;

class CconfView implements UiViewInterface
{
    public function scaffoldOptions()
    {
        return [
            'filter' => ['name%'],
            'form' => [
                'id|#' => '',
                'namespace|命名空间' => [
                    'rule' => 'required',
                    'type' => 'select',
                    'options' => function ($field, $data) {
                        $namespaces = Config::query()->where(['name' => 'namespace'])->value('value');
                        $options = [];
                        foreach ($namespaces as $value => $label) {
                            $options[] = [
                                'value' => $value,
                                'label' => $label,
                            ];
                        }
                        return $options;
                    },
                    'default' => 'common',
                ],
                'name|名称' => [
                    'rule' => 'required|unique:config,name',
                    'readonly' => true,
                ],
                'title|可读名称' => [
                    'rule' => 'required',
                ],
                'rules|规则' => [
                    'type' => 'json',
                    'rule' => 'json',
                    'depend' => [
                        'field' => 'is_need_form',
                        'value' => 1,
                    ],
                ],
                'remark|备注' => 'max:100',
                'is_need_form|是否使用表单' => [
                    'rule' => 'integer',
                    'type' => 'radio',
                    'options' => [
                        0 => '否',
                        1 => '是',
                    ],
                    'default' => 1,
                ],
                'value|配置值' => [
                    'type' => 'json',
                    'rule' => 'json',
                    'depend' => [
                        'field' => 'is_need_form',
                        'value' => 0,
                    ],
                ],
            ],
            'table' => [
                'columns' => [
                    'id',
                    [
                        'field' => 'namespace',
                        'enum' => [
                            'default' => '通用',
                        ],
                    ],
                    'name',
                    'title',
                    [
                        'field' => 'is_need_form',
                        'hidden' => true,
                    ],
                ],
                'rowActions' => [
                    ['action' => '/cconf/form?id={id}', 'text' => '编辑'],
                    [
                        'action' => '/cconf/cconf_{name}',
                        'text' => '表单',
                        'when' => [
                            ['is_need_form', '=', 1],
                        ],
                    ],
                ],
            ],
        ];
    }
}
