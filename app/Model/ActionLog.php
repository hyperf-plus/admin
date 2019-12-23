<?php
declare(strict_types=1);

namespace App\Model;

use App\Service\MenuService;
use Hyperf\Cache\Annotation\Cacheable;

class ActionLog extends Model
{
    public $timestamps = true;
    protected $table = 'action_log';
    protected $primaryKey = 'action_log_id';
    protected $fillable = ['client_type', 'user_id', 'username', 'path', 'module', 'params', 'result', 'ip', 'status'];

    protected $appends = ['action'];

    /**
     * 更新日期字段
     * @var bool/string
     */
    protected $updateTime = false;

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'action_log_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'action_log_id' => 'integer',
        'client_type' => 'integer',
        'user_id' => 'integer',
        'params' => 'json',
        'result' => 'json',
        'status' => 'integer',
    ];

    /**
     * 敏感词过滤字段
     * @var array
     */
    protected $hidden = [
        'password',
        'password_confirm',
        'appkey',
        'app_key',
        'app_secret',
        'give_code',
        'exchange_code',
        'setting',
        'value',
        'token',
        'token_expires',
        'refresh',
        'refresh_expires',
        'source_no',
        'tel',
        'mobile',
        'email',
        'account',
    ];


    /**
     * 设置菜单操作动作
     * @access private
     * @param string $key 来源值
     * @param string $value 修改值
     * @throws \Exception
     */
    private function setMenuMap($key, &$value)
    {
        $menuMap = array_column(MenuService::getMenuListData([], 'api'), 'name', 'url');
        if (array_key_exists($key, $menuMap)) {
            $value = $menuMap[$key];
            return;
        }
        $value = '未知操作';
    }

    /**
     * 获取器设置日志操作动作
     * @access public
     * @param $value
     * @param $data
     * @return string
     */
    public function getActionAttribute()
    {
        $value = '';
        try {
            $this->setMenuMap(trim($this->getAttribute('path'),'/'), $value);
        } catch (\Exception $e) {
            $value = '未知操作';
        }
        return $value;
    }

    /**
     * 获取器设置请求参数
     * @access public
     * @param $value
     * @return mixed
     */
    public function getParamsAttribute($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (is_array($value)) {
            $this->privacyField($value);
        }

        return $value;
    }

    /**
     * 获取器设置处理结果
     * @access public
     * @param $value
     * @return mixed
     */
    public function getResultAttribute($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (is_array($value)) {
            $this->privacyField($value);
        }

        return $value;
    }

    /**
     * 对过敏字段进行隐私保护
     * @access private
     * @param array $arr 原始数组
     */
    private function privacyField(&$arr)
    {
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $this->privacyField($arr[$key]);
            } elseif (in_array($key, $this->hidden, true)) {
                $arr[$key] = autoHidSubstr($val);
            }
        }
    }
}