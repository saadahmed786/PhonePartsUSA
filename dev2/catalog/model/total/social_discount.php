<?php
class ModelTotalSocialDiscount extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		if(isset($this->session->data['social_discount'])) {
		
		if (($this->config->get('social_discount_type') == 'F' && $this->cart->getSubTotal() > $this->config->get('social_discount_discount')) || ($this->config->get('social_discount_type') == 'P' && $this->cart->getSubTotal() > 0)) {
			
			if($this->config->get('social_discount_type') == 'F') {
			$discount = -$this->config->get('social_discount_discount');
			} else {
			$discount = -($total*$this->config->get('social_discount_discount'))/100;
			}
			
			$total_data[] = array( 
				'code'       => 'social_discount',
        		'title'      => $this->session->data['social_discount'],
        		'text'       => $this->currency->format($discount),
        		'value'      => $discount,
				'sort_order' => $this->config->get('social_discount_sort_order')
			);
		
			
			$total += $discount;	
			
			}
		}
	}
}
?>