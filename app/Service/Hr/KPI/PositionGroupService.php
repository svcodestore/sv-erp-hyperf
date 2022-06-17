<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\PositionGroupModel;
use Hyperf\HttpServer\Contract\RequestInterface;

class PositionGroupService extends \App\Service\Service
{
    public function getAllPositionGroup(): array
    {
        return $this->all(new PositionGroupModel);
    }

    public function saveCrudPositionGroup(RequestInterface $request): bool
    {
        return $this->crudBatch(new PositionGroupModel, $request);
    }
}
