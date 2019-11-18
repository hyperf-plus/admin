<?php

namespace App\Controller\Api\v1;

use App\Controller\AdminBase;
use App\Exception\StatusException;
use App\Exception\SystemException;
use App\Model\Admin;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 * Class Message
 * @package app\api\controller\v1
 */
class Upload extends AdminBase
{
    /**
     * 方法路由器
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        return [
            // 获取上传模块列表
            'get.upload.module'   => ['getUploadModule', 'App\Service\UploadService'],
            // 获取上传地址
            'get.upload.url'      => ['getUploadUrl', 'App\Service\UploadService'],
            // 获取上传Token
            'get.upload.token'    => ['getUploadToken', 'App\Service\UploadService'],
            // 替换上传资源
            'replace.upload.item' => ['replaceUploadItem', 'App\Service\UploadService'],
            // 资源上传请求(第三方OSS只能单文件直传方式上传)
            'add.upload.list'     => ['addUploadList', 'App\Service\UploadService'],
            // 接收第三方推送数据
            'put.upload.data'     => ['putUploadData', 'App\Service\UploadService']
        ];
    }
}
