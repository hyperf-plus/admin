<?php
declare(strict_types=1);

namespace App\Model;

class MessageUser extends Model
{
    protected $table = 'message_user';
    protected $primaryKey = 'message_user_id';

    /**
     * 只读属性
     * @var array
     */
    protected $guarded = [
        'message_user_id',
        'message_id',
        'user_id',
        'admin_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'message_user_id' => 'integer',
        'message_id'      => 'integer',
        'user_id'         => 'integer',
        'admin_id'        => 'integer',
        'is_read'         => 'integer',
        'is_delete'       => 'integer',
    ];

}
