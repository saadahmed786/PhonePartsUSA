<?php echo $header; ?>
<!-- @End of header -->
	<main class="main">
		<div class="container payment-3-page">
			<div class="row">
				<div class="col-md-9">
					
					<div class="tab-content">
					    
					    <div id="paymentMethod" class="tab-pane fade in active">
					    	<div class="pamentMethod-head">
						   		<div class="row">
						        	<div class="col-lg-4 col-md-5">
						        		<h3>Payment method:</h3>
						        	</div>
						        	<div class="col-lg-3 col-md-4 col-xs-6">
						        		<input type="radio" name="creditcard" id="creditcard">
						        		<label for="rdo1" class="css-radio2">credit card</label>
						        	</div>
						        	<div class="col-lg-4 col-md-3 col-xs-6">
						        		<input type="radio" name="paypal"  id="paypal">
						        		<label for="rdo2" class="css-radio2">paypal</label>
						        	</div>
						        </div>
					        </div>
					        <div class="payment-address">	
						        <div class="row">
							        <div class="col-md-5">
								        <div class="form-horizontal v-form">
								        	<div class="billing-info address-box">
												<h3 class="form-title">Billing address</h3>
												<div class="form-group">
													<div class="col-md-12">
					  									<input type="checkbox" name="chk1"  id="chk1">
														<label for="ck1" class="css-label2">Same as Shipping Address</label>
													</div>
												</div>
												<div class="form-group">
													<label for="inputStreet" class="col-sm-12 control-label">Street Address</label>
												    <div class="col-sm-12">
												      <input type="text" class="form-control" id="streetaddress1"
												      name="streetaddress1" placeholder="5536 Gordon Mills">

												    </div>
												</div>
												<div class="form-group">
													<label for="inputSuite" class="col-sm-12 control-label pt0">Suite or Apartament</label>
												    <div class="col-sm-12">
												      <input type="text" class="form-control" id="suitorapartment1" name="suitorapartment1" placeholder="725 Lorenz Cliff">
												    </div>
												</div>
												<div class="form-group">
													<label for="inputZip" class="col-sm-12 control-label">ZIP Code</label>
												    <div class="col-sm-12">
												    	<div class="row margin5">
												    		<div class="col-md-12">
												    			<input type="text" class="form-control" id="zipcode1" name="zipcode1" placeholder="312512512">
												    		</div>
												    	</div>	
												    </div>
												</div>
												<div class="form-group">
													<div class="col-md-7 mb-sm-15">
														<div class="row">
															<label for="inputState" class="col-sm-12 control-label">City</label>
														    <div class="col-sm-12">
														    	<input type="text" class="form-control" id="city1" name="city1" placeholder="Dustystad">
														    </div>
													    </div>
												    </div>
												    <div class="col-md-5 pl0">
														<div class="row">
															<label for="inputState" class="col-sm-12 control-label">State</label>
														    <div class="col-sm-12">
														    	<select id="state1" name="state1" >
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
												    	<select id="country1" name="country1" >
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
							        		<div class="col-xs-3">
							        			<div class="text-left">
							        				<img src="images/payment/Payment.png" alt="">
							        			</div>
							        		</div>
							        		<div class="col-xs-3">
							        			<div class="text-center">
							        				<img src="images/payment/Payment1.png" alt="">
							        			</div>
							        		</div>
							        		<div class="col-xs-3">
							        			<div class="text-center">
							        				<img src="images/payment/Payment2.png" alt="">
							        			</div>
							        		</div>
							        		<div class="col-xs-3">
							        			<div class="text-right">
							        				<img src="images/payment/Payment3.png" alt="">
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
										   		<div class="col-md-5 pr0 mb-sm-15">
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
										
							        </div>
						        </div>
						        
						        
						    </div>
						    <div class="row">
								<div class="col-md-12">
									 <div class="text-right prev-next-btns greybg">
										<a id="load_shipping_info" class="btn btn-primary"><i class="fa fa-angle-left"></i>Previous Step</a>
										<a id="load_confirm_order" onclick="opentablink3();" class="btn btn-info light">Next Step <i class="fa fa-angle-right"></i></a>
									</div>
								</div>
							</div>
						         
					    </div>
					    
					</div>
				</div>
				
			</div>
		</div>
	</main><!-- @End of main -->
<?php echo $footer; ?>
<!-- @End of footer -->