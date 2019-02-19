<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
  <div class="middle">
    <?php if ($success) { ?>
    <div class="success"><?php echo $success; ?></div>
    <?php } ?>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    
    	<?php 
    if (!$logged_in) {
    ?>    
    <!-- START LOGIN -->
    
    <div class="content">
    	<form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="login">
    		<?php if ($error_login) { ?>
              <span class="error"><?php echo $error_login; ?></span>
              <?php } ?>
   
            <?php echo $text_i_am_returning_customer; ?><br />
            <br />
            <b><?php echo $entry_email; ?></b><br />
            <input type="text" name="email" />
            <br />
            <br />
            <b><?php echo $entry_password; ?></b><br />
            <input type="password" name="password" />
            <br />
            <a href="<?php echo str_replace('&', '&amp;', $forgotten); ?>"><?php echo $text_forgotten; ?></a><br />
            <div style="text-align: left; margin-top:15px;"><a onclick="$('#login').submit();" class="button"><span><?php echo $button_login; ?></span></a></div>
		</form>
    </div>
    
    <!-- END LOGIN -->
    <?php
    }
    ?>
    
    <!-- START PRODUCTS -->
    <div class="content">
      <table width="100%">
	    <thead>
	      <tr>
	        <td class="name"><?php echo $column_name; ?></td>
	        <td class="model"><?php echo $column_model; ?></td>
	        <td class="quantity"><?php echo $column_quantity; ?></td>
	        <td class="price"><?php echo $column_price; ?></td>
	        <td class="total"><?php echo $column_total; ?></td>
	      </tr>
	    </thead>
	    <tbody>
	      <?php foreach ($products as $product) { ?>
	      <tr>
	        <td class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
	          <?php foreach ($product['option'] as $option) { ?>
	          <br />
	          &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
	          <?php } ?></td>
	        <td class="model"><?php echo $product['model']; ?></td>
	        <td class="quantity"><?php echo $product['quantity']; ?></td>
	        <td class="price"><?php echo $product['price']; ?></td>
	        <td class="total"><?php echo $product['total']; ?></td>
	      </tr>
	      <?php } ?>
	      <?php foreach ($vouchers as $voucher) { ?>
	      <tr>
	        <td class="name"><?php echo $voucher['description']; ?></td>
	        <td class="model"></td>
	        <td class="quantity">1</td>
	        <td class="price"><?php echo $voucher['amount']; ?></td>
	        <td class="total"><?php echo $voucher['amount']; ?></td>
	      </tr>
	      <?php } ?>
	    </tbody>
	    <tfoot>
	      <?php foreach ($totals as $total) { ?>
	      <tr>
	        <td colspan="4" class="price"><b><?php echo $total['title']; ?>:</b></td>
	        <td class="total"><?php echo $total['text']; ?></td>
	      </tr>
	      <?php } ?>
	    </tfoot>
	  </table>
    </div>
    <!-- END PRODUCTS -->
    
    
 <form name="order" id="order" action="<?php echo $action ?>" method="post">   
    

    <!-- START ACCOUNT -->

    <input type="hidden" name="shipping[country_id]" value="203" />
    <input type="hidden" name="shipping[zone_id]" value="0" />
    <div class="content" id="personal_info">
    	<table>
    	  <tr>
            <td width="150"><label for="shipping_company"><?php echo $entry_company; ?></label></td>
            <td><input type="text" name="shipping[company]" id="shipping_company" value="<?php echo $company; ?>" /></td>
          </tr>
          <tr>
            <td width="150"><label for="shipping_firstname"><span class="required">*</span> <?php echo $entry_firstname; ?></label></td>
            <td><input type="text" name="shipping[firstname]" id="shipping_firstname" value="<?php echo $firstname; ?>" />
              <?php if ($error_firstname) { ?>
              <span class="error"><?php echo $error_firstname; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><label for="shipping_lastname"><span class="required">*</span> <?php echo $entry_lastname; ?></label></td>
            <td><input type="text" name="shipping[lastname]" id="shipping_lastname" value="<?php echo $lastname; ?>" />
              <?php if ($error_lastname) { ?>
              <span class="error"><?php echo $error_lastname; ?></span>
              <?php } ?></td>
          </tr>
          
          <tr>
            <td><label for="shipping_address1"><span class="required">*</span> <?php echo $entry_address_1; ?></label></td>
            <td><input type="text" name="shipping[address_1]" id="shipping_address1" value="<?php echo $address_1; ?>" />
              <?php if ($error_address_1) { ?>
              <span class="error"><?php echo $error_address_1; ?></span>
              <?php } ?></td>
          </tr>
          
          <tr>
            <td><label for="shipping_address1"><?php echo $entry_address_2; ?></label></td>
            <td><input type="text" name="shipping[address_2]" id="shipping_address2" value="<?php echo $address_2; ?>" /></td>
          </tr>
          
          <tr>
            <td><label for="shipping_postcode" id="postcode"><span class="required">*</span> <?php echo $entry_postcode; ?></label></td>
            <td><input type="text" name="shipping[postcode]" id="shipping_postcode" value="<?php echo $postcode; ?>" />
			  <?php if ($error_postcode) { ?>
              <span class="error"><?php echo $error_postcode; ?></span>
              <?php } ?></td>
          </tr>
          
          <tr>
            <td><label for="shipping_city"><span class="required">*</span> <?php echo $entry_city; ?></label></td>
            <td><input type="text" name="shipping[city]" id="shipping_city" value="<?php echo $city; ?>" />
              <?php if ($error_city) { ?>
              <span class="error"><?php echo $error_city; ?></span>
              <?php } ?></td>
          </tr>
           <?php if (!$hide_country) { ?>
          <tr>
          <td><span class="required">*</span> <?php echo $entry_country; ?></td>
          <td><select name="shipping[country_id]" onchange="$('select[name=\'shipping[zone_id]\']').load('index.php?route=account/register/zone&country_id=' + this.value + '&zone_id=<?php //echo $zone_id; ?>');">
              <option value=""><?php echo $text_select; ?></option>
              <?php foreach ($countries as $country) { ?>
              <?php if ($country['country_id'] == $country_id) { ?>
              <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
            <?php if ($error_country) { ?>
            <span class="error"><?php echo $error_country; ?></span>
            <?php } ?></td>
        </tr>
        <?php
        }
        if (!$hide_zone) { ?>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_zone; ?></td>
          <td><select name="shipping[zone_id]">
          	<?php foreach ($zones as $zone) { ?>
              <?php if ($zone['zone_id'] == $zone_id) { ?>
              <option value="<?php echo $zone['zone_id']; ?>" selected="selected"><?php echo $zone['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
            <?php if ($error_zone) { ?>
            <span class="error"><?php echo $error_zone; ?></span>
            <?php } ?></td>
        </tr>
        <?php } ?>
          <?php 
        if (!$logged_in) {
        ?>
       
          <tr>
            <td><label for="customer_email"><span class="required">*</span> <?php echo $entry_email; ?></label></td>
            <td><input type="text" name="email" id="customer_email" value="<?php echo $email; ?>" />
              <?php if ($error_email) { ?>
              <span class="error"><?php echo $error_email; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><label for="customer_telephone"><span class="required">*</span> <?php echo $entry_telephone; ?></label></td>
            <td><input type="text" name="customer[telephone]" id="customer_telephone" value="<?php echo $telephone2; ?>" />
              <?php if ($error_telephone) { ?>
              <span class="error"><?php echo $error_telephone; ?></span>
              <?php } ?></td>
          </tr>
          
          <tr>
            <td><label for="customer_fax"><?php echo $entry_fax; ?></label></td>
            <td><input type="text" name="customer[fax]" id="customer_fax" value="<?php echo $fax; ?>" /></td>
          </tr>
		<?php
    	}
    	?>
        </table>

        
        
        <?php 
        if (!$logged_in) {
        ?>
           
        
         <?php
        	if ($guest_checkout) {
        ?>
        <table style="margin-top: 15px">
        <tr>
            <td width="150"><label for="customer_email"><span class="required">*</span> <?php echo $entry_create_account; ?></label></td>
            <td>
            	<input type="radio" name="checkout_type" value="guest" id="checkout_type_guest" /> <label for="checkout_type_guest"><?php echo $text_no; ?></label>
         	   <input type="radio" name="checkout_type" value="account" id="checkout_type_account" checked="checked" /> <label for="checkout_type_account"><?php echo $text_yes; ?></label>
         	</td>
          </tr>
        </table>
        <?php
        }
        ?>
        <div id="password_holder">
        <table>
          <tr>
            <td width="150"><span class="required">*</span> <?php echo $entry_password; ?></td>
            <td><input type="password" name="customer[password]"  />
              <?php if ($error_password) { ?>
              <span class="error"><?php echo $error_password; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_confirm; ?></td>
            <td><input type="password" name="customer[confirm]" />
              <?php if ($error_confirm) { ?>
              <span class="error"><?php echo $error_confirm; ?></span>
              <?php } ?></td>
          </tr>
        </table>
        <?php if ($text_agree_account) { ?>

  <div class=""><?php echo $text_agree_account; ?>
    <input type="checkbox" name="agree_account" value="1" />
  </div>
<?php } ?>
        </div>
         
    	<?php
    	}
    	?>
    </div>
    
    
    <!-- END ACCOUNT -->
    
    <!-- START SEPARTE SHIPPING ADDRESS -->
    
    <input type="checkbox" name="different_shipping_address" id="different_shipping_address" <?php echo $different_shipping_address; ?> /> <label for="different_shipping_address"><?php echo $entry_different_shipping_address; ?></label>
    <input type="hidden" name="different_shipping[country_id]" value="203" />
    <input type="hidden" name="different_shipping[zone_id]" value="0" />
    <div class="content" id="different_shipping_holder" <?php if (!$different_shipping_address) echo 'style="display: none"'; ?>>
    	<table>

    	  <tr>
            <td width="150"><label for="different_shipping_company"><?php echo $entry_company; ?></label></td>
            <td><input type="text" name="different_shipping[company]" id="different_shipping_company" value="<?php echo $different_company; ?>" /></td>
          </tr>
          <tr>
            <td width="150"><label for="different_shipping_firstname"><span class="required">*</span> <?php echo $entry_firstname; ?></label></td>
            <td><input type="text" name="different_shipping[firstname]" id="different_shipping_firstname" value="<?php echo $different_firstname; ?>" />
              <?php if ($error_different_firstname) { ?>
              <span class="error"><?php echo $error_different_firstname; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><label for="different_shipping_lastname"><span class="required">*</span> <?php echo $entry_lastname; ?></label></td>
            <td><input type="text" name="different_shipping[lastname]" id="different_shipping_lastname" value="<?php echo $different_lastname; ?>" />
              <?php if ($error_different_lastname) { ?>
              <span class="error"><?php echo $error_different_lastname; ?></span>
              <?php } ?></td>
          </tr>
          
          <tr>
            <td><label for="different_shipping_address1"><span class="required">*</span> <?php echo $entry_address_1; ?></label></td>
            <td><input type="text" name="different_shipping[address_1]" id="different_shipping_address1" value="<?php echo $different_address_1; ?>" />
              <?php if ($error_different_address_1) { ?>
              <span class="error"><?php echo $error_different_address_1; ?></span>
              <?php } ?></td>
          </tr>
          
          <tr>
            <td><label for="different_shipping_address2"><?php echo $entry_address_2; ?></label></td>
            <td><input type="text" name="different_shipping[address_2]" id="different_shipping_address2" value="<?php echo $different_address_2; ?>" /></td>
          </tr>
          
          <tr>
            <td id="postcode"><label for="different_shipping_postcode"><span class="required">*</span> <?php echo $entry_postcode; ?></label></td>
            <td><input type="text" name="different_shipping[postcode]" id="different_shipping_postcode" value="<?php echo $different_postcode; ?>" />
			  <?php if ($error_different_postcode) { ?>
              <span class="error"><?php echo $error_different_postcode; ?></span>
              <?php } ?></td>
          </tr>
          
          <tr>
            <td><label for="different_shipping_city"><span class="required">*</span> <?php echo $entry_city; ?></label></td>
            <td><input type="text" name="different_shipping[city]" id="different_shipping_city" value="<?php echo $different_city; ?>" />
              <?php if ($error_different_city) { ?>
              <span class="error"><?php echo $error_different_city; ?></span>
              <?php } ?></td>
          </tr>
         <?php if (!$hide_country) { ?>
			<tr>
          <td><span class="required">*</span> <?php echo $entry_country; ?></td>
          <td><select name="different_shipping[country_id]" onchange="$('select[name=\'different_shipping[zone_id]\']').load('index.php?route=account/register/zone&country_id=' + this.value + '&zone_id=<?php echo $zone_id; ?>');">
              <option value=""><?php echo $text_select; ?></option>
              <?php foreach ($countries as $country) { ?>
              <?php if ($country['country_id'] == $country_id) { ?>
              <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
            <?php if ($error_country) { ?>
            <span class="error"><?php echo $error_country; ?></span>
            <?php } ?></td>
        </tr>
        <?php 
        }
        if (!$hide_zone) { ?>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_zone; ?></td>
          <td><select name="different_shipping[zone_id]">
        	  <?php foreach ($zones as $zone) { ?>
              <?php if ($zone['zone_id'] == $zone_id) { ?>
              <option value="<?php echo $zone['zone_id']; ?>" selected="selected"><?php echo $zone['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
            <?php if ($error_different_zone) { ?>
            <span class="error"><?php echo $error_different_zone; ?></span>
            <?php } ?></td>
        </tr>
		<?php
			}
		?>
        </table>
    </div>
    
    <!-- END SEPRATE SHIPPING ADDRESS -->
    
    <!-- START PAYMENT -->
    
     <?php if ($payment_methods) { ?>
<div class="content">
<p><?php echo $text_payment_method; ?></p>
<table class="form" id="payment_methods">
  <?php foreach ($payment_methods as $payment_method) { ?>
  <tr>
    <td style="width: 1px;"><?php if ($payment_method['code'] == $code || !$code) { ?>
      <?php $code = $payment_method['code']; ?>
      <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" />
      <?php } else { ?>
      <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" />
      <?php } ?></td>
    <td><label for="<?php echo $payment_method['code']; ?>"><?php echo $payment_method['title']; ?></label></td>
  </tr>
  <?php } ?>
</table>
</div>
<?php } ?>
    
    <!-- END PAYMENT -->
    
    
    <!-- START SHIPPING METHOD -->
    

      <?php if ($shipping_methods || $dynamic_shipping) { ?>
<div class="content">
	<p><?php echo $text_shipping_method; ?></p>
	<table class="form" id="shipping_methods">
	  <?php foreach ($shipping_methods as $shipping_method) { ?>
	  <tr>
	    <td colspan="3"><b><?php echo $shipping_method['title']; ?></b></td>
	  </tr>
	  <?php if (!$shipping_method['error']) { ?>
	  <?php foreach ($shipping_method['quote'] as $quote) {  ?>
	  <tr>
	    <td style="width: 1px;"><?php if ($quote['code'] == $shipping_code || !$shipping_code) { ?>
	      <?php $shipping_code = $quote['code']; ?>
	      <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" checked="checked" />
	      <?php } else { ?>
	      <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" />
	      <?php } ?></td>
	    <td><label for="<?php echo $quote['code']; ?>"><?php echo $quote['title']; ?></label></td>
	    <td style="text-align: right;"><label for="<?php echo $quote['code']; ?>"><?php echo $quote['text']; ?></label></td>
	  </tr>
	  <?php } ?>
	  <?php } else { ?>
	  <tr>
	    <td colspan="3"><div class="error"><?php echo $shipping_method['error']; ?></div></td>
	  </tr>
	  <?php } ?>
	  <?php } ?>
	</table>

	<?php if ($dynamic_shipping) { ?>
	<p><?php echo $text_dynamic_shipping; ?></p>
	<a onclick="javascript: updateShippingMethods();" class="button" id="updateShippingMethods"><span><?php echo $button_update_shipping; ?></span></a>
	<?php } ?>
</div>
<?php } ?>
    
    
    <!-- END SHIPPING METHOD -->
    
    
    
    <div class="content">
    	<b style="margin-bottom: 2px; display: block;"><?php echo $text_comment; ?></b>
    	<textarea name="comment"><?php echo $comment; ?></textarea>
    </div>
    
    
<div class="buttons">
  <div class="right">
  <?php if ($text_agree_payment) { ?>
  	<?php echo $text_agree_payment; ?>
    <input type="checkbox" name="agree_payment" value="1" /> 
  <?php } ?>
    <a onclick="$('#order').submit();" class="button" id="checkout"><span><?php echo $button_order; ?></span></a>
  </div>
</div>



  </div>
</form>
  <?php echo $content_bottom; ?>
</div>
<script type="text/javascript" src="catalog/view/javascript/jquery/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/fancybox/jquery.fancybox-1.3.4.css" media="screen" /> 
<script type="text/javascript"><!--

$('.fancybox').fancybox({
	width: 560,
	height: 560,
	autoDimensions: false
});

<?php
if ($hide_zone) { ?>
/* If zones is hidden, update shipping and payment methods on country change */
?>
$('select[name=\'shipping[country_id]\']').change(function() {
	$('select[name=\'shipping[zone_id]\']').load('index.php?route=account/register/zone&country_id=' + this.value + '&zone_id=<?php //echo $zone_id; ?>');
	
	updatePaymentMethods();
	
	if ($('#different_shipping_address').is(':checked') !== true) {
		updateShippingMethods();
	}
});

$('select[name=\'different_shipping[country_id]\']').change(function() {
	updateShippingMethods();
});
<?php
} else {
/* else, update shipping and payment methods on zone change */
?>
$('select[name=\'shipping[country_id]\']').change(function() {
	$('select[name=\'shipping[zone_id]\']').load('index.php?route=account/register/zone&country_id=' + this.value + '&zone_id=<?php //echo $zone_id; ?>');
});
$('select[name=\'shipping[zone_id]\']').change(function() {

	updatePaymentMethods();
	
	if ($('#different_shipping_address').is(':checked') !== true) {
		updateShippingMethods()
	}
});


$('select[name=\'different_shipping[country_id]\']').change(function() {
	if ($('#different_shipping_address').is(':checked') === true) {
		$('select[name=\'different_shipping[zone_id]\']').load('index.php?route=account/register/zone&country_id=' + this.value + '&zone_id=<?php echo $zone_id; ?>');
	}
});

$('select[name=\'different_shipping[zone_id]\']').change(function() {
	updateShippingMethods();

});

<?php 
}
?>
<?php 
if ($dynamic_shipping) {
/* If dynamic shipping is enabled, update shipping methods when city or postcode is changed so the price estimate is correct */
?>
$('input[name=\'shipping[city]\'], input[name=\'shipping[postcode]\']').blur(function() {
	if ($('#different_shipping_address').is(':checked') !== true) {
		if ($('select[name=\'shipping[country_id]\']').val() != 0) {
	
			if (($('select[name=\'shipping[zone_id]\']').length > 0 && $('select[name=\'shipping[zone_id]\']').val() != 0) || $('select[name=\'shipping[zone_id]\']').length == 0) {
				updateShippingMethods();
			}
		}
	}
});
$('input[name=\'different_shipping[city]\'], input[name=\'different_shipping[postcode]\']').blur(function() {
	if ($('#different_shipping_address').is(':checked') === true) {
		if ($('select[name=\'different_shipping[country_id]\']').val() != 0) {
	
			if (($('select[name=\'different_shipping[zone_id]\']').length > 0 && $('select[name=\'different_shipping[zone_id]\']').val() != 0) || $('select[name=\'different_shipping[zone_id]\']').length == 0) {
				updateShippingMethods();
			}
		}
	}
});
<?php } ?>
function updatePaymentMethods() {
	$('#payment_methods').html('<img src="<?php echo $loading_image; ?>" />');
	$.ajax({
		url: 'index.php?route=checkout/simplified_checkout/getPaymentMethods',
		dataType: 'json',
		data: 'country_id=' + $('select[name=\'shipping[country_id]\']').val() + '&zone_id=' + $('select[name=\'shipping[zone_id]\']').val(),
		success: function(data) {
			var items = [];
			$('#payment_methods').html('');
			var html = '';
			var i = 0;
			$.each(data, function(key, value) {
				 html += '<tr>';
				 if (i == 0) {
				 	html += '<td style="width: 1px;"><input type="radio" name="payment_method" value="' + key + '" id="' + key + '" checked="checked" /></td>';
				 } else {
				 	html += '<td style="width: 1px;"><input type="radio" name="payment_method" value="' + key + '" id="' + key + '" /></td>';
				 }
				 html += '<td><label for="' + key + '">' + value.title + '</label></td>';
				 html += '</tr>';
				 i++;
			});
			$('#payment_methods').html(html);
		}
	});
}

function updateShippingMethods() {
	$('#shipping_methods').html('<img src="<?php echo $loading_image; ?>" />');
	
	if ($('#different_shipping_address').is(':checked') === true) {
		var data = $('input[name^="different_shipping"], select[name^="different_shipping"]').serialize();
	} else {
		var data = $('input[name^="shipping"], select[name^="shipping"]').serialize();
	}

	$.ajax({
		url: 'index.php?route=checkout/simplified_checkout/getShippingMethods',
		dataType: 'json',
		//data: 'country_id=' + $('select[name=\'different_shipping[country_id]\']').val() + '&zone_id=' + this.value,
		data: data,
		success: function(data) {
			var items = [];
			$('#shipping_methods').html('');
			var html = '';
			var i = 0;
			$.each(data, function(key, value) {
				html += '<tr>';
				html += '<td colspan="3"><b>' + value.title + '</b></td>';
				html += '</tr>';
				
				for (var n in value.quote) {
					html += '<tr>';
					if (i == 0) {
						html += '<td style="width: 1px;"><input type="radio" name="shipping_method" value="' + value.quote[n].code + '" id="' + value.quote[n].code + '" checked="checked" /></td>';
					} else {
						html += '<td style="width: 1px;"><input type="radio" name="shipping_method" value="' + value.quote[n].code + '" id="' + value.quote[n].code + '" /></td>';
					}
					html += '<td><label for="' + value.quote[n].code + '">' + value.quote[n].title + '</label></td>';
					html += '<td style="text-align: right;"><label for="' + value.quote[n].code + '">' + value.quote[n].text + '</label></td>'
					html += '</tr>';
					 
					i++;
				} 
			});
			$('#shipping_methods').html(html);
		}
	});
}

function updateTotals() {
	var data = $('input[name=payment_method]:checked, input[name=shipping_method]:checked').serialize();

	$.ajax({
		url: 'index.php?route=checkout/simplified_checkout/getTotals',
		dataType: 'json',
		data: data,
		success: function(data) {
			var items = [];
			$('#totals').html('');
			var html = '';
			var i = 0;
			$.each(data, function(key, value) {
				html += '<tr>';
				html += '<td colspan="4"><b>' + value.title + '</b></td>';
				html += '<td class="total">' + value.text + '';
				html += '</tr>';
			});
			$('#totals').html(html);
		}
	});
}


$('input[name=\'checkout_type\']').change(function() {

	$('#password_holder').slideToggle();
});

$('#different_shipping_address').click(function() {
	$('#different_shipping_holder').slideToggle();
});
//--></script>
<?php echo $footer; ?>