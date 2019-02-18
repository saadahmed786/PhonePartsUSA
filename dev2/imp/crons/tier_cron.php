<?php
require_once("../config.php");
require_once("../inc/functions.php");

$skus = $db->func_query("SELECT sku FROM oc_product where status = '1' limit 1000");
foreach ($skus as $sku) {
$sale_60 = $db->func_query_first_cell("SELECT sum(b.product_qty) FROM inv_orders_items b, inv_orders a where a.order_id=b.order_id and b.product_sku='".$sku['sku']."' and lower(a.order_status) not in ('on hold','voided','canceled','cancelled') and a.order_date BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE() ");
	if ($sale_60>10) {
		$db->func_query("update oc_product SET tier = '1' WHERE sku='" . $sku['sku'] . "'");
	} else if($sale_60>0) {
		$db->func_query("update oc_product SET tier = '2' WHERE sku='" . $sku['sku'] . "'");
	} else {
		$db->func_query("update oc_product SET tier = '3' WHERE sku='" . $sku['sku'] . "'");
	}
}
echo "Success";
?>