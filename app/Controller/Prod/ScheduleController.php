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
        $params = $request->validated();
        $data = $this->scheduleService->getScheduleList($params['workLine'], $params['year'], $params['month']);
        if (is_string($data)) {
            return $this->responseDetail($data);
        }
        return $this->responseOk($data);
    }
}
