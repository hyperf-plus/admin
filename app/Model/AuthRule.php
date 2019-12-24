<?php
declare(strict_types=1);

namespace App\Model;

class AuthRule extends Model
{

    protected $table = 'auth_rule';
    protected $primaryKey = 'rule_id';
    protected $fillable = ['module', 'group_id', 'name', 'menu_auth', 'log_auth', 'sort', 'status', 'order_type', 'order_field'];

    /**
     * 只读属性
     * @var array
     */
    protected $guarded = [
        'rule_id',
        'group_id',
    ];

    protected $hidden = [
        'is_delete'
//       ,
//        'menu_auth',
//        'tenant_id',
//        'log_auth'
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $casts = [
        'rule_id' => 'integer',
        'group_id' => 'integer',
        'menu_auth' => 'array',
        'log_auth' => 'array',
        'sort' => 'integer',
        'status' => 'integer',
    ];


    /**
     * 根据用户组编号与对应模块获取权限明细
     * @access public
     * @param string $module 对应模块
     * @param array $groupId 用户组编号
     * @return array|false
     * @throws
     */
    public static function getMenuAuthRule($module, $groupId)
    {
        $groupId = [getUserInfo()->getGroupId()];

        $result = self::query()->where('module', $module)->where('status', 1)->whereIn('group_id', $groupId)->get();
        if ($result->isEmpty()) {
            return [
                'menu_auth' => [],
                'log_auth' => [],
                'white_list' => []
            ];
        }
        $menuAuth = [];
        $logAuth = [];
        $whiteList = [];
        foreach ($result as $value) {
            // 默认将所有获取到的编号都归入数组
            if (!empty($value['menu_auth'])) {
                $menuAuth = array_merge($menuAuth, (array)$value['menu_auth']);
            }
            if (!empty($value['log_auth'])) {
                $logAuth = array_merge($logAuth, (array)$value['log_auth']);
            }
        }
        return [
            'menu_auth' => array_unique($menuAuth),
            'log_auth' => array_unique($logAuth),
            'white_list' => $whiteList,
        ];
    }
}
