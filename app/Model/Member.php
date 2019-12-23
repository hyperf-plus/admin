<?php
declare(strict_types=1);

namespace App\Model;

class Member extends Model
{
    protected  $table = 'system_member';
    public  $timestamps = true;
    public  $dateFormat = "Y-m-d H:i:s";
    public  $fillable = ['openid', 'tenant_id'];

    public const UPDATED_AT = 'update_at';
    public const CREATED_AT = 'create_at';
}
