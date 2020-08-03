<?php
declare(strict_types=1);

namespace Mzh\Admin\Traits;

use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Exception\ValidateException;
use Mzh\Swagger\Annotation\DeleteApi;
use Mzh\Swagger\Annotation\Path;
use Psr\Http\Message\ResponseInterface;

trait GetApiDelete
{
    use GetApiBase;

    /**
     * @DeleteApi(path="{id:\d+}",summary="删除一单条信息",security=true)
     * @Path(key="id")
     * @return ResponseInterface
     * @throws ValidateException|BusinessException
     */
    public function delete($id)
    {
        $this->_delete($id);
        return $this->json('删除成功');
    }
}