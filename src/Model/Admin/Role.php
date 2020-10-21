<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.plus
 *
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/hyperf-plus/admin/blob/master/LICENSE
 */
namespace HPlus\Admin\Model\Admin;

use HPlus\Admin\Model\Model;
use Hyperf\Database\Model\Events\Saved;
use Hyperf\Database\Model\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /**
     * Create a new Eloquent model instance.
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('admin.database.roles_table'));
        parent::__construct($attributes);
    }

    /**
     * A role belongs to many users.
     */
    public function administrators(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.users_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'user_id');
    }

    /**
     * A role belongs to many permissions.
     */
    public function menu(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_menu_table');

        $relatedModel = config('admin.database.menu_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'menu_id', 'role_id');
    }

    /**
     * A role belongs to many menus.
     */
    public function menus(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_menu_table');

        $relatedModel = config('admin.database.menu_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'menu_id');
    }

    /**
     * A role belongs to many permissions.
     */
    public function permissions(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_permissions_table');
        $relatedModel = config('admin.database.permissions_model');
        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'permission_id');
    }

    /**
     * Check user has permission.
     *
     * @param $permission
     */
    public function can(string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }

    /**
     * Check user has no permission.
     *
     * @param $permission
     */
    public function cannot(string $permission): bool
    {
        return ! $this->can($permission);
    }

    public function saved(Saved $event)
    {
        #更新角色后需要清理缓存
        if (function_exists('permission')) {
            permission()->loadRoles(true);
        }
    }
}
