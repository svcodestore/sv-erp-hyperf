<?php

namespace App\Service;

use App\Util\CurlUtil;

class AuthService
{
    public function userMenus(string $accessToken, string $applicationId, string $userId): array
    {
        $params = [
            'applicationId' => $applicationId,
            'userId' => $userId,
        ];
        $url = env('AUTH_URL') . '/authorization/user-menus?' .  http_build_query($params);
        $headers = ['Authorization: ' . $accessToken];

        $data = CurlUtil::get($url, $headers);

        $arr = json_decode($data, true);
        if (isset($arr['data'])) {
            return $arr['data'];
        }

        return [];
    }
}
