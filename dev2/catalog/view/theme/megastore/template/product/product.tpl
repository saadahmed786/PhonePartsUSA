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
 <?php
	$useragent=$_SERVER['HTTP_USER_AGENT'];
	if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
		$lmobile = true ;
	}else{
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(stripos($ua,'android') !== false) {
			$lmobile = true ;
		}else{
			$lmobile = false ;
		}
	}
    
  ?>            
	<script type="text/javascript"> 
			  $(document).ready(function() {
				$("a#youtube").colorbox({
						'transition': 'elastic',
						'speed' : 350,
						'iframe' : true,
						'innerWidth' : 800,
						'innerHeight' : 500,
						'opacity' : 0.5,
						'callbackOnClose': function() {
				   $("#fancy_content").empty();
				   } 
				});
			  }); 
        </script>	  
		</div>
	
	</div>
	
	<p class="border"></p>

</div>

<div id="content" class="set-size">

	<?php echo $content_top; ?>

	  <?php if($column_left != '') { echo '<div class="grid-3 float-left">'.$column_left.'</div>'; } ?>
	  
	  <div class="grid-<?php echo $grid; ?> float-left">
	  
	  	<p class="clear" style="height:20px;"></p>
		

  <div class="product-info">
    <?php if ($thumb || $images) { ?>
    <div class="left">
      <?php if ($thumb) { ?>
      <div class="image" style="position:relative;width:380px"><a href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>" class="colorbox" rel="colorbox">
	  <?php echo $promo_tag_product_top_right; ?>
	  <?php echo $promo_tag_product_top_left; ?>
	  <?php echo $promo_tag_product_bottom_left; ?>
	  <?php echo $promo_tag_product_bottom_right; ?>
	  <img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" id="image" /></a></div>
      <?php } ?>
      <?php if ($images) { ?>
      <div class="image-additional">
        <?php foreach ($images as $image) { ?>
        <a href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>" class="colorbox" rel="colorbox"><img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
        <?php } ?>
      </div>
      <?php } ?>
   
      
            <?php if (strlen($youtubeproduct)>2) { ?>
               <div style="clear: both;"></div> 
               <div style="padding-top: 15px;padding-right: 20px;">
               
               <div style="border: 1px solid #ddd;padding: 5px;"> 
				<table  style="width: 100%; border-collapse: collapse; ">
				<tr>
				<?php if ($lmobile == false) { ?>
				<td style="text-align: center; height: 205px; width: 205px; background: URL('http://img.youtube.com/vi/<?php echo $youtubeproduct ; ?>/0.jpg') center no-repeat; ">
    				<a id="youtube" href="http://www.youtube.com/v/<?php echo $youtubeproduct.'&autoplay=1&autohide=1'  ; ?>">
				    <img border="0" src="image/run.png" width="70" height="70"></a>
				</td>
				<?php }else{ ?>
					<iframe class="youtube-player" type="text/html" width="853" height="480" src="http://www.youtube.com/embed/<?php echo $youtubeproduct ; ?>" frameborder="0">
				<?php } ?>
				</tr>
				</table>
                </div>
                </div>
            <?php } ?>
      
    </div>
    <?php } ?>
    <div class="right">
    
							
							<div class="right_left">
							
						      <?php if ($price) { ?>
						      <div class="price">
						        <?php if (!$special) { ?>
						        <?php echo '<span class="price-new">'.$price.'</span>'; ?>
						        <?php } else { ?>
						        <span class="price-old"><?php echo $price; ?></span><span class="price-new"><?php echo $special; ?></span>
						        <?php } ?>
						        <br />
						        <?php if ($tax && $this->config->get('ex_tax_price') != '0') { ?>
						        <span class="price-tax"><?php echo $text_tax; ?> <?php echo $tax; ?></span><br />
						        <?php } ?>
						        <?php if ($points && $this->config->get('reward_points') != '0') { ?>
						        <span class="reward"><small><?php echo $text_points; ?> <?php echo $points; ?></small></span><br />
						        <?php } ?>
						        <?php if ($discounts) { ?>
						        <br />
						        <div class="discount">
						          <?php foreach ($discounts as $discount) { ?>
						          <?php echo sprintf($text_discount, $discount['quantity'], $discount['price']); ?><br />
						          <?php } ?>
						        </div>
						        <?php } ?>
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
						        <span><?php echo $text_stock; ?></span> <?php echo $stock; ?>
								
								</div>
								
      <?php if ($review_status) { ?>
      <div class="review">
      	<?php if($this->config->get('reviews') != '0') { ?>
        <div style="padding-top:10px"><img src="catalog/view/theme/megastore/images/stars-<?php echo $rating; ?>.png" alt="<?php echo $reviews; ?>" />&nbsp;&nbsp;<a onclick="$('a[href=\'#tab-review\']').trigger('click');"><?php echo $reviews; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('a[href=\'#tab-review\']').trigger('click');"><?php echo $text_write; ?></a></div>
        <?php } else { echo '<div style="height:7px"></div>'; } ?>
        <?php if($this->config->get('product_social_share') != '0') { ?>
        <div class="share" style="padding-top:5px"><!-- AddThis Button BEGIN -->
          <div class="addthis_default_style"><a class="addthis_button_compact"><?php echo $text_share; ?></a> <a class="addthis_button_email"></a><a class="addthis_button_print"></a> <a class="addthis_button_facebook"></a> <a class="addthis_button_twitter"></a></div>
          <script type="text/javascript" src="//s7.addthis.com/js/250/addthis_widget.js"></script> 
          <!-- AddThis Button END --> 
        </div>
        <?php } ?>
      </div>
      <?php } ?>

								
							</div>
							
							<div class="right_right">

      <?php if ($options) { ?>
      <div class="options">
        <h2><?php echo $text_option; ?></h2>
        <?php foreach ($options as $option) { ?>
        <?php if ($option['type'] == 'select') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <p>          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?><?php echo $option['name']; ?>:</p>
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
          <p>          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?><?php echo $option['name']; ?>:</p>
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
        <?php } ?>
        <?php if ($option['type'] == 'checkbox') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <p>          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?><?php echo $option['name']; ?>:</p>
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
        <?php } ?>
        <?php if ($option['type'] == 'image') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <p>          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?><?php echo $option['name']; ?>:</p>
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
          <p>          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?><?php echo $option['name']; ?>:</p>
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" />
        </div>
        <?php } ?>
        <?php if ($option['type'] == 'textarea') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <p>          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?><?php echo $option['name']; ?>:</p>
          <textarea name="option[<?php echo $option['product_option_id']; ?>]" cols="40" rows="5"><?php echo $option['option_value']; ?></textarea>
        </div>
        <?php } ?>
        <?php if ($option['type'] == 'file') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <p>          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?><?php echo $option['name']; ?>:</p>
          <input type="button" value="<?php echo $button_upload; ?>" id="button-option-<?php echo $option['product_option_id']; ?>" class="button">
          <input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" />
        </div>
        <?php } ?>
        <?php if ($option['type'] == 'date') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <p>          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?><?php echo $option['name']; ?>:</p>
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="date" />
        </div>
        <?php } ?>
        <?php if ($option['type'] == 'datetime') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <p>          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?><?php echo $option['name']; ?>:</p>
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="datetime" />
        </div>
        <?php } ?>
        <?php if ($option['type'] == 'time') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <p>          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?><?php echo $option['name']; ?>:</p>
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="time" />
        </div>
        <?php } ?>
        <?php } ?>
      </div>
      <?php } ?>
								<div class="cart">
								
									<div class="qty">
									
										<?php echo $text_qty; ?> <input type="text" name="quantity" size="2" value="<?php echo $minimum; ?>" /><input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
										<a id="button-cart" class="button"><span><?php echo $button_cart; ?></span></a>
										
									</div>
									<div class="wish-list"><a onclick="addToWishList('<?php echo $product_id; ?>');"><?php echo $button_wishlist; ?></a> &nbsp;&nbsp;&nbsp;<a onclick="addToCompare('<?php echo $product_id; ?>');"><?php echo $button_compare; ?></a></div>
								
<?php if ($minimum > 1) { ?>
        <div class="minimum" style="padding-top:6px"><?php echo $text_minimum; ?></div>
        <?php } ?>
        
								</div>

      <?php if ($review_status) { ?>
      <div class="review">
      	<?php if($this->config->get('reviews') != '0') { ?>
        <div style="padding-top:10px"><img src="catalog/view/theme/megastore/images/stars-<?php echo $rating; ?>.png" alt="<?php echo $reviews; ?>" />&nbsp;&nbsp;<a onclick="$('a[href=\'#tab-review\']').trigger('click');"><?php echo $reviews; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('a[href=\'#tab-review\']').trigger('click');"><?php echo $text_write; ?></a></div>
        <?php } else { echo '<div style="height:7px"></div>'; } ?>
        <?php if($this->config->get('product_social_share') != '0') { ?>
        <div class="share" style="padding-top:5px"><!-- AddThis Button BEGIN -->
          <div class="addthis_default_style"><a class="addthis_button_compact"><?php echo $text_share; ?></a> <a class="addthis_button_email"></a><a class="addthis_button_print"></a> <a class="addthis_button_facebook"></a> <a class="addthis_button_twitter"></a></div>
          <script type="text/javascript" src="//s7.addthis.com/js/250/addthis_widget.js"></script> 
          <!-- AddThis Button END --> 
        </div>
        <?php } ?>
      </div>
      <?php } ?>
      
      
      </div>
      
      <!-- End Right RIGHT -->
      
    </div>
  </div>
  <div id="tabs" class="htabs"><a href="#tab-description"><?php echo $tab_description; ?></a>
    <?php if ($attribute_groups) { ?>
    <a href="#tab-attribute"><?php echo $tab_attribute; ?></a>
    <?php } ?>
    <?php if ($review_status && $this->config->get('reviews') != '0') { ?>
    <a href="#tab-review"><?php echo $tab_review; ?></a>
    <?php } ?>
    <?php /*if (strlen($youtubeproduct)>2) { ?>
        <a href="#tab-youtube">Video</a>
    <? }*/ ?>
  </div>
         <?php /*if (strlen($youtubeproduct)>2) { ?>
  <div id="tab-youtube" class="tab-content">
	<!-- Add youtube -->
  
    
	  
     
            
				<table  style="width: 100%; border-collapse: collapse;">
				<tr>
				<?php if ($lmobile == false) { ?>
				<td style="text-align: center; height: 205px; width: 205px; background: URL('http://img.youtube.com/vi/<?php echo $youtubeproduct ; ?>/0.jpg') center no-repeat; ">
    				<a id="youtube" href="http://www.youtube.com/v/<?php echo $youtubeproduct.'&autoplay=1&autohide=1'  ; ?>">
				    <img border="0" src="image/run.png" width="70" height="70"></a>
				</td>
				<?php }else{ ?>
					<iframe class="youtube-player" type="text/html" width="853" height="480" src="http://www.youtube.com/embed/<?php echo $youtubeproduct ; ?>" frameborder="0">
				<?php } ?>
				</tr>
				</table>
           
		
        <!-- End Add youtube -->  
  </div>
   <?php }*/ ?>
  
  
  <div id="tab-description" class="tab-content"><?php echo $description; ?></div>
  <?php if ($attribute_groups) { ?>
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
  <?php if ($review_status && $this->config->get('reviews') != '0') { ?>
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
      <div class="right"><a id="button-review" class="button"><?php echo $button_continue; ?></a></div>
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
  
      <?php if ($products) { ?>

		<!-- Box -->
		
		<div class="box">
			
			<!-- Title -->
			
			<div class="box-heading"><?php echo $tab_related; ?></div>
			
			<!-- Content -->
			
			<div class="box-content">
			
					<!-- Products -->
					
					<div class="box-product">
					
					<?php foreach ($products as $product) {  ?>	
					<!-- Product -->
						
						<div>
							
							<!-- Hover PRODUCT -->
							
							<div class="absolute-hover-product">
								
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
					
					<!-- End Products -->
					
					<p class="clear"></p>
			
			</div>
		
		</div>
		
		<!-- End Box -->
		
    <?php } ?>
  
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
						$('#option-' + i).after('<p style="padding-top:6px"><span class="error" style="color:red">' + json['error']['option'][i] + '</span></p>');
					}
				}
			} 
			
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
				$('.success').fadeIn('slow');
					
				$('#cart').load('index.php?route=module/cart #cart > *');
				
				$('html, body').animate({ scrollTop: 0 }, 'slow'); 
			}	
		}
	});
});
//--></script>
<script type="text/javascript"><!--
$('#button-cart1').bind('click', function() {
	$.ajax({
		url: '',
		type: 'post',
		data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, information, .error').remove();
			
			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						$('#option-' + i).after('<p style="padding-top:6px"><span class="error" style="color:red">' + json['error']['option'][i] + '</span></p>');
					}
				}
			} 
			
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
				$('.success').fadeIn('slow');
					
				$('#cart').load('index.php?route=module/cart #cart > *');
				
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
 	  
	  </div>
	  
	  <?php if($column_right != '') { echo '<div class="grid-3 float-left">'.$column_right.'</div>'; } ?>

	<?php echo $content_bottom; ?>

</div>

<?php echo $footer; ?>

