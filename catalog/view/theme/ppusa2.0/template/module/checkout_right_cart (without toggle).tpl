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
							<input type="checkbox" <?= ($sign_product_exist) ? 'checked="checked"': '';?> name="signProduct" id="addSign" > 
      Signature <span class="glyphicon glyphicon-question-sign" rel="tipsy" original-title="Prevent yourself from non-delivery! (a very very rare event, but it does happen) Mailmen and women often leave packages on the porch, next to the door or common areas. Theft does occur! We do not take liability on missing or lost packages if shown as delivered in carrier tracking. We recommend all customers require signature for error-free delivery."></span>

								<!-- <p class="total-lbl">Signature <span class="glyphicon glyphicon-question-sign" rel="tipsy" original-title="Prevent yourself from non-delivery! (a very very rare event, but it does happen) Mailmen and women often leave packages on the porch, next to the door or common areas. Theft does occur! We do not take liability on missing or lost packages if shown as delivered in carrier tracking. We recommend all customers require signature for error-free delivery."></span></p> -->
							</div>
							<div class="col-xs-6">
								<p class="total-price text-right">$3.00</p>
							</div>
						</div>
						
					
						
						<span class="line"></span>
						
						<div class="cart-total-row row">
							<div class="col-xs-12">
								<div class="code discountcode">
								<p>Discount Code</p>
								
								<div id="discount_codes" >
								<?php 
								foreach($vouchers as $voucher)
								{
								 ?>
								<div class="clearfix" style="padding-top:10px"  ><input type="text" class="code-box" disabled style="float:left;margin-right:3px;width:55%" value="<?php echo $voucher['key'];?> "> <button class="btn btn-danger" onclick="window.location='<?php echo $voucher['remove'];?>'" style="float:left;margin-top:3px">-</button> </div>
								<?php
							}
							?>
									<div class="clearfix display_on_step1 display_on_step2" style="padding-top:10px"  ><input type="text" class="code-box" style="float:left;width:55%" value=""> <button class="btn btn-info cart_apply_btn" style="padding:11px">Apply</button></div>
									<div class="clearfix display_on_step3 display_on_step4" style="display:none"><small style="font-size:11px">Please go to Step 1 / Step 2 to Apply Store credit</div>
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
								<p class="total-lbl">TOTAL</p>
							</div>
							<div class="col-xs-6">
								<p class="total-price text-right"><?php echo $total;?></p>
							</div>
						</div>
					</div>
					<div class="prev-next-btns greybg text-center" style="border-top:none">
								<button id="button-place-order" style="padding:10px 60px;font-size:18px" class="btn btn-green-reverse disabled">Place Order</button>
							</div>
							<script>
							$(document).ready(function(){
								$("span[rel=tipsy]").tipsy();
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
          } 
        }
      });
    } else {
      $.ajax({
        url: 'index.php?route=checkout/cart&remove=<?= $sign_product;?>&sign=1&is_ajax=1',
        dataType: 'html',
        success: function(html) {
        	$( "#checkout_right_cart" ).load( "index.php?route=module/checkout_right_cart" );
          // console.log('Signature Removed');
        }
      });
    }
  });

							</script>