<?php
declare(strict_types=1);

namespace App\Model;

class Admin extends Model
{
    protected $table = 'admin';
    protected $primaryKey = 'admin_id';
    public $timestamps = true;

    /**
     * 应该被调整为日期的属性
     * @var array
     */
    protected $dates = [
        'create_time',
        'update_time'
    ];

    protected $fillable = ['username', 'password', 'group_id', 'nickname', 'head_pic'];

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'password',
        'is_delete',
        'delete_time'
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $guarded = [
        'admin_id',
        'username',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'admin_id' => 'integer',
        'group_id' => 'integer',
        'last_login' => 'timestamp',
        'status' => 'integer',
        'is_delete' => 'integer',
        'head_pic' => 'array',
    ];


    /**
     * hasOne cs_auth_group
     * @access public
     * @return mixed
     */
    public function getAuthGroup()
    {
        return $this->hasOne(AuthGroup::class, 'group_id', 'group_id');
    }

    public function setAttributePassword($key, $value)
    {
        $this->attributes[$key] = userMd5($value);
        p($value);
    }


}