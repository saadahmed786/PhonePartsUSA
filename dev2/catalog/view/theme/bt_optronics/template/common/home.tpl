<?php echo $header; ?>
<style>
.home-heading{
	margin-bottom:10px;margin-top:10px;padding:5px;font-size:18px;font-weight:bold;background-color:#EAEAEA;color:#000;text-align:center;}
</style>
<div id="menumove"><?php echo $column_left; ?>
<div id="content">
  <?php echo $content_top; ?>
  <?php if ($products) { ?>
  <!--<div class="product-filter row">
    <div class="product-compare"><a href="<?php echo $compare; ?>" id="compare-total"><?php //echo $text_compare; ?></a></div>	
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
  </div>-->
  
  <div class="pagination" style="display:none"><?php echo $pagination; ?></div>
  <?php } ?>
  
  <div style="" class="home-heading">Weekly Specials</div>
  
  <?php if (!$home_products1) { ?>
  <?php echo $text_empty; ?>
  <?php
  }
  else{ ?>
  <div class="product-list" >
  
  
  <ul class="product-list2">
   
    <?php foreach ($home_products1 as $key => $product) { ?>
    <li>
    <div class="ad-box" style="width:150px">
    	<span class="heart-icon"><a onclick="boss_addToWishList('<?php echo $product['product_id']; ?>');"><img src="catalog/view/theme/bt_optronics/image/heart-icon.png" class="heart-icon"></a></span>
    	<span class="product-img"><?php if ($product['thumb']) { ?><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" style="height:125px;width:125px"></a><?php } ?></span>
    	<p style="height:60px"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></p>
     <?php if ($product['sale_price']) { ?>
     <strong class="cut-price" ><del> <?php echo $product['price']; ?> </del></strong>&nbsp<strong class="sale-prise"><?php echo $product['sale_price']; ?></strong>
     <?php } else {  ?>
      <strong class="prise"><?php if ($product['price']) { ?>
          <?php if (!$product['special']) { ?>
        <?php echo $product['price']; ?></strong>
        <?php } else { ?>
        <span class="old-prise">$49.95</span>$39.95</strong>
        <?php } 
        } 
      }?>
    	<div class="quantity-box">
      	<em class="qty">Qty:</em>
      	<span class="text-field"><input data-min="1" type="text" id="qty<?php echo $product['product_id'];?>" name="quantity_<?php echo $product['product_id'];?>" value="1" ></span>
        <span class="pluse-icon"><a href="javascript:QtyChange('+','<?php echo $product['product_id'];?>')"><img src="catalog/view/theme/bt_optronics/image/pluse-icon.png" alt="pluse-icon"></a></span>
        <span class="less-icon"><a href="javascript:QtyChange('-','<?php echo $product['product_id'];?>')"><img src="catalog/view/theme/bt_optronics/image/less-icon.png" alt="less-icon"></a></span>
      </div>
      <a class="btn" href="javascript:void(0);" onclick="<?php if($product['quantity']>0) { ?>boss_addToCart('<?php echo $product['product_id'];?>',$('#qty<?php echo $product['product_id'];?>').val()); <?php } ?>" ><?php if($product['quantity']>0) { ?>ADD TO CART<?php } else {?>SOLD OUT <?php } ?> </a>
    </div>
    </li>
    <?php
    }
    ?>
    
    
    
    
				</ul>
  
	<?php foreach ($products as $key => $product) { ?>
    <div class="one-product-box<?php echo ((($key+1) %4 ==0 ) ? ' last' : ''); ?>" style="display:none">
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
  
  <?php } ?>
  
  
  
  <?php if (!$categories && !$products) { ?>
  <!--<div class="content"><?php echo $text_empty; ?></div>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button_pink"><span><?php echo $button_continue; ?></span></a></div>
  </div> -->
  <?php } ?>
</div>

 <div style="" class="home-heading">Popular Accessories</div>
 
 <?php if (!$home_products3) { ?>
  <?php echo $text_empty; ?>
  <?php
  }
  else{ ?>
  <div class="product-list" >
  
  
  <ul class="product-list2">
   
    <?php foreach ($home_products3 as $key => $product) { ?>
    <li>
    <div class="ad-box" style="width:160px">
    	<span class="heart-icon"><a onclick="boss_addToWishList('<?php echo $product['product_id']; ?>');"><img src="catalog/view/theme/bt_optronics/image/heart-icon.png" class="heart-icon"></a></span>
    	<span class="product-img"><?php if ($product['thumb']) { ?><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" style="height:125px;width:125px"></a><?php } ?></span>
    	<p style="height:60px"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></p>
     <?php if ($product['sale_price']) { ?>
     <strong class="cut-price" ><del> <?php echo $product['price']; ?> </del></strong>&nbsp<strong class="sale-prise"><?php echo $product['sale_price']; ?></strong>
     <?php } else {  ?>
      <strong class="prise"><?php if ($product['price']) { ?><?php if (!$product['special']) { ?>
        <?php echo $product['price']; ?></strong>
        <?php } else { ?><span class="old-prise">$49.95</span>$39.95</strong><?php } } }?>
    	<div class="quantity-box">
      	<em class="qty">Qty:</em>
      	<span class="text-field"><input data-min="1" type="text" id="qty<?php echo $product['product_id'];?>" name="quantity_<?php echo $product['product_id'];?>" value="1" ></span>
        <span class="pluse-icon"><a href="javascript:QtyChange('+','<?php echo $product['product_id'];?>')"><img src="catalog/view/theme/bt_optronics/image/pluse-icon.png" alt="pluse-icon"></a></span>
        <span class="less-icon"><a href="javascript:QtyChange('-','<?php echo $product['product_id'];?>')"><img src="catalog/view/theme/bt_optronics/image/less-icon.png" alt="less-icon"></a></span>
      </div>
      <a class="btn" href="javascript:void(0);" onclick="<?php if($product['quantity']>0) { ?>boss_addToCart('<?php echo $product['product_id'];?>',$('#qty<?php echo $product['product_id'];?>').val()); <?php } ?>" ><?php if($product['quantity']>0) { ?>ADD TO CART<?php } else {?>SOLD OUT <?php } ?> </a>
    </div>
    </li>
    <?php
    }
    ?>
    
    
    
    
				</ul>
  
	<?php foreach ($products as $key => $product) { ?>
    <div class="one-product-box<?php echo ((($key+1) %4 ==0 ) ? ' last' : ''); ?>" style="display:none">
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
  
  <?php } ?>

 <div style="" class="home-heading">Popular Repair Parts</div>
 <?php if (!$home_products2) { ?>
  <?php echo $text_empty; ?>
  <?php
  }
  else{ ?>
  <div class="product-list" >
  
  
  <ul class="product-list2">
   
    <?php foreach ($home_products2 as $key => $product) { ?>
    <li>
    <div class="ad-box" style="width:160px">
    	<span class="heart-icon"><a onclick="boss_addToWishList('<?php echo $product['product_id']; ?>');"><img src="catalog/view/theme/bt_optronics/image/heart-icon.png" class="heart-icon"></a></span>
    	<span class="product-img"><?php if ($product['thumb']) { ?><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" style="height:125px;width:125px"></a><?php } ?></span>
    	<p style="height:60px"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></p>
     <?php if ($product['sale_price']) { ?>
     <strong class="cut-price" ><del> <?php echo $product['price']; ?> </del></strong>&nbsp<strong class="sale-prise"><?php echo $product['sale_price']; ?></strong>
     <?php } else {  ?>
      <strong class="prise"><?php if ($product['price']) { ?><?php if (!$product['special']) { ?>
        <?php echo $product['price']; ?></strong>
        <?php } else { ?><span class="old-prise">$49.95</span>$39.95</strong><?php } } }?>
    	<div class="quantity-box">
      	<em class="qty">Qty:</em>
      	<span class="text-field"><input data-min="1" type="text" id="qty<?php echo $product['product_id'];?>" name="quantity_<?php echo $product['product_id'];?>" value="1" ></span>
        <span class="pluse-icon"><a href="javascript:QtyChange('+','<?php echo $product['product_id'];?>')"><img src="catalog/view/theme/bt_optronics/image/pluse-icon.png" alt="pluse-icon"></a></span>
        <span class="less-icon"><a href="javascript:QtyChange('-','<?php echo $product['product_id'];?>')"><img src="catalog/view/theme/bt_optronics/image/less-icon.png" alt="less-icon"></a></span>
      </div>
      <a class="btn" href="javascript:void(0);" onclick="<?php if($product['quantity']>0) { ?>boss_addToCart('<?php echo $product['product_id'];?>',$('#qty<?php echo $product['product_id'];?>').val()); <?php } ?>" ><?php if($product['quantity']>0) { ?>ADD TO CART<?php } else {?>SOLD OUT <?php } ?> </a>
    </div>
    </li>
    <?php
    }
    ?>
    
    
    
    
				</ul>
  
	<?php foreach ($products as $key => $product) { ?>
    <div class="one-product-box<?php echo ((($key+1) %4 ==0 ) ? ' last' : ''); ?>" style="display:none">
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
		
		//$('.display').html('<b><?php echo $text_display; ?></b> <span class="active-list" title="<?php echo $text_list; ?>"><?php echo $text_list; ?></span><a title="<?php echo $text_grid; ?>" class="no-active-gird" onclick="display(\'grid\');"><?php echo $text_grid; ?></a>');
		
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
					
		//$('.display').html('<b><?php echo $text_display; ?></b> <a title="<?php echo $text_list; ?>" class="no-active-list" onclick="display(\'list\');"><?php echo $text_list; ?></a><span class="active-gird" title="<?php echo $text_grid; ?>" ><?php echo $text_grid; ?></span>');
		
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
	function QtyChange(xtype,product_id)
	{
		
		if(xtype=='+')
		{
			$('#qty'+product_id).val(parseInt($('#qty'+product_id).val())+1);
		}
		
		if(xtype=='-' && $('#qty'+product_id).val()>1)
		{
			
			$('#qty'+product_id).val(parseInt($('#qty'+product_id).val())-1);
			
		}
	}
//--></script> 
<?php echo $footer; ?>
