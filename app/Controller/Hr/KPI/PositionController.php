<?php

declare(strict_types=1);

namespace App\Controller\Hr\KPI;

use App\Service\Hr\KPI\PositionService;
use Hyperf\Di\Annotation\Inject;

class PositionController extends \App\Controller\AbstractController
{
    /**
     * @Inject
     * @var PositionService
     */
    private $positionService;

    public function getAllPosition(): array
    {
        return $this->responseOk($this->positionService->getAllPosition());
    }

    public function saveCrudPosition()
    {
        $isOk = $this->positionService->saveCrudPosition($this->request);

        if ($isOk) {
            return $this->responseOk();
        }
        return $this->responseDetail();
    }
}
