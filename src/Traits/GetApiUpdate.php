<?php
declare(strict_types=1);

namespace Mzh\Admin\Traits;

use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Exception\ValidateException;
use Mzh\Swagger\Annotation\Body;
use Mzh\Swagger\Annotation\Path;
use Mzh\Swagger\Annotation\PutApi;
use Psr\Http\Message\ResponseInterface;

trait GetApiUpdate
{
    use GetApiBase;

    /**
     * @PutApi(path="{id:\d+}",summary="更新单条信息",security=true)
     * @Path(key="id")
     * @Body(scene="update",security=true)
     * @return ResponseInterface
     * @throws ValidateException|BusinessException
     */
    public function update($id)
    {
        return $this->json($this->_update($id));
    }

}