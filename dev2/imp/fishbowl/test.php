<?php

require_once("applicationTop.php");

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");

$sku = 'FLX-SAM-631';

$qtyInHand  = $fbapi->getPartQty($sku);
$qtyforSale = $fbapi->getItemQty($sku);

$qtySold = 10;
$qtyAvailable =  $qtyInHand - $qtySold;

$fbapi->updateQty($sku , $qtyAvailable);

$availQty = $qtyforSale - $qtySold;


print $qtyInHand . "--" . $qtyforSale . "--" . $qtyAvailable . "---" . $availQty;