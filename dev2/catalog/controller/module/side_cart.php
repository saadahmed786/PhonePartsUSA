<?php 
class ControllerModuleSideCart extends Controller {
	public function index() {
		$this->language->load('module/cart');
		
		if (isset($this->request->get['remove'])) {
			$this->cart->remove($this->request->get['remove']);
			
			unset($this->session->data['vouchers'][$this->request->get['remove']]);
		}	

		// Totals
		$this->load->model('setting/extension');
		
		$total_data = array();					
		$total = 0;
		$taxes = $this->cart->getTaxes();
		
		// Display prices
		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
			$sort_order = array(); 
			
			$results = $this->model_setting_extension->getExtensions('total');
			
			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}
			
			array_multisort($sort_order, SORT_ASC, $results);
			
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);

					$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
				}
				
				$sort_order = array(); 

				foreach ($total_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $total_data);			
			}		
		}
		
		$this->data['totals'] = $total_data;
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_items'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
		$this->data['v_text_items'] = $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0);
		$this->data['total_items'] = $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0);
		$this->data['text_empty'] = $this->language->get('text_empty');
		$this->data['text_cart'] = $this->language->get('text_cart');
		$this->data['text_checkout'] = $this->language->get('text_checkout');
		
		$this->data['button_remove'] = $this->language->get('button_remove');
		if (isset($this->request->get['returnTotal'])) {
			$json['total_items'] = $this->data['total_items'];
			$json['totals'] = '';
			foreach ($total_data as $total) {
				$json['totals'] .= '<tr>';
				$json['totals'] .= '<td><strong>' . $total['title'] . '</strong></td>';
				$json['totals'] .= '<td><span class="blue">' . $total['text'] . '</span></td>';
				$json['totals'] .= '</tr>';
			}
			echo json_encode($json);
			exit;
		}
		$this->load->model('tool/image');
		
		$this->data['products'] = array();

		foreach ($this->cart->getProducts() as $product) {
			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
			} else {
				$image = '';
			}

			$option_data = array();
			
			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['option_value'];	
				} else {
					$filename = $this->encryption->decrypt($option['option_value']);
					
					$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
				}				
				
				$option_data[] = array(								   
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
					'type'  => $option['type']
					);
			}
			
			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = false;
			}
			
			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$total = $this->currency->format($this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$total = false;
			}

			// Load Discounts for PPUSA 2.0
			$this->load->model('catalog/product');
			$discountsData = $this->model_catalog_product->getProductDiscounts($product['product_id']);
			
			$discounts = array(); 
			$discounts[] = array(
				'quantity' => '1' . (($discountsData[0]) ? '-' . ($discountsData[0]['quantity'] - 1) : ''),
				'price'    => $price
				);
			foreach ($discountsData as $key => $discount) {
				$discounts[] = array(
					'quantity' => $discount['quantity'] . (($discountsData[($key + 1)]) ? '-' . ($discountsData[($key + 1)]['quantity'] - 1) : '+'),
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
					);
			}

			$this->data['products'][] = array(
				'product_id'      => $product['product_id'],
				'key'      => $product['key'],
				'thumb'    => $image,
				'name'     => $product['name'],
				'model'    => $product['model'], 
				'option'   => $option_data,
				'quantity' => $product['quantity'],
				'price'    => $price,	
				'discounts'    => $discounts,
				'total'    => $total,	
				'href'     => $this->url->link('product/product', 'product_id=' . $product['product_id'])		
				);
		}
		
		// Gift Voucher
		$this->data['vouchers'] = array();
		
		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$this->data['vouchers'][] = array(
					'key'         => $key,
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'])
					);
			}
		}

		$this->data['cart'] = $this->url->link('checkout/cart');

		$this->data['checkout'] = $this->url->link('checkout/checkout', 'nc=1', 'SSL');

		$this->data['data'] = $this->data;

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/side_cart.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/side_cart.tpl';
		} else {
			$this->template = 'default/template/module/side_cart.tpl';
		}

		$this->response->setOutput($this->render());		
	}
}
?>