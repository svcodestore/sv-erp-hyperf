<?php

namespace App\Service\Prod;

use App\Model\Prod\Schedule\PhaseModel;
use App\Service\Service;
use Hyperf\HttpServer\Contract\RequestInterface;

class PhaseService extends Service
{

    public function getPhaseByCode(string $codes): array
    {
        $columns = [
            'id',
            'code',
            'name',
            'code_id',
            'cost_time',
            'is_master',
            'ahead_time',
            'dead_time',
            'out_time',
            'worker_num',
        ];

        return PhaseModel::query()->whereIn('code', explode(',', $codes))->get($columns)->toArray();
    }

    public function getPhases(): array
    {
        $columns = [
            'id',
            'code',
            'name',
            'code_id',
            'cost_time',
            'is_master',
            'ahead_time',
            'dead_time',
            'out_time',
            'worker_num',
        ];
        return $this->all(new PhaseModel, [], $columns);
    }

    public function saveCrudPhases(RequestInterface $request): bool
    {
        return $this->crudBatch(new PhaseModel, $request);
    }
}