<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\PositionGroupModel;

class PositionGroupService extends \App\Service\Service
{
    public function getAllPositionGroup(): array
    {
        return $this->all(new PositionGroupModel);
    }
}