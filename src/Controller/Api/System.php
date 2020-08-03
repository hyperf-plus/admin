<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Mzh\Admin\Controller\Api;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Str;
use Mzh\Admin\Controller\AbstractController;
use Mzh\Admin\Service\AuthService;
use Mzh\Swagger\Annotation\ApiController;
use Mzh\Swagger\Annotation\GetApi;
use Psr\Http\Message\ResponseInterface;

/**
 * @ApiController(tag="用户模块", description="系统")
 * Class IndexController
 * @package Mzh\Admin\Controller
 */
class System extends AbstractController
{

    /**
     * @Inject()
     * @var AuthService $authService
     */
    protected $authService;

    /**
     * @GetApi(security=true)
     * @return ResponseInterface
     */
    public function config()
    {
        $json = '
        {"open_export":0,"navbar_notice":"cccccc","system_module":[{"icon":"el-icon-setting","name":"system","label":"系统","indexUrl":"\/system\/#\/dashboard"},{"icon":"eye-open","name":"default","label":"首页","indexUrl":"\/default\/#\/dashboard"}],"open_screen_lock":0,"screen_autho_lock_time":36}
        ';
        return $this->response->json(json_decode($json, true));
    }

    /**
     * @GetApi(security=true)
     * @return ResponseInterface
     */
    public function routes()
    {
        $kw = $this->request->input('kw', '');
        $routes = $this->authService->getSystemRouteOptions();
        $routes = array_filter($routes, function ($item) use ($kw) {
            return Str::contains($item['value'], $kw);
        });
        return $this->response->json(array_values($routes));
    }
}
