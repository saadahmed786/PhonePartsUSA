<?php
class ModelTotalTax extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		if ($this->session->data['customer_id']) {
			$dis_tax = $this->db->query("SELECT dis_tax FROM " . DB_PREFIX . "customer WHERE customer_id = '" . $this->session->data['customer_id'] . "'")->row['dis_tax'];
		} else {
			$email = $this->db->query("SELECT email FROM " . DB_PREFIX . "order WHERE order_id = '" . $this->session->data['order_id'] . "'")->row['email'];
			$dis_tax = $this->db->query("SELECT dis_tax FROM " . DB_PREFIX . "customer WHERE email = '" . $email . "'")->row['dis_tax'];
		}
		if (!$dis_tax) {
			foreach ($taxes as $key => $value) {
				if ($value > 0) {
					$total_data[] = array(
						'code'       => 'tax',
						'title'      => $this->tax->getRateName($key), 
						'text'       => $this->currency->format($value),
						'value'      => $value,
						'sort_order' => $this->config->get('tax_sort_order')
						);

					$total += $value;
				}
			}
		}
	}
}
?>