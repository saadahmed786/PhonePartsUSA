<?php 
class ControllerCheckoutSuccess extends Controller { 
	public function index() { 
		
		$this->load->model('checkout/order');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		
		if(!isset($this->session->data['order_id']) and isset($this->session->data['temp_order_id']))
		{
				$this->session->data['order_id'] = $this->session->data['temp_order_id'];
		}
		if(isset($this->session->data['order_id']))
		{
			$this->session->data['success_order_id'] = $this->session->data['order_id'];
			$this->session->data['success_shipping_method_cost'] = $this->session->data['shipping_method']['cost'];
			$this->session->data['success_cc_number'] = $this->session->data['newcheckout']['cc_number'];
		}		

		

		if(!isset($this->session->data['order_id']) && !isset($this->session->data['success_order_id']))
		{
			$this->redirect($this->url->link('common/home'));
		}
		
		if (isset($this->session->data['success_order_id'])) {
			
			// Google Trusted Store Code Starts
			if ($this->config->get('config_gts_status')) {

			}
			$this->load->model('checkout/order');
				$this->data['orderDetails'] = $this->model_checkout_order->getOrder($this->session->data['success_order_id']);
				// print_r($this->data['orderDetails']);exit;
				$order_products = $this->model_checkout_order->getGTSOrderProduct($this->session->data['success_order_id']);  
				// print_r($order_products);exit;
				$this->data['orderProducts'] = array();
				foreach($order_products as $order_product)
				{
					$result = $this->model_catalog_product->getProduct($order_product['product_id']);
					if ($result['image']) {
								$image = $this->model_tool_image->resize($result['image'], 368, 383);
							} else {
								$image = $this->model_tool_image->resize('data/image-coming-soon.jpg', 368, 383);
							}
							// echo $image;exit;
							$this->data['orderProducts'][] = array(
								'name'	=> $order_product['name'],
								'quantity'=>$order_product['quantity'],
								'price'=>$this->currency->format($order_product['price']),
								'total'=>$this->currency->format($order_product['total']),
								'image' => $image,
								'model'=>$order_product['model']
								);
				}
				// print_r($this->data['orderProducts']);exit;


				$this->data['orderDetails']['shipping_total'] = (isset($this->session->data['success_shipping_method_cost'])) ? $this->session->data['success_shipping_method_cost'] : 0;
				$ship_date = strtotime("+3 day");
				$this->data['ship_date'] = date('Y-m-d', $ship_date);
				
				$deliv_date = strtotime("+5 day");
				$this->data['deliv_date'] = date('Y-m-d', $deliv_date);
			// Google Trusted Store Code ends
			
			
		// print_r($this->data['orderDetails']);exit;
			$this->cart->clear();
			$OrderDetails = $this->model_checkout_order->getOrder($this->session->data['success_order_id']);	
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
				
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['tmp_order_id']);
			 unset($this->session->data['unconfirmed_alert_sent']);
			unset($this->session->data['newcheckout']);
			unset($this->session->data['logged_in']);
			
			
		}	
									   
		$this->language->load('checkout/success');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->data['breadcrumbs'] = array(); 

      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => false
      	); 
		
      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('checkout/cart'),
        	'text'      => $this->language->get('text_basket'),
        	'separator' => $this->language->get('text_separator')
      	);
				
		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
			'text'      => $this->language->get('text_checkout'),
			'separator' => $this->language->get('text_separator')
		);	
					
      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('checkout/success'),
        	'text'      => $this->language->get('text_success'),
        	'separator' => $this->language->get('text_separator')
      	);
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

		if ($this->customer->isLogged()) {
    		$this->data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $this->url->link('account/download', '', 'SSL'), $this->url->link('information/contact'));
		} else {
    		$this->data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}
		
    	$this->data['button_continue'] = $this->language->get('button_continue');

    	$this->data['continue'] = $this->url->link('common/home');
		$this->data['Norton_Total'] = $OrderDetails['total'];
		$this->data['Norton_Order'] = $OrderDetails['order_id'];
		$this->data['Norton_Email'] = $OrderDetails['email'];
		$this->data['Total_Units']	= $OrderDetails['total_units'];
		$this->data['Customer_Status'] =  $this->model_checkout_order->ifCustomerIsNew($OrderDetails['email']);
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/common/success.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/common/success.tpl';
		} else {
			$this->template = 'default/template/common/success.tpl';
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'			
		);
				
		$this->response->setOutput($this->render());
  	}
}
?>