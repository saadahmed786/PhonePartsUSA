<?php echo $header; ?>
<style>
.disabled{
background-color:#f4f2f2;
color:#7f7a6d;	
	
}
#header,#footer{
	display:none;
}
</style>
<div id="content">
  
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    
    <div class="content">
<a href="index.php?route=sale/rma/index1&order_id=<?php echo $order_id;?>&token=<?php echo $token;?>" class="button">Generate RMA</a>  <a href="index.php?route=sale/return_program/insert&xorder_id=<?php echo $order_id;?>&token=<?php echo $token;?>" class="button">Issue Replacement</a> <?php if(!$voucher_info)
{
?> <a href="javascript:void(0);" onclick="issue_credit();" class="button">Issue Store Credit</a>      

<?php
}
?>


<a href="javascript:void(0);" onclick="issue_refund();" class="button">Issue Refund</a> 


<form id="payment_method_paypal" style="display:none;margin-top:20px">
			<table>
			  <tr>
				<td>Action:</td>
				<td>
				  <select name="ppat_action" onchange="$('#ppat_response').html(''); if (this.value == 'Partial') { $('input[name=ppat_amount]').show(); } else { $('input[name=ppat_amount]').hide(); }" style="display:none">
			  	    
			  		<option value="Partial" selected>Partial Refund</option>
				  </select>
				  <input  type="text" size="5" name="ppat_amount" value="<?php echo $order_info['total'];?>" />
				  <input type="hidden" name="ppat_order_id" value="<?php echo $order_id;?>" />
				  <input type="button" value="Submit" id="ppat_submit" />
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
			data: $('#payment_method_paypal').serialize(),
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
					location.reload();
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
     
      <?php
      if($voucher_info)
      {
      ?>
      <br />
      <br />
      <span style="color:red;font-weight:bold">Voucher # <?php echo $voucher_info['code'];?> has been generated worth <?php echo $this->currency->format($voucher_info['amount']); ?> on <?php echo date($this->language->get('date_format_short'), strtotime($voucher_info['date_added']));?>
      <?php
      
      }
      ?>
    </div>
  </div>
</div> 
<script>

function issue_credit()
{

	if(!confirm('Are you sure to issue a store credit and complete order'))
	{
	return false;	
	}
	
	
	
	$.ajax({
		url: 'index.php?route=sale/voucher/voucher_payment&token=<?php echo $token; ?>&order_code_type=C&order_id=<?php echo $order_id;?>',
		type: 'post',
		data: {generate_gv:'<?php echo $order_info['total'];?>',message:'POS Issued'},
		dataType: 'json',		
		beforeSend: function() {
		//	$('#button-confirm-gv').attr('disabled', true);
			
		},
		complete: function() {
			//$('#button-confirm-gv').attr('disabled', false);
		//	$('.attention').remove();
		},				
		success: function(json) {
			if (json['error']) {
					//$('#aat_response').html('<div class="warning"  style="display: none;color:red">' + json['error'] + '</div>');

					//$('.warning').fadeIn('slow');
					alert(json['error']);return false;
				}

				if (json['success']) {
	               alert(json['success']);
				   location.reload();
				  // $('#order_update').click();
				  // location.reload(true);
				}
		}
	});

}

function issue_refund()
{
	$('#ppat_submit').click();
	
	
}

</script>
<?php echo $footer; ?>