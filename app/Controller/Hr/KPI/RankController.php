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

    public function getAllItem()
    {
        return $this->responseOk($this->rankService->getAllItem());
    }
}
