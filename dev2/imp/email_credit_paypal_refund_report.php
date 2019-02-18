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

$store_credits = $db->func_query("SELECT v.voucher_id as v_id,v.amount,v.date_added,v.code,v.to_email,v.is_manual as manual,v.user_id as user,v.reason_id as manual_reason_id,v.voucher_items_reasons as regular_reasons ,vd.* FROM `oc_voucher` v LEFt JOIN `inv_voucher_details` vd on (v.voucher_id = vd.voucher_id) WHERE v.status = '1' AND DATE(v.date_added) = DATE(( NOW() - INTERVAL 1 DAY ))  ORDER BY v.`date_added` desc");
$refunds = $db->func_query("SELECT * FROM `inv_transactions` WHERE payment_status='Completed' and order_status='Completed' and (receiver_email='paypal@phonepartsusa.com' and amount<0) AND DATE(date_added) = DATE(( NOW() - INTERVAL 1 DAY )) ORDER BY `date_added` desc");

$filename = "refund_reports/StoreCreditPaypalRefund Report-".date("m-d-Y", strtotime(' -1 day')).".csv";
if ($store_credits) {


	$file = fopen($filename,"w");
	fputcsv($file , array('Store Credits Generated'),',');
	$headers = array("Date Added","Sent To","Ref Order/Shipment ID","Ref RMA","Source","User","Voucher Code","Voucher Total","Item Total","Item SKU","Item Name","Reason");
	fputcsv($file , $headers,',');

	$rowData = array();
	foreach($store_credits as $refund){

		$is_lbb = ($refund['is_lbb']);
		$is_manual = ($refund['manual']);
		$is_rma = ($refund['is_rma']);
		$is_order_cancellation = ($refund['is_order_cancellation']);
		$is_pos = ($refund['is_pos']);
		if($is_lbb)
		{
			$source = 'BuyBack';
		}
		else if ($is_manual){
			$source = 'Manual';
		}
		else if($is_rma)
		{
			$source = 'RMA';
		}
		else if($is_order_cancellation)
		{
			$source = 'Cancellation';
		}
		else if($is_pos)
		{
			$source = 'POS';
		} else {
			$source = 'Order';
		}

		if (!$refund['rma_number']) {
			$refund['rma_number'] = 'N/A';
		}
		if (!$refund['order_id']) {
			$refund['order_id'] = 'N/A';
		}
		
		if ($is_manual) {
			$user = get_username($refund['user']);
		}else if ($refund['user_id']) {
			$user = get_username($refund['user_id']);
		} else {
			$user = $db->func_query_first_cell("SELECT username FROM oc_user WHERE user_id='".$refund['oc_user_id']."'");
		}
		if ($user == '' && $refund['user']) {
			$user = get_username($refund['user']);
			if ($source != 'POS') {
				$source = 'Manual';
			}
		}
		/*if ($refund['regular_reasons']) {
			$reason = '*'.str_replace(';', '* *', $refund['regular_reasons']);
		} else if ($refund['manual_reason']) {
			$reason = $db->func_query_first_cell("SELECT reason FROM inv_voucher_reasons WHERE id='".$refund['manual_reason']."'");
		} else {
			$reason = 'No reasons defined';
		}*/

		$voucher_items = $db->func_query("SELECT * FROM inv_voucher_products WHERE voucher_id='".$refund['v_id']."'");
		if ($voucher_items) {

			foreach ($voucher_items as $item) {

				$rowData = array($refund['date_added'],$refund['to_email'],$refund['order_id'],$refund['rma_number'],$source,$user,$refund['code'],$refund['amount'],$item['price'],$item['sku'],getItemName($item['sku']),$item['reason']);
				fputcsv($file , $rowData,',');

			}
			
		} else if ($is_manual)  {
			$reason = $db->func_query_first_cell('SELECT reason FROM inv_voucher_reasons where id = "'.$refund['manual_reason_id'].'"');
			$rowData = array($refund['date_added'],$refund['to_email'],$refund['order_id'],$refund['rma_number'],$source,$user,$refund['code'],$refund['amount'],'','','',$reason);
				fputcsv($file , $rowData,',');

		} else if($source == 'POS'){

			$items = explode('<br>', $refund['item_detail']);
			$i = 0;
			$len = count($items);
			foreach ($items as $item) {
				if ($i != $len - 1) {
					$item = explode(',', $item);
					$sku = str_replace("SKU: ", " ", $item[0]);
					$title = str_replace("Title: ", " ", $item[1]);
					$price = str_replace("Price:", " ", $item[3]);
					if ($refund['rma_number']) {
						$return_id = $db->func_query_first_cell('SELECT id FROM inv_returns where rma_number = "'.$refund['rma_number'].'"');
						$reason = $db->func_query_first('SELECT * FROM inv_return_items where sku = "'.trim($sku).'" AND return_id = "'.$return_id.'"');
					}
					$rowData = array($refund['date_added'],$refund['to_email'],$refund['order_id'],$refund['rma_number'],$source,$user,$refund['code'],$refund['amount'],$price,$sku,$title,
						$reason['item_condition'] .'-'. $reason['item_issue']);
					fputcsv($file , $rowData,',');
				}
				$i++;
			}


		}
		

		
		
	}
fclose($file);
}
// print_r($_SERVER);exit;
// echo $_SERVER['DOCUMENT_ROOT']."/refund_reports/StoreCreditPaypalRefund Report-".date("m-d-Y", strtotime(' -1 day')).".csv";exit;
if ($refunds) {
	if (file_exists($_SERVER['DOCUMENT_ROOT']."/imp/refund_reports/StoreCreditPaypalRefund Report-".date("m-d-Y", strtotime(' -1 day')).".csv")) {
		// echo 'exists';exit;
		$file = fopen($filename,"a");
	} else {
		// echo 'no exists';exit;
		$file = fopen($filename,"w");
		fputcsv($file , array('No Store Credits issued Generated'),',');
	}
	fputcsv($file , array('PayPal Refunds Generated'),',');
	$headers = array("Refund Date","User","Transaction ID","Ref ID","Sender","Receiver","Amount Sent");
	fputcsv($file , $headers,',');
	$rowData = array();
	foreach($refunds as $refund){

		if ($refund['order_id']) {
			$user = $db->func_query_first_cell("SELECT user_id FROM  inv_order_history WHERE comment LIKE '%refunded%' AND order_id='".$refund['order_id']."'");
			$user = get_username($user);
		} else {
			$user = 'N/A';
		}
		
		$rowData = array($refund['date_added'],$user,$refund['transaction_id'],$refund['order_id'],$refund['receiver_email'],$refund['email'],$refund['amount']*-1);
		fputcsv($file , $rowData,',');	
		
	}

fclose($file);
} else {
	if (file_exists($_SERVER['DOCUMENT_ROOT']."/imp/refund_reports/StoreCreditPaypalRefund Report-".date("m-d-Y", strtotime(' -1 day')).".csv")) {
		$file = fopen($filename,"a");
		fputcsv($file , array('No PayPal Refunds Generated'),',');
		fclose($file);
	}
}



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
$mail->addAddress('saad@phonepartsusa.com', 'Saad Ahmed');
//$mail->addAddress('gohar.chattha@gmail.com', 'Gohar Chattha');
$mail->Subject = ('Store Credits and Paypal Refunds - PhonePartsUSA');
$mail->Body = 'Store Credits and Paypal Refunds generated Yesterday. ';
$mail->IsHTML(true);
$mail->addAttachment($filename);
if ($refunds || $store_credits) {
	$sendm1 = $mail->send();
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
$test_mail->Subject = ('Store Credits and Paypal Refunds Report');
$test_mail->Body = 'Store Credits and Paypal Refunds Report! ';
$test_mail->IsHTML(true);
$test_mail->addAttachment($filename);
$test = $test_mail->send();
}

//Price Update Report Email by Gohar
$price_file = "price_report/PriceUpdateReport-".date("Y-m-d", strtotime(' -1 day')).".csv";

if (file_exists($_SERVER['DOCUMENT_ROOT']."/imp/price_report/PriceUpdateReport-".date("Y-m-d", strtotime(' -1 day')).".csv")) {
$price_mail = new PHPMailer();
$price_mail->IsSMTP();
$price_mail->CharSet = 'UTF-8';
$price_mail->Host = MAIL_HOST; // SMTP server example
$price_mail->SMTPDebug = 0;                     // enables SMTP debug information (for testing)
$price_mail->SMTPAuth = true;                  // enable SMTP authentication
$price_mail->Port = 25;                    // set the SMTP port for the GMAIL server
$price_mail->Username = MAIL_USER; // SMTP account username example
$price_mail->Password = MAIL_PASSWORD;        // SMTP account password example
$price_mail->SetFrom(MAIL_USER, 'PhonePartsUSA');
$price_mail->addAddress('saad@phonepartsusa.com', 'Saad Ahmed');
$price_mail->Subject = ('Product Pricing Update Report - PhonePartsUSA');
$price_mail->Body = 'Product Prices Updated on '.date("m-d-Y", strtotime(' -1 day'));
$price_mail->IsHTML(true);
$price_mail->addAttachment($price_file);
$sendm3 = $price_mail->send();

//Test Email to Zaman
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
$test_mail->Subject = ('Test Email for Product Pricing Update Report');
$test_mail->Body = 'Product Prices Updated on '.date("m-d-Y", strtotime(' -1 day'));
$test_mail->IsHTML(true);
$test_mail->addAttachment($price_file);
$test = $test_mail->send();
}

echo "Success";