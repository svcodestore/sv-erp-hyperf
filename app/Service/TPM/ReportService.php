<?php

namespace App\Service\TPM;

use App\Service\Service;
use App\Model\TPM\StaffModel;
use App\Model\TPM\RecordModel;
use App\Model\TPM\MachineModel;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Di\Annotation\Inject;

class ReportService extends Service
{

  /**
   * @Inject
   * @var \App\Service\Sms\AliyunSms
   */
  private $smsService;

  /**
   * @Inject
   * @var \App\Service\Wechat\WxNotify
   */
  private $wxMsgService;

  /**
   * 快速报修
   * 
   */
  public function quickReport($mache_num, $mache_name, $cause, $department = '', $location = '', $reporter_name = '', $reporter_id = ''): array
  {

    $newRecord = new RecordModel();
    $return['code'] = 1;
    $return['msg'] = '发送失败';

    if ($mache_num) {
      $mache_info = MachineModel::query()->where('mache_num', $mache_num)->first();
    } else {
      $mache_info = new MachineModel();
    }

    // 记录数据
    $newRecord->mechenum = $mache_num;
    $newRecord->mache_name = $mache_name;
    $newRecord->alarmtime = time();
    $newRecord->repairAttr = '';
    $newRecord->repairstatus = 'false';
    $newRecord->dell_repair = 0;
    $newRecord->ismis = 0;
    $newRecord->repaircontents = $cause;
    $newRecord->reporter_con_id = $reporter_id;
    $newRecord->reporter_name = $reporter_name;
    $newRecord->report_dep = $mache_info->keep_department ?: $department ?: '';
    $newRecord->report_place = $location;

    // 保存数据后通知维修人员
    if ($newRecord->save()) {
      $_dpart = $department ?: $mache_info->keep_department ?: '';

      $content['part'] =  $_dpart ?: $mache_info['keep_department'];
      $content['number'] = $mache_info['line_num'];
      $content['line_num'] = $mache_info['produc_num'];
      $content['meche_num'] = $mache_num;
      $content['meche_name'] = ($mache_info ? $mache_info['mache_name'] : $mache_name); //机器名和初步原因
      $content['department'] = $content['part'];
      $content['meche'] = $mache_info['mache_name'];
      $content['cause'] = $cause;
      $content['time'] = date('Y-m-d H:i:s');

      // 微信通知
      // 获取要通知的人, 时延：0  目前只有 TPM
      $notifyMans = StaffModel::query()->where('delay_time', 0)->where('type', 1)->get()->toArray();

      // 根据过滤字符, 匹配信息发送人
      $filterMans = array_filter($notifyMans, function ($item) use ($mache_info, $content) {

        if (intval($item['max_times']) < 1) {
          return false;
        }

        // 有过滤字符串， 但与部门不匹配， 不通知
        $_filter_content = implode(',', $mache_info) . $content['part'] . $content['department'];
        if (strlen($item['filter_string']) > 0 && preg_match('/' . $item['filter_string'] . '/', $_filter_content) == 0) {
          return false;
        }

        return true;
      });


      $con_id = array_map(function ($item) {
        return $item['notify_con_id'];
      }, $filterMans);

      $template =
        "斯达文星TPM报修
            部门：%s
            设备：%s
            编号: %s
            故障: %s
            请及时维修 %s";

      $this->wxMsgService->send(
        $con_id,
        sprintf(
          $template,
          $content['part'],
          $content['meche_name'],
          $content['meche_num'],
          $content['cause'],
          $content['time']
        )
      );

      // 短信通知
      $phone = array_map(function ($item) {
        return $item['notify_phone'];
      }, $filterMans);
      $phone = implode(',', $phone);

      $res = $this->smsService->send($phone, $content);

      if ($res['Code'] !== 'OK') {
        $return['msg']  = '已添加到系统, 但未能发送短信通知';
      } else {
        $return['code'] = 0;
        $return['msg']  = '发送成功';
      }
    }

    return $return;
  }


  /**
   * 维修到场确认
   * @param $phone 维修人电话
   * @param $inputNum 维修人确认码(电话后四位)
   * @param $record_id 报修记录 id
   * @param $mache_num 机器编号
   */
  public function checkCode($phone, $inputNum, $record_id = null, $mache_num = null)
  {
    $msg['code'] = 1;
    $msg['msg'] = 'error';

    if (!$record_id && $mache_num) {
      // $noreach_ids = $record->repair_record(
      //   'r.id',
      //   "`mechenum` = '$mache_num' AND `reachtime` = 0 AND `repairstatus` = 'false'",
      //   0,
      //   1000
      // );

      $noreach_ids = RecordModel::query()->select('id')
        ->where('mechenum', '=', $mache_num)
        ->where('reachtime', '=', 0)
        ->where('repairstatus', '=', false)
        ->get();

      $ids = array_map(function ($item) {
        return $item['id'];
      }, $noreach_ids);
    } else {
      $ids = [$record_id];
    }

    if (empty($ids)) {
      return $msg;
    }

    $staff = StaffModel::query()->where('notify_phone', '=', $phone)->first();
    if ($staff->notify_name && $phone && $inputNum) {

      foreach ($ids as $row) {
        $record = RecordModel::query()->find($row->id);
        $record->repairman = $staff->notify_name;
        $record->reachtime = time();
        $record->save();
      }

      $msg['code'] = 0;
      $msg['msg'] = 'success';
    }

    // $where['notify_phone'] = $phone;
    // $list = $record->getNotify('id,notify_phone,notify_name', $where, 0, 10000);

    // if ($list[0]['notify_name'] && $phone && $phoneFour) {
    //   $data['repairman'] = $list[0]['notify_name'];
    //   $data['reachtime'] = time();
    //   foreach ($ids as $id) {
    //     $res = $record->updateRecord($id, $data);
    //   }
    //   if ($res) {
    //     $msg['code'] = 0;
    //     $msg['msg'] = 'success';
    //   }
    //   return json($msg);
    // }



    return $msg;
  }
}
