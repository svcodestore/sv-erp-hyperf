<?php

namespace App\Service\TPM;

use App\Service\Service;
use Hyperf\DbConnection\Db;
use App\Model\TPM\RecordModel;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;


class RecordService extends Service
{


  /**
   * 维修记录
   */
  public function getRecord(RequestInterface $request, ResponseInterface $response)
  {
    $data['msg'] = '数据返回错误';
    $data['code'] = 1;
    if ($request->isMethod('isMethod')) {
      return $response->json($data);
    }


    $record = new recordModel;
    $where = array();
    $data['repairAttr'] = $request->input('repairAttr');
    $data['pro_time_star'] = $request->input('leftTime');
    $data['pro_time_end'] = $request->input('rightTime');
    $data['repaircontents'] = $request->input('repaircontents');
    $data['mechenum'] = $request->input('mechenum');

    $limit = $request->input('limit', 1000);
    if ($request->input('repairAttr')) {
      $where[] = ['repairAttr', '=', $request->input('repairAttr')];
    }
    if ($request->input('leftTime') || $request->input('rightTime')) {
      $pro_time_star = strtotime($request->input('leftTime') . ' 00:00:00');
      $pro_time_end = strtotime($request->input('rightTime') . ' 23:59:59');
      if ($pro_time_star && $pro_time_end) {
        $where[] = ['alarmtime', '>', "$pro_time_star"];
        $where[] = ['alarmtime', '<', "$pro_time_end"];
      } elseif ($pro_time_star && !$pro_time_end) {
        $where[] = ['alarmtime', '>', "$pro_time_star"];
      } elseif ($pro_time_end && !$pro_time_star) {
        $where[] = ['alarmtime', '<', "$pro_time_end"];
      }
    }
    if ($request->input('repaircontents')) {
      $where[] = ['repaircontents', 'LIKE', '%' . $request->input('repaircontents') . '%'];
    }
    if ($request->input('mechenum')) {
      $where[] = ['mechenum', '=', $request->input('mechenum')];
    }
    // 新增：报修人查询
    if ($request->input('reporterConId')) {
      $reporterConId = $request->input('reporterConId');

      // 增加字符过滤匹配
      $reporters = $record->getNotify('*', ['notify_con_id' => $reporterConId], 0, 1);
      $filter_str = '';
      if ($reporters) {
        $filter_str = $reporters[0]['filter_string'];
      }

      if (!empty($filter_str)) {
        $where[] = ['keep_department|report_dep', 'LIKE', '%' . $filter_str . '%'];
      } else {
        $where[] = ['reporter_con_id', '=', $reporterConId];
      }
    }

    if ($request->input('noreach', false)) {
      $where[] = ['reachtime', '=', 0];
      $where[] = ['repairstatus', '=', 'false'];
    }

    $cnd = $where;
    $result = array();
    if ($request->input('error')) {
      $result = 'condition error';
      $data['data'] = $result['data'];
      $data['count'] = $result['count'];
    } else {

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

      $data['data'] = Db::table('tpmdb.repair_record as r')
        ->select($field)
        ->leftjoin('tpmdb.machines as m', 'r.mechenum', '=', 'm.mache_num')
        ->where($cnd)
        ->orderBy('repairstatus', ' asc')
        ->orderBy('repairtime', 'desc')
        ->limit($limit)
        ->get();


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
    }
    $data['code'] = 0;
    $data['msg'] = 'success';
    return $response->json($data);
  }


  /**
   * 手动添加、修改报修记录
   */
  public function saveRecord()
  {
  }
}
