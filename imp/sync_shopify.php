<?php
require_once("config.php");
require_once("inc/functions.php");
include_once 'inc/shopify.php';
$product_data = $db->func_query("SELECT p.product_id,p.sku,p.quantity,p.image,p.price,p.sale_price,p.shopify_compare_price,p.shopify_price, pd.name , pd.description FROM oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) WHERE p.is_shopify = '1' AND p.is_shopify_uploaded = '0' limit 30");

foreach ($product_data as $product) {
	$product_img = str_replace('/imp', '', $host_path). 'image/' . $product['image'];
	$added_product = addShopifyProduct($product);
	$p_id = $added_product['product']['id'];

	$shopify_data = array();
	$shopify_data['shopify_product_id'] = $p_id;
	$check = $db->func_query_first("SELECT * from inv_shopify where sku = '".$product['sku']."' ");
	if ($check['id']) {
		$db->func_array2update("inv_shopify", $shopify_data, "id = '". $check['id'] ."'");		
	} else {
		$shopify_data['sku'] = $product['sku'];
		$db->func_array2insert("inv_shopify", $shopify_data);
	}

	addShopifyProductImg($p_id,$product_img);
	if ($product['shopify_price'] != '0.0000' && $product['shopify_compare_price'] != '0.0000' ) {
		updateShopifyProductPrice($p_id,$product['shopify_price'],$product['shopify_compare_price']);
	} else if($product['sale_price'] != '0.0000'){
		updateShopifyProductPrice($p_id,$product['sale_price'],$product['price']);
	} else {
		updateShopifyProductPrice($p_id,$product['price'],$product['sale_price']);
	}
	updateShopifyProductSku($p_id,$product['sku']);
	updateShopifyProductInventoryStatus($p_id);
	updateShopifyProductQty($p_id,$product['quantity']);
	$db->db_exec('UPDATE oc_product SET is_shopify_uploaded = "1" WHERE product_id = "'. (int)$product['product_id'] .'"');
}
$_SESSION['message'] = 'Products Uploaded to Shopify Sucessfully';
header("Location:" . $host_path . "products.php");
exit;
echo "Success";
?>