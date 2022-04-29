<?php

namespace App\Controller\TPM;

/**
 * TPM 报修功能 Controller
 */


class ReportController extends \App\Controller\AbstractController
{

  public function report(): array
  {
    return $this->responseOk();
  }
}
