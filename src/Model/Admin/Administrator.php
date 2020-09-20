<?php
declare(strict_types=1);

namespace HPlus\Admin\Model\Admin;

use Hyperf\Database\Model\Events\Saved;
use Hyperf\Database\Model\Relations\BelongsToMany;
use HPlus\Admin\Model\Model;
use HPlus\Admin\Traits\HasPermissions;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * Class Administrator.
 *
 * @property Role[] $roles
 */
class Administrator extends Model implements Authenticatable
{
    protected $fillable = ['username', 'password', 'name', 'avatar'];
    protected $hidden = ['password'];
    protected $primaryKey = 'id';

    protected $casts = [
        'images' => 'array',
        'created_at' => "Y-m-d H:i:s",
        'updated_at' => "Y-m-d H:i:s",
    ];

    use HasPermissions;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('admin.database.users_table'));
        parent::__construct($attributes);
    }

    /**
     * Get avatar attribute.
     *
     * @param string $avatar
     *
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        if (is_validURL($avatar)) {
            return $avatar;
        }
        $disk = config('admin.upload.disk');
        if ($avatar && array_key_exists($disk, config('file.storage'))) {
            return Storage()->route($avatar);
        }
        $default = config('admin.default_avatar');
        return admin_asset($default);
    }

    /**
     * A user has and belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_users_table');
        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id');
    }

    /**
     * A User has and belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        $pivotTable = config('admin.database.user_permissions_table');
        $relatedModel = config('admin.database.permissions_model');
        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'permission_id');
    }

    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->id;
    }

    public static function retrieveById($key): ?Authenticatable
    {
        // TODO: Implement retrieveById() method.
        return Administrator::findFromCache($key);
    }

    public function saved(Saved $event)
    {
        #更新角色后需要清理缓存
        permission()->reloadUser($event->getModel()->id);
    }
}

