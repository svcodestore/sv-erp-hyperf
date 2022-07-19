<?php

declare(strict_types=1);

namespace App\Service\Prod;

use App\Model\Prod\Schedule\CalendarModel;
use App\Service\Service;

class CalendarService extends Service
{
    public function getAll(): array
    {
        return $this->all(new CalendarModel);
    }

    public function getCalendarByDate(string $year, string $month): array
    {
        $cond = ['year' => $year, 'month' => $month];

        return $this->all(new CalendarModel, $cond);
    }

    public function updateCalendarById(int $id, array $update): bool
    {
        return CalendarModel::query()->where('id', $id)->update($update) === 1;
    }

    public function addCalendar(array $add): bool
    {
        return CalendarModel::insert($add);
    }
}
