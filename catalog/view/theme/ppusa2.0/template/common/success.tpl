<?php echo $header; ?>
<main class="main">
		<div class="container confirm-page">
			<div class="white-box overflow-hide">
				<div class="row">
					<div class="col-md-9 table-cell">
						<div class="row inline-block">
							<div class="col-md-8 white-box-right inline-block">
								<h4 class="blue-title text-sm-center">Congratulations, Your Order Has been received!</h4>
								<div class="row download-row">
									<div class="col-md-6">Your Order Number is: <a href="javascript:void(0);" style="vertical-align: top" class="fw-700"><?php echo $orderDetails['order_id'];?></a></div>
									<!-- <div class="col-md-3"><i class="fa fa-print"></i><a href="<?php echo $this->session->data['checkout_order_pdf_file'];?>" class="underline">Print reciept</a></div>
									<div class="col-md-3 pl0"><i class="fa fa-file-pdf-o"></i><a href="<?php echo $this->session->data['checkout_order_pdf_file'];?>" class="underline">Download PDF</a></div> -->
								</div>

								<?php
								if($orderDetails['shipping_code']=='multiflatrate.multiflatrate_0')
								{
									if((!$this->customer->isLogged()) or $this->customer->getCustomerGroupId()=='8')
									{
									?>
									<div class="row download-row product-detail-inner text-center">
    	<h4 class="blue-title text-sm-center text-center" style="
    margin-top: 5px;
    text-transform: none;
">$5 Pickup Fee will apply to all orders from Non-Approved Businesses.</h4>
    If you're a Phone Repair Business complete our <a href="<?php echo $this->url->link('wholesale/wholesale'); ?>" style="
    /* text-decoration:  underline; */
">business application</a> for approval.

</div>

									<?php
									}

								}

								?>
								<p>
								If any details in your Order Summary or Order Contents are incorrect, contact us immediately! Be sure to include your Order Number and the correction you would like to make. In case you need to return any items from this order, you may see our <a href="<?php echo $this->url->link('information/information', 'information_id=' . $this->config->get('config_account_id'));?>" class="underline blue">returns policy</a> and access our EZ Returns Form. Thanks again for your order and good luck with your repairs!

								
								</p>
								<div class="shoping-cart-box cart-product-small">
									<?php
									//print_r($orderProducts);exit;
									foreach($orderProducts as $orderProduct )
									{
									?>
									<div class="product-detail row">
										<div class="product-detail-inner clearfix">
											<div class="col-md-2 product-detail-img">
												<div class="image"><img src="<?php echo $orderProduct['image'];?>" alt=""></div>
											</div>
											<div class="col-md-10 product-detail-text">
											<span class="hidden-md hidden-lg"><?php echo $orderProduct['model'];?></span><br class="hidden-md hidden-lg"/>
												<h3><?php echo $orderProduct['name'];?></h3>
												<div class="table mb0">
													<div class="row table-row">
														<div class="col-md-6 col-xs-6 pl0 table-cell v-top">
															<p class="item-qty">Qty: <?php echo $orderProduct['quantity'];?> at <?php echo $orderProduct['price'];?> ea</p>
														</div>
														<div class="col-md-6 col-xs-6 cart-total-wrp table-cell v-bottom">
															<div class="cart-total text-right">
																<h3 class="onbottom"><span>Item Total:</span><?php echo $orderProduct['total'];?></h3>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<?php
								}
								?>
								
								</div>	
							</div>
							<div class="col-md-4 white-box-left pr0 inline-block">
								<?php
								if(!$this->customer->isLogged())
								{
								?>
								<div class="white-box-inner">
									<h4 class="blue-title">What’s next?</h4>
									<div class="your-check">
	  									<input type="checkbox" class="css-checkbox check-toggler" id="ck1">
										<label for="ck1" class="css-label2">Create an Account</label>
									</div>
									<p class="aileron">
										Save the trouble of entering all your information again next time. All we need is your new password, and we’ll save all your date for the future.
									</p>
									<form role="form" action="<?php echo $this->url->link('account/register'); ?>" method="post" class="vertical-form check-toggled">
										<div class="form-group">
									    	<label for="password">Password</label>
									    	<input type="password" class="form-control" id="password" name="password">
									 	</div>
										<div class="form-group">
									    	<label for="Cpassword">Confirm Password</label>
									    	<input type="password" class="form-control" id="Cpassword" name="confirm">
									 	</div>

									 	<div class="form-group">
									 			<input type="hidden" name="firstname" value="<?php echo $orderDetails['firstname']; ?>">
									 			<input type="hidden" name="lastname" value="<?php echo $orderDetails['lastname']; ?>">
									 			<input type="hidden" name="email" value="<?php echo $orderDetails['email']; ?>">
									 			<input type="hidden" name="telephone" value="<?php echo $orderDetails['telephone']; ?>">
									 			<input type="hidden" name="address_1" value="<?php echo $orderDetails['shipping_address_1']; ?>">
									 			<input type="hidden" name="address_2" value="<?php echo $orderDetails['shipping_address_2']; ?>">
									 			<input type="hidden" name="city" value="<?php echo $orderDetails['shipping_city']; ?>">
									 			<input type="hidden" name="postcode" value="<?php echo $orderDetails['shipping_postcode']; ?>">
									 			<input type="hidden" name="country_id" value="<?php echo $orderDetails['shipping_country_id']; ?>">
									 			<input type="hidden" name="zone_id" value="<?php echo $orderDetails['shipping_zone_id']; ?>">
									 			<input type="hidden" name="agree" value="1">
									 		<button class="btn btn-primary" type="submit">Create Account</button>
									 	</div>
									</form>
								</div>
								<div class="border"></div>
								<?php
							}
							?>
							<?php
								if($this->customer->isLogged())
								{
								?>
								<div class="white-box-inner">
									<div class="form-group">
									 		<button class="btn btn-primary" onClick="$('#subscribe_form').submit();">Subscribe</button>
									 	</div>
									<p>
										Sign up for our mailing list, and getnotified about our latest specials and newest products.
									</p>
								</div>

								<form id="subscribe_form" style="display:none" accept-charset="utf-8" action="<?php echo HTTPS_SERVER.'subscribe_mailchimp.php'; ?>" method="post" target="_blank">
					<input type="email" name="email" value="<?php echo $this->customer->getEmail(); ?> style
					display:none">
					
				</form>

								<!-- <div class="border"></div> -->
								<?php
							}
							?>
							<!-- 	<div class="white-box-inner">
									<h3>Rate your Experience</h3>
									<p>We’d love to hear about your experience with our sit. Let us know how things went, and feel free to leave aby thoughts or ideas</p>
									<div class="review-area">
										<ul class="review-stars clearfix">
											<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
											<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
											<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
											<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
											<li><a href="#"><i class="fa fa-star"></i></a></li>
										</ul>
									</div>
									<form role="form" action="" class="vertical-form">
										<div class="form-group">
									    	<label for="password">Comments:</label>
									    	<textarea name="" class="form-control"></textarea>
									 	</div>
									 	<div class="form-group">
									 		<button class="btn btn-primary uppercase">Send Feedback</button>
									 	</div>
									</form> 
								</div> -->
							</div>
						</div>
					</div>
					<div class="col-md-3 table-cell">
						<div class="sidebox overflow-hide inline-block w100">
							<h4>order summary </h4>
							<p class="sidebox-lbl">Contact info</p>
							<ul class="info-list">
								<li><?php echo $orderDetails['firstname'];?> <?php echo $orderDetails['lastname'];?> </li>
								<li><?php echo $orderDetails['telephone'];?></li>
								<li><?php echo $orderDetails['email'];?></li>
							</ul>
							<div class="line"></div>
							<p class="sidebox-lbl">Billing method</p>
							<?php
					      				if($orderDetails['payment_code']=='paypal_express_new')
					      				{
					      					$small_image = 'pp_standard_new';
					      				}
					      				else
					      				{
					      					$small_image = $orderDetails['payment_code'];
					      				}
					      				?>
					      				<?php
													if($orderDetails['payment_code']=='pp_payflow_pro')
													{	
														$first_head = 'Payment Method: ';
														$second_head = 'Credit/Debit Card';
													}
													else if ($orderDetails['payment_code']=='pp_standard_new' || $orderDetails['payment_code']=='paypal_express_new' )
													{
														$first_head = 'Payment Mode: ';
														$second_head = 'PayPal';
													}
													else if($orderDetails['payment_code']=='free_checkout')
													{	
														$first_head = 'Free Checkout';
														$second_head = 'PPUSA';
													}
													else if($orderDetails['payment_code']=='behalf')
													{	
														$first_head = 'Payment Mode: ';
														$second_head = 'Behalf';
													}
													else
													{

													$first_head = 'Local Pickup: ';
														$second_head = 'PPUSA';
														

													}
													?>
							<table class="table sidebox-billing">
								<tbody>
									<tr>
										<td><img src="catalog/view/theme/ppusa2.0/images/<?php echo $small_image;?>.png" alt=""></td>
										<td>
											<p><?php echo $first_head;?></p>
											<p><?php echo $second_head;?></p>
										</td>
									</tr>
								</tbody>
							</table>
							<span class="line"></span>
							<?php
							if($orderDetails['payment_code']=='pp_payflow_pro')
							{	
							?>
							<p class="sidebox-lbl">Billing address</p>
							<ul class="info-list">
								<li><?php echo $orderDetails['payment_firstname'];?> <?php echo $orderDetails['payment_lastname'];?></li>
								<li><?php echo $orderDetails['payment_address_1'];?></li>
								<?php if($orderDetails['payment_address_2'])
								{
									?>
								<li><?php echo $orderDetails['payment_address_2'];?></li>
									<?php
								}
								?>
								<li><?php echo $orderDetails['payment_city'];?>, <?php echo $orderDetails['payment_zone'];?> <?php echo $orderDetails['payment_postcode'];?></li>
							</ul>
							<span class="line hidden"></span>
							<?php
						}
						?>
							<p class="sidebox-lbl hidden">Shipping method </p>
							<ul class="info-list hidden">
								<li><?php echo $orderDetails['shipping_method'];?> - <?php echo $this->currency->format($orderDetails['shipping_total']);?></li>
							</ul>
							<span class="line"></span>
							<p class="sidebox-lbl">Shipping address</p>
							<ul class="info-list">
								<!-- <li>Notareal Person</li> -->
								<li><?php echo $orderDetails['shipping_address_1'];?> </li>
								<?php if($orderDetails['shipping_address_2'])
								{
									?>
								<li><?php echo $orderDetails['payment_address_2'];?></li>
									<?php
								}
								?>
								<li><?php echo $orderDetails['shipping_city'];?>, <?php echo $orderDetails['shipping_zone'];?> <?php echo $orderDetails['shipping_postcode'];?></li>
							</ul>
							<span class="line mb20"></span>
							<div class="cart-total-row row">
								<div class="col-xs-6">
									<p class="total-lbl">Sub-total</p>
								</div>
								<div class="col-xs-6">
									<p class="total-price text-right"><?php echo $this->currency->format($orderDetails['sub_total']);?></p>
								</div>
							</div>
							<span class="line mt5"></span>
							<div class="cart-total-row row">
								<div class="col-xs-6">
									<p class="total-lbl">Shipping </p>
								</div>
								<div class="col-xs-6">
									<p class="total-price text-right"><?php echo $this->currency->format($orderDetails['shipping_total']);?></p>
								</div>
							</div>
							<ul class="info-list">
								<li><?php echo ($orderDetails['shipping_method']);?></li>
								<!-- <li>ETA Many Days </li> -->
							</ul>
							<span class="line"></span>
							<?php
							if($orderDetails['voucher_total'])
							{



							?>
							<div class="cart-total-row row">
								<div class="col-xs-8">
									<p class="total-lbl">Voucher Code</p>
								</div>
								<div class="col-xs-4">
									<p class="total-price text-right">- <?php echo $this->currency->format($orderDetails['voucher_total']);?></p>
								</div>
							</div>
							<ul class="info-list">
							<?php
							foreach($orderDetails['total_vouchers'] as $voucher)
							{


							?>
								<li><?php echo $voucher['title'];?> </li>
								<?php
							}
							?>
							</ul>
							<span class="line"></span>
							<?php
						}
						?>
						<?php

						if($orderDetails['tax_total'])
						{
						?>
						<!-- <span class="line mb20"></span> -->
							<div class="cart-total-row row">
								<div class="col-xs-6">
									<p class="total-lbl">Tax</p>
								</div>
								<div class="col-xs-6">
									<p class="total-price text-right"><?php echo $this->currency->format($orderDetails['tax_total']);?></p>
								</div>
							</div>
							<span class="line mt5"></span>
							<?php

							}
							?>
							<div class="cart-total-row row">
								<div class="col-xs-8">
									<p class="total-lbl">TOTAL</p>
								</div>
								<div class="col-xs-4">
									<p class="total-price text-right" id="net-total"><?php echo $this->currency->format($orderDetails['total']);?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main><!-- @End of main -->
<?php echo $footer; ?>