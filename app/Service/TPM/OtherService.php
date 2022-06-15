<?php

namespace App\Service\TPM;

use App\Model\TPM\CommonDeviceModel;
use App\Model\TPM\ReseaonsModel;
use App\Service\Service;

class OtherService extends Service
{


  public function getCommonReseaons()
  {
    return ReseaonsModel::all();
  }

  public function getCommonDevices()
  {
    return CommonDeviceModel::all();
  }
}
