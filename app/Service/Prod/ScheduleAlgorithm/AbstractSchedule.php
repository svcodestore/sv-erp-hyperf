<?php

declare(strict_types=1);

namespace App\Service\Prod\ScheduleAlgorithm;

use Exception;
use Throwable;

abstract class AbstractSchedule implements ISchedule
{
    protected const DAY_SECONDS = 86400;
    protected const DATETIME_FORMAT = 'Y-m-d H:i:s';
    protected const DATE_FORMAT = 'Y-m-d';

    protected array $prodList = [];
    protected array $maxPhsCost = [];
    protected string $year;
    protected string $month;
    protected string $workLine;
    protected array $arrangeDays = [];
    protected array $pArrangeDays = [];
    protected array $nArrangeDays = [];
    protected array $nnArrangeDays = [];
    protected int $splitCount;
    protected array $shifts = [];
    protected int $schStartAt;

    public function scheduleList(): array
    {
        return $this->prodList;
    }

    /**
     * @throws Exception
     */
    protected function __construct(array $params)
    {
        $this->prodList = $params['prodList'];

        $this->year = $params['year'];
        $this->month = $params['month'];
        $this->workLine = $params['workLine'];
        $this->arrangeDays = $params['arrangeDays'];
        $this->pArrangeDays = $params['pArrangeDays'];
        $this->nArrangeDays = $params['nArrangeDays'];
        $this->nnArrangeDays = $params['nnArrangeDays'];

        $this->splitCount = (int)$params['bisection_count'];

        $this->setTimeParam();

        $this->setMaxPhaseCost();

        $this->adjustPhasePosition(true);
    }

    abstract protected function setTimeParam();

    protected function setMaxPhaseCost(): void
    {
        $phsCost = [];
        foreach ($this->prodList as $orderItem) {
            if (!empty($orderItem['phases'])) {
                foreach ($orderItem['phases'] as $phase) {
                    $phsCost[$orderItem['id']]['cost'][] = $phase['cost_time'];
                }
            }
        }

        $this->maxPhsCost = array_map(fn ($e) => max($e['cost']), $phsCost);
    }

    /**
     *  调整工站位置，副流程工站在车间上线和皮企入库之间
     * @throws Exception
     */
    private function adjustPhasePosition(bool $isSchedule = false)
    {
        foreach ($this->prodList as $key => $orderItem) {
            if (empty($orderItem['phases'])) continue;

            $beforeOnlineWorkshops = [];
            $afterOnlineWorkshops = [];
            foreach ($orderItem['phases'] as $p) {
                if ($p['code_id'] === '005') {
                    array_push(
                        $beforeOnlineWorkshops,
                        ...array_filter(
                            $orderItem['phases'],
                            fn ($e) => $e['is_master'] === 0
                        )
                    );

                    $beforeOnlineWorkshops[] = $p;
                } else if ($p['is_master'] === 1) {
                    if ($p['code_id'] < 5) {
                        $beforeOnlineWorkshops[] = $p;
                    } else {
                        $afterOnlineWorkshops[] = $p;
                    }
                }
            }

            // 调整工站顺序一并计算工序开始时间和完成时间
            if ($isSchedule) {
                $this->schedule($key, $beforeOnlineWorkshops, $afterOnlineWorkshops);
                // try {
                // } catch (Throwable $th) {
                //     $msg = "生产单工站设定错误，未找到车间上线工站。\n";
                //     $msg .= "{$this->prodList[$key]['workshop_name']} {$this->prodList[$key]['customer_no']} {$this->prodList[$key]['customer_pono']} {$this->prodList[$key]['item_code']} 生产单工站：\n";
                //     $msg .= implode("，", array_map(function ($e) {
                //         return $e['name'];
                //     }, $this->prodList[$key]['phases']));
                //     throw new Exception($msg);
                // }
            }

            $this->prodList[$key]['phases'] = array_merge($beforeOnlineWorkshops, $afterOnlineWorkshops);
        }
    }

    /**
     * @description: 自动排程算法，通过生产单，行事历，生产工序信息（耗时）三个表中的数据来计算
     */
    protected function schedule(int $phsSeq, array &$beforeOnlineWorkshops, array &$afterOnlineWorkshops)
    {
        $prdTotal = $this->prodList[$phsSeq]['item_qty'];

        /**
         *  将生产单的每一单以车间上线工站为划分点，分成车间上线之前的工站，和车间上线之后的工站；
         *  工站的开始时间是上一工站的等分生产所需时间
         *  每一个工站一个接着一个顺序生产。车间上线之前的工站向前计算开始时间
         *  第一单的车间上线时间给定，上一单的车间上线之后第一个工站完成时间是下一单车间上线的时间
         */

        // 处理车间上线之前工站
        $index = count($beforeOnlineWorkshops) - 1;

        while ($index > -1) {
            $p = $beforeOnlineWorkshops[$index];
            list($allPhaseNeed, $singlePhaseNeed)
                = $this->phaseNeedTime(
                    $prdTotal,
                    (int)$p['cost_time'],
                    $phsSeq,
                    (int) $p['worker_num']
                );

            if ($p['code_id'] === '005') {
                if ($phsSeq === 0) {
                    $start = $this->schStartAt;
                } else {
                    $start =
                        strtotime(
                            $this->getFirstMasterPhase(
                                $this->prodList[$phsSeq - 1]['phases']
                            )['complete_at']
                        );
                }
                $lastStart = $start;
            } else {
                $nextPhase = $beforeOnlineWorkshops[$index + 1];
                $lastStart = $start =
                    strtotime($nextPhase['start_at']);
                if ((int)$p['out_time'] > 0) {
                    $start -= (int)$p['out_time'];
                } else {
                    $start -= $singlePhaseNeed;
                }
                $start -= ($p['dead_time'] + $p['ahead_time']);
            }

            $start = $this->handlePhaseStartTime($lastStart, $start, true);

            $this->handleStartTimeWithArrange($start, true);
            $beforeOnlineWorkshops[$index]['start_at'] = date(self::DATETIME_FORMAT, $start);

            if ((int)$p['out_time'] > 0) {
                $complete = $start + (int)$p['out_time'];
                $complete = $this->handlePhaseCompleteTime($complete, $complete);
            } else {
                $complete = $start + $allPhaseNeed;
                $complete = $this->handlePhaseCompleteTime($start, $complete);
            }
            $this->handleCompleteTimeWithArrange($complete, $start);

            $beforeOnlineWorkshops[$index]['complete_at'] = date(self::DATETIME_FORMAT, $complete);

            $index--;
        }

        $onlineWorkshop = $beforeOnlineWorkshops[count($beforeOnlineWorkshops) - 1];

        // 处理车间上线之后工站
        foreach ($afterOnlineWorkshops as $k => $p) {
            list($allPhaseNeed, $singlePhaseNeed) = $this->phaseNeedTime(
                $prdTotal,
                (int)$p['cost_time'],
                $phsSeq,
                (int) $p['worker_num']
            );
            if ($k === 0) {
                $lastStart = strtotime($onlineWorkshop['start_at']);
                $start = $lastStart + $onlineWorkshop['dead_time'] + $onlineWorkshop['ahead_time'];
            } else {
                $prevPhase = $afterOnlineWorkshops[$k - 1];
                list($preAllPhaseNeed, $preSinglePhaseNeed) = $this->phaseNeedTime(
                    $prdTotal,
                    (int)$prevPhase['cost_time'],
                    $phsSeq,
                    (int) $p['worker_num']
                );

                if ((int)$prevPhase['out_time'] > 0) {
                    $lastStart = $start = strtotime($prevPhase['complete_at']);
                } else {
                    $lastStart = $start;
                    $start += ($preSinglePhaseNeed + $prevPhase['dead_time'] + $prevPhase['ahead_time']);
                    unset($prevPhase);
                }
            }
            $start = $this->handlePhaseStartTime($lastStart, $start);
            $this->handleStartTimeWithArrange($start);
            $afterOnlineWorkshops[$k]['start_at'] = date(self::DATETIME_FORMAT, $start);

            if ((int)$p['out_time'] > 0) {
                $complete = $start + (int)$p['out_time'];
                $complete = $this->handlePhaseCompleteTime($complete, $complete);
            } else {
                $complete = $start + $allPhaseNeed;
                $complete = $this->handlePhaseCompleteTime($start, $complete, $phsSeq, $k);
            }
            $this->handleCompleteTimeWithArrange($complete, $start);
            $afterOnlineWorkshops[$k]['complete_at'] = date(self::DATETIME_FORMAT, $complete);
        }
        // die;
    }

    /**
     * @description: 计算工站单等分所需时间和全部生产所需时间
     */
    protected function phaseNeedTime(int $total, int $cost, int $phsSeq, int $workerNum = 1): array
    {
        // 工序耗时为整张生产单中最大工序耗时
        $costTime        = $cost > 0 ? $this->maxPhsCost[$this->prodList[$phsSeq]['id']] : 0;
        $costTime       /= $workerNum;
        $singlePhaseNeed = $this->splitCount * $costTime;
        $allPhaseNeed    = $total * $costTime;

        return [(int)$allPhaseNeed, (int)$singlePhaseNeed];
    }

    /**
     * @description: 查找车间上线工站后的第一个主流程
     */
    protected function getFirstMasterPhase(array $phases): array
    {
        foreach ($phases as $k => $p) {
            if ($p['code_id'] === '005') {
                if (isset($phases[$k + 1])) {
                    return $phases[$k + 1];
                }
            }
        }

        return [];
    }

    /**
     * 处理工序开始时间。加入工作日上下班休息时间往后推
     * @param int $computeStartAt 工序开始时间，时间戳。未算入算入工作日上下班休息时间。
     * @param int $isReverse 是否向前计算时间
     * @return int $phaseActualStartAt 工序完成时间，时间戳。已算入工作日上下班休息时间。
     * @description 开始时间超过下班时间点就算对应的休息时间
     * @access protected
     */
    abstract protected function handlePhaseStartTime(int $lastStartAt, int &$computeStartAt, $isReverse = false): int;

    /**
     * @description: 是否在行事历时间范围内
     */
    abstract protected function isInCalendar(int $timestamp): bool;

    /**
     * @description: 计算当天的行事历设定
     */
    abstract protected function arrangeDaysCompute(int $timestamp): array;

    /**
     * @description: 计算工站开始时间，加入休息日
     */
    protected function handleStartTimeWithArrange(int &$timestamp, bool $isReverse = false): void
    {
        $arrangeDays = $this->getNowArrangeDays($timestamp);

        foreach ($arrangeDays as $arrangeDay) {
            if (
                date(self::DATE_FORMAT, $timestamp) ===
                $arrangeDay['date'] && $arrangeDay['is_rest'] === 1
            ) {
                if ($isReverse) {
                    $timestamp -= self::DAY_SECONDS;
                } else {
                    $timestamp += self::DAY_SECONDS;
                }
                $this->handleStartTimeWithArrange($timestamp, $isReverse);
            }
        }
    }

    /**
     * @description: 获取用于当前日期的行事历
     */
    protected function getNowArrangeDays(int $timestamp): array
    {
        $arrangeDays = $this->arrangeDays;

        // 其它月计算
        $realMonth = date('Y-m', $timestamp);
        $currMonth = date('Y-m', $this->schStartAt);
        if (isset($arrangeDays[0]) && isset($arrangeDays[0]['date'])) {
            $currMonth = date('Y-m', strtotime($arrangeDays[0]['date']));
        }

        if ($realMonth !== $currMonth) {
            if (strtotime($realMonth) < strtotime($currMonth)) {
                $arrangeDays = $this->pArrangeDays;
            } else {
                $realYear = substr($realMonth, 0, 4);
                $currYear = substr($currMonth, 0, 4);
                $rm = substr($realMonth, -2);
                $cm = substr($currMonth, -2);
                if ($realYear > $currYear) {
                    if ($cm === '12') {
                        if ($rm === '01') {
                            $arrangeDays = $this->nArrangeDays;
                        } else {
                            $arrangeDays = $this->nnArrangeDays;
                        }
                    }
                } else {
                    if ($rm - $cm > 1) {
                        $arrangeDays = $this->nnArrangeDays;
                    } else {
                        $arrangeDays = $this->nArrangeDays;
                    }
                }
            }
        }

        return $arrangeDays;
    }

    /**
     * 处理工序完成时间。加入工作日上下班休息时间
     * @param int $computeStartAt 工序开始时间，时间戳。未算入工作日上下班休息时间。
     * @param int &$phaseCompleteAt 工序完成时间，时间戳。未算入工作日上下班休息时间。
     * @return int $phaseActualCompleteAt 工序完成时间，时间戳。已算入工作日上下班休息时间。
     * @description 函数内自身递归调用自身。通过工序开始、完成时间是否在同一天进行计算
     * @access protected
     */
    abstract protected function handlePhaseCompleteTime(int $computeStartAt, int &$phaseCompleteAt): int;

    /**
     * @description: 计算工站完成时间，加入休息日
     */
    protected function handleCompleteTimeWithArrange(int &$timestamp, int $startTime): void
    {
        $arrangeDays = $this->getNowArrangeDays($timestamp);

        foreach ($arrangeDays as $arrangeDay) {
            if (
                strtotime($arrangeDay['date']) > $startTime &&
                strtotime($arrangeDay['date']) <
                $timestamp &&
                $arrangeDay['is_rest'] === 1
            ) {
                $timestamp += self::DAY_SECONDS;
            }
        }
    }
}
