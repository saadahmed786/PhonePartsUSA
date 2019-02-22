<?php
require_once("../auth.php");
include_once '../inc/split_page_results.php';
include_once '../inc/functions.php';
$order = $db->func_query_first("SELECT * FROM inv_orders a,inv_orders_details b  WHERE a.order_id=b.order_id AND a.order_id='".$_GET['order_id']."'");
$total = $db->func_query_first_cell("SELECT SUM(product_price) FROM inv_orders_items where order_id='".$_GET['order_id']."'");
$order['order_price'] = $total + $order['shipping_cost'];
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
		 .red td{ box-shadow:1px 2px 5px #990000}
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
  <table border="1" width="60%" cellpadding="5" cellspacing="0" align="center" id="aim_table" >
    <tr>
    <td colspan="2" align="center">
    <?php
	if($order['paid_price']==0.00)
	{
	?>
    <input type="radio" onclick="calculatePartialValue('25');" name="paying_price" id="paying_price" /> 25% <input name="paying_price" type="radio" onclick="calculatePartialValue('100');" /> 100%
    <?php
	}
	else
	{
	?>
    <input type="radio" onclick="calculatePartialValue('0');" name="paying_price" id="paying_price" /> Remaining
    <?php	
	}
	?>
    <span id="span_amount" style="margin-left:25px"></span>
	
    </td>
    
    </tr>
    <tr>
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
    <input type="hidden" type="hidden" name="orders[order_id]" value="<?php echo $order['order_id'];?>">
    
    <input type="hidden" name="orders_details[first_name]" value='<?php echo $order['first_name'];?>'>
    <input type="hidden" name="orders_details[last_name]" value='<?php echo $order['last_name'];?>'>
    <input type="hidden" name="orders[email]" value='<?php echo $order['email'];?>'>
    <input type="hidden" name="orders_details[phone_number]" value='<?php echo $order['phone_number'];?>'>
    <input type="hidden" name="orders_details[address1]" value='<?php echo $order['address1'];?>'>
    <input type="hidden" name="orders_details[city]" value='<?php echo $order['city'];?>'>
    <input type="hidden" name="orders_details[state]" value='<?php echo $order['state'];?>'>
    <input type="hidden" name="orders_details[zip]" value='<?php echo $order['zip'];?>'>
    <input type="hidden" name="total" value=''>
    
    <input type="button" class="button" value="Charge Card" onclick="confirmAim();" id="confirm-btn"  /></td>
    
    </tr>
  </table>
</div>
	   </div>			
	   <br />
	
	   		
  </body>
</html>  
<script>
function calculatePartialValue(per)
{
if(per=='0')
{
	perVal = <?=$order['order_price'] - $order['paid_price'];?>;
	$('input[name=total]').val(perVal.toFixed(2));	
	
}
else
{
	perVal = (per * parseFloat(<?=$order['order_price'];?>)) / 100;	
	$('input[name=total]').val(perVal.toFixed(2));	
}
	$('#span_amount').html('<strong>Amount: </strong>$'+perVal.toFixed(2));
	
}
$(document).ready(function(e) {
    $('#paying_price').click();
});

</script>
          			   <script>
		
		function confirmAim()
		   {
			   var status = true;
			
			$.ajax({
		url: '../ajax_aim_send.php',
		type: 'post',
		//data: {},
		data:$('#aim_table :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#confirm-btn').attr('disabled', true);
			$('#confirm-btn').val('Processing...');
		},
		complete: function() {
			$('#confirm-btn').attr('disabled', false);
			$('#confirm-btn').val('Update');
		},				
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
				
			}
			
			if (json['success']) {
				alert(json['success']);
				$('input[name=payment_method]',window.parent.document).val('Credit Card / Debit Card (Authorize.Net)');
				$('input[name=paid_price]',window.parent.document).val($('input[name=total]').val());			
				$('input[name=update]',window.parent.document).click();
			}
		}
	});   
			   
		   }
		</script>