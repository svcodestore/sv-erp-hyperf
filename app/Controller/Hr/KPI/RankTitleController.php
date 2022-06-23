<?php

declare(strict_types=1);

namespace App\Controller\Hr\KPI;

use App\Controller\AbstractController;
use App\Service\Hr\KPI\RankTitleService;
use Hyperf\Di\Annotation\Inject;

class RankTitleController extends AbstractController
{
    /**
     * @Inject
     * @var RankTitleService
     */
    private $rankTitleService;

    public function getAllRankTitle(): array
    {
        return $this->responseOk($this->rankTitleService->getAllRankTitle());
    }

    public function saveCrudRankTitle()
    {
        $isOk = $this->rankTitleService->saveCrudRankTitle($this->request);

        if ($isOk) {
            return $this->responseOk();
        }
        return $this->responseDetail();
    }
}
