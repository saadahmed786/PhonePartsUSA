<?php $config = $this->config->get('quickcheckout'); ?>
<!-- Quick Checkout quickcheckout/payment_address.tpl -->
<input type="hidden" value="0" id="registered"/>
<?php if ($addresses) { ?>
<div id="payment_address_existing_input">
    <input type="radio" name="payment_address" value="existing" id="payment-address-existing" checked="checked" class="styled" />
    <label for="payment-address-existing"><?php echo $text_address_existing; ?></label>
</div>
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
<div id="payment_address_new_input">
  <input type="radio" name="payment_address" value="new" id="payment-address-new" class="styled"  />
  <label for="payment-address-new"><?php echo $text_address_new; ?></label>
</div>
<?php } ?>
<div id="payment-new" style="display: <?php echo ($addresses ? 'none' : 'block'); ?>;">
  <div class="box box-border">
    <div class="box-heading"><?php echo $entry_address_1; ?></div>
    <div class="box-content">
      <div id="firstname_input" class="sort-item <?php if(!$config['register_firstname_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_firstname_input']) ? $config['sort_firstname_input'] : '0'); ?>">
        <label for="firstname"><span class="required <?php if(!$config['register_firstname_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_firstname; ?></label>
        <input type="text" name="firstname" id="firstname" value="" class="large-field" />
      </div>
      <div id="lastname_input" class="sort-item <?php if(!$config['register_lastname_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_lastname_input']) ? $config['sort_lastname_input'] : '0'); ?>">
        <label for="lastname"><span class="required <?php if(!$config['register_lastname_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_lastname; ?></label>
        <input type="text" name="lastname" id="lastname" value="" class="large-field" />
      </div>
      <div class="sort-item <?php if(!$config['register_company_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_company_input']) ? $config['sort_company_input'] : '0'); ?>">
        <label for="company"><?php echo $entry_company; ?></label>
        <input type="text" name="company"  id="company" value="" class="large-field" />
      </div>
      <div id="company_id_input" class="sort-item <?php if(!$config['register_company_id_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_company_id_input']) ? $config['sort_company_id_input'] : '0'); ?>">
      <?php if ($company_id_display) { ?>
      <label for="company_id">
        <?php if ($company_id_required) { ?>
        <span class="required <?php if(!$config['register_company_id_require']){ echo 'hide'; } ?>">*</span>
        <?php } ?>
        <?php echo $entry_company_id; ?></label>
      <input type="text" name="company_id"  id="company_id" value="" class="large-field" />
      <?php } ?>
      </div>
      <div id="tax_id_input" class="sort-item <?php if(!$config['register_tax_id_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_tax_id_input']) ? $config['sort_tax_id_input'] : '0'); ?>">
      <?php if ($tax_id_display) { ?>
      <label for="tax_id">
        <?php if ($tax_id_required) { ?>
        <span class="required <?php if(!$config['register_tax_id_require']){ echo 'hide'; } ?>">*</span>
        <?php } ?>
        <?php echo $entry_tax_id; ?></label>
      <input type="text" name="tax_id"  id="tax_id" value="" class="large-field" />
      <?php } ?>
      </div>
      <div id="address_1_input" class="sort-item <?php if(!$config['register_address_1_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_address_1_input']) ? $config['sort_address_1_input'] : '0'); ?>">
        <label for="address_1"><span class="required <?php if(!$config['register_address_1_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_address_1; ?></label>
        <input type="text" name="address_1"  id="address_1" value="" class="large-field" />
      </div>
      <div id="address_2_input" class="sort-item <?php if(!$config['register_address_2_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_address_2_input']) ? $config['sort_address_2_input'] : '0'); ?>">
        <label for="address_2"><?php echo $entry_address_2; ?></label>
        <input type="text" name="address_2" id="address_2"  value="" class="large-field" />
      </div>
      <div id="city_input" class="sort-item <?php if(!$config['register_city_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_city_input']) ? $config['sort_city_input'] : '0'); ?>">
        <label for="city"><span class="required <?php if(!$config['register_city_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_city; ?></label>
        <input type="text" name="city"  id="city" value="" class="large-field" />
      </div>
      <div id="postcode_input" class="sort-item <?php if(!$config['register_postcode_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_postcode_input']) ? $config['sort_postcode_input'] : '0'); ?>">
        <label for="postcode">
          <?php if($config['register_postcode_require']){ echo '<span id="payment-postcode-required" class="required">*</span> '; } ?>
          <?php echo $entry_postcode; ?></label>
        <input type="text" name="postcode"  id="postcode" value="" class="large-field" />
      </div>
      <div id="country_id_input" class="sort-item <?php if(!$config['register_country_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_country_input']) ? $config['sort_country_input'] : '0'); ?>">
        <label for="country_id"><span class="required <?php if(!$config['register_country_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_country; ?></label>
        <select name="country_id"  id="country_id" class="large-field">
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
      <div id="zone_id_input" class="sort-item <?php if(!$config['register_zone_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_zone_input']) ? $config['sort_zone_input'] : '0'); ?>">
        <label for="zone_id"><span class="required <?php if(!$config['register_zone_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_zone; ?></label>
        <select name="zone_id"  id="zone_id" class="large-field">
        </select>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>
<div class="clear"></div>
<script type="text/javascript"><!--
$('#payment-address input[name=\'payment_address\']').live('click', function() {
	if (this.value == 'new') {
		$('#payment-existing').hide();
		$('#payment-new').show();
	} else {
		$('#payment-existing').show();
		$('#payment-new').hide();
	}
});
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
$('.sort-item').tsort({attr:'sort-data'});
//--></script>
<script><!--
$(function(){

		if($.isFunction($.fn.uniform)){
        $(" .styled, input:radio.styled").uniform().removeClass('styled');
		}
      });
//--></script>