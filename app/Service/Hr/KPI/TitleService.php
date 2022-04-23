<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\TitleModel;

class TitleService extends \App\Service\Service
{
    public function getAllTitle(): array
    {
        return $this->all(new TitleModel);
    }
}