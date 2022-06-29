<?php

declare(strict_types=1);

namespace App\Controller\Hr\KPI;

use App\Service\Hr\KPI\StaffScoreService;
use Hyperf\Di\Annotation\Inject;

class StaffScoreController extends \App\Controller\AbstractController
{
    /**
     * @Inject
     * @var StaffScoreService
     */
    private $staffScoreService;

    public function getAll(): array
    {
        return $this->responseOk($this->staffScoreService->getAll());
    }

    public function saveCrud()
    {
        $isOk = $this->staffScoreService->saveCrud($this->request);

        if ($isOk) {
            return $this->responseOk();
        }
        return $this->responseDetail();
    }
}
