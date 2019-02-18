<?php
require_once("auth.php");
$order_id = $_POST['order_id'];
$items = rtrim($_POST['items'],",");
$items = explode(",",$items);

$data = $db->func_query_first("SELECT * FROM oc_order WHERE order_id='".(int)$order_id."'");



$array = array();
		$array['invoice_prefix'] = $data['invoice_prefix'];
		$array['store_id'] = $data['store_id'];
		$array['store_name'] = $data['store_name'];
		$array['store_url'] = $data['store_url'];
		$array['customer_id'] = $data['customer_id'];
		$array['customer_group_id'] = $data['customer_group_id'];
		$array['firstname'] = $data['firstname'];
		$array['lastname'] = $data['lastname'];
		$array['email'] = $data['email'];
		$array['telephone'] = $data['telephone'];
		$array['fax'] = $data['fax'];
		$array['payment_firstname'] = $data['payment_firstname'];
		$array['payment_lastname'] = $data['payment_lastname'];
		$array['payment_company'] = $data['payment_company'];
		$array['payment_company_id'] = $data['payment_company_id'];
		$array['payment_tax_id'] = $data['payment_tax_id'];
		$array['payment_address_1'] = $data['payment_address_1'];
		$array['payment_address_2'] = $data['payment_address_2'];
		$array['payment_city'] = $data['payment_city'];
		$array['payment_postcode'] = $data['payment_postcode'];
		$array['payment_country'] = $data['payment_country'];
		$array['payment_country_id'] = $data['payment_country_id'];
		$array['payment_zone'] = $data['payment_zone'];
		$array['payment_zone_id'] = $data['payment_zone_id'];
		$array['payment_address_format'] = $data['payment_address_format'];
		$array['payment_method'] = $data['payment_method'];
		$array['payment_code'] = $data['payment_code'];
		$array['shipping_firstname'] = $data['shipping_firstname'];
		$array['shipping_lastname'] = $data['shipping_lastname'];
		$array['shipping_company'] = $data['shipping_company'];
		$array['shipping_address_1'] = $data['shipping_address_1'];
		$array['shipping_address_2'] = $data['shipping_address_2'];
		$array['shipping_city'] = $data['shipping_city'];
		$array['shipping_postcode'] = $data['shipping_postcode'];
		$array['shipping_country'] = $data['shipping_country'];
		$array['shipping_country_id'] = $data['shipping_country_id'];
		$array['shipping_zone'] = $data['shipping_zone'];
		$array['shipping_zone_id'] = $data['shipping_zone_id'];
		$array['shipping_address_format'] = $data['shipping_address_format'];
		$array['shipping_method'] = $data['shipping_method'];
		$array['shipping_code'] = $data['shipping_code'];
		$array['comment'] = $_POST['message'];
		$array['order_status_id'] = $data['order_status_id'];
		$array['affiliate_id'] = $data['affiliate_id'];
		$array['language_id'] = $data['language_id'];
		$array['currency_id'] = $data['currency_id'];
		$array['currency_code'] = $data['currency_code'];
		$array['currency_value'] = $data['currency_value'];
		$array['admin_view_only'] = $data['admin_view_only'];
		$array['date_added'] = date('Y-m-d H:i:s');
		$array['date_modified'] = date('Y-m-d H:i:s');
		$array['admin_view_only']=1;
		$array['order_status_id'] = 21 ; //default on hold status
		
		$xorder_id = $db->func_array2insert("oc_order",$array);
		
		
		foreach($items as $item)
		{
			
			$return_info = $db->func_query_first("SELECT
a.*,
b.sku,b.title,b.`quantity`,b.price,b.`return_code`,b.`reason`,b.`decision`,b.return_id
FROM
    `inv_returns` a
    INNER JOIN `inv_return_items`  b
        ON (a.`id` = b.`return_id`) 
		
		WHERE b.id='".(int)$item."'
		
		");
		
		//$order_product = $db->func_query_first("SELECT * FROM oc_order_product WHERE model='".$return_info['sku']."' AND order_id='".$return_info['order_id']."'");
		
		$order_product = $db->func_query_first("SELECT a.*,b.name FROM oc_product a,oc_product_description b WHERE a.product_id=b.product_id AND a.model='".$return_info['sku']."'");
		
			
		$db->db_exec("INSERT INTO oc_order_product SET order_id = '" . (int)$xorder_id . "', product_id = '" . (int)$order_product['product_id'] . "', name = '" . $order_product['name'] . "', model = '" . ($order_product['model']) . "', quantity = '" . (int)1 . "', price = '" . (float)$return_info['price'] . "', total = '" . (float)$return_info['price'] . "', tax = '" . (float)$order_product['tax'] . "', reward = '" . (int)$order_product['reward'] . "'");	
		
		
		
		}
if($xorder_id)
{
	
echo 'Order # '.$xorder_id.' has been created';exit;	
}
?>
