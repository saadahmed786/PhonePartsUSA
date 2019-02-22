<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$permission = 'paypal_transactions';
$pageName = 'PayPal Transactional Order';
$pageLink = 'paypal_orders.php';
$pageCreateLink = false;
$pageSetting = false;
$table = '`inv_orders`';
page_permission($permission);
if(isset($_POST['action']) && $_POST['action']=='mapbox')
{
	$transaction_id = $db->func_escape_string($_POST['transaction_id']);
	$transaction_category=(int)$_POST['transaction_category'];
	$orders_id=$db->func_query_first_cell("select order_id from inv_transactions where transaction_id='".$transaction_id."'");
	$order_id = $db->func_escape_string($_POST['ref_id']);
	$db->db_exec("update inv_orders_details SET payment_method='paypal' where order_id = '$order_id'");
	$check_order = $db->func_query_first("SELECT order_id, store_type, paid_price, order_price,email FROM inv_orders where order_id='".$order_id."' ");
	$exPaid = (float) $check_order['paid_price'];
	$current_ord_price = (float) $check_order['order_price'];
	$paypal_transaction_amount = (float) $db->func_query_first_cell("SELECT amount FROM inv_transactions where transaction_id='$transaction_id'");
	if ($exPaid == '0.00' && $current_ord_price > $paypal_transaction_amount) {
			$paid_price = (float) $paypal_transaction_amount;
	} else if ($exPaid == '0.00' && $current_ord_price < $paypal_transaction_amount) {
			$paid_price = (float) $current_ord_price;
	} else if ($exPaid == $current_ord_price || $exPaid > $current_ord_price) {
		$json = array ();
		$json['error']  = 'Cannot map. Order is already paid in full';
		echo json_encode($json);
		exit;
	} else if ($exPaid < $current_ord_price){
		$difference = $current_ord_price - $exPaid;
		if ($difference > $paypal_transaction_amount) {
			$paid_price = $paypal_transaction_amount + $exPaid;	
		} else {	
		$paid_price = (float) $difference;
		$paid_price += $exPaid;
		}
	}
	if ($paid_price == $current_ord_price) {
			$db->db_exec("UPDATE inv_orders SET payment_source='Paid' WHERE order_id='".$check_order['order_id']."'");
		}
	
	$ref_id='';
	$is_lbb = 0;
	$is_multi = 0;
	$error = array();
	if($check_order)
	{
		
		$map_check = $db->func_query_first("SELECT transaction_id,is_mapped FROM inv_transactions WHERE order_id='".$check_order['order_id']."'");
		if($map_check && $map_check['is_mapped']==1)
		{
			//$error[] = 'Ref is already mapped with Transaction ID: '.$map_check['transaction_id'];
			$is_multi=1;
			$ref_id = $check_order['order_id'];
			$is_lbb = 0;
		}
		else
		{
			$multi_map_check = $db->func_query_first("SELECT transaction_id FROM inv_transactions_multi WHERE order_id='".$check_order['order_id']."'");
			if($multi_map_check)
			{
				//$error[] = 'Ref is already mapped with Transaction ID: '.$multi_map_check['transaction_id'];
				$is_multi=1;
				$ref_id = $check_order['order_id'];
				$is_lbb = 0;
			}
			else
			{
				$is_multi = 0;
				$ref_id = $check_order['order_id'];
				$is_lbb = 0;
			}	
		}		
	}
	else
	{
		$check_lbb = $db->func_query_first("SELECT shipment_number FROM oc_buyback WHERE shipment_number='".$order_id."'");
		
		if($check_lbb)
		{
			$map_check = $db->func_query_first("SELECT transaction_id,is_mapped FROM inv_transactions WHERE order_id='".$check_lbb['shipment_number']."'");
			if($map_check && $map_check['is_mapped']==1)
			{
			//$error[] = 'Reference is already mapped with Transaction ID: '.$map_check['transaction_id'];
				$is_multi=1;
				$ref_id = $check_lbb['shipment_number'];
				$is_lbb = 1;	
			}
			else
			{
				$multi_map_check = $db->func_query_first("SELECT transaction_id FROM inv_transactions_multi WHERE order_id='".$check_lbb['shipment_number']."'");
				if($multi_map_check)
				{
					$is_multi=1;
					$ref_id = $check_lbb['shipment_number'];
					$is_lbb = 1;
				//$error[] = 'Reference is already mapped with Transaction ID: '.$multi_map_check['transaction_id'];
				}
				else
				{
					$ref_id = $check_lbb['shipment_number'];
					$is_lbb = 1;
					$is_multi = 0;
				}
				
			}		
		}
		else
		{
			$error[] = 'Invalid reference id provided';
			//$error[] = "SELECT shipment_number FROM oc_buyback WHERE shipment_number='".$order_id."'";
		}
	}
	$json = array();
	if($error)
	{
		$json['error'] = implode("\n", $error);
	}
	else
	{

		$_map_id = ($orders_id?$orders_id.",".$ref_id:$ref_id);
		
		if($is_multi==0)
		{
			$check_for_paid = $db->func_query_first("SELECT paid_price,order_price,order_status FROM inv_orders WHERE order_id='".$ref_id."'");
		
				
			
			if(strtolower($check_for_paid['order_status'])=='on hold' && round($check_for_paid['order_price'],1)==round($data['amount'],1) && $is_lbb==0)
			{
				$db->db_exec("UPDATE inv_orders SET order_status = 'Processed' WHERE order_id='".$ref_id."'");
				$db->db_exec("UPDATE oc_order SET order_status_id = '15' WHERE cast(order_id as char(50))='".$ref_id."' or ref_order_id='".$ref_id."'");
			}
			// if ($check_order['store_type'] == 'po_business') {
				$db->db_exec("UPDATE inv_orders SET paid_price=paid_price+$paid_price, payment_detail_1='$transaction_id' WHERE order_id='$ref_id'");

				addVoucher($ref_id,'paypal',$paid_price);

					$_order = getOrder($ref_id);
					$_amount = $db->func_query_first("SELECT net_amount,transaction_fee from inv_transactions where transaction_id='$transaction_id'");
					$accounts = array();
					$accounts['description'] = 'Payment made #'.$transaction_id.' @ '.$ref_id;
					if($_amount['net_amount']>0)
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $_amount['net_amount'];
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $_amount['net_amount']*(-1);
					}
					$accounts['order_id'] = $ref_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type']='paypal';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $_order['order_date'];

					
					if(trim($ref_id) && ($_order))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					$accounts = array();
					$accounts['description'] = 'Payment made #'.$transaction_id.' @ '.$ref_id;
					if($_amount['net_amount']>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $_amount['net_amount'];
						
					}
					else
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $_amount['net_amount']*(-1);
					}
					$accounts['order_id'] = $ref_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'paypal';
					$accounts['date_added'] = $_order['order_date'];

					
					if(trim($ref_id) && ($_order))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					// PayPal Fee


					$accounts = array();
					$accounts['description'] = 'Payment made #'.$transaction_id.' @ '.$ref_id;
					if($_amount['transaction_fee']>0)
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $_amount['transaction_fee'];
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $_amount['transaction_fee']*(-1);
					}
					$accounts['order_id'] = $ref_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type']='paypal_fee';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $_order['order_date'];

					
					if(trim($ref_id) && ($_order))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					$accounts = array();
					$accounts['description'] = 'Payment made #'.$transaction_id.' @ '.$ref_id;
					if($_amount['transaction_fee']>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $_amount['transaction_fee'];
						
					}
					else
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $_amount['transaction_fee']*(-1);
					}
					$accounts['order_id'] = $ref_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'paypal_fee';
					$accounts['date_added'] = $_order['order_date'];

					
					if(trim($ref_id) && ($_order))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					// end Paypal fee


					$tax_check = $db->func_query_first_cell("SELECT tax from inv_orders where order_id='".$ref_id."'");

					if($tax_check>0)
					{
						$tax_rate = $db->func_query_first("SELECT * FROM oc_tax_rate WHERE geo_zone_id=10");
						$tax_amount = ($paid_price*(float)$tax_detail['rate'])/100;

					$accounts = array();
					$accounts['description'] = 'State Tax';
					if($tax_amount>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $tax_amount;
						
					}
					else
					{
						$accounts['debit'] = $tax_amount*(-1);
						$accounts['credit'] = 0.00;
					}
					$accounts['order_id'] = $ref_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type']='tax';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $_order['order_date'];
					if(trim($ref_id) && $_order)
					{
						add_accounting_voucher($accounts); // Tax Account
						
					}


					$accounts = array();
					$accounts['description'] = 'State Tax';
					if($tax_amount>0)
					{
						$accounts['debit'] = $tax_amount;
						$accounts['credit'] = 0.00;
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $tax_amount*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'tax';
					$accounts['date_added'] = $_order['order_date'];

					if(trim($data['order_id']) && $_order)
					{
						add_accounting_voucher($accounts); // Tax Account
						
					}

					}


			// }
			$db->db_exec("UPDATE inv_transactions SET order_id='".$_map_id."',transaction_category='".$transaction_category."',is_mapped=1,is_lbb='".$is_lbb."' WHERE transaction_id='".$transaction_id."'");
		}
		else{
			// if ($check_order['store_type'] == 'po_business') {
				$db->db_exec("UPDATE inv_orders SET paid_price=paid_price+$paid_price, payment_detail_2='$transaction_id' WHERE order_id='$ref_id'");

				addVoucher($ref_id,'paypal',$paid_price);

				$_order = getOrder($ref_id);
					$_amount = $db->func_query_first("SELECT net_amount,transaction_fee from inv_transactions where transaction_id='$transaction_id'");
					$accounts = array();
					$accounts['description'] = 'Payment made #'.$transaction_id.' @ '.$ref_id;
					if($_amount['net_amount']>0)
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $_amount['net_amount'];
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $_amount['net_amount']*(-1);
					}
					$accounts['order_id'] = $ref_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type']='paypal';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $_order['order_date'];

					
					if(trim($ref_id) && ($_order))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					$accounts = array();
					$accounts['description'] = 'Payment made #'.$transaction_id.' @ '.$ref_id;
					if($_amount['net_amount']>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $_amount['net_amount'];
						
					}
					else
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $_amount['net_amount']*(-1);
					}
					$accounts['order_id'] = $ref_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'paypal';
					$accounts['date_added'] = $_order['order_date'];

					
					if(trim($ref_id) && ($_order))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					// PayPal Fee


					$accounts = array();
					$accounts['description'] = 'Payment made #'.$transaction_id.' @ '.$ref_id;
					if($_amount['transaction_fee']>0)
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $_amount['transaction_fee'];
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $_amount['transaction_fee']*(-1);
					}
					$accounts['order_id'] = $ref_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type']='paypal_fee';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $_order['order_date'];

					
					if(trim($ref_id) && ($_order))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					$accounts = array();
					$accounts['description'] = 'Payment made #'.$transaction_id.' @ '.$ref_id;
					if($_amount['transaction_fee']>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $_amount['transaction_fee'];
						
					}
					else
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $_amount['transaction_fee']*(-1);
					}
					$accounts['order_id'] = $ref_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'paypal_fee';
					$accounts['date_added'] = $_order['order_date'];

					
					if(trim($ref_id) && ($_order))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					// end Paypal fee


					$tax_check = $db->func_query_first_cell("SELECT tax from inv_orders where order_id='".$ref_id."'");

					if($tax_check>0)
					{
						$tax_rate = $db->func_query_first("SELECT * FROM oc_tax_rate WHERE geo_zone_id=10");
						$tax_amount = ($paid_price*(float)$tax_detail['rate'])/100;

					$accounts = array();
					$accounts['description'] = 'State Tax';
					if($tax_amount>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $tax_amount;
						
					}
					else
					{
						$accounts['debit'] = $tax_amount*(-1);
						$accounts['credit'] = 0.00;
					}
					$accounts['order_id'] = $ref_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type']='tax';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $_order['order_date'];
					if(trim($ref_id) && $_order)
					{
						add_accounting_voucher($accounts); // Tax Account
						
					}


					$accounts = array();
					$accounts['description'] = 'State Tax';
					if($tax_amount>0)
					{
						$accounts['debit'] = $tax_amount;
						$accounts['credit'] = 0.00;
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $tax_amount*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'tax';
					$accounts['date_added'] = $_order['order_date'];

					if(trim($data['order_id']) && $_order)
					{
						add_accounting_voucher($accounts); // Tax Account
						
					}

					}


				
			// }
			$db->db_exec("UPDATE inv_transactions SET order_id='".$_map_id."',transaction_category='".$transaction_category."',is_mapped=1,is_lbb='".$is_lbb."',is_multi=1 WHERE transaction_id='".$transaction_id."'");
			$db->db_exec("INSERT INTO inv_transactions_multi SET transaction_id='".$transaction_id."',order_id='".$ref_id."'");
		}	
		if($is_lbb)
		{
			$json['success'] = linkToLbbShipment($ref_id,$host_path);
		}
		else
		{
			$json['success'] = linkToOrder($ref_id,$host_path);
		}
	}
	echo json_encode($json);
	exit;
}
// Getting Page information
if (isset($_GET['page'])) {
	$page = intval($_GET['page']);
}
if ($page < 1) {
	$page = 1;
}
$parameters = '&page='.$page;
//Setting PAgination Limits
$max_page_links = 10;
$num_rows = 30;
$start = ($page - 1) * $num_rows;
// Search Setup
$where = array();
if ($_GET['filter'] == 'Search') {
	
	
	if($_GET['filter_order_id'])
	{
		$where[] = " order_id LIKE '%".$db->func_escape_string(trim($_GET['filter_order_id']))."%'";
		$parameters.='&filter_order_id='.trim($_GET['filter_order_id']);
	}
	if($_GET['filter_transaction_id'])
	{
		$where[] = " transaction_id LIKE '%".$db->func_escape_string(trim($_GET['filter_transaction_id']))."%'";
		$parameters.='&filter_transaction_id='.trim($_GET['filter_transaction_id']);
	}
	if($_GET['filter_refund']==1)
	{
		$where[] = " (order_id <>'' and receiver_email='paypal@phonepartsusa.com' and amount<0) ";
		$parameters.='&filter_refund='.trim($_GET['filter_refund']);
	}
	
	if($_GET['filter_email'])
	{
		$where[] = " (email LIKE '%".$db->func_escape_string(trim($_GET['filter_email']))."%' or receiver_email LIKE '%".$db->func_escape_string(trim($_GET['filter_email']))."%' ) ";
		$parameters.='&filter_email='.trim($_GET['filter_email']);
	}
	if($_GET['filter_radio_amount']=='1' && ($_GET['filter_radio_amount2']=='' or $_GET['filter_radio_amount2']=='0' ))
	{
		$where[] = " (amount > 0  ) ";
		$parameters.='&filter_radio_amount='.trim($_GET['filter_radio_amount']);
	}
	if($_GET['filter_radio_amount2']=='1' && ($_GET['filter_radio_amount']=='' or $_GET['filter_radio_amount']=='0' ))
	{
		$where[] = " (amount < 0  ) ";
		$parameters.='&filter_radio_amount2='.trim($_GET['filter_radio_amount2']);
	}
	if($_GET['filter_radio_amount']=='1' && ($_GET['filter_radio_amount2']=='1' ))
	{
		$where[] = " ( 1 = 1  ) ";
		$parameters.='&filter_radio_amount='.trim($_GET['filter_radio_amount']).'&filter_radio_amount2='.trim($_GET['filter_radio_amount2']);
	}
		// if($_GET['filter_radio_amount2']==1)
		// {
		// 	$where[] = " amount < 0 ";
		// 	$parameters.='&filter_radio_amount2='.trim($_GET['filter_radio_amount2']);
		// }
	if($_GET['filter_amount'])
	{
		
		$where[] = " (amount LIKE '%".$db->func_escape_string(trim($_GET['filter_amount']))."%' or net_amount LIKE '%".$db->func_escape_string(trim($_GET['filter_amount']))."%' ) ";
		$parameters.='&filter_amount='.trim($_GET['filter_amount']);
		$paramters.='&filter_radio_amount='.$_GET['filter_radio_amount'];
	}
	if($_GET['filter_order_date'])
	{
		$where[] = " DATE(order_date) = '".date('Y-m-d',strtotime($_GET['filter_order_date']))."'";
		$parameters.='&filter_order_date='.$_GET['filter_order_date'];
	}
	if($_GET['filter_orderType'] == '1' || $_GET['filter_orderType'] == '0')
	{
		$where[] = " is_mapped = '".(int)($_GET['filter_orderType'])."'";
		$parameters.='&filter_orderType='.$_GET['filter_orderType'];
	}
	
}
if(!$where)
{
	$where[] = " 1 = 1 ";
}
$where = implode(" AND ", $where);
$sort = $_GET['sort'];
$order_by = $_GET['order_by'];
$sort_array  = array('order_date','email','receiver_email','is_mapped');
if(!in_array($sort, $sort_array))
{
	$sort = $sort_array[0];
	$order_by = 'desc';
}
$orderby = ' ORDER BY `'.$sort.'` '.$order_by;
if($order_by=='asc') $order_by='desc'; else $order_by = 'asc';
//Writing query 
$inv_query = "SELECT * FROM inv_transactions WHERE payment_status='Completed' and order_status='Completed' and $where
$orderby";
if(isset($_GET['debug']))
{
 echo $inv_query;
}
//exit;
//Using Split Page Class to make pagination
$splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);
//Getting All Messages
$rows = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	<link rel="stylesheet" type="text/css" href="include/xtable.css" media="screen" />
	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script>
		$(document).ready(function (e) {
			$('.fancybox3').fancybox({width: '90%', 'height': 800, autoCenter: true, autoSize: false});
		});
		function MapBox(obj,transaction_id)
		{
			var ref = $(obj).parent().parent().find('.mapbox');
			var ref_id = $(ref).val();
			if(jQuery.trim(ref_id)=='')
			{
				alert('Please provide a valid reference');
				return false;
			}
			if(!confirm('Are you sure to Map?'))
			{
				return false;
			}
			$.ajax({
				url: '<?=$pageLink;?>',
				type:"POST",
				dataType:"json",
				data:{'ref_id':ref_id,'action':'mapbox','transaction_id':transaction_id},
				beforeSend: function() {
					$(ref).parent().find('.map_button').hide(200);
			//$(ref).parent().find('.map_wait').html('Please wait...').show(200);
		},		
		complete: function() {
		//	$(ref).parent().find('.map_wait').hide(200);
	},		
	success: function(json){
		if(json['error'])
		{
			alert(json['error']);
			$(ref).parent().find('.map_button').show(200);
			return false;
		}
		if (json['success']) {
			$(ref).parent().hide(200);
			$(ref).parent().parent().find('span.tag').removeClass('red-bg');
			$(ref).parent().parent().find('span.tag').addClass('blue-bg');
			$(ref).parent().parent().find('span.tag').html('Mapped');
			$(ref).parent().parent().parent().find('.reference_id').append(json['success']);
												//window.location.replace("viewOrderDetail.php?order=" + json['msg']);
											}
										}
									});
			
		}
	</script>
	<style>
		small{
			font-size:8px;
			font-weight: bold
		}
	</style>
</head>
<body>
	<div align="center">
		<div align="center"> 
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if ($_SESSION['message']) { ?>
		<div align="center"><br />
			<font color="red">
				<?php
				echo $_SESSION['message'];
				unset($_SESSION['message']);
				?>
				<br />
			</font>
		</div>
		<?php } ?>
		<h2>Manage <?= $pageName; ?>s</h2>
		<form action="" method="get">
			<table width="80%" cellpadding="5">
				<tr>
					<th>Ref ID:</th>
					<td><input type="text" name="filter_order_id" value="<?=$_GET['filter_order_id'];?>"></td>
					<th>Transaction ID:</th>
					<td><input type="text" name="filter_transaction_id" value="<?=$_GET['filter_transaction_id'];?>"></td>
					<th>Email:</th>
					<td><input type="text" name="filter_email" value="<?=$_GET['filter_email'];?>"></td>
					<th>Amount:</th>
					<td><input size="5" type="text" name="filter_amount" value="<?=$_GET['filter_amount'];?>"></td>
					<th>Order Date:</th>
					<td><input type="text" class="datepicker" readOnly name="filter_order_date" value="<?=$_GET['filter_order_date'];?>"></td>
					<th>Type:</th>
					<td>
						<select name="filter_orderType">
							<option value="">Please Select</option>
							<option value="1" <?=($_GET['filter_orderType']=='1'?'selected':'');?>>Mapped</option>
							<option value="0" <?=($_GET['filter_orderType']=='0'?'selected':'');?>>Missing</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="3"><input type="checkbox" value="1" name="filter_radio_amount" <?php if( $_GET['filter_radio_amount']==1) echo 'checked';?>> <strong>Amount Received<br></strong> 
						<input type="checkbox" name="filter_radio_amount2" value="1" <?php if($_GET['filter_radio_amount2']==1) echo 'checked';?>> <strong>Amount Sent</strong> </td>
						<td colspan="9"><input type="checkbox" name="filter_refund" value="1" <?php if($_GET['filter_refund']==1) echo 'checked'; ?> value="1"><strong>Refund Orders?</strong></td>
					</tr>
					<tr>
						<td colspan="12" align="center"><input class="button" type="submit" name="filter" value="Search"></td>
					</tr>
				</table>
			</form>
			<table  class="xtable"   align="center" style="width:98%;margin-top: 30px;">
				<thead>
					<tr>
						<th  width="13%"  align="center"><a <?=($sort=='order_date'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=order_date&order_by=<?=$order_by;?>&<?=$parameters;?>">Date</a></th>
						<th width="11%" align="center">Transaction ID</th>
						<th width="10%" align="center">Ref ID</th>
						<th width="15%" align="center"><a <?=($sort=='email'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=email&order_by=<?=$order_by;?>&<?=$parameters;?>">Sender</a></th>
						<th width="15%" align="center"><a <?=($sort=='receiver_email'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=receiver_email&order_by=<?=$order_by;?>&<?=$parameters;?>">Receiver</a></th>
						<th width="9%" align="center">Amt Received</th>
						<th width="9%" align="center">Amt Sent</th>
						<!--<th width="14%">Paid Amount</th>-->
						<th width="8%" align="center"><a <?=($sort=='is_mapped'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=is_mapped&order_by=<?=$order_by;?>&<?=$parameters;?>">Status</a></th>
						<th width="10%" align="center">Source</th>
					</tr>
				</thead>
				<tbody>
					<!-- Showing All REcord -->
					<?php
					foreach($rows as $i=> $row)
					{
						$is_missing = false;
						if($row['is_mapped']=='0')
						{
							$is_missing = true;
						}
						/* Start comment for run time order mapping check */
						/*
						$missing_check = getOrder($row['order_id']);
						if($missing_check['order_id'])
						{
							$is_missing = false;
						}
						if($is_missing)
						{
							$missing_check = getOrder('E'.$row['order_id']);	
							if($missing_check['order_id'])
							{
								$row['order_id'] = 'E'.$row['order_id'];
								$is_missing = false;
							}
						}
						if($is_missing)
						{
							$missing_check = getOrder('RL'.$row['order_id']);
							if($missing_check['order_id'])
							{
								$row['order_id'] = 'RL'.$row['order_id'];
								$is_missing = false;
							}
						}
						if($is_missing==false)
						{
							$db->func_query("UPDATE inv_transactions SET is_mapped=1,order_id='".$row['order_id']."' WHERE id='".$row['id']."'");
						}
						*/
						/* End Comment for run time application check */
						
						// if(isset($_GET['filter_orderType']) and $_GET['filter_orderType']=='0')
						// {
						// 	if($is_missing==false)
						// 	{
						// 		//continue;
						// 	}
						// }
						$is_refund = false;
						if($row['order_id']!='' and $row['amount']<0.00 and $row['receiver_email']=='paypal@phonepartsusa.com')
						{
							$is_refund = true;
						}
						
						if($row['order_source']=='Web')
						{
							$row['order_source'] = 'PayPal Express';
						}
						else if($row['order_source']=='RLCD')
						{
							$row['order_source'] = 'PayPal';
						}
						else if($row['order_source']=='Unknown')
						{
							$row['order_source'] = 'PayPal';
						}
					//
					//Swaping the Receiver & Sender Email if Amount Negative
						if($row['amount']<0.00 and $row['receiver_email']=='paypal@phonepartsusa.com')
						{
							$_temp =	$row['receiver_email'];
							$row['receiver_email'] = $row['email'];
							$row['email'] = $_temp;
						}
						$row_id=explode(',', $row['order_id']);
						$unmapped_price= $row['amount'];
						?>
						<tr>
							<td><?=americanDate($row['order_date']);?></td>
							<td><?=$row['transaction_id'];?></td>
							<td class="reference_id">
								<?php
								foreach ($row_id as $rowid) {
									
								$price=$db->func_query_first_cell("select order_price from inv_orders where order_id='".$rowid."' ");	
								
								if($row['is_lbb']==1)
								{
									?>
									<?=linkToLbbShipment($rowid,$host_path);?>
									<?php echo '('.$price.')'; ?>
									<?php
								}
								else
								{
									if($is_missing==false)
									{
										?>
										<?=linkToOrder($rowid,$host_path);?>
										<?php echo '('.$price.')'; ?>
										<?php
									}
									else
									{
										echo $rowid;
										echo '('.$price.')'; 
									}
									$unmapped_price=$unmapped_price-$price;
									?>
									<?php
								}
								if($is_refund)
								{
									echo '<br><small style="color:red">Refund</small>';
								}
							}
								?>
							</td>
							<td><?=linkToProfile($row['email'],$host_path);?></td>
							<td><?=linkToProfile($row['receiver_email'],$host_path);?></td>
							<?php
							if($row['net_amount']<=0)
							{
								?>
								<td align="center">-</td>
								<td align="center">$<?=number_format($row['amount'],2);?><br><small> ($<?=number_format($row['net_amount'],2);?>) </small></td>
								<?php
							}
							else
							{
								?>
								<td align="center">$<?=number_format($row['amount'],2);?><br><small> ($<?=number_format($row['net_amount'],2);?>) </small></td>
								<td align="center">-</td>
								<?php
							}
							?>
							<!-- <td>$<?=number_format($row['transaction_fee'],2);?></td> -->
							<!-- <td>$<?=number_format($row['net_amount'],2);?></td> -->
							<td align="center"><span class="tag <?=($is_missing?'red-bg':'blue-bg');?>"><?=($is_missing?'Unmapped':'Mapped');?></span>
								<?php
								if($is_missing)
								{
									?>
									<br>
									<small><a href="javascript:void(0);" onclick="$(this).parent().hide(200);$(this).parent().parent().find('.map').show(200)" style="text-decoration:underline">Map</a></small>
									<div class="map" style="margin-top:10px;display:none"><input class="mapbox" type="text" size="13" placeholder="LBB # / Order ID"> <div class="map_button"><a href="javascript:void(0)" onclick="MapBox(this,'<?=$row['transaction_id'];?>')" style="">Confirm</a></div><div class="map_wait" style="display:none;text-align:center"></div> </div>
<?php
								} else{
								?>
								<?php if($unmapped_price>0){ ?>
								Unmapped (<?php echo $unmapped_price;?>)	
								<small><a href="javascript:void(0);" onclick="$(this).parent().parent().find('.maps').show(200)" style="text-decoration:underline">+</a></small>
									<div class="maps" style="margin-top:10px;display:none"><input class="mapbox" type="text" size="13" placeholder="LBB # / Order ID"> <div class="map_button"><a href="javascript:void(0)" onclick="MapBox(this,'<?=$row['transaction_id'];?>')" style="">Confirm</a></div><div class="map_wait" style="display:none;text-align:center"></div> </div>
									<?php }?>
							<?php }?>
							</td>
							<td align="center"><?=$row['order_source'];?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<?php
						$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
						?>
						<td colspan="11">
							<em><?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?></em>
							<div class="pagination" style="float:right">
								<?php echo $splitPage->display_links(10,$parameters);?>
							</div>
						</td>
					</tr>
				</tfoot>
			</table>
			<br />
		</div>
	</body>