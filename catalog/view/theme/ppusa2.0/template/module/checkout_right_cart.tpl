<div class="sidebox overflow-hide">
						<div class="cart-total-row row">
							<div class="col-xs-6">
								<p class="total-lbl">Sub-total</p>
							</div>
							<div class="col-xs-6">
								<p class="total-price text-right"><?php echo $sub_total;?></p>
							</div>
						</div>
						<span class="line"></span>
						<div class="cart-total-row row">
							<div class="col-xs-6">
								<p class="total-lbl">Shipping </p>
							</div>
							<div class="col-xs-6">
								<p class="total-price text-right"><?php echo $shipping_cost;?></p>
							</div>
						</div>
						<?php
						if($shipping_title!='N/A')
						{


						?>
						<ul class="sidebox-bluestrips">
						
							<li><?php echo $shipping_title;?></li>
							<li><?php echo $delivery_time;?></li>
							
						</ul>
						<?php
					}
					?>

						<span class="line"></span>
						<div class="cart-total-row row">
							<div class="col-xs-6">
							<input type="checkbox" <?php echo ($sign_product_exist) ? 'checked="checked"': '';?> name="signProduct" id="addSign" > 
      Signature <span class="glyphicon glyphicon-question-sign" rel="tipsy" original-title="Prevent yourself from non-delivery! (a very very rare event, but it does happen) Mailmen and women often leave packages on the porch, next to the door or common areas. Theft does occur! We do not take liability on missing or lost packages if shown as delivered in carrier tracking. We recommend all customers require signature for error-free delivery."></span>

								<!-- <p class="total-lbl">Signature <span class="glyphicon glyphicon-question-sign" rel="tipsy" original-title="Prevent yourself from non-delivery! (a very very rare event, but it does happen) Mailmen and women often leave packages on the porch, next to the door or common areas. Theft does occur! We do not take liability on missing or lost packages if shown as delivered in carrier tracking. We recommend all customers require signature for error-free delivery."></span></p> -->
							</div>
							<div class="col-xs-6">
								<p class="total-price text-right" id="sign_label"><?php echo ($sign_product_exist) ? '$3.00': '$0.00';?></p>
							</div>
						</div>
						
					
						
						<span class="line"></span>
						<div class="cart-total-row row">
										<div class="col-xs-6">
											<p>Voucher Code<a style="color:white" data-toggle="collapse" class="collapsed hidden-md hidden-lg" data-parent="#accordion" href="#discount_codes">
												<i class="fa fa-angle-down"></i>
											</a></p>
										</div>
										<div class="col-xs-6">
											<p class="total-voucher-price total-price text-right"></p>
										</div>
									</div>
						<div class="cart-total-row row">
							<div class="col-xs-12">
								<div class="code discountcode panel-heading" style="padding-left:0px;padding-top:0px;padding-bottom:0">


									<?php
									if($available_vouchers)
									{
									?>
								
								<div class="display_on_step1 display_on_step2" style="margin-bottom:5px"><a style="color:white;font-size:12px" data-toggle="collapse" class="collapsed" data-parent="#accordion" href="#available_vouchers">+ View Available Vouchers</a><br></div>

									<div id="available_vouchers" class="panel-collapse collapse" >
											<ul class="sidebox-bluestrips" style="margin-top:5px;margin-bottom:35px">
											<?php 
								foreach($available_vouchers as $voucher)
								{
								
								 ?>
								 <li><input type="hidden" class="code-box" style="float:left;color:#FFF;background-color:#4986fe" value="<?php echo $voucher['code'];?>" readOnly> <?php echo $voucher['code'];?> (<?php echo $this->currency->format($voucher['balance']);?>)  <button class="btn btn-success cart_apply_btn" style="float:right;padding:0px 14px;margin-top:-2px">+</button>
								 
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
								if($vouchers)
								{
								?>
								<ul class="sidebox-bluestrips">
						
								<?php
								$total_voucher =0;
								foreach($vouchers as $voucher)
								{
								$total_voucher = $total_voucher + $voucher['raw_amount'];
								 ?>
								<!-- <div class="clearfix" style="padding-top:10px"  ><input type="text" class="code-box" disabled style="float:left;margin-right:3px;width:55%" value="<?php echo $voucher['key'];?> "> <button class="btn btn-danger" onclick="window.location='<?php echo $voucher['remove'];?>'" style="float:left;margin-top:3px">-</button> </div> -->
								<li><?php echo $voucher['key'];?> 
								
								(<?php echo ($voucher['is_special']?'SPECIAL':'-'.$voucher['amount']) ;?>) 
								<button class="btn btn-danger" onclick="window.location='<?php echo $voucher['remove'];?>'" style="float:right;padding:0px 14px;margin-top:-2px">-</button></li>
								<?php
							}
							?>
							<script type="text/javascript">
								var voucher = parseFloat(<?php echo $total_voucher;?>);
								$('.total-voucher-price').html('-$'+voucher.toFixed(2));</script>
							</ul>
							<?php
							}
							?>
									<div class="clearfix display_on_step1 display_on_step2"   ><input type="text" class="code-box" style="float:left;width:55%" value=""> <button class="btn btn-info cart_apply_btn" style="padding:11px">Apply</button></div>
									<div class="clearfix display_on_step3 display_on_step4" style="display:none"><small style="font-size:11px">Please go to Step 1 / Step 2 to Apply Store credit</small></div>
									<br>
								
								</div>
							</div>
							</div>
						</div>
						<span class="line"></span>
						
					<?php
					if($tax)
					{
					?>
					<div class="cart-total-row row">
							<div class="col-xs-6">
								<p class="total-lbl"><?php echo $this->tax->getRateName('88');?></p>
							</div>
							<div class="col-xs-6">
								<p class="total-price text-right"><?php echo $tax;?></p>
							</div>
						</div>
						<span class="line"></span>
						<?php
					}
					?>
						
						<div class="cart-total-row row">
							<div class="col-xs-6">
								<p class="total-lbl" style="font-size:17px;font-weight: bold">TOTAL</p>
							</div>
							<div class="col-xs-6">
								<p class="total-price text-right" id="net-total" style="font-size:17px;font-weight: bold"><?php echo $total;?></p>
							</div>
						</div>
					</div>
					<div class="prev-next-btns greybg text-center" style="border-top:none">
								<button  style="padding:10px 60px;font-size:18px" class="button-place-order btn btn-green-reverse disabled hidden-xs hidden">Place Order</button>
							</div>

							<div style="padding:15px 65px;font-size:20px;width:100%;background-color: #44CA9F  " class="sticky_add_cart row button-place-order btn btn-green-reverse disabled hidden ">
	PLACE ORDER <i class="fa  fa-chevron-right"></i>
		
	</div>
							<script>
							$(document).ready(function(){
								$("span[rel=tipsy]").tipsy();
							});
							 $(document).ready(function(){
  if ($(window).width() <= 991){  
    
    $('#checkout_right_cart .panel-collapse').removeClass('in');
  }
});

							$('#addSign').on('click', function() {
    if (this.checked) {
    	
      $.ajax({
        url: 'index.php?route=checkout/cart/addSignature',
        type: 'post',
        data: 'product_id=<?= $sign_product;?>&quantity=1',
        dataType: 'json',
        success: function(json) {
          if (json['success']) {
            // console.log('Signature Added');
            $( "#checkout_right_cart" ).load( "index.php?route=module/checkout_right_cart" );
            $('#sign_label').html('$3.00');
          } 
        }
      });
    } else {
    	
      $.ajax({
        url: 'index.php?route=checkout/cart&remove=<?= $sign_product;?>&sign=1&is_ajax=1',
        dataType: 'html',
        success: function(html) {
        	$( "#checkout_right_cart" ).load( "index.php?route=module/checkout_right_cart" );
        	$('#sign_label').html('$0.00');
          // console.log('Signature Removed');
        }
      });
    }
  });

							</script>