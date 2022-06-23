<?php

declare(strict_types=1);

namespace App\Controller\Hr\KPI;

use App\Controller\AbstractController;
use App\Service\Hr\KPI\RankService;
use Hyperf\Di\Annotation\Inject;

class RankController extends AbstractController
{
    /**
     * @Inject
     * @var RankService
     */
    private $rankService;

    public function getAllRank(): array
    {
        return $this->responseOk($this->rankService->getAllRank());
    }

    public function saveCrudRank()
    {
        $isOk = $this->rankService->saveCrudRank($this->request);

        if ($isOk) {
            return $this->responseOk();
        }
        return $this->responseDetail();
    }
}
