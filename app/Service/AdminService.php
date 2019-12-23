<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Admin;
use Exception;
use Hyperf\Database\Query\Builder;
use Mzh\Validate\Annotations\Validation;

class AdminService
{

    /**
     * @Validation(mode="Admin",value="list")
     * @param $data
     * @param $page
     * @param $pageSize
     * @return array
     */
    public function list($data, $page = 0, $pageSize = 25)
    {
        // 排序方式
        $data['order_type'] ??= 'desc';
        $data['order_field'] ??= 'admin_id';

        $data['page_no'] ??= $page;
        $data['page_size'] ??= $pageSize;
        // 排序的字段
        $result = Admin::query()->where(function ($query) use ($data) {
            if (isset($data['account']) && !empty($data['account'])) {
                /** @var Builder $query */
                $query->where(function ($query) use ($data) {
                    $query->orWhere('username', 'like', '%' . $data['account'] . '%');
                    $query->orWhere('nickname', 'like', '%' . $data['account'] . '%');
                });
            }
            if (isset($data['group_id']) && $data['group_id'] != '') {
                $query->where('group_id', intval($data['group_id']));
            }
            if (isset($data['status']) && $data['status'] != '') {
                $query->where('status', intval($data['status']));
            }
        })->orderBy($data['order_field'], $data['order_type'])->with('getAuthGroup')->paginate($data['page_size'], ['*'], '', $data['page_no']);
        if ($result->isEmpty()) {
            return ['total_result' => 0];
        } else {
            return ['items' => $result->items(), 'total_result' => $result->total()];
        }
    }

    /**
     * @Validation(mode="Admin",value="set",filter=true)
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function update(array $data)
    {
        $result = Admin::query()->find($data['client_id']);
        if ($result->fill(($data))->save()) {
            return $result->toArray();
        }
        throw new Exception('更新失败！');
    }


    /**
     * @Validation(mode="Admin",filter=true)
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function create(array $data)
    {
        $admin = Admin::create($data);
        if ($admin) {
            return $admin->setHidden(['password'])->toArray();
        }
        throw new Exception('添加失败！');
    }

    /**
     * @Validation(mode="Admin",value="reset")
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function reset(array $data)
    {
        // 初始化部分数据
        $map['admin_id'] = ['eq', $data['admin_id']];
        $password = mb_strtolower(getRandStr(8), 'utf-8');
        if (Admin::query()->where('admin_id', $data['admin_id'])->update(['password' => userMd5($password)])) {
            return ['password' => $password];
        }
        throw new Exception('重置失败！');
    }


}
