<style type="text/css">
#checkout-payment_address .ui-select .ui-btn-text, .ui-selectmenu .ui-btn-text  {
    display: block;
}
</style>
<div id="checkout-payment_address" class="form">
<?php if ($addresses) { ?>
<input type="radio" name="payment_address" value="existing" id="payment-address-existing" checked="checked" />
<label for="payment-address-existing"><?php echo $text_address_existing; ?></label>
<div id="payment-existing">
  <select name="address_id" style="width: 100%; margin-bottom: 15px;" size="5">
    <?php foreach ($addresses as $address) { ?>
    <?php if ($address['address_id'] == $address_id) { ?>
    <option value="<?php echo $address['address_id']; ?>" selected="selected"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
    <?php } else { ?>
    <option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
    <?php } ?>
    <?php } ?>
  </select>
</div>
<p>
  <input type="radio" name="payment_address" value="new" id="payment-address-new" />
  <label for="payment-address-new"><?php echo $text_address_new; ?></label>
</p>
<?php } ?>
<div id="payment-new" style="display: <?php echo ($addresses ? 'none' : 'block'); ?>;">

		<div class="shipping_guest">
			<div class="billing_address">
				<h2>Billing Details</h2>
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
				<!--<p>
					<label>Email:</label>
					<input type="text" name="email" value="" class="" />
					<span class="req">*</span>
				</p>
				<p>
					<label>Phone:</label>
					<input type="text" name="telephone" value="" class="" />
					<span class="req">*</span>
					<input type="hidden" name="fax" value="" class="" />
				</p>-->
			</div>
			<div class="billing_address">
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
				  <div id="company-id-display"><!-- <?php echo $entry_company_id; ?><span id="company-id-required" class="required">*</span><br />-->
					<input type="hidden" name="company_id" value="<?php echo $company_id; ?>" class="large-field" />
					<br />
					<br />
				  </div>
				  <div id="tax-id-display"><!-- <?php echo $entry_tax_id; ?><span id="tax-id-required" class="required">*</span><br />-->
					<input type="hidden" name="tax_id" value="<?php echo $tax_id; ?>" class="large-field" />
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
					<option value=""><?php echo $text_select; ?></option>
					</select>
					<span class="req">*</span>
				</div>
				<p class="state" style="width:37%">
					<label>Zip:</label>
					<input type="text" name="postcode" value="" />
					<span class="req">*</span>
				</p>
			</div>
		</div>

	<p class="country" style="width:40%; float: left;">
		<label>Country:<span class="req_state">*</span></label>
	</p>
	<div style="width: 198px; float: left;">
	<select name="country_id" class="large-field" data-native-menu="true" style="">
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
<? /*
<span class="required">*</span> <?php echo $entry_firstname; ?><br/>
<input type="text" name="firstname" value="" class="large-field" /><br/>
<span class="required">*</span> <?php echo $entry_lastname; ?><br/>
<input type="text" name="lastname" value="" class="large-field" /><br/>
<?php echo $entry_company; ?><br/>
<input type="text" name="company" value="" class="large-field" /><br/>

<div style="display: <?php echo ($company_id_display ? 'table-row' : 'none'); ?>;">
      <span style="display: <?php echo ($company_id_required ? 'table-row' : 'none'); ?>;" class="required">*</span><?php echo $entry_company_id; ?> <br />
      <input type="text" name="company_id" value="" class="large-field" />
    </div>
    
    <div style="display: <?php echo ($tax_id_display ? 'table-row' : 'none'); ?>;">
      <span style="display: <?php echo ($tax_id_required ? 'table-row' : 'none'); ?>;" class="required">*</span><?php echo $entry_tax_id; ?> <br />
      <input type="text" name="tax_id" value="" class="large-field" />
    </div>
    
<span class="required">*</span> <?php echo $entry_address_1; ?><br/>
<input type="text" name="address_1" value="" class="large-field" /><br/>
<?php echo $entry_address_2; ?><br/>
<input type="text" name="address_2" value="" class="large-field" /><br/>
<span class="required">*</span> <?php echo $entry_city; ?><br/>
<input type="text" name="city" value="" class="large-field" /><br/>

<span class="required">*</span> <?php echo $entry_zone; ?>
<select name="zone_id" class="large-field" data-native-menu="true">
    <option value=""><?php echo $text_select; ?></option>
    </select><br />
<span class="required" id="payment-postcode-required">*</span> <?php echo $entry_postcode; ?><br/>
<input type="text" name="postcode" value="" class="large-field" /><br/>
<span class="required">*</span> <?php echo $entry_country; ?><br/>
<select name="country_id" class="large-field" data-native-menu="true">
          <option value=""><?php echo $text_select; ?></option>
          <?php foreach ($countries as $country) { ?>
          <?php if ($country['country_id'] == $country_id) { ?>
          <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
          <?php } else { ?>
          <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
          <?php } ?>
          <?php } ?>
        </select>
*/ ?>
</div>


<div class="buttons" style="clear: both;">
  <div class="right"><input type="button" value="<?php echo $button_continue; ?>" id="button-payment-address" class="button" data-theme="a" /></div>
</div>
</div>
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
<script type="text/javascript"><!--
$('#checkout-payment_address').page();
$('#state span.ui-btn-text').html('<span>Select</span>');

$('#payment-address input[name=\'payment_address\']').live('change', function() {
//	alert(this.value);
	if (this.value == 'new') {
		$('#payment-existing').hide();
		$('#payment-new').show();
	} else {
		$('#payment-existing').show();
		$('#payment-new').hide();
	}
});

$('input:text').focus(
function(){
    $(this).css({'border' : '2px solid rgba(81, 203, 238, 1)'});
});
$('input:text').focusout(
function(){
    $(this).css({'border' : '2px solid #000'});
});
//--></script> 
