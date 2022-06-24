<?php
$pdo = new PDO('mysql:host=192.168.123.51;dbname=starvc_homedb;charset=utf8mb4', 'root', 'root');
$flag = true;
$pdo2 = new PDO('mysql:host=192.168.123.83;dbname=sv_erp;charset=utf8mb4', 'root', 'root');
$stat = $pdo->prepare("select * from  prodlib_prdschd_initpo");
$stat->execute();
$data = $stat->fetchAll(PDO::FETCH_ASSOC);
$pdo2->prepare("truncate table prod_schedule_po")->execute();
foreach ($data as $value) {
  $sql = "insert into prod_schedule_po(workshop, workshop_name, customer_no, customer_po_no, item_code, item_qty, po_month, po_year, is_dirty) value('{$value['ppi_workshop']}', '{$value['ppi_workshop_name']}', '{$value['ppi_customer_no']}', '{$value['ppi_customer_pono']}', '{$value['ppi_prd_item']}', '{$value['ppi_expected_qty']}', '{$value['ppi_po_month']}', '{$value['ppi_po_year']}', 1)";
  $sta = $pdo2->prepare($sql);
  $flag = $flag && $sta->execute();
}

$stat = $pdo->prepare("select * from  prodlibmap_prdschd_initpdo2phs");
$stat->execute();
$data = $stat->fetchAll(PDO::FETCH_ASSOC);
$pdo2->prepare("truncate table prod_schedule_phase")->execute();
foreach ($data as $value) {
  $sql = "insert into prod_schedule_phase(code, code_id, name, cost_time, is_master, ahead_time, dead_time, out_time, worker_num, is_dirty) value('{$value['map_ppi_prd_item']}', '{$value['map_ppi_phsid']}', '{$value['map_ppi_phs']}', '{$value['map_ppi_cost_time']}', '{$value['map_ppi_ismaster']}', '{$value['map_ppi_aheadtime']}', '{$value['map_ppi_deadtime']}', '{$value['map_ppi_outime']}', '{$value['map_ppi_worker_num']}', 1)";
  $sta = $pdo2->prepare($sql);
  $flag = $flag && $sta->execute();
}

$stat = $pdo->prepare("select * from  prodlib_prdschd_initcald");
$stat->execute();
$data = $stat->fetchAll(PDO::FETCH_ASSOC);
$pdo2->prepare("truncate table prod_schedule_calendar")->execute();
foreach ($data as $value) {
  $profile = json_decode($value['ppi_cald_profile'], true);
  $profile['ppi_work_shifts'] = json_decode($profile['ppi_work_shifts'], true);
  $profile = json_encode($profile, JSON_UNESCAPED_UNICODE);

  $sql = "insert into prod_schedule_calendar(date, year, month, day, is_rest, profile) value('{$value['ppi_cald_date']}', '{$value['ppi_cald_year']}', '{$value['ppi_cald_month']}', '{$value['ppi_cald_day']}', '{$value['ppi_cald_is_rest']}', '"
    . $profile
    . "')";
  $sta = $pdo2->prepare($sql);
  $flag = $flag && $sta->execute();
}
