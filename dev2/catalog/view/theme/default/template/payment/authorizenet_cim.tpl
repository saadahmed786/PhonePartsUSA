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
<h2>
	<?php echo $text_credit_card; ?>
</h2>
<?php if (isset($error) && $error): ?>
<div class="content" id="payment">
	<div class="warning" id="cim_error">
		<?php echo $error; ?>
	</div>
</div>
<?php else : ?>

<style type="text/css">
table.form tr td:first-child {
    vertical-align: top;
    width: inherit;
}
</style>

<div class="content" id="payment">
<form action="" id="cimpayment">
	<table class="form">
<?php if ($isguest) {
	/* Start Quest Form  */
	?>
	<tr>
		<td id="guest_credit_card">
		<div id="guest_payment_option" >	
			<table >
				<tr>
					<td style="width: 200px;"><span class="required">*</span> <?php echo $entry_cc_number; ?></td>
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
			</table>
			</div>
		</td>
	</tr>
	

<?php } else { 
	/* Start Cim Form  */
	?>
	<?php if (isset($cim_customer_profile->paymentProfiles)) {?>
	<tr style="font-size: 13px; font-weight: bold;">
		<td style="width: inherit;"><input type="radio" name="select_payment_account" onchange="$('#cim_payment_option2').slideUp();$('#cim_payment_option3').slideUp();$('#payment_billing_address').slideUp();"
			id="use_cim_payment_account" value="use_cim_payment_account"
			checked="checked"><label for="use_cim_payment_account" style="font-weight: bold;"><?php echo $text_select_paymentaccount; ?>
		</label></td>
	</tr>
	<tr>
		<td id="cim_accounts"
			onclick="$('#use_cim_payment_account').click();">				
			<div id="cim_payment_option1">				
			<select	name="customer_payment_profile_id">
			<option	value=""><?php echo $text_select; ?></option>
			
				<?php foreach ($cim_customer_profile->paymentProfiles as $paymentcard) { ?>
				<?php if ( isset($paymentcard->payment->creditCard) ) : ?>
				<option <?php echo ($paymentcard->customerPaymentProfileId == $default_payment_profile_id) ? 'selected="selected"' : '' ;  ?>
					value="<?php echo $paymentcard->customerPaymentProfileId; ?>">
					<?php echo $text_select_prfx_credit_card.': '.$paymentcard->payment->creditCard->cardNumber; /* .' '.$paymentcard->payment->creditCard->expirationDate; */ 
			        if (isset($local_payment_profile_list['pid_'.$paymentcard->customerPaymentProfileId]) && ($local_payment_profile_list['pid_'.$paymentcard->customerPaymentProfileId]->cc_type)) {
			        	echo ' - '.$local_payment_profile_list['pid_'.$paymentcard->customerPaymentProfileId]->cc_type;
			        }if (isset($paymentcard->payment->creditCard->nameOnAccount)) {
						echo ' '.$paymentcard->payment->creditCard->nameOnAccount;
					}
					?> 
				</option>
				<?php  else :  ?>
				<option <?php echo ($paymentcard->customerPaymentProfileId == $default_payment_profile_id) ? 'selected="selected"' : '' ;  ?>
					value="<?php echo $paymentcard->customerPaymentProfileId; ?>">
					<?php echo $text_select_prfx_bank_account.': '.$paymentcard->payment->bankAccount->accountNumber ; 
					if (isset($paymentcard->payment->bankAccount->nameOnAccount)) {
						echo ' '.$paymentcard->payment->bankAccount->nameOnAccount;
					}
					?> 
				</option>
				<?php endif; }?>
		  </select>
		 </div>
		</td>
	</tr>	
	<?php } ?>	
	<tr>
		<td><input type="radio" name="select_payment_account" onchange="$('#cim_payment_option3').slideUp();$('#cim_payment_option2').slideDown();$('#payment_billing_address').slideDown();"
			id="create_new_credit_card" <?php if(! isset($cim_customer_profile->paymentProfiles)) echo 'checked="checked"'; ?> value="create_new_credit_card" /><label
			for="create_new_credit_card"style="font-weight: bold;"><?php echo $text_wanttouse_newcredit_card; ?>
		</label></td>
	</tr>
		<?php if($authorizenet_cim_disable_bank_payment != 'disable_bank_payment') { ?>
	<tr>						
		<td><input type="radio" name="select_payment_account" onchange="$('#cim_payment_option2').slideUp();$('#cim_payment_option3').slideDown();$('#payment_billing_address').slideDown();"
			id="create_new_bank_account" value="create_new_bank_account" /><label
			for="create_new_bank_account" style="font-weight: bold;"><?php echo $text_wanttouse_bank_account; ?>
		</label></td>
	</tr>
	<?php } ?>	
	<tr>
		<td id="custom_credit_card" onclick="$('#create_new_credit_card').click();">
		<div id="cim_payment_option2" style="<?php if(isset($cim_customer_profile->paymentProfiles)) echo "display: none;" ?>">	
			<table >
			<!-- 
				<tr>
					<td><?php echo $entry_cc_owner; ?></td>
					<td><input type="text" name="cc_owner" value="" /></td>
				</tr>
			 -->
				<tr>
					<td style="width: 200px;"><span class="required">*</span> <?php echo $entry_cc_number; ?></td>
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
			</div>
		</td>
	</tr>
	<?php if($authorizenet_cim_disable_bank_payment != 'disable_bank_payment') { ?>
	<tr>
		<td id="custom_bank_account" onclick="$('#create_new_bank_account').click();">
		<div id="cim_payment_option3" style="display: none;">	
			<table >
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
					<td style="width: 200px;"><span class="required">*</span> <?php echo $entry_ba_routingnumber; ?></td>
					<td><input type="text" name="ba_routingnumber" autocomplete="off" value="" /></td>
				</tr>
				<tr>
					<td><span class="required">*</span> <?php echo $entry_ba_accountnumber; ?></td>
					<td><input type="text" name="ba_accountnumber" autocomplete="off" value="" /></td>
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
				<tr>
					<td><?php echo $entry_ba_bankname; ?></td>
					<td><input type="text" name="ba_bankname" value="" /></td>
				</tr>
				 -->
				<tr>
					<td><?php echo $entry_customer_type; ?></td>
					<td>								
					<input type="radio" name="ba_payment_customer_type" checked="checked" id="bapayment_customer_type_individual" value="individual" /><label	for="bapayment_customer_type_individual"><?php echo $text_individual; ?> </label>
					<input type="radio" name="ba_payment_customer_type" id="bapayment_customer_type_business" value="business" /><label	for="bapayment_customer_type_business"><?php echo $text_business; ?> </label>
					</td>
				</tr>
			</table>
			</div>
		</td>
	</tr>	
		<?php } ?>
	
<?php } /* END Cim Form  */  ?>	

<?php if( $authorizenet_cim_require_billing_adress == 'forcebillingadress'){ ?>	
<tr>
	<td>	
	<div id="payment_billing_address" style="<?php if(!$isguest) echo "display: none;"; ?>">	
	<h3 onclick="$('#payment_billing_address_tbl').slideToggle();" style="cursor: pointer;" ><?php echo $text_validate_billing_adress; ?></h3>
		<div id="payment_billing_address_tbl" style="<?php if(!$isguest && false) echo "display: none;"; ?>">
	      <table >
	        <tr>
	          <td  style="width: 200px;"><span class="required">*</span> <?php echo $entry_firstname; ?></td>
	          <td><input type="text" name="firstname" value="<?php echo $payment_address['firstname']; ?>" /></td>
	        </tr>
	        <tr>
	          <td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
	          <td><input type="text" name="lastname" value="<?php echo $payment_address['lastname']; ?>" /></td>
	        </tr>
	        <!-- 
	        <tr>
	          <td><?php echo $entry_company; ?></td>
	          <td><input type="text" name="company" value="<?php echo $payment_address['company']; ?>" /></td>
	        </tr>
	         -->
	        <tr>
	          <td><span class="required">*</span> <?php echo $this->language->get('entry_telephone'); ?></td>
	          <td><input type="text" name="telephone" value="<?php echo $payment_address['telephone']; ?>" /></td>
	        </tr>
	        <tr>
	          <td><span class="required">*</span> <?php echo $entry_address_1; ?></td>
	          <td><input type="text" name="address_1" value="<?php echo $payment_address['address_1'].' '.$payment_address['address_2']; ?>" /></td>
	        </tr>
	        <tr>
	          <td><span class="required">*</span> <?php echo $entry_city; ?></td>
	          <td><input type="text" name="city" value="<?php echo $payment_address['city']; ?>" /></td>
	        </tr>
	        <tr>
	          <td><span id="postcode-required" class="required">*</span> <?php echo $entry_postcode; ?></td>
	          <td><input type="text" name="postcode" value="<?php echo $payment_address['postcode']; ?>" /></td>
	        </tr>
	        <tr>
	          <td><span class="required">*</span> <?php echo $entry_country; ?></td>
	          <td><select name="country_id">
	              <option value=""><?php echo $text_select; ?></option>
	              <?php foreach ($countries as $country) { ?>
	              <?php if ($country['country_id'] == $payment_address['country_id']) { ?>
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
	            <?php if($isguest){ ?>
	            <input type="hidden" name="select_payment_account" value="create_new_credit_card" />
	            <?php } ?>
			  </td>
	        </tr>
	      </table>
	    </div>
	    </div>	
	</td>
</tr>
			<?php  } ?>	
					
	</table>	
				
</form> 
</div>
<div class="buttons">
	<div class="right">
		<span class="button_pink"><input type="button" id="button-confirm" class="button" value="<?php echo $button_confirm; ?>" > </span>
	</div>
</div>

<script type="text/javascript"><!--
	$('#button-confirm').bind('click', function() {
		$.ajax({
			url: 'index.php?route=payment/authorizenet_cim/send',
			type: 'post',
			data: $('#cimpayment').serialize(),
			dataType: 'json',		
			beforeSend: function() {
				$('#button-confirm').attr('disabled', true);
				$('#payment').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
			},
			complete: function() {
				$('.attention').remove();
			},				
			success: function(json) {
				if (json['error']) {
				   $('#button-confirm').attr('disabled', false);					
					<?php if ($authorizenet_cim_use_jquerydialog == 'usejquerdialog') { ?>
					json['error'] = json['error'].replace(/\n/g, '<br />'); 
					$("<div>"+json['error']+"</div>").dialog({
						modal: true,
						buttons: {
							<?php echo $text_close; ?>: function() {
							$( this ).dialog( "close" );
							$('.attention').remove();
							}
						}
						});
				<?php }else {?>
				alert(json['error']);
			  <?php } ?>					
				}
			   if (json['success_held']) {					
					<?php if ($authorizenet_cim_use_jquerydialog == 'usejquerdialog') { ?>
					json['success_held'] = json['success_held'].replace(/\n/g, '<br />'); 
					$("<div>"+json['success_held']+"</div>").dialog({
						modal: true,
						buttons: {
							<?php echo $text_close; ?>: function() {
							$( this ).dialog( "close" );
							$('.attention').remove();
							location = json['checkout_success_url'];
							}
						}
						});
					<?php }else {?>
					alert(json['success_held']);
					 location = json['checkout_success_url'];
				  <?php } ?>
					 $('.attention').remove();
					 location = json['checkout_success_url'];
				   }else if (json['checkout_success_url']) {
						$('.attention').remove();
						location = json['checkout_success_url'];
					}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				    $('#button-confirm').attr('disabled', false);
					alert(thrownError);
				}
		});
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
	    			
					if (json['zone'][i]['zone_id'] == '<?php echo $payment_address['zone_id'];?>') {
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
	
//--></script>
<?php endif; ?>