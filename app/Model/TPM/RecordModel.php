<?php

namespace App\Model\TPM;

use App\Model\Model;



class RecordModel extends Model
{
  protected $table = 'tpmdb.repair_record';

  /**
   * 关联机器表
   */
  public function machine()
  {
    return $this->hasOne(MachineModel::class, 'mechenum', 'mache_num');
  }
}
