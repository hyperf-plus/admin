<?php
declare(strict_types=1);

namespace Mzh\Admin\Traits;

use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Exception\ValidateException;
use Mzh\Swagger\Annotation\Body;
use Mzh\Swagger\Annotation\Path;
use Mzh\Swagger\Annotation\PutApi;
use Psr\Http\Message\ResponseInterface;

trait GetApiRowChange
{
    use GetApiBase;
    /**
     * @PutApi(path="rowchange/{id:\d+}",summary="更新单个字段内容",security=true)
     * @Path(key="id")
     * @Body(name="body",security=true)
     * @param $id
     * @return ResponseInterface
     */
    public function rowchange($id)
    {
        return $this->json($this->_update($id, $this->request->all(), null, null, $validate = false));
    }
}