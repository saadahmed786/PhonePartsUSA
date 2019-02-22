<?php
include_once '../auth.php';
include_once '../inc/functions.php';
$order_id = $_GET['order'];
$row = $db->func_query_first("SELECT o.*,od.* FROM inv_orders o,inv_orders_details od WHERE o.order_id=od.order_id AND  o.order_id='".$db->func_escape_string($order_id)."'");
// $business_fee = round($db->func_query_first_cell('SELECT SUM(`value`) FROM `oc_order_total` WHERE cast(`order_id` as char(50)) = "'. $row['order_id'] .'" AND `code` = "business_fee"'),2);

$order_total = $row['shipping_cost'] + $row['sub_total'] + $row['tax'] + $business_fee;
	
	$total_vouchers = $db->func_query_first_cell('SELECT SUM(`a`.`amount`) AS `used` FROM `oc_voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND ' . (($row['store_type'] == 'web')? 'cast(a.order_id as char(50))': 'a.inv_order_id') . ' = "'. $row['order_id'] .'"');
	
	
	$order_total += $total_vouchers;


	$_type='Cash';

	if(strtolower($row['payment_method']=='check'))
	{
		$_type='Check';
	}
	if(strtolower($row['payment_method'])=='behalf')
	{
		$_type='Behalf';
	}

	if(strtolower($row['payment_method'])=='wire transfer')
	{
		$_type='Wire';
	}

	if(strtolower(substr($row['payment_method'], 0,4))=='cash')
	{
		$_type='Cash';
	}
	if(strtolower(substr($row['payment_method'], 0,4))=='card')
	{
		$_type='Card';
	}

	if(strtolower(substr($row['payment_method'], 0,6))=='paypal')
	{
		$_type='PayPal';
	}



// print_r($_POST);
if($_POST['order_id']  ){
	$order_id = $_POST['order_id'];
	$deposited_date = date('Y-m-d',strtotime($_POST['deposited_date']));
	$gross_amount  = (float)$_POST['gross_amount'];
	$payment_fee = (float)$_POST['payment_fee'];
	$net_amount = (float)round($_POST['net_amount'],2);

	$payment_ref = trim($db->func_escape_string($_POST['payment_ref']));
	if(isset($_POST['cash']))
	{
		$gross_amount = $net_amount;
		$payment_type='cash';
	}
	elseif(isset($_POST['check']))
	{
		$payment_type='check';
	}
	elseif(isset($_POST['wire']))
	{
		$payment_type='wire';
	}
	elseif(isset($_POST['behalf']))
	{
		$payment_type='behalf';
	}
	elseif(isset($_POST['card']))
	{
		$payment_type='card';
	}
	if($net_amount)
	{
		$db->db_exec("UPDATE inv_orders SET gross_amount='".$gross_amount."',payment_fee='".$payment_fee."',net_amount='".$net_amount."',undeposited_date='".$deposited_date."',undeposited_by='".$_SESSION['user_id']."',payment_ref='".$payment_ref."',paid_price=paid_price+".(float)$gross_amount." where order_id='".$order_id."'");
		// echo 'here';exit;
		
		$_SESSION['message'] = "Funds have been deposited";
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
	}

	if($_POST['payment_method']!='')
	{
		$db->db_exec("UPDATE inv_orders_details set payment_method='".$db->func_escape_string($_POST['payment_method'])."' where order_id='".$db->func_escape_string($order_id)."'");


		$_SESSION['message'] = "Payment method has been changed";
	echo "<script>window.location='".$host_path."popupfiles/make_payment_order.php?order=".$order_id."';</script>";
	exit;
	}
	
	
}


?>
<!DOCTYPE html>
<html>
<title>Left</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
   <link href='../include/date-picker/css/bootstrap-datetimepicker.min.css' rel='stylesheet' />
   <link href='../include/style.css' rel='stylesheet' />
<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
<script src="../js/jquery.min.js"></script>
 <script src="../js/moment.min.js"></script>
<script src='../include/date-picker/js/bootstrap-datetimepicker.min.js'></script>

<body>

<div class="w3-sidebar w3-bar-block w3-card w3-animate-left" style="display:none" id="mySidebar">
  <button class="w3-bar-item w3-button w3-large"
  onclick="w3_close()">Close &times;</button>
  <button class="w3-bar-item w3-button tablink" onclick="openCity(event, 'Cash')">Cash</button>
  <button class="w3-bar-item w3-button tablink" onclick="openCity(event, 'Check')">Check</button>
  <button class="w3-bar-item w3-button tablink" onclick="openCity(event, 'Card')">Card</button>
  <button class="w3-bar-item w3-button tablink" onclick="openCity(event, 'Wire')">Wire Transfer</button>
  <button class="w3-bar-item w3-button tablink" onclick="openCity(event, 'Behalf')">Behalf</button>
</div>

<div id="main">

<div class="w3-teal">
  <!-- <button id="openNav" class="w3-button w3-teal w3-xlarge" onclick="w3_open()">&#9776;</button> -->
  <div class="w3-container">
    <h2>Make Payment</h2>
  </div>
</div>

<div>
<table width="100%" align="left" style="font-weight: bold">
<tr>
	<th class="" align="left" width="20%">Payment Method : </th>
						<td align="left" class="" width="30%"> <span id="span_payment_method"> <?= ($row['store_type'] == 'amazon' || $row['store_type']=='amazon_fba')? 'amazon': ucwords($row['payment_method']); ?></span>
							<?php
							if($_SESSION['edit_payment_method'] ):
							?>
							<select id="temp_payment_method"  onchange="toggleMethod(this.value)" >
								<option value="">Default Selection</option>
								<option value="Cash or Credit at Store Pick-Up">Cash or Credit at Store Pick-Up</option>
								<option value="Card">Card</option>
								<option value="PayPal">PayPal</option>
								<?php if ($row['payment_method'] == 'Cash On Delivery') { ?>
								<option selected="selected" value="Cash On Delivery">COD</option>
								<?php } else { ?>
								<option value="Cash On Delivery">COD</option>
								<?php } ?>
									<?php if ($row['payment_method'] == 'Behalf') { ?>
								<option selected="selected" value="Behalf">Behalf</option>
								<?php } else { ?>
								<option value="Behalf">Behalf</option>
								<?php } ?>

								<?php if ($row['payment_method'] == 'Wire Transfer') { ?>
								<option selected="selected" value="Wire Transfer">Wire Transfer</option>
								<?php } else { ?>
								<option value="Wire Transfer">Wire Transfer</option>
								<?php } ?>

								<?php if ($row['payment_method'] == 'check') { ?>
								<option selected="selected" value="check">Check</option>
								<?php } else { ?>
								<option value="check">Check</option>
								<?php } ?>
							</select>

							<?php
							endif;
							?>
						</td>

						<td width="50%" align="center" rowspan="3" style="vertical-align: top">

<div class="w3-container">
<div id="PayPal" class="w3-container adjustment" style="<?php echo ($_type=='PayPal'?'display:block;':'display:none');?>">
<form method="post">
<table width="100%" align="left" style="font-weight: bold">
<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="card"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
<input type="hidden" name="order_id" value="<?php echo $row['order_id'];?>">
					<input type="hidden" class="payment_method" name="payment_method" value="<?php echo $row['payment_method'];?>">
</form>

</div>
<div id="Card" class="w3-container adjustment" style="<?php echo ($_type=='Card'?'display:block;':'display:none');?>">
    <!-- <h2>Add Card Funds</h2> -->
    <form method="post">
					<table width="100%" align="left" style="font-weight:bold">
						<tr>
							<td>Order ID:</td>
							<td><?php echo $row['order_id'];?></td>
						</tr>
						<tr>
							<td>Total Amount:</td>
							<td>$<?php echo number_format($row['sub_total']+$row['shipping_amount']+$row['tax'],2);?></td>
						</tr>

						<tr>
							<td>Payment Date:</td>
							<td><input type="date" class="datepicker"  name="deposited_date" value="<?php echo date('Y-m-d');?>" required="" style="width:90%"></td>
						</tr>

							<tr>
							<td>Gross:</td>
							<td><input type="number" class="gross_amount" value="0" name="gross_amount" step=".01" required="" style="width:90%"></td>
						</tr>

						<tr>
							<td>Payment Fee:</td>
							<td><input type="number" class="payment_fee" value="0" name="payment_fee" step=".01" required="" style="width:90%"></td>
						</tr>

						<tr>
							<td>Net:</td>
							<td><input type="number" class="net_amount" value="0" name="net_amount" step=".01" required="" readOnly style="width:90%"></td>
						</tr>

						<tr>
						<td>Ref #:</td>
						
							<td><input type="text" value="" name="payment_ref" style="width:90%"></td>

						

						</tr>

						
							<!-- <tr>
							<td>Comment:</td>
							<td><textarea name="comment" required="" style="width:90%;height:100px"></textarea></td>
						</tr> -->
						<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="card"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
					<input type="hidden" name="order_id" value="<?php echo $row['order_id'];?>">
					<input type="hidden" class="payment_method" name="payment_method" value="<?php echo $row['payment_method'];?>">
					</form>
  </div>

<div id="Behalf" class="w3-container adjustment" style="<?php echo ($_type=='Behalf'?'display:block;':'display:none');?>">
    <!-- <h2>Add Behalf Funds</h2> -->
    <form method="post">
					<table width="100%" align="left" style="font-weight:bold">
						<tr>
							<td>Order ID:</td>
							<td><?php echo $row['order_id'];?></td>
						</tr>
						<tr>
							<td>Total Amount:</td>
							<td>$<?php echo number_format($row['sub_total']+$row['shipping_amount']+$row['tax'],2);?></td>
						</tr>

						<tr>
							<td>Payment Date:</td>
							<td><input type="date" class="datepicker"  name="deposited_date" value="<?php echo date('Y-m-d');?>" required="" style="width:90%"></td>
						</tr>

							<tr>
							<td>Gross:</td>
							<td><input type="number" class="gross_amount" value="0" name="gross_amount" step=".01" required="" style="width:90%"></td>
						</tr>

						<tr>
							<td>Payment Fee:</td>
							<td><input type="number" class="payment_fee" value="0" name="payment_fee" step=".01" required="" style="width:90%"></td>
						</tr>

						<tr>
							<td>Net:</td>
							<td><input type="number" class="net_amount" value="0" name="net_amount" step=".01" required="" readOnly style="width:90%"></td>
						</tr>

						<tr>
						<td>Ref #:</td>
						
							<td><input type="text" value="" name="payment_ref" style="width:90%"></td>

						

						</tr>

						
							<!-- <tr>
							<td>Comment:</td>
							<td><textarea name="comment" required="" style="width:90%;height:100px"></textarea></td>
						</tr> -->
						<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="behalf"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
					<input type="hidden" name="order_id" value="<?php echo $row['order_id'];?>">
					<input type="hidden" class="payment_method" name="payment_method" value="<?php echo $row['payment_method'];?>">
					</form>
  </div>

  <div id="Cash" class="w3-container adjustment" style="<?php echo ($_type=='Cash'?'display:block;':'display:none');?>">
    <!-- <h2>Cash Payment</h2> -->
    <form method="post">
					<table width="100%" align="left" style="font-weight:bold">
						<tr>
							<td>Order ID:</td>
							<td><?php echo $row['order_id'];?></td>
						</tr>
						<tr>
							<td>Total Amount:</td>
							<td>$<?php echo number_format($row['sub_total']+$row['shipping_amount']+$row['tax'],2);?></td>
						</tr>

						<tr>
							<td>Payment Date:</td>
							<td><input type="date" class="datepicker"  name="deposited_date" value="<?php echo date('Y-m-d');?>" required="" style="width:90%"></td>
						</tr>

						

						<tr>
							<td>Amount:</td>
							<td><input type="number" class="net_amount" value="0" name="net_amount" step=".01" required=""  style="width:90%"></td>
						</tr>

					<tr>
						<td>Ref #:</td>
						
							<td><input type="text" value="" name="payment_ref" style="width:90%"></td>

						

						</tr>
						
							<!-- <tr>
							<td>Comment:</td>
							<td><textarea name="comment" required="" style="width:90%;height:100px"></textarea></td>
						</tr> -->
						<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="cash"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
					<input type="hidden" name="order_id" value="<?php echo $row['order_id'];?>">
					<input type="hidden" class="payment_method" name="payment_method" value="<?php echo $row['payment_method'];?>">
					</form>
  </div>

  <div id="Wire" class="w3-container adjustment" style="<?php echo ($_type=='Wire'?'display:block;':'display:none');?>">
    <!-- <h2>Add Wire Transfer Funds</h2> -->
    <form method="post">
					<table width="100%" align="left" style="font-weight:bold">
						<tr>
							<td>Order ID:</td>
							<td><?php echo $row['order_id'];?></td>
						</tr>
						<tr>
						<td>Total Amount:</td>
							<td>$<?php echo number_format($row['sub_total']+$row['shipping_amount']+$row['tax'],2);?></td>
						</tr>

						<tr>
							<td>Payment Date:</td>
							<td><input type="date" class="datepicker"  name="deposited_date" value="<?php echo date('Y-m-d');?>" required="" style="width:90%"></td>
						</tr>

							<tr>
							<td>Gross:</td>
							<td><input type="number" class="gross_amount" value="0" name="gross_amount" step=".01" required="" style="width:90%"></td>
						</tr>

						<tr>
							<td>Payment Fee:</td>
							<td><input type="number" class="payment_fee" value="0" name="payment_fee" step=".01" required="" style="width:90%"></td>
						</tr>

						<tr>
							<td>Net:</td>
							<td><input type="number" class="net_amount" value="0" name="net_amount" step=".01" required="" readOnly style="width:90%"></td>
						</tr>

						<tr>
						<td>Ref #:</td>
						
							<td><input type="text" value="" name="payment_ref" style="width:90%"></td>

						

						</tr>

						
							<!-- <tr>
							<td>Comment:</td>
							<td><textarea name="comment" required="" style="width:90%;height:100px"></textarea></td>
						</tr> -->
						<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="wire"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
					<input type="hidden" name="order_id" value="<?php echo $row['order_id'];?>">
					<input type="hidden" class="payment_method" name="payment_method" value="<?php echo $row['payment_method'];?>">
					</form>
  </div>

  <div id="Check" class="w3-container adjustment" style="<?php echo ($_type=='Check'?'display:block;':'display:none');?>">
    <!-- <h2>Add Check Funds</h2> -->
    <form method="post">
					<table width="100%" align="left" style="font-weight:bold">
						<tr>
							<td>Order ID:</td>
							<td><?php echo $row['order_id'];?></td>
						</tr>
						<tr>
						<td>Total Amount:</td>
							<td>$<?php echo number_format($row['sub_total']+$row['shipping_amount']+$row['tax'],2);?></td>
							</tr>
						

						<tr>
							<td>Payment Date:</td>
							<td><input type="date" class="datepicker"  name="deposited_date" value="<?php echo date('Y-m-d');?>" required="" style="width:90%"></td>
						</tr>

							<tr>
							<td>Gross:</td>
							<td><input type="number" class="gross_amount" value="0" name="gross_amount" step=".01" required="" style="width:90%"></td>
						</tr>

						<tr>
							<td>Payment Fee:</td>
							<td><input type="number" class="payment_fee" value="0" name="payment_fee" step=".01" required="" style="width:90%"></td>
						</tr>

						<tr>
							<td>Net:</td>
							<td><input type="number" class="net_amount" value="0" name="net_amount" step=".01" required="" readOnly style="width:90%"></td>
						</tr>


						<tr>
						<td>Ref #:</td>
						
							<td><input type="text" value="" name="payment_ref" style="width:90%"></td>

						

						</tr>
						
							<!-- <tr>
							<td>Comment:</td>
							<td><textarea name="comment" required="" style="width:90%;height:100px"></textarea></td>
						</tr> -->
						<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="check"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
					<input type="hidden" name="order_id" value="<?php echo $row['order_id'];?>">
					<input type="hidden" class="payment_method" name="payment_method" value="<?php echo $row['payment_method'];?>">
					</form>
  </div>


</div>

						</td>

						</tr>

							<?php if (($row['store_type'] == 'web' || $row['store_type']=='po_business')  && $order_total > $row['paid_price']) { ?>
					<tr class="voucher">
						<th align="left">Voucher:  <b><span class="total"></span></b></th>
						<td align="left"><input type="text" name="voucher_codes" id="voucher_code"  placeholder="VOUCHER1,VOUCHER2,..." style="width:230px"  value="" /> <input type="button" class="button" value="Apply" onclick="parent.verifyVoucher($('#voucher_code'));">

						<br><span class="error" style="color: #F00;"></span> </td>

					</tr>
					<?php } ?>

						<?php
									if (round($order_total - $row['paid_price'], 2) > 0) {
										?>
										<tr>
										

										<td colspan="2">
										<iframe src="<?php echo $host_path;?>popupfiles/charge_card.php?order_id=<?= $row['order_id']; ?><?= ($row['store_type'] == 'web')? '&payment=full': ''; ?>" width="100%" style="border:none;height:240px"></iframe>
										

										</td>
										</tr>
										<?php
										}
										?>

										

</table>

</div>



</div>

<script>
$(document).ready(function(){
	  $('input[data-type=monthyear]').datetimepicker({
                format: 'Y-MM'
            });
})
function w3_open() {
  document.getElementById("main").style.marginLeft = "25%";
  document.getElementById("mySidebar").style.width = "25%";
  document.getElementById("mySidebar").style.display = "block";
  document.getElementById("openNav").style.display = 'none';
}
function w3_close() {
  document.getElementById("main").style.marginLeft = "0%";
  document.getElementById("mySidebar").style.display = "none";
  document.getElementById("openNav").style.display = "inline-block";
}
$(document).on('change','.gross_amount',function(){
	var $this = $(this).parent().parent().parent();

var payment_fee = $this.find('.payment_fee').val();
console.log(payment_fee);

if(parseFloat(payment_fee)>=parseFloat($(this).val()))
{
	alert('Gross amount cannot be less than Payment Fee');
	return false;
}

$this.find('.net_amount').val(parseFloat(parseFloat($(this).val())-parseFloat(payment_fee)).toFixed(2));
});

$(document).on('change','.payment_fee',function(){
	var $this = $(this).parent().parent().parent();

var gross_amount = $this.find('.gross_amount').val();

if(parseFloat(gross_amount)<=parseFloat($(this).val()))
{
	alert('Gross amount cannot be less than Payment Fee');
	return false;
}

$this.find('.net_amount').val(parseFloat(parseFloat(gross_amount)-parseFloat($(this).val())).toFixed(2));
});

function toggleMethod(objVal)
{
	$('.adjustment').hide();
	$('.payment_method').val(objVal);
	var temp_method = '<?php echo $_type;?>';
	if(objVal!='')
	{
		 temp_method = objVal.substring(0, 4);
		 temp_method = temp_method.toLowerCase();
	}

	if(temp_method=='cash')
	{
		temp_method = 'Cash';
	}
	if(temp_method=='chec')
	{
		temp_method='Check';
	}
	if(temp_method=='beha')
	{
		temp_method='Behalf';
	}

	if(temp_method=='wire')
	{
		temp_method='Wire';
	}

	
	if(temp_method=='card')
	{
		temp_method='Card';
	}

	if(temp_method=='payp')
	{
		temp_method='PayPal';
	}

	$('#'+temp_method).show();

}
</script>


<script>
function openCity(cityName) {
  var i, x, tablinks;
  x = document.getElementsByClassName("adjustment");
  for (i = 0; i < x.length; i++) {
     x[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < x.length; i++) {
      // tablinks[i].className = tablinks[i].className.replace(" w3-red", ""); 
  }
  document.getElementById(cityName).style.display = "block";
  
}
</script>


</body>
</html>
<script>



</script>