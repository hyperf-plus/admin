<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    规则验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/3/27
 */

namespace App\Validate;


use Mzh\Validate\Validate\Validate;

class AuthRuleValidation extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'rule_id' => 'integer|gt:0',
        'module' => 'require|checkModule',
        'group_id' => 'require|integer|gt:0',
        'name' => 'require|max:32',
        'menu_auth' => 'arrayHasOnlyInts',
        'log_auth' => 'arrayHasOnlyInts',
        'sort' => 'integer|between:0,255',
        'status' => 'in:0,1',
        'order_type' => 'in:asc,desc',
        'order_field' => 'in:rule_id,name,sort,status',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'rule_id' => '规则编号',
        'module' => '所属模块',
        'group_id' => '用户组编号',
        'name' => '规则名称',
        'menu_auth' => '菜单权限',
        'log_auth' => '记录权限',
        'sort' => '规则排序值',
        'status' => '规则状态',
        'order_type' => '排序方式',
        'order_field' => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set' => [
            'rule_id' => 'require|integer|gt:0',
            'module',
            'name',
            'menu_auth',
            'group_id',
            'log_auth',
            'sort',
            'status',
        ],
        'get' => [
            'rule_id' => 'require|integer|gt:0',
        ],
        'del' => [
            'rule_id' => 'require|arrayHasOnlyInts',
        ],
        'list' => [
            'module' => 'checkModule',
            'group_id' => 'integer|gt:0',
            'status',
            'order_type',
            'order_field',
        ],
        'status' => [
            'rule_id' => 'require|arrayHasOnlyInts',
            'status' => 'require|in:0,1',
        ],
        'sort' => [
            'rule_id' => 'require|integer|gt:0',
            'sort' => 'require|integer|between:0,255',
        ],
        'index' => [
            'rule_id' => 'require|arrayHasOnlyInts',
        ],
    ];


    /**
     * 验证模块是否在指定范围内
     * @access public
     * @param array $args 参数
     * @return bool
     */
    public function checkModule(...$args)
    {
        $moduleList = ['admin'=>'', 'api'=>'', 'home'=>''];
        if (!isset($moduleList[$args[0]])) {
            return sprintf('%s必须在 %s 范围内', $args[4], implode(',', array_keys($moduleList)));
        }
        return true;
    }

}
