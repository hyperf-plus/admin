<?php
declare(strict_types=1);

namespace Mzh\Admin\Traits;

use Mzh\Swagger\Annotation\GetApi;
use Mzh\Swagger\Annotation\Path;
use Psr\Http\Message\ResponseInterface;

trait GetApiDetail
{
    use GetApiBase;

    /**
     * @GetApi(path="{id:\d+}",summary="获取详情",security=true)
     * @Path(key="id")
     * @param int $id
     * @return ResponseInterface
     */
    public function detail($id = 0)
    {
        return $this->json($this->_detail($id));
    }
}