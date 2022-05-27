<?php

namespace App\Service;

use App\Util\CurlUtil;

class OauthService
{
    public function accessToken(array $params): array
    {
        $data = CurlUtil::post(env('OAUTH_SSO_TOKEN_URL') . http_build_query($params));
        return json_decode($data, true);
    }

    public function logout(string $accessToken): array
    {
        $data = CurlUtil::post(env('SSO_URL') . '/logout', [], ['Authorization: ' . $accessToken]);
        return json_decode($data, true)['data'];
    }

    public function isUserLogin(string $accessToken)
    {
        $url = env('SSO_URL') . '/user/ping';
        $headers = ['Authorization: ' . $accessToken];

        $data = CurlUtil::get($url, $headers);

        return $data;
    }
}
