<?php

declare(strict_types=1);

namespace App\Controller\Hr\KPI;

use App\Service\Hr\KPI\PositionGroupService;
use Hyperf\Di\Annotation\Inject;

class PositionGroupController extends \App\Controller\AbstractController
{
    /**
     * @Inject
     * @var PositionGroupService
     */
    private $positionGroupService;

    public function getAllPositionGroup(): array
    {
        return $this->responseOk($this->positionGroupService->getAllPositionGroup());
    }

    public function saveCrudPositionGroup()
    {
        $isOk = $this->positionGroupService->saveCrudPositionGroup($this->request);

        if ($isOk) {
            return $this->responseOk();
        }
        return $this->responseDetail();
    }
}
