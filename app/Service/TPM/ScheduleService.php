<?php

namespace App\Service\TPM;

use App\Model\TPM\FittingModel;
use App\Model\TPM\FittingUsedModel;
use App\Model\TPM\ScheduleModel;
use App\Service\Service;
use Hyperf\DbConnection\Db;

class ScheduleService extends Service
{

  /**
   * Get TPM Maintainance Schedules
   * 获取TPM维护排程计划
   * @param string $stime 搜索开始日期 format YYYY-mm-dd 
   * @param string $etime 搜索结束日期 format YYYY-mm-dd
   * @param string $mache_num 机器编号
   * @return array
   */
  public function getSchedules(string $stime, string $etime = '', string $mache_num = '')
  {

    $fields = [
      's.*',
      'f.fitting_service_life',
      Db::raw('(SELECT add_time FROM' . (new FittingUsedModel)->getTable() . ' WHERE record_id = s.record_id AND fitting_id = s.fitting_id ORDER BY add_time desc LIMIT 1)  AS last_replace_time')
    ];

    $query = Db::table((new ScheduleModel)->getTable() . ' as s')
      ->select($fields)
      ->leftJoin((new FittingModel)->getTable() . ' as f', 'f.id', '=', 's.fitting_id');

    $stime and $query->where('s.start_time', '>', $stime);
    $etime and $query->where('s.start_time', '<', $etime);
    $mache_num and $query->where('s.mache_num', '=', $mache_num);

    return $query->get();
  }
}
