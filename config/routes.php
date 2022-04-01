<?php

declare(strict_types=1);

use App\Controller\Prod\ScheduleController;
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');
Router::addRoute(['GET'], '/api', 'App\Controller\IndexController@index');

Router::get('/favicon.ico', function () {
    return '';
});


Router::addGroup('/api', function () {
    Router::addRoute(['GET'], '/schedule', [ScheduleController::class, 'schedule']);
});
