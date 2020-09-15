<?php


namespace HPlus\Admin\Traits;

use HPlus\Route\Annotation\GetApi;
use HPlus\Route\Annotation\PutApi;
use HPlus\UI\Layout\Content;

trait HasApiPut
{
    use HasApiBase;

    /**
     * @GetApi(path="{id:\d+}",summary="修改")
     * @param $id
     * @return array|mixed
     */
    public function getEdit($id)
    {
        if ($this->isGetData()) {
            $form = $this->form(true);
            $form->isGetData(true);
            return $form->edit($id)->jsonSerialize();
        }
        $content = new Content();
        //可以重写这里，实现自定义布局
        $content->body($this->form(true)->edit($id))->className("m-10");
        //这里必须这样写
        return $content;
    }

    /**
     * 修改数据
     * @PutApi(path="{id:\d+}",summary="修改数据")
     * @param $id
     * @return array|mixed
     */
    public function postEdit($id)
    {
        return $this->form(true)->update($id);
    }
}