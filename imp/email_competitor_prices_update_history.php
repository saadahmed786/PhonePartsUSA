<?php
include 'config.php';
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
include_once 'product_catalog/load_catalog2.php';
include_once 'inc/functions.php';
ini_set('memory_limit','2048M');
ini_set('max_execution_time', 500); //300 seconds = 5 minutes
if ($_GET['date']) {
 $date = $_GET['date'];
} else {	
 $date = date('Y-m-d');
}

$products= $db->func_query("SELECT * FROM inv_competitor_prices_history WHERE DATE(date_updated) = DATE(( NOW() - INTERVAL 1 DAY ))  ORDER BY date_updated asc");

//$headers = array("Date Updated","SKU","Competitor","Previous Price","Updated Price");
$headers = array("SKU","Title","Last Received","30 Days Item Sale","Quantity","True Cost","D1","P1","Sale Price","Date Updated","Competitor","Previous Price","Price");


$filename = "competitor_csv/CompetitorPriceChangeHistory-".$date.".csv";
$file = fopen($filename,"w");
fputcsv($file , $headers,',');

if ($products) {

	foreach($products as $product){
		$detail = $db->func_query_first("Select p.product_id,p.status, p.price,p.sale_price,p.quantity from oc_product p where  p.sku='".$product['sku']."' ");
		$product['product_id'] = $detail['product_id'];
		$product['price'] = $detail['price'];
		$product['sale_price'] = $detail['sale_price'];
		$product['quantity'] = $detail['quantity'];

		$p1_price = $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=1633 AND product_id='" . (int) $product['product_id'] . "' AND quantity=1");
		$last_received = $db->func_query_first_cell("SELECT s.date_received FROM inv_shipments s Inner join inv_shipment_items si on (s.id=si.shipment_id) WHERE si.product_id ='" . (int) $product['product_id'] . "' order by s.date_received desc");

		$costs = $catalog->getTrueCostRow($product['sku']);
		$last_30_days = getLast30DaysItemSale($product['sku']);

				$rowData = array($product['sku'],getItemName($product['sku']),americanDate($last_received),(int)$last_30_days,(int)$product['quantity'],(float)$costs['true_cost'],(float)$product['price'],(float)$p1_price,(float)$product['sale_price'],americanDate($product['date_updated']),$product['site'],(float)$product['previous_price'],(float)$product['updated_price']);
				if ($detail['status'] == '1') {
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
$mail->Subject = ('Competitor Price Changing History - PhonePartsUSA');
$mail->Body = 'Changes recorded in the Competitor Prices yesterday';
$mail->IsHTML(true);
$mail->addAttachment($filename);
if ($products) {
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
$test_mail->addAddress('jordan@phonepartsusa.com', 'Jordan');
$test_mail->Subject = ('Competitor Price Changing History - PhonePartsUSA');
$test_mail->Body = 'Changes recorded in the Competitor Prices yesterday';
$test_mail->IsHTML(true);
$test_mail->addAttachment($filename);
$test = $test_mail->send();
}

echo "Success";