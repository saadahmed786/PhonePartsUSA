<?php
class ModelCheckoutCoupon extends Model {
	public function getCoupon($code) {
		$status = true;
		$this->load->model('catalog/product');
		
		$coupon_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon WHERE code = '" . $this->db->escape($code) . "' ".($has_limit?' AND  has_product_limit=1 AND product_limit_qty>0':'')." AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1'");

		// echo "SELECT * FROM " . DB_PREFIX . "coupon WHERE code = '" . $this->db->escape($code) . "' ".($has_limit?' AND  has_product_limit=1 AND product_limit_qty>0':'')." AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1'";exit;
			
		if ($coupon_query->num_rows) {
			if($coupon_query->row['has_product_limit']==0)
			{
			if ($coupon_query->row['total'] >= $this->cart->getSubTotal()) {
				$status = false;
			}
		
			$coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch WHERE ch.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");

			if ($coupon_query->row['uses_total'] > 0 && ($coupon_history_query->row['total'] >= $coupon_query->row['uses_total'])) {
				$status = false;
			}
			
			if ($coupon_query->row['logged'] && !$this->customer->getId()) {
				$status = false;
			}
			
			if ($this->customer->getId()) {
				$coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch WHERE ch.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "' AND ch.customer_id = '" . (int)$this->customer->getId() . "'");
				
				if ($coupon_query->row['uses_customer'] > 0 && ($coupon_history_query->row['total'] >= $coupon_query->row['uses_customer'])) {
					$status = false;
				}
			}
				
			$coupon_product_data = array();
				
			$coupon_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");
			
			

			foreach ($coupon_product_query->rows as $result) {
				$coupon_product_data[] = $result['product_id'];
			}
				
			if ($coupon_product_data) {
				$coupon_product = false;
					
				foreach ($this->cart->getProducts() as $product) {
					if (in_array($product['product_id'], $coupon_product_data)) {
						$coupon_product = true;
							
						break;
					}
				}
					
				if (!$coupon_product) {
					$status = false;
				}
			}
		}
		else
		{
			$product_limit_qty=0;
			foreach ($this->cart->getProducts() as $product) {
				$product_class =	$this->model_catalog_product->getProductClass($product['model']);
				$product_quality = $this->model_catalog_product->getProductQuality($product['model']);
				// echo $product_quality;exit;
				if ((strtolower($product_class['name']) == 'screen-lcdtouchscreenassembly' || strtolower($product_class['name']) == 'screen-touchscreen') && strtolower($product_quality)=='economy plus') {
					// echo $product['quantity'];exit;
					$product_limit_qty+=(int)$product['quantity'];

				}
			}
			// echo $product_limit_qty;exit;
			if($product_limit_qty<$coupon_query->row['product_limit_qty'])
			{
				$status = false;
			}
		}
		} else {
			$status = false;
		}
		
		$coupon_customer_group_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon_customer_group WHERE coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");
		
		
		foreach($coupon_customer_group_query->rows as $result)
		{
			$coupon_customer_group_data[] = $result['customer_group_id'];	
			
		}
		
		
		if($coupon_customer_group_data){
				$coupon_cg = false;
					
				$CustomerGroupID = $this->customer->getCustomerGroupId() ;
				if($CustomerGroupID=='') $CustomerGroupID=-1;
				
				
					if (in_array($CustomerGroupID, $coupon_customer_group_data)) {
						$coupon_cg = true;
							
						
					}
					else
					{
					$coupon_cg = false;	
					}
				
					
				if (!$coupon_cg) {
					$status = false;
				}
			}
			
		
		
		
		if ($status) {
			return array(
				'coupon_id'     => $coupon_query->row['coupon_id'],
				'code'          => $coupon_query->row['code'],
				'name'          => $coupon_query->row['name'],
				'type'          => $coupon_query->row['type'],
				'discount'      => $coupon_query->row['discount'],
				'shipping'      => $coupon_query->row['shipping'],
				'total'         => $coupon_query->row['total'],
				'maximum_amount'=> $coupon_query->row['maximum_amount'],
				'product'       => $coupon_product_data,
				'date_start'    => $coupon_query->row['date_start'],
				'date_end'      => $coupon_query->row['date_end'],
				'uses_total'    => $coupon_query->row['uses_total'],
				'uses_customer' => $coupon_query->row['uses_customer'],
				'status'        => $coupon_query->row['status'],
				'date_added'    => $coupon_query->row['date_added']
			);
		}
	}
	
	public function redeem($coupon_id, $order_id, $customer_id, $amount) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "coupon_history` SET coupon_id = '" . (int)$coupon_id . "', order_id = '" . (int)$order_id . "', customer_id = '" . (int)$customer_id . "', amount = '" . (float)$amount . "', date_added = NOW()");
	}
}
?>