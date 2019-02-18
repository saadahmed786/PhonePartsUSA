<?php 
class ModelPaymentTerms extends Model {
  	public function getMethod($address, $total) {
		$this->load->language('payment/terms');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('terms_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
	
		if ($this->config->get('terms_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('terms_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'terms',
        		'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('terms_sort_order')
      		);
    	}
   if($this->customer->isLogged())
   {
	   $customer_check_query = $this->db->query("SELECT is_termed FROM ".DB_PREFIX."customer WHERE email='".$this->customer->getEmail()."' AND is_termed=1");
	  
	   if($customer_check_query->row)
	   {
	   return $method_data;
	   }
	   else
	   {
			return array();
	   }
   }
    	
  	}
}
?>