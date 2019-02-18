<?php



set_time_limit(0);

ini_set("memory_limit", "20000M");



include_once("config.php");
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");


$productUpdateArray = json_decode($_REQUEST['productUpdateArray'],true);



$response = array();

if(is_array($productUpdateArray) and count($productUpdateArray) > 0){
	$product_email_body = '';

	foreach($productUpdateArray as $product){


		$disc_check = $db->func_query_first("select * from oc_product where model = '".$product['sku']."' limit 1");
		if ($disc_check['quantity']<=0 && $product['qty'] > 0 && $disc_check['discontinue'] == '1') {
			$product_email_body .= $product['sku'].': '.$disc_check['quantity'].' Updated to '.$product['qty'].'<br>';
		}

		$SKU = $product['sku'];

		$Qty = $product['qty'];


		
		// $db->db_exec("Update oc_product SET quantity='".$Qty."',  date_modified = '".date('Y-m-d H:i:s')."', need_sync = 1 where model = '$SKU' OR sku = '$SKU'");


		
			//$log  = "SKU:".$SKU." QTY:".$Qty.PHP_EOL;

			//file_put_contents('product_sync.txt', $log, FILE_APPEND);


		$check_result = $db->func_query_first("select * from inv_product_inout_stocks where product_sku = '$SKU' order by date_modified limit 1");
		if($check_result)
		{
			if($Qty==0)
			{

			}
			else
			{
				$inout_stock = array();

				$inout_stock['instock_date']  = date("Y-m-d H:i:s");

				$inout_stock['date_modified'] = date("Y-m-d H:i:s");

				$db->func_array2update("inv_product_inout_stocks", $inout_stock , "id = '". $check_result['id'] ."'");
			}
		}
		else
		{
			if($Qty==0)
			{
				$inout_stock = array();
				$inout_stock['product_sku'] = $SKU;

				$inout_stock['outstock_date']  = date("Y-m-d H:i:s");

				$inout_stock['date_modified'] = date("Y-m-d H:i:s");

				$db->func_array2insert("inv_product_inout_stocks", $inout_stock);


				// Email if out of stock
		if (strtolower($SKU)== 'apl-001-2184' || strtolower($SKU)== 'apl-001-2185') {
			
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

	$mail->addAddress('jordan@phonepartsusa.com', 'Jordan');
	$mail->Subject = ($SKU. ' Quantity Got out of stock Recently - PhonePartsUSA');
	$mail->Body = "<p>Hi</p><p>".$SKU." is out of stock now, please consult billy and do the rightful things.</p><p>Thanks,</p>Zaman";
	$mail->IsHTML(true);
	$sendm1 = $mail->send();
		
}




		// end email

			}
			else
			{

			}
		}
        
		/*if($Qty == 0){

			$insert = false;

			if(!$check_result){

				$insert = true;

			}

			elseif(intval($check_result['instock_date']) && strtotime($check_result['instock_date']) > strtotime($check_result['outstock_date'])){

				$insert = true;

			}



			if($insert){

				$inout_stock = array();

				$inout_stock['product_sku']   = $SKU;

				$inout_stock['outstock_date'] = date("Y-m-d H:i:s");

				$inout_stock['date_modified'] = date("Y-m-d H:i:s");

				$db->func_array2insert("inv_product_inout_stocks", $inout_stock);

			}

		} else  if(!intval($check_result['instock_date'])) {

			$inout_stock = array();

			$inout_stock['instock_date']  = date("Y-m-d H:i:s");

			$inout_stock['date_modified'] = date("Y-m-d H:i:s");

			$db->func_array2update("inv_product_inout_stocks", $inout_stock , "id = '". $check_result['id'] ."'");

		}*/

	}

}



if(isset($_GET['need_kit_sync']) AND $_GET['need_kit_sync'] == 1){

	$db->db_exec("update inv_kit_skus SET need_sync = 1");

}

// if ($product_email_body != '') {
// 	$mail = new PHPMailer();
// 	$mail->IsSMTP();
// 	$mail->CharSet = 'UTF-8';
// 	$mail->Host = MAIL_HOST; // SMTP server example
// 	$mail->SMTPDebug = 0;                     // enables SMTP debug information (for testing)
// 	$mail->SMTPAuth = true;                  // enable SMTP authentication
// 	$mail->Port = 25;                    // set the SMTP port for the GMAIL server
// 	$mail->Username = MAIL_USER; // SMTP account username example
// 	$mail->Password = MAIL_PASSWORD;        // SMTP account password example
// 	$mail->SetFrom(MAIL_USER, 'PhonePartsUSA');

// 	$mail->addAddress('xaman.riaz@gmail.com', 'Xaman Riaz');
// 	$mail->Subject = ('Quantities Restocked Recently - PhonePartsUSA');
// 	$mail->Body = $product_email_body;
// 	$mail->IsHTML(true);
// 	$sendm1 = $mail->send();
// }

exec("php /home/phonerep/public_html/imp/need_sync.php");

echo "success";