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

 
$result = array();
$vouchers = $db->func_query("SELECT vd.voucher_id,vd.rma_number,vd.item_detail,v.code FROM `oc_voucher` v Inner JOIN `inv_voucher_details` vd on (v.voucher_id = vd.voucher_id) WHERE v.status = '1' AND DATE(v.date_added) = DATE('".$date."') AND vd.is_rma=1 ORDER BY v.`date_added` desc");
//testObject($vouchers);
$headers = array("Date Added","Voucher Code","Vendor","SKU","Title","Amount");


$filename = "refund_reports/VendorsStoreCreditReport-".$date.".csv";
$file = fopen($filename,"w");
fputcsv($file , $headers,',');
foreach ($vouchers as $voucher ) {

		$return_id = $db->func_query_first_cell("select id from inv_returns where rma_number = '".$voucher['rma_number']."'");
		$items = $db->func_query("select * from inv_return_items where return_id = '".$return_id."' AND item_condition LIKE '%Item Issue%'");
		foreach ($items as $item) {
			
			$decisionCheckQuery = $db->func_query_first("SELECT * FROM inv_return_decision WHERE return_item_id='" . $item['id'] . "'");
			if ($decisionCheckQuery['action'] == 'Issue Credit') {
				if (!$item['rtv_vendor_id']) {
					$item['rtv_vendor_id'] = $db->func_query_first_cell("SELECT vendor FROM inv_product_vendors WHERE product_sku='".$item['sku']."' limit 1");
				}
				//$result[$voucher['code']][get_username($item['rtv_vendor_id'])] = $result[$voucher['code']][get_username($item['rtv_vendor_id'])] + $decisionCheckQuery['price'];

				$rowData = array($date,$voucher['code'],get_username($item['rtv_vendor_id']),$item['sku'],$item['title'],$decisionCheckQuery['price']);
				fputcsv($file , $rowData,',');
			}
		}
		
	
}


/*

	foreach ($result as $voucher_code => $value) {
		
		foreach ($value as $vendor => $amount) {

			$rowData = array($date,$voucher_code,$vendor,$amount);
				fputcsv($file , $rowData,',');

		}
	}
*/


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
$mail->Subject = ('Vendor oriented Store Credits Report - PhonePartsUSA');
$mail->Body = 'Vendor wise Store Credits generated on '.$date;
$mail->IsHTML(true);
$mail->addAttachment($filename);
$sendm1 = $mail->send();

/*if ($refunds) {
	$test_mail = new PHPMailer();
$test_mail->IsSMTP();
$test_mail->CharSet = 'UTF-8';
$test_mail->Host = MAIL_HOST; // SMTP server example
$test_mail->SMTPDebug = 0;                     // enables SMTP debug information (for testing)
$test_mail->SMTPAuth = true;                  // enable SMTP authentication
$test_mail->Port = 25;                    // set the SMTP port for the GMAIL server
$test_mail->Username = MAIL_USER; // SMTP account username example
$test_mail->Password = MAIL_PASSWORD;        // SMTP account password example
$test_mail->SetFrom(MAIL_USER, 'PhonePartsUSA');
$test_mail->addAddress('xaman.riaz@gmail.com', 'Zaman Riaz');
$test_mail->Subject = ('Test Email for Store Credits Report');
$test_mail->Body = 'Test Store Credits Report! ';
$test_mail->IsHTML(true);
$test_mail->addAttachment($filename);
$test = $test_mail->send();
}*/


echo "Success";