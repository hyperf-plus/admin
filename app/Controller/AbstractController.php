<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Model\Model;
use Exception;
use Hyperf\Database\Query\Builder;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Mzh\Validate\Validate\Validate;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    public function json($data = [], $msg = '', $code = 200)
    {
        return [
            'status' => $code,
            'message' => $msg,
            'data' => $data
        ];
    }


    public function success($data = [], $msg = '', $code = 200)
    {
        return [
            'status' => $code,
            'message' => $msg,
            'data' => $data
        ];
    }


    public function error($msg = '', $data = [], $code = 500)
    {
        return [
            'status' => $code,
            'message' => $msg,
            'data' => $data
        ];
    }

    /**
     * 使用验证器注解、并且开启严格过滤模式后，这里取到的数据是安全的
     * @return array|object|null
     */
    public function validated()
    {
        return $this->request->getParsedBody();
    }

}
