<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\RankModel;
use App\Service\Service;

class RankService extends Service
{
    public function getAllItem(): array
    {
        return $this->all(new RankModel);
    }
}
