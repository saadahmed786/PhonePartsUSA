<?php echo $header; ?>
<?php $grid = 12; if($column_left != '') { $grid = $grid-3; } if($column_right != '') { $grid = $grid-3; } ?>

<div class="page-title">

	<div class="set-size">
	
		<div class="grid-12">
		
		  <div class="breadcrumb">
		    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
		    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		    <?php } ?>
		  </div>
		  <h3><?php echo $heading_title; ?></h3>
	  
		</div>
	
	</div>
	
	<p class="border"></p>

</div>

<div id="content" class="set-size">

	<?php echo $content_top; ?>

	  <?php if($column_left != '') { echo '<div class="grid-3 float-left">'.$column_left.'</div>'; } ?>
	  
	  <div class="grid-<?php echo $grid; ?> float-left">
	  
	  	<p class="clear" style="height:20px;"></p>
		

  <h4><?php echo $text_critea; ?></h4>
  <div class="content">
    <p><?php echo $entry_search; ?>
      <?php if ($filter_name) { ?>
      <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" />
      <?php } else { ?>
      <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" onclick="this.value = '';" onkeydown="this.style.color = '000000'" style="color: #999;" />
      <?php } ?>
      <select name="filter_category_id">
        <option value="0"><?php echo $text_category; ?></option>
        <?php foreach ($categories as $category_1) { ?>
        <?php if ($category_1['category_id'] == $filter_category_id) { ?>
        <option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
        <?php } ?>
        <?php foreach ($category_1['children'] as $category_2) { ?>
        <?php if ($category_2['category_id'] == $filter_category_id) { ?>
        <option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
        <?php } ?>
        <?php foreach ($category_2['children'] as $category_3) { ?>
        <?php if ($category_3['category_id'] == $filter_category_id) { ?>
        <option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
        <?php } ?>
        <?php } ?>
        <?php } ?>
        <?php } ?>
      </select>
      <?php if ($filter_sub_category) { ?>
      <input type="checkbox" name="filter_sub_category" value="1" id="sub_category" checked="checked" />
      <?php } else { ?>
      <input type="checkbox" name="filter_sub_category" value="1" id="sub_category" />
      <?php } ?>
      <label for="sub_category"><?php echo $text_sub_category; ?></label>
    </p>
    <?php if ($filter_description) { ?>
    <input type="checkbox" name="filter_description" value="1" id="description" checked="checked" />
    <?php } else { ?>
    <input type="checkbox" name="filter_description" value="1" id="description" />
    <?php } ?>
    <label for="description"><?php echo $entry_description; ?></label>
  </div>
  <div class="buttons">
    <div class="right"><input type="button" value="<?php echo $button_search; ?>" id="button-search" class="button" /></div>
  </div>
  <h2 style="margin-bottom:0px"><?php echo $text_search; ?></h2>
  <?php if ($products) { ?>
  <div class="product-filter">
	 <div class="display"><div class="<?php if($this->config->get('default_list_grid') == '1' && $this->config->get('general_status') == '1') { echo 'active-'; } ?>display-grid"><a onclick="display('grid');"><?php echo $text_grid; ?></a></div><div class="<?php if(!($this->config->get('default_list_grid') == '1' && $this->config->get('general_status') == '1')) { echo 'active-'; } ?>display-list"><a onclick="display('list');"><?php echo $text_list; ?></a></div></div>
  	<div class="product-compare"><a href="<?php echo $compare; ?>" id="compare-total"><?php echo $text_compare; ?></a></div>
    <div class="limit"><b><?php echo $text_limit; ?></b>
      <select onchange="location = this.value;">
        <?php foreach ($limits as $limits) { ?>
        <?php if ($limits['value'] == $limit) { ?>
        <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
    <div class="sort"><b><?php echo $text_sort; ?></b>
      <select onchange="location = this.value;" style="max-width:100px">
        <?php foreach ($sorts as $sorts) { ?>
        <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
        <option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
  </div>
  <div class="product-list"<?php if($this->config->get('default_list_grid') == '1' && $this->config->get('general_status') == '1') { echo ' style="display:none;"'; } ?>>
    <?php foreach ($products as $product) { ?>

						<!-- Product -->
						
						<div>
							
							<div class="left">
								
								<?php if ($product['thumb']) { ?>
								<div class="image">
								<div class="banner-square">
								<a href="<?php echo $product['href']; ?>">
								<?php echo $product['promo_tag_top_right']; ?>
								<?php echo $product['promo_tag_top_left']; ?>
								<?php echo $product['promo_tag_bottom_left']; ?>
								<?php echo $product['promo_tag_bottom_right']; ?>
								<img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></div></a></div>
								<?php } ?>

								<div class="name">
								
							  		<a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
									
									<p><?php echo $product['description']; ?>      <?php if ($product['rating']) { ?>
      <div class="rating" style="padding-top:4px"><img src="catalog/view/theme/megastore/images/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
      <?php } ?></p>
		
								</div>
							
							</div>
							
							<div class="right">

								<?php if ($product['price']) { ?>
									<?php if (!$product['special']) { ?>
						        <div class="price"><?php echo $product['price']; ?></div>
						        <?php } else { ?>
						        <div class="price"><span class="price-old"><?php echo $product['price']; ?></span><span class="price-new"><?php echo $product['special']; ?></span></div>
						        <?php } ?>
								<?php } ?>
							
								<div class="cart"><a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button"><span><?php echo $button_cart; ?></span></a></div>
								
								<div class="wish-list"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');"><?php echo $button_wishlist; ?></a><br /><a onclick="addToCompare('<?php echo $product['product_id']; ?>');"><?php echo $button_compare; ?></a></div>
								
							</div>
							
							<p class="clear"></p>

						</div>
						
						<!-- End Product -->
	 
    <?php } ?>
  </div>
	
  <div class="box-product product-grid<?php if($this->config->get('product_per_pow') == '1' && $this->config->get('general_status') == '1') { echo ' version-two'; } ?>"<?php if(!($this->config->get('default_list_grid') == '1' && $this->config->get('general_status') == '1')) { echo ' style="display:none;"'; } ?>>
    <?php foreach ($products as $product) { ?>

					<!-- Product -->
						
						<div>
							
							<!-- Hover PRODUCT -->
							
							<div class="absolute-hover-product">
								
								<?php if ($product['thumb']) { ?>
								<div class="image">
								<div class="banner-square">
								<a href="<?php echo $product['href']; ?>">
								<?php echo $product['promo_tag_top_right']; ?>
								<?php echo $product['promo_tag_top_left']; ?>
								<?php echo $product['promo_tag_bottom_left']; ?>
								<?php echo $product['promo_tag_bottom_right']; ?>
								<img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></div></a></div>
								<?php } ?>
								
								<div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
								
					        <?php if ($product['price']) { ?>
					        <div class="price">
					          <?php if (!$product['special']) { ?>
					          <?php echo $product['price']; ?>
					          <?php } else { ?>
					          <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
					          <?php } ?>
					        </div>
					        <?php } ?>
					        
					        <?php if ($product['rating']) { ?>
					        <div class="ratings"><img src="catalog/view/theme/megastore/images/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
					        <?php } ?>
																										
								<div class="cart"><a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button"><span><?php echo $button_cart; ?></span></a></div>
								
								<div class="wish-list"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');"><?php echo $button_wishlist; ?></a><br /><a onclick="addToCompare('<?php echo $product['product_id']; ?>');"><?php echo $button_compare; ?></a></div>
							
							</div>
							
							<!-- End Hover PRODUCT -->
							
							<div class="left">
								
								<?php if ($product['thumb']) { ?>
								<div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
								<?php } ?>
							
							</div>
													
							<div class="right">

								<div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>

					        <?php if ($product['price']) { ?>
					        <div class="price">
					          <?php if (!$product['special']) { ?>
					          <?php echo $product['price']; ?>
					          <?php } else { ?>
					          <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
					          <?php } ?>
					        </div>
					        <?php } ?>
								
					        <?php if ($product['rating']) { ?>
					        <div class="ratings"><img src="catalog/view/theme/megastore/images/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
					        <?php } ?>
								
							</div>

						</div>
						
						<!-- End Product -->

						
    <?php } ?>	
  </div>	
  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } else { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <?php }?>
<script type="text/javascript"><!--
function display(view) {
	if (view == 'list') {
		$('.product-grid').css("display", "none");
		$('.product-list').css("display", "block");

		$('.display').html('<div class="display-grid"><a onclick="display(\'grid\');"><?php echo $text_grid; ?></a></div><div class="active-display-list"><?php echo $text_list; ?></div>');
		
		$.cookie('display', 'list'); 
	} else {
	
		$('.product-grid').css("display", "block");
		$('.product-list').css("display", "none");
					
		$('.display').html('<div class="active-display-grid"><?php echo $text_grid; ?></div><div class="display-list"><a onclick="display(\'list\');"><?php echo $text_list; ?></a></div>');
		
		$.cookie('display', 'grid');
	}
}

view = $.cookie('display');

if (view) {
	display(view);
} else {
	display('list');
}
//--></script> 
 	  
	  </div>
	  
	  <?php if($column_right != '') { echo '<div class="grid-3 float-left">'.$column_right.'</div>'; } ?>

	<?php echo $content_bottom; ?>

</div>

<?php echo $footer; ?>

