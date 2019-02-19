<?php echo $header; ?> 
 
 <style>
 <?php 
 if(count($categories)<5)
 {
	 ?>
 .category-list ul li {
 
    margin: auto auto 10px 50px !important;
 
}
<?php
}
else
{
	?>
	.category-list ul li {
 
    margin-bottom: 10px ;
	width:25%;
 
}
	<?php
}
?>
 </style>
 <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <a <?php echo(($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''); ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content">
<h1><center><?php echo $heading_title; ?></center></h1>
    <?php if ($thumb) { ?>
  <div class="category-info" style="text-align:center;margin-bottom:7px;background:none">
    <?php if ($thumb) { ?>
    <div class="image" style="float:none"><img src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>" /></div>
    <?php } ?>
    
  </div>
  <?php if ($description) { ?>
  <div class="category-info">
  
    <?php echo $description; ?>
    
  </div>
  <?php } ?>
  <?php } ?>
  <?php echo $content_top; ?>
  <?php if ($categories) { ?>
  <div class="category-list">
  
  <h2> NARROW YOUR SEARCH<?php //echo $text_refine; ?></h2>
    <?php if (count($categories) <= 5) { ?>
    <ul class="refine_categories" style="margin-bottom:10px;">
      <?php foreach ($categories as $category) { ?>
      <li class="bordered" style="margin-top:10px;"><a href="<?php echo $category['href']; ?>"><img src="<?php echo $category['thumb']; ?>" /><br/><span style="font-weight:bold;color:#3a3a3a"><?php echo $category['name']; ?></span></a></li>
      <?php } ?>
    </ul>
    <?php } else { ?>
    
    <?php //for ($i = 0; $i < count($categories);) { ?>
    <ul class="refine_categories" style="margin-bottom:10px;width:100%">
      <?php 
      //$j=4;
     // $j = $i + ceil(count($categories) / 4);
      ?>
      <?php //for (; $i < $j; $i++) {
      $i=0;
      foreach($categories as $category){
      
       ?>
      <?php if (isset($categories[$i])) { ?>
      <li class="bordered"><a href="<?php echo $categories[$i]['href']; ?>"><img src="<?php echo $categories[$i]['thumb']; ?>" /><br/><span style="font-weight:bold;color:#3a3a3a"><?php echo $categories[$i]['name']; ?></span></a></li>
      <?php } ?>
      <?php 
      $i++;
      /*if($i%4==0)
      {
      	echo '</ul> <ul class="refine_categories" style="margin-bottom:10px">';
      }*/
      } ?>
    </ul>
    <?php //} ?>
    <?php } ?>
  </div>
  <?php } ?>
  <?php if ($products) { ?>
  <div class="product-filter row">
    <div class="product-compare"><a href="<?php echo $compare; ?>" id="compare-total"><?php //echo $text_compare; ?></a></div>	
   <h2 style="color: #d0344a;
    font-family: 'HelveticaRegular';
    font-size: 14px;
    font-weight: 400;
    margin-bottom: 14px;
    ">VIEW ALL PRODUCTS</h2>
    <div class="form-choice-category">
	<div class="display"><b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display('grid');"><?php echo $text_grid; ?></a></div>
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
      <select onchange="location = this.value;">
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
  </div>
  
  <div class="product-list">
  
	<?php foreach ($products as $key => $product) { ?>
    <div class="one-product-box<?php echo ((($key+1) %4 ==0 ) ? ' last' : ''); ?>">
      <div class="image">
        <?php if ($product['thumb']) { ?>
        <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></a>
        <?php } ?>
      </div>
      <div class="min-height">
        <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
        <div class="description"><?php echo $product['description']; ?></div>
      </div>
      <?php if ($product['price']) { ?>
      <div class="price">
        <?php if (!$product['special']) { ?>
        <?php echo $product['price']; ?>
        <?php } else { ?>
        <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
        <?php } ?>
        <?php if ($product['tax']) { ?>
        <br />
        <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
        <?php } ?>
      </div>
      <?php } ?>
      <div class="cart">
        <span class="button_pink"><input type="button" value="<?php echo $button_cart; ?>" onclick="boss_addToCart('<?php echo $product['product_id']; ?>');" class="button" /></span>
      </div>
      <div class="compare"><a onclick="boss_addToCompare('<?php echo $product['product_id']; ?>');"><?php echo $button_compare; ?></a></div>
      <div class="wishlist"><a onclick="boss_addToWishList('<?php echo $product['product_id']; ?>');"><?php echo $button_wishlist; ?></a></div>
    </div>
    <?php } ?>
  </div>
  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } ?>
  <?php if (!$categories && !$products) { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button_pink"><span><?php echo $button_continue; ?></span></a></div>
  </div>
  <?php } ?>
  <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--
function display(view) {
	if (view == 'list') {
		$('.product-grid').attr('class', 'product-list');
		
		$('.product-list > div').each(function(index, element) {
			
			html = '<div class="left">';
			
			var image = $(element).find('.image').html();
			
			if (image != null) { 
				html += '<div class="image">' + image + '</div>';
			}
			html += '</div>';
			
			html  += '<div class="right">';
			html += '  <div class="name">' + $(element).find('.name').html() + '</div>';
			html += '  <div class="description">' + $(element).find('.description').html() + '</div>';

			var price = $(element).find('.price').html();
			
			if (price != null) {
				html += '<div class="price">' + price  + '</div>';
			}
			html += '  <div class="cart">' + $(element).find('.cart').html() + '</div>';
			html += '  <div class="compare">' + $(element).find('.compare').html() + '</div>';
			html += '  <div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
			html += '</div>';			
			
			$(element).html(html);
		});		
		
		$('.display').html('<b><?php echo $text_display; ?></b> <span class="active-list" title="<?php echo $text_list; ?>"><?php echo $text_list; ?></span><a title="<?php echo $text_grid; ?>" class="no-active-gird" onclick="display(\'grid\');"><?php echo $text_grid; ?></a>');
		
		$.cookie('display', 'list'); 
	} else {
		$('.product-list').attr('class', 'product-grid');
		
		$('.product-grid > div').each(function(index, element) {
			html = '';
			
			var image = $(element).find('.image').html();
			
			if (image != null) {
				html += '<div class="image">' + image + '</div>';
			}
			
      html += '<div class="min-height"><div class="name">' + $(element).find('.name').html() + '</div>';
      html += '<div class="description">' + $(element).find('.description').html() + '</div></div>';

			var price = $(element).find('.price').html();
			
			if (price != null) {
				html += '<div class="price">' + price  + '</div>';
			}
						
			html += '<div class="cart">' + $(element).find('.cart').html() + '</div>';
			html += '<div class="compare">' + $(element).find('.compare').html() + '</div>';
			html += '<div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
			
			$(element).html(html);
		});	
					
		$('.display').html('<b><?php echo $text_display; ?></b> <a title="<?php echo $text_list; ?>" class="no-active-list" onclick="display(\'list\');"><?php echo $text_list; ?></a><span class="active-gird" title="<?php echo $text_grid; ?>" ><?php echo $text_grid; ?></span>');
		
		$.cookie('display', 'grid');
	}
}

view = $.cookie('display');

display('grid');

//--></script> 
<script type="text/javascript"><!--
	$(document).ready(function() {
		category_resize();
	});
	$(window).resize(function() {
		category_resize();
	});
	function category_resize()
	{
		if(getWidthBrowser() < 767){
			display('grid');
		}
	}
//--></script> 

<?php echo $footer; ?>

