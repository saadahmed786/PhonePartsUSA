

<div class="cart-total-row row" style="display:none" >
						<div class="col-xs-6">
							<p class="total-lbl" style="font-size:20px">Sub-total</p>
						</div>
						<div class="col-xs-6">	
						<p class="total-price text-right" style="font-size:20px"><?php echo $sub_total;?></p>
						</div>
					</div>
					<div class="cart-total-row row panel-heading" style="padding-left:0px"  >
						<div class="col-xs-12">
							<p class="total-lbl cart-headings">ESTIMATE SHIPPING &amp; TAX <a style="color:white" data-toggle="collapse" class="collapsed hidden-md hidden-lg" data-parent="#accordion" href="#collapse1">
									 <i class="fa fa-angle-down"></i>
								</a></p>
						</div>
						
					</div>
					
					<span class="line"></span>
					<div id="collapse1" class="panel-collapse collapse in">
					<div class="cart-total-row row">

					<div class="col-xs-5">
							<p class="total-lbl" style="display:none">Shipping Estimate</p>
							<div class="code zipcode" style="margin-top:0px">
								<p>ZIP Code</p>
								<input type="text" id="zip_cart" name="postcode" class="code-box" value="<?php echo $postcode; ?>" style="width:61%"><button id="apply_zip_cart" class="btn btn-info cart_apply_btn" style="padding:11px;width:35%">Go</button>
							</div>
						</div> 
						
						<div class="col-xs-6" id="cart-shipping-estimate" style="margin-left:0px" >
							
						</div>
						

						<div class="col-xs-2" style="display:none">
							<!-- <p class="total-price text-right" id="shipping-text">$ 0.00</p> -->
						</div>
					</div>

					<span class="line"></span>
					<div class="cart-total-row row" style="line-height:40px">
						<div class="col-xs-6">
							<p class="total-lbl cart-sub-headings">SUB-TOTAL</p>
						</div>
						<div class="col-xs-6">
							<p class="total-price text-right cart-sub-headings"><?php echo $sub_total;?></p>
						</div>

						<div class="col-xs-6">
							<p class="total-lbl cart-sub-headings">SHIPPING</p>
						</div>
						<div class="col-xs-6">
							<p class=" total-price text-right cart-sub-headings" id="shipping-text" >$ 0.00</p>
						</div>
						

						<div class="col-xs-6">
							<p class="total-lbl cart-sub-headings">TAX</p>
						</div>
						<div class="col-xs-6">
							<p class=" total-price text-right cart-sub-headings"  ><?php echo $tax;?></p>
						</div>

					</div>


					<span class="line"></span>
					</div>
					<div class="cart-total-row row">
						
						<div class="col-xs-8">
							<div class="code discountcode panel-heading" style="padding-left:0px;padding-top:0px;padding-bottom:0">
								<p>Voucher Code <a style="color:white" data-toggle="collapse" class="collapsed hidden-md hidden-lg" data-parent="#accordion" href="#discount_codes">
									 <i class="fa fa-angle-down"></i>
								</a></p>
								<?php
									if($available_vouchers)
									{
								?>
								
								<div style="margin-bottom:5px"><a style="color:white;font-size:12px" data-toggle="collapse" class="collapsed" data-parent="#accordion" href="#available_vouchers">+ View Available Vouchers</a><br></div>

									<div id="available_vouchers" style="padding-left:32px;padding-right:0px;width:151%" class="panel-collapse collapse" >
											<ul class="sidebox-bluestrips" style="margin-top:5px;margin-bottom:35px">
											<?php 
								foreach($available_vouchers as $voucher)
								{
								
								 ?>
								 <li><input type="hidden" class="code-box" style="float:left;color:#FFF;background-color:#4986fe" value="<?php echo $voucher['code'];?>" readOnly> <?php echo $voucher['code'];?> Balance: (<?php echo $this->currency->format($voucher['balance']);?>)  <button class="btn btn-success cart_apply_btn" style="float:right;padding:0px 14px;margin-top:-2px">+</button>
								 
								 </li>

								 <?php

								 }
								 ?>
								 </ul>
									</div>

								<?php
								}
								?>
								
								<div id="discount_codes" class="panel-collapse collapse in" >
								<?php 
								foreach($vouchers as $voucher)
								{
								 ?>
								<div class="clearfix" style="padding-top:10px;margin-bottom:5px"  ><input type="text" class="code-box" disabled style="float:left;margin-right:3px" value="<?php echo $voucher['key'];?> "> <button class="btn btn-danger" onclick="window.location='<?php echo $voucher['remove'];?>'" style="float:left;margin-top:3px">-</button> </div>
								<?php
							}
							?>
									<div class="clearfix"   ><input type="text" class="code-box" style="float:left;" value=""> <button class="btn btn-info cart_apply_btn" style="padding:11px">Apply</button></div><br><a href="javascript:void(0);" id="add_discount_row" style="font-size:12px;color:#FFF">+ Add More</a>
								
								</div>
							</div>
						</div>
						<div class="col-xs-4">
							<p class="total-price text-right"><?php echo ($voucher_total!='$0.00'?'-':'');?> <?php echo $voucher_total;?></p>
						</div>
					</div>
					<span class="line"></span>
					<div class="cart-total-row row">
						<div class="col-xs-6">
							<p class="total-lbl cart-headings" style="font-weight:bold">TOTAL</p>
						</div>
						<div class="col-xs-6">
							<p class="total-price text-right cart-headings" id="net-total"><?php echo $total;?></p>
						</div>
					</div>
	