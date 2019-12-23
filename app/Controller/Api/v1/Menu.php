<?php
declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Controller\AbstractController;
use App\Service\MenuService;
use App\Traits\GetFastAction;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @package App\Controller\Api\v1
 * @AutoController()
 */
class Menu extends AbstractController
{
    /**
     * @Inject()
     * @var MenuService
     */
    protected $menuService;

    use GetFastAction;

    /**
     * 获取当前登录账号对应的权限数据
     * @throws Exception
     */
    public function auth()
    {
        $data = $this->request->getParsedBody();
        return $this->json($this->menuService->auth($data));
    }


    /**
     * @return array
     * @throws \Exception
     */
    public function list()
    {
        $data = $this->request->getParsedBody();
        $result = $this->menuService->list($data);
        return $this->json($result);
    }


    /**
     * @return array
     * @throws \Exception
     */
    public function update()
    {
        $data = $this->request->getParsedBody();
        $result = $this->menuService->update($data);
        return $this->json($result);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function update_index()
    {
        $data = $this->request->getParsedBody();
        $result = $this->menuService->update_index($data);
        return $this->json($result);
    }


}
