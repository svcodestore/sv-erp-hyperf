<?php

namespace App\Service\TPM;

use App\Model\TPM\MachineModel;
use App\Service\Service;


class MachineService extends Service
{


  public function getMachines($line_num = null, $status = null, $page = 0, $limit = 10000)
  {
    $query = MachineModel::query();

    $line_num !== null and $query->where('line_num', '=', $line_num);
    $status !== null and $query->where('status', '=', $status);
    $page and $query->offset($page * $limit);
    $limit and $query->limit($limit);

    return $query->get();
  }

  public function getMachineDetailInfo()
  {
  }

  public function saveMacheInfo()
  {
  }
}
