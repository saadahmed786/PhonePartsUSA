<?php
include 'config.php';
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
include_once 'inc/functions.php';
ini_set('memory_limit','2048M');
ini_set('max_execution_time', 500); //300 seconds = 5 minutes

$refunds = $db->func_query("SELECT * FROM `inv_transactions` WHERE payment_status='Completed' and order_status='Completed' and (receiver_email='paypal@phonepartsusa.com' and amount<0) AND DATE(date_added) = DATE(( NOW() - INTERVAL 1 DAY )) ORDER BY `date_added` desc");


$headers = array("Refund Date","User","Transaction ID","Ref ID","Sender","Receiver","Amount Sent");
$filename = "refund_reports/PayPal Refund Report-".date("Y-m-d").".csv";
$file = fopen($filename,"w");
fputcsv($file , $headers,',');

if ($refunds) {

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
$mail->addAddress('saad@phonepartsusa.com', 'Saad Ahmed');
$mail->Subject = ('Paypal Refunds Report - PhonePartsUSA');
$mail->Body = 'Refunds generated for PayPal Yesterday. ';
$mail->IsHTML(true);
$mail->addAttachment($filename);
if ($refunds) {
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
$test_mail->Subject = ('Test Email for refund report');
$test_mail->Body = 'Test for paypal report! ';
$test_mail->IsHTML(true);
$test_mail->addAttachment($filename);
$test = $test_mail->send();
}

echo "Success";