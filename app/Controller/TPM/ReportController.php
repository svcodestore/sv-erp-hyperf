<?php

namespace App\Controller\TPM;

use App\Service\TPM\ReportService;

/**
 * TPM 报修功能 Controller
 */


class ReportController extends \App\Controller\AbstractController
{

  public function report(ReportService $service)
  {
    $service->quickReport($this->request);
    return [123, 321];
  }
}
