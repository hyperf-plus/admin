<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\ActionLog;
use App\Model\AuthRule;
use App\Model\Menu;
use App\Model\Topic;
use Exception;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Str;
use Mzh\Validate\Annotations\Validation;

class TopicService
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
        $data['order_field'] ??= 'topic_id';
        $data['page_no'] ??= $page;
        $data['page_size'] ??= $pageSize;

        $model = Topic::query();
        // 后台管理搜索
        if (isClientAdmin()) {
            if (isset($data['status'])) $model->where('status', $data['status']);
            if (isset($data['title']) && !empty($data['title'])) $model->where('title', 'like', '%' . $data['title'] . '%');
            if (isset($data['alias']) && !empty($data['alias'])) $model->where('alias', 'like', '%' . $data['alias'] . '%');
            if (isset($data['keywords']) && !empty($data['keywords'])) $model->where('keywords', 'like', '%' . $data['keywords'] . '%');
        } else {
            $model->where('status', 1);
        }
        $result = $model->orderBy($data['order_field'], $data['order_type'])->paginate($data['page_size'], ['*'], '', $data['page_no']);
        if ($result->isNotEmpty()) {
            return ['items' => $result->items(), 'total_result' => $result->total()];
        }
        return ['items' => [], 'total_result' => 0];
    }
}
