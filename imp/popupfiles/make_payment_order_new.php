<?php
include_once '../auth.php';
include_once '../inc/functions.php';
$order_id = $db->func_escape_string($_GET['order']);
$row = $db->func_query_first("SELECT o.*,od.* FROM inv_orders o,inv_orders_details od WHERE o.order_id=od.order_id AND  o.order_id='".($order_id)."'");

$total_paid = $db->func_query_first_cell("SELECT SUM(amount) from inv_vouchers where order_id='".$order_id."'");
$business_fee = round($db->func_query_first_cell('SELECT SUM(`value`) FROM `oc_order_total` WHERE cast(`order_id` as char(50)) = "'. $row['order_id'] .'" AND `code` = "business_fee"'),2);

$order_total = $row['shipping_cost'] + $row['sub_total'] + $row['tax'] + $business_fee;

if(($order_total-$total_paid)<=0)
{
	if($_SESSION['user_id']!='43')
	{
		
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
	}
}
	
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
	$deposited_date = date('Y-m-d H:i:s',strtotime($_POST['deposited_date']));
	$gross_amount  = (float)$_POST['gross_amount'];
	$payment_fee = (float)$_POST['payment_fee'];
	$net_amount = (float)round($_POST['net_amount'],2);

	$payment_ref = trim($db->func_escape_string($_POST['payment_ref']));
	if($_POST['payment_method']=='cash')
	{
		//$gross_amount = $net_amount;
		// $payment_type='cash';
	}
	
	if($gross_amount)
	{
		$db->db_exec("UPDATE inv_orders SET gross_amount='".$gross_amount."',payment_fee='".$payment_fee."',net_amount='".$net_amount."',undeposited_date='".$deposited_date."',undeposited_by='".$_SESSION['user_id']."',payment_ref='".$payment_ref."',paid_price=paid_price+".(float)$gross_amount." where order_id='".$order_id."'");
		// echo 'here';exit;

					addVoucher($order_id,$_POST['payment_method'],$gross_amount,$payment_ref,$payment_fee,$net_amount,$deposited_date);

					$_order = getOrder($order_id);
					$accounts = array();
					$accounts['description'] = 'Payment made @ '.$order_id;
					if($gross_amount>0)
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $gross_amount;
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $gross_amount*(-1);
					}
					$accounts['order_id'] = $order_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type']=strtolower($_POST['payment_method']);
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $_order['order_date'];

					
					if(trim($_order['order_id']))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					$accounts = array();
					$accounts['description'] = 'Payment made @ '.$order_id;
					if($gross_amount>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $gross_amount;
						
					}
					else
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $gross_amount*(-1);
					}
					$accounts['order_id'] = $order_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type'] = 'sales';
					$accounts['contra_account_code']=strtolower($_POST['payment_method']);
					$accounts['date_added'] = $_order['order_date'];

					
					if(trim($_order['order_id']))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					// Other Fees

					$accounts = array();
					$accounts['description'] = 'Other Payment Fees @ '.$order_id;
					if($payment_fee>0)
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $payment_fee;
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $payment_fee*(-1);
					}
					$accounts['order_id'] = $order_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type']='other_payment_fee';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $_order['order_date'];

					
					if(trim($_order['order_id']) && $payment_fee)
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					$accounts = array();
					$accounts['description'] = 'Other Payment Fees @ '.$order_id;
					if($payment_fee>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $payment_fee;
						
					}
					else
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $payment_fee*(-1);
					}
					$accounts['order_id'] = $order_id;
					$accounts['customer_email'] = $_order['email'];
					$accounts['type'] = 'sales';
					$accounts['contra_account_code']='other_payment_fee';
					$accounts['date_added'] = $_order['order_date'];

					
					if(trim($_order['order_id']) && $payment_fee)
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}

					// end other fees



		
		$_SESSION['message'] = "Funds have been deposited";
	// echo "<script>window.close();parent.window.location.reload();</script>";
		header("Location: ".$host_path."popupfiles/make_payment_order_new.php?order=".$order_id);
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
<style>
table tr th{
	padding-bottom:15px 
}
table tr td{
	padding-bottom:15px 
}
</style>
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
    <h2>Make Payment - <?php echo $row['order_id'];?> ($<?php echo number_format($order_total-$total_paid,2);?>)</h2>
  </div>
</div>

<div>
<table width="100%" align="left" style="font-weight: bold">
<tr>
	<th class="" align="left" width="41%">Payment Method: </th>
						<td align="left" class="" > <span id="span_payment_method" style="display:none"> <?= ($row['store_type'] == 'amazon' || $row['store_type']=='amazon_fba')? 'amazon': ucwords($row['payment_method']); ?></span>
							<?php
							if($_SESSION['edit_payment_method'] ):
							?>
							<select id="temp_payment_method"  onchange="toggleMethod(this.value)" >
								<option value="">Please Select</option>
								<option value="store_credit">Voucher</option>
								<option value="cash">Cash</option>
								<option value="paypal">PayPal Transfer</option>
								<option value="payflow">PayPal Card</option>
								<option value="card">Card Terminal</option>
								<option value="behalf">Behalf</option>
								<option value="cod">Cash on Delivery</option>
								<option value="wire">Wire Transfer</option>
								<option value="check">Check</option>
							</select>

							<?php
							endif;
							?>
						</td>

						</tr>
						</table>

						
<div class="">
<div id="paypal" class=" adjustment" style="display:none">
<form method="post">
<table width="100%" align="left" style="font-weight: bold">
<tr>
<td>Transaction ID:</td>
<td><input id="transaction_id" type="text" width="90%" placeholder="PayPal Transaction ID"></td>
</tr>
<tr>
						<td colspan="2" align="center"><input type="button" value="Map Payment" id="mapbox" class="button" onclick="MapBox();"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
<input type="hidden" name="order_id" value="<?php echo $row['order_id'];?>">
					<input type="hidden" class="payment_method" name="payment_method" value="<?php echo $row['payment_method'];?>">
</form>

</div>
<div id="other" class=" adjustment" style="display: none">
    <!-- <h2>Add Card Funds</h2> -->
    <form method="post">
					<table width="100%" align="left" style="font-weight:bold">
						

						<tr>
							<td width="41%">Payment Date:</td>
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

					
						<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="submit"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
					<input type="hidden" name="order_id" value="<?php echo $row['order_id'];?>">
					<input type="hidden" class="payment_method" name="payment_method" value="">
					</form>
  </div>

  <div id="store_credit" class=" adjustment" style="display:none">

<table width="100%" align="left" style="font-weight: bold">
<tr class="voucher">
						<th align="left" width="41%">Voucher:  <b><span class="total"></span></b></th>
						<td align="left"><input type="text" name="voucher_codes" id="voucher_code"  placeholder="VOUCHER1,VOUCHER2,..."   value="" /> 
						<br><span class="error" style="color: #F00;"></span>

					</tr>
<tr >
						<td colspan="2" align="center">
							<input type="button" class="button" value="Apply" onclick="parent.verifyVoucher($('#voucher_code'));">

						

						 <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>

					


</div>


  <div id="payflow" class=" adjustment" style="display:none">
<form method="post">
<table width="100%" align="left" style="font-weight: bold">
<tr>
										

										<td colspan="2">
										<iframe src="<?php echo $host_path;?>popupfiles/charge_card.php?order_id=<?= $row['order_id']; ?><?= ($row['store_type'] == 'web')? '&payment=full': ''; ?>" width="100%" style="border:none;height:240px"></iframe>
										

										</td>
										</tr>

						
					</table>

					
</form>

</div>

<div>
<form method="post">
<table width="100%" align="left" style="font-weight: bold">
<tr>
										

										<td colspan="2">
										Payment History:
										

										</td>
										</tr>
										<tr>
										<td height="200">
											
										<div style="width: 100%; height: 200px; overflow-y: scroll;background-color: white;font-weight: normal;">
  <?php
  $payments = $db->func_query("SELECT * FROM inv_vouchers WHERE order_id='".$order_id."'");
  $i=1;
  if(!$payments)
  {
  	echo 'No Payment History Found';
  }
  foreach($payments as $payment)
  {
  	echo $i.') ';
  	echo '$'.number_format($payment['amount'],2);
  	if($payment['amount']<0)
  	{
  		echo ' Refunded';
  	}
  	else
  	{
  		echo ' Paid';
  	}
  	echo ' via ';
  	echo map_payment_method($payment['method']);
  	echo ' on ';
  	echo americanDate($payment['date_added']);
  	echo "<br>";
  	$i++;
  }

  ?>
 </div>

										</td>
										</tr>

						
					</table>
</div>



</div>

						

						



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

function toggleMethodOld(objVal)
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
function toggleMethod(objVal)
{
	if(objVal=='')
	{
		return false;
	}
	$('.adjustment').hide();
	var temp_method = 'other';
	if(objVal=='paypal' || objVal=='payflow' || objVal=='store_credit' )
	{
		temp_method = objVal;
	}
	$('.payment_method').val(objVal);
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

function MapBox()
		{
			//var ref = $(obj).parent().parent().find('.mapbox');
			var ref_id = '<?php echo $row['order_id'];?>';
			var transaction_id = $('#transaction_id').val();
			if(jQuery.trim(transaction_id)=='')
			{
				alert('Please provide a valid transaction id');
				return false;
			}
			if(!confirm('Are you sure to Map?'))
			{
				return false;
			}
			$.ajax({
				url: '../',
				type:"POST",
				dataType:"json",
				data:{'ref_id':ref_id,'action':'mapbox','transaction_id':transaction_id},
				beforeSend: function() {
					//$(ref).parent().find('.map_button').hide(200);
			//$(ref).parent().find('.map_wait').html('Please wait...').show(200);
			$('#mapbox').attr('disabled','disabled');
			$('#mapbox').html('Please wait...');
		},		
		complete: function() {
			$('#mapbox').removeAttr('disabled');
			$('#mapbox').html('Map Payment');
		//	$(ref).parent().find('.map_wait').hide(200);
	},		
	success: function(json){
		if(json['error'])
		{
			alert(json['error']);
			// $(ref).parent().find('.map_button').show(200);
			return false;
		}
		if (json['success']) {
		
												location.reload(true);
											}
										}
									});
			
		}
</script>


</body>
</html>
<script>



</script>