<?php
require_once("auth.php");
include_once 'inc/functions.php';


if ($_POST['action'] == 'getStates') {
	$states = $db->func_query("SELECT zone_id,name FROM oc_zone WHERE country_id='". $_POST['country_id'] ."' AND status=1 ORDER BY name");
	$json['s'] = '<option value="">Select State</option>';
	foreach($states as $state) {
		$json['s'] .= '<option value="' . $state['zone_id'] . '">'. $state['name'] .'</option>';
	}

	echo json_encode($json);
	exit;
}
if ($_GET['action'] == 'showAddresses') {
	$email = $db->func_escape_string($_GET['email']);
	$cust_addresses = $db->func_query("SELECT DISTINCT a.address1,a.city,a.state,a.zip FROM inv_orders_details a,inv_orders b where a.order_id=b.order_id and b.email='".$email."' GROUP BY address1");
	$html = '<br><br>
	<h2>Previous Addresses</h2>
	<select id="customer_address" size="5" onchange="populateCustomerAddress(this)">';
	foreach ($cust_addresses as $c_address) {
		$html.='<option value="'.$c_address['address1'].'~'.$c_address['city'].'~'.$c_address['state'].'~'.$c_address['zip'].'">'.$c_address['address1'].','.$c_address['city'].','.$c_address['state'].','.$c_address['zip'].'. </option>';
	}
	$html.= '</select>';
	$json['success'] = 1;
	$json['html'] = $html;
	echo json_encode($json);
	exit;
	
}
// Verify Vouchers
$x_customer_group_id = 8;
if ($_POST['action'] == 'getTax') {
	$email = $db->func_escape_string($_GET['email']);
	$zone_id = $db->func_query_first_cell("SELECT zone_id FROM oc_zone WHERE name = '".$db->func_escape_string($_POST['zone'])."'");

	if($zone_id=='3651') {
		$tax_detail = $db->func_query_first("SELECT * FROM oc_tax_rate WHERE geo_zone_id=10");
		if ($tax_detail) {
			$taxDis = $db->func_query_first_cell("SELECT dis_tax FROM inv_customers WHERE email='$email'");
			if (!$taxDis) {
				$json['success'] = 1;
				$json['tax'] = $tax_detail['rate'];
			} else {
				$json['success'] = 1;
				$json['tax'] = 0.00;
			}
		} else {
			$json['error'] = 1;
			$json['tax'] = $tax_detail;
		}
	} else {
		$json['error'] = 1;
		$json['zone'] = $zone_id;
	}
	echo json_encode($json);
	exit;
}
if ($_POST['action'] == 'verifyVoucher') {
	$_POST['vouchers'] = rtrim($_POST['vouchers'], ',');
	$vouchers = $db->func_query("SELECT  code, ov.`amount`, (ov.`amount` + SUM(oh.`amount`)) AS balance FROM oc_voucher ov LEFT JOIN oc_voucher_history oh ON ov.`voucher_id` = oh.`voucher_id` WHERE CODE IN ('". str_replace(',', "','", $_POST['vouchers']) ."') GROUP BY ov.`voucher_id` HAVING `balance` IS NULL OR `balance` > 0");

	// echo "SELECT  code, ov.`amount`, (ov.`amount` + SUM(oh.`amount`)) AS balance FROM oc_voucher ov LEFT JOIN oc_voucher_history oh ON ov.`voucher_id` = oh.`voucher_id` WHERE CODE IN ('". str_replace(',', "','", $_POST['vouchers']) ."') GROUP BY ov.`voucher_id` HAVING `balance` IS NULL OR `balance` > 0";exit;
	//echo "SELECT  code, ov.`amount`, (ov.`amount` + SUM(oh.`amount`)) AS balance FROM oc_voucher ov LEFT JOIN oc_voucher_history oh ON ov.`voucher_id` = oh.`voucher_id` WHERE CODE IN ('". str_replace(',', "','", $_POST['vouchers']) ."') GROUP BY ov.`voucher_id` HAVING `balance` IS NULL OR `balance` > 0";
	$extract = array();
	$total = 0.00;
	foreach ($vouchers as $voucher) {
		$extract[] = $voucher['code'];
		if ($voucher['balance']) {
			$total += $voucher['balance'];
		} else {
			$total += $voucher['amount'];
		}
	}
	
	$invalid = array_diff(explode(',', $_POST['vouchers']), $extract);

	// print_r(explode(',', $_POST['vouchers']));
	// print_r($extract);
	// print_r($invalid);

	if ($invalid) {
		$json['error'] = 1;
		$json['msg'] = implode(',', $invalid) . ' invalid or used';
		$json['total'] = '$' . number_format($total, 2);
		$json['valid'] = implode(',', $extract);
	} else {
		$json['success'] = 1;
		$json['total'] = '$' . number_format($total, 2);
		$json['valid'] = implode(',', $extract);
	}
	echo json_encode($json);
	exit;
}


if(@$_GET['action'] == 'business' && @$_GET['business_id']) {
	$customer = $db->func_query_first("select * from inv_po_customers where id = '".(int)$_GET['business_id']."'");
	/*print_r(json_encode($customer));
	exit;*/
	$addresses = $db->func_query("SELECT * FROM inv_po_address WHERE po_customer_id='".(int)$_GET['business_id']."'");
	$return = '<option value="">Select Address</option>';
	if(!$addresses)
	{
		$addresses[0]['address_id'] = 0;
		$addresses[0]['address'] = $customer['address1'];
		$addresses[0]['city'] = $customer['city'];
		$addresses[0]['state'] = $customer['state'];
		$addresses[0]['zip'] = $customer['zip'];
		
	}
	foreach($addresses as $address)
	{
		$return.= '<option value="'.$address['address_id'].'">'.$address['address'].', '.$address['city'].', '.$address['state'].', '.$address['zip'].'.</option>';

	}
	
	
	echo $return;exit;
}

if(@$_GET['action'] == 'business_address' && @$_GET['business_id']) {
	$customer = $db->func_query_first("select * from inv_po_customers where id = '".(int)$_GET['business_id']."'");
	/*print_r(json_encode($customer));
	exit;*/
	$address = $db->func_query_first("SELECT * FROM inv_po_address WHERE po_customer_id='".(int)$_GET['business_id']."' and address_id='".(int)$_GET['address_id']."'");
	
	$return = array();
	$return = $customer;
	if($address)
	{
		$return['address1'] = $address['address'];
		$return['city'] = $address['city'];
		$return['state'] = $address['state'];
		$return['zip'] = $address['zip'];
		$return['telephone'] = $address['telephone'];
	}
	print_r(json_encode($return));
	exit;
}
if(@$_GET['action'] == 'next_po'){
	/*$max_order_id = $db->func_query_first("SELECT order_id FROM inv_orders WHERE store_type='po_business' ORDER BY id DESC");
	
	if(!$max_order_id) $max_order_id = 'PO001';
	$max_order_id = substr($max_order_id['order_id'],2);
	$max_order_id++;
	$max_order_id = sprintf('%03d', $max_order_id);
	$max_order_id = 'PO'.$max_order_id;
	$json = array();
	$json['order_id'] = $max_order_id;*/
	
	$max_order_no = $db->func_query_first_cell("SELECT MAX( ABS( REPLACE( order_id, 'PO', '' ) ) ) AS max_order_id
		FROM inv_orders
		WHERE store_type = 'po_business'
		AND right( order_id, 3 ) REGEXP '^[0-9]+$'");

	$max_order_no++;
	$max_order_no = sprintf('%03d', $max_order_no);

	$json = array();
	$json['order_id'] = 'PO'.$max_order_no;
	echo json_encode($json);
	
	exit;
}
if(@$_GET['action'] == 'next_web_order_id'){
	
	$max_order_no = $db->func_query_first_cell("SELECT MAX( order_id ) AS max_order_id
		FROM oc_order"
		);

	$max_order_no++;
	$max_order_no = sprintf('%03d', $max_order_no);

	$json = array();
	$json['order_id'] = $max_order_no;
	echo json_encode($json);
	
	exit;
}

page_permission('create_order');
if($_POST['action']=='ajax_order_validate')
{
	
	$order_id = $db->func_escape_string($_POST['order_id']);
	
	//check exist order
	$isExist = $db->func_query_first_cell("select id from inv_orders where order_id = '$order_id'");
	$json = array();
	if($isExist){
		$json['error']='Order already exists!';
	}
	else
	{
		$json['success'] = "ok";	
	}
	echo json_encode($json);exit;
	
}
$customer_addresses = array();
if($_GET['action']!='customer_order')
{
	unset($_SESSION['cart']);
}
if($_GET['action']=='customer_order' and $_GET['email']<>'')
{
	$_POST['orders_details']['first_name'] = $db->func_escape_string($_GET['firstname']);
	$_POST['orders_details']['last_name'] = $db->func_escape_string($_GET['lastname']);
	$_POST['orders']['email'] = $db->func_escape_string($_GET['email']);
	$_POST['orders_details']['phone_number'] = $db->func_escape_string($_GET['telephone']);

	$customer_addresses = $db->func_query("SELECT DISTINCT a.address1,a.city,a.state,a.zip FROM inv_orders_details a,inv_orders b where a.order_id=b.order_id and b.email='".$db->func_escape_string($_GET['email'])."' GROUP BY address1");
	$x_customer_group_id = $db->func_query_first_cell("SELECT customer_group_id from oc_customer WHERE email='".$db->func_escape_string($_GET['email'])."'");
	if($x_customer_group_id=='') $x_customer_group_id = 8;
}
if($_POST['customer_cart']){
	
	$_POST['orders_details']['first_name'] = $db->func_escape_string($_POST['cust_firstname']);
	$_POST['orders_details']['last_name'] = $db->func_escape_string($_POST['cust_lastname']);
	$_POST['orders']['email'] = $db->func_escape_string($_POST['cust_email']);
	$_POST['orders_details']['phone_number'] = $db->func_escape_string($_POST['cust_phone']);
	$_POST['orders_details']['state'] = $db->func_escape_string($_POST['cust_state']);
	$_POST['orders_details']['city'] = $db->func_escape_string($_POST['cust_city']);
	$_POST['orders_details']['address1'] = $db->func_escape_string($_POST['cust_add_1']);
	$_POST['orders_details']['address2'] = $db->func_escape_string($_POST['cust_add_2']);
	$_POST['orders_details']['zip'] = $db->func_escape_string($_POST['cust_postcode']);
	$x_customer_group_id = $db->func_escape_string($_POST['cust_group_id']);
	$x_cust_id = $db->func_escape_string($_POST['cust_id']);
	foreach ($_POST['items'] as $key => $value) {
		$_POST['orders_items'][$key]['product_sku'] = $value;
		$_POST['orders_items'][$key]['product_qty'] = $_POST['cart_quantity'][$value];
	}
}
if($_POST['saveOrder']){
	$order_id = $db->func_escape_string($_POST['orders']['order_id']);
	$zone_id = $db->func_query_first_cell("SELECT zone_id FROM oc_zone WHERE name = '".$db->func_escape_string($_POST['orders_details']['state'])."'");
	$dis_tax = $db->func_query_first_cell("SELECT dis_tax FROM inv_customers WHERE email = '" . $_POST['orders']['email'] . "'");
	$country_id = $db->func_query_first_cell("SELECT country_id FROM oc_country WHERE name = '".$db->func_escape_string($_POST['orders_details']['country'])."'");
	//check exist order
	$isExist = $db->func_query_first_cell("select id from inv_orders where order_id = '$order_id'");
	if($isExist and $order_id!=''){
		$_SESSION['message'] = "Order is already exist";
	} else {
		

		if($_POST['orders']['store_type']=='web') {
			$_POST['orders']['shipstation_added']=0;
			if($_POST['customer_id']=='0') {
				$cdata = array(); 	
				$cdata['store_id'] = 0; 
				$cdata['firstname'] = $db->func_escape_string($_POST['orders_details']['first_name']); 
				$cdata['lastname'] = $db->func_escape_string($_POST['orders_details']['last_name']);
				//$cdata['company'] = $db->func_escape_string($_POST['orders_details']['company']); 
				$cdata['email'] = $db->func_escape_string($_POST['orders']['email']);; 
				$cdata['telephone'] = $db->func_escape_string($_POST['orders_details']['phone_number']);
				$cdata['password'] = md5('phoneparts123'); 
				$cdata['fax'] = ''; 
				$cdata['cart'] = 'a:0:{}'; 
				$cdata['wishlist'] = ''; 
				$cdata['newsletter'] = 0; 
				$cdata['customer_group_id'] = $_POST['customer_group_id']; 
				$cdata['ip'] = $_SERVER['REMOTE_ADDR']; 
				$cdata['status'] = 1; 
				$cdata['approved'] = 1; 
				$cdata['date_added'] = date('Y-m-d H:i:s'); 
				$_POST['customer_id'] = $db->func_array2insert("oc_customer",$cdata);

				$cdata = array();
				$cdata['customer_id'] = $_POST['customer_id']; 
				$cdata['firstname'] = $db->func_escape_string($_POST['orders_details']['first_name']); 
				$cdata['lastname'] = $db->func_escape_string($_POST['orders_details']['last_name']); 
				$cdata['address_1'] = $db->func_escape_string($_POST['orders_details']['address1']);
				$cdata['address_2'] = $db->func_escape_string($_POST['orders_details']['address2']);
				$cdata['city'] = $db->func_escape_string($_POST['orders_details']['city']);
				$cdata['postcode'] = $db->func_escape_string($_POST['orders_details']['zip']);
				$cdata['country_id'] = $country_id;
				$cdata['zone_id'] = $zone_id;
				$address_id = $db->func_array2insert("oc_address",$cdata);

				$db->db_exec("UPDATE oc_customer SET address_id='".(int)$address_id."' WHERE customer_id='".(int)$_POST['customer_id']."'");
			}

			$oc_order_check = $db->func_query_first("SELECT order_id FROM oc_order WHERE order_id='".$_POST['orders']['order_id']."'");
			$array = array();

			if(empty($oc_order_check))
			{
				$array['order_id'] = $_POST['orders']['order_id'];
			}

			$array['invoice_prefix'] = oc_config("config_invoice_prefix");
			$array['store_id'] = oc_config("config_store_id");
			$array['store_name'] = oc_config("config_name");
			$array['store_url'] = "https://phonepartsusa.com/";
			$array['customer_id'] = $_POST['customer_id'];
			$array['customer_group_id'] = $_POST['customer_group_id'];
			$array['firstname'] = $db->func_escape_string($_POST['orders_details']['first_name']);
			$array['lastname'] = $db->func_escape_string($_POST['orders_details']['last_name']);
			//$array['company'] = $db->func_escape_string($_POST['orders_details']['company']);
			$array['email'] = $db->func_escape_string($_POST['orders']['email']);
			$array['telephone'] = $db->func_escape_string($_POST['orders_details']['phone_number']);
			$array['fax'] = '';
			$array['payment_firstname'] = $db->func_escape_string($_POST['orders_details']['first_name']);
			$array['payment_lastname'] = $db->func_escape_string($_POST['orders_details']['last_name']);
			$array['payment_company'] = '';
			$array['payment_company_id'] = '';
			$array['payment_tax_id'] = '';
			$array['payment_address_1'] = $db->func_escape_string($_POST['orders_details']['address1']);
			$array['payment_address_2'] = $db->func_escape_string($_POST['orders_details']['address2']);;
			$array['company']=$db->func_escape_string(htmlentities($_POST['orders_details']['company']));
			$array['payment_city'] = $db->func_escape_string($_POST['orders_details']['city']);
			$array['payment_postcode'] = $db->func_escape_string($_POST['orders_details']['zip']);
			$array['payment_country'] = $db->func_escape_string($_POST['orders_details']['country']);
			$array['payment_country_id'] = $country_id;
			$array['payment_zone'] = $db->func_escape_string($_POST['orders_details']['state']);
			$array['payment_zone_id'] = $zone_id;
			$array['payment_address_format'] = '{firstname} {lastname}
			{company}
			{address_1}
			{address_2}
			{city}, {zone} {postcode}
			{country}';
			$array['payment_method'] = ($_POST['charge_aim']?'Credit or Debit Card (Processed securely by PayPal)':'Unpaid');
			$array['payment_code'] = ($_POST['charge_aim']?'authorizenet_aim':'unpaid');
			if(isset($_POST['orders_details']['payment_method']))
			{

			$array['payment_method'] = $_POST['orders_details']['payment_method'];
			$array['payment_code'] = 'in_store';
			
			}
			$array['shipping_firstname'] = $db->func_escape_string($_POST['orders_details']['first_name']);
			$array['shipping_lastname'] = $db->func_escape_string($_POST['orders_details']['last_name']);
			$array['shipping_company'] = '';
			$array['shipping_address_1'] = $db->func_escape_string($_POST['orders_details']['address1']);
			$array['shipping_address_2'] = $db->func_escape_string($_POST['orders_details']['address2']);;
			$array['shipping_city'] = $db->func_escape_string($_POST['orders_details']['city']);
			$array['shipping_postcode'] = $db->func_escape_string($_POST['orders_details']['zip']);
			$array['shipping_country'] = $db->func_escape_string($_POST['orders_details']['country']);
			$array['shipping_country_id'] = $country_id;
			$array['shipping_zone'] = $db->func_escape_string($_POST['orders_details']['state']);
			$array['shipping_zone_id'] = $zone_id;
			$array['shipping_address_format'] = '{firstname} {lastname}
			{company}
			{address_1}
			{address_2}
			{city}, {zone} {postcode}
			{country}';
			$array['shipping_method'] = $db->func_escape_string($_POST['orders_details']['shipping_method']);
			$array['shipping_code'] = $db->func_escape_string($_POST['shipping_code']);
			$array['comment'] = '';
			$array['order_status_id'] = ($_SESSION['paid_order']?'15':'32');
			$array['affiliate_id'] = 0;
			$array['language_id'] = 1;
			$array['currency_id'] = 2;
			$array['currency_code'] = "USD";
			$array['currency_value'] = 1.00;
			$array['date_added'] = date('Y-m-d H:i:s');
			$array['date_modified'] = date('Y-m-d H:i:s');
			$array['admin_view_only']=1;
			$array['is_manual']=1;


			$order_id = $db->func_array2insert("oc_order",$array);

			$xsub_total = 0.00;
			$xtotal = 0.00;
			foreach($_POST['orders_items'] as $order_item) {
				if($order_item['product_price']) {
					$xsub_total+=(float)$order_item['product_price'];
					$xtotal+=(float)$order_item['product_price'];

					$order_product = $db->func_query_first("SELECT * FROM oc_product WHERE sku='".$order_item['product_sku']."'");


					$db->db_exec("INSERT INTO oc_order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$order_product['product_id'] . "', name = '" . $order_product['name'] . "', model = '" . ($order_product['model']) . "', quantity = '" . (int)$order_item['product_qty'] . "', price = '" . (float)$order_item['product_unit'] . "', total = '" . (float)$order_item['product_price'] . "', tax = '" . (float)0.00 . "', reward = '0'");	
				}


			}

			$array = array();
			$array['order_id'] = $order_id;
			$array['code'] = 'sub_total';
			$array['title'] = 'Sub-Total';
			$array['text'] = '$'.number_format($xsub_total,2);
			$array['value'] = (float)$xsub_total;
			$array['sort_order'] = 1;

			$db->func_array2insert("oc_order_total",$array);

			$shipping_cost = $_POST['orders_details']['shipping_cost'];
			$xtotal = $xtotal+$shipping_cost;
			$array = array();
			$array['order_id'] = $order_id;
			$array['code'] = 'shipping';
			$array['title'] = $_POST['orders_details']['shipping_method'];
			$array['text'] = '$'.number_format($shipping_cost,2);
			$array['value'] = (float)$shipping_cost;
			$array['sort_order'] = 3;

			$db->func_array2insert("oc_order_total",$array);
			$tax_amount = 0.00;
			if($zone_id=='3651' && $dis_tax == '0') {
				$tax_detail = $db->func_query_first("SELECT * FROM oc_tax_rate WHERE geo_zone_id=10");
				$tax_amount = ($xsub_total*(float)$tax_detail['rate'])/100;
				$xtotal = $xtotal+$tax_amount;
				$array = array();
				$array['order_id'] = $order_id;
				$array['code'] = 'tax';
				$array['title'] = $tax_detail['name'];
				$array['text'] = '$'.number_format($tax_amount,2);
				$array['value'] = (float)$tax_amount;
				$array['sort_order'] = 5;

				$db->func_array2insert("oc_order_total",$array);
			}

			$array = array();
			$array['order_id'] = $order_id;
			$array['code'] = 'total';
			$array['title'] = 'Total';
			$array['text'] = '$'.number_format($xtotal,2);
			$array['value'] = (float)$xtotal;
			$array['sort_order'] = 9;
			$db->func_array2insert("oc_order_total",$array);
		}
		$db->db_exec("UPDATE oc_order SET total='".(float)$xtotal."' WHERE order_id='".$order_id."'");

		
		$_POST['orders']['order_date']   = date('Y-m-d H:i:s');
		$_POST['orders']['order_price']  = 0;
		$_POST['orders']['dateofmodification'] = date('Y-m-d H:i:s');
		if($_POST['orders']['order_id']=='' || $_POST['orders']['store_type']=='web') {
			$_POST['orders']['order_id'] = $order_id;	
			
		}


		if($_POST['orders']['store_type'] != 'po_business') {
			if($_SESSION['paid_order']) {
				$_POST['orders']['order_status'] = 'Paid';
				unset($_SESSION['paid_order']);
			} else if ($_POST['orders']['store_type'] == 'web') {
				$_POST['orders']['order_status'] = 'Estimate';
			} else {
				$_POST['orders']['order_status'] = 'Estimate';
			}
		}
		//if ($_POST['orders']['store_type'] != 'po_business') {
		$_POST['orders']['is_manual'] = 1;
		//}

		$_POST['orders']['customer_name'] = $_POST['orders_details']['first_name'].' '.$_POST['orders_details']['last_name'];
		$_POST['orders']['order_user'] = $_SESSION['user_id'];
		$_POST['orders']['shipping_amount'] = (float)$_POST['orders_details']['shipping_cost'];;
		$_POST['orders']['sub_total'] = (float)$xsub_total;
		$_POST['orders']['tax'] = (float)$tax_amount;


		$db->func_array2insert("inv_orders", $_POST['orders']);
		
		$_POST['orders_details']['order_id'] = $order_id;
		$_POST['orders_details']['company'] = htmlentities($_POST['orders_details']['company']);
		$_POST['orders_details']['dateofmodification'] = date('Y-m-d H:i:s');
		if($_POST['orders']['order_type']=='replacement')
		{
			$_POST['orders_details']['payment_method'] = 'Replacement';
		}
		$_POST['orders_details']['bill_firstname'] = $_POST['orders_details']['first_name'];
		$_POST['orders_details']['bill_lastname'] = $_POST['orders_details']['last_name'];
		$db->func_array2insert("inv_orders_details", $_POST['orders_details']);
		$po_order_total = $_POST['orders_details']['shipping_cost'];
		$items_true_cost = 0.00;
		foreach($_POST['orders_items'] as $order_item){
			$order_item['order_id'] = $order_id;
			$order_item['ostatus'] = strtolower($_POST['orders']['order_status']);
			unset($order_item['avail_qty']);
			
			//check if SKU is KIT SKU
			$item_sku = $db->func_escape_string($order_item['product_sku']);
			$order_item['product_true_cost'] = getTrueCost($item_sku);
			$po_order_total += $order_item['product_price'];
			if(strlen($item_sku) > 5) {
				$kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$item_sku'");
				if($kit_skus){
					$kit_skus_array = explode(",",$kit_skus['linked_sku']);
					$z=0;
					foreach($kit_skus_array as $kit_skus_row){
						if($z>0)
						{
							$order_item['product_true_cost'] = 0.00;	
							$order_item['product_price'] = 0.00;	
							$order_item['product_unit'] = 0.00;	
						}
						$items_true_cost = (float)$items_true_cost+($order_item['product_true_cost']*(int)$order_item['product_qty']);
						$order_item['product_sku']  = $kit_skus_row;
						$db->func_array2insert("inv_orders_items",$order_item);
						$z++;
					}

					//mark kit sku need_sync on all marketplaces
					$db->db_exec("update inv_kit_skus SET need_sync = 1 where kit_sku = '$item_sku'");
				}
				else{

					$items_true_cost = (float)$items_true_cost+($order_item['product_true_cost']*(int)$order_item['product_qty']);
					$db->func_array2insert("inv_orders_items",$order_item);
				}
			}
		}

		$db->db_exec("UPDATE inv_orders SET items_true_cost='".(float)$items_true_cost."' WHERE order_id='".$order_id."'");
		
		//upload return item item images
		if($_FILES['order_docs']['tmp_name']) {
			$imageCount = 0;
			$orderID  = $db->func_escape_string($_GET['order']);
			$count    = count($_FILES['order_docs']['tmp_name']);

			for($i=0; $i<$count; $i++){
				$uniqid = uniqid();
				$name   = explode(".",$_FILES['order_docs']['name'][$i]);
				$ext    = end($name);

				$destination = $path."files/".$uniqid.".$ext";
				$file = $_FILES['order_docs']['tmp_name'][$i];

				if(move_uploaded_file($file, $destination)){
					$orderDoc = array();
					$orderDoc['attachment_path'] = "files/".basename($destination);
					$orderDoc['type'] = $_FILES['order_docs']['type'][$i];
					$orderDoc['size'] = $_FILES['order_docs']['size'][$i];
					$orderDoc['date_added'] = date('Y-m-d H:i:s');
					$orderDoc['order_id']   = $order_id;

					$db->func_array2insert("inv_order_docs",$orderDoc);
					$imageCount++;
				}
			}
		}


		if ($_POST['orders']['store_type'] == 'po_business' || $_POST['orders']['store_type'] == 'web') {

			if ($_POST['order']['voucher_code']) {
				
				$codes = explode(',', $_POST['order']['voucher_code']);
				$orderTotal = ($_POST['orders']['store_type'] == 'web')? (float)$xtotal: (float)$po_order_total;
				$totalUsed = 0;
				foreach ($codes as $code) {
					$voucher = $db->func_query_first("SELECT a.*, (a.`amount` + SUM(b.`amount`)) balance FROM `oc_voucher` a LEFT OUTER JOIN `oc_voucher_history` b ON a.`voucher_id` = b.`voucher_id` WHERE a.code = '". $code ."'");
					if (!$voucher['balance']) {
						$voucher['balance'] = $voucher['amount'];
					}

					if ($orderTotal > 0) {

						if ($orderTotal > $voucher['balance']) {
							$orderTotal = $orderTotal - $voucher['balance'];
							$used = $voucher['balance'];
						} else if ($orderTotal <= $voucher['balance']) {
							$used = $voucher['balance'] - $orderTotal;
							$used = $voucher['balance'] - $used;
							$orderTotal = 0;
						}

						$voucher_array = array();
						$voucher_array['voucher_id'] = $voucher['voucher_id'];
						//if ($_POST['orders']['store_type'] == 'web') {
							$voucher_array['order_id'] = $order_id;
						//} else {
						//	$voucher_array['inv_order_id'] = $order_id;
						//}
						$totalUsed += $used;
						$voucher_array['amount'] = '-'.$used;
						$voucher_array['date_added'] = date('Y-m-d h:i:s');
						$db->func_array2insert("oc_voucher_history",$voucher_array);

						$vouch_id = addVoucher($order_id,'store_credit',$used,linkToVoucher($voucher['voucher_id'],'', $voucher['code']));
						$db->db_exec("UPDATE inv_vouchers SET voucher_id='".(int)$voucher['voucher_id']."' where id='".$vouch_id."'");

						$accounts = array();
					$accounts['description'] = $code.' Applied @ Order # '.$order_id;
					$accounts['credit'] = 0.00;
					$accounts['debit'] = $used*(-1);
					$accounts['order_id'] = $order_id;
					$accounts['customer_email'] = $voucher['email'];
					$accounts['type']='store_credit';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // store credit applied


					$accounts = array();
					$accounts['description'] = $code.' Applied @ Order #'.$order_id;
					$accounts['debit'] = 0.00;
					$accounts['credit'] = $used*(-1);
					$accounts['order_id'] = $order_id;
					$accounts['customer_email'] = $voucher['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'store_credit';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // store credit applied

					}
				}
				
			}
			unset($_POST['order']['voucher_code']);

		}

		
		$hdata = array();
		$hdata['order_id'] = $order_id;
		$hdata['comment'] = 'Order # '. linkToOrder($order_id) .' has been created.';
		$hdata['user_id'] = $_SESSION['user_id'];
		$hdata['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_order_history",$hdata);

		actionLog($hdata['comment']);
		
		orderTotal($order_id, true);
		unset($_SESSION['cart']);
		$_SESSION['message'] = "Order created successfully.";
		header("Location:viewOrderDetail.php?order=$order_id");
		exit;
	}
}

$po_business = $db->func_query("select id , company_name from inv_po_customers");

$months = array();
for ($i = 1; $i <= 12; $i++) {
	$months[] = array(
		'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)), 
		'value' => sprintf('%02d', $i)
		);
}

$today = getdate();

$year_expire = array();

for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
	$year_expire[] = array(
		'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
		'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) 
		);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Create Order</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	<style type="text/css">
		.reqprc {display: none;}
	</style>
	<script type="text/javascript">
		var current_row = 20;
		function addRow(){
			var row = "<tr class='product'>"+
			"<td>"+(current_row + 1)+"</td>"+
			"<td align='center'><?php echo createField('orders_items["+current_row+"][product_sku]', 'product_sku"+current_row+"', 'text',null , null , 'onChange=\'duplicateSkuCheck(this)\' data-index=\'"+current_row+"\' class=\'sku\'')?></td>"+
			"<td align='center'><?php echo createField('orders_items["+current_row+"][product_qty]', 'product_qty"+current_row+"', 'text',null , null , 'onChange=\'duplicateSkuCheck(this)\' data-index=\'"+current_row+"\' class=\'qty\'')?></td>"+
			"<td align='center'><?php echo createField('orders_items["+current_row+"][avail_qty]', 'avail_qty"+current_row+"', 'text',null , null , ' readOnly')?></td>"+
			"<td align='center'>"+
			"<?php echo createField('orders_items["+current_row+"][product_unit]', 'product_unit"+current_row+"', 'text' , null ,null, ((!$_SESSION['order_price_override'])? 'readOnly' : '' ) . ' data-index=\'"+current_row+"\' class=\'product_units\' onChange=\'updateOverPrice(this)\'')?>"+
			"<?php echo (!$_SESSION['order_price_override'])? '<div class=\'reqprc\'><input data-index-ch=\'"+current_row+"\' style=\'width: 30px;\' /><button class=\'btn\' onclick=\'requestCPrice("+current_row+");\'>Req</button></div>' : ""; ?>"+
			"</td>"+
			"<td align='center'><select name='orders_items["+current_row+"][product_discount]' id='product_discount"+current_row+"' onchange='updateOverPrice(this)' data-index='"+current_row+"'><option value='0'>No Discount</option><option value='5' >5%</option><option value='10'>10%</option><option value='15'>15%</option><option value='25'>25%</option><option value='50'>50%</option></select></td>"+
			"<td align='center'><?php echo createField('orders_items["+current_row+"][product_price]', 'product_price"+current_row+"', 'text',null , null , 'readOnly class=\'productTotal\' onchange=\'productTotal()\' data-index=\'"+current_row+"\'')?></td>"+
			"<td><a href='javascript://' onclick='$(this).parent().parent().remove();'>X</a></td>"+
			"</tr>";
			$("#order_items").append(row);		
			current_row++;	 
		}

		function checkCustomer(store_type){
			if(store_type == 'po_business' ){

				jQuery('.po_business').show();
				jQuery('.voucher').show();
				jQuery.ajax({
					url: 'order_create.php?&action=next_po',
					dataType:"json",
					success: function(json){
						
						$('#order_id').val(json['order_id']);
					}
				});

			}
			else{
				jQuery('.po_business').hide();
				jQuery('.voucher').hide();
				if (store_type == 'web') {
					jQuery('.voucher').show();

					jQuery.ajax({
						url: 'order_create.php?&action=next_web_order_id',
						dataType:"json",
						success: function(json){

							$('#order_id').val(json['order_id']);
						}
					});
				}

				$('#order_id').val('');
			}
		}

	 	  /* function FillAddress(business_id){
				jQuery.ajax({
					url: 'order_create.php?business_id='+business_id+'&action=business',
					success: function(data){
						customer = jQuery.parseJSON(data);
						jQuery("#address1").val(customer['address1']);
						jQuery("#address2").val(customer['address2']);
						jQuery("#city").val(customer['city']);
						jQuery("#email").val(customer['email']);
						jQuery("#state").val(customer['state']);
						jQuery("#xstate option:contains("+customer['state']+")").prop('selected',true);
						jQuery("#zip").val(customer['zip']);
						jQuery("#phone_number").val(customer['telephone']);
						jQuery("#first_name").val(customer['firstname']);
						jQuery("#last_name").val(customer['lastname']);
						getShipping();
					}
			    });
			}*/

			function PopulateAddress(address_id){
				var business_id = $('#po_business_id').val();
				jQuery.ajax({
					url: 'order_create.php?business_id='+business_id+'&address_id='+address_id+'&action=business_address',
					success: function(data){
						customer = jQuery.parseJSON(data);
						jQuery("#address1").val(customer['address1']);
						jQuery("#address2").val(customer['address2']);
						jQuery("#city").val(customer['city']);
						jQuery("#email").val(customer['email']);
						jQuery("#state").val(customer['state']);
						jQuery("#xstate option:contains("+customer['state']+")").prop('selected',true);
						jQuery("#zip").val(customer['zip']);
						jQuery("#phone_number").val(customer['telephone']);
						jQuery("#first_name").val(customer['firstname']);
						jQuery("#last_name").val(customer['lastname']);
						getShipping();
					}
				});
			}

			function FillAddress(business_id){
				jQuery.ajax({
					url: 'order_create.php?business_id='+business_id+'&action=business',
					success: function(data){
						$('#po_customer_address').html(data);

					//	getShipping();
				}
			});
			}

			function validateOrder()
			{
			   //var returnable = 'no';
			   var status = true;
			   $('input[type=text]').each(function(index, element) {
			   	if($(this).attr('required') && $(this).val()=='')
			   	{
			   		alert("Please Fill the mandatory fields first");
			   		$(this).focus();
			   		status = false;
			   		return false;	
			   	}
			   });

			   if(status==false)
			   {
			   	return false;   

			   }
			   
			   $.ajax({
			   	url: 'order_create.php',
			   	type: 'post',
			   	data: {action:'ajax_order_validate',order_id:$('#order_id').val()},
			   	dataType: 'json',		
			   	beforeSend: function() {
			   		$('#confirm-btn').attr('disabled', true);
			   		$('#confirm-btn').val('Processing...');
			   	},
			   	complete: function() {

			   	},				
			   	success: function(json) {
			   		if (json['error']) {
			   			if($('#order_id').val()=='')
			   			{

			   				confirmAim();
			   			}
			   			else
			   			{
			   				alert(json['error']);
			   				return false;
			   			}
			   		}

			   		if (json['success']) {
			   			confirmAim();
			   		}
			   	}

			   });   

			}
			function confirmAim()
			{



				$.ajax({
					url: 'ajax_aim_send.php',
					type: 'post',
					data: $('#frm :input'),
					dataType: 'json',		
					beforeSend: function() {
						$('#confirm-btn').attr('disabled', true);
						$('#confirm-btn').val('Processing...');
					},
					complete: function() {
						$('#confirm-btn').attr('disabled', false);
						$('#confirm-btn').val('Confirm');
					},				
					success: function(json) {
						if (json['error']) {
							alert(json['error']);

						}

						if (json['success']) {
							alert(json['success']);
							$('input[name=saveOrder]').click();
						}
					}
				});   

			}
			$(document).ready(function(e) {
				$('.fancybox3').fancybox({ width: '90%' , autoCenter : true , autoSize : true });
				checkCustomer($('#xstore_type').val());

			});
		</script>	
	</head>
	<body>
	    <?php if (!$_GET['hide_header']) { ?>
		<div align="center"> 
		<?php } else { ?>
		<div style="display: none;" align="center">
		<?php } ?>
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<div align="center">
			<form method="post" id="frm" enctype="multipart/form-data" onsubmit="if(!confirm('Are you sure want to continue')) return false;">
				<h2>Add New Order <br /></h2>
				
				<table border="1" width="60%" cellpadding="5" cellspacing="0" align="center">
					<tr>
						<td>Select Store</td>
						<td>
							<select name="orders[store_type]" id="xstore_type" onchange="checkCustomer(this.value);">
								<option value="web">PPUSA</option>
								<option value="ebay">OverStock Partz eBay</option>
								<option value="amazon">Amazon</option>
								<option value="amazon_ca">Amazon CA</option>
								<option value="amazon_mx">Amazon MX</option>
								<option value="amazon_pg">Amazon PG</option>
								<option value="amazon_pgca">Amazon PGCA</option>
								<option value="amazon_pgmx">Amazon PGMX</option>
								<option value="channel_advisor">Channel Advisor</option>
								<option value="bonanza">Bonanza</option>
								<option value="wish">Wish</option>
								<option value="bigcommerce">RLCD</option>
								<option value="open_sky">Open Sky</option>
								<option value="po_business">PO Business</option>
							</select>

							<label class="po_business" style="display:none;">Status: <strong id="po_status_val">Estimate</strong></label>
							<select name="orders[order_status]" class="" id="select_order_status" style="display:none;">
								<option value="Estimate">Estimate</option>
								<option value="Unshipped">Unshipped</option>
								<option value="Shipped">Shipped</option>
							</select>
							<!--<input type="button" class="button po_business" style="display:none" value="Confirm Order" onclick="changeOrderStatus('Unshipped',this)" /> -->
							<script>
								function changeOrderStatus(status,obj)
								{
									if(!confirm('Are you sure?'))
									{
										return false; 
									}
									else
									{
										$('#select_order_status option[value='+status+']').prop('selected',true);
									 //$('#po_status_val').html($('#select_order_status option:selected').val());
									 $(obj).hide();
									}
								}

							</script>
						</td>

						<td>Order ID</td>
						<td><?php echo createField("orders[order_id]", "order_id", "text" , $_POST['orders']['order_id'], null , "")?></td>
					</tr>

					<tr class="po_business" style="display:none;">
						<td>Customer</td>
						<td>
							<select id="po_business_id" name="orders[po_business_id]" onchange="FillAddress(this.value);">
								<option value="0">Select Customer</option>
								<?php foreach($po_business as $business):?>
									<option value="<?php echo $business['id']?>"><?php echo $business['company_name']?></option>
								<?php endforeach;?>
							</select>

							<input type="file" name="order_docs[]" multiple="true" id="order_docs"  />
						</td>

						<td>Customer PO #</td>
						<td><?php echo createField("orders[customer_po]", "customer_po", "text" , $_POST['orders']['customer_po'], null , "")?></td>
					</tr>

					<tr class="po_business" style="display:none;">
						<td>Select Address</td>
						<td colspan="3">
							<select id="po_customer_address"  onchange="PopulateAddress(this.value);">
								<option value="">Select Address</option>

							</select>


						</td>
					</tr>

					<tr>
						<td>First Name</td>
						<td><?php echo createField("orders_details[first_name]", "first_name" , "text" , $_POST['orders_details']['first_name'] , null , " tabindex='3' required")?></td>
						<td>Company:</td>
						<td><?php echo createField("orders_details[company]", "company", "text" , $_POST['orders_details']['company'] , null , "tabindex='7' ")?></td>
						
					</tr>

					<tr>
						<td>Last Name</td>
						<td><?php echo createField("orders_details[last_name]", "last_name", "text" , $_POST['orders_details']['last_name'] , null , "tabindex='4' required")?></td>
						<td>Address 1 <br> <br> Address 2</td>
						<td><?php echo createField("orders_details[address1]", "address1", "text" , $_POST['orders_details']['address1'] , null , "tabindex='7' required")?><br><br>
						<?php echo createField("orders_details[address2]", "address2", "text" , $_POST['orders_details']['address2'] , null , "tabindex='7'")?></td>
						
					</tr>

					<tr>
						<td>Email</td>
						<td><?php echo createField("orders[email]", "email", "email",$_POST['orders']['email'] , null , "tabindex='5' required")?> <a href="customer_lookup.php" class="fancybox3 fancybox.iframe to_be_hidden" >Customer Lookup</a></td>
						<td>City</td>
						<td><?php echo createField("orders_details[city]", "city", "text" , $_POST['orders_details']['city'] , null , "tabindex='8' required")?></td>
						
					</tr>

					<tr>
						<td>TelePhone</td>
						<td><?php echo createField("orders_details[phone_number]", "phone_number", "number", $_POST['orders_details']['phone_number'], null, " tabindex='6' ")?></td>
						<td>State</td>
						<td>
							<?php
							$states_query = $db->func_query("SELECT zone_id,name FROM oc_zone WHERE country_id='223' AND status=1 ORDER BY name");
							?>
							<select id="xstate" name="orders_details[zone_id]" tabindex="9" required onchange="getShipping();" style="width:156px;">
								<option value="">Select State</option>
								<?php
								foreach($states_query as $state)
								{
									?>
									<option value="<?php echo $state['zone_id'];?>" <?php if($_POST['orders_details']['state']==$state['name']) echo 'selected'; ?>><?php echo $state['name'];?></option>
								<?php 
								}
								?>
							</select>
							<input type="hidden" name="orders_details[state]" id="state" value="<?php echo $_POST['orders_details']['state'];?>" />
						</td>
						
					</tr>
					<tr>
						<td>Order Type</td>
						<td>
							<select id="order_type" onchange="orderTypeChange()" name="orders[order_type]">
								<option value="new">New Order</option>
								<option value="replacement">Replacement Order</option>
								<option value="fb">FB Upload</option>

							</select>
						</td>
						<td>Zip Code</td>
						<td><?php echo createField("orders_details[zip]", "zip", "text" , $_POST['orders_details']['zip'], null , " tabindex='10' ")?>
							<input type="hidden" id="customer_group_id" value="<?=$x_customer_group_id;?>" name="customer_group_id" />
							<input type="hidden" id="customer_id" value="<?=$x_cust_id;?>" name="customer_id" />
						</td>



					</tr>
					<tr>
						<td>Shipping Method</td>
						<td><select onchange="shippingCost()" id="select_shipping" required>
							<option value="">Select Shipping Method</option>

						</select>
						<input type="text" name="orders_details[customer_fedex_code]" class="customer_fedex_code" placeholder="Please provide Code " maxlength="10" style="display:none;margin-top:5px;width:225px" />
						<input type="hidden" name="orders_details[shipping_method]" id="shipping_method" />
						<input type="hidden" name="shipping_code" id="shipping_code" />

					</td>
					<td>Country</td>
						<td>
							<?php
							$countries = $db->func_query("SELECT * FROM oc_country WHERE status = 1 ORDER BY `name`");
							$_POST['orders_details']['country'] = ($_POST['orders_details']['country'])? $_POST['orders_details']['country']: 'United States';
							?>
							<select id="xcountry" name="orders_details[country_id]" onchange="countryChange()">
								<?php foreach($countries as $country) { ?>
								<option value="<?php echo $country['country_id'];?>" <?php if($_POST['orders_details']['country']==$country['name']) echo 'selected'; ?>><?php echo $country['name'];?></option>
								<?php } ?>
							</select>
							<input type="hidden" name="orders_details[country]" id="country" value="<?php echo $_POST['orders_details']['country'];?>" />
						</td>
					
				</tr>
				<tr>
					<td>Terms</td>
					<td>
						
						<select name="orders[terms]" >
						
							<option value="Prepaid">Prepaid</option>
							<option value="Net 15">Net 15</option>
							<option value="Net 30">Net 30</option>
						</select>
					</td>
					<td></td>
					<td></td>

				</tr>
				<tr class="po_business" style="display:none">
					<td>Terms</td>
					<td>
						<?php
						$terms = array(5,10,15,30,45);
						?>
						<select name="orders_details[po_term]" >
							<option value="0">No Terms</option>
							<?php
							foreach($terms as $term)
							{
								?>
								<option value="<?=$term;?>" <?php if($term==$order['po_term']) echo 'selected';?>>Net <?=$term;?></option>
								<?php
							}
							?>
						</select>
					</td>
					<td>Reference</td>
					<td><input type="text" name="orders_details[reference_no]" id="reference_no" placeholder="Max 35 Characters" maxlength="35"  value="<?=$_POST['orders_details']['reference_no'];?>" size="30" /></td>
				</tr>
				<tr class="voucher">
					<td>Voucher <b><span class="total"></span></b></td>
					<td><input type="text" name="order[voucher_code]" id="voucher_code" onchange="verifyVoucher(this);" placeholder="Add multiple voucher sapreted by comma (,)"  value="<?=$_POST['orders_details']['voucher_code'];?>" size="40"/><br><span class="error" style="color: #F00;"></span></td>
					<td>Shipping Cost</td>
					<td> <input type="text" onkeyup="allowNum(this)" name="orders_details[shipping_cost]" id="shipping_cost" readOnly value="0" /></td>
				</tr>
				<tr class="totalOrderPrice">
					<td colspan="2" >
						<table border="0">
							<tr class="subTotalX">
								<td>Sub Total</td>
								<td align="right">$<b>0.00</b></td>
							</tr>
							<tr class="taxX">
								<td>Tax <input type="hidden" id="taxX" value="0"></td>
								<td align="right">$<b>0.00</b></td>
							</tr>
							<tr class="shippingCostX">
								<td>Shipping Cost</td>
								<td align="right">$<b>0.00</b></td>
							</tr>
							<tr class="voucherX">
								<td>Voucher Amount</td>
								<td align="right">-$<b>0.00</b></td>
							</tr>
							<tr class="totalX">
								<td>Total</td>
								<td align="right">$<b>0.00</b></td>
							</tr>
						</table>
					</td>
					<?php

							if($_SESSION['edit_payment_method'] ):

							?>
					<td>Payment Method</td>
					<td><select name="orders_details[payment_method]" >



								



								<option value="Cash or Credit at Store Pick-Up">Cash or Credit at Store Pick-Up</option>



								<option value="Card">Card</option>



								<option value="PayPal">PayPal</option>
								<option value="Cash On Delivery">COD</option>
								<option value="Behalf">Behalf</option>
								<option value="Wire Transfer">Wire Transfer</option>
								<option value="check">Check</option>



							</select></td>
					<?php

					endif;
					?>
					
				</tr>
                     <!--<tr class="po_business" style="display:none">
                     <td>Payment Status:</td>
                     <td><a href="popupfiles/payment_status.php" class="fancybox3 fancybox.iframe button" >Update</a></td>
                 </tr>-->
             </table>
             <script>
             	function verifyVoucher(t) {
             		if ($(t).val()) {
             			$.ajax({
             				url: 'order_create.php',
             				type: 'post',
             				dataType: 'json',
             				data: {action: 'verifyVoucher', vouchers: $(t).val()},
             			})
             			.always(function(data) {             				
             				if (data['error']) {
             					$('.voucher .error').html(data['msg']);
             					$('.voucher .total').html(data['total']);
             					makeTotal();
             					$('#voucher_code').val(data['valid']);
             				}
             				if (data['success']) {
             					$('.voucher .error').html('');
             					$('.voucher .total').html(data['total']);
             					makeTotal();
             					$('#voucher_code').val(data['valid']);
             				}
             			});
             			
             		}
             	}

             	function productTotal () {
             		var total = 0.00;
             		$('.productTotal').each(function() {
             			if ($(this).val()) {
             				total = total + parseFloat($(this).val());
             			}
             		});
             		$('.subTotalX').find('b').text(Number(total.toFixed(2)));
             		makeTotal();
             	}
             	function makeTotal () 
             	{
             		var total = parseFloat($('.subTotalX').find('b').text()) + parseFloat($('.shippingCostX').find('b').text());

             		var tax = parseFloat($('#taxX').val());
             		var taxAmount = (parseFloat($('.subTotalX').find('b').text())*tax)/100;
             		$('.taxX').find('b').text(Number(taxAmount.toFixed(2)));
             		var vouA = $('.voucher .total').text();
             		if (!vouA) {
             			vouA = '$0.00';
             		}
             		var vou = parseFloat(vouA.substr(1));

             		total = total + taxAmount;

             		if (vou >= total) {
             			$('.voucherX').find('b').text(Number(total.toFixed(2)));
             			total = 0.00;
             		} else if (total > vou) {
             			$('.voucherX').find('b').text(Number(vou.toFixed(2)));
             			total = total - vou;
             		}
             		$('.totalX').find('b').text(Number(total.toFixed(2)));
             	}

             </script>
             <div id="customer_addresses"></div>
             <?php
             if($customer_addresses)
             {
             	?>
             	<br> <br>
             	<h2>Previous Addresses</h2>
             	<select id="customer_address" size="5" onchange="populateCustomerAddress(this)">
             		<?php
             		foreach ($customer_addresses as $c_address) {
             			?>
             			<option value="<?=$c_address['address1'];?>~<?=$c_address['city'];?>~<?=$c_address['state'];?>~<?=$c_address['zip'];?>"><?=$c_address['address1'];?>, <?=$c_address['city'];?>, <?=$c_address['state'];?>, <?=$c_address['zip'];?>. </option> 

             			<?php
             		}
             		?>

             	</select>
             	<?php
             }
             ?>

             <br /> <br />
             <a href="popupfiles/import_order_files.php" style="clear:both" id="decision-anchor" class="fancybox3 fancybox.iframe button">Import Order Items</a>
             <!-- <a onclick="pCatalog();" style="clear:both" class="button">Product Catalog</a> -->
             <a style="clear:both; display: none;" class="fancybox3 fancybox.iframe" id="pCat"></a>
             <script type="text/javascript">
             	function pCatalog () {
             		var email = $('#email').val();

             		var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
             		if (!re.test(email)) {
             			alert('Please enter email of select customer!');
             			return false;
             		}

             		$('#pCat').attr('href', 'product_catalog/man_catalog.php?email=' + email).click();
             	}

             	function addProduct (sku, qty) {
             		$('.product .sku').each(function() {
             			addRow();
             			if ($(this).val() == sku) {
             				$(this).parent().parent().find('.qty').val(qty).trigger('change');
             				return false;
             			}
             			if ($(this).val() == '') {
             				$(this).val(sku);
             				$(this).parent().parent().find('.qty').val(qty).trigger('change');
             				return false;
             			}

             		});
             	}
             </script>
             <br /><br />
             <table border="1" width="60%" cellpadding="5" cellspacing="0" align="center" id="order_items">
             	<tr>	
             		<th>#</th>
             		<th>SKU</th>
             		<th>Qty</th>
             		<th>Available Qty</th>
             		<th class="to_be_hidden"><div style="width: 180px;">Unit Price</div></th>
             		<th class="to_be_hidden">Discount</th>
             		<th class="to_be_hidden">Total</th>
             		<th>
             			<a href="javascript://" onclick="addRow();">Add Row</a>
             		</th>
             	</tr>

             	<?php for($i=0; $i<20; $i++):?>
             		<tr class="product">
             			<td><?php echo $i+1;?></td>
             			<td align="center"><?php echo createField("orders_items[$i][product_sku]", "product_sku".$i, "text" , $_POST['orders_items'][$i]['product_sku'] ,null, "onChange='duplicateSkuCheck(this)' data-index='".$i."' class='sku'")?></td>
             			<td align="center"><?php echo createField("orders_items[$i][product_qty]", "product_qty".$i, "text" , $_POST['orders_items'][$i]['product_qty'] ,null, "onChange='duplicateSkuCheck(this)' data-index='".$i."' class='qty'")?></td>
             			<td align="center"><?php echo createField("orders_items[$i][avail_qty]", "avail_qty".$i, "text" , '' ,null, " readOnly")?></td>
             			<td align="center" style="width: 180px;" class="to_be_hidden">
             				<?php echo createField("orders_items[$i][product_unit]", "product_unit".$i, "text" , $_POST['orders_items'][$i]['product_unit'] ,null, ((!$_SESSION['order_price_override'])? "readOnly" : "" ) . " data-index='".$i."' class='product_units' onChange='updateOverPrice(this)'"); ?>
             				<?php echo (!$_SESSION['order_price_override'])? '<div class="reqprc"><input data-index-ch="' . $i . '" style="width: 30px;" /><button class="btn" type="button" onclick="requestCPrice(' . $i . ');">Req</button></div>' : ""; ?>
             			</td>

             			<td align="center" class="to_be_hidden">
             				<select name="orders_items[<?php echo $i;?>][product_discount]" id="product_discount<?=$i;?>" onchange="updateOverPrice(this)" data-index='<?=$i;?>'>
             					<option value="0">No Discount</option>
             					<option value="5" <?php if($_POST['orders_items'][$i]['product_discount']=='5') echo 'selected';?>>5%</option>
             					<option value="10" <?php if($_POST['orders_items'][$i]['product_discount']=='10') echo 'selected';?>>10%</option>
             					<option value="15" <?php if($_POST['orders_items'][$i]['product_discount']=='15') echo 'selected';?>>15%</option>
             					<option value="25" <?php if($_POST['orders_items'][$i]['product_discount']=='25') echo 'selected';?>>25%</option>
             					<option value="50" <?php if($_POST['orders_items'][$i]['product_discount']=='50') echo 'selected';?>>50%</option>

             				</select></td>


             				<td align="center" class="to_be_hidden"><?php echo createField("orders_items[$i][product_price]", "product_price".$i, "text" , $_POST['orders_items'][$i]['product_price'] ,null, "readOnly class='productTotal' onchange='productTotal()' data-index='".$i."'")?></td>
             				<td></td>
             			</tr>
             		<?php endfor;?>		 
             	</table>
             	<br />
             	<br />
             	<div  id="aim_div" style="display:none">
             		Charge Card <input type="checkbox" onchange="if(this.checked){$('input[name=saveOrder]').hide(500);$('#aim_table').fadeIn();}else{$('#aim_table').fadeOut();$('input[name=saveOrder]').show(500);}" id="charge_aim" name="charge_aim" /><br /><br />
             		<table border="1" width="60%" cellpadding="5" cellspacing="0" align="center" id="aim_table" style="display:none">
             			<tr>
             				<td>Card Owner:</td>
             				<td><input type="text" name="cc_owner" value="" /></td>
             			</tr>
             			<tr>
             				<td>Card Number:</td>
             				<td><input type="text" name="cc_number" value="" /></td>
             			</tr>
             			<tr>
             				<td>Card Expiry Date:</td>
             				<td><select name="cc_expire_date_month">
             					<?php foreach ($months as $month) { ?>
             					<option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
             					<?php } ?>
             				</select>
             				/
             				<select name="cc_expire_date_year">
             					<?php foreach ($year_expire as $year) { ?>
             					<option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
             					<?php } ?>
             				</select></td>
             			</tr>
             			<tr>
             				<td>Card Security Code (CVV2):</td>
             				<td><input type="text" name="cc_cvv2" value="" size="3" /></td>
             			</tr>
             			<tr>
             				<td align="center" colspan="2"><input type="button" class="button" value="Confirm" onclick="validateOrder();" id="confirm-btn"  /></td>
             			</tr>
             		</table>
             	</div>
             	<br />
             	<div style="width: 990px">
             		<div align="left">
             			
             	<a href="order_create.php" class="button" style="background-color: red; color: white">Cancel Order</a>
             		</div>
             		<br>
             		<div align="center">
             			
             	<input type="submit" name="saveOrder" class="button" style="background-color: green;color: white" value="Create Order"  />
             		</div>
             	</div>
             	<input type="hidden" name="orders[po_payment_source]" id="po_payment_source" />
             	<input type="hidden" name="orders[po_payment_source_detail]" id="po_payment_source_detail" />
             	<input type="hidden" name="orders[po_payment_source_amount]" id="po_payment_source_amount" />
             	<br /><br />
             </form>
         </div>
     </body>
     </html>					
     <script>
     checkState();
     function checkState(){
     		var zone = $('#xstate option:selected').text();
     		if(zone=='Select State') {	
     		}
     		else {
     			getShipping();
     		}

     }
     	function ChangePrice (index, price) {
     		$('#product_unit'+ index).val(price).change();
     		$('input[data-index-ch='+ index +']').val('');
     	}
     	function requestCPrice (index) {
     		var price = $('input[data-index-ch='+ index +']').val();
     		console.log(price);
     		var href = 'priceChangeReq.php?index='+ index +'&price='+ price;
     		$('body').append('<a class="fancybox3 fancybox.iframe" href="'+ href +'" style="display: none;" id="changePriceReq"></a>');
     		$('#changePriceReq').click().remove();
     	} 
     	function duplicateSkuCheck(obj,csv = false) {
     		var index = 0;
     		var check = $(obj).attr('data-index');
     		while(index < 50){
     			if (index != check ) {
     				if($('#product_sku'+index).val() == $('#product_sku'+check).val()){
     					if ($('#product_sku'+check).val() != '') {
     						if (csv) {
     							alert('Error: '+$('#product_sku'+check).val()+' has been added into multiple lines in CSV File. Consolidate same SKUs into 1 line item.');
     						} else {
     							alert('Error: '+$('#product_sku'+check).val()+' has been added into multiple Order line items. Consolidate same SKUs into 1 line item.');
     						}
     						$('#product_sku'+check).val('');
     						$('#product_qty'+check).val('');
     						$('#product_unit'+check).val('');

     						return false;
     					}
     				}
     			}
     			index++;
     		}
     		updatePrice(obj);
     	}

     	function updatePrice(obj,is_unit_price)
     	{
     		var is_unit_price = is_unit_price || -1;
     		var index = $(obj).attr('data-index');	
     		var sku = $('#product_sku'+index).val();
     		var qty = $('#product_qty'+index).val();
     		var req = $('#product_unit'+index).parent().find('.reqprc');
     		var discount = $('#product_discount'+index).val();
     		var total_discount = 0;
     		if(sku=='' || qty==''){ return false;}


     		$.ajax({
     			url: 'ajax_product_price.php',
     			type: 'post',

     			data:{sku:sku,customer_group_id:$('#customer_group_id').val(),qty:qty,store_type:$('#xstore_type').val()},
     			dataType: 'json',		
     			beforeSend: function() {

     			},
     			complete: function() {

     			},				
     			success: function(json) {
     				if (json['error']) {
     					alert(json['error']);

     				}

     				if (json['success']) {
     					if(is_unit_price=='-1')
     					{
     						var unit_price = (json['success']);
     					}
     					else
     					{
     						var unit_price = is_unit_price;
     					}

     					price = parseFloat(unit_price) * parseInt(qty);
     					total_discount = price*parseFloat(discount) / 100;
     					price = price - total_discount;
     					$('#product_unit'+index).val(parseFloat(unit_price).toFixed(2));
     					$('#product_price'+index).val(price.toFixed(2));
     					productTotal();
     					getAvailableQty(obj);
     					if (req) {
     						req.css('display', 'inline-block');
     						$('#product_unit'+index).css('width', '50px');
     					}
     				}
     			}
     		}); 

     	}
     	function populateCustomerAddress(obj)
     	{
     		var address = obj.value;
     		var address = address.split("~");
     		var address1 = address[0];
     		var city = address[1];
     		var state = address[2];
     		var zip = address[3];
     		$('#address1').val(address1);
     		$('#city').val(city);
     		$('#state').val(state);
     		$('#zip').val(zip);

     		$("#xstate option:contains("+state+")").prop('selected',true);
     		getShipping();


     	}
     	function getAvailableQty(obj)
     	{
     		var index = $(obj).attr('data-index');	
     		var sku = $('#product_sku'+index).val();

     		$.ajax({
     			url: 'ajax_product_price.php',
     			type: 'post',

     			data:{sku:sku,action:'getAvailableQty'},
     			dataType: 'json',		
     			beforeSend: function() {

     			},
     			complete: function() {

     			},				
     			success: function(json) {
     				if (json['error']) {

     				}

     				if (json['success']) {
     					returnVal =  json['success'];
     					$('#avail_qty'+index).val(returnVal);
     				}
     			}
     		}); 

     	}




     	function updateOverPrice(obj)
     	{
     		var index = $(obj).attr('data-index');	
     		var sku = $('#product_sku'+index).val();
     		var qty = $('#product_qty'+index).val();
     		var discount = $('#product_discount'+index).val();
     		var total_discount = 0;


     		var unit_price = $('#product_unit'+index).val();
     		price = parseFloat(unit_price) * parseInt(qty);
     		total_discount = price*parseFloat(discount) / 100;
     		price = price - total_discount;
     		$('#product_unit'+index).val(parseFloat(unit_price).toFixed(2));
     		$('#product_price'+index).val(price.toFixed(2));
     		productTotal();

     	}
     	function orderTypeChange()
     	{

     		if($('#order_type').val()=='new')
     		{

     			$('.to_be_hidden').show(500);	

     		}
     		else
     		{
     			$('.to_be_hidden').hide(500);

     		}
     	}
     	function getTax (code) {
     		var email = $('#email').val();
     		$.ajax({
     			url: 'order_create.php?email='+email,
     			type: 'post',

     			data:{zone: code, action: 'getTax'},
     			dataType: 'json',		
     			beforeSend: function() {

     			},
     			complete: function() {

     			},				
     			success: function(json) {
     				if (json['error']) {

     				}

     				if (json['success']) {
     					$('#taxX').val(json['tax']);
     				}
     			}
     		}); 
     	}
     	function showAddresses (email) {
     		
     		$.ajax({
     			url: 'order_create.php',
     			type: 'get',

     			data:{email: email, action: 'showAddresses'},
     			dataType: 'json',		
     			beforeSend: function() {

     			},
     			complete: function() {

     			},				
     			success: function(json) {
     				//alert('here');

     				if (json['success']) {
     					$('#customer_addresses').html(json['html']);
     				}
     			}
     		}); 
     	}

     	function countryChange() {
     		var country_id = $('#xcountry').val();
     		var country = $('#xcountry option:selected').text();
     		$('#country').val(country);
     		$.ajax({
     			url: 'order_create.php',
     			type: 'POST',
     			dataType: 'json',
     			data: {country_id: country_id, action: 'getStates'},
     		})
     		.always(function(data) {
     			$('#xstate').html(data['s']);
     			$('#state').val('');
     			$('#select_shipping').html('<option value="">Select Shipping Method</option>');
     			shippingCost();
     		});
     	}
     	function getShipping() {
     		var zone_id = $('#xstate').val();
     		var zone = $('#xstate option:selected').text();
     		$('#state').val(zone);
     		if(zone=='') {
     			return false;	
     		}
     		else {
     			getTax(zone);
     			$.ajax({
     				url: 'https://phonepartsusa.com/index.php?route=checkout/manual/shipping_method_for_imp',

     				type: 'post',
     				crossDomain: true,
    dataType: 'jsonp',

     				data:{zone:encodeURIComponent(zone),sub_total:$('.subTotalX').find('b').text()},
     				dataType: 'json',		
     				beforeSend: function() {

     				},
     				complete: function() {

     				},				
     				success: function(json) {
     					if (json['error']) {
     						//alert(json['error']);
     						$('#select_shipping').html('<option value="">Select Shipping Method</option><option value="0.00-Custom Shipping">Custom Shipping</option><option value="0.00-Customer FedEx">Customer FedEx</option><option value="0.00-Customer UPS">Customer UPS</option>');
     						shippingCost();
     						return false;
     					}



     					if (json['shipping_method']) {

     						html='<optgroup label="Custom Shippings">' +
     						'<option value="0.00-Free Shipping">Free Shipping</option>' +
     						<?php if ($_SESSION['shipment_custom_shipping']) { ?>
     							'<option value="0.00-Custom Shipping">Custom Shipping</option>' +
     							<?php } ?>
     							'<option value="0.00-Customer FedEx">Customer FedEx</option>' +
     							'<option value="0.00-Customer UPS">Customer UPS</option>';
     							for (i in json['shipping_method']) {
     								html += '<optgroup label="' + json['shipping_method'][i]['title'] + '">';

     								if (!json['shipping_method'][i]['error']) {
     									for (j in json['shipping_method'][i]['quote']) {

     										html += '<option value="' + json['shipping_method'][i]['quote'][j]['cost'] + '-'+json['shipping_method'][i]['quote'][j]['code']+'">' + json['shipping_method'][i]['quote'][j]['title'] + '</option>';

     									}		
     								} else {
     									html += '<option value="" style="color: #F00;" disabled="disabled">' + json['shipping_method'][i]['error'] + '</option>';
     								}

     								html += '</optgroup>';
     							}

     							$('#select_shipping').html(html);	

     							shippingCost();

     						}

     					}
     				}); 	

     		}


     	}
     	function allowNum (t) {
     		var input = $(t).val();
     		var valid = input.substring(0, input.length - 1);
     		if (isNaN(input)) {
     			if (!valid) {
     				valid = 0;
     			}
     			$(t).val(valid);
     		}
     	}
     	function shippingCustom() {
     		if ($('#select_shipping option:selected').text()=='Custom Shipping') {
     			$('.shippingCostX').find('b').text($('#shipping_cost').val());
     		}
     		makeTotal();
     	}
     	function shippingCost()
     	{
     		var obj = document.getElementById('select_shipping').value;
     		if (obj) {
     			$('#shipping_method').attr('value', $('#select_shipping option:selected').text());
     		} else {
     			$('#shipping_method').attr('value', '');
     		}
     		$('#shipping_method').attr('type', 'hidden');
     		$('#shipping_cost').attr('readOnly', '');
     		$('#shipping_cost').removeAttr('onchange');
     		if($('#shipping_method').val()=='Customer FedEx' || $('#shipping_method').val()=='Customer UPS')
     		{
     			$('.customer_fedex_code').show();
     		} else if ($('#select_shipping option:selected').text()=='Custom Shipping') {
     			$('#shipping_method').val('');
     			$('#shipping_method').attr('type', 'text');
     			$('#shipping_method').attr('required', 'required');
     			$('#shipping_cost').attr('required', 'required');
     			$('#shipping_cost').attr('onchange', 'shippingCustom()');
     			$('#shipping_cost').removeAttr('readOnly');
     			$('.customer_fedex_code').hide();
     		} else {
     			$('.customer_fedex_code').hide();
     		}
     		var shipping = obj.split("-");
     		$('#shipping_cost').val(shipping[0]);
     		$('.shippingCostX').find('b').text(shipping[0]);
     		makeTotal();
     		$('#shipping_code').attr('value', shipping[1]);	
     	}
     	<?php
     	$kk = 0;
     	foreach($_SESSION['cart'] as $cart_temp => $cart)
     	{
     		?>
     		if(!document.getElementById('product_sku<?php echo $kk;?>'))
     		{
     			addRow();
     		}

     		document.getElementById('product_sku<?php echo $kk;?>').value = '<?php echo $cart['sku'];?>';
     		document.getElementById('product_qty<?php echo $kk;?>').value = '<?php echo (float)$cart['qty'];?>';
     		// alert(parent.document.getElementById('product_sku<?php echo $kk;?>').value);
     		
     		<?php
     		if(isset($_GET['hide_header']))
     		{
     			?>
     			updatePrice(document.getElementById('product_sku<?php echo $kk;?>'));
     		updateOverPrice(document.getElementById('product_unit<?php echo $kk;?>'));
     			<?php
     		}
     		else
     		{


     		?>
     		updatePrice(parent.document.getElementById('product_sku<?php echo $kk;?>'));
     		updateOverPrice(parent.document.getElementById('product_unit<?php echo $kk;?>'));

     		<?php

     	}
     	?>
     		<?php
     		$kk++;
     	}
     	?>

     </script>
     <?php 
     if ($_POST['customer_cart']) { ?> 
     <script type="text/javascript">
     	getShipping();
     	<?php
     	$kk = 0;
     	foreach($_POST['items'] as $key => $item){ ?>
     		updatePrice(parent.document.getElementById('product_sku<?php echo $kk;?>'));
     		updateOverPrice(parent.document.getElementById('product_unit<?php echo $kk;?>'));
     		<?php $kk++; } ?>
     	//showAddresses(email);
     </script>
     <?php }
      ?>