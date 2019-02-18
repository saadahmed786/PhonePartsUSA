<?php
//==============================================================================
// Multi Flat Rate Shipping v154.1
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

class ModelShippingMultiflatrate extends Model {
	private $type = 'shipping';
	private $name = 'multiflatrate';
	
	private function getSetting($setting) {
		$value = $this->config->get($this->name . '_' . $setting);
		return (is_string($value) && strpos($value, 'a:') === 0) ? unserialize($value) : $value;
	}
	
	public function getQuote($address,$manual=false,$sub_total=0.00) {
		if (!$this->getSetting('status') || !$this->getSetting('data')) {
			return;
		}
		
		$v14x = (!defined('VERSION') || VERSION < 1.5);
		$v150 = (defined('VERSION') && strpos(VERSION, '1.5.0') === 0);
		
		$default_currency = $this->config->get('config_currency');
		$currency = $this->session->data['currency'];
		$language = $this->session->data['language'];
		
		$current_geozones = array();
		$geozones = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '0' OR zone_id = '" . (int)$address['zone_id'] . "')");
		foreach ($geozones->rows as $geozone) {
			$current_geozones[] = $geozone['geo_zone_id'];
		}
		
		$keycode = ($v14x) ? 'key' : 'code';
		$total = 0;
		$total_data = array();
		$taxes = $this->cart->getTaxes();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = 'total'");
		$order_totals = $query->rows;
		$sort_order = array();
		foreach ($order_totals as $key => $value) $sort_order[$key] = $this->config->get($value[$keycode] . '_sort_order');
		array_multisort($sort_order, SORT_ASC, $order_totals);
		foreach ($order_totals as $order_total) {
			if ($order_total[$keycode] == $this->type) break;
			if ($this->config->get($order_total[$keycode] . '_status')) {
				$this->load->model('total/' . $order_total[$keycode]);
				$this->{'model_total_' . $order_total[$keycode]}->getTotal($total_data, $total, $taxes);
			}
		}
		
		$quote_data = array();
		
		$classes =  array();
		
		foreach($this->cart->getProducts() as $_product)
		{
			$class = $this->db->query("SELECT classification_id FROM `".DB_PREFIX."product` WHERE product_id='".$_product['product_id']."'");
			if($class->row['classification_id'])
			{
				$classes[] = $class->row['classification_id'];
			}
			
		}

		$show_combine = false;
		if($this->customer->isLogged())
		{
			$last_order = $this->db->query("SELECT a.order_id,a.order_status,a.ship_date,b.shipping_method,b.shipping_cost,b.zone_id,b.zip FROM inv_orders a,inv_orders_details b where a.order_id=b.order_id and  trim(lower(a.email))='".trim(strtolower($this->customer->getEmail()))."' and b.shipping_method not in ('Combined Shipping','Local Las Vegas Store Pickup - 9:30am-4:30pm') order by a.order_date desc limit 1 ");
			if($last_order->row['order_status']=='Processed')
			{
				$show_combine = true;
			}

			if(strtolower($last_order->row['order_status'])=='shipped' && $last_order->row['ship_date']!='0000-00-00 00:00:00')
			{
				if (strtotime($last_order->row['ship_date']) <= strtotime('3:30 pm ' . date('Y-m-d')) && date('Y-m-d', strtotime($last_order->row['ship_date'])) == date('Y-m-d') && time() <= strtotime('3:30 pm ' . date('Y-m-d')) ) {
					$show_combine = true;
				}
			}

			


		}
		unset($this->session->data['combined_order_id']);
		$previous_delivery_time = 'N/A';
		$previous_shipping_cost = '0.00';
		if($show_combine)
		{
			if($last_order->row['zone_id']!=$this->session->data['shipping_zone_id'] || $last_order->row['zip']!=$this->session->data['shipping_postcode'])
			{
				$show_combine = false;
			}
		}
		if($show_combine)
		{

			$this->session->data['combined_order_id'] = $last_order->row['order_id'];



			$_methods = $this->getSetting('data');
			// $_methods = unserialize($data);
			

			foreach($_methods as $method)
			{

				if( $method['title']['en']==$last_order->row['shipping_method'] && $method['cost']==$last_order->row['shipping_cost'])
				{

					$previous_delivery_time = $method['delivery_time'];

					$previous_shipping_cost = (float)$method['cost'];
					break;
				}   
			}

			$quote_data['combine_shipping'] = array(
				'id'=>'multiflatrate.combine_shipping',
				'code'=>'multiflatrate.combine_shipping',
				'title'=>'Combined Shipping',
				'cost'=>0,
				'tax_class_id'=>0,
				'delivery_time'=>$previous_delivery_time.' <span class="glyphicon glyphicon-question-sign" rel="tipsy" original-title="Order #'.$last_order->row['order_id'].', '.$last_order->row['shipping_method'].'."></span>',
				'text'=>'$0.00'

				);

		}

		if($this->customer->getEmail()=='phonepartusa@gmail.com')
		{
				// $quote_data['combine_shipping'] = array(
				// 	'id'=>'multiflatrate.combine_shipping',
				// 	'code'=>'multiflatrate.combine_shipping',
				// 	'title'=>'Combined Shipping',
				// 	'cost'=>0,
				// 	'tax_class_id'=>0,
				// 	'delivery_time'=>'Order #'.$last_order->row['order_id'],
				// 	'text'=>'$0.00'

				// 	);



		}
		
		//print_r($classes);
		foreach ($this->getSetting('data') as $row_num => $row) {
			
			// Check Order Criteria
			if (empty($row['stores']) ||
				!in_array((int)$this->config->get('config_store_id'), $row['stores']) ||
				empty($row['currencys']) ||
				(!in_array('autoconvert', $row['currencys']) && !in_array($currency, $row['currencys'])) ||
				empty($row['customer_groups']) ||
				!in_array((int)$this->customer->getCustomerGroupId(), $row['customer_groups']) ||
				empty($row['geo_zones']) ||
				(empty($current_geozones) && !in_array(0, $row['geo_zones'])) ||
				(!empty($current_geozones) && !array_intersect($row['geo_zones'], $current_geozones)) ||
				
				(!empty($classes) && array_intersect($row['classifications'], $classes))
				) {
				continue;
		}

			// Generate Comparison Values
		$autoconvert = !in_array($currency, $row['currencys']);
		$conversion_currency = $row['currencys'][0];
		if ($conversion_currency == 'autoconvert') {
			$conversion_currency = (isset($row['currencys'][1])) ? $row['currencys'][1] : $default_currency;
		}

		$item = 0;
		$subtotal = 0;
		$taxed = 0;



		foreach ($this->cart->getProducts() as $product) {
			if (!$product['shipping']) continue;
			$item += $product['quantity'];
			$subtotal += $product['total'];
			$taxed += $this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_tax'));
		}
		if($manual==true)
		{
			$subtotal = $sub_total;
		}

		$total_value = $this->currency->convert(${$row['total_value']}, $default_currency, $currency);
		$total_value = ($autoconvert) ? $this->currency->convert($total_value, $currency, $conversion_currency) : $total_value;

			// Calculate Cost
		$cost = (strpos($row['cost'], '%')) ? $total_value * (float)$row['cost'] / 100 : (float)$row['cost'];
		$cost = ($row['type'] == 'peritem') ? $cost * $item : $cost;
		$cost = ($autoconvert) ? $this->currency->convert($cost, $conversion_currency, $currency) : $cost;
		$cost = $this->currency->convert($cost-$previous_shipping_cost, $currency, $default_currency);
		if(substr($cost,0,1)!='-')
		{
		$quote_data[$this->name . '_' . $row_num] = array(
			'id'			=> $this->name . '.' . $this->name . '_' . $row_num,
			'code'			=> $this->name . '.' . $this->name . '_' . $row_num,
			'title'			=> html_entity_decode($row['title'][$language], ENT_QUOTES, 'UTF-8'),
			'cost'			=> $cost,
			'tax_class_id'	=> $row['tax_class_id'],
			'delivery_time'=>$row['delivery_time'],
			'text'			=> $this->currency->format($this->tax->calculate($cost, $row['tax_class_id'], $this->config->get('config_tax')))
			);
		}
	}

	$method_data = array();
	if ($quote_data) {
		$sort_by_cost = array();
		foreach ($quote_data as $key => $value) $sort_by_cost[$key] = $value['cost'];
		array_multisort($sort_by_cost, SORT_ASC, $quote_data);

		$heading = $this->getSetting('heading');
		$heading[$language] = str_replace("(Ships 4:00 pm PST)", "", $heading[$language]);
		$heading[$language] = trim($heading[$language]);
		$method_data = array(
			'id'			=> $this->name,
			'code'			=> $this->name,
			'title'			=> html_entity_decode($heading[$language], ENT_QUOTES, 'UTF-8'),
			'quote'			=> $quote_data,
			'sort_order'	=> $this->getSetting('sort_order'),
			'error'			=> false
			);
	}
	return $method_data;
}	
}
?>