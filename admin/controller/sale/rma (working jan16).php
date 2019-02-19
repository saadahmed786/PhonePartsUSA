<?php    
class ControllerSaleRma extends Controller { 
	private $error = array();


	public function index1()
	{

		$this->document->setTitle('RMA Return');
		$this->data['heading_title'] = 'RMA Return';
		$this->load->model('sale/rma');
		$this->load->model('sale/order');
		$this->load->model('catalog/product');
		$this->load->model('sale/voucher');
		$order_id = $this->request->get['order_id'];
		$order_info = $this->model_sale_order->getOrder($order_id);
		$this->data['order_info'] = $order_info;
		$this->data['order_id'] = $order_id;
		$this->data['token'] = $this->session->data['token'];
		$this->data['voucher_info'] = $this->model_sale_voucher->getVoucherByOrderID($order_id);
		
		
		$this->data['ppat_env'] = $this->config->get('paypal_express_test')==0 ? 'live' : 'sandbox';
		$this->data['ppat_api_user'] = $this->config->get('paypal_express_new_apiuser') ? $this->config->get('paypal_express_new_apiuser') : '';
		$this->data['ppat_api_pass'] = $this->config->get('paypal_express_new_apipass') ? $this->config->get('paypal_express_new_apipass') : '';
		$this->data['ppat_api_sig']  = $this->config->get('paypal_express_new_apisig')  ? $this->config->get('paypal_express_new_apisig')  : '';
		
		$this->template = 'pos/returns.tpl';		
		$this->children = array(
			'common/header',
			'common/footer'
			);
		$this->response->setOutput($this->render());


	}

	public function index() {
		

		$this->document->setTitle('Returns');
		$this->data['heading_title'] = 'Returns';
		$this->load->model('sale/rma');
		$this->load->model('sale/order');
		$this->load->model('catalog/product');
		
		$order_id = $this->request->get['order_id'];

		$total_shipping_query = $this->db->query("SELECT value FROM ".DB_PREFIX."order_total WHERE order_id='".(int)$order_id."' AND code='shipping'");
		$total_shipping = $total_shipping_query->row['value'];

		$total_tax_query = $this->db->query("SELECT value FROM ".DB_PREFIX."order_total WHERE order_id='".(int)$order_id."' AND code='tax'");
		$total_tax = $total_tax_query->row['value'];

		$total_voucher_query = $this->db->query("SELECT value FROM ".DB_PREFIX."order_total WHERE order_id='".(int)$order_id."' AND code='voucher'");
		$total_voucher = $total_voucher_query->row['value'];

		$total_sub_query = $this->db->query("SELECT value FROM ".DB_PREFIX."order_total WHERE order_id='".(int)$order_id."' AND code='sub_total'");
		$total_sub_total = $total_sub_query->row['value'];


		$this->data['total_shipping'] = (float)$total_shipping;
		$this->data['total_tax'] = (float)$total_tax;
		$this->data['total_voucher'] = (float)$total_voucher;
		$tax_per = ((float)$total_tax/(float)$total_sub_total) * 100;
		
		
		
		$order_info = $this->model_sale_order->getOrder($order_id);
		if(strtotime($order_info['date_modified'])>strtotime('-30 day'))
		{
			$is_old_order = false;
		}
		else
		{
			$is_old_order = true;	
		}
		$paid_status = ($order_info['payment_method'] == 'Cash' ? 'unpaid' : 'paid');
		
		if($order_info['ref_order_id'])
		{
			$ref_order_id=explode("-",$order_info['ref_order_id']);
			$ref_order_id = $ref_order_id[0];
			
		}
		$order_products = $this->model_sale_order->getOrderProducts($order_id);
		$rma_reasons = $this->model_sale_rma->getReasons();
		
		$products = array();
		$sku_returned = array();
		$sku_rma = array();
		$item_count = 1;
		foreach($order_products as $product)
		{
			
			$product_detail =$this->model_catalog_product->getProduct($product['product_id']);
			if($product_detail['sku']=='SIGN') continue;
			for($i=1;$i<=$product['quantity'];$i++)
			{
				
				if($ref_order_id)
				{
					$order_product = $this->model_sale_order->getOrderProduct($ref_order_id,$product_detail['product_id']);
				}
				else
				{
					$order_product = $this->model_sale_order->getOrderProduct($order_id,$product_detail['product_id']);		
				}
					// 
				if($is_old_order==true and $paid_status=='paid')
				{
					$latest_price = $this->model_sale_order->getOrderLatestProductPrice($order_id,$product_detail['product_id']);	
						//echo $latest_price;exit;
					if($latest_price<$order_product['price'])
					{
						$price = $latest_price;	
					}
					else
					{
						$price = $order_product['price'];	
					}

				}
				else
				{
					$price = $order_product['price'];	
				}

				$products[] = array(
					'product_id'=>$product_detail['product_id'],
					'name'=>$product_detail['name'],
					'sku'=>$product_detail['sku'],
					'is_processed'=>$this->model_sale_rma->isRMAGenerated($this->model_sale_order->getReplacementRef($order_id),$product_detail['sku']),
					'price'=>$price,
					'tax'=>round(((float)$order_product['price']*(float)$tax_per)/100,4),
					'in_stock_qty'=>$product_detail['quantity']
					);	
				
				$query = $this->db->query("SELECT
					sum(quantity) as quantity
					FROM
					`inv_returns` a
					INNER JOIN `inv_return_items` b
					ON (a.`id` = b.`return_id`)
					WHERE a.`order_id`='".$this->model_sale_order->getReplacementRef($order_id)."' AND b.sku='".$this->db->escape($product_detail['sku'])."'");



				$sku_returned[$product_detail['sku']] = $row = $query->row['quantity'];
				$total_qty = $query->row['quantity'];
				$query = $this->db->query("SELECT
					rma_number
					FROM
					`inv_returns` a
					INNER JOIN `inv_return_items` b
					ON (a.`id` = b.`return_id`)
					WHERE a.`order_id`='".$this->model_sale_order->getReplacementRef($order_id)."' AND b.sku='".$this->db->escape($product_detail['sku'])."'");

				$p=1;
				foreach($query->rows as $xrow)
				{

					$sku_rma[$product_detail['sku']][$p] = $xrow['rma_number'];;
					$p++;
				}
				$item_count++;
			}
			
		}
		
		$this->data['sku_returned'] = $sku_returned;
		$this->data['sku_rma'] = $sku_rma;
		$this->data['ppat_env'] = $this->config->get('paypal_express_new_test')==0 ? 'live' : 'sandbox';
		$this->data['ppat_api_user'] = $this->config->get('paypal_express_new_apiuser') ? $this->config->get('paypal_express_new_apiuser') : '';
		$this->data['ppat_api_pass'] = $this->config->get('paypal_express_new_apipass') ? $this->config->get('paypal_express_new_apipass') : '';
		$this->data['ppat_api_sig']  = $this->config->get('paypal_express_new_apisig')  ? $this->config->get('paypal_express_new_apisig')  : '';


		$this->data['aat_env'] = $this->config->get('aat_env') ? $this->config->get('aat_env') : '';
		$this->data['aat_merchant_id'] = $this->config->get('authorizenet_aim_login') ? $this->config->get('authorizenet_aim_login') : '';
		$this->data['aat_transaction_key'] = $this->config->get('authorizenet_aim_key') ? $this->config->get('authorizenet_aim_key') : '';
		$item_conditions = array(array('id' => 'Good For Stock', 'value' => 'Good For Stock'),
			array('id' => 'Item Issue', 'value' => 'Item Issue'),
			array('id' => 'Customer Damage', 'value' => 'Customer Damage'),
			array('id' => 'Not Tested', 'value' => 'Not Tested'),
			array('id' => 'Not PPUSA Part', 'value' => 'Not PPUSA Part'),
			array('id' => 'Over 60 days', 'value' => 'Over 60 days')
			);
		$this->data['item_conditions'] =$item_conditions;
		$item_issues = $this->db->query("select * from inv_reasonlist");
		$this->data['item_issues'] = $item_issues->rows;




		$this->data['token'] = $this->session->data['token'] ;
		$this->data['products']=$products;
		$this->data['order_info']=$order_info;
		$this->data['rma_reasons']=$rma_reasons;
		$this->data['action'] = $this->url->link('sale/rma/insert', 'token=' . $this->session->data['token'] .'&order_id='.$order_id, 'SSL');
		$this->data['error_warning'] = '';
		$this->template = 'sale/rma.tpl';		
		$this->children = array(
			'common/header',
			'common/footer'
			);
		$this->response->setOutput($this->render());

	}
	
	public function insert()
	{
		$this->load->model('sale/order');
		$this->load->model('sale/rma');
		$this->load->model('catalog/product');
		$products = $this->request->post['product'];
		$reasons = $this->request->post['reason'];
		$processes = $this->request->post['process'];
		$item_condition = $this->request->post['item_condition'];
		$item_issue = $this->request->post['item_issue'];
		$decision = $this->request->post['decision'];
		$order_id = $this->request->get['order_id'];

		$order_info =  $this->model_sale_order->getOrder($order_id);

		$data = array();
		$data['email'] = $order_info['email'];
		if($order_info['ref_order_id'])
		{
		$data['order_id'] = $order_info['ref_order_id'];
	}
	else
	{
		$data['order_id'] = $order_info['order_id'];	
	}
		$data['store_type'] = 'storefront';
		$data['rma_status'] = 'Awaiting';

		$return_id = $this->model_sale_rma->addReturnMain($data);

		foreach($products as $key => $product)
		{
			$product_detail = $this->model_catalog_product->getProduct($product);
			$order_product = $this->model_sale_order->getOrderProduct($order_id,$product_detail['product_id']);
			$data = array();
			$data['product_id'] = $product_detail['product_id'];
			$data['product_title'] = $product_detail['name'];
			$data['sku'] = $product_detail['sku'];
			$data['price'] = $order_product['price'];
			$data['quantity'] = 1;
			$data['return_id'] = $return_id;
			$data['reason'] = $reasons[$key];
			$data['process'] = $processes[$key];
			$data['item_condition'] = $item_condition[$key];
			$data['item_issue'] = $item_issue[$key];
			$data['decision'] = $decision[$key];
			$this->model_sale_rma->addReturnDetail($data);

			$this->db->query("INSERT into inv_return_decision SET order_id='".$order_id."',return_id='".$return_id."',sku='".$data['sku']."',price='".$data['price']."',action='".$data['decision']."',date_added=NOW()");

		}

		$RMA_Number = $this->model_sale_rma->getRMA($return_id);
		echo "RMA # ".$RMA_Number." is generated.";exit;
	}
	protected function refOrderId($order_id,$n=1)
	{
		//When i made a replacement of this replacement order the order id was 318940-1. This is wrong, the replacement order id should have been: 318657-2.
		$_check = $this->db->query("SELECT order_id,ref_order_id FROM ".DB_PREFIX."order WHERE order_id='".$order_id."'");
		if($_check->row['ref_order_id'])
		{
			$order_id = explode("-",$_check->row['ref_order_id']);
			$order_id = $order_id[0];
		}
		
		$ref_order_id = $order_id.'-'.$n;
		$check = $this->db->query("SELECT ref_order_id FROM ".DB_PREFIX."order WHERE ref_order_id='".$ref_order_id."'");	
		$row = $check->row;




		if($row)
		{
			$n = $n+1;
			return $this->refOrderId($order_id,$n);

		}
		else
		{
			return $ref_order_id;
		}
		
	}
	public function issue_replacement()
	{
		$this->load->model('sale/order');

		$order_id = $this->request->get['order_id'];

		$refID = $this->model_sale_order->getReplacementRef($order_id);
		if (strpos($refID, '-') !== false) {
			$refID = explode('-', $refID)[0];
		}

		$ref_order_id = $this->refOrderId($refID);
		$products = $this->request->post['product_list'];
		$products = explode(",",$products);
		$this->load->model('catalog/product');
		$order_info = $this->model_sale_order->getOrder($order_id);
		$order_info['shipping_code'] = $order_info['shipping_code'];
		$order_info['shipping_method'] = $order_info['shipping_method'];
		$order_info['payment_method'] = 'Replacement';
		$order_info['payment_code'] = 'cod';
		$order_info['ref_order_id'] = $ref_order_id;
		$data = array();	
		$data = $order_info;
		$data['order_product'] = array();
		$price = 0;
		foreach($products as $product)
		{
			$order_product = $this->model_sale_order->getOrderProduct($order_id,$product);
			$product_info = $this->model_catalog_product->getProduct($product);
			$data['order_product'][] = array(

				'product_id'	=>$product_info['product_id'],
				'name'		=>	$product_info['name'],
				'model'		=>	$product_info['model'],
				'quantity'	=>	1,
				'price'		=>	$order_product['price'],
				'total'		=>	$order_product['price'],
				'tax'		=>	0.0000

				);
			
			$price+=$order_product['price'];
		}
		$data['order_total'] = array();
		
		
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."order_total WHERE order_id='".(int)$order_id."'");
		foreach($query->rows as $row)
		{
			if($row['code']=='total' or $row['code']=='sub_total')
			{
				$value = 	$price;
			}
			else
			{
				
				$value = 0;	
			}
			
			$data['order_total'][] = array(
				'code'=>$row['code'],
				'title'=>$row['title'],
				'text'=>$this->currency->format(0.00, $order_info['currency_code'], $order_info['currency_value']),
				'value'=>0.00,
				'sort_order'=>$row['sort_order']
				)	;

			
		}
		$data['admin_view_only']=1;
		$data['order_status_id'] = 15 ; //default on hold status
		$data['user_id'] = $this->user->getId();
		unset($data['order_voucher']);
		
		$this->model_sale_order->addOrder($data);
		
		$_query = $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE ref_order_id='".$ref_order_id."'");
		$rec = $_query->row;
		echo $rec['order_id'];
				//echo 'Replacement Order is successfully maintained';
		exit;
		
	}
	private function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function issue_credit()
	{
		$order_id = $this->request->get['order_id'];
		$product_list = $this->request->post['product_list'];
		$products =  explode(",",$product_list);
		$total = (float)$this->request->get['amount'];
		$code = $order_id.'R';


		$this->load->model('sale/rma');
		$this->load->model('sale/voucher');
		$this->load->model('sale/order');
		if($this->model_sale_voucher->getVoucherByCode($code))
		{
			$code = $code.'-'.$this->generateRandomString(2);	
		}

		$order_info = $this->model_sale_order->getOrder($order_id);
		
		$product_items = array();
		$amount = 0;
		
		foreach($products as $product)
		{
			$product_info = $this->model_sale_order->getOrderProduct($order_id,$product);
			if($product_info)
			{
				$product_items[] =$product.'-'.$product_info['price'];

				$amount+=$product_info['price'];
			}
		}
		$product_ids = rtrim($product_ids,",");
		$data = array();
		$data['code'] = $code;
		$data['message'] = 'Store Credit # '.$code.' has been issued' ;
		$data['amount'] = $total;
		$data['status'] = 1;
		$data['product_items'] = $product_items;
		$data['order_id'] = $order_id;
		$data['to_name'] = $order_info['firstname'];
		$data['to_email'] = $order_info['email'];
		$data['reason'] = 3;
		$data['credit_shipping'] = 0;
		$data['code'] = $code;	

		$voucher_id = 	$this->model_sale_voucher->addVoucher($data);
		
		if($voucher_id)
		{
			
			$this->model_sale_voucher->sendVoucher($voucher_id);
			echo 'Voucher #: '.$code.' has been generated and sent';exit;	
		}
		
	}


	public function refund_invoice()
	{

		$order_id = $this->request->get['order_id'];
		$ref_order_id = $this->refOrderId($order_id);
		$products = $this->request->post['product_list'];
		$products = explode(",",$products);
		$this->load->model('sale/order');
		$this->load->model('catalog/product');
		$order_info = $this->model_sale_order->getOrder($order_id);
		
		$total_shipping_query = $this->db->query("SELECT value FROM ".DB_PREFIX."order_total WHERE order_id='".(int)$order_id."' AND code='shipping'");
		$total_shipping = $total_shipping_query->row['value'];
		
		$total_tax_query = $this->db->query("SELECT value FROM ".DB_PREFIX."order_total WHERE order_id='".(int)$order_id."' AND code='tax'");
		$total_tax = $total_tax_query->row['value'];




		
		$data = array();	
		$data = $order_info;
		$data['shipping_method']='';
		$data['shipping_code']='';
		
		$data['order_product'] = array();
		$price = 0;
		foreach($products as $product)
		{
			$order_product = $this->model_sale_order->getOrderProduct($order_id,$product);
			$product_info = $this->model_catalog_product->getProduct($product);
			$data['order_product'][] = array(

				'product_id'	=>$product_info['product_id'],
				'name'		=>	$product_info['name'],
				'model'		=>	$product_info['model'],
				'quantity'	=>	1,
				'price'		=>	$order_product['price']*(-1),
				'total'		=>	$order_product['price']*(-1),
				'tax'		=>	0.0000

				);
			
			$price+=$order_product['price'];
		}
		$data['order_total'] = array();
		
		
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."order_total WHERE order_id='".(int)$order_id."'");
		foreach($query->rows as $row)
		{
			if($row['code']=='sub_total')
			{
				$value = 	$price;
			}
			elseif($row['code']=='shipping')
			{
				$value = $total_shipping;
			}
			
			elseif($row['code']=='tax')
			{
				$value = $total_tax;	
			}
			elseif($row['code']=='total')
			{
				$value = $price+$total_tax+$total_shipping;
				
			}
			else
			{
				
				$value = 0;	
			}
			
			$data['order_total'][] = array(
				'code'=>$row['code'],
				'title'=>$row['title'],
				'text'=>$this->currency->format($price*(-1), $order_info['currency_code'], $order_info['currency_value']),
				'value'=>$value*(-1),
				'sort_order'=>$row['sort_order']
				)	;

			
		}
		$total_order_edit = $this->db->query("SELECT total_edits FROM ".DB_PREFIX."order WHERE order_id='".(int)$order_id."'");
		$total_order_edit = $total_order_edit->row;
		$data['admin_view_only']=1;
		$data['order_status_id'] = 11 ; //default on refunded status
		$data['user_id'] = $this->user->getId();
		$data['pos_total'] = $price*(-1);
		$data['ref_order_id'] = $ref_order_id;
		unset($data['order_voucher']);
		
		
		$this->db->query("UPDATE ".DB_PREFIX."order SET total_edits = total_edits+1 WHERE order_id='".(int)$order_id."'");
		$this->model_sale_order->addOrder($data);
		
		

	}
	public function getDecision()
	{
		$item_condition = $this->request->get['item_condition'];
		$html = '';
		if ($item_condition != 'Customer Damage' && $item_condition != 'Not PPUSA Part' && $item_condition != 'Over 60 days')
		{
			$decisionsx = array(array('id' => 'Issue Credit', 'value' => 'Issue Credit'),
				array('id' => 'Issue Refund', 'value' => 'Issue Refund'),
				array('id' => 'Issue Replacement', 'value' => 'Issue Replacement')
				);
			
		}
		else
		{
			$decisionsx = array(array('id' => 'Denied', 'value' => 'Denied'));
		}
		foreach ($decisionsx as $decision) {

			$html.='<option value="'.$decision['id'].'" >'.$decision['value'].'</option>';

		}

		echo $html;exit;

	}

}
?>