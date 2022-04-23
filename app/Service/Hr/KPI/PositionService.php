<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\PositionModel;

class PositionService extends \App\Service\Service
{
    public function getAllPosition(): array
    {
        return $this->all(new PositionModel);
    }
}