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
namespace HPlus\Admin\Controller;

use HPlus\Admin\Exception\BusinessException;
use HPlus\Admin\Facades\Admin;
use Hyperf\HttpMessage\Upload\UploadedFile as UploadedFileAlias;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @Controller(prefix="/upload")
 * Class IndexAdminController
 */
class Upload extends AbstractAdminController
{
    /**
     * @PostMapping(path="image")
     */
    public function image()
    {
        $file = $this->request->file('file');
        if (! $file->isValid()) {
            throw new BusinessException(400, '请选择正确的文件！');
        }
        $fileSize = config('admin.upload.image_size', 1024 * 1024 * 5);
        if ($file->getSize() > $fileSize) {
            throw new BusinessException(1000, '文件不能大于！' . ($fileSize / 1024 / 1024) . 'MB');
        }
        $imageMimes = explode(',', $config['image_mimes'] ?? 'jpeg,bmp,png,gif,jpg');
        if (! in_array($file->getExtension(), $imageMimes)) {
            throw new BusinessException(1000, '后缀不允许！');
        }
        #检测类型
        if (! in_array($file->getClientMediaType(), ['image/gif', 'image/jpeg', 'image/jpg', 'image/png', 'image/pjpeg', 'image/x-png'])) {
            throw new BusinessException(1000, '不允许上传此文件！');
        }
        return Admin::response($this->saveFiles($file, 'image'));
    }

    /**
     * @PostMapping(path="file")
     */
    public function file()
    {
        $file = $this->request->file('file');
        if (! $file->isValid()) {
            throw new BusinessException(400, '请选择正确的文件！');
        }
        $fileSize = config('admin.upload.file_size', 1024 * 1024 * 50);
        if ($file->getSize() > $fileSize) {
            throw new BusinessException(1000, '文件不能大于！' . ($fileSize / 1024 / 1024) . 'MB');
        }
        #检测类型
        $imageMimes = explode(',', $config['file_mimes'] ?? 'txt,sql,zip,rar,ppt,word,xls,xlsx,doc,docx');
        if (! in_array($file->getExtension(), $imageMimes)) {
            throw new BusinessException(1000, '类型不允许！');
        }
        return Admin::response($this->saveFiles($file, 'files'));
    }

    protected function saveFiles(UploadedFileAlias $file, $fileName = 'image')
    {
        $file_name = config('admin.upload.save_path', '/upload');
        $file_name = $file_name . '/' . $fileName . '/' . date('Ym') . '/' . date('d') . '/' . uuid(16) . '.' . $file->getExtension();
        Storage()->getDriver()->put($file_name, $file->getStream()->getContents());
        return [
            'path' => $file_name,
            'name' => $file->getClientFilename(),
            'url' => Storage()->route($file_name),
        ];
    }
}
