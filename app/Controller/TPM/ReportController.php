<?php

namespace App\Controller\TPM;

use App\Service\TPM\ReportService;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * TPM 报修功能 Controller
 */


class ReportController extends \App\Controller\AbstractController
{

  public function report(RequestInterface $request, ReportService $service)
  {
    $service->quickReport($request);
    return [123, 321];
  }
}
