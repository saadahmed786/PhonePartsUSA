<?php
require_once("../config.php");
require_once("../inc/functions.php");
$start = $db->func_escape_string(trim($_GET['start']));
$end = $db->func_escape_string(trim($_GET['end']));
if((int)$_GET['user_id']==0)
	{
		$_GET['user_id']=$_SESSION['user_id'];
	}
if (isset($_GET['created'])) {


	$assigned_customer_emails = $db->func_query("SELECT email from inv_customers where user_id = '".(int)$_GET['user_id']."' ");
	$email_string = '';
	foreach ($assigned_customer_emails as $email) {
		$email_string.="'".$email['email']."',";
	}
	$email_string.="'***'";
	

	$orders =   $db->func_query("SELECT i.order_id,i.order_price as amount,i.email,i.customer_name,i.order_status,od.shipping_method  from inv_orders i inner join inv_orders_details od on (i.order_id = od.order_id) where i.order_user='".(int)$_GET['user_id']."' AND i.is_manual='1' and date(i.order_date)>= '$start' and date(i.order_date)<= '$end' order by i.order_date desc ");
	

} else if (isset($_GET['assigned'])) {
	
	$assigned_customer_emails = $db->func_query("SELECT email from inv_customers where user_id = '".(int)$_GET['user_id']."' ");
	$email_string = '';
	foreach ($assigned_customer_emails as $email) {
		$email_string.="'".$email['email']."',";
	}
	$email_string.="'***'";
	$orders =   $db->func_query("SELECT i.order_id,i.order_price as amount,i.email,i.customer_name,i.order_status,od.shipping_method  from inv_orders i inner join inv_orders_details od on (i.order_id = od.order_id) where email IN ($email_string) and date(i.order_date)>= '$start' and date(i.order_date)<= '$end' order by i.order_date desc ");
}else if (isset($_GET['delivered'])){


	$assigned_customer_emails = $db->func_query("SELECT email from inv_customers where user_id = '".(int)$_GET['user_id']."' ");
	$email_string = '';
	foreach ($assigned_customer_emails as $email) {
		$email_string.="'".$email['email']."',";
	}
	$email_string.="'***'";
	$orders =   $db->func_query("SELECT i.order_id,i.order_price as amount,i.email,i.customer_name,i.order_status,od.shipping_method  from inv_orders i inner join inv_orders_details od on (i.order_id = od.order_id) where (i.order_user = '".(int)$_GET['user_id']."' OR i.email IN ($email_string) )  order by i.order_date desc ");


	$json = array();
	$i = 0;
	foreach ($orders as $key => $order) {

		$order_shipments = $db->func_query("select * from inv_shipstation_transactions where order_id = '".$order['order_id']."' ORDER BY voided DESC");
		foreach ($order_shipments as $_oshipment) {
			$tracker = $db->func_query_first("SELECT * FROM inv_tracker WHERE tracking_code='".$_oshipment['tracking_number']."'");
			$tracker_statuses = $db->func_query("SELECT * FROM inv_tracker_status WHERE tracker_id='".$tracker['tracker_id']."' and date(datetime)>= '$start' and date(datetime)<= '$end' order by id desc");
			foreach($tracker_statuses as $tracker_status)
			{
				if ($tracker_status['status'] == 'delivered') {
					$json['orders'][$i] = $order;
					$json['orders'][$i]['delivery_date'] = americanDate($tracker_status['datetime']);
					$json['orders'][$i]['order_id'] = '<a target="_blank" href="viewOrderDetail.php?order='. $order['order_id'].'">'.$order['order_id'].'</a>';
					$json['orders'][$i]['email'] = linkToProfile($order['email'],'','','_blank');
					$json['orders'][$i]['amount'] = number_format($order['amount'],2);
					$i++;
				}
			}
		}

	}
	if ($json['orders']) {
		$json['success'] = 1;
	} else {
		$json['error'] = 1;
	}

	echo json_encode($json);
	exit;
} else if (isset($_GET['transit'])){


	$assigned_customer_emails = $db->func_query("SELECT email from inv_customers where user_id = '".(int)$_GET['user_id']."' ");
	$email_string = '';
	foreach ($assigned_customer_emails as $email) {
		$email_string.="'".$email['email']."',";
	}
	$email_string.="'***'";
	
	$orders =   $db->func_query("SELECT i.order_id,i.order_price as amount,od.shipping_date,i.email,i.customer_name,i.order_status,od.shipping_method  from inv_orders i inner join inv_orders_details od on (i.order_id = od.order_id) where lower(i.order_status) = 'shipped' and od.shipping_method not like '%Local Las Vegas Store%' AND i.order_user = '".(int)$_GET['user_id']."' OR i.email IN ($email_string)  order by i.order_date desc limit 100 ");


	$json = array();
	$i = 0;
	foreach ($orders as $key => $order) {

		$order_shipments = $db->func_query("select *,date(ship_date) as shipping_date from inv_shipstation_transactions where order_id = '".$order['order_id']."' and voided = '0' ORDER BY voided DESC");
		foreach ($order_shipments as $_oshipment) {
			$tracker = $db->func_query_first("SELECT * FROM inv_tracker WHERE tracking_code='".$_oshipment['tracking_number']."'");
			$tracker_status = $db->func_query_first_cell("SELECT status FROM inv_tracker_status WHERE tracker_id='".$tracker['tracker_id']."' AND status = 'delivered'  order by datetime asc");
			$last_tracker_status = $db->func_query_first_cell("SELECT status FROM inv_tracker_status WHERE tracker_id='".$tracker['tracker_id']."' order by datetime desc");
			
			if (!$tracker_status && $last_tracker_status && strtolower($last_tracker_status)!= 'cancelled') {

				if ($_oshipment['carrier_code'] == 'fedex') {
		  	$last_tracker_status = '<a href="https://www.fedex.com/apps/fedextrack/?action=track&language=english&tracknumbers='.$_oshipment['tracking_number'].'" target = "_blank">'.$last_tracker_status.'</a>';
		  }
		  if ($_oshipment['carrier_code'] == 'ups') {
		  	$last_tracker_status = '<a href="http://wwwapps.ups.com/WebTracking/processRequest?HTMLVersion=5.0&Requester=NES&AgreeToTermsAndConditions=yes&loc=en_US&tracknum='.$_oshipment['tracking_number'].'" target = "_blank">'.$last_tracker_status.'</a>';
		  }
		  if ($_oshipment['carrier_code'] == 'endicia' || $_oshipment['carrier_code'] == 'express_1') {
		  	$last_tracker_status = '<a href="https://tools.usps.com/go/TrackConfirmAction?tLabels='.$_oshipment['tracking_number'].'" target = "_blank">'.$last_tracker_status.'</a>';
		  }
		  // if (!$url) 
		  // {
		  // 	$last_tracker_status =  $last_tracker_status;
		  // }

				$now = date('Y-m-d');
				$then = $_oshipment['shipping_date'];
				
				$diff = abs(strtotime($then) - strtotime($now));

$years = floor($diff / (365*60*60*24));
$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
//echo $days;exit;


				$json['orders'][$i] = $order;
				$json['orders'][$i]['days'] = $days;
				$json['orders'][$i]['last_status'] = $last_tracker_status;
				$json['orders'][$i]['order_id'] = '<a target="_blank" href="viewOrderDetail.php?order='. $order['order_id'].'">'.$order['order_id'].'</a>';
				$json['orders'][$i]['email'] = linkToProfile($order['email'],'','','_blank');
				$json['orders'][$i]['amount'] = number_format($order['amount'],2);
				$i++;
			}
			
		}

	}
	if ($json['orders']) {
		$json['success'] = 1;
	} else {
		$json['error'] = 1;
	}

	echo json_encode($json);
	exit;

}

$json = array();
if ($orders) {	
	$json['orders'] = $orders;
	foreach ($orders as $key => $order) {
			$json['orders'][$key]['order_id'] = '<a target="_blank" href="viewOrderDetail.php?order='. $order['order_id'].'">'.$order['order_id'].'</a>';
			$json['orders'][$key]['email'] = linkToProfile($order['email'],'','','_blank');
			$json['orders'][$key]['amount'] = number_format($order['amount'],2);
		}	
	$json['success'] = 1;	
} else {
	$json['error'] = 1;
}
echo json_encode($json);

?>