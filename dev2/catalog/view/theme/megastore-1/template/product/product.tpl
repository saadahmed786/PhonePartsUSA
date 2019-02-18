<?php echo $header; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content">
<?php echo $content_top; ?>
<ul class="product-tab">
	<li class="overview"><a href="javascript:void(0);" class="active"><?php echo "Overview"; ?></a></li>
	<li class="description"><a href="javascript:void(0);"><?php echo $tab_description; ?></a></li>
    <?php if ($attribute_groups) { ?>
    	<li class="prod-attribute"><a href="javascript:void(0);"><?php echo $tab_attribute; ?></a></li>
    <?php } ?>
    <?php if ($review_status) { ?>
    	<li class="reviews"><a href="javascript:void(0);"><?php echo $tab_review; ?></a></li>
    <?php } ?>   
</ul>

  <div id="overview" class="product-info tab-content">
    <?php if ($thumb || $images) { ?>
    <div class="left">
      <?php if ($thumb) { ?>
      <div class="image"><a href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>" class="colorbox" rel="colorbox"><img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" id="image" /></a></div>
      <?php } ?>
      <?php if ($images) { ?>
      <div class="image-additional">
        <?php foreach ($images as $image) { ?>
        <a href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>" class="colorbox" rel="colorbox"><img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
        <?php } ?>
      </div>
      <?php } ?>
    </div>
    <?php } ?>
    <div class="right">
      <h1><?php echo $heading_title; ?></h1>
      <p class="short-desc"><?php echo strip_tags(substr($description,0,250)) . "..."; ?> <a href="javascript:void(0);" class="description">More &raquo;</a></p> 
	  <?php if ($review_status) { ?>
      <div class="review">
        <div><img src="catalog/view/theme/megastore/image/stars-<?php echo $rating; ?>.png" alt="<?php echo $reviews; ?>" />&nbsp;&nbsp;<a href="javascript:void(0);" class="reviews"><?php echo $reviews; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" class="reviews"><?php echo $text_write; ?></a></div>        
      </div>
      <?php } ?>
      <div class="description">
        <?php if ($manufacturer) { ?>
        <span><?php echo $text_manufacturer; ?></span> <a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a><br />
        <?php } ?>
        <span><?php echo $text_model; ?></span> <?php echo $model; ?><br />
        <?php if ($reward) { ?>
        <span><?php echo $text_reward; ?></span> <?php echo $reward; ?><br />
        <?php } ?>
        <span><?php echo $text_stock; ?></span> <?php echo $stock; ?><br />
        <?php if ($price) { ?>
        <div class="price">
        <span><?php echo $text_price; ?></span>   
			<?php if (!$special) { ?>
                <?php echo $price; ?>
            <?php } else { ?>
                <span class="price-old"><?php echo $price; ?></span> <span class="price-new"><?php echo $special; ?></span>
            <?php } ?>
            
            <?php if ($tax) { ?>
                <span class="price-tax">- <?php echo $text_tax; ?> <?php echo $tax; ?></span>
            <?php } ?>
            </div>
               <?php } ?>
       <?php if ($points) { ?>
        <span class="reward"><?php echo $text_points; ?> <?php echo $points; ?></span>
        <?php } ?>
        
        <div class="share">
    	<span><?php echo $this->language->get('text_hshare') ?></span>
        <div class="fb-like" data-href="<?php echo HTTP_SERVER . "index.php?route=product/product&product_id=" . $this->request->get['product_id']; ?>" data-layout="button_count" data-width="350" data-show-faces="false" data-font="lucida grande"></div>
        
        <a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo HTTP_SERVER . "index.php?route=product/product&product_id=" . $this->request->get['product_id']; ?>" data-dnt="true">Tweet</a>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        <div class="pin-it-button">
        <a href="http://pinterest.com/pin/create/button/?url=<?php echo HTTP_SERVER . "index.php?route=product/product&product_id=" . $this->request->get['product_id']; ?>&media=<?php echo $thumb; ?>&description=<?php echo strip_tags(substr($description,0,250)); ?>" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a> 
        </div>
	</div>
               
        </div>        
       
        <?php if ($discounts) { ?>
        <div class="discount">
        	<p class="heading"><span><?php echo $this->language->get('text_bulk'); ?></span></p>
          <ul>
                      <?php foreach ($discounts as $discount): ?>
                            <li><?php echo $this->language->get('text_buy') . sprintf($text_discount, "<span>".$discount['quantity']."</span>", $this->language->get('text_for'). "<span class=\"dPrice\">" . $discount['price'] . "</span>") . $this->language->get('text_each'); ?></li>
                      <?php endforeach; ?>
                </ul>
        </div>
      <?php } ?>
       <?php if ($options) { ?>
      <div class="options">
        <h2><?php echo $text_option; ?></h2>
        <div class="prod-options">
        <?php foreach ($options as $option) { ?>
        <?php if ($option['type'] == 'select') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          
          <p class="required"><?php if ($option['required']) { ?>* <?php } ?><b><?php echo $option['name']; ?>:</b></p>
         
          
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
       
        <?php } ?>
        <?php if ($option['type'] == 'radio') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          
          <p class="required"><?php if ($option['required']) { ?>* <?php } ?><b><?php echo $option['name']; ?>:</b></p>
         <div class="radio">
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
          <div class="clear"></div>
          
        </div>
        
        <?php } ?>
        <?php if ($option['type'] == 'checkbox') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          
          <p class="required"><?php if ($option['required']) { ?>*<?php } ?><b><?php echo $option['name']; ?>:</b></p>
       <div class="checkbox">
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
          <div class="clear"></div>
        </div>
       
        <?php } ?>
        <?php if ($option['type'] == 'image') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          
          <p class="required"><?php if ($option['required']) { ?>* <?php } ?><b><?php echo $option['name']; ?>:</b></p>
          
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
       
        <?php } ?>
        <?php if ($option['type'] == 'text') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          
          <p class="required"><?php if ($option['required']) { ?>*<?php } ?> <b><?php echo $option['name']; ?>:</b></p>
          
         
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" />
        </div>
       
        <?php } ?>
        <?php if ($option['type'] == 'textarea') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          
          <p class="required"><?php if ($option['required']) { ?>*<?php } ?><b><?php echo $option['name']; ?>:</b></p>
       
          <textarea name="option[<?php echo $option['product_option_id']; ?>]" cols="40" rows="5"><?php echo $option['option_value']; ?></textarea>
        </div>
       
        <?php } ?>
        <?php if ($option['type'] == 'file') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <p class="required"><?php if ($option['required']) { ?>*<?php } ?><b><?php echo $option['name']; ?>:</b></p>
          <input type="button" value="<?php echo $button_upload; ?>" id="button-option-<?php echo $option['product_option_id']; ?>" class="button">
          <input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" />
        </div>
       
        <?php } ?>
        <?php if ($option['type'] == 'date') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
         <p class="required"><?php if ($option['required']) { ?>*<?php } ?><b><?php echo $option['name']; ?>:</b></p>
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="date" />
        </div>
       
        <?php } ?>
        <?php if ($option['type'] == 'datetime') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <p class="required"><?php if ($option['required']) { ?>*<?php } ?><b><?php echo $option['name']; ?>:</b></p>
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="datetime" />
        </div>
       
        <?php } ?>
        <?php if ($option['type'] == 'time') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <p class="required"><?php if ($option['required']) { ?>*<?php } ?><b><?php echo $option['name']; ?>:</b></p>
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="time" />
        </div>
        
        <?php } ?>
        <?php } ?>
      </div>
      </div>
      <?php } ?>
      <div class="cart">
        <div><?php echo $text_qty; ?>
          <input type="text" name="quantity" size="2" value="<?php echo $minimum; ?>" />
          <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
          &nbsp;
          <input type="button" value="<?php echo $button_cart; ?>" id="button-cart" class="button" />
        </div>
        <div><span>&nbsp;&nbsp;&nbsp;<?php echo $text_or; ?>&nbsp;&nbsp;&nbsp;</span></div>
        <div><a onclick="addToWishList('<?php echo $product_id; ?>');"><?php echo $button_wishlist; ?></a><br />
          <a onclick="addToCompare('<?php echo $product_id; ?>');"><?php echo $button_compare; ?></a></div>
        <?php if ($minimum > 1) { ?>
        <div class="minimum"><?php echo $text_minimum; ?></div>
        <?php } ?>
      
     
    <div class="clear"></div>
    </div>     
	  </div>
       </div>

  <div id="description" class="tab-content">
  	<h2 class="heading"><?php echo $heading_title; ?></h2>
  	<?php echo $description; ?>
  </div>
  <?php if ($attribute_groups) { ?>
  <div id="prod-attribute" class="tab-content prod-attribute">
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
  <div id="reviews" class="tab-content">
    <div id="review"></div>
    <h2 id="review-title"><?php echo $text_write; ?></h2>
    <div class="review-form">
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
          <div class="right"><a id="button-review" class="button"><?php echo $button_continue; ?></a></div>
        </div>
    </div>
  </div>
  <?php } ?>
 
<?php /*?>  <?php if ($tags) {  //Product Tags ?>
  
  <div class="tags"><b><?php echo $text_tags; ?></b>
    <?php for ($i = 0; $i < count($tags); $i++) { ?>
    <?php if ($i < (count($tags) - 1)) { ?>
    <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>,
    <?php } else { ?>
    <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>
    <?php } ?>
    <?php } ?>
  </div>
  <?php } ?><?php */?>
  
   <?php if ($products) { // Related Products ?>
  <div class="box">
  	<div class="box-heading"><h2><?php echo $tab_related; ?> (<?php echo count($products); ?>)</h2></div>
    <div class="box-content">
    <ul class="box-product">
      <?php foreach ($products as $product) { ?>
      <li>
        <?php if ($product['thumb']) { ?>
        <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
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
        <a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button"><?php echo $button_cart; ?></a></li>
      <?php } ?>
    </ul>
    </div>
    <div class="clear"></div>
  </div>
  <?php } ?>
  
  <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--
$('.colorbox').colorbox({
	overlayClose: true,
	opacity: 0.5
});
//--></script> 
<script type="text/javascript"><!--
$('#button-cart').bind('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, information, .error').remove();
			
			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						$('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
					}
				}
			} 
			
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
				$('.success').fadeIn('slow');
					
				$('#cart-total').html(json['total']);
				
				$('html, body').animate({ scrollTop: 0 }, 'slow'); 
			}	
		}
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
$('#tabs a').tabs();
//--></script> 
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-timepicker-addon.js"></script> 
<script type="text/javascript"><!--
if ($.browser.msie && $.browser.version == 6) {
	$('.date, .datetime, .time').bgIframe();
}

$('.date').datepicker({dateFormat: 'yy-mm-dd'});
$('.datetime').datetimepicker({
	dateFormat: 'yy-mm-dd',
	timeFormat: 'h:m'
});
$('.time').timepicker({timeFormat: 'h:m'});
//--></script> 
<?php echo $footer; ?>