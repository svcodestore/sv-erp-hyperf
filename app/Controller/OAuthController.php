<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\OauthService;
use Hyperf\Di\Annotation\Inject;

class OAuthController extends AbstractController
{
    /**
     * @Inject
     * @var OauthService
     */
    private $oauthService;

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
            $data = $this->oauthService->accessToken($params);

            return $this->responseOk($data);
        }
        return $this->responseOk();
    }

    public function logout()
    {
        $token = $this->request->getHeaders()['authorization'][0] ?? '';
        if ($token) {
            $data = $this->oauthService->logout($token);
            return $this->responseOk($data);
        }
        return $this->responseDetail();
    }
}
