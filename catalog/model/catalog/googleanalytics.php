<?php
/**
 * Google Analytics PRO OpenCart Module
 * 
 * Adds standard and ecommerce analytics tracking to OpenCart.
 *
 * @author		Ian Gallacher (www.opencartstore.com)
 * @version		1.5.1.3
 * @support		www.opencartstore.com/support
 * @email		info@opencartstore.com
 */

class ModelCatalogGoogleAnalytics extends Model {
	/**
	 * Retrieve the Category name (string) given a product id. 
	 *
	 * @param int 
	 * @return string		
	 */
	public function get_category_name($product_id)
	{
		$query = $this->db->query("SELECT " . DB_PREFIX . "category_description.name " . 
								  "FROM " . DB_PREFIX . "category_description LEFT JOIN " . DB_PREFIX . "product_to_category ON " . DB_PREFIX . "product_to_category.category_id = " . DB_PREFIX . "category_description.category_id " . 
								  "WHERE " . DB_PREFIX . "product_to_category.product_id = '" . (int)$product_id ."'");
		
		if (isset($query->row['name'])) {
			return $query->row['name'];
		} else {
			return 'Uncategorized';
		}
	}
	
	/**
	 * Retrieve the complete order details.
	 *
	 * @param int 
	 * @return array					
	 */
	public function get_order_details($order_id)
	{
		$order_details = $this->db->query("SELECT payment_city, payment_zone, payment_country, total FROM `" . DB_PREFIX . "order` WHERE order_id = '". (int)$order_id ."'");
		
		$order_details = $order_details->row;
		
		$order_tax_value = $this->db->query("SELECT value FROM `" . DB_PREFIX . "order_total` WHERE code = 'tax' AND order_id = '" . (int)$order_id . "'");
		
		$order_details['tax'] = isset($order_tax_value->row['value']) ? $order_tax_value->row['value'] : 0;
				
		return $order_details;
	}
	 
	/**
	 * Retrieve product details from the order.
	 *
	 * @param int 
	 * @param int 
	 */
	public function get_product_details($order_id, $product_id) {
		$query = $this->db->query("SELECT name, model, price, tax, quantity FROM " . DB_PREFIX . "order_product WHERE order_id = '". (int)$order_id ."' AND product_id = '" . (int)$product_id . "'");
		
		return $query->row;
	}
}
?>