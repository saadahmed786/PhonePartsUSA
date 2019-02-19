<style type="text/css">
#checkout-shipping_address .ui-select .ui-btn-text, .ui-selectmenu .ui-btn-text  {
    display: block;
}

</style>
<div id="checkout-shipping_address" class="form">
<?php if ($addresses) { ?>
<input type="radio" name="shipping_address" value="existing" id="shipping-address-existing" checked="checked" />
<label for="shipping-address-existing"><?php echo $text_address_existing; ?></label>
<div id="shipping-existing">
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
<?php } ?>
<p>
  <input type="radio" name="shipping_address" value="new" id="shipping-address-new" />
  <label for="shipping-address-new"><?php echo $text_address_new; ?></label>
</p>
<div id="shipping-new" style="display: none;" style="margin-top: -20px;">

		<div class="shipping_guest">
			<div class="ship_add">
				<h2>Shipping Address</h2>
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
			</div>
			<div class="billing_address">
				<input type="hidden" name="company" value="" class="large-field" />
				<p>
					<label>Address:</label>
					<input type="text" name="address_1" value="" class="" />
					<span class="req">*</span>
				</p>
				<p>
					<label>Apt/Suite:</label>
					<input type="text" name="address_2" value="" class="" />
					<span class="req">*</span>
				</p>
				<p>
					<label>City:</label>
					<input type="text" name="city" value="" class="" />
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
				<!--<p class="country" style="width:44%">
					<label>Country:</label>
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
				</div>-->

			</div>
		</div>
<? /*
<span class="required">*</span> <?php echo $entry_firstname; ?><br/>
<input type="text" name="firstname" value="" class="large-field" /><br/>
<span class="required">*</span> <?php echo $entry_lastname; ?><br/>
<input type="text" name="lastname" value="" class="large-field" /><br/>
<!--<?php echo $entry_company; ?><br/>-->
<input type="hidden" name="company" value="" class="large-field" /><br/>
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

<span class="required" id="shipping-postcode-required" >*</span> <?php echo $entry_postcode; ?><br/>
<input type="text" name="postcode" value="" class="large-field" /><br/>
<span class="required">*</span> <?php echo $entry_country; ?><br/>
*/ ?>
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
</div>

<div class="buttons" style="clear: both;">
  <div class="right"><input type="button" value="<?php echo $button_continue; ?>" id="button-shipping-address" class="button" data-theme="a" /></div>
</div>
</div>
<script type="text/javascript"><!--
$('#checkout-shipping_address').page();

$('#shipping-address input[name=\'shipping_address\']').live('change', function() {
	if (this.value == 'new') {
		$('#shipping-existing').hide();
		$('#shipping-new').show();
	} else {
		$('#shipping-existing').show();
		$('#shipping-new').hide();
	}
});
//--></script> 
<script type="text/javascript"><!--
$('#shipping-address select[name=\'country_id\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('#shipping-address select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
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
			
			$('#shipping-address select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#shipping-address select[name=\'country_id\']').trigger('change');
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
