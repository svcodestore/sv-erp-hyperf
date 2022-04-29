<?php

namespace App\Service;

interface INotification
{

  public function send($to, $msg);
}
