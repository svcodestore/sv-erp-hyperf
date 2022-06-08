<?php

declare(strict_types=1);

namespace App\Service\Bs;

use App\Util\ArrayUtil;
use App\Util\DbUtil;

class OrderService
{
    public function getAllOrder(string $KhPONo, string $sp_No, string $khNo, string $company): array
    {
        $db_name = 'DGSV';
        if ($company == 2) {
            $db_name = 'JStw';
        }

        $db = DbUtil::pdosqlsrv([
            'dsn' => 'sqlsrv:server=192.168.123.245,1433;Database=' . $db_name,
        ]);

        $cond = "";
        $KhPONo && ($cond .= "AND smSOA.KhPONo = '$KhPONo'");
        $sp_No && ($cond .= "AND erpSp.sp_No LIKE '$sp_No%'");
        $khNo && ($cond .= "AND smSOBPlus.smSOBPlusmyField12 LIKE '$khNo%'");

        $sql = "SELECT  SUBSTRING(smSOBPlus.smSOBPlusmyField12, 0,  
                                case WHEN CHARINDEX('-', smSOBPlus.smSOBPlusmyField12) > 0 
                                then CHARINDEX('-', smSOBPlus.smSOBPlusmyField12) 
                                ELSE LEN(smSOBPlus.smSOBPlusmyField12)+1 end ) AS smSOBPlusmyField12,
                        smOType.SC_Name,
                        smSOA.KhPONo,
                        smSOA.Bt_Date                                                                            AS dingDanShiJian,
                        smSOA.OrdA_ID,
                        smSOB.OrdB_ID,
                        smSOB.P_Qty                                                                              AS dingDanShuLiang,
                        smSOB.Due_Date                                                                           AS jiHuaJiaoQi,
                        SUBSTRING(erpSp.sp_No, 0, 7)                                                             AS cunHuoBianHao,
                        smSOQuan.Ship_Qty                                                                        AS leiJiChuHuo
                FROM    smSOBPlus,
                        smSOQuan,
                        smOType,
                        smSOA,
                        smSOB,
                        erpSp
                WHERE   smSOBPlus.OrdB_ID = smSOB.OrdB_ID
                    AND smSOQuan.OrdB_ID = smSOB.OrdB_ID
                    AND smOType.SC_ID = smSOA.SC_ID
                    AND smSOA.OrdA_ID = smSOB.OrdA_ID
                    AND erpSp.SP_ID = smSOB.SP_ID
                    {$cond}
                ORDER BY leiJiChuHuo,dingDanShiJian DESC;
        ";

        $statement = $db->query($sql);
        if (!$statement) {
            return [];
        }
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $keys = ['smSOBPlusmyField12', 'SC_Name', 'KhPONo', 'cunHuoBianHao'];
        $arr = ArrayUtil::array_unique_ext($result, SORT_STRING, $keys);

        foreach ($arr as $k => $item) {
            foreach ($result as $record) {
                $flag = true;
                foreach ($keys as $key) {
                    if ($item[$key] != $record[$key]) {
                        $flag = false;
                    }
                }
                if ($flag) {
                    isset($arr[$k]['dingDanShiJian']) || ($arr[$k]['dingDanShiJian'] = $record['dingDanShiJian']);
                    isset($arr[$k]['OrdA_ID']) || ($arr[$k]['OrdA_ID'] = $record['OrdA_ID']);
                    isset($arr[$k]['OrdB_ID']) || ($arr[$k]['OrdB_ID'] = []);
                    isset($arr[$k]['dingDanShuLiang']) || ($arr[$k]['dingDanShuLiang'] = 0);
                    isset($arr[$k]['jiHuaJiaoQi']) || ($arr[$k]['jiHuaJiaoQi'] = $record['jiHuaJiaoQi']);
                    isset($arr[$k]['leiJiChuHuo']) || ($arr[$k]['leiJiChuHuo'] = 0);

                    $arr[$k]['OrdB_ID'][] = $record['OrdB_ID'];
                    $arr[$k]['dingDanShuLiang'] += $record['dingDanShuLiang'];
                    $arr[$k]['leiJiChuHuo'] += $record['leiJiChuHuo'];
                }
            }
        }

        return $arr;
    }

    public function getOrderDetails($OrdBIDs, string $KhPONo, string $sp_No, string $khNo, string $company): array
    {
        $db_name = 'DGSV';
        if ($company == 2) {
            $db_name = 'JStw';
        }

        $db = DbUtil::pdosqlsrv([
            'dsn' => 'sqlsrv:server=192.168.123.245,1433;Database=' . $db_name,
        ]);

        $cond = "";
        $KhPONo && ($cond .= "AND smSOA.KhPONo = '$KhPONo'");
        $sp_No && ($cond .= "AND erpSp.sp_No LIKE '$sp_No%'");
        $khNo && ($cond .= "AND smSOBPlus.smSOBPlusmyField12 LIKE '$khNo%'");
        if (gettype($OrdBIDs) == 'array') {
            $OrdB_ID = implode(',', array_map(fn ($e) => "'{$e}'", $OrdBIDs));
        }
        $sql = "SELECT danCiChuHuoShiJian, danCiChuHuo, smSOBPlusmyField12, SC_Name, KhPONo, Sp_No
                FROM (SELECT btBook.OrdB_ID,
                            smShipmentA.Bt_Date AS danCiChuHuoShiJian,
                            btBook.P_Qty        AS danCiChuHuo
                    FROM smShipmentA,
                        btBook
                    WHERE smShipmentA.Bt_ID = btBook.Bt_ID
                        AND btBook.BT_CODE = 'smShip'
                        AND btBook.OrdB_ID IN ({$OrdBIDs}) ) AS a
                        LEFT JOIN
                    (SELECT smSOB.OrdB_ID,
                            smSOBPlus.smSOBPlusmyField12,
                            smOType.SC_Name,
                            smSOA.KhPONo,
                            erpSp.sp_No
                    FROM smSOBPlus,
                        smSOQuan,
                        smOType,
                        smSOA,
                        smSOB,
                        erpSp
                    WHERE smSOBPlus.OrdB_ID = smSOB.OrdB_ID
                        AND smSOQuan.OrdB_ID = smSOB.OrdB_ID
                        AND smOType.SC_ID = smSOA.SC_ID
                        AND smSOA.OrdA_ID = smSOB.OrdA_ID
                        AND erpSp.SP_ID = smSOB.SP_ID) AS b ON a.OrdB_ID = b.OrdB_ID
                    ORDER BY danCiChuHuoShiJian,smSOBPlusmyField12";

        $statement = $db->query($sql);
        if (!$statement) {
            return [];
        }
        $orderInfo = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $orderInfo;
    }
}
