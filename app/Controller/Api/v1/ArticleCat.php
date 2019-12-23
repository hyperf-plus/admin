<?php
declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Controller\AbstractController;
use App\Service\ArticleCatService;
use App\Service\MenuService;
use App\Traits\GetFastAction;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @package App\Controller\Api\v1
 * @AutoController()
 */
class ArticleCat extends AbstractController
{
    /**
     * @Inject()
     * @var ArticleCatService
     */
    protected ArticleCatService $articleCatService;

    use GetFastAction;

    /**
     * @return array
     * @throws \Exception
     */
    public function list()
    {
        $data = $this->request->getParsedBody();
        $result = $this->articleCatService->list($data);
        return $this->json($result);
    }
}
