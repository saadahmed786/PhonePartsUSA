	<form action="index.php?route=checkout/payment_address/validate" method="post">
		<?php if ($addresses) { ?>	
		<fieldset>
			<label>
				<?php if ((isset($payment_address) && ($payment_address != 'existing'))) { ?>		
				<input type="radio" name="payment_address" value="existing" id="payment_address"  />
				<?php } else { ?>
				<input type="radio" name="payment_address" value="existing" id="payment_address" checked="checked" />
				<?php } ?>
				<?php echo $text_address_existing; ?>
			</label>
			
			<div id="payment-existing">
			<select id="address_id" name="address_id" size="5">
				<?php foreach ($addresses as $address) { ?>
				<?php if ($address['address_id'] == $address_id) { ?>
				<option value="<?php echo $address['address_id']; ?>" selected="selected"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['country']; ?></option>
				<?php } else { ?>
				<option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['country']; ?></option>
			<?php } ?>
			<?php } ?>
			</select>
			</div>			
		</fieldset>		
		<?php } 	?>
		<fieldset>			
			<label>
				<?php if ((isset($payment_address) && ($payment_address == 'new'))) { ?>
				<input type="radio" name="payment_address" value="new" id="payment_address" checked="checked" />
				<?php } else { ?>
				<input type="radio" name="payment_address" value="new" id="payment_address" />
				<?php } ?>
				<?php echo $text_address_new; ?>
			</label>			
			<ul id="payment-new" style="display: <?php echo ($addresses ? 'none' : 'block'); ?>;">
				<li>
					<label for="firstname"><span class="required">*</span> <?php echo $entry_firstname; ?></label>
					<input type="text" id="firstname" name="firstname" value="<?php echo isset($firstname) ? $firstname : ''; ?>"  />
				</li>				
				<li>
					<label for="lastname"><span class="required">*</span> <?php echo $entry_lastname; ?></label>
					<input type="text" id="lastname" name="lastname" value="<?php echo isset($lastname) ? $lastname : ''; ?>"  />
				</li>
				<li>
					<label for="company"><?php echo $entry_company; ?></label>
					<input type="text" id="company" name="company" value=""  />
				</li>
				<li style="display: <?php echo ($company_id_display ? 'block' : 'none'); ?>;">
					<label for="company_id"><span id="company-id-required" style="display: <?php echo ($company_id_required ? 'inline' : 'none'); ?>;" class="required">*</span> <?php echo $entry_company_id; ?></label>
					<input type="text" id="company_id" name="company_id" value=""  />			
				</li>
				<li style="display: <?php echo ($tax_id_display ? 'block' : 'none'); ?>;">
					<label for="tax_id"><span id="tax-id-required" style="display: <?php echo ($tax_id_required ? 'inline' : 'none'); ?>;" class="required">*</span> <?php echo $entry_tax_id; ?></label>
					<input type="text" id="tax_id" name="tax_id" value=""  />			
				</li>							
				<li>
					<label for="address_1"><span class="required">*</span> <?php echo $entry_address_1; ?></label>
					<input type="text" id="address_1" name="address_1" value="<?php echo isset($address_1) ? $address_1 : ''; ?>"  />
				</li>
				<li>
					<label for="address_2"><?php echo $entry_address_2; ?></label>
					<input type="text" id="address_2" name="address_2" value="<?php echo isset($address_2) ? $address_2 : ''; ?>"  />
				</li>
				<li>
					<label for="city"><span class="required">*</span> <?php echo $entry_city; ?></label>
					<input type="text" id="city" name="city" value="<?php echo isset($city) ? $city : ''; ?>"  />
				</li>			
				<li>
					<label for="postcode"><span id="payment-postcode-required" class="required">*</span> <?php echo $entry_postcode; ?></label>
					<input type="text" id="postcode" name="postcode" value="<?php echo isset($postcode) ? $postcode : ''; ?>"  />
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
			</ul>		
		</fieldset>
		<input type="submit" value="<?php echo $button_continue; ?>" id="button-payment-address" />
	</form>
	
	<script type="text/javascript"><!--
	$('#payment-address input[name=\'payment_address\']').bind('change', function() {		
		if (this.value == 'new') {
			$('#payment-existing').hide();
			$('#payment-new').show();
		} else {
			$('#payment-existing').show();
			$('#payment-new').hide();
		}
	});	
	
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