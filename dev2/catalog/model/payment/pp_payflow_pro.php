<?php
class ModelPaymentPPPayFlowPro extends Model {
  	public function getMethod($address, $total) { 
		if (version_compare('1.5.5',VERSION,'>')) {
			//Opencart version less than 1.5.5.0
			$this->load->language('payment/pp_payflow_pro');
		}else {
			$this->language->load('payment/pp_payflow_pro');
		}

		if ($this->config->get('pp_payflow_pro_status')) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('pp_payflow_pro_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

			if ($this->config->get('pp_payflow_pro_total') > $total) {
				$status = FALSE;
			}elseif (!$this->config->get('pp_payflow_pro_geo_zone_id')) {
				$status = TRUE;
			} elseif ($query->num_rows) {
				$status = TRUE;
			} else {
				$status = FALSE;
			}
		} else {
			$status = FALSE;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'         => 'pp_payflow_pro',
				'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('pp_payflow_pro_sort_order')
			);
		}

    	return $method_data;
  	}
}
?>
