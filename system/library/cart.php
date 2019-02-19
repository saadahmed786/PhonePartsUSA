<?php
class Cart {
	private $data = array();
	
	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		$this->db = $registry->get('db');
		$this->tax = $registry->get('tax');
		$this->weight = $registry->get('weight');

		if (!isset($this->session->data['cart']) || !is_array($this->session->data['cart'])) {
			$this->session->data['cart'] = array();
		}
	}

	public function getProducts() {
		if (!$this->data) {
			$product_limit_qty=0;

			$_limit_qty = false;

			foreach ($this->session->data['voucher'] as $_voucher ) {

				$coupon_query =  $this->db->query("SELECT product_limit_qty FROM ".DB_PREFIX."coupon WHERE code='".$this->db->escape($_voucher)."' AND has_product_limit=1 ");
				if($coupon_query->num_rows)
				{
					
					foreach ($this->session->data['cart'] as $key => $quantity) {
						$product = explode(':', $key);
						$product_id = $product[0];
						$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
						if($product_query->num_rows)
						{
				
											
						$product_class =	$this->getProductClass($product_query->row['model']);
						$product_quality = $this->getProductQuality($product_query->row['model']);
						// echo $product_quality;
						
						if ((strtolower($product_class['name']) == 'screen-lcdtouchscreenassembly' || strtolower($product_class['name']) == 'screen-touchscreen') && strtolower($product_quality)=='economy plus') {
			
							$product_limit_qty+=(int)$quantity;

						}
					}
					}
					
					if($product_limit_qty>=$coupon_query->row['product_limit_qty'])
					{
						$_limit_qty = true;
					}
				}
			}

			foreach ($this->session->data['cart'] as $key => $quantity) {
				$product = explode(':', $key);
				$product_id = $product[0];
				$stock = true;

				// Options
				if (isset($product[1])) {
					$options = unserialize(base64_decode($product[1]));
				} else {
					$options = array();
				} 
				
				$product_query = $this->db->query("SELECT *,p.quantity+p.prefill-((SELECT COALESCE(sum(b.product_qty) - sum(b.picked_quantity),0) FROM inv_orders_items b where b.ostatus in ('processed','on hold') and b.product_sku=p.model) + (SELECT COALESCE(sum(b.picked_quantity) - sum(b.packed_quantity),0) FROM inv_orders_items b where b.ostatus in ('processed','unshipped','on hold') and b.is_picked=1 and b.opacked=0 and b.product_sku=p.model) + (SELECT COALESCE(sum(b.packed_quantity),0) FROM inv_orders_items b where b.ostatus in ('processed','unshipped','on hold') and b.is_packed=1 and b.product_sku=p.model) - (SELECT COALESCE(sum(b.product_qty),0) FROM inv_orders_items b where b.ostatus='on hold' and b.is_picked=0 and b.is_packed=0 and b.product_sku=p.model)) as quantity FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
				
				if ($product_query->num_rows) {
					$option_price = 0;
					$option_points = 0;
					$option_weight = 0;

					$option_data = array();

					foreach ($options as $product_option_id => $option_value) {
						$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");
						
						if ($option_query->num_rows) {
							if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
								$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$option_value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
								
								if ($option_value_query->num_rows) {
									if ($option_value_query->row['price_prefix'] == '+') {
										$option_price += $option_value_query->row['price'];
									} elseif ($option_value_query->row['price_prefix'] == '-') {
										$option_price -= $option_value_query->row['price'];
									}

									if ($option_value_query->row['points_prefix'] == '+') {
										$option_points += $option_value_query->row['points'];
									} elseif ($option_value_query->row['points_prefix'] == '-') {
										$option_points -= $option_value_query->row['points'];
									}

									if ($option_value_query->row['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row['weight'];
									} elseif ($option_value_query->row['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row['weight'];
									}
									
									if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
										$stock = false;
									}
									
									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $option_value,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'option_value'            => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity'],
										'subtract'                => $option_value_query->row['subtract'],
										'price'                   => $option_value_query->row['price'],
										'price_prefix'            => $option_value_query->row['price_prefix'],
										'points'                  => $option_value_query->row['points'],
										'points_prefix'           => $option_value_query->row['points_prefix'],									
										'weight'                  => $option_value_query->row['weight'],
										'weight_prefix'           => $option_value_query->row['weight_prefix']
										);								
								}
							} elseif ($option_query->row['type'] == 'checkbox' && is_array($option_value)) {
								foreach ($option_value as $product_option_value_id) {
									$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
									
									if ($option_value_query->num_rows) {
										if ($option_value_query->row['price_prefix'] == '+') {
											$option_price += $option_value_query->row['price'];
										} elseif ($option_value_query->row['price_prefix'] == '-') {
											$option_price -= $option_value_query->row['price'];
										}

										if ($option_value_query->row['points_prefix'] == '+') {
											$option_points += $option_value_query->row['points'];
										} elseif ($option_value_query->row['points_prefix'] == '-') {
											$option_points -= $option_value_query->row['points'];
										}

										if ($option_value_query->row['weight_prefix'] == '+') {
											$option_weight += $option_value_query->row['weight'];
										} elseif ($option_value_query->row['weight_prefix'] == '-') {
											$option_weight -= $option_value_query->row['weight'];
										}
										
										if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
											$stock = false;
										}
										
										$option_data[] = array(
											'product_option_id'       => $product_option_id,
											'product_option_value_id' => $product_option_value_id,
											'option_id'               => $option_query->row['option_id'],
											'option_value_id'         => $option_value_query->row['option_value_id'],
											'name'                    => $option_query->row['name'],
											'option_value'            => $option_value_query->row['name'],
											'type'                    => $option_query->row['type'],
											'quantity'                => $option_value_query->row['quantity'],
											'subtract'                => $option_value_query->row['subtract'],
											'price'                   => $option_value_query->row['price'],
											'price_prefix'            => $option_value_query->row['price_prefix'],
											'points'                  => $option_value_query->row['points'],
											'points_prefix'           => $option_value_query->row['points_prefix'],
											'weight'                  => $option_value_query->row['weight'],
											'weight_prefix'           => $option_value_query->row['weight_prefix']
											);								
									}
								}						
							} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => '',
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => '',
									'name'                    => $option_query->row['name'],
									'option_value'            => $option_value,
									'type'                    => $option_query->row['type'],
									'quantity'                => '',
									'subtract'                => '',
									'price'                   => '',
									'price_prefix'            => '',
									'points'                  => '',
									'points_prefix'           => '',								
									'weight'                  => '',
									'weight_prefix'           => ''
									);						
							}
						}
					} 

					if ($this->customer->isLogged()) {
						$customer_group_id = $this->customer->getCustomerGroupId();
					} else {
						$customer_group_id = $this->config->get('config_customer_group_id');
					}
					$is_platinum = false;
					if((substr($product_query->row['model'],0,7)=='APL-001' || substr($product_query->row['model'],0,4)=='SRN-' || substr($product_query->row['model'],0,7)=='TAB-SRN') and $product_query->row['is_kit']==0  )
					{
						$is_platinum = true;
					}
					if($is_platinum)
					{

					$customer_group_id = '1633'; // force assigning the customer group of platinum 1633
				}
				$product_class =	$this->getProductClass($product_query->row['model']);
				$product_quality = $this->getProductQuality($product_query->row['model']);
				$old_price = 0.00;
		
						
				if ((strtolower($product_class['name']) == 'screen-lcdtouchscreenassembly' || strtolower($product_class['name']) == 'screen-touchscreen') && strtolower($product_quality)=='economy plus' && $_limit_qty==true && $product_query->row['bulk_price']>0.00 ) 
				{
					$price = $product_query->row['bulk_price'];
					if($product_query->row['sale_price']!= '0.0000')
					{
						$old_price = $product_query->row['sale_price'];
					}
					else{
						$old_price = $product_query->row['price'];
					}	
				}
				elseif ($product_query->row['sale_price']!= '0.0000') {
					$price = $product_query->row['sale_price'];
				} else {
					
					
					$price = $product_query->row['price'];

					// Product Discounts
					$discount_quantity = 0;

					foreach ($this->session->data['cart'] as $key_2 => $quantity_2) {
						$product_2 = explode(':', $key_2);

						if ($product_2[0] == $product_id) {
							$discount_quantity += $quantity_2;
						}
					}

					$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

					if ($product_discount_query->num_rows) {
						$price = $product_discount_query->row['price'];
					}

					// Product Specials
					$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

					if ($product_special_query->num_rows) {
						$price = $product_special_query->row['price'];
					}
				}						

					// Reward Points
				$product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "'");

				if ($product_reward_query->num_rows) {	
					$reward = $product_reward_query->row['points'];
				} else {
					$reward = 0;
				}

					// Downloads		
				$download_data = array();     		

				$download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE p2d.product_id = '" . (int)$product_id . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
				
				foreach ($download_query->rows as $download) {
					$download_data[] = array(
						'download_id' => $download['download_id'],
						'name'        => $download['name'],
						'filename'    => $download['filename'],
						'mask'        => $download['mask'],
						'remaining'   => $download['remaining']
						);
				}

					// Stock
				if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $quantity)) {
					$stock = false;
				}

				$this->data[$key] = array(
					'key'             => $key,
					'product_id'      => $product_query->row['product_id'],
					'name'            => $product_query->row['name'],
					'model'           => $product_query->row['model'],
					'shipping'        => $product_query->row['shipping'],
					'image'           => $product_query->row['image'],
					'option'          => $option_data,
					'download'        => $download_data,
					'quantity'        => $quantity,
					'minimum'         => $product_query->row['minimum'],
					'subtract'        => $product_query->row['subtract'],
					'stock'           => $stock,
					'stock_available'=> $product_query->row['quantity'],
					'price'           => ($price + $option_price),
					'old_price'           => ($old_price + $option_price),
					'total'           => ($price + $option_price) * $quantity,
					'reward'          => $reward * $quantity,
					'points'          => ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $quantity : 0),
					'tax_class_id'    => $product_query->row['tax_class_id'],
					'weight'          => ($product_query->row['weight'] + $option_weight) * $quantity,
					'weight_class_id' => $product_query->row['weight_class_id'],
					'length'          => $product_query->row['length'],
					'width'           => $product_query->row['width'],
					'height'          => $product_query->row['height'],
					'length_class_id' => $product_query->row['length_class_id']					
					);
			} else {
				$this->remove($key);
			}
		}
	}

	return $this->data;
}

public function add($product_id, $qty = 1, $option = array()) {
	if (!$option) {
		$key = (int)$product_id;
	} else {
		$key = (int)$product_id . ':' . base64_encode(serialize($option));
	}

	if ((int)$qty && ((int)$qty > 0)) {
		if (!isset($this->session->data['cart'][$key])) {
			$this->session->data['cart'][$key] = (int)$qty;
		} else {
			$this->session->data['cart'][$key] += (int)$qty;
		}
	}

	$this->data = array();
}

public function update($key, $qty) {
	if ((int)$qty && ((int)$qty > 0)) {
		$this->session->data['cart'][$key] = (int)$qty;
	} else {
		$this->remove($key);
	}

	$this->data = array();
}

public function remove($key) {
	if (isset($this->session->data['cart'][$key])) {
		unset($this->session->data['cart'][$key]);
	}

	$this->data = array();
}

public function clear() {
	$this->session->data['cart'] = array();
	$this->data = array();
}

public function getWeight() {
	$weight = 0;
	
	foreach ($this->getProducts() as $product) {
		if ($product['shipping']) {
			$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
		}
	}
	
	return $weight;
}

public function getSubTotal() {
	$total = 0;

	foreach ($this->getProducts() as $product) {
		$total += $product['total'];
	}

	return $total;
}

public function getTaxes() {
	$tax_data = array();

	foreach ($this->getProducts() as $product) {
		if ($product['tax_class_id']) {
			$tax_rates = $this->tax->getRates($product['total'], $product['tax_class_id']);

			foreach ($tax_rates as $tax_rate) {
				$amount = 0;

				if ($tax_rate['type'] == 'F') {
					$amount = ($tax_rate['amount'] * $product['quantity']);
				} elseif ($tax_rate['type'] == 'P') {
					$amount = $tax_rate['amount'];
				}

				if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
					$tax_data[$tax_rate['tax_rate_id']] = $amount;
				} else {
					$tax_data[$tax_rate['tax_rate_id']] += $amount;
				}
			}
		}
	}

	return $tax_data;
}

public function getTotal() {
	$total = 0;

	foreach ($this->getProducts() as $product) {
		$total += $this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_tax'));
	}

	return $total;
}

public function countProducts() {
	$product_total = 0;

	$products = $this->getProducts();

	foreach ($products as $product) {
		$product_total += $product['quantity'];
	}		

	return $product_total;
}

public function hasProducts() {
	return count($this->session->data['cart']);
}

public function hasStock() {
	$stock = true;

	foreach ($this->getProducts() as $product) {
		if (!$product['stock']) {
			$stock = false;
		}
	}

	return $stock;
}

public function hasShipping() {
	$shipping = false;

	foreach ($this->getProducts() as $product) {
		if ($product['shipping']) {
			$shipping = true;

			break;
		}		
	}

	return $shipping;
}

public function hasDownload() {
	$download = false;

	foreach ($this->getProducts() as $product) {
		if ($product['download']) {
			$download = true;

			break;
		}		
	}

	return $download;
}
private  function getProductClass($sku) {

	$query = $this->db->query("SELECT c.*, d.name as main_category FROM inv_device_product AS a, inv_device_class AS b, inv_classification AS c, inv_main_classification AS d WHERE a.device_product_id = b.device_product_id AND b.class_id = c.id AND c.main_class_id = d.id AND sku = '$sku'");

	if ($query->num_rows) {
		return $query->row;
	} else {
		return false;
	}
}	
private function getProductQuality($sku) {

		$query = $this->db->query("SELECT f.* FROM inv_device_product AS a, inv_device_manufacturer AS b, inv_device_device AS c, inv_device_model AS d, inv_device_attrib AS e, inv_attr AS f, inv_attribute_group AS g WHERE a.device_product_id = b.device_product_id AND b.device_manufacturer_id = c.device_manufacturer_id AND c.device_device_id = d.device_device_id AND d.device_model_id = e.device_model_id AND e.attrib_id = f.id AND f.attribute_group_id = g.id AND g.name = 'Quality' AND a.sku = '$sku' GROUP BY e.attrib_id");
		
		if ($query->num_rows) {
			return $query->row['name'];
		} else {
			return false;
		}
	}
}
?>