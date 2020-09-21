<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.plus
 *
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/hyperf/hyperf-plus/blob/master/LICENSE
 */
namespace HPlus\Admin\Model\Admin;

use HPlus\Admin\Traits\ModelTree;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Arr;
use Hyperf\Utils\Str;

/**
 * Class Menu.
 *
 * @property int $id
 *
 * @method where($parent_id, $id)
 */
class Menu extends Model
{
    //AdminBuilder,
    use ModelTree {
        ModelTree::boot as treeBoot;
    }

    protected $table = 'admin_menu';

    protected $appends = ['route'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'permission' => 'array',
        'created_at' => 'Y-m-d H:i:s',
        'updated_at' => 'Y-m-d H:i:s',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'order', 'is_menu', 'title', 'icon', 'uri', 'permission'];

    public function children()
    {
        return $this->hasMany(get_class($this), 'parent_id')->orderBy('order')->with('children');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * A Menu belongs to many roles.
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_menu_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'menu_id', 'role_id');
    }


    public function allNodes(): array
    {
        $orderColumn = DB::connection()->getQueryGrammar()->wrap($this->orderColumn);
        $byOrder = 'ROOT ASC,' . $orderColumn;
        $query = static::query();
        if (config('admin.check_menu_roles') !== false) {
            $query->with('roles:id,name,slug');
        }
        $all_list = $query->selectRaw('*, ' . $orderColumn . ' ROOT')->orderByRaw($byOrder)->get()->toArray();
        if (config('admin.check_route_permission') !== false) {
            $permissions = config('admin.database.permissions_model')::query()->get();
            $all_list = collect($all_list)->map(function ($item) use ($permissions) {
                $permissionIds = collect(Arr::get($item, 'permission', []))->toArray();
                $permission = collect($permissions)->filter(function ($permissionItem) use ($permissionIds) {
                    return in_array($permissionItem->id, $permissionIds);
                })->map(function ($item) {
                    return $item->id;
                })->flatten()->all();
                Arr::set($item, 'permission', $permission);
                return $item;
            })->all();
        }
        $user = auth()->user();
        $permissionIds = $user->allPermissions()->pluck('id')->toArray();
        $userRolesIds = $user->roles()->pluck('id')->toArray();
        return collect($all_list)->filter(function ($item) use ($permissionIds, $userRolesIds) {
            $roles = collect($item['roles'])->pluck('id')->toArray();
            foreach ($userRolesIds as $role) {
                if (in_array($role, $roles)) {
                    return 1;
                }
            }
            $permissions = (array)$item['permission'];
            foreach ($permissions as $permissionId) {
                if (in_array($permissionId, $permissionIds)) {
                    return 1;
                }
            }
            return 0;
        })->toArray();
    }

    /**
     * determine if enable menu bind permission.
     *
     * @return bool
     */
    public function withPermission()
    {
        return (bool) config('admin.menu_bind_permission');
    }

    public function getRouteAttribute()
    {
        return Str::start($this->uri, '/');
    }
}
