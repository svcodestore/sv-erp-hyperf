<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\PositionItemModel;
use Hyperf\HttpServer\Contract\RequestInterface;

class PositionItemService extends \App\Service\Service
{
    public function getAllPositionItem(): array
    {
        return $this->all(new PositionItemModel);
    }

    public function saveCrudPositionItem(RequestInterface $request): bool
    {
        return $this->crudBatch(new PositionItemModel, $request);
    }
}
