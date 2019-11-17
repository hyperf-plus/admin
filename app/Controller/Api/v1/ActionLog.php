<?php
declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Controller\AdminBase;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * 订单管理
 * Class OrderController
 * @AutoController()
 * @package App\Controller\Order
 */
class ActionLog extends AdminBase
{

    /**
     * 方法路由器
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        return [
            // 获取一条操作日志
            'get.action.log.item' => ['getActionLogItem'],
            // 获取操作日志列表
            'get.action.log.list' => ['getActionLogList'],
        ];
    }
}
