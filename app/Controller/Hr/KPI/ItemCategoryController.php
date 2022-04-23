<?php

declare(strict_types=1);

namespace App\Controller\Hr\KPI;

use App\Controller\AbstractController;
use App\Service\Hr\KPI\ItemCategoryService;
use Hyperf\Di\Annotation\Inject;

class ItemCategoryController extends AbstractController
{
    /**
     * @Inject
     * @var ItemCategoryService
     */
    private $itemCategoryService;

    public function getAllItemCategory(): array
    {
        return $this->responseOk($this->itemCategoryService->getAllItemCategory());
    }
}
