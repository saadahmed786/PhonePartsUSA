<?php
class ModelTotalOneCheckOutPaymentFee extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		
		if ($this->config->get('onecheckout_status') && $this->config->get('onecheckout_payment_fee_status') && $this->config->get('onecheckout_payment_fee_types') && isset($this->session->data['payment_method']['code']) && $this->cart->hasProducts()) {
			if(substr(VERSION,0,5) == '1.5.0'){
				$selected_payment_types = unserialize($this->config->get('onecheckout_payment_fee_types'));
			}else{
				$selected_payment_types = $this->config->get('onecheckout_payment_fee_types');
			}
					
				if (in_array($this->session->data['payment_method']['code'], $selected_payment_types)) {
						
					if (isset($this->session->data['guest']['shipping']['country_id']) && $this->session->data['guest']['shipping']['country_id']) {
					
						$country_id = $this->session->data['guest']['shipping']['country_id'];
						$zone_id = $this->session->data['guest']['shipping']['zone_id'];
					
					} elseif (isset($this->session->data['payment_address_id']) && $this->session->data['payment_address_id']) {
				
						$address_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$this->session->data['payment_address_id'] . "'");
						$country_id = $address_query->row['country_id'];
						$zone_id = $address_query->row['zone_id'];
					 
					} else {
				
						$country_id = 0;
						$zone_id = 0;
				
					}
							
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('onecheckout_payment_fee_geo_zone_id') . "' AND country_id = '" . (int)$country_id . "' AND (zone_id = '" . (int)$zone_id . "' OR zone_id = '0')");
					
					if ($total > $this->config->get('onecheckout_payment_fee_total')) {
						$status = false;
					} elseif (!$this->config->get('onecheckout_payment_fee_geo_zone_id')) {
						$status = true;
					} elseif ($query->num_rows) {
						$status = true;
					} else {
						$status = false;
					}
					
					if (($status) && ($this->cart->getSubTotal() > 0)) {
						
						$this->load->language('onecheckout/checkout');
						
						//$paymentfee = (($total + $this->config->get('onecheckout_payment_fee_fixed_fee')) / (1 - ($this->config->get('onecheckout_payment_fee_percentage')/100))) - $total;
						$paymentfee = ((float)$this->config->get('onecheckout_payment_fee_percentage')*$total)/100 + (float)$this->config->get('onecheckout_payment_fee_fixed_fee');
						
						$total_data[] = array( 
							'code'       => 'onecheckout_payment_fee',
							'title'      => $this->language->get('text_onecheckout_payment_fee'),
							'text'       => $this->currency->format($paymentfee),
							'value'      => $paymentfee,
							'sort_order' => $this->config->get('onecheckout_payment_fee_sort_order')
						);

						if ($this->config->get('onecheckout_payment_fee_tax_class_id')) {
							$this->load->model('onecheckout/checkout');	
							$version_int = $this->model_onecheckout_checkout->versiontoint();
							if($version_int >= 1513){
								if (!isset($taxes[$this->config->get('onecheckout_payment_fee_tax_class_id')])) {
								$tax_rates = $this->tax->getRates($paymentfee, $this->config->get('onecheckout_payment_fee_tax_class_id'));
										
								foreach ($tax_rates as $tax_rate) {
									if (!isset($taxes[$tax_rate['tax_rate_id']])) {
										$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
									} else {
										$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
									}
								}
								}
							} else {
								if (!isset($taxes[$this->config->get('onecheckout_payment_fee_tax_class_id')])) {
								$taxes[$this->config->get('onecheckout_payment_fee_tax_class_id')] = $paymentfee / 100 * $this->tax->getRate($this->config->get('onecheckout_payment_fee_tax_class_id'));
								} else {
								$taxes[$this->config->get('onecheckout_payment_fee_tax_class_id')] += $paymentfee / 100 * $this->tax->getRate($this->config->get('onecheckout_payment_fee_tax_class_id'));
								}
							}
							
						}

						$total += $paymentfee;
					}
				}
			
		}
	}
}
?>