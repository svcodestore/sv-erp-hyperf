<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Service\OauthService;
use Hyperf\Context\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Di\Annotation\Inject;

class SsoMiddleware implements MiddlewareInterface
{
    /**
     * @Inject
     * @var OauthService
     */
    private $oauthService;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = Context::get(ResponseInterface::class);

        $requestUri = $request->getServerParams()['request_uri'];
        $whiteList = ['/api/oauth2.0/token', '/api/application/current-application'];
        if (in_array($requestUri, $whiteList)) {
            return $handler->handle($request);
        }

        $jwt = $request->getHeaders()['authorization'][0] ?? '';

        $status = 401;
        if ($jwt) {
            $isLogin = $this->oauthService->isUserLogin($jwt);
            if ($isLogin) {
                return $handler->handle($request);
            }
        }

        return $response->withStatus($status);
    }
}
