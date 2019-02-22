<?php
ini_set("memory_limit",-1);
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';


$filename = 'duplicate_account.csv';
$fp = fopen($filename, "w");
$headers = array("Firstnam","Lastname","Email","Company", "Address 1","Address 2","City","State","Zip");
fputcsv($fp, $headers,',');

$rows = $db->func_query("select *,(select name from oc_zone where oc_zone.zone_id=inv_customers.zone_id) as state_name from inv_customers where temp_bit2=1 and parent_id=0");


foreach($rows as $row)
{
	$subs = $db->func_query("SELECT *,(select name from oc_zone where oc_zone.zone_id=inv_customers.zone_id) as state_name FROM inv_customers WHERE parent_id='".$row['id']."'");

	if($subs)
	{

	$rowData = array($row['firstname'],$row['lastname'],$row['email'],$row['company'],$row['address1'],$row['address2'],$row['city'],$row['state_name'],$row['zip'],'X');
		fputcsv($fp, $rowData,',');

	}

	foreach($subs as $sub)
	{
		$rowData = array($sub['firstname'],$sub['lastname'],$sub['email'],$sub['company'],$sub['address1'],$sub['address2'],$sub['city'],$sub['state_name'],$sub['zip'],'-');
		fputcsv($fp, $rowData,',');		
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