<?php

declare(strict_types=1);

namespace App\Util;

class DbUtil
{
    public static function pdosqlsrv(array $options = null): \PDO
    {
        $dbinfo = array_merge([
            'type'                      => 'Sqlsrv',
            'username'                  => 'sa',
            'password'                  => 'Sql2012',
            'dsn'                    => env('mssqlDsn', 'sqlsrv:server=192.168.123.245,1433;Database=databasesdwx'),
        ], $options ?? []);;

        $dbh = new \PDO($dbinfo['dsn'], $dbinfo['username'], $dbinfo['password']);

        return $dbh;
    }
}
