<?php

require_once("applicationTop.php");

error_reporting(0);
set_time_limit(2000);
ini_set("memory_limit", "20000M");

include_once("../config.php");

$all   = (int)$_REQUEST['all'];
$limit = (int)$_REQUEST['limit'];
$page  = (int)$_REQUEST['page'];
if(!$page){
	$page = 1;
}

if(!$limit){
	$total_url = $host_path . "/fetchProducts.php?count=1";
	$total = file_get_contents($total_url);
	$start = 0;
}
else{
	$total = $limit*$page;
	$start = ($page-1)*$limit;
}

for($i=$start; $i < $total; $i += 200){
	$product_url = $host_path . "/fetchProducts.php?start=$i&all=$all";
	print $product_url . "<br />";

	sleep(2);

	$ch = curl_init();
	curl_setopt($ch , CURLOPT_URL , $product_url);
	curl_setopt($ch , CURLOPT_TIMEOUT, 10);
	curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
	$products = curl_exec($ch);

	//$products = file_get_contents($product_url);
	if($products == 'NO'){
		break;
		exit;
	}

	$products = json_decode($products,true);
	if(is_array($products) and count($products) > 0){

		$productUpdateArray = array();
		foreach($products as $product)
		{
			$sku = $product['model'];
			if($sku){
				$qtyforSale = $fbapi->getItemQty($sku);

				if( $qtyforSale == 'FBStopped' || stristr($qtyforSale , 'FBStopped')){
					print "FB stop worked" . $qtyforSale;
					continue;
				}
				elseif($qtyforSale == 'notExist'){
					//skip the product not exist
					continue;
				}
				elseif($qtyforSale >= 0){
					$qtyforSale = (int)$qtyforSale;
					if($qtyforSale >= 0){
						$productUpdateArray[] = array('sku' => $sku , 'qty' => $qtyforSale);
					}
				}
			}
		}

		//print_r($productUpdateArray);exit;
		if($productUpdateArray and count($productUpdateArray) > 0){
			if($page != 1){
				$updateUrl = $host_path . "/updateProducts.php";
			}
			else{
				$updateUrl = $host_path . "/updateProducts.php?need_kit_sync=1";
			}

			$ch = curl_init();
			curl_setopt($ch , CURLOPT_URL , $updateUrl);
			curl_setopt($ch , CURLOPT_POST , 1);
			curl_setopt($ch , CURLOPT_POSTFIELDS , array('productUpdateArray' => json_encode($productUpdateArray)));
			curl_setopt($ch , CURLOPT_TIMEOUT, 10);
			curl_setopt($ch , CURLOPT_RETURNTRANSFER, 0);
			curl_exec($ch);
		}
	}
	else{
		break;
	}
}

echo "success";