<?php

declare(strict_types=1);

namespace App\Service\Prod\ScheduleAlgorithm;

interface ISchedule
{
    public function scheduleList(): array;
}
