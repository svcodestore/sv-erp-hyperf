<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\TitleCategoryModel;

class TitleCategoryService extends \App\Service\Service
{
    public function getAllTitleCategory(): array
    {
        return $this->all(new TitleCategoryModel);
    }
}