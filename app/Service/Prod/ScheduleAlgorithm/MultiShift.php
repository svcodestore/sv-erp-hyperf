<?php

declare(strict_types=1);

namespace App\Service\Prod\ScheduleAlgorithm;

class MultiShift extends AbstractSchedule
{
    public function __construct(array $params)
    {
        parent::__construct($params);
    }

    protected function setTimeParam()
    {
        $this->parseShiftTime($this->shifts);

        $firstWorkdayOfMonth = "$this->year-$this->month-01";

        $morningWorktimeStart = null;
        // 算入行事历设置
        foreach ($this->arrangeDays ?? [] as $arrangeDay) {
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
                    if (isset($currArrange['shiftsTime'])) {
                        $morningWorktimeStart = date('H:i:s', strtotime($currArrange['shiftsTime'][0]['times'][0]['start']));
                    }
                }
            } else {
                break;
            }
        }

        if ($morningWorktimeStart) {
            $start = $morningWorktimeStart;
        } elseif (empty($this->shifts)) {
            $start = '07:30:00';
        } else {
            $start = date('H:i:s', strtotime($this->shifts[0]['times'][0]['start']));
        }

        // 排程开始日期
        $this->schStartAt = strtotime("$firstWorkdayOfMonth $start");
    }

    private function parseShiftTime(array &$times)
    {
        foreach ($times as $key => $shiftTime) {
            $rest = [];
            foreach ($shiftTime['times'] as $k => $range) {
                $times[$key]['times'][$k]['duration'] = strtotime($range['end']) - strtotime($range['start']);
                if ($k > 0) {
                    $prev = $k - 1;
                    $temp = [];
                    $temp['name'] = "$prev - $k";
                    $temp['start'] = $times[$key]['times'][$prev]['end'];
                    $temp['end'] = $times[$key]['times'][$k]['start'];
                    $temp['duration'] = strtotime($temp['end']) - strtotime($temp['start']);
                    $rest[] = $temp;
                }
            }
            $times[$key]['rests'] = $rest;
        }
        unset($temp);
        unset($rest);
    }

    /**
     * @inheritDoc
     */
    protected function handlePhaseStartTime(int $lastStartAt, int &$computeStartAt, $isReverse = false): int
    {
        // bug
        $computeStartDate = date(self::DATE_FORMAT, $computeStartAt);

        $shiftTime = $this->arrangeDaysCompute($computeStartAt);
        if (empty($shiftTime)) {
            $shiftTime = $this->shifts;
        }

        $phaseActualStartAt = $computeStartAt;

        foreach ($shiftTime as $time) {
            foreach ($time['rests'] as $rest) {
                $start = date('H:i:s', strtotime($rest['start']));
                $end = date('H:i:s', strtotime($rest['end']));
                $startTimestamp = strtotime("$computeStartDate $start");
                $endTimestamp = strtotime("$computeStartDate $end");
                if ($rest['duration'] < 0) {
                    $rest['duration'] += self::DAY_SECONDS;
                }
                if ($phaseActualStartAt < $endTimestamp && $phaseActualStartAt > $startTimestamp) {
                    if ($isReverse) {
                        $phaseActualStartAt -= $rest['duration'];
                    } else {
                        $phaseActualStartAt += $rest['duration'];
                    }
                }
            }
        }

        return $phaseActualStartAt;
    }

    /**
     * @inheritDoc
     */
    protected function isInCalendar(int $timestamp): bool
    {
        $currDate = date(self::DATE_FORMAT, $timestamp);

        $shiftTime = $this->arrangeDaysCompute($timestamp);
        if (empty($shiftTime)) {
            $shiftTime = $this->shifts;
        }

        $flag = false;
        foreach ($shiftTime as $time) {
            foreach ($time['times'] as $v) {
                $start = date('H:i:s', strtotime($v['start']));
                $end = date('H:i:s', strtotime($v['end']));
                $startTimestamp = strtotime("$currDate $start");
                $endTimestamp = strtotime("$currDate $end");
                $flag = $flag || ($timestamp > $startTimestamp && $timestamp < $endTimestamp);
                if ($flag) {
                    break 2;
                }
            }
        }

        return $flag;
    }

    /**
     * @inheritDoc
     */
    protected function arrangeDaysCompute(int $timestamp): array
    {
        $arrangeDays = $this->getNowArrangeDays($timestamp);
        // 算入行事历设定
        foreach ($arrangeDays ?? [] as $arrangeDay) {

            if ($arrangeDay['is_rest'] === 0 && date(self::DATE_FORMAT, $timestamp) === $arrangeDay['date']) {

                if (isset($arrangeDay['shiftsTime']) && is_array($arrangeDay['shiftsTime'])) {
                    $this->parseShiftTime($arrangeDay['shiftsTime']);
                    return $arrangeDay['shiftsTime'];
                }
            }
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    protected function handlePhaseCompleteTime(int $computeStartAt, int &$phaseCompleteAt): int
    {
        $startDate = date(self::DATE_FORMAT, $computeStartAt);

        $shiftTime = $this->arrangeDaysCompute($computeStartAt);
        if (empty($shiftTime)) {
            $shiftTime = $this->shifts;
        }

        $phaseActualCompleteAt = $phaseCompleteAt;

        foreach ($shiftTime as $time) {
            foreach ($time['rests'] ?? [] as $rest) {
                $start = date('H:i:s', strtotime($rest['start']));
                $end = date('H:i:s', strtotime($rest['end']));
                $startTimestamp = strtotime("$startDate $start");
                $endTimestamp = strtotime("$startDate $end");
                if ($rest['duration'] < 0) {
                    $rest['duration'] += self::DAY_SECONDS;
                }
                if ($phaseActualCompleteAt < $endTimestamp && $phaseActualCompleteAt > $startTimestamp) {

                    $phaseActualCompleteAt += $rest['duration'];
                }
            }
        }
        $completeDate = date(self::DATE_FORMAT, $phaseActualCompleteAt);
        if ($startDate === $completeDate) {
            return $phaseActualCompleteAt;
        } else {
            return $this->handlePhaseCompleteTime($computeStartAt + self::DAY_SECONDS, $phaseActualCompleteAt);
        }
    }
}
