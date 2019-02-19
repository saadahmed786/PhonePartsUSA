<span class="badge" ><?php echo $total_items; ?></span>
<div class="top-dropdowns cart-menu" <?php echo ($_GET['remove'])? 'style="display:block;"':''; ?>>
	<div class="caret-up"><img src="catalog/view/theme/ppusa2.0/images/icons/angle-down-grey.png" alt=""></div>
	<h2 class="top-dropdowns-title uppercase">Shopping Cart</h2>
	<?php if ($products) { ?>
		<div class="top-dropdown-body shoping-cart-box cart-product-small cart-scroll">
			<?php foreach ($products as $kr => $product) { ?>
			<!-- Putting Signature in the End -->
			<?php if ($product['model'] == "SIGN") { ?>
			<?php
			$sign = '<div class="product-detail row pr<?php echo (count($products) - 1); ?>">';
			$sign = '<div class="product-detail-inner clearfix">';
			$sign = '<div class="col-md-2 product-detail-img">';
			$sign = '<div class="image">';
			$sign = '<a href="javascript:void(0);"></a>';
			$sign = '</div>';
			$sign = '</div>';
			$sign = '<div class="col-md-10 product-detail-text">';
			$sign = '<h3>' . $product['name'] . '</h3>';
			$sign = '<div class="row">';
			$sign = '<div class="col-md-5">';
			$sign = '</div>';
			$sign = '<div class="col-md-3">';
			$sign = '</div>';
			$sign = '<div class="col-md-4 cart-total-wrp">';
			$sign = '<div class="cart-total text-center">';
			$sign = '<h3 class="cartPPrice">' . $product['total'] . '</h3>';
			$sign = '<div class="qtyt-box">';
			$sign = '</div>';
			$sign = '</div>';
			$sign = '</div>';
			$sign = '</div>';
			$sign = '</div>';
			$sign = '</div>';
			$sign = '</div>';
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
					</div>
					<div class="col-md-10 product-detail-text">
						<h3><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h3>
						<div class="row">
							<div class="col-md-5">
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
							<div class="col-md-3">
								<div class="cart-quality">
									<table class="table">
										<thead>
											<tr>
												<th>Quantity</th>
												<th>Our Price</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($product['discounts'] as $discount) : ?>
												<tr>
													<td><?php echo $discount['quantity']; ?></td>
													<td><?php echo $discount['price']; ?></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
							<div class="col-md-4 cart-total-wrp">
								<div class="cart-total text-center">
									<input type="hidden" class="product_id" value="<?php echo $product['product_id']; ?>" />
									<h3 class="cartPPrice"><?php echo $product['total']; ?></h3>
									<div class="qtyt-box">
										<div class="input-group spinner">
											<span class="txt">QTY</span>
											<input type="text" class="form-control" value="<?php echo $product['quantity']; ?>">
											<div class="input-group-btn-vertical">
												<button class="btn" type="button"><i class="fa fa-plus"></i></button>
												<button class="btn" type="button"><i class="fa fa-minus"></i></button>
											</div>

										</div>
									</div>
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

<!-- 	<div class="top-dropdown-ftr">
		<table class="cart-total">
			<tbody>
				<?php foreach ($totals as $total) { ?>
				<tr>
					<td><strong><?php echo $total['title']; ?></strong></td>
					<td><span class="blue"><?php echo $total['text']; ?></span></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<a href="<?php echo $cart; ?>" class="btn btn-info light cart-checkout"><img src="catalog/view/theme/ppusa2.0/images/icons/basket2.png" alt="">Checkout</a>

		

		<span>or</span>
		<a href="<?php echo $store_url . 'index.php?route=payment/paypal_express_new/SetExpressCheckout';?>" class="btn btn-green cart-paypal"><img src="catalog/view/theme/ppusa2.0/images/icons/paypal.png" alt=""> Paypal Express Chechout </a>
	</div> -->

	<?php } else { ?>
	<div class="body-height">
		<h3><?php echo $text_empty; ?></h3>
	</div>
	<?php } ?>
</div>