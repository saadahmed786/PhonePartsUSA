<?php 
class ControllerAccountOrder extends Controller {
	private $error = array();

	public function getOrder()
	{
		$this->load->model('account/order');
		$results = $this->model_account_order->getOrders();
		$json = [];
		foreach ($results as $key => $result) {
			$json[$key]['date_added'] = date($this->language->get('date_format_short'), strtotime($result['date_added']));
			$json[$key]['total'] = $this->currency->format($result['total']);

		}
		 
		echo	json_encode($json);
		 
	}

	public function index() {

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order', '', 'SSL');

			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}
		
		$this->language->load('account/order');
		
		$this->load->model('account/order');
		$this->load->model('account/address');

		if (isset($this->request->get['order_id'])) {
			$order_info = $this->model_account_order->getOrder($this->request->get['order_id']);
			
			if ($order_info) {
				$order_products = $this->model_account_order->getOrderProducts($this->request->get['order_id']);

				foreach ($order_products as $order_product) {
					$option_data = array();

					$order_options = $this->model_account_order->getOrderOptions($this->request->get['order_id'], $order_product['order_product_id']);

					foreach ($order_options as $order_option) {
						if ($order_option['type'] == 'select' || $order_option['type'] == 'radio') {
							$option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
						} elseif ($order_option['type'] == 'checkbox') {
							$option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
						} elseif ($order_option['type'] == 'input' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
							$option_data[$order_option['product_option_id']] = $order_option['value'];	
						} elseif ($order_option['type'] == 'file') {
							$option_data[$order_option['product_option_id']] = $this->encryption->encrypt($order_option['value']);
						}
					}

					$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->request->get['order_id']);

					$this->cart->add($order_product['product_id'], $order_product['quantity'], $option_data);
				}

				$this->redirect($this->url->link('checkout/cart'));
			}
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),        	
			'separator' => false
			); 

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_account'),
			'href'      => $this->url->link('account/account', '', 'SSL'),        	
			'separator' => $this->language->get('text_separator')
			);
		
		$url = '';
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('account/order', $url, 'SSL'),        	
			'separator' => $this->language->get('text_separator')
			);

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_order_id'] = $this->language->get('text_order_id');
		$this->data['text_status'] = $this->language->get('text_status');
		$this->data['text_date_added'] = $this->language->get('text_date_added');
		$this->data['text_customer'] = $this->language->get('text_customer');
		$this->data['text_products'] = $this->language->get('text_products');
		$this->data['text_total'] = $this->language->get('text_total');
		$this->data['text_empty'] = $this->language->get('text_empty');

		$this->data['button_view'] = $this->language->get('button_view');
		$this->data['button_reorder'] = $this->language->get('button_reorder');
		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['dashboard'] = $this->url->link('account/account', '', 'SSL');
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$this->data['orders'] = array();
		
		$order_total = $this->model_account_order->getTotalOrders();
		
		$results = $this->model_account_order->getOrders(($page - 1) * 10, 10);
		
		$kk=0;
		foreach ($results as $result) {
			if($kk>=2 && isset($this->session->data['vieworder'])) 
			{
				break;
			}
			$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
			$voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);
			$tracking = $this->model_account_order->getTracking($result['order_id']);
			// $tracking_carrier = $result['service_code'];
			// if(!$tracking_carrier)
			// {
				$tracking_carrier = $tracking[0]['service_code'];
			// }
			if($tracking_carrier)
			{
				$tracking_carrier = str_replace("_", " ", $tracking_carrier);
				$tracking_carrier = ucwords($tracking_carrier);

				$to_be_replaced = array('Ups'=>'UPS','Usps'=>'USPS');
				$tracking_carrier = str_replace(array_keys($to_be_replaced), array_values($to_be_replaced), $tracking_carrier);
			
			}
			$this->data['orders'][] = array(
				'order_id'   => $result['order_id'],
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'status'     => $result['status'],
				'shipped_to'     => $result['shipping_address_1'],
				'zone' => $result['shipping_zone'],
				'country' => $result['shipping_country'],
				'city' => $result['shipping_city'],
				'postcode' => $result['shipping_postcode'],
				'tracking_id'     => ($tracking[0]['tracking_code']?$this->trackingLink($tracking[0]['tracking_code'],$tracking_carrier):''),
				'tracking_service' => $tracking_carrier,
				'tracking' => $tracking,
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'products'   => ($product_total + $voucher_total),
				'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'href'       => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], 'SSL'),
				'reorder'    => $this->url->link('account/order', 'order_id=' . $result['order_id'], 'SSL')
				);
			$json[] = array(
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value'])
				);
			json_encode($json);	 
			$kk++;
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('account/order', 'page={page}', 'SSL');
		
		$this->data['pagination'] = $pagination->render();

		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');
		$this->data['return'] = $this->url->link('account/return/insert', '', 'SSL');

		if (isset($this->session->data['vieworder'])) {
			$this->data['ischild'] = $this->session->data['vieworder'];
		}

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/order_list.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/order_list.tpl';
		} else {
			$this->template = 'default/template/account/order_list.tpl';
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
	protected function trackingLink($tracking_number,$tracking_carrier)
	{

		$usps_link = '<a href="https://tools.usps.com/go/TrackConfirmAction?tLabels=%s">%s</a>';
		$ups_link = '<a href="http://wwwapps.ups.com/WebTracking/processRequest?HTMLVersion=5.0&Requester=NES&AgreeToTermsAndConditions=yes&loc=en_US&tracknum=%s">%s</a>';
		$fedex_link = '<a href="https://www.fedex.com/apps/fedextrack/?action=track&language=english&tracknumbers=%s">%s</a>';
		if(stristr($tracking_carrier, 'USPS'))
		{

			$tracking_no = sprintf ( $usps_link, $tracking_number, $tracking_number );
			//echo $tracking_no;exit;
		}
		else if (stristr($tracking_carrier, 'UPS')) {
			$tracking_no = sprintf ( $ups_link, $tracking_number, $tracking_number );
		}
		else
		{
			$tracking_no = sprintf ( $fedex_link, $tracking_number, $tracking_number );
		}
		return $tracking_no;
	}	
	public function info() { 
		$this->language->load('account/order');
		
		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}	

		if (!$this->customer->isLogged()) {
			//$this->session->data['redirect'] = $this->url->link('account/order', 'order_id=' . $order_id, 'SSL');
			
			//$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->load->model('account/order');
		$this->load->model('checkout/order');
		
		$order_info = $this->model_account_order->getOrder($order_id);
		$this->data['checkout_order_info'] = $this->model_checkout_order->getOrder($order_id);
		$process_class = 'process-bar';
		if($order_info['order_status_id']==3 || $order_info['order_status_id']==5 )
		{
			$process_class = 'process-bar3';
		}
		else if($order_info['order_status_id']==15)
		{
			$process_class = 'process-bar2';
		}
		else if($order_info['order_status_id']==21)
		{
			$process_class = 'process-bar1';
		}
		$this->data['process_class']  = $process_class;
		// if(!$order_info)
		// {
		// 	$this->redirect($this->url->link('common/home', '', 'SSL'));
		// 	exit;
		// }
		// print_r($order_info);exit;
		if ($order_info) {
			$this->document->setTitle($this->language->get('text_order'));
			
			$this->data['breadcrumbs'] = array();

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home'),        	
				'separator' => false
				); 

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_account'),
				'href'      => $this->url->link('account/account', '', 'SSL'),        	
				'separator' => $this->language->get('text_separator')
				);
			
			$url = '';
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('account/order', $url, 'SSL'),      	
				'separator' => $this->language->get('text_separator')
				);
			
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_order'),
				'href'      => $this->url->link('account/order/info', 'order_id=' . $this->request->get['order_id'] . $url, 'SSL'),
				'separator' => $this->language->get('text_separator')
				);

			$this->data['heading_title'] = $this->language->get('text_order');

			$this->data['customer_name'] = $this->customer->getFirstName();
			$this->data['order_total'] = $order_info['total'];
			$this->data['shipping_cost'] = (float)$order_info['shipping_cost']; 
			$this->data['customer_email'] = $this->customer->getEmail();
			$this->data['telephone'] = $this->customer->getTelephone();
			$this->data['text_order_detail'] = $this->language->get('text_order_detail');
			$this->data['text_invoice_no'] = $this->language->get('text_invoice_no');
			$this->data['text_order_id'] = $this->language->get('text_order_id');
			$this->data['text_date_added'] = $this->language->get('text_date_added');
			$this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
			$this->data['text_shipping_address'] = $this->language->get('text_shipping_address');
			$this->data['text_payment_method'] = $this->language->get('text_payment_method');
			$this->data['text_payment_address'] = $this->language->get('text_payment_address');
			$this->data['text_history'] = $this->language->get('text_history');
			$this->data['text_comment'] = $this->language->get('text_comment');

			$this->data['column_name'] = $this->language->get('column_name');
			$this->data['column_model'] = $this->language->get('column_model');
			$this->data['column_quantity'] = $this->language->get('column_quantity');
			$this->data['column_price'] = $this->language->get('column_price');
			$this->data['column_total'] = $this->language->get('column_total');
			$this->data['column_action'] = $this->language->get('column_action');
			$this->data['column_date_added'] = $this->language->get('column_date_added');
			$this->data['column_status'] = $this->language->get('column_status');
			$this->data['column_comment'] = $this->language->get('column_comment');
			
			$this->data['button_return'] = $this->language->get('button_return');
			$this->data['button_continue'] = $this->language->get('button_continue');

			if ($order_info['invoice_no']) {
				$this->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$this->data['invoice_no'] = '';
			}
			
			$this->data['order_id'] = $this->request->get['order_id'];
			$this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
			
			if ($order_info['payment_address_format']) {
				$format = $order_info['payment_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
				);

			$replace = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'   => $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city'      => $order_info['payment_city'],
				'postcode'  => $order_info['payment_postcode'],
				'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
				'country'   => $order_info['payment_country']  
				);
			
			$this->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$this->data['payment_method'] = $order_info['payment_method'];
			
			if ($order_info['shipping_address_format']) {
				$format = $order_info['shipping_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
				);

			$replace = array(
				'firstname' => $order_info['shipping_firstname'],
				'lastname'  => $order_info['shipping_lastname'],
				'company'   => $order_info['shipping_company'],
				'address_1' => $order_info['shipping_address_1'],
				'address_2' => $order_info['shipping_address_2'],
				'city'      => $order_info['shipping_city'],
				'postcode'  => $order_info['shipping_postcode'],
				'zone'      => $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
				'country'   => $order_info['shipping_country']  
				);

			$this->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$this->data['shipping_method'] = $order_info['shipping_method'];
			
			$this->data['products'] = array();
			
			$products = $this->model_account_order->getOrderProducts($this->request->get['order_id']);
			$this->load->model('catalog/product');
			$this->load->model('tool/image');
			foreach ($products as $product) {

				$_result = $this->model_catalog_product->getProduct($product['product_id']);

				if ($_result['image']) {
					$_image = $this->model_tool_image->resize($_result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				} else {
					$_image = false;
				}

				$option_data = array();
				
				$options = $this->model_account_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);

				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
					}
					
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
						);					
				}

        		// if the product name is blank, take from the product page
				if(trim($product['name'])=='')
				{

					$__p =  $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product['product_id'] . "'");
					$_p = $__p->row;

					$product['name'] = $_p['name'];
				}
					// end check

				$this->data['products'][] = array(
					'name'     => $product['name'],
					'model'    => $product['model'],
					'option'   => $option_data,
					'thumb'	   => $_image,
					'quantity' => $product['quantity'],
					'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					'return'   => $this->url->link('account/return/insert', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], 'SSL'),
					'href'        => $this->url->link('product/product', 'product_id=' . $_result['product_id'])
					);
			}

			// Voucher
			$this->data['vouchers'] = array();
			
			$vouchers = $this->model_account_order->getOrderVouchers($this->request->get['order_id']);
			

			foreach ($vouchers as $voucher) {
				$this->data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
					);
			}
			
			$this->data['totals'] = $this->model_account_order->getOrderTotals($this->request->get['order_id']);
			
			$this->data['comment'] = nl2br($order_info['comment']);
			
			$this->data['histories'] = array();

			$results = $this->model_account_order->getOrderHistories($this->request->get['order_id']);

			foreach ($results as $result) {
				$this->data['histories'][] = array(
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'status'     => $result['status'],
					'comment'    => nl2br($result['comment'])
					);
			}

			$this->data['continue'] = $this->url->link('account/order', '', 'SSL');

			// order store credits if any

			$order_vouchers = $this->db->query("SELECT a.code FROM ".DB_PREFIX."voucher a,".DB_PREFIX."order_voucher b where a.voucher_id=b.voucher_id and b.order_id='".(int)$order_id."'");
			$this->data['voucher_codes'] = $order_vouchers->rows;

			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/order_info.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/order_info.tpl';
			} else {
				$this->template = 'default/template/account/order_info.tpl';
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
		} else {
			$this->document->setTitle('Order not Found');
			
			$this->data['heading_title'] = 'Order not Found';
			if($this->customer->isLogged())
			{
			$this->data['text_error'] = "Oops! No matching Order ID found in the system, please try again.";

			}
			else
			{
			$this->data['text_error'] = "Oops! seems like you're not logged in, please try logging in and locate the Order ID";
				
			}

			$this->data['button_continue'] = $this->language->get('button_continue');
			
			$this->data['breadcrumbs'] = array();

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home'),
				'separator' => false
				);
			
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_account'),
				'href'      => $this->url->link('account/account', '', 'SSL'),
				'separator' => $this->language->get('text_separator')
				);

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('account/order', '', 'SSL'),
				'separator' => $this->language->get('text_separator')
				);
			
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_order'),
				'href'      => $this->url->link('account/order/info', 'order_id=' . $order_id, 'SSL'),
				'separator' => $this->language->get('text_separator')
				);

			$this->data['continue'] = $this->url->link('account/order', '', 'SSL');

			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/error/not_found.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/error/not_found.tpl';
			} else {
				$this->template = 'default/template/error/not_found.tpl';
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
	public function printout()
	{
		require_once('system/html2_pdf_lib/html2pdf.class.php');

		$this->language->load('account/order');
		$this->load->model('account/order');
		$this->load->model('checkout/order');

		
		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}	

		$order_info = $this->model_account_order->getOrder($order_id);

		if (!$this->customer->isLogged() or !$order_info) {
			// $this->session->data['redirect'] = $this->url->link('account/order', 'order_id=' . $order_id, 'SSL');
			
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		
		
		
		$logo =  "image/logo_new.png";
		
		$html = '
<style>
	.grey{
		color:#878D91;	
	}
	.dark-grey{
		color:#817D7D;	
	}
	.bold{
		font-weight:bold;	
	}
	.right{
		text-align:right;	
	}
	.normal{
		font-size:10px;
	}
	.detail{
		font-size:10px;
		color:#878D91;	
	}
	.nobreak {
		page-break-before: always;
	}
</style>';
$order_id = $order_info['order_id'];
$vouchers = $this->db->query('SELECT *, `a`.`amount` as `used` FROM `'.DB_PREFIX.'voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND a.`order_id` = "'. $order_id .'"');
$total_vouchers = 0.00;
$coupons = $this->db->query('SELECT *, `a`.`amount` as `used` FROM `'.DB_PREFIX.'coupon_history` as a, `oc_coupon` as b WHERE a.`coupon_id` = b.`coupon_id` AND a.`order_id` = "'. $order_id .'"');
$total_coupons = 0.00;

foreach ($vouchers as $key => $voucher) {
	$total_vouchers += str_replace('-', '', $voucher['used']);
}
foreach ($coupons as $key => $coupon) {
	$total_coupons += str_replace('-', '', $coupon['used']);
}
$items = $this->model_account_order->getOrderProducts($order_id);
$sub_total = 0.00;

$order_totals = $this->model_account_order->getOrderTotals($order_id);

foreach($order_totals as $order_total)
{
	if($order_total['code']=='sub_total')
	{
		$sub_total = $order_total['value'];
	}
	else if($order_total['code']=='total')
	{
		$invoice_total = $order_total['value'];
	} 
	else if($order_total['code']=='shipping')
	{
		$shipping_total = $order_total['value'];
	} 
	else if($order_total['code']=='tax')
	{
		$tax = $order_total['value'];
	} 
}



$header='<page><page_footer>
<table class="page_footer" align="right">
	<tr>
		<td align="right" style="width: 100%; text-align: right">
			Page [[page_cu]] of [[page_nb]]
		</td>
	</tr>
</table>
</page_footer><table border="0" >
<tr>
	<td style="width:560px">
		<img src="' . $logo . '">
	</td>';

	$header.='<td  style="font-size:34px;" class="right" >
	INVOICE
</td>';

$header.='
</tr>
<tr>
	<td style="font-weight:bold">PhonePartsUSA.com LLC</td>
	<td  class="dark-grey bold right" style="font-size:12px"># ' . $order_id . '</td>
</tr>
<tr>
	<td class="grey">5145 South Arville Street Suite A</td>
	<td class="bold right" > </td>
</tr>
<tr>
	<td class="grey">Las Vegas NV 89118</td>
	<td > </td>
</tr>
<tr>
	<td class="grey">U.S.A</td>';

	$header.='<td class="bold right" style="font-size:10px;">Invoice Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
	$header.='</tr>';

	$header.='
		<tr>
			<td> </td>
			<td class="right bold" style="font-size:17px">' . $this->currency->format($invoice_total) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr>';

		$header.='
	<tr>
		<td colspan="2">
			<table  border="0">
				<tr>
					<td valign="top" >
						<table border="0" >
							<tr>
								<td style="width:250px" class="grey" >Bill To</td>
							</tr>
							<tr>
								<td>' . $order_info['payment_firstname'].' '.$order_info['payment_firstname'] . '</td>
							</tr>
							<tr>
								<td>' . $order_info['payment_company'].'</td>
							</tr>
							<tr>
								<td>' . $order_info['payment_address_1'] . '</td>
							</tr>
							<tr>
								<td>' . $order_info['payment_city'] . ', ' . $order_info['payment_zone'] . ' ' . $order_info['payment_postcode'] . '</td>
							</tr>
							<tr>
								<td>' . $order_info['payment_country'] . '</td>
							</tr>
						</table>
					</td>
					<td valign="top" >
						<table border="0" >
							<tr>
								<td style="width:250px" class="grey" >Ship To</td>
							</tr>
							<tr>
								<td>' . $order_info['shipping_firstname'].' '.$order_info['shipping_lastname'] . '</td>
							</tr>
							<tr>
								<td>' . $order_info['shipping_company'].'</td>
							</tr>
							<tr>
								<td>' . $order_info['shipping_address_1'] . '</td>
							</tr>
							<tr>
								<td>' . $order_info['shipping_city'] . ', ' . $order_info['shipping_zone'] . ' ' . $order_info['shipping_postcode'] . '</td>
							</tr>
							<tr>
								<td>' . $order_info['shipping_company'] . '</td>
							</tr>
						</table>
					</td>
					<td  align="right">
						<table align="left" border="0" cellspacing="10" >
							<tr>
								<td class="right grey">Invoice Date :</td>
								<td class="right" >' . date('d M Y', strtotime($order_info['date_added'])) . '
								</td>
							</tr>';

							//
							if($order_info['payment_method']=='Cash or Credit at Store Pick-Up')
						{
							$order_info['payment_method']='Cash / Credit';
						}
						if($order_info['payment_method']=='Credit or Debit Card (Processed securely by PayPal)')
						{
							$order_info['payment_method']='Credit or Debit Card';
						}
						$header.='<tr>
						<td class="right grey" >Payment Method :</td>
						<td class="right" style="font-size:10px" >' . $order_info['payment_method']  . '</td>
					</tr>';
					$header.='<tr>
					<td class="right grey" >Channel :</td>
					<td class="right" style="font-size:10px" >Web</td>
				</tr>';
				if($order_info['shipping_method']=='Local Las Vegas Store Pickup - 9:30am-4:30pm')
				{
					$order_info['shipping_method']='Local Order';
				}
				
				$header.='<tr>
				<td colspan="2" class="right" style="font-size:10px;word-wrap: break-word;width:150px" ><strong>' . $order_info['shipping_method']  . '</strong></td>
			</tr>';

			//

			$header.='
	</table>
</td>
</tr>
</table>
</td>
</tr>
<tr>
	<td colspan="2"  >
		<table  cellpadding="0" cellspacing="1" border="0"     >
			<tr style="background-color:#3C3D3A;color:#fff;">
				<td style="width:25px;height:20px;padding:0px;text-align:center">#</td>
				<td style="width:450px;height:20px;padding:5px">Item Description</td>
				<td style="width:60px;text-align:right;padding:5px" >Qty</td>
				<td style="width:60px;text-align:right;padding:5px"  >Rate</td>
				<td  style="width:80px;text-align:right;padding:5px">Amount</td>
			</tr>';

			//
			$item_html = '';
			$i_i = 1;
			// $zam_counter = 1;
			foreach ($items as $item) {
				if(count($items)<=15)
				{
					$_b = 14;	
				}
				else
				{
					$_b = 14;
				}
				if($i_i%$_b==0)
				{
					$item_html.='</table></td></tr></table></page>'.$header;	
				}
				$product_name = $item['name'];
				if(strlen($product_name)>82)
				{
					$product_name = substr($product_name,0,82).'...';	
				}
				$item_html.='	
				<tr>
				<td style="height:20px;padding:0px;text-align:center" class="normal">'.$i_i.'</td>
					<td style="height:20px;padding:5px" >
						<span class="normal">' . $item['model'] . '</span><br />
						<span class="detail">' .$product_name . '</span>
					</td>
					<td class="right" style="height:20px;padding:5px">
						<span class="normal" >' . number_format($item['quantity'], 2) . '</span><br />
						
					</td>
					<td  class="normal right" style="height:20px;padding:5px">' . number_format($item['price'], 2) . '</td>
					<td  class="normal right" style="height:20px;padding:5px">' . number_format($item['total'], 2) . '</td>
				</tr>';
				$i_i++;
			}

			//
			$html=$html.$header.$item_html;
			$html.='
		</table>
		<table  cellspacing="10">
			<tr>
				<td style="text-align:right;width:626px">Sub Total</td>
				<td style="text-align:right;width:100px">' . $this->currency->format($sub_total) . '</td>
			</tr>
			<tr>
				<td  class="right">Shipping</td>
				<td class="right">' . $this->currency->format($shipping_total) . '</td>
			</tr>';

			foreach ($vouchers->rows as $key => $voucher) {
				$html.= '<tr>
				<td class="right">Voucher('. $voucher['code'] .'):</td>
				<td class="right">$'. number_format($voucher['used'], 2) .'</td></tr>';
				//$total_vouchers += str_replace('-', '', $voucher['used']);
			}
			foreach ($coupons->rows as $key => $coupon) {
				$html = '<tr>'
				. '<td align="right">Coupon(' . $coupon['code'] . '):</td>'
				. '<td>$' . number_format($coupon['used'], 2) . '</td></tr>';
				//$total_coupons += str_replace('-', '', $coupon['used']);
			}

			//

			$html.='<tr>
				<td  class="right">Tax / Extra</td>';
				$html.='<td class="right">'.$this->currency->format($tax).'</td>';
				$html.='</tr>';
				$html.='<tr>
				<td  class="right bold">Total</td>';
				
					$html.='<td class="right bold">' . $this->currency->format($invoice_total) . '</td>';
				
				$html.='</tr>';

				//

				if (($order_info['payment_method'] == 'Cash' or strtolower($order_info['payment_method']) == 'paypal' or strtolower($order_info['payment_method']) == 'credit/debit card' or strtolower($order_info['payment_method']) == 'paypal express' ) and strtolower($order_info['order_status_id'])=='15') {
					// $amount_due = 	$invoice_total;
					$html.='
					<tr>
						<td class="right bold" style="height:30px;">Amount Paid</td>
						<td class="right bold" style="">$0.00</td>
					</tr>';
					$html.='
					<tr >
						<td class="right bold" style="height:30px;">Amount Due</td>
						<td class="right bold" style="">' . $this->currency->format($invoice_total, 2) . '</td>
					</tr>';
				}

				//
				$html.='
		</table>
	</td>
</tr>
</table></page>
';
try {
	$html2pdf = new HTML2PDF('P', 'A4', 'en');
	$html2pdf->setDefaultFont('arial');
	$html2pdf->writeHTML($html);
	$filename =  $order_id.'-'.time();
	$file = $html2pdf->Output('image/invoice/' . $filename . '.pdf', 'D');

  } catch (HTML2PDF_exception $e) {
  	echo $e;
  	exit;
  }

  // $this->redirect('image/invoice/'.$filename.'.pdf');
  
  exit; 

	}
}
?>