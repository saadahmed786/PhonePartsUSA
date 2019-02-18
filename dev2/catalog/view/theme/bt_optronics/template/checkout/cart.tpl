<?php echo $header; ?>
<style>
/* @Reset
********************************************************************************************
********************************************************************************************/


/* @phon cart css
********************************************************************************************
********************************************************************************************/

@media (min-width: 1200px) {

}

/* Portrait tablets and medium desktops */
@media (min-width: 992px) and (max-width: 1199px) {

}

/* Portrait tablets and small desktops */
@media (min-width: 768px) and (max-width: 800px) {
  .btn-holder { margin-right: 18px;}
  .box{margin-right:18px;}
}

.cart-holder{ /*max-width:722px;*/ margin:0 0 20px;}
.cart-anchor { color: #237dc1 !important; text-decoration: none; transition: all 0.5s; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; outline: none;}
.cart-anchor:hover { color: #237dc1; text-decoration: underline; }
.top-bar{ margin:0 0 24px;}
.cart-holder h1{ margin:0 0 14px; padding:0; font-size:22pt; display:block;color:#000;}
.btn-holder{ overflow:hidden;}
.btn-holder ul{ padding:0; margin:0; list-style:none; float:right;}
.btn-holder ul li{ float:left; margin:0 0 0 5px;}
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
#boss_menu{
  display:none;	
}
#content{
  margin-left:0px	!important;
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
.items-details{border:1px solid #e7e6e9; margin:0 0 7px;} 
.haddings-bg{ padding:5px 131px 5px 21px; overflow:hidden; background:#e7e6e9;}
.items-details h4{ margin:0; float:left;}
.details-list{ margin:0; padding:0; list-style:none; float:right;}
.details-list li{ margin:0 36px 0 0; float:left;}

.items-details2{ padding:11px 74px 9px 32px; overflow:hidden; }
.img-area{ float:left; width:179px;}
.product-img{ display:block; float:left; margin:0 30px 0 0;}
.product-img img{width:70px}
.model{ display:block; color:#727376; font-size:10pt; margin:0 0 7px;width:330px}
.product-name{ display:block; font-size:11pt; font-weight:bold; color:#000;width:330px}
.text-field{ height:25px; width:34px; display:flex;}
.details-list2 li{ color:#000;}
.details-list2 li:first-child{ color:#727376;width:45px}
.details-list2 li:last-child{ margin:0;}
.update{ display:block; margin-top:5px}

.bottom-area{ overflow:hidden; background:#e6e7e9; padding:5px; margin:0 0 18px;}
.form-inn1{ margin:0 0 50px 0;}
.form-holder{ width:223px; overflow:hidden; float:left;}
.form-holder h5{ margin:0 0 5px; font-size:9pt;}
.form-holder input{ width:100%; margin:0 0 5px;}
.apply-btn{ font-size:9pt; font-weight:bold; width:67px; text-align:center; display:block; background:#f2f2f2; color:#333; border:1px solid #dedede; padding:5px 0;}
.apply-btn:hover{ text-decoration:none; color:#333;}
.form-inn2 input{ width:116px;}
.form-inn2 .apply-btn{ width:106px;}

.result-area{ padding:26px 36px 86px 43px; float:right; width:278px; background:#fff;height:126px}
.result-list{ padding:0; margin:0; list-style:none;}
.result-list li{ width:271px; margin:0 0 5px;}
.result-list li:last-child{ font-weight:bold;  border-top:1px solid #e6e7e9; margin:10px 0 0; padding:10px 0 0;}
.pull-right{ float:right;}

/* @chipping methord css
********************************************************************************************
********************************************************************************************/

</style>
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <a <?php echo(($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''); ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>

<?php if ($attention) { ?>
<div class="attention"><?php echo $attention; ?><img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>
<?php } ?>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content">
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
    <div class="cart-holder">
      <div class="top-bar">
       <h1><?php echo $heading_title; ?> <?php if ($weight) { ?>
        &nbsp;(<?php echo $weight; ?>)
        <?php } ?></h1>
        <div class="btn-holder">
         <ul>
           <li><a class="btn" href="<?php echo $continue;?>"><?php echo $button_shopping;?></a></li>
           <li><a class="btn btn2" href="<?php echo $checkout; ?>"><?php echo $button_checkout; ?></a></li>
         </ul>
       </div>
     </div>
     
     <?php if ($success) { ?>
     <div class="success"><?php echo $success; ?><img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>
     <?php } ?>
     <?php if ($error_warning) { ?>
     <div class="warning"><?php
/*$stock_message = 'Please update stock:<br><br>';
$k= 1;
 foreach($products as $xproduct)
{
if($xproduct['stock']==false)
{
	$stock_message.=$k.') '.$xproduct['model'].', only '.$xproduct['stock_available'].' pcs are available<br>';
$k++;
}


}
echo $stock_message;*/
echo $error_warning;
?></div>
<?php } ?>


<div class="items-details">
 <div class="haddings-bg">
   <h4>Items</h4>
   <ul class="details-list">
     <li>Item Price</li>
     <li>Qty</li>
     <li>Total</li>
   </ul>
 </div>
 <?php foreach ($products as $product) { ?>
 <?php
 if ($product['model'] == "SIGN") {
  $sign = '<div class="items-details2 "> <div class="img-area"> <span class="product-img"><a href="javascript:void(0)"><img src="'.$product['thumb'].'" alt="Sign" title="'.$product['name'].'" /></a></span>';
  $sign .= '<span class="model">'.$product['model'].'</span>';
  $sign .= '<span class="product-name"><a href="javascript:void(0)">'.$product['name'].'</a><div></div></div>';
  $sign .= '<ul class="details-list details-list2" >';
  $sign .= '<li>'.$product['price'].'</li>';
  $sign .= '<li class="text-field" style="width:;"><div class="display:block"></div></li>';
  $sign .= '<li>'.$product['total'].'</li>';
  $sign .= '<li><a style="visibility: hidden;">Remove</a></li></ul></div>';
  continue;
}
?>
<div class="items-details2 ">
 <div class="img-area">
   <span class="product-img"><?php if ($product['thumb']) { ?>
    <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a>
    <?php } ?></span>
    <span class="model"><?php echo $product['model'];?></span>
    <span class="product-name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a> 


      <?php if (!$product['stock']) { ?>
      <span class="stock" rel="tipsy" style="color:red" title="<?php if($product['stock_available']>0) echo 'Sorry, only '.$product['stock_available'].' pcs are available'; else echo 'Stock is empty for this product'; ?>">***</span>
      <?php } ?>
    </span>
    <div>

      <?php foreach ($product['option'] as $option) { ?>
      - <small><?php echo $option['name']; ?>: <?php echo $option['value']; ?></small><br />
      <?php } ?>
    </div>
    <?php if ($product['reward']) { ?>
    <small><?php echo $product['reward']; ?></small>
    <?php } ?>
  </div>
  <ul class="details-list details-list2" >
   <li><?php echo $product['price']; ?></li>
   <li class="text-field"><div class="display:block"><span style="display:block"><input type="text" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" size="1" style="width:35px;text-align:center"></span>
    <span class="update"> <a  style="color:#237dc1;" onClick="$(this).next('input[type=image]').click();">Update</a> <input type="image" style="display:none" src="catalog/view/theme/default/image/update.png" alt="<?php echo $button_update; ?>" title="<?php echo $button_update; ?>" /></span>

  </div>
</li>
<li><?php echo $product['total']; ?></li>
<li><a href="<?php echo $product['remove']; ?>" class="cart-anchor"><?php echo $button_remove; ?></a></li>
</ul>
</div>
<?php } ?>
<?php echo $sign; ?>
<?php
foreach ($vouchers as $vouchers) {
 ?>
 <div class="items-details2 ">
   <div class="img-area">
     <span class="product-img"> </span>
     <span class="model"> </span>
     <span class="product-name"><?php echo $vouchers['description']; ?></span>
     <ul class="details-list details-list2" >
       <li><?php echo $vouchers['amount']; ?></li>
       <li class="text-field"><div class="display:block"><span style="display:block"><input type="text" name="" value="1" disabled size="1" style="width:35px;text-align:center"></span>

       </div>
     </li>
     <li><?php echo $vouchers['amount']; ?></li>
     <li><?php echo $vouchers['amount']; ?></li>
   </ul>
 </div>
 
 <?php
}


?>

</div>
</form>
<div class="bottom-area">
  <?php if ($coupon_status || $voucher_status || $reward_status || $shipping_status) { ?>
  <div class="form-holder">
   <div class="form-inn1">
    <?php if ($coupon_status) { ?>
    <h5>Apply a Code</h5>
    
    <input type="radio" name="next" value="coupon" id="use_coupon" checked="checked" style="display:none"/>
    
    
    <form id="voucher_frm" action="<?php echo $action; ?>" onsubmit="" method="post" enctype="multipart/form-data">
      <?php
      if(isset($coupon) and $coupon!='')
      {
        $xvoucher = $coupon;
      }
      elseif(isset($voucher) and $voucher!='')
      {
        $xvoucher = $voucher;
      }
      else
      {
        $xvoucher = '';
      }
      ?>
      
      <input type="text" name="coupon" value="<?php //echo $xvoucher; ?>" />
      <input type="hidden" name="next" value="coupon" />
      <br/>
      <input class="apply-btn" type="button" onclick="$('#voucher_frm').submit();" value="Apply" style="width:30%" />
    </form>
    
  </div>
  <?php }
  ?>
  <?php if ($shipping_status) { ?>
  <div class="form-inn2">
   <h5>Enter ZIP Code To Calculate Shipping Rates</h5>
   <?php if ($next == 'shipping') { ?>
   <input type="radio" name="next" value="shipping" id="shipping_estimate" checked="checked" style="display:none" />
   <?php } else { ?>
   <input type="radio" name="next" value="shipping" id="shipping_estimate" style="display:none" />
   <?php } ?>
   <select name="country_id" style="display:none">
    <option value=""><?php echo $text_select; ?></option>
    <?php foreach ($countries as $country) { ?>
    <?php if ($country['country_id'] == $country_id) { ?>
    <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
    <?php } else { ?>
    <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
    <?php } ?>
    <?php } ?>
  </select>
  <select name="zone_id" id="zone_id" style="display:none">
  </select>
  <input type="text" id="postcode" name="postcode" value="<?php echo $postcode; ?>" />
  <input type="button" value="<?php echo $button_quote; ?>" id="button-quote" class="apply-btn" style="width:45%" />
</div>
<?php 
}
?>
</div>
<?php
}
?>
<div class="result-area">
 <ul class="result-list">

  <?php foreach ($totals as $total) { ?>
  <li><div style="width:220px"><?php echo $total['title'];?></div><span class="pull-right" style="margin-top:-14px"><?php echo $total['text'];?></span></li>
  <?php
}
?>


</ul>
</div>
</div>
<div class="btn-holder">
 <ul>
   <li><a class="btn" href="<?php echo $continue;?>"><?php echo $button_shopping;?></a></li>
   <li><a class="btn btn2" href="<?php echo $checkout; ?>"><?php echo $button_checkout; ?></a></li>
 </ul>
</div>
</div>
</div>

  <?php if ($coupon_status || $voucher_status || $reward_status || $shipping_status) { /* ?>
  <div class="content choice_shopping_cart">
  <h2><?php echo $text_next; ?></h2>
    <p><?php echo $text_next_choice; ?></p>
    <table class="radio">
      <?php if ($coupon_status) { ?>
      <tr class="highlight">
        <td><?php if ($next == 'coupon') { ?>
          <input type="radio" name="next" value="coupon" id="use_coupon" checked="checked" />
          <?php } else { ?>
          <input type="radio" name="next" value="coupon" id="use_coupon" />
          <?php } ?></td>
        <td><label for="use_coupon"><?php echo $text_use_coupon; ?></label></td>
      </tr>
      <?php } ?>
      <?php if ($voucher_status) { ?>
      <tr class="highlight">
        <td><?php if ($next == 'voucher') { ?>
          <input type="radio" name="next" value="voucher" id="use_voucher" checked="checked" />
          <?php } else { ?>
          <input type="radio" name="next" value="voucher" id="use_voucher" />
          <?php } ?></td>
        <td><label for="use_voucher"><?php echo $text_use_voucher; ?></label></td>
      </tr>
      <?php } ?>
      <?php if ($reward_status) { ?>
      <tr class="highlight">
        <td><?php if ($next == 'reward') { ?>
          <input type="radio" name="next" value="reward" id="use_reward" checked="checked" />
          <?php } else { ?>
          <input type="radio" name="next" value="reward" id="use_reward" />
          <?php } ?></td>
        <td><label for="use_reward"><?php echo $text_use_reward; ?></label></td>
      </tr>
      <?php } ?>
      <?php if ($shipping_status) { ?>
      <tr class="highlight">
        <td><?php if ($next == 'shipping') { ?>
          <input type="radio" name="next" value="shipping" id="shipping_estimate" checked="checked" />
          <?php } else { ?>
          <input type="radio" name="next" value="shipping" id="shipping_estimate" />
          <?php } ?></td>
        <td><label for="shipping_estimate"><?php echo $text_shipping_estimate; ?></label></td>
      </tr>
      <?php } ?>
    </table>
  </div>
  
  
  <div class="cart-module">
    <div id="coupon" class="content" style="display: <?php echo ($next == 'coupon' ? 'block' : 'none'); ?>;">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
        <?php echo $entry_coupon; ?><br/>
        <input type="text" name="coupon" value="<?php //echo $coupon; ?>" />
        <input type="hidden" name="next" value="coupon" />
        <br/>
        <span class="button_pink"><input type="submit" value="<?php echo $button_coupon; ?>" class="button" /></span>
      </form>
    </div>
    <div id="voucher" class="content" style="display: <?php echo ($next == 'voucher' ? 'block' : 'none'); ?>;">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
        <?php echo $entry_voucher; ?><br/>
        <input type="text" name="voucher" value="<?php //echo $voucher; ?>" />
        <input type="hidden" name="next" value="voucher" />
        <br/>
        <span class="button_pink"><input type="submit" value="<?php echo $button_voucher; ?>" class="button" /></span>
      </form>
    </div>
    <div id="reward" class="content" style="display: <?php echo ($next == 'reward' ? 'block' : 'none'); ?>;">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
        <?php echo $entry_reward; ?><br/>
        <input type="text" name="reward" value="<?php echo $reward; ?>" />
        <input type="hidden" name="next" value="reward" />
        <br/>
        <span class="button_pink"><input type="submit" value="<?php echo $button_reward; ?>" class="button" /></span>
      </form>
    </div>
    <div id="shipping" class="content" style="display: <?php echo ($next == 'shipping' ? 'block' : 'none'); ?>;">
      <p><?php echo $text_shipping_detail; ?></p>
      <table>
        <tr>
          <td><?php echo $entry_country; ?><span class="required">*</span> </td></tr>
          <tr><td><select name="country_id">
              <option value=""><?php echo $text_select; ?></option>
              <?php foreach ($countries as $country) { ?>
              <?php if ($country['country_id'] == $country_id) { ?>
              <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_zone; ?><span class="required">*</span> </td></tr>
		<tr>
          <td><select name="zone_id">
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_postcode; ?><span id="postcode-required" class="required">*</span> </td></tr>
        <tr>  
		  <td><input type="text" name="postcode" value="<?php echo $postcode; ?>" /></td>
        </tr>
      </table>
      <span class="button_pink"><input type="button" value="<?php echo $button_quote; ?>" id="button-quote" class="button" /></span>
    </div>
  </div>
  <?php */ } ?>
  <div class="cart-total" style="display:none">
    <table id="total">
      <?php foreach ($totals as $total) { ?>
      <tr>
        <td class="left<?php echo ($total==end($totals) ? ' last' : ''); ?>"><?php echo $total['title']; ?>:</td>
        <td class="right<?php echo ($total==end($totals) ? ' last' : ''); ?>"><?php echo $total['text']; ?></td>
      </tr>
      <?php } ?>
    </table>
  </div>
  <div class="buttons shopping_cart_button" style="display:none">
    <div class="right"><a href="<?php echo $checkout; ?>" class="button_pink"><span><?php echo $button_checkout; ?></span></a></div>
    <div class="right"><a href="<?php echo $continue; ?>" class="button_black"><span><?php echo $button_shopping; ?></span></a></div>
  </div>
  <div id="vif-add-bottom"><?php echo $content_top; ?></div>
  <div style="margin-top:20px">

    <span id="_GUARANTEE_Kicker" name="_GUARANTEE_Kicker" type="Kicker Custom Cart"></span>
  </div>
  
  
  <?php echo $content_bottom; ?></div>
  <script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=true"></script>
  <script type="text/javascript"><!--
  $('input[name=\'next\']').bind('change', function() {
   $('.cart-module > div').hide();
   
   $('#' + this.value).show();
 });
  //--></script>
  <?php if ($shipping_status) { ?>
  <script type="text/javascript"><!--
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
$('#button-quote').live('click', function() {
	var zip = $("#postcode").val();
  var lat;
  var lng;
  
  
  $('#button-quote').attr('disabled', true);
  $('#button-quote').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
  
  var geocoder = new google.maps.Geocoder();
  geocoder.geocode({ 'address': zip }, function (results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      geocoder.geocode({'latLng': results[0].geometry.location}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[1]) {
            var loc = getCityState(results);
            
          }
        }
      });
    }
  }); 
  
  function getCityState(results)
  {
    var a = results[0].address_components;
    var city, state;
    for(i = 0; i <  a.length; ++i)
    {
     var t = a[i].types;
     if(compIsType(t, 'administrative_area_level_1'))
              state = a[i].long_name; //store the state
            else if(compIsType(t, 'locality'))
              city = a[i].long_name; //store the city
          }
          console.log(city + ', ' + state)
	//	$("input[name=city]").val(city);

  $('select[name=zone_id]').find('option:contains('+state+')').attr('selected','selected');
}

function compIsType(t, s) { 
 for(z = 0; z < t.length; ++z) 
  if(t[z] == s)
   return true;
 return false;
}

setTimeout(function() { loadxPopup(); }, 4000);	
});

function loadxPopup()
{
  $.ajax({
    url: 'index.php?route=checkout/cart/quote',
    type: 'post',
    data: 'country_id=' + $('select[name=\'country_id\']').val() + '&zone_id=' + $('select[name=\'zone_id\']').val() + '&postcode=' + encodeURIComponent($('input[name=\'postcode\']').val()),
    dataType: 'json',		
    beforeSend: function() {
			//$('#button-quote').attr('disabled', true);
			//$('#button-quote').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('#button-quote').attr('disabled', false);
			$('.wait').remove();
		},		
		success: function(json) {
			$('.success, .warning, .attention, .error').remove();			

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
				//	$('select[name=\'zone_id\']').after('<span class="error">' + json['error']['zone'] + '</span>');
      }
      
      if (json['error']['postcode']) {
				//	$('input[name=\'postcode\']').after('<span class="error">' + json['error']['postcode'] + '</span>');
      }					
    }
    
    if (json['shipping_method']) {
      html  = '<style>.shipping-holder{ padding:8px; border:1px solid #d6d6d6; }.shipping-methord{  text-align:center;}.hadding-bg{ padding:6px 10px 11px 18px; background:#cfe3f8; overflow:hidden;}.haddings-list{ margin:0; padding:0; list-style:none;}.haddings-list li{ float:left; font-size:10pt; color:#010915; margin:0 170px 0 0; font-weight:bold;}.haddings-list li:first-child{ margin:0 270px 0 0;}.haddings-list li:last-child{ margin:0;}.shippng-inn{ padding:22px 10px 22px 17px; background:#f3f6f9; margin:0 0 20px;}.first-list{ margin:0; padding-bottom:5px; list-style:none; float:left;}.first-list li{ margin:0 0 5px; font-size:10pt; float:left; font-weight:bold; margin:0 60px 0 0;}.first-list li:last-child{ margin:0;}.confirimation{ display:block; font-size:6pt; color:#b5b5b5; margin:0 0 0 20px;}.second-list{ margin:0 92px 0 0; padding:0; list-style:none; float:left;}.second-list li{ margin:0 0 6px; font-size:11pt; font-weight:bold;}.third-list{ margin:0; padding:0; list-style:none;}.third-list li{ margin:0 0 6px; font-size:11pt; font-weight:bold;}.btn3{ width:268px; font-size:17pt;padding:5px 27px;text-align:center;display:inline-block;}.title-span{width:300px;display:inline-flex;text-align:left}</style>';
      html += '<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">';
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
				html += '  <input type="submit" value="<?php echo $button_shipping; ?>" id="button-shipping" class="btn btn2 btn3" />';	
				<?php } else { ?>
			/*	html += '  <span class="button_pink">';
				html += '  <input type="submit" value="<?php echo $button_shipping; ?>" id="button-shipping" class="button" disabled="disabled" />';	
				html += '  </span>';*/
				html += '  <input type="submit" value="<?php echo $button_shipping; ?>" id="button-shipping" class="btn btn2 btn3" disabled="disabled" />';	
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
<script type="text/javascript"><!--
$('select[name=\'country_id\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=checkout/cart/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#postcode-required').show();
			} else {
				$('#postcode-required').hide();
			}
			
			html = '<option value=""><?php echo $text_select; ?></option>';
			
			if (json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
         html += '<option value="' + json['zone'][i]['zone_id'] + '"';
         
         if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
           html += ' selected="selected"';
         }
         
         html += '>' + json['zone'][i]['name'] + '</option>';
       }
     } else {
      html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
    }
    
    $('select[name=\'zone_id\']').html(html);
  },
  error: function(xhr, ajaxOptions, thrownError) {
   alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
 }
});
});

$('select[name=\'country_id\']').trigger('change');
$('span[rel=tipsy]').tipsy({gravity:'w',fade:true});
//--></script>
<?php } ?>


<?php echo $footer; ?>
