<?php

declare(strict_types=1);

namespace App\Controller\Prod;

use App\Controller\AbstractController;
use App\Service\Prod\CalendarService;
use App\Util\StrUtil;
use Hyperf\Di\Annotation\Inject;

class CalendarController extends AbstractController
{
    /**
     * @Inject
     * @var CalendarService
     */
    private $calendarService;

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->calendarService->getAll();
    }

    /**
     * @return array
     */
    public function getCalendarByDate(): array
    {
        $year = $this->request->query('year');
        $month = $this->request->query('month');
        $month = $month > 9 ? $month : '0' . $month;

        $data = $this->calendarService->getCalendarByDate($year, $month);

        return $this->responseOk($data);
    }

    /**
     * @param int $id
     * @return array
     */
    public function updateCalendarById(int $id): array
    {
        $update =  StrUtil::withSnakeCase($this->request->input('update'));
        $result = $this->calendarService->updateCalendarById($id, $update);

        if ($result) {
            return $this->responseOk(true);
        }

        return $this->responseDetail();
    }

    /**
     * @return array
     */
    public function addCalendar(): array
    {
        $add = $this->request->input('add');
        $result = $this->calendarService->addCalendar($add);

        if ($result) {
            return $this->responseOk(true);
        }

        return $this->responseDetail();
    }
}