<?php echo $header; ?>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content">
	<?php echo $content_top; ?>
	<div class="breadcrumb">
	  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
	  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
	  <?php } ?>
	</div>
	<h1>
		<?php echo $heading_title; ?>
		<?php if ($weight) { ?>
		&nbsp;(<?php echo $weight; ?>)
		<?php } ?>
	</h1>
	<?php if ($attention) { ?>
	<div class="attention"><?php echo $attention; ?></div>
	<?php } ?>    
	<?php if ($success) { ?>
	<div class="success"><?php echo $success; ?></div>
	<?php } ?>
	<?php if ($error_warning) { ?>
	<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="basket">
		<div class="cart-info">				
			<ul>
				<?php foreach ($products as $product) { ?>
				<li>
					<div class="image"><?php if ($product['thumb']) { ?>
						<a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a>
						<?php } ?>
					</div>
					<div class="name">
						<a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
						<?php if (!$product['stock']) { ?>
						<span class="stock">***</span>
						<?php } ?>
						<div>
						<?php foreach ($product['option'] as $option) { ?>
						- <small><?php echo $option['name']; ?>: <?php echo $option['value']; ?></small><br />
						<?php } ?>
						</div>
						<?php if (isset($product['reward'])) { ?>
						<small><?php echo $product['reward']; ?></small>
						<?php } ?>
					</div>
					<div class="model"><?php echo $product['model']; ?></div>
					<label for="quantity"><?php echo $column_quantity; ?></label>
					<?php if ($direction === "ltr") { ?>
					<span class="quantity"><input type="number" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" size="3" />X</span>
					<span class="price"><?php echo $product['price']; ?></span>
					<span class="total">= <?php echo $product['total']; ?></span>
					<?php } else if ($direction === "rtl") { ?>
					<span class="quantity"><input type="number" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" size="3" /></span>
					<span class="price">&#x200E;<?php echo $product['price']; ?> X</span>
					<span class="total">&#x200E;<?php echo $product['total']; ?> =&#x200E;</span>
					<?php } ?>
	<?php if (defined('VERSION') && (version_compare(VERSION, '1.5.2', '<') == true)) { ?>
					<div class="remove"><input type="checkbox" name="remove[]" value="<?php echo $product['key']; ?>" /> <?php echo $column_remove; ?></div>
	<?php } else { ?>
					<div class="remove"><a href="<?php echo $product['remove']; ?>"><?php echo $button_remove; ?></a></div>
	<?php } ?>

				</li>
				<?php } ?>
				<?php foreach ($vouchers as $voucher) { ?>
				<li>
					<div class="image"></div>
					<div class="name"><?php echo $voucher['description']; ?></div>
					<div class="model"></div>
					<div class="quantity">1</div>
					<div class="price"><?php echo $voucher['amount']; ?></div>
					<div class="total"><?php echo $voucher['amount']; ?></div>
	<?php if (defined('VERSION') && (version_compare(VERSION, '1.5.2', '<') == true)) { ?>
					<div class="remove"><input type="checkbox" name="voucher[]" value="<?php echo $voucher['key']; ?>" /></div>
	<?php } else { ?>
					<div class="remove"><input type="checkbox" name="voucher[]" value="<?php echo $voucher['key']; ?>" /> <?php echo $text_remove; ?></div>
	<?php } ?>
				</li>
				<?php } ?>
			</ul>				
		</div>
		<input type="submit" value="<?php echo $button_update; ?>" />
	</form>

	<div class="cart-module">
	<?php if (defined('VERSION') && (version_compare(VERSION, '1.5.2', '<') == true)) { ?>
	<?php foreach ($modules as $module) { ?>
		<?php echo $module; ?>
	<?php } ?>
	<?php } else { ?>
		<?php if ($coupon_status) { ?>
		<div id="coupon" class="m-cart">
			<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="inline-form">
				<label for="coupon"><?php echo $entry_coupon; ?></label>
				<input type="text" name="coupon" value="<?php echo $coupon; ?>" />
				<input type="hidden" name="next" value="coupon" />				
				<input type="submit" value="<?php echo $button_apply; ?>"/>
			</form>
		</div>
		<?php } ?>
		<?php if ($voucher_status) { ?>
		<div id="voucher" class="m-cart">
			<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="inline-form">
				<label for="voucher"><?php echo $entry_voucher; ?></label>
				<input type="text" name="voucher" value="<?php echo $voucher; ?>" />
				<input type="hidden" name="next" value="voucher" />
				<input type="submit" value="<?php echo $button_apply; ?>"/>
			</form>
		</div>
		<?php } ?>
		<?php if ($reward_status) { ?>
		<div id="reward" class="m-cart">
			<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="inline-form">
				<label for="reward"><?php echo $entry_reward; ?></label>
				<input type="text" name="reward" value="<?php echo $reward; ?>" />
				<input type="hidden" name="next" value="reward" />
				<input type="submit" value="<?php echo $button_apply; ?>"/>
			</form>
		</div>
		<?php } ?>
	<?php } ?>
	</div>	

	<ul class="cart-total">
		<?php foreach ($totals as $total) { ?>				
		<li>
			<?php echo $total['title']; ?>: <strong><?php echo $total['text']; ?></strong>
		</li>
		<?php } ?>
	</ul>	
		
	<div class="buttons">      
		<a href="<?php echo $continue; ?>" class="button"><?php echo $button_shopping; ?></a>
		<a href="<?php echo $checkout; ?>" class="button"><?php echo $button_checkout; ?></a>		
	</div>
	<?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>