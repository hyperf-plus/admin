<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/26
 * Time: 14:26
 */

namespace App\Model;


class WeChatFans extends Model
{
    protected  $table = 'wechat_fans';
    protected  $primaryKey = 'openid';
    protected  $dateFormat = 'U';
    public  $timestamps = true;

    protected  $fillable = ['subscribe', 'openid', 'nickname', 'sex', 'language', 'city', 'province', 'country', 'headimgurl', 'subscribe_time', 'unionid', 'remark', 'groupid', 'tagid_list', 'subscribe_scene', 'qr_scene', 'qr_scene_str', 'appid'];
    protected  $casts = [
        'tagid_list' => 'array'
    ];

}