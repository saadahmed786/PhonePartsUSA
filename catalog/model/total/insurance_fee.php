<?php
class ModelTotalInsuranceFee extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		
		if (isset($this->session->data['insurance_fee']) && $this->session->data['insurance_fee'] && $this->getstatus()) {
	
				//$this->load->language('onecheckout/checkout');
				$this->load->language('total/insurance_fee');
						
				$paymentfee = ((float)$this->config->get('insurance_fee_percentage')*$this->cart->getSubTotal())/100 + (float)$this->config->get('insurance_fee_fixed_fee');
						
				$total_data[] = array( 
					'code'       => 'insurance_fee',
					'title'      => $this->language->get('text_insurance_fee'),
					'text'       => $this->currency->format($paymentfee),
					'value'      => $paymentfee,
					'sort_order' => $this->config->get('insurance_fee_sort_order')
				);

				$total += $paymentfee;
			
		}
	}
	
	public function getstatus() {
			if($this->config->get('insurance_fee_status') && $this->config->get('insurance_fee_selection') && $this->cart->hasProducts()){		
				
				if (isset($this->session->data['guest']['shipping']['country_id']) && $this->session->data['guest']['shipping']['country_id']) {
					
					$country_id = $this->session->data['guest']['shipping']['country_id'];
					$zone_id = $this->session->data['guest']['shipping']['zone_id'];
					
				} elseif (isset($this->session->data['shipping_address_id']) && $this->session->data['shipping_address_id']) {
				
					$address_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$this->session->data['shipping_address_id'] . "'");
					$country_id = $address_query->row['country_id'];
					$zone_id = $address_query->row['zone_id'];
					 
				} else {
				
					$country_id = 0;
					$zone_id = 0;
				
				}
							
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('insurance_fee_geo_zone_id') . "' AND country_id = '" . (int)$country_id . "' AND (zone_id = '" . (int)$zone_id . "' OR zone_id = '0')");
					
				if (!$this->config->get('insurance_fee_geo_zone_id')) {
					
					return true;
				
				} elseif ($query->num_rows) {
				
					return true;
				
				} else {
				
					return false;
				
				}
				
			} else {
		
				return false;
			}
	}
}
?>