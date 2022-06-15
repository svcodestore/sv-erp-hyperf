<?php

namespace App\Controller\TPM;

use App\Controller\AbstractController;
use App\Service\TPM\OtherService;

class OtherController extends AbstractController
{


  public function commonReseaons(OtherService $service)
  {
    return $service->getCommonReseaons();
  }

  public function commonDevices(OtherService $service)
  {
    return $service->getCommonDevices();
  }
}
