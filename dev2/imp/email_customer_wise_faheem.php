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

$vouchers = $db->func_query("SELECT vd.voucher_id,vd.rma_number,vd.item_detail,v.code,v.to_email FROM `oc_voucher` v Inner JOIN `inv_voucher_details` vd on (v.voucher_id = vd.voucher_id) WHERE v.status = '1' AND DATE(v.date_added) = DATE('".$date."') AND vd.is_rma=1 ORDER BY v.`date_added` desc");

$refund_items = $db->func_query("SELECT r.email,r.rma_number,ri.* FROM `inv_return_items` ri Inner Join inv_returns r on (ri.return_id = r.id) WHERE ri.decision = 'Issue Refund'  AND ri.item_condition LIKE '%Item Issue%' AND DATE(r.date_completed) = DATE('".$date."') order by r.date_completed desc");

$headers = array("Date","Customer","Email","Address","City","State","Telephone","Customer Group","Store Credit Issued","Refund Issued");
$filename = "refund_reports/CustomerWiseCreditAndRefundReport-".$date.".csv";
$file = fopen($filename,"w");
fputcsv($file , $headers,',');

//Iterate Store Credit Totals per item
foreach ($vouchers as $voucher ) {
		$return_id = $db->func_query_first_cell("select id from inv_returns where rma_number = '".$voucher['rma_number']."'");
		$customer_data = $db->func_query_first("select * from inv_customers where email = '".$voucher['to_email']."'");
		$items = $db->func_query("select * from inv_return_items where return_id = '".$return_id."' AND item_condition LIKE '%Item Issue%'");
		foreach ($items as $item) {
			$decisionCheckQuery = $db->func_query_first("SELECT * FROM inv_return_decision WHERE return_item_id='" . $item['id'] . "'");
			if ($decisionCheckQuery['action'] == 'Issue Credit') {
				if (!$item['rtv_vendor_id']) {
					$item['rtv_vendor_id'] = $db->func_query_first_cell("SELECT vendor FROM inv_product_vendors WHERE product_sku='".$item['sku']."' limit 1");
				}
				$result[$voucher['to_email']]['store_credit'] = $result[$voucher['to_email']]['store_credit'] + $decisionCheckQuery['price'];

				
			}
		}
		if ($result[$voucher['to_email']]['store_credit']) {
			$result[$voucher['to_email']]['firstname'] =  $customer_data['firstname'];
			$result[$voucher['to_email']]['lastname'] =  $customer_data['lastname'];
			$result[$voucher['to_email']]['city'] =  $customer_data['city'];
			$result[$voucher['to_email']]['state'] =  $customer_data['state'];
			$result[$voucher['to_email']]['address1'] =  $customer_data['address1'];
			$result[$voucher['to_email']]['address2'] =  $customer_data['address2'];
			$result[$voucher['to_email']]['telephone'] =  $customer_data['telephone'];
			$result[$voucher['to_email']]['customer_group'] =  $customer_data['customer_group'];
			
		}
		

	}
//Iterate Refund Totals per customer
	foreach ($refund_items as $item) {
		$result[$item['email']]['refund'] = $result[$item['email']]['refund'] + $item['price'];
		if ($result[$item['email']]['refund']) {
			$customer_data = $db->func_query_first("select * from inv_customers where email = '".$item['email']."'");
			$result[$item['email']]['firstname'] =  $customer_data['firstname'];
			$result[$item['email']]['lastname'] =  $customer_data['lastname'];
			$result[$item['email']]['city'] =  $customer_data['city'];
			$result[$item['email']]['state'] =  $customer_data['state'];
			$result[$item['email']]['address1'] =  $customer_data['address1'];
			$result[$item['email']]['address2'] =  $customer_data['address2'];
			$result[$item['email']]['telephone'] =  $customer_data['telephone'];
			$result[$item['email']]['customer_group'] =  $customer_data['customer_group'];
		}
		
	}

//Saving result

foreach ($result as $email => $customer) {
	$rowData = array($date,$customer['firstname'].' '.$customer['lastname'],$email,$customer['address1'].' '.$customer['address2'],$customer['city'],$customer['state'],$customer['telephone'],$customer['customer_group'],$customer['store_credit'],$customer['refund']);
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
$mail->Subject = ('Customer Wise Refunds & Store Credits Report - PhonePartsUSA');
$mail->Body = 'Customer Wise Refunds & Store Credits generated on '.$date;
$mail->IsHTML(true);
$mail->addAttachment($filename);
$sendm1 = $mail->send();

echo "Success";