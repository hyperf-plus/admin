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

namespace HPlus\Admin\Controller;

use HPlus\Admin\Exception\BusinessException;
use HPlus\Admin\Facades\Admin;
use HPlus\Route\Annotation\ApiController;
use HPlus\Route\Annotation\FormData;
use HPlus\Route\Annotation\PostApi;
use Hyperf\HttpMessage\Upload\UploadedFile as UploadedFileAlias;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

/**
 * @ApiController(prefix="/upload")
 * Class IndexAdminController
 */
class Upload
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(ContainerInterface $container, RequestInterface $request, ResponseInterface $response)
    {
        $this->response = $response;
        $this->request = $request;
        $this->container = $container;
    }

    /**
     * @PostApi(path="image")
     * @FormData(key="file",type="file",rule="file")
     * @FormData(key="path",default="avatar")
     */
    public function image()
    {
        $file = $this->request->file('file');
        if (empty($file) || !$file->isValid()) {
            throw new BusinessException(400, '请选择正确的文件！');
        }
        $fileSize = config('admin.upload.image_size', 1024 * 1024 * 5);
        if ($file->getSize() > $fileSize) {
            throw new BusinessException(1000, '文件不能大于！' . ($fileSize / 1024 / 1024) . 'MB');
        }
        $imageMimes = explode(',', $config['image_mimes'] ?? 'jpeg,bmp,png,gif,jpg');
        if (!in_array(strtolower($file->getExtension()), $imageMimes)) {
            throw new BusinessException(1000, '后缀不允许！');
        }
        #检测类型
        if (!in_array(strtolower($file->getClientMediaType()), ['image/gif', 'image/jpeg', 'image/jpg', 'image/png', 'image/pjpeg', 'image/x-png'])) {
            throw new BusinessException(1000, '不允许上传此文件！');
        }
        return Admin::response($this->saveFiles($file, 'image'));
    }

    /**
     * @PostApi(path="file")
     * @FormData(key="file",type="file",rule="file")
     * @FormData(key="path",default="file")
     */
    public function file()
    {
        $file = $this->request->file('file');
        if (empty($file) || !$file->isValid()) {
            throw new BusinessException(400, '请选择正确的文件！');
        }
        $fileSize = config('admin.upload.file_size', 1024 * 1024 * 50);
        if ($file->getSize() > $fileSize) {
            throw new BusinessException(1000, '文件不能大于！' . ($fileSize / 1024 / 1024) . 'MB');
        }
        #检测类型
        $imageMimes = explode(',', $config['file_mimes'] ?? 'txt,sql,zip,rar,ppt,word,xls,xlsx,doc,docx');
        if (!in_array(strtolower($file->getExtension()), $imageMimes)) {
            throw new BusinessException(1000, '类型不允许！');
        }
        return Admin::response($this->saveFiles($file, 'files'));
    }

    protected function saveFiles(UploadedFileAlias $file, $fileName = 'image')
    {
        $file_name = config('admin.upload.save_path', '/upload');
        $file_name = $file_name . '/' . $fileName . '/' . date('Ym') . '/' . date('d') . '/' . uuid(16) . '.' . strtolower($file->getExtension());
        Storage()->getDriver()->put($file_name, $file->getStream()->getContents());
        return [
            'path' => $file_name,
            'name' => $file->getClientFilename(),
            'url' => Storage()->route($file_name),
        ];
    }
}
