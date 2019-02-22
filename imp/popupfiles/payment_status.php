<?php
require_once("../auth.php");
include_once '../inc/split_page_results.php';
include_once '../inc/functions.php';
if ($_POST['ids']) {
	// echo '<pre>';
	// print_r($_POST);
	// exit;
	$data = array();
	foreach (explode(',', $_POST['ids']) as $i => $orderId) {
		$db->db_exec("update inv_orders_details SET payment_method='". $_POST['payment_method'] ."' where order_id = '$orderId'");

		$paid_price = $db->func_query_first_cell("SELECT (sum(`shipping_cost`) + (SELECT SUM(product_price) FROM inv_orders_items where order_id = '$orderId')) AS `total` FROM inv_orders_details  WHERE order_id = '$orderId'");
		$checkOld = $db->func_query_first_cell("SELECT  paid_price FROM inv_orders WHERE order_id='$orderId'");
		if ($_POST['payment_amount']) {
			$paid_price = ($_POST['payment_amount'] * $paid_price) / 100;
		}
		$payment_detail = ($_POST['check_or_tran_1'] != '')? "payment_detail_1='" . $_POST['check_or_tran_1'] . "'" : "payment_detail_2='". $_POST['check_or_tran_2'] ."'";
		
		$query = "UPDATE inv_orders SET paid_price=$paid_price, $payment_detail WHERE order_id='$orderId'";
		$db->db_exec($query);
		$data[$i]['order_id'] = $orderId;
		$data[$i]['paid_price'] = $paid_price;
		$data[$i]['payment_detail'] = $payment_detail;

		$log = 'Amount of '. $paid_price .' was paid for Order # '. linkToOrder($orderId) .' Transaction Details are "'. $payment_detail .'"';
        actionLog($log);
	}
	$_SESSION['message'] = 'Order Details Updated';
	$json['success'] = 1;
	echo json_encode($json);
	exit;
}
if ($_GET['payOrderIds']) {
	$orderIds = '' . str_replace(',', '","', $_GET['payOrderIds']) . '';
	$order = array();
	$order['paid_price'] = $db->func_query_first_cell('SELECT sum(`paid_price`) AS `paid_price` FROM inv_orders  WHERE order_id in ("' . $orderIds . '")');
	$order['shipping_cost'] = $db->func_query_first_cell('SELECT sum(`shipping_cost`) AS `shipping_cost` FROM inv_orders_details  WHERE order_id in ("' . $orderIds . '")');
	$order['payment_method'] = 'check';
	$total = $db->func_query_first_cell('SELECT SUM(product_price) FROM inv_orders_items where order_id in ("' . $orderIds . '")');

	$order['order_price'] = $total + $order['shipping_cost'];

}

if ($_GET['order_id']) {
	$order = $db->func_query_first("SELECT * FROM inv_orders a,inv_orders_details b  WHERE a.order_id=b.order_id AND a.order_id='".$_GET['order_id']."'");
	$total = $db->func_query_first_cell("SELECT SUM(product_price) FROM inv_orders_items where order_id='".$_GET['order_id']."'");
	$order['order_price'] = $total + $order['shipping_cost'];
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
		.data td,.data th{
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
		<?php include_once '../inc/header.php';?>
	</div>

	<?php if($_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php else:?>
		<br /><br /> 
	<?php endif;?>

	<div align="center">
		<form action="" method="get">
			<table border="1" id="form-data" cellpadding="5" cellspacing="0" style="border-collapse:collapse;" width="100%">
				<tr>
					<td width="20%">
						<input type="radio"  name="payment_status" <?php if($order['payment_method']=='Credit Card / Debit Card (Authorize.Net)' || $order['payment_method']=='Credit or Debit Card (Processed securely by PayPal)' || $order['payment_method']=='' || $order['payment_method']=='check'){ echo 'checked="checked"';} ?> value="check"  /> Check 
					</td>

					<td rowspan="3">


						<div style="float:left">
							<?php
							if($order['paid_price']==0.00)
							{

								?>
								<span id="paying_price_span1"> <input type="radio" onclick="calculatePartialValue(this);" data-partial-amount="25" value="25" name="paying_price" id="paying_price1" /> 25%</span> <span id="paying_price_span2"> <input type="radio" onclick="calculatePartialValue(this);" data-partial-amount="100" value="100" name="paying_price" id="paying_price2" /> 100%</span> 
								<?php
							}
							else
							{
								?>
								<span id="paying_price_span2"> <input type="radio" onclick="calculatePartialValue(this);" data-partial-amount="0" value="0" name="paying_price" id="paying_price2" /> Remaining</span>
								<?php 
							}
							?> <span id="span_amount" style="margin-left:25px"></span><br /><br />
							<span class="check_or_tran_span">Check Number:</span> <input type="text" id="check_or_tran_1" name="check_or_tran_1" value="<?php echo $order['payment_detail_1'];?>" /><input type="text" name="check_or_tran_2" id="check_or_tran_2" value="<?php echo $order['payment_detail_2'];?>" />
						</div>

					</td>
				</tr>
				<tr>
					<td>
						<input type="radio"  name="payment_status"  value="paypal" <?php if($order['payment_method']=='paypal'){ echo 'checked="checked"';}?>  /> PayPal
					</td>
				</tr>
				<tr>
					<td>
						<input type="radio"  name="payment_status"  value="auth.net" <?php if($order['payment_method']=='auth.net'){ echo 'checked="checked"';}?>  /> Auth.Net
						<input type="hidden" name="payment_amount" value="100">
						<input type="hidden" name="payment_method" value='<?= $order['payment_method']; ?>'>
						<?php if ($orderIds) { ?>
						<input type="hidden" name="ids" value="<?= $_GET['payOrderIds']; ?>">
						<?php } ?>
					</td>
				</tr>
			</table>
			<br />
			<input type="hidden" id="amount" />
			<input type="button" name="search" value="Save Detail" class="button" onclick="addDetail(this);" />
		</form>
	</div>			
	<br />


</body>
</html>  
<script>
	$(document).ready(function(e) {
		$('input[name=payment_status]:checked').click();
		$('#paying_price2').click();
		<?php
		if($order['paid_price']>'0.00')
		{
			?>
			$('#check_or_tran_1').hide();
			<?php	
		}
		else
		{
			?>
			$('#check_or_tran_2').hide();
			<?php	
		}
		?>
	});
	$('input[name=paying_price]').on('click', function(e) {
		$('input[name="payment_amount"]').val($(this).val());
	});
	$('input[name=payment_status]').on('click',function(e){

		var my_val = $(this).val();
		$('input[name="payment_method"]').val(my_val);
		if(my_val=='check')
		{
			$('.check_or_tran_span').html('Check Number:');
			$('#paying_price_span1').hide();
			$('#paying_price2').click();

		}
		else
		{

			$('.check_or_tran_span').html('Transaction ID:');
			$('#paying_price_span1').show();

		}

	});

	function calculatePartialValue(obj)
	{
		var per = $(obj).attr('data-partial-amount');

		if(per=='0') {
			perVal = <?=$order['order_price'] - $order['paid_price'];?>;
			$('#amount').val(perVal.toFixed(2));	

		} else {
			perVal = (per * parseFloat(<?=$order['order_price'];?>)) / 100;	
			$('#amount').val(perVal.toFixed(2));	
		}

		$('#span_amount').html('<strong>Amount: </strong>$'+perVal.toFixed(2));
	}
	function addDetail(t) {
		<?php
		if($order['paid_price']>0)
		{
			?>
			if(jQuery.trim($('#check_or_tran_2').val())=='')
			{
				alert("Please provide with detail");
				return false;	

			}
			<?php

		}
		else
		{

			?>
			if(jQuery.trim($('#check_or_tran_1').val())=='')
			{
				alert("Please provide with detail");
				return false;	

			}
			<?php
		}
		?>
		<?php if ($_GET['order_id']) { ?>
			$('input[name=payment_method]',window.parent.document).val($('input[name=payment_status]:checked').val());	

			$('input[name=payment_detail_1]',window.parent.document).val($('#check_or_tran_1').val());			

			$('input[name=payment_detail_2]',window.parent.document).val($('#check_or_tran_2').val());			
			$('input[name=paid_price]',window.parent.document).val($('#amount').val());			
			$('input[name=update]',window.parent.document).click();
			<?php } ?>
			<?php if ($_GET['payOrderIds']) { ?>
				$.ajax({
					url: 'payment_status.php',
					type: 'post',
					data:$('#form-data input'),
					dataType: 'json',		
					beforeSend: function() {
						$(t).attr('disabled', true);
						$(t).val('Processing...');
					},
					complete: function() {
						$(t).attr('disabled', false);
						$(t).val('Update');
					},				
					success: function(json) {
						if (json['error']) {
							alert(json['error']);
						}

						if (json['success']) {
							window.parent.location.reload();
						}
					}
				});
				<?php } ?>
			}
		</script>