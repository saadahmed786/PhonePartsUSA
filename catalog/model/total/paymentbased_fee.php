<?php
//==============================================================================
// Payment-Based Fee/Discount v155.1
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

class ModelTotalPaymentbasedFee extends Model {
	private $type = 'total';
	private $name = 'paymentbased_fee';
	
	private function getSetting($setting) {
		$value = $this->config->get($this->name . '_' . $setting);
		return (is_string($value) && strpos($value, 'a:') === 0) ? unserialize($value) : $value;
	}
	
	public function getTotal(&$total_data, &$order_total, &$taxes) {
		if (!$this->getSetting('status') || !$this->getSetting('data')) {
			return;
		}
		
		$version = (!defined('VERSION')) ? 140 : (int)substr(str_replace('.', '', VERSION), 0, 3);
		
		$default_currency = $this->config->get('config_currency');
		$currency = $this->session->data['currency'];
		$language = $this->session->data['language'];
		$length_unit = ($version < 151) ? 'length_class' : 'length_class_id';
		
		// Extension-specific
		if (!isset($this->session->data[str_replace('based_fee', '', $this->name) . '_method'][($version < 150) ? 'id' : 'code'])) return;
		if ($this->name == 'shippingbased_fee') {
			$method = explode('.', $this->session->data['shipping_method'][($version < 150) ? 'id' : 'code']);
			$method = $method[0];
			$shipping = $this->session->data['shipping_method']['cost'];
		} else {
			$method = $this->session->data['payment_method'][($version < 150) ? 'id' : 'code'];
		}
		// end
		
		if ($this->type == 'shipping') {
			$shipping_geozones = array();
			$geozones = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE country_id = " . (int)$address['country_id'] . " AND (zone_id = 0 OR zone_id = " . (int)$address['zone_id'] . ")");
			foreach ($geozones->rows as $geozone) {
				$shipping_geozones[] = $geozone['geo_zone_id'];
			}
			$shipping_postcode = preg_replace('/[^A-Za-z0-9 ]/', '', isset($address['postcode']) ? $address['postcode'] : '');
			
			$keycode = ($version < 150) ? 'key' : 'code';
			$total_data = array();
			$order_total = 0;
			$taxes = $this->cart->getTaxes();
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = 'total'");
			$order_totals = $query->rows;
			$sort_order = array();
			foreach ($order_totals as $key => $value) $sort_order[$key] = $this->config->get($value[$keycode] . '_sort_order');
			array_multisort($sort_order, SORT_ASC, $order_totals);
			foreach ($order_totals as $ot) {
				if ($ot[$keycode] == $this->type) break;
				if ($this->config->get($ot[$keycode] . '_status')) {
					$this->load->model('total/' . $ot[$keycode]);
					$this->{'model_total_' . $ot[$keycode]}->getTotal($total_data, $order_total, $taxes);
				}
			}
		} else {
			$this->load->model('account/address');
			foreach (array('shipping', 'payment') as $address_type) {
				$address = array();
				if ($this->customer->isLogged()) 								$address = $this->model_account_address->getAddress($this->customer->getAddressId());
				if (isset($this->session->data['country_id']))					$address['country_id'] = $this->session->data['country_id'];
				if (isset($this->session->data['zone_id']))						$address['zone_id'] = $this->session->data['zone_id'];
				if (isset($this->session->data['postcode']))					$address['postcode'] = $this->session->data['postcode'];
				if (isset($this->session->data['shipping_country_id']))			$address['country_id'] = $this->session->data['shipping_country_id'];
				if (isset($this->session->data['shipping_zone_id']))			$address['zone_id'] = $this->session->data['shipping_zone_id'];
				if (isset($this->session->data['shipping_postcode']))			$address['postcode'] = $this->session->data['shipping_postcode'];
				if (isset($this->session->data['guest']))						$address = $this->session->data['guest'];
				if (isset($this->session->data['guest'][$address_type]))		$address = $this->session->data['guest'][$address_type];
				if (isset($this->session->data[$address_type . '_address_id']))	$address = $this->model_account_address->getAddress($this->session->data[$address_type . '_address_id']);		
				if (isset($this->session->data[$address_type . '_country_id']))	$address['country_id'] = $this->session->data[$address_type . '_country_id'];
				if (isset($this->session->data[$address_type . '_zone_id']))	$address['zone_id'] = $this->session->data[$address_type . '_zone_id'];
				if (isset($this->session->data[$address_type . '_postcode']))	$address['postcode'] = $this->session->data[$address_type . '_postcode'];
				if (empty($address['country_id']))								$address['country_id'] = $this->config->get('config_country_id');
				if (empty($address['zone_id']))									$address['zone_id'] =  $this->config->get('config_zone_id');
				if (empty($address['postcode']))								$address['postcode'] = '';
				${$address_type.'_geozones'} = array();
				$geozones = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE country_id = " . (int)$address['country_id'] . " AND (zone_id = 0 OR zone_id = " . (int)$address['zone_id'] . ")");
				foreach ($geozones->rows as $geozone) {
					${$address_type.'_geozones'}[] = $geozone['geo_zone_id'];
				}
				${$address_type.'_postcode'} = preg_replace('/[^A-Za-z0-9 ]/', '', $address['postcode']);
			}
		}
		
		$this->load->model('catalog/product');
		$quote_data = array();
		
		foreach ($this->getSetting('data') as $row_num => $row) {
			// Check Order Criteria
			$geozone_comparison = ($this->type == 'shipping') ? 'shipping' : $row['geozone_comparison'];
			if (empty($row['stores']) ||
				!in_array((int)$this->config->get('config_store_id'), $row['stores']) ||
				empty($row['currencys']) ||
				(!in_array('autoconvert', $row['currencys']) && !in_array($currency, $row['currencys'])) ||
				empty($row['customer_groups']) ||
				!in_array((int)$this->customer->getCustomerGroupId(), $row['customer_groups']) ||
				empty($row['geo_zones']) ||
				(empty(${$geozone_comparison.'_geozones'}) && !in_array(0, $row['geo_zones'])) ||
				(!empty(${$geozone_comparison.'_geozones'}) && !array_intersect($row['geo_zones'], ${$geozone_comparison.'_geozones'})) ||
				empty($row['costs'])
			) {
				continue;
			}
			
			// Extension-specific
			if (empty($row['extensions']) || !in_array($method, $row['extensions'])) continue;
			// end
			
			// Generate Comparison Values
			$item = 0;
			$postcode = ($row['postcode_format'] == 'uk') ? substr_replace(substr_replace(str_replace(' ', '', ${$geozone_comparison.'_postcode'}), ' ', -3, 0), ' ', -2, 0) : ${$geozone_comparison.'_postcode'};
			$prediscounted = 0;
			$subtotal = 0;
			$taxed = 0;
			$total = $order_total;
			$volume = 0;
			$weight = 0;
			$disabled = false;
			
			foreach ($this->cart->getProducts() as $product) {
				if (!$product['shipping'] && $this->type == 'shipping') continue;
				
				$item += $product['quantity'];
				
				$product_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product['product_id']);
				$product_info = $this->model_catalog_product->getProduct($product['product_id']);
				$price = ($product_info['special']) ? $product_info['special'] : $product_info['price'];
				
				$prediscounted += $product['total'] + ($product['quantity'] * ($product_query->row['price'] - $price));
				$subtotal += $product['total'];
				$taxed += $this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_tax'));
				
				$length = $this->length->convert($product['length'], $product[$length_unit], $this->config->get('config_' . $length_unit));
				$width = $this->length->convert($product['width'], $product[$length_unit], $this->config->get('config_' . $length_unit));
				$height = $this->length->convert($product['height'], $product[$length_unit], $this->config->get('config_' . $length_unit));
				$volume += $length * $width * $height * $product['quantity'];
				
				$length += (strpos($row['add_length'], '%')) ? $length * (float)$row['add_length'] / 100 : (float)$row['add_length'];
				$width += (strpos($row['add_width'], '%')) ? $width * (float)$row['add_width'] / 100 : (float)$row['add_width'];
				$height += (strpos($row['add_height'], '%')) ? $height * (float)$row['add_height'] / 100 : (float)$row['add_height'];
				if (($row['min_length'] && $length < (float)$row['min_length']) ||
					($row['max_length'] && $length > (float)$row['max_length']) ||
					($row['min_width'] && $width < (float)$row['min_width']) ||
					($row['max_width'] && $width > (float)$row['max_width']) ||
					($row['min_height'] && $height < (float)$row['min_height']) ||
					($row['max_height'] && $height > (float)$row['max_height'])
				) {
					$disabled = true;
				}
				
				if ($version < 150) {
					$weight += $this->weight->convert($product['weight'] * $product['quantity'], $product['weight_class'], $this->config->get('config_weight_class'));
				} elseif ($version < 151) {
					$weight += $this->weight->convert($product['weight'], $product['weight_class'], $this->config->get('config_weight_class'));
				} else {
					$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
				}
			}
			
			// Check Cart Criteria
			$autoconvert = (!in_array($currency, $row['currencys']));
			$conversion_currency = $row['currencys'][0];
			if ($conversion_currency == 'autoconvert') {
				$conversion_currency = (isset($row['currencys'][1])) ? $row['currencys'][1] : $default_currency;
			}
			
			$total_value = ${$row['total_value']};
			$total_value += (strpos($row['add_total'], '%')) ? $total_value * (float)$row['add_total'] / 100 : (float)$row['add_total'];
			$total_value = $this->currency->convert($total_value, $default_currency, $currency);
			$total_value = ($autoconvert) ? $this->currency->convert($total_value, $currency, $conversion_currency) : $total_value;
			
			// Extension-specific
			if ($this->name == 'shippingbased_fee') {
				$shipping += (strpos($row['add_shipping'], '%')) ? $shipping * (float)$row['add_shipping'] / 100 : (float)$row['add_shipping'];
				$shipping = $this->currency->convert($shipping, $default_currency, $currency);
				$shipping = ($autoconvert) ? $this->currency->convert($shipping, $currency, $conversion_currency) : $shipping;
				
				if ($row['min_shipping'] && $shipping < (float)$row['min_shipping']) continue;
				if ($row['max_shipping'] && $shipping > (float)$row['max_shipping']) continue;
				
				$percentage_value = ($row['percentages'] == 'total') ? $total_value : $shipping;
			}
			// end
			
			$item += (strpos($row['add_item'], '%')) ? $item * (float)$row['add_item'] / 100 : (float)$row['add_item'];
			$volume += (strpos($row['add_volume'], '%')) ? $volume * (float)$row['add_volume'] / 100 : (float)$row['add_volume'];
			$weight += (strpos($row['add_weight'], '%')) ? $weight * (float)$row['add_weight'] / 100 : (float)$row['add_weight'];
			
			if ($row['postcodes']) {
				$no_matches = true;
				$postcodes = explode(',', $row['postcodes']);
				foreach ($postcodes as $pc) {
					$range = explode('-', trim($pc));
					$from = trim($range[0]);
					$to = (isset($range[1])) ? trim($range[1]) : '';
					
					if ($row['postcode_format'] == 'uk') {
						$from = str_replace(' ', '', $from);
						$from .= (strlen($from) < 5) ? '000' : '';
						$from = substr_replace(substr_replace($from, ' ', -3, 0), ' ', -2, 0);
						
						if ($to) {
							$to = str_replace(' ', '', $to);
							$to .= (strlen($to) < 5) ? 'ZZZ' : '';
							$to = substr_replace(substr_replace($to, ' ', -3, 0), ' ', -2, 0);
						} else {
							$to = str_replace('0 00', 'Z ZZ', $from);
						}
					}
					
					if ((!$to && strnatcasecmp($from, $postcode) == 0) ||
						((empty($from) || strnatcasecmp($from, $postcode) <= 0) && (empty($to) || strnatcasecmp($postcode, $to) <= 0))
					) {
						$no_matches = false;
						break;
					}
				}
				if ($no_matches) $disabled = true;
			}
			
			if ($disabled ||
				($row['min_item'] && $item < (float)$row['min_item']) ||
				($row['max_item'] && $item > (float)$row['max_item']) ||
				($row['min_total'] && $total_value < (float)$row['min_total']) ||
				($row['max_total'] && $total_value > (float)$row['max_total']) ||
				($row['min_volume'] && $volume < (float)$row['min_volume']) ||
				($row['max_volume'] && $volume > (float)$row['max_volume']) ||
				($row['min_weight'] && $weight < (float)$row['min_weight']) ||
				($row['max_weight'] && $weight > (float)$row['max_weight']) ||
				($row['date_start'] && strtotime(date('Y-m-d')) < strtotime($row['date_start'])) ||
				($row['date_end'] && strtotime(date('Y-m-d')) > strtotime($row['date_end']))
			) {
				continue;
			}
			
			// Calculate Cost
			$cost = 0;
			$comparison_value = ($row['rate_type'] == 'total') ? $total_value : ${$row['rate_type']};
			
			for ($i = 0; $i < count($row['costs']['from']); $i++) {
				if ($row['rate_type'] == 'postcode') {
					$from = $row['costs']['from'][$i];
					$to = $row['costs']['to'][$i];
					
					if ($row['postcode_format'] == 'uk') {
						$from = str_replace(' ', '', $from);
						$from .= (strlen($from) < 5) ? '000' : '';
						$from = substr_replace(substr_replace($from, ' ', -3, 0), ' ', -2, 0);
						
						if ($to) {
							$to = str_replace(' ', '', $to);
							$to .= (strlen($to) < 5) ? 'ZZZ' : '';
							$to = substr_replace(substr_replace($to, ' ', -3, 0), ' ', -2, 0);
						} else {
							$to = str_replace('0 00', 'Z ZZ', $from);
						}
					}
					
					$difference = $item;
				} else {
					$from = (!empty($row['costs']['from'][$i])) ? (float)$row['costs']['from'][$i] : 0;
					$to = (!empty($row['costs']['to'][$i])) ? (float)$row['costs']['to'][$i] : 999999;
					
					$top = min($to, $comparison_value);
					$bottom = ($row['final_cost'] == 'single') ? 0 : $from;
					$difference = $top - $bottom;
				}
				
				$multiplier = (!empty($row['costs']['per'][$i])) ? ceil($difference / (float)$row['costs']['per'][$i]) : 1;
				
				$charge = (float)$row['costs']['charge'][$i] * $multiplier;
				$charge *= (strpos($row['costs']['charge'][$i], '%')) ? $total_value / 100 : 1;
				
				$cost = ($row['final_cost'] == 'single' || $row['rate_type'] == 'postcode') ? $charge : $cost + $charge;
				
				if ($row['rate_type'] == 'postcode') {
					$bracket_match = ((empty($from) || strnatcasecmp($from, $comparison_value) <= 0) && (empty($to) || strnatcasecmp($comparison_value, $to) <= 0));
				} else {
					$bracket_match = (round($from, 3) <= round($comparison_value, 3) && round($comparison_value, 3) <= round($to, 3));
				}
				
				if ($bracket_match) {
					$cost += (strpos($row['add_cost'], '%')) ? $total_value * (float)$row['add_cost'] / 100 : (float)$row['add_cost'];
					$cost = ($row['min_cost'] && $cost < (float)$row['min_cost']) ? (float)$row['min_cost'] : $cost;
					$cost = ($row['max_cost'] && $cost > (float)$row['max_cost']) ? (float)$row['max_cost'] : $cost;
					
					$cost = ($autoconvert) ? $this->currency->convert($cost, $conversion_currency, $currency) : $cost;
					$cost = $this->currency->convert($cost, $currency, $default_currency);
					if ($cost == 0 && $this->type == 'total') continue;
					
					if (isset($quote_data[$this->name . '_' . $row['sort_order']])) {
						$quote_data[$this->name . '_' . $row['sort_order']]['cost'][] = $cost;
					} else {
						$quote_data[$this->name . '_' . $row['sort_order']] = array(
							'id'						=> ($this->type == 'total' ? $this->name : $this->name . '.' . $this->name . '_' . $row['sort_order']),
							'code'						=> ($this->type == 'total' ? $this->name : $this->name . '.' . $this->name . '_' . $row['sort_order']),
							'title'						=> html_entity_decode($row['title'][$language], ENT_QUOTES, 'UTF-8') . ($version < 150 && $this->type == 'total' ? ':' : ''),
							'cost'						=> array($cost),
							'tax_class_id'				=> $row['tax_class_id'],
							'sort_order'				=> $this->getSetting('sort_order'),
							'multi_rate_calculation'	=> $row['multi_rate_calculation']
						);
					}
					
					break;
				}
			}
		}
		
		// Combine Rates
		$method_data = array();
		$round = (float)$this->getSetting('round');
		
		foreach ($quote_data as $key => $value) {
			if ($value['multi_rate_calculation'] == 'average') {
				$cost = array_sum($value['cost']) / count($value['cost']);
			} elseif ($value['multi_rate_calculation'] == 'highest') {
				$cost = max($value['cost']);
			} elseif ($value['multi_rate_calculation'] == 'lowest') {
				$cost = min($value['cost']);
			} elseif ($value['multi_rate_calculation'] == 'sum') {
				$cost = array_sum($value['cost']);
			}
			
			if ($round) {
				$cost = round($cost / $round) * $round;
			}
			
			if ($this->type == 'shipping') {
				if ($cost < 0) {
					unset($quote_data[$key]);
				} else {
					$quote_data[$key]['cost'] = $cost;
					$quote_data[$key]['text'] = $this->currency->format($this->tax->calculate($cost, $value['tax_class_id'], $this->config->get('config_tax')));
				}
			} else {
				$quote_data[$key]['value'] = $cost;
				$quote_data[$key]['text'] = $this->currency->format($cost);
				
				$total_data[] = $quote_data[$key];
				
				$tax_class_id = $value['tax_class_id'];
				if ($tax_class_id) {
					if (method_exists($this->tax, 'getRates')) {
						$tax_rates = $this->tax->getRates($cost, $tax_class_id);
						foreach ($tax_rates as $tax_rate) {
							$taxes[$tax_rate['tax_rate_id']] = (isset($taxes[$tax_rate['tax_rate_id']])) ? $taxes[$tax_rate['tax_rate_id']] : 0;
							$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
						}
					} else {
						$taxes[$tax_class_id] = (isset($taxes[$tax_class_id])) ? $taxes[$tax_class_id] : 0;
						$taxes[$tax_class_id] += $cost * $this->tax->getRate($tax_class_id) / 100;
					}
				}
				
				$order_total += $cost;
			}
		}
		
		if ($this->type == 'shipping') {
			if ($quote_data) {
				$heading = $this->getSetting('heading');
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
}
?>