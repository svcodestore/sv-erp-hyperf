<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\StaffScoreModel;
use Hyperf\HttpServer\Contract\RequestInterface;

class StaffScoreService extends \App\Service\Service
{
    public function getAll(): array
    {
        return $this->all(new StaffScoreModel);
    }

    public function saveCrud(RequestInterface $request): bool
    {
        return $this->crudBatch(new StaffScoreModel, $request);
    }
}
