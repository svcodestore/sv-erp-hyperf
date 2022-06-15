<?php

namespace App\Controller\TPM;

use App\Model\TPM\MachineModel;
use App\Service\TPM\ReportService;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * TPM 报修功能 Controller
 */


class ReportController extends \App\Controller\AbstractController
{

  public function report(RequestInterface $request, ReportService $service)
  {

    $mache_num = $request->input('mache_num');
    $mache_name = $request->input('mache_name');
    $department = $request->input('department', '');
    $location = $request->input('location', '');
    $cause = $request->input('cause', '');
    $reporter_name = $request->input('reporterName');
    $reporter_id = $request->input('reporterConId');

    return $service->quickReport(
      $mache_num,
      $mache_name,
      $cause,
      $department,
      $location,
      $reporter_name,
      $reporter_id
    );
  }

  public function checkCode(RequestInterface $request)
  {
  }
}
