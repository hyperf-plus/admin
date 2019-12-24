<?php

namespace App\Model;

use App\Exception\AddException;
use App\Exception\RESTException;

class UserLevel extends Model
{

    protected $table = 'user_level';
    protected $primaryKey = 'user_level_id';

    /**
     * 只读属性
     * @var array
     */
    protected $guarded = [
        'user_level_id',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $hidden = [
        'is_delete'
    ];
    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'user_level_id' => 'integer',
        'amount' => 'float',
        'discount' => 'integer',
    ];

}
