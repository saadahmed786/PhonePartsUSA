<?php
include 'config.php';
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
include_once 'inc/functions.php';
include_once 'product_catalog/load_catalog2.php';
//$test = date("Y-m-d");
//print_r($test);exit;
$_query = "SELECT DISTINCT sku FROM `inv_product_price_scrap_history` where csv_date<>'".date("Y-m-d")."' order by csv_date ASC limit 500";
//$_query = "Select p.product_id,p.sku,p.is_csv_added, p.price,p.sale_price, pd.name from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) where p.is_csv_added <> '0' AND p.status = '1' order by p.product_id asc ";
//print_r($_query);exit;
$products = $db->func_query($_query);
if ($products) {
	$csv_complete = '0';
} else {
	$csv_complete = '1';
}
//testObject($products);
$headers = array("SKU","Title","Quantity","30 Days Item Sale","Raw Cost","Ex Rate","Shipping Fee","True Cost","D1","P1","Sale Price","MS","FZ","MG","MD","ETS","MC","LL","P4C","CPH");
$filenameup = "competitor_csv/CompetitorPriceUp-".date("Y-m-d").".csv";
$filenamedown = "competitor_csv/CompetitorPriceDown-".date("Y-m-d").".csv";
if (!file_exists($_SERVER['DOCUMENT_ROOT']."/imp/competitor_csv/CompetitorPriceUp-".date("Y-m-d").".csv")) {
	$fup = fopen($filenameup,"w");
	fputcsv($fup , $headers);
} else {
	$fup = fopen($filenameup,"a");
}
if (!file_exists($_SERVER['DOCUMENT_ROOT']."/imp/competitor_csv/CompetitorPriceDown-".date("Y-m-d").".csv")) {
	$fdown = fopen($filenamedown,"w");
	fputcsv($fdown , $headers);	
} else {
	$fdown = fopen($filenamedown,"a");
}
//$faheem = "files/faheem-".date("Y-m-d").".csv";
//$fah = fopen($faheem,"w");
//$headers = array("SKU","Title","MS","FZ","MG","MD","ETS","MC","LL","P4C","CPH");
//fputcsv($fah , $headers);
if ($products) {
	
	foreach($products as $product){
		
		$db->db_exec ( "update inv_product_price_scrap_history set csv_date='".date("Y-m-d")."' where sku='".$product['sku']."'" );
		$costs = $catalog->getTrueCostRow($product['sku']);
		$detail = $db->func_query_first("Select p.product_id,p.sku,p.is_csv_added, p.price,p.sale_price,p.quantity, pd.name from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) where p.is_csv_added <> '0' AND p.status = '1' and sku='".$product['sku']."' ");
		$product['product_id'] = $detail['product_id'];
		$product['price'] = $detail['price'];
		$product['name'] = $detail['name'];
		$product['sale_price'] = $detail['sale_price'];
		$product['quantity'] = $detail['quantity'];
		if(!$detail)
		{
			continue;
		}
		$scrapping_sites = array('mobile_sentrix', 'fixez', 'mengtor', 'mobile_defenders','etrade_supply','maya_cellular','lcd_loop','parts_4_cells','cell_parts_hub');
		$price_values = array();
		foreach ($scrapping_sites as $site) {
			$price = $db->func_query_first("select price from inv_product_price_scrap_history ph where ph.sku = '" . $product['sku'] . "' AND ph.type = '$site' order by ph.added DESC limit 1");
			$price_values[] = $price;
		}
		$p1_price = $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=1633 AND product_id='" . (int) $product['product_id'] . "' AND quantity=1");
		$last_30_days = getLast30DaysItemSale($product['sku']);
		$rowData = array($product['sku'] , $product['name'],$product['quantity'],$last_30_days, $costs['raw_cost'],$costs['ex_rate'],$costs['shipping_fee'],$costs['true_cost'],$product['price'],$p1_price,$product['sale_price'],$price_values[0]['price'],$price_values[1]['price'],$price_values[2]['price'],$price_values[3]['price'],$price_values[4]['price'],$price_values[5]['price'],$price_values[6]['price'],$price_values[7]['price'],$price_values[8]['price']);
	//for faheem csv
	//$rowData = array($product['sku'] , $product['name'] ,$price_values[0]['price'],$price_values[1]['price'],$price_values[2]['price'],$price_values[3]['price'],$price_values[4]['price'],$price_values[5]['price'],$price_values[6]['price'],$price_values[7]['price'],$price_values[8]['price']);
	//fputcsv($fah , $rowData);
		if (($price_values[0]['price'] && $price_values[0]['price']<$product['price']) || ($price_values[1]['price'] && $price_values[1]['price']<$product['price']) || ($price_values[2]['price'] && $price_values[2]['price']<$product['price']) || ($price_values[3]['price'] && $price_values[3]['price']<$product['price']) || ($price_values[4]['price'] && $price_values[4]['price']<$product['price']) || ($price_values[5]['price'] && $price_values[5]['price']<$product['price']) || ($price_values[6]['price'] && $price_values[6]['price']<$product['price']) || ($price_values[7]['price'] && $price_values[7]['price']<$product['price']) || ($price_values[8]['price'] && $price_values[8]['price']<$product['price'])) {
			$insert_down = true;
			$insert_up = false;
		} else if (($price_values[0]['price'] && $price_values[0]['price']>$product['price']) || ($price_values[1]['price'] && $price_values[1]['price']>$product['price']) || ($price_values[2]['price'] && $price_values[2]['price']>$product['price']) || ($price_values[3]['price'] && $price_values[3]['price']>$product['price']) || ($price_values[4]['price'] && $price_values[4]['price']>$product['price']) || ($price_values[5]['price'] && $price_values[5]['price']>$product['price']) || ($price_values[6]['price'] && $price_values[6]['price']>$product['price']) || ($price_values[7]['price'] && $price_values[7]['price']>$product['price']) || ($price_values[8]['price'] && $price_values[8]['price']>$product['price'])) {
			$insert_up = true;
			$insert_down = false;
		} else
		{
			$insert_down = false;
			$insert_up = false;
		}
		if ($insert_up) {
			fputcsv($fup , $rowData);
		} else if ($insert_down) {
			fputcsv($fdown , $rowData);
		}
	}
}
//fclose($fah);
fclose($fup);
fclose($fdown);
if ($csv_complete == '1') {
	$mail_up = new PHPMailer();
	$mail_up->IsSMTP();
	$mail_up->CharSet = 'UTF-8';
$mail_up->Host = MAIL_HOST; // SMTP server example
$mail_up->SMTPDebug = 0;                     // enables SMTP debug information (for testing)
$mail_up->SMTPAuth = true;                  // enable SMTP authentication
$mail_up->Port = 25;                    // set the SMTP port for the GMAIL server
$mail_up->Username = MAIL_USER; // SMTP account username example
$mail_up->Password = MAIL_PASSWORD;        // SMTP account password example
$mail_up->SetFrom(MAIL_USER, 'PhonePartsUSA');
$mail_up->addAddress('saad@phonepartsusa.com', 'Saad Ahmed');
$mail_up->Subject = ('Competitors Prices Up - PhonePartsUSA');
$mail_up->Body = 'List of Competitors prices Above our Prices. ';
$mail_up->IsHTML(true);
$mail_up->addAttachment($filenameup);
$sendm1 = $mail_up->send();
$mail_down = new PHPMailer();
$mail_down->IsSMTP();
$mail_down->CharSet = 'UTF-8';
$mail_down->Host = MAIL_HOST; // SMTP server example
$mail_down->SMTPDebug = 0;                     // enables SMTP debug information (for testing)
$mail_down->SMTPAuth = true;                  // enable SMTP authentication
$mail_down->Port = 25;                    // set the SMTP port for the GMAIL server
$mail_down->Username = MAIL_USER; // SMTP account username example
$mail_down->Password = MAIL_PASSWORD;        // SMTP account password example
$mail_down->SetFrom(MAIL_USER, 'PhonePartsUSA');
$mail_down->addAddress('saad@phonepartsusa.com', 'Saad Ahmed');
$mail_down->Subject = ('Competitors Prices Down - PhonePartsUSA');
$mail_down->Body = 'List of Competitors prices Below our Prices. ';
$mail_down->IsHTML(true);
$mail_down->addAttachment($filenamedown);
$sendm2 = $mail_down->send();
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
$test_mail->Subject = ('Test Email by Gohar');
$test_mail->Body = 'Test for saad Competitor csv email. If you receive this email that means cron issue resolved :D Congrats ! ';
$test_mail->IsHTML(true);
$test_mail->addAttachment($filenameup);
$test_mail->addAttachment($filenamedown);
$test = $test_mail->send();
}
/*$mail_up_data = array ();
$mail_up = array ();
$mail_up_data['email'] = 'gohar.chattha@gmail.com';
$mail_up_data['customer_name'] = 'Gohar Chattha';
$mail_up['subject'] = 'Competitors Prices Up - PhonePartsUSA';
$mail_up['message'] = 'List of Competitors prices Above our Prices';
$attachment_up = $filenameup;
$attachment_up_name = 'Competitors Prices Up';
$sendm1 = sendEmailDetails($mail_up_data,$mail_up,array(),$attachment_up,$attachment_up_name);
$mail_down_data = array ();
$mail_down = array ();
$mail_down_data['email'] = 'gohar.chattha@gmail.com';
$mail_down_data['customer_name'] = 'Gohar Chattha';
$mail_down['subject'] = 'Competitors Prices Down - PhonePartsUSA';
$mail_down['message'] = 'List of Competitors prices Below our Prices.';
$attachment_down = $filenamedown;
$attachment_down_name = 'Competitors Prices Down';
$sendm1=sendEmailDetails($mail_down_data,$mail_down,array(),$attachment_down,$attachment_down_name);
*/
print_r($csv_complete);
echo "Success";