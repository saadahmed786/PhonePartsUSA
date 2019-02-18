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

$lbbs = $db->func_query("SELECT * from oc_buyback where carrier_code <>'' AND carrier_code <>'In House' ");
$rmas = $db->func_query("SELECT * from inv_returns where carrier <>'' AND carrier <>'In House' ");

$filename = "test_files/LBB_RMA Tracking Report.csv";
	$file = fopen($filename,"w");
	$headers = array("LBB/RMA","Tracking Code","Tracking Update");
	fputcsv($file , $headers,',');
if ($lbbs) {
	$rowData = array();
	foreach($lbbs as $lbb){
			$tracker = $db->func_query_first("SELECT * FROM inv_tracker WHERE shipment_id='".$lbb['shipment_number']."'"); 
			$tracker_status = $db->func_query_first("SELECT * FROM inv_tracker_status WHERE tracker_id='".$tracker['tracker_id']."' order by id desc");
			if ($tracker_status['message']) {
			$rowData = array($lbb['shipment_number'],$tracker['tracking_code'],$tracker_status['message']);
			fputcsv($file , $rowData,',');
			
			}
	}
}
if ($rmas) {
	$rowData = array();
	foreach($rmas as $rma){
			$tracker = $db->func_query_first("SELECT * FROM inv_tracker WHERE shipment_id = '".$rma['rma_number']."'"); 
			$tracker_status = $db->func_query_first("SELECT * FROM inv_tracker_status WHERE tracker_id='".$tracker['tracker_id']."' order by id desc limit 1");
			if ($tracker_status['message']) {
			$rowData = array($rma['rma_number'],$tracker['tracking_code'],$tracker_status['message']);
			fputcsv($file , $rowData,',');
		}
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
//$mail->addAddress('gohar.chattha@gmail.com', 'Gohar Chattha');
$mail->Subject = ('LBB - RMA Tracking Report - PhonePartsUSA');
$mail->Body = 'LBB/RMA recent tracking updates.';
$mail->IsHTML(true);
$mail->addAttachment($filename);
$sendm1 = $mail->send();

echo "Success";