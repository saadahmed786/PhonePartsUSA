<?php
require_once("../auth.php");
include_once '../inc/split_page_results.php';
include_once '../inc/functions.php';
if($_POST['pay']){
		$order_id = $_POST['ord_id'];
		$ord_details = array();
		$ord_details['payment_method'] = 'Wire Transfer';
		$db->func_array2update("inv_orders_details",$ord_details,"order_id = '$order_id'");
		$order = array();
		$order['paid_price'] = $_POST['amount'];
		$order['payment_source'] = 'Paid';
		$order['is_cod_wire'] = '1';
		$order['cod_wire_ref'] = $_POST['ref_num'];
		$order['cod_wire_date'] = date('Y-m-d H:i:s');
		$db->func_array2update("inv_orders",$order,"order_id = '$order_id'");
		$_SESSION['message'] = "Payment Added through Wire Transfer.";
		echo "<script>window.close();parent.window.location.reload();</script>";
		exit;
	
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
	<script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
</head>
<body>
	<?php if($_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php else:?>
		<br /><br /> 
	<?php endif;?>

	<div align="center">
	<form method="post" action="">
			<?php if ($_GET['order_id']) { ?>
			<table border="1" width="60%" cellpadding="5" cellspacing="0" align="center" id="aim_table" >
				<tr>
					<td colspan="2" align="center">
						RECEIVE WIRE PAYMENT
					</td>
				</tr>
				<tr>
					<td>Payment Amount</td>
					<td><input id="amount" type="text" name="amount" value="" /></td>
				</tr>
				<tr>
					<td>Check or Ref #</td>
					<td><input id="ref_num" type="text" name="ref_num" value="" /></td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<input type="button" id="final_click" onclick="validateIt()" class="button" name="pay" value="Receive Payment"  />
						<input type="hidden" name="ord_id" value="<?php echo $_GET['order_id']; ?>">
					</td>

				</tr>
			</table>
			<?php } ?>
			</form>
	</div>			
	<br />


</body>
</html>  
<script type="text/javascript">
	function validateIt ()
	{
		if ($('#amount').val()=='' || parseFloat($('#amount').val())=='0.00' )
		{
			alert('Amount cannot be blanked or 0');
			// event.stopPropagation();
			return false;
		}
		if($('#ref_num').val()==''){
			alert('Please provide the reference #');

			return false;
		} 
		$('#final_click').attr('type','submit');
		$('#final_click').click();
	}
</script>