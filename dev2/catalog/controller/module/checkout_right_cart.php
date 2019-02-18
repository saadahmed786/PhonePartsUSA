<?php 
class ControllerModuleCheckoutRightCart extends Controller {
	public function index() {

		// Totals
		$this->load->model('setting/extension');
		$this->load->model('account/address');

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
			$this->data['sign_product'] = $this->model_account_address->getSignProduct();
			// echo $this->data['sign_product']."<br>";
		$this->data['sign_product_exist'] = false;
		
		if (isset($this->session->data['cart'][$this->data['sign_product']])) {
			$this->data['sign_product_exist'] = 1;
		}
		// var_dump( $this->data['sign_product_exist']);
		// print_r($total_data);exit;


		$this->data['totals'] = $total_data;

		if (isset($this->request->post['postcode'])) {
			$this->data['postcode'] = $this->request->post['postcode'];				
		} elseif (isset($this->session->data['shipping_postcode'])) {
			$this->data['postcode'] = $this->session->data['shipping_postcode'];					
		} else {
			$this->data['postcode'] = '';
		}
		if (isset($this->request->post['shipping_method'])) {
			$this->data['shipping_method'] = $this->request->post['shipping_method'];				
		} elseif (isset($this->session->data['shipping_method'])) {
			$this->data['shipping_method'] = $this->session->data['shipping_method']['code']; 
		} else {
			$this->data['shipping_method'] = '';
		}
		// print_r($total_data);;
		$voucher_total = 0.00;
		$this->data['shipping_cost'] = $this->currency->format('0.00');
		$shipping_title ='';
		// print_r($total_data);exit;

		foreach ($total_data as $total) {
			if($total['code']=='sub_total')
			{
				$this->data['sub_total'] = $total['text'];
			}

			if($total['code']=='shipping')
			{
				$shipping_title = $total['title'];
				$this->data['shipping_cost'] = $total['text'];
			}

			elseif($total['code']=='total')
			{
				$this->data['total'] = $total['text'];
			}

			elseif($total['code']=='tax')
			{
				$this->data['tax'] = $total['text'];
			}

			elseif($total['code']=='voucher')
			{
				// $this->data['total'] = $total['text'];
				$voucher_total+=$total['value'];
			}
			


		}

		$quote_data = array();

			// $this->load->model('setting/extension');
		// print_r($_SESSION);exit;

			$this->data['shipping_title'] = 'N/A';
			$this->data['delivery_time'] = 'N/A';
			// print_r($this->session->data['shipping_methods']);exit;
			// echo $shipping_title;exit;
			foreach ($this->session->data['shipping_methods']['multiflatrate']['quote'] as $result) {
				
				// print_r($result);
				
						

						if($result['title']==$shipping_title)
						{

							$this->data['shipping_title'] = $shipping_title;
							$this->data['delivery_time'] = $result['delivery_time'];
						}
					
				
			}
			// exit;
			// print_r($quote_data);exit;







		// list vouchers
		$this->load->model('checkout/voucher');
		$this->load->model('checkout/coupon');
		$this->load->model('account/viewvouchers');
		
		$_temp_vouchers = array();
		
		if (!empty($this->session->data['voucher'])) {
			foreach ($this->session->data['voucher'] as $key ) {
				$voucher_detail = $this->model_checkout_voucher->getVoucher($key);
				if($voucher_detail)
				{
				$_temp_vouchers[] = $voucher_detail['code'];


				$this->data['vouchers'][] = array(
					'key'         => $voucher_detail['code'],
					'description' => $voucher_detail['description'],
					'amount'      => $this->currency->format($voucher_detail['amount']),
					'raw_amount'      => $voucher_detail['amount'],
					'remove'      => $this->url->link('checkout/cart', 'remove=' . $voucher_detail['code']),
					'is_special' => false  
					);
				}
				else
				{
					$voucher_detail = $this->model_checkout_coupon->getCoupon($key);
					if($voucher_detail)
				{
				

				$this->data['vouchers'][] = array(
					'key'         => $voucher_detail['code'],
					'description' => 'Bulk Ordered Coupon',
					'amount'      => $this->currency->format($voucher_detail['discount']),
					'raw_amount'      => 0.00,
					'remove'      => $this->url->link('checkout/cart', 'remove=' . $voucher_detail['code']) ,
					'is_special'	=> true  
					);
				}
				}

					// $voucher_total+=$voucher_detail['amount'];
			}
				// print_r($this->data['vouchers']);

		}

		$available_vouchers = array();
		if($this->customer->isLogged())
		{
			$_temps = $this->model_account_viewvouchers->getVouchers($this->customer->getEmail(), 0 * 10,10,90);
			
			foreach($_temps as $_temp)
			{
				if(!in_array($_temp['code'], $_temp_vouchers))
			{
				if($_temp['balance']>0.00)
				{
					$available_vouchers[] = $_temp;
				}
			}
			}
			
		


		}
		$this->data['available_vouchers'] = $available_vouchers;


		$this->data['voucher_total'] = $this->currency->format($voucher_total);

		$this->template = 'ppusa2.0/template/module/checkout_right_cart.tpl';
		$this->response->setOutput($this->render());	
	}
}
?>