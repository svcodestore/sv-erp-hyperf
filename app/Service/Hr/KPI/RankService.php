<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\RankModel;
use App\Service\Service;
use Hyperf\HttpServer\Contract\RequestInterface;

class RankService extends Service
{
    public function getAllRank(): array
    {
        return $this->all(new RankModel);
    }

    public function saveCrudRank(RequestInterface $request): bool
    {
        return $this->crudBatch(new RankModel, $request);
    }
}
