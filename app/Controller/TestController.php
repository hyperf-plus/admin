<?php

namespace App\Controller;

use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 */
class TestController
{

    public function index()
    {
        p(123123);
        CacheClear('CommonAuth-update', 'api',  1);
        return '123123';

    }

}
