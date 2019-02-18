<?php echo $header; ?>
<div id="main" role="main">
	<?php echo $content_top; ?>
	<div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
	</div>
	<h1><?php echo $heading_title; ?></h1>
	<div class="checkout">
		<div id="checkout">
			<div class="checkout-heading"><?php echo $text_checkout_option; ?></div>
			<div class="checkout-content"></div>
		</div>
    <?php if (!$logged) { ?>
		<div id="payment-address">
			<div class="checkout-heading"><span><?php echo $text_checkout_account; ?></span></div>
			<div class="checkout-content"></div>
		</div>
    <?php } else { ?>
		<div id="payment-address">
			<div class="checkout-heading"><span><?php echo $text_checkout_payment_address; ?></span></div>
			<div class="checkout-content"></div>
		</div>
    <?php } ?>
    <?php if ($shipping_required) { ?>
		<div id="shipping-address">
			<div class="checkout-heading"><?php echo $text_checkout_shipping_address; ?></div>
			<div class="checkout-content"></div>
		</div>
		<div id="shipping-method">
			<div class="checkout-heading"><?php echo $text_checkout_shipping_method; ?></div>
			<div class="checkout-content"></div>
		</div>
    <?php } ?>
		<div id="payment-method">
			<div class="checkout-heading"><?php echo $text_checkout_payment_method; ?></div>
			<div class="checkout-content"></div>
		</div>
		<div id="confirm">
			<div class="checkout-heading"><?php echo $text_checkout_confirm; ?></div>
			<div class="checkout-content"></div>
		</div>
	</div>
	<?php echo $content_bottom; ?>
</div>
  
<script type="text/javascript"><!--
 <?php /* Turning the live binding into a function that is called when needed. 
 
 Instructions for future functions or adapting to a new version. Wherever you see a live binding it should be wrapped by a function called on ajax success in the appropriate place.
 
 Jqm limitations
 live
 after = (content).insertAfter(tag)
 slideUp = hide()
 slideDown = show();
 value = val()
 attr = sometimes you need removeAttr
 */ ?>

function bindHeader() {
	$('.checkout-heading a').bind('click', function() {
		$('.checkout-content').hide().css("opacity","0")//slideUp
		
		$(this).parent().parent().find('.checkout-content').show().css("opacity","1");//slideDown
		window.location.hash = $(this).parent().parent().attr("id"); //focus
	});
}

<?php if (!$logged) { ?> 
$(document).ready(function() {		
	$.ajax({
		type:"GET",
		url: 'index.php?route=checkout/login',
		dataType: 'html',
		success: function(html) {
			
			$('#checkout .checkout-content').html(html);
				
			$('#checkout .checkout-content').show().css("opacity","1"); //slideDown
			
			window.location.hash = '#checkout'; //focus
			
			$('#guestCheckout').bind('click', function() {
				$('#payment-address .checkout-heading span').html('<?php echo 	$text_checkout_payment_address; ?>');
				checkout('guest');
				return false;
			});
			
			$('#registerAccount').bind('click', function() {
				$('#payment-address .checkout-heading span').html('<?php echo $text_checkout_account; ?>');
				checkout('register');
				return false;
			});
			
			$('#button-login').bind('click', bindButtonLogin);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
});		
<?php } else { ?>
$(document).ready(function() {
	$.ajax({
		url: 'index.php?route=checkout/payment_address',
		dataType: 'html',
		success: function(html) {
			$('#payment-address .checkout-content').html(html);			
			
			window.eval($('#payment-address .checkout-content script').text()); //execute the loaded javascript
			
			bindHeader();
			
			$('#button-payment-address').bind('click', bindButtonPaymentAddress);
			
			$('#payment-address .checkout-content').show().css("opacity","1"); //slideDown
			
			window.location.hash = '#payment-address'; //focus
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
});
<?php } ?>

// Checkout 
function checkout(type) {	
	$.ajax({
		url: 'index.php?route=checkout/' + type,
		dataType: 'html',
		beforeSend: function() {
			//$('#button-account').attr('disabled', true);
			$('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>').insertAfter("h2");
		},		
		complete: function() {
			//$('#button-account').attr('disabled', false);
			$('.wait').remove();
		},			
		success: function(html) {
			$('.warning, .s-error').remove();		
			
			$('#payment-address .checkout-content').html(html);			
			
			window.eval($('#payment-address .checkout-content script').text()); //execute the loaded javascript
				
			$('#checkout .checkout-content').hide().css("opacity","0") //slideUp
				
			$('#payment-address .checkout-content').show().css("opacity","1"); //slideDown
			
			window.location.hash = '#payment-address'; //focus
				
			$('.checkout-heading a').remove();
				
			$('#checkout .checkout-heading').append('<a><?php echo $text_modify; ?></a>');			
			
			bindHeader();
			
			if( type == "guest") {				
				$('#button-guest').bind('click', bindButtonGuest);				
			} else {
				$('#button-register').bind('click', bindButtonRegister);
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
}

// Login
function bindButtonLogin() {
	$.ajax({
		url: 'index.php?route=checkout/login/validate',
		type: 'post',
		data: $('#checkout input'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-login').attr('disabled', true);
			$('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>').insertAfter($('#button-login'));
		},	
		complete: function() {
			$('#button-login').removeAttr('disabled');
			$('.wait').remove();
		},				
		success: function(json) {
			$('.warning, .s-error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				$('#checkout .checkout-content form').prepend('<div id="warning" class="warning" style="display: none;">' + json['error']['warning'] + '</div>');
				
				$('.warning').show().css("opacity","1");
				window.location.hash = '#warning'; //focus
				
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
	return false;
}

// Register
function bindButtonRegister() {
	$.ajax({
		url: 'index.php?route=checkout/register/validate',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'email\'], #payment-address input[type=\'tel\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-register').attr('disabled', true);
			$('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>').insertAfter(
			$('#button-register'));
		},	
		complete: function() {
			$('#button-register').removeAttr('disabled'); 
			$('.wait').remove();
		},			
		success: function(json) {
			$('.warning, .s-error').remove();
						
			if (json['redirect']) {
				location = json['redirect'];				
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#payment-address .checkout-content').prepend('<div id="warning" class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
					$('.warning').show().css("opacity","1");
					window.location.hash = '#warning'; //focus
				}
				
				if (json['error']['firstname']) {					
					$('<span class="s-error">' + json['error']['firstname'] + '</span>').insertAfter($('#payment-address input[name=\'firstname\']'));
				}
				
				if (json['error']['lastname']) {
					$('<span class="s-error">' + json['error']['lastname'] + '</span>').insertAfter($('#payment-address input[name=\'lastname\']'));
				}	
				
				if (json['error']['email']) {
					$('<span class="s-error">' + json['error']['email'] + '</span>').insertAfter($('#payment-address input[name=\'email\']'));
				}
				
				if (json['error']['telephone']) {
					$('<span class="s-error">' + json['error']['telephone'] + '</span>').insertAfter($('#payment-address input[name=\'telephone\']'));
				}	
					
				if (json['error']['company_id']) {
					$('<span class="s-error">' + json['error']['company_id'] + '</span>').insertAfter($('#payment-address input[name=\'company_id\']'));
				}	
				
				if (json['error']['tax_id']) {
					$('<span class="s-error">' + json['error']['tax_id'] + '</span>').insertAfter($('#payment-address input[name=\'tax_id\']'));
				}	
				
				if (json['error']['address_1']) {
					$('<span class="s-error">' + json['error']['address_1'] + '</span>').insertAfter($('#payment-address input[name=\'address_1\']'));
				}	
				
				if (json['error']['city']) {
					$('<span class="s-error">' + json['error']['city'] + '</span>').insertAfter($('#payment-address input[name=\'city\']'));
				}	
				
				if (json['error']['postcode']) {
					$('<span class="s-error">' + json['error']['postcode'] + '</span>').insertAfter($('#payment-address input[name=\'postcode\']'));
				}	
				
				if (json['error']['country']) {
					$('<span class="s-error">' + json['error']['country'] + '</span>').insertAfter($('#payment-address select[name=\'country_id\']'));
				}	
				
				if (json['error']['zone']) {
					$('<span class="s-error">' + json['error']['zone'] + '</span>').insertAfter($('#payment-address select[name=\'zone_id\']'));
				}
				
				if (json['error']['password']) {
					$('<span class="s-error">' + json['error']['password'] + '</span>').insertAfter($('#payment-address input[name=\'password\']'));
				}	
				
				if (json['error']['confirm']) {
					$('<span class="s-error">' + json['error']['confirm'] + '</span>').insertAfter($('#payment-address input[name=\'confirm\']'));
				}		
				window.location.hash = '#payment-address'; //focus				
			} else {
				<?php if ($shipping_required) { ?>				
				var shipping_address = $('#payment-address input[name=\'shipping_address\']:checked').attr('value');				
				
				if (shipping_address) {
					$.ajax({
						url: 'index.php?route=checkout/shipping_method',
						dataType: 'html',
						success: function(html) {
							$('#shipping-method .checkout-content').html(html);
							
							$('#payment-address .checkout-content').hide().css("opacity","0")
							
							$('#shipping-method .checkout-content').show().css("opacity","1");
							
							$('#checkout .checkout-heading a').remove();
							$('#payment-address .checkout-heading a').remove();
							$('#shipping-address .checkout-heading a').remove();
							$('#shipping-method .checkout-heading a').remove();
							$('#payment-method .checkout-heading a').remove();											
							
							$('#shipping-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');									
							$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
							
							bindHeader();
							
							$('#button-shipping-method').bind('click', bindButtonShippingMethod);
							
							window.location.hash = '#shipping-method'; //focus
							
							$.ajax({
								url: 'index.php?route=checkout/shipping_address',
								dataType: 'html',
								success: function(html) {
									$('#shipping-address .checkout-content').html(html);
									
									$('#shipping-address .checkout-content').hide().css("opacity","0")
									
									$('#button-shipping-address').bind('click', bindButtonShippingAddress) ;
								},
								error: function(xhr, ajaxOptions, thrownError) {
									alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
								}
							});	
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});	
				} else {
					$.ajax({
						url: 'index.php?route=checkout/shipping_address',
						dataType: 'html',
						success: function(html) {
							$('#shipping-address .checkout-content').html(html);
							
							$('#payment-address .checkout-content').hide().css("opacity","0")
							
							$('#shipping-address .checkout-content').show().css("opacity","1");
							
							$('#checkout .checkout-heading a').remove();
							$('#payment-address .checkout-heading a').remove();
							$('#shipping-address .checkout-heading a').remove();
							$('#shipping-method .checkout-heading a').remove();
							$('#payment-method .checkout-heading a').remove();							

							$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
							
							bindHeader();
							
							window.eval($('#shipping-address .checkout-content script').text()); //execute the loaded javascript
							
							$('#button-shipping-address').bind('click', bindButtonShippingAddress) ;
							
							window.location.hash = '#shipping-address'; //focus
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});			
				}
				<?php } else { ?>
				$.ajax({
					url: 'index.php?route=checkout/payment_method',
					dataType: 'html',
					success: function(html) {
						$('#payment-method .checkout-content').html(html);
						
						$('#payment-address .checkout-content').hide().css("opacity","0")
						
						$('#payment-method .checkout-content').show().css("opacity","1");
						
						$('#checkout .checkout-heading a').remove();
						$('#payment-address .checkout-heading a').remove();
						$('#payment-method .checkout-heading a').remove();								
						
						$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
						
						bindHeader();
						
						$('#button-payment-method').bind('click', bindButtonPaymentMethod);
						
						window.location.hash = '#payment-method'; //focus
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});					
				<?php } ?>
				
				$.ajax({
					url: 'index.php?route=checkout/payment_address',
					dataType: 'html',
					success: function(html) {
						$('#payment-address .checkout-content').html(html);
							
						$('#payment-address .checkout-heading span').html('<?php echo $text_checkout_payment_address; ?>');
						
						window.eval($('#payment-address .checkout-content script').text()); //execute the loaded javascript
						
						bindHeader();
						
						$('#button-payment-address').bind('click', bindButtonPaymentAddress);						
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}	 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
	return false;	
}

// Payment Address	
function bindButtonPaymentAddress() {
	$.ajax({
		url: 'index.php?route=checkout/payment_address/validate',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-payment-address').attr('disabled', true);
			$('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>').insertAfter(
			$('#button-payment-address'));
		},	
		complete: function() {
			$('#button-payment-address').removeAttr('disabled');
			$('.wait').remove();
		},			
		success: function(json) {
			$('.warning, .s-error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#payment-address .checkout-content').prepend('<div id="warning" class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
					$('.warning').show().css("opacity","1");
					window.location.hash = '#warning'; //focus
				}
								
				if (json['error']['firstname']) {					
					$('<span class="s-error">' + json['error']['firstname'] + '</span>').insertAfter($('#payment-address input[name=\'firstname\']'));
				}
				
				if (json['error']['lastname']) {
					$('<span class="s-error">' + json['error']['lastname'] + '</span>').insertAfter($('#payment-address input[name=\'lastname\']'));
				}	
				
				if (json['error']['telephone']) {
					$('<span class="s-error">' + json['error']['telephone'] + '</span>').insertAfter($('#payment-address input[name=\'telephone\']'));
				}	
					
				if (json['error']['company_id']) {
					$('<span class="s-error">' + json['error']['company_id'] + '</span>').insertAfter($('#payment-address input[name=\'company_id\']'));
				}	
				
				if (json['error']['tax_id']) {
					$('<span class="s-error">' + json['error']['tax_id'] + '</span>').insertAfter($('#payment-address input[name=\'tax_id\']'));
				}	
				
				if (json['error']['address_1']) {
					$('<span class="s-error">' + json['error']['address_1'] + '</span>').insertAfter($('#payment-address input[name=\'address_1\']'));
				}	
				
				if (json['error']['city']) {
					$('<span class="s-error">' + json['error']['city'] + '</span>').insertAfter($('#payment-address input[name=\'city\']'));
				}	
				
				if (json['error']['postcode']) {
					$('<span class="s-error">' + json['error']['postcode'] + '</span>').insertAfter($('#payment-address input[name=\'postcode\']'));
				}	
				
				if (json['error']['country']) {
					$('<span class="s-error">' + json['error']['country'] + '</span>').insertAfter($('#payment-address select[name=\'country_id\']'));
				}	
				
				if (json['error']['zone']) {
					$('<span class="s-error">' + json['error']['zone'] + '</span>').insertAfter($('#payment-address select[name=\'zone_id\']'));
				}
			} else {
				<?php if ($shipping_required) { ?>
				$.ajax({
					url: 'index.php?route=checkout/shipping_address',
					dataType: 'html',
					success: function(html) {
						$('#shipping-address .checkout-content').html(html);
					
						$('#payment-address .checkout-content').hide().css("opacity","0")
						
						$('#shipping-address .checkout-content').show().css("opacity","1");						
						
						$('#payment-address .checkout-heading a').remove();
						$('#shipping-address .checkout-heading a').remove();
						$('#shipping-method .checkout-heading a').remove();
						$('#payment-method .checkout-heading a').remove();
						
						$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
						
						window.eval($('#shipping-address .checkout-content script').text()); //execute the loaded javascript
			
						bindHeader();
			
						$('#button-shipping-address').bind('click', bindButtonShippingAddress);
						
						window.location.hash = '#shipping-address'; //focus						
						
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
				<?php } else { ?>
				$.ajax({
					url: 'index.php?route=checkout/payment_method',
					dataType: 'html',
					success: function(html) {
						$('#payment-method .checkout-content').html(html);
					
						$('#payment-address .checkout-content').hide().css("opacity","0");
						
						$('#payment-method .checkout-content').show().css("opacity","1");
						
						$('#payment-address .checkout-heading a').remove();
						$('#payment-method .checkout-heading a').remove();
													
						$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
						
						bindHeader();
						
						$('#button-payment-method').bind('click', bindButtonPaymentMethod);
						
						window.location.hash = '#payment-method'; //focus
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});	
				<?php } ?>
				
				$.ajax({
					url: 'index.php?route=checkout/payment_address',
					dataType: 'html',
					success: function(html) {
						$('#payment-address .checkout-content').html(html);
						
						window.eval($('#payment-address .checkout-content script').text()); //execute the loaded javascript
									
						bindHeader();
									
						$('#button-payment-address').bind('click', bindButtonPaymentAddress);						
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});					
			}	  
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
	return false;
}

// Shipping Address			
function bindButtonShippingAddress() {
	$.ajax({
		url: 'index.php?route=checkout/shipping_address/validate',
		type: 'post',
		data: $('#shipping-address input[type=\'text\'], #shipping-address input[type=\'checkbox\']:checked, #shipping-address input[type=\'radio\']:checked, #shipping-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping-address').attr('disabled', true);
			$('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>').insertAfter($('#button-shipping-address'));
		},	
		complete: function() {
			$('#button-shipping-address').removeAttr('disabled');
			$('.wait').remove();
		},			
		success: function(json) {
			$('.warning, .s-error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#shipping-address .checkout-content').prepend('<div id="warning" class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
					$('.warning').show().css("opacity","1");
					window.location.hash = '#warning'; //focus
				}
								
				if (json['error']['firstname']) {					
					$('<span class="s-error">' + json['error']['firstname'] + '</span>').insertAfter($('#shipping-address input[name=\'firstname\']'));
				}
				
				if (json['error']['lastname']) {
					$('<span class="s-error">' + json['error']['lastname'] + '</span>').insertAfter($('#shipping-address input[name=\'lastname\']'));
				}	
				
				if (json['error']['telephone']) {
					$('<span class="s-error">' + json['error']['telephone'] + '</span>').insertAfter($('#shipping-address input[name=\'telephone\']'));
				}	
					
				if (json['error']['company_id']) {
					$('<span class="s-error">' + json['error']['company_id'] + '</span>').insertAfter($('#shipping-address input[name=\'company_id\']'));
				}	
				
				if (json['error']['tax_id']) {
					$('<span class="s-error">' + json['error']['tax_id'] + '</span>').insertAfter($('#shipping-address input[name=\'tax_id\']'));
				}	
				
				if (json['error']['address_1']) {
					$('<span class="s-error">' + json['error']['address_1'] + '</span>').insertAfter($('#shipping-address input[name=\'address_1\']'));
				}	
				
				if (json['error']['city']) {
					$('<span class="s-error">' + json['error']['city'] + '</span>').insertAfter($('#shipping-address input[name=\'city\']'));
				}	
				
				if (json['error']['postcode']) {
					$('<span class="s-error">' + json['error']['postcode'] + '</span>').insertAfter($('#shipping-address input[name=\'postcode\']'));
				}	
				
				if (json['error']['country']) {
					$('<span class="s-error">' + json['error']['country'] + '</span>').insertAfter($('#shipping-address select[name=\'country_id\']'));
				}	
				
				if (json['error']['zone']) {
					$('<span class="s-error">' + json['error']['zone'] + '</span>').insertAfter($('#shipping-address select[name=\'zone_id\']'));
				}
			} else {
				$.ajax({
					url: 'index.php?route=checkout/shipping_method',
					dataType: 'html',
					success: function(html) {
						$('#shipping-method .checkout-content').html(html);
						
						$('#shipping-address .checkout-content').hide().css("opacity","0")
						
						$('#shipping-method .checkout-content').show().css("opacity","1");
						
						$('#shipping-address .checkout-heading a').remove();
						$('#shipping-method .checkout-heading a').remove();
						$('#payment-method .checkout-heading a').remove();
						
						$('#shipping-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');							
						
						bindHeader();
						
						$('#button-shipping-method').bind('click', bindButtonShippingMethod);
						
						window.location.hash = '#shipping-method'; //focus
						
						$.ajax({
							url: 'index.php?route=checkout/shipping_address',
							dataType: 'html',
							success: function(html) {
								$('#shipping-address .checkout-content').html(html);
								$('#shipping-address .checkout-content').hide().css("opacity","0")
								window.eval($('#shipping-address .checkout-content script').text()); //execute the loaded javascript
								$('#button-shipping-address').bind('click', bindButtonShippingAddress) ;
							},
							error: function(xhr, ajaxOptions, thrownError) {
								alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							}
						});						
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});	
			}  
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
	return false;
}

// Guest
function bindButtonGuest() {
	console.log("#Button-guest clicked");
	$.ajax({
		url: 'index.php?route=checkout/guest/validate',
		type: 'post',
		data: $('#payment-address form input, #payment-address input[type=\'checkbox\']:checked, #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-guest').attr('disabled', 'true');
			$('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>').insertAfter($('#button-guest'));			
		},	
		complete: function() {
			$('#button-guest').removeAttr('disabled'); 
			$('.wait').remove();
		},			
		success: function(json) {
			$('.warning, .s-error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#payment-address .checkout-content').prepend('<div id="warning" class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
					$('.warning').show().css("opacity","1");
					window.location.hash = '#warning'; //focus
				}
								
				if (json['error']['firstname']) {					
					$('<span class="s-error">' + json['error']['firstname'] + '</span>').insertAfter($('#payment-address input[name=\'firstname\']'));
				}
				
				if (json['error']['lastname']) {
					$('<span class="s-error">' + json['error']['lastname'] + '</span>').insertAfter($('#payment-address input[name=\'lastname\']'));
				}	
				
				if (json['error']['email']) {
					$('<span class="s-error">' + json['error']['email'] + '</span>').insertAfter($('#payment-address input[name=\'email\']'));
				}
				
				if (json['error']['telephone']) {
					$('<span class="s-error">' + json['error']['telephone'] + '</span>').insertAfter($('#payment-address input[name=\'telephone\']'));
				}	
					
				if (json['error']['company_id']) {
					$('<span class="s-error">' + json['error']['company_id'] + '</span>').insertAfter($('#payment-address input[name=\'company_id\']'));
				}	
				
				if (json['error']['tax_id']) {
					$('<span class="s-error">' + json['error']['tax_id'] + '</span>').insertAfter($('#payment-address input[name=\'tax_id\']'));
				}	
				
				if (json['error']['address_1']) {
					$('<span class="s-error">' + json['error']['address_1'] + '</span>').insertAfter($('#payment-address input[name=\'address_1\']'));
				}	
				
				if (json['error']['city']) {
					$('<span class="s-error">' + json['error']['city'] + '</span>').insertAfter($('#payment-address input[name=\'city\']'));
				}	
				
				if (json['error']['postcode']) {
					$('<span class="s-error">' + json['error']['postcode'] + '</span>').insertAfter($('#payment-address input[name=\'postcode\']'));
				}	
				
				if (json['error']['country']) {
					$('<span class="s-error">' + json['error']['country'] + '</span>').insertAfter($('#payment-address select[name=\'country_id\']'));
				}	
				
				if (json['error']['zone']) {
					$('<span class="s-error">' + json['error']['zone'] + '</span>').insertAfter($('#payment-address select[name=\'zone_id\']'));
				}
			} else {
				<?php if ($shipping_required) { ?>	
				var shipping_address = $('#payment-address input[name=\'shipping_address\']:checked').attr('value');
				
				if (shipping_address) {
					$.ajax({
						url: 'index.php?route=checkout/shipping_method',
						dataType: 'html',
						success: function(html) {
							$('#shipping-method .checkout-content').html(html);							
							
							$('#payment-address .checkout-content').hide().css("opacity","0")
							
							$('#shipping-method .checkout-content').show().css("opacity","1");
							
							$('#payment-address .checkout-heading a').remove();
							$('#shipping-address .checkout-heading a').remove();
							$('#shipping-method .checkout-heading a').remove();
							$('#payment-method .checkout-heading a').remove();		
															
							$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
							$('#shipping-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');									
							
							bindHeader();
							
							$('#button-shipping-method').bind('click', bindButtonShippingMethod);
							
							window.location.hash = '#shipping-method'; //focus
							
							$.ajax({
								url: 'index.php?route=checkout/guest_shipping',
								dataType: 'html',
								success: function(html) {
									$('#shipping-address .checkout-content').html(html);
									$('#shipping-address .checkout-content').hide().css("opacity","0")
									
									window.eval($('#shipping-address .checkout-content script').text()); //execute the loaded javascript
									bindHeader();
									
									$('#button-guest-shipping').bind('click', bindButtonGuestShipping);
								},
								error: function(xhr, ajaxOptions, thrownError) {
									alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
								}
							});
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});					
				} else {
					$.ajax({
						url: 'index.php?route=checkout/guest_shipping',
						dataType: 'html',
						success: function(html) {
							$('#shipping-address .checkout-content').html(html);
							
							window.eval($('#shipping-address .checkout-content script').text()); //execute the loaded javascript
							
							$('#payment-address .checkout-content').hide().css("opacity","0")
							
							$('#shipping-address .checkout-content').show().css("opacity","1");
							
							$('#payment-address .checkout-heading a').remove();
							$('#shipping-address .checkout-heading a').remove();
							$('#shipping-method .checkout-heading a').remove();
							$('#payment-method .checkout-heading a').remove();
							
							$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
							
							bindHeader();
							
							$('#button-guest-shipping').bind('click', bindButtonGuestShipping);
							
							window.location.hash = '#shipping-address'; //focus
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				}
				<?php } else { ?>				
				$.ajax({
					url: 'index.php?route=checkout/payment_method',
					dataType: 'html',
					success: function(html) {
						$('#payment-method .checkout-content').html(html);
						
						$('#payment-address .checkout-content').hide().css("opacity","0")
							
						$('#payment-method .checkout-content').show().css("opacity","1");
							
						$('#payment-address .checkout-heading a').remove();
						$('#payment-method .checkout-heading a').remove();
														
						$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
						
						bindHeader();
						
						$('#button-payment-method').bind('click', bindButtonPaymentMethod);
						
						window.location.hash = '#payment-method'; //focus
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});				
				<?php } ?>
			}	 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	return false;
}

// Guest Shipping
function bindButtonGuestShipping() {
	console.log("#Button-guest-shipping clicked");
	$.ajax({
		url: 'index.php?route=checkout/guest_shipping/validate',
		type: 'post',
		data: $('#shipping-address input, #shipping-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-guest-shipping').attr('disabled', true);
			$('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>').insertAfter($('#button-guest-shipping'));
		},	
		complete: function() {
			$('#button-guest-shipping').removeAttr('disabled'); 
			$('.wait').remove();
		},			
		success: function(json) {
			$('.warning, .s-error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#shipping-address .checkout-content').prepend('<div id="warning" class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
					$('.warning').show().css("opacity","1");
					window.location.hash = '#warning'; //focus
				}
								
				if (json['error']['firstname']) {					
					$('<span class="s-error">' + json['error']['firstname'] + '</span>').insertAfter($('#shipping-address input[name=\'firstname\']'));
				}
				
				if (json['error']['lastname']) {
					$('<span class="s-error">' + json['error']['lastname'] + '</span>').insertAfter($('#shipping-address input[name=\'lastname\']'));
				}	
										
				if (json['error']['address_1']) {
					$('<span class="s-error">' + json['error']['address_1'] + '</span>').insertAfter($('#shipping-address input[name=\'address_1\']'));
				}	
				
				if (json['error']['city']) {
					$('<span class="s-error">' + json['error']['city'] + '</span>').insertAfter($('#shipping-address input[name=\'city\']'));
				}	
				
				if (json['error']['postcode']) {
					$('<span class="s-error">' + json['error']['postcode'] + '</span>').insertAfter($('#shipping-address input[name=\'postcode\']'));
				}	
				
				if (json['error']['country']) {
					$('<span class="s-error">' + json['error']['country'] + '</span>').insertAfter($('#shipping-address select[name=\'country_id\']'));
				}	
				
				if (json['error']['zone']) {
					$('<span class="s-error">' + json['error']['zone'] + '</span>').insertAfter($('#shipping-address select[name=\'zone_id\']'));
				}
			} else {
				$.ajax({
					url: 'index.php?route=checkout/shipping_method',
					dataType: 'html',
					success: function(html) {
						$('#shipping-method .checkout-content').html(html);
						
						$('#shipping-address .checkout-content').hide().css("opacity","0")
						
						$('#shipping-method .checkout-content').show().css("opacity","1");
						
						$('#shipping-address .checkout-heading a').remove();
						$('#shipping-method .checkout-heading a').remove();
						$('#payment-method .checkout-heading a').remove();
							
						$('#shipping-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
						
						bindHeader();
						
						$('#button-shipping-method').bind('click', bindButtonShippingMethod);
						
						window.location.hash = '#shipping-method'; //focus
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});				
			}	 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
	return false;
}

function bindButtonShippingMethod() {
	$.ajax({
		url: 'index.php?route=checkout/shipping_method/validate',
		type: 'post',
		data: $('#shipping-method input[type=\'radio\']:checked, #shipping-method textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping-method').attr('disabled', true);
			$('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>').insertAfter($('#button-shipping-method'));
		},	
		complete: function() {
			$('#button-shipping-method').removeAttr('disabled');
			$('.wait').remove();
		},			
		success: function(json) {
			$('.warning, .s-error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#shipping-method .checkout-content').prepend('<div id="warning" class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
					$('.warning').show().css("opacity","1");
					window.location.hash = '#warning'; //focus
				}			
			} else {
				$.ajax({
					url: 'index.php?route=checkout/payment_method',
					dataType: 'html',
					success: function(html) {
						$('#payment-method .checkout-content').html(html);
						
						$('#shipping-method .checkout-content').hide().css("opacity","0")
						
						$('#payment-method .checkout-content').show().css("opacity","1");

						$('#shipping-method .checkout-heading a').remove();
						$('#payment-method .checkout-heading a').remove();
						
						$('#shipping-method .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
						
						bindHeader();
						
						$('#button-payment-method').bind('click', bindButtonPaymentMethod);
						
						window.location.hash = '#payment-method'; //focus
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});					
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
	return false;
}

function bindButtonPaymentMethod() {
	$.ajax({
		url: 'index.php?route=checkout/payment_method/validate', 
		type: 'post',
		data: $('#payment-method input[type=\'radio\']:checked, #payment-method input[type=\'checkbox\']:checked, #payment-method textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-payment-method').attr('disabled', true);
			$('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>').insertAfter($('#button-payment-method'));
		},	
		complete: function() {
			$('#button-payment-method').removeAttr('disabled');
			$('.wait').remove();
		},			
		success: function(json) {
			$('.warning, .s-error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#payment-method .checkout-content').prepend('<div id="warning" class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
					$('.warning').show();
					window.location.hash = '#warning'; //focus
				}			
			} else {
				$.ajax({
					url: 'index.php?route=checkout/confirm',
					dataType: 'html',
					success: function(html) {
						$('#confirm .checkout-content').html(html);
						
						$('#payment-method .checkout-content').hide().css("opacity","0")
						
						$('#confirm .checkout-content').show().css("opacity","1");
						
						$('#payment-method .checkout-heading a').remove();
						
						$('#payment-method .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
						
						bindHeader();
						
						window.eval($('#confirm .checkout-content script').text()); //execute the loaded javascript
						
						window.location.hash = '#confirm'; //focus
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});					
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
	return false;
}
//--></script> 
<?php echo $footer; ?>