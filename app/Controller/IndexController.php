<?php

declare(strict_types=1);

namespace App\Controller;

use App\Grpc\GrpcController;
use App\Util\DbfUtil;

class IndexController extends AbstractController
{
    public function index()
    {
        $path ="/mnt/win252/database/";
        $dbfReader = new DbfUtil($path . 'prdmodel.DBF');
        $info = [];
        while(($record = $dbfReader->GetNextRecord(true))) {
            foreach (array_keys($record) as $k) {
                $record[$k] = mb_convert_encoding($record[$k], 'UTF-8', 'GBK');
            }

            $info[] = $record;
            break;
        }
        
        return $info;
    }
}
