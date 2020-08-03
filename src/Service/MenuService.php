<?php


namespace Mzh\Admin\Service;


use Mzh\Admin\Model\Admin\FrontRoutes;

class MenuService
{

    public function tree($where = [], $fields = ['id as value', 'pid', 'label', 'module'], $pk_key = 'value')
    {
        $where['status'] = 1;
        $query = FrontRoutes::query();
        foreach ($where as $key => $val) {
            if (is_array($val)) {
                $query->whereIn($key, $val);
            } else {
                $query->where($key, $val);
            }
        }
        $query->select($fields);
        $list = $query->orderBy('sort', 'desc')->get();
        if (empty($list)) {
            return [];
        }
        $list = $list->toArray();
        return generate_tree($list, 'pid', $pk_key, 'children', function (&$item) use ($pk_key) {
            $item[$pk_key] = (int)$item[$pk_key];
            $item['pid'] = (int)$item['pid'];
            if (isset($item['hidden'])) {
                $item['hidden'] = !(bool)$item['hidden'];
            }
            if (isset($item['scaffold'])) {
                $item['scaffold'] = (bool)$item['scaffold'];
            }
            unset($item);
        });
    }


    public function getParent($id)
    {
        return FrontRoutes::query()->select(['id', 'pid'])->find($id);
    }

    public function getPathNodeIds($id)
    {
        $parents = [];
        while ($p = $this->getParent($id)) {
            $id = (int)$p['pid'];
            if ($id) {
                $parents[] = $id;
            }
        }
        return array_reverse($parents);
    }
}