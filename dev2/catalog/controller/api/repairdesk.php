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
	public function product_data()
	{
		$this->load->model('catalog/product');
		// $request = json_decode($this->request->post['request']);
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
}
?>