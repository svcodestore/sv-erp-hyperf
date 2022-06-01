<?php

declare(strict_types=1);

namespace App\Service\Prod\ScheduleAlgorithm;

class SingleShift extends AbstractSchedule
{
    private string $worktimeStr;
    private string $morningWorktimeStart;
    private string $morningWorktimeStop;
    private string $afternoonWorktimeStart;
    private string $afternoonWorktimeStop;
    private string $eveningWorktimeStart;
    private string $eveningWorktimeStop;
    private int $morningWorkRest;
    private int $afternoonWorkRest;

    public function __construct(array $params)
    {
        parent::__construct($params);
    }

    protected function setShiftParam(array $param)
    {
        if ($param['ppi_extra_key'] === 'ppi_workday_time_range') {
            // 每天的上下班时间，字符串
            $this->worktimeStr = $param['ppi_extra_value'];
        }
    }

    protected function setTimeParam()
    {
        preg_match_all('/[\d:]{8}/', $this->worktimeStr, $worktimeArr);
        $worktime = $worktimeArr[0];
        list(
            $this->morningWorktimeStart,
            $this->morningWorktimeStop,
            $this->afternoonWorktimeStart,
            $this->afternoonWorktimeStop,
            $this->eveningWorktimeStart,
            $this->eveningWorktimeStop
        ) = $worktime;

        // 上午上班时长
        if ($this->morningWorktimeStart && $this->morningWorktimeStop) {
            $this->morningWorktime = strtotime($this->morningWorktimeStop) - strtotime($this->morningWorktimeStart);
        }
        // 下午上班时长
        if ($this->afternoonWorktimeStart && $this->afternoonWorktimeStop) {
            $this->afternoonWorktime = strtotime($this->afternoonWorktimeStop) - strtotime($this->afternoonWorktimeStart);
        }
        // 晚上上班时长
        if ($this->eveningWorktimeStart && $this->eveningWorktimeStop) {
            $this->eveningWorktime = strtotime($this->eveningWorktimeStop) - strtotime($this->eveningWorktimeStart);
        }
        // 计算中午休息时间
        if ($this->afternoonWorktimeStart && $this->morningWorktimeStop) {
            $this->morningWorkRest = strtotime($this->afternoonWorktimeStart)  - strtotime($this->morningWorktimeStop);
        }
        // 计算下午休息时间
        if ($this->eveningWorktimeStart && $this->afternoonWorktimeStop) {
            $this->afternoonWorkRest = strtotime($this->eveningWorktimeStart)  - strtotime($this->afternoonWorktimeStop);
        }

        $firstWorkdayOfMonth = "$this->year-$this->month-01";

        $morningWorktimeStart = null;
        // 算入行事历设置
        foreach ($this->arrangeDays as $arrangeDay) {
            $currArrange = current($this->arrangeDays);
            if ($firstWorkdayOfMonth === $arrangeDay['date']) {
                // 月初是休息日则下一非休息天开始排程
                if ($arrangeDay['is_rest'] === 1) {
                    $firstWorkdayOfMonth = date(
                        self::DATE_FORMAT,
                        strtotime(
                            '1 day',
                            strtotime($firstWorkdayOfMonth)
                        )
                    );
                    // 不是休息日，按设定的上下班时间排程
                } else {
                    if (isset($currArrange['morning'])) {
                        list($morningWorktimeStart) = explode(' - ', $currArrange['morning']);
                    }
                }
            } else {
                break;
            }
        }

        $start = $morningWorktimeStart ?: $this->morningWorktimeStart;
        // 排程开始日期
        $this->schStartAt     = strtotime("$firstWorkdayOfMonth $start");
    }

    private function getDayWorkInfo($timestamp): array
    {
        $date                       = date(self::DATE_FORMAT, $timestamp);

        list(
            $morningWorkRest,
            $afternoonWorkRest,
            $morningWorktimeStart,
            $morningWorktimeStop,
            $morningWorktime,
            $afternoonWorktimeStart,
            $afternoonWorktimeStop,
            $afternoonWorktime,
            $eveningWorktimeStart,
            $eveningWorktimeStop,
            $eveningWorktime
        )
            = $this->arrangeDaysCompute($timestamp);

        $morRest                    = $morningWorkRest ?: $this->morningWorkRest;
        $aftRest                    = $afternoonWorkRest ?: $this->afternoonWorkRest;

        $morStart                   = $morningWorktimeStart ?: $this->morningWorktimeStart;
        $morStop                    = $morningWorktimeStop ?: $this->morningWorktimeStop;
        $aftStart                   = $afternoonWorktimeStart ?: $this->afternoonWorktimeStart;
        $aftStop                    = $afternoonWorktimeStop ?: $this->afternoonWorktimeStop;
        $eveStart                   = $eveningWorktimeStart ?: $this->eveningWorktimeStart;
        $eveStop                    = $eveningWorktimeStop ?: $this->eveningWorktimeStop;

        $morningWorkDatetimeStart   = strtotime("$date $morStart");
        $morningWorkDatetimeStop    = strtotime("$date $morStop");
        $afternoonWorkDatetimeStart = strtotime("$date $aftStart");
        $afternoonWorkDatetimeStop  = strtotime("$date $aftStop");
        $eveningWorkDatetimeStart   = strtotime("$date $eveStart");
        $eveningWorkDatetimeStop    = strtotime("$date $eveStop");

        return [
            $morRest,
            $aftRest,
            $morningWorkDatetimeStart,
            $morningWorkDatetimeStop,
            $afternoonWorkDatetimeStart,
            $afternoonWorkDatetimeStop,
            $eveningWorkDatetimeStart,
            $eveningWorkDatetimeStop
        ];
    }

    /**
     * @inheritDoc
     */
    protected function handlePhaseStartTime(int $lastStartAt, int &$computeStartAt, $isReverse = false): int
    {
        if ($lastStartAt == $computeStartAt) {
            return $computeStartAt;
        }

        if ($isReverse) {
            return $this->handlePhaseStartTimeReverse($lastStartAt, $computeStartAt);
        } else {
            list(
                $morRest,
                $aftRest,
                $morningWorkDatetimeStart,
                $morningWorkDatetimeStop,
                $afternoonWorkDatetimeStart,
                $afternoonWorkDatetimeStop,
                $eveningWorkDatetimeStart,
                $eveningWorkDatetimeStop
            ) = $this->getDayWorkInfo($lastStartAt);

            $phaseActualStartAt        = $computeStartAt;

            if ($phaseActualStartAt >= $eveningWorkDatetimeStop) {
                $_eveningWorkDatetimeStop = $eveningWorkDatetimeStop;
                list(
                    $morRest,
                    $aftRest,
                    $morningWorkDatetimeStart,
                    $morningWorkDatetimeStop,
                    $afternoonWorkDatetimeStart,
                    $afternoonWorkDatetimeStop,
                    $eveningWorkDatetimeStart,
                    $eveningWorkDatetimeStop
                ) = $this->getDayWorkInfo($lastStartAt + self::DAY_SECONDS);

                $r = $phaseActualStartAt - $_eveningWorkDatetimeStop;
                $next = $morningWorkDatetimeStart + $r;
                if ($r < $morningWorkDatetimeStop - $morningWorkDatetimeStart) {
                    $phaseActualStartAt = $next;
                } else {
                    $phaseActualStartAt = $this->handlePhaseStartTime($morningWorkDatetimeStart, $next);
                }
            } else {
                $_lastStartAt = $lastStartAt;
                $diff = $phaseActualStartAt - $lastStartAt;
                $scd = 60 * 10;

                $isInMorRest = false;
                $isInAftRest = false;
                while ($diff > 0) {
                    $_lastStartAt += $scd;
                    if ($_lastStartAt >= $morningWorkDatetimeStop && $_lastStartAt < $afternoonWorkDatetimeStart) {
                        $isInMorRest = true;
                    }
                    if ($_lastStartAt >= $afternoonWorkDatetimeStop && $_lastStartAt < $eveningWorkDatetimeStart) {
                        $isInAftRest = true;
                    }
                    $diff -= $scd;
                }

                if ($isInMorRest) {
                    $phaseActualStartAt += $morRest;
                }
                if ($isInAftRest) {
                    $phaseActualStartAt += $aftRest;
                }

                if ($phaseActualStartAt >= $eveningWorkDatetimeStop) {
                    $phaseActualStartAt = $this->handlePhaseStartTime($lastStartAt, $phaseActualStartAt);
                }
            }

            return $phaseActualStartAt;
        }
    }

    /**
     * @inheritDoc
     */
    protected function isInCalendar(int $timestamp): bool
    {
        $currDate = date(self::DATE_FORMAT, $timestamp);
        list(
            $morningWorkRest,
            $afternoonWorkRest,
            $morningWorktimeStart,
            $morningWorktimeStop,
            $morningWorktime,
            $afternoonWorktimeStart,
            $afternoonWorktimeStop,
            $afternoonWorktime,
            $eveningWorktimeStart,
            $eveningWorktimeStop,
            $eveningWorktime
        )                           =
            $this->arrangeDaysCompute($timestamp);

        $morStart                   = $morningWorktimeStart ?: $this->morningWorktimeStart;
        $morStop                    = $morningWorktimeStop ?: $this->morningWorktimeStop;
        $aftStart                   = $afternoonWorktimeStart ?: $this->afternoonWorktimeStart;
        $aftStop                    = $afternoonWorktimeStop ?: $this->afternoonWorktimeStop;
        $eveStart                   = $eveningWorktimeStart ?: $this->eveningWorktimeStart;
        $eveStop                    = $eveningWorktimeStop ?: $this->eveningWorktimeStop;

        $morningWorkDatetimeStart   = strtotime("$currDate $morStart");
        $morningWorkDatetimeStop    = strtotime("$currDate $morStop");
        $afternoonWorkDatetimeStart = strtotime("$currDate $aftStart");
        $afternoonWorkDatetimeStop  = strtotime("$currDate $aftStop");
        $eveningWorkDatetimeStart   = strtotime("$currDate $eveStart");
        $eveningWorkDatetimeStop    = strtotime("$currDate $eveStop");

        return ($timestamp > $morningWorkDatetimeStart && $timestamp < $morningWorkDatetimeStop) ||
            ($timestamp > $afternoonWorkDatetimeStart && $timestamp < $afternoonWorkDatetimeStop) ||
            ($timestamp > $eveningWorkDatetimeStart && $timestamp < $eveningWorkDatetimeStop);
    }

    /**
     * @inheritDoc
     */
    protected function arrangeDaysCompute(int $timestamp): array
    {
        $morningWorkRest        = null;
        $afternoonWorkRest      = null;
        $morningWorktimeStart   = null;
        $morningWorktimeStop    = null;
        $morningWorktime        = null;
        $afternoonWorktimeStart = null;
        $afternoonWorktimeStop  = null;
        $afternoonWorktime      = null;
        $eveningWorktimeStart   = null;
        $eveningWorktimeStop    = null;
        $eveningWorktime        = null;

        $arrangeDays            = $this->getNowArrangeDays($timestamp);

        // 算入行事历设定
        foreach ($arrangeDays as $arrangeDay) {

            if ($arrangeDay['is_rest'] === 0 && date(self::DATE_FORMAT, $timestamp) === $arrangeDay['date']) {
                if (isset($arrangeDay['morning'])) {
                    list($morningWorktimeStart, $morningWorktimeStop) = explode(' - ', $arrangeDay['morning']);
                }
                if (isset($arrangeDay['afternoon'])) {
                    list($afternoonWorktimeStart, $afternoonWorktimeStop) = explode(' - ', $arrangeDay['afternoon']);
                }
                if (isset($arrangeDay['evening'])) {
                    list($eveningWorktimeStart, $eveningWorktimeStop) = explode(' - ', $arrangeDay['evening']);
                }
            }
        }

        // 计算中午休息时间
        if ($afternoonWorktimeStart && $morningWorktimeStop) {
            $morningWorkRest = strtotime($afternoonWorktimeStart)  - strtotime($morningWorktimeStop);
        }
        // 计算下午休息时间
        if ($eveningWorktimeStart && $afternoonWorktimeStop) {
            $afternoonWorkRest = strtotime($eveningWorktimeStart)  - strtotime($afternoonWorktimeStop);
        }

        // 上午上班时长
        if ($morningWorktimeStart && $morningWorktimeStop) {
            $morningWorktime = strtotime($morningWorktimeStop) - strtotime($morningWorktimeStart);
        }
        // 下午上班时长
        if ($afternoonWorktimeStart && $afternoonWorktimeStop) {
            $afternoonWorktime = strtotime($afternoonWorktimeStop) - strtotime($afternoonWorktimeStart);
        }
        // 晚上上班时长
        if ($eveningWorktimeStart && $eveningWorktimeStop) {
            $eveningWorktime = strtotime($eveningWorktimeStop) - strtotime($eveningWorktimeStart);
        }

        return [
            $morningWorkRest,
            $afternoonWorkRest,
            $morningWorktimeStart,
            $morningWorktimeStop,
            $morningWorktime,
            $afternoonWorktimeStart,
            $afternoonWorktimeStop,
            $afternoonWorktime,
            $eveningWorktimeStart,
            $eveningWorktimeStop,
            $eveningWorktime
        ];
    }

    private function handlePhaseStartTimeReverse(int $lastStartAt, int &$computeStartAt): int
    {
        list(
            $morRest,
            $aftRest,
            $morningWorkDatetimeStart,
            $morningWorkDatetimeStop,
            $afternoonWorkDatetimeStart,
            $afternoonWorkDatetimeStop,
            $eveningWorkDatetimeStart,
            $eveningWorkDatetimeStop
        ) = $this->getDayWorkInfo($lastStartAt);

        $phaseActualStartAt         = $computeStartAt;

        if ($phaseActualStartAt < $morningWorkDatetimeStart) {
            $_morningWorkDatetimeStart = $morningWorkDatetimeStart;
            list(
                $morRest,
                $aftRest,
                $morningWorkDatetimeStart,
                $morningWorkDatetimeStop,
                $afternoonWorkDatetimeStart,
                $afternoonWorkDatetimeStop,
                $eveningWorkDatetimeStart,
                $eveningWorkDatetimeStop
            ) = $this->getDayWorkInfo($lastStartAt - self::DAY_SECONDS);

            $r = $_morningWorkDatetimeStart - $phaseActualStartAt;
            $next = $eveningWorkDatetimeStop - $r;
            if ($r < $eveningWorkDatetimeStop - $eveningWorkDatetimeStart) {
                $phaseActualStartAt = $next;
            } else {
                $phaseActualStartAt = $this->handlePhaseStartTime($eveningWorkDatetimeStop, $next);
            }
        } else {
            $_lastStartAt = $lastStartAt;
            $diff = $lastStartAt - $phaseActualStartAt;
            $scd = 60 * 10;

            $isInMorRest = false;
            $isInAftRest = false;
            while ($diff > 0) {
                $_lastStartAt -= $scd;
                if ($_lastStartAt >= $morningWorkDatetimeStop && $_lastStartAt < $afternoonWorkDatetimeStart) {
                    if (!$isInMorRest) {
                        $isInMorRest = true;
                    }
                }
                if ($_lastStartAt >= $afternoonWorkDatetimeStop && $_lastStartAt < $eveningWorkDatetimeStart) {
                    if (!$isInAftRest) {
                        $isInAftRest = true;
                    }
                }
                $diff -= $scd;
            }

            if ($isInAftRest) {
                $phaseActualStartAt -= $aftRest;
                if ($phaseActualStartAt >= $morningWorkDatetimeStop && $phaseActualStartAt < $afternoonWorkDatetimeStart) {
                    $phaseActualStartAt -= $morRest;
                }
            } elseif ($isInMorRest) {
                $phaseActualStartAt -= $morRest;
            }

            if ($phaseActualStartAt < $morningWorkDatetimeStart) {
                $phaseActualStartAt = $this->handlePhaseStartTime($lastStartAt, $phaseActualStartAt);
            }
        }


        return $phaseActualStartAt;
    }

    /**
     * @inheritDoc
     */
    protected function handlePhaseCompleteTime(int $computeStartAt, int &$phaseCompleteAt, $phsSeq = null, $k = null): int
    {
        $startDate                  = date(self::DATE_FORMAT, $computeStartAt);
        $completeDate               = date(self::DATE_FORMAT, $phaseCompleteAt);

        list(
            $morRest,
            $aftRest,
            $morningWorkDatetimeStart,
            $morningWorkDatetimeStop,
            $afternoonWorkDatetimeStart,
            $afternoonWorkDatetimeStop,
            $eveningWorkDatetimeStart,
            $eveningWorkDatetimeStop
        ) = $this->getDayWorkInfo($computeStartAt);

        $phaseActualCompleteAt      = $phaseCompleteAt;
        // 如果工序在当天内完成
        // 比较完成时间是否超过下班时间点，超过则算入对应的休息时间
        // 开始完成在同一天
        if ($startDate == $completeDate) {
            if ($phaseActualCompleteAt >= $morningWorkDatetimeStop) {
                $phaseActualCompleteAt += $morRest;
            }

            if ($phaseActualCompleteAt >= $afternoonWorkDatetimeStop) {
                $phaseActualCompleteAt += $aftRest;
            }

            // 当完成时间在当天下班时间之后，则开始时间和完成时间各增加一天，递归计算
            if ($phaseActualCompleteAt > $eveningWorkDatetimeStop) {
                $remainTime            = $phaseActualCompleteAt - $eveningWorkDatetimeStop;

                list(
                    $morRest,
                    $aftRest,
                    $morningWorkDatetimeStart,
                    $morningWorkDatetimeStop,
                    $afternoonWorkDatetimeStart,
                    $afternoonWorkDatetimeStop,
                    $eveningWorkDatetimeStart,
                    $eveningWorkDatetimeStop
                ) = $this->getDayWorkInfo($morningWorkDatetimeStart + self::DAY_SECONDS);

                $phaseActualCompleteAt = $morningWorkDatetimeStart + $remainTime;
                $computeStartAt        = $morningWorkDatetimeStart;

                return $this->handlePhaseCompleteTime(
                    $computeStartAt,
                    $phaseActualCompleteAt,
                    $phsSeq,
                    $k
                );
            }

            return $phaseActualCompleteAt;

            // 不在同一天，就减去当天开始到当天下班的时间，得到剩余时间进行递归计算
        } else if ($phaseCompleteAt > $computeStartAt) {
            $remainTime         = $phaseActualCompleteAt - $eveningWorkDatetimeStop;
            if ($computeStartAt <= $morningWorkDatetimeStop) {
                $remainTime += $morRest;
            }
            if ($computeStartAt <= $afternoonWorkDatetimeStart) {
                $remainTime += $aftRest;
            }
            list(
                $morRest,
                $aftRest,
                $morningWorkDatetimeStart,
                $morningWorkDatetimeStop,
                $afternoonWorkDatetimeStart,
                $afternoonWorkDatetimeStop,
                $eveningWorkDatetimeStart,
                $eveningWorkDatetimeStop
            ) = $this->getDayWorkInfo($morningWorkDatetimeStart + self::DAY_SECONDS);

            $phaseActualCompleteAt = $morningWorkDatetimeStart + $remainTime;
            $computeStartAt        = $morningWorkDatetimeStart;

            return $this->handlePhaseCompleteTime(
                $computeStartAt,
                $phaseActualCompleteAt,
                $phsSeq,
                $k
            );
        }
    }
}
