<?php

declare(strict_types=1);

namespace App\Controller\Hr\KPI;
use App\Service\Hr\KPI\PositionGroupService;
use App\Service\Hr\KPI\TitleCategoryService;
use Hyperf\Di\Annotation\Inject;

class TitleCategoryController extends \App\Controller\AbstractController
{
    /**
     * @Inject
     * @var TitleCategoryService
     */
    private $titleCategoryService;

    public function getAllTitleCategory(): array
    {
        return $this->responseOk($this->titleCategoryService->getAllTitleCategory());
    }
}