<?php
declare(strict_types=1);

namespace App\Model;

class User extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'user_id';
    const DELETED_AT = 'delete_time';
    const UPDATED_AT = 'update_time';
    const CREATED_AT = 'create_time';
    protected $dateFormat = 'U';

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'password',
        'is_delete',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $guarded = [
        'user_id',
        'username',
        'mobile',
        'email'
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'is_mobile' => 'integer',
        'is_email' => 'integer',
        'sex' => 'integer',
        'user_level_id' => 'integer',
        'user_address_id' => 'integer',
        'group_id' => 'integer',
        'last_login' => 'timestamp',
        'status' => 'integer',
        'is_delete' => 'integer',
    ];

    /**
     * 密码修改器
     * @access protected
     * @param string $value 值
     * @return string
     */
    protected function setPasswordAttribute($value)
    {
        return userMd5($value);
    }


    /**
     * hasOne cs_user_level
     * @access public
     * @return mixed
     */
    public function getUserLevel()
    {
        return $this->hasOne(userLevel::class, 'user_level_id', 'user_level_id')->addSelect(['user_level_id', 'icon', 'name']);
    }

    /**
     * hasOne cs_auth_group
     * @access public
     * @return mixed
     */
    public function getAuthGroup()
    {
        return $this->hasOne(AuthGroup::class, 'group_id', 'group_id')->addSelect(['*']);
    }

}