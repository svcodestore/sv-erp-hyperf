<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\RankTitleModel;
use App\Service\Service;
use Hyperf\HttpServer\Contract\RequestInterface;

class RankTitleService extends Service
{
    public function getAllRankTitle(): array
    {
        return $this->all(new RankTitleModel);
    }

    public function saveCrudRankTitle(RequestInterface $request): bool
    {
        return $this->crudBatch(new RankTitleModel, $request);
    }
}
