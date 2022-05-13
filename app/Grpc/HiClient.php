<?php

namespace App\Grpc;

class HiClient extends \Hyperf\GrpcClient\BaseClient
{
  public function sayHello(\Grpc\HiUser $argument)
  {
    return $this->_simpleRequest(
      '/grpc.hi/sayHello',
      $argument,
      [HiReply::class, 'decode']
    );
  }
}
