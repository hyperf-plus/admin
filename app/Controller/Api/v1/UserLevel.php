<?php
declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Controller\AbstractController;
use App\Service\UserLevelService;
use App\Traits\GetFastAction;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @package App\Controller\Api\v1
 * @AutoController()
 */
class UserLevel extends AbstractController
{
    use GetFastAction;

    /**
     * @Inject()
     * @var UserLevelService
     */
    protected  $userLevelService;

    /**
     * 获取等级列表
     * @return array
     * @throws Exception
     */
    public function list()
    {
        $data = $this->request->getParsedBody();
        $result = $this->userLevelService->list($data);
        return $this->json($result );
    }
}