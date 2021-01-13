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

use HPlus\Admin\Exception\BusinessException;
use HPlus\Route\Annotation\GetApi;
use HPlus\UI\Grid;
use HPlus\UI\Layout\Content;

/**
 * Trait HasApiList
 * @package HPlus\Admin\Traits
 */
trait HasApiList
{
    use HasApiBase;

    /**
     * @GetApi(summary="获取列表")
     * @return array|mixed
     */
    public function list()
    {
        /**
         * @var Grid $grid
         */
        $grid = $this->grid();
        $response = $grid->handleExportRequest();
        if ($response instanceof \Hyperf\HttpServer\Response) {
            return $response;
        }
        return $this->isGetData() ? $this->grid()->jsonSerialize() : Content::make()->body($grid)->className('p-10')->jsonSerialize();
    }
}
