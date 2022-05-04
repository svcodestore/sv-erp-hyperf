<?php

namespace App\Controller\TPM;

use App\Model\TPM\FittingModel;
use App\Controller\AbstractController;

/**
 * TPM 配件相关功能 API Controller
 */

class FittingController //extends AbstractController
{
  public function fittings()
  {
    return FittingModel::all();
  }
}
