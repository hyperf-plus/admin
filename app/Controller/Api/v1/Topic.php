<?php
declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Controller\AbstractController;
use App\Service\TopicService;
use App\Traits\GetFastAction;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @package App\Controller\Api\v1
 * @AutoController()
 */
class Topic extends AbstractController
{
    /**
     * @Inject()
     * @var TopicService
     */
    protected  $topicService;

    //快捷方式里需要用判断当前是在哪个模型里
    use GetFastAction;

    /**
     * @return array
     * @throws \Exception
     */
    public function list()
    {
        $data = $this->request->getParsedBody();
        $result = $this->topicService->list($data);
        return $this->json($result);
    }
}
