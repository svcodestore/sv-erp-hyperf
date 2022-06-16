<?php

namespace App\Service;

use App\Model\Model;
use App\Util\StrUtil;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Contract\RequestInterface;

class Service implements IService
{

    public function all(Model $m, array $cond = [], array $cols = ['*']): array
    {
        $statement = $m::query();
        if (empty($cond)) return $statement->get($cols)->toArray();
        return $statement->where($cond)->get($cols)->toArray();
    }

    public function crudBatch(Model $m, RequestInterface $request): bool
    {
        $create = StrUtil::withSnakeCase($request->input('A', []));
        $delete = StrUtil::withSnakeCase($request->input('D', []));
        $update = StrUtil::withSnakeCase($request->input('U', []));
        if (empty($create) && empty($delete) && empty($update)) return true;

        Db::beginTransaction();

        if (!empty($delete)) {
            $m::destroy($delete);
        }

        if (!empty($update)) {
            foreach ($update as $updateItem) {
                $m::query()->where('id', $updateItem['id'])->update($updateItem);
            }
        }

        if (!empty($create)) {
            foreach ($create as $createItem) {
                foreach (array_keys($createItem) as $key) {
                    $m->$key = $createItem[$key];
                }
                $m->save();
            }
        }
    }
}
