<?php
declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Controller\AbstractController;

/**
 * @package App\Controller\Api\v1
 */
class Help extends AbstractController
{

    /**
     * @return array
     * @throws \Exception
     */
    public function index()
    {
        return $this->json(['content' => '本栏目暂无帮助内容']);
    }
}
