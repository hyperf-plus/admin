<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.plus
 *
 * @link     https://www.hyperf.plus
 * @document https://doc.hyperf.plus
 * @contact  4213509@qq.com
 * @license  https://github.com/hyperf/hyperf-plus/blob/master/LICENSE
 */
namespace HPlus\Admin\Traits;

use HPlus\Route\Annotation\DeleteApi;

trait HasApiDelete
{
    use HasApiBase;

    /**
     * @DeleteApi(path="{id:\d+}", summary="删除接口")
     * @param $id
     * @return array|mixed
     */
    public function delete($id)
    {
        return $this->form(true)->destroy($id);
    }
}
