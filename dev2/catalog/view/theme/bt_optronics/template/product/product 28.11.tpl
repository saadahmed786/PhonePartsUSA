<?php echo $header; ?> 
<style>
  #column-left #boss_menu ul.display-menu {
    position: relative;
    display: block;
    border-color: #dedcdc;
    top: auto;
  }
  .boss-image-add img{
	width:35px !important;  
  }
  .es-nav
  {
	display:none;  
  }
  .product-info .image {
  margin-bottom:15px !important;
  }
</style>
 <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <a <?php echo(($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''); ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content">


  <div class="product-info">
    <h2><?php echo $heading_title; ?></h2>
    <?php if ($thumb || $images) { ?>
    
    <div class="left" >
      <?php if ($thumb) { ?>
      <div class="image a_bossthemes" >
        <a href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>" class="cloud-zoom" id="zoom1">
          <img src="<?php echo $thumb; ?>11" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" id="image" /></a></div>
      <?php } ?>
      
      <?php if ($images) { ?>
      <div class="image-additional a_bossthemes" >
		<div class="es-carousel">
        
		  <ul  class="skin-opencart" style="width:auto">
			<?php foreach ($images as $image) { ?>
			<li><div class="boss-image-add"><a href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>" class="colorbox"><img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a></div></li>
			<?php } ?>
		  </ul>
		</div>
      </div>
      <?php } ?>
    </div>
    <?php } ?>
    <div class="right" >
      <div class="description">
        <?php if ($manufacturer) { ?>
        <span>- <?php echo $text_manufacturer; ?></span> <a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a><br />
        <?php } ?>
        <span>- <?php echo $text_model; ?></span> <?php echo $model; ?><br />
        <?php if ($reward) { ?>
        <span>- <?php echo $text_reward; ?></span> <?php echo $reward; ?><br />
        <?php } ?>
        <span>- <?php echo $text_stock; ?></span> <?php echo $stock; ?></div>
        <?php if ($stock == 'In Stock') { ?>
          <img class="in-stock" src="/catalog/view/theme/bt_optronics/image/in-stock.png">
		  <div class="product_check_row_custom" style="/* margin-top: 20px;*/">
             <ul style="
    list-style: none;
    margin-top: 20px;
    display: block;
    /* margin: 0px; */
    /* margin-top: 20px; */
">
  <li>
      <img src="http://cdn3.bigcommerce.com/s-1rpfvdxj/templates/__custom/images/check.png?t=1413538013" border="0" width="17" height="14">
    Free Shipping For Orders Over $10
  </li>
  <li>
      <img src="http://cdn3.bigcommerce.com/s-1rpfvdxj/templates/__custom/images/check.png?t=1413538013" border="0" width="17" height="14">
    Leaves Our US Warehouse Within 24 Hours
  </li>
  
  <li>
      <img src="http://cdn3.bigcommerce.com/s-1rpfvdxj/templates/__custom/images/check.png?t=1413538013" border="0" width="17" height="14">
    Lowest Prices Online Guaranteed
  </li><li>
      <img src="http://cdn3.bigcommerce.com/s-1rpfvdxj/templates/__custom/images/check.png?t=1413538013" border="0" width="17" height="14">
    Unconditional 90 Day Return Policy
  </li>
</ul>
          
        </div>
        <?php } ?>
      <?php if ($price) { ?>
            <div class="price">
        <?php if ($tax) { ?>
        <span class="price-tax"><?php echo $text_tax; ?> <?php echo $tax; ?></span>
        <?php } ?>
        <?php if ($points) { ?>
        <span class="reward"><small><?php echo $text_points; ?> <?php echo $points; ?></small></span>
        <?php } ?>
        <div class="discounts">
          <table cellpadding="0" cellspacing="0" border="0">
            <thead>
              <tr>
                <th>Quantity</th>
                <th>Our Price</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($discounts) { ?>
                <tr>
                  <td>1</td>
                  <td><?php echo $price; ?></td>
                </tr>
                <?php foreach ($discounts as $key=>$discount) { ?>
                  <tr class="<?php echo ($key % 2 == 0 ? 'even' : '') ?>">
                    <td>
                      <?php echo $discount['quantity'] . ($discount === end($discounts) ? '+' : ' - ' . ( $discounts[$key+1]['quantity'] - 1 )) ?>
                    </td>
                    <td>
                      <?php echo $discount['price']; ?>
                    </td>
                  </tr>
                <?php } ?>
              <?php } else { ?>
              <tr>
                <td>1</td>
                <td class="red"><?php echo $price; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php } ?>
      <?php if ($options) { ?>
      <div class="options">
        <h2><?php echo $text_option; ?></h2>
        
        <?php foreach ($options as $option) { ?>
        <?php if ($option['type'] == 'select') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
		  <b><?php echo $option['name']; ?>:</b>	
          <?php if ($option['required']) { ?>
          <span class="required">(*)</span>
          <?php } ?>
          <br />
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
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'radio') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
		  <b><?php echo $option['name']; ?>:</b>
          <?php if ($option['required']) { ?>
          <span class="required">(*)</span>
          <?php } ?>
          <br />
          <?php foreach ($option['option_value'] as $option_value) { ?>
          <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
          <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
            <?php if ($option_value['price']) { ?>
            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
            <?php } ?>
          </label>
          <br />
          <?php } ?>
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'checkbox') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
		  <b><?php echo $option['name']; ?>:</b>
          <?php if ($option['required']) { ?>
          <span class="required">(*)</span>
          <?php } ?>
          <br />
          <?php foreach ($option['option_value'] as $option_value) { ?>
          <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
          <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
            <?php if ($option_value['price']) { ?>
            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
            <?php } ?>
          </label>
          <br />
          <?php } ?>
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'image') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
		  <b><?php echo $option['name']; ?>:</b>
          <?php if ($option['required']) { ?>
          <span class="required">(*)</span>
          <?php } ?>
          <br />
          <table class="option-image">
            <?php foreach ($option['option_value'] as $option_value) { ?>
            <tr>
              <td style="width: 1px;"><input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" /></td>
              <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" /></label></td>
              <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                  <?php if ($option_value['price']) { ?>
                  (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                  <?php } ?>
                </label></td>
            </tr>
            <?php } ?>
          </table>
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'text') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
		  <b><?php echo $option['name']; ?>:</b>
          <?php if ($option['required']) { ?>
          <span class="required">(*)</span>
          <?php } ?>
          <br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" />
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'textarea') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
		  <b><?php echo $option['name']; ?>:</b>
          <?php if ($option['required']) { ?>
          <span class="required">(*)</span>
          <?php } ?>
          <br />
          <textarea name="option[<?php echo $option['product_option_id']; ?>]" cols="40" rows="5"><?php echo $option['option_value']; ?></textarea>
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'file') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
		  <b><?php echo $option['name']; ?>:</b>
          <?php if ($option['required']) { ?>
          <span class="required">(*)</span>
          <?php } ?>
          <br />
          <br />
          <span class="button_pink"><input type="button" value="<?php echo $button_upload; ?>" id="button-option-<?php echo $option['product_option_id']; ?>" class="button"></span>
          <input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" />
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'date') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
		  <b><?php echo $option['name']; ?>:</b>
          <?php if ($option['required']) { ?>
          <span class="required">(*)</span>
          <?php } ?>
          <br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="date" />
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'datetime') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <b><?php echo $option['name']; ?>:</b>
          <?php if ($option['required']) { ?>
          <span class="required">(*)</span>
          <?php } ?>
          <br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="datetime" />
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'time') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
		  <b><?php echo $option['name']; ?>:</b>	
          <?php if ($option['required']) { ?>
          <span class="required">(*)</span>
          <?php } ?>
          <br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="time" />
        </div>
        <br />
        <?php } ?>
        <?php } ?>
      </div>
      <?php } ?>
      
      <div class="cart">
        
		<div class="action">
		<table cellpadding="0" cellspacing="0" border="0">
		<tbody>
		<tr>
		  <td><div><b><?php echo $text_qty; ?></b>
          <input type="text" name="quantity" size="2" value="<?php echo $minimum; ?>" />
		</div>  
        <?php if ($minimum > 1) { ?>
        <div class="minimum"><?php echo $text_minimum; ?></div>
        <?php } ?></td>
          <td><input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />          
          <span class="button_pink"><input type="button" value="<?php echo $button_cart; ?>" id="button-cart" class="button" /></span></td>
          </tbody>
          </table>
          
        </div>
      </div>
      <?php if ($review_status) { ?>
      <div class="review">
        <div><img src="catalog/view/theme/bt_optronics/image/stars-<?php echo $rating; ?>.png" alt="<?php echo $reviews; ?>" />&nbsp;&nbsp;<a class="reviews" onclick="$('a[href=\'#tab-review\']').trigger('click');goToByScroll('tab-review');"><?php   $explode_reviews = explode(" ", $reviews); echo $explode_reviews[0]." ".$explode_reviews[1]." \"".$explode_reviews[2]." ".$explode_reviews[3]."\"";  ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="write_review" onclick="$('a[href=\'#tab-review\']').trigger('click');goToByScroll('review-title');"><?php echo $text_write; ?></a></div>
        <div class="share"><!-- AddThis Button BEGIN -->
          <div class="addthis_default_style"><a class="addthis_button_compact"><?php echo $text_share; ?></a> <a class="addthis_button_email"></a><a class="addthis_button_print"></a> <a class="addthis_button_facebook"></a> <a class="addthis_button_twitter"></a></div>
          <script type="text/javascript" src="//s7.addthis.com/js/250/addthis_widget.js"></script> 
          <!-- AddThis Button END --> 
        </div>
      </div>
      
      <?php } ?>
    </div>
  </div>
  <?php echo $content_bottom; ?>
  <div id="tabs" class="htabs"><a href="#tab-description"><span style="font-size: 17px;"><?php echo $tab_description; ?></span></a>
    <?php if ($attribute_groups) { ?>
    <a href="#tab-attribute"><?php echo $tab_attribute; ?></a>
    <?php } ?>
  </div>
  <h2 class="ta-header"><span><?php echo $tab_description; ?></span></h2>
  <div id="tab-description" class="tab-content"><?php echo $description; ?></div>
  <?php if ($attribute_groups) { ?>
  <h2 class="ta-header"><span><?php echo $tab_attribute; ?></span></h2>
  <div id="tab-attribute" class="tab-content">
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
  </div>
  <?php } ?>
 
 <?php if ($review_status) { ?>
  <h2 class="ta-status"><span><?php echo $tab_review; ?></span></h2>
  <div id="tab-review" class="tab-content">
    <div id="review"></div>
    <h2 id="review-title"><?php echo $text_write; ?></h2>
    <b><?php echo $entry_name; ?></b><br />
    <input type="text" name="name" value="" />
    <br />
    <br />
    <b><?php echo $entry_review; ?></b>
    <textarea name="text" cols="40" rows="8" style="width: 98%;"></textarea>
    <span style="font-size: 11px;"><?php echo $text_note; ?></span><br />
    <br />
    <b><?php echo $entry_rating; ?></b> <span><?php echo $entry_bad; ?></span>&nbsp;
    <input type="radio" name="rating" value="1" />
    &nbsp;
    <input type="radio" name="rating" value="2" />
    &nbsp;
    <input type="radio" name="rating" value="3" />
    &nbsp;
    <input type="radio" name="rating" value="4" />
    &nbsp;
    <input type="radio" name="rating" value="5" />
    &nbsp;<span><?php echo $entry_good; ?></span><br />
    <br />
    <b><?php echo $entry_captcha; ?></b><br />
    <input type="text" name="captcha" value="" />
    <br />
    <img src="index.php?route=product/product/captcha" alt="" id="captcha" /><br />
    <br />
    <div class="buttons">
      <div class="left"><a id="button-review" class="button_pink"><span><?php echo $button_continue; ?></span></a></div>
    </div>
  </div>
  <?php } ?>
 
 <?php if ($products) { ?>
  <h2 class="ta-related"><span><?php echo $tab_related; ?> (<?php echo count($products); ?>)</span></h2>
  <div id="tab-related" class="tab-content">
	<div class="es-carousel">
	 <ul class="skin-opencart" >
      <?php foreach ($products as $product) { ?>
      <li><div class="boss-tab-related">
        <?php if ($product['thumb']) { ?>
        <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a></div>
        <?php } ?>
        <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
        <?php if ($product['rating']) { ?>
        <div class="rating"><img src="catalog/view/theme/bt_optronics/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
        <?php } ?>
        <?php if ($product['price']) { ?>
        <div class="price">
          <?php if (!$product['special']) { ?>
          <?php echo $product['price']; ?>
          <?php } else { ?>
          <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
          <?php } ?>
        </div>
        <?php } ?>
		<div class="cart">
        <span class="button_pink"><input type="button" value="<?php echo $button_cart; ?>" onclick="boss_addToCart('<?php echo $product['product_id']; ?>');" class="button" /></span>
		</div>
          <a class="compare" onclick="boss_addToCompare('<?php echo $product['product_id']; ?>');"><?php echo $button_compare; ?></a><br />
		  <a class="wishlist" onclick="boss_addToWishList('<?php echo $product['product_id']; ?>');"><?php echo $button_wishlist; ?></a>
        </div>
		</li>
      <?php } ?>
	  </ul>
    </div>
  </div>
  <?php } ?>
  
  <?php if ($tags) { ?>
  <div class="tags"><b><?php echo $text_tags; ?></b>
    <?php for ($i = 0; $i < count($tags); $i++) { ?>
    <?php if ($i < (count($tags) - 1)) { ?>
    <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>,
    <?php } else { ?>
    <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>
    <?php } ?>
    <?php } ?>
  </div>
  <?php } ?>
  
  
  
<?php if (file_exists('catalog/view/theme/bt_optronics/stylesheet/boss_carousel_product.css')) {
		echo '<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/boss_carousel_product.css" media="screen" />';
	} else {
		echo '<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/boss_carousel_product.css" media="screen" />';
	}
?>
</div> <!-- End static header bottom -->
<script type="text/javascript" src="catalog/view/javascript/bossthemes/jquery.easing.js"></script>
<script type="text/javascript" src="catalog/view/javascript/bossthemes/jquery.elastislide.js"></script>


<script type="text/javascript"><!--
function goToByScroll(id){
		$('html,body').animate({scrollTop: $("#"+id).offset().top},'slow');
		$('h2.ta-header').removeClass('selected');
		$('#tab-review').prev().addClass('selected');
}
//--></script>  
  
  <script type="text/javascript"><!--
$('#button-cart').bind('click', function() {
	$.ajax({
		url: 'index.php?route=bossthemes/cart/add',
		type: 'post',
		data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
		dataType: 'json',
		success: function(json) {
		$('.warning, .attention, information, .error').remove();
			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						$('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
					}
				}
			}  

			if (json['success']) {
				//addProductNotice(json['title'], json['thumb'], json['success'], 'success');
				openThecart();
				$( "#divTopright" ).fadeIn( "slow", function() {});
				$('#cart_menu span.s_grand_total').html(json['total_sum']);
				$('#cart_menu div.s_cart_holder').html(json['output']);
				$('#cart-total').html(json['total']);
				$('#cart-total2').html(json['total']);
				$.get('index.php?route=module/cart #cart > *', function(data) {
				  
				  var cartContent = data.split('<!--SPLIT FOR PRODUCT PAGE-->');
				  
				  $('#cart').html(cartContent[0]+cartContent[2]);


				  

				
				});
				
				
			}
		}
	});
});
//--></script>
<script type="text/javascript"><!--
$(document).ready(function() {
	$('.colorbox').colorbox({
		overlayClose: true,
		opacity: 0.5,
		rel: "colorbox"
	});
});
//--></script> 

<?php if ($options) { ?>
<script type="text/javascript" src="catalog/view/javascript/jquery/ajaxupload.js"></script>
<?php foreach ($options as $option) { ?>
<?php if ($option['type'] == 'file') { ?>
<script type="text/javascript"><!--
new AjaxUpload('#button-option-<?php echo $option['product_option_id']; ?>', {
	action: 'index.php?route=product/product/upload',
	name: 'file',
	autoSubmit: true,
	responseType: 'json',
	onSubmit: function(file, extension) {
		$('#button-option-<?php echo $option['product_option_id']; ?>').after('<img src="catalog/view/theme/default/image/loading.gif" class="loading" style="padding-left: 5px;" />');
		$('#button-option-<?php echo $option['product_option_id']; ?>').attr('disabled', true);
	},
	onComplete: function(file, json) {
		$('#button-option-<?php echo $option['product_option_id']; ?>').attr('disabled', false);
		
		$('.error').remove();
		
		if (json['success']) {
			alert(json['success']);
			
			$('input[name=\'option[<?php echo $option['product_option_id']; ?>]\']').attr('value', json['file']);
		}
		
		if (json['error']) {
			$('#option-<?php echo $option['product_option_id']; ?>').after('<span class="error">' + json['error'] + '</span>');
		}
		
		$('.loading').remove();	
	}
});
//--></script>
<?php } ?>
<?php } ?>
<?php } ?>
<script type="text/javascript"><!--
$('#review .pagination a').live('click', function() {
	$('#review').fadeOut('slow');
		
	$('#review').load(this.href);
	
	$('#review').fadeIn('slow');
	
	return false;
});			

$('#review').load('index.php?route=product/product/review&product_id=<?php echo $product_id; ?>');

$('#button-review').bind('click', function() {
	$.ajax({
		url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
		type: 'post',
		dataType: 'json',
		data: 'name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&text=' + encodeURIComponent($('textarea[name=\'text\']').val()) + '&rating=' + encodeURIComponent($('input[name=\'rating\']:checked').val() ? $('input[name=\'rating\']:checked').val() : '') + '&captcha=' + encodeURIComponent($('input[name=\'captcha\']').val()),
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#button-review').attr('disabled', true);
			$('#review-title').after('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-review').attr('disabled', false);
			$('.attention').remove();
		},
		success: function(data) {
			if (data['error']) {
				$('#review-title').after('<div class="warning">' + data['error'] + '</div>');
			}
			
			if (data['success']) {
				$('#review-title').after('<div class="success">' + data['success'] + '</div>');
								
				$('input[name=\'name\']').val('');
				$('textarea[name=\'text\']').val('');
				$('input[name=\'rating\']:checked').attr('checked', '');
				$('input[name=\'captcha\']').val('');
			}
		}
	});
});
//--></script>
<script type="text/javascript"><!--
	$('h2.ta-header').first().addClass('selected');
	$('h2.ta-header').click(function() {
		if($(this).next().css('display') == 'none'){
			$(this).next().show();
			$(this).addClass('selected');
		}else{
			$(this).next().hide();
			$(this).removeClass('selected');
		}	
		return false;
	}).next().hide();	
//--></script> 
<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script> 
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-timepicker-addon.js"></script> 
<script type="text/javascript"><!--
$(document).ready(function() {
	if ($.browser.msie && $.browser.version == 6) {
		$('.date, .datetime, .time').bgIframe();
	}

	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
	$('.datetime').datetimepicker({
		dateFormat: 'yy-mm-dd',
		timeFormat: 'h:m'
	});
	$('.time').timepicker({timeFormat: 'h:m'});
});

//--></script>
<script type="text/javascript"><!--
	$(document).ready(function() {
		product_resize();
	});
	$(window).resize(function() {
		product_resize();
	});
	
	function disableLink(e) {
		e.preventDefault();
		return false;
	}

	function product_resize()	{
		if(getWidthBrowser() < 767){
			$('div.a_bossthemes a').bind('click', disableLink);
			$('#tabs').hide();
			$('h2.ta-header').show();
		} else {
			$('div.a_bossthemes a').unbind('click', disableLink);
			$('h2.ta-header').hide();
			$('#tabs').show();
			if(getWidthBrowser() < 960){
				$('#tab-related').elastislide({
					imageW 		: 160,
					border		: 0,
					current		: 0,
					margin		: 10,
					onClick 	: true,
					minItems	: 1,
					disable_touch		: false
				});
			}else{
				$('#tab-related').elastislide({
					imageW 		: 160,
					border		: 0,
					current		: 0,
					margin		: 20,
					onClick 	: true,
					minItems	: 1,
					disable_touch		: false
				});
			}
			$('.image-additional').elastislide({
				imageW 		: 37.9,
				border		: 0,
				current		: 0,
				margin		: 8,
				onClick 	: true,
				minItems	: 3,
				disable_touch		: false
			});
		}
	}

//--></script> 

<?php echo $footer; ?>
