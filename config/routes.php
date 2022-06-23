<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\OAuthController;
use App\Controller\Hr\KPI\ItemCategoryController;
use App\Controller\Hr\KPI\ItemController;
use App\Controller\Hr\KPI\RankController;
use App\Controller\Prod\ScheduleController;
use App\Controller\Application\ApplicationController;
use App\Controller\Bs\OrderController;
use App\Controller\Hr\KPI\RankTitleController;
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@sayHello');
Router::addRoute(['GET'], '/api', 'App\Controller\IndexController@index');

Router::get('/favicon.ico', function () {
    return '';
});


Router::addGroup('/api', function () {
    Router::addGroup('/oauth2.0', function () {
        Router::addRoute(['POST'], '/token', [OAuthController::class, 'getAccessToken']);
    });

    Router::post('/logout', [OAuthController::class, 'logout']);

    Router::addGroup('/application', function () {
        Router::addRoute(['GET'], '/current-application', [ApplicationController::class, 'getCurrentApplication']);
    });

    Router::addGroup('/authorization', function () {
        Router::addRoute(['GET'], '/user-menus', [AuthController::class, 'getUserMenusByAppIdAndUserId']);
    });

    Router::addGroup('/prod', function () {
        Router::addRoute(['GET'], '/schedule', [ScheduleController::class, 'schedule']);
        Router::addRoute(['GET'], '/phases', [ScheduleController::class, 'getPhaseByCode']);
        Router::addRoute(['GET'], '/phases/[{code}]', [ScheduleController::class, 'getPhaseByCode']);
        Router::addRoute(['GET'], '/po', [ScheduleController::class, 'getPo']);
    });

    Router::addGroup('/hr', function () {
        Router::addRoute(['GET'], '/items', [ItemController::class, 'getAllItem']);

        Router::addRoute(['POST'], '/items/batch', [ItemController::class, 'saveCurdItem']);

        Router::addRoute(['GET'], '/item-categories', [ItemCategoryController::class, 'getAllItemCategory']);

        Router::addRoute(['POST'], '/item-categories/batch', [ItemCategoryController::class, 'saveCrudItemCategory']);

        Router::addRoute(['GET'], '/titles', [\App\Controller\Hr\KPI\TitleController::class, 'getAllTitle']);

        Router::addRoute(['POST'], '/titles/batch', [\App\Controller\Hr\KPI\TitleController::class, 'saveCrudTitle']);

        Router::addRoute(['GET'], '/ranks', [RankController::class, 'getAllRank']);

        Router::addRoute(['POST'], '/ranks/batch', [RankController::class, 'saveCrudRank']);

        Router::addRoute(['GET'], '/rank-titles', [RankTitleController::class, 'getAllRankTitle']);

        Router::addRoute(['POST'], '/rank-titles/batch', [RankTitleController::class, 'saveCrudRankTitle']);

        Router::addRoute(['GET'], '/positions', [\App\Controller\Hr\KPI\PositionController::class, 'getAllPosition']);

        Router::addRoute(['POST'], '/positions/batch', [\App\Controller\Hr\KPI\PositionController::class, 'saveCrudPosition']);

        Router::addRoute(['GET'], '/position-items', [\App\Controller\Hr\KPI\PositionItemController::class, 'getAllPositionItem']);

        Router::addRoute(['POST'], '/position-items/batch', [\App\Controller\Hr\KPI\PositionItemController::class, 'saveCrudPositionItem']);

        Router::addRoute(['GET'], '/position-groups', [\App\Controller\Hr\KPI\PositionGroupController::class, 'getAllPositionGroup']);

        Router::addRoute(['POST'], '/position-groups/batch', [\App\Controller\Hr\KPI\PositionGroupController::class, 'saveCrudPositionGroup']);

        Router::addRoute(['GET'], '/staffs', [\App\Controller\Hr\KPI\StaffController::class, 'getAllStaff']);

        Router::addRoute(['POST'], '/staffs/batch', [\App\Controller\Hr\KPI\StaffController::class, 'saveCrudStaff']);
    });

    Router::addGroup('/bs', function () {
        Router::addRoute(['GET'], '/orders', [OrderController::class, 'getAllOrder']);
        Router::addRoute(['GET'], '/order-details', [OrderController::class, 'getOrderDetails']);
    });

    Router::get('/sayHello', 'App\Controller\IndexController@sayHello');
});

Router::addServer('grpc', function () {
    Router::addGroup('/grpc.hi', function () {
        Router::post('/sayHello', 'App\Controller\IndexController@sayHello');
    });
});
