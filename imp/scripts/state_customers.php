<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
$filename = 'state_customers.csv';
$fp = fopen($filename, "w");
$headers = array("Assigned Agent", "Customer Email","Name", "Address","City","# of Orders","Order Total","Last Order","Customer Tier","Tax Exempt?"
	);
fputcsv($fp, $headers,',');

$rows = $db->func_query("SELECT * FROM inv_customers WHERE trim(lower(state))='nevada'");
foreach($rows as $row)
{
	$agent= $db->func_query_first_cell("SELECT name FROM inv_users WHERE id='".(int)$row['user_id']."'");
	if(!$agent){
		$agent = 'N/A';
	}
	$tax_exempt = ($row['dis_tax']?'Yes':'No');

	//$row['total_amount'] = $db->func_query_first_cell("SELECT SUM(order_price) FROM inv_orders WHERE trim(lower(email))='".$row['email']."' and lower(order_status) in ('processed','shipped','unshipped','completed')");
	
		$rowData = array($agent,$row['email'],$row['firstname'].' '.$row['lastname'],$row['address1'],$row['city'],(int)$row['no_of_orders'],(float)$row['total_amount'],$row['last_order'],$row['customer_group'],$tax_exempt);
		
	 fputcsv($fp, $rowData,',');
	// print_r($rowData);exit;
}
fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);
?>