<?php

namespace App\Controller\TPM;

use App\Model\TPM\StaffModel;

class StaffController extends \App\Controller\AbstractController
{


  public function staffs()
  {
    return StaffModel::all();
  }
}
