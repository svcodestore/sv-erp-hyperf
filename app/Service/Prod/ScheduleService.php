<?php

declare(strict_types=1);

namespace App\Service\Prod;

use App\Model\Prod\Schedule\ParamModel;
use App\Model\Prod\Schedule\PhaseModel;
use App\Service\Prod\ScheduleAlgorithm\MultiShift;
use App\Service\Prod\ScheduleAlgorithm\SingleShift;
use App\Service\Service;

class ScheduleService extends Service
{
    public function getScheduleList(string $workLine, string $year, string $month)
    {
        $pos = (new PoService)->getMonthPo($workLine, $year, $month);
        if (empty($pos)) return $pos;

        foreach ($pos as $k => $po) {
            $pos[$k]['phases'] = PhaseModel::query()->where(['code' => $po['item_code']])->get()->toArray();
            if (empty($pos[$k]['phases'])) {
                unset($pos[$k]);
            }
        }

        $params = $this->getScheduleWrapParams($workLine, $year, $month);
        $params['year'] = $year;
        $params['month'] = $month;
        $params['workLine'] = $workLine;
        $params['prodList'] = array_values($pos);

        try {
            $schedule = $params['isMultiShift'] ? (new MultiShift($params)) : (new SingleShift($params));
            return $schedule->scheduleList();
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function getScheduleParams(): array
    {
        $params = $this->all(new ParamModel);

        $bisection_count = 1;
        $shifts = [];
        foreach ($params as $param) {
            if ($param['key'] == 'bisection_count') {
                $bisection_count = $param['value'];
            }
            if ($param['key'] == 'shifts') {
                $shifts = json_decode($param['value'], true);
            }
        }

        return ['bisection_count' => $bisection_count, 'shifts' => $shifts];
    }

    public function getScheduleWrapParams(string $workLine, string $year, string $month): array
    {
        $params = $this->getScheduleParams();
        $shifts = $params['shifts'];
        $getArrangeDays = function (string $year, string $month) use ($shifts) {
            // 行事历
            $arrangeDays = (new CalendarService)->getCalendarByDate($year, $month);

            foreach ($arrangeDays as $k => $v) {
                if ($v['profile']) {
                    $profile = json_decode($v['profile'], true);
                    if (isset($profile['shifts'])) {
                        $shiftsTime = json_decode($profile['shifts'], true);
                        if (count($shifts) === 1) {
                            $shiftsTime = $shiftsTime[0];
                            $workdayTimeRange = array_map(function ($e) {
                                return date('H:i:s', strtotime($e['start'])) . ' - ' . date('H:i:s', strtotime($e['end']));
                            }, $shiftsTime['times']);

                            try {
                                list($morning, $afternoon, $evening) = $workdayTimeRange;
                            } catch (\Throwable $th) {
                                $offset = substr($th->getMessage(), -1);
                                if ($offset == '1') {
                                    $afternoon = null;
                                    $evening = null;
                                } else if ($offset == '2') {
                                    $evening = null;
                                }
                            }
                            $arrangeDays[$k]['morning'] = $morning;
                            $arrangeDays[$k]['afternoon'] = $afternoon;
                            $arrangeDays[$k]['evening'] = $evening;
                        } else {
                            $arrangeDays[$k]['shiftsTime'] = $shiftsTime;
                        }
                    }
                }
            }

            return $arrangeDays;
        };

        $date = "{$year}-{$month}";
        $prevDate = explode('-', date('Y-m', strtotime('-1 month', strtotime($date))));
        $nextDate = explode('-', date('Y-m', strtotime('1 month', strtotime($date))));
        $nnextDate = explode('-', date('Y-m', strtotime('2 month', strtotime($date))));

        $params = [
            'arrangeDays' => $getArrangeDays($year, $month),
            'pArrangeDays' => $getArrangeDays($prevDate[0], $prevDate[1]),
            'nArrangeDays' => $getArrangeDays($nextDate[0], $nextDate[1]),
            'nnArrangeDays' => $getArrangeDays($nnextDate[0], $nnextDate[1]),
            'shifts' => $shifts,
            'bisection_count' => $params['bisection_count'],
            'isMultiShift' => count($shifts) === 1
        ];

        return $params;
    }

}
