<?php

namespace App\Extend\oss\local;

use App\Exception\FileException;
use \App\Extend\oss\Upload as UploadBase;
use App\Model\Storage;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;
use think\cache\driver\File;


class Upload extends UploadBase
{
    /**
     * 模块名称
     * @var string
     */
    const NAME = '本地上传';

    /**
     * 模块
     * @var string
     */
    const MODULE = 'local';

    /**
     * 最大上传字节数
     * @var int
     */
    protected static $maxSize;

    /**
     * 最大上传信息
     * @var string
     */
    protected static $maxSizeInfo;

    /**
     * 设置最大可上传大小
     * @access private
     * @return void
     */
    protected function setFileMaxSize()
    {
        if (is_null(self::$maxSize)) {
            $serverSize = ini_get('upload_max_filesize');
            $userMaxSize = config('upload.file_size', '1024');
            if (!empty($userMaxSize)) {
                if (string_to_byte($userMaxSize) < string_to_byte($serverSize)) {
                    self::$maxSizeInfo = $userMaxSize;
                    self::$maxSize = string_to_byte($userMaxSize);
                    return;
                }
            }
            self::$maxSizeInfo = $serverSize;
            self::$maxSize = string_to_byte($serverSize);
        }
    }

    /**
     * 获取上传地址
     * @access public
     * @return array
     */
    public function getUploadUrl()
    {
        $uploadUrl = $this->request->getUri()->getScheme() . '://' . $this->request->getUri()->getAuthority() . '/api/v1/upload?method=add.upload.list';
        $param = [
            ['name' => 'x:replace', 'type' => 'hidden', 'default' => $this->replace],
            ['name' => 'x:parent_id', 'type' => 'hidden', 'default' => 0],
            ['name' => 'x:filename', 'type' => 'hidden', 'default' => ''],
            ['name' => 'key', 'type' => 'hidden', 'default' => ''],
            ['name' => 'token', 'type' => 'hidden', 'default' => ''],
            ['name' => 'file', 'type' => 'file', 'default' => ''],
        ];
        return ['upload_url' => $uploadUrl, 'module' => self::MODULE, 'param' => $param];
    }

    /**
     * 获取上传Token
     * @access public
     * @param string $replace 替换资源(path)
     * @return array
     */
    public function getToken($replace = '')
    {
        empty($replace) ?: $this->replace = $replace;
        $response['upload_url'] = $this->getUploadUrl();
        $response['token'] = self::MODULE;
        $response['dir'] = '';
        return ['token' => $response, 'expires' => 0];
    }

    /**
     * 上传资源
     * @access public
     * @param $files
     * @param $param
     * @return array|false
     */
    public function uploadFiles()
    {
        // 检测请求数据总量不得超过服务器设置值
//        $posMaxSize = ini_get('post_max_size');
//        if ($this->request->server('CONTENT_LENGTH') > string_to_byte($posMaxSize)) {
//            return $this->setError('附件合计总大小不能超过 ' . $posMaxSize);
//        }

        $files = $this->request->getUploadedFiles();
        $param = $this->request->all();
        // 获取上传资源数据
        $filesData = [];

        if (empty($files)) {
            $uploadMaxSize = config('upload.file_size', '1024');
            if (!string_to_byte($uploadMaxSize)) {
                $uploadMaxSize = ini_get('upload_max_filesize');
            }
            throw new FileException(sprintf('请选择需要上传的附件(单附件大小不能超过 %s)', $uploadMaxSize));
        }
        if ($param['x:replace'] == 1 && count($files) > 1) {
            throw new FileException('替换资源只能上传单个文件');
        }

        foreach ($files as $value) {
            if ($param['x:replace'] == 1 && count($value) > 1) {
                throw new FileException('替换资源只能上传单个文件');
            }

            if (is_object($value)) {
                $result = $this->saveFile($value, $param);
                if (is_array($result)) {
                    $filesData[] = $result;
                } else {
                    $filesData[] = ['status' => 500, 'message' => $result];
                }
            } else if (is_array($value)) {
                foreach ($value as $item) {
                    $result = $this->saveFile($item, $param);
                    if (is_array($result)) {
                        $filesData[] = $result;
                    } else {
                        $filesData[] = ['status' => 500, 'message' => $result];
                    }
                }
            }
        }

        return $filesData;
    }

    /**
     * 接收第三方推送数据
     * @access public
     * @return false
     */
    public function putUploadData()
    {
        return $this->setError(self::NAME . '模块异常访问!');
    }

    /**
     * 保存资源并写入库
     * @access private
     * @param UploadedFile $file 上传文件对象
     * @param $param
     * @return array|string
     */
    private function saveFile(UploadedFile $file, $param)
    {
        if (!$file || !$file instanceof UploadedFile) {
            throw new FileException('请选择需要上传的附件');
        }
        // 非法附件检测
        if (in_array($file->getMimeType(), ['text/x-php', 'text/html'])) {
            throw new FileException('禁止上传非法附件');
        }
        if ($file->getSize() > self::$maxSize) {
            throw new FileException('上传的文件不能超过' . self::$maxSize);
        }
        if (!in_array($file->getExtension(), explode(',', config('upload.image_ext')))) {
            throw new FileException('上传的文件类型错误');
        }
        if ($file->getExtension() > self::$maxSize) {
            throw new FileException('上传的文件不能超过' . self::$maxSize);
        }
        // 判断是否为图片
        list($width, $height) = @getimagesize($file->getRealPath());
        $isImage = (int)$width > 0 && (int)$height > 0;
        // 附件相对路径,并统一斜杠为'/'

        $fileName = uuid(5) . '-' . uuid(5) . '-' . uuid(5) . '-' . uuid(5) . '.' . mb_strtolower($file->getExtension(), 'utf-8');
        // 保存附件到项目目录
        $filePath =  DS . 'uploads' . DS . 'files' . DS . date("Ymd") . DS;
        $savePath = BASE_PATH . DS . 'public' . DS .'uploads' . DS . 'files' . DS . date("Ymd") . DS;
        $savePathName = $savePath . $fileName;

        $path = str_replace('\\', '/', $filePath . $fileName);
        // 自定义附件名
        $getFilename = $param['x:filename'];
        $defaultUploadUrl = $this->request->getUri()->getAuthority();
        // 对外访问域名
        $host = config('upload.image_url');
        if (empty($host)) {
            $host = $defaultUploadUrl;
        }        // 写入库数据准备
        $data = [
            'parent_id' => (int)$param['x:parent_id'],
            'name' => !empty($getFilename) ? $getFilename : $file->getFilename(),
            'mime' => $file->getMimeType(),
            'ext' => mb_strtolower($file->getExtension(), 'utf-8'),
            'size' => $file->getSize(),
            'pixel' => $isImage ? ['width' => $width, 'height' => $height] : [],
            'hash' => sha1_file($file->getRealPath()),
            'path' => $path,
            'url' => $host . $path . '?type=' . self::MODULE,
            'protocol' => self::MODULE,
            'type' => $isImage ? 0 : $this->getFileType($file->getMimeType()),
        ];

        if (!empty($param['x:replace'])) {
            unset($data['parent_id']);
            $data['url'] .= sprintf('&rand=%s', mt_rand(0, time()));
        }
        $result = Storage::where(function ($query) use ($data) {
            $query->where('path', $data['path']);
            $query->where('protocol', self::MODULE);
            $query->where('type', '!=', 2);
        })->first();
        if (!empty($result)) {
            // 删除被替换资源的缩略图文件
            if (0 == $result->getAttribute('type')) {
                $thumb = str_replace('\\', DS, $savePath);
                $this->clearThumb($thumb);
            }
            // 替换资源进行更新
            $result->forceFill($data)->save();
            $result->setAttribute('status', 200)->toArray();
        } else {
            // 插入新记录
            (new Storage())->forceFill($data)->save();
            $data['status'] = 200;
            $result = $data;
        }
        !is_dir($savePath) && mkdir( $savePath, 0755, true);
        $file->moveTo($savePathName);
        $result['oss'] = config('upload.default', 'local');
        return $result;
    }

    /**
     * 根据请求参数组合成hash值
     * @access private
     * @param array $param 请求参数
     * @param string $path 资源路径
     * @return string|false
     */
    private function getFileSign($param, $path)
    {
        if (!is_file($path)) {
            return false;
        }

        $sign = sha1_file($path);
        foreach ($param as $key => $value) {
            switch ($key) {
                case 'size':
                case 'crop':
                    if (is_array($value) && count($value) <= 2) {
                        $sign .= ($key . implode('', $value));
                    }
                    break;

                case 'resize':
                case 'format':
                case 'quality':
                    if (is_string($value) || is_numeric($value)) {
                        $sign .= ($key . $value);
                    }
                    break;
            }
        }

        return hash('sha1', $sign);
    }

    /**
     * 组合新的URL或PATH
     * @access private
     * @param string $fileName 文件名
     * @param string $suffix 后缀
     * @param array $fileInfo 原文件信息
     * @param array $urlArray 外部URL信息
     * @param string $type 新的路径方式
     * @return string
     */
    private function getNewUrl($fileName, $suffix, $fileInfo, $urlArray = null, $type = 'url')
    {
        if ($type === 'url') {
            $url = $urlArray['scheme'] . '://';
            $url .= $urlArray['host'];
            $url .= $fileInfo['dirname'];
            $url .= '/' . $fileName;
            $url .= '.' . $suffix;

            if (isset($urlArray['query'])) {
                parse_str($urlArray['query'], $query);
                if (array_key_exists('rand', $query)) {
                    $url .= sprintf('?rand=%s', $query['rand']);
                }
            }
        } else if ($type === 'path') {
            $url = ROOT_PATH . 'public';
            $url .= str_replace(IS_WIN ? '/' : '\\', DS, $fileInfo['dirname']);
            $url .= DS . $fileName;
            $url .= '.' . $suffix;
        } else {
            $url = ROOT_PATH . 'public';
            $url .= str_replace(IS_WIN ? '/' : '\\', DS, $fileInfo['dirname']);
            $url .= DS . $fileInfo['basename'];
        }

        return $url;
    }

    /**
     * 获取缩略大小请求参数
     * @param int $width 宽度
     * @param int $height 高度
     * @param mixed $imageFile 图片文件
     * @param string $resize 缩放方式
     */
    private function getSizeParam(&$width, &$height, $imageFile, $resize)
    {
        if ('pad' === $resize) {
            $width <= 0 && $width = $height;
            $height <= 0 && $height = $width;
        } else if ('proportion' === $resize) {
            list($sWidth, $sHeight) = $imageFile->size();
            $width = ($width / 100) * $sWidth;
            $height = ($width / 100) * $sHeight;
        } else {
            $width <= 0 && $width = $imageFile->width();
            $height <= 0 && $height = $imageFile->height();
        }
    }

    /**
     * 获取资源缩略图实际路径
     * @access public
     * @param array $urlArray 路径结构
     * @return string
     */
    public function getThumbUrl($urlArray)
    {
        // 获取自定义后缀,不合法则使用原后缀
        $fileInfo = pathinfo($urlArray['path']);
        $suffix = $fileInfo['extension'];
        $param = $this->request->param();
        $extension = ['jpg', 'png', 'svg', 'gif', 'bmp', 'tiff', 'webp'];
        $url = $this->getNewUrl($fileInfo['filename'], $fileInfo['extension'], $fileInfo, $urlArray);

        // 非图片资源则直接返回
        if (!in_array(strtolower($fileInfo['extension']), $extension, true)) {
            return $url;
        }

        // 不支持第三方样式,如果存在样式则直接返回
        if (!empty($param['style'])) {
            return $url;
        }

        // 获取源文件位置,并且生成缩略图文件名,验证源文件是否存在
        $source = $this->getNewUrl('', '', $fileInfo, null, null);
        $fileSign = $this->getFileSign($param, $source);

        if (false === $fileSign) {
            return $url . '?error=' . rawurlencode('资源文件不存在');
        }

        // 处理输出格式
        if (!empty($param['suffix'])) {
            if (in_array($param['suffix'], $extension, true)) {
                $suffix = $param['suffix'];
            }
        }

        // 如果缩略图已存在则直接返回(转成缩略图路径)
        $fileInfo['dirname'] .= '/' . $fileInfo['filename'];
        if (is_file($this->getNewUrl($fileSign, $suffix, $fileInfo, null, 'path'))) {
            return $this->getNewUrl($fileSign, $suffix, $fileInfo, $urlArray);
        }

        // 检测尺寸是否正确
        list($sWidth, $sHeight) = @array_pad(isset($param['size']) ? $param['size'] : [], 2, 0);
        list($cWidth, $cHeight) = @array_pad(isset($param['crop']) ? $param['crop'] : [], 2, 0);

        try {
            // 创建图片实例(并且是图片才创建缩略图文件夹)
            $imageFile = Image::open($source);

            $thumb = ROOT_PATH . 'public' . $fileInfo['dirname'];
            $thumb = str_replace(IS_WIN ? '/' : '\\', DS, $thumb);
            !is_dir($thumb) && mkdir($thumb, 0755, true);

            if ($sWidth || $sHeight) {
                // 处理缩放样式
                $resize = isset($param['resize']) ? $param['resize'] : 'scaling';
                $type = 'pad' === $resize ? Image::THUMB_PAD : Image::THUMB_SCALING;

                // 处理缩放尺寸、裁剪尺寸
                foreach ($param as $key => $value) {
                    switch ($key) {
                        case 'size':
                            $this->getSizeParam($sWidth, $sHeight, $imageFile, $resize);
                            $imageFile->thumb($sWidth, $sHeight, $type);
                            break;

                        case 'crop':
                            $cWidth > $imageFile->width() && $cWidth = $imageFile->width();
                            $cHeight > $imageFile->height() && $cHeight = $imageFile->height();
                            $cWidth <= 0 && $cWidth = $imageFile->width();
                            $cHeight <= 0 && $cHeight = $imageFile->height();
                            $x = ($imageFile->width() - $cWidth) / 2;
                            $y = ($imageFile->height() - $cHeight) / 2;
                            $imageFile->crop($cWidth, $cHeight, $x, $y);
                            break;
                    }
                }
            }

            // 处理图片质量
            $quality = 90;
            if (!empty($param['quality'])) {
                $quality = $param['quality'] > 100 ? 100 : $param['quality'];
            }

            // 保存缩略图片
            $savePath = $this->getNewUrl($fileSign, $suffix, $fileInfo, null, 'path');
            $imageFile->save($savePath, $suffix, $quality);
        } catch (\Exception $e) {
            return $url . '?error=' . rawurlencode($e->getMessage());
        }

        return $this->getNewUrl($fileSign, $suffix, $fileInfo, $urlArray);
    }

    /**
     * 批量删除资源
     * @access public
     * @return bool
     */
    public function delFileList()
    {
        foreach ($this->delFileList as $value) {
            $path = ROOT_PATH . 'public' . $value;
            $path = str_replace(IS_WIN ? '/' : '\\', DS, $path);

            $this->clearThumb($path);
            is_file($path) && @unlink($path);
        }

        return true;
    }

    /**
     * 清除缩略图文件夹
     * @access public
     * @param string $path 路径
     * @return void
     */
    public function clearThumb($path)
    {
        // 去掉后缀名,获得目录路径
        $thumb = mb_substr($path, 0, mb_strripos($path, '.', null, 'utf-8'), 'utf-8');

        if (is_dir($thumb) && $this->checkImg($path)) {
            $matches = glob($thumb . DS . '*');
            is_array($matches) && @array_map('unlink', $matches);
            @rmdir($thumb);
        }
    }

    /**
     * 验证是否为图片
     * @access private
     * @param string $path 路径
     * @return bool
     */
    private function checkImg($path)
    {
        $info = @getimagesize($path);
        if (false === $info || (IMAGETYPE_GIF === $info[2] && empty($info['bits']))) {
            return false;
        }

        return true;
    }

    /**
     * 响应实际下载路径
     * @access public
     * @param string $url 路径
     * @param string $filename 文件名
     * @return void
     */
    public function getDownload($url, $filename = '')
    {
        $fileInfo = parse_url($url);
        $filePath = ROOT_PATH . 'public' . $fileInfo['path'];
        $filePath = str_replace(IS_WIN ? '/' : '\\', DS, $filePath);

        if (!is_readable($filePath)) {
            header('status: 404 Not Found', true, 404);
        } else {
            // 设置超时时间,避免文件读取过长而导致内容不全
            set_time_limit(0);

            try {
                $sumBuffer = 0;
                $readBuffer = 2048;
                $fp = fopen($filePath, 'rb');
                $size = filesize($filePath);
                $ua = $this->request->header('user-agent');

                $encodedFileName = urlencode($filename);
                $encodedFileName = str_replace('+', '%20', $encodedFileName);
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Accept-Ranges: bytes');
                header('Accept-Length: ' . $size);

                if (preg_match('/Trident/', $ua)) {
                    header('Content-Disposition: attachment; filename="' . $encodedFileName . '"');
                } else if (preg_match('/Firefox/', $ua)) {
                    header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
                } else {
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                }

                while (!feof($fp) && $sumBuffer < $size) {
                    echo fread($fp, $readBuffer);
                    $sumBuffer += $readBuffer;
                }

                fclose($fp);
            } catch (\Exception $e) {
                header('status: 505 HTTP Version Not Supported', true, 505);
            }

            exit();
        }
    }

    /**
     * 获取资源缩略图信息
     * @access public
     * @param string $url 路径
     * @return array
     */
    public function getThumbInfo($url)
    {
        $info = [
            'size' => 0,
            'width' => 0,
            'height' => 0,
        ];

        try {
            $fileInfo = parse_url($url);
            $pos = mb_strpos($fileInfo['path'], '/');

            $filePath = ROOT_PATH . 'public' . mb_substr($fileInfo['path'], $pos, null, 'utf-8');
            $result = str_replace(IS_WIN ? '/' : '\\', DS, $filePath);

            if (!file_exists($result)) {
                return $info;
            }

            list($width, $height) = @getimagesize($result);
            if ($width <= 0 || $height <= 0) {
                return $info;
            }

            $info = [
                'size' => filesize($result),
                'width' => $width,
                'height' => $height,
            ];
        } catch (\Exception $e) {
            return $info;
        }

        return $info;
    }
}
