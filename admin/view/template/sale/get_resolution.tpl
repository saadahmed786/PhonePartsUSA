<script type="text/javascript" src="view/javascript/jquery/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />


<script type="text/javascript" src="view/javascript/jquery/ui/external/jquery.bgiframe-2.1.2.js"></script>
<script type="text/javascript" src="view/javascript/jquery/jstree/jquery.tree.min.js"></script>
<script type="text/javascript" src="view/javascript/jquery/ajaxupload.js"></script>
<style>

.button {
    background: none repeat scroll 0 0 #003a88;
    border-radius: 10px;
    color: #fff !important;
    display: inline-block;
    padding: 5px 15px;
    text-decoration: none;
	cursor:pointer;
	border:none;
	
}
</style>
<?php if($order_info['payment_code']=='authorizenet_aim' or $order_info['payment_code']=='pp_standard' or $order_info['payment_code']=='paypal_express') { ?><a class="button" onclick="$('.all-areas').hide(500);$('.payment_method_xx').show(500);$('.amount').val($('input[name=amount]').val());" >Refund</a> <?php } ?> <a class="button" onclick="$('.all-areas').hide(500);$('#payment_method_gv').toggle(500);$('.amount').val($('input[name=amount]').val());" >Store Credit</a> <a class="button" onClick="$('.all-areas').hide(500);$('#send_replacement').toggle(500);$('.amount').val($('input[name=amount]').val());;getShippingMethod();" >Send Replacement</a>
<br>
<?php
if($order_info['payment_code']=='authorizenet_aim')
{
?>

<div id="payment_method_cc" class="all-areas payment_method_xx" style="display:none" >
            
            <form id="abc1">
			<table>
			  <tr>
				<td>Action:</td>
				<td>
					<select name="aat_action" onchange="$('#aat_response').html(''); if (this.value == 'CREDIT' || this.value == 'PRIOR_AUTH_CAPTURE') { $('input[name=aat_amount]').show(); } else { $('input[name=aat_amount]').hide(); }">
					  <option value="FALSE">---</option>
					  
					 <?php
                      $order_date = date('d-m-Y',strtotime($order_info['date_added']));
    if($order_date==date('d-m-Y'))
    {?>
                      <option value="VOID">Void</option>
                      <?php 
                      }
                      else
                      {
                      
                      ?>
                      <option value="PRIOR_AUTH_CAPTURE">Capture</option>
                      <?php
                      }
                      ?>
					  <option value="CREDIT">Refund</option>
					</select>
					<input style="display:none;" class="amount" type="text" size="5" name="aat_amount" value="" />
					<input type="hidden" name="aat_order_id" value="<?php echo $order_id; ?>" />
					
				</td>
			  </tr>
			  <tr style="display:none">
				<td>Environment:</td>
				<td><select name="aat_env"><option value="live" <?php if($aat_env == 'live'){ echo 'selected="selected"'; } ?> >Live</option><option value="test" <?php if($aat_env == 'test'){ echo 'selected="selected"'; } ?>>Test</option></td>
			  </tr>
			  <tr style="display:none">
				<td>Merchant ID:</td>
				<td><input type="text" name="aat_merchant_id" value="<?php echo $aat_merchant_id; ?>" /></td>
			  </tr>
			  <tr>
				<td>Transaction Key:</td>
				<td><input type="text" name="aat_transaction_key" value="<?php echo $aat_transaction_key; ?>" /></td>
			  </tr>
			</table>
			<script type="text/javascript"><!--//
	$('#aat_submit').live('click', function() {
		if(validateForm()==false)
		{
			return false;	
			
		}
		
		$.ajax({
			url: 'index.php?route=sale/order/aat_doaction&token=<?php echo $token; ?>',
			type: 'post',
			data: $('#abc1').serialize(),
			dataType: 'json',
			beforeSend: function() {
				$('#aat_submit').attr('disabled', 'disabled');
			},
			complete: function() {
				$('#aat_submit').removeAttr('disabled');
			},
			success: function(json) {
				//$('.success, .warning').remove();

				if (json['error']) {
					//$('#aat_response').html('<div class="warning" style="display: none;">' + json['error'] + '</div>');

					//$('.warning').fadeIn('slow');
					alert(json['error']);
				}

				if (json['success']) {
	               alert(json['success']);
				    $('#resolution_code').val("<?php echo $order_info['payment_code'];?>");
				  $('#form').submit();
				}
	
				
	
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
			//--></script>
			</form>
            
            
            
            
            <div class="buttons">
  <div class="right"><a  id="aat_submit" class="button"  >Confirm</a></div>
</div>

            </div>
<?php


}
else if($order_info['payment_code']=='pp_standard' or $order_info['payment_code']=='paypal_express')
{
?>
<div class="all-areas payment_method_xx" id="payment_method_paypal" style="display:none">
   <form  id="form_paypal_refund" style="margin-top:20px;clear:both;">
			<table>
            <tr>
            <td>Refunded Amount:</td>
            <td><input type="text" class="amount" readOnly /></td>
            
            </tr>
            
            
            
            <tr >
				<td>Action: </td>
				<td>
				  <select name="ppat_action"  onchange="$('#ppat_response').html(''); if (this.value == 'Partial') { $('input[name=ppat_amount]').show(); } else { $('input[name=ppat_amount]').hide(); }">
			  	    
			  		
			  		<option value="Void">Void</option>
			  		<!--<option value="Full">Full Refund</option>-->
			  		<option value="Partial">Partial Refund</option>

				  </select>
				  <input style="display:none"  type="text" class="amount" size="5" name="ppat_amount" value="<?php echo preg_replace("/[^0-9.]/", "", round($amount_owned,2)); ?>" onblur="$('.amount').val(this.value)" />
				  <input type="hidden" name="ppat_order_id" value="<?php echo $order_id;?>" />
                  <input type="hidden" name="ppat_order_editing" value="true" />
				  
				</td>
			  </tr>
			  
			  <tr style="display:none" >
				<td>Environment:</td>
				<td><select name="ppat_env"><option value="live" <?php if($ppat_env == 'live'){ echo 'selected="selected"'; } ?> >Live</option><option value="sandbox" <?php if($ppat_env == 'sandbox'){ echo 'selected="selected"'; } ?>>Sandbox</option></td>
			  </tr>
			  <tr >
				<td>API User:</td>
				<td><input type="text" name="ppat_api_user" value="<?php echo $ppat_api_user; ?>" /></td>
			  </tr>
			  <tr >
				<td>API Pass:</td>
				<td><input type="text" name="ppat_api_pass" value="<?php echo $ppat_api_pass; ?>" /></td>
			  </tr>
			  <tr >
				<td>API Signature:</td>
				<td><input type="text" name="ppat_api_sig" value="<?php echo $ppat_api_sig; ?>" /></td>
			  </tr>
              <tr>
              <td colspan="2" align="center"><input type="button" value="Submit" id="ppat_submit" class="button" /></td>
              </tr>
              
              
			</table>
			<script type="text/javascript">
	$('#ppat_submit').live('click', function() {
if(validateForm()==false)
	{
		
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
					 $('#resolution_code').val("<?php echo $order_info['payment_code'];?>");
					$('#form').submit();
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
           
<?php
}

?>

<div id="payment_method_gv" class="all-areas" style="display:none;">
            <table class="form">
    <tr>
              <td><span class="required">*</span> Reason</td>
              <td>
              <select name="reason" onchange="">
              <option value="">Select Reason</option>
             <?php
             foreach($reasons as $reason)
             {
             ?>
             <option value="<?php echo $reason['reason_id'];?>" <?php if($reason['reason_id']==$reason_id) echo 'selected';?>><?php echo $reason['name'];?></option>
             
             <?php
             
             
             }
             
             ?>
              </select><input type="hidden" id="order_code_type" value="" />
              
              
              </td>
            </tr>
            
            <tr>
            <td>Store Credit Amount:</td>
            <td> <div id="shipping_method">  <?php echo $order_info['shipping_method'];?>
                </div>
                <br />
                <input type="checkbox" name="credit_shipping" value="1" onchange="updateAmount();"  /> Credit Shipping <input type="text" class="" id="shipping_price" value="<?php echo $shipping_price;?>" onblur="if(this.value><?php echo $shipping_price;?>){ alert('Shipping credit amount cannot be greater than <?php echo $shipping_price;?>'); this.focus; }" /></td>
            
            
            </tr>
            <tr>
            <td>Total Credit:</td><td> <input type="text" class="amount" readOnly /></td>
            
            </tr>
    <tr>
      
      <td>Message: </td>
      <td><textarea name="message" style="height:85px;width:350px"></textarea> </td>
    </tr>
    <tr style="display:none" >
      
      <td><input type="hidden" id="generate_gv" name="generate_gv" class="amount" /></td>
      <td></td>
    </tr>
    
    
    
    
    
    
  </table>
            
            <div class="buttons">
  <div class="right"><input type="button" value="Confirm &amp; Send" id="button-confirm-gv" class="button" /></div>
   
</div>
<script type="text/javascript"><!--


$('select[name=reason]').change(function() {
		var id = $(this).val();
		if(id > 0) {
			
			//$('input[name=notify]').attr('checked', 'checked');
			if(typeof CKEDITOR !== 'undefined' && typeof CKEDITOR.instances.comment == 'object') {
				CKEDITOR.instances.comment.setData(msgs[id]);
			} else {
				$('textarea[name=message]').html(msgs[id]);
			}
			
			$("#order_code_type").val(msgs2[id]);
			//makeCode();
			
			
		}
	});



var canned_messages = <?php echo empty($canned_messages) ? "''" : $canned_messages; ?>;
var reason_codes = <?php echo empty($reason_codes) ? "''" : $reason_codes; ?>;
var msgs = {};
var msgs2 = {};
$(function() {
	if(canned_messages.length) {
		$.each(canned_messages, function(i, msg) {
			msgs[msg.reason_id] = msg.message;
			
		});
	}
	
	if(reason_codes.length) {
		$.each(reason_codes, function(i, msg) {
			msgs2[msg.reason_id] = msg.code;
			
		});
	}
	
	

	
	$('select[name=reason]').keyup(function() {
		$(this).change();
	});
});



$('#button-confirm-gv').bind('click', function() {
	
	$('#resolution_code').val('store_credit');
	if(validateForm()==false)
	{
		
	return false;	
	}
	if($('select[name=reason]').val()=='')
	{
		alert('Please select a reason');
		return false;	
	}
	
	
	$.ajax({
		url: 'index.php?route=sale/voucher/voucher_payment&token=<?php echo $token; ?>&order_id=<?php echo $order_id;?>&order_code_type='+$('#order_code_type').val(),
		type: 'post',
		data: $('#payment_method_gv :input,#form input:checked'),
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
					alert(json['error']);
					return false;
				}

				if (json['success']) {
					 
	               alert(json['success']);
				  
				  $('#form').submit();
				}
		}
	});
});
//--></script>
            </div>


<div id="send_replacement" class="all-areas" style="display:none;margin-bottom:10px">
         
         

          <table class="form">
          <tr>
                <td class="left">Shipping Method</td>
                <td class="left"><select name="shipping">
                    
                    
                  </select>
                  <input type="hidden" name="shipping_method" value="<?php echo $order_info['shipping_method']; ?>" />
                  <input type="hidden" name="shipping_code" value="<?php echo $order_info['shipping_code']; ?>" />
                </td>
              </tr>
          
          </table>  
            
            <div class="buttons">
  <div class="right"><input type="button" value="Confirm &amp; Replace" id="button-confirm-replacement" class="button" /></div>

</div>
<script type="text/javascript"><!--
$('select[name=\'shipping\']').bind('change', function() {
	if (this.value) {
		$('input[name=\'shipping_method\']').attr('value', $('select[name=\'shipping\'] option:selected').text());
	} else {
		$('input[name=\'shipping_method\']').attr('value', '');
	}
	
	$('input[name=\'shipping_code\']').attr('value', this.value);
});
function getShippingMethod()
{
	
	$.ajax({
		url: '<?php echo $store_url; ?>index.php?route=checkout/manual/shipping_method&order_id=<?php echo $order_id;?>&token=<?php echo $token; ?>',
		type: 'post',
		dataType: 'json',		
		beforeSend: function() {
			//$('#button-confirm-replacement').attr('disabled', true);
			
			$('#send_replacement').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> Loading, Please wait...</div>');
			
		},
		complete: function() {
		//	$('#button-confirm-replacement').attr('disabled', false);
			//$('.attention').remove();
			$('.attention').remove();
		},				
		success: function(json) {
			if (json['error']) {
					alert('error');
					return false;
				}

			
					 
	              if (json['shipping_method']) {
				//html = '<option value=""><?php echo $text_select; ?></option>';
html='';
				for (i in json['shipping_method']) {
					html += '<optgroup label="' + json['shipping_method'][i]['title'] + '">';
				
					if (!json['shipping_method'][i]['error']) {
						for (j in json['shipping_method'][i]['quote']) {
							if (json['shipping_method'][i]['quote'][j]['code'] == $('input[name=\'shipping_code\']').attr('value')) {
								html += '<option value="' + json['shipping_method'][i]['quote'][j]['code'] + '" selected="selected">' + json['shipping_method'][i]['quote'][j]['title'] + '</option>';
							} else {
								html += '<option value="' + json['shipping_method'][i]['quote'][j]['code'] + '">' + json['shipping_method'][i]['quote'][j]['title'] + '</option>';
							}
						}		
					} else {
						html += '<option value="" style="color: #F00;" disabled="disabled">' + json['shipping_method'][i]['error'] + '</option>';
					}
					
					html += '</optgroup>';
				}
		
				$('select[name=\'shipping\']').html(html);	
				
				if ($('select[name=\'shipping\'] option:selected').attr('value')) {
					$('input[name=\'shipping_method\']').attr('value', $('select[name=\'shipping\'] option:selected').text());
				} else {
					$('input[name=\'shipping_method\']').attr('value', '');
				}
				
				$('input[name=\'shipping_code\']').attr('value', $('select[name=\'shipping\'] option:selected').attr('value'));	
			}
				
		}
	});
	
}

$('#button-confirm-replacement').bind('click', function() {
	
	$('#resolution_code').val('replacement');
	if(validateForm()==false)
	{
		
	return false;	
	}
	
	
	$.ajax({
		url: 'index.php?route=sale/order/prepare_order&token=<?php echo $token; ?>&order_id=<?php echo $order_id;?>',
		type: 'post',
		data: $('#product-related input:checked,#send_replacement input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm-replacement').attr('disabled', true);
			
		},
		complete: function() {
			$('#button-confirm-replacement').attr('disabled', false);
			$('.attention').remove();
		},				
		success: function(json) {
			if (json['error']) {
					alert(json['error']);
					return false;
				}

				if (json['success']) {
					 
	               alert(json['success']);
				  
				  $('#form').submit();
				}
		}
	});
});


//--></script>
            </div>

<script>

function validateForm()
{
	
if (!confirm('Are you sure?')) {
			return false;
		}
		
		
		
		if($('input[name=order_id]').val()=='')
	{
	alert('Please provide Order ID');
	return false;	
	}
	
	if($('input[name=amount]').val()=='')
	{
	alert('Please select products');
	return false;	
	}
	 var type=true;
	$('.amount_checkbox').each(function(index, element) {
       
		if($(this).is(':checked'))
		{
			
			if($('#reason-'+(parseInt(index)+1)).val()=='')
			{
				alert('Please Select Claiming Reason of Selected Product');
				type= false;	
				
			}
			
		}
    });
	return type;
	
}
</script>