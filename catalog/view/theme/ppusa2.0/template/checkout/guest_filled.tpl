<?php echo $header; ?>
<main class="main">
		<div class="container payment-3-page">
			<div class="row">
				<div class="col-md-9">
					<ul class="nav nav-tabs small">
					    <li class="active"><a href="#contactInfo">1.Contact Information</a></li>
					    <li><a href="#shippingInfo">2.Shipping Information </a></li>
					    <li><a href="#paymentMethod">3.Payment Method</a></li>
					    <li><a href="#confmOdr">4.Confirm Order</a></li>
					</ul>
					<div class="tab-content">
					    <div id="contactInfo" class="tab-pane fade in active">
					    	<div class="tab-inner pd100">
					    		<h3 class="blue-title uppercase mb40">contact information</h3>
					    		<div class="row">
						    		<div class="col-md-11">
								    	<form class="form-horizontal">
								    		<div class="form-group">
			  									<label for="inputFname" class="col-xs-4 control-label">First Name</label>
											    <div class="col-xs-8">
											      <input type="text" class="form-control" id="inputFname" placeholder="Gerald">
											    </div>
			  								</div>
			  								<div class="form-group">
			  									<label for="inputLname" class="col-xs-4 control-label">Last name</label>
											    <div class="col-xs-8">
											      <input type="text" class="form-control" id="inputLname" placeholder="Anderson">
											    </div>
			  								</div>
			  								<div class="form-group">
			  									<label for="Bname" class="col-xs-4 control-label">Business Name </label>
											    <div class="col-xs-8">
											      <input type="text" class="form-control" id="Bname" placeholder="lorem">
											    </div>
			  								</div>
			  								<div class="form-group">
			  									<label for="phone" class="col-xs-4 control-label">Phone</label>
											    <div class="col-xs-8">
											      <input type="tel" class="form-control" id="phone" placeholder="lorem">
											    </div>
			  								</div>
			  								<div class="form-group">
			  									<label for="inputEmail" class="col-xs-4 control-label">E-mail</label>
											    <div class="col-xs-8">
											      <input type="email" class="form-control" id="inputEmail" placeholder="tech@phoneshop.com">
											    </div>
			  								</div>
			  								<div class="form-group">
											    <label for="inputEmail2" class="col-xs-4 control-label">Confirm Email</label>
											    <div class="col-xs-8">
											      <input type="email" class="form-control" id="inputEmail2" placeholder="tech@phoneshop.com">
											    </div>
			  								</div>
			  								<div class="form-group">
			  									<label for="inputLname" class="col-xs-4 control-label"></label>
			  									<div class="col-sm-8 col-xs-12">
													<p>
					  									<input type="checkbox" class="css-checkbox check-toggler" id="ck1">
														<label for="ck1" class="css-label2">Create Account</label>
													</p>
													<br>
											        <p>
					  									<input type="checkbox" class="css-checkbox" id="ck2" checked="checked">
														<label for="ck2" class="css-label2">Receive <a href="#" class="underline">Special Offers</a> from <a href="" class="underline">PhonePortsUSA</a></label>
													</p>
				  								</div>
			  								</div>
			  								<div class="check-toggled mt-xs">
				  								<div class="form-group">
				  									<label for="inputPassword" class="col-xs-4 control-label">Password</label>
												    <div class="col-xs-8">
												      <input type="password" class="form-control" id="inputPassword" placeholder="e.g., ••••••••••••">
												    </div>
				  								</div>
				  								<div class="form-group">
				  									<label for="inputPassword2" class="col-xs-4 control-label">Confirm Password</label>
												    <div class="col-xs-8">
												      <input type="password" class="form-control" id="inputPassword2" placeholder="e.g., ••••••••••••">
												    </div>
				  								</div>
				  							</div>	
			  							</form>	
		  							</div>	
	  							</div>
					    	</div>
					    	<div class="row">
								<div class="col-md-12">
									 <div class="text-right prev-next-btns greybg">
										<a href="#" class="btn btn-info light">Next Step <i class="fa fa-angle-right"></i></a>
									</div>
								</div>
							</div>
					    </div>
					    <div id="shippingInfo" class="tab-pane fade">
					      	lorem ipsum
					    </div>
					    <div id="paymentMethod" class="tab-pane fade">
					    	<div class="pamentMethod-head">
						   		<div class="row">
						        	<div class="col-md-4">
						        		<h3>Payment method:</h3>
						        	</div>
						        	<div class="col-md-3 col-xs-6">
						        		<input type="radio" name="payment-type" class="css-radio2" id="rdo1">
						        		<label for="rdo1" class="css-radio2">credit card</label>
						        	</div>
						        	<div class="col-md-4 col-xs-6">
						        		<input type="radio" name="payment-type" class="css-radio2" id="rdo2">
						        		<label for="rdo2" class="css-radio2">paypal</label>
						        	</div>
						        </div>
					        </div>
					        <div class="payment-address">	
						        <div class="row">
							        <div class="col-md-5">
								        <div class="form-horizontal v-form">
								        	<div class="billing-info address-box">
												<h3 class="form-title">shipping address</h3>
												<div class="form-group">
													<label for="inputStreet" class="col-sm-12 control-label">Street Address</label>
												    <div class="col-sm-12">
												      <input type="text" class="form-control" id="inputStreet" placeholder="5536 Gordon Mills">
												    </div>
												</div>
												<div class="form-group">
													<label for="inputSuite" class="col-sm-12 control-label pt0">Suite or Apartament</label>
												    <div class="col-sm-12">
												      <input type="text" class="form-control" id="inputSuite" placeholder="725 Lorenz Cliff">
												    </div>
												</div>
												<div class="form-group">
													<label for="inputZip" class="col-sm-12 control-label">ZIP Code</label>
												    <div class="col-sm-12">
												    	<div class="row margin5">
												    		<div class="col-md-12">
												    			<input type="text" class="form-control" id="inputZip" placeholder="312512512">
												    		</div>
												    	</div>	
												    </div>
												</div>
												<div class="form-group">
													<div class="col-md-7">
														<div class="row">
															<label for="inputState" class="col-sm-12 control-label">City</label>
														    <div class="col-sm-12">
														    	<input type="text" class="form-control" id="inputZip" placeholder="Dustystad">
														    </div>
													    </div>
												    </div>
												    <div class="col-md-5 pl0">
														<div class="row">
															<label for="inputState" class="col-sm-12 control-label">State</label>
														    <div class="col-sm-12">
														    	<select class="selectpicker">
										    						<option value="">Skylarbury</option>
										    						<option value="">Skylarbury</option>
										    						<option value="">Skylarbury</option>
										    						<option value="">Skylarbury</option>
										    					</select>	
														    </div>
													    </div>
												    </div>
												</div>
												<div class="form-group">
													<label for="inputState" class="col-sm-12 control-label">Country</label>
												    <div class="col-sm-12">
												    	<select class="selectpicker">
								    						<option value="">Thailand</option>
								    						<option value="">Thailand</option>
								    						<option value="">Thailand</option>
								    						<option value="">Thailand</option>
								    					</select>	
												    </div>
												</div>
											</div>
								        </div>
							        </div>
							        <div class="col-md-7 credit-payment sidebox">
							        	<h3>secure credit card payment </h3>
							        	<p class="subtitle">256-Bit ssl encrypted payment </p>
							        	<div class="row payment-images">
							        		<div class="col-sm-3">
							        			<div class="text-left">
							        				<img src="images/payment/payment.png" alt="">
							        			</div>
							        		</div>
							        		<div class="col-sm-3">
							        			<div class="text-center">
							        				<img src="images/payment/payment1.png" alt="">
							        			</div>
							        		</div>
							        		<div class="col-sm-3">
							        			<div class="text-center">
							        				<img src="images/payment/payment2.png" alt="">
							        			</div>
							        		</div>
							        		<div class="col-sm-3">
							        			<div class="text-right">
							        				<img src="images/payment/payment3.png" alt="">
							        			</div>
							        		</div>
							        	</div>
							        	<span class="line"></span> 	
							        	<form role="form">
										  <div class="form-group">
										    <label for="cartName">Name on Card</label>
										    <input type="text" class="form-control" id="cartName" placeholder="Name">
										  </div>
										  <div class="form-group">
										    <label for="cardNum">Card Number</label>
										    <input type="text" class="form-control" id="cardNum" placeholder="0000 0000 0000 0000">
										  </div>
										  <div class="form-group">
										    <label for="expiryDate">Expiration Date</label>
										    <div class="row">
										   		<div class="col-md-5 pr0">
										   			<select class="selectpicker">
							    						<option value="">11 - November</option>
							    						<option value="">11 - November</option>
							    						<option value="">11 - November</option>
							    						<option value="">11 - November</option>
							    					</select>
										   		</div>
										   		<div class="col-md-4">
										   			<select class="selectpicker">
							    						<option value="">2018</option>
							    						<option value="">2018</option>
							    						<option value="">2018</option>
							    						<option value="">2018</option>
							    					</select>
										   		</div>
										    </div>
										  </div>
										  <div class="form-group">
										    <label for="securityCod">Security Code</label>
										    <div class="row">
											    <div class="col-md-5 pr0">
										   			<input type="text" class="form-control" id="securityCod" placeholder="000">
										   		</div>
									   		</div>
										  </div>
										</form>
										<span class="line"></span>
										<div class="row security-img">
											<div class="col-md-4">
												<div class="text-center">
													<img src="images/security/security.png" alt="">
												</div>
											</div>
											<div class="col-md-4">
												<div class="text-center">
													<img src="images/security/security1.png" alt="">
												</div>
											</div>
											<div class="col-md-4">
												<div class="text-center">
													<img src="images/security/security2.png" alt="">
												</div>
											</div>
										</div>
							        </div>
						        </div>
						        <p class="text-center note">Note: A billing address whitch does no t match the shipping address may  require additional verification, and may delay your oreder</p>
						    </div>
						    <div class="row">
								<div class="col-md-12">
									 <div class="text-right prev-next-btns greybg">
										<a href="#" class="btn btn-primary"><i class="fa fa-angle-left"></i>Previous Step</a>
										<a href="#" class="btn btn-info light">Next Step <i class="fa fa-angle-right"></i></a>
									</div>
								</div>
							</div>    
					    </div>
					    <div id="confmOdr" class="tab-pane fade">
					      <p>Eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
					    </div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="sidebox overflow-hide">
						<div class="cart-total-row row">
							<div class="col-xs-6">
								<p class="total-lbl">Sub-total</p>
							</div>
							<div class="col-xs-6">
								<p class="total-price text-right">$ 310.00</p>
							</div>
						</div>
						<span class="line"></span>
						<div class="cart-total-row row">
							<div class="col-xs-6">
								<p class="total-lbl">Shipping </p>
							</div>
							<div class="col-xs-6">
								<p class="total-price text-right">$ 0.00</p>
							</div>
						</div>
						<ul class="sidebox-bluestrips">
							<li>Bike Courier</li>
							<li>ETA Many Days </li>
						</ul>
						<span class="line"></span>
						<div class="cart-total-row row">
							<div class="col-xs-12">
								<div class="code discountcode">
									<p>Credit Voucher <span class="total-price text-right">- 7.00</span></p>
									<input type="text" class="code-box" value="201RMWE12">
								</div>
							</div>
						</div>
						<span class="line"></span>
						<div class="cart-total-row row">
							<div class="col-xs-12">
								<div class="code discountcode">
									<p>Discount Code <span class="total-price text-right">- 10.00</span></p>
									<input type="text" class="code-box" value="201RMWE12">
								</div>
							</div>
						</div>
						<span class="line"></span>
						<div class="cart-total-row row">
							<div class="col-xs-6">
								<p class="total-lbl">TOTAL</p>
							</div>
							<div class="col-xs-6">
								<p class="total-price text-right">$300.00</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main><!-- @End of main -->
<?php echo $footer; ?>
<!-- @End of footer -->