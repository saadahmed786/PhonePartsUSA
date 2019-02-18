<?php echo $header; ?> 
<script>

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-24721193-1', 'auto'); // Insert your GA Web Property ID here, e.g., UA-12345-1
  ga('set','ecomm_prodid','<?php echo $product_id;?>_us'); // REQUIRED Product ID value, e.g., 12345, 67890
  ga('set','ecomm_pagetype','product'); // Optional Page type value, e.g., home, cart, purchase
  ga('set','ecomm_totalvalue',<?php echo $product_info['price'];?>); // Optional Total value, e.g., 99.95, 5.00, 1500.00
  ga('send', 'pageview');

</script>

<style>
  .btn{
    font-size:15px;
    display:block;
    font-weight:bold;
    background: #ffffff;
    background-image: -webkit-linear-gradient(top, #ffffff, #dedede);
    background-image: -moz-linear-gradient(top, #ffffff, #dedede);
    background-image: -ms-linear-gradient(top, #ffffff, #dedede);
    background-image: -o-linear-gradient(top, #ffffff, #dedede);
    background-image: linear-gradient(to bottom, #ffffff, #dedede);
    -webkit-border-radius: 8;
    -moz-border-radius: 8;
    border-radius: 8px;
    font-family: Arial;
    color: #333;
    font-size: 13pt;
    padding: 4px 20px;
    border: solid #dedede 1px;
    text-decoration: none;
  }
  .btn:hover {
    background: #efefef;
    background-image: -webkit-linear-gradient(top, #ffffff, #dedede);
    background-image: -moz-linear-gradient(top, #ffffff, #dedede);
    background-image: -ms-linear-gradient(top, #ffffff, #dedede);
    background-image: -o-linear-gradient(top, #ffffff, #dedede);
    background-image: linear-gradient(to bottom, #ffffff, #dedede);
    text-decoration: none;
    color:#333;
  }

  .btn2{
    -webkit-border-radius: 4;
    -moz-border-radius: 4;
    border-radius: 4px;
    font-family: Arial;
    color: #fff !important;
    font-size: 13pt;
    background: #3498db;
    padding: 5px 20px;
    border:0;
  }
  .btn2:hover {
    background: #036;
    color:#fff;
    background-image: -webkit-linear-gradient(top, #4d63b8, #4d63b8);
    background-image: -moz-linear-gradient(top, #4d63b8, #4d63b8);
    background-image: -ms-linear-gradient(top, #4d63b8, #4d63b8);
    background-image: -o-linear-gradient(top, #4d63b8, #4d63b8);
    background-image: linear-gradient(to bottom, #4d63b8, #4d63b8);
    text-decoration: none;
  }


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
.tooltip-mark {
  background: #FF8;
  border: 1px solid #888;
  border-radius: 10px;
  color: #000;
  font-size: 10px;
  padding: 1px 4px;
}
.tooltip {
  white-space: normal;
  background: #FFC;
  border: 1px solid #CCC;
  color: #000;
  display: none;
  font-size: 11px;
  font-weight: normal;
  line-height: 1.3;
  max-width: 300px;
  padding: 10px;
  position: absolute;
  text-align: left;
  z-index: 100;
}
.tooltip-mark:hover, .tooltip-mark:hover + .tooltip, .tooltip:hover {
  display: inline;
  cursor: help;
}
.tooltip, .ui-dialog {
  box-shadow: 0 6px 9px #AAA;
  -moz-box-shadow: 0 6px 9px #AAA;
  -webkit-box-shadow: 0 6px 9px #AAA;
}
</style>
<div class="popupbox condition">
  <span class="close">x</span>
  <div class="details">
    <h2>Item Condition:</h2>

    <h3>New - Like new condition</h3>
    <h5>OEM quality aftermarket or refurbished item</h5><br>

    <h3>Grade A - Minor cosmetic issues</h3>
    <h5>item is fully functional, but has 1-2 scratches or minor blemishes</h5><br>

    <h3>Grade B - Moderate cosmetic issues</h3>
    <h5>item is fully functional, but has 3-5 scratches or minor blemishes</h5><br>

    <h3>Grade C - Major cosmetic issues</h3>
    <h5>item is fully functional, but has several scratches or minor blemishes</h5><br>

    <h3>Grade D - Severe cosmetic issues</h3>
    <h5>item is fully functional, but has many severe scratches or minor blemishes</h5><br>
  </div>
</div>
<div class="popupbox quality">
  <span class="close">x</span>
  <div class="details">
    <h2>Quality:</h2>

    <h3>Premium - OEM LCD, OEM Touchscreen, OEM Flex Cable</h3><br>

    <h3>Standard - OEM LCD, Aftermarket Touchscreen, Aftermaket Flex Cable</h3><br>
    
    <h3>Economy Plus - Highest Quality Aftermarket Parts</h3><br>

    <h3>Economy - Aftermarket LCD, Aftermarket Touchscreen, Aftermarket Flex Cable</h3>
  </div>
</div>
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
          <?php if (strtolower($product_info['class']['main_category']) == 'replacement parts') { ?>
          <div class="newstock">
          <div class="new">
              <h2><?php echo $product_info['item_grade']; ?></h2>
              <span class="showinfo" onclick="$('.condition').fadeIn('400');">?</span>
              <h5><?php echo $qualities[$product_info['item_grade']]; ?></h5>
            </div>
            <?php if (strtolower($product_info['class']['name']) == 'screen-lcdtouchscreenassembly' || strtolower($product_info['class']['name']) == 'screen-touchscreen') { ?>
            <div class="qualityStock">
              <h3><?php echo (($product_info['quality']) ? $product_info['quality']: 'Standard') . ' Quality'; ?></h3>
              <a href="javascript:void(0);" onclick="$('.quality').fadeIn('400');"><small>What does this mean?</small></a>
            </div>
            <?php } ?>
          </div>
          <?php } else { ?>
          <img class="in-stock" src="/catalog/view/theme/bt_optronics/image/in-stock.png">
          <?php } ?>
          <!-- Add youtube -->

          <div class="price">



          </div>

          <!-- End Add youtube -->
          <div class="product_check_row_custom" style="/* margin-top: 20px;*/">
           <ul style="
           list-style: none;
           margin-top: 20px;
           display: block;
           /* margin: 0px; */
           /* margin-top: 20px; */
           ">
           <li  >
            <img src="<?php echo HTTPS_SERVER;?>catalog/view/theme/bt_optronics/image/check.png" border="0" width="17" height="14">
            Free Shipping on orders above $500
          </li>
          <li style="float:left">
            <img src="<?php echo HTTPS_SERVER;?>catalog/view/theme/bt_optronics/image/check.png" border="0" width="17" height="14">
            Leaves Our US Warehouse Within 24 Hours  &nbsp;<div style="float: right;display:none"><span class="tooltip-mark">?</span> <span class="tooltip">Orders ship with in 24 business hours after payment confirmation. Please note orders placed on weekends with standard shipping may be delayed an additional 24 hours.</span></div>
          </li>

          <li style="clear:both">
            <img src="<?php echo HTTPS_SERVER;?>catalog/view/theme/bt_optronics/image/check.png" border="0" width="17" height="14">
            Lowest Prices Online Guaranteed
          </li><li>
          <img src="<?php echo HTTPS_SERVER;?>catalog/view/theme/bt_optronics/image/check.png" border="0" width="17" height="14">
          60 Day Return Policy
        </li>

      </ul>

    </div>
    <?php } ?>

    <?php if ($price) { ?>
    <div class="price">
     <?php if ($youtubeproduct) { ?>
     <div class="youtubeVid">
      <table  style="width: 100%; border-collapse: collapse;">

        <tr>

          <?php
          $lmobile = true;
          if ($lmobile == false) { ?>

          <td style="text-align: center; height: 205px; width: 205px; background: URL('https://img.youtube.com/vi/<?php echo $youtubeproduct ; ?>/0.jpg') center no-repeat; background-size:100% 100% ">

            <a id="youtube" href="https://www.youtube.com/v/<?php echo $youtubeproduct.'&autoplay=1&autohide=1'  ; ?>">

              <img border="0" src="image/run.png" width="70" height="70"></a>

            </td>

            <?php }else{ ?>

            <iframe class="youtube-player" type="text/html" width="364" height="204.75" src="https://www.youtube.com/embed/<?php echo $youtubeproduct ; ?>" frameborder="0"></iframe>

            <?php } ?>

          </tr>

        </table>
      </div>
      <?php } ?>
      <?php if ($tax) { ?>
      <span class="price-tax"><?php echo $text_tax; ?> <?php echo $tax; ?></span>
      <?php } ?>
      <?php if ($points) { ?>
      <span class="reward"><small><?php echo $text_points; ?> <?php echo $points; ?></small></span>
      <?php } ?>
      <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tbody>

          <tr>
            <td width="15%"><img src="catalog/view/theme/bt_optronics/image/delivery-icon.png" height="32" width="32"></td>
            <td width="33%">
              <input style="width:100px;text-align:center" type="text" id="postcode" size="5" value="<?php echo ( $this->session->data['shipping_postcode']? $this->session->data['shipping_postcode']:''); ?>" placeholder="Zip Code" />

            </td>
            <td width="33%">        
              <span class="button_pink">  <input type="button" value="Get Shipping Cost" id="button-shipping-cost" class="button" style="width:222px" /></span></td>
            </tbody>
          </table>
          <br>
          <?php if ($sale_price){ ?> 
          <div class="discounts">
            <table cellpadding="0" cellspacing="0" border="0">
              <thead>
                <tr>
                  <th>Quantity</th>
                  <th>Our Sale Price</th>
                </tr>
              </thead>
              <tbody>
              <tr>
                  <td>1</td>
                  <td class="red"><?php echo $sale_price; ?></td>
              </tr>
                </tr>
              </tbody>
            </table>
          </div>
          <?php } else { ?>
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
          <?php } ?>
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

      <!--<div class="cart">
        
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
      </div>-->
      <div class="cart">

        <?php if ($quantity <= 0){ ?>
        <div class="attention" style="padding-top:20px; color:#F00;"><?php echo $oosn_info_text; ?></div>

        <?php if ($this->customer->isLogged()){
          $email = $this->customer->getEmail();
        }else {
          $email = '';
        } ?>

        <div class="warning" style="width:100%"> <div style="width:67%;margin:auto;"><input name="notifyemail" type="text" id="notifyemail" value="<?php echo $email;?>" style="width:170px;" /><span class="button_pink"><input name="notify" type="button" class="button" id="notify_btn" value="<?php echo $notify_button;?>" /></span><br><div id='loadingmessage' style='display:none'><img src='catalog/view/theme/default/image/loading.gif'/></div><br /><div id="msg" style="width:95%;margin-left:-20px;text-align:center;"></div></div></div><br>

        <?php }else { ?>
        <div style="text-align:center" class="action">
          <div class="quantity-box" style="width: 240px">
            <em class="qty" style="font-style:normal;font-size:28px;text-align:center;font-weight:bold">$0.00</em>
            <span class="text-field"><input type="text" data-min="1" size="2" maxlength="4" name="quantity" value="<?php echo $minimum; ?>" style="width: 70px; height: 35px; font-size: 28px;" onKeyUp="getProductPrice()"> <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />     </span>
            <span class="pluse-icon"><a href="javascript:QtyChange('+')"><img src="catalog/view/theme/bt_optronics/image/pluse-icon.png" alt="pluse-icon" style="height: 15px;"></a></span>
            <span class="less-icon" style="bottom: 11px;"><a href="javascript:QtyChange('-')"><img src="catalog/view/theme/bt_optronics/image/less-icon.png" alt="less-icon" style="height: 15px;"></a></span>
          </div>
          <input type="button" value="<?php echo $button_cart; ?>" id="button-cart" class="btn4" style="width:100%" />
        </div>
        <?php 
      }
      ?>
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
    <div style="margin-top:20px">
      <span id="_GUARANTEE_Kicker" name="_GUARANTEE_Kicker" type="Kicker Custom Product"></span>
    </div>
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

<!-- Google Code for Remarketing Tag -->
<script type="text/javascript">
// var google_tag_params = {
// ecomm_prodid: '<?php echo $product_id;?>_us',
// ecomm_pagetype: 'product',
// ecomm_totalvalue: <?php echo $product_info['price'];?>,
// };
</script>
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1020579853;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/1020579853/?value=0&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

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
				$( "#divTopright" ).fadeIn( "slow", function() {});
				$('#cart_menu span.s_grand_total').html(json['total_sum']);
				$('#cart_menu div.s_cart_holder').html(json['output']);
				$('#cart-total').html(json['total']);
				$('#cart-total2').html(json['total']);
				$('#cart').load('index.php?route=module/cart #cart > *');
				
				
				var myTotal = json['total'].split(" ");

       $(".mailnumber").html(myTotal[0]);
       if(getWidthBrowser() < 800)
       {
         $("html, body").animate({
          scrollTop: 0
        }, 600);
         $("#added_to_cart").html('<div  class="success" style="height:14px"><div style="float:left">Items added to Shopping Cart!</div> <img src="catalog/view/theme/default/image/tablet-cart-btn.png" onClick="window.location=\'/index.php?route=checkout/cart\'" style="height:23px;cursor:pointer;margin-top:-5px;margin-left:10px">   <img src="catalog/view/theme/default/image/tablet-checkout-btn.png" onClick="window.location=\'/index.php?route=checkout/checkout\'" style="height:23px;cursor:pointer;margin-top:-5px;margin-left:10px"><img src="catalog/view/theme/default/image/close.png" alt="" class="close"></div>');
         $("#added_to_cart").fadeIn();

       }

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
  $('#review .matrialPagination a').live('click', function() {
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
      <script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=true"></script>
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

    $('#button-shipping-cost').bind('click', function() {
      loadxPopup()
    });
    function get_Width_Height() {
      var array = new Array();
      if(getWidthBrowser() > 766){
        array[0] = 640;
        array[1] = 480;
      } else if(getWidthBrowser() < 767 && getWidthBrowser() > 480) {
        array[0] = 450;
        array[1] = 350;
      }else{
        array[0] = 300;
        array[1] = 300;
      }
      return array;
    }
    function loadxPopup()
    {
      $.ajax({
        url: 'index.php?route=checkout/cart/quote',
        type: 'post',
        data: 'country_id=223&postcode=' + encodeURIComponent($('#postcode').val()),
        dataType: 'json',   
        beforeSend: function() {
          $('#button-shipping-cost').attr('disabled', true);
          $('#button-shipping').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
        },
        complete: function() {
          $('#button-shipping-cost').attr('disabled', false);
          $('.wait').remove();
        },    
        success: function(json) {
          $('.success, .warning, .error').remove();     

          if (json['error']) {
            if (json['error']['warning']) {
              $('#notification').html('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');

              $('.warning').fadeIn('slow');

              $('html, body').animate({ scrollTop: 0 }, 'slow'); 
            } 

            if (json['error']['country']) {

        //$('select[name=\'country_id\']').after('<span class="error">' + json['error']['country'] + '</span>');
      } 

      if (json['error']['zone']) {
       alert(json['error']['zone']);return false;
        //  $('select[name=\'zone_id\']').after('<span class="error">' + json['error']['zone'] + '</span>');
      }

      if (json['error']['postcode']) {
        alert(json['error']['postcode']);return false;
        //  $('input[name=\'postcode\']').after('<span class="error">' + json['error']['postcode'] + '</span>');
      }         
    }

    if (json['shipping_method']) {
      html  = '<style>.shipping-holder{ padding:8px; border:1px solid #d6d6d6; }.shipping-methord{  text-align:center;}.hadding-bg{ padding:6px 10px 11px 18px; background:#cfe3f8; overflow:hidden;}.haddings-list{ margin:0; padding:0; list-style:none;}.haddings-list li{ float:left; font-size:10pt; color:#010915; margin:0 170px 0 0; font-weight:bold;}.haddings-list li:first-child{ margin:0 270px 0 0;}.haddings-list li:last-child{ margin:0;}.shippng-inn{ padding:22px 10px 22px 17px; background:#f3f6f9; margin:0 0 20px;}.first-list{ margin:0; padding-bottom:5px; list-style:none; float:left;}.first-list li{ margin:0 0 5px; font-size:10pt; float:left; font-weight:bold; margin:0 60px 0 0;}.first-list li:last-child{ margin:0;}.confirimation{ display:block; font-size:6pt; color:#b5b5b5; margin:0 0 0 20px;}.second-list{ margin:0 92px 0 0; padding:0; list-style:none; float:left;}.second-list li{ margin:0 0 6px; font-size:11pt; font-weight:bold;}.third-list{ margin:0; padding:0; list-style:none;}.third-list li{ margin:0 0 6px; font-size:11pt; font-weight:bold;}.btn3{ width:268px; font-size:17pt;padding:5px 27px;text-align:center;display:inline-block;color:#FFF}.title-span{width:300px;display:inline-flex;text-align:left}</style>';
      html += '<form action="<?php echo HTTP_SERVER; ?>index.php?route=checkout/cart" method="post" enctype="multipart/form-data">';
      html += '<div class="shipping-holder">';
      html += '<div class="shipping-methord">';

      html += '<div class="hadding-bg">';
      html += '<ul class="haddings-list">';
      html+= '<li>Shipping Method</li>';
      html+='<li style="margin-right:233px;">ETA (Business Days)</li>';
      html+='<li>Cost</li>';
      html+='</ul>';
      html+='</div>';
      html +='<div style="clear:both"></div><div class="shippng-inn" style="float:left" >';

      for (i in json['shipping_method']) {
          /*html += '<tr>';
          html += '  <td colspan="3"><b>' + json['shipping_method'][i]['title'] + '</b></td>';
          html += '</tr>';*/
          

          

          if (!json['shipping_method'][i]['error']) {
            console.log(JSON.stringify(json));
            for (j in json['shipping_method'][i]['quote']) {
             html += '<ul class="first-list">';

             if (json['shipping_method'][i]['quote'][j]['code'] == '<?php echo $shipping_method; ?>') {
              html += '<li ><input type="radio" name="shipping_method" value="' + json['shipping_method'][i]['quote'][j]['code'] + '" id="' + json['shipping_method'][i]['quote'][j]['code'] + '" checked="checked" />';
            } else {
              html += '<li ><input type="radio" name="shipping_method" value="' + json['shipping_method'][i]['quote'][j]['code'] + '" id="' + json['shipping_method'][i]['quote'][j]['code'] + '" />';
            }

            html += '  <span class="title-span"> ' + json['shipping_method'][i]['quote'][j]['title'] + '</span></li>';
            html += '<li><span class="title-span">'+(json['shipping_method'][i]['quote'][j]['delivery_time'])+' </span></li>';
            html += ' <li> ' + json['shipping_method'][i]['quote'][j]['text'] + '</li>';
              //html += '  <td style="text-align: left;"><label for="' + json['shipping_method'][i]['quote'][j]['code'] + '">' + json['shipping_method'][i]['quote'][j]['text'] + '</label></td>';
              //html += '</tr>';

              html +='</ul>'; 
            } 

          } else {
            html += '<tr>';
            html += '  <td colspan="3"><div class="error">' + json['shipping_method'][i]['error'] + '</div></td>';
            html += '</tr>';            
          }
        }
        html +='</div><div style="clear:both"></div>';
        
        //html += '  </table>';
        //html += '  <br />';
        html += '  <input type="hidden" name="next" value="shipping" />';
        
        <?php if ($shipping_method) { ?>
        /*html += '  <span class="button_pink">';
        html += '  <input type="submit" value="<?php echo $button_shipping; ?>" id="button-shipping" class="button" />';  
        html += '  </span>';*/
        
        //html += '<a href="#" id="button-shipping" class="btn btn2 btn3"><?php echo $button_shipping; ?></a>';
        html += '  <input type="submit" value="<?php echo $button_shipping; ?>" id="button-shipping" class="btn2 btn3" />'; 
        <?php } else { ?>
      /*  html += '  <span class="button_pink">';
        html += '  <input type="submit" value="<?php echo $button_shipping; ?>" id="button-shipping" class="button" disabled="disabled" />';  
        html += '  </span>';*/
        html += '  <input type="submit" value="Apply Shipping" id="button-shipping" class="btn2 btn3" disabled="disabled" />'; 
        <?php } ?>

        html += '</form>';
        
        $.colorbox({
          overlayClose: true,
          opacity: 0.5,
          width: '900px',
          height: get_Width_Height()[1],
          href: false,
          html: html
        });
        
        $('input[name=\'shipping_method\']').bind('change', function() {
          $('#button-shipping').attr('disabled', false);
        });
      }
    }
  }); 
}

//--></script> 

<script>
  function QtyChange(xtype)
  {
   $qty = $('input[name=quantity]');
   if(xtype=='+')
   {
    $qty.val(parseInt($qty.val())+1);
  }

  if(xtype=='-' && $qty.val()>1)
  {

    $qty.val(parseInt($qty.val())-1);

  }
  getProductPrice();

}
function getProductPrice()  {
  var product_id = $('input[name=product_id]').val();
  var quantity = $('input[name=quantity]').val();
  if (quantity === '0') {
    quantity = 1;
    $('input[name=quantity]').val('1');
  }

  $.ajax({
    url: 'index.php?route=product/product/getUpdatedPrice',
    type: 'post',
    data: {product_id:product_id,quantity:quantity},
    dataType: 'json',
    beforeSend: function() {
     // $('.success, .warning').remove();
      //$('#button-review').attr('disabled', true);
      $('.quantity-box .qty').html('<small>Updating...</small>');
    },
    complete: function() {
     // $('#button-review').attr('disabled', false);
      //$('.attention').remove();
    },
    success: function(json) {
      $('information, .error').remove();
      if (json['error']) {

      }  

      if (json['success']) {
        $('.quantity-box .qty').html(json['success']); 
      }
    }
  });
}
$(document).ready(function(){

  getProductPrice();
});
$('.close').click(function(e) {
  e.preventDefault();
  $('.popupbox').fadeOut('400');
});
</script>
<?php echo $footer; ?>
