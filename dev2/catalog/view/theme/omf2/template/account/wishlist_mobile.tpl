<?php echo $header; ?>
<?php /* echo $column_left; ?><?php echo $column_right; */ ?>
<div id="content">
	<?php echo $content_top; ?>
	<div class="breadcrumb">
	  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
	  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
	  <?php } ?>
	</div>
	<h1>
		<?php echo $heading_title; ?>

	</h1>
	<?php if ($products) { ?>
		<div class="wishlist-product">
			<ul>
				<?php foreach ($products as $product) { ?>
				<li id='product-container-<?php echo $product['product_id']; ?>'>
					<div class="image"><?php if ($product['thumb']) { ?>
						<a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a>
						<?php } ?>
					</div>
					<div class="name">
						<br />
						<br />
						<a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
						<div>
						<?php echo $product['model']; ?>
						</div>
						<div>
							<?php echo $product['stock']; ?>
						</div>
						<?php if ($product['price']) { ?>
						<div class="price">
						  <?php if (!$product['special']) { ?>
						  <?php echo $product['price']; ?>
						  <?php } else { ?>
						  <s><?php echo $product['price']; ?></s> <b><?php echo $product['special']; ?></b>
						  <?php } ?>
						</div>
						<?php } ?>

						<a id="button-add-to-cart" class="button" onclick="addToCart('<?php echo $product['product_id']; ?>');"><?php echo $button_cart; ?></a>
						<a id="button-remove" class="button" href="<?php echo $product['remove']; ?>"><?php echo $button_remove; ?></a>

					</div>
				</li>
				<?php } ?>

			</ul>
		</div>

	<div class="buttons">
	<div class="right"><a href="<?php echo $continue; ?>" class="button"><span><?php echo $button_continue; ?></span></a></div>
	</div>
	<?php } else { ?>
	<div class="content"><?php echo $text_empty; ?></div>
	<div class="buttons">
	<div class="right"><a href="<?php echo $continue; ?>" class="button"><span><?php echo $button_continue; ?></span></a></div>
	</div>
	<?php } ?>
	<?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>