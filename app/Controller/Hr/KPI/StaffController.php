<?php

declare(strict_types=1);

namespace App\Controller\Hr\KPI;

use App\Service\Hr\KPI\StaffService;
use Hyperf\Di\Annotation\Inject;

class StaffController extends \App\Controller\AbstractController
{
    /**
     * @Inject
     * @var StaffService
     */
    private $staffService;

    public function getAllTitle(): array
    {
        return $this->responseOk($this->staffService->getAllStaff());
    }

    public function saveCrudTitle()
    {
        $isOk = $this->staffService->saveCrudStaff($this->request);

        if ($isOk) {
            return $this->responseOk();
        }
        return $this->responseDetail();
    }
}
