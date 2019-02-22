<?php

include 'Newegg.php';

$sku = 'ACC-APL-0006';
$qty = '4';

$Newegg = new Newegg();
$result = $Newegg->updateInventory($sku , $qty);

print_r($result);