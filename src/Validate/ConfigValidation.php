<?php
declare(strict_types=1);

namespace Mzh\Admin\Validate;

use Mzh\Validate\Validate\Validate;

class ConfigValidation extends Validate
{
    protected $rule = [
        'name' => 'required|unique:config,name',
        'namespace' => 'require',
        'title' => 'max:50',
        'remark' => 'max:255',
        'rules' => 'max:1000',
        'value' => 'array',
    ];


    protected $field = [
        'name' => '名称',
        'namespace' => '命名空间',
        'title' => '标题',
        'remark' => '备注信息',
        'rules' => '规则',
        'value' => '配置值'
    ];

    protected $scene = [
        'create' => ['name', 'namespace', 'title', 'remark', 'rules', 'value']


    ];
}