<?php

namespace App\Service\TPM;

use App\Model\TPM\MachineModel;
use App\Service\Service;
use Hyperf\DbConnection\Db;
use App\Model\TPM\RecordModel;
use App\Model\TPM\StaffModel;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;


class RecordService extends Service
{


  /**
   * 维修记录
   * @param $search_contition 搜索条件，包括：
   *          left_time : string  开始日期  格式: YYYY-mm-dd
   *          right_time: string  结束日期  格式: YYYY-mm-dd
   *          mache_num:  string  设备编号   e.g: SCA1007
   *          reporter_id: string 报修人账号  e.g: admin
   *          noreach:   boolean  未到场
   *          cause:     string   报修原因
   * 
   * @param $limit 输出行数
   */
  public function getRecord(array $search_condition = [], int $limit = 1000)
  {
    $data['msg'] = '数据返回错误';
    $data['code'] = 1;

    $field = [
      'r.id',
      'r.mechenum',
      'r.mache_name as mache_name_record',
      'r.alarmtime',
      'r.reachtime',
      'r.repaircontents',
      'r.repairmethod',
      'r.repairman',
      'r.report_dep',
      'r.repairtime',
      'r.repairAttr',
      'r.repairstatus',
      'm.mache_name',
      'm.keep_department'
    ];

    $query =  Db::table((new RecordModel)->getTable() . ' as r')
      ->select($field)
      ->leftjoin((new MachineModel)->getTable() . ' as m', 'r.mechenum', '=', 'm.mache_num');

    if ($search_condition['left_time']) {
      $pro_time_star = strtotime($search_condition['left_time'] . ' 00:00:00');
      $query->where('alarmtime', '>', $pro_time_star);
    }

    if ($search_condition['right_time']) {
      $pro_time_end = strtotime($search_condition['right_time'] . ' 23:59:59');
      $query->where('alarmtime', '<', $pro_time_end);
    }

    if ($search_condition['mache_num']) {
      $query->where('mechenum', '=', $search_condition['mache_num']);
    }

    if ($search_condition['cause']) {
      $query->where('repaircontents', 'like', '%' . $search_condition['cause'] . '%');
    }

    // 报修人查询 及 部门管理查看下属机器报修
    if ($search_condition['reporter_id']) {
      $reporterConId = $search_condition['reporter_id'];

      // 增加字符过滤匹配
      $reporter = StaffModel::query()->where('notify_con_id', $reporterConId)->first();
      $filter_str = $reporter ? $reporter->filter_string : '';

      // 部门报修匹配到部门管理人
      if (!empty($filter_str)) {
        $query->where(function ($q) use ($filter_str) {
          $q->where('keep_department', 'like', '%' . $filter_str . '%')
            ->orWhere('report_dep', 'like', '%' . $filter_str . '%');
        });
      } else {
        $query->where('reporter_con_id', '=', $reporterConId);
      }
    }

    // 未到场的报修
    if ($search_condition['noreach']) {
      $query->where('reachtime', '=', 0);
      $query->where('repairstatus', '=', false);
    }

    $data['data'] = $query->orderBy('repairstatus', 'asc')
      ->orderBy('repairtime', 'desc')
      ->limit($limit)
      ->get();

    // 整理输出格式
    $data['data'] = json_decode(json_encode($data['data']), true);
    foreach ($data['data'] as $key => $value) {
      if (!empty(intval($value['repairtime']))) {
        $data['data'][$key]['expendtime'] = (intval($value['repairtime']) - intval($value['alarmtime'])) / 60;
      } else {
        $data['data'][$key]['expendtime'] = 0;
      }
      if ($value['mache_name_record']) {
        $data['data'][$key]['mache_name'] = $value['mache_name_record'];
      }
      $data['data'][$key]['alarmtime'] = date('Y-m-d H:i:s', $value['alarmtime']);
      $data['data'][$key]['reachtime'] = date('Y-m-d H:i:s', intval($value['reachtime']));
      $data['data'][$key]['repairtime'] = date('Y-m-d H:i:s', $value['repairtime']);
    }

    $data['code'] = 0;
    $data['msg'] = 'success';
    return $data;
  }


  /**
   * 手动添加、修改报修记录
   */
  public function saveRecord()
  {
  }
}
