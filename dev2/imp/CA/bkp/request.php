<?php
set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("ca_keys.php");
include_once 'ChannelAdvisor.php';

global $db,$DEV_KEY,$Password,$AccountID;

$ChannelAdvisor = new ChannelAdvisor($DEV_KEY , $Password , $AccountID);
$result = $ChannelAdvisor->requestPermission('12014844');

print_r($result);