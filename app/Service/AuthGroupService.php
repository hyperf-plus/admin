<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\AuthGroup;
use App\Model\AuthRule;
use App\Model\Menu;
use Exception;
use Hyperf\Database\Query\Builder;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Db;
use Mzh\Validate\Annotations\Validation;

class AuthGroupService
{

    /**
     * Validation(mode="AuthGroup",field="data")
     * @param array $data
     * @param array $groupIds
     * @return array
     * @throws Exception
     */
    public function list(array $data, array $groupIds = [])
    {
        // 排序方式
        $data['order_type'] ??= 'asc';
        $data['order_field'] ??= 'group_id';
        $result = AuthGroup::query()->select(['group_id','name','description','system', 'sort','status'])->where(function ($query) use ($data) {
            // 搜索条件
            $map = [];
            if (isset($data['exclude_id'])) {
                $query->whereNotIn('group_id', $data['exclude_id']);
            }
            if (isset($data['status'])) $map[] = ['status', '=', $data['status']];
            $query->where($map);
        })->orderBy($data['order_field'], $data['order_type'])->get();

        if (false == $result) {
            throw new Exception('查询失败！');
        }
        return $result->toArray();
    }


}
