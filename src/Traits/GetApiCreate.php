<?php
declare(strict_types=1);

namespace Mzh\Admin\Traits;

use \Exception;
use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Exception\ValidateException;
use Mzh\Swagger\Annotation\Body;
use Mzh\Swagger\Annotation\PostApi;
use Psr\Http\Message\ResponseInterface;

trait GetApiCreate
{
    use GetApiBase;
    /**
     * @PostApi(summary="创建数据",security=true)
     * @Body(scene="create")
     * @return ResponseInterface
     * @throws ValidateException|BusinessException
     */
    public function create()
    {
        return $this->json($this->_create());
    }


}