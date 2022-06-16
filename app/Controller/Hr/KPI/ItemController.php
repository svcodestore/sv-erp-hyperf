<?php

declare(strict_types=1);

namespace App\Controller\Hr\KPI;

use App\Controller\AbstractController;
use App\Service\Hr\KPI\ItemService;
use Hyperf\Di\Annotation\Inject;

class ItemController extends AbstractController
{
    /**
     * @Inject
     * @var ItemService
     */
    private $itemService;

    public function getAllItem(): array
    {
        return $this->responseOk($this->itemService->getAllItem());
    }

    public function saveCurdItem()
    {
        $isOk = $this->itemService->saveCrudItem($this->request);

        if ($isOk) {
            return $this->responseOk();
        }
        return $this->responseDetail();
    }
}
