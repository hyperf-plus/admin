<?php
namespace App\Controller\Api\v1;

use App\Controller\AdminBase;
use Hyperf\HttpServer\Annotation\AutoController;


/**
 * @AutoController()
 * Class Ads
 * @package App\Controller\Api\v1
 */
class Ads extends AdminBase
{
    /**
     * 方法路由器
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        return [
            // 添加一个广告
            'add.ads.item'    => ['addAdsItem'],
            // 编辑一个广告
            'set.ads.item'    => ['setAdsItem'],
            // 批量删除广告
            'del.ads.list'    => ['delAdsList'],
            // 设置广告排序
            'set.ads.sort'    => ['setAdsSort'],
            // 根据编号自动排序
            'set.ads.index'   => ['setAdsIndex'],
            // 批量设置是否显示
            'set.ads.status'  => ['setAdsStatus'],
            // 获取一个广告
            'get.ads.item'    => ['getAdsItem'],
            // 获取广告列表
            'get.ads.list'    => ['getAdsList'],
            // 根据编码获取一个广告
            'get.ads.code'    => ['getAdsCode'],
            // 验证广告编码是否唯一
            'unique.ads.code' => ['uniqueAdsCode'],
        ];
    }
}
