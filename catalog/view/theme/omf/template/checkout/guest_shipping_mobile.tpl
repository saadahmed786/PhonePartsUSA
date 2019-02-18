	<form action="index.php?route=checkout/guest_shipping/validate" method="post">
		<fieldset>
			<h2><?php echo $text_your_details; ?></h2>
			<ul>
				<li>
					<label for="firstname"><span class="required">*</span> <?php echo $entry_firstname; ?></label>
					<input type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>"  />					
				</li>				
				<li>
					<label for="lastname"><span class="required">*</span> <?php echo $entry_lastname; ?></label>
					<input type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>"  />					
				</li>
			</ul>
		</fieldset>
		<fieldset>
			<h2><?php echo $text_your_address; ?></h2> 			
			<input type="hidden" id="company" name="company" value="<?php echo $company; ?>"  />
			<ul>
				<li>
					<label for="address_1"><span class="required">*</span> <?php echo $entry_address_1; ?></label>
					<input type="text" id="address_1" name="address_1" value="<?php echo $address_1; ?>"  />					
				</li>
				<li>
					<label for="address_2"><?php echo $entry_address_2; ?></label>
					<input type="text" id="address_2" name="address_2" value="<?php echo $address_2; ?>"  />					
				</li>
				<li>
					<label for="city"><span class="required">*</span> <?php echo $entry_city; ?></label>
					<input type="text" id="city" name="city" value="<?php echo $city; ?>"  />					
				</li>
				<li>
					<label for="postcode"><span class="required">*</span> <?php echo $entry_postcode; ?></label>
					<input type="text" id="postcode" name="postcode" value="<?php echo $postcode; ?>"  />				
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
					<select id="zone_id" name="zone_id">
					</select>										
				</li>
			</ul>
		</fieldset>
		<input type="submit" value="<?php echo $button_continue; ?>" id="button-guest-shipping"/>
	</form>	
	
<script type="text/javascript"><!--
$('#shipping-address select[name=\'country_id\']').bind('change', function() {	
	$.ajax({
		url: 'index.php?route=checkout/checkout/country&country_id=' + $('#shipping-address select[name=\'country_id\']').val(),
		dataType: 'json',
		beforeSend: function() {
			$('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>').insertAfter($('#shipping-address select[name=\'country_id\']'));
		},
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#shipping-postcode-required').show();
			} else {
				$('#shipping-postcode-required').hide();
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
			
			$('#shipping-address select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#shipping-address select[name=\'country_id\']').trigger('change');
//--></script>	