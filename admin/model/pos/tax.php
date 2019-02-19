<?php
class ModelPosTax extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		$tax_check = $this->db->query("SELECT * FROM ".DB_PREFIX."order_total WHERE order_id='".$this->request->get['order_idx']."' AND code='tax'");
		$tax_check = $tax_check->row;
		if($tax_check && $tax_check['value']>0)
		{
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