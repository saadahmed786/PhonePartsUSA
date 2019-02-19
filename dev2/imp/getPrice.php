<?php
include_once ("auth.php");
set_time_limit ( 0 );
ini_set ( "memory_limit", "1024M" );

error_reporting ( E_ALL & ~ E_NOTICE & ~ E_STRICT & ~ E_DEPRECATED );

$product_sku = $db->func_escape_string ( $_REQUEST ['product_sku'] );

use Bigcommerce\Api\Client as Bigcommerce;

use Wish\WishClient;

$isExist = $db->func_query_first_cell ( "select id from inv_product_prices where product_sku = '$product_sku'" );
if (! $isExist) {
	$db->db_exec ( "insert into inv_product_prices set product_sku = '$product_sku'" );
}

if ($_REQUEST ['market'] == 'amazon') {
	include_once ("amazon/amazon_config.php");
	include_once 'amazon/AmazonAPI.php';
	
	$merchantInfo = $db->func_query_first ( "Select id ,merchant_id,market_place_id, last_cron_date from amazon_credential order by dateofmodifications DESC" );
	if (! @$merchantInfo) {
		echo "No merchant exist";
		exit ();
	}
	
	$amazon_credential_id = $merchantInfo ['id'];
	$merchant_id = $merchantInfo ['merchant_id'];
	$market_place_id = $merchantInfo ['market_place_id'];
	
	$amazonAPI = new AmazonAPI ( $market_place_id );
	$response = $amazonAPI->getProductPrice ( $merchant_id, $product_sku );
}
elseif ($_REQUEST ['market'] == 'channel_advisor') {
	include_once ("CA/ca_keys.php");
	include_once 'CA/ChannelAdvisor.php';
	
	$ChannelAdvisor = new ChannelAdvisor ( $DEV_KEY, $Password, $AccountID, 'MM' );
	$response = $ChannelAdvisor->GetInventoryItemList ( $product_sku );
	
	if ($response ['GetInventoryItemListResult'] ['Status'] == 'Success') {
		//$price = $response ['GetInventoryItemListResult'] ['ResultData'] ['InventoryItemResponse'] ['PriceInfo'] ['TakeItPrice'];
		$price = $response ['GetInventoryItemListResult'] ['ResultData'] ['InventoryItemResponse'] ['PriceInfo'] ['RetailPrice'];
		
		$db->db_exec ( "update inv_product_prices SET channel_advisor = '$price' , channel_advisor_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
	}
	else {
	}
}
elseif ($_REQUEST ['market'] == 'channel_advisor1') {
	include_once ("CA/ca_keys.php");
	include_once 'CA/ChannelAdvisor.php';
	
	$ChannelAdvisor = new ChannelAdvisor ( $DEV_KEY, $Password, $accounts [1] ['AccountID'], $accounts [1] ['Prefix'] );
	$response = $ChannelAdvisor->GetInventoryItemList ( $product_sku );
	
	if ($response ['GetInventoryItemListResult'] ['Status'] == 'Success') {
		$price = $response ['GetInventoryItemListResult'] ['ResultData'] ['InventoryItemResponse'] ['PriceInfo'] ['TakeItPrice'];
		//$retail_price = $response ['GetInventoryItemListResult'] ['ResultData'] ['InventoryItemResponse'] ['PriceInfo'] ['RetailPrice'];
		
		$db->db_exec ( "update inv_product_prices SET channel_advisor1 = '$price' , channel_advisor1_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
	}
	else {
	}
}
elseif ($_REQUEST ['market'] == 'channel_advisor2') {
	include_once ("CA/ca_keys.php");
	include_once 'CA/ChannelAdvisor.php';
	
	$ChannelAdvisor = new ChannelAdvisor ( $DEV_KEY, $Password, $accounts [2] ['AccountID'], $accounts [2] ['Prefix'] );
	$response = $ChannelAdvisor->GetInventoryItemList ( $product_sku );
	
	if ($response ['GetInventoryItemListResult'] ['Status'] == 'Success') {
		$price = $response ['GetInventoryItemListResult'] ['ResultData'] ['InventoryItemResponse'] ['PriceInfo'] ['TakeItPrice'];
		//$retail_price = $response ['GetInventoryItemListResult'] ['ResultData'] ['InventoryItemResponse'] ['PriceInfo'] ['RetailPrice'];
		
		$db->db_exec ( "update inv_product_prices SET channel_advisor2 = '$price' , channel_advisor2_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
	}
	else {
	}
}
elseif ($_REQUEST ['market'] == 'ebay') {
	include_once ("ebay/session.php");
	include_once ("ebay/eb_config.php");
	include_once ("ebay/ebAPI.php");
	
	$ebAPI = new ebAPI ();
	$response = $ebAPI->getItem ( userToken, $product_sku );
}
elseif ($_REQUEST ['market'] == 'bigcommerce' || $_REQUEST ['market'] == 'bigcommerce_retail') {
	include_once 'Bigcommerce/big_keys.php';
	include_once 'Bigcommerce/bigcommerce-api-php-master/vendor/autoload.php';
	
	Bigcommerce::configure ( array (
			'store_url' => 'https://www.replacementlcds.com',
			'username' => $username,
			'api_key' => $api_token 
	) );
	
	Bigcommerce::setCipher ( 'RC4-SHA' );
	Bigcommerce::verifyPeer ( false );
	Bigcommerce::failOnError ();
	
	$product_id = $db->func_query_first_cell ( "select product_id from bigcommerce_mappings where product_sku = '$product_sku'" );
	try {
		$response = Bigcommerce::getProduct ( $product_id );
		$price = ($_REQUEST ['market'] == 'bigcommerce_retail') ? $response->retail_price : $response->sale_price;
		
		if ($_REQUEST ['market'] == 'bigcommerce_retail') {
			$db->db_exec ( "update inv_product_prices SET bigcommerce_retail = '$price' , bigcommerce_retail_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
		}
		else {
			$db->db_exec ( "update inv_product_prices SET bigcommerce = '$price' , bigcommerce_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
		}
	}
	catch ( Exception $e ) {
		$price = $e->getMessage ();
	}
}
elseif ($_REQUEST ['market'] == 'bonanza') {
	include_once ("Bonanza/bonanza_keys.php");
	include_once 'Bonanza/Bonanza.php';
	
	$Bonanza = new Bonanza ();
	$Bonanza->setCredential ( $dev_key, $cert_key );
	
	try {
		$response = $Bonanza->getProductPrice ( $product_sku );
		$price = $response ['item'] ['buyItNowPrice'];
		
		$db->db_exec ( "update inv_product_prices SET bonanza = '$price' , bonanza_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
	}
	catch ( Exception $e ) {
		$price = $e->getMessage ();
	}
}
elseif ($_REQUEST ['market'] == 'wish') {
	include_once 'Wish/keys.php';
	include_once 'Wish/Wish-Merchant-API-master/vendor/autoload.php';
	
	$client = new WishClient ( $api_token, 'prod' );
	try {
		$response = $client->getProductBySKU ( $product_sku );
		$price = $response->variants [0]->price;
		
		$db->db_exec ( "update inv_product_prices SET wish = '$price' , wish_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
	}
	catch ( Exception $e ) {
		$price = $e->getMessage ();
	}
}
elseif ($_REQUEST ['market'] == 'open_sky') {
}

print_r ( $price );