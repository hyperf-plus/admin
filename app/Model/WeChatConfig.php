<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/22
 * Time: 0:47
 */

namespace App\Model;


class WeChatConfig extends Model
{
    protected $table = 'wechat_config';
    protected $primaryKey = 'authorizer_appid';

}