<?php 
class ModelPaymentAuthorizeNetCim extends Model {
  	public function getMethod($address, $total) {
		$this->language->load('payment/authorizenet_cim');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('authorizenet_cim_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		if ($this->config->get('authorizenet_cim_total') > 0 && $this->config->get('authorizenet_cim_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('authorizenet_cim_geo_zone_id')) {
			$status = true;
		} elseif (isset($query->num_rows) && $query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}	
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'authorizenet_cim',
        		'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('authorizenet_cim_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
}
?>