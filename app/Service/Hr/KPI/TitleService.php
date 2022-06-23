<?php

declare(strict_types=1);

namespace App\Service\Hr\KPI;

use App\Model\Hr\KPI\TitleModel;
use Hyperf\HttpServer\Contract\RequestInterface;

class TitleService extends \App\Service\Service
{
    public function getAllTitle(): array
    {
        return $this->all(new TitleModel);
    }

    public function saveCrudTitle(RequestInterface $request): bool
    {
        return $this->crudBatch(new TitleModel, $request);
    }
}
