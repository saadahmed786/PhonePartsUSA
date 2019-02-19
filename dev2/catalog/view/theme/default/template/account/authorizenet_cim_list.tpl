<?php
/**
 * Contains part of the Opencart Authorize.Net CIM Payment Module code.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to memiiso license.
 * Please see the LICENSE.txt file for more information.
 * All other rights reserved.
 *
 * @author     memiiso <gel.yine.gel@hotmail.com>
 * @copyright  2013-~ memiiso
 * @license    Commercial License. Please see the LICENSE.txt file
 */
?>
<?php echo $header; ?>
<?php if (isset($success) && $success) { ?>
<div class="success">
	<?php echo $success; ?>
</div>
<?php } ?>
<?php if (isset($error_warning) &&  $error_warning) { ?>
<div class="warning">
	<?php echo $error_warning; ?>
</div>
<?php } ?>
<?php if (isset($error) && $error) { ?>
<div class="warning">
	<?php echo $error; ?>
</div>
<?php } ?>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content">
	<?php echo $content_top; ?>
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?>
		<a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?>
		</a>
		<?php } ?>
	</div>

	<h1>
		<?php echo $heading_title; ?>
	</h1>
	<?php if (isset($text_error_connecting_cim_body)) { 	?>
	<div class="content">
		<?php echo $text_error_connecting_cim_body;  ?>
	</div>
	<?php }	?>
	<fieldset class="" style=" margin: 5px;">
		<legend> <h2><?php echo $text_cim_payment_accounts;  ?></h2>	</legend>
			
	<h2><?php echo $text_credit_card_entries; ?></h2>
	<?php 
	foreach ($cim_customer_profile->paymentProfiles as $paymentcard) {
		if (isset($paymentcard->payment->creditCard)) {
		?>
	<div class="content"
		id="payment_account_<?php echo $paymentcard->customerPaymentProfileId;?>">
		<table style="width: 100%;">
			<tr>
				<td><?php 

				echo $paymentcard->customerType.'<br>';
				if (isset($paymentcard->payment->creditCard->nameOnAccount)) {
		        	echo ' '.$paymentcard->payment->creditCard->nameOnAccount.'<br>';
		        }
		        echo $paymentcard->payment->creditCard->cardNumber;
		        if (isset($local_payment_profile_list['pid_'.$paymentcard->customerPaymentProfileId]) && ($local_payment_profile_list['pid_'.$paymentcard->customerPaymentProfileId]->cc_type)) {
		        	echo ' - '.$local_payment_profile_list['pid_'.$paymentcard->customerPaymentProfileId]->cc_type;
		        }
		        ?>
				</td>
				<td style="text-align: right;"><?php if($paymentcard->customerPaymentProfileId == $default_payment_profile_id) { ?>				
				<div class="default_cim" style="display: inline-block;margin-right: 12px;"><img src="catalog/view/theme/default/image/check.png" alt="" /></div>				
				 <?php }else{ ?> <a href="#"
					paymentaccountid="<?php echo $paymentcard->customerPaymentProfileId;?>"
					class="button setdefault_cim_payment_account"><span><?php echo $button_set_default; ?>
				
				</a> <?php } ?> <a href="#"
					paymentaccountid="<?php echo $paymentcard->customerPaymentProfileId;?>"
					class="button deletecimpa"><span><?php echo $button_delete; ?>				
				</a></td>
			</tr>
		</table>
	</div>
	<?php 
		}
	}
	?>
	<?php if($authorizenet_cim_disable_bank_payment != 'disable_bank_payment') { ?>
	<h2><?php echo $text_bank_accont_entries; ?></h2>
	<?php 
	foreach ($cim_customer_profile->paymentProfiles as $paymentcard) {
  		if (isset($paymentcard->payment->bankAccount)) { ?>
	<div class="content"
		id="payment_account_<?php echo $paymentcard->customerPaymentProfileId;?>">
		<table style="width: 100%;">
			<tr>
				<td><?php 
				echo $paymentcard->customerType.'<br>';
				if (isset($paymentcard->payment->bankAccount->nameOnAccount)) {
        	echo ' '.$paymentcard->payment->bankAccount->nameOnAccount.'<br>';
        }
        echo $paymentcard->payment->bankAccount->routingNumber.'<br>'.$paymentcard->payment->bankAccount->accountNumber;
        ?>
				</td>
				<td style="text-align: right;"><?php if($paymentcard->customerPaymentProfileId == $default_payment_profile_id) { ?>							
				<div class="default_cim" style="display: inline-block;margin-right: 12px;"><img src="catalog/view/theme/default/image/check.png" alt="" /></div>
				 <?php }else{ ?> <a href="#"
					paymentaccountid="<?php echo $paymentcard->customerPaymentProfileId;?>"
					class="button setdefault_cim_payment_account"><span><?php echo $button_set_default; ?>
				
				</a> <?php } ?> <a href="#"
					paymentaccountid="<?php echo $paymentcard->customerPaymentProfileId;?>"
					class="button deletecimpa"><span><?php echo $button_delete; ?> </span>
				</a>
				</td>
			</tr>
		</table>
	</div>
	<?php 
		}
	}
	?>
	<?php }  ?>
	<!-- 
	<h2><?php echo $text_adress_entries; ?></h2>
	<?php 
	foreach ($sc_adress_list as $address) { ?>
	<div class="content"
		id="payment_adress_<?php echo $address['address_id'];?>">
		<table style="width: 100%;">
			<tr>
				<td><?php 
				echo 'adresss<br>'; ?>
				</td>
				<td style="text-align: right;"><?php if($address['address_id'] == $sc_payment_adress_id) { ?>											
						<div class="default_cim"><img src="catalog/view/theme/default/image/check.png" alt="" /></div>
					<?php }else{ ?> <a href="#"
					paymentaddressid="<?php echo $address['address_id'];?>"
					class="button setdefault_cim_payment_adress"><span><?php echo $button_set_default; ?>
				</a> <?php } ?> 
				</td>
			</tr>
		</table>
	</div>
	<?php 	} ?>
	 -->
	 
	<div class="content" id="add_new_account" style="display: none;">
		<form action="" id="newcimpaymentaccount">
			<table class="">
				<tr style="font-size: 13px; font-weight: bold;">
					<td><input type="radio" name="select_payment_account" onchange="$('#t_new_ba').slideUp({complete:function(){$('#t_new_cc').slideDown();}});"
						checked="checked" id="create_new_credit_card"
						value="create_new_credit_card" /><label
						for="create_new_credit_card"><b><?php echo $text_create_newcredit_card; ?></b>
					</label></td>
				</tr>
				<?php if($authorizenet_cim_disable_bank_payment != 'disable_bank_payment') { ?>
				</tr>
					<td><input type="radio" name="select_payment_account" onchange="$('#t_new_cc').slideUp({complete:function(){$('#t_new_ba').slideDown();}});"
						id="create_new_bank_account" value="create_new_bank_account" /><label
						for="create_new_bank_account" style="font-weight: bold;"><b><?php echo $text_create_bank_account; ?></b>
					</label></td>
				</tr>
				<?php } ?>
				<tr>
					<td id="addnew_credit_card" valign="top"
						onclick="$('#create_new_credit_card').click();">
						<table class="" id="t_new_cc">
							<!--   
					<tr>
						<td><?php echo $entry_cc_owner; ?></td>
						<td><input type="text" name="cc_owner" value="" /></td>
					</tr>
					-->
							<tr>
								<td><span class="required">*</span> <?php echo $entry_cc_number; ?></td>
								<td><input type="text" name="cc_number" autocomplete="off" value="" /></td>
							</tr>
							<tr>
								<td><span class="required">*</span> <?php echo $entry_cc_expire_date; ?></td>
								<td><select name="cc_expire_date_month">
										<?php foreach ($months as $month) { ?>
										<option value="<?php echo $month['value']; ?>">
											<?php echo $month['text']; ?>
										</option>
										<?php } ?>
								</select> / <select name="cc_expire_date_year">
										<?php foreach ($year_expire as $year) { ?>
										<option value="<?php echo $year['value']; ?>">
											<?php echo $year['text']; ?>
										</option>
										<?php } ?>
								</select></td>
							</tr>
							<tr>
								<td><?php echo $entry_cc_cvv2; ?></td>
								<td><input type="text" name="cc_cvv2" autocomplete="off" value="" size="3" /></td>
							</tr>							
							<tr>
								<td><?php echo $entry_customer_type; ?></td>
								<td>
								<input type="radio" name="cc_payment_customer_type" checked="checked" id="payment_customer_type_individual" value="individual" /><label	for="payment_customer_type_individual"><?php echo $text_individual; ?> </label>
								<input type="radio" name="cc_payment_customer_type" id="payment_customer_type_business" value="business" /><label	for="payment_customer_type_business"><?php echo $text_business; ?> </label>
								</td>
							</tr>
						</table>
					</td>
					</tr>
					
					<tr>
					
					<td id="addnew_bank_account"
						onclick="$('#create_new_bank_account').click();">
						<table class="" id="t_new_ba" style="display: none;">
							<!-- 
					<tr>
						<td><?php echo $entry_ba_accounttype; ?></td>
						<td>
						<select name="ba_accounttype">
								<option value="checking"><?php echo $entry_checking; ?></option>
								<option value="savings"><?php echo $entry_savings; ?></option>
								<option value="businessChecking"><?php echo $entry_businesschecking; ?></option>
						</select>
						</td>
					</tr>
					-->
							<tr>
								<td><span class="required">*</span> <?php echo $entry_ba_routingnumber; ?></td>
								<td><input type="text" name="ba_routingnumber" autocomplete="off" value="" /></td>
							</tr>
							<tr>
								<td><span class="required">*</span> <?php echo $entry_ba_accountnumber; ?></td>
								<td><input type="text" name="ba_accountnumber"  autocomplete="off" value="" /></td>
							</tr>
							<tr>
								<td><span class="required">*</span> <?php echo $entry_ba_nameonaccount; ?></td>
								<td><input type="text" name="ba_nameonaccount" autocomplete="off" value="" /></td>
							</tr>
							<!-- 
					<tr>
						<td><?php echo $entry_ba_echecktype; ?></td>
						<td>
						<select name="ba_echecktype">
								<option value="CCD">CCD</option>
								<option value="PPD">PPD</option>
								<option value="TEL">TEL</option>
								<option value="ARC" disabled="disabled">ARC <?php echo $not_supported;?></option>
								<option value="BOC" disabled="disabled">BOC <?php echo $not_supported;?></option>
						</select>
						</td>
					</tr>
					 -->
							<tr>
								<td><?php echo $entry_ba_bankname; ?></td>
								<td><input type="text" name="ba_bankname" autocomplete="off" value="" /></td>
							</tr>
							<tr>
								<td><?php echo $entry_customer_type; ?></td>
								<td>								
								<input type="radio" name="ba_payment_customer_type" checked="checked" id="bapayment_customer_type_individual" value="individual" /><label	for="bapayment_customer_type_individual"><?php echo $text_individual; ?> </label>
								<input type="radio" name="ba_payment_customer_type" id="bapayment_customer_type_business" value="business" /><label	for="bapayment_customer_type_business"><?php echo $text_business; ?> </label>
								</td>
							</tr>
							
							
						</table>
					</td>
				</tr>
			</table>
			<?php if( $authorizenet_cim_require_billing_adress == 'forcebillingadress'){ ?>			
			<div id="payment_billing_address">	
<h3><?php echo $entry_cim_pa_billing_address; ?></h3>
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $entry_firstname; ?></td>
          <td><input type="text" name="firstname" value="" /></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
          <td><input type="text" name="lastname" value="" /></td>
        </tr>
        <!-- 
        <tr>
          <td><?php echo $entry_company; ?></td>
          <td><input type="text" name="company" value="" /></td>
        </tr>
         -->
        <tr>
          <td><span class="required">*</span> <?php echo $this->language->get('entry_telephone'); ?></td>
          <td><input type="text" name="telephone" value="" /></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_address_1; ?></td>
          <td><input type="text" name="address_1" value="" /></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_city; ?></td>
          <td><input type="text" name="city" value="" /></td>
        </tr>
        <tr>
          <td><span id="postcode-required" class="required">*</span> <?php echo $entry_postcode; ?></td>
          <td><input type="text" name="postcode" value="" /></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_country; ?></td>
          <td><select name="country_id">
              <option value=""><?php echo $text_select; ?></option>
              <?php foreach ($countries as $country) { ?>
              <?php if ($country['country_id'] == $country_id) { ?>
              <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_zone; ?></td>
          <td><select name="zone_id">
            </select>
		  </td>
        </tr>
      </table>
    </div>
			<?php  } ?>
			
		</form>
		<div class="right" style="text-align: right;">
			<a href="#" onclick="$('#add_new_account').slideUp(); return false;"
				class="button"><span><?php echo $button_cancel; ?> </span> </a> <a
				href="#" id="add_new_account_button" class="button"><span><?php echo $button_save; ?>
			</span> </a>
		</div>
	</div>

		<?php 
		if(!isset($text_error_connecting_cim_body)) { ?>
		<div class="right" style="text-align: right;">
			<a href="#"
				onclick="$('#add_new_account').slideDown(); return false;"
				class="button"><span><?php echo $button_new_pamet_account; ?> </span>
			</a>
		</div>
		<?php  } ?>
	</fieldset>
	
	
	<!-- 
	<fieldset class="" style=" margin: 5px;">
		<legend> <h2> Single Click Purchase Settings</h2>	</legend>
	<h2><?php echo $text_single_click_setup; ?></h2>
	<div class="content">
	
		<form action="" id="singleclicksetup">
		<table style="width: 100%;">
			<tr>				
				<td><?php echo $text_sc_billing_address; ?> </td>
				<td><?php echo $text_sc_shiping_address; ?> </td>
				<td><?php echo $text_sc_shiping_method; ?> </td>
				<td><?php echo $text_sc_payment_card; ?> </td>
			</tr>
			<tr>				
				<td>		
				</td>
				<td>			
				</td>
				<td>	
					<div id="sc_shipping_method">
					<?php echo $button_sc_select_shipping_address ; ?>
					</div>
				</td>
				<td>		
				</td>
			</tr>
			<tr>
				<td colspan="4" style="text-align: right;">
				<a href="#" class="button set_single_click_options"><span><?php echo $button_sc_save; ?></a>
			 	</td>
			</tr>
		</table>
		</form>
	</div>	
	</fieldset>
	 -->
	 
	<?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>

<script type="text/javascript">
$(".deletecimpa").click(function(){
  var id = $(this).attr('paymentaccountid');
	$.ajax({
		url: '<?php echo $delete; ?>',
		type: 'post',
		data:  { cimpaymentid: id },
		dataType: 'json',		
		beforeSend: function() {
			$(this).attr('disabled', true);
			$(this).before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /></div>');
		},
		complete: function() {
			$(this).attr('disabled', false);
			$('.attention').remove();
		},				
		success: function(json) {
			if (json['error']) {
				<?php if ($authorizenet_cim_use_jquerydialog == 'usejquerdialog') { ?> 
				json['error'] = json['error'].replace(/\n/g, '<br />');	
				$("<div>"+json['error']+"</div>").dialog({
					modal: true,
					buttons: {
						'<?php echo $text_close; ?>': function() {
						$( this ).dialog( "close" );
						}
					}
					});
			<?php }else {?>
			alert(json['error']);
		  <?php } ?>				
			}
			if (json['success']) {
				<?php if ($authorizenet_cim_use_jquerydialog == 'usejquerdialog') { ?> 
				json['success'] = json['success'].replace(/\n/g, '<br />');	
				$("<div>"+json['success']+"</div>").dialog({
					modal: true,
					buttons: {
						'<?php echo $text_close; ?>': function() {
						$( this ).dialog( "close" );
						$('#payment_account_'+id).slideUp();
						location = json['success_url'];
						}
					}
					});
			<?php }else {?>
			alert(json['success']);
		  <?php } ?>
				$('#payment_account_'+id).slideUp();
				location = json['success_url'];
			}
		}
	}); 
  return false;
});

$(".setdefault_cim_payment_account").click(function(){
	  var id = $(this).attr('paymentaccountid');
		$.ajax({
			url: '<?php echo $setdefaultpayment; ?>',
			type: 'post',
			data:  { default_paymentid: id },
			dataType: 'json',		
			beforeSend: function() {
				$(this).attr('disabled', true);
				$(this).before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /></div>');
			},
			complete: function() {
				$(this).attr('disabled', false);
				$('.attention').remove();
			},				
			success: function(json) {
				if (json['error']) {
					<?php if ($authorizenet_cim_use_jquerydialog == 'usejquerdialog') { ?> 
					json['error'] = json['error'].replace(/\n/g, '<br />');	
					$("<div>"+json['error']+"</div>").dialog({
						modal: true,
						buttons: {
							'<?php echo $text_close; ?>': function() {
							$( this ).dialog( "close" );
							}
						}
						});
				<?php }else {?>
				alert(json['error']);
			  <?php } ?>				
				}
				if (json['success']) {
					<?php if ($authorizenet_cim_use_jquerydialog == 'usejquerdialog') { ?> 
					json['success'] = json['success'].replace(/\n/g, '<br />');	
					$("<div>"+json['success']+"</div>").dialog({
						modal: true,
						buttons: {
							'<?php echo $text_close; ?>': function() {
							$( this ).dialog( "close" );
							location = json['success_url'];
							}
						}
						});
				<?php }else {?>
				alert(json['success']);
			  <?php } ?>
			  location = json['success_url'];
				}
			}
		}); 
	  return false;
	});

$(".setdefault_cim_payment_adress").click(function(){
	  var id = $(this).attr('paymentaddressid');
		$.ajax({
			url: '<?php echo $setdefaultaddress; ?>',
			type: 'post',
			data:  { default_payment_addressid: id },
			dataType: 'json',		
			beforeSend: function() {
				$(this).attr('disabled', true);
				$(this).before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /></div>');
			},
			complete: function() {
				$(this).attr('disabled', false);
				$('.attention').remove();
			},				
			success: function(json) {
				if (json['error']) {
					<?php if ($authorizenet_cim_use_jquerydialog == 'usejquerdialog') { ?> 
					json['error'] = json['error'].replace(/\n/g, '<br />');	
					$("<div>"+json['error']+"</div>").dialog({
						modal: true,
						buttons: {
							'<?php echo $text_close; ?>': function() {
							$( this ).dialog( "close" );
							}
						}
						});
				<?php }else {?>
				alert(json['error']);
			  <?php } ?>				
				}
				if (json['success']) {
					<?php if ($authorizenet_cim_use_jquerydialog == 'usejquerdialog') { ?> 
					json['success'] = json['success'].replace(/\n/g, '<br />');	
					$("<div>"+json['success']+"</div>").dialog({
						modal: true,
						buttons: {
							'<?php echo $text_close; ?>': function() {
								$( this ).dialog( "close" );
								 location = json['success_url'];
								}
							}
						});
				<?php }else {?>
				alert(json['success']);
			  <?php } ?>
			  location = json['success_url'];
				}
			}
		}); 
	  return false;
	});

$("#add_new_account_button").click(function(){
		$.ajax({
			url: '<?php echo $insert; ?>',
			type: 'post',
			data:  $('#newcimpaymentaccount').serialize(),
			dataType: 'json',		
			beforeSend: function() {
				$(this).attr('disabled', true);
				$(this).before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /></div>');
			},
			complete: function() {
				$(this).attr('disabled', false);
				$('.attention').remove();
			},				
			success: function(json) {
				if (json['error']) {
					<?php if ($authorizenet_cim_use_jquerydialog == 'usejquerdialog') { ?> 
					json['error'] = json['error'].replace(/\n/g, '<br />');	
					$("<div>"+json['error']+"</div>").dialog({
						modal: true,
						buttons: {
							'<?php echo $text_close; ?>': function() {
							$( this ).dialog("close");
							}						
						}
						});
				<?php }else {?>
				alert(json['error']);
			  <?php } ?>					
				}
				if (json['success']) {
					<?php if ($authorizenet_cim_use_jquerydialog == 'usejquerdialog') { ?> 
					json['success'] = json['success'].replace(/\n/g, '<br />');	
					$("<div>"+json['success']+"</div>").dialog({
						modal: true,
						buttons: {
							'<?php echo $text_close; ?>': function() {
							$( this ).dialog( "close");
							location = json['success_url'];
							}
						}
						});
				<?php }else {?>
				alert(json['success']);
			  <?php } ?>
			  location = json['success_url'];
				}
			}
		}); 
	  return false;
	});


//Adress code 
$('select[name=\'country_id\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=account/address/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},		
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#postcode-required').show();
			} else {
				$('#postcode-required').hide();
			}
			
			html = '<option value=""><?php echo $text_select; ?></option>';
			
			if (json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
     			html += '<option value="' + json['zone'][i]['zone_id'] + '"';
	    			
					if (json['zone'][i]['zone_id'] == '') {
	      				html += ' selected="selected"';
	    			}
	
	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}
			
			$('select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name=\'country_id\']').trigger('change');

</script>
