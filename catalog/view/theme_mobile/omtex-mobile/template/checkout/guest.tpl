<div id="checkout-guest">
	<div class="inner_container">
		<div class="shipping_guest">
			<div class="personal_detail">
				<h2>Personal Details</h2>
				<p>
					<label>First Name:</label>
					<input type="text" name="firstname" value="" class="" />
					<span class="req">*</span>
				</p>
				<p>
					<label>Last Name:</label>
					<input type="text" name="lastname" value="" class="" />
					<span class="req">*</span>
				</p>
				<p>
					<label>Email:</label>
					<input type="text" name="email" value="" class="" />
					<span class="req">*</span>
				</p>
				<p>
					<label>Phone:</label>
					<input type="text" name="telephone" value="" class="" />
					<span class="req">*</span>
					<input type="hidden" name="fax" value="" class="" />
				</p>
			</div>
			<div class="billing_address">
				<h2>Billing Address</h2>
				<input type="hidden" name="company" value="" class="large-field" /><br />

				<div style="display: <?php echo (count($customer_groups) > 1 ? 'block' : 'none'); ?>;"><?php echo $entry_account; ?><br />
					<select name="customer_group_id" class="large-field">
					  <?php foreach ($customer_groups as $customer_group) { ?>
					  <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
					  <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
					  <?php } else { ?>
					  <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
					  <?php } ?>

					  <?php } ?>
					</select>
					<br />
					<br />
				  </div>
				  <div id="company-id-display"> <?php echo $entry_company_id; ?><span id="company-id-required" class="required">*</span><br />
					<input type="text" name="company_id" value="<?php echo $company_id; ?>" class="large-field" />
					<br />
					<br />
				  </div>
				  <div id="tax-id-display"> <?php echo $entry_tax_id; ?><span id="tax-id-required" class="required">*</span><br />
					<input type="text" name="tax_id" value="<?php echo $tax_id; ?>" class="large-field" />
					<br />
					<br />
				  </div>
				<p>
					<label>Address:</label>
					<input type="text" name="address_1" value="" class="" />
					<span class="req">*</span>
				</p>
				<p>
					<label>Apt/Suite:</label>
					<input type="text" name="address_2" value="" class="" />
				</p>
				<p>
					<label>City:</label>
					<input type="text" name="city" value="" class="" />
					<span class="req">*</span>
				</p>
				<p class="state" style="width: 41px; padding-right: 5px;">
					<label>State:<span class="req_state">*</span></label>
				</p>
				<div style="width: 160px; float: left;" id="state">
					<select name="zone_id" data-native-menu="true">
					</select>
				</div>
				<p class="state" style="width:37%">
					<label>Zip:</label>
					<input type="text" name="postcode" value="" />
					<span class="req">*</span>
				</p>
				<p class="country" style="width:44%">
					<label>Country:<span class="req_state">*</span></label>
				</p>
				<div style="width: 167px; float: left;">
					<select name="country_id" class="large-field"  data-native-menu="true">
						<option value=""><?php echo $text_select; ?></option>
						<?php foreach ($countries as $country) { ?>
						<?php if ($country['country_id'] == $country_id) { ?>
						<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>

						<?php } ?>
						<?php } ?>
					</select>
				</div>

			</div>
		</div>
	</div>
<? /*
<h2><?php echo $text_your_details; ?></h2>
<?php echo $entry_firstname; ?><span class="required">*</span><br />
<input type="text" name="firstname" value="" class="large-field" /><br />
<?php echo $entry_lastname; ?><span class="required">*</span><br />
<input type="text" name="lastname" value="" class="large-field" /><br />
<?php echo $entry_email; ?><span class="required">*</span><br />
<input type="text" name="email" value="" class="large-field" /><br />
<?php echo $entry_telephone; ?><span class="required">*</span><br />
<input type="text" name="telephone" value="" class="large-field" /><br />
<!--<?php echo $entry_fax; ?><br />
<input type="text" name="fax" value="" class="large-field" /><br />-->
<input type="hidden" name="fax" value="" class="large-field" /><br />
<h2><?php echo $text_your_address; ?></h2>
<!--<?php echo $entry_company; ?><br />-->
<input type="hidden" name="company" value="" class="large-field" /><br />

<div style="display: <?php echo (count($customer_groups) > 1 ? 'block' : 'none'); ?>;"><?php echo $entry_account; ?><br />
    <select name="customer_group_id" class="large-field">
      <?php foreach ($customer_groups as $customer_group) { ?>
      <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
      <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
      <?php } else { ?>
      <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
      <?php } ?>
      <?php } ?>
    </select>
    <br />
    <br />
  </div>
  <div id="company-id-display"> <?php echo $entry_company_id; ?><span id="company-id-required" class="required">*</span><br />
    <input type="text" name="company_id" value="<?php echo $company_id; ?>" class="large-field" />
    <br />
    <br />
  </div>
  <div id="tax-id-display"> <?php echo $entry_tax_id; ?><span id="tax-id-required" class="required">*</span><br />
    <input type="text" name="tax_id" value="<?php echo $tax_id; ?>" class="large-field" />
    <br />
    <br />
  </div>
  
<?php echo $entry_address_1; ?><span class="required">*</span><br />
<input type="text" name="address_1" value="" class="large-field" /><br />
<?php echo $entry_address_2; ?><br />
<input type="text" name="address_2" value="" class="large-field" /><br />
<?php echo $entry_city; ?><span class="required">*</span><br />
<input type="text" name="city" value="" class="large-field" /><br />
<?php echo $entry_zone; ?><span class="required">*</span>

<select name="zone_id" class="large-field" data-native-menu="true">
<option value=""><?php echo $text_select; ?></option>
</select><br />

<?php echo $entry_postcode; ?><span id="payment-postcode-required"  class="required">*</span><br />
<input type="text" name="postcode" value="" class="large-field" /><br />
<?php echo $entry_country; ?><span class="required">*</span>
<select name="country_id" class="large-field"  data-native-menu="true">
    <option value=""><?php echo $text_select; ?></option>
    <?php foreach ($countries as $country) { ?>
    <?php if ($country['country_id'] == $country_id) { ?>
    <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
    <?php } else { ?>
    <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
    <?php } ?>
    <?php } ?>
</select><br />
*/ ?>
<!--<?php echo $entry_shipping; ?>-->
<?php if ($shipping_required) { ?>
  <?php if ($shipping_address) { ?>
  <input type="checkbox" name="shipping_address" value="1" id="shipping" checked="checked" />
  <?php } else { ?>
  <input type="checkbox" name="shipping_address" value="1" id="shipping" />
  <?php } ?>
  <label for="shipping"><?php echo $entry_shipping; ?></label>
<?php } ?>
<input type="button" value="<?php echo $button_continue; ?>" id="button-guest" class="button" data-theme="a" />


</div>
<script type="text/javascript"><!--
$('#payment-address select[name=\'customer_group_id\']').live('change', function() {
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
//--></script>  
<script type="text/javascript"><!--
$('#payment-address select[name=\'country_id\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('#payment-address select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
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
			
			html = '<option value=""><?php echo "Select"; ?></option>';
			
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
$('#checkout-guest').page();

$('#state span.ui-btn-text').html('<span>Select</span>');

$('input:text').focus(
function(){
    $(this).css({'border' : '2px solid rgba(81, 203, 238, 1)'});
});
$('input:text').focusout(
function(){
    $(this).css({'border' : '2px solid #000'});
});
//--></script> 
