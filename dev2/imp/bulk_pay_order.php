<?php
require_once("auth.php");
require_once("inc/functions.php");
if ($_POST['action'] == 'checkID') {

	$id = $db->func_escape_string($_POST['id']);

	$amount = round($db->func_query_first_cell("SELECT amount FROM inv_transactions WHERE transaction_id = '$id' AND is_mapped = '0'"), 2);

	$total = round($_POST['amount'], 2);
	$less_amount = $total - 0.1001;
	$more_amount = $total + 0.1001;
	
	if ($amount > $less_amount && $amount < $more_amount) {
		$json['success'] = 1;
		$json['amount'] = $amount;
	} else {
		$json['error'] = "Order's Total don't match with Paid Price";
		$json['amount'] = $amount;
	}
	echo json_encode($json);
	exit;
}
if ($_POST['trans_id']) {
	$id = $db->func_escape_string($_POST['trans_id']);
	$order_ids = explode(',', $_GET['order_ids']);
	$amount = round($_POST['amount'], 2);
	$less_amount = $_POST['paying_price'] - 0.1001;
	$more_amount = $_POST['paying_price'] + 0.1001;
	if ($amount > $less_amount && $amount < $more_amount) {

		if (count($order_ids) == 1) {
			foreach ($order_ids as $key => $order_id) {
				// get order details
				$order = $db->func_query_first_cell("SELECT * from inv_orders where order_id = '$order_id'");
				// update transctions details for imp
				$db->db_exec("UPDATE inv_transactions SET is_mapped='1', order_id = '$order_id' where transaction_id = '$id'");
				// update imp details
				$db->db_exec("UPDATE inv_orders_details SET payment_method='paypal' where order_id = '$order_id'");
				$db->db_exec("UPDATE inv_orders SET paid_price=order_price, payment_detail_1 = '$id' where order_id = '$order_id'");
				// update opencart if it's a web order
				if ($order['store_type'] == 'web') {
					if ($db->func_query_first_cell("SELECT order_id from oc_payflow_admin_tools where order_id = '" . $o_id . "'")) {
						$db->db_exec("UPDATE oc_payflow_admin_tools SET transaction_id='". $response_data['PPREF'] ."', order_id = '$o_id', amount='". $_POST['amount'] ."'");
					} else {
						$db->db_exec("INSERT INTO oc_payflow_admin_tools SET `order_id` = '" . $o_id . "', `pp_transaction_id`='".$response_data['PPREF']."', `authorization_id`='".$response_data['AUTHCODE']."', `avsaddr`='". $response_data['AVSADDR'] ."', `avszip`='" . $response_data['AVSZIP'] . "', `cvv2match`='" . $response_data['CVV2MATCH'] . "'");
					}
				}
			}
		} else {
			// update transctions details for imp
			$db->db_exec("UPDATE inv_transactions SET is_mapped='1', is_multi='1' where transaction_id = '$id'");
			// loop thorugh all orders
			foreach ($order_ids as $key => $order_id) {
				// get order details
				$order = $db->func_query_first_cell("SELECT * from inv_orders where order_id = '$order_id'");
				// update imp details
				$db->db_exec("UPDATE inv_orders_details SET payment_method='paypal' where order_id = '$order_id'");
				$db->db_exec("UPDATE inv_orders SET transaction_id='$id', paid_price=order_price, payment_detail_1 = '$id' where order_id = '$order_id'");
				// add multiple orders with one transactions
				$db->db_exec("INSERT into inv_transactions_multi SET transaction_id='$id', order_id = '$order_id'");
				// update opencart if it's a web order
				if ($order['store_type'] == 'web') {
					if ($db->func_query_first_cell("SELECT order_id from oc_payflow_admin_tools where order_id = '" . $o_id . "'")) {
						$db->db_exec("UPDATE oc_payflow_admin_tools SET transaction_id='". $response_data['PPREF'] ."', order_id = '$o_id', amount='". $_POST['amount'] ."'");
					} else {
						$db->db_exec("INSERT INTO oc_payflow_admin_tools SET `order_id` = '" . $o_id . "', `pp_transaction_id`='".$response_data['PPREF']."', `authorization_id`='".$response_data['AUTHCODE']."', `avsaddr`='". $response_data['AVSADDR'] ."', `avszip`='" . $response_data['AVSZIP'] . "', `cvv2match`='" . $response_data['CVV2MATCH'] . "'");
					}
				}
			}
		}
		$json['success'] = 'Order Payment Compelete';
	} else {
		$json['error'] = "Order's Total don't match with Paid Price";
	}
	echo json_encode($json);
	exit;
}
if ($_POST['check']) {
	$order_ids = explode(',', $_GET['order_ids']);
	$amount = round($_POST['amount'], 2);
	$less_amount = $_POST['paying_price'] - 0.1001;
	$more_amount = $_POST['paying_price'] + 0.1001;
	if ($amount > $less_amount && $amount < $more_amount) {

		foreach ($order_ids as $key => $order_id) {
			$db->db_exec("UPDATE inv_orders_details SET payment_method='check' where order_id = '$order_id'");
			$db->db_exec("UPDATE inv_orders SET paid_price=order_price, payment_detail_1 = '" . $_POST['check'] . "' where order_id = '$order_id'");
		}

		$json['success'] = 'Order Payment Compelete';
	} else {
		$json['error'] = "Order's Total don't match with Paid Price";
	}
	echo json_encode($json);
	exit;
}

if ($_GET['order_ids']) {
	$orders = array();
	$order_totals = array();
	$total_payable = 0;
	foreach (explode(',', $_GET['order_ids']) as $i => $order_id) {
	$orders[$i] = $db->func_query_first("SELECT * FROM inv_orders a,inv_orders_details b  WHERE a.order_id=b.order_id AND a.order_id='".$order_id."'");
	$order_totals[$i] = orderTotal($order_id, true);
	$total_payable += $order_totals[$i]['order_total'] - $orders[$i]['paid_price'];
}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Payment Status</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />


	
	<style type="text/css">
		.data td,.data th {
			border: 1px solid #e8e8e8;
			text-align:center;
			width: 150px;
		}
		.div-fixed{
			position:fixed;
			top:0px;
			left:8px;
			background:#fff;
			width:98.8%; 
		}
		.red td{ box-shadow:1px 2px 5px #990000;}
	</style>
</head>
<body>
	<div align="center" style="display:none"> 
		<?php include_once 'inc/header.php';?>
	</div>

	<?php if($_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php else:?>
		<br /><br /> 
	<?php endif;?>

	<div align="center">
		<div  id="aim_div">


			<?php

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
			<table>
				<tbody>
					<tr>
						<td><label><input type="radio" name="method" value="card" checked=""> Card</label></td>
						<td><label><input type="radio" name="method" value="paypal"> Paypal</label></td>
						<td><label><input type="radio" name="method" value="check"> Check</label></td>
					</tr>
				</tbody>
			</table>
			<table border="1" width="60%" cellpadding="5" cellspacing="0" class="tables" align="center" id="card" >
				<tr>
					<td colspan="2" align="center">
						<input type="hidden" value="<?php echo $total_payable;?>" name="paying_price" id="paying_price" /> $<?php echo $total_payable;?> Total
						<span class="span_amount" style="margin-left:25px"></span>
					</td>
				</tr>
				<tr style="display:none">
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
					<td align="center" colspan="2">
						<input type="hidden" type="hidden" name="orders[order_ids]" value="<?php echo $_GET['order_ids'];?>">

						<input type="hidden" name="orders_details[first_name]" value='<?php echo $orders[0]['first_name'];?>'>
						<input type="hidden" name="orders_details[last_name]" value='<?php echo $orders[0]['last_name'];?>'>
						<input type="hidden" name="orders[email]" value='<?php echo $orders[0]['email'];?>'>
						<input type="hidden" name="orders_details[phone_number]" value='<?php echo $orders[0]['phone_number'];?>'>
						<input type="hidden" name="orders_details[address1]" value='<?php echo $orders[0]['address1'];?>'>
						<input type="hidden" name="orders_details[city]" value='<?php echo $orders[0]['city'];?>'>
						<input type="hidden" name="orders_details[state]" value='<?php echo $orders[0]['state'];?>'>
						<input type="hidden" name="orders_details[zip]" value='<?php echo $orders[0]['zip'];?>'>
						<input type="hidden" name="total" value="<?php echo $total_payable; ?>">

						<input type="button" class="button confirm-btn" value="Charge Card" onclick="confirmAim('card', 1);"   />
					</td>

				</tr>
			</table>
			<table style="display: none;" border="1" width="60%" cellpadding="5" cellspacing="0" class="tables" align="center" id="paypal" >
				<tr>
					<td colspan="2" align="center">
						<input type="hidden" value="<?php echo $total_payable;?>" name="paying_price" id="paying_price_paypal" /> Total: $<?php echo $total_payable;?> 
						<span class="span_amount" style="margin-left:25px"></span>
					</td>
				</tr>
				<tr>
					<td>PayPal Transaction ID</td>
					<td>
						<input type="text" name="trans_id" onchange="check_id(this);" value="" />
						<input type="hidden" name="amount" id="paypal-amount" value="" />
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<input type="button" disabled="" class="button confirm-btn" value="Pay Orders" onclick="confirmAim('paypal', 1);"   />
					</td>
				</tr>
			</table>
			<table style="display: none;" border="1" width="60%" cellpadding="5" cellspacing="0" class="tables" align="center" id="check" >
				<tr>
					<td colspan="2" align="center">
						<input type="hidden" value="<?php echo $total_payable;?>" name="paying_price" id="paying_price_check" /> $<?php echo $total_payable;?> Total
						<span class="span_amount" style="margin-left:25px"></span>
					</td>
				</tr>
				<tr>
					<td>Check No: </td>
					<td><input type="text" name="check" value="" /></td>
				</tr>
				<tr>
					<td>Amount:  </td>
					<td><input type="text" id="check-amount" name="amount" value="" /></td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<input type="button" class="button confirm-btn" value="Pay Orders" onclick="confirmAim('check', 1);"   />
					</td>
				</tr>
			</table>
		</div>
	</div>			
	<br />


</body>
<script>
	$('input[name=method]').change(function () {
		$('.tables').hide();
		$('#'+$(this).val()).show();
	});
</script>
<script>
	function check_id (t) {
		if ($(t).val()) {
			$.ajax({
				url: 'bulk_pay_order.php',
				type: 'post',
				data:{'id': $(t).val(), 'amount': $('#paying_price_paypal').val(), 'action': 'checkID'},
				dataType: 'json',
				success: function(json) {
					if (json['error']) {
						alert(json['error']);
						$(t).parent().parent().parent().find('span').html('Paid: $'+ json['amount']);
					}

					if (json['success']) {
						$(t).parent().parent().parent().find('.confirm-btn').removeAttr('disabled');
						$(t).parent().parent().parent().find('span').html('Paid: $'+ json['amount']);
						$('#paypal-amount').val(json['amount']);
						$(t).attr('readonly', 'readonly');
					}
				}
			});
		}
	}
	function confirmAim(table, exit) {
		var status = true;
		var url = 'bulk_pay_order.php?order_ids=<?php echo $_GET['order_ids']; ?>';
		if (table == 'card') {
			url = 'ajax_bulk_pay_order.php?order_ids=<?php echo $_GET['order_ids']; ?>';
		}
		if (table != 'card') {
			if ($('input[name=trans_id]').val() || $('input[name=check]').val()) {

			} else {
				alert('Please Enter Check # or Transaction ID');
				return false;
			}
		}
		if (table == 'check' && !$('#check-amount').val()) {
			alert('Please Enter Check Amount');
			return false;
		}
		$.ajax({
			url: url,
			type: 'post',
			data:$('#'+ table +' :input'),
			dataType: 'json',		
			beforeSend: function() {
				$('.confirm-btn').attr('disabled', true);
				$('.confirm-btn').val('Processing...');
			},
			complete: function() {
				
			},				
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
					$('.confirm-btn').attr('disabled', false);
					$('.confirm-btn').val('Update');
				}

				if (json['success']) {
					alert(json['success']);
					$('.confirm-btn').attr('disabled', true);
					$('.confirm-btn').val('Please Wait');
					if (exit) {
						window.parent.location.reload();
					}
				}
			}
		});

	}
</script>
</html>