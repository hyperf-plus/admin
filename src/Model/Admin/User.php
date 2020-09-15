<?php

declare (strict_types=1);
namespace Mzh\Admin\Model\Admin;

use Mzh\Admin\Model\Model;

/**
 * @property int $id 
 * @property string $username 
 * @property string $password 
 * @property string $name 
 * @property string $avatar 
 * @property string $remember_token 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}