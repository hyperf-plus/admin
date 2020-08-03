<?php 
declare(strict_types = 1);
namespace Mzh\Admin\Model\Admin;

use Hyperf\Database\Model\Model;

/**
 * @property int $id
 * @property string $username 用户名
 * @property string $realname
 * @property string $password
 * @property string $mobile
 * @property string $email
 * @property int $status
 * @property timestamp $login_time
 * @property string $login_ip
 * @property int $is_admin is admin
 * @property int $is_default_pass 是否初始密码1:是,0:否
 * @property string $qq 用户qq
 * @property string $roles
 * @property string $sign 签名
 * @property string $avatar
 * @property string $avatar_small
 * @property timestamp $create_at
 * @property timestamp $update_at
 */
class User extends Model
{
    protected $connection = 'default';
    protected $table = 'user';
    protected $database = 'admin';

    protected $fillable = [
        'id',
        'username',
        'realname',
        'password',
        'mobile',
        'email',
        'status',
        'login_time',
        'login_ip',
        'is_admin',
        'is_default_pass',
        'qq',
        'roles',
        'sign',
        'avatar',
        'avatar_small',
    ];

    protected $casts = [
        'username' => 'string',
        'realname' => 'string',
        'password' => 'string',
        'mobile' => 'string',
        'email' => 'string',
        'status' => 'int',
        'login_time' => 'timestamp',
        'login_ip' => 'string',
        'is_admin' => 'int',
        'is_default_pass' => 'int',
        'qq' => 'string',
        'roles' => 'string',
        'sign' => 'string',
        'avatar' => 'string',
        'avatar_small' => 'string',
    ];
}
