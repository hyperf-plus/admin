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
        $content->body($this->grid())->className('p-10');
        //这里必须这样写
        return $this->isGetData() ? $this->grid()->jsonSerialize() : $content->jsonSerialize();
    }
}
