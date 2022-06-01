<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\OAuthController;
use App\Controller\Hr\KPI\ItemController;
use App\Controller\Hr\KPI\RankController;
use App\Controller\Hr\KPI\TitleController;
use App\Controller\Prod\ScheduleController;
use App\Controller\Application\ApplicationController;
use App\Controller\Bs\OrderController;
use Hyperf\HttpServer\Router\Router;
use App\Controller\Hr\KPI\PositionController;
use App\Controller\Hr\KPI\PositionGroupController;
use App\Controller\Hr\KPI\TitleCategoryController;


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

        Router::addRoute(['GET'], '/item-categories', [ItemCategoryController::class, 'getAllItemCategory']);

        Router::addRoute(['GET'], '/titles', [\App\Controller\Hr\KPI\TitleController::class, 'getAllTitle']);

        Router::addRoute(['GET'], '/title-categories', [\App\Controller\Hr\KPI\TitleCategoryController::class, 'getAllTitleCategory']);

        Router::addRoute(['GET'], '/ranks', [RankController::class, 'getAllItem']);

        Router::addRoute(['GET'], '/positions', [\App\Controller\Hr\KPI\PositionController::class, 'getAllPosition']);

        Router::addRoute(['GET'], '/position-groups', [\App\Controller\Hr\KPI\PositionGroupController::class, 'getAllPositionGroup']);
    });

    Router::addGroup('/tpm', function () {
        Router::addRoute(['GET'], '/fittings', [\App\Controller\TPM\FittingController::class, 'fittings']);
        Router::addRoute(['GET'], '/machines', [\App\Controller\TPM\MachineController::class, 'machines']);
        Router::addRoute(['GET'], '/staffs', [\App\Controller\TPM\StaffController::class, 'staffs']);
        Router::addRoute(['GET'], '/quickReport', [\App\Controller\TPM\ReportController::class, 'report']);

        Router::addRoute(['GET'], '/records', [\App\Controller\TPM\RecordController::class, 'records']);
    });

    Router::get('/sayHello', 'App\Controller\IndexController@sayHello');
});

Router::addServer('grpc', function () {
    Router::addGroup('/grpc.hi', function () {
        Router::post('/sayHello', 'App\Controller\IndexController@sayHello');
    });
});
