<?php

declare(strict_types=1);

namespace App\Controller;

use App\Grpc\GrpcController;
use App\Util\DbfUtil;

class IndexController extends AbstractController
{
    public function index()
    {
        return $this->responseOk(json_decode('[{"id":10001,"name":"Test1","role":"Develop","sex":"Man","age":28,"address":"test abc"},{"id":10002,"name":"Test2","role":"Test","sex":"Women","age":22,"address":"Guangzhou"},{"id":10003,"name":"Test3","role":"PM","sex":"Man","age":32,"address":"Shanghai"},{"id":10004,"name":"Test4","role":"Designer","sex":"Women","age":24,"address":"Shanghai"}]', true));
    }
    public function sayHello()
    {
        $path = '/var/www/';
        $dbfReader = new DbfUtil($path . 'prdmodel.DBF');
        $info = [];
        while(($record = $dbfReader->GetNextRecord(true))) {
            foreach (array_keys($record) as $k) {
                $record[$k] = mb_convert_encoding($record[$k], 'UTF-8', 'GBK');
            }

            $info[] = ($record);
            break;
        }
        
        return $info;
    }
}
