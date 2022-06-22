<?php

namespace App\Controller\TPM;

use App\Controller\AbstractController;
use App\Service\TPM\ScheduleService;
use Hyperf\HttpServer\Contract\RequestInterface;

class ScheduleController extends AbstractController
{

  public function schedules(RequestInterface $request, ScheduleService $service)
  {
    $stime = $request->input('stime', date('Y-m-d'));
    $etime = $request->input('etime');
    $mache_num = $request->input('mache_num');

    return $service->getSchedules($stime, $etime, $mache_num);
  }
}
