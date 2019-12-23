<?php
declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Controller\AbstractController;
use App\Service\AuthGroupService;
use App\Traits\GetFastAction;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @package App\Controller\Api\v1
 * @AutoController()
 */
class AuthGroup extends AbstractController
{
    use GetFastAction;

    /**
     * @Inject()
     * @var AuthGroupService
     */
    protected AuthGroupService $authGroupService;


    /**
     * @return array
     * @throws \Exception
     */
    public function list()
    {
        $data = $this->request->getParsedBody();
        $result = $this->authGroupService->list($data);
        return $this->json($result);
    }


}