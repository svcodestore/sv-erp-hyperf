<?php

declare(strict_types=1);

namespace App\Service\Prod;

use App\Model\Prod\Schedule\ParamModel;
use App\Service\Service;

class ParamService extends Service
{
    public function getAll(): array
    {
        return $this->all(new ParamModel);
    }

    public function getParamByKey(string $key): array
    {
        return ParamModel::query()->where('key', $key)->first()->toArray();
    }

    public function setParamByKey(string $key, array $update): bool
    {
        return ParamModel::query()->where('key', $key)->update($update) === 1;
    }
}