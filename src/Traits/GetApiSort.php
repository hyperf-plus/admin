<?php
declare(strict_types=1);

namespace Mzh\Admin\Traits;

use \Exception;
use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Exception\ValidateException;
use Mzh\Swagger\Annotation\Body;
use Mzh\Swagger\Annotation\PostApi;
use Psr\Http\Message\ResponseInterface;

trait GetApiSort
{
    use GetApiBase;

    /**
     * @PostApi(summary="更新排序",security=true)
     * @Body(scene="sort",security=true)
     * @return ResponseInterface
     * @throws ValidateException|BusinessException
     */
    public function sort()
    {
        return $this->json($this->_field('sort'));
    }
}