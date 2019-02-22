<?php

require_once("applicationTop.php");

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");

$saveOrderUrl = $local_path .'/fishbowl/saveOrder.php';
file_get_contents($saveOrderUrl);


$voidOrderUrl = $local_path .'/fishbowl/voidOrder.php';
file_get_contents($voidOrderUrl);

?>