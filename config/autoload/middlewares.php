<?php

declare(strict_types=1);

use App\Middleware\CorsMiddleware;

return [
    'http' => [
        \Hyperf\Validation\Middleware\ValidationMiddleware::class,
        App\Middleware\CorsMiddleware::class,
    ],
];
