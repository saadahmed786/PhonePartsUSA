<?php

include '../auth.php';
include '../inc/functions.php';

if($_POST['upload'] && $_FILES['products']['tmp_name']){
	$csv_mimetypes = array(
		'text/csv',
		'text/plain',
		'application/csv',
		'text/comma-separated-values',
		'application/excel',
		'application/vnd.ms-excel',
		'application/vnd.msexcel',
		'text/anytext',
		'application/octet-stream',
		'application/txt',
		);

	$type = $_FILES['products']['type'];
	if(in_array($type,$csv_mimetypes)){
		$filename = $_FILES['products']['tmp_name'];
		$handle   = fopen("$filename", "r");

		$heading  = fgetcsv($handle);
		$products = array();

		$i = 0;
		while(!feof($handle)){
			$row = fgetcsv($handle);
			for($j=0;$j<count($heading);$j++){
				if($row[$j]){
					$products[$i][$heading[$j]] = trim($row[$j]);
				}
			}
			$i++;
		}
		
		$log = "Product Has been imported via CSV ";
		$bits .= '';
		$i = 0;
		foreach($products as $product){
			$product_sku = $db->func_escape_string($product['SKU']);
			$product_name = $db->func_escape_string($product['ItemName']);
			$class = strtolower($db->func_escape_string($product['Class']));
//			$sub_class = strtolower($db->func_escape_string($product['Sub Class']));
			$status = strtolower($db->func_escape_string($product['Status']));
			
			$raw_cost = (float)$product['Raw Cost'];
			$raw_cost_usd = (float)$product['Raw Cost(USD)'];
			
			$shipping_fee = (float)$product['Shipping Fee'];
			$exchange_rate = (float)$product['Exchange Rate'];
			$retail_price = (float)$product['Retail Price'];
			$product_price = (float)$product['Price'];
			$default_1 = (float)$product['Default Qty 1'];
			$default_3 = (float)$product['Default Qty 3'];
			$default_10 = (float)$product['Default Qty 10'];
			$local_1 = (float)$product['Local Qty 1'];
			$local_3 = (float)$product['Local Qty 3'];
			$local_10 = (float)$product['Local Qty 10'];
			$ws_1 = (float)$product['WS Qty 1'];
			$ws_3 = (float)$product['WS Qty 3'];
			$ws_10 = (float)$product['WS Qty 10'];
			
			$silver1 = (float)$product['Silver 1'];
			$silver3 = (float)$product['Silver 3'];
			$silver10 = (float)$product['Silver 10'];
			
			$gold1 = (float)$product['Gold 1'];
			$gold3 = (float)$product['Gold 3'];
			$gold10 = (float)$product['Gold 10'];
			
			$platinum1 = (float)$product['Platinum 1'];
			$platinum3 = (float)$product['Platinum 3'];
			$platinum10 = (float)$product['Platinum 10'];
			
			$diamond1 = (float)$product['Diamond 1'];
			$diamond3 = (float)$product['Diamond 3'];
			$diamond10 = (float)$product['Diamond 10'];
			
			
			$grade_a = (float)$product['Grade A'];
			$grade_b = (float)$product['Grade B'];
			$grade_c = (float)$product['Grade C'];
			$kit_sku = $db->func_escape_string($product['Kit SKU']);
			$vendor = $db->func_escape_string(strtolower($product['Vendor']));
			$kit_price = (float)$product['Kit Price'];
			$gadgetfix = $db->func_escape_string($product['Gadget Fix URL']);
			$ebay = $db->func_escape_string($product['Ebay URL']);
			$ebay2 = $db->func_escape_string($product['Ebay 2 URL']);
			$ebay3 = $db->func_escape_string($product['Ebay 3 URL']);
			$ebay4 = $db->func_escape_string($product['Ebay 4 URL']);
			$mengtor = $db->func_escape_string($product['Mengtor URL']);
			$mobile_defenders = $db->func_escape_string($product['Mobile Defenders URL']);
			
			if($product_sku)
			{
				$log .= linkToProduct($product_sku) . ', ';
				$product_id = $db->func_query_first_cell("SELECT product_id FROM oc_product WHERE sku='".$product_sku."'");

				if($product_name and $_POST['for_item'])
				{
					$db->db_exec("update oc_product_description SET name='".$product_name."' WHERE product_id='".(int)$product_id."'");	
					if ($i == 0) {
						$bits .= 'Item Name' . ', ';
					}
				}
				
				if($retail_price and $_POST['for_retail'])
				{
					
					$db->db_exec("update oc_product SET retail_price='".$retail_price."' WHERE product_id='".(int)$product_id."'");	
					
					if ($i == 0) {
						$bits .= 'Retail Price' . ', ';
					}
				}
				if($product_price and $_POST['for_price'])
				{
					
					$db->db_exec("update oc_product SET price='".$product_price."' WHERE product_id='".(int)$product_id."'");	
					
					if ($i == 0) {
						$bits .= 'Product Price' . ', ';
					}
				}
				
				if($status and $_POST['for_status'])
				{
					
					$db->db_exec("update oc_product SET status='".($status=='enable'?1:0)."' WHERE product_id='".(int)$product_id."'");	
					
					if ($i == 0) {
						$bits .= 'Product Status' . ', ';
					}
				}
				
				if($kit_sku and $kit_price and $_POST['for_kit'])
				{
					
					$db->db_exec("update oc_product SET price='".$kit_price."',is_kit=1 WHERE sku='".$kit_sku."'");	
					
					if ($i == 0) {
						$bits .= 'Product Kits' . ', ';
					}
				}
				
				if($vendor and $_POST['for_vendor'])
				{
					
					$db->db_exec("update oc_product SET vendor='".$vendor."' WHERE product_id='".(int)$product_id."'");	
					
					if ($i == 0) {
						$bits .= 'Product Vendor' . ', ';
					}
				}
				
				if($class and $_POST['for_class'])
				{
					$classification_id = $db->func_query_first_cell("SELECT id FROM inv_classification WHERE name LIKE '%$class%' ");
					
					if($classification_id)
					{
						$db->db_exec("update oc_product SET classification_id='".(int)$classification_id."' WHERE product_id='".(int)$product_id."'");	
					}
					
					if ($i == 0) {
						$bits .= 'Product Class' . ', ';
					}
				}
				
//				if($sub_class and $_POST['for_sub_class'])
//				{
//					$sub_classification_id = $db->func_query_first_cell("SELECT id FROM inv_sub_classification WHERE name LIKE '%$sub_class%' ");
//					
//					if($sub_classification_id)
//					{
//					$db->db_exec("update oc_product SET sub_classification_id='".(int)$sub_classification_id."' WHERE product_id='".(int)$product_id."'");	
//					}
//					
//					
//				}
				
				if(($raw_cost and $exchange_rate) and $_POST['for_cost'])
				{
					$db->db_exec("INSERT INTO inv_product_costs SET sku='".$product_sku."',cost_date='".date('Y-m-d H:i:s')."',raw_cost='".$raw_cost."',current_cost='".$raw_cost/$exchange_rate."',ex_rate='".$exchange_rate."',shipping_fee='".$shipping_fee."',vendor_code='China Office'");	
				 	
					if ($i == 0) {
						$bits .= 'Product True Cost, Raw Cost, Shipping Fee, Exchange Rate' . ', ';
					}
				}
				if(($raw_cost_usd and ($raw_cost=='' || $raw_cost==0)) and $_POST['for_cost'])
				{
					$db->db_exec("INSERT INTO inv_product_costs SET sku='".$product_sku."',cost_date='".date('Y-m-d H:i:s')."',raw_cost='".$raw_cost_usd."',current_cost='".$raw_cost_usd."',ex_rate='1',shipping_fee='0.00',vendor_code='China Office'");	
				}
				if($grade_a and $_POST['for_grade_a'])
				{
					$g_sku = $db->func_query_first_cell("SELECT sku FROM oc_product where is_main_sku=0 AND  main_sku='".$product_sku."' and item_grade='Grade A' ");	
					if($g_sku)
					{
						$db->db_exec("UPDATE oc_product SET price='".$grade_a."' WHERE sku='".$g_sku."'");	
						
					}

					if ($i == 0) {
						$bits .= 'Product Grade A' . ', ';
					}
				}
				if($grade_b and $_POST['for_grade_b'])
				{
					$g_sku = $db->func_query_first_cell("SELECT sku FROM oc_product where is_main_sku=0 AND  main_sku='".$product_sku."' and item_grade='Grade B' ");	
					if($g_sku)
					{
						$db->db_exec("UPDATE oc_product SET price='".$grade_b."' WHERE sku='".$g_sku."'");	
						
					}
					if ($i == 0) {
						$bits .= 'Product Grade B' . ', ';
					}
				}
				
				if($grade_c and $_POST['for_grade_c'])
				{
					$g_sku = $db->func_query_first_cell("SELECT sku FROM oc_product where is_main_sku=0 AND  main_sku='".$product_sku."' and item_grade='Grade C' ");	
					if($g_sku)
					{
						$db->db_exec("UPDATE oc_product SET price='".$grade_c."' WHERE sku='".$g_sku."'");	
						
					}
					if ($i == 0) {
						$bits .= 'Product Grade C' . ', ';
					}
				}
				
				if($gadgetfix and $_POST['for_gadgetfix'])
				{
					$check = 	$db->func_query_first("SELECT * FROM inv_product_scrape_prices WHERE sku='".$product_sku."' AND scrape_site='gadgetfix'");
					if($check)
					{
						$db->func_query("UPDATE inv_product_scrape_prices SET url = '".$gadgetfix."',date_updated='".date('Y-m-d H:i:s')."' WHERE sku='".$product_sku."' AND scrape_site='gadgetfix' ");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO inv_product_scrape_prices SET sku = '".$product_sku."', scrape_site='gadgetfix' , url='".$gadgetfix."' , price='0', date_updated='".date('Y-m-d H:i:s')."'");	
						
					}

					if ($i == 0) {
						$bits .= 'Gadget Fix URL' . ', ';
					}
					
				}
				
				if($mengtor and $_POST['for_mengtor'])
				{
					$check = 	$db->func_query_first("SELECT * FROM inv_product_scrape_prices WHERE sku='".$product_sku."' AND scrape_site='mengtor'");
					if($check)
					{
						$db->func_query("UPDATE inv_product_scrape_prices SET url = '".$mengtor."',date_updated='".date('Y-m-d H:i:s')."' WHERE sku='".$product_sku."' AND scrape_site='mengtor' ");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO inv_product_scrape_prices SET sku = '".$product_sku."', scrape_site='mengtor' , url='".$mengtor."' , price='0', date_updated='".date('Y-m-d H:i:s')."'");	
						
					}
					if ($i == 0) {
						$bits .= 'Mengtor URL' . ', ';
					}
					
				}
				
				if($mobile_defenders and $_POST['for_mobile_defenders'])
				{
					$check = 	$db->func_query_first("SELECT * FROM inv_product_scrape_prices WHERE sku='".$product_sku."' AND scrape_site='mobile_defenders'");
					if($check)
					{
						$db->func_query("UPDATE inv_product_scrape_prices SET url = '".$mobile_defenders."',date_updated='".date('Y-m-d H:i:s')."' WHERE sku='".$product_sku."' AND scrape_site='mobile_defenders' ");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO inv_product_scrape_prices SET sku = '".$product_sku."', scrape_site='mobile_defenders' , url='".$mobile_defenders."' , price='0', date_updated='".date('Y-m-d H:i:s')."'");	
						
					}
					if ($i == 0) {
						$bits .= 'Mobile Defenders URL' . ', ';
					}
					
				}
				
				if($ebay and $_POST['for_ebay'])
				{
					$check = 	$db->func_query_first("SELECT * FROM inv_product_scrape_prices WHERE sku='".$product_sku."' AND scrape_site='ebay'");
					if($check)
					{
						$db->func_query("UPDATE inv_product_scrape_prices SET url = '".$ebay."',date_updated='".date('Y-m-d H:i:s')."' WHERE sku='".$product_sku."' AND scrape_site='ebay' ");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO inv_product_scrape_prices SET sku = '".$product_sku."', scrape_site='ebay' , url='".$ebay."' , price='0', date_updated='".date('Y-m-d H:i:s')."'");	
						
					}
					if ($i == 0) {
						$bits .= 'Ebay URL' . ', ';
					}
					
				}
				
				if($ebay2 and $_POST['for_ebay'])
				{
					$check = 	$db->func_query_first("SELECT * FROM inv_product_scrape_prices WHERE sku='".$product_sku."' AND scrape_site='ebay_2'");
					if($check)
					{
						$db->func_query("UPDATE inv_product_scrape_prices SET url = '".$ebay2."',date_updated='".date('Y-m-d H:i:s')."' WHERE sku='".$product_sku."' AND scrape_site='ebay_2' ");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO inv_product_scrape_prices SET sku = '".$product_sku."', scrape_site='ebay_2' , url='".$ebay2."' , price='0', date_updated='".date('Y-m-d H:i:s')."'");	
						
					}
					
				}
				
				
				if($ebay3 and $_POST['for_ebay'])
				{
					$check = 	$db->func_query_first("SELECT * FROM inv_product_scrape_prices WHERE sku='".$product_sku."' AND scrape_site='ebay_3'");
					if($check)
					{
						$db->func_query("UPDATE inv_product_scrape_prices SET url = '".$ebay3."',date_updated='".date('Y-m-d H:i:s')."' WHERE sku='".$product_sku."' AND scrape_site='ebay_3' ");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO inv_product_scrape_prices SET sku = '".$product_sku."', scrape_site='ebay_3' , url='".$ebay3."' , price='0', date_updated='".date('Y-m-d H:i:s')."'");	
						
					}
					
				}
				
				if($ebay4 and $_POST['for_ebay'])
				{
					$check = 	$db->func_query_first("SELECT * FROM inv_product_scrape_prices WHERE sku='".$product_sku."' AND scrape_site='ebay_4'");
					if($check)
					{
						$db->func_query("UPDATE inv_product_scrape_prices SET url = '".$ebay4."',date_updated='".date('Y-m-d H:i:s')."' WHERE sku='".$product_sku."' AND scrape_site='ebay_4' ");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO inv_product_scrape_prices SET sku = '".$product_sku."', scrape_site='ebay_4' , url='".$ebay4."' , price='0', date_updated='".date('Y-m-d H:i:s')."'");	
						
					}
					
				}
				
				if($default_1 and $_POST['for_dq1'])
				{
					$check = 	$db->func_query_first("SELECT * FROM oc_product_discount WHERE product_id='".(int)$product_id."' AND customer_group_id=8 AND quantity=1 ");
					if($check)
					{
						$db->db_exec("UPDATE oc_product_discount SET price = '".$default_1."' WHERE product_id='".(int)$product_id."' AND customer_group_id=8 AND quantity=1");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$default_1."', product_id='".(int)$product_id."' , customer_group_id=8 , quantity=1");	
						
					}

					if ($i == 0) {
						$bits .= 'Default Qty 1' . ', ';
					}
					
				}
				
				if($default_3 and $_POST['for_dq3'])
				{
					$check = 	$db->func_query_first("SELECT * FROM oc_product_discount WHERE product_id='".(int)$product_id."' AND customer_group_id=8 AND quantity=3 ");
					if($check)
					{
						$db->db_exec("UPDATE oc_product_discount SET price = '".$default_3."' WHERE product_id='".(int)$product_id."' AND customer_group_id=8 AND quantity=3");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$default_3."', product_id='".(int)$product_id."' , customer_group_id=8 , quantity=3");	
						
					}
					if ($i == 0) {
						$bits .= 'Default Qty 3' . ', ';
					}
					
				}
				
				if($default_10 and $_POST['for_dq10'])
				{
					$check = 	$db->func_query_first("SELECT * FROM oc_product_discount WHERE product_id='".(int)$product_id."' AND customer_group_id=8 AND quantity=10 ");
					if($check)
					{
						$db->db_exec("UPDATE oc_product_discount SET price = '".$default_10."' WHERE product_id='".(int)$product_id."' AND customer_group_id=8 AND quantity=10");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$default_10."', product_id='".(int)$product_id."' , customer_group_id=8 , quantity=10");	
						
					}
					if ($i == 0) {
						$bits .= 'Default Qty 3' . ', ';
					}
					
				}
				
				
				if($local_1 and $_POST['for_lq1'])
				{
					$check = 	$db->func_query_first("SELECT * FROM oc_product_discount WHERE product_id='".(int)$product_id."' AND customer_group_id=10 AND quantity=1 ");
					if($check)
					{
						$db->db_exec("UPDATE oc_product_discount SET price = '".$local_1."' WHERE product_id='".(int)$product_id."' AND customer_group_id=10 AND quantity=1");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$local_1."', product_id='".(int)$product_id."' , customer_group_id=10 , quantity=1");	
						
					}
					if ($i == 0) {
						$bits .= 'Local Qty 1' . ', ';
					}
					
				}
				
				if($local_3 and $_POST['for_lq3'])
				{
					$check = 	$db->func_query_first("SELECT * FROM oc_product_discount WHERE product_id='".(int)$product_id."' AND customer_group_id=10 AND quantity=3 ");
					if($check)
					{
						$db->db_exec("UPDATE oc_product_discount SET price = '".$local_3."' WHERE product_id='".(int)$product_id."' AND customer_group_id=10 AND quantity=3");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$local_3."', product_id='".(int)$product_id."' , customer_group_id=10 , quantity=3");	
						
					}
					if ($i == 0) {
						$bits .= 'Local Qty 3' . ', ';
					}
					
				}
				
				if($local_10 and $_POST['for_lq10'])
				{
					$check = 	$db->func_query_first("SELECT * FROM oc_product_discount WHERE product_id='".(int)$product_id."' AND customer_group_id=10 AND quantity=10 ");
					if($check)
					{
						$db->db_exec("UPDATE oc_product_discount SET price = '".$local_10."' WHERE product_id='".(int)$product_id."' AND customer_group_id=10 AND quantity=10");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$local_10."', product_id='".(int)$product_id."' , customer_group_id=10 , quantity=10");	
						
					}

					if ($i == 0) {
						$bits .= 'Local Qty 3' . ', ';
					}
					
				}
				
				
				
				if($ws_1 and $_POST['for_wsq1'])
				{
					$check = 	$db->func_query_first("SELECT * FROM oc_product_discount WHERE product_id='".(int)$product_id."' AND customer_group_id=6 AND quantity=1 ");
					if($check)
					{
						$db->db_exec("UPDATE oc_product_discount SET price = '".$ws_1."' WHERE product_id='".(int)$product_id."' AND customer_group_id=6 AND quantity=1");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$ws_1."', product_id='".(int)$product_id."' , customer_group_id=6 , quantity=1");	
						
					}
					if ($i == 0) {
						$bits .= 'WS Qty 1' . ', ';
					}
					
				}
				
				if($ws_3 and $_POST['for_wsq3'])
				{
					$check = 	$db->func_query_first("SELECT * FROM oc_product_discount WHERE product_id='".(int)$product_id."' AND customer_group_id=6 AND quantity=3 ");
					if($check)
					{
						$db->db_exec("UPDATE oc_product_discount SET price = '".$ws_3."' WHERE product_id='".(int)$product_id."' AND customer_group_id=6 AND quantity=3");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$ws_3."', product_id='".(int)$product_id."' , customer_group_id=6 , quantity=3");	
						
					}
					if ($i == 0) {
						$bits .= 'WS Qty 3' . ', ';
					}
					
				}
				
				if($ws_10 and $_POST['for_wsq10'])
				{
					$check = 	$db->func_query_first("SELECT * FROM oc_product_discount WHERE product_id='".(int)$product_id."' AND customer_group_id=6 AND quantity=10 ");
					if($check)
					{
						$db->db_exec("UPDATE oc_product_discount SET price = '".$ws_10."' WHERE product_id='".(int)$product_id."' AND customer_group_id=6 AND quantity=10");	
						
					}
					else
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$ws_10."', product_id='".(int)$product_id."' , customer_group_id=6 , quantity=10");	
						
					}
					if ($i == 0) {
						$bits .= 'WS Qty 10' . ', ';
					}
					
				}
				
				if($silver1 and $_POST['for_silver'])
				{
					$db->db_exec("delete from oc_product_discount WHERE product_id='".(int)$product_id."' and customer_group_id=1631 ");
					
					$db->db_exec("INSERT INTO oc_product_discount SET price = '".$silver1."', product_id='".(int)$product_id."' , customer_group_id=1631 , quantity=1");
					if($silver3)
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$silver3."', product_id='".(int)$product_id."' , customer_group_id=1631 , quantity=3");
					}

					if($silver10)
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$silver10."', product_id='".(int)$product_id."' , customer_group_id=1631 , quantity=10");	
					}
					
					if ($i == 0) {
						$bits .= 'Silver Tier' . ', ';
					}
				}
				
				if($gold1 and $_POST['for_gold'])
				{
					
					$db->db_exec("delete from oc_product_discount WHERE product_id='".(int)$product_id."' and customer_group_id=1632 ");
					
					$db->db_exec("INSERT INTO oc_product_discount SET price = '".$gold1."', product_id='".(int)$product_id."' , customer_group_id=1632 , quantity=1");
					if($gold3)
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$gold3."', product_id='".(int)$product_id."' , customer_group_id=1632 , quantity=3");
					}
					if($gold10)
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$gold10."', product_id='".(int)$product_id."' , customer_group_id=1632 , quantity=10");	
						
					}
					if ($i == 0) {
						$bits .= 'Gold Tier' . ', ';
					}
					
				}
				
				if($platinum1 and $_POST['for_platinum'])
				{
					
					
					$db->db_exec("delete from oc_product_discount WHERE product_id='".(int)$product_id."' and customer_group_id=1633 ");
					
					$db->db_exec("INSERT INTO oc_product_discount SET price = '".$platinum1."', product_id='".(int)$product_id."' , customer_group_id=1633 , quantity=1");
					if($platinum3)
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$platinum3."', product_id='".(int)$product_id."' , customer_group_id=1633 , quantity=3");
					}
					if($platinum10)
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$platinum10."', product_id='".(int)$product_id."' , customer_group_id=1633 , quantity=10");	
					}
					if ($i == 0) {
						$bits .= 'Platinum Tier' . ', ';
					}
					
					
				}
				
				if($diamond1 and $_POST['for_diamond'])
				{
					
					
					$db->db_exec("delete from oc_product_discount WHERE product_id='".(int)$product_id."' and customer_group_id=1634 ");
					
					
					$db->db_exec("INSERT INTO oc_product_discount SET price = '".$diamond1."', product_id='".(int)$product_id."' , customer_group_id=1634 , quantity=1");
					if($diamond3)
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$diamond3."', product_id='".(int)$product_id."' , customer_group_id=1634 , quantity=3");
					}
					if($diamond10)
					{
						$db->db_exec("INSERT INTO oc_product_discount SET price = '".$diamond10."', product_id='".(int)$product_id."' , customer_group_id=1634 , quantity=10");	
					}
					if ($i == 0) {
						$bits .= 'Diamond Tier' . ', ';
					}
					
					
				}
				
				
				
				
				
			}
			
			$i++;
		}

		$log .= " and " . $bits . ' Has updated';

		if(!$_SESSION['message']){
			$_SESSION['message'] = 'Products uploaded successfully.';
			actionLog($log);
		}
		echo "<script>window.close();parent.window.location.reload();</script>";
		exit;
	}
	else{
		$_SESSION['message'] = 'Uploaded file is not valid, try again';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Import Products</title>
</head>
<body>
	<div class="div-fixed">
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<div align="center">
			<a href="<?php echo $host_path;?>/csvfiles/products.csv">Download Sample</a>
			<form method="post" action="" enctype="multipart/form-data">
				<table cellpadding="10" cellspacing="0">
					<tr>
						<td>File:</td>
						<td colspan="3">
							<input type="file" name="products" required value="" />
						</td>
					</tr>
					<tr>


						<td>Item Name:</td>
						<td><input type="checkbox" name="for_item" checked="checked" /></td>

						<td>True Cost Fields</td>
						<td><input type="checkbox" name="for_cost" checked="checked" /></td>
					</tr>
					<tr>
						<td >Retail Price:</td>
						<td><input type="checkbox" name="for_retail" checked="checked" /></td>
						<td >Product Price:</td>
						<td><input type="checkbox" name="for_price" checked="checked" /></td>
					</tr>
					<tr>
						<td>Default Qty 1:</td>
						<td><input type="checkbox" name="for_dq1" checked="checked" /></td>

						<td>Default Qty 3:</td>
						<td><input type="checkbox" name="for_dq3" checked="checked" /></td>
					</tr>

					<tr>
						<td>Default Qty 10:</td>
						<td><input type="checkbox" name="for_dq10" checked="checked" /></td>

						<td>Local Qty 1:</td>
						<td><input type="checkbox" name="for_lq1" checked="checked" /></td>
					</tr>
					<tr>
						<td>Local Qty 3:</td>
						<td><input type="checkbox" name="for_lq3" checked="checked" /></td>

						<td>Local Qty 10:</td>
						<td><input type="checkbox" name="for_lq10" checked="checked" /></td>
					</tr>

					<tr>
						<td>WS Qty 1:</td>
						<td><input type="checkbox" name="for_wsq1" checked="checked" /></td>

						<td>WS Qty 3:</td>
						<td><input type="checkbox" name="for_wsq3" checked="checked" /></td>
					</tr>

					<tr>
						<td>WS Qty 10:</td>
						<td><input type="checkbox" name="for_wsq10" checked="checked" /></td>
						<td>Silver Tier:</td>
						<td><input type="checkbox" name="for_silver" checked="checked" /></td>
					</tr>
					<tr>
						<td>Gold Tier:</td>
						<td><input type="checkbox" name="for_gold" checked="checked" /></td>

						<td>Platinum Tier:</td>
						<td><input type="checkbox" name="for_platinum" checked="checked" /></td>
					</tr>
					<tr>
						<td>Diamond Tier:</td>
						<td><input type="checkbox" name="for_diamond" checked="checked" /></td>
						<td>Grade A:</td>
						<td><input type="checkbox" name="for_grade_a" checked="checked" /></td>
					</tr>

					<tr>
						<td>Grade B:</td>
						<td><input type="checkbox" name="for_grade_b" checked="checked" /></td>

						<td>Grade C:</td>
						<td><input type="checkbox" name="for_grade_c" checked="checked" /></td>
					</tr>

					<tr>
						<td>Gadget Fix URL:</td>
						<td><input type="checkbox" name="for_gadgetfix" checked="checked" /></td>

						<td>Ebay URL:</td>
						<td><input type="checkbox" name="for_ebay" checked="checked" /></td>
					</tr>
					<tr>
						<td>Mengtor URL:</td>
						<td><input type="checkbox" name="for_mengtor" checked="checked" /></td>

						<td>Mobile Defenders URL:</td>
						<td><input type="checkbox" name="for_mobile_defenders" checked="checked" /></td>
					</tr>
					<tr>
						<td>Update Kit:</td>
						<td><input type="checkbox" name="for_kit" checked="checked" /></td>

						<td>Vendor</td>
						<td><input type="checkbox" name="for_vendor" checked="checked" /></td>

					</tr>

					<tr>
						<td>Class:</td>
						<td><input type="checkbox" name="for_class" checked="checked" /></td>
						<td></td>
						<td></td>
<!--                     <td>Sub Class</td>
	<td><input type="checkbox" name="for_sub_class" checked="checked" /></td>-->

</tr>


<tr>
	<td>Status:</td>
	<td><input type="checkbox" name="for_status" checked="checked" /></td>

	<td> </td>
	<td> </td>

</tr>







<tr>
	<td align="center" colspan="4">
		<input type="submit" name="upload" value="Upload" />
	</td>
</tr>
</table>
</form>
</div>
</div>
</body>
</html>  	 