<?php
declare(strict_types=1);

namespace App\Service;


use App\Constants\ClientConstants;
use App\Model\Admin;
use App\Model\Message;
use App\Model\MessageUser;
use App\Model\User;
use Exception;
use Hyperf\Database\Query\Builder;
use Hyperf\DbConnection\Db;
use Mzh\Validate\Annotations\Validation;

class MessageService
{


    /**
     * @param null $type
     * @param int $clientId
     * @return array
     * @throws Exception
     */
    public function unread($type = null, int $clientId = 0)
    {
        if ($clientId == 0) {
            $clientId = getUserInfo()->getUid();
        }

        switch (getUserInfo()->getType()) {
            case ClientConstants::TYPE_ADMIN:
                $member = '= 2';
                $clientType = 'admin_id';
                $createTime = Admin::query()->where('admin_id', $clientId)->value('create_time') ?? 0;
                break;

            case ClientConstants::TYPE_USER:
                $member = '< 2';
                $clientType = 'user_id';
                $createTime = User::query()->where('user_id', $clientId)->select(['create_time'])->value('create_time') ?? 0;
                break;
            default:
                throw new \Exception('不支持该会员类型');
        }
        $map = [
            ['m.status', '=', 1],
            ['m.is_delete', '=', 0],
            ['m.create_time', '>=', ($createTime)]
        ];
        if (!empty($type)) array_push($map, ['m.type', '=', $type]);
        // 联合查询语句
        $result = Db::table('message as m')
            ->select(['m.type', Db::raw('COUNT(*) AS total')])
            ->joinSub('SELECT * FROM `cs_message_user` WHERE `' . $clientType . '` = ' . $clientId, 'cs_u', 'u.message_id', '=', 'm.message_id', 'left')
            ->where(function (Builder $query) use ($clientType, $clientId) {
                $query->whereRaw('`cs_u`.' . $clientType . ' IS NULL OR `cs_u`.' . $clientType . ' = ' . $clientId);
            })
            ->where(function (Builder $query) use ($clientType) {
                $query->whereRaw('`cs_u`.' . $clientType . ' IS NULL OR `cs_u`.is_delete = 0');
            })
            ->where(function (Builder $query) use ($member, $clientType) {
                $query->whereRaw('`cs_u`.' . $clientType . ' IS NOT NULL OR `cs_m`.member ' . $member);
            })
            ->where(function (Builder $query) use ($clientType) {
                $query->whereRaw('`cs_u`.' . $clientType . ' IS NULL OR `cs_u`.is_read = 0');
            })
            ->where($map)
            ->groupBy('m.type')
            ->get();
        if (!$result->isEmpty()) {
            $totalItem = [];
            foreach ($result as $key) {
                $totalItem[] = $key->total;
            }
            $totalItem['total'] = array_sum($totalItem);
            return $totalItem;
        }
        return ['total' => 0];
    }


    /**
     * @Validation(mode="Message",value="list")
     * @param $data
     * @param $page
     * @param $pageSize
     * @return array
     * @throws Exception
     */
    public function userList($data, $page = 0, $pageSize = 25)
    {
        //  m.message_id,m.type,m.title,m.url,m.is_top,m.target,ifnull(`u`.is_read, 0) is_read,m.create_time
        // 排序方式
        $data['order_type'] ??= 'desc';
        $data['order_field'] ??= 'admin_id';
        // 排序的字段
        $data['page_no'] ??= $page;
        $data['page_size'] ??= $pageSize;

        // 客户类型
        switch (getUserInfo()->getType()) {
            case ClientConstants::TYPE_ADMIN:
                $clientType = 'admin_id';
                $member = '=';
                break;
            case ClientConstants::TYPE_USER:
                $clientType = 'user_id';
                $member = '<';
                break;
            default:
                throw new \Exception('不支持该会员类型');
        }
        $query = Message::query()->from('message as m');
        if (isset($data['type'])) $query->where('m.type', intval($data['type']));
        $query->where('m.is_delete', 0);
        $query->where(function ($query) use ($data, $clientType) {
            /** @var Builder $query */
            if (isset($data['is_read'])) {
                switch ($data['is_read']) {
                    case 0:
                        $query->whereNotNull('u.' . $clientType);
                        $query->where('u.is_read', 0);
                        break;
                    case 1:
                        $query->orWhere('u.is_read', 1);
                        break;
                }
            }
        });

        $result = $query->joinSub(MessageUser::query()->where($clientType, getUserInfo()->getUid()), 'cs_u', 'u.message_id', '=', 'm.message_id', 'left')
            ->where(function ($query) use ($data, $clientType, $member) {
                /** @var Builder $query */
                $query->where(function ($query) use ($clientType) {
                    /** @var Builder $query */
                    $query->whereNotNull('u.' . $clientType);
                    $query->orWhere('u.' . $clientType, getUserInfo()->getUid());
                });
                $query->where(function ($query) use ($clientType) {
                    /** @var Builder $query */
                    $query->whereNotNull('u.' . $clientType);
                    $query->orWhere('u.is_delete', 0);
                });
                $query->where(function ($query) use ($clientType, $member) {
                    /** @var Builder $query */
                    $query->whereNotNull('u.' . $clientType);
                    $query->orWhere('m.member', $member, 2);
                });
            })->orderBy($data['order_field'], $data['order_type'])->paginate($data['page_size'], ['*'], '', $data['page_no']);
        if ($result->isEmpty()) {
            return ['total_result' => 0];
        } else {
            return ['items' => $result->items(), 'total_result' => $result->total()];
        }
    }

    /**
     * @param array $data
     * @param bool $all 是否全部已读
     * @return bool
     */
    public function read(array $data = [], $all = false)
    {
        return $this->updateMessageUserList($data, 'is_read', $all);
    }

    /**
     * @param array $data
     * @param bool $all 是否全部已读
     * @return bool
     */
    public function delete(array $data = [], $all = false)
    {
        return $this->updateMessageUserList($data, 'is_delete', $all);
    }


    /**
     * 批量插入记录或更新记录
     * @param array $messageIds 消息编号
     * @param string $field 字段
     * @param bool $isAll 是否操作所有
     * @return bool
     */
    private function updateMessageUserList($messageIds, $field, $isAll = false)
    {
        $client = getUserInfo();
        Db::beginTransaction();
        try {
            $unreadList = MessageUser::query(true)->where($client->getPrimaryKey(), $client->getUid())->whereIn('message_id', $messageIds)->get(['message_id'])->pluck('message_id')->toArray();
            // 补齐不存在记录
            $notExistsId = array_diff($messageIds, $unreadList);
            if (!empty($notExistsId)) {
                $dataUser = null;
                foreach ($notExistsId as $item) {
                    $dataUser[] = [
                        'message_id' => $item,
                        $client->getPrimaryKey() => $client->getUid(),
                        $field => 1,
                        'create_time' => time(),
                    ];
                }
                MessageUser::query()->insert($dataUser);
            }
            // 更新已存在记录
            if (true === $isAll) {
                $res = MessageUser::query(true)->where($client->getPrimaryKey(), $client->getUid())->update([$field => 1]);
            } else {
                $existsId = array_intersect($messageIds, $unreadList);
                if (!empty($existsId)) {
                    $res = MessageUser::query(true)->where($client->getPrimaryKey(), $client->getUid())->whereIn('message_id', $existsId)->update([$field => 1]);
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            p($e->getMessage());
            Db::rollBack();
            return false;
        }
    }


    /**
     * 获取文章列表
     * @Validation(mode="Message",scene="list",field="data",filter=true)
     * @param array $data
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws Exception
     */
    public function list(array $data, $page = 0, $pageSize = 25)
    {
        // 排序方式
        $data['order_type'] ??= 'desc';
        $data['order_field'] ??= 'message_id';

        $data['page_no'] ??= $page;
        $data['page_size'] ??= $pageSize;

        $model = Message::query();
        // 搜索条件
        if (isset($data['status'])) $model->where('status', intval($data['status']));
        if (isset($data['is_top'])) $model->where('is_top', intval($data['is_top']));
        if (isset($data['type'])) $model->where('type', intval($data['type']));
        if (isset($data['title']) && !empty($data['title'])) $model->where('title', 'like', '%' . $data['title'] . '%');
        if (isset($data['member'])) {
            $model->where('member', $data['member']);
        } else {
            $model->where('member', '!=', 0);
        }
        $model->where('is_delete', 0);

        $result = $model->orderBy($data['order_field'], $data['order_type'])->paginate($data['page_size'], ['*'], '', $data['page_no']);
        if ($result->isNotEmpty()) {
            return ['items' => $result->items(), 'total_result' => $result->total()];
        }
        return ['items' => [], 'total_result' => 0];
    }


}