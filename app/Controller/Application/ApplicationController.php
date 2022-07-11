<?php

declare(strict_types=1);

namespace App\Controller\Application;

use App\Controller\AbstractController;
use App\Service\ApplicationService;
use Hyperf\Di\Annotation\Inject;

class ApplicationController extends AbstractController
{
    /**
     * @Inject
     * @var ApplicationService
     */
    private $applicationService;

    public function getCurrentApplication()
    {
        $application = $this->applicationService->currentApplication();

        $isIntranet = true;
        $ip = $this->request->getHeader('x-forwarded-for')[0];
        if (strpos($ip, '192.') === false || strpos($ip, '172.') === false) {
            $isIntranet = false;
        }

        $redirectUris = explode("|", $application["redirectUris"]);
        if (count($redirectUris) > 1) {
            if ($isIntranet) {
                $application["redirectUris"] = $redirectUris[0];
            } else {
                $application["redirectUris"] = $redirectUris[1];
            }
        }
        $loginUris = explode("|", $application["loginUris"]);
        if (count($loginUris) > 1) {
            if ($isIntranet) {
                $application["loginUris"] = $loginUris[0];
            } else {
                $application["loginUris"] = $loginUris[1];
            }
        }

        return $this->responseOk($application);
    }
}
