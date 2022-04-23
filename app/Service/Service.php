<?php

namespace App\Service;

use App\Model\Model;

class Service implements IService
{

    public function all(Model $m, array $cond = [], array $cols = ['*']): array
    {
        $statement = $m::query();
        if (empty($cond)) return $statement->get($cols)->toArray();
        return $statement->where($cond)->get($cols)->toArray();
    }
}