<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\RuleModel;
use Hyperf\HttpServer\Contract\RequestInterface;

class RuleService extends \App\Service\Service
{
    public function getAll(): array
    {
        return $this->all(new RuleModel);
    }

    public function saveCrud(RequestInterface $request): bool
    {
        return $this->crudBatch(new RuleModel, $request);
    }
}
