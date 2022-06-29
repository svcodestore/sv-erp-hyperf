<?php

declare(strict_types=1);

namespace App\Controller\Hr\KPI;

use App\Service\Hr\KPI\PositionGroupScoreService;
use Hyperf\Di\Annotation\Inject;

class PositionGroupScoreController extends \App\Controller\AbstractController
{
    /**
     * @Inject
     * @var PositionGroupScoreService
     */
    private $positionGroupScoreService;

    public function getAll(): array
    {
        return $this->responseOk($this->positionGroupScoreService->getAll());
    }

    public function saveCrud()
    {
        $isOk = $this->positionGroupScoreService->saveCrud($this->request);

        if ($isOk) {
            return $this->responseOk();
        }
        return $this->responseDetail();
    }
}
