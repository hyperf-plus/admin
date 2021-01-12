<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.plus
 *
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/hyperf-plus/admin/blob/master/LICENSE
 */
namespace HPlus\Admin\Traits;

use HPlus\Route\Annotation\DeleteApi;
use HPlus\Route\Annotation\Path;

trait HasApiDelete
{
    use HasApiBase;

    /**
     * @DeleteApi(path="{id:\d+}", summary="删除接口")
     * @Path(key="id", name="id", required=true);
     * @param $id
     * @return array|mixed
     */
    public function delete($id)
    {
        return $this->form(true)->destroy($id);
    }
}
