<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\ItemModel;
use App\Service\Service;

class ItemCategoryService extends Service
{
    public function getAllItemCategory(): array
    {
        return $this->all(new ItemModel);
    }
}
