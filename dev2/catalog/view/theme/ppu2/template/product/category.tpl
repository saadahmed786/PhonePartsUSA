<?php //echo '<pre>'; print_r($data); exit; ?>

<?php echo $header; ?> 

<div class="container-fluid short_link_path">
	<div class="container">
		<p class="short_link_text">
			<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<a <?= ($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''; ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
			<?= ($breadcrumb != end($breadcrumbs)) ? '<i class="fa fa-caret-right"></i>' : ''; ?>

			<?php } ?>
		</p>
	</div>
</div>
<div class="container-fluid product_page_wrapper">
	<?php echo $column_left; ?>
	<?php echo $content_top; ?>
	<div class="col-lg-10 product_page_right_wrapper">
		<ul class="nav nav-tabs custom_tab_style">
			<li class="active"><a data-toggle="tab" href="#allProducts">ALL PRODUCTS <span>(<?= count($products); ?>)</span></a></li>
			<!-- <li><a data-toggle="tab" href="#populerProducts">MOST POPULER <span>(52,00)</span></a></li>
			<li><a data-toggle="tab" href="#unanswered">MARKETEPLACE SELLER ITEMS <span>(52,00)</span></a></li> -->
		</ul>
		<div class="tab-content">
			<div class="sorting_top_panel">
				<label><?php echo $text_sort; ?></label>
				<span class="match_box sort">
					<select onchange="location = this.value;">
						<?php foreach ($sorts as $sorts) { ?>
						<?php if ($sorts['value'] == $sort . '-' . $order) { ?>
						<option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
						<?php } ?>
						<?php } ?>
					</select>
				</span>
				<label><?php echo $text_limit; ?></label>
				<span class="item_sel_box limit">
					<select onchange="location = this.value;">
						<?php foreach ($limits as $limits) { ?>
						<?php if ($limits['value'] == $limit) { ?>
						<option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
						<?php } ?>
						<?php } ?>
					</select>
				</span>
				<div class="product_view_btn">
					<span class="grid_view" onclick="display('grid');"><i class="fa fa-th"></i></span>
					<span class="list_view" onclick="display('list');"><i class="fa fa-list"></i></span>
					<div class="clearfic"></div>
				</div>
			</div>
			<div id="popular" class="tab-pane fade in active">
				<?php if ($products) { ?>
				<div class="grid_view_pdt_wraper">
					<?php //echo '<pre>'; print_r($products); exit;?>
					<ul>
						<?php foreach ($products as $i => $product) { ?>
						<?php if (!($i % 4) && $i != 0) { ?>
						</ul><ul>
						<?php }?>
						<li>
							<div class="grid_view_img_div">
								<img src="<?= $product['thumb']; ?>" alt="<?= $product['name']; ?>" />
							</div>
							<div class="grid_view_content_div">
								<h3><?= $product['name']; ?></h3>
								<h4>SKU: </span><?= $product['model']; ?></h4>
								<span style="color: #ffcd0e;">
									<?php for ($x=0; $x < 5 ; $x++) { ?>
									<i class="fa <?= ($x < $product['rating'])? 'fa-star': 'fa-star-o'; ?>"></i>
									<?php } ?>
								</span>
								<p><?= $product['rating']; ?>  (<?= $product['reviews']; ?>)</p>
							</div>
							<div class="grid_view_cart_div">
								<h3><?= $product['price']; ?></h3>
								<!-- <h4>With 2-year contract</h4> -->
								<!-- <P><span>SAVE $100</span>(Reg. $199.99)</P> -->
								<button onclick="<?php if($product['quantity']>0) { ?>boss_addToCart('<?php echo $product['product_id'];?>', '1'); <?php } ?>"><span><i class="fa fa-shopping-cart"></i></span><?= ($product['quantity'] > 0 )? 'Add to Package': 'Out of Stock'; ?></button>
							</div>
						</li>
						<?php } ?>
					</ul>
					<div class="clearfix"></div>
				</div>

				<div class="custome_pagination" style="padding:15px 0; border:1px solid #d5d5d5;">
				<?php echo $pagination; ?>
					<!-- <ul style="width:37%;">
						<li class="prev_pag_btn"><i class="fa fa-angle-left"></i></li>
						<li>1</li>
						<li>2</li>
						<li>3</li>
						<li>4</li>
						<li>5</li>
						<li>6</li>
						<li>7</li>
						<li>8</li>
						<li>9</li>
						<li>10</li>
						<li class="next_pag_btn"><i class="fa fa-angle-right"></i></li>
					</ul> -->
					<div class="clearfix"></div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php echo $content_bottom; ?>
	<?php echo $column_right; ?>
</div>
<div class="clearfix"></div>
<?php echo $footer; ?>

