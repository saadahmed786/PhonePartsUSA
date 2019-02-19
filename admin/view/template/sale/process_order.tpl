<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<script type="text/javascript" src="view/javascript/jquery/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />


<script type="text/javascript" src="view/javascript/jquery/ui/external/jquery.bgiframe-2.1.2.js"></script>
<script type="text/javascript" src="view/javascript/jquery/jstree/jquery.tree.min.js"></script>
<script type="text/javascript" src="view/javascript/jquery/ajaxupload.js"></script>
<style type="text/css">
body {
	padding: 0;
	margin: 0;
	background: #F7F7F7;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
}
.button {
    background: none repeat scroll 0 0 #003a88;
    border-radius: 10px;
    color: #fff;
    display: inline-block;
    padding: 5px 15px;
    text-decoration: none;
	cursor:pointer;
	border:none;
	
}
img {
	border: 0;
}
#container {
	padding: 0px 10px 7px 10px;
	height: 340px;
}
#menu {
	clear: both;
	height: 29px;
	margin-bottom: 3px;
}
#column-left {
	background: #FFF;
	border: 1px solid #CCC;
	float: left;
	width: 20%;
	height: 320px;
	overflow: auto;
}
#column-right {
	background: #FFF;
	border: 1px solid #CCC;
	float: right;
	width: 98%;
	height: 364px;
	overflow: auto;
	text-align: center;
}
#column-right div {
	text-align: left;
	padding: 5px;
}
#column-right a {
	display: inline-block;
	text-align: center;
	border: 1px solid #EEEEEE;
	cursor: pointer;
	margin: 5px;
	padding: 5px;
}
#column-right a.selected {
	border: 1px solid #7DA2CE;
	background: #EBF4FD;
}
#dialog {
	display: none;
}
.thumb {
	padding: 5px;
	width: 105px;
	height: 105px;
	background: #F7F7F7;
	border: 1px solid #CCCCCC;
	cursor: pointer;
	cursor: move;
	position: relative;
}
</style>
</head>
<body>
<div id="container">
  
  
  <div id="column-right">
  
  <?php
 
  if($type_check==1)
  {
  	$amount_owned = $new_total - $previous_total;
  ?>
  <table width="100%">
  <tr style="font-size:14px;font-weight:bold">
  <td width="33%">Previous Total: <?php echo '$'.number_format($previous_total,2); // ?></td>
  <td width="33%">New Total: <?php echo '$'.number_format($new_total,2); // ?></td>
  <td width="33%">Amount Owned: <?php echo '$'.number_format($amount_owned,2); // ?></td>
  </tr>
  
  
  </table>
   
   <br />
   <div id="main_buttons" style="border-bottom: 1px solid grey;
    height: 80px;">
   <div style="float:left">
   <button class="button" onclick="$('.all-areas').hide(500);$('#payment_method_cc').show(500);" >Pay with CC</button>
   </div>
   
   <div style="float:right">
   <button class="button" onclick="$('.all-areas').hide(500);$('#payment_method_cash').show(500);">Pay with Cash</button>
   <br /><br />
   <button class="button"  onclick="$('.all-areas').hide(500);$('#payment_method').show(500);">Store Front CC</button>
   
   </div>
   </div>
   <div class="all-areas" id="payment_method" style="display:none">
   <form  style="margin-top:20px;clear:both">
			<table  style="margin-top:10px">
			  <tr style="font-size:14px">
				<td >Pay Amount:</td>
				<td >
					<select name="aat_action" style="display:none" >
					  
					  <option value="PRIOR_AUTH_CAPTURE" selected>Capture</option>
					  
					</select>
					<input readonly="readonly" type="text" size="5" name="aat_amount" value="<?php echo preg_replace("/[^0-9.]/", "", $amount_owned); ?>" />
					<input type="hidden" name="aat_order_id" value="<?php echo $order_id; ?>" />
					<input type="button" class="button"  value="Submit" id="aat_submit" />
				</td>
			  </tr>
			  <tr style="display:none;">
				<td>Environment:</td>
				<td><select name="aat_env"><option value="live" <?php if($aat_env == 'live'){ echo 'selected="selected"'; } ?> >Live</option><option value="test" <?php if($aat_env == 'test'){ echo 'selected="selected"'; } ?>>Test</option></select></td>
			  </tr>
			  <tr style="display:none">
				<td>Merchant ID:</td>
				<td><input type="text" name="aat_merchant_id" value="<?php echo $aat_merchant_id; ?>" /></td>
			  </tr>
			  <tr style="display:none">
				<td>Transaction Key:</td>
				<td><input type="text" name="aat_transaction_key" value="<?php echo $aat_transaction_key; ?>" /></td>
			  </tr>
			</table>
			<script type="text/javascript"><!--//
	$('#aat_submit').live('click', function() {
		if (!confirm('Are you sure?')) {
			return false;
		}
		$.ajax({
			url: 'index.php?route=sale/order/aat_doaction&token=<?php echo $token; ?>',
			type: 'post',
			data: $('#payment_method form').serialize(),
			dataType: 'json',
			beforeSend: function() {
				$('#aat_submit').attr('disabled', 'disabled');
			},
			complete: function() {
				$('#aat_submit').removeAttr('disabled');
			},
			success: function(json) {
				$('.success, .warning').remove();

				if (json['error']) {
					$('#aat_response').html('<div class="warning"  style="display: none;color:red">' + json['error'] + '</div>');

					$('.warning').fadeIn('slow');
				}

				if (json['success']) {
	                alert(json['success']);
				   location.reload(true);
				}
	
				if (json['sent']) {
					$('#msgsent').val(json['sent']);
				}

				if (json['rcvd']) {
					$('#msgrcvd').val(json['rcvd']);
				}
	
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
			</script>
			</form>
            
            
            
            
            </div>
            
            
            <!-- Payment with Credit Card -->
            <div id="payment_method_cc" class="all-areas" style="display:none;">
            <table class="form">
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
      <td>Billing Address:</td>
      <td><input type="text" name="payment_address_1"  value="<?php echo $payment_address_1;?>" /></td>
    </tr>
    
     <tr>
      <td>City:</td>
      <td><input type="text" name="payment_city"  value="<?php echo $payment_city;?>" /></td>
    </tr>
    
    
     <tr>
      <td>State:</td>
      <td><input type="text" name="payment_zone" value="<?php echo $payment_zone;?>" /></td>
    </tr>
    
     <tr>
      <td>Postcode:</td>
      <td><input type="text" name="payment_postcode" value="<?php echo $payment_postcode;?>" /></td>
    </tr>
    
    <tr>
      <td>Amount:</td>
      <td><input type="text" name="cc_xamount" value="<?php echo preg_replace("/[^0-9.]/", "", $amount_owned); ?>" readonly="readonly" />
      <input type="hidden" name="cc_xtype" value="AUTH_CAPTURE" />
      </td>
    </tr>
    
  </table>
            
            <div class="buttons">
  <div class="right"><input type="button" value="Confirm" id="button-confirm" class="button" /></div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({
		url: 'index.php?route=payment/authorizenet_aim/send&token=<?php echo $token; ?>&order_id=<?php echo $order_id;?>',
		type: 'post',
		data: $('#payment_method_cc :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm').attr('disabled', true);
			$('#payment').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-confirm').attr('disabled', false);
			$('.attention').remove();
		},				
		success: function(json) {
			if (json['error']) {
					$('#aat_response').html('<div class="warning"  style="display: none;color:red">' + json['error'] + '</div>');

					$('.warning').fadeIn('slow');
				}

				if (json['success']) {
	               alert(json['success']);
				   location.reload(true);
				}
		}
	});
});
//--></script>
            </div>
            
            <!-- End Payment with Credit Card -->
            
            
            <!-- Payment Method Cash -->
            
            <div id="payment_method_cash" class="all-areas" style="display:none;">
            <table class="form">
    <tr>
      
      <td><input type="checkbox" name="cash_received" value="<?php echo $amount_owned;?>" /></td>
      <td>$<?php echo number_format($amount_owned,2);?> Cash Received</td>
    </tr>
    
    
    
    
    
    
  </table>
            
            <div class="buttons">
  <div class="right"><input type="button" value="Confirm" id="button-confirm-cash" class="button" /></div>
</div>
<script type="text/javascript"><!--
$('#button-confirm-cash').bind('click', function() {
	
	if(!$('input[name=cash_received]').is(':checked'))
	{
	alert('Please check the checkbox to proceed');
	return false;	
		
	}
	
	$.ajax({
		url: 'index.php?route=sale/order/cash_payment&token=<?php echo $token; ?>&order_id=<?php echo $order_id;?>',
		type: 'post',
		data: $('#payment_method_cash :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm-cash').attr('disabled', true);
			
		},
		complete: function() {
			$('#button-confirm-cash').attr('disabled', false);
			$('.attention').remove();
		},				
		success: function(json) {
			if (json['error']) {
					$('#aat_response').html('<div class="warning"  style="display: none;color:red">' + json['error'] + '</div>');

					$('.warning').fadeIn('slow');
				}

				if (json['success']) {
	               alert(json['success']);
				   location.reload(true);
				}
		}
	});
});
//--></script>
            </div>
            <!-- End Payment Method Cash-->
            
            <div id="aat_response"></div>
			<div id="msgsent"></div>
			<div id="msgrcvd"></div>
  
  <?php
  
  }
  else if ($type_check==2)
  {
  	$amount_owned = $new_total - $previous_total;
    $amount_owned = $amount_owned * -1;
  ?>
  <table width="100%">
  <tr style="font-size:14px;font-weight:bold">
  <td width="33%">Previous Total: <?php echo '$'.number_format($previous_total,2); // ?></td>
  <td width="33%">New Total: <?php echo '$'.number_format($new_total,2); // ?></td>
  <td width="33%">Difference: (<?php echo '$'.number_format($amount_owned,2); // ?>)</td>
  </tr>
  
  
  </table>
   
   <br />
   <div id="main_buttons" style="border-bottom: 1px solid grey;
    height: 80px;">
   
   <table width="100%">
   <tr>
   <td width="33%">
   <button class="button" onclick="$('.all-areas').hide(500);$('#payment_method_cc').show(500);" >Refund CC</button>
   </td>
   <td width="33%" align="center">
   <button class="button" onclick="$('.all-areas').hide(500);$('#payment_method_gv').show(500);" >Store Credit</button>
   </td>
   
   <td width="33%" align="right">
    <button class="button" onclick="$('.all-areas').hide(500);$('#payment_method_cash').show(500);">Refund Cash</button>
   
   </td>
   
   
   </tr>
   <tr>
   <td>&nbsp;</td>
   <td align="center"><button class="button" onclick="$('.all-areas').hide(500);$('#payment_method_paypal').show(500);">Refund Paypal</button></td>
   <td align="right"><button class="button"  onclick="$('.all-areas').hide(500);$('#payment_method').show(500);">Refund SFront CC</button></td>
   
   </tr>
   </table>
   
   </div>
   <div class="all-areas" id="payment_method" style="display:none">
   <form  style="margin-top:20px;clear:both">
			<table  style="margin-top:10px">
			  <tr style="font-size:14px">
				<td >Pay Amount:</td>
				<td >
					<select name="aat_action" style="display:none" >
					  
					  <option value="CREDIT" selected>Capture</option>
					  
					</select>
					<input readonly="readonly" type="text" size="5" name="aat_amount" value="<?php echo preg_replace("/[^0-9.]/", "", $amount_owned); ?>" />
					<input type="hidden" name="aat_order_id" value="<?php echo $order_id; ?>" />
					<input type="button" class="button"  value="Submit" id="aat_submit" />
				</td>
			  </tr>
			  <tr style="display:none;">
				<td>Environment:</td>
				<td><select name="aat_env"><option value="live" <?php if($aat_env == 'live'){ echo 'selected="selected"'; } ?> >Live</option><option value="test" <?php if($aat_env == 'test'){ echo 'selected="selected"'; } ?>>Test</option></select></td>
			  </tr>
			  <tr style="display:none">
				<td>Merchant ID:</td>
				<td><input type="text" name="aat_merchant_id" value="<?php echo $aat_merchant_id; ?>" /></td>
			  </tr>
			  <tr style="display:none">
				<td>Transaction Key:</td>
				<td><input type="text" name="aat_transaction_key" value="<?php echo $aat_transaction_key; ?>" /></td>
			  </tr>
			</table>
			<script type="text/javascript"><!--//
	$('#aat_submit').live('click', function() {
		if (!confirm('Are you sure?')) {
			return false;
		}
		
		$.ajax({
			url: 'index.php?route=sale/order/aat_doaction&token=<?php echo $token; ?>',
			type: 'post',
			data: $('#payment_method form').serialize(),
			dataType: 'json',
			beforeSend: function() {
				$('#aat_submit').attr('disabled', 'disabled');
			},
			complete: function() {
				$('#aat_submit').removeAttr('disabled');
			},
			success: function(json) {
				$('.success, .warning').remove();

				if (json['error']) {
					$('#aat_response').html('<div class="warning"  style="display: none;color:red">' + json['error'] + '</div>');

					$('.warning').fadeIn('slow');
				}

				if (json['success']) {
	                alert(json['success']);
				   location.reload(true);
				}
	
				if (json['sent']) {
					$('#msgsent').val(json['sent']);
				}

				if (json['rcvd']) {
					$('#msgrcvd').val(json['rcvd']);
				}
	
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
			</script>
			</form>
            
            
            
            
            </div>
            
            
            <div class="all-areas" id="payment_method_paypal" style="display:none">
   <form  id="form_paypal_refund" style="margin-top:20px;clear:both;">
			<table>
			  <tr>
				<td>Refund Amount:</td>
				<td>
				  <select name="ppat_action"  style="display:none" onchange="$('#ppat_response').html(''); if (this.value == 'Partial') { $('input[name=ppat_amount]').show(); } else { $('input[name=ppat_amount]').hide(); }">
			  	    
			  		<option value="Partial" selected>Partial Refund</option>
				  </select>
				  <input  type="text" readonly="readonly" size="5" name="ppat_amount" value="<?php echo preg_replace("/[^0-9.]/", "", round($amount_owned,2)); ?>" />
				  <input type="hidden" name="ppat_order_id" value="<?php echo $order_id;?>" />
                  <input type="hidden" name="ppat_order_editing" value="true" />
				  <input type="button" value="Submit" id="ppat_submit" class="button" />
				</td>
			  </tr>
			  <tr style="display:none">
				<td>Environment:</td>
				<td><select name="ppat_env"><option value="live" <?php if($ppat_env == 'live'){ echo 'selected="selected"'; } ?> >Live</option><option value="sandbox" <?php if($ppat_env == 'sandbox'){ echo 'selected="selected"'; } ?>>Sandbox</option></td>
			  </tr>
			  <tr style="display:none">
				<td>API User:</td>
				<td><input type="text" name="ppat_api_user" value="<?php echo $ppat_api_user; ?>" /></td>
			  </tr>
			  <tr style="display:none">
				<td>API Pass:</td>
				<td><input type="text" name="ppat_api_pass" value="<?php echo $ppat_api_pass; ?>" /></td>
			  </tr>
			  <tr style="display:none">
				<td>API Signature:</td>
				<td><input type="text" name="ppat_api_sig" value="<?php echo $ppat_api_sig; ?>" /></td>
			  </tr>
			</table>
			<script type="text/javascript">
	$('#ppat_submit').live('click', function() {
		if (!confirm('Are you sure?')) {
			return false;
		}
		$.ajax({
			url: 'index.php?route=sale/order/ppat_doaction&token=<?php echo $token; ?>',
			type: 'post',
			data: $('#form_paypal_refund').serialize(),
			dataType: 'json',
			beforeSend: function() {
				$('#ppat_submit').attr('disabled', 'disabled');
			},
			complete: function() {
				$('#ppat_submit').removeAttr('disabled');
			},
			success: function(json) {
				$('.success, .warning').remove();

				if (json['error']) {
					alert(json['error']);
				}

				if (json['success']) {
	                alert(json['success']);
					location.reload(true);
					//$('#order_update').click();
				}
	
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
			</script>
			</form>
            
            
            
            
            </div>
            
            
            <!-- Payment with Credit Card -->
            <div id="payment_method_cc" class="all-areas" style="display:none;">
            <table class="form">
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
    
     <tr style="display:none">
      <td>Billing Address:</td>
      <td><input type="text" name="payment_address_1"  value="<?php echo $payment_address_1;?>" /></td>
    </tr>
    
     <tr style="display:none">
      <td>City:</td>
      <td><input type="text" name="payment_city"  value="<?php echo $payment_city;?>" /></td>
    </tr>
    
    
     <tr style="display:none">
      <td>State:</td>
      <td><input type="text" name="payment_zone" value="<?php echo $payment_zone;?>" /></td>
    </tr>
    
     <tr style="display:none">
      <td>Postcode:</td>
      <td><input type="text" name="payment_postcode" value="<?php echo $payment_postcode;?>" /></td>
    </tr>
    
    <tr>
      <td>Amount:</td>
      <td><input type="text" name="cc_xamount" value="<?php echo preg_replace("/[^0-9.]/", "", $amount_owned); ?>" readonly="readonly" />
      <input type="hidden" name="cc_xtype" value="CREDIT" />
      </td>
    </tr>
    
  </table>
            
            <div class="buttons">
  <div class="right"><input type="button" value="Confirm" id="button-confirm" class="button" /></div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({
		url: 'index.php?route=payment/authorizenet_aim/send&token=<?php echo $token; ?>&order_id=<?php echo $order_id;?>',
		type: 'post',
		data: $('#payment_method_cc :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm').attr('disabled', true);
			$('#payment').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-confirm').attr('disabled', false);
			$('.attention').remove();
		},				
		success: function(json) {
			if (json['error']) {
					$('#aat_response').html('<div class="warning"  style="display: none;color:red">' + json['error'] + '</div>');

					$('.warning').fadeIn('slow');
				}

				if (json['success']) {
	               alert(json['success']);
				   location.reload(true);
				}
		}
	});
});
//--></script>
            </div>
            
            <!-- End Payment with Credit Card -->
            
            
            <!-- Payment Method Cash -->
            
            <div id="payment_method_cash" class="all-areas" style="display:none;">
            <table class="form">
    <tr>
      
      <td><input type="checkbox" id="cash_checkbox" name="cash_received" value="<?php echo $amount_owned;?>" /></td>
      <td>$<?php echo number_format($amount_owned,2);?> Cash Refund</td>
    </tr>
    
    
    
    
    
    
  </table>
            
            <div class="buttons">
  <div class="right"><input type="button" value="Confirm" id="button-confirm-cash" class="button" /></div>
</div>
<script type="text/javascript"><!--
$('#button-confirm-cash').bind('click', function() {
	
	if(!$('input[name=cash_received]').is(':checked'))
	{
	alert('Please check the checkbox to proceed');
	return false;	
		
	}
	
	
	$.ajax({
		url: 'index.php?route=sale/order/cash_payment&token=<?php echo $token; ?>&order_id=<?php echo $order_id;?>',
		type: 'post',
		data: $('#payment_method_cash :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm-cash').attr('disabled', true);
			
		},
		complete: function() {
			$('#button-confirm-cash').attr('disabled', false);
			$('.attention').remove();
		},				
		success: function(json) {
			if (json['error']) {
					$('#aat_response').html('<div class="warning"  style="display: none;color:red">' + json['error'] + '</div>');

					$('.warning').fadeIn('slow');
				}

				if (json['success']) {
	               alert(json['success']);
				   location.reload(true);
				}
		}
	});
});
//--></script>
            </div>
            <!-- End Payment Method Cash-->
            
            
            <!--GV Payment Method-->
            
            <div id="payment_method_gv" class="all-areas" style="display:none;">
            <table class="form">
    
    <tr>
      
      <td>Message: </td>
      <td><textarea name="message" style="height:85px;width:350px"></textarea> </td>
    </tr>
    <tr>
      
      <td><input type="checkbox" id="generate_gv" name="generate_gv" value="<?php echo $amount_owned;?>" /></td>
      <td>I wish to generate a Gift Voucher worth <strong>$<?php echo number_format($amount_owned,2);?></strong> </td>
    </tr>
    
    
    
    
    
    
  </table>
            
            <div class="buttons">
  <div class="right"><input type="button" value="Confirm &amp; Send" id="button-confirm-gv" class="button" /></div>
</div>
<script type="text/javascript"><!--
$('#button-confirm-gv').bind('click', function() {
	
	if(!$('input[name=generate_gv]').is(':checked'))
	{
	alert('Please check the checkbox to proceed');
	return false;	
		
	}
	
	
	$.ajax({
		url: 'index.php?route=sale/voucher/voucher_payment&token=<?php echo $token; ?>&order_id=<?php echo $order_id;?>',
		type: 'post',
		data: $('#payment_method_gv :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm-gv').attr('disabled', true);
			
		},
		complete: function() {
			$('#button-confirm-gv').attr('disabled', false);
			$('.attention').remove();
		},				
		success: function(json) {
			if (json['error']) {
					$('#aat_response').html('<div class="warning"  style="display: none;color:red">' + json['error'] + '</div>');

					$('.warning').fadeIn('slow');
				}

				if (json['success']) {
	               alert(json['success']);
				   location.reload(true);
				}
		}
	});
});
//--></script>
            </div>
            <!--End GV Payment Method-->
            
            <div id="aat_response"></div>
			<div id="msgsent"></div>
			<div id="msgrcvd"></div>
  
  <?php
  
  }
  else
  {
  ?>
  <h2>Your Order seems to be updated, Please edit the order and save the products to make it work.</h2>
  <?php
  
  }
  
  ?>
  
  </div>
</div>

</body>
</html>