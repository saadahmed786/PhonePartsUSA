	<?php if (isset($errors['warning'])) { ?>
	<div class="warning"><?php echo $errors['warning']; ?></div>
	<?php } ?>	
  
	<form action="index.php?route=checkout/register/validate" method="post">
		<fieldset>
			<h2><?php echo $text_your_details; ?></h2>
			<ul>
				<li>
					<label for="firstname"><span class="required">*</span> <?php echo $entry_firstname; ?></label>
					<input type="text" id="firstname" name="firstname" value="<?php if (isset($firstname)) echo $firstname; ?>"  />					
				</li>				
				<li>
					<label for="lastname"><span class="required">*</span> <?php echo $entry_lastname; ?></label>
					<input type="text" id="lastname" name="lastname" value="<?php if (isset($lastname)) echo $lastname; ?>"  />					
				</li>
				<li>
					<label for="email"><span class="required">*</span> <?php echo $entry_email; ?></label>
					<input type="email" id="email" name="email" value="<?php if (isset($email)) echo $email; ?>"  />					
				</li>
				<li>
					<label for="telephone"><span class="required">*</span> <?php echo $entry_telephone; ?></label>
					<input type="tel" id="telephone" name="telephone" value="<?php if (isset($telephone)) echo $telephone; ?>"  />					
				</li>
			</ul>
			<input type="hidden" id="fax" name="fax" value="<?php if (isset($fax)) echo $fax; ?>"  /> 			
		</fieldset>
		
		<fieldset>
			<h2><?php echo $text_your_address; ?></h2> 			
			<input type="hidden" id="company" name="company" value="<?php if (isset($company)) echo  $company; ?>"  />
			<ul>
				<li style="display: <?php echo (count($customer_groups) > 1 ? 'block' : 'none'); ?>;">
					<label for="customer_group_id"><?php echo $entry_account; ?></label>
					<select id="customer_group_id" name="customer_group_id">
					<?php foreach ($customer_groups as $customer_group) { ?>
						<?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
						<option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
						<?php } ?>
					<?php } ?>
					</select>
				</li>
				<li id="company-id-display">
					<label for="company-id-required"><span id="company-id-required" class="required">*</span> <?php echo $entry_company_id; ?></label>
					<input type="text" id="company_id" name="company_id" value=""  />
				</li>
				<li id="tax-id-display">
					<label for="tax_id"><span id="tax-id-required" class="required">*</span> <?php echo $entry_tax_id; ?></label>
					<input type="text" id="tax_id" name="tax_id" value="" />
				</li>			
				<li>
					<label for="address_1"><span class="required">*</span> <?php echo $entry_address_1; ?></label>
					<input type="text" id="address_1" name="address_1" value="<?php if (isset($address_1)) echo $address_1; ?>"  />					
				</li>
				<li>
					<label for="address_2"><?php echo $entry_address_2; ?></label>
					<input type="text" id="address_2" name="address_2" value="<?php if (isset($address_2)) echo $address_2; ?>"  />					
				</li>
				<li>
					<label for="city"><span class="required">*</span> <?php echo $entry_city; ?></label>
					<input type="text" id="city" name="city" value="<?php if (isset($city)) echo $city; ?>"  />					
				</li>
				<li>
					<label for="postcode"><span class="required">*</span> <?php echo $entry_postcode; ?></label>
					<input type="text" id="postcode" name="postcode" value="<?php if (isset($postcode)) echo $postcode; ?>"  />					
				</li>
				<li>
					<label for="country_id"><span class="required">*</span> <?php echo $entry_country; ?></label>
					<select id="country_id" name="country_id" >
						<option value=""><?php echo $text_select; ?></option>
						<?php foreach ($countries as $country) { ?>
						<?php if ($country['country_id'] == $country_id) { ?>
						<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
						<?php } ?>
						<?php } ?>
					</select>								
				</li>
				<li>
					<label for="zone_id"><span class="required">*</span> <?php echo $entry_zone; ?></label>
					<select id="zone_id" name="zone_id" >						
					</select>					
				</li>
				<?php if ($shipping_required) { ?>
				<li>				
					<hr>
					<label for="shipping_address">					
						<input type="checkbox" name="shipping_address" value="1" id="shipping_address" checked="checked" />
						<?php echo $entry_shipping; ?>
					</label>				
				</li>
				<?php } ?>
			</ul>
		</fieldset>
		<fieldset>	
			<h2><?php echo $text_your_password; ?></h2>
			<ul>		
				<li>
					<label for="password"><span class="required">*</span> <?php echo $entry_password; ?></label>
					<input type="password" id="password" name="password" value="<?php if (isset($password)) echo $password; ?>" />					
				</li>
				<li>
					<label for="confirm"><span class="required">*</span> <?php echo $entry_confirm; ?></label>
					<input type="password" id="confirm" name="confirm" value="<?php if (isset($confirm)) echo $confirm; ?>" />					
				</li>
				<li>
					<hr />
					<label for="newsletter"><input type="checkbox" name="newsletter" value="1" id="newsletter" checked="checked" /><?php echo $entry_newsletter; ?>		
					</label>				
				</li>
		  </ul>
		</fieldset>
		<?php if ($text_agree) { ?>
		<fieldset>
			<ul>
				<li>
					<label for="agree">
						<input type="checkbox" id="agree" name="agree" value="1" /><?php echo $text_agree; ?>
					</label>
				</li>
			</ul>
		</fieldset>
		<?php } ?>
		<input type="submit" value="<?php echo $button_continue; ?>"  id="button-register" />
	</form>
	
	<script type="text/javascript"><!--	
	$('#payment-address select[name=\'customer_group_id\']').bind('change', function() {
		var customer_group = [];
		
	<?php foreach ($customer_groups as $customer_group) { ?>
		customer_group[<?php echo $customer_group['customer_group_id']; ?>] = [];
		customer_group[<?php echo $customer_group['customer_group_id']; ?>]['company_id_display'] = '<?php echo $customer_group['company_id_display']; ?>';
		customer_group[<?php echo $customer_group['customer_group_id']; ?>]['company_id_required'] = '<?php echo $customer_group['company_id_required']; ?>';
		customer_group[<?php echo $customer_group['customer_group_id']; ?>]['tax_id_display'] = '<?php echo $customer_group['tax_id_display']; ?>';
		customer_group[<?php echo $customer_group['customer_group_id']; ?>]['tax_id_required'] = '<?php echo $customer_group['tax_id_required']; ?>';
	<?php } ?>	

		if (customer_group[this.value]) {
			if (customer_group[this.value]['company_id_display'] == '1') {
				$('#company-id-display').show();
			} else {
				$('#company-id-display').hide();
			}
			
			if (customer_group[this.value]['company_id_required'] == '1') {
				$('#company-id-required').show();
			} else {
				$('#company-id-required').hide();
			}
			
			if (customer_group[this.value]['tax_id_display'] == '1') {
				$('#tax-id-display').show();
			} else {
				$('#tax-id-display').hide();
			}
			
			if (customer_group[this.value]['tax_id_required'] == '1') {
				$('#tax-id-required').show();
			} else {
				$('#tax-id-required').hide();
			}	
		}
	});
	
	$('#payment-address select[name=\'customer_group_id\']').trigger('change');

	$('#payment-address select[name=\'country_id\']').bind('change', function() {			
		
		$.ajax({
			url: 'index.php?route=checkout/checkout/country&country_id=' + $('#payment-address select[name=\'country_id\']').val(),
			dataType: 'json',
			beforeSend: function() {				
				$('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>').insertAfter('#payment-address select[name=\'country_id\']');
			},
			complete: function() {
				$('.wait').remove();
			},			
			success: function(json) {								
				if (json['postcode_required'] == '1') {
					$('#payment-postcode-required').show();
				} else {
					$('#payment-postcode-required').hide();
				}
				
				html = '<option value=""><?php echo $text_select; ?></option>';
				
				if (json['zone'] != '') {
					for (i = 0; i < json['zone'].length; i++) {
						html += '<option value="' + json['zone'][i]['zone_id'] + '"';
						
						if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
							html += ' selected="selected"';
						}
		
						html += '>' + json['zone'][i]['name'] + '</option>';
					}
				} else {
					html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
				}
				
				$('#payment-address select[name=\'zone_id\']').html(html);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
	
	$('#payment-address select[name=\'country_id\']').trigger('change');
	//--></script>	