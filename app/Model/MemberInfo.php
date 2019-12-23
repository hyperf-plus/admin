<?php
declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model as BaseModel;


class MemberInfo extends Model
{
    protected  $table = 'system_member_info';
    public  $timestamps = true;
    protected  $casts = [
        'sex' => 'integer'
    ];
    protected  $attributes = [
        'status',
        'abroad',
        'address',
        'birth_date',
        'company',
        'cppcc',
        'degree',
        'email',
        'finish_school',
        'household_reg',
        'interest',
        'name',
        'nation',
        'other',
        'phone',
        'photo',
        'politic',
        'positions',
        'professional',
        'remark',
        'resume',
        'sex'
    ];

    protected  $hidden = [
        "subscribe",
        "update_time",
        "create_time",
        "tenant_id"
    ];


}
