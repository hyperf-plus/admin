<?php
namespace App\Controller\Api\v1;

use App\Controller\AdminBase;
use Hyperf\HttpServer\Annotation\AutoController;


/**
 * @AutoController()
 * Class Ads
 * @package App\Controller\Api\v1
 * */
class AuthRule extends AdminBase
{
    /**
     * 方法路由器
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        return [
            // 添加一条规则
            'add.auth.rule.item'   => ['addAuthRuleItem'],
            // 获取一条规则
            'get.auth.rule.item'   => ['getAuthRuleItem'],
            // 编辑一条规则
            'set.auth.rule.item'   => ['setAuthRuleItem'],
            // 批量删除规则
            'del.auth.rule.list'   => ['delAuthRuleList'],
            // 获取规则列表
            'get.auth.rule.list'   => ['getAuthRuleList'],
            // 批量设置规则状态
            'set.auth.rule.status' => ['setAuthRuleStatus'],
            // 设置规则排序
            'set.auth.rule.sort'   => ['setAuthRuleSort'],
            // 根据编号自动排序
            'set.auth.rule.index'  => ['setAuthRuleIndex'],
        ];
    }
}
