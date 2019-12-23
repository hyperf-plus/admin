<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */
use Hyperf\HttpServer\Router\Router;

//Router::get( '/', 'App\Controller\Api\v1\User@index');
//Router::get( '/api/User/info', 'App\Controller\Api\v1\User@index');
//Router::put('/api/User/info', 'App\Controller\Api\v1\User@edit');
//Router::get( '/api/User/token', 'App\Controller\Api\v1\User@token');
Router::post( '/api/v1/help', 'App\Controller\Api\v1\help@index');


