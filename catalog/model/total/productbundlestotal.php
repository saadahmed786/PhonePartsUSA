<?php
class ModelTotalProductBundlesTotal extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		$cartProducts = $this->cart->getProducts();
		$cartProductsFlat = array();
		$cartProductsQuantities = array();
		$matchingBundles = array();
		foreach ($cartProducts as $product) {
			$cartProductsFlat[] = $product['product_id'];
			if (empty($cartProductsQuantities[$product['product_id']])) {
				$cartProductsQuantities[$product['product_id']] = $product['quantity'];
			} else {
				$cartProductsQuantities[$product['product_id']] += $product['quantity'];
			}
		}
		
		$bundles = $this->config->get('productbundles_custom');
		$setting = $this->config->get('ProductBundles');
		$discountsApply = (isset($setting['MultipleBundles']) && ($setting['MultipleBundles']=='yes')) ? true : false;
		
		if (isset($bundles)) {
			foreach ($bundles as $bundle) {
				if (array_diff($bundle['products'], $cartProductsFlat) === array()) {
					$bundleQuantities = array();
					foreach($bundle['products'] as $product_id) {
						if (empty($bundleQuantities[$product_id])) {
							$bundleQuantities[$product_id] = 1;
						} else {
							$bundleQuantities[$product_id]++;
						}
					}
					
					for(;;) {
						foreach($bundleQuantities as $product_id=>$quantity) {
							if (!isset($cartProductsQuantities[$product_id]) || ($quantity > $cartProductsQuantities[$product_id])) {
								continue 3;
							}
						}
						
						foreach($bundleQuantities as $product_id=>$quantity) {
							$cartProductsQuantities[$product_id] -= $quantity;
						}
						
						if (!array_key_exists($bundle['id'], $matchingBundles)) {
							$matchingBundles[$bundle['id']] = array();
							$matchingBundles[$bundle['id']][] = $bundle;
						} else if ($discountsApply) {
							$matchingBundles[$bundle['id']][] = $bundle;
						}
					}
				}
			}
			
			if (!empty($matchingBundles)) {
				$this->language->load('total/productbundlestotal');
				
				$grandTotal = 0;
				foreach ($matchingBundles as $bundle) {
					foreach($bundle as $instance) {
						$grandTotal += (float)$instance['voucherprice'];
					}
				}
				
				$total_data[] = array(
					'code'       => 'productbundlestotal',
					'title'      => $this->language->get('entry_title'),
					'text'       => $this->currency->format(-$grandTotal),
					'value'      => -$grandTotal,
					'sort_order' => $this->config->get('productbundlestotal_sort_order')
				);
		
				$total -= (float)$grandTotal;
				if ($total < 0) {
					$total = 0;
				}
			}
		}
	}	
}
?>