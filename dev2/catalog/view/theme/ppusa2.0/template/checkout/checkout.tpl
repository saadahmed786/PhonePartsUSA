
<?php echo $header; ?>
<style>
	.nav-tabs > li.active > a:before,
	.nav-tabs > li:hover > a:before{width: 0px !important;}
	.nav-tabs > li.active > a:after,
	.nav-tabs > li:hover > a:after{width: 0px !important;}
	.nav-tabs {border-left:none !important;}
	.form-group{margin-bottom:12px !important;}
	.form-title{margin-bottom:5px !important;}
</style>
<div id="cart_overlay" style="position:fixed;height:200%;margin-top:-15%;margin-left:0px;">
  <img src="catalog/view/theme/default/image/loader_white.gif" style="width:115px;margin-left:40%;margin-top:20%" 
    id="cart-img-load" />
</div>

<div id="content">

	<!-- <div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<a <?php echo(($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''); ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
-->


<main class="main">
	<div class="container payment-3-page">
	<div class="col-md-9 wizard hidden-xs hidden-sm">
	<div class="wizard-inner">
		<div class="connecting-line"></div>
		<div class="connecting-line"></div>
		<ul class="nav nav-tabs" role="tablist">

			<li role="presentation" class="active" id="checkout">
				<a href="#contactInfo" data-toggle="tab" aria-controls="contactInfo" role="tab" title="Contact Information">
					<span class="round-tab">
						<i class="glyphicon glyphicon-user"></i>
					</span>
				</a>
			</li>

			<li id="shippingTab" role="presentation" class="disabled">
				<a href="#shippingInfo" data-toggle="tab" aria-controls="shippingInfo" role="tab" title="Shipping Information">
					<span class="round-tab">
						<i class="glyphicon glyphicon-map-marker"></i>
					</span>
				</a>
			</li>
			<li id="paymentTab" role="presentation" class="disabled" <?php if (isset($this->session->data['ppx']['token'])) { echo 'style="display:none"'; } ?> >
				<a href="#paymentMethod" data-toggle="tab" aria-controls="paymentMethod" role="tab" title="Payment Method">
					<span class="round-tab">
						<i class="glyphicon glyphicon-credit-card"></i>
					</span>
				</a>
			</li>

			<li role="presentation" class="disabled">
				<a href="#confirmOrder" data-toggle="tab" aria-controls="confirmOrder" role="tab" title="Confirmation">
					<span class="round-tab">
						<i class="glyphicon glyphicon-ok"></i>
					</span>
				</a>
			</li>
		</ul>


	</div>


</div>
		
		<div class="row">
			<div class="col-md-9">
					<!-- <ul class="nav nav-tabs small">

						<li id="checkout" class="active"> <a id="link0" href="<?php echo $_SERVER['REQUEST_URI']; ?>#contactInfo">1.Contact Information </a></li>
						<li id="shippingTab" ><a id="link1" href="<?php echo $_SERVER['REQUEST_URI']; ?>#shippingInfo" >2.Shipping Information </a></li>
						<li id="paymentTab"><a id="link2" href="<?php echo $_SERVER['REQUEST_URI']; ?>#paymentMethod">3.Payment Method</a></li>
						<li><a id="link3" href="<?php echo $_SERVER['REQUEST_URI']; ?>#confirmOrder">4.Confirm Order</a></li>
					</ul> -->
					<div class="alert alert-danger alert-dismissible" style="display:none" role="alert"><?php echo $error_warning; ?></div>
					<div class="tab-content">
						<div id="contactInfo" class="tab-pane fade in active" role="tabpanel">
						</div>
						<div id="shippingInfo" class="tab-pane fade" role="tabpanel">
						</div>
						<div id="paymentMethod" class="tab-pane fade" role="tabpanel">
						</div>
						<div id="confirmOrder" class="tab-pane fade" role="tabpanel">
						</div>
					</div>
				</div>
				<div class="col-md-3" id="checkout_right_cart">
					<?php echo $checkout_right_cart;?>
				</div>
				<select name="country_id" style="display:none">
					<option value="">Select Country</option>
					<?php foreach ($countries as $country) { ?>
					<?php if ($country['country_id'] == $country_id) { ?>
					<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
					<?php } else { ?>
					<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
					<?php } ?>
					<?php } ?>
				</select>
				<select name="zone_id" id="zone_id" style="display:none">
				</select>
			</div>
		</div>
	</main><!-- @End of main -->
</div>
<br><br>

<?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--

	$(document).ready(function() {

<?php
	if(isset($this->session->data['checkout_error_email']))
	{
		unset($this->session->data['checkout_error_email']);
		?>
		$('.alert-danger').html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Login failed: Username / password mismatch.');
		$('.alert-danger').show();
<?php
	}
	elseif(isset($this->session->data['checkout_error_password']))
	{
		unset($this->session->data['checkout_error_password']);
		?>
		$('.alert-danger').html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Login failed: Username / password mismatch.');
		$('.alert-danger').show();
<?php
	}
?>

		$.ajax({
			<?php
			if($logged || isset($this->session->data['ppx']['token']) || isset($this->session->data['guest']['firstname']))
			{
				?>
				url: 'index.php?route=checkout/user_info',

				<?php
			}
			else
			{
				?>
				url: 'index.php?route=checkout/checkout/guest_first_step',
				<?php
			}
			?>
			dataType: 'html',
			success: function(html) {
				$('#contactInfo').html(html);
				$('.selectpicker').selectpicker('refresh');
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});	
	});  

	$(document).on('click', '#button-guest_first_step', function(event) {
		$('.alert-danger').hide();
		$.ajax({
			
			url: 'index.php?route=checkout/checkout/validate_guest_first_step',
			type:'post',
			data:'email='+$('#guest_email_first_step').val(),
			dataType: 'json',
			success: function(json) {
				$(".has-error").removeClass("has-error");
				$(".alert-danger").hide("has-error");
				if(json['error'])
				{
					$('.alert-danger').html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Please provide a valid email address.');
					$('.alert-danger').show();
					$('#guest_email_first_step').parent().parent().addClass('has-error');
					scrollToTop();
				}
				else if(json['error2'])
				{
					$('.alert-danger').html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>This email is already registered in our system, please try logged in checkout.');
					$('.alert-danger').show();
					$('#guest_email_first_step').parent().parent().addClass('has-error');
					scrollToTop();
				}
				else
				{
					$.ajax({

						url: 'index.php?route=checkout/user_info',


						dataType: 'html',
						success: function(html) {
							scrollToTop();
							$('#contactInfo').html(html);
							$('.selectpicker').selectpicker('refresh');
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});	
	// });  
	}
},
error: function(xhr, ajaxOptions, thrownError) {
	alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
}
});	
});	
//-->
</script>
<!-- Guest Checkout -->
<script type="text/javascript"><!--
	$(document).on('click', '#button-guest', function(event) {
		$.ajax({
			url: 'index.php?route=checkout/guest/validate',
			type: 'post',
			data: $('#contactInfo input[type=\'text\'], #contactInfo input[type=\'checkbox\']:checked, #contactInfo input[type=\'radio\']:checked, #contactInfo input[type=\'hidden\'], #contactInfo select'),
			dataType: 'json',
			beforeSend: function() {
				$('#button-guest').attr('disabled', true);
				$('#button-guest').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},	
			complete: function() {
				$('#button-guest').attr('disabled', false); 
				$('.wait').remove();
			},			
			success: function(json) {
				$('.warning, .error').remove();
				$(".has-error").removeClass("has-error");
				$(".error-class").removeClass("error-class");
				$(".alert-danger").hide("has-error");
				if (json['redirect']) {
					location = json['redirect'];
				} else if (json['error']) {

				// $('.alert-danger').show();
				$('.alert-danger').html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>The form has some problems, please check the highlighted fields.');
				$('html,body').animate({
					scrollTop: 0
				}, 700);
				$('.alert-danger').show(500);

				if (json['error']['firstname']) {
					// $('#firstname').after('<span class="error">' + json['error']['firstname'] + '</span>');
					$('#firstname').parent().parent().addClass('has-error');
				}
				
				if (json['error']['lastname']) {
					// $('#lastname').after('<span class="error">' + json['error']['lastname'] + '</span>');
					$('#lastname').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['email']) {
					// $('#email').after('<span class="error">' + json['error']['email'] + '</span>');
					$('#email').parent().parent().addClass('has-error');
				}

				if (json['error']['confirmEmail']) {
					// $('#confirmEmail').after('<span class="error">' + json['error']['confirmEmail'] + '</span>');
					$('#confirmEmail').parent().parent().addClass('has-error');
				}
				
				if (json['error']['telephone']) {
					// $('#telephone').after('<span class="error">' + json['error']['telephone'] + '</span>');
					$('#telephone').parent().parent().addClass('has-error');
				}	

				if (json['error']['company_id']) {
					// $('#company_id').after('<span class="error">' + json['error']['company_id'] + '</span>');
					$('#company_id').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['tax_id']) {
					// $('#tax_id').after('<span class="error">' + json['error']['tax_id'] + '</span>');
					$('#tax_id').parent().parent().addClass('has-error');
				}	

				if (json['error']['address_1']) {
					// $('#address_1').after('<span class="error">' + json['error']['address_1'] + '</span>');
					$('#address_1').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['city']) {
					// $('#city').after('<span class="error">' + json['error']['city'] + '</span>');
					$('#city').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['postcode']) {
					// $('#postcode').after('<span class="error">' + json['error']['postcode'] + '</span>');
					$('#zipcode').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['country_id']) {
					// $('#country_id').after('<span class="error">' + json['error']['country_id'] + '</span>');
					$('#country_id').parent().parent().addClass('has-error');
				}
				
				if (json['error']['zone']) {
					// $('#zone_id').after('<span class="error">' + json['error']['zone'] + '</span>');
					$('#zone_id').parent().parent().addClass('has-error');
				}
				if (json['error']['agree']) {
					
					$('#agree_div').addClass('error-class');
				}
			} else {

				var shipping_address = $('#contactInfo input[name=\'shipping_address\']:checked').attr('value');
				
				if (shipping_address) {
					$.ajax({
						url: 'index.php?route=checkout/guest_shipping',
						dataType: 'html',
						success: function(html) {
							$('#shippingInfo').html(html);
							$.ajax({
								url: 'index.php?route=checkout/shipping_method',
								dataType: 'html',
								success: function(html) {
									$('#shippingMethods').html(html);
								},
								error: function(xhr, ajaxOptions, thrownError) {
									alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
								}
							});
							// console.log('here');
							// $('#shippingTab a').trigger('click');
							nextTab();
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
							$('#shippingInfo').html(html);
							// $('#shippingTab a').trigger('click');
							
							scrollToTop();

							nextTab();
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				}
			}	 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
});
$(document).on('click','#button-shipping-method-registered',function(){

	$.ajax({
		url: 'index.php?route=checkout/shipping_address/validate',
		type: 'post',
		data: 'shipping_address='+$('#shippingInfo input[name=shipping_address]').val()+'&comment='+$('textarea[name=comment]').val()+'&address_id='+$('#shippingInfo #address_id_registered').val()+'&firstname='+$('#shippingInfo #addressfirstname').val()+'&lastname='+$('#shippingInfo #addresslastname').val()+'&address_1='+$('#shippingInfo #addressaddress_1').val()+'&address_2='+$('#shippingInfo #addressaddress_2').val()+'&city='+$('#shippingInfo #addresscity').val()+'&zone_id='+$('#shippingInfo #addresszone_id').val()+'&postcode='+$('#shippingInfo #addresspostcode').val()+'&country_id='+$('#shippingInfo #addresscountry_id').val()+'&company='+$('#addresscompany').val(),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping-method').attr('disabled', true);
			$('#button-shipping-method').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},  
		complete: function() {
			$('#button-shipping-method').attr('disabled', false);
			$('.wait').remove();
		},      
		success: function(json) {
			$('.warning, .error').remove();
			$(".has-error").removeClass("has-error");
			$(".alert-danger").hide("has-error");
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				
				// $('.alert-danger').show();
				$('.alert-danger').html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>The form has some problems, please check the highlighted fields.');
				   
				$('.alert-danger').show(500);

				if (json['error']['firstname']) {
					// $('#firstname').after('<span class="error">' + json['error']['firstname'] + '</span>');
					$('#addressfirstname').parent().parent().addClass('has-error');
				}  
				if (json['error']['lastname']) {
					// $('#firstname').after('<span class="error">' + json['error']['firstname'] + '</span>');
					$('#addresslastname').parent().parent().addClass('has-error');
				} 

				if (json['error']['address_1']) {
					// $('#firstname').after('<span class="error">' + json['error']['firstname'] + '</span>');
					$('#addressaddress_1').parent().parent().addClass('has-error');
				}  
				if (json['error']['city']) {
					// $('#firstname').after('<span class="error">' + json['error']['firstname'] + '</span>');
					$('#addresscity').parent().parent().addClass('has-error');
				}  

				if (json['error']['postcode']) {
					// $('#firstname').after('<span class="error">' + json['error']['firstname'] + '</span>');
					$('#addresspostcode').parent().parent().addClass('has-error');
				} 

				if (json['error']['country_id']) {
					// $('#firstname').after('<span class="error">' + json['error']['firstname'] + '</span>');
					$('#addresscountry_id').parent().parent().addClass('has-error');
				}   

				if (json['error']['zone_id']) {
					// $('#firstname').after('<span class="error">' + json['error']['firstname'] + '</span>');
					//$('#addresszone_id').parent().parent().addClass('has-error');
				}  

			} else {
				$.ajax({
					url: 'index.php?route=checkout/shipping_method/validate',
					type: 'post',
					data: 'shipping_method='+$('input[name=shipping_method]:checked').val()+'&comment='+$('textarea[name=comment]').val(),
					dataType: 'json',
					beforeSend: function() {
						$('#button-shipping-method').attr('disabled', true);
						$('#button-shipping-method').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
					},  
					complete: function() {
						$('#button-shipping-method').attr('disabled', false);
						$('.wait').remove();
					},      
					success: function(json) {
						$('.warning, .error').remove();
						$(".has-error").removeClass("has-error");
						$(".alert-danger").hide("has-error");
						if (json['redirect']) {
							location = json['redirect'];
						} else if (json['error']) {

				// $('.alert-danger').show();
				$('.alert-danger').html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'+json['error']['warning']);
				// $('html,body').animate({
				// 	scrollTop: 0
				// }, 700);     
				$('.alert-danger').show(500);
			} else {
				$.ajax({
					url: 'index.php?route=checkout/payment_method',
					dataType: 'html',
					success: function(html) {

						$('#paymentMethod').html(html);
           // $('#paymentTab a').trigger('click'); 

           $('.selectpicker').selectpicker('refresh');
           $('input[name=payment_method]:checked').trigger('click');
          
          	<?php
          	if(!isset($this->session->data['ppx']['token']))
          	{
          		?>

          		<?php
          	}
          	?>
           	nextTab();
          

          
           $('html,body').animate({
           	scrollTop: 0
           }, 700);
           
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
}
},
error: function(xhr, ajaxOptions, thrownError) {
	alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
}
}); 
});

$(document).on('click','#button-shipping-method',function(){

	$.ajax({
		url: 'index.php?route=checkout/shipping_method/validate',
		type: 'post',
		data: 'shipping_method='+$('input[name=shipping_method]:checked').val()+'&comment='+$('textarea[name=comment]').val()+'&shipping_address='+$('#shippingInfo input[name=shipping_address]').val()+'&address_id='+$('#shippingInfo #address_id_registered').val()+'&firstname='+$('#shippingInfo #addressfirstname').val()+'&lastname='+$('#shippingInfo #addresslastname').val()+'&address_1='+$('#shippingInfo #addressaddress_1').val()+'&address_2='+$('#shippingInfo #addressaddress_2').val()+'&city='+$('#shippingInfo #addresscity').val()+'&zone_id='+$('#shippingInfo #addresszone_id').val()+'&postcode='+$('#shippingInfo #addresspostcode').val()+'&country_id='+$('#shippingInfo #addresscountry_id').val(),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping-method').attr('disabled', true);
			$('#button-shipping-method').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},  
		complete: function() {
			$('#button-shipping-method').attr('disabled', false);
			$('.wait').remove();
		},      
		success: function(json) {
			$('.warning, .error').remove();
			$(".has-error").removeClass("has-error");
			$(".alert-danger").hide("has-error");
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				
				// $('.alert-danger').show();
				$('.alert-danger').html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'+json['error']['warning']);
				$('html,body').animate({
					scrollTop: 0
				}, 700);    
				$('.alert-danger').show(500); 
			} else {
				$.ajax({
					url: 'index.php?route=checkout/payment_method',
					dataType: 'html',
					success: function(html) {

						$('#paymentMethod').html(html);
           // $('#paymentTab a').trigger('click');
           
           	nextTab();
           	
           
           $('.selectpicker').selectpicker('refresh');
           $('input[name=payment_method]:checked').trigger('click');
           $('html,body').animate({
           	scrollTop: 0
           }, 700);
           
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
});

$(document).on('click','input[name=payment_method]',function(){
	$.ajax({
		url: 'index.php?route=checkout/payment_method/paymentType',
		type: 'post',
		data: 'payment_type='+$(this).val(),
		dataType: 'html',
		beforeSend: function() {
			// $('#button-guest-shipping').attr('disabled', true);
			// $('#button-guest-shipping').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			$('#payment-details-checkout').html('<img src="catalog/view/theme/ppusa2.0/images/spinner.gif">');
		},	
		complete: function() {
			// $('#button-guest-shipping').attr('disabled', false); 
			// $('.wait').remove();
		},			
		success: function(html) {
			$('#payment-details-checkout').html(html); 
			$('.selectpicker').selectpicker('refresh');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
});
$(document).on('click', '#button-guest-shipping', function() {
	$.ajax({
		url: 'index.php?route=checkout/guest_shipping/validate',
		type: 'post',
		data: $('#shippingInfo input[type=\'text\'], #shippingInfo select'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-guest-shipping').attr('disabled', true);
			$('#button-shipping-method').addClass('disabled');
			$('#button-shipping-method').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			$('.warning, .error').remove();
			$(".has-error").removeClass("has-error");
			$(".alert-danger").hide("has-error");
			// $('#button-guest-shipping').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('#button-guest-shipping').attr('disabled', false); 
			$('.wait').remove();
		},			
		success: function(json) {
			$('.warning, .error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#shippingInfo .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
					$('.warning').fadeIn('slow');
				}

				if (json['error']['firstname']) {
					// $('#shippingInfo input[name=\'firstname\']').after('<span class="error">' + json['error']['firstname'] + '</span>');

					$('#shippingInfo input[name=\'firstname\']').parent().parent().addClass('has-error');
				}
				
				if (json['error']['lastname']) {
					// $('#shippingInfo input[name=\'lastname\']').after('<span class="error">' + json['error']['lastname'] + '</span>');
					$('#shippingInfo input[name=\'lastname\']').parent().parent().addClass('has-error');
				}	

				if (json['error']['address_1']) {
					// $('#shippingInfo input[name=\'address_1\']').after('<span class="error">' + json['error']['address_1'] + '</span>');
					$('#shippingInfo input[name=\'address_1\']').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['city']) {
					// $('#shippingInfo input[name=\'city\']').after('<span class="error">' + json['error']['city'] + '</span>');
					$('#shippingInfo input[name=\'city\']').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['postcode']) {
					// $('#shippingInfo input[name=\'postcode\']').after('<span class="error">' + json['error']['postcode'] + '</span>');
					$('#shippingInfo input[name=\'postcode\']').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['country']) {
					// $('#shippingInfo select[name=\'country_id\']').after('<span class="error">' + json['error']['country'] + '</span>');
					$('#shippingInfo select[name=\'country_id\']').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['zone']) {
					// $('#shippingInfo select[name=\'zone_id\']').after('<span class="error">' + json['error']['zone'] + '</span>');

					$('#shippingInfo select[name=\'zone_id\']').parent().parent().addClass('has-error');
				}
			} else {
				$.ajax({
					url: 'index.php?route=checkout/shipping_method',
					dataType: 'html',
					success: function(html) {
						$('#shippingMethods').html(html);
						
						$('#button-shipping-method').removeClass('disabled');
						// alert('her');
						// setTimeout(function() { $('input[name=shipping_method]:eq(0)').trigger('click'); }, 000);	
						
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
});

$(document).on('click', '#button-logged-shipping', function() {
	$.ajax({
		url: 'index.php?route=checkout/shipping_address/validate',
		type: 'post',
		data:'shipping_address='+$('#shippingInfo input[name=shipping_address]').val()+'&comment='+$('textarea[name=comment]').val()+'&address_id='+$('#shippingInfo #address_id_registered').val()+'&firstname='+$('#shippingInfo #addressfirstname').val()+'&lastname='+$('#shippingInfo #addresslastname').val()+'&address_1='+$('#shippingInfo #addressaddress_1').val()+'&address_2='+$('#shippingInfo #addressaddress_2').val()+'&city='+$('#shippingInfo #addresscity').val()+'&zone_id='+$('#shippingInfo #addresszone_id').val()+'&postcode='+$('#shippingInfo #addresspostcode').val()+'&country_id='+$('#shippingInfo #addresscountry_id').val()+'&company='+$('#addresscompany').val(),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping-method-registered').attr('disabled', true);
			$('#button-shipping-method-registered').addClass('disabled');
			$('#button-shipping-method-registered').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			$('.warning, .error').remove();
			$(".has-error").removeClass("has-error");
			$(".alert-danger").hide("has-error");
			// $('#button-guest-shipping').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('#button-shipping-method-registered').attr('disabled', false); 
			$('.wait').remove();
		},			
		success: function(json) {
			$('.warning, .error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					
				}

				if (json['error']['firstname']) {
					// $('#shippingInfo input[name=\'firstname\']').after('<span class="error">' + json['error']['firstname'] + '</span>');

					$('#shippingInfo input[name=\'firstname\']').parent().parent().addClass('has-error');
				}
				
				if (json['error']['lastname']) {
					// $('#shippingInfo input[name=\'lastname\']').after('<span class="error">' + json['error']['lastname'] + '</span>');
					$('#shippingInfo input[name=\'lastname\']').parent().parent().addClass('has-error');
				}	

				if (json['error']['address_1']) {
					// $('#shippingInfo input[name=\'address_1\']').after('<span class="error">' + json['error']['address_1'] + '</span>');
					$('#shippingInfo input[name=\'address_1\']').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['city']) {
					// $('#shippingInfo input[name=\'city\']').after('<span class="error">' + json['error']['city'] + '</span>');
					$('#shippingInfo input[name=\'city\']').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['postcode']) {
					// $('#shippingInfo input[name=\'postcode\']').after('<span class="error">' + json['error']['postcode'] + '</span>');
					$('#shippingInfo input[name=\'postcode\']').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['country']) {
					// $('#shippingInfo select[name=\'country_id\']').after('<span class="error">' + json['error']['country'] + '</span>');
					$('#shippingInfo select[name=\'country_id\']').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['zone']) {
					// $('#shippingInfo select[name=\'zone_id\']').after('<span class="error">' + json['error']['zone'] + '</span>');

					$('#shippingInfo select[name=\'zone_id\']').parent().parent().addClass('has-error');
				}
			} else {
				$.ajax({
					url: 'index.php?route=checkout/shipping_method',
					dataType: 'html',
					success: function(html) {
						$('#shippingMethods').html(html);
						
						$('#button-shipping-method-registered').removeClass('disabled');
						// alert('her');
						// setTimeout(function() { $('input[name=shipping_method]:eq(0)').trigger('click'); }, 000);	
						
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
});


$(document).on('click', '#confirm-payment-method', function(event) {
	$.ajax({
		url: 'index.php?route=checkout/payment_method/validate_new',
		type: 'post',
		data: $('#paymentMethod input[name=payment_method]:checked, #paymentMethod input[type=\'text\'], #paymentMethod input[type=\'checkbox\']:checked, #paymentMethod input[type=\'radio\']:checked, #paymentMethod input[type=\'hidden\'], #paymentMethod select'),
		dataType: 'json',
		beforeSend: function() {
			$('#confirm-payment-method').attr('disabled', true);
			$('#confirm-payment-method').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('#confirm-payment-method').attr('disabled', false); 
			$('.wait').remove();
		},			
		success: function(json) {
			$('.warning, .error').remove();
			$(".has-error").removeClass("has-error");
			$(".alert-danger").hide("has-error");
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				
				// $('.alert-danger').show();
				$('.alert-danger').html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>The form has some problems or invalid Credit Card details.');
				$('html,body').animate({
					scrollTop: 0
				}, 700);
				$('.alert-danger').show(500);

				if (json['error']['firstname']) {
					// $('#firstname').after('<span class="error">' + json['error']['firstname'] + '</span>');
					$('#inputFirstName').parent().parent().addClass('has-error');
				}

				if (json['error']['lastname']) {
					// $('#firstname').after('<span class="error">' + json['error']['firstname'] + '</span>');
					$('#inputLastName').parent().parent().addClass('has-error');
				}



				if (json['error']['address_1']) {
					// $('#firstname').after('<span class="error">' + json['error']['firstname'] + '</span>');
					$('#inputStreet').parent().parent().addClass('has-error');
				}
				
				if (json['error']['zip']) {
					// $('#lastname').after('<span class="error">' + json['error']['lastname'] + '</span>');
					$('#inputZip').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['city']) {
					// $('#email').after('<span class="error">' + json['error']['email'] + '</span>');
					$('#inputCity').parent().parent().addClass('has-error');
				}

				if (json['error']['state']) {
					// $('#confirmEmail').after('<span class="error">' + json['error']['confirmEmail'] + '</span>');
					$('#inputState').parent().parent().addClass('has-error');
				}
				
				if (json['error']['country']) {
					// $('#telephone').after('<span class="error">' + json['error']['telephone'] + '</span>');
					$('#inputCountry').parent().parent().addClass('has-error');
				}	

				if (json['error']['cc_name']) {
					// $('#telephone').after('<span class="error">' + json['error']['telephone'] + '</span>');
					$('input[name=cc_name]').parent().parent().addClass('has-error');
				}	

				if (json['error']['cc_number']) {
					// $('#telephone').after('<span class="error">' + json['error']['telephone'] + '</span>');
					$('input[name=cc_number]').parent().parent().addClass('has-error');
				}	

				if (json['error']['cc_cvv2']) {
					// $('#telephone').after('<span class="error">' + json['error']['telephone'] + '</span>');
					$('input[name=cc_cvv2]').parent().parent().addClass('has-error');
				}	

				
			} else {

				
				
				$.ajax({
					url: 'index.php?route=checkout/checkout/order_confirm',
					dataType: 'html',
					success: function(html) {
						$('#confirmOrder').html(html);
							// $('#link3').trigger('click');
							nextTab();
							$('html,body').animate({
								scrollTop: 0
							}, 700);
							$('.btn.btn-green-reverse').removeClass('disabled').removeClass('hidden');
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
});

//-->
</script>
<script type="text/javascript"><!-- 


	$(document).on('click', '#button-register', function(event) {	
		$.ajax({
			url: 'index.php?route=checkout/register/validate',
			type: 'post',
			data: $('#contactInfo input[type=\'text\'], #contactInfo input[type=\'password\'], #contactInfo input[type=\'checkbox\']:checked, #contactInfo input[type=\'radio\']:checked, #contactInfo input[type=\'hidden\'], #contactInfo select'),
			dataType: 'json',
			beforeSend: function() {
				$('#button-register').attr('disabled', true);
				$('#button-register').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},	
			complete: function() {
				$('#button-register').attr('disabled', false); 
				$('.wait').remove();
			},			
			success: function(json) {
				$('.warning, .error').remove();

				if (json['redirect']) {
					location = json['redirect'];				
				} else if (json['error']) {
					if (json['error']['warning']) {
						$('#payment-address .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');

						$('.warning').fadeIn('slow');
					}

					if (json['error']['firstname']) {
						$('#firstname').after('<span class="error">' + json['error']['firstname'] + '</span>');
					}

					if (json['error']['lastname']) {
						$('#lastname').after('<span class="error">' + json['error']['lastname'] + '</span>');
					}	

					if (json['error']['email']) {
						$('#email').after('<span class="error">' + json['error']['email'] + '</span>');
					}

					if (json['error']['confirmEmail']) {
						$('#confirmEmail').after('<span class="error">' + json['error']['confirmEmail'] + '</span>');
					}

					if (json['error']['telephone']) {
						$('#telephone').after('<span class="error">' + json['error']['telephone'] + '</span>');
					}	
					
					if (json['error']['company_id']) {
						$('#company_id').after('<span class="error">' + json['error']['company_id'] + '</span>');
					}	

					if (json['error']['tax_id']) {
						$('#tax_id').after('<span class="error">' + json['error']['tax_id'] + '</span>');
					}	

					if (json['error']['address_1']) {
						$('#address_1').after('<span class="error">' + json['error']['address_1'] + '</span>');
					}	

					if (json['error']['city']) {
						$('#city').after('<span class="error">' + json['error']['city'] + '</span>');
					}	

					if (json['error']['postcode']) {
						$('#postcode').after('<span class="error">' + json['error']['postcode'] + '</span>');
					}	

					if (json['error']['country_id']) {
						$('#country_id').after('<span class="error">' + json['error']['country_id'] + '</span>');
					}	

					if (json['error']['zone']) {
						$('#zone_id').after('<span class="error">' + json['error']['zone'] + '</span>');
					}

					if (json['error']['password']) {
						$('#password').after('<span class="error">' + json['error']['password'] + '</span>');
					}	

					if (json['error']['confirm']) {
						$('#confirm').after('<span class="error">' + json['error']['confirm'] + '</span>');
					}

					if (json['error']['agree']) {
					
					$('#agree_div').addClass('error-class');
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

									$('#payment-address .checkout-content').slideUp('slow');

									$('#shipping-method .checkout-content').slideDown('slow');

									$('#checkout .checkout-heading a').remove();
									$('#payment-address .checkout-heading a').remove();
									$('#shipping-address .checkout-heading a').remove();
									$('#shipping-method .checkout-heading a').remove();
									$('#payment-method .checkout-heading a').remove();											

									$('#shipping-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');									
									$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	

									$.ajax({
										url: 'index.php?route=checkout/shipping_address',
										dataType: 'html',
										success: function(html) {
											$('#shipping-address .checkout-content').html(html);
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
			$('#shippingInfo').html(html);
			$.ajax({
				url: 'index.php?route=checkout/shipping_method',
				dataType: 'html',
				success: function(html) {
					$('#shippingMethods').html(html);
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});

							// $('#payment-address .checkout-content').slideUp('slow');
							
							// $('#shipping-address .checkout-content').slideDown('slow');
							
							 // $('#shippingTab a').trigger('click');
							 nextTab();

							 $('#checkout .checkout-heading a').remove();
							 $('#payment-address .checkout-heading a').remove();
							 $('#shipping-address .checkout-heading a').remove();
							 $('#shipping-method .checkout-heading a').remove();
							 $('#payment-method .checkout-heading a').remove();							

							 $('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
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

			$('#payment-address .checkout-content').slideUp('slow');

			$('#payment-method .checkout-content').slideDown('slow');

			$('#checkout .checkout-heading a').remove();
			$('#payment-address .checkout-heading a').remove();
			$('#payment-method .checkout-heading a').remove();								

			$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
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
});

$(document).on('click','#button-registered-user-info',function(){
	<?php if ($shipping_required) { ?>				
		var shipping_address = $('#payment-address input[name=\'shipping_address\']:checked').attr('value');

		if (shipping_address) {
			$.ajax({
				url: 'index.php?route=checkout/shipping_method',
				dataType: 'html',
				success: function(html) {
					$('#shipping-method .checkout-content').html(html);

					$('#payment-address .checkout-content').slideUp('slow');

					$('#shipping-method .checkout-content').slideDown('slow');

					$('#checkout .checkout-heading a').remove();
					$('#payment-address .checkout-heading a').remove();
					$('#shipping-address .checkout-heading a').remove();
					$('#shipping-method .checkout-heading a').remove();
					$('#payment-method .checkout-heading a').remove();											

					$('#shipping-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');									
					$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	

					$.ajax({
						url: 'index.php?route=checkout/shipping_address',
						dataType: 'html',
						success: function(html) {
							$('#shipping-address .checkout-content').html(html);
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
	url: 'index.php?route=checkout/shipping_address/validate_logged_in',
	type: 'post',
	data: $('#contactInfo input[type=\'text\'], #contactInfo input[type=\'checkbox\']:checked, #contactInfo input[type=\'radio\']:checked, #contactInfo input[type=\'hidden\'], #contactInfo select'),
	dataType: 'json',
	beforeSend: function() {
		$('#button-registered-user-info').attr('disabled', true);
		$('#button-registered-user-info').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
	},	
	complete: function() {
		$('#button-registered-user-info').attr('disabled', false); 
		$('.wait').remove();
	},
	success: function(json) {
		$('.warning, .error').remove();
		$(".has-error").removeClass("has-error");
		$(".error-class").removeClass("error-class");
		$(".alert-danger").hide("has-error");
		if (json['redirect']) {
			location = json['redirect'];
		} else if (json['error']) {
				
				$('.alert-danger').html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>The form has some problems, please check the highlighted fields.');
				$('html,body').animate({
					scrollTop: 0
				}, 700);
				$('.alert-danger').show(500);
				if (json['error']['firstname']) {
					
					$('#firstname').parent().parent().addClass('has-error');
				}
				
				if (json['error']['lastname']) {
				
					$('#lastname').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['email']) {
					$('#email').parent().parent().addClass('has-error');
				}

				if (json['error']['email']) {
					$('#email').parent().parent().addClass('has-error');
				}
				if (json['error']['confirmEmail']) {
					// $('#confirmEmail').after('<span class="error">' + json['error']['confirmEmail'] + '</span>');
					$('#confirmEmail').parent().parent().addClass('has-error');
				}
				
				if (json['error']['telephone']) {
					// $('#telephone').after('<span class="error">' + json['error']['telephone'] + '</span>');
					$('#telephone').parent().parent().addClass('has-error');
				}	
				if (json['error']['company_id']) {
					// $('#company_id').after('<span class="error">' + json['error']['company_id'] + '</span>');
					$('#company_id').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['tax_id']) {
					// $('#tax_id').after('<span class="error">' + json['error']['tax_id'] + '</span>');
					$('#tax_id').parent().parent().addClass('has-error');
				}	
				if (json['error']['address_1']) {
					// $('#address_1').after('<span class="error">' + json['error']['address_1'] + '</span>');
					$('#address_1').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['city']) {
					// $('#city').after('<span class="error">' + json['error']['city'] + '</span>');
					$('#city').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['postcode']) {
					// $('#postcode').after('<span class="error">' + json['error']['postcode'] + '</span>');
					$('#zipcode').parent().parent().addClass('has-error');
				}	
				
				if (json['error']['country_id']) {
					// $('#country_id').after('<span class="error">' + json['error']['country_id'] + '</span>');
					$('#country_id').parent().parent().addClass('has-error');
				}
				
				if (json['error']['zone']) {
					// $('#zone_id').after('<span class="error">' + json['error']['zone'] + '</span>');
					$('#zone_id').parent().parent().addClass('has-error');
				}
				if (json['error']['agree']) {
					
					$('#agree_div').addClass('error-class');
				}
			} else {
					// Shipping Address

					$.ajax({
						url: 'index.php?route=checkout/shipping_address',
						dataType: 'html',
						success: function(html) {
							$('#shippingInfo').html(html);
							$.ajax({
								url: 'index.php?route=checkout/shipping_method',
								dataType: 'html',
								success: function(html) {
									$('#shippingMethods').html(html);
								},
								error: function(xhr, ajaxOptions, thrownError) {
									alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
								}
							});
							nextTab();
							$('#checkout .checkout-heading a').remove();
							$('#payment-address .checkout-heading a').remove();
							$('#shipping-address .checkout-heading a').remove();
							$('#shipping-method .checkout-heading a').remove();
							$('#payment-method .checkout-heading a').remove();							
							$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});	
}
		// End Shipping Address
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

			$('#payment-address .checkout-content').slideUp('slow');

			$('#payment-method .checkout-content').slideDown('slow');

			$('#checkout .checkout-heading a').remove();
			$('#payment-address .checkout-heading a').remove();
			$('#payment-method .checkout-heading a').remove();								

			$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});					
	<?php } ?>
	
});
 //-->
</script>
<script type="text/javascript">
	var gr_goal_params = {
		param_0 : '',
		param_1 : '',
		param_2 : '',
		param_3 : '',
		param_4 : '',
		param_5 : ''
	};
</script>
<script type="text/javascript" src="https://app.getresponse.com/goals_log.js?p=668602&u=jgEp"></script>
<script type="text/javascript" src="//maps.google.com/maps/api/js?key=AIzaSyCDBA_9lOZO0FkWHBvsmwhZdaWKALqJJsg&sensor=true"></script>

<script>
	var zone_id = '';
	$(document).on('blur', '#postcode', function() {
		var zip = $(this).val();
		var lat;
		var lng;


  // $('#button-quote').attr('disabled', true);
  // $('#button-quote').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
  
  var geocoder = new google.maps.Geocoder();
  geocoder.geocode({ 'address': zip }, function (results, status) {
  	if (status == google.maps.GeocoderStatus.OK) {
  		geocoder.geocode({'latLng': results[0].geometry.location}, function(results, status) {
  			if (status == google.maps.GeocoderStatus.OK) {
  				if (results[1]) {
  					var loc = getCityState(results);

  				}
  			}
  		});
  	}
  }); 
});
	function getCityState(results)
	{
		var a = results[0].address_components;
		var city, state;
		for(i = 0; i <  a.length; ++i)
		{
			var t = a[i].types;
			if(compIsType(t, 'administrative_area_level_1'))
			{
			state = a[i].long_name; //store the state other
		}
		else if(compIsType(t, 'locality')){
			city = a[i].long_name;         
		     //store the city
		 }

		}
		alert(state);
		$('select[name=zone_id]').find('option').removeAttr('selected');
		$('select[name=zone_id]').find('option:contains('+state+')').attr('selected','selected');
		// $('select[name=zone_id]').val(state).change();
		setTimeout(function() { loadxPopup(); }, 4000);	
  //$('select[name=zone_id]').find('option:contains('+state+')').attr('selected','selected');
}

function compIsType(t, s) { 
	for(z = 0; z < t.length; ++z) 
		if(t[z] == s)
			return true;
		return false;
	}

// setTimeout(function() { loadxPopup(); }, 4000);	

function loadxPopup()
{

	$.ajax({
		url: 'index.php?route=checkout/cart/quote',
		type: 'post',
		data: 'country_id=<?php echo $country_id;?>&zone_id=' + $('select[name=zone_id]').val() + '&postcode=' + encodeURIComponent($('input[name=\'postcode\']').val()),
		dataType: 'json',		
		beforeSend: function() {

		},
		complete: function() {
			// $('#button-quote').attr('disabled', false);
			// $('.wait').remove();
		},		
		success: function(json) {
			// $('.success, .warning, .attention, .error').remove();			

			if (json['error']) {
				if (json['error']['warning']) {
					// $('#notification').html('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
					// $('.warning').fadeIn('slow');
					
					// $('html, body').animate({ scrollTop: 0 }, 'slow'); 
				}	

				if (json['error']['country']) {

				}	

				if (json['error']['zone']) {

				}

				if (json['error']['postcode']) {

				}					
			}

			if (json['shipping_method']) {
				html  = '<ul class="total-types">';


				for (i in json['shipping_method']) {

					

					if (!json['shipping_method'][i]['error']) {
						html += '<ul class="first-list">';
						for (j in json['shipping_method'][i]['quote']) {


							if (json['shipping_method'][i]['quote'][j]['code'] == json['default_shipping_method']) {
								html += '<li ><input type="radio" name="shipping_method" data-value="'+json['shipping_method'][i]['quote'][j]['text']+'" value="' + json['shipping_method'][i]['quote'][j]['code'] + '" id="' + json['shipping_method'][i]['quote'][j]['code'] + '" class="css-radio" checked="checked" />';
							} else {
								html += '<li ><input type="radio" name="shipping_method" data-value="'+json['shipping_method'][i]['quote'][j]['text']+'" value="' + json['shipping_method'][i]['quote'][j]['code'] + '" id="' + json['shipping_method'][i]['quote'][j]['code'] + '" class="css-radio" />';
							}
							html +='<label for="'+json['shipping_method'][i]['quote'][j]['code']+'" class="css-radio">'+json['shipping_method'][i]['quote'][j]['title']+'</label>';



						}	
						html +='</ul>';
					} else {

					}
				}
				html += '  <input type="hidden" name="next" value="shipping" />';
				$('#cart-shipping-estimate').html(html);
				updateShippingCost();
				<?php if ($shipping_method) { ?>

					<?php } else { ?>

						<?php } ?>



					}
				}
			});	
}
// $(document).on('change','select[name=\'country_id\']', function() {
// 	$.ajax({
// 		url: 'index.php?route=checkout/cart/country&country_id=' + this.value,
// 		dataType: 'json',
// 		beforeSend: function() {
// 			// $('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
// 		},
// 		complete: function() {
// 			// $('.wait').remove();
// 		},			
// 		success: function(json) {
// 			if (json['postcode_required'] == '1') {
// 				// $('#postcode-required').show();
// 			} else {
// 				// $('#postcode-required').hide();
// 			}

// 			html = '<option value=""><?php echo $text_select; ?></option>';

// 			if (json['zone'] != '') {
// 				for (i = 0; i < json['zone'].length; i++) {
//          html += '<option value="' + json['zone'][i]['zone_id'] + '"';

//          if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
//            html += ' selected="selected"';
//          }

//          html += '>' + json['zone'][i]['name'] + '</option>';
//        }
//      } else {
//       html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
//     }

//     $('select[name=\'zone_id\']').html(html);
//     loadxPopup();
//     // updateShippingCost();
//   },
//   error: function(xhr, ajaxOptions, thrownError) {
//    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
//  }
// });
// });
// $(document).on('change','',function(){
// 	 $('#shipping-text').html($(this).attr('data-value'));
// 	$.ajax({
//     url: 'index.php?route=checkout/cart',
//     type: 'post',
//     data: 'shipping_method='+$(this).val(),
//     dataType: 'json',		
// 		success: function(json) {
// 			// alert('here');
// 			$( ".cart-total-box" ).load( "index.php?route=module/cart_right_cart" );
// 			 setTimeout(function() { loadxPopup(); }, 100);


//     }


// 	});

// });
function updateShippingCost(){
	var data_value = $('input[name=shipping_method]:checked').attr('data-value');
	$('#shipping-text').html(data_value);
}
$('select[name=\'country_id\']').trigger('change');

$(document).on('change','input[name=shipping_method_step2],input[name=shipping_method]',function(){
	 // $('#shipping-text').html($(this).attr('data-value'));
	 updateShippingMethodx($(this));

	});
function updateShippingMethodx(obj)
{
	$.ajax({
	 	url: 'index.php?route=checkout/cart',
	 	type: 'post',
	 	data: 'shipping_method='+$(obj).val(),
	 	dataType: 'json',		
	 	success: function(json) {
			// alert('here');
			$( "#checkout_right_cart" ).load( "index.php?route=module/checkout_right_cart" );
			 // setTimeout(function() { loadxPopup(); }, 100);


			}


		});
}

$(document).on('change','#address_id_registered',function(){
	if($(this).val()=='')
	{
		$('#shippingInfo #shipping_address').val('new');
		// $('#shippingInfo .new_attribs').show();
		
	}
	else
	{
		$('#shippingInfo #shipping_address').val('existing');
		// $('#shippingInfo .new_attribs').hide();
	}
	if($(this).val()=='')
	{
		return false;
	}
	$.ajax({
	 	url: 'index.php?route=checkout/shipping_address/getAddress',
	 	type: 'post',
	 	data: 'address_id='+$(this).val(),
	 	dataType: 'json',		
	 	success: function(json) {
			// alert('here');
			// $( "#checkout_right_cart" ).load( "index.php?route=module/checkout_right_cart" );
			 // setTimeout(function() { loadxPopup(); }, 100);
			  if(json['error'])
			 {

			 }
			 else
			 {
			 	// console.log(json['zone_id']);
			 	$('#shippingInfo #addressfirstname').val(json['firstname']);
			 	$('#shippingInfo #addresslastname').val(json['lastname']);
			 	$('#shippingInfo #addresscompany').val(json['company']);
			 	$('#shippingInfo #addressaddress_1').val(json['address_1']);
			 	$('#shippingInfo #addressaddress_2').val(json['address_2']);
			 	$('#shippingInfo #addresscity').val(json['city']);
			 	$('#shippingInfo #addresspostcode').val(json['postcode']);
			 	
			 	$('#shippingInfo #addresscountry_id option').removeAttr('selected');
			 	$('#shippingInfo #addresszone_id option').removeAttr('selected');


			 	// $('#shippingInfo #addresscountry_id option[value="'+json['country_id']+'"]').attr('selected','selected');
			 	// $('#shippingInfo #addresszone_id option[value="'+json['zone_id']+'"]').attr('selected','selected');
			 	
			 	$('#shippingInfo #addresscountry_id').val(json['country_id']);
			 	//$('#shippingInfo #addresszone_id').val(json['zone_id']);

			 	$('#shippingInfo select[name=\'country_id\']').trigger('change');
			 	setTimeout(function() { 
			 		$('#shippingInfo #addresszone_id').val(json['zone_id']);
			 		$('.selectpicker').selectpicker('refresh');
			 		$('#button-logged-shipping').trigger('click');
			 		 }, 2000);
			 	

			 	// $('#shippingInfo #addressaddress_2').change();
			 	$('.selectpicker').selectpicker('refresh');
			 	

			 	
			 	
			 }


			}


		});

});

$(document).ready(function () {
    //Initialize tooltips
    $('.nav-tabs > li a[title]').tooltip();
    
    //Wizard
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

    	var $target = $(e.target);

    	if ($target.parent().hasClass('disabled')) {
    		return false;
    	}
    });

    $(".next-step").click(function (e) {

    	var $active = $('.wizard .nav-tabs li.active');
    	$active.next().removeClass('disabled');
    	nextTab($active);

    });
    $(".prev-step").click(function (e) {

    	var $active = $('.wizard .nav-tabs li.active');
    	prevTab($active);

    });
});

function nextTab() {
	var $active = $('.wizard .nav-tabs li.active');
	$active.next().removeClass('disabled');
	$($active).next().find('a[data-toggle="tab"]').click();
}
function gotoLastStep()
{
		var $active = $('.wizard .nav-tabs li.active');
	$active.next().next().removeClass('disabled');
	$($active).next().next().find('a[data-toggle="tab"]').click();
}
function prevTab(elem) {
	$(elem).prev().find('a[data-toggle="tab"]').click();
}
$(document).on('click','a[data-toggle="tab"]',function(){
	var var_id = $(this).parent().attr('id');

	if(var_id=='checkout' || var_id=='shippingTab')
	{
		// alert('here');
		$('.display_on_step1,.display_on_step2').show();
		$('.display_on_step3,.display_on_step4').hide();
		$('#addSign').removeAttr('disabled');
		$('ul[role=tablist] li#paymentTab').addClass('disabled');
		$('ul[role=tablist] li:last').addClass('disabled');

	}
	else
	{
		// alert('here2')
		$('#addSign').attr('disabled',true);
		$('.display_on_step1,.display_on_step2').hide();
		$('.display_on_step3,.display_on_step4').show();
	}


})

</script>

