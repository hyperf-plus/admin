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

use App\Kernel\Http\Response;
use App\Model\Entity\User;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;

abstract class Controller
{
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
     * @var User
     */
    public $user;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->request = $container->get(RequestInterface::class);
        $this->response = $container->get(Response::class);
        $this->user = $container->get(User::class);
    }

    public function isPost()
    {
        return $this->request->isMethod('post');
    }

    public function isGet()
    {
        return $this->request->isMethod('get');
         }

    public function getAdmin()
    {
        return $this->request->getAttribute('admin');
    }

    public function getAdminID()
    {
        $admin = $this->getAdmin();
        return $admin['user_id'];
    }

    public function getAdminName()
    {
        $admin = $this->getAdmin();
        return $admin['user_name'];
    }

    public function getAdminRole()
    {
        $admin = $this->getAdmin();
        return $admin['role'];
    }
}
