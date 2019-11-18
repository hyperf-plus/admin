<?php
namespace App\Extend\oss;


use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

abstract class Upload
{
    /**
     * 错误信息
     * @var string
     */
    protected $error = '';

    /**
     * 待删除资源列表
     * @var array
     */
    protected $delFileList = [];

    /**
     * 待删除资源Id列表
     * @var array
     */
    protected $delFileIdList = [];

    /**
     * 资源替换
     * @var string
     */
    protected $replace = '';

    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * @var RequestInterface
     */
    public $request;

    /**
     * @var ResponseInterface
     */
    public $response;

    /**
     * 构造函数
     * @access public
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->request = $container->get(RequestInterface::class);
        $this->setFileMaxSize();
    }
    /**
     * 添加待删除资源
     * @access public
     * @param  string $path 资源路径
     * @return void
     */
    public function addDelFile($path)
    {
        $this->delFileList[] = $path;
    }

    /**
     * 添加待删除资源Id
     * @access public
     * @param  mixed $id 资源Id
     * @return void
     */
    public function addDelFileId($id)
    {
        $this->delFileIdList[] = $id;
    }

    /**
     * 获取待删除资源Id列表
     * @access public
     * @return array
     */
    public function getDelFileIdList()
    {
        return $this->delFileIdList;
    }

    /**
     * 查询条件数据转字符
     * @access public
     * @param  array $options 查询条件
     * @return string
     */
    protected function queryToString($options = [])
    {
        $temp = [];
        foreach ($options as $key => $value) {
            if (is_string($key) && !is_array($value)) {
                $temp[] = rawurlencode($key) . '=' . rawurlencode($value);
            }
        }

        return implode('&', $temp);
    }

    /**
     * 根据文件mime判断资源类型 1=普通资源 3=视频资源
     * @access public
     * @param  array $options 查询条件
     * @return string
     */
    protected function getFileType($mime)
    {
        if (stripos($mime, 'video') !== false) {
            return 3;
        }

        return 1;
    }

    /**
     * 获取上传地址
     * @access protected
     * @return array
     */
    abstract protected function getUploadUrl();

    /**
     * 获取上传Token
     * @access protected
     * @param  string $replace 替换资源(path)
     * @return array
     */
    abstract protected function getToken($replace = '');

    /**
     * 接收第三方推送数据
     * @access protected
     * @return array
     */
    abstract protected function putUploadData();

    /**
     * 上传资源
     * @access protected
     * @return array
     */
    abstract protected function uploadFiles();

    /**
     * 获取资源缩略图实际路径
     * @access protected
     * @param  array $urlArray 路径结构
     * @return void
     */
    abstract protected function getThumbUrl($urlArray);

    /**
     * 批量删除资源
     * @access protected
     * @return bool
     */
    abstract protected function delFileList();

    /**
     * 批量删除资源
     * @access protected
     * @param  string $path 路径
     * @return void
     */
    abstract protected function clearThumb($path);

    /**
     * 响应实际下载路径
     * @access protected
     * @param  string $url      路径
     * @param  string $filename 文件名
     * @return void
     */
    abstract protected function getDownload($url, $filename);

    /**
     * 获取资源缩略图信息
     * @access protected
     * @param  string $url 路径
     * @return array
     */
    abstract protected function getThumbInfo($url);

    protected function setFileMaxSize()
    {
    }
}
