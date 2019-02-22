<?php
ini_set("memory_limit",-1);
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';


$filename = 'duplicate_customers.csv';
$fp = fopen($filename, "w");
$headers = array("Category","Customer ID","Address ID","First Name", "Last Name","Email", "Address 1","Address 2","City",'State','Zip');
fputcsv($fp, $headers,',');

$rows = $db->func_query("select a.customer_id,b.address_id,a.email from oc_customer a left join oc_address b on(a.customer_id=b.customer_id)  where b.address_id is not null and a.customer_id>82 and a.status=1 group by a.email order by a.customer_id limit 2500");

$customer_ids = array();

foreach($rows as $row)
{
	// echo "SELECT * FROM oc_address where customer_id='".$row['customer_id']."' ".($customer_ids?" and address_id not in (".implode(",", $customer_ids).") ":"";exit;
	$addresses  = $db->func_query("SELECT * FROM oc_address where customer_id='".$row['customer_id']."' ".($customer_ids?" and address_id not in (".implode(",", array_unique($customer_ids)).") ":"")."GROUP BY address_1");
	$i=0;	
	foreach($addresses as $address)
	{
		$check = $db->func_query("SELECT b.*,(select a.email from oc_customer a where a.customer_id=b.customer_id) as email FROM oc_address b where b.customer_id>82 and   b.customer_id<>'".$address['customer_id']."' and TRIM(LOWER(CONCAT(b.address_1,' ',b.address_2))) like  '".trim(strtolower($db->func_escape_string($address['address_1']).' '.$db->func_escape_string($address['address_2'])))."%' and trim(lower(b.postcode)) like '".trim(strtolower($address['postcode']))."%'".($customer_ids?" and b.address_id not in (".implode(",", array_unique($customer_ids)).") ":""));

		if($check)
		{
				$rowData = array('Main',$address['customer_id'],$address['address_id'],$address['firstname'],$address['lastname'],$row['email'],$address['address_1'],$address['address_2'],$address['city'],$address['zone_id'],$address['postcode']);
	fputcsv($fp, $rowData,',');
	$customer_ids[]= $address['address_id'];
	$i++;
		}
		foreach($check as $c)
		{
			$rowData = array('Sub',$c['customer_id'],$c['address_id'],$c['firstname'],$c['lastname'],$c['email'],$c['address_1'],$c['address_2'],$c['city'],$c['zone_id'],$c['postcode']);
	fputcsv($fp, $rowData,',');	
	$customer_ids[]= $address['address_id'];
		}

		
	}


}



// $rows = $db->func_query("select email,address1,address2,zip,count(*) as c from inv_customers where email not like '%@marketplace%' and address1<>'' and zip<>'' group by address1,zip having c>1  ");

// foreach($rows as $row )
// {
	
// 	$duplicates = $db->func_query("SELECT firstname,lastname,email,address1,address2,city,state,zip FROM inv_customers WHERE TRIM(LOWER(CONCAT(address1,' ',address2))) like  '".trim(strtolower($db->func_escape_string($row['address1']).' '.$db->func_escape_string($row['address2'])))."%' and trim(lower(zip)) like '".trim(strtolower($row['zip']))."%' and email not like '%@marketplace%'");
	
// 	foreach($duplicates as $dup)
// 	{

// 	$rowData = array($dup['firstname'],$dup['lastname'],$dup['email'],$dup['address1'],$dup['address2'],$dup['city'],$dup['state'],$dup['zip']);
// 	fputcsv($fp, $rowData,',');
// 	}

	

	
// }
fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);


?>