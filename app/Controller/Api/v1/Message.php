<?php

namespace App\Controller\Api\v1;

use App\Controller\AdminBase;
use App\Exception\StatusException;
use App\Exception\SystemException;
use App\Model\Admin;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 * Class Message
 * @package app\api\controller\v1
 */
class Message extends AdminBase
{

    /**
     * 方法路由器
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        return [
            // 添加一条消息
            'add.message.item' => ['addMessageItem'],
            // 编辑一条消息
            'set.message.item' => ['setMessageItem'],
            // 批量删除消息
            'del.message.list' => ['delMessageList'],
            // 批量正式发布消息
            'set.message.status' => ['setMessageStatus'],
            // 获取一条消息(后台)
            'get.message.item' => ['getMessageItem'],
            // 获取消息列表(后台)
            'get.message.list' => ['getMessageList'],
            // 用户获取一条消息
            'get.message.user.item' => ['getMessageUserItem'],
            // 用户获取消息列表
            'get.message.user.list' => ['getMessageUserList'],
            // 用户获取未读消息数
            'get.message.user.unread' => ['getMessageUserUnread'],
            // 用户批量设置消息已读
            'set.message.user.read' => ['setMessageUserRead', 'app\common\model\MessageUser'],
            // 用户设置消息全部已读
            'set.message.user.allread' => ['setMessageUserAllRead', 'app\common\model\MessageUser'],
            // 用户批量删除消息
            'del.message.user.list' => ['delMessageUserList', 'app\common\model\MessageUser'],
            // 用户删除全部消息
            'del.message.user.all' => ['delMessageUserAll', 'app\common\model\MessageUser'],
        ];
    }
}
