<?php


namespace HPlus\Admin\Traits;

use HPlus\Route\Annotation\GetApi;
use HPlus\UI\Layout\Content;

trait HasApiList
{
    use HasApiBase;

    /**
     * @GetApi(summary="获取列表")
     * @return array|mixed
     */
    public function list()
    {
        $content = new Content();
        //可以重写这里，实现自定义布局
        $content->body($this->grid())->className("p-10");
        //这里必须这样写
        return $this->isGetData() ? $this->grid()->jsonSerialize() : $content->jsonSerialize();
    }
}