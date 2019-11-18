<?php
namespace App\Controller\Api\v1;

use App\Controller\AdminBase;
use Hyperf\HttpServer\Annotation\AutoController;


/**
 * @AutoController()
 * Class Storage
 * @package App\Controller\Api\v1
 */
class Storage extends AdminBase
{
    /**
     * 方法路由器
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        return [
            // 添加一个资源目录
            'add.storage.directory.item'    => ['addStorageDirectoryItem'],
            // 编辑一个资源目录
            'set.storage.directory.item'    => ['setStorageDirectoryItem'],
            // 获取资源目录选择列表
            'get.storage.directory.select'  => ['getStorageDirectorySelect'],
            // 将资源目录标设为默认目录
            'set.storage.directory.default' => ['setStorageDirectoryDefault'],
            // 获取默认目录的资源编号
            'get.storage.directory.default' => ['getDefaultStorageId'],
            // 获取一个资源或资源目录
            'get.storage.item'              => ['getStorageItem'],
            // 获取资源列表
            'get.storage.list'              => ['getStorageList'],
            // 获取导航数据
            'get.storage.navi'              => ['getStorageNavi'],
            // 重命名一个资源
            'rename.storage.item'           => ['renameStorageItem'],
            // 将某张图片资源设为目录或视频封面
            'set.storage.cover'             => ['setStorageCover'],
            // 清除目录资源的封面
            'clear.storage.cover'           => ['clearStorageCover'],
            // 批量移动资源到指定目录
            'move.storage.list'             => ['moveStorageList'],
            // 获取资源缩略图
            'get.storage.thumb'             => ['getThumb', 'App\Service\Upload'],
            // 获取资源缩略图实际路径
            'get.storage.thumb.url'         => ['getThumbUrl', 'App\Service\Upload'],
            // 获取资源缩略图信息
            'get.storage.thumb.info'        => ['getThumbInfo', 'App\Service\Upload'],
            // 获取资源下载链接
            'get.storage.download'          => ['getDownload', 'App\Service\Upload'],
            // 批量删除资源
            'del.storage.list'              => ['delStorageList'],
            // 清除图片资源缓存
            'clear.storage.thumb'           => ['clearStorageThumb'],
        ];
    }
}
