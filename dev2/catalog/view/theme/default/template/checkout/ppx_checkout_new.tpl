<?php if (defined('VERSION') && (version_compare(VERSION, '1.5.2', '<') == true)) { ?>

<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
  <div class="checkout">
    <?php if ($shipping_required) { ?>
    
    <div id="shipping-method">
      <div class="checkout-heading"><?php echo $text_checkout_shipping_method; ?></div>
      <div class="checkout-content"></div>
    </div>
    <?php } ?>
    
    <div id="confirm">
	  <!-- Coupon -->
	  <?php if ($this->config->get('coupon_status')) { ?>
      <div class="content">
        <div style="text-align: right;"><?php echo $entry_coupon; ?>&nbsp;
          <input type="text" name="coupon" value="" />&nbsp;
          <span id="span_coupon_button"><a onclick="updateCoupon();" class="button"><span><?php echo $button_coupon; ?></span></a></span>
          <p id="coupon-message" style="display:none;"></p>
        </div>
      </div>
      <?php } ?>
	  <!-- Coupon -->
      <div class="checkout-heading"><?php echo $text_checkout_confirm; ?></div>
      <div class="checkout-content"></div>
    </div>
  </div>
  <?php //echo nl2br(print_r($_SESSION, 1)); ?>
  <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--

$(document).ready(function() {

	$('.checkout-heading a').live('click', function() {
		$('.checkout-content').slideUp('slow');

		$(this).parent().parent().find('.checkout-content').slideDown('slow');
	});

	<?php if ($shipping_required) { ?>

	$.ajax({
		url: 'index.php?route=checkout/shipping',
		dataType: 'json',
		success: function(json) {
			if (json['redirect']) {
				//location = json['redirect'];
				location = 'index.php?route=payment/paypal_express/SetExpressCheckout&resetppx';
			}

			if (json['output']) {

				$('#shipping-method .checkout-content').html(json['output']);

				<?php if (!isset($this->session->data['shipping_method'])) { ?>
					$('#shipping-method .checkout-content').slideDown('slow');

					$('#shipping-method .checkout-heading a').remove();
				<?php } else { ?>
					$('#button-shipping').click();
				<?php } ?>

			}

			<?php if (isset($this->session->data['guest'])) { ?>

				$.ajax({
					url: 'index.php?route=checkout/guest/shipping',
					dataType: 'json',
					success: function(json) {
						if (json['redirect']) {
							
							location = json['redirect'];
						}

						if (json['output']) {
							$('#shipping-address .checkout-content').html(json['output']);
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert('(A) - ' + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});

			<?php } else { ?>

				$.ajax({
					url: 'index.php?route=checkout/address/shipping',
					dataType: 'json',
					success: function(json) {
						if (json['redirect']) {
							
							location = json['redirect'];
						}

						if (json['output']) {
							$('#shipping-address .checkout-content').html(json['output']);
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						//alert('(B) - ' + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});

			<?php } ?>
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert('(C) - ' + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	<?php } else { ?>
	$.ajax({
		url: 'index.php?route=checkout/confirm',
		dataType: 'json',
		success: function(json) {
			if (json['redirect']) {
				
				location = json['redirect'];
			}

			if (json['output']) {
				$('#confirm .checkout-content').html(json['output']);

				$('#shipping-method .checkout-content').slideUp('slow');

				$('#confirm .checkout-content').slideDown('slow');

				$('#shipping-method .checkout-heading a').remove();

				$('#shipping-method .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert('(D) - ' + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	<?php } ?>

});

// Shoppica fix
$('#button-shipping-method').live('click', function() {
	$('#button-shipping').click();
});//

$('#button-shipping').live('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/shipping',
		type: 'post',
		data: $('#shipping-method input[type=\'radio\']:checked, #shipping-method textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping').attr('disabled', true);
			$('#button-shipping').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('#button-shipping').attr('disabled', false);
			$('.wait').remove();
		},
		success: function(json) {
			$('.warning').remove();

			if (json['redirect']) {
				
				location = json['redirect'];
			}

			if (json['error']) {
				if (json['error']['warning']) {
					$('#shipping-method .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');

					$('.warning').fadeIn('slow');
				}
			} else {
				$.ajax({
					url: 'index.php?route=checkout/confirm',
					dataType: 'json',
					success: function(json) {
						if (json['redirect']) {
							
							location = json['redirect'];
						}

						if (json['output']) {
							$('#confirm .checkout-content').html(json['output']);

							$('#shipping-method .checkout-content').slideUp('slow');

							$('#confirm .checkout-content').slideDown('slow');

							$('#shipping-method .checkout-heading a').remove();

							$('#shipping-method .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert('(E) - ' + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert('(F) - ' + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});
//--></script>
<?php echo $footer; ?>


<?php } else { //v1.5.2+ ?>


<?php echo $header;  ?><?php //echo $column_left; ?><?php echo $column_right; ?>
<style>
.tooltip-mark {
    background: #FF8;
    border: 1px solid #888;
    border-radius: 10px;
    color: #000;
    font-size: 10px;
    padding: 1px 4px;
  }
  .signPro {
  	width: 10px;
  	height: 10px;
  	border: 1px solid #ccc;
  	display: block;
  }
  .signProS {
  	background: #000;
  }
  .signProR {
  	background: #fff;
  }
  .tooltip {
    white-space: normal;
    background: #FFC;
    border: 1px solid #CCC;
    color: #000;
    display: none;
    font-size: 11px;
    font-weight: normal;
    line-height: 1.3;
    max-width: 300px;
    padding: 10px;
    position: absolute;
    text-align: left;
    z-index: 100;
  }
  .tooltip-mark:hover, .tooltip-mark:hover + .tooltip, .tooltip:hover {
    display: inline;
    cursor: help;
  }
  .tooltip, .ui-dialog {
    box-shadow: 0 6px 9px #AAA;
    -moz-box-shadow: 0 6px 9px #AAA;
    -webkit-box-shadow: 0 6px 9px #AAA;
  }
</style>
<div id="content"><?php //echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
  <?php
  
  ?>
 
  
  <div class="checkout">
    <?php if ($shipping_required) { ?>
    <div id="shipping-address">
      <div class="checkout-heading">Delivery Details</div>
      <div class="checkout-content"></div>
    </div>
    <div id="shipping-method">
      <div class="checkout-heading"><?php echo $text_checkout_shipping_method; ?></div>
      <div class="checkout-content"></div>
    </div>
    <?php } ?>
   
    
   
    <div id="confirm">
      <div class="checkout-heading"><?php echo $text_checkout_confirm; ?></div>
      <div class="checkout-content"></div>
    </div>
  </div>
  <?php //echo nl2br(print_r($_SESSION, 1)); ?>
  <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--

$(document).ready(function() {

	$('.checkout-heading a').live('click', function() {
		$('.checkout-content').slideUp('slow');

		$(this).parent().parent().find('.checkout-content').slideDown('slow');
	});
	
	
	BillingDetails();
	
	
	
	});
	
	function ShippingMethod()
	{

	<?php if ($shipping_required) { ?>

	$.ajax({
		url: 'index.php?route=checkout/shipping_method',
		dataType: 'html',
		success: function(html) {
			$('#shipping-method .checkout-content').html(html);

			<?php //if (!isset($this->session->data['shipping_method'])) { 
			?>
				$('#shipping-address .checkout-content').slideUp('slow');
				$('#shipping-method .checkout-content').slideDown('slow');

				$('#shipping-method .checkout-heading a').remove();
				
				
				$('#shipping-address .checkout-heading a').remove();

						$('#shipping-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
						
			<?php //} else {
				 ?>
				//$('#button-shipping-method').click();
			<?php //} 
			?>

			
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert('(C) - ' + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	<?php } else { ?>

	$.ajax({
		url: 'index.php?route=checkout/confirm',
		dataType: 'html',
		success: function(html) {
			$('#confirm .checkout-content').html(html);

			$('#shipping-method .checkout-content').slideUp('slow');

			$('#confirm .checkout-content').slideDown('slow');

			$('#shipping-method .checkout-heading a').remove();

			$('#shipping-method .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert('(D) - ' + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	<?php } ?>
	
	}



// Shoppica fix
$('#button-shipping-method').live('click', function() {
	$('#button-shipping').click();
});//

function BillingDetails(){

$.ajax({
		url: 'index.php?route=checkout/shipping_address',
		dataType: 'html',
		success: function(html) {
			$('#shipping-address .checkout-content').html(html);
			$('#shipping-method .checkout-heading a').remove();
					$('#shipping-method .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
					$('#shipping-method .checkout-content').slideUp('slow');
			$('#shipping-address .checkout-content').slideDown('slow');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
/*
	$.ajax({
		url: 'index.php?route=checkout/shipping_method/validate',
		type: 'post',
		data: $('#shipping-method input[type=\'radio\']:checked, #shipping-method textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping').attr('disabled', true);
			$('#button-shipping').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('#button-shipping').attr('disabled', false);
			$('.wait').remove();
		},
		success: function(json) {
			$('.warning').remove();

			if (json['redirect']) {
				location = json['redirect'];
			}

			if (json['error']) {
				if (json['error']['warning']) {
					$('#shipping-method .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');

					$('.warning').fadeIn('slow');
				}
			} else {
				$.ajax({
					url: 'index.php?route=checkout/confirm',
					dataType: 'html',
					success: function(html) {
						$('#confirm .checkout-content').html(html);

						$('#shipping-method .checkout-content').slideUp('slow');

						$('#confirm .checkout-content').slideDown('slow');

						$('#shipping-method .checkout-heading a').remove();

						$('#shipping-method .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert('(E) - ' + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert('(F) - ' + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});*/
}

$('#button-shipping-address').live('click', function() {
	
	<?php if (isset($this->session->data['guest']['shipping']['firstname'] )) { ?>
	
	$.ajax({
		url: 'index.php?route=checkout/guest_shipping/validate',
		type: 'post',
		data: $('#shipping-address input[type=\'text\'], #shipping-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping-address').attr('disabled', true);
			$('#button-shipping-address').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('#button-shipping-address').attr('disabled', false); 
			$('.wait').remove();
		},			
		success: function(json) {
			$('.warning, .error').remove();
			
			if (json['redirect']) {
				alert(json['redirect']);return false;
				location = json['redirect'];
			} else if (json['error']) {$('#shipping-method .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');

					$('.warning').fadeIn('slow');} else {
				ShippingMethod();
				}	 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
	<?php }
	else
	{
	?>	
		$.ajax({
		url: 'index.php?route=checkout/shipping_address/validate',
		type: 'post',
		data: $('#shipping-address input[type=\'text\'], #shipping-address input[type=\'password\'], #shipping-address input[type=\'checkbox\']:checked, #shipping-address input[type=\'radio\']:checked, #shipping-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping-address').attr('disabled', true);
			$('#button-shipping-address').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('#button-shipping-address').attr('disabled', false);
			$('.wait').remove();
		},			
			
		success: function(json) {
			$('.warning, .error').remove();
			
			if (json['redirect']) {
				alert(json['redirect']);
				//location = json['redirect'];
			} else if (json['error']) {$('#shipping-address .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');

					$('.warning').fadeIn('slow');} else {
				ShippingMethod()
				}	  
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
		<?php
	}
	
	 ?>

});
$('#button-shipping-method').live('click', function() {



	$.ajax({
		url: 'index.php?route=checkout/shipping_method/validate',
		type: 'post',
		data: $('#shipping-method input[type=\'radio\']:checked, #shipping-method textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping').attr('disabled', true);
			$('#button-shipping').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('#button-shipping').attr('disabled', false);
			$('.wait').remove();
		},
		success: function(json) {
			
			$('.warning').remove();

			if (json['redirect']) {
				//alert(json['redirect']);return false;
				//location = json['redirect'];
			}

			if (json['error']) {
				if (json['error']['warning']) {
					$('#shipping-method .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');

					$('.warning').fadeIn('slow');
				}
			} else {
				$.ajax({
					url: 'index.php?route=checkout/confirm',
					dataType: 'html',
					success: function(html) {
						$('#confirm .checkout-content').html(html);

						$('#shipping-method .checkout-content').slideUp('slow');

						$('#confirm .checkout-content').slideDown('slow');

						$('#shipping-method .checkout-heading a').remove();

						$('#shipping-method .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert('(E) - ' + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert('(F) - ' + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

//--></script>
<?php echo $footer; ?>

<?php } ?>

<script type="text/javascript"><!--

function updateCoupon() {
	var oldcouponhtml;
	$.ajax({
		type: 'post',
		url: 'index.php?route=checkout/ppx_checkout_new/coupon',
		dataType: 'json',
		data: $('input[name=\'coupon\']'),
		beforeSend: function() {
			$('#coupon-message').fadeOut('fast');
			oldcouponhtml = $('#span_coupon_button').html();
			$('#span_coupon_button').html('<div class="loader right" style="width:82px; margin:5px 30px 0 0;"><img src="catalog/view/theme/default/image/ajax_load_small.gif" alt="" /></div>');
		},
		success: function(json) {
			if (json['success_coupon']) {
				$('#coupon-message').removeClass('warning').addClass('success').html(json['success_coupon']).fadeIn('slow');
				location.reload();
			} else if (json['fail_coupon']) {
				$('#coupon-message').removeClass('success').addClass('warning').html(json['fail_coupon']).fadeIn('slow');
			}
		},
		complete: function() {
			$('#span_coupon_button').html(oldcouponhtml);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}
//--></script>