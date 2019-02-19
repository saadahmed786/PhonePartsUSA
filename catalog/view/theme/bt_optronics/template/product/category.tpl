<?php echo $header; ?> 
 
 <style>
 /* @Header
********************************************************************************************
********************************************************************************************/
.product-holder{ max-width:826px; overflow:hidden; padding:53px 10px 10px 47px;}
.ad-box{ width:130px; text-align:center; padding:6px 4px 12px 10px; border:1px solid #d6d6d6; margin:0 0 0 0; float:left; position:relative;}
.ad-box:hover { display:block; transition:all 0.5s;
-webkit-box-shadow: 0px 0px 8px 0px rgba(0,0,0,0.50);
-moz-box-shadow: 0px 0px 8px 0px rgba(0,0,0,0.50);
box-shadow: 0px 0px 8px 0px rgba(0,0,0,0.50);	
}
.ad-box:hover .heart-icon{ display:block;}

.product-list2{ margin:0; padding:0; list-style:none;}
.product-list2 li{ float:left; margin:0 14px 10px 0;}

.heart-icon{ display:block; position:absolute; top:5px; left:5px; display:none;}

.product-img{ margin:0 0 20px; display:block;}
.ad-box p{ margin:0; font-size:9pt; color:#595959; height:85px; overflow:hidden;}
.modal{ font-size:6.48pt; color:#d2d2d2; display:block; margin:0 0 29px;height:12px;overflow:hidden;}
.prise{ font-size:16px; color:#000; display:block; margin:0 0 14px;}
.cut-prise{ font-size:15px; display:inline-block; margin:0 0 14px; color:#000; }
.sale-prise{ font-size:16px; display:inline-block; margin:0 0 14px; color:red; }
.old-prise{ font-size:12px; color:#d2d2d2; margin:0 5px 0 0; text-decoration:line-through;}

.quantity-box{ width:84px; display:inline-block; position:relative;}
.text-field{ display:inline-flex;  margin:0 5px 0 -13px;}
.text-field input {height:24px;width:29px;margin-top:-4px;text-align:center;border-radius:4px}
.quantity-box em{ display:block; float:left; color:#4c4c4c; margin:0 0 15px;}
.pluse-icon{ position:absolute; top:-4px;}
.less-icon{ position:absolute; bottom:5px;}
.btn{
    display: inline-block;
    text-align: center;
    vertical-align: middle;
    padding: 11px 24px;
    border: 1px solid #f7de00;
    border-radius: 5px;
    background: #ffffc4;
    background: -webkit-gradient(linear, left top, left bottom, from(#ffffc4), to(#f7de00));
    background: -moz-linear-gradient(top, #ffffc4, #f7de00);
    background: linear-gradient(to bottom, #ffffc4, #f7de00);
    text-shadow: #ffff00 1px 1px 1px;
    font: normal normal bold 7.5pt arial;
    color: #111111;
    text-decoration: none;
}
.btn:hover,
.btn:focus {
    background: #ffffc4;
    background: -webkit-gradient(linear, left top, left bottom, from(#ffffc4), to(#ffff00));
    background: -moz-linear-gradient(top, #ffffc4, #ffff00);
    background: linear-gradient(to bottom, #ffffc4, #ffff00);
    color: #111111;
    text-decoration: none;
}
.btn:active {
    background: #b3a900;
    background: -webkit-gradient(linear, left top, left bottom, from(#ffff00), to(#ffff00));
    background: -moz-linear-gradient(top, #ffff00, #ffff00);
    background: linear-gradient(to bottom, #ffff00, #ffff00);
}



 <?php 
 if(count($categories)<=5)
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
	height:300px;
 
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
  <div class="product-holder">
  	
    <ul class="product-list2">
   
    <?php foreach ($products as $key => $product) { ?>
    <li>
    <div class="ad-box">
    	<span class="heart-icon"><a onclick="boss_addToWishList('<?php echo $product['product_id']; ?>');"><img src="catalog/view/theme/bt_optronics/image/heart-icon.png" class="heart-icon"></a></span>
    	<span class="product-img"><?php if ($product['thumb']) { ?><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" style="height:125px;width:125px"></a><?php } ?></span>
    	<p><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></p>
      <span class="modal"><?php echo $product['model']; ?></span>
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
      <a class="btn" href="javascript:void(0);" onclick="<?php if($product['quantity']>0) { ?>addToCartQty('<?php echo $product['product_id'];?>', this); <?php } ?>" ><?php if($product['quantity']>0) { ?>ADD TO CART<?php } else {?>OUT OF STOCK <?php } ?> </a>
    </div>
    </li>
    <?php
    }
    ?>
    
    
    
    
				</ul>
  </div>
  
  <div class="matrialPagination"><?php echo $pagination; ?></div>
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

