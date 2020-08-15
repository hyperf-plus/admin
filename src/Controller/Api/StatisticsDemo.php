<?php
declare(strict_types=1);

namespace Mzh\Admin\Controller\Api;

use Mzh\Swagger\Annotation\ApiController;
use Mzh\Swagger\Annotation\GetApi;

/**
 * @ApiController(tag="后台-首页统计模块-示例")
 */
class StatisticsDemo
{

    /**
     * @GetApi(summary="main统计面板",security=false)
     */
    public function main()
    {
        return '{"today":{"orderNum":0,"payPrice":0,"payUser":0,"visitNum":0,"likeStore":0},"yesterday":{"orderNum":0,"payPrice":0,"payUser":0,"visitNum":0,"likeStore":0},"lastWeekRate":{"orderNum":0,"payPrice":0,"payUser":0,"visitNum":0,"likeStore":0},"day":"2020-08-15"}';
    }

    /**
     * @GetApi(summary="user_rate",security=false)
     */
    public function user_rate()
    {
        return '{"newTotalPrice":"0","newUser":0,"oldTotalPrice":0,"oldUser":0,"totalPrice":0,"user":0}';
    }

    /**
     * @GetApi(summary="order",security=false)
     */
    public function order()
    {
        return '[{"day":"08-08","total":0,"user":0,"pay_price":0},{"day":"08-09","total":0,"user":0,"pay_price":0},{"day":"08-10","total":0,"user":0,"pay_price":0},{"day":"08-11","total":0,"user":0,"pay_price":0},{"day":"08-12","total":0,"user":0,"pay_price":0},{"day":"08-13","total":0,"user":0,"pay_price":0},{"day":"08-14","total":0,"user":0,"pay_price":0},{"day":"08-15","total":0,"user":0,"pay_price":0}]';
    }

    /**
     * @GetApi(summary="product",security=false)
     */
    public function product()
    {
        return [];
    }

    /**
     * @GetApi(summary="product_visit",security=false)
     */
    public function product_visit()
    {
        return [];
    }

    /**
     * @GetApi(summary="product_cart",security=false)
     */
    public function product_cart()
    {
        return [];
    }

    /**
     * @GetApi(summary="user",security=false)
     */
    public function user()
    {
        return '{"visitUser":0,"orderUser":0,"orderPrice":0,"payOrderUser":0,"payOrderPrice":0,"payOrderRate":0,"userRate":0,"orderRate":0}';
    }
}