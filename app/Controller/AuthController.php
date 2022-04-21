<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\RedisKey;
use App\Util\CurlUtil;
use Hyperf\Utils\ApplicationContext;

class AuthController extends AbstractController
{
    public function getAccessToken()
    {
        $grantType = $this->request->input("grant_type");
        if ($grantType == 'authorization_code') {
            $clientId = $this->request->input("client_id");
            $code = $this->request->input("code");
            $clientSecret = env('OAUTH_CLIENT_SECRET');
            $redirectUri = $this->request->input("redirect_uri");
            $params = [
                'grant_type' => $grantType,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'redirect_uri' => $redirectUri,
            ];
            $data = CurlUtil::post(env('OAUTH_SSO_TOKEN_URL') . http_build_query($params));
            return $this->responseOk(json_decode($data, true));
        }
        $this->responseOk();
    }

    public function logout()
    {
        $token = $this->request->getHeaders()['authorization'][0] ?? '';
        if ($token) {
            $token = substr($token, 7);
            $container = ApplicationContext::getContainer();
            $redis = $container->get(\Hyperf\Redis\Redis::class);
            $affected = $redis->srem(RedisKey::ISSUED_ACCESS_TOKEN, $token);
            if ($affected > 0) {
                return $this->responseOk();
            }
        }
        return $this->responseDetail();
    }
}
