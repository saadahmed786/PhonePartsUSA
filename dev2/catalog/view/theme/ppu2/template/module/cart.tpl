<?php //echo '<pre>'; print_r($data); exit;?>
<div class="cart_btn"><i><img src="catalog/view/theme/ppu2/image/cart_icon.png" alt="cart" /></i>Cart <span><?php echo $total_items; ?></span></div>
<script>
	$(document).ready(function(){
		$(".cart_item_box").hide();
		$('.cart_btn').on('click', function() {
			$(".cart_item_box").slideToggle();
		});
	});
</script>
<div class="cart_item_box">
	<img class="cart_arrow_img" src="catalog/view/theme/ppu2/image/cart_arrow_img.png" alt="img" />
	<!--<img class="close_cart" src="catalog/view/theme/ppu2/image/close_cart.png" />-->
	<?php if ($products || $vouchers) { ?>
	<?php foreach ($products as $product) { ?>
	<?php
            //Putting Signature in the End
	if ($product['model'] == "SIGN") {
		$sign = '<div class="cart_item_wraper">';
		$sign .= '<div class="col-lg-3"></div>';
		$sign .= '<div class="col-lg-8">';
		$sign .= '<h3><a href="' . $product['href'] . '">' . substr($product['name'], 0, 30) . '...</a></h3>';
		$sign .= '<p><span></span>' . $product['price'] . '</p>';
		$sign .= '</div>';
		$sign .= '<div class="col-lg-1"></div>';
		$sign .= '<div class="clearfix"></div>';
		$sign .= '</div>';
		continue;
	}
	?>
	<div class="cart_item_wraper">
		<div class="col-lg-3">
			<?php if ($product['thumb']) { ?>

			<a href="<?php echo $product['href']; ?>">
				<img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name'];?>" title="<?php echo $product['name']; ?>" />
			</a>

			<?php } ?>
		</div>
		<div class="col-lg-8">
			<h3><a href="<?php echo $product['href']; ?>"><?php echo substr($product['name'], 0, 30); ?>...</a></h3>

			<?php foreach ($product['option'] as $option) { ?>
			<p><span><?php echo $option['name']; ?></span><?php echo $option['value']; ?></p>
			<?php } ?>


			<p><span><?php echo $product['quantity']; ?></span><?php echo $product['price']; ?></p>

		</div>
		<div class="col-lg-1">
			<a <?= ($this->request->get['route'] == 'checkout/cart' || $this->request->get['route'] == 'checkout/checkout')? 'onclick="location = \'index.php?route=checkout/cart&remove='. $product['key'] .'\'': 'onclick="$(\'#cart\').load(\'index.php?route=module/cart&remove='. $product['key'] .'\')"';?> class="remove_cart_item"></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php } ?>

	<?php echo $sign; ?>

	<?php foreach ($vouchers as $voucher) { ?>

	<div class="cart_item_wraper">
		<div class="col-lg-3">
		</div>
		<div class="col-lg-8">
			<h3><?php echo $voucher['description']; ?></h3>
			<p><span>1</span><?php echo $voucher['amount']; ?></p>

		</div>
		<div class="col-lg-1">
			<a onclick="(getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') ? location = 'index.php?route=checkout/cart&amp;remove=<?php echo $voucher['key']; ?>' : $('#cart').load('index.php?route=module/cart&amp;remove=<?php echo $voucher['key']; ?>' + ' #cart > *');');" class="remove_cart_item"></a>
		</div>
		<div class="clearfix"></div>
	</div>

	<?php } ?>

	<div class="cart_total_box">
		<p><?php echo $heading_title; ?> - <?php echo $text_items;?></p>
		<?php foreach ($totals as $total) { ?>
		<p class="cart_total_p"><strong><?php echo $total['title']; ?></strong> <?php echo $total['text']; ?></p>
		<?php } ?>
	</div>

	<div class="cart_btn_div">
		<a href="<?php echo $cart; ?>"><button class="checkout_now_btn"><?php echo $text_cart; ?></button></a>
		<a href="<?php echo $checkout; ?>"><button class="shoping_cart_btn"><?php echo $text_checkout; ?></button></a>
		<div class="clearfix"></div>
	</div>

	<?php } else { ?>
	<div class="cart_item_wraper">
		<p><?php echo $text_empty; ?></p>
	</div>
	<?php } ?>
</div>