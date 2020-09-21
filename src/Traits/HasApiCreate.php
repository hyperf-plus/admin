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

use HPlus\Route\Annotation\GetApi;
use HPlus\Route\Annotation\PostApi;
use HPlus\UI\Layout\Content;

trait HasApiCreate
{
    use HasApiBase;

    /**
     * @PostApi(summary="创建数据")
     */
    public function _self_path()
    {
        return $this->form(true)->store();
    }

    /**
     * @GetApi(summary="获取创建UI配置")
     * @return array|mixed
     */
    public function create()
    {
        $content = new Content();
        //这里必须这样写
        return $content->body($this->form())->className('m-10');
    }
}
