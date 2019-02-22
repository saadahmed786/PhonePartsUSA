<?php
ini_set("memory_limit",-1);
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$filename = 'new_pricing.csv';
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
		// echo "<pre>";
$available = array();


foreach($products as $product)
{
	$default_1 = (float)$product['D1'];
	$default_3 = (float)$product['D3'];
	$default_10 = (float)$product['D10'];
	$local_1 = (float)$product['L1'];
	$local_3 = (float)$product['L3'];
	$local_10 = (float)$product['L10'];
	$ws_1 = (float)$product['B1'];
	$ws_3 = (float)$product['B3'];
	$ws_10 = (float)$product['B10'];

	$silver1 = (float)$product['S1'];
	$silver3 = (float)$product['S3'];
	$silver10 = (float)$product['S10'];

	$gold1 = (float)$product['G1'];
	$gold3 = (float)$product['G3'];
	$gold10 = (float)$product['G10'];

	$platinum1 = (float)$product['P1'];
	$platinum3 = (float)$product['P3'];
	$platinum10 = (float)$product['P10'];

	$diamond1 = (float)$product['Dm1'];
	$diamond3 = (float)$product['Dm3'];
	$diamond10 = (float)$product['Dm10'];
	// print_r(get_defined_vars());exit;
	$check = $db->func_query_first("SELECT product_id from oc_product where sku='".trim($product['SKU'])."'");
	$product_id = (int)$check['product_id'];
	if($check)
	{
		if((float)$product['D1']>0.00)
		{
			$db->db_exec("UPDATE oc_product SET price='".(float)$product['D1']."',ignore_up=1 where product_id='".$product_id."'");
		}
		if((float)$product['D1']>0.00)
		{

			$db->db_exec("DELETE FROM oc_product_discount WHERE product_id='".(int)$product_id."'");

			if($default_1)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$default_1."', product_id='".(int)$product_id."' , customer_group_id=8 , quantity=1");	
			}

			if($default_3)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$default_3."', product_id='".(int)$product_id."' , customer_group_id=8 , quantity=3");
			}	


			if($default_10)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$default_10."', product_id='".(int)$product_id."' , customer_group_id=8 , quantity=10");
			}



			if($local_1)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$local_1."', product_id='".(int)$product_id."' , customer_group_id=10 , quantity=1");
			}	
			if($local_3)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$local_3."', product_id='".(int)$product_id."' , customer_group_id=10 , quantity=3");
			}	
			if($local_10)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$local_10."', product_id='".(int)$product_id."' , customer_group_id=10 , quantity=10");
			}


			if($ws_1)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$ws_1."', product_id='".(int)$product_id."' , customer_group_id=6 , quantity=1");
			}	

			if($ws_3)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$ws_3."', product_id='".(int)$product_id."' , customer_group_id=6 , quantity=3");
			}	
			if($ws_10)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$ws_10."', product_id='".(int)$product_id."' , customer_group_id=6 , quantity=10");
			}	


			if($silver1)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$silver1."', product_id='".(int)$product_id."' , customer_group_id=1631 , quantity=1");
			}	
			if($silver3)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$silver3."', product_id='".(int)$product_id."' , customer_group_id=1631 , quantity=3");
			}	

			if($silver10)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$silver10."', product_id='".(int)$product_id."' , customer_group_id=1631 , quantity=10");
			}


			if($gold1)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$gold1."', product_id='".(int)$product_id."' , customer_group_id=1632 , quantity=1");
			}	
			if($gold3)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$gold3."', product_id='".(int)$product_id."' , customer_group_id=1632 , quantity=3");
			}	
			if($gold10)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$gold10."', product_id='".(int)$product_id."' , customer_group_id=1632 , quantity=10");
			}

			if($platinum1)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$platinum1."', product_id='".(int)$product_id."' , customer_group_id=1633 , quantity=1");
			}	

			if($platinum3)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$platinum3."', product_id='".(int)$product_id."' , customer_group_id=1633 , quantity=3");
			}	


			if($platinum10)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$platinum10."', product_id='".(int)$product_id."' , customer_group_id=1633 , quantity=10");
			}


			if($diamond1)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$diamond1."', product_id='".(int)$product_id."' , customer_group_id=1634 , quantity=1");
			}	
			if($diamond3)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$diamond3."', product_id='".(int)$product_id."' , customer_group_id=1634 , quantity=3");
			}	
			if($diamond10)
			{
				$db->db_exec("INSERT INTO oc_product_discount SET price = '".$diamond10."', product_id='".(int)$product_id."' , customer_group_id=1634 , quantity=10");
			}
		}
	}
}
echo 'success';

		// print_r($available);
?>