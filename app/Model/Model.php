<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Model\Model as BaseModel;
use Hyperf\ModelCache\Cacheable;
use Hyperf\ModelCache\CacheableInterface;

abstract class Model extends BaseModel implements CacheableInterface
{
    use Cacheable;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    const DELETED_AT = 'delete_time';
    const UPDATED_AT = 'update_time';
    const CREATED_AT = 'create_time';

    protected $dateFormat = 'U';
    protected static $ignore_corp = false;

    public function getUpdateTimeAttribute($value)
    {
        if (intval($value) == 0) {
            return '-';
        }
        return date('Y-m-d H:i:s', intval($value));
    }

    public function getLastLoginAttribute($value)
    {
        if (intval($value) == 0) {
            return '-';
        }
        return date('Y-m-d H:i:s', intval($value));
    }

    public function getCreateTimeAttribute($value)
    {
        if (intval($value) == 0) {
            return '-';
        }
        return date('Y-m-d H:i:s', intval($value));
    }

    protected function boot(): void
    {
        parent::boot();
        //暂时隐藏调租户功能
        if (static::$ignore_corp) {
            static::addGlobalScope('tenantWhere', function (Builder $builder) {
                return $builder->where(static::getTable() . '.tenant_id', getTenant()->getId());
            });
        }
    }

    public function delete(array $ids = [])
    {
        return self::query()->whereIn(self::getKeyName(), $ids)->delete();
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
        $count = self::query()->where($map)->count();
        if ($count > 0) {
            return true;
        }
        return false;
    }

}
