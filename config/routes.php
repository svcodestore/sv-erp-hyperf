<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\Hr\KPI\ItemCategoryController;
use App\Controller\Hr\KPI\ItemController;
use App\Controller\Hr\KPI\RankController;
use App\Controller\Prod\ScheduleController;
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@sayHello');
Router::addRoute(['GET'], '/api', 'App\Controller\IndexController@index');

Router::get('/favicon.ico', function () {
    return '';
});


Router::addGroup('/api', function () {
    Router::addGroup('/oauth2.0', function () {
        Router::addRoute(['POST'], '/token', [AuthController::class, 'getAccessToken']);
    });

    Router::post('/logout', [AuthController::class, 'logout']);

    Router::addGroup('/prod', function () {
        Router::addRoute(['GET'], '/schedule', [ScheduleController::class, 'schedule']);
        Router::addRoute(['GET'], '/phases', [ScheduleController::class, 'getPhaseByCode']);
        Router::addRoute(['GET'], '/phases/[{code}]', [ScheduleController::class, 'getPhaseByCode']);
        Router::addRoute(['GET'], '/po', [ScheduleController::class, 'getPo']);
    });

    Router::addGroup('/hr', function () {
        Router::addRoute(['GET'], '/items', [ItemController::class, 'getAllItem']);

        Router::addRoute(['GET'], '/item-categories', [ItemCategoryController::class, 'getAllItemCategory']);

        Router::addRoute(['GET'], '/titles', [\App\Controller\Hr\KPI\TitleController::class, 'getAllTitle']);

        Router::addRoute(['GET'], '/title-categories', [\App\Controller\Hr\KPI\TitleCategoryController::class, 'getAllTitleCategory']);

        Router::addRoute(['GET'], '/ranks', [RankController::class, 'getAllItem']);

        Router::addRoute(['GET'], '/positions', [\App\Controller\Hr\KPI\PositionController::class, 'getAllPosition']);

        Router::addRoute(['GET'], '/position-groups', [\App\Controller\Hr\KPI\PositionGroupController::class, 'getAllPositionGroup']);
    });
});

Router::addServer('grpc', function () {
    Router::addGroup('/grpc.hi', function () {
        Router::post('/sayHello', 'App\Controller\HiController@sayHello');
    });
});
