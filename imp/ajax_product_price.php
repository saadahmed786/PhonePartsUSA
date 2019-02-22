<?php

require_once("auth.php");

require_once("inc/functions.php");

$sku = $db->func_escape_string($_POST['sku']);

$customer_group_id = $db->func_escape_string($_POST['customer_group_id']);

// $customer_group_id = '1633'; // force assigning the customer group of platinum 1633	

$qty = $db->func_escape_string($_POST['qty']);

$product_query = $db->func_query_first("SELECT * FROM oc_product p LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id) WHERE p.sku = '" . $sku . "' AND pd.language_id = '1' AND p.date_available <= NOW() AND p.status = '1'");



if(empty($product_query))

{

	$product_query = $db->func_query_first("SELECT qty as quantity,0.00 as price,0 as product_id FROM inv_kit_skus WHERE kit_sku='".$sku."'");



}



$json = array();

if($_POST['action']=='getAvailableQty')

{
	$inv_data = getInventoryDetail($sku);

	$json['success'] = $inv_data['available'];

	

	

	echo json_encode($json);;exit;

}





if($product_query)

{

	$product_id = $product_query['product_id'];

	if ($product_query['sale_price'] != '0.0000') {

		$price = $product_query['sale_price']; 

	} else {

		$price = $product_query['price'];



		$is_platinum = false;

		if(substr($product_query['model'],0,7)=='APL-001' || substr($product_query['model'],0,4)=='SRN-' || substr($product_query['model'],0,7)=='TAB-SRN'  )

		{

			$is_platinum = true;

		}

		if($is_platinum)

		{



					$customer_group_id = '1633'; // force assigning the customer group of platinum 1633

				}

				if($_POST['store_type']=='po_business')

				{

					$discount_query = $db->func_query_first("SELECT price FROM oc_product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '6' AND quantity <= '10' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW()))");

				}

				else

				{

					$discount_query = $db->func_query_first("SELECT price FROM oc_product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND quantity <= '" . (int)$qty . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW()))");

				}

				

				if($discount_query)

				{

					$price = $discount_query['price'];	

					

				}

				

				$special_query = $db->func_query_first("SELECT price FROM oc_product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ");

				

				if($special_query)

				{

					$price = $special_query['price'];	

					

				}

	}

	

	$json['success'] = $price;

}

else

{

	$json['error'] = $sku.' -  Product not found';

	

}

echo json_encode($json);

?>

