<?php

/**
 * MachineController
 * TPM 机器设备相关 API Controller
 */

namespace App\Controller\TPM;

use App\Model\TPM\MachineModel;

class MachineController extends \App\Controller\AbstractController
{

  public function machines()
  {
    return MachineModel::all();
  }
}
