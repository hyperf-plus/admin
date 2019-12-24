<?php
declare(strict_types=1);

namespace App\Model;

use Hyperf\Database\Query\Builder;

class AuthGroup extends Model
{
    protected $table = 'auth_group';
    protected $primaryKey = 'group_id';
    /**
     * 只读属性
     * @var array
     */
    protected $guarded = [
        'group_id',
        'system',
    ];

    protected $hidden = [
        'is_delete',
        'update_time',
        'delete_time',
        'create_time'
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'group_id' => 'integer',
        'system' => 'integer',
        'sort' => 'integer',
        'status' => 'integer',
    ];

    /**
     * 获取以URL为索引的菜单列表
     * @access public
     * @param int $groupId
     * @return array|false
     */
    public static function getGroupAuthUrl(int $groupId = 0)
    {
        $result = AuthRule::query()->where(function ($query) use ($groupId) {
            $query->where('status', 1);
            if ($groupId > 0) $query->where('group_id', $groupId);
        })->get(['menu_auth', 'log_auth', 'group_id']);
        /** @var Builder $item */
        $rule = [];
        foreach ($result as $item) {
            //规则
            $group = $item->getAttribute('group_id');
            $list = Menu::query()->whereIn('menu_id', $item->getAttribute('menu_auth'))->where('url', '!=', '')->get(['url', 'menu_id']);
            $menu = [];
            if (!$list->isEmpty()) {
                $data = [];
                foreach ($list as $menu) {
                    $data[strtolower($menu->getAttribute('url'))] = $menu->getAttribute('menu_id');
                }
                $menu = $data;
            }
            if (!isset($rule[$group]['menu'])) $rule[$group]['menu'] = [];
            $rule[$group]['menu'] = array_merge($rule[$group]['menu'], $menu);
            unset($data, $menu);
            $log_list = Menu::query()->whereIn('menu_id', $item->getAttribute('log_auth'))->where('url', '!=', '')->get(['url', 'menu_id']);
            $menu = [];
            if (!$log_list->isEmpty()) {
                $data = [];
                foreach ($log_list as $menu) {
                    $data[strtolower($menu->getAttribute('url'))] = $menu->getAttribute('menu_id');
                }
                $menu = $data;
            }
            if (!isset($rule[$group]['log'])) $rule[$group]['log'] = [];
            $rule[$group]['log'] = array_merge($rule[$group]['log'], $menu);
            unset($data, $menu);
        }
        return $rule;
    }

}
