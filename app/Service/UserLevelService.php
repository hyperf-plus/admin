<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Token;
use App\Model\UserLevel;
use Mzh\JwtAuth\JwtData;
use Mzh\Validate\Annotations\Validation;

class UserLevelService
{
    /**
     * @Validation(mode="User",field="data")
     * @param array $data
     * @return array
     */
    public function list(array $data)
    {
        // 排序方式
        $data['order_type'] ??= 'asc';
        // 排序的字段
        $data['order_field'] ??= 'amount';
        return UserLevel::query()->orderBy($data['order_field'], $data['order_type'])->get()->toArray();
    }
}
