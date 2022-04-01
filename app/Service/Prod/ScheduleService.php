<?php

declare(strict_types=1);

namespace App\Service\Prod;

use App\Model\Prod\Schedule\CalendarModel;

class ScheduleService
{
    public function getAllCalendar()
    {
        return CalendarModel::query()->get()->toArray();
    }
}
