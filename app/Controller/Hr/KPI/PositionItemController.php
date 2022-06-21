<?php

declare(strict_types=1);

namespace App\Controller\Hr\KPI;

use App\Service\Hr\KPI\PositionItemService;
use Hyperf\Di\Annotation\Inject;

class PositionItemController extends \App\Controller\AbstractController
{
    /**
     * @Inject
     * @var PositionItemService
     */
    private $positionItemService;

    public function getAllPositionItem(): array
    {
        return $this->responseOk($this->positionItemService->getAllPositionItem());
    }

    public function saveCrudPositionItem()
    {
        $isOk = $this->positionItemService->saveCrudPositionItem($this->request);

        if ($isOk) {
            return $this->responseOk();
        }
        return $this->responseDetail();
    }
}
