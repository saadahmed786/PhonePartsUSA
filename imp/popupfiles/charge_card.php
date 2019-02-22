<?php
require_once("../auth.php");
include_once '../inc/split_page_results.php';
include_once '../inc/functions.php';
if ($_GET['payOrderIds']) {
	$orders = array();
	$gtotal = 0;
	$tpaid = 0;
	foreach (explode(',', $_GET['payOrderIds']) as $i => $order_id) {
		$orders[$i] = $db->func_query_first("SELECT * FROM inv_orders a,inv_orders_details b  WHERE a.order_id=b.order_id AND a.order_id='".$order_id."'");
		$total = $db->func_query_first_cell("SELECT SUM(product_price) FROM inv_orders_items where order_id='".$order_id."'");
		
		$vouchers = $db->func_query('SELECT SUM(`a`.`amount`) AS `used` FROM `oc_voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND a.`' . (($orders[$i]['store_type'] == 'po_business')? 'inv_order_id': 'order_id') . '` = "'. $order_id .'"');
		$_tax = round($db->func_query_first_cell('SELECT SUM(`value`) FROM `oc_order_total` WHERE `order_id` = "'. $order_id .'" AND `code` = "tax"'),2);
		$orders[$i]['order_price'] = $total + $orders['shipping_cost'] + $vouchers['used'] + $_tax;
		$tpaid += $orders[$i]['paid_price'];
		$gtotal += $orders[$i]['order_price'];
	}
	$remain = $gtotal - $tpaid;

}
if ($_GET['order_id']) {
	$order = $db->func_query_first("SELECT * FROM inv_orders a,inv_orders_details b  WHERE a.order_id=b.order_id AND a.order_id='".$_GET['order_id']."'");
	$total = $db->func_query_first_cell("SELECT SUM(product_price) FROM inv_orders_items where order_id='".$_GET['order_id']."'");
	
	$vouchers = $db->func_query_first('SELECT SUM(`a`.`amount`) AS `used` FROM `oc_voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND ' . (($orders[$i]['store_type'] == 'web')? 'cast(a.order_id as char(50))': 'a.inv_order_id') . ' = "'. $order['order_id'] .'"');
	$_tax = round($db->func_query_first_cell('SELECT SUM(`value`) FROM `oc_order_total` WHERE `order_id` = "'. $order['order_id'] .'" AND `code` = "tax"'),2);
	
	$order['order_price'] = ($total + $order['shipping_cost'] + $_tax) + $vouchers['used'];	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Payment Status</title>
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script src="../js/ccFormat.js"></script>
	<script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link href='../include/style.css' rel='stylesheet' />


	
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
		body{
			font-size:15px;
			font-weight: bold;
		}
		.red td{ box-shadow:1px 2px 5px #990000;}
	</style>
</head>
<body>
	<div align="center" style="display:none"> 
		<?php //include_once '../inc/header.php';?>
	</div>

	<?php if($_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php else:?>
		
	<?php endif;?>

	<div align="center">
		<div  id="aim_div">


			<?php

			$months = array();
			for ($i = 1; $i <= 12; $i++) {
				$month_name = strftime('%B', mktime(0, 0, 0, $i, 1, 2000));
				$months[] = array(
					'text'  => sprintf('%02d', $i).' - '.$month_name[0].''.$month_name[1].''.$month_name[2], 
					'value' => sprintf('%02d', $i)
					);
			}

			$today = getdate();

			$year_expire = array();

			for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
				$year_expire[] = array(
					'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
					'value' => strftime('%y', mktime(0, 0, 0, 1, 1, $i)) 
					);
			}
			?>

			<?php if ($_GET['payOrderIds']) { ?>
			<table  width="100%" cellpadding="5" cellspacing="0" align="center" >
				<tr >
					<td colspan="2" align="center">
						<?php
						if($tpaid==0.00)
						{
							?>
							<input type="radio" onclick="calculatePartialValue('25', <?= $gtotal; ?>);" name="paying_price"  /> 25% <input name="paying_price" type="radio" onclick="calculatePartialValue('100', <?= $gtotal; ?>);" id="paying_price" /> 100%
							<?php
						}
						else
						{
							?>
							<input type="radio" checked="" onclick="calculatePartialValue('0', <?= $remain; ?>);" name="paying_price" id="paying_price" /> Remaining
							<?php	
						}
						?>
						<span id="span_amount" style="margin-left:25px"></span>

					</td>

				</tr>

				<tr style="display:none">
					<td>Card Owner:</td>
					<td><input type="text" onkeyup="copyThis(this, 'cc_owner');" value="" /></td>
				</tr>
				<tr>
					<td>Card Number:</td>
					<td><input type="text" id="card_number" maxlength="20" onkeyup="copyThis(this, 'cc_number');" value="" /></td>
				</tr>
				<tr>
					<td>Card Expiry Date:</td>
					<td><select onchange="copyThis(this, 'cc_expire_date_month');">
						<?php foreach ($months as $month) { ?>
						<option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
						<?php } ?>
					</select>
					/
					<select onchange="copyThis(this, 'cc_expire_date_year');">
						<?php foreach ($year_expire as $year) { ?>
						<option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
						<?php } ?>
					</select></td>
				</tr>
				<tr>
					<td>Card Security Code (CVV2):</td>
					<td><input type="text" onkeyup="copyThis(this, 'cc_cvv2');" value="" size="3" /></td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="button" class="button" value="Charge Card" onclick="submitPayments();" id="confirm-btn"  />
					</td>
				</tr>

			</table>
			<?php $last = (count($orders) - 1); ?>
			<?php foreach ($orders as $key => $order) { ?>
			<table style="display: none;" border="1" width="60%" cellpadding="5" cellspacing="0" align="center" id="aim_table<?= $key; ?>" >
				<tr style="display:none">
					<td>Card Owner:</td>
					<td><input type="text" name="cc_owner" class="cc_owner" value="" /></td>
				</tr>
				<tr>
					<td>Card Number:</td>
					<td><input type="text" id="card_number" maxlength="20" name="cc_number" class="cc_number" value="" /></td>
				</tr>
				<tr>
					<td>Card Expiry Date:</td>
					<td><select name="cc_expire_date_month" class="cc_expire_date_month">
						<?php foreach ($months as $month) { ?>
						<option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
						<?php } ?>
					</select>
					/
					<select name="cc_expire_date_year" class="cc_expire_date_year">
						<?php foreach ($year_expire as $year) { ?>
						<option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
						<?php } ?>
					</select></td>
				</tr>
				<tr>
					<td>Card Security Code (CVV2):</td>
					<td><input type="text" name="cc_cvv2" class="cc_cvv2" value="" size="3" /></td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<input type="hidden" type="hidden" name="orders[order_id]" value="<?php echo $order['order_id'];?>">

						<input type="hidden" name="orders_details[first_name]" value='<?php echo $order['first_name'];?>'>
						<input type="hidden" name="orders_details[last_name]" value='<?php echo $order['last_name'];?>'>
						<input type="hidden" name="orders[email]" value='<?php echo $order['email'];?>'>
						<input type="hidden" name="orders_details[phone_number]" value='<?php echo $order['phone_number'];?>'>
						<input type="hidden" name="orders_details[address1]" value='<?php echo $order['address1'];?>'>
						<input type="hidden" name="orders_details[city]" value='<?php echo $order['city'];?>'>
						<input type="hidden" name="orders_details[state]" value='<?php echo $order['state'];?>'>
						<input type="hidden" name="orders_details[zip]" value='<?php echo $order['zip'];?>'>
						<input type="hidden" name="total" data-order-price="<?= ($order['paid_price'] != 0.00)? $order['order_price'] - $order['paid_price'] : $order['order_price'] ;?>" value=''>

						<input type="button" class="button submitPayment" value="Charge Card" onclick="confirmAim('aim_table<?= $key; ?>', <?= ($key == $last)? '1': '0'; ?>);" id="confirm-btn"  />
					</td>

				</tr>
			</table>
			<?php } ?>
			<?php } ?>

			<?php if ($_GET['order_id']) { ?>
			<table border="0" width="100%" cellpadding="5" cellspacing="0" align="center" id="aim_table" >
				<tr style="display: none">
					<td colspan="2" align="center">
					    <?php
						if($order['store_type']=='web')
						{
							$_voucher_query = 'cast(a.order_id as char(50)) = "'. $_GET['order_id'] .'"';
						}
						else
						{
							$_voucher_query = 'cast(a.inv_order_id as char(50)) = "'. $_GET['order_id'] .'"';
						}

						$vouchers = $db->func_query('SELECT *, `a`.`amount` as `used`, `b`.`amount` as `remain` FROM `oc_voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND '.$_voucher_query.' ');
						$vouch_applied_amount =0;
						foreach ($vouchers as $voucher) {
							$voucher['used'] = $voucher['used']*-1;
							$vouch_applied_amount = $vouch_applied_amount + $voucher['used'];

						}

						$order['order_price'] = $order['order_price'] - $vouch_applied_amount;
						?>
						<?php
						if($order['paid_price']==0.00 && $_GET['payment'] != 'full')
						{
							?>
							<input name="paying_price" id="paying_price" type="radio" onclick="calculatePartialValue('100', <?= $order['order_price']?>);" /> 100% <input type="radio" onclick="calculatePartialValue('25', <?= $order['order_price']?>);" name="paying_price"  /> 25% 
							<?php
						}
						else
						{
							?>
							<input type="radio" onclick="calculatePartialValue('0', <?= $order['order_price'] - $order['paid_price']; ?>);" name="paying_price" id="paying_price" /> <?= ($_GET['payment'] == 'full')? 'Total': 'Remaining'; ?>
							<?php	
						}
						?>
						<span id="span_amount" style="margin-left:25px"></span>

					</td>

				</tr>
				<tr style="display:none">
					<td>Card Owner:</td>
					<td><input type="text" name="cc_owner" value="" /></td>
				</tr>
				<tr>
					<td width="40%">Card Number:</td>
					<td><input type="text" id="card_number" maxlength="20" name="cc_number" value="" /></td>
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

				<td>Amount:</td>
				<td><input type="number" name="total" value='0' step=".01" required="" style="width:90%"></td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<input type="hidden" type="hidden" name="orders[order_id]" value="<?php echo $order['order_id'];?>">

						<input type="hidden" name="orders_details[first_name]" value='<?php echo $order['first_name'];?>'>
						<input type="hidden" name="orders_details[last_name]" value='<?php echo $order['last_name'];?>'>
						<input type="hidden" name="orders[email]" value='<?php echo $order['email'];?>'>
						<input type="hidden" name="orders_details[phone_number]" value='<?php echo $order['phone_number'];?>'>
						<input type="hidden" name="orders_details[address1]" value='<?php echo $order['address1'];?>'>
						<input type="hidden" name="orders_details[city]" value='<?php echo $order['city'];?>'>
						<input type="hidden" name="orders_details[state]" value='<?php echo $order['state'];?>'>
						<input type="hidden" name="orders_details[zip]" value='<?php echo $order['zip'];?>'>
						

						<input type="button" class="button" value="Charge Card" onclick="confirmAim('aim_table', 1);" id="confirm-btn"  />
					</td>

				</tr>
			</table>
			<?php } ?>
		</div>
	</div>			
	<br />


</body>
</html>  
<script>
	function submitPayments() {
		$('.submitPayment').each(function () {
			$(this).click();
		});
	}
	function copyThis(t, name) {
		currentVal = $(t).val();
		$('.' + name).each(function () {
			$(this).val(currentVal);
		});
	}

	function setTotal(per) {
		$('input[name="total"]').each(function () {
			var order_price = $(this).attr('data-order-price');
			if(per=='0') {
				perVal = parseFloat(order_price);
				$(this).val(perVal);
				console.log(per);
				console.log(perVal);
			} else {
				perVal = (per * parseFloat(order_price)) / 100;	
				$(this).val(perVal.toFixed(2));	
			}
		});

	}

	function calculatePartialValue(per, order_price) {
		
		if(per=='0')
		{
			perVal = parseFloat(order_price);
			$('input[name=total]').val(perVal.toFixed(2));	

		}
		else
		{
			perVal = (per * parseFloat(order_price)) / 100;	
			$('input[name=total]').val(perVal.toFixed(2));	
		}
		$('#span_amount').html('<strong>Amount: </strong>$'+perVal.toFixed(2));

		<?= ($_GET['payOrderIds'])? 'setTotal(per);': '';?>
	}
	$(document).ready(function(e) {
		$('#paying_price').click();
	});

</script>
<script>

	function confirmAim(table, exit) {
		var status = true;

		$.ajax({
			url: '../ajax_payflow_send.php',
			type: 'post',
			data:$('#'+ table +' :input'),
			dataType: 'json',		
			beforeSend: function() {
				$('#confirm-btn').attr('disabled', true);
				$('#confirm-btn').val('Processing...');
			},
			complete: function() {
				$('#confirm-btn').attr('disabled', false);
				$('#confirm-btn').val('Charge Card');
			},				
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
				}

				if (json['success']) {
					alert(json['success']);
					if (exit) {
						window.parent.location.reload();
					}
						// alert(json['success']);
						// $('input[name=payment_method]',window.parent.document).val('Credit Card / Debit Card (Authorize.Net)');
						// $('input[name=paid_price]',window.parent.document).val($('input[name=total]').val());
						// $('input[name=update]',window.parent.document).click();
					}
				}
			});

	}
</script>