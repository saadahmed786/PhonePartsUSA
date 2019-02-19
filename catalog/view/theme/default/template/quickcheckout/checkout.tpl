<?php $config = $this->config->get('quickcheckout'); ?>
<?php if(isset($_POST['reset_session'])){ unset($_SESSION); }?>
<?php echo $header; ?>
<!-- Quick Checkout v3.0 quickcheckout/checkout.tpl -->

<style>
.clear{
	clear:both
}
.hide{
	display:none !important
}
.left{
	float:left
}
.right{
	float:right
}
.column{
	float:left;
	}
.column > div{
	padding:0 5px;}
.payment-step .column{
	float:left; 
	width:28%;
	}
thead th{
	padding:4px;}
label{
	display: block;
	padding-bottom:5px;
	line-height:22px;
}
input[type="radio"] {
	padding: 6px 5px 5px 5px;
	height:13px;
	width:13px;
	outline:none
}
input[type="checkbox"] {
	padding: 3px 5px 5px 5px;
	height:13px;
	width:13px;
	outline:none
}
.checkout-content{
	padding: 0px}
<?php if($config['checkout_labels_float']){ ?>
.has-fields .box-content label{
	display: inline-block;
	min-width:38%;
	text-align:right;
}
.has-fields input.large-field, .has-fields select.large-field{
	width:55%
}
.customer-group label{
	float:left
}
<?php } ?>
/*Login form*/
#step_1 .box{
	display: table-cell;
	width:30%;
	padding:0px 5px 15px 5px ;
	}
#signup_group{
	padding-bottom:15px;}
#step_1 .box .box-content {
	height:100px}
#step_1 .box.selected .box-content{
	height:116px;
	border-bottom:1px solid #fff;
	-webkit-border-radius: 0px ;
	-moz-border-radius: 0px ;
	-khtml-border-radius:0px ;
	border-radius:0px ;
	background:#FEFEFE
	}
#login .form-inline{
		margin:5px 0px 0px 80px;
		}
#login .block-row input{
	margin:0px;
	}
.block-row input,
.block-row label{
		display:inline-block}
#login .block-row label{
	width:80px;
	text-align:right}
#register_option .text{
		margin:0px 40px;
}
.payment-step .box-content{
	margin-bottom:15px;}
#step_1 {
	border-bottom:1px solid #DBDEE1;
	max-height:215px;}
#step_2,
#step_3{
	padding-top:15px;
	background:#FEFEFE}
/*Fix design*/
.box-heading,
.box-content{
	display:block}

.customer-group ul{
	display: inline-block;
	padding: 0px;
	list-style: none;
	margin: 0px;
}
.customer-group li label{
	display:inline-block
}
input[type=checkbox]{
	float:left
}
input[type=radio]{
	float:left}
.box .box-heading {
	color:#333px;
	
}
.payment > #payment_button .buttons,
.payment > #payment_button .button{
	display:none
}
.checkout-content{
	overflow:hidden
}
.wait{
	margin:5px;
	display:inline-block}
.loading{
	display:inline-block;
	float:right;
	background:url(catalog/view/theme/default/image/loading.gif);
	width:10px;
	height:10px;
	margin:5px;
	display:none
	}
.payment-image{
	height:24px; 
	float:right}
#comment {
	width:96%}
#confirm .checkout-product{
	background:#fff}
<?php if($config['checkout_force_default_style']){ ?>
#content_holder > .layout{
	max-width:980px;}

.breadcrumb {
	clear:both;
color: #CCCCCC;
margin-bottom: 10px;
}

.breadcrumb a,
.breadcrumb span {
font-size: 12px;
}
.warning.alert.alert-error{
	margin-bottom:15px}
.alert-error{
	padding:5px;
	background:#FFD1D1;
	border: 1px solid #F8ACAC;
	margin-bottom:3px;
	color:#900 }
.h1{
color: #636E75;
font: Verdana;
margin-top: 0px;
margin-bottom: 20px;
font-size: 32px;
font-weight: normal;
text-shadow: 0 0 1px rgba(0, 0, 0, .01);
}
p {
margin-top: 0px;
margin-bottom: 15px;
}
.checkout-heading {
background: #F8F8F8;
border: 1px solid #DBDEE1;
padding: 8px;
font-weight: bold;
font-size: 13px;
color: #555555;
margin-bottom: 15px;
}
.box {
margin-bottom: 20px;
}
.box .box-heading {
-webkit-border-radius: 7px 7px 0px 0px;
-moz-border-radius: 7px 7px 0px 0px;
-khtml-border-radius: 7px 7px 0px 0px;
border-radius: 7px 7px 0px 0px;
border: 1px solid #DBDEE1;
background: #EEE;
background: url('../image/background.png') repeat-x;
padding: 8px 10px 7px 10px;
font-family: Arial, Helvetica, sans-serif;
font-size: 14px;
font-weight: bold;
line-height: 14px;
color: #333;
margin:0px;
display:block
}
.box .box-content {
background: #FFFFFF;
-webkit-border-radius: 0px 0px 7px 7px;
-moz-border-radius: 0px 0px 7px 7px;
-khtml-border-radius: 0px 0px 7px 7px;
border-radius: 0px 0px 7px 7px;
border-left: 1px solid #DBDEE1;
border-right: 1px solid #DBDEE1;
border-bottom: 1px solid #DBDEE1;
padding: 10px;
}
.checkout-product table {
width: 100%;
border-collapse: collapse;
border-top: 1px solid #DDDDDD;
border-left: 1px solid #DDDDDD;
border-right: 1px solid #DDDDDD;
margin-bottom: 20px;
}
.checkout-product table {
border-collapse: collapse;
}
.checkout-product thead td {
	color: #4D4D4D;
	font-weight: bold;
	background-color: #F7F7F7;
	border-bottom: 1px solid #DDDDDD;
	padding:7px;
}
.checkout-product thead .name, .checkout-product thead .model {
	text-align: left;
}
.checkout-product thead .quantity, .checkout-product thead .price, .checkout-product thead .total {
	text-align: right;
}
.checkout-product tbody td {
	vertical-align: top;
	border-bottom: 1px solid #DDDDDD;
	padding:7px;
}
.checkout-product tbody .name, .checkout-product tbody .model {
	text-align: left;
}
.checkout-product tbody .quantity, .checkout-product tbody .price, .checkout-product tbody .total {
	text-align: right;
}
.checkout-product tfoot td {
	text-align: right;
	border-bottom: 1px solid #DDDDDD;
	padding:7px;
}
<?php } ?>
<?php echo $config['checkout_style']; ?>
</style>

<div id="content_holder">
<div class="layout">
<div class="wrap">
		<div class="breadcrumb">
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <?php echo $breadcrumb['separator']; ?>
            <span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
            <a href="<?php echo $breadcrumb['href']; ?>" itemprop="url">
            <span itemprop="title"> <?php echo $breadcrumb['text']; ?> </span>
            </a>
            </span> 
            <?php } ?>
        </div><!-- breadcrumb -->
<?php echo $column_left; ?><?php echo $column_right; ?>

<article id="content" class="checkout-checkout-page">
<div class="wrap">
<?php echo $content_top; ?>
<pre style="display:none"><?php print_r($config);?></pre>
  <h1 class="h1 page-title"><?php echo $heading_title; ?></h1>
  <div  class="checkout">
    <div id="step_1" style="display:<?php if(!$config['checkout_display_login'] && !$config['checkout_display_register'] && !$config['checkout_display_guest']){ echo 'none'; } ?>">
      <div class="checkout-heading"><span><?php echo $text_checkout_option; ?></span></div>
      <div class="checkout-content"></div>
    </div>
    <?php if($checkout_min_order_reached){
    $text = str_replace("{min_order}", $checkout_min_order, $config['text_min_order'][$this->config->get('config_language_id')]);
    if($config['text_min_order'][$this->config->get('config_language_id')]){ echo '<div class="warning">'.$text.'<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>'; } 
    }else{
    ?>
    <div id="step_2">
    <?php if (!$config['checkout_display_only_register'] || $logged) { ?>
    <div class="column column-1" style="width:<?php echo $config['column_width']['column-1']; ?>%">
    <?php if (!$logged) { ?>
    <div id="payment-address" class="has-fields" col-data="<?php echo $config['portlets'][0]['col']; ?>" row-data="<?php echo $config['portlets'][0]['row']; ?>">
      <div class="checkout-heading"><span><?php echo $text_checkout_account; ?></span><i class="loading"></i></div>
      <div class="checkout-content"></div>
    </div>
    <?php } else { ?>
    <div id="payment-address" class="has-fields" col-data="<?php echo $config['portlets'][0]['col']; ?>" row-data="<?php echo $config['portlets'][0]['row']; ?>">
      <div class="checkout-heading"><span><?php echo $text_checkout_payment_address; ?></span><i class="loading"></i></div>
      <div class="checkout-content"></div>
    </div>
    <?php } ?>
    <?php if ($shipping_required) { ?>
    <div id="shipping-address" class="has-fields" col-data="<?php echo $config['portlets'][1]['col']; ?>" row-data="<?php echo $config['portlets'][1]['row']; ?>">
      <div class="checkout-heading"><?php echo $text_checkout_shipping_address; ?><span class="loading"></span></div>
      <div class="checkout-content"></div>
    </div>
    <?php } ?>
    <?php if ($shipping_required) { ?>
    <div id="shipping-method" class="<?php if(!$config['shipping_method_display']){ echo 'hide'; } ?>" col-data="<?php echo $config['portlets'][2]['col']; ?>" row-data="<?php echo $config['portlets'][2]['row']; ?>">
      <div class="checkout-heading"><?php echo $text_checkout_shipping_method; ?><span class="loading"></span></div>
      <div class="checkout-content"></div>
    </div>
    <?php } ?>
    <div id="payment-method" class="<?php if(!$config['payment_method_display']){ echo 'hide'; } ?>" col-data="<?php echo $config['portlets'][3]['col']; ?>" row-data="<?php echo $config['portlets'][3]['row']; ?>">
      <div class="checkout-heading"><?php echo $text_checkout_payment_method; ?><span class="loading"></span></div>
      <div class="checkout-content"></div>
    </div>
     <div id="confirm" col-data="<?php echo $config['portlets'][4]['col']; ?>" row-data="<?php echo $config['portlets'][4]['row']; ?>">
      <div class="checkout-heading"><?php echo $text_checkout_confirm; ?><span class="loading"></span></div>
      <div class="checkout-content"></div>
    </div>
    <div class="extra-position" col-data="<?php echo $config['portlets'][5]['col']; ?>" row-data="<?php echo $config['portlets'][5]['row']; ?>"><?php if(isset($column_extra6)) { echo $column_extra6; } ?></div>
    <div class="extra-position" col-data="<?php echo $config['portlets'][6]['col']; ?>" row-data="<?php echo $config['portlets'][6]['row']; ?>"><?php if(isset($column_extra7)) { echo $column_extra7; } ?></div>
    <div class="extra-position" col-data="<?php echo $config['portlets'][7]['col']; ?>" row-data="<?php echo $config['portlets'][7]['row']; ?>"><?php if(isset($column_extra8)) { echo $column_extra8; } ?></div>
</div>
  <div class="column column-2" style="width:<?php echo $config['column_width']['column-2']; ?>%">
  </div>
  <div class="column column-3" style="width:<?php echo $config['column_width']['column-3']; ?>%">
  </div>
  <br class="clear"/>
  <?php } ?>
  </div>
  <div id="step_3">
  <div id="payment_block"></div>
  </div>
  <?php } ?>
 </div>

<script type="text/javascript"><!--


$('.column > div').tsort({attr:'row-data'});
$('.column > div').each(function(){
				$(this).appendTo('.column-' + $(this).attr('col-data'));
									})
// show options 
$('#step_1 .checkout-content input[name=\'account\']').live('click', function() {
																				 
	if ($(this).attr('value') == 'register') {
		$('#payment-address .checkout-heading span').html('<?php echo $text_checkout_account; ?>');
		$('#guest_option').removeClass('selected')
		$('#register_option').addClass('selected')
		
	} else {
		$('#payment-address .checkout-heading span').html('<?php echo $text_checkout_payment_address; ?>');
		$('#register_option').removeClass('selected')
		$('#guest_option').addClass('selected')
		
	}
	showStepTwo()
});

$('.checkout-heading a').live('click', function() {
	$('#debug').append('<p>======== CLICK EDIT STEP 1 ========</p>')
	
	$('.checkout-content').slideUp('slow');
	if($('#step_1 .checkout-content input[name=\'account\']:checked').attr('value') == 'register'){
	location.reload();
	}else{
	$(this).parent().parent().find('.checkout-content').slideDown('slow');
	showStepTwo()
	}
	$(this).remove();
	
});
<?php if(!$checkout_min_order_reached){ ?>
//Load Document
<?php if (!$logged) { ?> 
$(document).ready(function() {
	$('#debug').append('<p>======== SHOW if not logged in (checkout/login) ========</p>')
	$.ajax({
		url: 'index.php?route=checkout/login',
		dataType: 'html',
		success: function(html) {
			$('#debug').append('<p>--- AJAX Successs (checkout/login)</p>')
			$('#step_1 .checkout-content').html(html);
			$('#step_1 .checkout-content').slideDown('slow');
			showStepTwo()
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
});		
<?php } else { ?>
$(document).ready(function() {
	showFormLoggedIn()
	$('#step_1').hide()
});
<?php } ?>
function showFormLoggedIn(){
	<?php if(!$config['register_shipping_address_enable']){ echo 'var register_shipping = 0; '; }else{ echo 'var register_shipping = 1; '; } ?>
	if(register_shipping == 0) { $('#shipping-address').hide() }

	$('#debug').append('<p>======== SHOW if logged in (checkout/payment_address) ========</p>')
	$('.loading').show()
	$.ajax({
		url: 'index.php?route=checkout/payment_address',
		dataType: 'html',
		success: function(html) {
			$('#debug').append('<p>--- AJAX Successs (checkout/payment_address)</p>')
			$('#payment-address .checkout-content').html(html);				
			$('#payment-address .checkout-content').slideDown('slow');
			registerCheck();
			<?php if ($shipping_required) { ?>
			showRegistrateShipping(function(){
				registrateShippingCheck()
				showShippingMethod(function(){
					shippingMethod(function(){			
						$.ajax({
							url: 'index.php?route=checkout/payment_method',
							type: 'post',
							data: $('#payment-address input[type=\'radio\']:checked'),
							dataType: 'html',
							success: function(html) {
								$('#debug').append('<p>------ AJAX Success (checkout/payment_method)</p>')
								$('#payment-method .checkout-content').html(html);
								$('#payment-method .checkout-content').slideDown('slow');
								paymentMethod(function(){
									showConfirm($('.loading').hide())
								})
							},
							error: function(xhr, ajaxOptions, thrownError) {
								//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
								$('.loading').hide()
							}
						});		
					})
				});
			});
			<?php }else{ ?>

					$.ajax({
						url: 'index.php?route=checkout/payment_method',
						type: 'post',
						data: $('#payment-address input[type=\'radio\']:checked'),
						dataType: 'html',
						success: function(html) {
							$('#debug').append('<p>------ AJAX Success (checkout/payment_method)</p>')
							$('#payment-method .checkout-content').html(html);
							$('#payment-method .checkout-content').slideDown('slow');
							paymentMethod(function(){
								showConfirm($('.loading').hide())
							})
						},
						error: function(xhr, ajaxOptions, thrownError) {
							//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							$('.loading').hide()
						}
					});	
			<?php } ?>
			
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});
}
<?php } ?>
$('#button-login').live('click', function() {						  
	$.ajax({
		url: 'index.php?route=checkout/login/validate',
		type: 'post',
		data: $('#step_1 #login :input'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-login').attr('disabled', true);
			$('#button-login').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('#button-login').attr('disabled', false);
			$('.wait').remove();
		},				
		success: function(json) {
			$('.warning, .error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				$('#step_1 .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
});


// ---------------------------------------------------------------------------------------------------------------------  START
//start the checkout process - load all blocks
function showStepTwo() {
	$('#step_2').show();
	$('#step_3').hide();
	
$('#debug').append('<p>========CLICK Button account (Ajax Start checkout/' + $('input[name=\'account\']:checked').attr('value') + ') ========</p>')
<?php if(!$config['register_shipping_address_enable']){ echo 'var register_shipping = 0; '; }else{ echo 'var register_shipping = 1; '; } ?>
<?php if(!$config['guest_shipping_address_enable']){ echo 'var guest_shipping = 0'; }else{ echo 'var guest_shipping = 1'; } ?>

$('#shipping-address').show()
	if($('input[name=\'account\']:checked').attr('value') == 'register' && register_shipping == 0) { $('#shipping-address').hide() }
	if($('input[name=\'account\']:checked').attr('value') == 'guest' && guest_shipping == 0) { $('#shipping-address').hide() }

$('.loading').show()
//1 load the address form
	var payment_address =  $('input[name=\'account\']:checked').attr('value')

	if(payment_address == undefined ){
		payment_address = 'payment_address';	
	}


	$.ajax({
		url: 'index.php?route=checkout/' + payment_address ,
		dataType: 'html',
		beforeSend: function() {
			$('#button-account').attr('disabled', true);
			
		},		
		complete: function() {
			$('#button-account').attr('disabled', false);
			
		},			
		success: function(html) {
			$('#debug').append('<p>--- AJAX Success (checkout ' + $('input[name=\'account\']:checked').attr('value') + ')</p>')
			$('.warning, .error').remove();
			$('#payment-address .checkout-content').html(html);			
			//$('#step_1 .checkout-content').slideUp('slow');
			$('#payment-address .checkout-content').slideDown('slow');
			$('.checkout-heading a').remove();
			//$('#step_1 .checkout-heading').append('<a class="text-modify"><i class="icon-edit"></i> <?php echo $text_modify; ?></a>');
			
			$.ajax({
				url: 'index.php?route=checkout/' + payment_address +'/check',
				type: 'post',
				data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select'),
				success: function(html) {
					
					
// ----------------------------------------------------------------------------------------------------------------  START SHIPPING IS REQUIRED			
			<?php if ($shipping_required) { ?>
			$('#debug').append('<p>------ Shipping is Required </p>')
			var shipping_address = $('#payment-address input[name=\'shipping_address\']:checked').attr('value');
			if (shipping_address == '1') {
				$('#shipping-address .checkout-content').html('');
				$('#debug').append('<p>--------- Shipping Address Checked (checkout/shipping_method)</p>')

				$.ajax({
					url: 'index.php?route=checkout/shipping_method',
					dataType: 'html',
					success: function(html) {
						
						$('#debug').append('<p>------------ Ajax Success (checkout/shipping_method)</p>')
						
						$('#shipping-method .checkout-content').html(html);
						$('#shipping-method .checkout-content').slideDown('slow');
						
						$.ajax({
							url: 'index.php?route=checkout/payment_method',
							dataType: 'html',
							success: function(html) {
								
							$('#debug').append('<p>--------------- Ajax Success (checkout/payment_method)</p>')
							$('#payment-method .checkout-content').html(html);
							$('#payment-method .checkout-content').slideDown('slow');
							shippingMethod(function(){showValidatePaymentMethod(function(){
								$.ajax({
									url: 'index.php?route=checkout/confirm/show',
									dataType: 'html',
									success: function(html) {
										$('#debug').append('<p>------------------ Ajax Success (checkout/confirm)</p>')
										$('#confirm .checkout-content').html(html);
										$('#confirm .checkout-content').slideDown('slow');
										$('.loading').hide()
										
									},
									error: function(xhr, ajaxOptions, thrownError) {
										//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
										$('.loading').hide()
									}
								});	
							})})	
							},
							error: function(xhr, ajaxOptions, thrownError) {
								//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
								$('.loading').hide()
							}
						});					
					},
					error: function(xhr, ajaxOptions, thrownError) {
					//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					$('.loading').hide()
					}
				});	
			} else {
				
				//2 load the shipping address
				if($('input[name=\'account\']:checked').attr('value') == 'register' || $('input[name=\'account\']:checked').attr('value') == undefined){ 
					var shipping_address_form = 'shipping_address'; 
				}else{ 
					var shipping_address_form = 'guest_shipping';
				}
			
				$('#debug').append('<p>--------- Shipping Address Not checked (checkout/'+shipping_address_form+')</p>')
				$.ajax({
					url: 'index.php?route=checkout/'+shipping_address_form,
					dataType: 'html',
					success: function(html) {
						$('#debug').append('<p>------------ Ajax Success (checkout/'+shipping_address_form+')</p>');
						$('#shipping-address .checkout-content').html(html);
						$('#shipping-address .checkout-content').slideDown('slow');
						$.ajax({
							url: 'index.php?route=checkout/' + shipping_address_form +'/validate',
							type: 'post',
							data: $('#shipping-address input[type=\'text\'], #shipping-address select'),
							dataType: 'json',
							success: function(html) {
								
								//get the shippingMethod
								$.ajax({
									url: 'index.php?route=checkout/shipping_method',
									dataType: 'html',
									success: function(html) {
										$('#debug').append('<p>------------ Ajax Success (checkout/shipping_method)</p>')
										$('#shipping-method .checkout-content').html(html);
										$('#shipping-method .checkout-content').slideDown('slow');	
										$.ajax({
											url: 'index.php?route=checkout/payment_method',
											dataType: 'html',
											success: function(html) {
		
												$('#debug').append('<p>--------------- Ajax Success (checkout/payment_method)</p>')
												$('#payment-method .checkout-content').html(html);
												$('#payment-method .checkout-content').slideDown('slow');
												
												shippingMethod(function(){showValidatePaymentMethod(function(){
													$.ajax({
														url: 'index.php?route=checkout/confirm/show',
														dataType: 'html',
														success: function(html) {
															$('#confirm .checkout-content').html(html);
															$('#confirm .checkout-content').slideDown('slow');
															// ----------------------------------- Confirm Show
															$('#debug').append('<p>------------------ Ajax Success (checkout/confirm)</p>')
															$('.loading').hide()
														},
														error: function(xhr, ajaxOptions, thrownError) {
															//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
															$('.loading').hide()
														}
													});	
												})})
											},
											error: function(xhr, ajaxOptions, thrownError) {
												//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
												$('.loading').hide()
											}
										});					
									},
									error: function(xhr, ajaxOptions, thrownError) {
										//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
										$('.loading').hide()
									}
								});
						},
						error: function(xhr, ajaxOptions, thrownError) {
						//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						$('.loading').hide()
						}
					})
					},
					error: function(xhr, ajaxOptions, thrownError) {
						//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						$('.loading').hide()
					}
				});	
					
			}
// ----------------------------------------------------------------------------------------------------------------  START SHIPPING NOT REQUIRED
		<?php } else { ?>
			$('#debug').append('<p>------ Shipping not required -> do payment method(checkout/payment_method)</p>')
			$.ajax({
				url: 'index.php?route=checkout/payment_method',
				dataType: 'html',
				success: function(html) {
					$('#debug').append('<p>--------- Ajax Success (checkout/payment_method)</p>')
					$('#payment-method .checkout-content').html(html);
					$('#payment-method .checkout-content').slideDown('slow');
					showValidatePaymentMethod(function(){
						$.ajax({
							url: 'index.php?route=checkout/confirm/show',
							dataType: 'html',
							success: function(html) {
								$('#confirm .checkout-content').html(html);
								$('#confirm .checkout-content').slideDown('slow');
								// ----------------------------------- Confirm Show
								$('#debug').append('<p>--------------- Ajax Success (checkout/confirm/show)</p>')
								$('.loading').hide()
							},
							error: function(xhr, ajaxOptions, thrownError) {
								//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
								$('.loading').hide()
							}
						});	
					});
				},
				error: function(xhr, ajaxOptions, thrownError) {
					//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					$('.loading').hide()
				}
			});					
		<?php } ?>
				}
			})
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});		
}
// ---------------------------------------------------------------------------------------------------------------------  REGISTRATE CHECK
function registerCheck(field, func) {
	$('#debug').append('<p>======== CHECK REGISTER ========</p>');
	
	<?php if (!$logged) { ?> 
	$('#debug').append('<p> --- AJAX if not logged in (checkout/register/check)</p>')
	$.ajax({
		url: 'index.php?route=checkout/register/check',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
		$('.wait').remove();
		$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},		
		success: function(json) {
			$('#debug').append('<p>------ AJAX Success (checkout/register/check)</p>')

			if (json['redirect']) {
				$('#debug').append('<p>--------- AJAX Redirect (checkout/register/check)</p>')
				//location = json['redirect'];
				$('.loading').hide()
			} else if(field){
				$('.wait').remove();
				//$('.loading').hide()
				$('#payment-address .warning').remove();
				$('#payment-address #confirm_input .error').remove();
				$('#payment-address #'+field+'_input .error').remove();
				
				if (json['error']['warning']) {
					$('#debug').append('<p>--------- WARNING (checkout/register/check)</p>');
					$('#payment-address .checkout-content').prepend('<div class="warning  alert alert-error" style="display: none;">' + json['error']['warning'] + '<a class="close  icon-remove"></a></div>');
					
					$('.warning').fadeIn('slow');
				}
				
				if(field != 'confirm'){
					if (json['error']['confirm']) {
						$('#debug').append('<p>--------- ERROR confirm (checkout/register/check)</p>');
						$('#payment-address input[name=\'confirm\'] ').after('<div class="error alert alert-error">' + json['error']['confirm'] + '</div>');
					}	
				}			
				if (json['error'][field]) {
					$('#debug').append('<p>--------- ERROR (checkout/register/check)</p>');
					$('#payment-address input[name=\''+field+'\'] ').after('<div class="error alert alert-error">' + json['error'][field] + '</div>');
				}

				
			} else if (json['error']) {
				$('#debug').append('<p>--------- ERROR (checkout/register/check)</p>');
				$('.loading').hide()
				
				$('#payment-address  .warning, #payment-address  .error').remove();
				if (json['error']['warning']) {
					$('#payment-address .checkout-content').prepend('<div class="warning  alert alert-error" style="display: none;">' + json['error']['warning'] + '<a class="close  icon-remove"></a></div>');
					
					$('.warning').fadeIn('slow');
				}
				
				if (json['error']['firstname']) {
					$('#payment-address input[name=\'firstname\'] ').after('<div class="error alert alert-error">' + json['error']['firstname'] + '</div>');
				}
				
				if (json['error']['lastname']) {
					$('#payment-address input[name=\'lastname\'] ').after('<div class="error alert alert-error">' + json['error']['lastname'] + '</div>');
				}	
				
				if (json['error']['email']) {
					$('#payment-address input[name=\'email\'] ').after('<div class="error alert alert-error">' + json['error']['email'] + '</div>');
				}
				
				if (json['error']['telephone']) {
					$('#payment-address input[name=\'telephone\'] ').after('<div class="error alert alert-error">' + json['error']['telephone'] + '</div>');
				}	
					
				if (json['error']['company_id']) {
					$('#payment-address input[name=\'company_id\'] ').after('<div class="error alert alert-error">' + json['error']['company_id'] + '</div>');
				}	
				
				if (json['error']['tax_id']) {
					$('#payment-address input[name=\'tax_id\'] ').after('<div class="error alert alert-error">' + json['error']['tax_id'] + '</div>');
				}	
																		
				if (json['error']['address_1']) {
					$('#payment-address input[name=\'address_1\'] ').after('<div class="error alert alert-error">' + json['error']['address_1'] + '</div>');
				}	
				
				if (json['error']['city']) {
					$('#payment-address input[name=\'city\'] ').after('<div class="error alert alert-error">' + json['error']['city'] + '</div>');
				}	
				
				if (json['error']['postcode']) {
					$('#payment-address input[name=\'postcode\'] ').after('<div class="error alert alert-error">' + json['error']['postcode'] + '</div>');
				}	
				
				if (json['error']['country']) {
					$('#payment-address select[name=\'country_id\'] ').after('<div class="error alert alert-error">' + json['error']['country'] + '</div>');
				}	
				
				if (json['error']['zone']) {
					$('#payment-address select[name=\'zone_id\'] ').after('<div class="error alert alert-error">' + json['error']['zone'] + '</div>');
				}
				
				if (json['error']['password']) {
					$('#payment-address input[name=\'password\'] ').after('<div class="error alert alert-error">' + json['error']['password'] + '</div>');
				}	
				
				if (json['error']['confirm']) {
					$('#payment-address input[name=\'confirm\'] ').after('<div class="error alert alert-error">' + json['error']['confirm'] + '</div>');
				}																																	
			} else {
				$('#debug').append('<p>--------- REGISTER CHECK GOOD (checkout/register/check)</p>')
				if (typeof func == "function") func(); 
			} 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
	<?php } else { ?>
	$('#debug').append('<p>--- AJAX is logged in (checkout/payment_address/check)</p>')
	
	$.ajax({		
		url: 'index.php?route=checkout/payment_address/check',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('.wait').remove();
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			$('#debug').append('<p>------ AJAX Success (checkout/payment_address/check)</p>')
			$('#payment-address .warning, #payment-address .error').remove();
			
			if (json['redirect']) {
				//location = json['redirect'];
				$('.loading').hide()
			} else if(field){
				$('.wait').remove();
				$('.loading').hide()
				$('#payment-address .warning').remove();
				/*$('#payment-address #confirm_input .error').remove();*/
				$('#payment-address #'+field+'_input .error').remove();
				
				if (json['error']['warning']) {
					$('#debug').append('<p>--------- WARNING (checkout/register/check)</p>');
					$('#payment-address .checkout-content').prepend('<div class="warning  alert alert-error" style="display: none;">' + json['error']['warning'] + '<a class="close  icon-remove"></a></div>');
					
					$('.warning').fadeIn('slow');
				}
				
				/*if(field != 'confirm'){
					if (json['error']['confirm']) {
						$('#payment-address input[name=\'confirm\'] ').after('<div class="error alert alert-error">' + json['error']['confirm'] + '</div>');
					}	
				}	*/		
				if (json['error'][field]) {
					$('#debug').append('<p>--------- ERROR from '+field+'(checkout/register/check)</p>');
					$('#payment-address input[name=\''+field+'\'] ').after('<div class="error alert alert-error">' + json['error'][field] + '</div>');
				}
			} else if (json['error']) {
				$('#debug').append('<p>--------- ERROR (checkout/payment_address/check)</p>');
				$('.loading').hide()
				if (json['error']['warning']) {
					$('#payment-address .checkout-content').prepend('<div class="warning  alert alert-error" style="display: none;">' + json['error']['warning'] + '<a class="close  icon-remove"></a></div>');
					
					$('.warning').fadeIn('slow');
				}
								
				if (json['error']['firstname']) {
					$('#payment-address input[name=\'firstname\']').after('<div class="error alert alert-error">' + json['error']['firstname'] + '</div>');
				}
				
				if (json['error']['lastname']) {
					$('#payment-address input[name=\'lastname\']').after('<div class="error alert alert-error">' + json['error']['lastname'] + '</div>');
				}	
				
				if (json['error']['telephone']) {
					$('#payment-address input[name=\'telephone\']').after('<div class="error alert alert-error">' + json['error']['telephone'] + '</div>');
				}		
				
				if (json['error']['company_id']) {
					$('#payment-address input[name=\'company_id\']').after('<div class="error alert alert-error">' + json['error']['company_id'] + '</div>');
				}	
				
				if (json['error']['tax_id']) {
					$('#payment-address input[name=\'tax_id\']').after('<div class="error alert alert-error">' + json['error']['tax_id'] + '</div>');
				}	
														
				if (json['error']['address_1']) {
					$('#payment-address input[name=\'address_1\']').after('<div class="error alert alert-error">' + json['error']['address_1'] + '</div>');
				}	
				
				if (json['error']['city']) {
					$('#payment-address input[name=\'city\']').after('<div class="error alert alert-error">' + json['error']['city'] + '</div>');
				}	
				
				if (json['error']['postcode']) {
					$('#payment-address input[name=\'postcode\']').after('<div class="error alert alert-error">' + json['error']['postcode'] + '</div>');
				}	
				
				if (json['error']['country']) {
					$('#payment-address select[name=\'country_id\']').after('<div class="error alert alert-error">' + json['error']['country'] + '</div>');
				}	
				
				if (json['error']['zone']) {
					$('#payment-address select[name=\'zone_id\']').after('<div class="error alert alert-error">' + json['error']['zone'] + '</div>');
				}																																
			} else {
				$('#debug').append('<p>--------- REGISTER CHECK GOOD (checkout/payment_address/check)</p>')
				if (typeof func == "function") func(); 
			} 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});		
	<?php } ?>
}
function registerUpdate(func) {
	$('#debug').append('<p>======== UPDATE REGISTER ========</p>');
	
	<?php if (!$logged) { ?> 
	$('#debug').append('<p> --- AJAX if not logged in (checkout/register/update)</p>')
	$.ajax({
		url: 'index.php?route=checkout/register/update',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
		},	
		complete: function() {
		},		
		success: function(json) {
				$('#debug').append('<p>--------- REGISTER UPDATE GOOD (checkout/register/update)</p>')
				if (typeof func == "function") func(); 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
	<?php } else { ?>
	$('#debug').append('<p>--- AJAX is logged in (checkout/payment_address/update)</p>')
	
	$.ajax({		
		url: 'index.php?route=checkout/payment_address/update',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
		},	
		complete: function() {
		},			
		success: function(json, status, jqXHR) {
				//alert(jqXHR.responseText)
				$('#debug').append('<p>--------- REGISTER UPDATE GOOD (checkout/payment_address/check)</p>')
				if (typeof func == "function") func(); 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});		
	<?php } ?>
}
// ---------------------------------------------------------------------------------------------------------------------  SET PAYMENT COUNTRY
function paymentCountry(func) {
	$('#debug').append('<p>======== SET PAYMENT COUNTRY ========</p>');
	$.ajax({
		url: 'index.php?route=checkout/payment_address/country',
		type: 'post',
		data: $('#payment-address select, #payment-address input[name=\'postcode\']'),
		dataType: 'html',
		beforeSend: function() {
			$('.wait').remove();
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},			
		success: function(html) {
			$('#debug').append('<p>--- AJAX Success (checkout/payment_address/country)</p>')
				if (typeof func == "function") func(); 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
}
// ---------------------------------------------------------------------------------------------------------------------  SET SHIPPING COUNTRY
function shippingCountry(func) {
	$('#debug').append('<p>======== SET SHIPPING COUNTRY ========</p>');
	$.ajax({
		url: 'index.php?route=checkout/shipping_address/country',
		type: 'post',
		data: $('#shipping-address select, #shipping-address input[name=\'postcode\']'),
		dataType: 'html',
		beforeSend: function() {
			$('.wait').remove();
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},			
		success: function(html) {
			$('#debug').append('<p>--- AJAX Success (checkout/shipping_address/country)</p>')
				if (typeof func == "function") func(); 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
}
// ---------------------------------------------------------------------------------------------------------------------  REGISTRATE VALIDATE
function registerValidate(func) {
	$('#debug').append('<p>======== CHECK REGISTER ========</p>');

	<?php if (!$logged) { ?> 
	$('#debug').append('<p> --- AJAX if not logged in (checkout/register/validate)</p>')
	$.ajax({
		url: 'index.php?route=checkout/register/validate',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('.wait').remove();
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},		
		success: function(json) {
			$('#debug').append('<p>------ AJAX Success (checkout/register/validate)</p>')

			if (json['redirect']) {
				//location = json['redirect'];
				$('.loading').hide()
			} else if (json['error']) {
				$('#debug').append('<p>--------- ERROR (checkout/register/validate)</p>');
				$('.loading').hide()
				
				$('#payment-address  .warning, #payment-address  .error').remove();
				if (json['error']['warning']) {
					$('#payment-address .checkout-content').prepend('<div class="warning  alert alert-error" style="display: none;">' + json['error']['warning'] + '<a class="close  icon-remove"></a></div>');
					
					$('.warning').fadeIn('slow');
				}
				
				if (json['error']['firstname']) {
					$('#payment-address input[name=\'firstname\'] ').after('<div class="error alert alert-error">' + json['error']['firstname'] + '</div>');
				}
				
				if (json['error']['lastname']) {
					$('#payment-address input[name=\'lastname\'] ').after('<div class="error alert alert-error">' + json['error']['lastname'] + '</div>');
				}	
				
				if (json['error']['email']) {
					$('#payment-address input[name=\'email\'] ').after('<div class="error alert alert-error">' + json['error']['email'] + '</div>');
				}
				
				if (json['error']['telephone']) {
					$('#payment-address input[name=\'telephone\'] ').after('<div class="error alert alert-error">' + json['error']['telephone'] + '</div>');
				}	
					
				if (json['error']['company_id']) {
					$('#payment-address input[name=\'company_id\'] ').after('<div class="error alert alert-error">' + json['error']['company_id'] + '</div>');
				}	
				
				if (json['error']['tax_id']) {
					$('#payment-address input[name=\'tax_id\'] ').after('<div class="error alert alert-error">' + json['error']['tax_id'] + '</div>');
				}	
																		
				if (json['error']['address_1']) {
					$('#payment-address input[name=\'address_1\'] ').after('<div class="error alert alert-error">' + json['error']['address_1'] + '</div>');
				}	
				
				if (json['error']['city']) {
					$('#payment-address input[name=\'city\'] ').after('<div class="error alert alert-error">' + json['error']['city'] + '</div>');
				}	
				
				if (json['error']['postcode']) {
					$('#payment-address input[name=\'postcode\'] ').after('<div class="error alert alert-error">' + json['error']['postcode'] + '</div>');
				}	
				
				if (json['error']['country']) {
					$('#payment-address select[name=\'country_id\'] ').after('<div class="error alert alert-error">' + json['error']['country'] + '</div>');
				}	
				
				if (json['error']['zone']) {
					$('#payment-address select[name=\'zone_id\'] ').after('<div class="error alert alert-error">' + json['error']['zone'] + '</div>');
				}
				
				if (json['error']['password']) {
					$('#payment-address input[name=\'password\'] ').after('<div class="error alert alert-error">' + json['error']['password'] + '</div>');
				}	
				
				if (json['error']['confirm']) {
					$('#payment-address input[name=\'confirm\'] ').after('<div class="error alert alert-error">' + json['error']['confirm'] + '</div>');
				}																																	
			} else {
				$('#debug').append('<p>--------- REGISTER VALIDATE GOOD (checkout/register/validate)</p>')
				$('#registered').val('1')
				if (typeof func == "function") func(); 
			} 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
	<?php } else { ?>
	$('#debug').append('<p>--- AJAX is logged in (checkout/payment_address/validate)</p>')
	$.ajax({		
		url: 'index.php?route=checkout/payment_address/validate',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('.wait').remove();
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			$('#debug').append('<p>------ AJAX Success (checkout/payment_address/validate)</p>')
			$('#payment-address .warning, #payment-address .error').remove();
			
			if (json['redirect']) {
				//location = json['redirect'];
				$('.loading').hide()
			} else if (json['error']) {
				$('#debug').append('<p>--------- ERROR (checkout/payment_address/validate)</p>');
				$('.loading').hide()
				
				if (json['error']['warning']) {
					$('#payment-address .checkout-content').prepend('<div class="warning  alert alert-error" style="display: none;">' + json['error']['warning'] + '<a class="close  icon-remove"></a></div>');
					
					$('.warning').fadeIn('slow');
				}
								
				if (json['error']['firstname']) {
					$('#payment-address input[name=\'firstname\']').after('<div class="error alert alert-error">' + json['error']['firstname'] + '</div>');
				}
				
				if (json['error']['lastname']) {
					$('#payment-address input[name=\'lastname\']').after('<div class="error alert alert-error">' + json['error']['lastname'] + '</div>');
				}	
				
				if (json['error']['telephone']) {
					$('#payment-address input[name=\'telephone\']').after('<div class="error alert alert-error">' + json['error']['telephone'] + '</div>');
				}		
				
				if (json['error']['company_id']) {
					$('#payment-address input[name=\'company_id\']').after('<div class="error alert alert-error">' + json['error']['company_id'] + '</div>');
				}	
				
				if (json['error']['tax_id']) {
					$('#payment-address input[name=\'tax_id\']').after('<div class="error alert alert-error">' + json['error']['tax_id'] + '</div>');
				}	
														
				if (json['error']['address_1']) {
					$('#payment-address input[name=\'address_1\']').after('<div class="error alert alert-error">' + json['error']['address_1'] + '</div>');
				}	
				
				if (json['error']['city']) {
					$('#payment-address input[name=\'city\']').after('<div class="error alert alert-error">' + json['error']['city'] + '</div>');
				}	
				
				if (json['error']['postcode']) {
					$('#payment-address input[name=\'postcode\']').after('<div class="error alert alert-error">' + json['error']['postcode'] + '</div>');
				}	
				
				if (json['error']['country']) {
					$('#payment-address select[name=\'country_id\']').after('<div class="error alert alert-error">' + json['error']['country'] + '</div>');
				}	
				
				if (json['error']['zone']) {
					$('#payment-address select[name=\'zone_id\']').after('<div class="error alert alert-error">' + json['error']['zone'] + '</div>');
				}																																
			} else {
				$('#debug').append('<p>--------- REGISTER VALIDATE GOOD (checkout/payment_address/validate)</p>')
				if (typeof func == "function") func(); 
			} 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});		
	<?php } ?>
}

// --------------------------------------------------------------------------------------------------------------------- SHOW REGISTRATE SHIPPING
function showRegistrateShipping(func){
	$('#debug').append('<p>======== SHOW REGISTER SHIPPING ========</p>')
	var shipping_address = $('#payment-address input[name=\'shipping_address\']:checked').attr('value');	
	
	if (shipping_address == 1) {
		$('#debug').append('<p>--- Hide Shipping address </p>')
		$('#shipping-address .checkout-content').slideUp('slow',function(){										 
			$('#shipping-address .checkout-content').html(''); 
			if (typeof func == "function") func(); 
		});	
	} else {	
		$.ajax({
			url: 'index.php?route=checkout/shipping_address',
			dataType: 'html',
			success: function(html) {
				
				$('#debug').append('<p>--- Ajax Success (checkout/shipping_address)</p>')
				$('#shipping-address .checkout-content').html(html);
				$('#shipping-address .checkout-content').slideDown('slow');
				if (typeof func == "function") func(); 
			},
			error: function(xhr, ajaxOptions, thrownError) {
				//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}
// --------------------------------------------------------------------------------------------------------------------- CHECK REGISTRATE SHIPPING ADDRESS
function registrateShippingCheck(field, func){
	$('#debug').append('<p>========  CHECK REGISTRATE SHIPPING ADDRESS ========</p>')
	$.ajax({
		url: 'index.php?route=checkout/shipping_address/check',
		type: 'post',
		data: $('#shipping-address input[type=\'text\'], #shipping-address input[type=\'password\'], #shipping-address input[type=\'checkbox\']:checked, #shipping-address input[type=\'radio\']:checked, #shipping-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('.wait').remove();
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			$('#debug').append('<p>--- AJAX Success (checkout/shipping_address/check)</p>')

			if (json['redirect']) {
				//location = json['redirect'];
				$('.loading').hide()
			} else if(field){
				$('.wait').remove();
				$('.loading').hide()
				
				$('#shipping-address #shipping_'+field+'_input .warning, #shipping-address #shipping_'+field+'_input .error').remove();
				
				if (json['error'][field]) {
					$('#shipping-address input[name=\''+field+'\'] ').after('<div class="error alert alert-error">' + json['error'][field] + '</div>');
				}
			} else if (json['error']) {
				$('#debug').append('<p>- (3) RegistrateShipping() ERROR checkout/shipping_address/check</p>')
				$('.loading').hide()
				if (json['error']['warning']) {
					$('#shipping-address .checkout-content').prepend('<div class="warning  alert alert-error" style="display: none;">' + json['error']['warning'] + '<a class="close  icon-remove"></a></div>');
					
					$('.warning').fadeIn('slow');
				}
								
				if (json['error']['firstname']) {
					$('#shipping-address input[name=\'firstname\']').after('<div class="error alert alert-error">' + json['error']['firstname'] + '</div>');
				}
				
				if (json['error']['lastname']) {
					$('#shipping-address input[name=\'lastname\']').after('<div class="error alert alert-error">' + json['error']['lastname'] + '</div>');
				}	
				
				if (json['error']['email']) {
					$('#shipping-address input[name=\'email\']').after('<div class="error alert alert-error">' + json['error']['email'] + '</div>');
				}
				
				if (json['error']['telephone']) {
					$('#shipping-address input[name=\'telephone\']').after('<div class="error alert alert-error">' + json['error']['telephone'] + '</div>');
				}		
										
				if (json['error']['address_1']) {
					$('#shipping-address input[name=\'address_1\']').after('<div class="error alert alert-error">' + json['error']['address_1'] + '</div>');
				}	
				
				if (json['error']['city']) {
					$('#shipping-address input[name=\'city\']').after('<div class="error alert alert-error">' + json['error']['city'] + '</div>');
				}	
				
				if (json['error']['postcode']) {
					$('#shipping-address input[name=\'postcode\']').after('<div class="error alert alert-error">' + json['error']['postcode'] + '</div>');
				}	
				
				if (json['error']['country']) {
					$('#shipping-address select[name=\'country_id\']').after('<div class="error alert alert-error">' + json['error']['country'] + '</div>');
				}	
				
				if (json['error']['zone']) {
					$('#shipping-address select[name=\'zone_id\']').after('<div class="error alert alert-error">' + json['error']['zone'] + '</div>');
				}
			} else {
				$('#shipping-address .warning, #shipping-address  .error').remove();
				$('#debug').append('<p>------ CHECK ADDRESS GOOD (checkout/shipping_address/check)</p>')
				if (typeof func == "function") func(); 
			}  
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
}
// --------------------------------------------------------------------------------------------------------------------- CHECK REGISTRATE SHIPPING ADDRESS
function registrateShippingUpdate(func){
	$('#debug').append('<p>========  UPDATE REGISTRATE SHIPPING ADDRESS ========</p>')
	$.ajax({
		url: 'index.php?route=checkout/shipping_address/update',
		type: 'post',
		data: $('#shipping-address input[type=\'text\'], #shipping-address input[type=\'password\'], #shipping-address input[type=\'checkbox\']:checked, #shipping-address input[type=\'radio\']:checked, #shipping-address select'),
		dataType: 'json',
		beforeSend: function() {
		},	
		complete: function() {
		},			
		success: function(json) {
				$('#debug').append('<p>------ UPDATE ADDRESS GOOD (checkout/shipping_address/update)</p>')
				if (typeof func == "function") func(); 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
}
// --------------------------------------------------------------------------------------------------------------------- VALIDATE REGISTRATE SHIPPING ADDRESS
function registrateShippingValidate(field, func){
	$('#debug').append('<p>========  VALIDATE REGISTRATE SHIPPING ADDRESS ========</p>')
	$.ajax({
		url: 'index.php?route=checkout/shipping_address/validate',
		type: 'post',
		data: $('#shipping-address input[type=\'text\'], #shipping-address input[type=\'password\'], #shipping-address input[type=\'checkbox\']:checked, #shipping-address input[type=\'radio\']:checked, #shipping-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('.wait').remove();
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			$('#debug').append('<p>--- AJAX Success (checkout/shipping_address/validate)</p>')
			if (json['redirect']) {
				//location = json['redirect'];
				$('.loading').hide()
			} else if(field){
				
				$('.wait').remove();
				$('.loading').hide()
				
				$('#shipping-address #shipping_'+field+'_input .warning, #shipping-address #shipping_'+field+'_input .error').remove();
				
				if (json['error'][field]) {
					$('#shipping-address input[name=\''+field+'\'] ').after('<div class="error alert alert-error">' + json['error'][field] + '</div>');
				}
			} else if (json['error']) {
				$('#debug').append('<p>- (3) RegistrateShipping() ERROR checkout/shipping_address/validate</p>')
				$('.loading').hide()
				if (json['error']['warning']) {
					$('#shipping-address .checkout-content').prepend('<div class="warning  alert alert-error" style="display: none;">' + json['error']['warning'] + '<a class="close  icon-remove"></a></div>');
					
					$('.warning').fadeIn('slow');
				}
								
				if (json['error']['firstname']) {
					$('#shipping-address input[name=\'firstname\']').after('<div class="error alert alert-error">' + json['error']['firstname'] + '</div>');
				}
				
				if (json['error']['lastname']) {
					$('#shipping-address input[name=\'lastname\']').after('<div class="error alert alert-error">' + json['error']['lastname'] + '</div>');
				}	
				
				if (json['error']['email']) {
					$('#shipping-address input[name=\'email\']').after('<div class="error alert alert-error">' + json['error']['email'] + '</div>');
				}
				
				if (json['error']['telephone']) {
					$('#shipping-address input[name=\'telephone\']').after('<div class="error alert alert-error">' + json['error']['telephone'] + '</div>');
				}		
										
				if (json['error']['address_1']) {
					$('#shipping-address input[name=\'address_1\']').after('<div class="error alert alert-error">' + json['error']['address_1'] + '</div>');
				}	
				
				if (json['error']['city']) {
					$('#shipping-address input[name=\'city\']').after('<div class="error alert alert-error">' + json['error']['city'] + '</div>');
				}	
				
				if (json['error']['postcode']) {
					$('#shipping-address input[name=\'postcode\']').after('<div class="error alert alert-error">' + json['error']['postcode'] + '</div>');
				}	
				
				if (json['error']['country']) {
					$('#shipping-address select[name=\'country_id\']').after('<div class="error alert alert-error">' + json['error']['country'] + '</div>');
				}	
				
				if (json['error']['zone']) {
					$('#shipping-address select[name=\'zone_id\']').after('<div class="error alert alert-error">' + json['error']['zone'] + '</div>');
				}
			} else {
				$('#shipping-address .warning, #shipping-address  .error').remove();
				$('#debug').append('<p>------ VALIDATE ADDRESS GOOD (checkout/shipping_address/validate)</p>')
				if (typeof func == "function") func(); 
			}  
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
}

// ---------------------------------------------------------------------------------------------------------------------  GUEST CHECK
function guestCheck(field, func){
	$('#debug').append('<p>======== GUEST CHECK ========</p>')
	$.ajax({
		url: 'index.php?route=checkout/guest/validate',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'checkbox\']:checked, #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('.wait').remove();
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			$('#debug').append('<p>--- AJAX Success (checkout/guest/validate)</p>')
			//$('#payment-address  .warning, #payment-address  .error').remove();
			if (json['redirect']) {
				$('#debug').append('<p>--- ERROR (checkout/guest/validate)</p>')
				$('.loading').hide()
				//location = json['redirect'];
			} else if(field){
				//$('.wait').remove();
				$('.loading').hide()
				$('#payment-address .warning').remove();
				$('#payment-address #confirm_input .error').remove();
				$('#payment-address #'+field+'_input .error').remove();
				
				if (json['error']['warning']) {
					$('#debug').append('<p>--------- WARNING (checkout/register/check)</p>');
					$('#payment-address .checkout-content').prepend('<div class="warning  alert alert-error" style="display: none;">' + json['error']['warning'] + '<a class="close  icon-remove"></a></div>');
					
					$('.warning').fadeIn('slow');
				}
	
				if (json['error'][field]) {
					$('#debug').append('<p>--------- ERROR (checkout/register/check)</p>');
					$('#payment-address input[name=\''+field+'\'] ').after('<div class="error alert alert-error">' + json['error'][field] + '</div>');
				}
			} else if (json['error']) {
				$('#debug').append('<p>------ ERROR (checkout/guest/validate)</p>');
				$('.loading').hide()
				
				if (json['error']['warning']) {
					$('#payment-address .checkout-content').prepend('<div class="warning  alert alert-error" style="display: none;">' + json['error']['warning'] + '<a class="close  icon-remove"></a></div>');
					$('.warning').fadeIn('slow');
				}
				if (json['error']['firstname']) {
					$('#payment-address input[name=\'firstname\'] ').after('<div class="error alert alert-error">' + json['error']['firstname'] + '</div>');
				}
				if (json['error']['lastname']) {
					$('#payment-address input[name=\'lastname\'] ').after('<div class="error alert alert-error">' + json['error']['lastname'] + '</div>');
				}	
				if (json['error']['email']) {
					$('#payment-address input[name=\'email\'] ').after('<div class="error alert alert-error">' + json['error']['email'] + '</div>');
				}
				
				if (json['error']['telephone']) {
					$('#payment-address input[name=\'telephone\'] ').after('<div class="error alert alert-error">' + json['error']['telephone'] + '</div>');
				}	
					
				if (json['error']['company_id']) {
					$('#payment-address input[name=\'company_id\'] ').after('<div class="error alert alert-error">' + json['error']['company_id'] + '</div>');
				}	
				
				if (json['error']['tax_id']) {
					$('#payment-address input[name=\'tax_id\'] ').after('<div class="error alert alert-error">' + json['error']['tax_id'] + '</div>');
				}	
																		
				if (json['error']['address_1']) {
					$('#payment-address input[name=\'address_1\'] ').after('<div class="error alert alert-error">' + json['error']['address_1'] + '</div>');
				}	
				
				if (json['error']['city']) {
					$('#payment-address input[name=\'city\'] ').after('<div class="error alert alert-error">' + json['error']['city'] + '</div>');
				}	
				
				if (json['error']['postcode']) {
					$('#payment-address input[name=\'postcode\'] ').after('<div class="error alert alert-error">' + json['error']['postcode'] + '</div>');
				}	
				
				if (json['error']['country']) {
					$('#payment-address select[name=\'country_id\'] ').after('<div class="error alert alert-error">' + json['error']['country'] + '</div>');
				}	
				
				if (json['error']['zone']) {
					$('#payment-address select[name=\'zone_id\'] ').after('<div class="error alert alert-error">' + json['error']['zone'] + '</div>');
				}
			} else {
				$('#debug').append('<p>--------- GUEST CHECK GOOD (checkout/guest/validate)</p>')
				if (typeof func == "function") func(); 
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});
}
function guestUpdate(func){
	$('#debug').append('<p>======== GUEST UPDATE ========</p>')
	$.ajax({
		url: 'index.php?route=checkout/guest/update',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'checkbox\']:checked, #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
		},	
		complete: function() {
		},			
		success: function(json, status, jqXHR) {
			//alert(jqXHR.responseText)
				$('#debug').append('<p>--------- GUEST UPDATE GOOD (checkout/guest/validate)</p>')
				if (typeof func == "function") func(); 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});
}
// --------------------------------------------------------------------------------------------------------------------- SHOW SHIPPING GUEST
function showGuestShipping(func){
	$('#debug').append('<p>----------- Start showGuestShipping -----------</p>')
	var shipping_address = $('#payment-address input[name=\'shipping_address\']:checked').attr('value');		
	if (shipping_address) {
		$('#shipping-address .checkout-content').slideUp('slow',function(){										 
			$('#shipping-address .checkout-content').html(''); 
			if (typeof func == "function") func(); 
		});	
	} else {
		$.ajax({
			url: 'index.php?route=checkout/guest_shipping',
			dataType: 'html',
			beforeSend: function() {
				$('.wait').remove();
				$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},	
			complete: function() {
				$('.wait').remove();
			},	
			success: function(html) {
				
				$('#debug').append('<p>3.3 Ajax Success showGuestShipping</p>')
				$('#shipping-address .checkout-content').html(html);
				$('#shipping-address .checkout-content').slideDown('slow');
				if (typeof func == "function") func(); 

			},
			error: function(xhr, ajaxOptions, thrownError) {
				//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				$('.loading').hide()
			}
		});
	}
}

// --------------------------------------------------------------------------------------------------------------------- CHECK GUEST SHIPPING ADDRESS
function guestShippingCheck(field, func){
$('#debug').append('<p>======== CHECK GUEST SHIPPING ADDRESS ========</p>')	
	$.ajax({
		url: 'index.php?route=checkout/guest_shipping/validate',
		type: 'post',
		data: $('#shipping-address input[type=\'text\'], #shipping-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('.wait').remove();
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},		
		success: function(json) {
			$('#debug').append('<p>--- Ajax Success (guestShippingCheck)</p>')	
			
			if (json['redirect']) {
				// location = json['redirect'];
				$('.loading').hide()
			} else if(field){
				$('.loading').hide()
				$('#shipping-address .warning').remove();
				$('#shipping-address #shipping_'+field+'_input .error').remove();
				
				if (json['error']['warning']) {
					$('#debug').append('<p>--------- WARNING (checkout/register/check)</p>');
					$('#shipping-address .checkout-content').prepend('<div class="warning  alert alert-error" style="display: none;">' + json['error']['warning'] + '<a class="close  icon-remove"></a></div>');
					
					$('.warning').fadeIn('slow');
				}
	
				if (json['error'][field]) {
					$('#debug').append('<p>--------- ERROR (checkout/register/check)</p>');
					$('#shipping-address input[name=\''+field+'\'] ').after('<div class="error alert alert-error">' + json['error'][field] + '</div>');
				}
			} else if (json['error']) {
				$('#shipping-address .warning, #shipping-address .error').remove();
				$('.loading').hide()
				if (json['error']['warning']) {
					$('#shipping-address .checkout-content').prepend('<div class="warning  alert alert-error" style="display: none;">' + json['error']['warning'] + '<a class="close  icon-remove"></a></div>');
					$('.warning').fadeIn('slow');
				}
				if (json['error']['firstname']) {
					$('#shipping-address input[name=\'firstname\']').after('<div class="error alert alert-error">' + json['error']['firstname'] + '</div>');
				}
				if (json['error']['lastname']) {
					$('#shipping-address input[name=\'lastname\']').after('<div class="error alert alert-error">' + json['error']['lastname'] + '</div>');
				}							
				if (json['error']['address_1']) {
					$('#shipping-address input[name=\'address_1\']').after('<div class="error alert alert-error">' + json['error']['address_1'] + '</div>');
				}	
				if (json['error']['city']) {
					$('#shipping-address input[name=\'city\']').after('<div class="error alert alert-error">' + json['error']['city'] + '</div>');
				}	
				if (json['error']['postcode']) {
					$('#shipping-address input[name=\'postcode\']').after('<div class="error alert alert-error">' + json['error']['postcode'] + '</div>');
				}	
				if (json['error']['country']) {
					$('#shipping-address select[name=\'country_id\']').after('<div class="error alert alert-error">' + json['error']['country'] + '</div>');
				}	
				if (json['error']['zone']) {
					$('#shipping-address select[name=\'zone_id\']').after('<div class="error alert alert-error">' + json['error']['zone'] + '</div>');
				}

			} else {
				$('#debug').append('<p>--- CHECK GUEST ADDRESS GOOD (guestShippingCheck)</p>')
				if (typeof func == "function") func(); 
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
};

// --------------------------------------------------------------------------------------------------------------------- UPDATE VALUE GUEST SHIPPING ADDRESS
function guestShippingUpdate(func){
$('#debug').append('<p>======== CHECK GUEST SHIPPING ADDRESS ========</p>')	
	$.ajax({
		url: 'index.php?route=checkout/guest_shipping/update',
		type: 'post',
		data: $('#shipping-address input[type=\'text\'], #shipping-address select'),
		dataType: 'json',
		beforeSend: function() {
		},	
		complete: function() {
		},		
		success: function(json, status, jqXHR) {
			//alert(jqXHR.responseText)
				$('#debug').append('<p>--- CHECK GUEST ADDRESS GOOD (guestShippingCheck)</p>')
				if (typeof func == "function") func(); 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
};


// --------------------------------------------------------------------------------------------------------------------- SHOW SHIPPING METHOD
function showShippingMethod(func){
	$('#debug').append('<p>======== SHOW SHIPPING METHOD ========</p>');
	//send an address
	$.ajax({
		url: 'index.php?route=checkout/shipping_method',
		type: 'post',
		data: $('#shipping-address input[type=\'radio\']:checked, #shipping-address select'),
		dataType: 'html',
		beforeSend: function() {
			$('.wait').remove();
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},	
		success: function(html) {
			$('#shipping-method .checkout-content').html(html);
			$('#shipping-method .checkout-content').slideDown('slow');	
			$('#debug').append('<p>--- AJAX Success (checkout/shipping_method)</p>')
				if (typeof func == "function") func(); 

		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
}
// --------------------------------------------------------------------------------------------------------------------- VALIDATE SHIPPING METHOD
function shippingMethod(func){		
	$('#debug').append('<p>======== VALIDATE SHIPPING MATHOD ========</p>')
	$.ajax({
		url: 'index.php?route=checkout/shipping_method/validate',
		type: 'post',
		data: $('#shipping-method input[type=\'radio\']:checked, #shipping-method textarea, #shipping-method select, #shipping-method input[type=\'text\']'),
		dataType: 'json',
		beforeSend: function() {
			$('.wait').remove();
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			
			$('#debug').append('<p>--- Ajax Success (checkout/shipping_method/validate)</p>')

			$('#shipping-method  .warning, #shipping-method  .error').remove();
			if (json['redirect']) {
			//	location = json['redirect'];
			$('.loading').hide()
			$('#column-middle').animate({  opacity: 1 })
			} else if (json['error']) {
				$('#debug').append('<p>------- Ajax ERROR (checkout/shipping_method/validate)</p>');
				$('.loading').hide()
				if (json['error']['warning']) {
					$('#shipping-method .checkout-content').prepend('<div class="warning  alert alert-error" style="display: none;">' + json['error']['warning'] + '<a class="close  icon-remove"></a></div>');
					$('.warning').fadeIn('slow');
				}
			} else {
				$('#debug').append('<p>------ VALIDATE SHIPPING GOOD (checkout/shipping_method/validate)</p>')
				if (typeof func == "function") func(); 
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
};
// --------------------------------------------------------------------------------------------------------------------- SHOW VALIDATE PAYMENT METHOD
function showValidatePaymentMethod(func){
	$('#debug').append('<p>======== VALIDATE PAYMENT METHOD ========</p>')
	$.ajax({
		url: 'index.php?route=checkout/payment_method/validate', 
		type: 'post',
		data: $('#payment-method input[type=\'radio\']:checked, #payment-method select, #payment-method input[type=\'checkbox\']:checked, #payment-method textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('.wait').remove();
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},		
		success: function(json) {
			$('#debug').append('<p>--- AJAX Success (checkout/payment_method/validate)</p>')
			$('#payment-method .warning, #payment-method .error').remove();
			$('#debug').append('<p>------ VALIDAT PAYMENT GOOD (checkout/payment_method/validate)</p>')
				if (typeof func == "function") func(); 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
};
function paymentClear(){
	$('#payment_block').html('');
}

// --------------------------------------------------------------------------------------------------------------------- SHOW PAYMENT METHOD
function showPaymentMethod(func){
	$('#debug').append('<p>======== SHOW PAYMENT MATHOD ========</p>')
	$.ajax({
		url: 'index.php?route=checkout/payment_method',
		type: 'post',
		data: $('#payment-address input[type=\'radio\']:checked'),
		dataType: 'html',
		beforeSend: function() {
			$('.wait').remove();
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},
		success: function(html) {
			$('#debug').append('<p>--- AJAX Success (checkout/payment_method)</p>')
			$('#payment-method .checkout-content').show();
			$('#payment-method .checkout-content').html(html);
			if (typeof func == "function") func(); 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
};
// --------------------------------------------------------------------------------------------------------------------- VALIDATE PAYMENT METHOD
function paymentMethod(func){
	$('#debug').append('<p>======== VALIDATE PAYMENT METHOD ========</p>')
	$.ajax({
		url: 'index.php?route=checkout/payment_method/validate', 
		type: 'post',
		data: $('#payment-method input[type=\'radio\']:checked, #payment-method select, #payment-method input[type=\'checkbox\']:checked, #payment-method textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('.wait').remove();
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('.wait').remove();
		},		
		success: function(json) {
			$('#debug').append('<p>--- AJAX Success (checkout/payment_method/validate)</p>')
			$('#payment-method .warning, #payment-method .error').remove();
			if (json['redirect']) {
				alert(json['redirect'])
				
			} else if (json['error']) {
				$('.loading').hide()
				if (json['error']['warning']) {
					$('#payment-method .checkout-content').prepend('<div class="warning  alert alert-error" style="display: none;">' + json['error']['warning'] + '<a class="close  icon-remove"></a></div>');
					$('.warning').fadeIn('slow');
				}	
			} else {
				$('#debug').append('<p>------ VALIDAT PAYMENT GOOD (checkout/payment_method/validate)</p>')
				if (typeof func == "function") func(); 
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
};
function paymentClear(){
	$('#payment_block').html('');
}
function triggerPayment(){
	if($('#payment_method_second_step').val() == 0){
		$("#payment_button .button, #payment_button .buttons a").trigger("click")
	}
}
// --------------------------------------------------------------------------------------------------------------------- SHOW CONFIRM
function showConfirm(func){
	$('#debug').append('<p>======== SHOW COMFIRM ========</p>')
	$.ajax({
		url: 'index.php?route=checkout/confirm/show',
		dataType: 'html',
		beforeSend: function() {
		
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			
		},	
		success: function(html) {
			$('#debug').append('<p>--- AJAX Success (checkout/confirm)</p>')
			$('#confirm .checkout-content').html(html);
			$('#confirm .checkout-content').slideDown('slow');
			if (typeof func == "function") func(); 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
}

// --------------------------------------------------------------------------------------------------------------------- CONFIRM
function confirmIndex(func){
	$('#debug').append('<p>======== COMFIRM ========</p>')
	$.ajax({
		url: 'index.php?route=checkout/confirm',
		dataType: 'html',
		beforeSend: function() {
			
			$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			
			//$('.loading').hide()
		},	
		success: function(html) {
			$('.loading').hide()
			$('#debug').append('<p>--- AJAX Success (checkout/confirm)</p>')
		
			//$('#confirm .checkout-content').prepend(html);
			
			if($('#payment_method_second_step').val() == 1){
				$('#step_3 .box-heading ').append('<a class="text-modify"><i class="icon-edit"></i> <?php echo $text_modify; ?></a>');
				//$('#step_1 .checkout-content').slideUp('slow');
				$('#step_2').slideUp('slow');
				$('#payment_block').html(html);
				$('#step_3').slideDown('slow');
				
			}
			if (typeof func == "function") func(); 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('.loading').hide()
		}
	});	
}

// --------------------------------------------------------------------------------------------------------------------- CONFIRM
function confirmOrder(){
	$('#debug').append('<p>========== CONFRIM ORDER ============</p>')	
	$('#register_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
	if( $('input[name=\'account\']:checked').attr('value') == 'guest' ){
		$('#debug').append('<p> DO guestCheck()</p>')	
		var field;
		guestCheck(field, function(){
			<?php if ($shipping_required) { ?>
			if($('input[name=\'shipping_address\']:checked').attr('value') == '1' ){
				shippingMethod(function(){
					showPaymentMethod(function(){
						paymentMethod(function(){
							confirmIndex(function(){
								$('#debug').append('<p>--- GuestCheck -> GuestShippingCheck -> Confirm -> Click </p>')	
								triggerPayment()
							})	
						})
					})
				})
			}else{
				guestShippingCheck(field, function(){
					shippingMethod(function(){
						showPaymentMethod(function(){
							paymentMethod(function(){
								confirmIndex(function(){
									$('#debug').append('<p>--- GuestCheck -> GuestShippingCheck -> Confirm -> Click </p>')	
									triggerPayment()
								})	
							})
						})
					})						   
				})
			}
				
			<?php }else { ?>
			confirmIndex(function(){
					$('#debug').append('<p>--- GuestCheck -> Confirm -> Click </p>')	
					triggerPayment()
			})
			<?php } ?>	
		});
	}else{
		$('#debug').append('<p> DO registerValidate()</p>')	
		if($('#registered').val() == 0){
		registerCheck(field, function(){			  
			<?php if ($shipping_required) { ?>
				if($('input[name=\'shipping_address\']:checked').attr('value') == '1' ){
					shippingMethod(function(){
						showPaymentMethod(function(){
							paymentMethod(function(){
								registerValidate(function(){
									confirmIndex(function(){
										$('#debug').append('<p>--- registerCheck -> shippingMethod -> paymentMethod -> confirmIndex -> registerValidate -> Click</p>')	
										triggerPayment()
									})
								});
							});		
						});	
					});	
				}else{
					registrateShippingCheck(field, function(){
						shippingMethod(function(){
							showPaymentMethod(function(){
								paymentMethod(function(){
									registerValidate(function(){
										registrateShippingValidate(field, function(){
											confirmIndex(function(){
												$('#debug').append('<p>--- registerCheck -> registrateShippingCheck -> shippingMethod -> paymentMethod -> confirmIndex -> registerValidate -> Click</p>')	
												triggerPayment()
											});
										});
									});
								});		
							});	
						});												
					});	
				}
			<?php }else { ?>
				showPaymentMethod(function(){
					paymentMethod(function(){
						registerValidate(function(){
							confirmIndex(function(){
								$('#debug').append('<p>--- registerCheck -> paymentMethod -> confirmIndex -> registerValidate -> Click</p>')	
								triggerPayment()
							})
						});
					});	
				});
			<?php } ?>	
		});
		}else{ //$('#registered').val() = 1
			<?php if ($shipping_required) { ?>
				if($('input[name=\'shipping_address\']:checked').attr('value') == '1' ){
					shippingMethod(function(){
						showPaymentMethod(function(){
							paymentMethod(function(){

									confirmIndex(function(){
										$('#debug').append('<p>--- registerCheck -> shippingMethod -> paymentMethod -> confirmIndex -> registerValidate -> Click</p>')	
										triggerPayment()
									})

							});		
						});	
					});	
				}else{
					registrateShippingCheck(field, function(){
						shippingMethod(function(){
							showPaymentMethod(function(){
								paymentMethod(function(){

										registrateShippingValidate(field, function(){
											confirmIndex(function(){
												$('#debug').append('<p>--- registerCheck -> registrateShippingCheck -> shippingMethod -> paymentMethod -> confirmIndex -> registerValidate -> Click</p>')	
												triggerPayment()
											});
										});

								});		
							});	
						});												
					});	
				}
			<?php }else { ?>
				showPaymentMethod(function(){
					paymentMethod(function(){

							confirmIndex(function(){
								$('#debug').append('<p>--- registerCheck -> paymentMethod -> confirmIndex -> registerValidate -> Click</p>')	
								triggerPayment()
							})

					});	
				});
			<?php } ?>	
		}
	}

}

function updateCheckout(func){
	$('#debug').append('<p>======== UPDATE CHECKOUT ========</p>')	
	
	<?php if ($logged) { ?> 
	$('#debug').append('<p>--- logged in</p>')
		registerUpdate(function(){
		<?php if ($shipping_required) { ?>
			registrateShippingUpdate(function(){
				showShippingMethod(function(){
					showPaymentMethod(function(){
						showConfirm(function(){
							$('#debug').append('<p>------ logged in updated</p>')
											 $('.loading').hide() })
					})	
				});
			})
		<?php }else{ ?>
			showPaymentMethod(function(){
				showConfirm()
			});
		<?php }?>						 
		})
	<?php } else { ?>
	if( $('input[name=\'account\']:checked').attr('value') == 'guest' ){
		$('#debug').append('<p>--- Guest </p>')
			guestUpdate(function(){
				<?php if ($shipping_required) { ?>
				
				if( $('input[name=\'shipping_address\']:checked').attr('value') == '1' ){
						showShippingMethod(function(){
							showPaymentMethod(function(){
								showConfirm(function(){
													 $('#debug').append('<p>------ Guest updated </p>')
													 $('.loading').hide()})
							})
						});
				}else{
					
				
					guestShippingUpdate(function(){
						showShippingMethod(function(){
							showPaymentMethod(function(){
								showConfirm(function(){$('.loading').hide()})
							})	
						});
					})
				}
				<?php }else{ ?>
					showPaymentMethod(function(){
						showConfirm()
					});
				<?php }?>
			})
		}else{
			$('#debug').append('<p>--- Register </p>')
			registerUpdate(function(){
				<?php if ($shipping_required) { ?>
				if( $('input[name=\'shipping_address\']:checked').attr('value') == '1' ){
						showShippingMethod(function(){
							showPaymentMethod(function(){
								showConfirm(function(){
													 $('#debug').append('<p>--- Register updated</p>')
													 $('.loading').hide()})
							})
						});
				}else{
					
				
					registrateShippingUpdate(function(){
						showShippingMethod(function(){
							showPaymentMethod(function(){
								showConfirm(function(){$('.loading').hide()})
							})	
						});
					})
				}
				<?php }else{ ?>
					showPaymentMethod(function(){
						showConfirm()
					});
				<?php }?>
			})
					
		}
	<?php } ?>
}

function changeQuantity(product_id, quantity, func) {
	quantity = typeof(quantity) != 'undefined' ? quantity : 1;

	$.ajax({
		url: 'index.php?route=checkout/cart/update',
		type: 'post',
		data: 'quantity[' + product_id + ']=' + quantity,
		success: function(html, status, jqXHR) {
				//alert(jqXHR.responseText)
				if (typeof func == "function") func();
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(xhr.responseText);
			$('.loading').hide()
		}
	});
}
$('.product-qantity').live('focus', function(){	
	$(this).live('change', function(){
		changeQuantity($(this).attr('data-product-id'), $(this).val(), function(){
			updateCheckout()
		})					
	})																	
})
$('#coupon').live('focus', function(){	
	$(this).live('change', function(){
		$.ajax({
		url: 'index.php?route=checkout/cart/applyCoupon',
		type: 'post',
		dataType: 'json',
		data: $('#coupon'),
		success: function(json, status, jqXHR) {
				//alert(jqXHR.responseText)
				if(typeof  json['warning'] != 'undefined'){
					alert( json['warning'] )
				}else{
					updateCheckout()
				}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(xhr.responseText);
			$('.loading').hide()
		}
	});					
	})																	
})
$('#voucher').live('focus', function(){	
	$(this).live('change', function(){
		$.ajax({
		url: 'index.php?route=checkout/cart/applyVoucher',
		type: 'post',
		dataType: 'json',
		data: $('#voucher'),
		success: function(json, status, jqXHR) {
				//alert(jqXHR.responseText)
				if(typeof  json['warning'] != 'undefined'){
					alert( json['warning'] )
				}else{
					updateCheckout()
				}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			//alert(xhr.responseText);
			$('.loading').hide()
		}
	});					
	})																	
})

//--------------------------------------------------------------------------------------------------------------  EDIT PAYMENT INFO ACTION
//Update cart if these fields are changed

$('#payment-address input[name=\'firstname\'], #payment-address input[name=\'lastname\'], #payment-address input[name=\'email\'], #payment-address input[name=\'telephone\'], #payment-address input[name=\'fax\'], #payment-address input[name=\'password\'], #payment-address input[name=\'confirm\'], #payment-address input[name=\'customer_group_id\'], #payment-address input[name=\'company_id\'], #payment-address input[name=\'tax_id\'], #payment-address input[name=\'address_1\'], #payment-address input[name=\'address_2\'], #payment-address input[name=\'city\'], #payment-address input[name=\'postcode\'], #payment-address select[name=\'country_id\'], #payment-address select[name=\'zone_id\'], #payment-address select[name=\'address_id\'], #payment-address input[name=\'payment_address\']').live('focus', function() {
	$(this).live('change', function() {
		$('.loading').show()
		updateCheckout()
	});
});
$('#payment-address input[name=\'payment_address\']').live('click', function() {
											updateCheckout()					 
																			 })
$('#payment-address input, #payment-address select').live('focus', function() {
																			  
	$('#debug').append('<p> FOCUS #payment-address</p>')	

	$(this).bind('change', function() {

		$('#debug').append('<p> CHANGE #payment-address</p>')	
		if( $('input[name=\'account\']:checked').attr('value') == 'guest' ){
			$('#debug').append('<p> DO guestCheck('+$(this).attr('name')+')</p>')	
			guestCheck($(this).attr('name'));
		}else{
			$('#debug').append('<p> DO registerCheck('+$(this).attr('name')+')</p>')	
			registerCheck($(this).attr('name'));	
			
		}
	});	
});
//------------------------------------------------------------------------------  SHIPPING ADDRESS ACTION
//change payment address to shipping address
$('#payment-address input[name=\'shipping_address\']').live('click', function() {
	$('#debug').append('<p> CHANGE #shipping_address</p>')
	if( $('input[name=\'account\']:checked').attr('value') == 'register' ){
		showRegistrateShipping(function(){
			updateCheckout()
		});
	}else{
		showGuestShipping(function(){
			updateCheckout()
		});
	}
});
//------------------------------------------------------------------------------  CHECK SHIPPING ADDRESS ACTION
//change shipping address
$('#shipping-address input, #shipping-address select').live('focus', function() {
																			  
	$('#debug').append('<p> FOCUS #shipping-address</p>')	

	$(this).bind('change', function() {

		$('#debug').append('<p> CHANGE #shipping-address</p>')	
		if( $('input[name=\'account\']:checked').attr('value') == 'guest' ){
			$('#debug').append('<p> DO guestShippingCheck('+$(this).attr('name')+')</p>')	
			guestShippingCheck($(this).attr('name'));
		}else{
			$('#debug').append('<p> DO registrateShippingCheck('+$(this).attr('name')+')</p>')	
			registrateShippingCheck($(this).attr('name'));	
			
		}
	});	
});

$('#shipping-address input[name=\'firstname\'], #shipping-address input[name=\'lastname\'], #shipping-address input[name=\'email\'], #shipping-address input[name=\'telephone\'], #shipping-address input[name=\'fax\'], #shipping-address input[name=\'password\'], #shipping-address input[name=\'confirm\'], #shipping-address input[name=\'customer_group_id\'], #shipping-address input[name=\'company_id\'], #shipping-address input[name=\'tax_id\'], #shipping-address input[name=\'address_1\'], #shipping-address input[name=\'address_2\'], #shipping-address input[name=\'city\'], #shipping-address input[name=\'postcode\'], #shipping-address select[name=\'country_id\'], #shipping-address select[name=\'zone_id\'], #shipping-address select[name=\'address_id\']').live('focus', function() {
	$(this).live('change', function() {
		updateCheckout()
	});
});
$('#shipping-address input[name=\'shipping_address\']').live('click', function() {
											updateCheckout()					 
																			 })
//------------------------------------------------------------------------------  CHANGE SHIPPING METHOD ACTION
$('#shipping-method textarea').live('change', function() {			
		$('.loading').show()
		shippingMethod(function(){
			showShippingMethod(function(){
				showPaymentMethod(function(){
					showConfirm(function(){$('.loading').hide()})
				})
			})
		});
});
$('#shipping-method input[type=checkbox], #shipping-method input[type=radio]').live('click', function() {			
		$('.loading').show()
		shippingMethod(function(){
			showShippingMethod(function(){
				showPaymentMethod(function(){
					showConfirm(function(){$('.loading').hide()})
				})
			})
		});
});
$('#shipping-method select').live('focus', function() {
	$(this).live('change', function() {	
		$('.loading').show()
		shippingMethod(function(){
			showShippingMethod(function(){
				showPaymentMethod(function(){
					showConfirm(function(){$('.loading').hide()})
				})
			})
		});
	});
});

//--------------------------------------------------------------------------------------------------------------  SHOW PAYMENT METHOD ACTION
$('#payment-method textarea').live('change', function() {
	$('.loading').show()
	paymentMethod(function(){
		showPaymentMethod(function(){
			showConfirm(function(){$('.loading').hide()})
		})
	});
});
$('#payment-method input[type=checkbox], #payment-method input[type=radio]').live('click', function() {			
	$('.loading').show()
	paymentMethod(function(){
		showPaymentMethod(function(){
			showConfirm(function(){paymentClear(); $('.loading').hide()})
		})
	});
});
$('#payment-method select').live('focus', function() {
	$(this).live('change', function() {	
	$('.loading').show()
		paymentMethod(function(){
			showPaymentMethod(function(){
				showConfirm(function(){paymentClear(); $('.loading').hide()})
			})
		});
	});
});
//--------------------------------------------------------------------------------------------------------------  CONFIRM ORDER ACTION
$('#register_button').live('click', function(){
	$('.loading').show()
	$('#debug').append('<p> CLICK #register_button</p>')	
	confirmOrder(function(){$('.loading').hide()})
});

// Stop bubbling up live functions
$('input, select').live('focus', function() {
	$(this).bind('change', function(event) {
		event.stopImmediatePropagation()
	})
})

//	$('#register_button').attr("disabled", "disabled");



//--></script> 
<script><!--
$(function(){
	if($.isFunction($.fn.uniform)){
        $(" .styled, input:radio.styled").uniform().removeClass('styled');
		}
    });
//--></script>
<?php echo $content_bottom; ?>
</div><!--article wrap-->
</article>

</div><!--content wrap-->
</div><!--content layout-->
</div><!--content-->
<?php if($config['checkout_debug']){ ?>
<style>
#debug{
	position: fixed;
	top:0px;
	left:0px;
	background:rgba(255,255,255,0.5);
	border-bottom:1 solid #EFEFEF;
	padding:10px;
	height:100%;
	overflow:scroll;
	display:none
	}
#debug_toggle{
	position:fixed;
	top:15px;
	left:15px;}
#debug_session{
	padding:10px;
	background:#E7E7E7}
#debug_session pre{
	display:block;
	padding:15px;
	width:30%;
	float:left}
</style>
<div id="debug"></div>
<a id="debug_toggle" class="button btn-warning">Debug</a>

<script>
$('#debug_toggle').live('click',function(){
	$('#debug').toggle()
})
</script>


<br style=" clear:both" />
<div id="debug_session">
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']).'?route=checkout/checkout'; ?>" method="post">
<input type="hidden" value="1" name="reset_session" />
<input type="submit" class="button btn-warning" value="Reset Session" />
</form>
<span class="button btn-warning" id="show_payment_method">Show Payment Method</span>
<script>
	$('#show_payment_method').click(function(){
		showPaymentMethod()
		
		})
</script>
<pre>
<h3>shipping_methods</h3>
<?php  print_r($_SESSION['shipping_methods']); ?>
</pre>
<pre>
<h3>shipping_method</h3>
<?php  print_r($_SESSION['shipping_method']); ?>
</pre>
<pre>
<h3>payment_methods</h3>
<?php  print_r($_SESSION['payment_methods']); ?>
</pre>
<br style=" clear:both" />
<pre>
<h3>payment_method</h3>
<?php  print_r($_SESSION['payment_method']); ?>
</pre>
<pre>
<h3>guest</h3>
<?php  print_r($_SESSION['guest']); ?>
</pre>
<pre>
<h3>session</h3>
<?php  print_r($_SESSION); ?>
</pre>
<br style=" clear:both" />
</div>
<?php } ?>
<br style=" clear:both" />
<?php echo $footer; ?>