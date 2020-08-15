<?php
declare(strict_types=1);

namespace Mzh\Admin\Traits;

use Mzh\Helper\DbHelper\GetQueryHelper;
use Mzh\Swagger\Annotation\GetApi;
use Mzh\Swagger\Annotation\Query;

trait GetApiList
{
    use GetApiBase;
    use GetQueryHelper;
    /**
     * @GetApi(summary="列表",security=true)
     * @Query(key="page",description="页码")
     * @Query(key="limit",description="每页条数")
     * @Query(scene="list")
     * @throws \Exception
     */
    public function list()
    {
        $model = $this->getModel();
        $data = request()->all();
        return $this->json($this->_list($model, $data));
    }

}