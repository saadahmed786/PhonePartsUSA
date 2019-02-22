<?php
ini_set("memory_limit",-1);
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$filename = 'vouchers.csv';
$month = date('m');
$year = date('Y');
if(isset($_GET['month']))
{
	$month = (int)$_GET['month'];
}
if(isset($_GET['year']))
{
	$year = (int)$_GET['year'];
}
$rows = $db->func_query("SELECT a.to_email,a.date_added,a.status, a.code,a.voucher_id,a.amount,b.order_id,b.rma_number,b.is_lbb,b.is_rma,a.user_id from oc_voucher a LEFT JOIN inv_voucher_details b ON (a.voucher_id=b.voucher_id) where month(a.date_added)='$month' and year(a.date_added)='$year'   order by a.voucher_id desc");
$fp = fopen($filename, "w");
$headers = array("Date Issued","Date QC", "Voucher #","To Email", "RMA #","LBB #","Is Manual","Amount","Balance","Status","Created By"
	);

fputcsv($fp, $headers,',');
foreach($rows as $row)
{
	$code = $row['code'];
	$voucher_id = $row['voucher_id'];
	$order_id = $row['order_id'];
	$rma = $row['rma_number'];
	$is_lbb = $row['is_lbb'];
	$is_rma = $row['is_rma'];
	$status = 1;

	if($is_lbb==1)
	{
		$detail = $db->func_query_first("SELECT date_qc FROM oc_buyback where shipment_number='".$order_id."'");
	
	}
	else
	{
		$detail = $db->func_query_first("SELECT date_qc FROM inv_returns where rma_number='".$rma."'");	
		// $date_qc  = $detail['date_qc'];
	}

	$is_manual = 'No';
	if($order_id==NULL and $rma==NULL)
	{
		$is_manual = 'Yes';
	}

	if($row['status']==1)
	{
		$status = 'Enabled';
	}
	else
	{
		$status = 'Disabled';
	}

	if($is_rma)
	{
		$order_id='';
	}


	$date_qc  = $detail['date_qc'];

	 $balance = ((float) $row['amount']) + ((float) $db->func_query_first_cell("SELECT SUM(amount) FROM oc_voucher_history WHERE voucher_id='".$row['voucher_id']."'"));
	 $user_name = '';
	 if($row['user_id'])
	 {


	 $user_name = get_username($row['user_id'],true);
	 if(!$user_name)
	 {
	 	$user_name =get_username($row['user_id'],false); 
	 }
	}
	 $rowData = array($row['date_added'],$date_qc,$code,$row['to_email'],$rma,$order_id,$is_manual,$row['amount'],$balance,$status,$user_name);

fputcsv($fp, $rowData,',');
}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);

?>