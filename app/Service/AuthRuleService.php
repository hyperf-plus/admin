<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\AuthRule;
use Exception;
use Hyperf\Utils\Str;
use Mzh\Validate\Annotations\Validation;

class AuthRuleService
{
    /**
     * @Validation(mode="AuthRule",scene="list",field="data",filter=true)
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function list(array $data)
    {
        $db = AuthRule::query()->where(function ($query) use ($data) {
            // 搜索条件
            if (isset($data['group_id']) && $data['group_id'] != '') $query->where('group_id', $data['group_id']);
            if (isset($data['module']) && $data['module'] != '') $query->where('module', $data['module']);
            if (isset($data['status']) && $data['status'] != '') $query->where('status', $data['status']);
        });
        // 排序方式
        $data['order_type'] ??= 'asc';
        // 排序的字段
        $data['order_field'] ??= 'rule_id';
        // 排序处理
        $db->orderBy('sort', 'asc');
        $db->orderBy($data['order_field'], $data['order_type']);
        return $db->get()->toArray();
    }

    /**
     * @Validation(mode="AuthRule",scene="index",field="data",filter=true)
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function update_index(array $data)
    {
        $result = AuthRule::query(true)->find($data['menu_id']);
        if (count($result) <= 0) {
            throw new Exception('数据不存在');
        }
        $list = [];
        foreach ($result as $key => $item) {
            $item->sort = $key + 1;
            $item->save();
            $list[] = ['menu_id' => $item->menu_id, 'sort' => $key + 1];
        }
        return $list;
    }

    /**
     * @Validation(mode="AuthRule",filter=true)
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function create(array $data)
    {
        $AuthRule = AuthRule::query(true)->create($data);
        if ($AuthRule) {
            return $AuthRule->toArray();
        }
        throw new Exception('添加失败！');
    }

    /**
     * URL驼峰转下划线修改器
     * @access protected
     * @param string $value 值
     * @return string
     */
    private function strToSnake($value)
    {
        if (empty($value) || !is_string($value)) {
            return $value;
        }
        $word = explode('/', $value);
        $word = array_map([Str::class, 'snake'], $word);
        return implode('/', $word);
    }

    /**
     * @Validation(mode="AuthRule",scene="set",field="data",filter=true)
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function update($data)
    {
        $result = AuthRule::query(true)->find($data['menu_id']);
        if (!$result) {
            throw new Exception('数据不存在');
        }
        // 父菜单不能设置成自身或所属的子菜单
        if (isset($data['parent_id'])) {
            if ($data['parent_id'] == $data['menu_id']) {
                throw new Exception('上级菜单不能设为自身');
            }
            $menuList = self::getMenuListData([], $result->getAttribute('module'), $data['menu_id']);
            foreach ($menuList as $value) {
                if ($data['parent_id'] == $value->menu_id) {
                    throw new Exception('上级菜单不能设为自身的子菜单');
                }
            }
        }
        if ($result->fill($data)->save()) {
            return $result->toArray();
        }
        throw new Exception('修改失败');
    }
}
