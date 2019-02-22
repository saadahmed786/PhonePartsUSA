<?php
ini_set("memory_limit",-1);
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$filename = 'InvQtys.csv';
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
		// echo $i;exit;
		foreach($products as $product)
		{
			$sku = trim($product['PartNumber']);
			$qty = trim($product['Qty']);
			// $check = $db->func_query_first("SELECT sku from inv_buy_back where trim(sku)='".trim($product['SKU'])."'");
			// if(!$check)
			// {
				// $db->db_exec("INSERT INTO inv_buy_back SET sku='$sku',description='$item_name',weight=0.00,unit='oz'");
				$db->db_exec("update oc_product SET quantity='$qty' WHERE trim(sku)='".trim($sku)."'");
				// $available[] = createSKU($sku, $item_name, $item_name, 0.00, '',1, '', '', 1) ;
				// echo $sku.'- '.$qty;exit;
			// }
		}
		echo 'success';
		// print_r($available);
?>