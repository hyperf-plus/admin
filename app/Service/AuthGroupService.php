<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\AuthGroup;
use Exception;

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
