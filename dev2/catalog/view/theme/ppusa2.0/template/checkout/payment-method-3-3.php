<?php echo $header; ?>
<!-- @End of header -->
	<main class="main">
		<div class="container payment-3-page">
			<div class="row">
				<div class="col-md-9">
					<ul class="nav nav-tabs small">
					    <li><a href="#contactInfo">1.Contact Information</a></li>
					    <li><a href="#shippingInfo">2.Shipping Information </a></li>
					    <li class="active"><a href="#paymentMethod">3.Payment Method</a></li>
					    <li><a href="#confmOdr">4.Confirm Order</a></li>
					</ul>
					<div class="tab-content">
					    <div id="contactInfo" class="tab-pane fade">
					    </div>
					    <div id="shippingInfo" class="tab-pane fade">
					      	lorem ipsum
					    </div>
					    <div id="paymentMethod" class="tab-pane fade in active">
					    	<div class="pamentMethod-head">
						   		<div class="row">
						        	<div class="col-md-4">
						        		<h3>Payment method:</h3>
						        	</div>
						        	<div class="col-md-3 col-xs-6">
						        		<input type="radio" name="payment-type" class="css-radio2" id="rdo1" checked="checked">
						        		<label for="rdo1" class="css-radio2">pay at pickup</label>
						        	</div>
						        	<div class="col-md-4 col-xs-6">
						        		<input type="radio" name="payment-type" class="css-radio2" id="rdo2" >
						        		<label for="rdo2" class="css-radio2">paypal</label>
						        	</div>
						        </div>
					        </div>
					        <div class="tab-inner pd60">
						    	<div class="map">
						    		<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d60913294.694780126!2d-157.8342140009555!3d21.277307040060254!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2s!4v1461122914616" frameborder="0" style="border:0" allowfullscreen></iframe>
						    	</div>
						    	<div class="row map-direction">
						    		<div class="col-xs-6">
						    			<div class="map-address">
						    			<p>Phone Parts USA</p>
										<p>5145 S Arville St Suite A</p>
										<p>Las Vegas, NV 891118</p>
										</div>
						    		</div>
						    		<div class="col-xs-6">
						    			<div class="text-right">
						    				<a href="#" class="blue underline">Driving Directions</a>
						    			</div>
						    		</div>
						    	</div>
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