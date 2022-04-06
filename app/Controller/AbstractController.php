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
            'data' => $this->dataWithCamelCase($data),
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

    public function dataWithCamelCase($data): array
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $value = $this->dataWithCamelCase($value);
                }
                if (is_string($key)) {
                    $data[$this->toCamelCase($key)] = $value;
                    unset($data[$key]);
                } else {
                    $data[$key] = $value;
                }
            }
        }
        return $data;
    }

    public function toCamelCase(string $str): string
    {
        $arr = explode('_', $str);
        $camelCase = "";
        foreach ($arr as $key => $value) {
            if ($key != 0) {
                $value[0] = strtoupper($value[0]);
            }
            $camelCase .= $value;
        }
        return $camelCase;
    }

    public function toSnakeCase(string $str): string
    {
        $l = strlen($str);
        $snakeCase = "";
        for ($i = 0; $i < $l; $i++) {
            $ascii = ord($str[$i]);
            if ($ascii > 64 && $ascii < 91) {
                $snakeCase .= chr($ascii + 32);
                if ($l - 1 !== $i) {
                    $snakeCase .= '_';
                }
            } else {
                $snakeCase .= $str[$i];
            }
        }
        return $snakeCase;
    }
}
