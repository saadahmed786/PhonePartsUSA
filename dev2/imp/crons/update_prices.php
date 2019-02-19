<?php

use Bigcommerce\Api\Client as Bigcommerce;

include_once '../config.php';

$total = $db->func_query_first_cell("select count(*) from inv_product_prices");
$db->db_exec("update inv_prices_cron set total  = '$total' , last_cron_date = '".date('Y-m-d H:i:s')."'");

$result = $db->func_query_first("select last_id , total from inv_prices_cron");
if($result['last_id'] >= $total){
	$start = 0;
}
else{
	$start = $result['last_id'];
}

$marketplaces = explode(",",$_GET['marketplaces']);

include_once ("../CA/ca_keys.php");
include_once '../CA/ChannelAdvisor.php';

$ChannelAdvisor  = new ChannelAdvisor ( $DEV_KEY, $Password, $AccountID, 'MM' );
$ChannelAdvisor1 = new ChannelAdvisor ( $DEV_KEY, $Password, $accounts [1] ['AccountID'], $accounts [1] ['Prefix'] );
$ChannelAdvisor2 = new ChannelAdvisor ( $DEV_KEY, $Password, $accounts [2] ['AccountID'], $accounts [2] ['Prefix'] );

include_once ("../Bonanza/bonanza_keys.php");
include_once '../Bonanza/Bonanza.php';

$Bonanza = new Bonanza ();
$Bonanza->setCredential ( $dev_key, $cert_key );

include_once '../Bigcommerce/big_keys.php';
include_once '../Bigcommerce/bigcommerce-api-php-master/vendor/autoload.php';

Bigcommerce::configure ( array (
			'store_url' => 'https://www.replacementlcds.com',
			'username' => $username,
			'api_key' => $api_token 
));

Bigcommerce::setCipher ( 'RC4-SHA' );
Bigcommerce::verifyPeer ( false );
Bigcommerce::failOnError ();

$log_filename = "logs/price_updates_".date('Ymd').".txt";

for($i=$start; $i < $total; $i += 50){
	$prices = $db->func_query("select * from inv_product_prices limit $i , 50");

	$result = array();
	$ca_prices  = array();
	$ca_prices1 = array();
	$ca_prices2 = array();

	foreach($prices as $index => $price){
		$big_product_id = $db->func_query_first_cell ( "select product_id from bigcommerce_mappings where product_sku = '".$price['product_sku']."'" );

		if($price['channel_advisor_new'] > 0 && in_array("channel_advisor", $marketplaces)){
			$ca_prices[] = array("sku" => $price['product_sku'],"price" => $price['channel_advisor_new']);
		}

		if($price['channel_advisor1_new'] > 0 && in_array("channel_advisor1", $marketplaces)){
			$ca_prices1[] = array("sku" => $price['product_sku'],"price" => $price['channel_advisor1_new']);
		}

		if($price['channel_advisor2_new'] > 0 && in_array("channel_advisor2", $marketplaces)){
			$ca_prices2[] = array("sku" => $price['product_sku'],"price" => $price['channel_advisor2_new']);
		}

		if($price['bigcommerce_new'] > 0 && in_array("bigcommerce", $marketplaces)){
			try {
				$object = new stdClass ();
				$object->sale_price = $price['bigcommerce_new'];
				$response = Bigcommerce::updateProduct ( $big_product_id, $object );
				$result[$price['product_sku']]['bigcommerce'] = "Success";
			}
			catch ( Exception $e ) {
				$result[$price['product_sku']]['bigcommerce'] = $e->getMessage ();
			}
		}

		if($price['bigcommerce_retail_new'] > 0 && in_array("bigcommerce_retail", $marketplaces)){
			try {
				$object = new stdClass ();
				$object->retail_price = $price['bigcommerce_retail_new'];
				$response = Bigcommerce::updateProduct ( $big_product_id, $object );
				$result[$price['product_sku']]['bigcommerce_retail'] = "Success";
			}
			catch ( Exception $e ) {
				$result[$price['product_sku']]['bigcommerce_retail'] = $e->getMessage ();
			}
		}

		if($price['bonanza_new'] > 0 && in_array("bonanza", $marketplaces)){
			try {
				$response = $Bonanza->updateProductPrice ( $price['product_sku'], $price['bonanza_new'], $auth_token );
				$result[$price['product_sku']]['bonanza']   = "Success";
			}
			catch ( Exception $e ) {
				$result[$price['product_sku']]['bonanza'] = $e->getMessage ();
			}
		}

		$count = $i + $index;
		$db->db_exec("update inv_prices_cron set last_id = '$count' , last_cron_date = '".date('Y-m-d H:i:s')."'");
	}

	if($ca_prices){
		$result['channel_advisor']  = $ChannelAdvisor->UpdateInventoryPriceList ( $ca_prices );
	}

	if($ca_prices1){
		$result['channel_advisor1'] = $ChannelAdvisor1->UpdateInventoryPriceList ( $ca_prices1 );
	}

	if($ca_prices2){
		$result['channel_advisor2'] = $ChannelAdvisor2->UpdateInventoryPriceList ( $ca_prices2 );
	}

	file_put_contents($log_filename, print_r($result,true), FILE_APPEND);
}

echo "success";