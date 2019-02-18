<?php
include_once 'config.php';
include_once 'inc/functions.php';

$_query = 'SELECT  `isi`.`product_sku`, `is`.`vendor` FROM `inv_shipment_items` `isi`, `inv_shipments` `is`  WHERE `is`.`id` = `isi`.`shipment_id`  GROUP BY `isi`.`product_sku`';

$vendors = $db->func_query($_query);

if ($vendors) {
	foreach ($vendors as $key => $vendor) {
		if ($vendor['product_sku'] && !$db->func_query_first_cell('SELECT `product_sku` FROM `inv_product_vendors` WHERE `product_sku` = "'. $vendor['product_sku'] .'" AND `vendor` = "'. $vendor['vendor'] .'"')) {
			$db->func_array2insert("inv_product_vendors", $vendor);
		}
	}
	echo 'done';
}
?> 