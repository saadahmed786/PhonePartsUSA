<?php if ($this->config->get('uber_checkout_style') == 'popup') { ?>
<?php $find = array('"header"','"menu"'); ?>
<?php $repl = array('"header" style="display:none;"','"menu" style="display:none;"'); ?>
<?php echo str_replace($find, $repl, $header); ?>
<?php $content_top = ''; ?>
<?php $footer = ''; ?>
<?php $breadcrumbs = array(); ?>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ucstyle.css" />
<?php } else { ?>
<?php echo $header; ?>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ucstyle.css" />
<?php echo $column_left; ?><?php echo $column_right; ?>
<?php } ?>

<?php
// Button style
// input type=button -> 1.5.3+
// <a class=button -> 1.5.2-
// <button> generic button
if (version_compare(VERSION, '1.5.1.3', '>') == true) {
$button_style = 'input';
} else {
$button_style = 'anchor';
}
?>


<!--
//-----------------------------------------
// Author: 	Qphoria@gmail.com
// Web: 	http://www.OpenCartGuru.com/
// Title: 	Uber Checkout 1.5.x
//-----------------------------------------
-->

<style type="text/css">
.login-form {
	width:100%;
	border:0px;
}
.login-form input[type=text], .login-form input[type=password], .login-form select {
	width:30%;
}
.register-form {
	width:100%;
	border:0px;
}
.register-form input[type=text], .register-form input[type=password], .register-form select {
	width:90%;
}
</style>

<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
  <div class="content">
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>

    <!-- LOGIN FORM -->
	<form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="login">
	<b style="margin-bottom: 2px; display: block;"><?php echo $text_returning_customer; ?></b></b>
      <div class="content">
        <table class="login-form">
          <tr>
	        <td width="30%"><b><?php echo $entry_email; ?></b></td>
            <td><input type="text" name="email_login" /></td>
          </tr>
          <tr>
			<td><b><?php echo $entry_password; ?></b></td>
            <td><input type="password" name="password_login" /></td>
          </tr>
          <tr>
			<td></td>
            <td><a href="<?php echo str_replace('&', '&amp;', $forgotten); ?>"><?php echo $text_forgotten_password; ?></a></td>
          </tr>
        </table>
		<div class="buttons">
          <div class="right" style="text-align:right;">
		  <?php echo $login_button_html; ?>
		  </div>
        </div>
      </div>

      <?php if ($redirect) { ?>
      <input type="hidden" name="redirect" value="<?php echo str_replace('&', '&amp;', $redirect); ?>" />
      <?php } ?>

    </form>
    <!-- LOGIN FORM -->

    <div style="text-align: center; padding: 15px 0 25px 0;"><b style="font-size:15px;"><?php echo $text_or; ?></b></div>

    <div class="left">
      <!-- BILLING FORM -->
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="register">
        <b style="margin-bottom: 2px; display: block;"><?php echo $text_billing_address; ?></b>
        <div class="content">
          <table class="register-form">
            <tr>
              <td width="30%"><span class="required">*</span> <?php echo $entry_firstname; ?></td>
              <td><input type="text" name="firstname" value="<?php echo $firstname; ?>" />
                <?php if ($error_firstname) { ?>
                <span class="error"><?php echo $error_firstname; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
              <td><input type="text" name="lastname" value="<?php echo $lastname; ?>" />
                <?php if ($error_lastname) { ?>
                <span class="error"><?php echo $error_lastname; ?></span>
                <?php } ?></td>
            </tr>
            <?php if (file_exists(DIR_SYSTEM . '../catalog/model/account/customer_group.php')) { ?>
			<tr style="display: <?php echo (count($customer_groups) > 1 ? 'table-row' : 'none'); ?>;">
	          <td><?php echo $entry_account; ?></td>
	          <td><select name="customer_group_id">
	              <?php foreach ($customer_groups as $customer_group) { ?>
	              <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
	              <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
	              <?php } else { ?>
	              <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
	              <?php } ?>
	              <?php } ?>
	            </select></td>
	        </tr>
			<?php } ?>
			<tr>
              <td><?php echo $entry_company; ?></td>
              <td><input type="text" name="company" value="<?php echo $company; ?>" /></td>
            </tr>
			<?php if (!is_null($this->config->get('config_customer_group_display'))) { //v153+ ?>
	        <tr id="company-id-display">
	          <td><span id="company-id-required" class="required">*</span> <?php echo $entry_company_id; ?></td>
	          <td><input type="text" name="company_id" value="<?php echo $company_id; ?>" />
	            <?php if ($error_company_id) { ?>
	            <span class="error"><?php echo $error_company_id; ?></span>
	            <?php } ?></td>
	        </tr>
	        <tr id="tax-id-display">
	          <td><span id="tax-id-required" class="required">*</span> <?php echo $entry_tax_id; ?></td>
	          <td><input type="text" name="tax_id" value="<?php echo $tax_id; ?>" />
	            <?php if ($error_tax_id) { ?>
	            <span class="error"><?php echo $error_tax_id; ?></span>
	            <?php } ?></td>
	        </tr>
			<?php } ?>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_address_1; ?></td>
              <td><input type="text" name="address_1" value="<?php echo $address_1; ?>" />
                <?php if ($error_address_1) { ?>
                <span class="error"><?php echo $error_address_1; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_address_2; ?></td>
              <td><input type="text" name="address_2" value="<?php echo $address_2; ?>" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_city; ?></td>
              <td><input type="text" name="city" value="<?php echo $city; ?>" />
                <?php if ($error_city) { ?>
                <span class="error"><?php echo $error_city; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td id="postcode"><span class="required">*</span> <?php echo $entry_postcode; ?></td>
              <td><input type="text" name="postcode" value="<?php echo $postcode; ?>" />
			    <?php if ($error_postcode) { ?>
                <span class="error"><?php echo $error_postcode; ?></span>
                <?php } ?>
              </td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_country; ?></td>
              <td><select name="country_id" id="country_id" onchange="$('select[name=\'zone_id\']').load('index.php?route=checkout/checkout_one/zone&country_id=' + this.value + '&zone_id=<?php echo $zone_id; ?>');">
                  <option value="FALSE"><?php echo $text_select; ?></option>
                  <?php foreach ($countries as $country) { ?>
                  <option value="<?php echo $country['country_id']; ?>" <?php if( $country['country_id'] == $country_id){?> selected="selected" <?php } ?> ><?php echo $country['name']; ?></option>
                  <?php } ?>
                </select>
                <?php if ($error_country) { ?>
                <span class="error"><?php echo $error_country; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_zone; ?></td>
              <td><select name="zone_id" >
                  <option value="FALSE"><?php echo $text_select; ?></option>
                </select>
                <?php if ($error_zone) { ?>
                <span class="error"><?php echo $error_zone; ?></span>
                <?php } ?></td>
            </tr>
			<tr>
              <td><span class="required">*</span> <?php echo $entry_telephone; ?></td>
              <td><input type="text" name="telephone" value="<?php echo $telephone; ?>" />
                <?php if ($error_telephone) { ?>
                <span class="error"><?php echo $error_telephone; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_fax; ?></td>
              <td><input type="text" name="fax" value="<?php echo $fax; ?>" /></td>
            </tr>
			<tr>
              <td><span class="required">*</span> <?php echo $entry_email; ?></td>
              <td><input type="text" name="email" value="<?php echo $email; ?>" />
                <?php if ($error_email) { ?>
                <span class="error"><?php echo $error_email; ?></span>
                <?php } ?></td>
            </tr>
          </table>
        </div>
		<!-- BILLING FORM -->
	  </div>

	  <div class="right">
		<!-- SHIPPING FORM -->
		<div id="shippingBlock" <?php if (($this->cart->hasProducts() && !$this->cart->hasShipping()) || $this->config->get('uber_checkout_no_ship_address')) { ?>style="display:none;"<?php } ?>>
		<b style="margin-bottom: 2px; display: block;"><?php echo $text_shipping_address; ?></b>
        <div class="content">
          <table>
            <tr>
              <td width="100%">
			    <?php if ($shipping_indicator) { ?>
      		    <input style="vertical-align: middle;" type="checkbox" value="1" checked="checked" onchange="(this.checked) ? $('#shippingAddress').slideDown('fast') : $('#shippingAddress').slideUp('fast');" name="shipping_indicator" id="shipping_indicator" style="margin: 15px 5px 20px 5px;" /><label for="shipping_indicator"><span style="vertical-align: middle;"><?php echo $text_indicator; ?></span></label>
      			<?php } else { ?>
        		<input style="vertical-align: middle;" type="checkbox" value="1" onclick="(this.checked) ? $('#shippingAddress').slideDown('fast') : $('#shippingAddress').slideUp('fast');" name="shipping_indicator" id="shipping_indicator" style="margin: 15px 5px 20px 5px;" /><label for="shipping_indicator"><?php echo $text_indicator; ?></label>
      			<?php } ?>
              </td>
            </tr>
          </table>
          <table class="register-form" id="shippingAddress" style="<?php if (!$shipping_indicator) { ?>display:none<?php } ?>">
            <tr>
              <td width="30%"><span class="required">*</span> <?php echo $entry_firstname; ?></td>
              <td><input type="text" name="shipping_firstname" value="<?php echo $shipping_firstname; ?>" />
                <?php if ($error_shipping_firstname) { ?>
                <span class="error"><?php echo $error_shipping_firstname; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
              <td><input type="text" name="shipping_lastname" value="<?php echo $shipping_lastname; ?>" />
                <?php if ($error_shipping_lastname) { ?>
                <span class="error"><?php echo $error_shipping_lastname; ?></span>
                <?php } ?></td>
            </tr>
			<tr>
              <td><?php echo $entry_company; ?></td>
              <td><input type="text" name="shipping_company" value="<?php echo $shipping_company; ?>" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_address_1; ?></td>
              <td><input type="text" name="shipping_address_1" value="<?php echo $shipping_address_1; ?>" />
                <?php if ($error_shipping_address_1) { ?>
                <span class="error"><?php echo $error_shipping_address_1; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_address_2; ?></td>
              <td><input type="text" name="shipping_address_2" value="<?php echo $shipping_address_2; ?>" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_city; ?></td>
              <td><input type="text" name="shipping_city" value="<?php echo $shipping_city; ?>" />
                <?php if ($error_shipping_city) { ?>
                <span class="error"><?php echo $error_shipping_city; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_postcode; ?></td>
              <td><input type="text" name="shipping_postcode" value="<?php echo $shipping_postcode; ?>" />
			  	<?php if ($error_shipping_postcode) { ?>
                <span class="error"><?php echo $error_shipping_postcode; ?></span>
                <?php } ?></td>
              </td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_country; ?></td>
              <td><select name="shipping_country_id" id="shipping_country_id" onchange="$('select[name=\'shipping_zone_id\']').load('index.php?route=checkout/checkout_one/zone&shipping_country_id=' + this.value + '&zone_id=<?php echo $shipping_zone_id; ?>');">
                  <option value="FALSE"><?php echo $text_select; ?></option>
                  <?php foreach ($countries as $country) { ?>
                  <option value="<?php echo $country['country_id']; ?>" <?php if( $country['country_id'] == $shipping_country_id){?> selected="selected" <?php } ?> ><?php echo $country['name']; ?></option>
                  <?php } ?>
                </select>
                <?php if ($error_shipping_country) { ?>
                <span class="error"><?php echo $error_shipping_country; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_zone; ?></td>
              <td><select name="shipping_zone_id">
                  <option value="FALSE"><?php echo $text_select; ?></option>
                </select>
                <?php if ($error_shipping_zone) { ?>
                <span class="error"><?php echo $error_shipping_zone; ?></span>
                <?php } ?></td>
            </tr>
          </table>
        </div>
		</div>
		<!-- SHIPPING FORM -->


	  	<!-- PASSWORD FORM -->
        <b style="margin-bottom: 2px; display: block;"><?php echo $text_create_account; ?> <?php if ($guest_checkout) { echo $text_optional; } ?></b>
        <div class="content">
          <p><?php echo $text_password_info; ?></p>
          <table class="register-form">
            <tr>
              <td width="30%"><?php if (!$guest_checkout) { ?><span class="required">*</span> <?php } ?><?php echo $entry_password; ?></td>
              <td><input type="password" name="password" value="<?php echo $password; ?>" autocomplete="off" />
			  	<?php if ($error_password) { ?>
                <span class="error"><?php echo $error_password; ?></span>
                <?php } ?>
              </td>
            </tr>
            <tr>
              <td><?php if (!$guest_checkout) { ?><span class="required">*</span> <?php } ?><?php echo $entry_confirm; ?></td>
              <td>
                <input type="password" name="confirm" value="<?php echo $confirm; ?>" autocomplete="off" />
                <?php if ($error_confirm) { ?>
                <span class="error"><?php echo $error_confirm; ?></span>
                <?php } ?>
              </td>
            </tr>
            <tr>
	          <td><?php echo $entry_newsletter; ?></td>
	          <td><?php if ($newsletter == 1) { ?>
	            <input type="radio" name="newsletter" value="1" checked="checked" />
	            <?php echo $text_yes; ?>
	            <input type="radio" name="newsletter" value="0" />
	            <?php echo $text_no; ?>
	            <?php } else { ?>
	            <input type="radio" name="newsletter" value="1" />
	            <?php echo $text_yes; ?>
	            <input type="radio" name="newsletter" value="0" checked="checked" />
	            <?php echo $text_no; ?>
	            <?php } ?>
	          </td>
	        </tr>
          </table>
        </div>
		<!-- PASSWORD FORM -->

		<!-- CAPTCHA FORM -->
        <?php if ($captcha) { ?>
		<b style="margin-bottom: 2px; display: block;"><?php echo $text_captcha; ?></b>
        <div class="content">
          <table class="register-form">
            <tr>
              <td><input type="text" name="captcha" value="" autocomplete="off"/>
			  	<?php if ($error_captcha) { ?>
                <span class="error"><?php echo $error_captcha; ?></span>
                <?php } ?>
				<br />
				<img src="index.php?route=checkout/checkout_one/captcha" alt="" id="captcha" />
              </td>
            </tr>
          </table>
        </div>
		<?php } ?>
		<!-- CAPTCHA FORM -->

	  </div>

	  <div style="clear:both;"></div>

	  <!-- Agree -->
      <?php if ($text_agree) { ?>
	  <div class="buttons agree">
		<div class="right" style="width:100%"><?php echo $text_agree; ?>
		  <?php if ($agree) { ?>
		  <input type="checkbox" name="agree" value="1" checked="checked" />
		  <?php } else { ?>
		  <input type="checkbox" name="agree" value="1" />
		  <?php } ?>
		</div>
      </div>
      <?php } ?>
	  <div class="buttons">
	    <div class="right">
		<?php echo $register_button_html; ?>
		</div>
	  </div>
      <!-- Agree -->


    </form>
  </div>
</div>

<script type="text/javascript"><!--
$('#login input').keydown(function(e) {
	if (e.keyCode == 13) {
		$('#login').submit();
	}
});
//--></script>

<script type="text/javascript"><!--

function checkRegSubmit() {

	<?php if ($text_agree) { ?>
	if (!$('input[name=\'agree\']').is(':checked')) {
		alert('<?php echo $text_must_agree; ?>');
		return false;
	}
	<?php } ?>

	$('#register').submit();
}

$(document).ready(function () {
	if ($('#country_id').val()) {
		var country_id =  $('#country_id').val();
		$('select[name=\'zone_id\']').load('index.php?route=checkout/checkout_one/zone&country_id=' + country_id + '&zone_id=<?php echo $zone_id; ?>');
	}

	if ($('#shipping_country_id').val()) {
		var shipping_country_id =  $('#shipping_country_id').val();
		$('select[name=\'shipping_zone_id\']').load('index.php?route=checkout/checkout_one/zone&country_id=' + shipping_country_id + '&zone_id=<?php echo $shipping_zone_id; ?>');
	}
});

//--></script>

<script type="text/javascript"><!--
$('select[name=\'customer_group_id\']').live('change', function() {
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

$('select[name=\'customer_group_id\']').trigger('change');
//--></script>
<script type="text/javascript"><!--
$('select[name=\'country_id\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=checkout/checkout_one/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#postcode-required').show();
			} else {
				$('#postcode-required').hide();
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

			$('select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

//--></script>
<script type="text/javascript"><!--
if (jQuery().colorbox) {
	$('.colorbox').colorbox({
		overlayClose: true,
		opacity: 0.5,
		width: "80%",
		height: "80%"
	});
}
if (jQuery().fancybox) {
	$('.fancybox').fancybox({cyclic: true});
	$('.colorbox').fancybox({cyclic: true});
}
//--></script>
<?php if (!$this->config->get('uber_checkout_popup')) { ?>
<?php echo $footer; ?>
<?php } ?>