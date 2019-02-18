<?php
//-----------------------------------------
// Author: Qphoria@gmail.com
// Web: http://www.OpenCartGuru.com/
//-----------------------------------------
class ModelPaymentPaypalExpress extends Model {
  	public function getMethod($address) {
		$name = str_replace('vq2-catalog_model_payment_', '', basename(__FILE__, '.php'));

		// Allow use of express button only, hide this in the payment area to avoid confusion.
		if (!$this->config->get($name . '_checkout')) { return array(); }

		$this->load->language('payment/' . $name);

		if ($this->config->get($name . '_status')) {

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get($name . '_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

			if (!$this->config->get($name . '_geo_zone_id')) {
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
        		'id'		 => $name, //v14x
				'code'		 => $name, //v15x
        		'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get($name . '_sort_order')
      		);
    	}

    	if (method_exists($this->document, 'addBreadcrumb')) { //1.4.x
			unset($this->session->data['payment_method']);
      		//Q: If this module is installed & set in session...
			if (isset($this->session->data['ppx']['token'])) {

				// If coming from the "change" link on the checkout/confirm page, then allow user to change payment...
				if (isset($_SERVER['HTTP_REFERER'])) {
					$referer = parse_url($_SERVER['HTTP_REFERER']);
					//unset($this->session->data['payment_method']);
				}

				if (!isset($referer['query']) || (isset($referer['query']) && $referer['query'] != 'route=checkout/confirm' && $referer['query'] != 'route=checkout/payment')) {
					$this->session->data['payment_method'] = $method_data;
					$this->session->data['comment'] = (isset($this->session->data['comment'])) ? $this->session->data['comment'] : '';
					if ($this->customer->getId()) {
						$this->response->redirect((((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?route=checkout/confirm'));
					}
				}
			}
		}

    	return $method_data;
  	}
}
?>
