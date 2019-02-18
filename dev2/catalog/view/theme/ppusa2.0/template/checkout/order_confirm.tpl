

<div id="confmOdr" class="tab-pane fade in active">
					    	<div class="tab-inner">
					      		<div class="row order-address">
					      			<div class="col-lg-12 col-md-12">
					      				<div class="row address-row">
					      					<div class="col-md-4 col-lg-4 address-col">
					      						<h5>Contact information <a href="javascript:void(0);" onclick="navigateCheckoutTab('checkout');"><span class="edit-icon"><img src="catalog/view/theme/ppusa2.0/images/icons/edit-icon.png" alt=""></span></a></h5>
					      						<ul class="address-list">
					      							<li style="font-size:12px"><?php echo $contact_firstname;?> <?php echo $contact_lastname;?> </li>
					      							<li style="font-size:12px"><?php echo $contact_phone;?></li>
					      							<li style="font-size:12px"><?php echo $contact_email;?></li>
					      						</ul>
					      					</div>
					      					<div class="col-md-4 col-lg-4 address-col">
					      						<h5>Shipping Details <a href="javascript:void(0);" onclick="navigateCheckoutTab('shippingTab');"><span class="edit-icon"><img src="catalog/view/theme/ppusa2.0/images/icons/edit-icon.png" alt=""></span></a></h5>
					      						<p style="font-size:12px;font-family:'AileronRegular';margin-bottom: 10px"><?php echo $shipping_method;?></p>

					      						<ul class="address-list">
					      							<li style="font-size:12px"><?php echo $shipping_firstname;?> <?php echo $shipping_lastname;?></li>
					      							<?php
					      							if($shipping_company)
					      							{
					      								?>
					      							<li style="font-size:12px"><?php echo $shipping_company;?></li>
					      							<?php
					      							}
					      							?>
					      							<li style="font-size:12px"><?php echo $shipping_address_1;?></li>
					      							<?php
					      							if($shipping_address_2)
					      							{
					      								?>
					      							<li style="font-size:12px"><?php echo $shipping_address_2;?></li>
					      							<?php
					      							}
					      							?>
					      							<li style="font-size:12px"><?php echo $shipping_city;?>, <?php echo $shipping_state;?> <?php echo $shipping_zip;?></li>
					      						</ul>
					      					</div>
					      					<div class="col-md-4 col-lg-4 address-col">
					      						<h5>Billing Details <a href="javascript:void(0);" <?php if (!isset($this->session->data['ppx']['token'])) { ?> onClick="navigateCheckoutTab('paymentTab');" <?php } ?>><span class="edit-icon"><img src="catalog/view/theme/ppusa2.0/images/icons/edit-icon.png" alt=""></span></a></h5>
					      						<ul class="address-list">
					      							<li style="font-size:12px"><?php echo $payment_firstname;?> <?php echo $payment_lastname;?></li>
					      							<?php
					      							if($payment_company)
					      							{
					      								?>
					      							<li style="font-size:12px"><?php echo $payment_company;?></li>
					      							<?php
					      							}
					      							?>
					      							
					      							<li style="font-size:12px"><?php echo $payment_address_1;?></li>
					      							<?php
					      							if($payment_address_2)
					      							{
					      								?>
					      							<li style="font-size:12px"><?php echo $payment_address_2;?></li>
					      							<?php
					      							}
					      							?>
					      							<li style="font-size:12px"><?php echo $payment_city;?><?php echo (($payment_city && $payment_state?',':'')) ?> <?php echo $payment_state;?> <?php echo $payment_zip;?></li>
					      						</ul>

					      						<?php
					      				if($this->session->data['newcheckout']['payment_method']=='paypal_express_new')
					      				{
					      					$small_image = 'pp_standard_new';
					      				}
					      				else
					      				{
					      					$small_image = $this->session->data['newcheckout']['payment_method'];
					      				}
					      				?>
					      				<table class="table sidebox-billing">
											<tbody>
												<tr>
													<td><img src="catalog/view/theme/ppusa2.0/images/<?php echo $small_image;?>.png" alt="" class="visa-img"></td>
													<td style="font-family:'AileronRegular';">
													<?php
													if($this->session->data['newcheckout']['payment_method']=='pp_payflow_pro')
													{	
														$first_head = 'Ending in';
														$second_head = substr($this->session->data['newcheckout']['cc_number'], -4);
													}
													else if ($this->session->data['newcheckout']['payment_method']=='pp_standard_new' || $this->session->data['newcheckout']['payment_method']=='paypal_express_new' )
													{
														$first_head = 'Payment Mode: ';
														$second_head = 'PayPal';
													}
													else if($this->session->data['newcheckout']['payment_method']=='free_checkout')
													{	
														$first_head = 'Free Checkout';
														$second_head = 'PPUSA';
													}
													else
													{
														$first_head = 'Local Pickup: ';
														$second_head = 'PPUSA';

													}
													?>
														<p style="font-size:12px"><?php echo $first_head;?> </p>
														<p style="font-size:12px"><?php echo $second_head;?> </p>
													</td>
												</tr>
											</tbody>
										</table>
					      					</div>
					      				</div>
					      			</div>
					      			<div class="col-lg-12 col-md-12 order-shipping hidden">
					      				<h5>Shipping method <a href="javascript:void(0);" onClick="navigateCheckoutTab('shippingTab');"><span class="edit-icon"><img src="catalog/view/theme/ppusa2.0/images/icons/edit-icon.png" alt=""></span></a></h5>

					      				<p style="font-size:12px"><?php echo $shipping_method;?></p>
					      				<!-- <h5>Billing Method &nbsp; </h5> -->
					      				<?php
					      				if($this->session->data['newcheckout']['payment_method']=='paypal_express_new')
					      				{
					      					$small_image = 'pp_standard_new';
					      				}
					      				else
					      				{
					      					$small_image = $this->session->data['newcheckout']['payment_method'];
					      				}
					      				?>
					      				<table class="table sidebox-billing">
											<tbody>
												<tr>
													<td><img src="catalog/view/theme/ppusa2.0/images/<?php echo $small_image;?>.png" alt="" class="visa-img"></td>
													<td>
													<?php
													if($this->session->data['newcheckout']['payment_method']=='pp_payflow_pro')
													{	
														$first_head = 'Ending in';
														$second_head = substr($this->session->data['newcheckout']['cc_number'], -4);
													}
													else if ($this->session->data['newcheckout']['payment_method']=='pp_standard_new' || $this->session->data['newcheckout']['payment_method']=='paypal_express_new' )
													{
														$first_head = 'Payment Mode: ';
														$second_head = 'PayPal';
													}
													else if($this->session->data['newcheckout']['payment_method']=='free_checkout')
													{	
														$first_head = 'Free Checkout';
														$second_head = 'PPUSA';
													}
													else
													{
														$first_head = 'Local Pickup: ';
														$second_head = 'PPUSA';

													}
													?>
														<p style="font-size:12px"><?php echo $first_head;?> </p>
														<p style="font-size:12px"><?php echo $second_head;?> </p>
													</td>
												</tr>
											</tbody>
										</table>
					      			</div>
					      		</div>
					      	</div>	
				      		<div class="border"></div>
				      		<div class="tab-inner bbtm pad60 pb30">
					      		<div class="shoping-cart-box order-product cart-product-small  mb40">
									<?php
									foreach($cart_items as $item)
									{


									?>
									



									<div class="product-detail row pr<?php echo $kr; ?>">
				<div class="product-detail-inner clearfix">
				

					<div class="col-md-4 col-xs-4 hidden-xs hidden-sm product-detail-img" >
						<div class="image" style="text-align: center;font-weight:bold"><?php if ($item['image']) { ?>
							<a href="javascript:void(0);"><img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" title="<?php echo $item['name']; ?>" /><br><?php echo $item['model'];?></a>
							<?php } ?>
						</div>
					</div>
					<div class="col-md-8 product-detail-text" >

						<h2 style="margin-bottom:2px"><span class="hidden-md hidden-lg" style="font-weight:400"><?php echo $item['model'];?> </span><br class="hidden-md hidden-lg" /><a href="javascript:void(0);"><?php echo $item['name']; ?></a></h2>
						<div class="row">
							<div class="col-md-7">
								<table class="pricing-table table pricing-table-ok" style="margin-top:5px">
									<tbody>
										<tr>
											<td>Quantity</td>
											<?php foreach ($item['discounts'] as $discount) : ?>
												<td><?php echo $discount['quantity']; ?></td>
											<?php endforeach; ?>
										</tr>
										<tr>
											<td>Our Price</td>
											<?php foreach ($item['discounts'] as $discount) : ?>
												<td><?php echo $discount['price']; ?></td>
											<?php endforeach; ?>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-md-5 cart-total-wrp" style="padding-top:0px">
							
								<div class="cart-total">
								<div class="qtyt-box col-xs-8 text-center" style="max-width:100%;margin-left:25px;">
										

										</div>
									</div>
									
									<div class="cartPPrice text-center col-xs-12 text-center">
											<span class="txt" style="font-size: 12px;">QTY: 
											<?php echo
											sprintf('%02d', $item['quantity']);?>
											</span>
											</div>
									<h2 class="cartPPrice text-center col-xs-12 text-center" ><?php echo $item['total']; ?><br><small>(<?php echo $item['price']; ?> ea)</small></h2>
								</div>
							</div>
						</div>

					</div>
				</div>
			
									<!-- Product detail row -->
								<?php
							}
							?>
									
									<!-- Product detail row -->
								</div>	
							</div>
							
					    </div>

					      <?php
					    if($this->session->data['newcheckout']['payment_method']=='pp_payflow_pro')
					    {

					    	?>
					    	<div id="pp_payflow" style="display:none">
					    	<input type="hidden" name="cc_number" value="<?php echo $this->session->data['newcheckout']['cc_number'];?>">
					    	<input type="hidden" name="cc_expire_date_month" value="<?php echo $this->session->data['newcheckout']['cc_expire_date_month'];?>">
					    	<input type="hidden" name="cc_expire_date_year" value="<?php echo $this->session->data['newcheckout']['cc_expire_date_year'];?>">
					    	<input type="hidden" name="cc_cvv2" value="<?php echo $this->session->data['newcheckout']['cc_cvv2'];?>">

					    	</div>

					    	<?php

					    }
					    ?>

					    <script>

					    <?php
					    if($this->session->data['newcheckout']['payment_method']=='pp_payflow_pro')
					    {

					    	?>
					    	function isNumeric(n){
		return !isNaN(parseFloat(n)) && isFinite(n);
	}
	function submitForm()
	{
		$.ajax({
		type: 'POST',
		url: 'index.php?route=payment/pp_payflow_pro/send',
		data: $('#pp_payflow :input'),
		dataType: 'json',
		beforeSend: function() {
			$('.alert-danger').hide();
			$('#cart_overlay').fadeIn();
			if ( $('.button-place-order').attr('disabled')) {
				return false;
			}

			var error=false;
			$('#pp_payflow .pp_payflow_error').remove();

			var cc_number=$('#pp_payflow input[name=cc_number]');
			cc_number.val(cc_number.val().replace(/[ -]/g,''));
			var length=cc_number.val().length;
			if (length<13 || length>16 || !isNumeric(cc_number.val())) {
				//cc_number.after('<span class="error pp_payflow_error"><?php echo $entry_cc_number_error; ?></span>');
				alert('Invalid Credit Card Number, Please go back and check the CC Number');
				error=true;
			}

			var cc_cvv2=$('#pp_payflow input[name=cc_cvv2]');
			var length=cc_cvv2.val().length;
			if (length<3 || length>4 || !isNumeric(cc_cvv2.val())) {
				// cc_cvv2.next().after('<span class="error pp_payflow_error"><?php echo $entry_cc_cvv2_error; ?></span>');
				alert('Invalid CVV2 Number');
				error=true;
			}

			if (error) return false;

			$('.button-place-order').attr('disabled', 'disabled').after('<div class="wait"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> </div>');
			// $('#xcontent2').show();
		},
		success: function(data) {
			if (data.error) {
				//alert(data.error);
				$('.alert-danger').show();
                $('.alert-danger').html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'+data.error);
				//$('.pp_payflow_button').data('disabled',false).attr('disabled', '');
				$('.button-place-order').removeAttr('disabled');
				$('#cart_overlay').fadeOut();
				$('html,body').animate({
                scrollTop: 0
            }, 700);
				// $('#xcontent2').hide();
			}

			$('.wait').remove();
			if (data.success) {
                // $('#pp_payflow .messages').empty();
				location = data.success;
			}
		}
	});
	}
					    	
					    	
					    	<?php
					    }
					   else if($this->session->data['newcheckout']['payment_method']=='cod')
					    {
					    	?>
					    	function submitForm()
					    	{
					    		$.ajax({ 
		type: 'get',
		url: 'index.php?route=payment/cod/confirm',
		beforeSend: function(){
			$('.alert-danger').hide();
			$('.button-place-order').attr('disabled',true);
			$('#cart_overlay').fadeIn();

		},
		success: function() {
			location = '<?php echo $this->url->link('checkout/success'); ?>';

		}		
	});
					    	}

<?php
					    }
					    else if (isset($this->session->data['ppx']['token']))
					    {
					    	?>
					    	function submitForm()
					    	{
					    		$('.alert-danger').hide();
					    		$('#cart_overlay').fadeIn();
			$('.button-place-order').attr('disabled',true);
	window.location='index.php?route=payment/paypal_express_new/DoExpressCheckoutPayment'
					    	}
					    	


					    	<?php
					    }
					       else if($this->session->data['newcheckout']['payment_method']=='free_checkout')
					    {
					    	
					    	?>
					    	function submitForm()
					    	{
					    		$.ajax({ 
		type: 'get',
		url: 'index.php?route=payment/free_checkout/confirm',
			beforeSend: function(){
			$('.alert-danger').hide();
			$('.button-place-order').attr('disabled',true);
			$('#cart_overlay').fadeIn();

		},
		success: function() {
			location = '<?php echo $this->url->link('checkout/success'); ?>';
		}		
	});
					    	}
	


					    	<?php
					    }
					    else
					    {
					    	?>
					    	function submitForm()
					    	{
					    			$('.alert-danger').hide();
			$('.button-place-order').attr('disabled',true);
			$('#cart_overlay').fadeIn();
	window.location='index.php?route=payment/paypal_express_new/SetExpressCheckout&is_pp_checkout=1';
					    	}
					    	
					    <?php
					    }
					    ?>
					    	function navigateCheckoutTab(tab_id)
					    	{
					    		$('ul[role=tablist] li').removeClass('active');
					    		$('ul[role=tablist] li').addClass('disabled');
					    		
					    		$('ul[role=tablist] li#'+tab_id).removeClass('disabled');
					    		// $('ul[role=tablist] li#'+tab_id).addClass('active');
					    		$('ul[role=tablist] li#'+tab_id+' a[data-toggle="tab"]').click();
					    	}
					    	$(document).off('click','.button-place-order');
					    	$(document).on('click','.button-place-order', submitForm);
					    </script>