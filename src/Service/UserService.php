<?php
declare(strict_types=1);

namespace Mzh\Admin\Service;

use Mzh\Admin\Model\Admin\FrontRoutes;
use Mzh\Helper\DbHelper\GetQueryHelper;

class UserService implements ServiceInterface
{
    use GetQueryHelper;

    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    public function set($id, array $data)
    {
        // TODO: Implement set() method.
    }

    public function get($id)
    {
        // TODO: Implement get() method.
    }

    public function list($where, $page = 1, $size = 20)
    {
        // TODO: Implement list() method.
    }

    public function menu($where)
    {
        $list = FrontRoutes::query()->when(isset($where['module']), function ($query) use ($where) {
            $query->where('module', $where['module']);
        })->where('status', 1)->orderBy('sort', 'desc')->get([
            'id',
            'pid',
            'label as menu_name',
            'is_menu as hidden',
            'is_scaffold as scaffold',
            'path as url',
            'open_type',
            'view',
            'icon',
        ])->each(function (&$item) {
            $item->hidden = !(bool)$item->hidden;
            $item->scaffold = (bool)$item->scaffold;
        });
        return ['menuList' => generate_tree($list->toArray())];
    }
}