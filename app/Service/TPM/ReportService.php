<?php

namespace App\Service\TPM;

use App\Service\Service;
use App\Model\TPM\StaffModel;
use App\Model\TPM\RecordModel;
use App\Model\TPM\MachineModel;
use Hyperf\HttpServer\Contract\RequestInterface;
use App\Service\Sms\AliyunSms;
use App\Service\Wechat\WxNotify;

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
  public function quickReport(RequestInterface $request): array
  {
    $params = $request->all();
    $notifyMans = StaffModel::query()->where('delay_time', 0)->where('type', 2)->get();

    foreach ($notifyMans as $m) {
      var_dump($m['notify_name']);
    }
    return [];
    $newRecord = new RecordModel();
    $return['code'] = 1;
    $return['msg'] = '发送失败';

    // 根据编号，查找机器信息
    if (isset($params['macheNum']) && !empty($params['macheNum'])) {
      $mache_info = MachineModel::query()->where('mache_num', $params['macheNum'])->first();
    }

    // 默认，无编号或找不到机器信息
    if (empty($mache_info)) {
      $mache_info['mache_name'] = $request->input('macheName', '');
      $mache_info['mache_num'] = $request->input('macheNum', '');
      $mache_info['line_num'] = $request->input('lineNum', '');
      $mache_info['produc_num'] = $request->input('producNum', '');
      $mache_info['keep_department'] = '<无>';
    }

    // 记录数据
    $newRecord->mechenum = $params['macheNum'];
    $newRecord->mache_name = $params['macheName'];
    $newRecord->alarmtime = time();
    $newRecord->repairAttr = $request->input('cate', '');
    $newRecord->repairstatus = 'false';
    $newRecord->dell_repair = 0;
    $newRecord->ismis = $request->input('ismis', 0);
    $newRecord->repaircontents = $request->input('cause', '');
    $newRecord->reporter_con_id = $request->input('reporterConId', '');
    $newRecord->reporter_name = $request->input('reporterName', '');
    $newRecord->report_dep = $request->input('noticeDepartment', '');
    $newRecord->report_place = $request->input('address', '');

    return [];

    // 保存数据后通知维修人员
    if ($newRecord->save()) {
      $_dpart = $request->input('noticeDepartment', $mache_info['keep_department']);

      $content['part'] =  $_dpart ?: $mache_info['keep_department'];
      $content['number'] = $mache_info['line_num'];
      $content['line_num'] = $mache_info['produc_num'];
      $content['meche_num'] = $mache_info['mache_num'];
      $content['meche_name'] = ($mache_info ? $mache_info['mache_name'] : ''); //机器名和初步原因
      $content['department'] = $content['part'];
      $content['meche'] = $mache_info['mache_name'];
      $content['cause'] = $params['cause'];
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
      // $phone = implode(',', $params['arr']);
      $phone = array_map(function ($item) {
        return $item['notify_phone'];
      }, $filterMans);
      $phone = implode(',', $phone);

      $res = $this->smsService->send($phone, $content);
      // $res = smsSend($phone, '文迪软件', 'SMS_210075241', $content);

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
   * 循环提醒
   */
  public function remainLoop()
  {
  }
}
