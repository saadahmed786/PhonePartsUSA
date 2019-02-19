<?php

date_default_timezone_set('America/Los_Angeles');

require_once("applicationTop.php");

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("c:/xampp/htdocs/InventoryManager/config.php");

$time  = strtotime(date('H:i'));
$time1 = strtotime(date('09:00'));
$time2 = strtotime(date('17:00'));

if($time >= $time1 && $time <= $time2){
    echo "Running";
    
    $updateQtyUrl = $local_path .'/fishbowl/updateAllQty.php?all=1';
    
    echo file_get_contents($updateQtyUrl);
}
else{
    echo "Stopped";
    
    exit;
}

?>