<?php

namespace App\Service\Sms;

use AlibabaCloud\Client\AlibabaCloud;

use Exception;

class AliyunSms
{


  public function send($to, $msg): array
  {
    AlibabaCloud::accessKeyClient(env('ALIYUN_SMS_ACCESS_KEY'), env('ALIYUN_SMS_SECRET_KEY'))
      ->regionId('cn-hangzhou')
      ->asDefaultClient();
    try {
      $result = AlibabaCloud::rpc()
        ->product('Dysmsapi')
        // ->scheme('https') // https | http
        ->version('2017-05-25')
        ->action('SendSms')
        ->method('POST')
        ->host('dysmsapi.aliyuncs.com')
        ->options([
          'query' => [
            'RegionId' => "cn-hangzhou",
            'PhoneNumbers' => $to,
            'SignName' => '文迪软件',
            'TemplateCode' => 'SMS_210075241',
            'TemplateParam' => json_encode($msg),
          ],
        ])
        ->request();
      return $result->toArray();
    } catch (Exception $e) {
      var_dump($e);
    }
  }
}
