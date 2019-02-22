<?php
require_once("auth.php");
require_once("inc/functions.php");
if (!$_SESSION['vouchers']) {
	exit;
}
$table = "`oc_voucher`";

if ($_POST['submit'] == 'checkVoucher') {
	$code = $_POST['vcode'];
	$query = "SELECT * FROM $table WHERE `code` = '$code'";
	$vcode = $db->func_query_first($query);
	if ($vcode) {
		$datajson = array('error' => 1, 'msg' => 'Voucher Code Already Exist!');
	} else {
		$datajson = array('success' => 1, 'msg' => 'Verified!');
		
	}
	echo json_encode($datajson);
	exit;
}
if ($_POST['submit'] == 'getProducts') {
	$status = array( 'Shipped', 'Refunded', 'Processed', 'Completed', 'Active', 'Issued', 'Paid');
	$order_id = $_POST['order'];
	$query = "SELECT 
	`ioi`.*
	FROM
	`inv_orders_items` AS `ioi`
	WHERE `ioi`.`order_id` = '$order_id'";
	$products = $db->func_query($query);
	if (in_array($db->func_query_first_cell('SELECT `order_status` FROM `inv_orders` WHERE `order_id` = "'. $order_id .'"'), $status)) {
		$index = 0;
		if ($products) {
			$info = $db->func_query_first('SELECT `email`, `customer_name`  FROM `inv_orders` WHERE `order_id` = "'. $order_id .'"');
			$datajson = array('success' => 1, 'email' => $info['email'], 'name' => $info['customer_name']);
			$datajson['products'] = array();
			foreach ($products as $product) {
				if ($product['product_sku'] != 'SIGN') {
					$x = (int) $product['product_qty'];
					$ret = (int) $db->func_query_first_cell('SELECT COUNT(`id`) FROM `inv_voucher_products` WHERE `order_id` = "'. $order_id .'" AND `sku` = "'. $product['product_sku'] .'"');
					for ($i=$x; $i > 0; $i--) {
						$datajson['products'][$index]['sku'] = $product['product_sku'];
						$datajson['products'][$index]['price'] = $product['product_price'];
						$datajson['products'][$index]['order_id'] = $order_id;
						if ($i > $ret) {
							$datajson['products'][$index]['active'] = 1;
						} else {
							$datajson['products'][$index]['active'] = 0;
							
						}
						$index++;
					}
				}
			}
		} else {
			$datajson = array('error' => 1, 'msg' => 'Order Not Found');
		}
	} else {
		$datajson = array('error' => 1, 'msg' => 'Invalid Order');
	}
	
	
	echo json_encode($datajson);
	exit;
}


if ($_GET['edit']) {
	$voucher_id = $_GET['edit'];
	$voucher = $db->func_query_first('SELECT * FROM '. $table .' WHERE `voucher_id` = "'. $voucher_id .'"');
	$vProducts = $db->func_query('SELECT * FROM `inv_voucher_products` WHERE `voucher_id` = "'. $voucher_id .'"');
	$vComments = $db->func_query('SELECT * FROM `inv_voucher_comments` WHERE `voucher_id` = "'. $voucher_id .'" ORDER By `id` DESC');
	if (!$voucher) {
		$_SESSION['message'] = "Message Not Found";
		header("Location:vouchers_manage.php");
		exit;
	}
}
if ($_POST['update']) {
	$comment = $_POST['comment'];
	unset($_POST['update'], $_POST['comment']);
	if ($_SESSION['voucher_status']) {

		$log = 'Voucher '. linkToVoucher($voucher_id, $host_path, $_POST['code']) .' was ' . (($_POST['status'])? 'Enabled': 'Disabled');
		unset($_POST['to_name'], $_POST['to_email'], $_POST['code'], $_POST['amount']);
	}
	if ($_SESSION['vouchers_update']) {

		$log = 'Voucher '. linkToVoucher($voucher_id, $host_path, $_POST['code']) .' was updated (amount '. $_POST['amount'] .' for ' . linkToProfile($_POST['to_email']) . ' set ' . (($_POST['status'])? 'Enabled)': 'Disabled)');

		unset($_POST['to_name'], $_POST['code']);
	}

	$enable_check = $db->func_query_first_cell("select status from oc_voucher where voucher_id='".$voucher_id."'");
	$voucher_code = $db->func_query_first_cell("SELECT code from oc_voucher where voucher_id='".$voucher_id."'");
	if($enable_check==1 && $_POST['status']==0)
	{

		$_balance = $db->func_query_first_cell("SELECT (COALESCE(a.`amount`,0) + COALESCE(SUM(b.`amount`),0)) balance FROM `oc_voucher` a LEFT OUTER JOIN `oc_voucher_history` b ON a.`voucher_id` = b.`voucher_id` where a.status=1 and a.voucher_id='".$voucher_id."'");
		if((float)$_balance>0)
		{
			$vouch_id = addVoucher('','store_credit',($_balance*(-1)),linkToVoucher($voucher_id,'', $voucher_code));
       		$db->db_exec("UPDATE inv_vouchers SET voucher_id='".(int)$voucher_id."',description='Disabled Credit' where id='".$vouch_id."'");
			

					$accounts = array();
					$accounts['description'] = $voucher_code.' Disabled';
					$accounts['debit'] = 0.00;
					$accounts['credit'] = ($_balance<0?$_balance*(-1):$_balance);
					$accounts['customer_email'] = $_POST['to_email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'store_credit';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // disabled store credit


					$accounts = array();
					$accounts['description'] = $voucher_code.' Disabled';
					$accounts['credit'] = 0.00;
					$accounts['debit'] = ($_balance<0?$_balance*(-1):$_balance);
					$accounts['customer_email'] = $_POST['to_email'];
					$accounts['type']='store_credit';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // disabled store credit


		}
		
	}

	elseif($enable_check==0 && $_POST['status']==1)
	{

		// echo "SELECT (COALESCE(a.`amount`,0) + COALESCE(SUM(b.`amount`),0)) balance FROM `oc_voucher` a LEFT OUTER JOIN `oc_voucher_history` b ON a.`voucher_id` = b.`voucher_id` where a.status=1 and a.voucher_id='".$voucher_id."'";exit;

		$_balance = $db->func_query_first_cell("SELECT (COALESCE(a.`amount`,0) + COALESCE(SUM(b.`amount`),0)) balance FROM `oc_voucher` a LEFT OUTER JOIN `oc_voucher_history` b ON a.`voucher_id` = b.`voucher_id` where a.status=0 and a.voucher_id='".$voucher_id."'");
		
		if((float)$_balance>0)
		{
			$vouch_id = addVoucher('','store_credit',($_balance),linkToVoucher($voucher_id,'', $voucher_code));
       		$db->db_exec("UPDATE inv_vouchers SET voucher_id='".(int)$voucher_id."',description='Re-enable Credit' where id='".$vouch_id."'");


       				$accounts = array();
					$accounts['description'] = $voucher_code.' Re-Enabled';
					$accounts['credit'] = 0.00;
					$accounts['debit'] = ($_balance<0?$_balance*(-1):$_balance);
					$accounts['customer_email'] = $_POST['to_email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'store_credit';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // disabled store credit


					$accounts = array();
					$accounts['description'] = $voucher_code.' Re-Enabled';
					$accounts['debit'] = 0.00;
					$accounts['credit'] = ($_balance<0?$_balance*(-1):$_balance);
					$accounts['customer_email'] = $_POST['to_email'];
					$accounts['type']='store_credit';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // disabled store credit

			
		}


		// $vouch_id = addVoucher('','store_credit',($_POST['amount']),linkToVoucher($voucher_id,'', $voucher_code));
       		// $db->db_exec("UPDATE inv_vouchers SET voucher_id='".(int)$voucher_id."',description='Re-enable Credit' where id='".$vouch_id."'");
	}

	$data = array();
	$data['voucher_id'] = $voucher_id;
	$data['user_id'] = $_SESSION['user_id'];
	$data['comment'] = $_SESSION['login_as'] . ' updated Voucher';
	$data['comment'] .= '<table>';
	foreach ($_POST as $key => $value) {
		if ($key == 'status'){
			$value = ($value)? 'Enabled': 'Disabled';
		}
		if ($key == 'reason_id'){
			$value = $db->func_query_first_cell('SELECT reason FROM inv_voucher_reasons where id = "'.$value.'"');
			$key = 'reason';
		}
		$data['comment'] .= '<tr><th>'. ucfirst(str_replace('_', ' ', $key)) .'</th><td>'. $value .'</td></tr>';
	}

	$data['comment'] .= '</table>';
	$data['comment'] .= '<p>' . $comment . '</p>';
	$data['date_added'] = date('Y-m-d H:i:s');


	$db->func_array2insert('`inv_voucher_comments`', $data);
    unset($_POST['update_reason']);
	$db->func_array2update($table, $_POST, '`voucher_id` = "'. $voucher_id .'"');




	actionLog($log);

	$_SESSION['message'] = "Voucher Updated";
	header("Location:vouchers_manage.php");
	exit;
}
if ($_POST['add']) {
	if ($_SESSION['vouchers_update']) {
		
		$skus = $_POST['sku'];
		$order_ids = $_POST['order_id'];
		$prices = $_POST['price'];
		$comment = $_POST['comment'];

		unset($_POST['add'], $_POST['sku'], $_POST['order_id'], $_POST['price'], $_POST['comment']);

		$_POST['voucher_theme_id'] = '8';
		$_POST['from_name'] = $_SESSION['login_as'];
		$_POST['from_email'] = $_SESSION['email'];
		$_POST['user_id'] = $_SESSION['user_id'];
		$_POST['date_added'] = date('Y-m-d H:i:s');
		$_POST['is_manual'] = '1';
		$_POST['reason_id'] = $_POST['reason_id'];
		
		$id = $db->func_array2insert($table, $_POST);
		if($_POST['status']==1)
		{
			$vouch_id = addVoucher('','store_credit',($_POST['amount']),linkToVoucher($id,'', $_POST['code']));
       		$db->db_exec("UPDATE inv_vouchers SET voucher_id='".(int)$id."',description='Credit Issued' where id='".$vouch_id."'");	

       				$accounts = array();
					$accounts['description'] = $voucher_code.' Issued Manually';
					$accounts['credit'] = 0.00;
					$accounts['debit'] = ($_POST['amount']<0?$_POST['amount']*(-1):$_POST['amount']);
					$accounts['customer_email'] = $_POST['to_email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'store_credit';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // disabled store credit


					$accounts = array();
					$accounts['description'] = $voucher_code.' Issued Manually';
					$accounts['debit'] = 0.00;
					$accounts['credit'] = ($_POST['amount']<0?$_POST['amount']*(-1):$_POST['amount']);
					$accounts['customer_email'] = $_POST['to_email'];
					$accounts['type']='store_credit';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // disabled store credit

		}



		$log = 'Voucher '. linkToVoucher($id, $host_path, $_POST['code']) .' was created of amount '. $_POST['amount'] .' for ' . linkToProfile($_POST['to_email']) . ' as ' . (($_POST['status'])? 'Enabled': 'Disabled');
		actionLog($log);

		if ($id) {
			if ($skus) {
				$reason = $db->func_query_first_cell('SELECT reason FROM inv_voucher_reasons where id = "'.$_POST['reason_id'].'"');
				$products = array();
				foreach ($skus as $key => $data) {
					$products[$key]['voucher_id'] = $id;
					$products[$key]['order_id'] = $order_ids[$key];
					$products[$key]['sku'] = $skus[$key];
					$products[$key]['price'] = $prices[$key];
					$products[$key]['reason'] = $reason;
				}
			}
			foreach ($products as $product) {
				$db->func_array2insert('`inv_voucher_products`', $product);
			}

			$data = array();
			$data['voucher_id'] = $id;
			$data['user_id'] = $_SESSION['user_id'];
			$data['comment'] = $_SESSION['login_as'] . ' created this Voucher';
			$data['comment'] .= '<p>' . $comment . '</p>';
			$data['date_added'] = date('Y-m-d H:i:s');

			$db->func_array2insert('`inv_voucher_comments`', $data);

			$_SESSION['message'] = "Voucher Added";
			header("Location:vouchers_manage.php");
			exit;
		}

	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Vouchers | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script>
		var errorCode = true;
		$(document).ready(function(e) {
			$('.fancybox3').fancybox({ width: '90%' , autoCenter : true , autoSize : true });
		});

		function checkWhiteSpace (t) {
			if ($(t).val() == ' ') {
				$(t).val('');
			}
		}

		function allowFloat (t) {
			var input = $(t).val();
			var valid = input.substring(0, input.length - 1);
			if (isNaN(input)) {
				$(t).val(valid);
			}
		}

		function allowInt (t) {
			var re = /^-?[0-9]+$/;
			var input = $(t).val();
			var valid = input.substring(0, input.length - 1);
			if (!re.test(input)) {
				$(t).val(valid);
			}
		}

		function verifyEmail (t) {
			var email = $(t).val();
			var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
			if (!re.test(email)) {
				$(t).parent().find('.error').remove();
				$(t).parent().append('<span class="error">Enter Correct Email!<span>');
			} else {
				$(t).parent().find('.error').remove();
			}
		}

		function appendProducts (t) {
			$('input[type="submit"]').attr('disabled', 'disabled');
			var order_id = $(t).val();
			var main = $(t).parent().parent();
			var productHolder = main.find('.products');
			if (order_id != '') {
				$.ajax({
					url: 'vouchers_create.php',
					type: 'POST',
					dataType: 'json',
					data: {'order': order_id, 'order': order_id, 'submit': 'getProducts'},
					success: function(json){
						$('input[type="submit"]').removeAttr('disabled');
						if (json['success']) {
							$('input[name="to_email"]').val(json['email']);
							$('input[name="to_name"]').val(json['name']);
							var templete = '<table width="100%">'+
							'<thead>'+
							'<tr>'+
							'<th>&nbsp;</th>'+
							'<th>SKU</th>'+
							'<th>Price</th>'+
							'</tr>'+
							'</thead>'+
							'<tbody>';

							for (i = 0; i < json['products'].length; i++) {
								var active = 'disabled="disabled"'
								if (json['products'][i]['active']) {
									active = '';
								}
								templete = templete + '<tr>'+
								'<td>'+
								'<input class="price" '+ active +' onchange="addProduct(this);" data-price="'+ json['products'][i]['price'] +'" type="checkbox" name="sku[]" value="'+ json['products'][i]['sku'] +'">'+
								'<div style="display: none;">'+
								'<input type="checkbox" name="price[]" value="'+ json['products'][i]['price'] +'">'+
								'<input type="checkbox" name="order_id[]" value="'+ json['products'][i]['order_id'] +'">'+
								'</div>'+
								'</td>'+
								'<td>'+ json['products'][i]['sku'] +'</td>'+
								'<td>'+ json['products'][i]['price'] +'</td>'+
								'</tr>';
							}
							templete = templete + '</tbody>'+
							'</table>';
							productHolder.text('');
							productHolder.append(templete);
						}
						if (json['error']) {
							productHolder.text(json['msg']);
						}
					}
				});

}
}

function verifyCode (t) {
	var code = $(t).val();
	$('input[type="submit"]').attr('disabled', 'disabled');
	if (code != '') {
		$.ajax({
			url: 'vouchers_create.php',
			type: 'POST',
			dataType: 'json',
			data: {'vcode': code, 'submit': 'checkVoucher'},
			success: function(json){
				$('input[type="submit"]').removeAttr('disabled');
				if (json['error']) {
					alert(json['msg']);
					$(t).val('');
				}
				<?php
				if(!$_GET['edit'])
				{
					?>
					errorCode = false;
					<?php
				}
				?>
			}
		});
	}
}

function addProduct (t) {
	var productsHolder = $(t).parent().parent().parent();
	var amountHolder = $(t).parent().parent().parent().parent().parent().parent().find('.totalAmount').find('span');
	var amountH = amountHolder.parent().find('.subTotal');
	var table = amountHolder.parent().parent().parent();
	if ($(t).is(':checked')) {
		$(t).parent().find('input[type="checkbox"]').each(function() {
			$(this).attr('checked', 'checked');
		});
	} else {
		$(t).parent().find('input[type="checkbox"]').each(function() {
			$(this).removeAttr('checked');
		});
	}
	var totalPrice = 0;
	productsHolder.find('.price').each(function() {
		var price = 0;
		if ($(this).is(':checked')) {
			var price = $(this).attr('data-price');
		}
		totalPrice = totalPrice + parseFloat(price);
	});
	amountHolder.text('$' + totalPrice.toFixed(2));
	amountH.val(totalPrice.toFixed(2));
	var grandTotal = 0;
	table.find('.subTotal').each(function() {
		grandTotal = grandTotal + parseFloat($(this).val());
	});

	$('input[name="amount"]').val(grandTotal.toFixed(2));
}

function addRow () {
	var template = '<tr>'+
	'<td><input onchange="appendProducts(this);" type="text" /></td>'+
	'<td class="products">'+
	'</td>'+
	'<td class="totalAmount">'+
	'<span></span>'+
	'<input type="hidden" class="subTotal">'+
	'</td>'+
	'<td><a href="javascript:void(0);" onclick="$(this).parent().parent().remove();">X</a></td>'+
	'</tr>';

	$('.orders').append(template);
}
function formsubmit() {
	var amount = $('input[name="amount"]').val();
	<?php
	if($_GET['edit'])
	{
		?>
		errorCode = false;
		<?php
	}
	?>

	if (amount == '' || errorCode == true) {
		alert('Enter Amount and Voucher code!');
		return false;
	} else {
		return true;
	}
}

</script>
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
		<form action="" method="post" onsubmit="return formsubmit();" enctype="multipart/form-data">
			<h2><?= (isset($voucher_id)) ? 'Edit': 'Add';?> Vouchers</h2>
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<th>Amount</th>
					<td><input type="text" onkeyup="allowFloat(this);" name="amount" value="<?= ($voucher_id) ? number_format((float)$voucher['amount'], 2, '.', ''): '';?>" <?= ($voucher_id) ? 'disabled="disabled"': '';?> /></td>
				</tr>  
				<tr>
					<th>Voucher code</th>
					<td><input type="text" required="" name="code" onchange="verifyCode(this);" onkeyup="checkWhiteSpace(this);" value="<?= ($voucher_id) ? $voucher['code']: '';?>" <?= ($voucher_id) ? 'disabled="disabled"': '';?> /></td>
				</tr>
				<tr>
					<th>Email</th>
					<td>
						<input type="email" required="" onchange="verifyEmail(this);" onkeyup="checkWhiteSpace(this);" name="to_email" value="<?= ($voucher_id) ? $voucher['to_email']: '';?>" />
						<input type="hidden" name="to_name" />
					</td>
				</tr>
				<?php if (!$voucher_id) { ?>
				<tr>
					<th>Select Products</th>
					<td>
						<table width="100%">
							<thead>
								<tr>
									<th width="30%">Order ID</th>
									<th width="30%">Product</th>
									<th width="30%">Amount</th>
									<th width="10%">Action</th>
								</tr>
							</thead>
							<tbody class="orders">
								<tr>
									<td><input onchange="appendProducts(this);" type="text" /></td>
									<td class="products">
									</td>
									<td class="totalAmount">
										<span></span>
										<input type="hidden" class="subTotal">
									</td>
									<td><a href="javascript:void(0);" onclick="$(this).parent().parent().remove();">X</a></td>
								</tr>
							</tbody>
						</table>
						<p><a href="javascript:void(0);" onclick="addRow();">Add More</a></p>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<th>Status</th>
					<td>
						<select name="status">
							<?php $options = array('1' => 'Enabled', '0' => 'Disabled'); ?>
							<?php foreach ($options as $key => $value) { ?>
							<option value="<?= $key; ?>" <?= ($voucher['status'] == $key)? 'selected="selected"': ''; ?>><?= $value; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
			<?php
					$cancel_check = $db->func_query_first_cell('SELECT reason_id FROM inv_order_cancel_report where order_id = "'.$voucher['order_id'].'"');
					if ($cancel_check) {
						$cancelled_reason = $db->func_query_first_cell('SELECT name FROM inv_order_reasons where id = "'.$check.'"');?>
					<tr>
				
					<th>Reason</th>
					<td>
						
					<input style="width: 300px;" type="text" readonly value="<?php echo $cancelled_reason; ?>">
					</td>
					</tr>
						
				<?php	}
					else if ($voucher['reason_id']) { 
						$reason = $db->func_query_first_cell('SELECT reason FROM inv_voucher_reasons where id = "'.$voucher['reason_id'].'"');
						?>
						<tr>
				
					<th>Reason</th>
						<td>
					<input type="hidden" name="reason_id" value="<?php echo $voucher['reason_id']; ?>">
					<input style="width: 300px;" type="text" readonly value="<?php echo $reason; ?>">
						</td>
						</tr>
				<?php } else if(!$_GET['edit']) { ?>
				<tr>
				
					<th>Reason</th>
					<td>
						<select required name="reason_id">
						
							<?php
							 $reasons = $db->func_query('SELECT * FROM inv_voucher_reasons where reason_type = "Manual" order by reason_type, reason asc'); ?>
							 <option value="">Select</option>
							<?php foreach ($reasons as $reason) { ?>
							<option value="<?= $reason['id']; ?>"  <?= ($voucher['reason_id'] == $reason['id'] && $voucher['reason_id'] != '')? 'selected="selected"': ''; ?>><?= $reason['reason_type']; ?> - <?= $reason['reason']; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
					<?php } ?>
				<?php
							 $products_reasons = $db->func_query('SELECT * FROM inv_voucher_products where voucher_id = "'.$_GET['edit'].'"'); 
							   
							 if($products_reasons){
							 	?>
				<tr>
					<?php if (strpos($products_reasons[0]['reason'],')')) {?>
					<th>Item RMA Results:</th>
				<?php } else { ?>
				<th>Item Removal Results:</th>
				<?php } ?>
					<td>
							
							<?php foreach ($products_reasons as $product) { ?>
							<?php echo $product['sku'];?>: <?php echo $product['reason']; ?> <br>
							<?php } ?>
					</td>
				</tr>
				<?php }?>
				<?php if ($_GET['edit']) { ?>
				<tr>
					<th>Voucher Update Reason:</th>
					<td>
						<select required name="update_reason">
						
							<?php
							 $reasons = $db->func_query('SELECT * FROM inv_voucher_reasons where location like "%Manual%" order by reason '); ?>
							 <option value="">Select</option>
							<?php foreach ($reasons as $reason) { ?>
							<option value="<?= $reason['reason']; ?>" ><?= $reason['reason']; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<th>Comment</th>
					<td>
						<textarea name="comment"></textarea>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input class="button" type="submit" name="<?= (isset($voucher_id)) ? 'update': 'add';?>" value="<?= (isset($voucher_id)) ? 'Update': 'Submit';?>" /></td>
				</tr>
			</table>
		</form>

		<?php if ($vProducts) { ?>
		<br><br>
		<h2>Products</h2>
		<table align="center" border="1" width="40%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			<thead>
				<tr>
					<th>#</th>
					<th width="30%">Order id</th>
					<th width="35%">Product</th>
					<th width="30%">Price</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($vProducts as $i => $product) { ?>
				<tr>
					<td><?= ($i +1) ?></td>
					<td><?= linkToOrder($product['order_id'], $host_path); ?></td>
					<td><?= $product['sku']; ?></td>
					<td><?= $product['price']; ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php } ?>

		<?php if ($vComments) { ?>
		<br><br>
		<h2>Comments</h2>
		<table align="center" border="1" width="40%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			<thead>
				<tr>
					<th>#</th>
					<th width="15%">User</th>
					<th width="55%">Comment</th>
					<th width="30%">Date</th>
				</tr>
			</thead>
			<tbody>

				<?php foreach ($vComments as $i => $vComment) { ?>
				<tr>
					<td><?= ($i) + 1 ?></td>
					<td><?= ($vComment['user_id'] == 0)? 'Admin': $db->func_query_first_cell('SELECT `name` FROM `inv_users` WHERE `id` = "'. $vComment['user_id'] .'"'); ?></td>
					<td><?= $vComment['comment']; ?></td>
					<td><?= americanDate($vComment['date_added']); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php } ?>
		<?php
		$histories = array();
		if($voucher_id)
		{

		$histories = $db->func_query("SELECT * FROM oc_voucher_history WHERE voucher_id='$voucher_id'");
		}

		?>
		<?php if ($histories) { ?>
		<br><br>
		<h2>History</h2>
		<table align="center" border="1" width="40%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			<thead>
				<tr>
					<th>Date</th>
					<th width="15%">Order #</th>
					<th width="55%">Customer</th>
					<th width="30%">Amount</th>
				</tr>
			</thead>
			<tbody>
				<?php $balance = $voucher['amount'];?>
				<?php foreach ($histories as  $history) { ?>
				<?php
			//	echo $balance - $history['amount'];exit;
				$balance = $balance +$history['amount'] ;

				$history['order_id'] = ($history['order_id'])? $history['order_id']: $history['inv_order_id'];
				?>
				<tr>
					<td><?=americanDate($history['date_added']);?></td>
					<td><?=linkToOrder($history['order_id'],$host_path);?></td>
					<td><?=linkToProfile($db->func_query_first_cell("SELECT email FROM inv_orders WHERE order_id='".$history['order_id']."'"),$host_path);?></td>
					<td align="center">$<?=number_format($history['amount'],2);?></td>
				</tr>
				<?php } ?>
				<tr style="background-color:#c0c0c0">
					<th colspan="3" align="right">Balance:</th>
					<th align="center">$<?=number_format($balance,2);?></th>


				</tr>
			</tbody>
		</table>
		<?php } ?>

		<br>

		<?php
		$histories = $db->func_query("SELECT * FROM inv_voucher_details WHERE voucher_id='$voucher_id'");

		?>
		<?php if ($histories) { ?>
		<br><br>
		<h2>History</h2>
		<table align="center" border="1" width="40%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			<thead>
				<tr>
					<th>Order ID</th>
					<th width="20%">RMA #</th>
					<th width="40%">Detail</th>
					<th width="10%">Source</th>
					<th width="10%">User</th>
				</tr>
			</thead>
			<tbody>
				
				<?php foreach ($histories as  $history) { ?>
				
				<?php
				$is_lbb = ($history['is_lbb']);
				$is_rma = ($history['is_rma']);
				$is_order_cancellation = ($history['is_order_cancellation']);
				$is_pos = ($history['is_pos']);
				$order_id = linkToOrder($history['order_id']);
				if($is_lbb)
				{
					$order_id = linkToLbbShipment($history['order_id']);
					$source = 'BuyBack';
				}

				$rma_number = '';
				$source = 'Order';
				if($is_rma)
				{
					$rma_number = linkToRma($history['rma_number']);
					$source = 'RMA';
				}

				if($is_order_cancellation)
				{
					$source = 'Cancellation';
				}
				if($is_pos)
				{
					$source = 'POS';
				}
				
				$history['item_detail'] = str_replace("<br>", "<br>=============<br>", $history['item_detail']);
				$history['item_detail'] = str_replace(", ", "<br>", $history['item_detail']);

				
				$oc_username = $db->func_query_first_cell("SELECT username FROM oc_user WHERE user_id='".$history['oc_user_id']."'")
				?>
				<tr>
				<td><?=$order_id;?></td>
				<td><?=$rma_number;?></td>
				<td>
					<?=$history['item_detail'];?>

				</td>
				<td><?=($source);?></td>
				<td><?=($history['user_id']!=''?get_username($history['user_id']):$oc_username);?></td>
				</tr>
				<?php } ?>
				
			</tbody>
		</table>
		<?php } ?>
	</div>
</body>
