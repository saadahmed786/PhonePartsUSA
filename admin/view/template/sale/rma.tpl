<?php
if(!$this->user->canProcessRMA() && !$this->user->userHavePermission('pos_can_process_rma') && $this->user->getUserName() != 'admin' && !$this->user->userHavePermission('pos_can_issue_store_credit'))
{
	echo 'You are not authorized, please contact administrator';exit;
}
?>
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
<script>
	$(document).on('change', '#restockingfee', function(event) {
		var amountC = $('input[name="ppat_amount"]');
		var restocking = parseFloat($(this).val());
		var ppat_amount = parseFloat($('#xtotal').val());
		discount = (ppat_amount / 100) * restocking;
		ppat_amount = ppat_amount - discount;
		amountC.val(ppat_amount);
	});
	function restockingFee () {
		var restocking = $('#restockingfee').val();
		$('#discount_per').val(restocking);
		var grade = 'Oth';
		if (restocking > 0) {
			$('#restocking').val('1');
		} else {
			$('#restocking').val('0');
		}
		if (restocking == '10') {
			grade = 'A';
		} else if (restocking == '20') {
			grade = 'B';
		} else if (restocking == '30') {
			grade = 'C';
		} else if (restocking == '50') {
			grade = 'D';
		}
		$('#restocking_grade').val(grade);
	}
	function generateRMA()
	{
		restockingFee();
		$.ajax({
			url: 'index.php?route=sale/rma/insert&token=<?php echo $token; ?>&order_id=<?php echo $this->request->get['order_id'];?>',
			type:'POST',
			async: false,
			data:$('#form').serialize(),
			beforeSend: function() {
				$('.button').attr('disabled',true);
			},
			complete: function() {
			},
			success: function(data) {
				$('#rma_number').val(data.split(" ")[2]);
				alert(data);
				var decision = '';
				$('.amount_checkbox'). each(function(index, el) {
					if ($(el).is(':checked')) {
						decision = $(el).parents('tr').find('.decision').val();
					}
				});
				return false;
				if (decision != 'Issue Credit') {
					parent.location.reload();
				}
			}
		});
	}
	function confirmMsg()
	{
		if (!verifyPins()) {
			return false;
		}
		if($('#product_list').val()=='')
		{
			alert('Select an Item first');
			return false;	
		}
		var error = false;
		$('.amount_checkbox').each(function(index, element) {
			if($(element).is(':checked') && $('#comment'+index).val()=='')
			{
				alert('Please input comment to proceed...');
				error = true;
				return false;
			}
		});	
		if(error==true)
		{
			return false;
		}
		if(confirm('Are you sure to proceed?') )
		{
			return true;
		}
		else
		{
			return false;	
		}
	}


	function confirmCardMsg()
	{
		if (!verifyPins()) {
			return false;
		}
		if($('#product_list').val()=='')
		{
			alert('Select an Item first');
			return false;	

		}
		var error = false;
		$('.amount_checkbox').each(function(index, element) {
			if($(element).is(':checked') && $('#comment'+index).val()=='')
			{
				alert('Please input comment to proceed...');

				error = true;
				return false;
			}
		});	
		if(error==true)
		{
			return false;
		}

		if(confirm('This credit card payment will be refunded using cash. Make sure this is 100% correct before proceeding.') )
		{
			return true;
		}
		else
		{
			return false;	
		}

	}





	function issueReplacement()
	{
		setDecision('Issue Replacement');
		$.ajax({
			url: 'index.php?route=sale/rma/issue_replacement&token=<?php echo $token; ?>&order_id=<?php echo $this->request->get['order_id'];?>',
			type:'POST',
			data:$('#form').serialize(),
			beforeSend: function() {
				$('.button').attr('disabled',true);
			},
			complete: function() {
			},
			success: function(data) {
			//alert(data);
			UberAutoPrint(data);
			//location.reload();
			generateRMA();
		}
	});
	}
	function UberAutoPrint(order_id)
	{
	//console.log('<?php echo $store_url;?>index.php?route=checkout/manual/autoprint_confirm&token=<?php echo $token; ?>&order_status_id=15&order_id=<?php echo $this->request->get['order_id'];?>&comment='+$('textarea[name=comment]').html());
	$.ajax({
		url: '<?php echo HTTPS_CATALOG;?>index.php?route=checkout/manual/autoprint_confirm&token=<?php echo $token; ?>&order_status_id=21&order_id='+order_id+'&comment=Replacement Order',
		type:'POST',
		
		beforeSend: function() {
		//	$('#order_history_msg').after('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
	},
	complete: function() {
			//$('.loading').remove();
		},
		
		success: function(data) {
		//	$('#order_history_msg').html(data).fadeIn('slow');
		alert('Check your printer');
	}
});	
}
function issueCredit()
{	
	setDecision('Issue Credit');
	generateRMA()
	$.ajax({
		url: 'index.php?route=sale/rma/issue_credit&token=<?php echo $token; ?>&order_id=<?php echo $this->request->get['order_id'];?>&amount='+$('input[name=ppat_amount]').val()+'&rma_number='+$('#rma_number').val(),
		type:'POST',
		data:$('#form').serialize(),
		beforeSend: function() {
			$('.button').attr('disabled',true);
		},
		complete: function() {

		},

		success: function(data) {
			alert(data);
			// parent.location.reload();
		}
	});
}
function updateList()
{
	var product_list = "";
	$('.amount_checkbox').each(function(index, element) {
		if($(element).is(':checked'))
		{
			product_list += $(element).val()+',';
		}
	});	
	product_list = product_list.slice(0,-1);
	$('#product_list').val(product_list);
}
function createRefundInvoice()
{
	$.ajax({
		url: 'index.php?route=sale/rma/refund_invoice&token=<?php echo $token; ?>&order_id=<?php echo $this->request->get['order_id'];?>',
		type:'POST',
		data:{product_list:$('#product_list').val()},
		beforeSend: function() {
		},
		complete: function() {
			
		},
		
		success: function(data) {
			//alert(data);
			//location.reload();
			generateRMA();
		}
	});
	
}
function verifyPins() {
	var qc_lead_pin = $('#qc_lead_pin').val();
	var manager_pin = $('#manager_pin').val();
	$('#auth_qc').val('');
	$('#auth_manager').val('');
	var ret = false;
	$.ajax({
		url: 'index.php?route=sale/rma/verifyPins&token=<?php echo $token; ?>&order_id=<?php echo $this->request->get['order_id'];?>',
		type: 'POST',
		dataType: 'json',
		async: false,
		data: {qc_lead_pin: qc_lead_pin, manager_pin: manager_pin},
		success: function(json) {
			var error = '';
			if (json['qc_lead_pin'] == null) {
				$('#qc_lead_pin').val('').css('border', '1px solid #f00');
				error += 'QC Lead Pin'
			} else {
				$('#qc_lead_pin').css('border', '1px solid green');
			}
			if (json['manager_pin'] == null) {
				$('#manager_pin').val('').css('border', '1px solid #f00');
				error += (error != '')? ' AND ': ' ';
				error += 'Manager Pin'
			} else {
				$('#manager_pin').css('border', '1px solid green');
			}
			if (json['qc_lead_pin'] == null || json['manager_pin'] == null) {
				alert(error + ' Invalid');
				ret = false;
			} else {
				$('#auth_qc').val(json['qc_lead_pin']);
				$('#auth_manager').val(json['manager_pin']);
				ret = true;
			}
		}
	});
	return ret;
	
}
function setDecision(value) {
	$('.amount_checkbox'). each(function(index, el) {
		if ($(el).is(':checked')) {
			$(el).parents('tr').find('.decision').val(value);
		}
	});
}
</script>
<div id="content">
	<?php if ($error_warning) { ?>
	<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="view/image/customer.png" alt="" /> <?php echo $heading_title; ?></h1>      
		</div>
		<div class="content">
			<form class="return-list" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<input type="hidden" id="auth_qc" name="auth_qc" />
				<input type="hidden" id="rma_number" name="rma_number" />
				<input type="hidden" id="auth_manager" name="auth_manager" />
				<input type="hidden" id="restocking" name="restocking" value="0" />
				<input type="hidden" id="restocking_grade" name="restocking_grade" />
				<input type="hidden" id="discount_per" name="discount_per" />
				<table class="list">
					<thead>
						<tr>
							<td width="1"></td>
							<td class="left">Product * SKU</td>
							<td class="left">Reason</td>
							<td class="left">How to Process</td>
							<td class="left">Item Condition</td>
							<td class="left">Issue</td>
							<!-- <td class="left">Decision</td> -->
							<td class="left">Comment</td>
							<td class="left">Printer</td>
						</tr>
					</thead>
					<tbody>
						<?php
						$i=0;
						$p = 0;
						$old_sku = '';
						foreach($products as $product)
						{
						if($sku_returned[$product['sku']]>0)
						{
						$sku_returned[$product['sku']]--;
						$p++;
					}
					else
					{
					$p=0;
					$product['is_processed']='';
				}
				if($old_sku!=$product['sku']) $p=1;
				?>
				<tr>
					<td>
						<input type="checkbox" class="amount_checkbox" onclick="updateList()" data-price="<?php echo $product['price'];?>" data-tax="<?php echo $product['tax'];?>" name="product[<?php echo $i;?>]" id="product<?php echo $i;?>" value="<?php echo $product['product_id'];?>" onchange="changeStatus(this,<?php echo $i;?>)" <?php  if($product['is_processed']!='') {echo 'disabled';} ?> />
					</td>
					<td><?php echo $product['sku'];?> <small> In Stock: <?php echo $product['in_stock_qty'];?></small><br /><strong><?php echo $product['name'];?></strong></td>
					<?php
					if($product['is_processed']=='')
					{
					?>
					<td>
						<select name="reason[<?php echo $i;?>]" id="reason<?php echo $i;?>" disabled="disabled" class="disabled">
							<?php foreach($rma_reasons as $reason) { ?>
							<option><?php echo $reason['title'];?></option>
							<?php } ?>
						</select>
					</td>
					<td>
						<select name="process[<?php echo $i;?>]" id="process<?php echo $i;?>" disabled="disabled" class="disabled">
							<option value="Exchange">Exchange</option>
							<option value="Refund">Refund</option>
						</select>
					</td>
					<td>
						<select class="condition" onchange="$(this).parents('tr').find('.amount_checkbox').trigger('onchange');" name="item_condition[<?php echo $i;?>]" id="condition<?php echo $i;?>" onChange="itemCondition(<?php echo $i;?>);" disabled="disabled" class="disabled">
							<?php foreach ($item_conditions as $item_condition): ?>
								<option value="<?php echo $item_condition['id'] ?>" >
									<?php echo $item_condition['value'] ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>
						<select name="item_issue[<?php echo $i; ?>]" id="item_issue<?php echo $i;?>" style="width:135px;" class="disabled" disabled="disabled">
							<option value="">Select One</option>
							<?php foreach ($item_issues as $item_issue): ?>
								<option value="<?php echo $item_issue['name'] ?>">
									<?php echo $item_issue['name'] ?>
								</option>
							<?php endforeach; ?>
						</select>
						<select name="decision[<?php echo $i;?>]" id="decision<?php echo $i;?>" disabled="disabled" style="display: none;" class="decision disabled">
							<option value="">Select One</option>
						</select>
					</td>
					<!-- <td>
						<select name="decision[<?php echo $i;?>]" id="decision<?php echo $i;?>" disabled="disabled" class="disabled">
							<option value="">Select One</option>
						</select>
					</td> -->
					<td>
						<input name="comment[<?php echo $i;?>]" id="comment<?php echo $i;?>" size="10" disabled="disabled" class="disabled">
					</td>

					<td>
						<select class="printer" name="printer[<?php echo $i;?>]" id="printer<?php echo $i;?>" disabled="disabled" class="disabled">
							<option value="">Do Not Print</option>
							<?php foreach ($printers as $printer): ?>
								<option value="<?php echo $printer['id'] ?>" >
									<?php echo $printer['value'] ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>

					<?php
				}
				else
				{
				?>
				<td colspan="6">Already Processed: <strong><?php echo ($sku_rma[$product['sku']][$p]?$sku_rma[$product['sku']][$p]:var_dump($sku_rma[$product['sku']]));?></strong></td>
				<?php
			}
			?>
		</tr>
		<?php
		$i++;
		$old_sku = $product['sku'];
	}
	?>
</tbody>
</table>
<input type="hidden" id="product_list" name="product_list" />
</form>
<?php
$paypal_check = $this->db->query("SELECT * FROM ".DB_PREFIX."paypal_admin_tools WHERE order_id='".(int)$this->request->get['order_id']."'");
$paypal_check = $paypal_check->row;
$authnet_check = $this->db->query("SELECT * FROM ".DB_PREFIX."authnetaim_admin WHERE order_id='".(int)$this->request->get['order_id']."'");
$authnet_check = $authnet_check->row;


?>
<form id="payment_method_paypal" style="margin-top:10px">
	<table>
		<tr style="font-weight:bold">
			<td>
				Restocking Fee
			</td>
			<td>
				<input type="text" size="5" id="restockingfee" value="0" /> (0% - 100%)
			</td>
		</tr>
		<tr style="font-weight:bold">
			<td>Refund Amount:</td>
			<td>
				<?php
				if($paypal_check)
				{
				?>
				<select name="ppat_action" onchange="$('#ppat_response').html(''); if (this.value == 'Partial') { $('input[name=ppat_amount]').show(); } else { $('input[name=ppat_amount]').hide(); }" style="display:none">
					<option value="Partial" selected>Partial Refund</option>
				</select>
				<?php
			}
			if($authnet_check)
			{
			?>
			<select name="aat_action" style="display:none" >
				<option value="CREDIT" selected>Credit</option>
			</select>
			<?php
		}
		?>
		<input  type="text" size="5" name="ppat_amount" value="0" readOnly /> (For Refund Or Credit Purposes Only)
		<input type="hidden" id="xtotal" />
		<input type="hidden" name="ppat_order_id" value="<?php echo $this->request->get['order_id'];?>" />
	</td>
</tr>
<?php
if($order_info['payment_method']=='Cash')
{
	?>
                 <!-- <tr style="font-weight:bold" style="display:none">
                  <td>Round Amount</td>
                  <td>
                  <input type="checkbox" id="round_value" onclick="roundValue()" /></td>
              </tr>-->
              <?php
          }
          ?>
          <?php
          if($paypal_check)
          {
          ?>
          <tr style="display:none">
          	<td>Environment:</td>
          	<td><select name="ppat_env"><option value="live" <?php if($ppat_env == 'live'){ echo 'selected="selected"'; } ?> >Live</option><option value="sandbox" <?php if($ppat_env == 'sandbox'){ echo 'selected="selected"'; } ?>>Sandbox</option></td>
          </tr>
          <tr  style="display:none">
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
          <?php
      }
      ?>      
  </table>
  <script type="text/javascript">
  	function issueRefund() {
  		setDecision('Issue Refund');
  		if($('#product_list').val()=='')
  		{
  			alert('Select an Item first');
  			return false;	
  		}
  		if (!confirm('Are you sure?')) {
  			return false;
  		}
  		<?php
  		if($paypal_check)
  		{
  			?>
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
	            //    alert(json['success']);
	            alert('PayPal Refund has been completed');
					//generateRMA();
					createRefundInvoice();
					//$('#order_update').click();
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
  			<?php
  		}
  		else if($authnet_check)
  		{
  			?>
  			$.ajax({
  				url: 'index.php?route=sale/order/aat_doaction&token=<?php echo $token; ?>',
  				type: 'post',
  				data: {aat_action:$('select[name=aat_action]').val(),aat_amount:$('input[name=ppat_amount]').val(),aat_order_id:$('input[name=ppat_order_id]').val(),aat_env:'<?php echo $aat_env;?>',aat_merchant_id:'<?php echo $aat_merchant_id;?>',aat_transaction_key:'<?php echo $aat_transaction_key;?>'},
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
	              // alert(json['success']);
	              alert('Authorize.Net Refund has been completed');
	              createRefundInvoice();
					//generateRMA();
					
					//$('#order_update').click();
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
  			<?php	
  		}
  		else
  		{
  			?>
  			alert('Cash Refund has been completed');
  			createRefundInvoice();
  			<?php	
  		}
  		?>
  	}
  </script>
</form>
<div style="text-align:center;" class="buttons">
	<br><br><br>
	<table style="width:100%;">
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="80%" colspan="5"><p>All storefront returns require a Quality Control (QC Lead) & Manager approval. <br> Restocking fee may apply based on PhonePartsUSA discretion. <br> Item replacement is dependent on in stock levels. <br> Store credit issued in the form of a voucher code sent to customer's email. <br> Refunds are available only for cash or PayPal transactions.</p></td>
			<td width="10%">&nbsp;</td>
		</tr>
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="10%">QC Lead</td>
			<td width="20%"><input style="width: 70px;" type="password" id="qc_lead_pin" autocomplete="off" /></td>
			<td width="20%">&nbsp;</td>
			<td width="10%">Manager</td>
			<td width="20%"><input style="width: 70px;" type="password" id="manager_pin" autocomplete="off" /></td>
			<td width="10%">&nbsp;</td>
		</tr>
	</table>
	<br><br>
	<?php if ($this->user->userHavePermission('pos_can_process_rma') || $this->user->getUserName() == 'admin') { ?>
	<!-- <a href="javascript:void(0)" class="button" onclick="if(confirmMsg()){generateRMA();}">Generate RMA only</a> -->
	<?php } ?> 
	<?php
	//  if(!$order_info['ref_order_id'])
	// {
	?>
	<a href="javascript:void(0)" class="button" onclick="if(confirmMsg()){issueReplacement();}">Issue Replacement</a>
	<?php
	// }
	?>
	<?php
	if($this->user->canIssueStoreCredit())
	{
	?>
	<?php if ($this->user->userHavePermission('pos_can_issue_store_credit') || $this->user->getUserName() == 'admin') { ?>
	<a href="javascript:void(0)" class="button" onclick="if(confirmMsg()){issueCredit();}">Issue Store Credit</a> 
	<?php } ?>
	<?php
} 

//  if(!$order_info['ref_order_id'])
// {
?>
<a href="javascript:void(0)" class="button" <?php echo (strtolower($order_info['payment_method']) == 'paypal express' || strtolower($order_info['payment_method']) == 'cash' || strtolower($order_info['payment_method']) == 'Debit Card / Credit Card')? 'onclick="if(confirmMsg()){issueRefund();}"': 'onclick="if(confirmCardMsg()){issueRefund();}"'; ?> id="ppat_submit">Issue Refund</a>
<?php
//}
//}?>
</div>
</div>
</div>
</div> 
<script>
	$(".return-list").keypress(function(e) {
		var code = ($(e).keyCode ? $(e).keyCode : $(e).which);
	if(code == 13) { //Enter keycode
		return false;
	}
});
	function itemCondition(i)
	{
		if($('#condition'+i).val()=='Item Issue')
		{
			$('#item_issue'+i).removeClass('disabled');
			$('#item_issue'+i).removeAttr('disabled');
		}
		else
		{
			$('#item_issue'+i).addClass('disabled');
			$('#item_issue'+i).attr('disabled','disabled');
		}
		itemDecision(i);
	}
	function itemDecision(i)
	{
		$.ajax({
			url: 'index.php?route=sale/rma/getDecision&token=<?php echo $token; ?>&item_condition='+$('#condition'+i).val(),
			type:'POST',
	//	data:$('#form').serialize(),
	beforeSend: function() {
		//	$('.button').attr('disabled',true);
	},
	complete: function() {
	},
	success: function(data) {
		$('#decision'+i).html(data);
	}
});	
	}
	function roundValue()
	{
		if($('#round_value').is(':checked')){
			var roundValue = 0.00;
			if($('input[name=ppat_amount]').val()<=5.00) // If less than 5.00 always round up
			{
				roundValue = Math.ceil($('#xtotal').val());
			}
			else
			{
				roundValue = Math.round($('#xtotal').val());
			}
			$('input[name=ppat_amount]').val(roundValue);
		}
		else
		{
			$('input[name=ppat_amount]').val($('#xtotal').val());   
		}	
	}
	function changeStatus(obj,i)
	{
		var product_amount = 0;
		var amount = 0;
		if(obj.checked==true)
		{
			$('#reason'+i).removeClass('disabled');
			$('#process'+i).removeClass('disabled');
			$('#condition'+i).removeClass('disabled');
			$('#decision'+i).removeClass('disabled');
			$('#comment'+i).removeClass('disabled');
			$('#reason'+i).removeAttr('disabled');
			$('#process'+i).removeAttr('disabled');
			$('#condition'+i).removeAttr('disabled');
			$('#decision'+i).removeAttr('disabled');
			$('#comment'+i).removeAttr('disabled').attr('requierd', 'requierd');;
			$('#printer'+i).removeAttr('disabled').attr('requierd', 'requierd');;
			itemCondition(i);
		}
		else
		{
			$('#decision'+i).addClass('disabled');
			$('#decision'+i).attr('disabled','disabled');
			$('#reason'+i).addClass('disabled');
			$('#process'+i).addClass('disabled');
			$('#condition'+i).addClass('disabled');
			$('#reason'+i).attr('disabled','disabled');
			$('#process'+i).attr('disabled','disabled');
			$('#condition'+i).attr('disabled','disabled');
			$('#item_issue'+i).addClass('disabled');
			$('#item_issue'+i).attr('disabled','disabled');
			$('#decision'+i).addClass('disabled');
			$('#decision'+i).attr('disabled','disabled');
			$('#comment'+i).addClass('disabled');
			$('#comment'+i).attr('disabled','disabled').removeAttr('requierd');
			$('#printer'+i).attr('disabled','disabled').removeAttr('requierd');

		}
		$('.amount_checkbox').each(function(index, element) {
			var condition = $(element).parents('tr').find('.condition').val();
			if($(element).is(":checked"))
			{
				if (condition == 'Good For Stock' || condition == 'Item Issue' || condition == 'Not Tested') {
					product_amount+= parseFloat($(element).attr('data-price'))+parseFloat($(element).attr('data-tax'));
				}
			}
			amount = product_amount + parseFloat(<?php echo $total_shipping;?>);
			if(parseFloat(product_amount)==0.00)
			{
				amount = 0.00;	
			}
			$('input[name=ppat_amount]').val(amount.toFixed(2));
			$('#xtotal').val(amount.toFixed(2));
			$('#restockingfee').trigger('change');
		});
	}
</script>
<?php echo $footer; ?>