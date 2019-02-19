<?php $config = $this->config->get('quickcheckout'); ?>
<!-- Quick Checkout quickcheckout/register.tpl -->

<div class="box box-border">
  <div class="box-heading"><i class="icon-profile"></i> <?php echo $text_your_details; ?></div>
  <div class="box-content">
  <input type="hidden" value="0" id="registered"/>
    <div id="firstname_input" class="sort-item <?php if(!$config['register_firstname_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_firstname_input']) ? $config['sort_firstname_input'] : '0'); ?>">
      <label for="firstname"><span class="required <?php if(!$config['register_firstname_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_firstname; ?></label>
      <input type="text" name="firstname" id="firstname" value="" class="large-field" />
    </div>
    <div id="lastname_input" class="sort-item <?php if(!$config['register_lastname_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_lastname_input']) ? $config['sort_lastname_input'] : '0'); ?>">
      <label for="lastname"><span class="required <?php if(!$config['register_lastname_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_lastname; ?></label>
      <input type="text" name="lastname" id="lastname" value="" class="large-field" />
    </div>
    <div id="email_input" class="sort-item <?php if(!$config['register_email_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_email_input']) ? $config['sort_email_input'] : '0'); ?>">
      <label for="email"><span class="required <?php if(!$config['register_email_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_email; ?></label>
      <input type="text" name="email" id="email" value="" class="large-field" />
    </div>
    <div id="telephone_input" class="sort-item <?php if(!$config['register_telephone_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_telephone_input']) ? $config['sort_telephone_input'] : '0'); ?>">
      <label for="telephone"><span class="required <?php if(!$config['register_telephone_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_telephone; ?></label>
      <input type="text" name="telephone" id="telephone" value="" class="large-field" />
    </div>
    <div id="fax_input" class="sort-item <?php if(!$config['register_fax_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_fax_input']) ? $config['sort_fax_input'] : '0'); ?>">
      <label for="fax"><?php echo $entry_fax; ?></label>
      <input type="text" name="fax" id="fax" value="" class="large-field" />
    </div>
    <div id="password_group_input" class="sort-item <?php if(!$config['register_password_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_password_group_input']) ? $config['sort_password_group_input'] : '0'); ?>">
      <div id="password_input">
        <label for="password"><span class="required <?php if(!$config['register_password_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_password; ?></label>
        <input type="password" name="password" id="password" value="" class="large-field" />
      </div>
      <div id="confirm_input">
        <label for="confirm"><span class="required <?php if(!$config['register_password_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_confirm; ?></label>
        <input type="password" name="confirm" id="confirm" value="" class="large-field" />
      </div>
    </div>
  </div>
</div>
<div class="box box-border  <?php if(!$config['register_payment_address_display']){ echo 'hide'; } ?>">
  <div class="box-heading"><i class="icon-address"></i> <?php echo $text_your_address; ?></div>
  <div class="box-content">
    <div id="company_input" class="sort-item <?php if(!$config['register_company_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_company_input']) ? $config['sort_company_input'] : '0'); ?>">
      <label for="company"><?php echo $entry_company; ?></label>
      <input type="text" name="company" id="company" value="" class="large-field" />
    </div>
    <div id="customer_group_input" class="sort-item customer-group <?php echo (count($customer_groups) > 1 ? '' : 'hide'); ?> <?php if(!$config['register_customer_group_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_customer_group_input']) ? $config['sort_customer_group_input'] : '0'); ?>">
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
      <div class="<?php if(!$config['register_company_id_display']){ echo 'hide'; } ?>">
        <label for="company_id"><span id="company_id_required" class="required"><span class="required <?php if(!$config['register_company_id_require']){ echo 'hide'; } ?>">*</span></span> <?php echo $entry_company_id; ?></label>
        <input type="text" name="company_id" id="company_id" value="" class="large-field" />
      </div>
    </div>
    <div id="tax_id_input" class="sort-item" sort-data="<?php echo (isset($config['sort_tax_id_input']) ? $config['sort_tax_id_input'] : '0'); ?>">
      <div class="<?php if(!$config['register_tax_id_display']){ echo 'hide'; } ?>">
        <label for="tax_id"><span id="tax_id_required" class="required"><span class="required <?php if(!$config['register_tax_id_require']){ echo 'hide'; } ?>">*</span></span> <?php echo $entry_tax_id; ?></label>
        <input type="text" name="tax_id" id="tax_id"  value="" class="large-field" />
      </div>
    </div>
    <div id="address_1_input" class="sort-item <?php if(!$config['register_address_1_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_address_1_input']) ? $config['sort_address_1_input'] : '0'); ?>">
      <label for="address_1"><span class="required <?php if(!$config['register_address_1_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_address_1; ?></label>
      <input type="text" name="address_1" id="address_1" value="" class="large-field" />
    </div>
    <div id="address_2_input" class="sort-item <?php if(!$config['register_address_2_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_address_2_input']) ? $config['sort_address_2_input'] : '0'); ?>">
      <label for="address_2"><?php echo $entry_address_2; ?></label>
      <input type="text" name="address_2"  id="address_2" value="" class="large-field" />
    </div>
    <div id="city_input" class="sort-item <?php if(!$config['register_city_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_city_input']) ? $config['sort_city_input'] : '0'); ?>">
      <label for="city"><span class="required <?php if(!$config['register_city_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_city; ?></label>
      <input type="text" name="city" id="city" value="" class="large-field" />
    </div>
    <div id="postcode_input" class="sort-item <?php if(!$config['register_postcode_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_postcode_input']) ? $config['sort_postcode_input'] : '0'); ?>">
      <label for="postcode">
        <?php if($config['register_postcode_require']){ echo '<span id="payment-postcode-required" class="required">*</span> '; } ?>
        <?php echo $entry_postcode; ?></label>
      <input type="text" name="postcode" id="postcode" value="<?php echo $postcode; ?>" class="large-field" />
    </div>
    <div id="country_id_input" class="sort-item <?php if(!$config['register_country_display']){ echo 'hide'; } ?>" sort-data="<?php echo (isset($config['sort_country_input']) ? $config['sort_country_input'] : '0'); ?>">
      <label for="country_id"><span class="required <?php if(!$config['register_country_require']){ echo 'hide'; } ?>">*</span> <?php echo $entry_country; ?></label>
      <select name="country_id" id="country_id" class="large-field">
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
      <select name="zone_id" id="zone_id" class="large-field">
      </select>
    </div>
  </div>
</div>
<div class="clear"></div>
<div id="signup_group">
<div id="newsletter_checkbox" class="<?php if(!$config['register_newsletter_display']){ echo 'hide'; } ?>">
  <input type="checkbox" name="newsletter" value="1" id="newsletter" class="styled"/>
  <label for="newsletter"><?php echo $entry_newsletter; ?></label>
</div>
<?php if ($shipping_required) { ?>
<div id="shipping_address_checkbox" class="<?php if(!$config['register_shipping_address_enable']){ echo 'hide'; } ?>">
  <?php if(!$config['register_shipping_address_display']){?>
  <input type="checkbox" name="shipping_address" value="1" id="shipping" checked="checked" class="styled"  />
  <?php }else{?>
  <input type="checkbox" name="shipping_address" value="1" id="shipping" class="styled"  />
  <?php } ?>
  <label for="shipping"><?php echo $entry_shipping; ?></label>
</div>
<?php } ?>
<?php if ($text_agree) { ?>
<div id="privacy_agree_checkbox">
  <?php if($config['register_privacy_agree_display']){?>
  <?php echo $text_agree; ?>
  <input type="checkbox" name="agree" value="1" checked="checked" class="styled" />
  <?php }else{ ?>
  <input type="hidden" name="agree" value="1" checked="checked" class="hide" />
  <?php } ?>
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
//--></script>
<script type="text/javascript"><!--
$('.colorbox').colorbox({
	width: 640,
	height: 480
});
$('.sort-item').tsort({attr:'sort-data'});
//--></script>
<script><!--
$(function(){

		if($.isFunction($.fn.uniform)){
        $(" .styled, input:radio.styled").uniform().removeClass('styled');
		}
      });
//--></script>