<?php


namespace App\Model;

class MessageUser extends BaseModel
{
    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 更新日期字段
     * @var bool/string
     */
    protected $updateTime = false;

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'message_user_id',
        'message_id',
        'user_id',
        'admin_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $keyType = [
        'message_user_id' => 'integer',
        'message_id' => 'integer',
        'user_id' => 'integer',
        'admin_id' => 'integer',
        'is_read' => 'integer',
        'is_delete' => 'integer',
    ];

    /**
     * 插入记录或更新单条记录,并设为已读状态
     * @access public
     * @param int $messageId 消息编号
     * @return bool
     * @throws
     */
    public function updateMessageUserItem($messageId)
    {
        $clientType = is_client_admin() ? 'admin_id' : 'user_id';
        $map['message_id'] = ['eq', $messageId];
        $map[$clientType] = ['eq', get_client_id()];

        $result = $this->where($map)->find();
        if (false === $result) {
            return false;
        }

        // 存在则更新为已读
        if ($result) {
            $result->save(['is_read' => 1]);
            return true;
        }

        $data = ['message_id' => $messageId, $clientType => get_client_id(), 'is_read' => 1];
        return $this->allowField(true)->isUpdate(false)->save($data);
    }

    /**
     * 批量插入记录或更新记录
     * @access public
     * @param array $messageId 消息编号
     * @param string $field 字段
     * @param bool $isAll 是否操作所有
     * @return bool
     */
    private function updateMessageUserList($messageId, $field, $isAll = false)
    {
        // 获取已存在的消息
        $clientType = is_client_admin() ? 'admin_id' : 'user_id';
        $map['message_id'] = ['in', $messageId];
        $map[$clientType] = ['eq', get_client_id()];
        $unreadList = $this->where($map)->column('message_id');

        // 补齐不存在记录
        $notExistsId = array_diff($messageId, $unreadList);
        if (!empty($notExistsId)) {
            $dataUser = null;
            foreach ($notExistsId as $item) {
                $dataUser[] = [
                    'message_id' => $item,
                    $clientType => get_client_id(),
                    $field => 1,
                    'create_time' => time(),
                ];
            }

            if (false === $this->isUpdate(false)->insertAll($dataUser)) {
                return false;
            }
        }

        // 更新已存在记录
        if (true === $isAll) {
            $mapAll[$clientType] = ['eq', get_client_id()];
            if (false === self::update([$field => 1], $mapAll)) {
                return false;
            }
        } else {
            $existsId = array_intersect($messageId, $unreadList);
            if (!empty($existsId)) {
                $map['message_id'] = ['in', $existsId];
                if (false === self::update([$field => 1], $map)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 用户批量设置消息已读
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setMessageUserRead($data)
    {
        if (!$this->validateData($data, 'Message.user')) {
            return false;
        }

        $map['message_id'] = ['in', $data['message_id']];
        !isset($data['type']) ?: $map['type'] = ['eq', $data['type']];
        $map['member'] = ['eq', is_client_admin() ? 2 : 1];
        $map['status'] = ['eq', 1];
        $map['is_delete'] = ['eq', 0];

        $messageId = Message::where($map)->column('message_id');
        if (empty($messageId)) {
            return true;
        }

        return $this->updateMessageUserList($messageId, 'is_read');
    }

    /**
     * 用户设置消息全部已读
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setMessageUserAllRead($data)
    {
        if (!$this->validateData($data, 'Message.unread')) {
            return false;
        }

        !isset($data['type']) ?: $map['type'] = ['eq', $data['type']];
        $map['member'] = ['eq', is_client_admin() ? 2 : 1];
        $map['status'] = ['eq', 1];
        $map['is_delete'] = ['eq', 0];

        $messageId = Message::where($map)->column('message_id');
        if (empty($messageId)) {
            return true;
        }

        return $this->updateMessageUserList($messageId, 'is_read', true);
    }

    /**
     * 用户批量删除消息
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delMessageUserList($data)
    {
        if (!$this->validateData($data, 'Message.user')) {
            return false;
        }

        $map['message_id'] = ['in', $data['message_id']];
        !isset($data['type']) ?: $map['type'] = ['eq', $data['type']];
        $map['member'] = ['eq', is_client_admin() ? 2 : 1];
        $map['status'] = ['eq', 1];
        $map['is_delete'] = ['eq', 0];

        $messageId = Message::where($map)->column('message_id');
        if (empty($messageId)) {
            return true;
        }

        return $this->updateMessageUserList($messageId, 'is_delete');
    }

    /**
     * 用户删除全部消息
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delMessageUserAll($data)
    {
        if (!$this->validateData($data, 'Message.unread')) {
            return false;
        }

        !isset($data['type']) ?: $map['type'] = ['eq', $data['type']];
        $map['member'] = ['eq', is_client_admin() ? 2 : 1];
        $map['status'] = ['eq', 1];
        $map['is_delete'] = ['eq', 0];

        $messageId = Message::where($map)->column('message_id');
        if (empty($messageId)) {
            return true;
        }

        return $this->updateMessageUserList($messageId, 'is_delete', true);
    }
}
