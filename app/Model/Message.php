<?php


namespace App\Model;

use App\Exception\AddException;
use Hyperf\DbConnection\Db;
use mysql_xdevapi\Exception;
use Symfony\Component\Mime\Exception\AddressEncoderException;

class Message extends BaseModel
{
    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'is_delete',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'message_id',
        'member',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $keyType = [
        'message_id' => 'integer',
        'type' => 'integer',
        'member' => 'integer',
        'page_views' => 'integer',
        'is_top' => 'integer',
        'status' => 'integer',
        'is_delete' => 'integer',
    ];

    /**
     * hasOne cs_message_user
     * @access public
     * @return mixed
     */
    public function getMessageUser()
    {
        return $this->hasOne('MessageUser', 'message_id');
    }

    /**
     * 添加一条消息
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addMessageItem($data)
    {
        if (!$this->validateData($data, 'Message')) {
            return false;
        }
        // 避免无关参数及初始化部分数据
        unset($data['message_id'], $data['page_views']);
        if (false !== $this->forceFill($data)->save()) {
            return $this->toArray();
        }
        throw new AddException('添加失败');
    }

    /**
     * 添加一条私有函(内部调用)
     * @access public
     * @param array $data 消息结构数据
     * @param array $clientId 账号编号
     * @param int $clientType 消息成员组 0=顾客组 1=管理组
     * @return bool
     * @throws
     */
    public function inAddMessageItem($data, $clientId, $clientType)
    {
        if (!$this->validateData($data, 'Message')) {
            return false;
        }
        // 避免无关参数及初始化部分数据
        $data['member'] = 0;
        unset($data['message_id'], $data['page_views']);
        // 开启事务
        self::beginTransaction();
        try {
            if (false === $this->forceFill($data)->save()) {
                throw new \Exception($this->getError());
            }
            $messageUserData = [];
            $clientType = $clientType == 0 ? 'user_id' : 'admin_id';
            foreach ($clientId as $value) {
                $messageUserData[] = [
                    'message_id' => $this->getAttribute('message_id'),
                    $clientType => $value,
                    'is_read' => 0,
                    'create_time' => time(),
                ];
            }
            $messageUserDb = new MessageUser();
            $messageUserDb->forceFill($messageUserData)->saveOrFail();
            self::commit();
            return true;
        } catch (\Exception $e) {
            self::rollback();
            throw new AddException($e->getMessage());
        }
    }

    /**
     * 编辑一条消息
     * @access public
     * @param array $data 外部数据
     * @return bool|void
     * @throws \Exception
     */
    public function setMessageItem($data)
    {
        if (!$this->validateSetData($data, 'Message.set')) {
            return false;
        }
        $result = self::where(function ($query) use ($data) {
            $map[] = ['message_id', '=', $data['message_id']];
            $map[] = ['member', '!=', 0];
            $map[] = ['is_delete', 'eq', 0];
            $query->where($map);
        })->get();

        if (!$result) {
            return is_null($result) ? $this->setError('消息不存在') : false;
        }

        if ($result->getAttribute('status') === 1) {
            throw new \Exception('消息已发布，不允许编辑！');
        }
        if (false != $result->forceFill($data)->save()) {
            return $result->toArray();
        }
        throw new \Exception('消息发送失败！');
    }

    /**
     * 批量删除消息
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delMessageList($data)
    {
        if (!$this->validateData($data, 'Message.del')) {
            return false;
        }

        $map['message_id'] = ['in', $data['message_id']];
        $map['member'] = ['neq', 0];
        $map['is_delete'] = ['eq', 0];

        if (false !== $this->save(['is_delete' => 1], $map)) {
            return true;
        }

        return false;
    }

    /**
     * 批量正式发布消息
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setMessageStatus($data)
    {
        if (!$this->validateData($data, 'Message.status')) {
            return false;
        }
        $map[] = ['message_id', 'in', $data['message_id']];
        $map[] = ['member', '!=', 0];
        $map[] = ['status', '=', 0];
        $map[] = ['is_delete', '=', 0];
        if (false != $this->forceFill(['status' => 1])->where($map)->save()) {
            return true;
        }
        return false;
    }

    /**
     * 获取一条消息(后台)
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getMessageItem($data)
    {
        if (!$this->validateData($data, 'Message.item')) {
            return false;
        }
        $result = self::where(function ($query) use ($data) {
            $map[] = ['message_id', '=', $data['message_id']];
            $map[] = ['member', '!=', 0];
            $map[] = ['is_delete', '=', 0];
            $query->where($map);
        })->get();
        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }
        return false;
    }

    /**
     * 用户获取一条消息
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getMessageUserItem($data)
    {
        if (!$this->validateData($data, 'Message.item')) {
            return false;
        }
        $result = self::where(function ($query) use ($data) {
            $map[] = ['message_id', '=', $data['message_id']];
            $map[] = ['status', '=', 1];
            $map[] = ['is_delete', '=', 0];
            $query->where($map);
        })->get();
        if (!$result) {
            return is_null($result) ? null : false;
        }
        // 验证是否有阅读权限
        $map[] = ['message_id','=', $result->getAttribute('message_id')];
        $map[] = [is_client_admin() ? 'admin_id' : 'user_id','=', get_client_id()];

        $userDb = new MessageUser();
        $userResult = (float)$userDb->where($map)->where('is_delete');

        switch ($result->getAttribute('member')) {
            case 0:
                $notReadable = $userResult === 1;
                break;
            case 1:
                $notReadable = is_client_admin() || $userResult === 1;
                break;
            case 2:
                $notReadable = !is_client_admin() || $userResult === 1;
                break;
            default:
                $notReadable = true;
        }

        if ($notReadable) {
            return null;
        }

        // 存在权限则需要插入记录与更新
        $result->setInc('page_views');
        $userDb->updateMessageUserItem($data['message_id']);

        return $result->toArray();
    }

    /**
     * 获取消息列表(后台)
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getMessageList($data)
    {
        if (!$this->validateData($data, 'Message.list')) {
            return false;
        }

        // 搜索条件
        is_empty_parm($data['type']) ?: $map['type'] = ['eq', $data['type']];
        empty($data['title']) ?: $map['title'] = ['like', '%' . $data['title'] . '%'];
        is_empty_parm($data['is_top']) ?: $map['is_top'] = ['eq', $data['is_top']];
        is_empty_parm($data['status']) ?: $map['status'] = ['eq', $data['status']];
        $map['member'] = !is_empty_parm($data['member']) ? ['eq', $data['member']] : ['neq', 0];
        $map['is_delete'] = ['eq', 0];

        $totalResult = $this->where($map)->count();
        if ($totalResult <= 0) {
            return ['total_result' => 0];
        }

        $result = self::all(function ($query) use ($data, $map) {
            // 翻页页数
            $pageNo = isset($data['page_no']) ? $data['page_no'] : 1;

            // 每页条数
            $pageSize = isset($data['page_size']) ? $data['page_size'] : config('paginate.list_rows');

            // 排序方式
            $orderType = !empty($data['order_type']) ? $data['order_type'] : 'desc';

            // 排序的字段
            $orderField = !empty($data['order_field']) ? $data['order_field'] : 'message_id';

            $query
                ->where($map)
                ->order([$orderField => $orderType])
                ->page($pageNo, $pageSize);
        });

        if (false !== $result) {
            return ['items' => $result->toArray(), 'total_result' => $totalResult];
        }

        return false;
    }

    /**
     * 用户获取未读消息数
     * @access public
     * @param array $data 外部数据
     * @return false|array
     * @throws
     */
    public function getMessageUserUnread($data)
    {
        $this->validateData($data, 'Message.unread');

        $clientId = 1;//get_client_id();
        if (1) {
            $member = '= 2';
            $clientType = 'admin_id';
            $createTime = Admin::where($clientType, $clientId)->value('create_time');
        } else {
            $member = '< 2';
            $clientType = 'user_id';
            $createTime = User::where($clientType, $clientId)->value('create_time');
        }

        $map = [
            ['m.status', '=', 1],
            ['m.is_delete', '=', 0],
            ['m.create_time', '>=', $createTime]
        ];
        if (!empty($data['type'])) array_push($map, ['m.type', '=', $data['type']]);
        // 联合查询语句
        $userWhere_1 = '`cs_u`.' . $clientType . ' IS NULL OR `cs_u`.' . $clientType . ' = ' . $clientId;
        $userWhere_2 = '`cs_u`.' . $clientType . ' IS NULL OR `cs_u`.is_delete = 0';
        $userWhere_3 = '`cs_u`.' . $clientType . ' IS NOT NULL OR `cs_m`.member ' . $member;
        $result = Db::table('message as m')
            ->select(['m.type', Db::raw('COUNT(*) AS total')])
            ->joinSub('SELECT * FROM `cs_message_user` WHERE `' . $clientType . '` = ' . $clientId, 'cs_u', 'u.message_id', '=', 'm.message_id', 'left')
            ->where(function ($query) use ($userWhere_1, $clientType, $clientId) {
                $query->whereRaw($userWhere_1);
            })
            ->where(function ($query) use ($userWhere_2) {
                $query->whereRaw($userWhere_2);
            })
            ->where(function ($query) use ($userWhere_3) {
                $query->whereRaw($userWhere_3);
            })
            ->where(function ($query) use ($clientType) {
                $query->whereRaw('`cs_u`.' . $clientType . ' IS NULL OR `cs_u`.is_read = 0');
            })
            ->where($map)
            ->groupBy('m.type')
            ->get();
        if (false !== $result) {
            $totalItem = [];
            foreach ($result as $key) {
                $totalItem[] = $key->total;
            }
            $total['total'] = array_sum($totalItem);
            return $total;
        }

        return false;
    }

    /**
     * 用户获取消息列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getMessageUserList($data)
    {
        if (!$this->validateData($data, 'Message.list')) {
            return false;
        }

        $clientId = get_client_id();
        if (is_client_admin()) {
            $member = '= 2';
            $clientType = 'admin_id';
            $createTime = AdminService::where([$clientType => ['eq', $clientId]])->value('create_time');
        } else {
            $member = '< 2';
            $clientType = 'user_id';
            $createTime = User::where([$clientType => ['eq', $clientId]])->value('create_time');
        }

        is_empty_parm($data['type']) ?: $map['m.type'] = ['eq', $data['type']];
        $map['m.status'] = ['eq', 1];
        $map['m.is_delete'] = ['eq', 0];
        $map['m.create_time'] = ['egt', $createTime];
        $mapRead = null;

        // 是否已读需要特殊对待
        if (!is_empty_parm($data['is_read'])) {
            switch ($data['is_read']) {
                case 0:
                    $mapRead = '`u`.' . $clientType . ' IS NULL OR `u`.is_read = 0';
                    break;

                case 1:
                    $mapRead = ['u.is_read' => ['eq', 1]];
                    break;
            }
        }

        // 构建子语句
        $userSQL = MessageUser::where([$clientType => ['eq', $clientId]])->buildSql();

        // 联合查询语句
        $userWhere_1 = '`u`.' . $clientType . ' IS NULL OR `u`.' . $clientType . ' = :' . $clientType;
        $userWhere_2 = '`u`.' . $clientType . ' IS NULL OR `u`.is_delete = 0';
        $userWhere_3 = '`u`.' . $clientType . ' IS NOT NULL OR `m`.member ' . $member;

        $totalResult = $this
            ->alias('m')
            ->join([$userSQL => 'u'], 'u.message_id = m.message_id', 'left')
            ->where($userWhere_1, [$clientType => [$clientId, \PDO::PARAM_INT]])
            ->where($userWhere_2)
            ->where($userWhere_3)
            ->where($mapRead)
            ->where($map)
            ->count();

        $message = ['total_result' => $totalResult];
        if (!empty($data['is_unread'])) {
            $unread = $this->getMessageUserUnread([]);
            if (false !== $unread) {
                $message['unread_count'] = $unread;
            }
        }

        if ($totalResult <= 0) {
            return $message;
        }

        // 翻页页数
        $pageNo = isset($data['page_no']) ? $data['page_no'] : 1;

        // 每页条数
        $pageSize = isset($data['page_size']) ? $data['page_size'] : config('paginate.list_rows');

        // 排序方式
        $orderType = !empty($data['order_type']) ? $data['order_type'] : 'desc';

        // 排序的字段
        $orderField = !empty($data['order_field']) ? $data['order_field'] : 'message_id';

        // 排序处理
        $order['m.is_top'] = 'desc';
        $order['m.' . $orderField] = $orderType;

        if (!empty($data['order_field'])) {
            $order = array_reverse($order);
        }

        $result = $this
            ->alias('m')
            ->field('m.message_id,m.type,m.title,m.url,m.is_top,m.target,ifnull(`u`.is_read, 0) is_read,m.create_time')
            ->join([$userSQL => 'u'], 'u.message_id = m.message_id', 'left')
            ->where($userWhere_1, [$clientType => [$clientId, \PDO::PARAM_INT]])
            ->where($userWhere_2)
            ->where($userWhere_3)
            ->where($mapRead)
            ->where($map)
            ->order($order)
            ->page($pageNo, $pageSize)
            ->select();

        if (false !== $result) {
            $message['items'] = $result->toArray();
            return $message;
        }

        return false;
    }
}
