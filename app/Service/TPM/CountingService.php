<?php

namespace App\Service\TPM;

use App\Model\TPM\MachineModel;
use App\Service\Service;
use Hyperf\DbConnection\Db;
use App\Model\TPM\RecordModel;

class CountingService extends Service
{

  /**
   * 统计时间段内故障率
   * @param string $stime  DateTime Format: YYYY-mm-dd HH:ii:ss
   * @param string $etime  DateTime Format: YYYY-mm-dd HH:ii:ss
   * @return array
   */
  public function countFaildRate(string $stime, string $etime)
  {

    $record_table_name = (new RecordModel)->getTable();
    $machine_table_name = (new MachineModel)->getTable();

    $SQL = "SELECT prr.mechenum, pmi.mache_name, pmi.keep_department, 
      sum(if(prr.repairtime, prr.repairtime, UNIX_TIMESTAMP()) - prr.alarmtime) AS cost_time, 
      count(prr.id) as report_times
    FROM {$record_table_name} AS prr 
    LEFT JOIN {$machine_table_name} AS pmi ON pmi.mache_num = prr.mechenum
    WHERE (prr.alarmtime BETWEEN UNIX_TIMESTAMP('$stime') AND UNIX_TIMESTAMP('$etime'))
      AND repairAttr = '维修'
    GROUP BY mechenum, mache_name, keep_department";

    $data = Db::select($SQL);
    $durition =  strtotime($etime) - strtotime($stime);
    $data = json_decode(json_encode($data), true);

    foreach ($data as $k => $v) {
      if ($v['cost_time'] > 24 * 3600) {
        $data[$k]['rate'] = (floatval($v['cost_time']) / $durition) * 100;
      } else {
        // 每天只算12小时
        $data[$k]['rate'] = (floatval($v['cost_time']) / ($durition / 2)) * 100;
      }
    }
    return $data;
  }

  /**
   * 统计月份故障率
   * @param string $stime  DateTime Format: YYYY-mm-dd HH:ii:ss
   * @param string $etime  DateTime Format: YYYY-mm-dd HH:ii:ss
   * @return array
   */
  public function countFaildRateGroupByMonth(string $stime, string $etime, string $mache_num = '')
  {
    $record_table_name = (new RecordModel)->getTable();
    $machine_table_name = (new MachineModel)->getTable();

    $mache_condition = '';
    if ($mache_num) {
      $mache_condition = " AND prr.mechenum = '$mache_num'";
    }

    $allTimes = (strtotime($etime) - strtotime($stime)) * 0.5;

    $SQL = "SELECT sum(if(prr.repairtime, prr.repairtime, UNIX_TIMESTAMP()) - prr.alarmtime) AS cost_time, 
      FROM_UNIXTIME(alarmtime, '%Y-%m') AS month,
      count(prr.id) AS report_times
    FROM {$record_table_name} AS prr 
    WHERE (prr.alarmtime BETWEEN UNIX_TIMESTAMP('$stime') AND UNIX_TIMESTAMP('$etime') ) 
      AND prr.repairAttr = '维修' $mache_condition
    GROUP BY FROM_UNIXTIME(alarmtime, '%Y-%m')
    ORDER BY MONTH ASC";

    $data = Db::select($SQL);
    $SQL2 = "SELECT count(*) as mtc FROM {$machine_table_name}";
    $d2 = Db::select($SQL2);

    $data = json_decode(json_encode($data), true);
    $d2 = json_decode(json_encode($d2), true);
    foreach ($data as $k => $v) {
      $data[$k]['rate'] = $v['cost_time'] / $allTimes / $d2[0]['mtc'] * 100;
    }
    return $data;
  }


  /**
   * 按部门统计时间段内的故障率
   * @param string $stime
   * @param string $etime
   * @return array
   */
  public function countFaildRateGroupByDep(string $stime, string $etime)
  {

    $record_table_name = (new RecordModel)->getTable();
    $machine_table_name = (new MachineModel)->getTable();

    $SQL = "SELECT 
      sum(if(prr.repairtime, prr.repairtime, UNIX_TIMESTAMP()) - prr.alarmtime) AS cost_time, 
      count(prr.id) as report_times,
      pmi.keep_department
    FROM {$this->repair_record} AS prr  
    LEFT JOIN {$this->meche_info} AS pmi ON	prr.mechenum = pmi.mache_num
    WHERE (prr.repairtime IS NULL OR (prr.repairtime BETWEEN UNIX_TIMESTAMP('$start') AND UNIX_TIMESTAMP('$end')) ) 
      AND prr.repairAttr = '维修'
    GROUP BY pmi.keep_department";

    return Db::query($SQL);
  }
}
