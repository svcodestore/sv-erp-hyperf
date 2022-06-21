<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\ItemCategoryModel;
use App\Service\Service;
use Hyperf\HttpServer\Contract\RequestInterface;

class ItemCategoryService extends Service
{
    public function getAllItemCategory(): array
    {
        return $this->all(new ItemCategoryModel);
    }

    public function saveCrudItemCategory(RequestInterface $request): bool
    {
        return $this->crudBatch(new ItemCategoryModel, $request);
    }
}
