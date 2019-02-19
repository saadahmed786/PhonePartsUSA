<?php $config = $this->config->get('quickcheckout'); ?>
<div class="box box-border">
<!-- Quick Checkout quickcheckout/guest_shipping.tpl -->
  <div class="box-heading"><?php echo $entry_address_1; ?></div>
  <div class="box-content">
  	<div id="shipping_firstname_input" class="sort-item <?php if(!$config['guest_shipping_firstname_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_firstname_input']) ? $config['sort_shipping_firstname_input'] : '0'); ?>">
  	<label for="shipping_firstname"><span class="required <?php if(!$config['guest_shipping_firstname_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_firstname; ?></label>
   	<input type="text" name="firstname" id="shipping_firstname" value="<?php echo $firstname; ?>" class="large-field" />
    </div>
    <div id="shipping_lastname_input" class="sort-item <?php if(!$config['guest_shipping_lastname_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_lastname_input']) ? $config['sort_shipping_lastname_input'] : '0'); ?>">
  	<label for="shipping_lastname"><span class="required <?php if(!$config['guest_shipping_lastname_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_lastname; ?></label>
    <input type="text" name="lastname" id="shipping_lastname" value="<?php echo $lastname; ?>" class="large-field" />
    </div>
    <div id="shipping_company_input" class="sort-item <?php if(!$config['guest_shipping_company_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_company_input']) ? $config['sort_shipping_company_input'] : '0'); ?>">
    <label for="shipping_company"><?php echo $entry_company; ?></label>
    <input type="text" name="company" id="shipping_company" value="<?php echo $company; ?>" class="large-field" />
    </div>
    <div id="shipping_address_1_input" class="sort-item <?php if(!$config['guest_shipping_address_1_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_address_1_input']) ? $config['sort_shipping_address_1_input'] : '0'); ?>">    
    <label for="shipping_address_1"><span class="required <?php if(!$config['guest_shipping_address_1_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_address_1; ?></label>
    <input type="text" name="address_1"  id="shipping_address_1" value="<?php echo $address_1; ?>" class="large-field" />
    </div>
    <div id="shipping_address_2_input" class="sort-item <?php if(!$config['guest_shipping_address_2_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_address_2_input']) ? $config['sort_shipping_address_2_input'] : '0'); ?>">    
    <label for="shipping_address_2"><?php echo $entry_address_2; ?></label>
    <input type="text" name="address_2" id="shipping_address_2" value="<?php echo $address_2; ?>" class="large-field" />
    </div>
    <div id="shipping_city_input" class="sort-item <?php if(!$config['guest_shipping_city_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_city_input']) ? $config['sort_shipping_city_input'] : '0'); ?>">    
    <label for="shipping_city"><span class="required <?php if(!$config['guest_shipping_city_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_city; ?></label>
    <input type="text" name="city" id="shipping_city" value="<?php echo $city; ?>" class="large-field" />
    </div>
    <div id="shipping_postcode_input" class="sort-item <?php if(!$config['guest_shipping_postcode_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_postcode_input']) ? $config['sort_shipping_postcode_input'] : '0'); ?>">
    <label for="shipping_postcode"><?php if($config['guest_shipping_postcode_require']){ echo '<span id="shipping-postcode-required" class="required">*</span>'; } ?> <?php echo $entry_postcode; ?></label>
    <input type="text" name="postcode" id="shipping_postcode" value="<?php echo $postcode; ?>" class="large-field" />
    </div>
    <div id="shipping_country_id_input" class="sort-item <?php if(!$config['guest_shipping_country_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_country_input']) ? $config['sort_shipping_country_input'] : '0'); ?>">
    <label for="shipping_country_id"><span class="required <?php if(!$config['guest_shipping_country_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_country; ?></label>
    <select name="country_id" id="shipping_country_id" class="large-field">
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
      <div id="shipping_zone_id_input" class="sort-item <?php if(!$config['guest_shipping_zone_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_shipping_zone_input']) ? $config['sort_shipping_zone_input'] : '0'); ?>">
    <label for="shipping_zone_id"><span class="required <?php if(!$config['guest_shipping_zone_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_zone; ?></label>
    <select name="zone_id"  id="shipping_zone_id" class="large-field">
      </select>
      </div>
</div>
</div>
<script type="text/javascript"><!--
$('#shipping-address select[name=\'country_id\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
		},
		complete: function() {
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