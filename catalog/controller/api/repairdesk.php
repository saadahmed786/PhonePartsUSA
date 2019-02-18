<?php
class ControllerApiRepairdesk extends Controller {
	public function index()
	{
		
		echo json_encode(array('error'=>'Please try again'));
	}
	public function authenticate()
	{
		$this->load->model('account/customer');
		// $request = file_get_contents("php://input");
		// $request = json_decode($request, true);
		// echo $this->request->post['request'];exit;
		// $request = json_decode($this->request->post['request']);
			 //print_r($request);exit;
		$request = $this->request->post['request'];
		$customer_email = $request['email'];
		$customer_token = $request['customer_token'];
		
		$status = 0;
		$message = 'Error: There is some problem validating user from PhonePartsUSA';
		if(isset($customer_email) && isset($customer_token))
		{
			$customer_info = $this->model_account_customer->getRepairDeskCustomer($customer_email,$customer_token);
			if($customer_info)
			{
				$status = 1;
				$message = 'Success: User Validated!';
			}
		}
		echo json_encode(array('status'=>$status,'message'=>$message));
	}
	public function auth_system($customer_email,$customer_token)
	{
		$this->load->model('account/customer');
		
		
		
		$status = 0;
		$message = 'Error: There is some problem validating user from PhonePartsUSA';
		$customer_id = 0;
		$customer_info = array();
		if(isset($customer_email) && isset($customer_token))
		{
			$customer_info = $this->model_account_customer->getRepairDeskCustomer($customer_email,$customer_token);
			if($customer_info)
			{
				$status = 1;
				$customer_id = $customer_info['customer_id'];
				$message = 'Success: User Validated!';
			}
		}
		return array('status'=>$status,'message'=>$message,'customer_id'=>$customer_id,'customer_info'=>$customer_info);
	}
	public function get_order_status()
	{
		$inputJSON = file_get_contents('php://input');
		$post = json_decode($inputJSON, TRUE); //convert JSON into array
		$customer_email = $post['email'];
		$customer_token = $post['token'];
		$po_number = $post['repairdesk_po'];
		$customer_data = $this->auth_system($customer_email,$customer_token);
		
		$json = array();
		if($customer_data['status']==0 || ! $po_number)
		{
			
			echo json_encode(array('status'=>'error','message'=>$customer_data['message']));exit;
		}
		$repairdesk_po = $this->db->query("SELECT * FROM oc_order WHERE repairdesk_po='".$this->db->escape($post['repairdesk_po'])."'");
		if(!$repairdesk_po->row['order_id'])
		{
			echo json_encode(array('status'=>'error','message'=>'No Order Record Found'));exit;
		}
		else
		{
			$order_status = $this->db->query("SELECT name FROM `".DB_PREFIX."order_status` where order_status_id='".(int)$repairdesk_po->row['order_status_id']."'");
			$data = array();
			$data['order_id'] = $repairdesk_po->row['order_id'];
			$data['repairdesk_po'] = $repairdesk_po->row['repairdesk_po'];
			$data['order_status'] = $order_status->row['name'];
			$data['order_total'] = $repairdesk_po->row['total'];
			$data['created_at'] = $repairdesk_po->row['date_added'];
			$data['updated_at'] = $repairdesk_po->row['date_modified'];
			echo json_encode($data);
		}
		
		
	}
	public function add_order()
	{
		// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
		$this->load->model('catalog/product');
		$this->load->model('checkout/voucher');
		$inputJSON = file_get_contents('php://input');
		// echo $inputJSON;exit;
		$post = json_decode($inputJSON, TRUE); //convert JSON into array
// echo 'here';exit;
		$customer_email = $post['email'];
		$customer_token = $post['token'];
		// print_r($post);exit;
		$po_number = $post['repairdesk_po'];
		$customer_info = $this->auth_system($customer_email,$customer_token);
		
		// Variables
		$firstname = $post['firstname'];
		$lastname = $post['lastname'];
		$telephone = $post['telephone'];
		$company = $post['company'];
		$address_1 = $post['address_1'];
		$address_2 = $post['address_2'];
		$country = ($post['country']);
		$country_id = (strtolower($post['country'])=='United States'?223:38);
		$city = $post['city'];
		$postcode = $post['postcode'];
		$zone = $post['zone'];
		$zone_det = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE name like  '%" . $this->db->escape($zone) . "%' AND status = '1'");
		
		$zone_id = $zone_det->row['zone_id'];
		// echo $zone_id;exit;
		$shipping_method = $post['shipping_method'];
		$shipping_code = $post['shipping_code'];
		$shipping_amount = $post['shipping_amount'];
		$payment_firstname = $post['payment_firstname'];
		$payment_lastname = $post['payment_lastname'];
		$payment_company = $post['payment_company'];
		$payment_address_1 = $post['payment_address_1'];
		$payment_address_2 = $post['payment_address_2'];
		$payment_city = $post['payment_city'];
		$payment_postcode = $post['payment_postcode'];
		$payment_country = ($post['payment_country']);
		$payment_country_id = (strtolower($post['payment_country'])=='United States'?223:38);
		$payment_zone = $post['payment_zone'];
		$zone_det = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE name like  '%" . $this->db->escape($payment_zone) . "%' AND status = '1'");
		$payment_zone_id = $zone_det->row['zone_id'];
		// echo $payment_zone_id;exit;
		$payment_method = $post['payment_method'];
		$payment_code = 'paypal_express_new';
		$comment = $post['comment'];
		$sub_total = $post['sub_total'];
		$tax = $post['tax'];
		$total = $post['total'];
		$items = $post['items']; 
		$applied_vouchers = $post['applied_vouchers']; 
		
		$json = array();
		if($customer_info['status']==0)
		{
			echo json_encode(array('status'=>'error','message'=>$customer_info['message']));exit;
		}
		// print_r($items);exit;
		if(!$po_number or utf8_strlen($firstname)<1 or utf8_strlen($lastname)<1 or utf8_strlen($telephone)<3 or utf8_strlen($address_1)<3 or utf8_strlen($city)<1 or utf8_strlen($postcode)<3 or utf8_strlen($zone)<1 or utf8_strlen($shipping_method)<1 or utf8_strlen($shipping_code)<1 or utf8_strlen($payment_firstname)<1 or utf8_strlen($payment_lastname)<1 or utf8_strlen($payment_address_1)<3 or utf8_strlen($payment_city)<1 or utf8_strlen($payment_postcode)<3 or utf8_strlen($payment_zone)<1 or empty($items)  )
		{
			echo json_encode(array('status'=>'error','message'=>'Please check for the mandatory fields'));exit;
		}
		// print_r($items);exit;
		foreach($items as $item)
		{
			// echo $item['model'];exit;
			$product_id = $this->model_catalog_product->getProductIDbySku($item['model']);
			
			$product_detail = $this->model_catalog_product->getProduct($product_id);
			if(!$product_id)
			{
				echo json_encode(array('status'=>'error','message'=>'SKU: '.$item['model'].' does not exist in the inventory.'));exit;
			}
		}
		
		$this->db->query("INSERT INTO ". DB_PREFIX . "order SET 
			invoice_prefix='INV-2011-00',
			store_name='PhonePartsUSA.com ',
			store_url='https://phonepartsusa.com/',
			repairdesk_po='".$this->db->escape($po_number)."',
			order_status_id='15',
			customer_id='".(int)$customer_info['customer_info']['customer_id']."',
			customer_group_id='".(int)$customer_info['customer_info']['customer_group_id']."',
			firstname='".$this->db->escape($customer_info['customer_info']['firstname'])."',
			lastname='".$this->db->escape($customer_info['customer_info']['lastname'])."',
			email='".$this->db->escape($customer_email)."',
			telephone='".$this->db->escape($telephone)."',
			shipping_firstname='".$this->db->escape($firstname)."',
			shipping_lastname='".$this->db->escape($lastname)."',
			shipping_company='".$this->db->escape($company)."',
			shipping_address_1='".$this->db->escape($address_1)."',
			shipping_address_2='".$this->db->escape($address_2)."',
			shipping_city='".$this->db->escape($city)."',
			shipping_postcode='".$this->db->escape($postcode)."',
			shipping_country='".$this->db->escape($country)."',
			shipping_country_id='".$this->db->escape($country_id)."',
			shipping_zone='".$this->db->escape($zone)."',
			shipping_zone_id='".$this->db->escape($zone_id)."',
			shipping_method='".$this->db->escape($shipping_method)."',
			shipping_code='".$this->db->escape($shipping_code)."',
			payment_firstname='".$this->db->escape($payment_firstname)."',
			payment_lastname='".$this->db->escape($payment_lastname)."',
			payment_company='".$this->db->escape($payment_company)."',
			payment_address_1='".$this->db->escape($payment_address_1)."',
			payment_address_2='".$this->db->escape($payment_address_2)."',
			payment_city='".$this->db->escape($payment_city)."',
			payment_postcode='".$this->db->escape($payment_postcode)."',
			payment_country='".$this->db->escape($payment_country)."',
			payment_country_id='".$this->db->escape($payment_country_id)."',
			payment_zone='".$this->db->escape($payment_zone)."',
			payment_zone_id='".$this->db->escape($payment_zone_id)."',
			payment_method='".$this->db->escape($payment_method)."',
			payment_code='".$this->db->escape($payment_code)."',
			comment='".$this->db->escape($comment)."',
			total='".$this->db->escape($total)."',
			language_id=1,
			currency_id=2,
			currency_value=1.00,
			date_added=NOW()
			");
		$order_id = $this->db->getLastId();
		$sub_total = 0.00;
		foreach($items as $item)
		{
			$product_id = $this->model_catalog_product->getProductIDbySku($item['model']);	
			$product_detail = $this->model_catalog_product->getProduct($product_id);
			$this->db->query("INSERT INTO ".DB_PREFIX."order_product SET
				order_id='".(int)$order_id."',
				product_id='".(int)$product_id."',
				name='".$this->db->escape($product_detail['name'])."',
				model='".$this->db->escape($product_detail['model'])."',
				quantity='".(int)$item['quantity']."',
				price='".(float)$item['unit_price']."',
				total='".((float)$item['unit_price']*(int)$item['quantity'])."',
				location_id=1
				");
			$sub_total = $sub_total + ((float)$item['unit_price']*(int)$item['quantity']);
		}
		$this->db->query("INSERT INTO ".DB_PREFIX."order_total SET 
			order_id='".(int)$order_id."',
			code='sub_total',
			title='Sub-Total',
			text='".$this->currency->format($sub_total)."',
			value='".(float)($sub_total)."',
			sort_order='1'
			"
			);
		$this->db->query("INSERT INTO ".DB_PREFIX."order_total SET 
			order_id='".(int)$order_id."',
			code='shipping',
			title='".$this->db->escape($shipping_method)."',
			text='".$this->currency->format($shipping_amount)."',
			value='".(float)($shipping_amount)."',
			sort_order='3'
			"
			);
		if($tax>0)
		{
			$this->db->query("INSERT INTO ".DB_PREFIX."order_total SET 
				order_id='".(int)$order_id."',
				code='tax',
				title='Tax',
				text='".$this->currency->format($tax)."',
				value='".(float)($tax)."',
				sort_order='5'
				"
				);
		}
		$total = (float)$sub_total+(float)$tax+(float)$shipping_amount;
		if($applied_vouchers)
		{
			foreach($applied_vouchers as $applied_voucher)
			{
				$voucher_detail = $this->model_checkout_voucher->getVoucher($applied_voucher['code']);
				if($voucher_detail && $total>0.00)
				{
					if($voucher_detail['amount']<=$total)
					{
						$applied_amount = $voucher_detail['amount'];
					}
					else
					{
						$applied_amount = $total;
					}
					$total = (float)$total - (float)$applied_voucher;
					$this->db->query("INSERT INTO ".DB_PREFIX."order_total SET 
						order_id='".(int)$order_id."',
						code='voucher',
						title='Voucher(".$applied_voucher['code'].")',
						text='".$this->currency->format($applied_amount*(-1))."',
						value='".(float)($applied_amount*(-1))."',
						sort_order='8'
						"
						);
					$this->db->query("INSERT INTO ".DB_PREFIX."voucher_history
						SET voucher_id='".(int)$voucher_detail['voucher_id']."',
						order_id='".(int)$order_id."',
						amount='".(float)($applied_amount*(-1))."',
						date_added=NOW()
						");
				}
			}
		}
		$this->db->query("INSERT INTO ".DB_PREFIX."order_total SET 
			order_id='".(int)$order_id."',
			code='total',
			title='Total',
			text='".$this->currency->format($total)."',
			value='".(float)($total)."',
			sort_order='9'
			"
			);
		$this->db->query("UPDATE ".DB_PREFIX."order SET 
			total='".(float)$total."'
			where order_id='".(int)$order_id."'
			"
			);
		echo json_encode(array('status'=>'success','po_number'=>$po_number,'order_id'=>$order_id,'order_status'=>'Processed','total'=>(float)round($total,2),'created_at'=>date('Y-m-d H:i:s')));exit;
	}
	public function product_data()
	{
		$this->load->model('catalog/product');
		// $request = json_decode($post['request']);
		// $products = $request['item_sku'];
		// print_r($products);exit;
		$inputJSON = file_get_contents('php://input');
		$input = json_decode($inputJSON, TRUE); //convert JSON into array
		$products = $input['request'];
		// print_r($products);exit;
		if(!is_array($products['item_sku']))
		{
			$products['item_sku'] = array(array('sku'=>$products['item_sku']));
		}
		$return = array();
		$status = 0;
		$message = 'Error: There is some problem fetching Item Data from PhonePartsUSA';
		
		if($products)
		{
			// print_r($products);exit;
			foreach($products['item_sku'] as $product)
			{
				// print_r($product);exit;
				if($product['sku']=='') continue;
				$status = 1;
				$product_id = $this->model_catalog_product->getProductIDbySku($product['sku']);	
				$product_detail = $this->model_catalog_product->getProduct($product_id);
				if(!$product_detail)
				{
					continue;
				}
				$price = ($product_detail['sale_price']>0.00?$product_detail['sale_price']:$product_detail['price']);
				$return[] = array('item_sku'=>$product_detail['model'],'price'=>$price,'tax'=>'0.00','quantity'=>$product_detail['quantity']);
			}
		}
		if($status==1 && $return)
		{
			$message = 'Success: Item SKU Details have been fetched';
		}
		else
		{
			$status=0;
		}
		echo json_encode(array('status'=>$status,'message'=>$message,'item_details'=>json_encode($return)));
		
	}
	public function add_to_cart()
	{
		$this->load->model('account/customer');
		$this->load->model('catalog/product');
		// print_r($this->request->post);exit;
		// $request = json_decode($this->request->post['request']);
		// print_r($request);exit;
		$inputJSON = file_get_contents('php://input');
		$input = json_decode($inputJSON, TRUE); //convert JSON into array
		$request = $input['request'];
		// print_r($request);exit;
		$customer_email = $request['email'];
		$customer_token = $request['customer_token'];
		$po_number = $request['po_number'];
		$products = $request['products'];
		$status = 0;
		$message = 'Something went wrong adding items into the cart, please try again';
		$validation_message = 'User not Validated';
		$is_validated = false;
		if(isset($customer_email) && isset($customer_token))
		{
			$customer_info = $this->model_account_customer->getRepairDeskCustomer($customer_email,$customer_token);
			if($customer_info)
			{
				$is_validated = true;
				$validation_message = 'User Validated';
			}
		}
		$cart_body = array();
		if($products && $po_number && $is_validated)
		{
			$i = 0;
			$status = 1;
			foreach($products as $product)
			{
				$product_id = $this->model_catalog_product->getProductIDbySku($product['sku']);	
				$cart_status = 0;
				$cart_message = 'Item not found and not added to cart';
				$cart_sku = $product['sku'];
				if($product_id)
				{
					$cart_status = 1;
					$cart_message = 'Item Added to Cart';
					$cart_sku = $product['sku'];
					$this->cart->add($product_id,(int)$product['qty']);
					
				}
				
				$cart_body[$i]['status']=$cart_status;
				$cart_body[$i]['message']=$cart_message;
				$cart_body[$i]['sku']=$cart_sku;
				$i++;
			}
		}
		if($cart_body==1)
		{
			$this->session->data['newcheckout']['repairdesk_po']  = $po_number;
			$message = 'Success: Items added into PhonePartsUSA Cart';
		}
		// echo json_encode(array('status'=>$status,'message'=>$message));
		echo json_encode(array('status'=>$status,'message'=>$validation_message,'body'=>$cart_body));exit;
	}
	public function get_shipping_method_old()
	{
		// header('Access-Control-Allow-Origin: *');	
		// echo 'here';exit;
		$zone = $this->request->get['zone'];
		$this->load->model('account/order');
		
		$zone = trim(urldecode($zone));
		
		$zone_query = $this->db->query("SELECT zone_id,country_id FROM ".DB_PREFIX."zone WHERE lower(`name`) = '".$this->db->escape(strtolower($zone))."' and country_id in (38,223)");
		$zone_id = $zone_query->row;
		if(!$zone_id)
		{
			$zone_query = $this->db->query("SELECT zone_id,country_id FROM ".DB_PREFIX."zone WHERE lower(`code`) = '".$this->db->escape(strtolower($zone))."' AND country_id in (223,38)");
			$zone_id = $zone_query->row;
		}
		if(!$zone_id)
		{
			echo json_encode(array('error'=>'Please provide a valid state name'));	exit;
		}
		
		
		$address_data = array(
			'zone_id'        => $zone_id['zone_id'],
			'country_id'     => $zone_id['country_id'],
			);
		// print_r($address_data);exit;
		$this->load->model('setting/extension');
		$results = $this->model_setting_extension->getExtensions('shipping');
		// print_r($results);exit;
		$json = array();
		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('shipping/' . $result['code']);
				$quote = $this->{'model_shipping_' . $result['code']}->getQuote($address_data); 
								// print_r($quote);exit;
				if ($quote) {
					$json['shipping_method'] = array( 
									// 'title'      => $quote['title'],
						'quote'      => $quote['quote'], 
									// 'sort_order' => $quote['sort_order'],
									// 'error'      => $quote['error']
						);
				}
			}
		}
		$sort_order = array();
		foreach ($json['shipping_method'] as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}
		array_multisort($sort_order, SORT_ASC, $json['shipping_method']);
		if (!$json['shipping_method']) {
			$json['error']['shipping_method'] = $this->language->get('error_no_shipping');
		} 
		$this->response->setOutput(json_encode($json));	
	}
	public function get_shipping_method()
	{
		// header('Access-Control-Allow-Origin: *');	
		// echo 'here';exit;
		$inputJSON = file_get_contents('php://input');
		$request = json_decode($inputJSON, TRUE); //convert JSON into array
		$zone = $request['zone'];
		$cart_total = (float)$request['sub_total'];
		$this->load->model('account/order');
		
		$zone = trim(urldecode($zone));
		
		$zone_query = $this->db->query("SELECT zone_id,country_id FROM ".DB_PREFIX."zone WHERE lower(`name`) = '".$this->db->escape(strtolower($zone))."' and country_id in (38,223)");
		$zone_id = $zone_query->row;
		if(!$zone_id)
		{
			$zone_query = $this->db->query("SELECT zone_id,country_id FROM ".DB_PREFIX."zone WHERE lower(`code`) = '".$this->db->escape(strtolower($zone))."' AND country_id in (223,38)");
			$zone_id = $zone_query->row;
		}
		if(!$zone_id)
		{
			echo json_encode(array('error'=>'Please provide a valid state name'));	exit;
		}
		if($cart_total<=0.00)
		{
			echo json_encode(array('error'=>'Please provide the Cart Total Amount'));	exit;	
		}
		
		
		$address_data = array(
			'zone_id'        => $zone_id['zone_id'],
			'country_id'     => $zone_id['country_id'],
			);
		// print_r($address_data);exit;
		$this->load->model('setting/extension');
		$results = $this->model_setting_extension->getExtensions('shipping');
		// print_r($results);exit;
		$json = array();
		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('shipping/' . $result['code']);
				$quote = $this->{'model_shipping_' . $result['code']}->getQuote($address_data,true,$cart_total); 
								// print_r($quote);exit;
				if ($quote) {
					$json['shipping_method'] = array( 
									// 'title'      => $quote['title'],
						'quote'      => $quote['quote'], 
									// 'sort_order' => $quote['sort_order'],
									// 'error'      => $quote['error']
						);
				}
			}
		}
		$sort_order = array();
		foreach ($json['shipping_method'] as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}
		array_multisort($sort_order, SORT_ASC, $json['shipping_method']);
		if (!$json['shipping_method']) {
			$json['error']['shipping_method'] = $this->language->get('error_no_shipping');
		} 
		$this->response->setOutput(json_encode($json));	
	}
	public function update_repairdesk_po($email)
	{
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$message = 'There is something error parsing data to RepairDesk';
		$items_list = array();
		foreach($this->cart->getProducts() as $cart)
		{
			$items_list[] = array(
				'item_sku'=>$cart['model'],
				'item_qty'=>$cart['quantity'],
				'item_price'=>$cart['price'],
				'item_gst'=>0.00
				);
		}
		$posted = array(
			'ppusa_order_id' => $this->session->data['order_id'],
			'order_status' =>$order_info['order_status'],
			'repairdesk_po' =>$this->session->data['newcheckout']['repairdesk_po'],
			'shipping_method' =>$order_info['shipping_method'],
			'shipping_amount' =>$order_info['shipping_total'],
			'item_list' =>$items_list
			);
		$posted = json_encode($posted);
		$posted = array('request' =>$posted);
		$crul = curl_init();
		curl_setopt($crul, CURLOPT_HEADER, false);
		$headers = array("Host:www.repairdesk.co");
		curl_setopt($crul, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($crul, CURLOPT_URL,"https://www.repairdesk.co/index.php?r=site/updatePurchaseOrder");
		curl_setopt($crul, CURLOPT_RETURNTRANSFER,
			true); curl_setopt($crul, CURLOPT_POST, true);
		curl_setopt($crul, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($crul, CURLOPT_POSTFIELDS, $posted);
		$response = curl_exec($crul);
		if (curl_errno($crul) != CURLE_OK) {
			$status = 1;
			$message = 'Data Parsed Successfully to RepairDesk';
		}
		else{
			$status = 0;
		}
		echo json_encode(array('status'=>$status,'message'=>$message));
	}
	public function get_available_vouchers()
	{
		$this->load->model('account/viewvouchers'); 
		$inputJSON = file_get_contents('php://input');
		$post = json_decode($inputJSON, TRUE); //convert JSON into array
		$customer_email = $post['email'];
		$customer_token = $post['token'];
		
		$customer_data = $this->auth_system($customer_email,$customer_token);
		$json = array();
		if($customer_data['status']==0)
		{
			
			echo json_encode(array('status'=>'error','message'=>$customer_data['message']));exit;
		}
		$vouchers = $this->model_account_viewvouchers->getVouchers($customer_email,0,20, 90);
		$voucher_data = array();
		$i=0;
		foreach($vouchers as $voucher)
		{
			if($voucher['balance']>0.00)
			{
				$voucher_data[$i]['code'] = $voucher['code'];
				$voucher_data[$i]['date'] = $voucher['date'];
				$voucher_data[$i]['amount'] = round($voucher['amount'],2);
				$voucher_data[$i]['balance'] = round($voucher['balance'],2);
				$i++;
			}
		}
		
		$data = array();
		$data['status'] = 'success';
		$data['vouchers'] = $voucher_data;
		echo json_encode($data);
	}

	public function get_customer_address()
	{

		$this->load->model('account/customer');
		$this->load->model('account/address');

		$inputJSON = file_get_contents('php://input');
		$post = json_decode($inputJSON, TRUE); //convert JSON into array
		$customer_email = $post['email'];
		$customer_token = $post['token'];
		
		$customer_data = $this->auth_system($customer_email,$customer_token);
		$json = array();
		if($customer_data['status']==0)
		{
			
			echo json_encode(array('status'=>'error','message'=>$customer_data['message']));exit;
		}
		$rows = $this->model_account_address->getAddressesById($customer_data['customer_id']);
		
		$addresses = array();
		$i=0;
		foreach($rows as $row)
		{
			if($row['firstname'] && $row['address_1'] && $row['postcode'] && $row['city'] && $row['zone_id'] && $row['country_id'])
			{
				
					$address[$i]['firstname'] = $row['firstname'];
					$address[$i]['lastname'] = $row['lastname'];
					$address[$i]['company'] = $row['company'];
					$address[$i]['address_1'] = $row['address_1'];
					$address[$i]['address_2'] = $row['address_2'];
					$address[$i]['postcode'] = $row['postcode'];
					$address[$i]['city'] = $row['city'];
					$address[$i]['zone'] = $row['zone'];
					$address[$i]['country'] = $row['country'];
					$i++; 
			}
			
		}
		$data = array();
		$data['status'] = 'success';
		$data['data'] = $address;
		echo json_encode($data);

	}
}
?>