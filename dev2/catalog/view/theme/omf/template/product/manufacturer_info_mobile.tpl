<?php echo $header; ?>
<div id="main" role="main">
	<?php echo $content_top; ?>
	
	<ul id="breadcrumbs">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li><?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
	<?php } ?>
	</ul>
	
	<h1><?php echo $heading_title; ?></h1>
	<?php if ($products) { ?>
	<ul class="product-list">
		<?php foreach ($products as $product) { ?>
		<li>
			<?php if ($product['thumb']) { ?>
			<a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></a>
			<?php } ?>
			<a href="<?php echo $product['href']; ?>" class="name"><?php echo $product['name']; ?></a>
			<p class="description"><?php echo $product['description']; ?><a href="<?php echo $product['href']; ?>#tab-description">more&raquo;</a></p>
			<?php if ($product['price']) { ?>
			<div class="price">
				<?php if (!$product['special']) { ?>
				<?php echo $product['price']; ?>
				<?php } else { ?>
				<del class="price-old"><?php echo $product['price']; ?></del> <span class="price-new"><?php echo $product['special']; ?></span>
				<?php } ?>
				<?php if ($product['tax']) { ?>
				<br />
				<span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
				<?php } ?>
			</div>
			<?php } ?>
			<?php if ($product['rating']) { ?>
			<img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" class="rating">
			<?php } ?>			
			<form action="index.php?route=checkout/cart/update" method="post">
				<input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>" />
				<input type="submit" value="<?php echo $button_cart; ?>" class="add-to-cart-button"/>				
			</form>			
			<?php /*<form action="index.php?route=account/wishlist/update" method="post">
				<input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>" />
				<input type="submit" value="<?php echo $button_wishlist; ?>" class="wishlist-button"/>
			</form> */ //Coming in next version. If you really need it request it and I'll realize it ASAP.?>			
		</li>
		<?php } ?>
	</ul>
	<div class="pagination"><?php echo $pagination; ?></div>
	<?php } else { ?>	
	<p><?php echo $text_empty; ?></p>
	<a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a>
	<?php } ?>
	<?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>