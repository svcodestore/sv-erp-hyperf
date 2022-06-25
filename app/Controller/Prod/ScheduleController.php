<?php

declare(strict_types=1);

namespace App\Controller\Prod;

use App\Controller\AbstractController;
use App\Request\Prod\ScheduleRequest;
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

    public function getPhaseByCode($code)
    {

        $code || ($code = $this->request->input('code'));

        $data = [];
        if (is_null($code)) {
            $data = $this->scheduleService->getPhases();
        } else {
            $data = $this->scheduleService->getPhaseByCode($code);
        }
        return $this->responseOk($data);
    }

    public function getPo(ScheduleRequest $request)
    {
        $params = $request->validated();
        $data = $this->scheduleService->getMonthPo($params['workLine'], $params['year'], $params['month']);
        return $this->responseOk($data);
    }

    public function getCalendar()
    {
        $year = $this->request->query('year');
        $month = $this->request->query('month');
        $data = $this->scheduleService->getCalendar($year, $month);
        return $this->responseOk($data);
    }
}
