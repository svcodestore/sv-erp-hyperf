<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\ItemModel;
use App\Service\Service;

class ItemService extends Service
{
    public function getAllItem(): array
    {
        return $this->all(new ItemModel);
    }
}
