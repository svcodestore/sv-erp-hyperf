<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\ResponseCode;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    public function responseOk($data = [])
    {
        return [
            'code' => ResponseCode::RESPONSE_OK,
            'data' => $data,
            'message' => 'ok',
        ];
    }

    public function responseDetail(string $message, array $data = [], int $code = ResponseCode::RESPONSE_FAIL)
    {
        return [
            'code' => $code,
            'data' => $data,
            'message' => $message,
        ];
    }
}
