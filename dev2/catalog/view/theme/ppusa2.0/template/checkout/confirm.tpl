	<main class="main">
		<div class="container order-confirm-page">
			<div class="row">
				<div class="col-md-9">
					
					<div class="tab-content">
					    
					    <div id="confirmOrder" class="tab-pane fade in active">
					    	<div class="tab-inner">
					      		<div class="row order-address">
					      			<div class="col-lg-9 col-md-12">
					      				<div class="row address-row">
					      					<div class="col-md-4 address-col">
					      						<h5>Contact information <a href="#"><span class="edit-icon"><img src="images/icons/edit-icon.png" alt=""></span></a></h5>
					      						<ul class="address-list">
					      							<li id="confirmname"></li>
					      							<li id="confirmphone"></li>
					      							<li id="confirmemail"></li>
					      						</ul>
					      					</div>
					      					<div class="col-md-4 address-col">
					      						<h5>Shipping Address <a href="#"><span class="edit-icon"><img src="images/icons/edit-icon.png" alt=""></span></a></h5>
					      						<ul class="address-list">
					      							<li id="csname">Notareal Person</li>
					      							<li id="csaddress">6249 Franklin Avenue</li>
					      							<li id="cssuit">Suite 2A</li>
					      							<li id="csarea">Pelham, Al 35124</li>
					      						</ul>
					      					</div>
					      					<div class="col-md-4 address-col">
					      						<h5>Billing Address <a href="#"><span class="edit-icon"><img src="images/icons/edit-icon.png" alt=""></span></a></h5>
					      						<ul class="address-list">
					      							<li id="baname">Notareal Person</li>
					      							<li id="baaddress">6249 Franklin Avenue</li>
					      							<li id="basuit">Suite 2A</li>
					      							<li id="baarea">Pelham, Al 35124</li>
					      						</ul>
					      					</div>
					      				</div>
					      			</div>
					      			<div class="col-lg-3 col-md-12 order-shipping">
					      				<h5>Shipping method</h5>
					      				<select name="" class="selectpicker">
					      					<option value="">Bike Courier - $29.00"</option>
					      					<option value="">Bike Courier - $29.00"</option>
					      				</select>
					      				<h5>Billing Method &nbsp; <a href="#"><span class="edit-icon"><img src="images/icons/edit-icon.png" alt=""></span></a></h5>
					      				<table class="table sidebox-billing">
											<tbody>
												<tr>
													<td><img src="images/visa.png" alt="" class="visa-img"></td>
													<td>
														<p>Ending in </p>
														<p>6666</p>
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
					      		
									<div class="product-detail pr0 row">
										<div class="product-detail-inner clearfix">
											<div class="col-md-2 product-detail-img">
												<div class="image"><img src="images/cart-detail/iphone-big.png" alt=""></div>
											</div>
											<div class="col-md-10 product-detail-text">
												
												 <?php foreach ($products as $product) { ?>
												<a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
												<div class="table mb0">
													<div class="row table-row">
														<div class="col-md-6 pl0 table-cell v-top">
															<p class="item-qty">Qty: 5 at $105.65 ea</p>
														</div>
														<div class="col-md-6 cart-total-wrp table-cell v-bottom">
															<div class="cart-total text-right">
																<h3 class="onbottom"><span>Item Total:</span>$ 105.00</h3>
															</div>
														</div>
													</div>
												</div>
												<?php }?>
											</div>
										</div>
									</div>
									
									<!-- Product detail row -->
									
									<!-- Product detail row -->
									
									<!-- Product detail row -->
								</div>	
							</div>
							<div class="text-right prev-next-btns greybg">
								<button id="placeorder" class="btn btn-green">Place Order</button>
							</div>
					    </div>
					</div>
				</div>
				
			</div>
		</div>
	</main>