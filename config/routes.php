<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\Hr\KPI\PositionController;
use App\Controller\Hr\KPI\PositionGroupController;
use App\Controller\Hr\KPI\PositionGroupScoreController;
use App\Controller\Hr\KPI\PositionItemController;
use App\Controller\Hr\KPI\RuleController;
use App\Controller\Hr\KPI\RuleItemController;
use App\Controller\Hr\KPI\StaffController;
use App\Controller\Hr\KPI\StaffScoreController;
use App\Controller\Hr\KPI\TitleController;
use App\Controller\OAuthController;
use App\Controller\Hr\KPI\ItemCategoryController;
use App\Controller\Hr\KPI\ItemController;
use App\Controller\Hr\KPI\RankController;
use App\Controller\Prod\CalendarController;
use App\Controller\Prod\ParamController;
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
        Router::addRoute(['GET'], '/calendars', [CalendarController::class, 'getAll']);
        Router::addRoute(['GET'], '/calendar', [CalendarController::class, 'getCalendarByDate']);
        Router::addRoute(['POST'], '/calendar', [CalendarController::class, 'addCalendar']);
        Router::addRoute(['PUT'], '/calendar/{id}', [CalendarController::class, 'updateCalendarById']);
        Router::addRoute(['GET'], '/settings/parameters', [ParamController::class, 'getAll']);
        Router::addRoute(['GET'], '/settings/parameter', [ParamController::class, 'getParamByKey']);
        Router::addRoute(['PUT'], '/settings/parameter', [ParamController::class, 'setParamByKey']);
    });

    Router::addGroup('/hr', function () {
        Router::addRoute(['GET'], '/items', [ItemController::class, 'getAllItem']);

        Router::addRoute(['POST'], '/items/batch', [ItemController::class, 'saveCurdItem']);

        Router::addRoute(['GET'], '/item-categories', [ItemCategoryController::class, 'getAllItemCategory']);

        Router::addRoute(['POST'], '/item-categories/batch', [ItemCategoryController::class, 'saveCrudItemCategory']);

        Router::addRoute(['GET'], '/titles', [TitleController::class, 'getAllTitle']);

        Router::addRoute(['POST'], '/titles/batch', [TitleController::class, 'saveCrudTitle']);

        Router::addRoute(['GET'], '/ranks', [RankController::class, 'getAllRank']);

        Router::addRoute(['POST'], '/ranks/batch', [RankController::class, 'saveCrudRank']);

        Router::addRoute(['GET'], '/rank-titles', [RankTitleController::class, 'getAllRankTitle']);

        Router::addRoute(['POST'], '/rank-titles/batch', [RankTitleController::class, 'saveCrudRankTitle']);

        Router::addRoute(['GET'], '/positions', [PositionController::class, 'getAllPosition']);

        Router::addRoute(['POST'], '/positions/batch', [PositionController::class, 'saveCrudPosition']);

        Router::addRoute(['GET'], '/position-items', [PositionItemController::class, 'getAllPositionItem']);

        Router::addRoute(['POST'], '/position-items/batch', [PositionItemController::class, 'saveCrudPositionItem']);

        Router::addRoute(['GET'], '/position-groups', [PositionGroupController::class, 'getAllPositionGroup']);

        Router::addRoute(['POST'], '/position-groups/batch', [PositionGroupController::class, 'saveCrudPositionGroup']);

        Router::addRoute(['GET'], '/staffs', [StaffController::class, 'getAllStaff']);

        Router::addRoute(['POST'], '/staffs/batch', [StaffController::class, 'saveCrudStaff']);

        Router::addRoute(['GET'], '/rules', [RuleController::class, 'getAll']);

        Router::addRoute(['POST'], '/rules/batch', [RuleController::class, 'saveCrud']);

        Router::addRoute(['GET'], '/rule-items', [RuleItemController::class, 'getAll']);

        Router::addRoute(['POST'], '/rule-items/batch', [RuleItemController::class, 'saveCrud']);

        Router::addRoute(['GET'], '/group-scores', [PositionGroupScoreController::class, 'getAll']);

        Router::addRoute(['POST'], '/group-scores/batch', [PositionGroupScoreController::class, 'saveCrud']);

        Router::addRoute(['GET'], '/staff-scores', [StaffScoreController::class, 'getAll']);

        Router::addRoute(['POST'], '/staff-scores/batch', [StaffScoreController::class, 'saveCrud']);
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
