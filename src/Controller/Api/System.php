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
use Mzh\Admin\Service\ConfigService;
use Mzh\Swagger\Annotation\ApiController;
use Mzh\Swagger\Annotation\GetApi;
use Psr\Http\Message\ResponseInterface;

/**
 * @ApiController(tag="后台-系统配置模块", description="系统")
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
        $config = ConfigService::getConfig('system');
        return $this->response->json($config);
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
