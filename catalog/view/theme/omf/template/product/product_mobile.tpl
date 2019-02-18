<?php echo $header; ?>
<div id="main" role="main">
	<?php echo $content_top; ?>
	<ul id="breadcrumbs">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li><?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
	<?php } ?>
	</ul>
	<h1><?php echo $heading_title; ?></h1>
	<div class="product-info">
		<?php if ($thumb || $images) { ?>
		<section id="images">
			<?php if ($thumb) { ?>
			<a href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>"><img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
			<?php } ?>			
			<?php if ($images) { ?>
			<div class="image-additional">				
				<?php foreach ($images as $image) { ?>
				<a href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>" rel="additional images"><img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
				<?php } ?>			
			</div>
			<?php } ?>			
		</section>
		<?php } ?>
		
		<table class="description">				
			<?php if ($manufacturer) { ?>
			<tr><th><?php echo $text_manufacturer; ?></th><td><a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a></td></tr>
			<?php } ?>
			<tr><th><?php echo $text_model; ?></th><td><?php echo $model; ?></td></tr>
			<?php if ($reward) { ?>
			<tr><th><?php echo $text_reward; ?></th><td><?php echo $reward; ?></td></tr>
			<?php } ?>
			<tr><th><?php echo $text_stock; ?></th><td><?php echo $stock; ?></td></tr>				
		</table>
		<?php if ($price) { ?>
		<div class="price">
			<?php echo $text_price; ?>
			<?php if (!$special) { ?>
			<?php echo $price; ?>
			<?php } else { ?>
			<del class="price-old"><?php echo $price; ?></del> <strong class="price-new"><?php echo $special; ?></strong>
			<?php } ?>				
			<?php if ($tax) { ?>
			<span class="price-tax"><?php echo $text_tax; ?> <?php echo $tax; ?></span>
			<?php } ?>
			<?php if ($points) { ?>
			<p class="reward"><small><?php echo $text_points; ?> <?php echo $points; ?></small></p> 
			<?php } 
			?>
			<?php if ($discounts) { ?>			
			<table class="discount">
				<?php foreach ($discounts as $discount) { ?>
				<tr>
					<th><?php echo sprintf($text_discount, $discount['quantity'], '');?></th>
					<td><?php echo $discount['price'];?></td>					
				</tr>
				<?php } ?>
			</table>
			<?php } 
			?>
		</div>
		<?php } ?>
		<form action="index.php?route=checkout/cart/add" method="post">
			<?php if ($options) { ?>
			<section id="options">
				<h2><?php echo $text_option; ?></h2>					
				<?php foreach ($options as $option) { ?>
				<?php if ($option['type'] == 'select') { ?>
				<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
					<?php if ($option['required']) { ?>
					<span class="required">*</span>
					<?php } ?>
					<b><?php echo $option['name']; ?>:</b>
					<select name="option[<?php echo $option['product_option_id']; ?>]">
						<option value=""><?php echo $text_select; ?></option>
						<?php foreach ($option['option_value'] as $option_value) { ?>
						<option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
						<?php if ($option_value['price']) { ?>
						(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
						<?php } ?>
						</option>
						<?php } ?>
					</select>
				</div>				
				<?php } 
				?>					
				<?php if ($option['type'] == 'radio') { ?>
				<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
					<?php if ($option['required']) { ?>
					<span class="required">*</span>
					<?php } ?>
					<b><?php echo $option['name']; ?>:</b>
					<?php foreach ($option['option_value'] as $option_value) { ?>					
					<label for="option-value-<?php echo $option_value['product_option_value_id']; ?>">
					<input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
					<?php echo $option_value['name']; ?>
					<?php if ($option_value['price']) { ?>
					(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
					<?php } ?>
					</label>						
					<?php } ?>
				</div>					
				<?php } 
				?>					
				<?php if ($option['type'] == 'checkbox') { ?>
				<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
					<?php if ($option['required']) { ?>
					<span class="required">*</span>
					<?php } ?>
					<b><?php echo $option['name']; ?>:</b>
					<?php foreach ($option['option_value'] as $option_value) { ?>					
					<label for="option-value-<?php echo $option_value['product_option_value_id']; ?>">
					<input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
					<?php echo $option_value['name']; ?>
					<?php if ($option_value['price']) { ?>
					(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
					<?php } ?>
					</label>					
					<?php } ?>
				</div>					
				<?php } 
				?>					
				<?php if ($option['type'] == 'image') { ?>
				<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
					<?php if ($option['required']) { ?>
					<span class="required">*</span>
					<?php } ?>
					<b><?php echo $option['name']; ?>:</b>
					<table class="option-image">
						<?php foreach ($option['option_value'] as $option_value) { ?>
						<tr>
							<td style="width: 1px;">
								<input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
							</td>
							<td>
								<label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" /></label>
							</td>
							<td>
								<label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
								<?php if ($option_value['price']) { ?>
								(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
								<?php } ?>
								</label>
							</td>
						</tr>
						<?php } ?>
					</table>
				</div>					
				<?php } 
				?>					
				<?php if ($option['type'] == 'text') { ?>
				<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
					<?php if ($option['required']) { ?>
					<span class="required">*</span>
					<?php } ?>
					<b><?php echo $option['name']; ?>:</b>
					<input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" />
				</div>					
				<?php } 					
				?>					
				<?php if ($option['type'] == 'textarea') { ?>
				<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
					<?php if ($option['required']) { ?>
					<span class="required">*</span>
					<?php } ?>
					<b><?php echo $option['name']; ?>:</b>
					<textarea name="option[<?php echo $option['product_option_id']; ?>]" cols="40" rows="5"><?php echo $option['option_value']; ?></textarea>
				</div>					
				<?php } 
				?>					
				<?php if ($option['type'] == 'file') { ?>
				<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
					<?php if ($option['required']) { ?>
					<span class="required">*</span>
					<?php } ?>
					<b><?php echo $option['name']; ?>:</b>
					<a id="button-option-<?php echo $option['product_option_id']; ?>" class="button"><?php echo $button_upload; ?></a>
					<!-- <input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" /> -->
					<input type="file" name="option[<?php echo $option['product_option_id']; ?>]" /> <?php echo $button_upload; ?>
				</div>					
				<?php } 
				?>
				<?php if ($option['type'] == 'date') { ?>
				<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
					<?php if ($option['required']) { ?>
					<span class="required">*</span>
					<?php } ?>
					<b><?php echo $option['name']; ?>:</b>
					<input type="date" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="date" />
				</div>					
				<?php } 
				?>
				<?php if ($option['type'] == 'datetime') { ?>
				<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
					<?php if ($option['required']) { ?>
					<span class="required">*</span>
					<?php } ?>
					<b><?php echo $option['name']; ?>:</b>
					<input type="datetime" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="datetime" />
					</div>						
				<?php } 
				?>
				<?php if ($option['type'] == 'time') { ?>
				<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
					<?php if ($option['required']) { ?>
					<span class="required">*</span>
					<?php } ?>
					<b><?php echo $option['name']; ?>:</b>
					<input type="time" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="time" />
				</div>					
				<?php } ?>
				<?php if(isset($errors[$option['product_option_id']])) echo '<span class="s-error">'. $errors[$option['product_option_id']] .'</span>';?>
				<?php } ?>
			</section>
			<?php } 
			?>
			<div class="cart">					
				<?php echo $text_qty; ?>
				<input type="number" name="quantity" size="2" value="<?php echo $minimum; ?>"  min="<?php echo $minimum; ?>" />
				<input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />					
			</div>
			<?php if ($minimum > 1) { ?>
			<div class="minimum"><?php echo $text_minimum; ?></div>
			<?php } ?>				
			<input type="submit" value="<?php echo $button_cart; ?>"/>
		</form>		
		<?php /*<form action="index.php?route=account/wishlist/update" method="post">		
			<input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
			<input type="submit" value="<?php echo $button_wishlist; ?>" />
		</form>*/ //Coming soon ?>
	
		<?php /* if ($review_status) { ?>
		<div class="review">
			<img src="catalog/view/theme/default/image/stars-<?php echo $rating; ?>.png" alt="<?php echo $reviews; ?>" />&nbsp;&nbsp;<a onclick="$('a[href=\'#tab-review\']').trigger('click');"><?php echo $reviews; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('a[href=\'#tab-review\']').trigger('click');"><?php echo $text_write; ?></a>
		</div>					
		<?php } */?>
	</div>	
	<?php /*<div id="tabs" class="htabs">
		<a href="#description"><?php echo $tab_description; ?></a>
		<?php if ($attribute_groups) { ?>
		<a href="#tattribute"><?php echo $tab_attribute; ?></a>
		<?php } ?>
		<?php if ($review_status) { ?>
		<a href="#review"><?php echo $tab_review; ?></a>
		<?php } ?>
		<?php if ($products) { ?>
		<a href="#related"><?php echo $tab_related; ?> (<?php echo count($products); ?>)</a>
		<?php } ?>
	</div>*/ // Insert these through JS on capable devices so that they work as tabs. Not needed on a mobile. ?>
	<section id="description" class="tab-content">
		<h2><?php echo $tab_description; ?></h2>		
		<?php echo $description; ?>
	</section>
	<?php if ($attribute_groups) { ?>
	<section id="attribute" class="tab-content">
		<h2><?php echo $tab_attribute; ?></h2>
		<table class="attribute">
			<?php foreach ($attribute_groups as $attribute_group) { ?>
			<thead>
				<tr>
				<td colspan="2"><?php echo $attribute_group['name']; ?></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($attribute_group['attribute'] as $attribute) { ?>
				<tr>
					<td><?php echo $attribute['name']; ?></td>
					<td><?php echo $attribute['text']; ?></td>
				</tr>
				<?php } ?>
			</tbody>
			<?php } ?>
		</table>
	</section>
	<?php } 
	?>
	<?php //make reviews on a second page and put a link here. So far no reviews. ?>
	<?php /* if ($products) { ?>
	<section id="related" class="tab-content">
		<h2><?php echo $tab_related; ?> (<?php echo count($products); ?>)</a></h2>
		<ul class="box-product">
			<?php foreach ($products as $product) { ?>
			<li>
				<?php if ($product['thumb']) { ?>
				<a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></a> 
				<?php } ?>
				<a href="<?php echo $product['href']; ?>" class="name"><?php echo $product['name']; ?></a>
				<?php if ($product['price']) { ?>
				<div class="price">
					<?php if (!$product['special']) { ?>
					<?php echo $product['price']; ?>
					<?php } else { ?>
					<del class="price-old"><?php echo $product['price']; ?></del> <strong class="price-new"><?php echo $product['special']; ?></strong>
					<?php } ?>
				</div>
				<?php } ?>
				<?php if ($product['rating']) { ?>
				<img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" class="rating">
				<?php } ?>		
				<form action="index.php?route=checkout/cart/add" method="post">
					<input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>" />
					<input type="submit" value="<?php echo $button_cart; ?>" class="add-to-cart-button"/>				
				</form>									
			</li>
			<?php } ?>
		</ul>
	</section>
	<?php } */ //Simplify. You don't need related products. They are distracting. ?>
	<?php if ($tags) { ?>
	<div class="tags">
		<b><?php echo $text_tags; ?></b>
		<?php foreach ($tags as $tag) { ?>
		<a href="<?php echo $tag['href']; ?>"><?php echo $tag['tag']; ?></a>,
		<?php } ?>
	</div>
	<?php } ?>
	<?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>