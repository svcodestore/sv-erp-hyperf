<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\RuleItemModel;
use Hyperf\HttpServer\Contract\RequestInterface;

class RuleItemService extends \App\Service\Service
{
    public function getAll(): array
    {
        return $this->all(new RuleItemModel);
    }

    public function saveCrud(RequestInterface $request): bool
    {
        return $this->crudBatch(new RuleItemModel, $request);
    }
}
