<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AuthService;
use Hyperf\Di\Annotation\Inject;

class AuthController extends AbstractController
{
    /**
     * @Inject
     * @var AuthService
     */
    private $authService;

    public function getUserMenusByAppIdAndUserId()
    {
        $applicationId = $this->request->query('applicationId', '');
        $userId = $this->request->query('userId', '');
        $jwt = $this->request->getHeaders()['authorization'][0] ?? '';
        if ($applicationId === '' || $userId === '' || $jwt === '') {
            return $this->responseDetail();
        }

        $data = $this->authService->userMenus($jwt, $applicationId, $userId);

        return $this->responseOk($data);
    }
}
