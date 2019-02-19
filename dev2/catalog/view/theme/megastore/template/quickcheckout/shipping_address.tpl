<?php $config = $this->config->get('quickcheckout'); ?>
<!-- Quick Checkout quickcheckout/shipping_address.tpl -->
<?php if ($addresses) { ?>

<input type="radio" name="shipping_address" value="existing" id="shipping-address-existing" checked="checked" class="styled" />
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
<div>
  <input type="radio" name="shipping_address" value="new" id="shipping-address-new" class="styled" />
  <label for="shipping-address-new"><?php echo $text_address_new; ?></label>
</div>
<?php }else{?>
<input type="radio" name="shipping_address" value="new" id="shipping-address-existing" class="hide" checked="checked" />
<?php }?>
<div id="shipping-new" style="display: <?php echo ($addresses ? 'none' : 'block'); ?>;">
  <div class="box box-border">
    <div class="box-heading"><?php echo $entry_address_1; ?></div>
    <div class="box-content">
      <div id="shipping_firstname_input" class="sort-item  <?php if(!$config['register_shipping_firstname_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_firstname_input']) ? $config['sort_shipping_firstname_input'] : '0'); ?>">
        <label class="firstname"><span class="required <?php if(!$config['register_shipping_firstname_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_firstname; ?></label>
        <input type="text" name="firstname" id="firstname" value="" class="large-field" />
      </div>
      <div id="shipping_lastname_input" class="sort-item  <?php if(!$config['register_shipping_lastname_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_lastname_input']) ? $config['sort_shipping_lastname_input'] : '0'); ?>">
        <label class="lastname"><span class="required <?php if(!$config['register_shipping_lastname_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_lastname; ?></label>
        <input type="text" name="lastname" id="lastname"  value="" class="large-field" />
      </div>
      <div id="shipping_company_input" class="sort-item  <?php if(!$config['register_shipping_company_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_company_input']) ? $config['sort_shipping_company_input'] : '0'); ?>">
        <label class="company"><?php echo $entry_company; ?></label>
        <input type="text" name="company" id="company"  value="" class="large-field" />
      </div>
      <div id="shipping_address_1_input" class="sort-item <?php if(!$config['register_shipping_address_1_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_address_1_input']) ? $config['sort_shipping_address_1_input'] : '0'); ?>">
        <label class="address_1"><span class="required <?php if(!$config['register_shipping_address_1_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_address_1; ?></label>
        <input type="text" name="address_1" id="address_1"  value="" class="large-field" />
      </div>
      <div id="shipping_address_2_input" class="sort-item <?php if(!$config['register_shipping_address_2_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_address_2_input']) ? $config['sort_shipping_address_2_input'] : '0'); ?>">
        <label class="address_2"><?php echo $entry_address_2; ?></label>
        <input type="text" name="address_2" id="address_2"  value="" class="large-field" />
      </div>
      <div id="shipping_city_input" class="sort-item <?php if(!$config['register_shipping_city_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_city_input']) ? $config['sort_shipping_city_input'] : '0'); ?>">
        <label class="city"><span class="required <?php if(!$config['register_shipping_city_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_city; ?></label>
        <input type="text" name="city" id="city"  value="" class="large-field" />
      </div>
      <div id="shipping_postcode_input" class="sort-item <?php if(!$config['register_shipping_postcode_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_postcode_input']) ? $config['sort_shipping_postcode_input'] : '0'); ?>">
        <label class="postcode">
          <?php if($config['register_shipping_postcode_require']){ echo '<span id="shipping-postcode-required" class="required">*</span> '; } ?>
          <?php echo $entry_postcode; ?></label>
        <input type="text" name="postcode" id="postcode"  value="<?php echo $postcode; ?>" class="large-field" />
      </div>
      <div id="shipping_country_id_input" class="sort-item <?php if(!$config['register_shipping_country_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_country_input']) ? $config['sort_shipping_country_input'] : '0'); ?>">
        <label class="country_id"><span class="required <?php if(!$config['register_shipping_country_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_country; ?></label>
        <select name="country_id"  id="country_id"  class="large-field">
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
      <div id="shipping_zone_id_input" class="sort-item <?php if(!$config['register_shipping_zone_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_zone_input']) ? $config['sort_shipping_zone_input'] : '0'); ?>">
        <label class="zone_id"><span class="required <?php if(!$config['register_shipping_zone_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_zone; ?></label>
        <select name="zone_id" id="zone_id" class="large-field">
        </select>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>

<script type="text/javascript"><!--
$('#shipping-address input[name=\'shipping_address\']').live('click', function() {
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
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#shipping-address select[name=\'country_id\']').trigger('change');
$('.sort-item').tsort({attr:'sort-data'});
//--></script>
<script><!--
$(function(){

		if($.isFunction($.fn.uniform)){
        $(" .styled, input:radio.styled").uniform().removeClass('styled');
		}
      });
//--></script>