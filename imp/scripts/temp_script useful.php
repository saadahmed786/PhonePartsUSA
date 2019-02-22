<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
$rows = $db->func_query("SELECT id, TRIM( email ) AS email
FROM  `inv_customers` 
WHERE (
no_of_orders <=2
AND (
email LIKE  '%medic%'
OR email LIKE  '%phone%'
OR email LIKE  '%tech%'
OR email LIKE  '%computer%'
OR email LIKE  '%laptop%'
OR email LIKE  '%shop%'
OR email LIKE  '%store%'
OR email LIKE  '%paypal%'
)
)
AND address1 =  ''
");
foreach($rows as $row)
{
	$order_id = $db->func_query_first_cell("SELECT order_id FROM inv_orders WHERE trim(email)='".$row['email']."' limit 1");
	$zip = $db->func_query_first_cell("SELECT address1 FROM inv_orders_details WHERE order_id='".$order_id."'");
	//$address = $db->func_query_first_cell("SELECT b.address1 FROM inv_orders_details b,inv_orders a where a.order_id=b.order_id and trim(email)='".$row['email']."'");
	$db->db_exec("UPDATE inv_customers SET address1='".$db->func_escape_string($zip)."' where id='".(int)$row['id']."'");
	//echo $row['id']."<br>";

}
echo 1;
?>