<?php
declare(strict_types=1);


use Hyperf\HttpServer\Router\Router;

Router::get('/Test/index', 'App\Controller\TestController::index');

define('AUTH_CLIENT', 0);
