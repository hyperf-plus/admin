<?php
declare(strict_types=1);

namespace HPlus\Admin\Traits;
use HPlus\UI\Components\Widgets\Html;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

trait HasApiBase
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

    protected function br()
    {
        return Html::make()->html("<br>");
    }


    protected function isPost(): bool
    {
        return strtolower($this->request->getMethod()) == 'post';
    }

    protected function isGet(): bool
    {
        return strtolower($this->request->getMethod()) == 'get';
    }

    protected function isPut(): bool
    {
        return strtolower($this->request->getMethod()) == 'put';
    }

    protected function isDelete(): bool
    {
        return strtolower($this->request->getMethod()) == 'delete';
    }

    protected function isGetData()
    {
        return $this->request->query('get_data') == "true";
    }
}