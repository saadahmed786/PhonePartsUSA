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

$_query = "SELECT DISTINCT sku FROM `inv_product_price_scrap_history` where sku LIKE '%BT-BP%'";
//$_query = "Select p.product_id,p.sku,p.is_csv_added, p.price,p.sale_price, pd.name from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) where p.is_csv_added <> '0' AND p.status = '1' order by p.product_id asc ";
//print_r($_query);exit;
$products = $db->func_query($_query);
//$headers = array("Date Updated","SKU","Competitor","Previous Price","Updated Price");
$headers = array("SKU","Title","Quantity","30 Days Item Sale","Raw Cost","Ex Rate","Shipping Fee","True Cost","D1","P1","Sale Price","MS","FZ","MG","MD","ETS","MC","LL","P4C","CPH");


$filename = "test_files/BT-BP-SKUS.csv";
$file = fopen($filename,"w");
fputcsv($file , $headers,',');

if ($products) {

	foreach($products as $product){

		
		$costs = $catalog->getTrueCostRow($product['sku']);
		$detail = $db->func_query_first("Select p.product_id,p.sku,p.is_csv_added, p.price,p.sale_price,p.quantity, pd.name from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) where p.status = '1' and sku='".$product['sku']."' ");
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
//$mail->addAddress('gohar.chattha@gmail.com', 'Gohar Chattha');
$mail->Subject = ('BT-BP SKU List - PhonePartsUSA');
$mail->Body = 'Requested CSV';
$mail->IsHTML(true);
$mail->addAttachment($filename);
$sendm1 = $mail->send();


echo "Success";