<?php

namespace App\Model;

use App\Exception\LoginException;
use App\Exception\ValidateException;
use App\Service\CacheEventService;
use App\Validate\Validate;
use Generator;
use Hyperf\Database\ConnectionInterface;
use Hyperf\Database\Query\Builder;
use Hyperf\Database\Query\Expression;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Di\Annotation\Inject;


/**
 * Class BaseModel
 * @package App\Model
 * @method static Builder table(string $table)
 * @method static Expression raw($value)
 * @method static selectOne(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static Generator cursor(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static bool insert(string $query, array $bindings = [])
 * @method static int update(string $query, array $bindings = [])
 * @method static int delete(string $query, array $bindings = [])
 * @method static bool statement(string $query, array $bindings = [])
 * @method static int affectingStatement(string $query, array $bindings = [])
 * @method static bool unprepared(string $query)
 * @method static array prepareBindings(array $bindings)
 * @method static transaction(\Closure $callback, int $attempts = 1)
 * @method static beginTransaction()
 * @method static rollBack()
 * @method static commit()
 * @method static int transactionLevel()
 * @method static array pretend(\Closure $callback)
 * @method static ConnectionInterface connection(string $pool)
 * @method Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method Builder select($columns = ['*'])
 * @method Builder selectSub($query, $as)
 * @method Builder selectRaw($expression, array $bindings = [])
 * @method Builder fromSub($query, $as)
 * @method Builder fromRaw($expression, $bindings = [])
 * @method Builder addSelect($column)
 * @method Builder from($table)
 * @method Builder seTable($table)
 * @method Builder whereIn($column, $values, $boolean = 'and', $not = false)
 * @method Builder whereOr($table)
 *
 *
 */
class BaseModel extends Model
{

    /**
     * 自动验证数据
     * @access protected
     * @param array $data 验证数据
     * @param mixed $rule 验证规则
     * @param bool $batch 批量验证
     * @return bool
     */

    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = false;

//    /**
//     * @Inject()
//     * @var Validate
//     */
//    protected $validate;
    const DELETED_AT = 'delete_time';
    const UPDATED_AT = 'update_time';
    const CREATED_AT = 'create_time';
    protected $dateFormat = 'U';

//    /**
//     * 指定时间字符
//     *
//     * @param \DateTime|int $value
//     * @return string
//     */
//    public function fromDateTime($value)
//    {
//        return strtotime($value);
//    }
    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'is_delete',
        'delete_time'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected function boot(): void
    {
        parent::boot();
        static::addGlobalScope('delete', function (\Hyperf\Database\Model\Builder $builder) {
            $builder->where($this->table . '.is_delete', '=', 0);
        });
    }


    public function getUpdateTimeAttribute($value)
    {
        return date("Y-m-d H:i:s", (int)$value);
    }

    public function getDeleteTimeAttribute($value)
    {
        return date("Y-m-d H:i:s", (int)$value);
    }

    public function getCreateTimeAttribute($value)
    {
        return date("Y-m-d H:i:s", (int)$value);
    }

    protected function setAttr($key, $value)
    {
        return parent::setAttribute($key, $value);
    }

    /**
     * @param $data
     * @param null $info
     * @param bool $batch
     * @return bool ValidateException
     * @throws ValidateException
     */
    protected function validateData($data, $info = null, $batch = false)
    {
        if (!empty($info)) {
            if (is_array($info)) {
                $validate = new Validate();
                $validate->rule($info['rule']);
                $validate->message($info['msg']);
            } else {
                $name = $info;
                if (strpos($name, '.')) {
                    list($name, $scene) = explode('.', $name);
                }
                /**
                 * @var Validate $validate
                 */
                $class = 'app\\Validate\\' . $name;
                if (class_exists($class)) {
                    $validate = new $class;
                } else {
                    throw new ValidateException('class not exists:' . $class, $class);
                }
                if (!empty($scene)) {
                    $validate->scene($scene);
                }
            }

            if (!$validate->batch($batch)->check($data)) {
                throw new ValidateException($validate->getError());
            }
        }
        return true;
    }

    /**
     * 根据传入参数进行验证
     * @access public
     * @param array $data 待验证数据
     * @param string $name 验证器
     * @param string $scene 场景
     * @return bool
     * @throws ValidateException
     */
    public function validateSetData(&$data, $name, $scene = '', $pk = 'id')
    {
        if (strpos($name, '.')) {
            list($name, $scene) = explode('.', $name);
        }
        /**
         * @var Validate $validate
         */
        $class = 'app\\Validate\\' . $name;
        if (class_exists($class)) {
            $validate = new $class;
        } else {
            throw new ValidateException('class not exists:' . $class, $class);
        }
        if (!empty($scene) && !$validate->hasScene($scene)) {
            throw new ValidateException('场景不存在');
        }
        $validate->scene($scene);
        $rule = $validate->getSetScene($scene);
        foreach ($data as $key => $item) {
            if (!in_array($key, $rule, true) && !array_key_exists($key, $rule)) {
                unset($data[$key]);
                continue;
            }
        }
        unset($key, $item);

        foreach ($rule as $key => $value) {
            $field = is_string($key) ? $key : $value;
            if ($field == $pk) {
                continue;
            }
            if (!array_key_exists($field, $data)) {
                unset($rule[$key]);
            }
        }
        unset($key, $value);
        $validate->scene($scene)->check($data, $rule);
        return true;
    }

    /**
     * 检测是否存在相同值
     * @access public
     * @param array $map 查询条件
     * @return bool false:不存在
     * @throws
     */
    public static function checkUnique($map)
    {
        if (empty($map)) {
            return true;
        }
        $count = self::where($map)->count();
        if ($count <= 0) {
            return false;
        }
        return true;
    }

    public function setError($msg = '')
    {
        throw new LoginException($msg);
    }
}