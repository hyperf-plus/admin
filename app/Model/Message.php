<?php
declare(strict_types=1);

namespace App\Model;

class Message extends Model
{
    protected $table = 'message';
    protected $primaryKey = 'message_id';
    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $fillable = ['type','member','title','content','url','target','is_top','is_read','status','is_unread'];


    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'is_delete',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $guarded = [
        'message_id',
        'member',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'message_id' => 'integer',
        'type'       => 'integer',
        'member'     => 'integer',
        'page_views' => 'integer',
        'is_top'     => 'integer',
        'status'     => 'integer',
        'is_delete'  => 'integer',
    ];

    /**
     * hasOne cs_message_user
     * @access public
     * @return mixed
     */
    public function getMessageUser()
    {
        return $this->hasOne('MessageUser', 'message_id');
    }

}
