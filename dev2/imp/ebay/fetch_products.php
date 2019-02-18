<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

date_default_timezone_set("America/Los_Angeles");

include_once("../config.php");

include_once("session.php");
include_once("eb_config.php");
include_once("ebAPI.php");

$ebAPI = new ebAPI();

$token = $db->func_query_first_cell("Select config_value from configuration where config_key = 'USER_TOKEN' ");
if(!$token){
    exit;
}

$result = $ebAPI->getMyeBaySelling($token);

print_r($result);

echo "success";