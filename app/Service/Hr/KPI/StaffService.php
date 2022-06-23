<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\StaffModel;
use Hyperf\HttpServer\Contract\RequestInterface;

class StaffService extends \App\Service\Service
{
    public function getAllStaff(): array
    {
        return $this->all(new StaffModel);
    }

    public function saveCrudStaff(RequestInterface $request): bool
    {
        return $this->crudBatch(new StaffModel, $request);
    }
}
