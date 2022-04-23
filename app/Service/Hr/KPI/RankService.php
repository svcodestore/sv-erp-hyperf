<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\RankModel;

class RankService
{
    public function getAllItem()
    {
        return RankModel::query()->get()->toArray();
    }
}
