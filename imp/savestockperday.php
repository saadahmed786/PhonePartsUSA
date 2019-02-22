<?php
require_once 'config.php';
if ($_GET['reset'] == 1) {
	$db->db_exec("UPDATE oc_product set stockrecord = 0");
	exit;
}
echo date('Y-m-d H:i:s');
echo '<br>';
$products = $db->func_query("SELECT model, quantity from oc_product where stockrecord = 0 limit 1000");
foreach ($products as $product) {
	 $array = array(
	 	'sku' => $product['model'],
	 	'qty' => $product['quantity'],
	 	'date' => date('Y-m-d'),
	 );
	 $db->func_array2insert ( 'inv_product_stock_record', $array );
	 $db->db_exec("UPDATE oc_product set stockrecord = 1 WHERE model = '" . $product['model'] . "'");
}
echo date('Y-m-d H:i:s');
?>