<?php

date_default_timezone_set('America/Los_Angeles');

echo "started at - ". date('Y-m-d H:i:s') . "<br />";

$time  = date('H');
$time_11am = 11;
$time_7pm  = 19;

if($time == $time_11am || $time == $time_7pm){
	echo "Running";
}
else{
	echo "stopped.";
	//exit;
}

require_once("applicationTop.php");

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");

$product_url = $host_path . "/fishbowl/getProducts.php?start=0";
$ch = curl_init();
curl_setopt($ch , CURLOPT_URL , $product_url);
curl_setopt($ch , CURLOPT_TIMEOUT, 10);
curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
$products = curl_exec($ch);

if($products == 'NO'){
	echo "no new products";
	exit;
}

include_once 'db.php';
$sql_host = "localhost";
$sql_user = "root";
$sql_password = "";
$sql_db = "inv_manager";
$db = new Database();

$script = $db->func_query_first("select * from scripts where name != 'importProduct' and status = 1");
if( $script and $script['status'] == 1 and (time() - strtotime($script['last_time'])) < 300 ){
	echo "other script running";
	exit;
}

$db->db_exec("update scripts SET status = 1 , last_time = '".date('Y-m-d H:i:s')."' where name = 'importProduct'");

$products = json_decode($products , true);

$errorMessage = array();
$productIds = array();

$products_csv  = "<Rows>";
$products_csv .= "<Row>PartNumber,PartDescription,PartDetails,UOM,UPC,PartTypeID,Active,Taxable,StdCost,Tracks-Lot Number,Tracks-Revision Level,Tracks-Expiration Date,Tracks-Serial Number,AssetAccount,COGSAccount,AdjustmentAccount,ScrapAccount,VarianceAccount,ABCCode,Weight,WeightUOM,Width,Height,Len,SizeUOM,CF-Custom,CF-Product ID,CF-Bullet Point 1,CF-Bullet Point 2,CF-Bullet Point 3,CF-Bullet Point 4,CF-Bullet Point 5,CF-MPN,CF-MPN 2,CF-Search Keyword 1,CF-Search Keyword 2,CF-Search Keyword 3,CF-Search Keyword 4,CF-Search Keyword 5,CF-Thickness,CF-Color,CF-Condition,CF-Amazon Condition,CF-ASIN,CF-Compatible Phone 1,CF-Compatible Phone 2,CF-Compatible Phone 3,CF-Compatible Phone 4,CF-Compatible Phone 5,CF-Compatible Phone 6,CF-Brand,CF-Amazon Title,CF-eBay Title,CF-Amazon Price,CF-ChannelAdvisor Labels,CF-Amazon Condition Note,CF-Manufacturer,ProductNumber,ProductDescription,ProductDetails,Price,ProductSKU,ProductUPC,ProductActive,ProductTaxable,ProductSOItemTypeID,IncomeAccount,Vendor,DefaultVendor,VendorPartNumber,Cost,VendorUOM,CFP-Custom</Row>";

foreach($products as $product_data){
	$vendor = 'China Office';
	if(stristr($product_data['name'],"Grade")){
		$vendor = 'PPUSA';
	}

	$product = array();
	$product['PartNumber'] = $product_data['sku'];
	$product['PartDescription'] = preg_replace("/[^a-zA-Z0-9 ]/is", "", $product_data['name']);
	$product['PartDetails'] = '';
	$product['UOM'] = 'ea';
	$product['UPC'] = '';
	$product['PartTypeID'] = '10';
	$product['Active']  = 'TRUE';
	$product['Taxable'] = 'FALSE';
	$product['StdCost'] = '$0.00';
	$product['Tracks-Lot Number'] = 'FALSE';
	$product['Tracks-Revision Level']  = 'FALSE';
	$product['Tracks-Expiration Date'] = 'FALSE';
	$product['Tracks-Serial Number'] = 'FALSE';
	$product['AssetAccount'] = '';
	$product['COGSAccount']  = '';
	$product['AdjustmentAccount'] = '';
	$product['ScrapAccount']    = '';
	$product['VarianceAccount'] = '';
	$product['ABCCode']   = '';
	$product['Weight']    = '1';
	$product['WeightUOM'] = 'lbs';
	$product['Width']   = '1';
	$product['Height']  = '1';
	$product['Len']     = '';
	$product['SizeUOM'] = 'in';
	$product['CF-Custom'] = '';
	$product['CF-Product ID'] = $product_data['product_id'];
	$product['CF-Bullet Point 1'] = '';
	$product['CF-Bullet Point 2'] = '';
	$product['CF-Bullet Point 3'] = '';
	$product['CF-Bullet Point 4'] = '';
	$product['CF-Bullet Point 5'] = '';
	$product['CF-MPN'] = '';

	$product['CF-MPN 2'] = '';
	$product['CF-Search Keyword 1'] = '';
	$product['CF-Search Keyword 2'] = '';
	$product['CF-Search Keyword 3'] = '';
	$product['CF-Search Keyword 4'] = '';
	$product['CF-Search Keyword 5'] = '';
	$product['CF-Thickness'] = '';
	$product['CF-Color'] = '';
	$product['CF-Condition'] = '';
	$product['CF-Amazon Condition'] = '';
	$product['CF-ASIN'] = '';
	$product['CF-Compatible Phone 1'] = '';

	$product['CF-Compatible Phone 2'] = '';
	$product['CF-Compatible Phone 3'] = '';
	$product['CF-Compatible Phone 4'] = '';
	$product['CF-Compatible Phone 5'] = '';
	$product['CF-Compatible Phone 6'] = '';
	$product['CF-Brand'] = '';
	$product['CF-Amazon Title'] = '';
	$product['CF-eBay Title']   = '';
	$product['CF-Amazon Price'] = '';
	$product['CF-ChannelAdvisor Labels'] = '';
	$product['CF-Amazon Condition Note'] = '';
	$product['CF-Manufacturer'] = '';

	$product['ProductNumber'] = $product_data['sku'];
	$product['ProductDescription'] = preg_replace("/[^a-zA-Z0-9 ]/is", "", $product_data['name']);
	$product['ProductDetails'] = '';
	$product['Price'] = '1';
	$product['ProductSKU'] = $product_data['sku'];
	$product['ProductUPC'] = '';
	$product['ProductActive']  = 'TRUE';
	$product['ProductTaxable'] = 'FALSE';
	$product['ProductSOItemTypeID'] = '10';
	$product['IncomeAccount'] = '';
	$product['Vendor'] = $vendor;
	$product['DefaultVendor'] = 'TRUE';
	$product['VendorPartNumber'] = $product_data['sku'];
	$product['Cost'] = '1';
	$product['VendorUOM']  = 'ea';
	$product['CFP-Custom'] = '';

	$products_csv .= "<Row>".implode(",",$product)."</Row>";

	$productIds[] = $product_data['product_id'];
}

$products_csv .= "</Rows>";

//file_put_contents("test.csv",$products_csv);

//print_r($products_csv);
//exit;

$result = $fbapi->importProducts($products_csv);
//print_r($result);
//exit;

$FbiMsgsRsStatus = $result['FbiMsgsRs']['@attributes']['statusCode'];
if($result['FbiMsgsRs'][0]){
	$attributes    = $result['FbiMsgsRs'][0]->attributes();
	$SaveRsStatus  = $attributes['statusCode'];
	$SaveRsMessage = $attributes['statusMessage'];
}

print $SaveRsStatus . "----". $SaveRsMessage;

if(($productIds and count($productIds) > 0) and $SaveRsStatus == 1000){
	$productIds = implode(",",$productIds);
	$updateUrl  = $host_path . "/fishbowl/mark_added.php?success=1";
}
else{
	$productIds = implode(",",$productIds);
	$updateUrl  = $host_path . "/fishbowl/mark_added.php?success=0";
}

$ch = curl_init();
curl_setopt($ch , CURLOPT_URL , $updateUrl);
curl_setopt($ch , CURLOPT_POST , 1);
curl_setopt($ch , CURLOPT_POSTFIELDS , array('productIds' => $productIds));
curl_setopt($ch , CURLOPT_TIMEOUT, 30);
curl_exec($ch);

$db->db_exec("update scripts SET status = 0 where name = 'importProduct'");

echo "success";