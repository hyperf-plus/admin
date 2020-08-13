<?php

declare (strict_types=1);

namespace Mzh\Admin\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $role_id
 * @property string $create_at
 * @property string $update_at
 */
class UserRole extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_role';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'module', 'role_id'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'role_id' => 'integer'];
}