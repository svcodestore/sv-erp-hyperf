<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\ItemModel;

class ItemCategoryService
{
    public function getAllItem()
    {
        return ItemModel::query()->get()->toArray();
    }
}
