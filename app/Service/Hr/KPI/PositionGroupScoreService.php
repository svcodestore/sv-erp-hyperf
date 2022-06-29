<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\PositionGroupScoreModel;
use Hyperf\HttpServer\Contract\RequestInterface;

class PositionGroupScoreService extends \App\Service\Service
{
    public function getAll(): array
    {
        return $this->all(new PositionGroupScoreModel);
    }

    public function saveCrud(RequestInterface $request): bool
    {
        return $this->crudBatch(new PositionGroupScoreModel, $request);
    }
}
