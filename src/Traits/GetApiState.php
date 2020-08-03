<?php
declare(strict_types=1);

namespace Mzh\Admin\Traits;

use \Exception;
use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Exception\ValidateException;
use Mzh\Swagger\Annotation\Body;
use Mzh\Swagger\Annotation\PostApi;
use Psr\Http\Message\ResponseInterface;

trait GetApiState
{
    use GetApiBase;
    /**
     * @PostApi(summary="更新状态",security=true)
     * @Body(scene="state",security=true,description="主键可为数字或者数组")
     * @return ResponseInterface
     * @throws ValidateException|BusinessException
     */
    public function state()
    {
        return $this->json($this->_field('status','state'));
    }
}