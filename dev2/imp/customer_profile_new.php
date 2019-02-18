<?php
require_once("auth.php");
include_once 'inc/functions.php';
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
$phTypes = array ('', 'Office Mobile', 'Personal Mobile', 'Landline', 'Office');
function date_compare($a, $b)
{
	$t1 = strtotime($a['order_date']);
	$t2 = strtotime($b['order_date']);
	return $t1 - $t2;
}
function generateRandomString($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz=()@-+/';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


$id =  $db->func_escape_string($_GET['id']);
if (!$id) {
	$id =  $db->func_escape_string($_GET['customer_id']);
}
if(isset($_GET['email']) and $_GET['email']!='')
{
	$customer_email = $db->func_escape_string(base64_decode($_GET['email']));
}
else
{
	$id = explode("-",$id);
	if($id[0]=='PPC')
	{
		$customer_email = $db->func_query_first_cell("SELECT email FROM inv_customers WHERE id='".$id[1]."'");
		$business_license = $db->func_query_first("SELECT business_license,business_date_updated,business_license_user FROM inv_customers WHERE id='".$id[1]."'");
		if (!$business_license['business_license']) {
			$business_license = $db->func_query_first("SELECT business_license, date_added as business_date_updated FROM oc_wholesale_account WHERE personal_email='".$customer_email."' AND business_license <> ''");	
		}
		$tax_license = $db->func_query_first("SELECT tax_license,tax_date_updated,tax_license_user FROM inv_customers WHERE id='".$id[1]."'");
	}
	else if($id[0]=='POC')
	{
		$customer_email = $db->func_query_first_cell("SELECT email FROM inv_po_customers WHERE id='".$id[1]."'");
		$business_license = $db->func_query_first("SELECT business_license,business_date_updated,business_license_user FROM inv_po_customers WHERE id='".$id[1]."'");
		if (!$business_license['business_license']) {
			$business_license = $db->func_query_first("SELECT business_license, date_added as business_date_updated FROM oc_wholesale_account WHERE personal_email='".$customer_email."' AND business_license <> ''");
		}
		$tax_license = $db->func_query_first("SELECT tax_license,tax_date_updated,tax_license_user FROM inv_po_customers WHERE id='".$id[1]."'");
	}
	else
	{
		$customer_email = $db->func_query_first_cell("SELECT email FROM oc_buyback WHERE buyback_id='".$id[1]."'");
		$business_license = $db->func_query_first("SELECT business_license,business_date_updated,business_license_user FROM oc_buyback WHERE buyback_id='".$id[1]."'");
		if (!$business_license['business_license']) {
			$business_license = $db->func_query_first("SELECT business_license, date_added as business_date_updated FROM oc_wholesale_account WHERE personal_email='".$customer_email."' AND business_license <> ''");
		}
		$tax_license = $db->func_query_first("SELECT tax_license,tax_date_updated,tax_license_user FROM oc_buyback WHERE buyback_id='".$id[1]."'");
	}
}
if($_POST['action']=='updateAdded') {
	if ($_POST['date']) {
		$db->db_exec("UPDATE inv_customers SET date_added='". $db->func_escape_string($_POST['date']) ."' WHERE email='".$customer_email."'");
		$db->db_exec("UPDATE oc_customer SET date_added='". $db->func_escape_string($_POST['date']) ."' WHERE email='".$customer_email."'");
		echo json_encode(array('msg' => 'Date Updated'));
		exit;
	} else {
		echo json_encode(array('msg' => 'Error Try Again'));
		exit;
	}
}
if($_POST['action']=='apply_voucher')
{
	$code = $db->func_escape_string($_POST['code']);
	$balance = (float)$_POST['balance'];
	$json = array();
	$code_check = $db->func_query_first("SELECT to_email,voucher_id,code,amount FROM oc_voucher WHERE status=1 AND code='".$code."'");
	if(!$code_check)
	{
		$json['error'] = 'Invalid voucher code or disabled voucher, please check again';
	}
	else
	{
		$voucher_balance = ((float) $code_check['amount']) + ((float) $db->func_query_first_cell("SELECT SUM(amount) FROM oc_voucher_history WHERE voucher_id='".$code_check['voucher_id']."'"));
		if($voucher_balance<=0)
		{
			$json['error'] = "No Balance in your voucher, try with different one.";
		}
		else
		{
			//$orders = $db->func_query("SELECT a.* FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and a.store_type IN ('web','po_business') and a.email='$customer_email' AND a.order_price<>a.paid_price and LOWER(a.order_status) IN('processed','unshipped')");
			// $used = 0.00;
			// foreach($orders as $order)
			// {
			// 	if (preg_match("/^cash (.*)/i", strtolower($order['payment_method'])) == 0 and $order['store_type']=='web')
			// 	{
			// 		continue;
			// 	}
			// 	$subtotalTotal = $db->func_query_first_cell('SELECT SUM(`product_price`) FROM `inv_orders_items` WHERE `order_id` = "' . $order['order_id'] . '"');
			// 	$_tax = (float)$db->func_query_first_cell('SELECT SUM(`value`) FROM `oc_order_total` WHERE `order_id` = "'. $order['order_id'] .'" AND `code` = "tax"');
			// 	$order_total = (float)($subtotalTotal + $order['shipping_cost'] + $_tax) - $order['paid_price'];	
			// 	if($order_total>0.00 and $voucher_balance>0.00)
			// 	{
			// 		if($order_total > $voucher_balance)
			// 		{
			// 			$used = $voucher_balance;
			// 		}
			// 		else if($order_total<=$voucher_balance)
			// 		{
			// 			$used = $voucher_balance - $order_total;
			// 			$used = $voucher_balance - $used;
			// 		}
			// 		$voucher_balance = $voucher_balance - $used;
			// 		$xdata = array();
			// 		$xdata['voucher_id']=$code_check['voucher_id'];
			// 		if($order['store_type']=='web')
			// 		{
			// 			$xdata['order_id']=$order['order_id'];
			// 			$xdata['inv_order_id']=0;
			// 		}
			// 		else
			// 		{
			// 			$xdata['inv_order_id']=$order['order_id'];
			// 			$xdata['order_id']=0;
			// 		}
			// 		$xdata['customer_email']=$customer_email;
			// 		$xdata['amount'] = $used*(-1);
			// 		$xdata['date_added'] = date('Y-m-d H:i:s');
			// 		$xdata['manual'] = 1;
			// 		$db->func_array2insert("oc_voucher_history",$xdata);
			// 		$db->db_exec("UPDATE inv_orders SET paid_price=paid_price + $used WHERE order_id='".$order['order_id']."'");
			// 	}
			// }
			
			if($balance > $voucher_balance)
			{
				$used = $voucher_balance;
			}
			else if($balance<=$voucher_balance)
			{
				$used = $voucher_balance - $balance;
				$used = $voucher_balance - $used;
			}
			$voucher_balance = $voucher_balance - $used;
			$balance = $balance - $used;
			$xdata = array();
			$xdata['voucher_id']=$code_check['voucher_id'];
			$xdata['inv_order_id']=0;
			$xdata['order_id']=0;
			$xdata['customer_email']=$customer_email;
			$xdata['amount'] = $used*(-1);
			$xdata['date_added'] = date('Y-m-d H:i:s');
			$xdata['manual'] = 1;
			$db->func_array2insert("oc_voucher_history",$xdata);
			$json['success'] = 'Voucher Applied successfully';
			$json['balance'] = $balance;
		}
	}
	echo json_encode($json);exit;
}
//$customer_email = $db->func_escape_string($_GET['id']);
if($_POST['action']=='update_term_customer')
{
	$is_termed = $_POST['is_termed'];
	
	if($is_termed=='true') $is_termed = 1; else $is_termed = 0;
	$db->db_exec("UPDATE oc_customer SET is_termed='".$is_termed."' WHERE email='".$customer_email."' ");	
	echo $is_termed;
	exit;
}
if($_POST['action']=='disableTax')
{
	$dis_tax = $_POST['dis_tax'];
	
	if($dis_tax=='true') $dis_tax = 1; else $dis_tax = 0;
	$db->db_exec("UPDATE oc_customer SET dis_tax='".$dis_tax."' WHERE email='".$customer_email."' ");
	$db->db_exec("UPDATE inv_customers SET dis_tax='".$dis_tax."' WHERE email='".$customer_email."' ");	
	echo $dis_tax;
	exit;
}
if($_POST['action']=='changeDefaultAddress')
{
	$status = $_POST['status'];
	
	if($status=='true') $address_id = $_POST['address_id']; else $address_id = '';
	$db->db_exec("UPDATE oc_customer SET address_id='".$address_id."' WHERE email='".$customer_email."' ");	
	echo $status;
	exit;
}
if($_POST['action']=='updateSalesAgent')
{
    
	$user_id = (int)$_POST['user_id'];
	
	$check_user = $db->func_query_first_cell("SELECT user_id from inv_customers where email='".$customer_email."'");
	$date_time = date('Y-m-d H:i:s');
	if($check_user!=$user_id)
	{
		$db->db_exec("UPDATE inv_customers SET sales_assigned_date='".$date_time."' WHERE email='".$customer_email."' ");	
	}
	$update = $db->db_exec("UPDATE inv_customers SET user_id='".$user_id."' WHERE email='".$customer_email."' ");	
	$data_sales_log = array(
		'assigned_by' => $_SESSION['user_id'],
		'sales_agent' => $user_id,
		'customer_email' => $customer_email,
		'date_added' => date('Y-m-d H:i:s')
		);	
	$db->func_array2insert ( 'inv_customer_sales_agent_log', $data_sales_log );
	echo americanDate($date_time);
	exit;
}
if($_POST['action']=='updateCustomerDataIMP')
{
	$field = $_POST['field'];
	$value = $_POST['value'];
	foreach (explode(',', $_POST['update']) as $utable) {
		$db->db_exec("UPDATE $utable SET $field='".$value."' WHERE email='".$customer_email."' ");
	}
	echo 1;
	exit;
}
if($_POST['action']=='updateCompany')
{
	$company = $db->func_escape_string($_POST['company']);

	$_detail = $db->func_query_first("SELECT * FROM inv_customers WHERE email='".$customer_email."'");
	
	$business_id = generateRandomString(5);


		$check = $db->func_query_first("SELECT * FROM inv_customers WHERE business_id='".$company."' and business_id<>''");

		if($check)
		{

				if($_detail['business_id']!='')
				{

				$db->db_exec("UPDATE inv_customers SET company='".$check['company']."',business_id='".$check['business_id']."' WHERE business_id='".$_detail['business_id']."'  ");
				}
				else
				{
						$db->db_exec("UPDATE inv_customers SET company='".$check['company']."',business_id='".$check['business_id']."' WHERE email='".$customer_email."'  ");
				}

		}
		else
		{

	

	// $business_id = $db->func_query_first_cell("SELECT business_id FROM inv_customers where email<>'".$customer_email."' and company='".$company."'");
	// if($business_id)
	// {
	// 		$db->db_exec("UPDATE inv_customers WHERE business_id='".$business_id."' WHERE email='".$customer_email."'");
	// }

		$db->db_exec("UPDATE inv_customers SET company='".$company."' WHERE email='".$customer_email."'"); // // update company name
			if($_detail['business_id']!='')
			{
				$db->db_exec("UPDATE inv_customers SET company='".$company."' WHERE business_id='".$_detail['business_id']."'"); // // update company name
			}
			else
			{
					$db->db_exec("UPDATE inv_customers SET business_id='".$business_id."' WHERE email='".$customer_email."'"); // // update company name	
			}

		}
			
	echo json_encode(array(1));exit;



}
if($_POST['action']=='update_whitelist_customer') {
	$white_list = $_POST['white_list'];
	
	if($white_list=='true') $white_list = 1; else $white_list = 0;
	$db->db_exec("UPDATE inv_customers SET white_list='".$white_list."' WHERE email='".$customer_email."' ");
	
	$array['type'] = 'customer';
	$array['user'] = $_SESSION['user_id'];
	$array['details'] = $customer_email;
	$array['date_added'] = date('Y-m-d H:i:s');
	$db->func_array2insert("inv_whitelist_history", $array);
	unset($array);
	echo $is_termed;
	exit;
	
}
if($_POST['action']=='update_is_internal') {
	$is_internal = $_POST['is_internal'];
	
	if($is_internal=='true') $is_internal = 1; else $is_internal = 0;
	$db->db_exec("UPDATE inv_customers SET is_internal='".$is_internal."' WHERE email='".$customer_email."' ");
	
	exit;
	
}
if($_POST['action']=='update_special_customer') {
	$is_special_customer = $_POST['is_special_customer'];
	$discount = $_POST['special_discount'];
	if($is_special_customer=='true') $is_special_customer = 1; else $is_special_customer = 0;
	if($is_special_customer==0)
	{
		$discount = 0.00;	
	}
	$db->db_exec("UPDATE inv_customers SET is_special_customer='".$is_special_customer."',special_discount_per='".(float)$discount."' WHERE email='".$customer_email."' ");	
	echo $is_special_customer;
	exit;
	
}
$customer_details = $db->func_query_first("select c.* , cg.name , count(order_id) as total , sum(total) as price
	from oc_customer c inner join oc_customer_group cg on (c.customer_group_id = cg.customer_group_id) 
	left join oc_order o on (o.customer_id = c.customer_id and o.order_status_id in (15 , 24 , 21 , 3)) 
	where c.email = '$customer_email'");
if ($customer_details['email']) {
	$address_type = 'oc';

	$customer_address = $db->func_query_first("SELECT * FROM `oc_address` WHERE address_id='".(int)$customer_details['address_id']."' AND `customer_id` = '".$customer_details['customer_id']."' order by update_date desc");
	$address_id = $customer_address['address_id'];
	$state = $db->func_query_first_cell ("SELECT name FROM oc_zone WHERE zone_id ='".$customer_address['zone_id']."'");
	$country = $db->func_query_first_cell ("SELECT name FROM oc_country WHERE country_id ='".$customer_address['country_id']."'");
	$customer_address['state'] = $state;
	$customer_address['country'] = $country;
}

if (!$customer_details['email']) {
	$customer_details = $db->func_query_first("SELECT * FROM `inv_po_customers` WHERE `email` = '$customer_email'");
	$customer_details['name'] = 'PO Business';
	$customer_details['type'] = 'po';
	$customer_details['customer_id'] = 0;
	$customer_details['is_termed'] = 0;
	if ($customer_details['email']) {
	$address_type = 'po';
	$address_id = $customer_details['id'];
	$customer_address = array();
	$customer_address['firstname']= $customer_details['contact_name'];
	//$customer_address['lastname'] = $customer_details['contact_name'];
	$customer_address['company'] = $customer_details['company_name'];
	$customer_address['address_1']= $customer_details['address1'];
	$customer_address['address_2'] = $customer_details['address2'];
	$customer_address['city'] = $customer_details['city'];
	$customer_address['postcode'] = $customer_details['zip'];
	$customer_address['state'] = $customer_details['state'];
	}

}
if (!$customer_details['email']) {
	$customer_details = $db->func_query_first("SELECT * FROM `inv_customers` WHERE email = '$customer_email'");
	$customer_details['name'] = 'Guest';
	$customer_details['type'] = 'guest';
	$customer_details['customer_id'] = 0;
	$customer_details['is_termed'] = 0;	
	if($customer_details['email']) {
	$address_type = 'inv';
	$address_id = $customer_details['id'];
	$customer_address = array();
	$customer_address['firstname']= $customer_details['firstname'];
	$customer_address['lastname'] = $customer_details['lastname'];
	$customer_address['company'] = $customer_details['company'];
	$customer_address['address_1']= $customer_details['address1'];
	$customer_address['address_2'] = $customer_details['address2'];
	$customer_address['city'] = $customer_details['city'];
	$customer_address['postcode'] = $customer_details['zip'];
	$customer_address['state'] = $customer_details['state'];
	$customer_address['country'] = $customer_details['country'];
	}
}
if (!$customer_details['email']) {
	$customer_details = $db->func_query_first("SELECT * FROM `oc_buyback` WHERE email = '$customer_email' limit 0,1 ");
	$customer_details['name'] = 'LBB';
	$customer_details['type'] = 'lbb';
	$customer_details['customer_id'] = 0;
	$customer_details['is_termed'] = 0;
	if ($customer_details['email']) {
	$address_type = 'lbb';
	$address_id = $customer_details['buyback_id'];
	$customer_address = array();
	$customer_address['firstname']= $customer_details['firstname'];
	$customer_address['lastname'] = $customer_details['lastname'];
	$customer_address['company'] = $customer_details['company'];
	$customer_address['address_1']= $customer_details['address_1'];
	$customer_address['city'] = $customer_details['city'];
	$customer_address['postcode'] = $customer_details['postcode'];
	$state = $db->func_query_first_cell ("SELECT name FROM oc_zone WHERE lower(zone_id)  LIKE '%".strtolower($customer_details['zone_id'])."%'");
	$customer_address['state'] = $state;
	}
}
if (!$customer_details['email']) {
	$customer_details = $db->func_query_first("SELECT * FROM `oc_order` WHERE email = '$customer_email' limit 0,1 ");
	$customer_details['name'] = 'Guest';
	$customer_details['type'] = 'guest';
	//$customer_details['customer_id'] = 0;
	$customer_details['is_termed'] = 0;
	if ($customer_details['email']) {
	$address_type = 'oc_order';
	$address_id = $customer_details['order_id'];
	$customer_address = array();
	$customer_address['firstname']= $customer_details['shipping_firstname'];
	$customer_address['lastname'] = $customer_details['shipping_lastname'];
	$customer_address['company'] = $customer_details['shipping_company'];
	$customer_address['address_1']= $customer_details['shipping_address_1'];
	$customer_address['address_2']= $customer_details['shipping_address_2'];
	$customer_address['city'] = $customer_details['shipping_city'];
	$customer_address['postcode'] = $customer_details['shipping_postcode'];
	$customer_address['state'] = $customer_details['shipping_zone'];
	$customer_address['country'] = $customer_details['shipping_country'];
/*	$customer_address_billing = array();
	$customer_address_billing['firstname']= $customer_details['payment_firstname'];
	$customer_address_billing['lastname'] = $customer_details['payment_lastname'];
	$customer_address_billing['company'] = $customer_details['payment_company'];
	$customer_address_billing['address_1']= $customer_details['payment_address_1'];
	$customer_address_billing['address_2']= $customer_details['payment_address_2'];
	$customer_address_billing['city'] = $customer_details['payment_city'];
	$customer_address_billing['postcode'] = $customer_details['payment_postcode'];
	$customer_address_billing['state'] = $customer_details['payment_zone'];
	$customer_address_billing['country'] = $customer_details['payment_country']*/;
	}
}
if (!$customer_details['email']) {
	$customer_details = $db->func_query_first("SELECT a.*,b.*,b.first_name as firstname,b.last_name as lastname FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and a.email = '$customer_email' limit 0,1 ");
	$customer_details['name'] = 'Guest';
	$customer_details['type'] = 'guest';
	//$customer_details['customer_id'] = 0;
	$customer_details['is_termed'] = 0;
	if ($customer_details['email']) {
	$address_type = 'inv_order';
	$address_id = $customer_details['order_id'];
	$customer_address = array();
	$customer_address['firstname']= $customer_details['shipping_firstname'];
	$customer_address['lastname'] = $customer_details['shipping_lastname'];
	$customer_address['company'] = $customer_details['shipping_company'];
	$customer_address['address_1']= $customer_details['address1'];
	$customer_address['address_2']= $customer_details['address2'];
	$customer_address['city'] = $customer_details['city'];
	$customer_address['postcode'] = $customer_details['zip'];
	$customer_address['state'] = $customer_details['state'];
	$customer_address['country'] = $customer_details['country'];
/*	$customer_address_billing = array();
	$customer_address_billing['firstname']= $customer_details['bill_firstname'];
	$customer_address_billing['lastname'] = $customer_details['bill_lastname'];
	$customer_address_billing['company'] = $customer_details['billing_company'];
	$customer_address_billing['address_1']= $customer_details['bill_address_1'];
	$customer_address_billing['address_2']= $customer_details['bill_address_2'];
	$customer_address_billing['city'] = $customer_details['bill_city'];
	$customer_address_billing['postcode'] = $customer_details['bill_zip'];
	$customer_address_billing['state'] = $customer_details['bill_state'];
	$customer_address_billing['country'] = $customer_details['bill_country'];*/
	}
}
$_temp_detail = $db->func_query_first("SELECT * FROM inv_customers where email='".$customer_details['email']."' limit 1");
$customer_details['company'] = $_temp_detail['company'];
$customer_details['business_id'] = $_temp_detail['business_id'];
$customer_details['parent_id'] = $_temp_detail['parent_id'];

if($customer_details['parent_id']==0)
{
	$contacts = $db->func_query("SELECT * FROM inv_customers WHERE parent_id='".$_temp_detail['id']."'");
}
else
{
	// echo "SELECT * FROM inv_customers WHERE parent_id='".$_temp_detail['id']."' or id='".$_temp_detail['id']."' and email<>'".$customer_details['email']."'";exit;
	$contacts = $db->func_query("SELECT * FROM inv_customers WHERE (parent_id='".$_temp_detail['parent_id']."' or id='".$_temp_detail['parent_id']."') and email<>'".$customer_details['email']."' GROUP BY email");

}


$customer_address_billing = $db->func_query_first("SELECT * FROM `oc_order` WHERE email = '$customer_email' limit 0,1 ");
$bill_address_id = $customer_address_billing['order_id'];
//echo "<pre>"; print_r($customer_address_billing); exit;

if ($customer_details['customer_id'] == 0) {
	$query = "SELECT 
	`order_id`,
	`order_date` 
	FROM
	`inv_orders` 
	WHERE `email` = '$customer_email' 
	AND `order_status` IN (
	'Shipped',
	'On Hold',
	'Processed',
	'Store Pick Up',
	'Awaiting Fulfillment'
	) 
	ORDER BY `order_date` DESC";
	$order_data = $db->func_query($query);
	$order_idlist = array();
	foreach ($order_data as $order_ids) {
		$order_idlist[] = $order_ids['order_id'];
	}
	$customer_details['date_added'] = ($customer_details['date_added'])? $customer_details['date_added']:$order_data[0]['order_date'];
	$customer_details['total'] = count($order_idlist);
	$customer_details['is_termed'] = 0;
		//echo "<pre>"; print_r($customer_details); exit;
}

$customer_details['customer_mood'] = $db->func_query_first_cell("SELECT customer_mood FROM inv_customers WHERE email='".$customer_details['email']."'");

// $contacts = array();
// if($customer_details['business_id'] && $customer_details['business_id']!='')
// {
// $contacts = $db->func_query("SELECT * FROM inv_customers WHERE business_id='".$customer_details['business_id']."' and business_id<>''");
// }

/*if ($_POST['sendEmailPassword']) {
	$emailMessage['number']['title'] = 'Password';
	$emailMessage['number']['value'] = $_POST['password'];
	$emailMessage['image'] = $host_path . 'images/passwordreset.png' ;
	$emailMessage['message'] = 'Dear <b>'. $customer_details['firstname'] . ' ' . $customer_details['lastname'] .'</b> <br> A new password was requested from PhonePartsUSA.com <br>You can also change your password into the Account section by visiting our site!<br><br><br>Regards
PhonePartsUSA.com LLC <br><br>' ;
	$emailMessage['subject'] = $customer_details['firstname'] . ' ' . $customer_details['lastname'] .' Your password has changed' ;
	$emailMessage['title'] = 'New Password' ;
	$dataMessage['email'] = $customer_details['email'];
	$dataMessage['customer_name'] = $customer_details['firstname'] . ' ' . $customer_details['lastname'];
	if (sendEmailDetails ($dataMessage, $emailMessage)) {
		unset($_SESSION['message']);
		echo json_encode(array('message' => 'Email Sent to Customer'));
		exit;
	}
	echo json_encode(array('message' => 'Error Try Again'));
	exit;
}*/
if ($_POST['sendEmailPassword']) {
	$email = $db->func_escape_string($_POST['email']);
	$name = $db->func_escape_string($_POST['name']);
	$password = substr(md5(mt_rand()), 0, 10);
	$db->func_query("UPDATE oc_customer SET password = '" . $db->func_escape_string(md5($password)) . "' WHERE email = '" . $email . "'");
	$canned_mail = $db->func_query_first("Select * from inv_canned_message where type = 'Password Reset' ");
	$dataRep = array();
	$dataRep['customer_name'] = $name;
	$dataRep['email'] = $email;
	$canned_mail['subject'] = shortCodeReplace($dataRep, $canned_mail['subject']);
	$canned_mail['title'] = shortCodeReplace($dataRep, $canned_mail['title']);
	$canned_mail['message'] = shortCodeReplace($dataRep, $canned_mail['message']);
	$emailMessage['number']['title'] = 'Password';
	$emailMessage['number']['value'] = $password;
	$emailMessage['image'] = $host_path . 'images/passwordreset.png' ;
	$emailMessage['message'] = $canned_mail['message'];
	$emailMessage['subject'] = $canned_mail['subject'];
	$emailMessage['title'] = $canned_mail['title'];
	$dataMessage['email'] = $email;
	$dataMessage['customer_name'] = $name;
	if (sendEmailDetails ($dataMessage, $emailMessage)) {
		unset($_SESSION['message']);
		echo json_encode(array('message' => 'An email containing a temporary password has been sent to the customer. Tell the customer to login using this password and change it.'));
		exit;
	} else {
		echo json_encode(array('message' => 'Email Not Sent.'));
		exit;
	}
	
}
// $contacts = $db->func_query("SELECT * FROM `inv_customer_contacts` WHERE customer_id = '". $customer_email ."'");
// foreach ($contacts as $key => $contact) {
// 	$contacts[$key]['contacts'] = $db->func_query("SELECT * FROM `inv_contacts_ph` WHERE customer_contact_id = '". $contact['id'] ."'");
// }
$customer_details['is_internal'] = $db->func_query_first_cell("SELECT is_internal FROM inv_customers WHERE email='".$customer_email."'");
$customer_details['white_list'] = $db->func_query_first_cell("SELECT white_list FROM inv_customers WHERE email='".$customer_email."'");
$customer_details['user_id'] = $db->func_query_first_cell("SELECT user_id FROM inv_customers WHERE email='".$customer_email."'");
$customer_details['is_special_customer'] = $db->func_query_first_cell("SELECT is_special_customer FROM inv_customers WHERE email='".$customer_email."'");
$customer_details['special_discount_per'] = $db->func_query_first_cell("SELECT special_discount_per FROM inv_customers WHERE email='".$customer_email."'");
$customer_details['price'] = $db->func_query_first_cell("SELECT SUM(`order_price`) FROM `inv_orders` where  email='".$customer_email."'");
if($customer_details['price']==0.00)
{
	$customer_details['price'] = $db->func_query_first_cell("SELECT SUM(`paid_price`) FROM `inv_orders` where  email='".$customer_email."'");
}
$customer_id = $customer_details['customer_id'];
if ($_POST['delete_shipping_address']) {
	$add_id = $_POST['ship_address_id'];
	if ($_POST['ship_address_type'] == 'oc') {
		$db->db_exec("DELETE FROM oc_address WHERE address_id='".(int) $add_id."'");
		$_SESSION['message'] = 'Address Deleted successfully';
		header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
		exit;
	}
}
if ($_POST['add_shipping_address']) {
	$insert = array();
	$insert['firstname'] = $_POST['shipping_firstname'];
	$insert['lastname'] = $_POST['shipping_lastname'];
	$insert['address_1'] = $_POST['shipping_address1'];
	$insert['address_2'] = $_POST['shipping_address2'];
	$insert['city'] = $_POST['shipping_city'];
	$insert['company'] = $_POST['shipping_company'];
	$insert['postcode'] = $_POST['shipping_zip'];
	$insert['insert_date'] = date('Y-m-d H:i:s');
	$insert['customer_id'] = $customer_details['customer_id'];
	$state = $db->func_query_first_cell ("SELECT zone_id FROM oc_zone WHERE lower(name)  LIKE '%".strtolower($_POST['shipping_state'])."%'");
	$country = $db->func_query_first_cell ("SELECT country_id FROM oc_country WHERE lower(name)  LIKE '%".strtolower($_POST['shipping_country'])."%'");
	$insert['zone_id'] = $state;
	$insert['country_id'] = $country;
	$db->func_array2insert("oc_address",$insert);
	$_SESSION['message'] = 'Address Added successfully';
	header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
	exit;
}
if ($_POST['update_shipping_address']) {
	if ($_POST['ship_address_type'] == 'oc') {
		$oc_id = $_POST['ship_address_id'];
		$update_array = array ();
		$update_array['address_1'] = $_POST['shipping_address1'];
		$update_array['address_2'] = $_POST['shipping_address2'];
		$update_array['city'] = $_POST['shipping_city'];
		$update_array['company'] = $_POST['shipping_company'];
		$update_array['postcode'] = $_POST['shipping_zip'];
		$state = $db->func_query_first_cell ("SELECT zone_id FROM oc_zone WHERE lower(name)  LIKE '%".strtolower($_POST['shipping_state'])."%'");
		$country = $db->func_query_first_cell ("SELECT country_id FROM oc_country WHERE lower(name)  LIKE '%".strtolower($_POST['shipping_country'])."%'");
		$update_array['zone_id'] = $state;
		$update_array['country_id'] = $country;
		$db->func_array2update("oc_address",$update_array,"address_id = '$oc_id'");
		$_SESSION['message'] = 'Address Changed';
		header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
		exit;
	} else if ($_POST['ship_address_type'] == 'po') {
		$po_id = $_POST['ship_address_id'];
		$update_array = array ();
		$update_array['address1'] = $_POST['shipping_address1'];
		$update_array['address2'] = $_POST['shipping_address2'];
		$update_array['company_name'] = $_POST['shipping_company'];
		$update_array['city'] = $_POST['shipping_city'];
		$update_array['zip'] = $_POST['shipping_zip'];
		$update_array['state'] = $_POST['shipping_state'];
		$db->func_array2update("inv_po_customers",$update_array,"id = '$po_id'");
		$_SESSION['message'] = 'Address Changed';
		header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
		exit;
	} else if ($_POST['ship_address_type'] == 'inv') {
		$add_id = $_POST['ship_address_id'];
		$update_array['company'] = $_POST['shipping_company'];
		$update_array['address1']= $_POST['shipping_address1'];
		$update_array['address2'] = $_POST['shipping_address2'];
		$update_array['city'] = $_POST['shipping_city'];
		$update_array['zip'] = $_POST['shipping_zip'];
		$update_array['state'] = $_POST['shipping_state'];
		$update_array['country'] = $_POST['shipping_country'];
		$db->func_array2update("inv_customers",$update_array,"id = '$add_id'");
		$_SESSION['message'] = 'Address Changed';
		header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
		exit;
	} else if ($_POST['ship_address_type'] == 'lbb') {
		$lbb_shipment = $_POST['ship_address_id'];
		$update_array = array ();
		$update_array['address_1'] = $_POST['shipping_address1'];
		$update_array['city'] = $_POST['shipping_city'];
		$update_array['company'] = $_POST['shipping_company'];
		$update_array['postcode'] = $_POST['shipping_zip'];
		$state = $db->func_query_first_cell ("SELECT zone_id FROM oc_zone WHERE lower(name)  LIKE '%".strtolower($_POST['shipping_state'])."%'");
		$update_array['zone_id'] = $state;
		//testObject($update_array);
		$db->func_array2update("oc_buyback",$update_array,"buyback_id = '$lbb_shipment'");
		$_SESSION['message'] = 'Address Changed';
		header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
		exit;
	} else if ($_POST['ship_address_type'] == 'oc_order') {
		$order_id = $_POST['ship_address_id'];
		$update_array = array ();
		$update_array['shipping_address_1'] = $_POST['shipping_address1'];
		$update_array['shipping_address_2'] = $_POST['shipping_address2'];
		$update_array['shipping_city'] = $_POST['shipping_city'];
		$update_array['shipping_postcode'] = $_POST['shipping_zip'];
		$update_array['shipping_country'] = $_POST['shipping_country'];
		$update_array['shipping_company'] = $_POST['shipping_company'];
		$update_array['shipping_zone'] = $_POST['shipping_state'];
		$db->func_array2update("oc_order",$update_array,"order_id = '$order_id'");
		$_SESSION['message'] = 'Address Changed';
		header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
		exit;
	} else if ($_POST['ship_address_type'] == 'inv_order') {
		$order_id = $_POST['ship_address_id'];
		$update_array = array ();
		$update_array['address1'] = $_POST['shipping_address1'];
		$update_array['address2'] = $_POST['shipping_address2'];
		$update_array['city'] = $_POST['shipping_city'];
		$update_array['zip'] = $_POST['shipping_zip'];
		$update_array['country'] = $_POST['shipping_country'];
		$update_array['company'] = $_POST['shipping_company'];
		$update_array['state'] = $_POST['shipping_state'];
		$db->func_array2update("inv_orders_details",$update_array,"order_id = '$order_id'");
		$_SESSION['message'] = 'Address Changed';
		header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
		exit;
	}    
	/*if ($_POST['ship_address_type'] == '1') {
		$order_id = $_POST['address_order_id'];
		$update_array = array ();
		$update_array['address1'] = $_POST['shipping_address1'];
		$update_array['address2'] = $_POST['shipping_address2'];
		$update_array['city'] = $_POST['shipping_city'];
		$update_array['zip'] = $_POST['shipping_zip'];
		$update_array['country'] = $_POST['shipping_country'];
		$update_array['company'] = $_POST['shipping_company'];
		$update_array['state'] = $_POST['shipping_state'];
		$db->func_array2update("inv_orders_details",$update_array,"order_id = '$order_id'");
		$_SESSION['message'] = 'Address Changed';
		header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
		exit;
	} else if ($_POST['is_po_address'] == '1') {
		$po_id = $_POST['po_address_id'];
		$update_array = array ();
		$update_array['address1'] = $_POST['shipping_address1'];
		$update_array['address2'] = $_POST['shipping_address2'];
		$update_array['company_name'] = $_POST['shipping_company'];
		$update_array['city'] = $_POST['shipping_city'];
		$update_array['zip'] = $_POST['shipping_zip'];
		$update_array['state'] = $_POST['shipping_state'];
		$db->func_array2update("inv_po_customers",$update_array,"id = '$po_id'");
		$_SESSION['message'] = 'Address Changed';
		header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
		exit;
	} else if ($_POST['is_lbb_address'] == '1'){
		$lbb_shipment = ['lbb_address_id'];
		$update_array = array ();
		$check = $db->func_query_first_cell("SELECT address_id FROM oc_buyback WHERE shipment_number ='".$lbb_shipment."'");
		if ($check <= 0) {
			$update_array['address_1'] = $_POST['shipping_address1'];
			$update_array['city'] = $_POST['shipping_city'];
			$update_array['postcode'] = $_POST['shipping_zip'];
			$state = $db->func_query_first_cell ("SELECT zone_id FROM oc_zone WHERE lower(name)  LIKE '%".strtolower($_POST['shipping_state'])."%'");
			//print_r($state);exit;
			$update_array['zone_id'] = $state;
			$db->func_array2update("oc_buyback",$update_array,"shipment_number = '$lbb_shipment'");
			$_SESSION['message'] = 'Address Changed';
		header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
		exit;
		} else {
			$update_array['address_1'] = $_POST['shipping_address1'];
			$update_array['address_2'] = $_POST['shipping_address2'];
			$update_array['city'] = $_POST['shipping_city'];
			$update_array['postcode'] = $_POST['shipping_zip'];
			$state = $db->func_query_first_cell ("SELECT zone_id FROM oc_zone WHERE lower(name)  LIKE '%".strtolower($_POST['shipping_state'])."%'");
			//print_r($state);exit;
			$update_array['zone_id'] = $state;
			$db->func_array2update("oc_address",$update_array,"address_id = '$check'");
			$_SESSION['message'] = 'Address Changed';
		header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
		exit;
		}
	}*/
}
if ($_POST['update_billing_address']) {
		$order_id = $_POST['bill_address_id'];
		$update_array = array ();
		$update_array['payment_address_1'] = $_POST['bill_address1'];
		$update_array['payment_address_2'] = $_POST['bill_address2'];
		$update_array['payment_city'] = $_POST['bill_city'];
		$update_array['payment_postcode'] = $_POST['bill_zip'];
		$update_array['payment_country'] = $_POST['bill_country'];
		$update_array['payment_company'] = $_POST['bill_company'];
		$update_array['payment_zone'] = $_POST['bill_state'];
		$db->func_array2update("oc_order",$update_array,"order_id = '$order_id'");
		$_SESSION['message'] = 'Address Changed';
		header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
		exit;
}
//print_r($customer_id);exit;
//$address2=$db->func_query("select * from oc_address where customer_id=$customer_id");
//print_r($addres[0][address_1]);
//exit;
if($_GET['action']=='update_customer_group')
{
	$customer_id = (int)$_POST['customer_id'];
	$group_name = $db->func_query_first_cell("SELECT name FROM oc_customer_group_description WHERE customer_group_id ='".(int)$_POST['customer_group_id']."'");
	$db->db_exec("UPDATE oc_customer SET customer_group_id='".(int)$_POST['customer_group_id']."' WHERE customer_id='".$customer_id."'");
	$db->db_exec("UPDATE inv_customers SET customer_group='".$group_name."' WHERE customer_id='".(int)$customer_id."'");
	actionLog('Customer: '.linkToProfile($customer_email,$host_path).' customer group updated to '.$group_name);
	exit;
}
if(!$customer_details){
	$_SESSION['message'] = "Customer details not found";
	header("Location:home.php");
	exit;
}
if ($_GET['action'] == 'mergeEmail') {
	$tables = array("inv_customer_comments", "inv_customer_files", "inv_customer_return_orders", "inv_customers", "inv_orders", "inv_po_customers", "inv_return_orders", "inv_returns", "oc_customer", "oc_order", "oc_return");
	$email = $_POST['toEmail'];
	$oldEmail = $_POST['email'];
	$data = array();
	$data['customer_id'] = $customer_id;
	$data['comments'] = "$oldEmail is Merged to $email by " . $_SESSION['login_as'];
	$data['comment_type'] = "Sales Call";
	$data['user_id'] = $_SESSION['user_id'];
	$data['email'] = $oldEmail;
	$data['date_added'] = date('Y-m-d H:i:s');
	$db->func_array2insert("inv_customer_comments",$data);
	$db->db_exec('DELETE FROM `oc_customer` WHERE `email` = "'. $oldEmail .'"');
	$db->db_exec('DELETE FROM `inv_customers` WHERE `email` = "'. $oldEmail .'"');
	foreach ($tables as $table) {
		$db->db_exec("UPDATE `$table` SET `email`='$email' WHERE `email`='$oldEmail'");
	}
	$_SESSION['message'] = 'Email Merged';
	$cust_id = getProfileId($email);
	$array = array('success'=> 1,'msg' => $cust_id);
	echo json_encode($array);
	exit;
}
// Updating Email and leaving a comment in account as well.
if ($_GET['action'] == 'update_email') {
	$tables = array("inv_customer_comments", "inv_customer_files", "inv_customer_return_orders", "inv_customers", "inv_orders", "inv_po_customers", "inv_return_orders", "inv_returns", "oc_customer", "oc_order", "oc_return");
	$email = $_POST['email'];
	$oldEmail = $_POST['oldEmail'];
	$exist = $db->func_query_first_cell("SELECT `email` FROM `inv_customers` WHERE email = '$email'");
	if (!$exist) {
		$exist = $db->func_query_first_cell("SELECT `email` FROM `inv_po_customers` WHERE email = '$email'");
	}
	if (!$exist) {
		$data = array();
		$data['customer_id'] = $customer_id;
		$data['comments'] = "changed Email $oldEmail to $email";
		$data['comment_type'] = "Sales Call";
		$data['user_id'] = $_SESSION['user_id'];
		$data['email'] = $oldEmail;
		$data['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_customer_comments",$data);
		foreach ($tables as $table) {
			$db->db_exec("update `$table` set `email`='$email' WHERE `email`='$oldEmail'");
		}
		$_SESSION['message'] = 'Email Changed';
		$array = array('success'=> 1,'msg' => base64_encode($email));
		echo json_encode($array);
		exit;
	} else {
		$array = array('error' => 1, 'msg' => 'Email Already exist');
		echo json_encode($array);
		exit;
	}
	
}
// $query = "SELECT DISTINCT
// `inv_orders`.*,
// `inv_orders_details`.*,
// `oc_order`.`ip`,
// `oc_order`.`payment_method` 
// FROM
// `inv_orders` 
// INNER JOIN `inv_orders_details`
// ON `inv_orders`.`order_id` = `inv_orders_details`.`order_id`

// WHERE `inv_orders`.`email` = '$customer_email' 
// ORDER BY inv_orders.order_date  DESC";

$query="SELECT a.order_id,a.order_status,a.order_date,a.paid_price,b.shipping_cost,a.order_price from inv_orders a,inv_orders_details b where a.order_id=b.order_id and  a.email='".$customer_email."' order by a.order_date desc";
// echo $query;exit;
if ($_GET['action'] == 'fetch_orders') {
	$page = (int)$_GET['page'];
	$start = (int)($page-1)*20;
	$end = 20;
	// echo $query." limit $start,$end";exit;
	$orders = $db->func_query($query." limit $start,$end");
	$count_orders = $db->func_query($query);
	$count_orders = count($count_orders);
	$pages = ceil($count_orders/$end);
	for($i=1;$i<=$pages;$i++)
    {
    	$href_start='<strong>';
    	$href_end='</strong>';
    	if($i!=$page)
    	{
    		$href_start = '<a href="javascript:void(0)" class="pagination_link" data-page="'.$i.'">';
    		$href_end = '</a>';
    	}
    	$footer_data.=$href_start.$i.$href_end.' | ';
    }
    $peak_order = $page*20;
    $least_order = $peak_order - 19;
    $peak_order = (count($orders) + $least_order) - 1;
    // $footer_data.='<br>(Showing Orders '.$least_order.'-'.$peak_order.' ) / '.$count_orders.' Total Orders';
	foreach($orders as $i => $order) { 	
		$payStatus = 0;
		if (strtolower($order['order_status']) == 'processed' || (strtolower($order['store_type'] == 'po_business') && (strtolower($order['order_status']) == 'shipped' || strtolower($order['order_status']) == 'unshipped'))) {
			$payStatus = 1;
		}
		$orderData .= '<tr>';
		$orderData .= '<td>';
		    	//$order_totals = orderTotal($order['order_id']);
		if ($order['order_price'] > $order['paid_price'] && $payStatus) {
			$orderData .= '<input type="checkbox" onchange="payOrders();" class="order_id_input" value="' . $order['order_id'] . '"/>';
		}
		$orderData .= '</td>';
		$orderData .= '<td>'.($i+1).'</td>';
		$orderData .= '<td>'.americanDate($order['order_date']).'</td>';
		$orderData .= '<td>'.($order['manual']?linkToVoucher($order['order_id'],$host_path,$order['code']):'<a href="viewOrderDetail.php?order='.$order['order_id'].'">'. $order['order_id']) .'</a></td>';
		$orderData .= '<td>'.$order['order_status'].'</td>';
		$orderData .= '<td>'.($order['manual']?'':''.getTrackingNo($order['order_id'])).'</td>';
		$orderData .= '<td width="250px;">'.($order['manual']?'':''.getComments($order['order_id'],1)).'</td>';
	// $orderData .= '<td>'.($order['manual']?'':''.getAttachments($order['order_id'])).'</td>';
		if($_SESSION['display_cost']) {
			// $order_items = $db->func_query("Select * from inv_orders_items where order_id = '".$order['order_id']."' ");
			// $sub_total = 0;
			// foreach ($order_items as $zz => $order_item) {
			// 	$sub_total+=($order_item['product_price']-$order_item['promotion_discount']);
			// }


			$sub_total = $db->func_query_first_cell("SELECT SUM(b.product_price)-sum(b.promotion_discount) FROM inv_orders_items b, inv_orders a where a.order_id=b.order_id and b.order_id='".$order['order_id']."'");
			$other_charges = round($db->func_query_first_cell('SELECT SUM(`value`) FROM `oc_order_total` WHERE cast(`order_id` as char(50)) = "'. $order['order_id'] .'" AND (`code` = "business_fee" OR `code` = "tax")'),2);
			$shipping_charges = round($order['shipping_cost'],2);
			$orderData .= '<td>$'.number_format($sub_total, 2).'</td>';	
			$orderData .= '<td>$'.number_format($other_charges,2).'</td>';
			$orderData .= '<td>$'.number_format($shipping_charges,2).'</td>';	
		}
		$orderData .= '</tr>';
	}
	$orderData .= '<tr><td colspan="10" align="right">'.$footer_data.'</td></tr>';
	$json = array();
	$json['order_data'] = $orderData;
	if ($orders) {
		$json['success'] = 1;	
	}
	echo json_encode($json);
	exit;
}

//$balance = $balance + (float)$db->func_query_first_cell("SELECT SUM(amount) FROM oc_voucher_history WHERE manual=1 AND customer_email='".$customer_email."'");
$ip_address = array();
foreach($orders as $order){
	if ($order['ip']) {
		$ip_address[$order['ip']] = "<a target='_blank' href='http://www.ip-tracker.org/locator/ip-lookup.php?ip=".$order['ip']."'>".$order['ip']."</a><br />";
	}
}
$ip_address = implode("",$ip_address);
$rma_returns = $db->func_query("select * from inv_returns where email = '".$customer_details['email']."' ORDER BY date_added DESC");
foreach($rma_returns as $index => $rma_return){
	$rma_returns[$index]['extra_details'] = $db->func_query("select sku , quantity , price , decision from inv_return_items where return_id = '".$rma_return['id']."'");
}
$returns_total = 0.00;
$returns3_total = 0.00;
$returns6_total = 0.00;
$rma_returns_html = '';
foreach($rma_returns as $k => $rma_return) {
	$returnDate = date('Y-m-d', strtotime($rma_return['date_added']));	
	$amount = 0.00;
	$rma_returns_html .= '<tr>';
	$rma_returns_html .= '<td>' . americanDate($rma_return['date_added']) . '</td>	';
	$rma_returns_html .= '<td><a href="viewOrderDetail.php?order=' . $rma_return['order_id'] . '">'. $rma_return['order_id'] . '</a></td>';
	$rma_returns_html .= '<td>';
	$rma_returns_html .= '<a href="return_detail.php?rma_number=' . $rma_return['rma_number']. '">';
	$rma_returns_html .= $rma_return['rma_number'];
	$rma_returns_html .= '</a>';
	$rma_returns_html .= '</td>';
	$rma_returns_html .= '<td>' . (($rma_return['rma_status'] == 'In QC') ? 'QC Completed' : $rma_return['rma_status']) . '</td>';
	$rma_returns_html .= '<td>';
	$rma_returns_html .= '<?php $amount = 0; ?>';
	foreach($rma_return['extra_details'] as $item) {
		$rma_returns_html .= $item['sku'] . '/' . $item['decision'] . '<br />';
		$amount = $amount + $item['price'];
	}
	$rma_returns_html .= '</td>';
	if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){
	$rma_returns_html .= '<td>$' . $amount . '</td>';
	}
	$rma_returns_html .= '<td>$' . (($rma_return['ppusa']) ? number_format($rma_return['shipping_cost'], 2) : '0.00') . '</td>';
	$rma_returns_html .= '<td></td>';
	$rma_returns_html .= '</tr>';
	$orderDate3Month = date('Y-m-d', strtotime(date('Y-m-d') . ' -3 months'));
	$orderDate6Month = date('Y-m-d', strtotime(date('Y-m-d') . ' -6 months'));
	if (strtolower($rma_return['rma_status']) != 'awaiting') {
		$returns_total += $amount;
		if ($returnDate > $orderDate3Month) {
			$returns3_total += $amount;
		}
		if ($returnDate > $orderDate6Month) {
			$returns6_total += $amount;
		}
	}
}
if (isset($_GET['load_credit_debit'])) {
	// $d_sub = $db->func_query_first_cell("SELECT SUM(oi.product_price) FROM inv_orders_items oi inner join inv_orders o on (oi.order_id = o.order_id) WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' ");
	// $d_tax = $db->func_query_first_cell("SELECT SUM(ot.value) FROM oc_order_total ot inner join inv_orders o on (o.order_id = ot.order_id)  WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' AND ot.code = 'tax' ");
	// $d_shipping = $db->func_query_first_cell("SELECT SUM(d.shipping_cost) FROM inv_orders_details d inner join inv_orders o on (o.order_id = d.order_id)  WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped'");
	// $total_debit = $d_sub + $d_tax + $d_shipping;
	// $d_sub = $db->func_query_first_cell("SELECT SUM(oi.product_price) FROM inv_orders_items oi inner join inv_orders o on (oi.order_id = o.order_id) WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' AND o.order_date >= ( NOW() - INTERVAL 3 MONTH ) ");
	// $d_tax = $db->func_query_first_cell("SELECT SUM(ot.value) FROM oc_order_total ot inner join inv_orders o on (o.order_id = ot.order_id)  WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' AND ot.code = 'tax' AND o.order_date >= ( NOW() - INTERVAL 3 MONTH ) ");
	// $d_shipping = $db->func_query_first_cell("SELECT SUM(d.shipping_cost) FROM inv_orders_details d inner join inv_orders o on (o.order_id = d.order_id)  WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' AND o.order_date >= ( NOW() - INTERVAL 3 MONTH )");
	// $total3month_debit = $d_sub + $d_tax + $d_shipping;
	// $d_sub = $db->func_query_first_cell("SELECT SUM(oi.product_price) FROM inv_orders_items oi inner join inv_orders o on (oi.order_id = o.order_id) WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' AND o.order_date >= ( NOW() - INTERVAL 6 MONTH ) ");
	// $d_tax = $db->func_query_first_cell("SELECT SUM(ot.value) FROM oc_order_total ot inner join inv_orders o on (o.order_id = ot.order_id)  WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' AND ot.code = 'tax' AND o.order_date >= ( NOW() - INTERVAL 6 MONTH ) ");
	// $d_shipping = $db->func_query_first_cell("SELECT SUM(d.shipping_cost) FROM inv_orders_details d inner join inv_orders o on (o.order_id = d.order_id)  WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' AND o.order_date >= ( NOW() - INTERVAL 6 MONTH )");
	// $total6month_debit = $d_sub + $d_tax + $d_shipping;
	// $c_sub = $db->func_query_first_cell("SELECT SUM(oi.product_price) FROM inv_orders_items oi inner join inv_orders o on (oi.order_id = o.order_id) WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' ");
	// $c_tax = $db->func_query_first_cell("SELECT SUM(ot.value) FROM oc_order_total ot inner join inv_orders o on (o.order_id = ot.order_id)  WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' AND ot.code = 'tax' ");
	// $c_shipping = $db->func_query_first_cell("SELECT SUM(d.shipping_cost) FROM inv_orders_details d inner join inv_orders o on (o.order_id = d.order_id)  WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped'");
	// $total_credit = $c_sub + $c_tax + $c_shipping;
	// $c_sub = $db->func_query_first_cell("SELECT SUM(oi.product_price) FROM inv_orders_items oi inner join inv_orders o on (oi.order_id = o.order_id) WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' AND o.order_date >= ( NOW() - INTERVAL 3 MONTH ) ");
	// $c_tax = $db->func_query_first_cell("SELECT SUM(ot.value) FROM oc_order_total ot inner join inv_orders o on (o.order_id = ot.order_id)  WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' AND ot.code = 'tax' AND o.order_date >= ( NOW() - INTERVAL 3 MONTH ) ");
	// $c_shipping = $db->func_query_first_cell("SELECT SUM(d.shipping_cost) FROM inv_orders_details d inner join inv_orders o on (o.order_id = d.order_id)  WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' AND o.order_date >= ( NOW() - INTERVAL 3 MONTH )");
	// $total3month_credit = $c_sub + $c_tax + $c_shipping;
	// $c_sub = $db->func_query_first_cell("SELECT SUM(oi.product_price) FROM inv_orders_items oi inner join inv_orders o on (oi.order_id = o.order_id) WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' AND o.order_date >= ( NOW() - INTERVAL 6 MONTH ) ");
	// $c_tax = $db->func_query_first_cell("SELECT SUM(ot.value) FROM oc_order_total ot inner join inv_orders o on (o.order_id = ot.order_id)  WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' AND ot.code = 'tax' AND o.order_date >= ( NOW() - INTERVAL 6 MONTH ) ");
	// $c_shipping = $db->func_query_first_cell("SELECT SUM(d.shipping_cost) FROM inv_orders_details d inner join inv_orders o on (o.order_id = d.order_id)  WHERE o.email='".$customer_email."' AND lower(o.order_status) = 'shipped' AND o.order_date >= ( NOW() - INTERVAL 6 MONTH )");

	$total_debit = $db->func_query_first_cell("SELECT SUM(paid_price) FROM inv_orders where email='".$customer_email."' and lower(order_status)='shipped' ");
	$total_credit = $db->func_query_first_cell("SELECT SUM(order_price) FROM inv_orders where email='".$customer_email."' and lower(order_status)='shipped' ");

	$total3month_debit = $db->func_query_first_cell("SELECT SUM(paid_price) FROM inv_orders where email='".$customer_email."' and lower(order_status)='shipped' and order_date >= ( NOW() - INTERVAL 3 MONTH) ");
	$total6month_debit = $db->func_query_first_cell("SELECT SUM(paid_price) FROM inv_orders where email='".$customer_email."' and lower(order_status)='shipped' and order_date >= ( NOW() - INTERVAL 6 MONTH) ");

	$total3month_credit = $db->func_query_first_cell("SELECT SUM(order_price) FROM inv_orders where email='".$customer_email."' and lower(order_status)='shipped' and order_date >= ( NOW() - INTERVAL 3 MONTH) ");
	$total6month_credit = $db->func_query_first_cell("SELECT SUM(order_price) FROM inv_orders where email='".$customer_email."' and lower(order_status)='shipped' and order_date >= ( NOW() - INTERVAL 6 MONTH) ");
	
	$balance = $total_credit - $total_debit;
	$json = array();
	$json['total_debit'] = '$'.number_format($total_debit,2);
	$json['total_debit_3'] = '$'.number_format($total3month_debit,2);
	$json['total_debit_6'] = '$'.number_format($total6month_debit,2);
	$json['total_credit'] = '$'.number_format($total_credit,2);
	$json['total_credit_3'] = '$'.number_format($total3month_credit,2);
	$json['total_credit_6'] = '$'.number_format($total6month_credit,2);
	$json['balance'] = '$'.number_format($balance,2);
	$json['rp'] = number_format((($returns_total / $total_debit) * 100), 2).'%';
	$json['rp3'] = number_format((($returns3_total / $total3month_debit) * 100), 2).'%';
	$json['rp6'] = number_format((($returns6_total / $total6month_debit) * 100), 2).'%';
	$json['success'] = 1;
	echo json_encode($json);
	exit;
	
}
$bq = ($customer_details['customer_id'])? 'OR customer_id = "'. $customer_details['customer_id'] .'"': '';
$buybacks = $db->func_query("SELECT a . * 
FROM  `oc_buyback` a
LEFT JOIN oc_customer b ON ( a.customer_id = b.customer_id ) 
WHERE LCASE( a.email ) = LCASE('". $customer_details['email'] ."') or LCASE( b.email ) = LCASE('". $customer_details['email'] ."') order by a.buyback_id DESC");
// echo "SELECT * FROM `oc_buyback` WHERE LCASE(email) = LCASE('". $customer_details['email'] ."') ". $bq ." order by buyback_id DESC";
	//Getting All Vouchers
$query = 'SELECT 
`ov`.*
FROM
`oc_voucher` AS `ov`
WHERE LCASE(`ov`.`to_email`) = LCASE("' . $customer_email . '") ORDER BY ov.date_added DESC';
$vouchers = $db->func_query($query);
if($_GET['action']=='delete_doc')
{
	$db->db_exec("DELETE FROM inv_customer_files WHERE doc_id='".(int)$_GET['doc_id']."'");	
	$_SESSION['message'] = 'Attachment has been removed';
	header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);exit;
	
}
if(isset($_POST['submit']))
{
	
	if($_FILES['attachment']['tmp_name']){
		$imageCount = 0;
		$count    = count($_FILES['attachment']['tmp_name']);
		for($i=0; $i<$count; $i++){
			$uniqid = uniqid();
			$name   = explode(".",$_FILES['attachment']['name'][$i]);
			$ext    = end($name);
			$destination = $path."files/".$uniqid.".$ext";
			$file = $_FILES['attachment']['tmp_name'][$i];
			$mime = $_FILES['attachment']['type'][$i];
			$allowed = array("image/jpeg", "image/gif", "application/pdf","image/png");
			if($file and in_array($mime, $allowed))
			{
				if(move_uploaded_file($file, $destination)){
					$orderDoc = array();
					$orderDoc['customer_id'] = $customer_id;
					$orderDoc['path'] = "files/".basename($destination);
					$orderDoc['description'] = $_POST['attachment_description'][$i];
					$orderDoc['file_mime'] = $mime;
					$orderDoc['user_id'] = $_SESSION['user_id'];
					$orderDoc['email'] = $customer_email;
					$orderDoc['date_added'] = date('Y-m-d h:i:s');
					$db->func_array2insert("inv_customer_files",$orderDoc);
					$imageCount++;
				}
			}
		}
	}
	$_SESSION['message'] = 'File(s) attached successfully';
	header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
	exit;
}
if (isset($_POST['add_business_license'])) {
	$file_types = array('image/jpeg', 'image/gif','application/pdf');
	if ($_FILES['attachments']['tmp_name']) {
			$imageCount = 0;
			$count = count($_FILES['attachments']['tmp_name']);
			for ($i = 0; $i < $count; $i++) {
				if (in_array($_FILES['attachments']['type'][$i], $file_types)) {
					$uniqid = uniqid();
					$name = explode(".", $_FILES['attachments']['name'][$i]);
					$ext = end($name);
					$destination = $path."../image/" . $uniqid . ".$ext";
					$file = $_FILES['attachments']['tmp_name'][$i];
					if (move_uploaded_file($file, $destination)) {
						$image =  $uniqid . ".$ext";
					}
				} else {
					$_SESSION['message'] = 'File Format Not Supported.';
					header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
					exit;
				}
					
			}
		}	
		if ($image) {
				$upload_business = array();
				$upload_business['business_license'] = $image;
				$upload_business['business_date_updated'] = date('Y-m-d H:i:s');
				$upload_business['business_license_user'] = $_SESSION['user_id'];
				$cust_id = $db->func_escape_string($_GET['id']);
				$cust_id = explode("-",$cust_id);
				if($cust_id[0]=='PPC')
				{
					$db->func_query("UPDATE inv_customers SET business_license = '".$image."',business_license_user = '".$_SESSION['user_id']."',business_date_updated = NOW()  WHERE email='".$customer_email."'");
					$db->func_query("UPDATE oc_wholesale_account SET business_license = '".$image."'  WHERE personal_email='".$customer_email."'");
				}
				else if($cust_id[0]=='POC')
				{
					$db->func_query("UPDATE inv_po_customers SET business_license = '".$image."',business_license_user = '".$_SESSION['user_id']."',business_date_updated = NOW()  WHERE email='".$customer_email."'");
					$db->func_query("UPDATE oc_wholesale_account SET business_license = '".$image."'  WHERE personal_email='".$customer_email."'");
				}
				else
				{
					$db->func_query("UPDATE oc_buyback SET business_license = '".$image."',business_license_user = '".$_SESSION['user_id']."',business_date_updated = NOW()  WHERE email='".$customer_email."'");
					$db->func_query("UPDATE oc_wholesale_account SET business_license = '".$image."'  WHERE personal_email='".$customer_email."'");
				}
				
				$_SESSION['message'] = 'Business license attached successfully';
				header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
				exit;
			} else {
				$_SESSION['message'] = 'Business license Upload Failed';
				header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
				exit;
			}
			
}
if (isset($_POST['add_tax_license'])) {
	$file_types = array('image/jpeg', 'image/gif','application/pdf');
	if ($_FILES['attachments']['tmp_name']) {
			$imageCount = 0;
			$count = count($_FILES['attachments']['tmp_name']);
			for ($i = 0; $i < $count; $i++) {
				if (in_array($_FILES['attachments']['type'][$i], $file_types)) {
					$uniqid = uniqid();
					$name = explode(".", $_FILES['attachments']['name'][$i]);
					$ext = end($name);
					$destination = "images/" . $uniqid . ".$ext";
					$file = $_FILES['attachments']['tmp_name'][$i];
					if (move_uploaded_file($file, $destination)) {
						$image =  $uniqid . ".$ext";
					}	
				} else {
					$_SESSION['message'] = 'File Format Not Supported.';
					header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
					exit;
				}
				
			}
		}	
		if ($image) {
				$upload_tax = array();
				$upload_tax['tax_license'] = $image;
				$upload_tax['tax_date_updated'] = date('Y-m-d H:i:s');
				$upload_tax['tax_license_user'] = $_SESSION['user_id'];
				$cust_id = $db->func_escape_string($_GET['id']);
				$cust_id = explode("-",$cust_id);
				if($cust_id[0]=='PPC')
				{
					$db->func_query("UPDATE inv_customers SET tax_license = '".$image."',tax_license_user = '".$_SESSION['user_id']."',tax_date_updated = NOW()  WHERE email='".$customer_email."'");
				}
				else if($cust_id[0]=='POC')
				{
					$db->func_query("UPDATE inv_po_customers SET tax_license = '".$image."',tax_license_user = '".$_SESSION['user_id']."',tax_date_updated = NOW()  WHERE email='".$customer_email."'");
				}
				else
				{
					$db->func_query("UPDATE oc_buyback SET tax_license = '".$image."',tax_license_user = '".$_SESSION['user_id']."',tax_date_updated = NOW()  WHERE email='".$customer_email."'");
				}
				
				$_SESSION['message'] = 'Tax license attached successfully';
				header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
				exit;
			} else {
				$_SESSION['message'] = 'Tax license Upload Failed';
				header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
				exit;
			}
			
}
if ($_GET['action'] == 'remove_business_license') {
	$cust_id = $db->func_escape_string($_GET['id']);
	$cust_id = explode("-",$cust_id);
	if($cust_id[0]=='PPC')
	{
		@unlink( $path.'../image/'.$business_license['business_license']);
		$db->func_query("UPDATE inv_customers SET business_license = '',business_license_user = '',business_date_updated = NOW()  WHERE email='".$customer_email."'");
		$db->func_query("UPDATE oc_wholesale_account SET business_license = '' WHERE personal_email='".$customer_email."'");
	}
	else if($cust_id[0]=='POC')
	{
		@unlink( $path.'../image/'.$business_license['business_license']);
		$db->func_query("UPDATE inv_po_customers SET business_license = '".$image."',business_license_user = '".$_SESSION['user_id']."',business_date_updated = NOW()  WHERE email='".$customer_email."'");
		$db->func_query("UPDATE oc_wholesale_account SET business_license = '' WHERE personal_email='".$customer_email."'");
		
	}
	else
	{
		@unlink( $path.'../image/'.$business_license['business_license']);
		$db->func_query("UPDATE oc_buyback SET business_license = '".$image."',business_license_user = '".$_SESSION['user_id']."',business_date_updated = NOW()  WHERE email='".$customer_email."'");
		$db->func_query("UPDATE oc_wholesale_account SET business_license = '' WHERE personal_email='".$customer_email."'");
		
	}
	$_SESSION['message'] = 'Business License Removed';
	header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
	exit;
	
}
if ($_GET['action'] == 'remove_tax_license') {
	$cust_id = $db->func_escape_string($_GET['id']);
	$cust_id = explode("-",$cust_id);
	if($cust_id[0]=='PPC')
	{
		unlink( $host_path .'images/'.$tax_license['tax_license']);
		$db->func_query("UPDATE inv_customers SET tax_license = '',tax_license_user = '' WHERE email='".$customer_email."'");
	}
	else if($cust_id[0]=='POC')
	{
		unlink( $host_path .'images/'.$tax_license['tax_license']);
		$db->func_query("UPDATE inv_po_customers SET tax_license = '".$image."',tax_license_user = '".$_SESSION['user_id']."' WHERE email='".$customer_email."'");
	}
	else
	{
		unlink( $host_path .'images/'.$tax_license['tax_license']);
		$db->func_query("UPDATE oc_buyback SET tax_license = '".$image."',tax_license_user = '".$_SESSION['user_id']."' WHERE email='".$customer_email."'");
	}
	$_SESSION['message'] = 'Tax License Removed';
	header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
	exit;
	
}
if(isset($_POST['addcomment']))
{
	$data = array();
	$data['customer_id'] = $customer_id;
	$data['comments'] = $db->func_escape_string($_POST['comment']);
	$data['comment_type'] = $db->func_escape_string($_POST['comment_type']);
	$data['user_id'] = $_SESSION['user_id'];
	$data['email'] = $customer_email;
	$data['date_added'] = date('Y-m-d H:i:s');
	$db->func_array2insert("inv_customer_comments",$data);
	$_SESSION['message'] = 'Comment has been entered';
	header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);
	exit;
	
}
 //testObject($customer_details);
if ($_POST['createUser'] && $_SESSION['c_ua_for_guest'] && $customer_details['type'] == 'guest') {
	$password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*()_+=-?><'), 0,10);
	$account = array(		
		'store_id' => '0',
		'firstname' => $customer_details['firstname'],
		'lastname' => $customer_details['lastname'],
		'email' => $customer_details['email'],
		'telephone' => $customer_details['telephone'],
		'password' => md5($password),
		'newsletter' => '1',
		'address_id' => '',
		'customer_group_id' => '8',
		'status' => '1',
		'approved' => '1',
		'token' => '',
		'date_added' => date('Y-m-d H:i:s'),
		'orders_count' => $customer_details['total'],
		'orders_totals' => $total_debit,
		);
	$cid = $db->func_array2insert("oc_customer",$account);
	$adderss = array(
		'customer_id' => $cid,
		'firstname' => $customer_details['shipping_firstname'],
		'lastname' => $customer_details['shipping_lastname'],
		'address_1' => $customer_details['shipping_address_1'],
		'address_2' => $customer_details['shipping_address_2'],
		'city' => $customer_details['shipping_city'],
		'postcode' => $customer_details['shipping_postcode'],
		'country_id' => $customer_details['shipping_country_id'],
		'zone_id' => $customer_details['shipping_zone_id'],
		'insert_date' => date('Y-m-d H:i:s'),
		);
	$db->func_array2insert("oc_address",$adderss);
	$emailMessage = array(
		'text_subject'  => '%s - Thank you for registering',
		'text_welcome'  => 'Welcome and thank you for registering at %s!',
		'text_login'    => 'Your account has now been created and you can log in by using your email address and password by visiting our website or at the following URL:',
		'text_approval' => 'Your account must be approved before you can login. Once approved you can log in by using your email address and password by visiting our website or at the following URL:',
		'text_services' => 'Upon logging in, you will be able to access other services including reviewing past orders, printing invoices and editing your account information.',
		'text_thanks'   => 'Thanks,',
		);
	$subject = sprintf($emailMessage['text_subject'], $account['firstname'] . ' ' . $account['lastname']);
	$message = sprintf($emailMessage['text_welcome'], 'PhonePartsUSA.com');
	$message .= $emailMessage['text_login'] . "<br>";
	$message .= str_replace('imp/', 'index.php?route=account/login', $host_path) . "<br><br>";
	$message .= 'Password: ' . $password . "<br>";
	$message .= 'Email: ' . $account['email'] . "<br><br>";
	$message .= $emailMessage['text_services'] . "<br><br>";
	$message .= $emailMessage['text_thanks'] . "<br>";
	$message .= 'PhonePartsUSA.com';
	sendEmail($account['firstname'] . ' ' . $account['lastname'], $account['email'], $subject, $message);
	header("Location: $host_path/customer_profile.php?id=".$_GET['id']."&email=".$_GET['email']);exit;
}
//$em=$_GET['email'];
//print_r($em);
//exit;
if($_GET['address_id']){
	//unset($_GET['delete_address']);
	$db->db_exec("DELETE from oc_address WHERE address_id='".$_GET['address_id']."'");
	header("Location: $host_path/customer_profile.php?email=".$_GET['email']);exit;
	}
if($_GET['customer_id'] && $_GET['address_id']){
	//unset($_GET['delete_address']);
	//$db->db_exec("INSERT into oc_customer (address_id) values($_GET['address_id']) WHERE customer_id=$_GET['customer_id']");
	}
	if($customer_id){
	$address1=$db->func_query("select * from oc_address where customer_id=$customer_id ");
}
$email_history=$db->func_query("select * from inv_email_report where customer_email='".$customer_details['email']."' ");
if(isset($_POST['email_type'])){
	if($_POST['types']=='all'){
		$email_history=$db->func_query("select * from inv_email_report where customer_email='".$customer_details['email']."' ");
	}else if($_POST['types']=='rma'){
		$email_history=$db->func_query("select * from inv_email_report where customer_email='".$customer_details['email']."' AND (resolution LIKE  '%rma%' or resolution LIKE  '%returns%' or resolution LIKE  '%return%' or resolution LIKE  '%refund%' ) ");
	}else if($_POST['types']=='lbb'){
		$email_history=$db->func_query("select * from inv_email_report where customer_email='".$customer_details['email']."'  AND (resolution LIKE  '%LCD%' or resolution LIKE  '%buy back%' or resolution LIKE  '%lbb%' ) ");
	}else if($_POST['types']=='order'){
		$email_history=$db->func_query("select * from inv_email_report where customer_email='".$customer_details['email']."'  AND (resolution LIKE  '%order%' or resolution LIKE  '%orders%' or resolution LIKE  '%invoice%' )");
	}
	
}
$cus_id=$_GET['id'];
if (isset($_GET['email_id'])) {
	$emailss=$db->func_query_first("select * from inv_email_report where email_report_id='".$_GET['email_id']."' ");
	$order_price=$db->func_query_first_cell("select order_price from inv_orders where order_id='".$emailss['order_id']."' ");
	$email = array();
	$email['title'] = $email['title'];
	$email['subject'] = $emailss['email_subject'];
	$email['message'] = $emailss['comment'];
	$emailInfo['total_formatted'] = $order_price;
	sendEmailDetails($emailInfo, $email);
	header("Location:$host_path/customer_profile.php?id=$cus_id&openTab=tabCommentsAttach");
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Customer Profile and Orders Detail</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox({ width: '700px' , autoCenter : true , height : '500px'});
			$('.fancybox2').fancybox({ width: '700px' , height: 'auto' , autoCenter : true , autoSize : true });
		});
	</script>	
	
	<style type="text/css">
		.fancybox-inner{
			height:300px !important; 
		}
		#xcontent{width: 100%;
			height: 100%;
			top: 0px;
			left: 0px;
			position: fixed;
			display: block;
			opacity: 0.8;
			background-color: #000;
			z-index: 99;}
			.makeTabs .button{
				padding: 5px 8px;
				background-color:#3F51B5;
			}
	</style>
</head>
<body>
<div id="xcontent" style="display:none"><div style="color:#fff;
			top:40%;
			position:fixed;
			left:40%;
			font-weight:bold;font-size:25px"><img src="https://phonepartsusa.com/catalog/view/theme/default/image/loader_white.gif" /><span style="margin-left: 11%;
			margin-top: 33%;
			position: absolute;
			width: 201px;">Please wait...</span></div></div>
	<div class="div-fixed">
		<div align="center"> 
			<?php include_once 'inc/header.php';?>
		</div>
		
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		<div align="center">
			<div class="tabMenu" >
				<input type="button" class="toogleTab" data-tab="tabCDetails" value="Customer Details">
				<input type="button" class="toogleTab" data-tab="tabCommentsAttach" value="Contact History">
				<input type="button" class="toogleTab" data-tab="tabOrders" onclick="fetch_orders();" value="Orders History">
				<input type="button" class="toogleTab" data-tab="tabVouchers" value="Vouchers History">
				<input type="button" class="toogleTab" data-tab="tabReturns" value="Returns History">
				<input type="button" class="toogleTab" data-tab="tabBuyBack" value="Buy Back History">
			</div>
			<div class="tabHolder">
				<h3>Customer Profile</h3>
				<div id="loading">
					<h2>Loading...</h2>
				</div>
				<form method="post" action="" enctype="multipart/form-data">
					<div id="tabCDetails" class="makeTabs">
						<table width="70%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
							<tr>
								<td>Customer Group:</td>
								<td>
									<?php if ($customer_details['name'] != 'PO Business' && $customer_details['name'] != 'Guest' && $customer_details['name'] != 'LBB') : ?>
										<select name="customer_group_id" id="customer_group_id" onChange="updateCustomerGroup()">
											<option value="">Not Defined</option>
											<?php
											$customer_groups = $db->func_query("SELECT * FROM oc_customer_group_description");
											foreach($customer_groups as $customer_group)
											{
												?>
												<option value="<?php echo $customer_group['customer_group_id'];?>" <?php echo ($customer_details['customer_group_id']==$customer_group['customer_group_id']?'selected':''); ?>><?=$customer_group['name'];?></option>
												<?php 
											}
											?>
										</select>
										<!-- <a href="javascript:void(0)" onclick="updateCustomerGroup()">Update Customer Group</a> -->
									<?php else: ?>
										<?php echo $customer_details['name']; ?>
									<?php endif; ?>
									<?php //echo $customer_details['name'];?>
								</td>
								<!-- <td>Customer Name:</td>
								<td><b><?php echo $customer_details['firstname']." ".$customer_details['lastname'];?> </b>
									<?php
									if($customer_details['customer_mood'])
									{

										?>
										<img src="images/emoji/<?php echo $customer_details['customer_mood'];?>.png" style="width: 24px">
										<?php
									}

									?>

									<input type="hidden" id="customer_name" value="<?php echo $customer_details['firstname'].' '.$customer_details['lastname'];?>">
								</td> -->

										
									<td>Company</td>
								<td ><input type="text" name="company_business" value="<?php echo $customer_details['company'];?>"><a style="margin-left:5px" href="#" onclick="updateCompany();">Update</a>

										<?php
											if($customer_details['business_id'])
											{
												echo '<br><strong>Company Code: '.$customer_details['business_id'].'</strong>';
											}

										?>
								</td>

							</tr>
							<!-- <tr>
								<td width="15%"># of orders</td>
								<td width="35%"><?php echo $customer_details['total'] . " / ". count($orders);?></td>
							</tr> -->
							<tr>
								<td>Telephone:</td>
								<td>
									<b><?php echo $customer_details['telephone'];?></b> 
									<?php if (isset($customer_details['telephone_type'])) { ?>
									<select onchange="updateCustomer('telephone_type', this, 'inv_customers');">
										<?php foreach ($phTypes as $phType) : ?>
											<option <?php echo ($dataContact['type'] == $phType)? 'selected="selected"': ''; ?> value="<?php echo $phType; ?>"><?php echo ($phType)? $phType: '--Select--'; ?></option>
										<?php endforeach; ?>
									</select>
									<?php } ?>
								</td>
								<?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
								<td>
									Total:
									<br>
									<br>
									<hr>
									Last 6 Month
									<br>
									<br>
									<hr>									
									Last 3 Month
									<br>
									<br>
								</td>
								
								<td>
									Sale: <b class="total_dc"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a> </b>
									<br>
									Returns: <b>$<?php echo $returns_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
									<hr>
									Sale: <b class="total_dc_6"> <a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b>
									<br>
									Returns: <b>$<?php echo $returns6_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp6"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
									<hr>
									Sale: <b class="total_dc_3"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b>
									<br>
									Returns: <b>$<?php echo $returns3_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp3"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
								</td>
								<?php } else { ?>
								<td>
								</td><td></td>
								<?php } ?>
							</tr>
								<td colspan="4" align="center"><?php if ($_SESSION['create_order']): //permission for creating orders ?><a href="order_create.php?action=customer_order&firstname=<?=$customer_details['firstname'];?>&lastname=<?=$customer_details['lastname'];?>&email=<?=$customer_email;?>&telephone=<?=$customer_details['telephone'];?>" class="button">Create Order</a> <a class="fancybox3 fancybox.iframe button" href="<?php echo $host_path;?>/popupfiles/customer_cart_details.php?cust_email=<?= $customer_email; ?>">Customer Cart</a> <?php endif;?> <a target="_blank" class="button" href="customer_ledger.php?email=<?=base64_encode($customer_email);?>">Ledger CSV</a>
									<?php if($customer_details['customer_id']>0) { ?>
									<input type="button" value="Login as Customer" class="button" onclick="customerOCLogin('<?=$customer_details['customer_id'];?>','<?=md5($customer_email);?>')">
									<?php } ?>
									<a target="_blank" class="button" href="customer_orders_export.php?email=<?=base64_encode($customer_email);?>">Order History CSV</a>
								</td>
							</tr>
						</table>
						<br><br>
						<table width="70%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
							<tr>
								<td width="15%">Customer Since:</td>
								<td width="35%">
									<?php echo ($_SESSION['customer_creation_date'])? '<div><input id="datechange" onchange="updateDate(this);" data-type=datetime name="date_added" data-date-x="'. date('Y-m-d H:i', strtotime($customer_details['date_added'])) .'" value="'. $customer_details['date_added'] .'"></div>': americanDate($customer_details['date_added']);?>
									<?php if ($_SESSION['customer_creation_date']) { ?>
									<script type="text/javascript">
										$("#datechange").on("dp.change", function(e) {
											if ($("#datechange").val() != $("#datechange").data('date-x')) {
												$.ajax({
													url: '',
													type: 'POST',
													dataType: 'json',
													data: {date: $("#datechange").val(), action: 'updateAdded'},
												})
												.always(function(json) {
													// alert(json['msg']);
												});
											}
										});
									</script>
									<?php } ?>
								</td>
								<td>Email:</td>
								<td>
									<b><?php echo ($_SESSION['update_customer_email']) ? '<input type="email" data-email="'. $customer_details['email'] .'" value="'. $customer_details['email'] .'" name="updateEmail"/>' : $customer_details['email'] ;?></b>
									<?= ($_SESSION['reset_customer_password'] && $customer_details['customer_group_id'])? '<input class="button" title="Reset Password" type="button" onclick="reset_password();" value="Reset Password"/>': '';?>
									<?= ($_SESSION['c_ua_for_guest'] && $customer_details['type'] == 'guest')? '<input class="button" title="Create User" type="submit" name="createUser" value="Create Account"/>': '';?>
									<?= ($_SESSION['update_customer_email'])? '<a href="javascript:void(0)" title="Update" onclick="updateEmail(this)">Update Email</a>': '';?>
									<?= ($_SESSION['login_as'] == 'admin')? '<br><a href="javascript:void(0)" title="Merge" onclick="$(\'.mergeEmail\').toggle();">Merge Email</a>': '';?>
								</td>
							</tr>
							<tr class="mergeEmail" style="display: none;">
								<td>Merge Email:</td>
								<td>
									<input type="email" class="mergeEmailInput" data-toEmail="<?= $customer_details['email']; ?>" value="" name="mergeEmail"/>
									<a href="javascript:void(0)" class="button" title="Update" onclick="mergeEmailHistory(this)">Merge Email</a>
								</td>
								<td colspan="2" style="font-size: 9px;"><b>Note:</b> All History will be added to <b><?= $customer_details['email'] ?></b> and merging email will be deleted from opencart.</td>
							</tr>
							
								<tr>
								<td>Contacts:
								<br><br><br><br><br><br>
								</td>
								<td colspan="3">	
									<div style="height:118px;overflow-y:scroll;">
										
											<?php
											if($contacts)
											{
												foreach($contacts as $contact)
												{
											?>
										<?php echo $contact['firstname'];?> <?php echo $contact['lastname'];?><br>  <?php echo $contact['city'];?>,<br>



										 <?php echo $db->func_query_first_cell("SELECT name FROM oc_zone where zone_id='".$contact['zone_id']."'"); ?>, <?php echo $contact['zip'];?>. (<?php echo linkToProfile($contact['email']); ?>)   <hr>
										<?php
									}
								}
								else
								{
									
								}
									?>
									</div>	
								</td>
							</tr>


							<tr>
								<td>Ip Address:
								<br><br><br><br><br><br><hr>
								</td>
								<td colspan="3">	
									<div style="height:100px;overflow-y:scroll;">
										<b><?php echo $ip_address;?></b>
									</div>	
								</td>
							</tr>
							<tr>
								<td>Business License:</td>
								<td>
									<?php if ($business_license['business_license']){ ?>
											<a href="https://phonepartsusa.com/image/<?php echo $business_license['business_license'];?>" target="_blank" ><?php echo $business_license['business_license'];?></a>
										<a onclick="if (!confirm('Are you sure?')) {return false;}" href="customer_profile.php?id=<?php echo $_GET['id']; ?>&email=<?php echo $_GET['email']; ?>&action=remove_business_license">X</a><br><br>
										<strong>Uploaded by <?php if($business_license['business_license_user']){echo get_username($business_license['business_license_user']);}else{echo "Customer";}  ?> on <?php echo americanDate($business_license['business_date_updated']); ?></strong>
									<?php } else { ?>
										<div>
											<form method="post" action="" enctype="multipart/form-data">	
												<input type="file" accept=".gif,.jpg,.jpeg,.pdf" name="attachments[]" /><br><br>
												<input type="submit" class="button" name="add_business_license" value="Upload">
											</form>
										</div>
									<?php } ?>
								</td>
								<td>Tax License:</td>
								<td>
									<?php if ($tax_license['tax_license']){ ?>
									<a href="<?php echo $host_path ?>/images/<?php echo $tax_license['tax_license'];?>" target="_blank" ><?php echo $tax_license['tax_license'];?></a>
									<a onclick="if (!confirm('Are you sure?')) {return false;}" href="customer_profile.php?id=<?php echo $_GET['id']; ?>&email=<?php echo $_GET['email']; ?>&action=remove_tax_license">X</a><br><br>
									<strong>Uploaded by <?php echo get_username($tax_license['tax_license_user']); ?> on <?php echo americanDate($tax_license['tax_date_updated']); ?></strong>
									<?php } else { ?>
									<div>
										<form method="post" action="" enctype="multipart/form-data">	
											<input type="file" accept=".gif,.jpg,.jpeg,.pdf" name="attachments[]" /><br><br>
											<input type="submit" class="button" name="add_tax_license" value="Upload">
										</form>
									</div>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<td>Disable Tax:</td>
								<td>	
									<input type="checkbox" onchange="disableTax(this)" <?php if($customer_details['dis_tax']==1) echo 'checked';?> />
								</td>
								<td>Sales Agent:</td>
								<?php
								$agents =$db->func_query("SELECT id,name FROM inv_users WHERE is_sales_agent=1");
								?>
								<td>	
									<select name="user_id" style="width:150px" onChange="saveSalesAgent(this)" >
										<option value="0">None</option>
										<?php
										foreach($agents as $agent)
										{
											?>
											<option value="<?=$agent['id'];?>" <?=($agent['id']==$customer_details['user_id']?'selected':'');?>><?=$agent['name'];?></option>
											<?php
										}
										?>
									</select> 
									<span id="sales_assigned_date">
									<?php
									if($customer_details['sales_assigned_date'])
									{
										echo '('.americanDate($customer_details['sales_assigned_date']).')';
									}
									?>
									</span>
								</td>
							</tr>
							<tr>
								<?php
								if ($customer_details['name'] != 'PO Business' && $customer_details['name'] != 'Guest') { ?>
								<td>Is Termed Customer?</td>
								<td><input type="checkbox" id="is_termed" onchange="updateTermCustomer(this)" <?php if($customer_details['is_termed']==1) echo 'checked';?> /></td>
								<?php } ?>
								<!-- <td>Is Special Customer? </td> -->
								<!-- <td><input type="checkbox" id="is_special_customer" <?php if($customer_details['is_special_customer']==1) echo 'checked';?> onchange="if(this.checked){$('#special_discount').removeAttr('disabled');}else{$('#special_discount').attr('disabled','disabled');}" /> <input type="text" style="width:80px" id="special_discount" value="<?php echo $customer_details['special_discount_per'];?>" <?php if($customer_details['is_special_customer']==0) echo 'disabled';?> /> % <a href="javascript:void(0);" data-tooltip="Click to save discount" onclick="updateSpecialCustomer($('#is_special_customer'))"><img src="<?php echo $host_path;?>images/plus.png" id="special_icon" width="18" height="18"  style=" vertical-align:middle;"  /></a></td> -->
								<td>Is White-listed?</td>
								<td><input type="checkbox" id="white_list" onchange="updateWhiteList(this)" <?php if($customer_details['white_list']==1) echo 'checked';?> /></td>
							</tr>
							<?php if($_SESSION['inv_customer_is_internal'] || $_SESSION['login_as'] = 'admin') { ?>
							<tr>
								<td>Internal Account</td>
								<td><input type="checkbox" onchange="updateIsInternal(this)" id="internal_account" <?php if($customer_details['is_internal']==1) echo 'checked';?> /></td>
							</tr>
							<?php } ?>
							<?php if($customer_details['name'] == 'PO Business') { ?>
							<tr>
								<td>Password</td>
								<td><input type="text" id="customer_password" name="customer_password"  /></td>
								<td></td>
								<td></td>
							</tr>
							<?php } ?>
						</table><br><br>
						<table width="70%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;display: none;">
							<tr>
								<?php if ($customer_details['type'] == 'po' || $customer_details['type'] == 'lbb') { ?>
								<td>
									<b>Last Shipping Address</b> <br /><br />
									
									Street Address 1:<input type="text" id="oldaddress1" value="<?php echo $customer_details['address_1'];?>" > <br />
									Street Address 2:  <input type="text" id="oldaddress2" value="<?php echo $customer_details['address_2'];?>" ><br /><br />
									<b>City: </b><?php echo $customer_details['city'];?> 
									<b>State: </b> <?php echo $customer_details['state'];?>   
									<b>Zip: </b> <?php echo $customer_details['zip'];?>
								</td>
								<td>
									<b>Last Billing Address</b> <br /><br />
									Street Address 1: <?php echo $customer_details['address1'];?><br />
									Street Address 2: <?php echo $customer_details['address2'];?><br /><br />
									<b>City: </b><?php echo $customer_details['city'];?> 
									<b>State: </b> <?php echo $customer_details['state'];?>   
									<b>Zip: </b> <?php echo $customer_details['zip'];?>
								</td>
								<?php } else { ?>
								<td>
									<b>Last Shipping Address</b> <br /><br />
									Street Address 1: <?php echo $orders[0]['address1'];?><br />
									Street Address 2: <?php echo $orders[0]['address2'];?><br /><br />
									<b>City: </b><?php echo $orders[0]['city'];?> 
									<b>State: </b> <?php echo $orders[0]['state'];?>   
									<b>Zip: </b> <?php echo $orders[0]['zip'];?>
									<a type="submit" class="button " id="editAddress" name="editAddress" onclick="$.fancybox.open( $('#edit_address'), {afterClose: function(){}} )"/>Edit Address</a>
								</td>
								<td>
									<b>Last Billing Address</b> <br /><br />
									Street Address 1: <?php echo $orders[0]['bill_address1'];?><br />
									Street Address 2: <?php echo $orders[0]['bill_address2'];?><br /><br />
									<b>City: </b><?php echo $orders[0]['bill_city'];?> 
									<b>State: </b> <?php echo $orders[0]['bill_state'];?>   
									<b>Zip: </b> <?php echo $orders[0]['bill_zip'];?>
								</td>
								<?php } ?>
							</tr> 	   
						</table>
						<?php if ($customer_details['type'] != 'po' && $customer_details['type'] != 'guest' && $customer_details['type'] != 'lbb') { ?>
						<br /><br />
						
						<table width="70%" border="0">
							<tr>
								<td align="center">
									<h3 align="center">Previous Shipping Addresses</h3>
									<?php $cols = array('shipping_firstname','shipping_lastname','shipping_address_1','shipping_address_2','shipping_city','shipping_zone','shipping_country','shipping_postcode');
									$shipping_addresses = $db->func_query("Select ".implode(",", $cols)." from oc_order where customer_id = '$customer_id' group by shipping_address_1"); ?>
									<select name="shipping_dropdown">
									<option value=""> Select One</option>
									<?php foreach($shipping_addresses as $address){ ?>
										<option data-fname='<?php echo $address['shipping_firstname'];?>' data-lname='<?php echo $address['shipping_lastname'];?>' data-ad1='<?php echo $address['shipping_address_1'];?>' data-ad2='<?php echo $address['shipping_address_2'];?>' data-city='<?php echo $address['shipping_city'];?>' data-zone='<?php echo $address['shipping_zone'];?>' data-postcode='<?php echo $address['shipping_postcode'];?>' data-country='<?php echo $address['shipping_country'];?>' value=""><?php echo $address['shipping_address_1'].', '.$address['shipping_address_2'].', '.$address['shipping_city'].', '.$address['shipping_zone'].', '.$address['shipping_postcode'].', '.$address['shipping_country']; ?> </option>
										<?php } ?>
									</select>
								</td>
								<td align="center">
									<h3 align="center">Previous Billing Addresses</h3>
									<?php $cols = array('order_id','payment_firstname','payment_lastname','payment_address_1','payment_address_2','payment_city','payment_zone','payment_country','payment_postcode');
									$billing_addresses = $db->func_query("Select ".implode(",", $cols)." from oc_order where customer_id = '$customer_id' group by payment_address_1"); ?>
									<select name="billing_dropdown">
									<option value=""> Select One</option>
									<?php foreach($billing_addresses as $address){ ?>
										<option data-addid='<?php echo $address['order_id'];?>' data-fname='<?php echo $address['payment_firstname'];?>' data-lname='<?php echo $address['payment_lastname'];?>' data-ad1='<?php echo $address['payment_address_1'];?>' data-ad2='<?php echo $address['payment_address_2'];?>' data-city='<?php echo $address['payment_city'];?>' data-zone='<?php echo $address['payment_zone'];?>' data-postcode='<?php echo $address['payment_postcode'];?>' data-country='<?php echo $address['payment_country'];?>' value="" ><?php echo $address['payment_address_1'].', '.$address['payment_address_2'].', '.$address['payment_city'].', '.$address['payment_zone'].', '.$address['payment_postcode'].', '.$address['payment_country']; ?> </option>
										<?php } ?>
									</select>
								</td>
							</tr>
						</table>
						<br><br>
						<?php } ?>
						<table width="70%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
							<tr>
								<td>
									<table width="100%" border="0" align="left">
										<h3 align="center">Default Shipping Address </h3>
										<tr>
											<th>Name: </th>
											<td>
												<input type="text" name="shipping_firstname" readonly="" size="15" value="<?php echo $customer_address['firstname']; ?>" /> 
												<input type="text" name="shipping_lastname" readonly size="15" value="<?php echo $customer_address['lastname']; ?>" />  
											</td>
											 <tr>
												<th>Company</th>
												<td><input type="text" name="shipping_company" size="15" value="<?php echo $customer_address['company']; ?>" /></td>
											</tr>
											<tr>
												<th>Address : </th>
												<td>
													<input type="text" name="shipping_address1" size="15" value="<?php echo $customer_address['address_1'];?>" /> 
													<input type="text" name="shipping_address2" size="15" value="<?php echo $customer_address['address_2'];?>" /> 
												</td>
											</tr>
											<tr>
												<th>City : </th>
												<td>
													<input type="text" name="shipping_city" size="15" value="<?php echo $customer_address['city']; ?>" /> 
												</td>
											</tr>
											<tr>
												<th>State : </th>
												<td>
													<input type="text" name="shipping_state" size="15" value="<?php echo $customer_address['state']; ?>" /> 
												</td>
											</tr>
											<tr>
												<th>Country : </th>
												<td>
													<input type="text" name="shipping_country" size="15" value="<?php echo $customer_address['country']; ?>" /> 
												</td>
											</tr>
											<tr>
												<th>Zip : </th>
												<td><input type="text" name="shipping_zip" size="15" value="<?php echo $customer_address['postcode']; ?>" /> 
												</td>
											</tr>
											<tr><td colspan="2" align="center">
											<?php if($address_type == 'oc'){?>
												<input type="checkbox" onchange="changeDefaultAddress(this,'<?php echo $customer_address['address_id']; ?>')" name="default_address" value="<?php echo $customer_address['address_id'] ?>" <?php if($customer_details['address_id'] == $customer_address['address_id']) echo 'checked';?> >Default Address<br><br>
											<?php } ?>
											<input type="submit" name="update_shipping_address" class="submit" value="Update Address">
											<?php if($address_type == 'oc'){?>
											<input type="submit" name="add_shipping_address" class="submit" value="Add Address">
											<input type="submit" name="delete_shipping_address" class="submit" value="Delete Address">
											<?php } ?> 
											<input type="hidden" name="ship_address_type" value="<?php echo $address_type; ?>" >
											<input type="hidden" name="ship_address_id" value="<?php echo $address_id; ?>" ></td></tr>
										</table>
									</td>
									<td>
										<table width="100%" border="0" align="right">
											<h3 align="center">Default Billing Address </h3>
											<tr>
												<th>Name : </th>
												<td>
													<input type="text" name="bill_firstname" readonly size="15" value="<?php echo $customer_address_billing['payment_firstname']; ?>" /> 
													<input type="text" name="bill_lastname" readonly size="15" value="<?php echo $customer_address_billing['payment_lastname']; ?>" />  
												</td>
												 <tr>
													<th>Company</th>
													<td><input type="text" name="bill_company" size="15" value="<?php echo $customer_address_billing['payment_company']; ?>" /></td>
												</tr>
												<tr>
													<th>Address : </th>
													<td>
														<input type="text" name="bill_address1" size="15" value="<?php echo $customer_address_billing['payment_address_1'];?>" /> 
														<input type="text" name="bill_address2" size="15" value="<?php echo $customer_address_billing['payment_address_2'];?>" /> 
													</td>
												</tr>
												<tr>
													<th>City : </th>
													<td>
														<input type="text" name="bill_city" size="15" value="<?php echo $customer_address_billing['payment_city']; ?>" /> 
													</td>
												</tr>
												<tr>
													<th>State : </th>
													<td>
														<input type="text" name="bill_state" size="15" value="<?php echo $customer_address_billing['payment_zone']; ?>" /> 
													</td>
												</tr>
												<tr>
													<th>Country : </th>
													<td>
														<input type="text" name="bill_country" size="15" value="<?php echo $customer_address_billing['payment_country']; ?>" /> 
													</td>
												</tr>
												<tr>
													<th>Zip : </th>
													<td><input type="text" name="bill_zip" size="15" value="<?php echo $customer_address_billing['payment_postcode']; ?>" /> 
													</td>
												</tr>
												<tr><td colspan="2" align="center"><input type="submit" class="submit" name="update_billing_address" value="Update Address">
												<input type="hidden" name="bill_address_id" value="<?php echo $bill_address_id; ?>" ></td></tr>
											</table>
										</td>
							</tr> 	   
						</table>
						
						<?php if ($_SESSION['inv_customer_contact_add']) : ?>
							<!-- <a class="fancybox2 fancybox.iframe" href="addContact.php?customer_id=<?php echo $customer_email;?>">Add Contact</a> -->
						<?php endif; ?>
						<?php if ($_SESSION['inv_customer_contact_view']) : ?>
							<br><br>
							<div id="contactsx" style="width:70%;display:none">
								<h3>Contacts</h3>
								<table style="border-collapse:collapse;border:1px solid #ddd" border="1" width="100%" cellpadding="10" cellspacing="0">
									<thead>
										<tr>
											<th>Name</th>
											<th>Position</th>
											<th>Email</th>
											<th>Contacts</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($contacts as $contact) : ?>
											<tr>
												<td><?php echo $contact['first_name']; ?> <?php echo $contact['last_name']; ?></td>
												<td><?php echo $contact['position']; ?></td>
												<td><?php echo $contact['email']; ?></td>
												<td>
													<?php foreach ($contact['contacts'] as $contactx) : ?>
														<span><?php echo $contactx['type']; ?>: </span> <strong><?php echo $contactx['contact']; ?></strong><br>
													<?php endforeach;?>
												</td>
												<td><a class="fancybox2 fancybox.iframe" href="addContact.php?customer_id=<?php echo $customer_email;?>&contact_id=<?php echo $contact['id'];?>">Edit</a></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						<?php endif; ?>
					</div>
					<div id="tabCommentsAttach" class="makeTabs">
					<div>
					<a href=""> Customer service records </a>
					
					</div>
					<br>
					<table width="70%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
							<tr>
								<td>Customer Group:</td>
								<td>
									<?php if ($customer_details['name'] != 'PO Business' && $customer_details['name'] != 'Guest' && $customer_details['name'] != 'LBB') : ?>
										<select name="customer_group_id" id="customer_group_id" onChange="updateCustomerGroup()">
											<option value="">Not Defined</option>
											<?php
											$customer_groups = $db->func_query("SELECT * FROM oc_customer_group_description");
											foreach($customer_groups as $customer_group)
											{
												?>
												<option value="<?php echo $customer_group['customer_group_id'];?>" <?php echo ($customer_details['customer_group_id']==$customer_group['customer_group_id']?'selected':''); ?>><?=$customer_group['name'];?></option>
												<?php 
											}
											?>
										</select>
										<!-- <a href="javascript:void(0)" onclick="updateCustomerGroup()">Update Customer Group</a> -->
									<?php else: ?>
										<?php echo $customer_details['name']; ?>
									<?php endif; ?>
									<?php //echo $customer_details['name'];?>
								</td>
								<td>Customer Name:</td>
								<td><b><?php echo $customer_details['firstname']." ".$customer_details['lastname'];?> </b>
									<?php
									if($customer_details['customer_mood'])
									{

										?>
										<img src="images/emoji/<?php echo $customer_details['customer_mood'];?>.png" style="width: 24px">
										<?php
									}

									?>

									<input type="hidden" id="customer_name" value="<?php echo $customer_details['firstname'].' '.$customer_details['lastname'];?>">
								</td>

							</tr>
							<tr>
								<td width="15%"># of orders</td>
								<td width="35%"><?php echo $customer_details['total'] . " / ". count($orders);?></td>
							</tr>
							<tr>
								<td>Telephone:</td>
								<td>
									<b><?php echo $customer_details['telephone'];?></b> 
									<?php if (isset($customer_details['telephone_type'])) { ?>
									<select onchange="updateCustomer('telephone_type', this, 'inv_customers');">
										<?php foreach ($phTypes as $phType) : ?>
											<option <?php echo ($dataContact['type'] == $phType)? 'selected="selected"': ''; ?> value="<?php echo $phType; ?>"><?php echo ($phType)? $phType: '--Select--'; ?></option>
										<?php endforeach; ?>
									</select>
									<?php } ?>
								</td>
								<?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
								<td>
									Total:
									<br>
									<br>
									<hr>
									Last 6 Month
									<br>
									<br>
									<hr>									
									Last 3 Month
									<br>
									<br>
								</td>
								
								<td>
									Sale: <b class="total_dc"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a> </b>
									<br>
									Returns: <b>$<?php echo $returns_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
									<hr>
									Sale: <b class="total_dc_6"> <a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b>
									<br>
									Returns: <b>$<?php echo $returns6_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp6"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
									<hr>
									Sale: <b class="total_dc_3"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b>
									<br>
									Returns: <b>$<?php echo $returns3_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp3"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
								</td>
								<?php } else { ?>
								<td>
								</td><td></td>
								<?php } ?>
							</tr>
								<td colspan="4" align="center"><?php if ($_SESSION['create_order']): //permission for creating orders ?><a href="order_create.php?action=customer_order&firstname=<?=$customer_details['firstname'];?>&lastname=<?=$customer_details['lastname'];?>&email=<?=$customer_email;?>&telephone=<?=$customer_details['telephone'];?>" class="button">Create Order</a> <a class="fancybox3 fancybox.iframe button" href="<?php echo $host_path;?>/popupfiles/customer_cart_details.php?cust_email=<?= $customer_email; ?>">Customer Cart</a> <?php endif;?> <a target="_blank" class="button" href="customer_ledger.php?email=<?=base64_encode($customer_email);?>">Ledger CSV</a>
									<?php if($customer_details['customer_id']>0) { ?>
									<input type="button" value="Login as Customer" class="button" onclick="customerOCLogin('<?=$customer_details['customer_id'];?>','<?=md5($customer_email);?>')">
									<?php } ?>
									<a target="_blank" class="button" href="customer_orders_export.php?email=<?=base64_encode($customer_email);?>">Order History CSV</a>
								</td>
							</tr>
					</table>
					<br>
					<b>Autoamted email history</b>
					 
										
					<select name="types">
						<option value="all">All</option>
						<option value="rma">RMA</option>
						<option value="lbb">LBB</option>
						<option value="order">Order</option>
					</select>
					<input type="submit" name="email_type" value="Search" />
					<table width="90%" border="1" style="border-collapse:collapse;border:1px solid #ddd" cellpadding="10" cellspacing="0">
							<tr> 
							<th>Sent</th>
							<th>Subject</th>
							<th>Actions</th> 
							</tr>
							<?php foreach ($email_history as $emails) { ?>
							<tr> 
							<td align="center"><?php echo americanDate($emails['date_sent']);?></td>
							<td>
								<?php echo $emails['email_subject']; ?>
							</td>
							<td>
								<a href="<?php echo $host_path;?>/popupfiles/view_email.php?email_id=<?php echo $emails['email_report_id']?>" class="fancybox3 fancybox.iframe">View</a> /  
								<a href="<?php echo $host_path;?>customer_profile.php?id=<?php echo $_GET['id'];?>&openTab=tabCommentsAttach&email_id=<?php echo $emails['email_report_id']; ?>">Resend</a>
							</td> 
							</tr>
							<?php }?>
							
					</table>
					
					<br> 					
					
					<div>
						<table width="90%">
							<tr valign="top">
								<td width="50%">
									<form method="post" action="">
								<!--		<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;"> -->
								<table width="90%" border="1" style="border-collapse:collapse;border:1px solid #ddd" cellpadding="10" cellspacing="0">
											<tr>
												<td>
													<?php $accept_files = "image/jpeg,image/gif,image/png,application/pdf"; ?>
													<b>Attachments</b>
												</td>
												<td>
													<input type="file" name="attachment[]" accept="<?php echo $accept_files;?>" /><br />
													<input type="text" name="attachment_description[]" placeholder="Attachment Description"/><br /><br />
													<input type="file" name="attachment[]" accept="<?php echo $accept_files;?>" /><br />
													<input type="text" name="attachment_description[]" placeholder="Attachment Description" /><br /><br />
													<input type="file" name="attachment[]" accept="<?php echo $accept_files;?>" /><br />
													<input type="text" name="attachment_description[]" placeholder="Attachment Description" /><br /><br />
													<input type="file" name="attachment[]" accept="<?php echo $accept_files;?>" /><br />
													<input type="text" name="attachment_description[]" placeholder="Attachment Description" /><br /><br />
													<input type="file" name="attachment[]" accept="<?php echo $accept_files;?>" /><br />
													<input type="text" name="attachment_description[]" placeholder="Attachment Description" /><br /><br />
												</td>
											</tr>
											<tr>
												<td colspan="2" align="center"><input type="submit" value="Attach Files" name="submit" /></td>
											</tr> 	   
										</table>
									</form>
								</td>
								
								<td valign="top">
									<h2 align="center">Comment History</h2>
									<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
										<tr>
											<th>Date</th>
											<th>Comment</th>
											<th>Type</th>
											<th>Added By</th>
										</tr>
										<?php $comments = $db->func_query("SELECT * FROM inv_customer_comments WHERE email='".$customer_email."'"); ?>
										<?php foreach($comments as $comment) { ?>
										<tr>
											<td><?php echo americanDate($comment['date_added']);?></td>
											<td><?php echo $comment['comments'];?></td>
											<td><?php echo $comment['comment_type'];?></td>
											<td><?php echo get_username($comment['user_id']);?></td>
										</tr>
										<?php } ?>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<h2 align="center">Attachments</h2>
									<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
										<tr>
											<th>Date</th>
											<th>Description</th>
											<th>File Type</th>
											<th>Added By</th>
											<th>Action</th>
										</tr>
										<?php $customer_docs = $db->func_query("SELECT * FROM inv_customer_files WHERE email='".$customer_email."'"); ?>
										<?php foreach($customer_docs as $customer_doc) { ?>
										<tr>
											<td><?php echo americanDate($customer_doc['date_added']);?></td>
											<td><?php echo $customer_doc['description'];?></td>
											<td><?php echo $customer_doc['file_mime'];?></td>
											<td><?php echo get_username($customer_doc['user_id']);?></td>
											<td><a target="_blank" href="<?php echo $host_path ."". $customer_doc['path'];?>">Download</a> |  <a href="customer_profile.php?action=delete_doc&doc_id=<?php echo $customer_doc['doc_id']?>&id=<?php echo $_GET['id'];?>&email=<?=$_GET['email'];?>" onclick="if(!confirm('Are you sure, You want to delete this file?')){ return false; }">Delete</a></td>
										</tr>
										<?php } ?>
									</table>	
								</td>
								<td width="50%">
									<form method="post" action="">
										<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
											<tr>
												<td>
													<b>Comment</b>
												</td>
												<td>
													<textarea rows="5" cols="50" name="comment" required></textarea>
												</td>
											</tr>
											<tr>
												<td>Type:</td>
												<td>
													<select name="comment_type" id="comment_type" required>
														<option value="">Select Type</option>
														<option value="Sales Call">Sales Call</option>
														<option value="Complaint">Complaint</option>
														<option value="Incident">Incident</option>
													</select>
												</td>
											</tr>
											<tr>
												<td colspan="2" align="center"> <input type="submit" class="button" name="addcomment" value="Add Comment" />	</td>
											</tr> 	   
										</table>
									</form>
								</td>
							</tr>
						</table>
						</div>
					</div>
					<div id="tabOrders" class="makeTabs">
					<table width="70%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
							<tr>
								<td>Customer Group:</td>
								<td>
									<?php if ($customer_details['name'] != 'PO Business' && $customer_details['name'] != 'Guest' && $customer_details['name'] != 'LBB') : ?>
										<select name="customer_group_id" id="customer_group_id" onChange="updateCustomerGroup()">
											<option value="">Not Defined</option>
											<?php
											$customer_groups = $db->func_query("SELECT * FROM oc_customer_group_description");
											foreach($customer_groups as $customer_group)
											{
												?>
												<option value="<?php echo $customer_group['customer_group_id'];?>" <?php echo ($customer_details['customer_group_id']==$customer_group['customer_group_id']?'selected':''); ?>><?=$customer_group['name'];?></option>
												<?php 
											}
											?>
										</select>
										<!-- <a href="javascript:void(0)" onclick="updateCustomerGroup()">Update Customer Group</a> -->
									<?php else: ?>
										<?php echo $customer_details['name']; ?>
									<?php endif; ?>
									<?php //echo $customer_details['name'];?>
								</td>
								<td>Customer Name:</td>
								<td><b><?php echo $customer_details['firstname']." ".$customer_details['lastname'];?> </b>
									<?php
									if($customer_details['customer_mood'])
									{

										?>
										<img src="images/emoji/<?php echo $customer_details['customer_mood'];?>.png" style="width: 24px">
										<?php
									}

									?>

									<input type="hidden" id="customer_name" value="<?php echo $customer_details['firstname'].' '.$customer_details['lastname'];?>">
								</td>

							</tr>
							<tr>
								<td width="15%"># of orders</td>
								<td width="35%"><?php echo $customer_details['total'] . " / ". count($orders);?></td>
							</tr>
							<tr>
								<td>Telephone:</td>
								<td>
									<b><?php echo $customer_details['telephone'];?></b> 
									<?php if (isset($customer_details['telephone_type'])) { ?>
									<select onchange="updateCustomer('telephone_type', this, 'inv_customers');">
										<?php foreach ($phTypes as $phType) : ?>
											<option <?php echo ($dataContact['type'] == $phType)? 'selected="selected"': ''; ?> value="<?php echo $phType; ?>"><?php echo ($phType)? $phType: '--Select--'; ?></option>
										<?php endforeach; ?>
									</select>
									<?php } ?>
								</td>
								<?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
								<td>
									Total:
									<br>
									<br>
									<hr>
									Last 6 Month
									<br>
									<br>
									<hr>									
									Last 3 Month
									<br>
									<br>
								</td>
								
								<td>
									Sale: <b class="total_dc"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a> </b>
									<br>
									Returns: <b>$<?php echo $returns_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
									<hr>
									Sale: <b class="total_dc_6"> <a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b>
									<br>
									Returns: <b>$<?php echo $returns6_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp6"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
									<hr>
									Sale: <b class="total_dc_3"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b>
									<br>
									Returns: <b>$<?php echo $returns3_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp3"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
								</td>
								<?php } else { ?>
								<td>
								</td><td></td>
								<?php } ?>
							</tr>
								<td colspan="4" align="center"><?php if ($_SESSION['create_order']): //permission for creating orders ?><a href="order_create.php?action=customer_order&firstname=<?=$customer_details['firstname'];?>&lastname=<?=$customer_details['lastname'];?>&email=<?=$customer_email;?>&telephone=<?=$customer_details['telephone'];?>" class="button">Create Order</a> <a class="fancybox3 fancybox.iframe button" href="<?php echo $host_path;?>/popupfiles/customer_cart_details.php?cust_email=<?= $customer_email; ?>">Customer Cart</a> <?php endif;?> <a target="_blank" class="button" href="customer_ledger.php?email=<?=base64_encode($customer_email);?>">Ledger CSV</a>
									<?php if($customer_details['customer_id']>0) { ?>
									<input type="button" value="Login as Customer" class="button" onclick="customerOCLogin('<?=$customer_details['customer_id'];?>','<?=md5($customer_email);?>')">
									<?php } ?>
									<a target="_blank" class="button" href="customer_orders_export.php?email=<?=base64_encode($customer_email);?>">Order History CSV</a>
								</td>
							</tr>
					</table>
						<?php if ($_SESSION['display_cost']) : ?>
							<br><br>
							<div style="width:70%;">
								<h3>Amount Details</h3>
								<table style="border-collapse:collapse;border:1px solid #ddd" border="1" width="100%" cellpadding="10" cellspacing="0">
									<tbody>
										<tr>
											<th style="background-color: black;color: white">Ordering Total:</th>
											<td style="background-color: black;color: white" id="total_credit">$<?= number_format($total_credit,2); ?></td>
											<th style="background-color: black;color: white">Payments Total:</th>
											<td style="background-color: black;color: white" id="total_debit">$<?= number_format($total_debit,2); ?></td>
											<th style="background-color: black;color: white">Total Due:</th>
											<td style="background-color: black;color: white"><span id="span_balance">$<?= number_format($balance,2); ?></span></td>
										</tr>
										<?php if($balance>0) { ?>
										<tr>
											<th colspan="3" align="right">Apply Voucher:</th>
											<th colspan="3" align="left"><input type="text" id="apply_voucher" > <input type="button" class="button" value="Apply" onclick="applyVoucher(this);"></th>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						<?php endif; ?>
													
							<h3>Orders</h3>
							<div style="max-height:800px;overflow:scroll;width:70%;">
								<table width="100%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
									<tr>
										<th></th>
										<th>#</th>
										<th>Added</th>
										<th>Order ID</th>
										<th>Status</th>
										<th>Tracking No.</th>
										<th>Comments</th>
										<!-- <th>Attachments</th> -->
										<?php if($_SESSION['display_cost']) {?>
										<th>SubTotal</th>
										<th>Other Charges</th>
										<th>Shipping</th>
										<?php } ?>	
									</tr>
									<tbody id="order_table_body">
										
									</tbody>
									<!-- <?php echo $orderData; ?> -->
								</table>
							</div>
							<br>
							<a href="javascript:void(0);" class="fancyboxX3 fancybox.iframe button" id="bulk_pay_order">Pay Orders</a>
							<br>
						
					</div>
					<div id="tabReturns" class="makeTabs">
					<table width="70%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
							<tr>
								<td>Customer Group:</td>
								<td>
									<?php if ($customer_details['name'] != 'PO Business' && $customer_details['name'] != 'Guest' && $customer_details['name'] != 'LBB') : ?>
										<select name="customer_group_id" id="customer_group_id" onChange="updateCustomerGroup()">
											<option value="">Not Defined</option>
											<?php
											$customer_groups = $db->func_query("SELECT * FROM oc_customer_group_description");
											foreach($customer_groups as $customer_group)
											{
												?>
												<option value="<?php echo $customer_group['customer_group_id'];?>" <?php echo ($customer_details['customer_group_id']==$customer_group['customer_group_id']?'selected':''); ?>><?=$customer_group['name'];?></option>
												<?php 
											}
											?>
										</select>
										<!-- <a href="javascript:void(0)" onclick="updateCustomerGroup()">Update Customer Group</a> -->
									<?php else: ?>
										<?php echo $customer_details['name']; ?>
									<?php endif; ?>
									<?php //echo $customer_details['name'];?>
								</td>
								<td>Customer Name:</td>
								<td><b><?php echo $customer_details['firstname']." ".$customer_details['lastname'];?> </b>
									<?php
									if($customer_details['customer_mood'])
									{

										?>
										<img src="images/emoji/<?php echo $customer_details['customer_mood'];?>.png" style="width: 24px">
										<?php
									}

									?>

									<input type="hidden" id="customer_name" value="<?php echo $customer_details['firstname'].' '.$customer_details['lastname'];?>">
								</td>

							</tr>
							<tr>
								<td width="15%"># of orders</td>
								<td width="35%"><?php echo $customer_details['total'] . " / ". count($orders);?></td>
							</tr>
							<tr>
								<td>Telephone:</td>
								<td>
									<b><?php echo $customer_details['telephone'];?></b> 
									<?php if (isset($customer_details['telephone_type'])) { ?>
									<select onchange="updateCustomer('telephone_type', this, 'inv_customers');">
										<?php foreach ($phTypes as $phType) : ?>
											<option <?php echo ($dataContact['type'] == $phType)? 'selected="selected"': ''; ?> value="<?php echo $phType; ?>"><?php echo ($phType)? $phType: '--Select--'; ?></option>
										<?php endforeach; ?>
									</select>
									<?php } ?>
								</td>
								<?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
								<td>
									Total:
									<br>
									<br>
									<hr>
									Last 6 Month
									<br>
									<br>
									<hr>									
									Last 3 Month
									<br>
									<br>
								</td>
								
								<td>
									Sale: <b class="total_dc"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a> </b>
									<br>
									Returns: <b>$<?php echo $returns_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
									<hr>
									Sale: <b class="total_dc_6"> <a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b>
									<br>
									Returns: <b>$<?php echo $returns6_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp6"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
									<hr>
									Sale: <b class="total_dc_3"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b>
									<br>
									Returns: <b>$<?php echo $returns3_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp3"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
								</td>
								<?php } else { ?>
								<td>
								</td><td></td>
								<?php } ?>
							</tr>
								<td colspan="4" align="center"><?php if ($_SESSION['create_order']): //permission for creating orders ?><a href="order_create.php?action=customer_order&firstname=<?=$customer_details['firstname'];?>&lastname=<?=$customer_details['lastname'];?>&email=<?=$customer_email;?>&telephone=<?=$customer_details['telephone'];?>" class="button">Create Order</a> <a class="fancybox3 fancybox.iframe button" href="<?php echo $host_path;?>/popupfiles/customer_cart_details.php?cust_email=<?= $customer_email; ?>">Customer Cart</a> <?php endif;?> <a target="_blank" class="button" href="customer_ledger.php?email=<?=base64_encode($customer_email);?>">Ledger CSV</a>
									<?php if($customer_details['customer_id']>0) { ?>
									<input type="button" value="Login as Customer" class="button" onclick="customerOCLogin('<?=$customer_details['customer_id'];?>','<?=md5($customer_email);?>')">
									<?php } ?>
									<a target="_blank" class="button" href="customer_orders_export.php?email=<?=base64_encode($customer_email);?>">Order History CSV</a>
								</td>
							</tr>
					</table>
						<?php if ($customer_details['customer_group_id']) { ?>
						<?php
						$permission = $db->func_query("SELECT privilege_id FROM inv_customer_group_privilege WHERE group_id = '" . $customer_details['customer_group_id'] . "'");
						$perm = array();
						foreach ($permission as $value) {
							$perm[] = $value['privilege_id'];
						}
						$types = $db->func_query("SELECT * FROM inv_privilege_type order by privilege_type_id ASC");
						?>
						<br /><br />
						<font align="center" style="font-size: large;">Return Privileges</font>
						<table id="list" align="center" border="0" width="70%" cellpadding="10" cellspacing="0" style="border:0px solid #585858;border-collapse:collapse;">
							<tbody valign="top">
								<tr>
									<?php foreach ($types as $k => $type) { ?>
									<td style="border-right: 1px solid;">
										<div>
											<h2><?php echo $type['name']; ?></h2>
											<table align="center" border="0" width="100%" cellpadding="10" cellspacing="0" style="border:0px solid #585858;border-collapse:collapse;">
												<tbody valign="top">
													<?php foreach ($db->func_query("SELECT * FROM inv_privilege WHERE privilege_type_id = '". $type['privilege_type_id'] ."'") as $pk => $privilege): ?>
														<tr>
															<td>
																<label>
																	<?php if ($type['type'] == 'single') { ?>
																	<input type="radio" onclick="event.preventDefault();" <?php echo (in_array($privilege['privilege_id'], $perm))? 'checked=""': ''; ?> value="<?php echo $privilege['privilege_id']; ?>">
																	<?php } else { ?>
																	<input type="checkbox" onclick="event.preventDefault();" <?php echo (in_array($privilege['privilege_id'], $perm))? 'checked=""': ''; ?> value="<?php echo $privilege['privilege_id']; ?>">
																	<?php } ?>
																	<?php echo $privilege['name']; ?>
																</label>
															</td>
														</tr>
													<?php endforeach ?>
												</tbody>
											</table>
										</div>
									</td>
									<?php } ?>
								</tr>
							</tbody>
						</table>
						<?php } ?>
						<?php $exceptions = $db->func_query("SELECT * FROM inv_exception_list WHERE email='$customer_email'"); ?>
						<?php if($exceptions) : ?>
							<br /><br />
							<h2 align="center">RMA Exceptions</h2>
							<div style="max-height:800px;overflow:scroll;width:70%;">
								<table width="100%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
									<tr>
										<th>Date</th>
										<th>RMA #</th>
										<th>Order ID</th>
										<th>SKU</th>
									</tr>
									<?php foreach ($exceptions as $exception) { ?>
									<?php $_rma = $db->func_query_first("SELECT * FROM inv_returns WHERE id='".$exception['return_id']."'"); ?>
									<tr>
										<td>
											<?= americanDate($exception['date_added']);?>
										</td>
										<td>
											<?= linkToRma($_rma['rma_number'],$host_path);?>
										</td>
										<td>
											<?= linkToOrder($_rma['order_id'],$host_path);?>
										</td>
										<td>
											<?= linkToProduct($exception['sku'],$host_path);?>
										</td>
									</tr>
									<?php } ?>
								</table>
							</div>	
							<br /><br />
						<?php endif; ?>
						<?php if($rma_returns): ?>
							<br /><br />
							<h2 align="center">Customer Returns</h2>
							<table width="70%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
								<tr>
									<th>Added</th>
									<th>Order ID</th>
									<th>RMA #</th>
									<th>Status</th>
									<th>Items Returned</th>
									<?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
									<th>Amount</th>
									<?php } ?>
									<th>PPUSA Paid Shippping</th>
									<th>Comments</th>
								</tr>
								<?php echo $rma_returns_html; ?>
							</table>	
							<br /><br />
						<?php endif;?>
					</div>
					<div id="tabBuyBack" class="makeTabs">
					<table width="70%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
							<tr>
								<td>Customer Group:</td>
								<td>
									<?php if ($customer_details['name'] != 'PO Business' && $customer_details['name'] != 'Guest' && $customer_details['name'] != 'LBB') : ?>
										<select name="customer_group_id" id="customer_group_id" onChange="updateCustomerGroup()">
											<option value="">Not Defined</option>
											<?php
											$customer_groups = $db->func_query("SELECT * FROM oc_customer_group_description");
											foreach($customer_groups as $customer_group)
											{
												?>
												<option value="<?php echo $customer_group['customer_group_id'];?>" <?php echo ($customer_details['customer_group_id']==$customer_group['customer_group_id']?'selected':''); ?>><?=$customer_group['name'];?></option>
												<?php 
											}
											?>
										</select>
										<!-- <a href="javascript:void(0)" onclick="updateCustomerGroup()">Update Customer Group</a> -->
									<?php else: ?>
										<?php echo $customer_details['name']; ?>
									<?php endif; ?>
									<?php //echo $customer_details['name'];?>
								</td>
								<td>Customer Name:</td>
								<td><b><?php echo $customer_details['firstname']." ".$customer_details['lastname'];?> </b>
									<?php
									if($customer_details['customer_mood'])
									{

										?>
										<img src="images/emoji/<?php echo $customer_details['customer_mood'];?>.png" style="width: 24px">
										<?php
									}

									?>

									<input type="hidden" id="customer_name" value="<?php echo $customer_details['firstname'].' '.$customer_details['lastname'];?>">
								</td>

							</tr>
							<tr>
								<td width="15%"># of orders</td>
								<td width="35%"><?php echo $customer_details['total'] . " / ". count($orders);?></td>
							</tr>
							<tr>
								<td>Telephone:</td>
								<td>
									<b><?php echo $customer_details['telephone'];?></b> 
									<?php if (isset($customer_details['telephone_type'])) { ?>
									<select onchange="updateCustomer('telephone_type', this, 'inv_customers');">
										<?php foreach ($phTypes as $phType) : ?>
											<option <?php echo ($dataContact['type'] == $phType)? 'selected="selected"': ''; ?> value="<?php echo $phType; ?>"><?php echo ($phType)? $phType: '--Select--'; ?></option>
										<?php endforeach; ?>
									</select>
									<?php } ?>
								</td>
								<?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
								<td>
									Total:
									<br>
									<br>
									<hr>
									Last 6 Month
									<br>
									<br>
									<hr>									
									Last 3 Month
									<br>
									<br>
								</td>
								
								<td>
									Sale: <b class="total_dc"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a> </b>
									<br>
									Returns: <b>$<?php echo $returns_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
									<hr>
									Sale: <b class="total_dc_6"> <a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b>
									<br>
									Returns: <b>$<?php echo $returns6_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp6"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
									<hr>
									Sale: <b class="total_dc_3"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b>
									<br>
									Returns: <b>$<?php echo $returns3_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp3"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
								</td>
								<?php } else { ?>
								<td>
								</td><td></td>
								<?php } ?>
							</tr>
								<td colspan="4" align="center"><?php if ($_SESSION['create_order']): //permission for creating orders ?><a href="order_create.php?action=customer_order&firstname=<?=$customer_details['firstname'];?>&lastname=<?=$customer_details['lastname'];?>&email=<?=$customer_email;?>&telephone=<?=$customer_details['telephone'];?>" class="button">Create Order</a> <a class="fancybox3 fancybox.iframe button" href="<?php echo $host_path;?>/popupfiles/customer_cart_details.php?cust_email=<?= $customer_email; ?>">Customer Cart</a> <?php endif;?> <a target="_blank" class="button" href="customer_ledger.php?email=<?=base64_encode($customer_email);?>">Ledger CSV</a>
									<?php if($customer_details['customer_id']>0) { ?>
									<input type="button" value="Login as Customer" class="button" onclick="customerOCLogin('<?=$customer_details['customer_id'];?>','<?=md5($customer_email);?>')">
									<?php } ?>
									<a target="_blank" class="button" href="customer_orders_export.php?email=<?=base64_encode($customer_email);?>">Order History CSV</a>
								</td>
							</tr>
					</table>
						<?php if($buybacks) : ?>
							<br /><br />
							<h2 align="center">Buy Back Shipments</h2>
							<div style="max-height:800px;overflow:scroll;width:70%;">
								<table width="100%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
									<thead>
										<tr>
											<th width="12%">
												Added
											</th>
											<th width="12%">
												Received
											</th>
											<th width="12%">
												Date QC
											</th>
											<th width="7%">
												Shipment #
											</th>
											<th width="10%">
												Payment Type
											</th>
											<?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
											<th width="7%">
												Total
											</th>
											<?php } ?>
											<th width="10%">
												Status
											</th>
											<th colspan="2" width="15%">
												Action
											</th>
										</tr>
									</thead>
									<tbody>
										<!-- Showing All REcord -->
										<?php foreach ($buybacks as $i => $row) { ?>
										<tr>
											<td>
												<?php echo americanDate($row['date_added']);?>
											</td>
											<td>
												<?php echo americanDate($row['date_received']);?>
											</td>
											<td>
												<?php echo americanDate($row['date_qc']);?>
											</td>
											<td>
												<a href="<?=$host_path;?>buyback/shipment_detail.php?shipment=<?php echo $row['shipment_number'];?>"><?= $row['shipment_number']; ?></a>
											</td>
											<td>
												<?php echo ($row['payment_type']);?>
											</td>
											<?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
											<td>
												<?php echo '$'.(number_format($row['total'],2));?>
											</td>
											<?php } ?>
											<td>
												<?php echo $row['status'];?>
											</td>
											<td>
												<a href="<?=$host_path;?>buyback/shipment_detail.php?shipment=<?php echo $row['shipment_number'];?>">View</a>
											</td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						<?php endif;?>
					</div>
					<div id="tabVouchers" class="makeTabs">
					<table width="70%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
							<tr>
								<td>Customer Group:</td>
								<td>
									<?php if ($customer_details['name'] != 'PO Business' && $customer_details['name'] != 'Guest' && $customer_details['name'] != 'LBB') : ?>
										<select name="customer_group_id" id="customer_group_id" onChange="updateCustomerGroup()">
											<option value="">Not Defined</option>
											<?php
											$customer_groups = $db->func_query("SELECT * FROM oc_customer_group_description");
											foreach($customer_groups as $customer_group)
											{
												?>
												<option value="<?php echo $customer_group['customer_group_id'];?>" <?php echo ($customer_details['customer_group_id']==$customer_group['customer_group_id']?'selected':''); ?>><?=$customer_group['name'];?></option>
												<?php 
											}
											?>
										</select>
										<!-- <a href="javascript:void(0)" onclick="updateCustomerGroup()">Update Customer Group</a> -->
									<?php else: ?>
										<?php echo $customer_details['name']; ?>
									<?php endif; ?>
									<?php //echo $customer_details['name'];?>
								</td>
								<td>Customer Name:</td>
								<td><b><?php echo $customer_details['firstname']." ".$customer_details['lastname'];?> </b>
									<?php
									if($customer_details['customer_mood'])
									{

										?>
										<img src="images/emoji/<?php echo $customer_details['customer_mood'];?>.png" style="width: 24px">
										<?php
									}

									?>

									<input type="hidden" id="customer_name" value="<?php echo $customer_details['firstname'].' '.$customer_details['lastname'];?>">
								</td>

							</tr>
							<tr>
								<td width="15%"># of orders</td>
								<td width="35%"><?php echo $customer_details['total'] . " / ". count($orders);?></td>
							</tr>
							<tr>
								<td>Telephone:</td>
								<td>
									<b><?php echo $customer_details['telephone'];?></b> 
									<?php if (isset($customer_details['telephone_type'])) { ?>
									<select onchange="updateCustomer('telephone_type', this, 'inv_customers');">
										<?php foreach ($phTypes as $phType) : ?>
											<option <?php echo ($dataContact['type'] == $phType)? 'selected="selected"': ''; ?> value="<?php echo $phType; ?>"><?php echo ($phType)? $phType: '--Select--'; ?></option>
										<?php endforeach; ?>
									</select>
									<?php } ?>
								</td>
								<?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
								<td>
									Total:
									<br>
									<br>
									<hr>
									Last 6 Month
									<br>
									<br>
									<hr>									
									Last 3 Month
									<br>
									<br>
								</td>
								
								<td>
									Sale: <b class="total_dc"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a> </b>
									<br>
									Returns: <b>$<?php echo $returns_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
									<hr>
									Sale: <b class="total_dc_6"> <a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b>
									<br>
									Returns: <b>$<?php echo $returns6_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp6"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
									<hr>
									Sale: <b class="total_dc_3"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b>
									<br>
									Returns: <b>$<?php echo $returns3_total; ?></b> <span style="margin-left: 30px;"> Return Percent: <b class="rp3"><a class="loader_totals" id="loader_totals" style="display: none;" href="javascript:void(0);"><img src="images/loading.gif" style="width: 15px;"></a></b> </span>
								</td>
								<?php } else { ?>
								<td>
								</td><td></td>
								<?php } ?>
							</tr>
								<td colspan="4" align="center"><?php if ($_SESSION['create_order']): //permission for creating orders ?><a href="order_create.php?action=customer_order&firstname=<?=$customer_details['firstname'];?>&lastname=<?=$customer_details['lastname'];?>&email=<?=$customer_email;?>&telephone=<?=$customer_details['telephone'];?>" class="button">Create Order</a> <a class="fancybox3 fancybox.iframe button" href="<?php echo $host_path;?>/popupfiles/customer_cart_details.php?cust_email=<?= $customer_email; ?>">Customer Cart</a> <?php endif;?> <a target="_blank" class="button" href="customer_ledger.php?email=<?=base64_encode($customer_email);?>">Ledger CSV</a>
									<?php if($customer_details['customer_id']>0) { ?>
									<input type="button" value="Login as Customer" class="button" onclick="customerOCLogin('<?=$customer_details['customer_id'];?>','<?=md5($customer_email);?>')">
									<?php } ?>
									<a target="_blank" class="button" href="customer_orders_export.php?email=<?=base64_encode($customer_email);?>">Order History CSV</a>
								</td>
							</tr>
					</table>
						<?php if($vouchers) : ?>
							<br /><br />
							<h2 align="center">Customer Vouchers</h2>
							<div style="max-height:800px;overflow:scroll;width:70%;">
								<table width="100%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd">
									<tr>
										<th>Code</th>
										<th>To</th>
										<?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
										<th>Amount</th>
										<th>Available</th>
										<?php } ?>
										<th>Created By</th>
										<th>Status</th>
										<th>Data Added</th>
									</tr>
									<?php foreach ($vouchers as $voucher) { ?>
									<?php $balance = ((float) $voucher['amount']) + ((float) $db->func_query_first_cell("SELECT SUM(amount) FROM oc_voucher_history WHERE voucher_id='".$voucher['voucher_id']."'")); ?>
									<tr>
										<td>
											<?= linkToVoucher($voucher['voucher_id'],$host_path,$voucher['code']);?>
										</td>
										<td>
											<?= $voucher['to_email'];?>
										</td>
										<?php if($_SESSION['display_customer_price'] || $_SESSION['login_as'] == 'admin'){ ?>
										<td>
											$<?= number_format($voucher['amount'], 2);?>
										</td>
										<td>
											$<?= number_format($balance, 2);?>
										</td>
										<?php } ?>
										<td>
											<?= $voucher['from_name'];?>
										</td>
										<td>
											<?= ($voucher['status'] == '1')? 'Enabled': 'Disabled';?>
										</td>
										<td>
											<?= americanDate($voucher['date_added']);?>
										</td>
									</tr>
									<?php } ?>
								</table>
							</div>	
							<br /><br />
						<?php endif; ?>
					</div>
				
				<div style="text-align: right;display:none;" id="edit_address" >
				<form>
				<h2 style="text-align: left;">Default Address</h2>
				Address book Entries <br/><br/>
				<div id="addr">
				<?php $i=-1; ?>
				<?php foreach($address1 as $add1 ) :?>
				<input type="radio" name="add" value="<?php echo $add1['address_1'];?>"><?php echo $add1['firstname']; echo(" "); echo $add1['lastname']; echo("|"); echo $add1['address_1']; echo(",");echo $add1['address_2']; ?>
				
				<input type="hidden" id="adres1" name="adres1" value="<?php echo $add1['address_1']; ?>" />
				<input type="hidden" id="adres2" name="adres2" value="<?php echo $add1['address_2']; ?>" />
				<a class="button fancybox2 fancybox.iframe" href="editAddress.php?customer_id=<?php echo $add1['address_id'];?>">Edit</a>	
				<a class="button" href="customer_profile.php?email=<?php echo $_GET['email']; ?>&address_id=<?php echo $add1['address_id'];?>"  name="delete_address" >Delete</a>				
				&nbsp&nbsp
			  <br/><br/>
			  <?php $i=$i+1; ?>
				<?php endforeach; ?>
				</form>
				</div>
				<br/><br/>
				<div style="text-align: right;">
				<a class="button fancybox2 fancybox.iframe" href="editAddress.php?c_id=<?php echo $add1['address_id'];?>">New Address</a>
							
						
				<a class="button" href="customer_profile.php?customer_id=<?php echo $address1[$i]['customer_id'];?>&address_id=<?php echo $address1[$i]['address_id'];?>"  name="save_changes" >Save Changes</a>
						
							</div>
							</div>
				<div id="new_address" style="display:none;">
				<h2>Edit Address</h2>
				<form id="formEditAddress" >
					<label>First Name</label><br/>
					<input type="text" name="firstName">
					<br/>
					<label>Last Name</label><br/>
					<input type="text" name="lastName">
					<br/>
					<label>Company</label><br/>
					<input type="text" name="company">
					<br/>
					<label>Address 1</label><br/>
					<input type="text" name="addres_1">
					<br/>
					<label>Address 2</label><br/>
					<input type="text" name="addres_2">
					<br/>
					<label>City</label><br/>
					<input type="text" name="city">
					<br/>
					<label>Postal Code</label><br/>
					<input type="text" name="postalCode">
					<br/>
					<label>Country</label><br/>
					<select name="country">
							<option value="">--Select--</option>
							<?php foreach($db->func_query("SELECT * FROM `oc_country`") as $country):?>
								<option value="<?php echo $country['country_id'];?>" <?php if($country['country_id'] == $user['country']):?> selected="selected" <?php endif;?>><?php echo $country['name'];?></option>
							<?php endforeach;?>
						</select>
					<br/>
					<label>Region/State</label><br/>
					<select name="providence" >
							<option value="">--Select--</option>
							<?php foreach($db->func_query("SELECT * FROM `oc_zone` WHERE `country_id` = '". $user['country'] ."'") as $zone):?>
								<option value="<?php echo $zone['name'];?>" <?php if($zone['zone_id'] == $user['providence']):?> selected="selected" <?php endif;?>><?php echo $zone['name'];?></option>
							<?php endforeach;?>
						</select>
						<br/>
					<input type="submit" value="Save changes"> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
					<input type="submit" value="Cancel">
				</form>
					
				</div>
			</div>
		</div>
	</div>
</body>
</html>   	
<script>
$('[name="shipping_dropdown"]').change(function(){
    $('[name="shipping_firstname"]').val($(this).find(':selected').data('fname'));
    $('[name="shipping_lastname"]').val($(this).find(':selected').data('lname'));
    $('[name="shipping_address1"]').val($(this).find(':selected').data('ad1'));
    $('[name="shipping_address2"]').val($(this).find(':selected').data('ad2'));
    $('[name="shipping_city"]').val($(this).find(':selected').data('city'));
    $('[name="shipping_state"]').val($(this).find(':selected').data('zone'));
    $('[name="shipping_zip"]').val($(this).find(':selected').data('postcode'));
    $('[name="shipping_country"]').val($(this).find(':selected').data('country'));
});
$('[name="billing_dropdown"]').change(function(){
    $('[name="bill_address_id"]').val($(this).find(':selected').data('addid'));
    $('[name="bill_firstname"]').val($(this).find(':selected').data('fname'));
    $('[name="bill_lastname"]').val($(this).find(':selected').data('lname'));
    $('[name="bill_address1"]').val($(this).find(':selected').data('ad1'));
    $('[name="bill_address2"]').val($(this).find(':selected').data('ad2'));
    $('[name="bill_city"]').val($(this).find(':selected').data('city'));
    $('[name="bill_state"]').val($(this).find(':selected').data('zone'));
    $('[name="bill_zip"]').val($(this).find(':selected').data('postcode'));
    $('[name="bill_country"]').val($(this).find(':selected').data('country'));
});
	function updateCustomerGroup() {
		if($('#customer_group_id').val()=='')
		{
			alert("Please select customer group first"); 
			return false;
		}
		jQuery.ajax({
			url: 'customer_profile.php?&action=update_customer_group',
			type:"POST",
			data:{customer_group_id:$('#customer_group_id').val(),customer_id:'<?php echo $customer_id;?>'},
			success: function(data){
				alert('Customer Group Updated');
			}
		}); 
	}
	function updateTermCustomer(obj)
	{
		var status = $(obj).is(':checked');
		$.ajax({
			url: 'customer_profile.php?id=<?php echo $_GET['id'];?>&email=<?=$_GET['email'];?>',
			type: 'post',
			data: {action: 'update_term_customer',is_termed:status},
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (data) {
			}
		});
	}
	function disableTax(obj) {
		var status = $(obj).is(':checked');
		$.ajax({
			url: 'customer_profile.php?id=<?php echo $_GET['id'];?>&email=<?=$_GET['email'];?>',
			type: 'post',
			data: {action: 'disableTax',dis_tax:status},
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (data) {
			}
		});
	}
	function changeDefaultAddress(obj,address_id) {
		var status = $(obj).is(':checked');
		$.ajax({
			url: 'customer_profile.php?id=<?php echo $_GET['id'];?>&email=<?=$_GET['email'];?>',
			type: 'post',
			data: {action: 'changeDefaultAddress',status:status, address_id: address_id},
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (data) {
			}
		});
	}
	function saveSalesAgent(obj) {
		var agent = $(obj).val();
		if(!confirm('Are you sure want to update the sales agent?'))
		{
			return false;
		}
		$.ajax({
			url: 'customer_profile.php?id=<?php echo $_GET['id'];?>&email=<?=$_GET['email'];?>',
			type: 'post',
			data: {action: 'updateSalesAgent',user_id:agent},
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (data) {
				alert('Sales Agent Modified');
				$('#sales_assigned_date').html('('+data+')');
			}
		});
	}
	var balance = '<?=$balance;?>';
	function applyVoucher(obj)
	{
		if($('#apply_voucher').val()=='')
		{
			alert("Please provide a store credit code before proceed");
			return false;
		}
		if(!confirm('Are you sure want to apply store credit?'))
		{
			return false;
		}
		$.ajax({
			url: 'customer_profile.php?id=<?php echo $_GET['id'];?>&email=<?=$_GET['email'];?>',
			type: 'post',
			data: {action: 'apply_voucher',code:$('#apply_voucher').val(),balance:balance},
			dataType:'json',
			beforeSend: function () {
				$(obj).attr('disabled','disabled');
			},
			complete: function () {
				$(obj).removeAttr('disabled');
			},
			success: function (json) {
				if(json['error'])
				{
					alert(json['error']);
					return false;
				}
				if(json['success'])
				{
					alert(json['success']);
					balance = parseFloat(json['balance']);
					$('#span_balance').html('$'+balance.toFixed(2));
					$('#apply_voucher').val('');
					if(balance=='0.00')
					{
						$(obj).parent().parent().hide();
					}
						//location.reload(true);
					}
				}
			});
	}
	function updateWhiteList(obj)
	{
		var status = $(obj).is(':checked');
		$.ajax({
			url: 'customer_profile.php?id=<?php echo $_GET['id'];?>&email=<?=$_GET['email'];?>',
			type: 'post',
			data: {action: 'update_whitelist_customer',white_list:status},
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (data) {
			}
		});
	}
	function updateIsInternal(obj)
	{
		var status = $(obj).is(':checked');
		$.ajax({
			url: 'customer_profile.php?id=<?php echo $_GET['id'];?>&email=<?=$_GET['email'];?>',
			type: 'post',
			data: {action: 'update_is_internal',is_internal:status},
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (data) {
			}
		});
	}
	function updateSpecialCustomer(obj)
	{
		var status = $(obj).is(':checked');
		$.ajax({
			url: 'customer_profile.php?id=<?php echo $_GET['id'];?>&email=<?=$_GET['email'];?>',
			type: 'post',
			data: {action: 'update_special_customer',is_special_customer:status,special_discount:$('#special_discount').val()},
			beforeSend: function () {
			},
			complete: function () {
			},
			success: function (data) {
				alert('Record Saved');
			}
		});
	}
	function reset_password() {
		var email = $('input[name="updateEmail"]').val();
		var name = $('#customer_name').val();
		if (email) {
			$.ajax({
				//url: '/index.php?route=account/forgotten&ajaxupdate=yesyes',
				url: 'customer_profile.php',
				type:"POST",
				dataType:"json",
				data:{'email':email,'sendEmailPassword': 'yes','name':name},
				success: function(json){
						alert(json['message']);
					
					
				}
			});
		}
	}
	function updateEmail(obj) {
		var container = $(obj).parent();
		var email = $(obj).parent().find('input').val();
		var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
		var oldEmail = $(obj).parent().find('input').attr('data-email');
		var inputHolder = $(obj).parent().find('b');
		if (email == '' || !re.test(email)) {
			alert('Please Enter a Valid Email');
		} else {
			$.ajax({
				url: 'customer_profile.php?&action=update_email',
				type:"POST",
				dataType:"json",
				data:{'email':email,'oldEmail':oldEmail},
				success: function(json){
					if (json['error']) {
						alert(json['msg']);
					}
					if (json['success']) {
						alert('Email Changed');
						window.location.replace("customer_profile.php?id=" + json['msg']);
					}
				}
			});
		}
	}
	function mergeEmail (email, toEmail) {
		$.ajax({
			url: 'customer_profile.php?&action=mergeEmail',
			type:"POST",
			dataType:"json",
			data:{'email':email, 'toEmail': toEmail},
			success: function(json){
				if (json['error']) {
					alert(json['msg']);
				}
				if (json['success']) {
					alert('Email Merged');
					window.location.replace("customer_profile.php?id=" + json['msg']);
				}
			}
		});
	}
	function mergeEmailHistory (t) {
		var email = $('.mergeEmailInput').val();
		var toEmail = $('.mergeEmailInput').attr('data-toEmail');
		var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
		if (email == '' || !re.test(email)) {
			alert('Please Enter Valid Email Address!');
		} else {
			if (confirm('Are You Sure! this can\'t be undo!')) {
				mergeEmail(email, toEmail);
			} else {
				$('.mergeEmailInput').val('');
				$('.mergeEmail').hide();
			}
		}
	}
	function updateCustomer (field_name, t, update) {
		$.ajax({
			url: 'customer_profile.php?id=<?php echo $_GET['id'];?>&email=<?=$_GET['email'];?>',
			type: 'post',
			data: {action: 'updateCustomerDataIMP', update: update, field: field_name, value: $(t).val()},
			success: function (data) {
			}
		});
	}


	function updateCompany (field_name, t, update) {
		$.ajax({
			url: 'customer_profile_new.php?id=<?php echo $_GET['id'];?>&email=<?=$_GET['email'];?>',
			type: 'post',
			dataType: 'json',
			data: {action: 'updateCompany', company: $('input[name=company_business]').val()},

			success: function (json) {
				alert('Company updated');
					location.reload(true);
			}
		});
	}


	window.loadContacts = function () {
		$('#contactsx').load('loadContacts.php?customer_id=<?php echo $customer_email; ?>');
	}
	function customerOCLogin(customer_id,salt)
	{
		if(!confirm('Are you sure want to access customer account?'))
		{
			return false;
		}
		((this.value !== '') ? window.open('https://phonepartsusa.com/index.php?route=account/login/backdoor&customer_id='+customer_id+'&salt='+salt) : null); this.value = '';
	}
	function payOrders() {
		var order_ids = [];
		$('.order_id_input').each(function() {
			if ($(this).is(':checked')) {
				order_ids.push($(this).val());
			}
		});
		if (order_ids.length!=0) {
			$('#bulk_pay_order').attr('href', 'bulk_pay_order.php?order_ids=' + order_ids);
		} else {
			$('#bulk_pay_order').attr('href', 'javascript:void(0);');
		}
	}
	function changeAddress(){
		var newAddress1= $('#adres1').val();
		var newAddress2= $('#adres2').val();
		$("#oldaddress1").val("newAddress1");
		$("#oldaddress2").val("newAddress2");
	
	}
	function fetch_orders(page){
		//alert ($('#order_table_body').text());return false;
		if (!page && $.trim($("#order_table_body").html())) {
			return false;
			}
			var id = '<?=$_GET['id'];?>';
			page = page || 1;
			$.ajax({
				url: 'customer_profile.php?id='+id+'&action=fetch_orders&page='+page,
				async: true,
				type: 'get',
				dataType: 'json',
				beforeSend: function () {
					$('#broken_log_table tbody').html('');
					$('#xcontent').show();
				},
				complete: function () {
					$('#xcontent').hide();
				},
				success: function (json) {
					if (json['success']) {
						$('#order_table_body').html(json['order_data']);
						//$('#total_dc').text(json['total_debit']+' / '+json['total_credit']);
						//$('#total_dc_3').text(json['total_debit_3']+' / '+json['total_credit_3']);
						//$('#total_dc_6').text(json['total_debit_6']+' / '+json['total_credit_6']);
						//$('#total_debit').text(json['total_debit']);
						//$('#total_credit').text(json['total_credit']);
						//$('#span_balance').text(json['balance']);
					}
				}
			});
		}
		$(document).on('click','.pagination_link',function(){
		fetch_orders($(this).attr('data-page'));
	});
		$(document).ready(function() {
			var id = '<?=$_GET['id'];?>';
            $.ajax({
				url: 'customer_profile.php?id='+id+'&load_credit_debit=1',
				async: true,
				type: 'get',
				dataType: 'json',
				beforeSend: function () {
					$('.loader_totals').show();
				},
				complete: function () {
					$('.loader_totals').hide();
				},
				success: function (json) {
					if (json['success']) {
						$('.total_dc').text(json['total_debit']+' / '+json['total_credit']);
						$('.total_dc_3').text(json['total_debit_3']+' / '+json['total_credit_3']);
						$('.total_dc_6').text(json['total_debit_6']+' / '+json['total_credit_6']);
						$('#total_debit').text(json['total_debit']);
						$('#total_credit').text(json['total_credit']);
						$('#span_balance').text(json['balance']);
						$('.rp').text(json['rp']);
						$('.rp3').text(json['rp3']);
						$('.rp6').text(json['rp6']);
					}
				}
			});
         });
</script>