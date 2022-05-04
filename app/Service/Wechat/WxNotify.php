<?php

namespace App\Service\Wechat;

use App\Model\WeChat\WeChat;
use App\Service\Wechat\Wxpusher;

class WxNotify
{

  protected $pusher;
  protected $_token;
  public function __construct()
  {
    $this->_token = 'AT_Dl6oNbsjKmjOSv2KNeOSODfxSV7tWedY';
    $this->pusher = new Wxpusher($this->_token);
  }


  public function send($to, $msg)
  {
    return $this->sendTextMsg($to, $msg);
  }

  /**
   * 通过微信公众号发送文本信息
   * @param string $con_id 用户系统账号名
   * @param string $text 内容
   * @return bool
   */
  public function sendTextMsg($con_id, $text)
  {
    $wx_uid = [];
    is_string($con_id) and $con_id = [$con_id];

    foreach ($con_id as $cid) {
      $user = WeChat::query()->where('con_id', $cid)->first();
      if ($user) {
        $wx_uid[] = $user['wx_uid'];
      }
    }

    if (count($wx_uid)) {
      return $this->pusher->send($text, 1, true, $wx_uid);
    }
    return false;
  }

  /**
   * 生成用于扫描关注的二维码
   * @param string $extra 额外信息(用于区别扫码人)
   * @return bool
   */
  public function Qrcreate($extra = '')
  {
    return $this->pusher->Qrcreate($extra);
  }
}
