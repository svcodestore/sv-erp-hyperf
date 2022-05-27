<?php

namespace App\Service;

use App\Util\CurlUtil;

class ApplicationService
{
    public function currentApplication()
    {
        $params = [
            'id' => env('SYSTEM_ID'),
            'clientSecret' => env('OAUTH_CLIENT_SECRET'),
        ];
        $url = env('SSO_URL') . '/current-application?' . http_build_query($params);
        $data = CurlUtil::get($url);

        return json_decode($data, true)['data'];
    }
}
