<?php
declare(strict_types=1);

namespace Mzh\Admin\Traits;

use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Exception\ValidateException;
use Mzh\Swagger\Annotation\Body;
use Mzh\Swagger\Annotation\DeleteApi;
use Psr\Http\Message\ResponseInterface;

trait GetApiBatchDel
{
    use GetApiBase;

    /**
     * @DeleteApi(summary="批量删除",security=true)
     * @Body(name="body",security=true,description="主键可为数字或者数组")
     * @return ResponseInterface
     * @throws ValidateException|BusinessException|\Exception
     */
    public function batch_del()
    {
        $this->_delete($this->request->getParsedBody());
        return $this->json('删除成功');
    }
}