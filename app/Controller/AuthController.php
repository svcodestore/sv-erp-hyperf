<?php

declare(strict_types=1);

namespace App\Controller;

class AuthController extends AbstractController
{
    public function getAccessToken()
    {
        $grantType = $this->request->input("grant_type");
        $clientId = $this->request->input("client_id");
        $clientSecret = $this->request->input("client_secret");
        $code = $this->request->input("code");
        $redirectUri = $this->request->input("redirect_uri");
        return $this->responseOk($grantType);
    }
}
