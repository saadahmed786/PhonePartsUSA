<div class="strip-cart-circle">
       <span class="badge hidden"><?php echo $total_items;?></span>
       <div class="icon cart2-icon hidden"></div>
       <div style="position: relative;font-size:10px;font-weight: bold;color:#FFF;margin-left:10px;margin-top:40px">QUICK CART</div>
    </div>

	
	<h2 class="top-dropdowns-title uppercase"><img src="catalog/view/theme/ppusa2.0/images/icons/newcart.png" alt="" width="25px" style="margin-right: 15px;"><a class="blue" href="<?php echo $this->url->link('checkout/cart');?>">VIEW SHOPPING CART</a> <span onmouseover="$(this).css('text-decoration','underline')" onmouseout="$(this).css('text-decoration','none');" class="close-cart" style="cursor:pointer; font-size: 20px; margin-top: 4px; float: right; color: #4986fe;">Close</span></h2>
	<div class="cart-scroll2Parent">
		<div class="top-dropdown-body shoping-cart-box cart-product-small cart-scroll2">
			<?php foreach ($products as $kr => $product) { ?>
			<!-- Putting Signature in the End -->
			<?php if ($product['model'] == "SIGN") { ?>
			<?php
			$sign = '<div class="product-detail row pr<?php echo (count($products) - 1); ?>">';
			$sign .= '<div class="product-detail-inner clearfix">';
			$sign .= '<div class="col-md-2 product-detail-img">';
			$sign .= '<div class="image">';
			$sign .= '<a href="javascript:void(0);"></a>';
			$sign .= '</div>';
			$sign .= '</div>';
			$sign .= '<div class="col-md-10 product-detail-text">';
			$sign .= '<h3>' . $product['name'] . '</h3>';
			$sign .= '<div class="row">';
			$sign .= '<div class="col-md-5">';
			$sign .= '</div>';
			$sign .= '<div class="col-md-3">';
			$sign .= '</div>';
			$sign .= '<div class="col-md-4 cart-total-wrp">';
			$sign .= '<div class="cart-total text-center">';
			$sign .= '<h3 class="cartPPrice">' . $product['total'] . '</h3>';
			$sign .= '<div class="qtyt-box">';
			$sign .= '</div>';
			$sign .= '</div>';
			$sign .= '</div>';
			$sign .= '</div>';
			$sign .= '</div>';
			$sign .= '</div>';
			$sign .= '</div>';
			continue;
			?>

			<?php } ?>
			<div class="product-detail row pr<?php echo $kr; ?>">
				<div class="product-detail-inner clearfix">
					<a href="javascript:void(0);" class="cart-close" product-id="<?php echo $product['key']; ?>"><img src="catalog/view/theme/ppusa2.0/images/icons/cross2.png" alt=""></a>
					<span class="hidden-xs hidden-sm hidden-md hidden-lg removeProduct" product-id="<?php echo $product['key']; ?>">remove</span>
					<div class="col-md-2 product-detail-img">
						<div class="image">
							<?php if ($product['thumb']) { ?>
							<a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a>
							<?php } ?>
						</div>
						<h2><small><?php echo $product['model']; ?></small></h2>
					</div>
					<div class="col-md-10 product-detail-text">
						<h3><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h3>
						<div class="row">
							<div class="col-md-5" style="display: none;">
								<div class="row item-features">
									<ul>
										<li class="col-md-6">
											<?php echo $product['model'];?>
										</li>
										<li class="col-md-6">
											Product
										</li>
									</ul>
									<ul>
										<li class="col-md-6">
											Compatibility
										</li>
										<li class="col-md-6">
											Type
										</li>
									</ul>
									<ul>
										<li class="col-md-6">
											Information
										</li>
										<li class="col-md-6">
											Information
										</li>
									</ul>
								</div>
							</div>
							<div class="col-md-5">
								<div class="cart-quality">
									<table class="table">
										<tbody>
											<tr>
												<td><strong>Quantity</strong></td>
												<?php foreach ($product['discounts'] as $discount) : ?>
													<td><?php echo $discount['quantity']; ?></td>
												<?php endforeach; ?>
											</tr>
												<tr>
												<td><strong>Our Price</strong></td>
												<?php foreach ($product['discounts'] as $discount) : ?>
													<td><?php echo $discount['price']; ?></td>
												<?php endforeach; ?>
												</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="col-md-3 cart-total-wrp">
							<div class="qtyt-box">
								<input type="hidden" class="product_id" value="<?php echo $product['product_id']; ?>" />
								<div class="input-group spinner">
									<span class="txt"></span>
									<input type="text" class="form-control" value="<?php echo $product['quantity']; ?>">
									<div class="input-group-btn-vertical" style="margin-top:-5px">
										<button class="btn" type="button"><i class="fa fa-plus"></i></button>
										<button class="btn" type="button"><i class="fa fa-minus"></i></button>
									</div>
								</div>
							</div>
							</div>
							<div class="col-md-4 cart-total-wrp">
								<div class="cart-total text-center">
									<h2 class="cartPPrice"><?php echo $product['total']; ?><br><small>(<?php echo $product['price']; ?> ea)</small></h2>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>			
			<?php } ?>
			<!-- Putting Signature in the End -->
			<?php //echo $sign; ?>
		</div>
		</div>

	<div class="top-dropdown-ftr">
		<!-- <table class="cart-total">
			<tbody>
				<?php foreach ($totals as $total) { ?>
				<tr>
					<td><strong><?php echo $total['title']; ?></strong></td>
					<td><span class="blue"><?php echo $total['text']; ?></span></td>
				</tr>
				<?php } ?>
			</tbody>
		</table> -->
		<div class="row">
		<div class="col-md-6">
			<?php foreach ($totals as $total) { ?>
			<?php if (strtolower($total['title']) == 'sub-total') { ?>
			<h2 class="text-center" style="font-size: 70px; line-height: 50px; margin: 0;"><small style="font-size: 30%;" class="blue">SUB TOTAL</small><br><span class="blue"><?php echo $total['text']; ?></span></h2>
			<?php } ?>
			<?php } ?>
			<p class="text-center" style="margin-top: 15px;"><span><a id="calculate_shipping_tax" href="<?php echo $cart; ?>" style="color: gray;">CALCULATE SHIPPING & TAX</a></span></p>
			<!-- <p class="text-center"><span><a id="" onmouseover="$(this).css('text-decoration','underline');" onmouseout="$(this).css('text-decoration','none');" href="<?php echo $cart; ?>" style="color: gray;">VIEW SHOPPING CART</a></span></p> -->
		</div>
		<div class="col-md-1">
		</div>
		<div class="col-md-5">
			<a href="<?php echo $checkout; ?>"><img class="side-cart-bottom" src="catalog/view/theme/ppusa2.0/images/icons/newbasket2.png" alt="" width="257px"></a>
			<h2 class="blue strike">
			<span>OR</span>
			</h2>
			<a href="<?php echo $store_url . 'index.php?route=payment/paypal_express_new/SetExpressCheckout';?>"><img class="side-cart-bottom" src="catalog/view/theme/ppusa2.0/images/icons/newpaypal.png" alt="" width="257px"></a>
		</div>
		</div>
	</div>
