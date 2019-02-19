<div class="box box-border" id="qc_guest_profile">
  <!-- Quick Checkout quickcheckout/guest.tpl -->
  <?php
	$config = $this->config->get('quickcheckout');
?>
  <div class="box-heading"><?php echo $text_your_details; ?></div>
  <div class="box-content">
    <div id="firstname_input" class="sort-item <?php if(!$config['guest_firstname_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_firstname_input']) ? $config['sort_firstname_input'] : '0'); ?>">
      <label for="firstname"><span class="required <?php if(!$config['guest_firstname_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_firstname; ?></label>
      <input type="text" name="firstname" id="firstname"  value="<?php echo $firstname; ?>" class="large-field" />
    </div>
    <div id="lastname_input" class="sort-item <?php if(!$config['guest_lastname_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_lastname_input']) ? $config['sort_lastname_input'] : '0'); ?>">
      <label for="lastname"><span class="required <?php if(!$config['guest_lastname_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_lastname; ?></label>
      <input type="text" name="lastname" id="lastname"  value="<?php echo $lastname; ?>" class="large-field" />
    </div>
    <div id="email_input" class="sort-item <?php if(!$config['guest_email_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_email_input']) ? $config['sort_email_input'] : '0'); ?>">
      <label for="email"><span class="required <?php if(!$config['guest_email_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_email; ?></label>
      <input type="text" name="email" id="email"  value="<?php echo $email; ?>" class="large-field" />
    </div>
    <div id="telephone_input" class="sort-item <?php if(!$config['guest_telephone_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_telephone_input']) ? $config['sort_telephone_input'] : '0'); ?>">
      <label for="telephone"><span class="required <?php if(!$config['guest_telephone_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_telephone; ?></label>
      <input type="text" name="telephone" id="telephone"  value="<?php echo $telephone; ?>" class="large-field" />
    </div>
    <div id="fax_input" class="sort-item <?php if(!$config['guest_fax_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_fax_input']) ? $config['sort_fax_input'] : '0'); ?>">
      <label for="fax" ><?php echo $entry_fax; ?></label>
      <input type="text" name="fax" id="fax" value="<?php echo $fax; ?>" class="large-field" />
    </div>
  </div>
</div>
<div class="box box-border <?php if(!$config['guest_payment_address_display']){ echo 'hide'; } ?>" id="qc_guest_address">
  <div class="box-heading"><?php echo $text_your_address; ?></div>
  <div class="box-content">
    <div id="company_input" class="sort-item <?php if(!$config['guest_company_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_company_input']) ? $config['sort_company_input'] : '0'); ?>">
      <label for="company"><?php echo $entry_company; ?></label>
      <input type="text" name="company" id="company" value="<?php echo $company; ?>" class="large-field" />
    </div>    
      <div id="customer_group_input" class="sort-item customer-group <?php echo (count($customer_groups) > 1 ? '' : 'hide'); ?> <?php if(!$config['guest_customer_group_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_customer_input']) ? $config['sort_customer_input'] : '0'); ?>">
        <label><?php echo $entry_customer_group; ?></label>
        <ul>
        <?php foreach ($customer_groups as $customer_group) { ?>
        <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
        <li>
          <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer_group_id<?php echo $customer_group['customer_group_id']; ?>" checked="checked" />
          <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></label>
        </li>
        <?php } else { ?>
        <li>
          <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer_group_id<?php echo $customer_group['customer_group_id']; ?>" />
          <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></label>
        </li>
        <?php } ?>
        <?php } ?>
        </ul>
    </div>
    <div id="company_id_input" class="sort-item" sort-data="<?php echo (isset($config['sort_company_id_input']) ? $config['sort_company_id_input'] : '0'); ?>">
      <div class="<?php if(!$config['guest_company_id_display']){ echo 'hide'; } ?>">      
        <label for="company_id"><span id="company_id_required" class="required"><span class="required <?php if(!$config['guest_company_id_require']){ echo 'hide'; } ?>">* </span></span><?php echo $entry_company_id; ?></label>
        <input type="text" name="company_id" id="company_id" value="<?php echo $company_id; ?>" class="large-field" />
      </div>
    </div>
    <div id="tax_id_input" class="sort-item" sort-data="<?php echo (isset($config['sort_tax_id_input']) ? $config['sort_tax_id_input'] : '0'); ?>">
      <div class="<?php if(!$config['guest_tax_id_display']){ echo 'hide'; } ?>">
        <label for="tax_id"><span id="tax_id_required" class="required"><span class="required <?php if(!$config['guest_tax_id_require']){ echo 'hide'; } ?>">* </span></span><?php echo $entry_tax_id; ?></label>
        <input type="text" name="tax_id"  id="tax_id" value="<?php echo $tax_id; ?>" class="large-field" />
      </div>
    </div>
    <div id="address_1_input" class="sort-item <?php if(!$config['guest_address_1_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_address_1_input']) ? $config['sort_address_1_input'] : '0'); ?>">
      <label for="address_1"><span class="required <?php if(!$config['guest_address_1_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_address_1; ?></label>
      <input type="text" name="address_1" id="address_1" value="<?php echo $address_1; ?>" class="large-field" />
    </div>
    <div id="address_2_input" class="sort-item <?php if(!$config['guest_address_2_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_address_2_input']) ? $config['sort_address_2_input'] : '0'); ?>">
      <label for="address_2"><?php echo $entry_address_2; ?></label>
      <input type="text" name="address_2" id="address_2" value="<?php echo $address_2; ?>" class="large-field" />
    </div>
    <div id="city_input" class="sort-item <?php if(!$config['guest_city_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_city_input']) ? $config['sort_city_input'] : '0'); ?>">
      <label for="city"><span class="required <?php if(!$config['guest_city_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_city; ?></label>
      <input type="text" name="city" id="city" value="<?php echo $city; ?>" class="large-field" />
    </div>
    <div id="postcode_input" class="sort-item <?php if(!$config['guest_postcode_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_postcode_input']) ? $config['sort_postcode_input'] : '0'); ?>">
      <label for="postcode">
        <?php if($config['guest_postcode_require']){ echo '<span id="payment-postcode-required" class="required">*</span> '; } ?>
        <?php echo $entry_postcode; ?></label>
      <input type="text" name="postcode" id="postcode" value="<?php echo $postcode; ?>" class="large-field" />
    </div>
    <div id="country_id_input" class="sort-item <?php if(!$config['guest_country_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_country_input']) ? $config['sort_country_input'] : '0'); ?>">
      <label for="country_id"><span class="required <?php if(!$config['guest_country_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_country; ?></label>
      <select name="country_id" class="large-field" id="country_id">
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
    <div id="zone_id_input" class="sort-item <?php if(!$config['guest_zone_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_zone_input']) ? $config['sort_zone_input'] : '0'); ?>">
      <label for="zone_id"><span class="required <?php if(!$config['guest_zone_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_zone; ?></label>
      <select name="zone_id" id="zone_id" class="large-field">
      </select>
    </div>
  </div>
</div>
<?php if ($shipping_required) { ?>
<div class="clear"></div>
<div class="<?php if(!$config['guest_shipping_address_enable']){ echo 'hide'; } ?>">
  <?php if ($shipping_address && !$config['guest_shipping_address_display']) { ?>
  <input type="checkbox" name="shipping_address" value="1" id="shipping" checked="checked" class="styled" />
  <?php } else { ?>
  <input type="checkbox" name="shipping_address" value="1" id="shipping" class="styled"  />
  <?php } ?>
  <label for="shipping"><?php echo $entry_shipping; ?></label>
</div>
</div>
<?php } ?>
<script type="text/javascript"><!--
$('#payment-address input[name=\'customer_group_id\']:checked').live('change', function() {
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
			$('#company_id_input').show();
		} else {
			$('#company_id_input').hide();
		}
		
		if (customer_group[this.value]['company_id_required'] == '1') {
			$('#company_id_required').show();
		} else {
			$('#company_id_required').hide();
		}
		
		if (customer_group[this.value]['tax_id_display'] == '1') {
			$('#tax_id_input').show();
		} else {
			$('#tax_id_input').hide();
		}
		
		if (customer_group[this.value]['tax_id_required'] == '1') {
			$('#tax_id_required').show();
		} else {
			$('#tax_id_required').hide();
		}	
	}
});

$('#payment-address input[name=\'customer_group_id\']:checked').trigger('change');
//--></script>
<script type="text/javascript"><!--
$('#payment-address select[name=\'country_id\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
		},
		complete: function() {
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