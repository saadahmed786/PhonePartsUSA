<?php

use Bigcommerce\Api\Client as Bigcommerce;
use Wish\WishClient;
use Wish\Model\WishProductVariation;

include_once ("auth.php");
error_reporting ( E_ALL & ~ E_NOTICE & ~ E_STRICT & ~ E_DEPRECATED );

$product_sku = $_REQUEST ['product_sku']; // SRN-LGM-224';
$product_price = $_REQUEST ['product_price']; // SRN-LGM-224';

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
}
elseif ($_REQUEST ['market'] == 'channel_advisor') {
	include_once ("CA/ca_keys.php");
	include_once 'CA/ChannelAdvisor.php';
	
	$ChannelAdvisor = new ChannelAdvisor ( $DEV_KEY, $Password, $AccountID, 'MM' );
	$response = $ChannelAdvisor->UpdateInventoryPriceList ( array (array("sku" => $product_sku,"price" => $product_price)));
	
	if ($response ['UpdateInventoryItemQuantityAndPriceListResult'] ['Status'] == 'Success') {
		$result = "Success";
		$db->db_exec ( "update inv_product_prices SET channel_advisor = '$product_price' , channel_advisor_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
	}
	else {
		$result = $response; //$response ['UpdateInventoryItemQuantityAndPriceListResult'];
	}
}
elseif ($_REQUEST ['market'] == 'channel_advisor1') {
	include_once ("CA/ca_keys.php");
	include_once 'CA/ChannelAdvisor.php';
	
	$ChannelAdvisor = new ChannelAdvisor ( $DEV_KEY, $Password, $accounts [1] ['AccountID'], $accounts [1] ['Prefix'] );
	$response = $ChannelAdvisor->UpdateInventoryPriceList ( array (array("sku" => $product_sku,"price" => $product_price)) , $accounts [1] ['Prefix']);
	
	if ($response ['UpdateInventoryItemQuantityAndPriceListResult'] ['Status'] == 'Success') {
		$result = "Success";
		$db->db_exec ( "update inv_product_prices SET channel_advisor1 = '$product_price' , channel_advisor1_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
	}
	else {
		$result = $response ['UpdateInventoryItemQuantityAndPriceListResult'];
	}
}
elseif ($_REQUEST ['market'] == 'channel_advisor2') {
	include_once ("CA/ca_keys.php");
	include_once 'CA/ChannelAdvisor.php';
	
	$ChannelAdvisor = new ChannelAdvisor ( $DEV_KEY, $Password, $accounts [2] ['AccountID'], $accounts [2] ['Prefix'] );
	$response = $ChannelAdvisor->UpdateInventoryPriceList ( array (array("sku" => $product_sku,"price" => $product_price)) , $accounts [2] ['Prefix']);
	
	if ($response ['UpdateInventoryItemQuantityAndPriceListResult'] ['Status'] == 'Success') {
		$result = "Success";
		$db->db_exec ( "update inv_product_prices SET channel_advisor2 = '$product_price' , channel_advisor2_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
	}
	else {
		$result = $response ['UpdateInventoryItemQuantityAndPriceListResult'];
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
		$object = new stdClass ();
		if($_REQUEST ['market'] == 'bigcommerce_retail'){
			$object->retail_price = $product_price;
			$db->db_exec ( "update inv_product_prices SET bigcommerce_retail = '$product_price' , bigcommerce_retail_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
		}
		else{
			$object->sale_price = $product_price;
			$db->db_exec ( "update inv_product_prices SET bigcommerce = '$product_price' , bigcommerce_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
		}
		
		$response = Bigcommerce::updateProduct ( $product_id, $object );
		$result = "Success";
	}
	catch ( Exception $e ) {
		$result = $e->getMessage ();
	}
}
elseif ($_REQUEST ['market'] == 'bonanza') {
	include_once ("Bonanza/bonanza_keys.php");
	include_once 'Bonanza/Bonanza.php';
	
	$Bonanza = new Bonanza ();
	$Bonanza->setCredential ( $dev_key, $cert_key );
	
	try {
		$response = $Bonanza->updateProductPrice ( $product_sku, $product_price, $auth_token );
		$result   = "Success";
		$db->db_exec ( "update inv_product_prices SET bonanza = '$product_price' , bonanza_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
	}
	catch ( Exception $e ) {
		$result = $e->getMessage ();
	}
}
elseif ($_REQUEST ['market'] == 'wish') {
	include_once 'Wish/keys.php';
	include_once 'Wish/Wish-Merchant-API-master/vendor/autoload.php';
	
	$client = new WishClient ( $api_token, 'prod' );
	try {
		$variant = $client->getProductVariationBySKU($product_sku);
		$variant->price = $product_price;
		$result = $client->updateProductVariation($variant);
		
		$db->db_exec ( "update inv_product_prices SET wish = '$product_price' , wish_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
	}
	catch ( Exception $e ) {
		$result = $e->getMessage ();
	}
}
elseif ($_REQUEST ['market'] == 'open_sky') {
}

print_r ( $result );