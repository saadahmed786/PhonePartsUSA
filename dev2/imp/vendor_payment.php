<?php
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
require_once("auth.php");
require_once("inc/functions.php");
$shipment_ids = $_GET['shipment_ids'];
$account_id = $_GET['vendor'];
$received_total = 0.00;


function getVoucherNo()
{
	global $db;
	$prefix = 'PV-';
	$last_number = $db->func_query_first_cell ( "select max(abs(replace(voucher_no,'$prefix',''))) as voucher_no from inv_accounts where voucher_no LIKE '%$prefix%'" );

			if ($last_number >= 999 && $last_number < 9999) {
				$rma_number = $prefix . "0" . ($last_number + 1);
			}
			elseif ($last_number >= 99 && $last_number < 999) {
				$rma_number = $prefix . "00" . ($last_number + 1);
			}
			elseif ($last_number >= 9) {
				$rma_number = $prefix . "000" . ($last_number + 1);
			}
			elseif ($last_number < 9) {
				$rma_number = $prefix . "0000" . ($last_number + 1);
			}
			else {
				$rma_number = $prefix . "" . ($last_number + 1);
			}

			return $rma_number;
}

if($shipment_ids)
{
	$shipment_ids = explode(",",$shipment_ids);
	foreach($shipment_ids as $id)
	{
		$shipment = $db->func_query_first("select * from inv_shipments where id='$id'");
$received_total+= $db->func_query_first_cell("select sum(qty_received * unit_price) as received_total 
			from inv_shipment_items where shipment_id = '".$id."'") / $shipment['ex_rate'];

	}
}
if($_POST['price'])
{
	$voucher_no = getVoucherNo();
	if($_POST['payment_method']=='Cash'){
		$account = 'CASH1';
	}
	else
	{
		$account = 'PAYPAL1';
	}
	

		$data = array();
		$data['voucher_date'] = date('Y-m-d ',strtotime($_POST['voucher_date']));
		$data['voucher_no'] = $voucher_no;
		$data['account_id'] = $account;
		$data['order_no'] =1;
		$data['debit'] = (float)$_POST['price'];
		$data['credit'] = 0.00;
		$data['description'] = $db->func_escape_string($_POST['description']);
		$data['contra_account'] = 'V'.$account_id;
		$data['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_accounts",$data);


		$data = array();
		$data['voucher_date'] = date('Y-m-d',strtotime($_POST['voucher_date']));
		$data['voucher_no'] = $voucher_no;
		$data['contra_account'] = $account;
		$data['order_no'] =2;
		$data['credit'] = (float)$_POST['price'];
		$data['debit'] = 0.00;
		$data['description'] = $db->func_escape_string($_POST['description']);
		$data['account_id'] = 'V'.$account_id;
		$data['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_accounts",$data);

		foreach($shipment_ids as $id)
		{
			$data = array();
			$data['voucher_no'] = $voucher_no;
			$data['shipment_id'] = $id;
			$db->func_array2insert("inv_account_shipments",$data);
		}
	echo '<h1>Payment details have been saved!</h1>';

	echo '<script>parent.location.reload();</script>';exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Store Credit</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
</head>

<body>
<div style="display:none">
<?php include_once 'inc/header.php';?>
</div>
	<div align="center">


		<br clear="all" />



		<div align="center">
			<form action="" id="myFrm" method="post">
				<h2>Issue Payment</h2>
				<table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;width:90%">
					
					<tr>

					<td>Payment Date:</td>
					<td><input name="voucher_date" class="datepicker" readonly=""></td>

					</tr>
					<tr>

					<td>Total Amount:</td>
					<td>$<?=number_format($received_total,2);?></td>

					</tr>
					<tr>
						<td>Payable</td>
						<td>
							
							<input type="text" required name="price" value="<?php echo round($received_total,2);?>" />
							
						</td>
					</tr>

					<tr>
						<td>Payment Method</td>
						<td>
							<input type="radio" name="payment_method" value="Cash" checked> Cash <input type="radio" name="payment_method" value="PayPal"> PayPal
						</td>
					</tr>
					<tr>
						<td>Description</td>
						<td>
							
							<textarea name="description" style="width:200px;height:100px"></textarea>
							
						</td>
					</tr>



					

					
					
					<tr>
						<td colspan="2" align="center">
							<input type="button" name="add" value="Record Transaction" onclick="pay_it()" />
						</td>
					</tr>
					
					
				</table>
			</form>
		</div>		

		<script>
			
		// function submitForm() {
		// 		if($('#transaction_id').val()=='')
		// 		{
		// 			alert('Please provide the transaction id');
		// 			return false;	
		// 		}
		// 		$('#myFrm').submit();
		// 	}
		function pay_it() {
			if(!confirm('Are you sure want to record the transaction'))
			{
				return false;
			}

			$('#myFrm').submit();
		}
	</script> 
</div>		     
</body>
</html>