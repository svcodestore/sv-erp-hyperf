<?php

declare(strict_types=1);

namespace App\Controller\Application;

use App\Controller\AbstractController;
use App\Service\ApplicationService;
use Hyperf\Di\Annotation\Inject;

class ApplicationController  extends AbstractController
{
    /**
     * @Inject
     * @var ApplicationService
     */
    private $applicationService;

    public function getCurrentApplication()
    {
        $data = $this->applicationService->currentApplication();
        return $this->responseOk($data);
    }
}
