<?php

declare(strict_types=1);

use App\Middleware\CorsMiddleware;
use App\Middleware\SsoMiddleware;
use Hyperf\Validation\Middleware\ValidationMiddleware;

return [
    'http' => [
        ValidationMiddleware::class,
        CorsMiddleware::class,
        SsoMiddleware::class,
    ],
];
