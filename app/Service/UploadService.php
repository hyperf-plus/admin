<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\FileException;
use App\Extend\oss\local\Upload;
use Hyperf\Utils\Str;

class UploadService
{
    /**
     * 获取上传模块列表
     * @access public
     * @return array
     */
    public function getUploadModule()
    {
        $moduleList = [
            [
                'name' => Upload::NAME,
                'module' => Upload::MODULE,
                'default' => 0,
            ]
        ];
        $default = config('upload.default', 'upload');
        foreach ($moduleList as &$module) {
            if ($default === $module['module']) {
                $module['default'] = 1;
                break;
            }
        }
        return $moduleList;
    }

    /**
     * 创建资源上传对象
     * @access public
     * @param string $file 目录
     * @param string $model 模块
     * @return  |false
     */
    public function createOssObject($file, $model = 'Upload')
    {
        // 转换模块的名称
        $file = Str::lower($file);
        $model = Str::studly($model);
        if (empty($file) || empty($model)) {
            throw new FileException('资源目录或模块不存在');
        }
        $ossObject = '\\App\\Extend\\oss\\' . $file . '\\' . $model;
        if (class_exists($ossObject, false)) {
            return make($ossObject);
        }
        throw new FileException($ossObject . '模块不存在');
    }

    /**
     * 获取上传地址
     * @access public
     * @return mixed
     */
    public function getUploadUrl()
    {
        $file = $this->getModuleName();
        if (false === $file) {
            return false;
        }

        $ossObject = $this->createOssObject($file);
        if (false === $ossObject) {
            return false;
        }

        $result = $ossObject->getUploadUrl();
        if (false === $result) {
            return $this->setError($ossObject->getError());
        }

        return $result;
    }

    /**
     * 获取上传Token
     * @access public
     * @return mixed
     */
    public function getUploadToken($data)
    {
        $file = $this->getModuleName($data);
        if (false === $file) {
            throw new FileException('模块不存在');
        }

        /**
         * @var Upload $ossObject
         */
        $ossObject = $this->createOssObject($file);
        $result = $ossObject->getToken();
        // 附加可上传后缀及附件大小限制
        $result['image_ext'] = config('upload.image_ext');
        $result['file_ext'] = config('upload.file_ext');
        $result['file_size'] = config('upload.file_size');
        return $result;
    }

    /**
     * 替换上传资源
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function replaceUploadItem($data)
    {
        $validate = Loader::validate('Storage');
        if (!$validate->scene('replace')->check($data)) {
            return $this->setError($validate->getError());
        }

        // 获取已存在资源数据
        $map['storage_id'] = ['eq', $data['storage_id']];
        $map['type'] = ['neq', 2];

        $storageDB = new Storage();
        $storageData = $storageDB->field('path,protocol')->where($map)->find();

        if (!$storageData) {
            return $this->setError(is_null($storageData) ? '资源不存在' : $storageDB->getError());
        }

        $ossObject = $this->createOssObject($storageData->getAttr('protocol'));
        if (false === $ossObject) {
            return false;
        }

        $result = $ossObject->getToken($storageData->getAttr('path'));
        if (false === $result) {
            return $this->setError($ossObject->getError());
        }

        // 附加可上传后缀及附件大小限制
        $result['image_ext'] = Config::get('image_ext.value', 'upload');
        $result['file_ext'] = Config::get('file_ext.value', 'upload');
        $result['file_size'] = Config::get('file_size.value', 'upload');

        return $result;
    }

    /**
     * 当参数为空时获取默认上传模块名,否则验证指定模块名并返回
     * @access public
     * @param $data
     * @return string|false
     */
    private function getModuleName($data)
    {
        $module = $data['module'];
        if (empty($module)) {
            return config('upload.default', 'local');
        }

        $moduleList = array_column($this->getUploadModule(), 'module');
        if (!in_array($module, $moduleList)) {
            throw new FileException('上传模块名 ' . $module . ' 不存在');
        }
        return $module;
    }

    /**
     * 资源上传请求(第三方OSS只能单文件直传方式上传)
     * @access public
     * @return mixed
     */
    public function addUploadList($data)
    {
        $ossObject = $this->createOssObject('local');
        $result = $ossObject->uploadFiles();
        return $result;
    }

    /**
     * 接收第三方推送数据
     * @access public
     * @return mixed
     */
    public function putUploadData()
    {
        $ossObject = $this->createOssObject(Request::instance()->param('module', ''));
        if (false === $ossObject) {
            return false;
        }

        $result = $ossObject->putUploadData();
        if (false === $result) {
            return $this->setError($ossObject->getError());
        }

        return $result;
    }

    /**
     * 获取资源缩略图
     * @access public
     * @return mixed
     */
    public function getThumb()
    {
        $url = $this->getThumbUrl();
        if (false === $url) {
            $request = Request::instance();
            $oldUrl = $request->param('url');

            header('Location:' . $oldUrl, true, 301);
            exit;
        }

        if (empty($url['url_prefix'])) {
            header('status: 404 Not Found', true, 404);
        } else {
            header('Location:' . $url['url_prefix'], true, 301);
        }

        exit;
    }

    /**
     * 获取资源缩略图实际路径
     * @access public
     * @param bool $getObject 是否返回OSS组件对象
     * @return mixed
     */
    public function getThumbUrl($getObject = false)
    {
        // 补齐协议地址
        $request = Request::instance();
        $url = $request->param('url');

        $pattern = '/^((http|https)?:\/\/)/i';
        if (!preg_match($pattern, $url)) {
            $url = ($request->isSsl() ? 'https://' : 'http://') . $url;
        }

        // 从URL分析获取对应模型
        $urlArray = parse_url($url);
        if (!isset($urlArray['query'])) {
            return $this->setError('请填写合法的url参数');
        }

        parse_str($urlArray['query'], $items);
        if (!array_key_exists('type', $items)) {
            return $this->setError('type参数值不能为空');
        }

        $pact = array_column($this->getUploadModule(), 'module');
        if (!in_array($items['type'], $pact)) {
            return $this->setError('type协议错误');
        }

        // 是否定义资源样式
        if ($request->has('code', 'param', true)) {
            $style = new StorageStyle();
            $styleResult = $style->getStorageStyleCode(['code' => $request->param('code')]);

            if ($styleResult) {
                foreach ($styleResult as $key => $value) {
                    if ('scale' === $key) {
                        $isMobile = $request->isMobile() ? 'mobile' : 'pc';
                        if (array_key_exists($isMobile, $value)) {
                            $request->get($value[$isMobile]);
                        }

                        continue;
                    }

                    $request->get([$key => $value]);
                }
            }
        }

        $ossObject = $this->createOssObject($items['type']);
        if (false === $ossObject) {
            return false;
        }

        $url = $ossObject->getThumbUrl($urlArray);
        $notPrefix = preg_replace($pattern, '', $url);

        $data = [
            'source' => $items['type'],
            'url' => $notPrefix,
            'url_prefix' => strval($url),
        ];

        if (is_bool($getObject) && $getObject) {
            $data['ossObject'] = &$ossObject;
        }

        return $data;
    }

    /**
     * 获取资源缩略图信息
     * @access public
     * @return mixed
     */
    public function getThumbInfo()
    {
        // 协议头
        $request = Request::instance();

        $url = $request->param('url');
        if (!$url) {
            return $this->setError('url参数值不能为空');
        }

        $source = $request->param('source');
        if (!$source) {
            return $this->setError('source参数值不能为空');
        }

        if (!in_array($source, array_column($this->getUploadModule(), 'module'))) {
            return $this->setError('source参数值错误');
        }

        $ossObject = $this->createOssObject($source);
        if (false === $ossObject) {
            return false;
        }

        return $ossObject->getThumbInfo($url);
    }

    /**
     * 获取资源下载链接
     * @access public
     * @return void
     */
    public function getDownload()
    {
        // 下载的资源还是需要经过样式处理
        $url = $this->getThumbUrl(true);
        $request = Request::instance();

        // 文件不存在,则返回 404 错误提示
        if (empty($url['url_prefix'])) {
            header('status: 404 Not Found', true, 404);
            exit();
        }

        // 不需要强制另存为文件名,也直接返回
        if (!$request->has('filename')) {
            header('Location:' . $url['url_prefix'], true, 301);
            exit();
        }

        // 最终的处理方式由组件决定
        if (isset($url['ossObject'])) {
            $url['ossObject']->getDownload($url['url_prefix'], $request->get('filename'));
        }

        exit();
    }
}
