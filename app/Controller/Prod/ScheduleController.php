<?php

declare(strict_types=1);

namespace App\Controller\Prod;

use App\Controller\AbstractController;
use App\Request\ScheduleRequest;
use App\Service\Prod\ScheduleService;
use Hyperf\Di\Annotation\Inject;

class ScheduleController extends AbstractController
{
    /**
     * @Inject
     * @var ScheduleService
     */
    private $scheduleService;

    public function schedule(ScheduleRequest $request)
    {
        return $this->responseOk($this->scheduleService->getAllCalendar());
    }
}
