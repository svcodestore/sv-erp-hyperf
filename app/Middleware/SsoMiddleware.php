<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\RedisKey;
use App\Util\JwtUtil;
use Hyperf\Context\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Utils\ApplicationContext;



class SsoMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = Context::get(ResponseInterface::class);

        $requestUri = $request->getServerParams()['request_uri'];
        $whiteList = ['/api/oauth2.0/token', '/api/logout'];
        if (in_array($requestUri, $whiteList)) {
            return $handler->handle($request);
        }

        $jwt = $request->getHeaders()['authorization'][0] ?? '';

        $status = 401;
        if ($jwt) {
            $info = JwtUtil::parseJwt($jwt);
            if ($info) {
                $expireAt = $info['exp'];
                if (time() < $expireAt) {
                    $container = ApplicationContext::getContainer();
                    $redis = $container->get(\Hyperf\Redis\Redis::class);
                    $isLogin = $redis->sIsMember(RedisKey::ISSUED_ACCESS_TOKEN, $jwt);
                    if ($isLogin) {
                        return $handler->handle($request);
                    }
                }
            }
        }

        return $response->withStatus($status);
    }
}
