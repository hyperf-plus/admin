<?php

namespace Mzh\Admin\Traits;


use HPlus\Route\Annotation\DeleteApi;

trait HasApiDelete
{
    use HasApiBase;

    /**
     * @DeleteApi(path="{id:\d+}",summary="删除接口")
     * @param $id
     * @return array|mixed
     */
    public function delete($id)
    {
        return $this->form(true)->destroy($id);
    }
}