<?php

namespace App\Service\Prod;

use App\Model\Prod\Schedule\PoModel;
use App\Service\Service;

class PoService extends Service
{
    public function getMonthPo(string $workLine, string $year, string $month): array
    {
        $cond = [
            'workshop' => $workLine, 'po_year' => $year, 'po_month' => $month
        ];
        return $this->all(new PoModel, $cond);
    }

    public function deletePoWithId(int $id): bool
    {
        return PoModel::destroy($id) === 1;
    }

    public function addPo(array $add):bool
    {
        return PoModel::insert($add);
    }

    public function updatePoWithId(int $id, array $update): bool
    {
        return PoModel::query()->where('id', $id)->update($update);
    }
}