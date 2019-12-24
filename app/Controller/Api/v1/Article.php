<?php
declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Controller\AbstractController;
use App\Service\ArticleService;
use App\Traits\GetFastAction;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @package App\Controller\Api\v1
 * @AutoController()
 */
class Article extends AbstractController
{
    /**
     * @Inject()
     * @var ArticleService
     */
    protected ArticleService $articleService;

    use GetFastAction;

    /**
     * @return array
     * @throws \Exception
     */
    public function list()
    {
        $data = $this->request->getParsedBody();
        $result = $this->articleService->list($data);
        return $this->json($result);
    }
}
