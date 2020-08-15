<?php
declare(strict_types=1);

namespace Mzh\Admin\Controller\Api;

use Hyperf\Filesystem\FilesystemFactory;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\Utils\Str;
use League\Flysystem\FileExistsException;
use Mzh\Admin\Controller\AbstractController;
use Mzh\Admin\Exception\BusinessException;
use Mzh\Admin\Model\Admin\FrontRoutes;
use Mzh\Admin\Model\Admin\FrontRoutes as AdminMenu;
use Mzh\Admin\Model\Config;
use Mzh\Admin\Service\ConfigService;
use Mzh\Admin\Service\MenuService;
use Mzh\Admin\Traits\GetApiBatchDel;
use Mzh\Admin\Traits\GetApiCreate;
use Mzh\Admin\Traits\GetApiDelete;
use Mzh\Admin\Traits\GetApiDetail;
use Mzh\Admin\Traits\GetApiList;
use Mzh\Admin\Traits\GetApiRowChange;
use Mzh\Admin\Traits\GetApiSort;
use Mzh\Admin\Traits\GetApiUI;
use Mzh\Admin\Traits\GetApiUpdate;
use Mzh\Admin\Validate\FrontRoutesValidation;
use Mzh\Admin\Views\MenuView;
use Mzh\Helper\DbHelper\QueryHelper;
use Mzh\Swagger\Annotation\ApiController;
use Mzh\Swagger\Annotation\FormData;
use Mzh\Swagger\Annotation\GetApi;
use Mzh\Swagger\Annotation\PostApi;
use Mzh\Swagger\Annotation\Query;

/**
 * @ApiController(tag="公共-Upload模块")
 */
class Upload extends AbstractController
{

    /**
     * @PostApi(description="图片上传接口",security=false)
     * @FormData(in="formData",description="文件",name="file",type="file")
     * @param FilesystemFactory $factory
     * @throws FileExistsException
     */
    public function image(FilesystemFactory $factory)
    {
        $file = $this->request->file('file');
        if (!$file->isValid()) {
            throw new BusinessException(1000, '请选择正确的文件！');
        }
        $file_name = '/upload/' . date('Ym') . '/' . date('d') . '/' . uuid(16) . '.' . $file->getExtension();
        $config = ConfigService::getConfig('system_config');

        $image_size = $config['image_size'] ?? 1024 * 1024;
        if ($file->getSize() > $image_size) {
            throw new BusinessException(1000, '文件不能大于！' . $image_size);
        }

        if (!in_array($file->getExtension(), $config['allow_ext'] ?? ['jpg', 'jpeg', 'gif', 'bmp', 'png'])) {
            throw new BusinessException(1000, '后缀不允许！');
        }
        #检测类型
        if (!in_array($file->getClientMediaType(), ["image/gif", "image/jpeg", "image/jpg", "image/png", "image/pjpeg", "image/x-png"])) {
            throw new BusinessException(1000, '不允许上传此文件！');
        }
        $storage = $config['storage'] ?? 'local';
        $device = $factory->get($storage);
        $device->createDir(basename($file_name));
        $device->write($file_name, $file->getStream()->getContents());
        list($width, $height) = getimagesize($file_name);

        return [
            'path' => $file_name,
            'key' => 'file',
            'size' => $file->getSize(),
            'domain' => config('file.image_url'),
            'url' => config('file.image_url') . $file_name,
            'width' => $width,
            'height' => $height
        ];
    }


}
