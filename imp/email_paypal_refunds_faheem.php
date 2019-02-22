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

$items = $db->func_query("SELECT r.rma_number,ri.* FROM `inv_return_items` ri Inner Join inv_returns r on (ri.return_id = r.id) WHERE ri.decision = 'Issue Refund'  AND ri.item_condition LIKE '%Item Issue%' AND DATE(r.date_completed) = DATE('".$date."') order by r.date_completed desc");

$headers = array("Refund Date","RMA Number","Vendor","SKU","Title","Amount");
$filename = "refund_reports/VendorRefundReport-".$date.".csv";
$file = fopen($filename,"w");
fputcsv($file , $headers,',');

foreach ($items as $item) {
	if (!$item['rtv_vendor_id']) {
		$item['rtv_vendor_id'] = $db->func_query_first_cell("SELECT vendor FROM inv_product_vendors WHERE product_sku='".$item['sku']."' limit 1");
	}
	$rowData = array($date,$item['rma_number'],get_username($item['rtv_vendor_id']),$item['sku'],$item['title'],$item['price']);
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
$mail->Subject = ('Vendor oriented Refunds Report - PhonePartsUSA');
$mail->Body = 'Vendor wise Refunds generated on '.$date;
$mail->IsHTML(true);
$mail->addAttachment($filename);
$sendm1 = $mail->send();

echo "Success";