<?php

namespace App\Service;

use App\Model\Model;
use App\Util\ArrayUtil;
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

        $isOk = true;
        Db::beginTransaction();

        if (!empty($delete)) {
            $affectedRows = $m::destroy($delete);
            $isOk = $affectedRows === count($delete);
            if (!$isOk) {
                Db::rollBack();
                return false;
            }
        }


        if (!empty($update)) {
            foreach ($update as $updateItem) {
                $affectedRows = $m::query()->where('id', $updateItem['id'])->update($updateItem);

                $isOk = $affectedRows === 1;
            }
            if (!$isOk) {
                Db::rollBack();
                return false;
            }
        }

        if (!empty($create)) {
            $tree = ArrayUtil::toTree($create);
            if (count($create) === count($tree)) {
                foreach ($create as $createItem) {
                    unset($createItem['id']);
                    $isOk =  $isOk && Db::table($m->getTable())->insert($createItem);
                    if (!$isOk) {
                        Db::rollBack();
                        return false;
                    }
                }
            } else {
                $this->insertTree($m, $tree);
            }
        }

        Db::commit();
        return true;
    }

    public function insertTree(Model $m, array $tree, $pid = '0', $pk = 'id', $parentKey = 'pid', $childrenKey = 'children')
    {
        if (empty($tree)) return;

        foreach ($tree as $item) {
            unset($item[$pk]);
            $item[$parentKey] = $pid;
            $children = $item[$childrenKey];
            unset($item[$childrenKey]);
            $id = Db::table($m->getTable())->insertGetId($item);
            if (isset($children) && !empty($children)) {
                $this->insertTree($m, $children, $id);
            }
        }
    }
}
