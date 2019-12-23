<?php
declare(strict_types=1);

namespace App\Model;

class Topic extends Model
{
    protected $table = 'topic';
    protected $primaryKey = 'topic_id';

    protected $fillable = ['title','alias','content','keywords','description','status'];

    /**
     * 只读属性
     * @var array
     */
    protected $guarded = [
        'topic_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'topic_id'    => 'integer',
        'status'      => 'integer',
    ];

}
