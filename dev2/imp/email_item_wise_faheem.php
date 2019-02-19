<?php
include 'config.php';
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
include_once 'inc/functions.php';
ini_set('memory_limit','2048M');
ini_set('max_execution_time', 500); //300 seconds = 5 minutes
if ($_GET['date']) {
 $date = $_GET['date'];
} else {	
 $date = date('Y-m-d');
}

$result=array();

$vouchers = $db->func_query("SELECT vd.voucher_id,vd.rma_number,vd.item_detail,v.code FROM `oc_voucher` v Inner JOIN `inv_voucher_details` vd on (v.voucher_id = vd.voucher_id) WHERE v.status = '1' AND DATE(v.date_added) = DATE('".$date."') AND vd.is_rma=1 ORDER BY v.`date_added` desc");

$refund_items = $db->func_query("SELECT r.rma_number,ri.* FROM `inv_return_items` ri Inner Join inv_returns r on (ri.return_id = r.id) WHERE ri.decision = 'Issue Refund'  AND ri.item_condition LIKE '%Item Issue%' AND DATE(r.date_completed) = DATE('".$date."') order by r.date_completed desc");

$headers = array("Date Issued","SKU","Title","Store Credit Issued","Refund Issued");
$filename = "refund_reports/VendorRefundReport-".$date.".csv";
$file = fopen($filename,"w");
fputcsv($file , $headers,',');

//Iterate Store Credit Totals per item
foreach ($vouchers as $voucher ) {
		$return_id = $db->func_query_first_cell("select id from inv_returns where rma_number = '".$voucher['rma_number']."'");
		$items = $db->func_query("select * from inv_return_items where return_id = '".$return_id."' AND item_condition LIKE '%Item Issue%'");
		foreach ($items as $item) {
			$decisionCheckQuery = $db->func_query_first("SELECT * FROM inv_return_decision WHERE return_item_id='" . $item['id'] . "'");
			if ($decisionCheckQuery['action'] == 'Issue Credit') {
				if (!$item['rtv_vendor_id']) {
					$item['rtv_vendor_id'] = $db->func_query_first_cell("SELECT vendor FROM inv_product_vendors WHERE product_sku='".$item['sku']."' limit 1");
				}
				
				$result[$item['sku']]['store_credit'] = $result[$item['sku']]['store_credit'] + $decisionCheckQuery['price'];
				
			}
		}
		
	
}
//Iterate Refund Totals per item
foreach ($refund_items as $item) {
	$result[$item['sku']]['refund'] = $result[$item['sku']]['refund'] + $item['price'];
}

//Saving result

foreach ($result as $sku => $item) {
	
	$rowData = array($date,$sku,getItemName($sku),$item['store_credit'],$item['refund']);
	fputcsv($file , $rowData,',');

}



fclose($file);



$mail = new PHPMailer();
$mail->IsSMTP();
$mail->CharSet = 'UTF-8';
$mail->Host = MAIL_HOST; // SMTP server example
$mail->SMTPDebug = 0;                     // enables SMTP debug information (for testing)
$mail->SMTPAuth = true;                  // enable SMTP authentication
$mail->Port = 25;                    // set the SMTP port for the GMAIL server
$mail->Username = MAIL_USER; // SMTP account username example
$mail->Password = MAIL_PASSWORD;        // SMTP account password example
$mail->SetFrom(MAIL_USER, 'PhonePartsUSA');
$mail->addAddress('faheemmalik6280@gmail.com', 'Faheem Malik');
$mail->Subject = ('Item Wise Refunds & Store Credits Report - PhonePartsUSA');
$mail->Body = 'Item Wise Refunds & Store Credits generated on '.$date;
$mail->IsHTML(true);
$mail->addAttachment($filename);
$sendm1 = $mail->send();

echo "Success";