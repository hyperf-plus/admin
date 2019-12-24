<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\UserInfo;
use App\Model\ActionLog;
use Hyperf\AsyncQueue\Annotation\AsyncQueueMessage;
use Mzh\Validate\Annotations\Validation;

class ActionLogService
{

    /**
     * @Validation(mode="ActionLog",field="data",filter=true)
     * @param array $data
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public function list(array $data, $page = 0, $pageSize = 25)
    {
        $data['order_type'] ??= 'desc';
        $data['order_field'] ??= 'action_log_id';
        $data['page_no'] ??= $page;
        $data['page_size'] ??= $pageSize;
        $result = ActionLog::query()->where(function ($query) use ($data) {
            if (isset($data['client_type'])) $query->where('client_type', $data['client_type']);
            if (isset($data['username'])) $query->where('username', $data['username']);
            if (isset($data['path'])) $query->where('path', $data['path']);
            if (isset($data['status'])) $query->where('status', $data['status']);
            if (!empty($data['begin_time']) && !empty($data['end_time'])) {
                $query->whereBetween('create_time', [strtotime($data['begin_time']), strtotime($data['end_time'])]);
            }
        })->orderBy($data['order_field'], $data['order_type'])->paginate($data['page_size'], ['*'], '', $data['page_no']);

        if ($result->isNotEmpty()) {
            return ['items' => $result->items(), 'total_result' => $result->total()];
        }
        return ['items' => [], 'total_result' => 0];
    }


    /**
     * 记录日志
     * @AsyncQueueMessage
     * @access public
     * @param UserInfo $client
     * @param string $url Url(模块/控制器/操作名)
     * @param array $param
     * @param string $result 处理结果
     * @param string $class 手动输入当前类
     * @param int $status
     * @return bool
     */
    public function recordLog(UserInfo $client, string $url, array $param, string $result, string $class, int $status, $ip)
    {
        // 转为小写
        $url = mb_strtolower($url, 'utf-8');
        $res = json_decode($result, true);
        $data = [
            'client_type' => $client->getType(),
            'user_id' => $client->getUid(),
            'username' => $client->getUsername(),
            'path' => $url,
            'module' => $class,
            'params' => $param,
            'result' => $res,
            'ip' => $ip,
            'status' => (isset($res['status']) ? $res['status'] : 500) != 200,
        ];
        try {
            ActionLog::create($data);
        } catch (\Throwable $e) {
            p($e->getMessage(), '日志队列执行失败！');
        }
        return true;
    }


}
