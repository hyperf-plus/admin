<?php
declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Controller\AbstractController;
use App\Service\ActionLogService;
use App\Traits\GetFastAction;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Mzh\JwtAuth\Annotations\AuthUpEvict;

/**
 * @package App\Controller\Api\v1
 * @AutoController()
 */
class ActionLog extends AbstractController
{
    use GetFastAction;

    /**
     * @Inject()
     * @var ActionLogService
     */
    protected ActionLogService $logService;

    /**
     * @return array
     * @throws \Exception
     */
    public function list()
    {
        $data = $this->request->getParsedBody();
        $result = $this->logService->list($data);
        return $this->json($result);
    }

}