<?php echo $header; ?>
 <div class="breadcrumb">
 <div class="container">
 <h1><?php echo $heading_title; ?></h1>
 </div>
 <div id="breadcrumbs">
 <div class="container">
 <?php $zet = 100;  $counter = 0; ?>
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>

    <div id="bread" <?php if($zet == 100) echo 'class="first"' ?>style="z-index: <?php echo $zet--; ?>; left: <?php echo $counter * -30;?>px;">
    <a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <span></span>
    </div>
    <?php $counter++; } ?>
  </div>
    </div>
   </div>
  </div> <!--  end top_container -->
  <div class="container">
  <?php echo $column_left; ?><?php echo $column_right; ?>
<!--
//-----------------------------------------
// Author: 	Qphoria@gmail.com
// Web: 	http://www.OpenCartGuru.com/
// Title: 	Uber Checkout 1.5.x
//-----------------------------------------
-->
<style type="text/css">
.agree {
	background: #EAF7D9 10px center no-repeat;
	border: 1px solid #BBDF8D;
	-webkit-border-radius: 5px 5px 5px 5px;
	-moz-border-radius: 5px 5px 5px 5px;
	-khtml-border-radius: 5px 5px 5px 5px;
	border-radius: 5px 5px 5px 5px;
}
</style>
<div id="content"><?php echo $content_top; ?>
  <?php
  if ($this->config->get('uber_checkout_payment_style') == 'select') {
  	$payment_style = 'select';
  } else {
  	$payment_style = 'radio';
  }
  if ($this->config->get('uber_checkout_shipping_style') == 'select') {
  	$shipping_style = 'select';
  } else {
  	$shipping_style = 'radio';
  }
  ?>

  <div class="content">
    <?php if ($success) { ?>
    <div class="success"><?php echo $success; ?></div>
    <?php } ?>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>

    <!-- FLOAT LEFT -->
    <div style="width:42%; float:left;">

      <!-- Addresses -->
      <div class="content" id="address">
        <table style="width:100%">
          <tr>
            <td width="50%" valign="top"><b style="margin-bottom: 4px; display: block;"><?php echo $text_payment_address; ?></b> <span id="payment_address"><?php echo $payment_address; ?></span><br />
              <?php if (isset($this->session->data['guest'])) { ?>
              <a href="<?php echo str_replace('&', '&amp;', $checkout_payment_address); ?>"><?php echo $text_change; ?></a>
              <?php } else { ?>
              <a onclick="getAddresses();"><?php echo $text_change; ?></a>
              <?php } ?>
            </td>
            <?php if ($shipping_address) { ?>
            <td width="50%" valign="top">
              <b style="margin-bottom: 4px; display: block;"><?php echo $text_shipping_address; ?></b> <span id="shipping_address"><?php echo $shipping_address; ?></span><br />
              <?php if (isset($this->session->data['guest'])) { ?>
              <a href="<?php echo str_replace('&', '&amp;', $checkout_shipping_address); ?>"><?php echo $text_change; ?></a>
              <?php } else { ?>
              <a onclick="getAddresses();"><?php echo $text_change; ?></a>
              <?php } ?>
            </td>
            <?php } ?>
          </tr>
        </table>
      </div>
      <div style="display:none;" id="register-change-address">
      <div id="address-existing">
	    <b style="margin-bottom: 4px; display: block;"><?php echo $text_payment_address; ?></b>
	    <select name="payment_address_id" style="width: 100%; margin-bottom: 15px;" size="5">
	      <?php foreach ($addresses as $address) { ?>
	      <?php if ($address['address_id'] == $payment_address_id) { ?>
	      <option value="<?php echo $address['address_id']; ?>" selected="selected"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
	      <?php } else { ?>
	      <option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
	      <?php } ?>
	      <?php } ?>
	    </select>
	    <?php if ($this->cart->hasShipping() && !$this->config->get('uber_checkout_no_ship_address')) { ?>
		<b style="margin-bottom: 4px; display: block;"><?php echo $text_shipping_address; ?></b>
	    <select name="shipping_address_id" style="width: 100%; margin-bottom: 15px;" size="5">
	      <?php foreach ($addresses as $address) { ?>
	      <?php if ($address['address_id'] == $shipping_address_id) { ?>
	      <option value="<?php echo $address['address_id']; ?>" selected="selected"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
	      <?php } else { ?>
	      <option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
	      <?php } ?>
	      <?php } ?>
	    </select>
		<?php } ?>
	  </div>
	  <div class="buttons">
	    <div class="left"><a href="<?php echo $add_address; ?>" class="button" ><span><?php echo $button_add_address; ?></span></a></div>
	    <div class="right"><a id="updateAddress" onclick="updateAddress();" class="button"><span><?php echo $button_continue; ?></span></a></div>
	  </div>
	  <script type="text/javascript"><!--
	  $('#button-change-address').live('click', function() {


	  });
	  //--></script>
	  </div>
      <!-- Addresses -->


      <!-- Shipping Choice -->
	  <?php if ($shipping_methods) { ?>
      <div class="content" id="shipping_methods">
      <?php if ($shipping_style == 'select') { ?>
        <table width="100%" border="0">
          <tr class="highlight">
            <td align="left"><b><?php echo $entry_choose_shipping; ?></b></td>
          </tr>
          <tr>
            <td align="left" width="70%">
              <select name="shipping_method" onchange="updateShippingPaymentTotals();" style="width:100%;" >
                <?php foreach ($shipping_methods as $shipping_method) { ?>
		        <?php if (!empty($shipping_method['error'])) { ?>
				<optgroup label="<?php echo $shipping_method['title'] . ' ' . $shipping_method['error']; ?>"></optgroup>
				<?php } else { ?>
                <optgroup label="<?php echo $shipping_method['title']; ?>"></optgroup>
				<?php foreach ($shipping_method['quote'] as $quote) { ?>
                <?php if ($shipping_code == $quote['code'] || !$shipping_code) { ?>
                <option value="<?php echo $quote['code']?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $quote['title']?>  (<?php echo $quote['text']?>)</option>
                <?php } else { ?>
                <option value="<?php echo $quote['code']?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $quote['title']?> (<?php echo $quote['text']?>)</option>
                <?php } ?>
                <?php } ?>
                <?php } ?>
                <?php } ?>
              </select>
            </td>
          </tr>
        </table>
        <?php } else { ?>
	    <!-- (Radio) -->
        <table width="100%" cellpadding="3">
          <?php foreach ($shipping_methods as $shipping_method) { ?>
          <tr class="highlight">
            <td colspan="3"><b><?php echo $shipping_method['title']; ?></b></td>
          </tr>
          <?php if (!$shipping_method['error']) { ?>
          <?php foreach ($shipping_method['quote'] as $quote) { ?>
          <tr>
            <td width="1">
                <?php if ($quote['code'] == $shipping_code || !$shipping_code) { ?>
				<?php $shipping_code = $quote['code']; ?>
                <input type="radio" name="shipping_method" onchange="updateShippingPaymentTotals();" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" checked="checked" style="margin: 0px;" />
                <?php } else { ?>
                <input type="radio" name="shipping_method" onchange="updateShippingPaymentTotals();" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" style="margin: 0px;" />
                <?php } ?>
            </td>
            <td><label for="<?php echo $quote['code']; ?>" style="cursor: pointer;"><?php echo $quote['title']; ?></label></td>
            <td width="1" align="right"><label for="<?php echo $quote['code']; ?>" style="cursor: pointer;"><?php echo $quote['text']; ?></label></td>
          </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td colspan="3"><div class="error"><?php echo $shipping_method['error']; ?></div></td>
          </tr>
          <?php } ?>
          <?php } ?>
        </table>
	    <?php } ?>
	  </div>
      <?php } ?>
	  <!-- Shipping Choice -->



      <!-- Payment Choice -->
      <?php if ($payment_methods) { ?>
      <div class="content" id="payment_methods">
        <?php if ($payment_style == 'select') { ?>
        <table width="100%" border="0">
          <tr class="highlight">
            <td align="left"><b><?php echo $entry_choose_payment; ?></b></td>
          </tr>
          <tr>
            <td align="left" width="70%">
              <select name="payment_method" onchange="updatePayment();" style="width:100%;" >
                <?php foreach ($payment_methods as $payment_method) { ?>
                <?php if ($payment_code == $payment_method['code'] || !$payment_code) {?>
                <option value="<?php echo $payment_method['code'] ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $payment_method['title']?></option>
                <?php } else { ?>
                <option value="<?php echo $payment_method['code'] ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $payment_method['title']?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </td>
          </tr>
        </table>
        <?php } else { ?>
	    <!-- (Radio) -->
        <table width="100%" cellpadding="3">
        <?php foreach ($payment_methods as $payment_method) { ?>
          <tr class="highlight">
            <td width="1">
		      <label for="<?php echo $payment_method['code']; ?>">
                <?php if ($payment_method['code'] == $payment_code || !$payment_code) { ?>
		  	    <?php $payment_code = $payment_method['code']; ?>
                <input type="radio" name="payment_method" onchange="updatePayment();" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" style="margin: 0px;" />
                <?php } else { ?>
                <input type="radio" name="payment_method" onchange="updatePayment();" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" style="margin: 0px;" />
                <?php } ?>
              </label>
            </td>
            <td colspan="2"><label for="<?php echo $payment_method['code']; ?>" style="cursor: pointer;"><?php echo $payment_method['title']; ?></label></td>
          </tr>
          <?php } ?>
        </table>
	    <?php } ?>
	  </div>
      <?php } ?>
	  <!-- Payment Choice -->


	  <!-- Comment -->
	  <div class="content">
        <p><?php echo $text_comment; ?></p>
        <div style="text-align: right;">
          <textarea name="comment" rows="3" cols="40" style="width:95%;"><?php echo $comment; ?></textarea>
          <br/>
          <span id="span_comment_button"><a onclick="updateComment();" class="button"><span><?php echo $button_comment; ?></span></a></span>
          <p id="comment-message" style="display:none;"></p>
        </div>
      </div>
      <!-- Comment -->


    </div>
    <!-- FLOAT LEFT -->





    <!-- FLOAT RIGHT -->
    <div style="width:54%; float:right;">


	  <!-- Coupon -->
	  <?php if ($coupon_status) { ?>
      <div class="content">
        <div style="text-align: right;"><?php echo $entry_coupon; ?>&nbsp;
          <input type="text" name="coupon" value="<?php echo $coupon; ?>" />&nbsp;
          <span id="span_coupon_button"><a onclick="updateCouponPaymentTotals();" class="button"><span><?php echo $button_coupon; ?></span></a></span>
          <p id="coupon-message" style="display:none;"></p>
        </div>
      </div>
      <?php } ?>
	  <!-- Coupon -->

	  <!-- Voucher -->
	  <?php if ($voucher_status) { ?>
      <div class="content">
        <div style="text-align: right;"><?php echo $entry_voucher; ?>&nbsp;
          <input type="text" name="voucher" value="<?php echo $voucher; ?>" />&nbsp;
          <span id="span_voucher_button"><a onclick="updateVoucherPaymentTotals();" class="button"><span><?php echo $button_voucher; ?></span></a></span>
          <p id="voucher-message" style="display:none;"></p>
        </div>
      </div>
      <?php } ?>
	  <!-- Voucher -->




	  <!-- Products -->
      <div class="content">
        <table width="100%">
          <tr>
            <th align="name"><?php echo $column_product; ?></th>
            <th align="model"><?php echo $column_model; ?></th>
            <th align="quantity"><?php echo $column_quantity; ?></th>
            <th align="price"><?php echo $column_price; ?></th>
            <th align="total"><?php echo $column_total; ?></th>
          </tr>
          <?php foreach ($products as $product) { ?>
          <tr>
            <td align="name" valign="top"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
              <?php foreach ($product['option'] as $option) { ?>
              <br />
              &nbsp;<small> - <?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
              <?php } ?>
            </td>
            <td align="model" valign="top"><?php echo $product['model']; ?></td>
            <td align="quantity" valign="top"><?php echo $product['quantity']; ?></td>
            <td align="price" valign="top"><?php echo $product['price']; ?></td>
            <td align="total" valign="top"><?php echo $product['total']; ?></td>
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
        </table>
      </div>
	  <!-- Products -->



	  <!-- Totals -->
      <div id="totals" class="content" style="width: 100%; display: inline-block; padding:10px 0; margin-top:0;">
        <table style="float: right; display: inline-block; padding-right:5px;">
          <?php foreach ($totals as $total) { ?>
          <tr>
            <td align="right"><?php echo $total['title']; ?></td>
            <td align="right"><?php echo $total['text']; ?></td>
          </tr>
          <?php } ?>
        </table>
        <br />
      </div>
      <!-- Totals -->


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
      <div class="buttons well" id="agreefake">
		<span class="right"><a onclick="alert('<?php echo $text_must_agree;?>'); return false;" class="button"><span><?php echo $button_confirm; ?></span></a></span>
	  </div>
      <?php } ?>
      <!-- Agree -->


      <!-- Payment -->
      <div class="content" id="payment_div" <?php if ($text_agree) { ?> style="display:none;" <?php } ?>><?php echo $payment; ?></div>
      <!-- Payment -->


    </div><!-- FLOAT RIGHT -->

    <div style="clear:both;"></div>

  </div>
</div>


<script type="text/javascript"><!--
// Update coupon on enter button
$('input[name=\'coupon\']').keydown(function(e) {
	if (e.keyCode == 13) {
		updateCouponPaymentTotals();
	}
});
// Update voucher on enter button
$('input[name=\'voucher\']').keydown(function(e) {
	if (e.keyCode == 13) {
		updateVoucherPaymentTotals();
	}
});

<?php if (!$shipping_methods && $this->cart->hasShipping()) { ?>
$('#payment_div').html('<div class="warning"><?php echo $this->language->get('error_shipping_methods'); ?></div>');
<?php } ?>

function getAddresses() {
	$('#address').fadeOut('fast');
	$('#register-change-address').fadeIn('fast');
}

function updateAddress() {
	$.ajax({
		type: 'post',
		url: 'index.php?route=checkout/checkout_two/address',
		dataType: 'json',
		data: $('select[name=\'payment_address_id\'], select[name=\'shipping_address_id\']'),
		beforeSend: function() {
			$('#updateAddress').replaceWith('<div class="loader right" style="width:82px; margin:5px 30px 0 0;"><img src="catalog/view/theme/default/image/ajax_load_small.gif" alt="<?php echo $text_loading; ?>" /></div>');
		},
		success: function(json) {
			//reload page to force shipping/payment options to update. Should be redone with ajax. Then uncomment this code
			//if (json['payment_address']) {
			//	$('#payment_address').html(json['payment_address']).fadeOut('fast').fadeIn('slow');
			//}
			//if (json['shipping_address']) {
			//	$('#shipping_address').html(json['shipping_address']).fadeOut('fast').fadeIn('slow');
			//}
		},
		complete: function() {
			//updateTotals();
			//$('#register-change-address').fadeOut('fast');
			//$('#address').fadeIn('fast', function() {
				window.location.reload(true);
			//});
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function updatePayment() {
	$.ajax({
		type: 'post',
		url: 'index.php?route=checkout/checkout_two/payment',
		dataType: 'html',
		data: $('input[name=\'payment_method\']:checked, select[name=\'payment_method\']'),
		beforeSend: function() {
			$('#coupon-message').fadeOut('fast');
			$('#voucher-message').fadeOut('fast');
			$('#comment-message').fadeOut('fast');
			$('#payment_div').html('<div class="loader" style="text-align:center;"><img src="catalog/view/theme/default/image/ajax_load.gif" alt="<?php echo $text_loading; ?>" /></div>');
			<?php if ($this->config->get('uber_checkout_payment_update_total')) { ?>
			$('#totals').html('<div class="loader" style="text-align:center;"><img src="catalog/view/theme/default/image/ajax_load.gif" alt="<?php echo $text_loading; ?>" /></div>');
			<?php } ?>
		},
		success: function(html) {
			<?php if (!$shipping_methods && $this->cart->hasShipping()) { ?>
			$('#payment_div').html('<div class="warning"><?php echo $this->language->get('error_shipping_methods'); ?></div>');
			<?php } else { ?>
			$('#payment_div').html(html);
			<?php } ?>
			<?php if ($this->config->get('uber_checkout_payment_update_total')) { ?>
			updateTotals();
			<?php } ?>
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function updateShippingPaymentTotals() {
	$.ajax({
		type: 'post',
		url: 'index.php?route=checkout/checkout_two/shippingPaymentTotals',
		data: $('input[name=\'shipping_method\']:checked, select[name=\'shipping_method\'],input[name=\'payment_method\']:checked, select[name=\'payment_method\']'),
		dataType: 'json',
		beforeSend: function() {
			$('#coupon-message').fadeOut('fast');
			$('#voucher-message').fadeOut('fast');
			$('#comment-message').fadeOut('fast');
			$('#payment_div').html('<div class="loader" style="text-align:center;"><img src="catalog/view/theme/default/image/ajax_load.gif" alt="<?php echo $text_loading; ?>" /></div>');
			$('#totals').html('<div class="loader" style="text-align:center;"><img src="catalog/view/theme/default/image/ajax_load.gif" alt="<?php echo $text_loading; ?>" /></div>');
			<?php if ($this->config->get('uber_checkout_shipping_update_payment')) { ?>
				$('#payment_methods').html('<div class="loader" style="text-align:center;"><img src="catalog/view/theme/default/image/ajax_load.gif" alt="<?php echo $text_loading; ?>" /></div>');
			<?php } ?>
		},
		success: function(json) {
			<?php if (!$shipping_methods && $this->cart->hasShipping()) { ?>
			$('#payment_div').html('<div class="warning"><?php echo $this->language->get('error_shipping_methods'); ?></div>');
			<?php } else { ?>
			if (json['payment']) {
				$('#payment_div').html(json['payment']);
			}
			<?php } ?>
			if (json['reload'] == '1') {
				location = location.href;
			} else {
				$('#totals').html(json['totals']);
			}			
		},
		complete: function() {
			//updateTotals();
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function updateCouponPaymentTotals() {
	var oldcouponhtml;
	$.ajax({
		type: 'post',
		url: 'index.php?route=checkout/checkout_two/couponPaymentTotals',
		dataType: 'json',
		data: $('input[name=\'coupon\'], input[name=\'payment_method\']:checked, select[name=\'payment_method\']'),
		beforeSend: function() {
			$('#coupon-message').fadeOut('fast');
			$('#voucher-message').fadeOut('fast');
			$('#comment-message').fadeOut('fast');
			$('#payment_div').html('<div class="loader" style="text-align:center;"><img src="catalog/view/theme/default/image/ajax_load.gif" alt="<?php echo $text_loading; ?>" /></div>');
			$('#totals').html('<div class="loader" style="text-align:center;"><img src="catalog/view/theme/default/image/ajax_load.gif" alt="<?php echo $text_loading; ?>" /></div>');
			oldcouponhtml = $('#span_coupon_button').html();
			$('#span_coupon_button').html('<div class="loader right" style="width:82px; margin:5px 30px 0 0;"><img src="catalog/view/theme/default/image/ajax_load_small.gif" alt="<?php echo $text_loading; ?>" /></div>');
		},
		success: function(json) {
			if (json['success_coupon']) {
				$('#coupon-message').removeClass('warning').addClass('success').html(json['success_coupon']).fadeIn('slow');
			} else if (json['fail_coupon']) {
				$('#coupon-message').removeClass('success').addClass('warning').html(json['fail_coupon']).fadeIn('slow');
			}
			<?php if (!$shipping_methods && $this->cart->hasShipping()) { ?>
			$('#payment_div').html('<div class="warning"><?php echo $this->language->get('error_shipping_methods'); ?></div>');
			<?php } else { ?>
			$('#payment_div').html(json['payment']);
			<?php } ?>
			$('#totals').html(json['totals']);
		},
		complete: function() {
			$('#span_coupon_button').html(oldcouponhtml);
			//updateTotals();
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function updateVoucherPaymentTotals() {
	var oldvoucherhtml;
	$.ajax({
		type: 'post',
		url: 'index.php?route=checkout/checkout_two/voucherPaymentTotals',
		dataType: 'json',
		data: $('input[name=\'voucher\'], input[name=\'payment_method\']:checked, select[name=\'payment_method\']'),
		beforeSend: function() {
			$('#coupon-message').fadeOut('fast');
			$('#voucher-message').fadeOut('fast');
			$('#comment-message').fadeOut('fast');
			$('#payment_div').html('<div class="loader" style="text-align:center;"><img src="catalog/view/theme/default/image/ajax_load.gif" alt="<?php echo $text_loading; ?>" /></div>');
			$('#totals').html('<div class="loader" style="text-align:center;"><img src="catalog/view/theme/default/image/ajax_load.gif" alt="<?php echo $text_loading; ?>" /></div>');
			oldvoucherhtml = $('#span_voucher_button').html();
			$('#span_voucher_button').html('<div class="loader right" style="width:82px; margin:5px 30px 0 0;"><img src="catalog/view/theme/default/image/ajax_load_small.gif" alt="<?php echo $text_loading; ?>" /></div>');
		},
		success: function(json) {
			if (json['success_voucher']) {
				$('#voucher-message').removeClass('warning').addClass('success').html(json['success_voucher']).fadeIn('slow');
			} else if (json['fail_voucher']) {
				$('#voucher-message').removeClass('success').addClass('warning').html(json['fail_voucher']).fadeIn('slow');
			}
			<?php if (!$shipping_methods && $this->cart->hasShipping()) { ?>
			$('#payment_div').html('<div class="warning"><?php echo $this->language->get('error_shipping_methods'); ?></div>');
			<?php } else { ?>
			$('#payment_div').html(json['payment']);
			<?php } ?>
			$('#totals').html(json['totals']);
		},
		complete: function() {
			$('#span_voucher_button').html(oldvoucherhtml);
			//updateTotals();
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

$('textarea[name=\'comment\']').blur(function() {
	updateComment();
});

function updateComment() {
	var oldhtml;
	$.ajax({
		type: 'post',
		url: 'index.php?route=checkout/checkout_two/comment',
		dataType: 'json',
		data: $('textarea[name=\'comment\']'),
		beforeSend: function() {
			<?php if ($this->config->get('uber_checkout_comment_update_total')) { ?>
			$('#totals').html('<div class="loader" style="text-align:center;"><img src="catalog/view/theme/default/image/ajax_load.gif" alt="<?php echo $text_loading; ?>" /></div>');
			<?php } ?>
			$('#comment-message').fadeOut('fast');
			oldhtml = $('#span_comment_button').html();
			$('#span_comment_button').html('<div class="loader" style="text-align:right;margin-right:50px;"><img src="catalog/view/theme/default/image/ajax_load_small.gif" alt="<?php echo $text_loading; ?>" /></div>');
		},
		success: function(json) {
			if (json['success']) {
				$('#comment-message').addClass('success').html(json['success']).fadeIn('slow');
			}
		},
		complete: function() {
			$('#span_comment_button').html(oldhtml);
			<?php if ($this->config->get('uber_checkout_comment_update_total')) { ?>
			updateTotals();
			<?php } ?>
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function updateTotals() {
	$.ajax({
		type: 'post',
		url: 'index.php?route=checkout/checkout_two/totals',
		dataType: 'html',
		beforeSend: function() {
			//$('#totals').css('background', '#cccccc');
			$('#totals').html('<div class="loader" style="text-align:center;"><img src="catalog/view/theme/default/image/ajax_load.gif" alt="<?php echo $text_loading; ?>" /></div>');
		},
		success: function(html) {
			$('#totals').css('background', 'inherit');
			$('#totals').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}


$(document).ready(function () {
	updateTotals();
	<?php if (isset($showAddressSelect)) { ?>
	getAddresses();
	<?php } ?>

	//updatePayment();
	<?php if ($text_agree) { ?>
	if ($('input[name=\'agree\']').is(':checked')) {
		jQuery('#agreefake').hide();
		$('#payment_div').fadeIn();
	} else {
		jQuery('#agreefake').show();
		$('#payment_div').fadeOut();
	}

	$('input[name=\'agree\']').live('click', function() {
		if ($(this).is(':checked')) {
			jQuery('#agreefake').hide();
			$('#payment_div').fadeIn();
		} else {
			jQuery('#agreefake').show();
			$('#payment_div').fadeOut();
		}
	});
	<?php } ?>
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
}
//--></script>
<?php if (!$this->config->get('uber_checkout_popup')) { ?>
<?php echo $footer; ?>
<?php } ?>