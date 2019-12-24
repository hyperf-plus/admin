<?php
declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Annotations\AuthUpEvict;
use App\Controller\AbstractController;
use App\Service\AuthRuleService;
use App\Traits\GetFastAction;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * @package App\Controller\Api\v1
 * @Controller()
 */
class AuthRule extends AbstractController
{
    use GetFastAction;

    /**
     * @Inject()
     * @var AuthRuleService
     */
    protected AuthRuleService $authRuleService;

    /**
     * @RequestMapping(path="list")
     * @return array
     * @throws \Exception
     */
    public function list()
    {
        $data = $this->request->getParsedBody();
        $result = $this->authRuleService->list($data);
        return $this->json($result);
    }

    /**
     * @RequestMapping(path="update")
     * @AuthUpEvict()
     * @throws \Exception
     */
    public function up()
    {
        return $this->update();
    }

    /**
     * @RequestMapping(path="create")
     * @AuthUpEvict()
     * @throws \Exception
     */
    public function add()
    {
        return $this->create();
    }


}