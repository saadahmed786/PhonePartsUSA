<div id="cart">
  <div class="heading">
    <h4><?php echo $heading_title; ?> - <span id="cart-total"><?php echo $text_items;?></span></h4></div>
  <div class="content">
  <div class="bg_content">
    <?php if ($products || $vouchers) { ?>
    <div class="mini-cart-info">
      <table>
        <?php foreach ($products as $product) { ?>
        <tr>
          <td class="image">
		  <?php if ($product['thumb']) { ?>
            <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name'];?>" title="<?php echo $product['name']; ?>" style="-moz-transform:scale(0.8);-webkit-transform:scale(0.8); transform:scale(0.8);" /></a>
            <?php } ?>
		  </td>
          <td class="name">
			<div class="name"><a href="<?php echo $product['href']; ?>" style="font-size: 12px;"><?php echo $product['name']; ?></a></div>
            <div class="subs">
              <?php foreach ($product['option'] as $option) { ?>
              - <small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small><br/><br/>
              <?php } ?>
            </div>
            <div class="total"><?php echo $product['price']; ?>&nbsp;&nbsp;x&nbsp;<?php echo $product['quantity']; ?></div>
			<div class="remove">
			<a onclick="(getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') ? location = 'index.php?route=checkout/cart&amp;remove=<?php echo $product['key']; ?>' : $('#floatCart').load('index.php?route=module/cart&amp;remove=<?php echo $product['key']; ?>',function(){updateCartButtons();})"><?php echo $button_remove; ?></a>
			</div>
		</td>
          
        </tr>
        <?php } ?>
        <?php foreach ($vouchers as $voucher) { ?>
        <tr>
          <td class="image">
		  </td>
          <td class="name">
			<div class="name"><span><?php echo $voucher['description']; ?></span></div>
			  <div class="total"><?php echo $voucher['amount']; ?></div>
			  <div class="quantity">x&nbsp;1</div>
			  <div class="remove">
				<a onclick="(getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') ? location = 'index.php?route=checkout/cart&amp;remove=<?php echo $voucher['key']; ?>' : $('#floatCart').load('index.php?route=module/cart&amp;remove=<?php echo $voucher['key']; ?>' + ' #cart > *',function(){updateCartButtons();});');"><?php echo $button_remove; ?></a>
			  </div>
		  </td>
        </tr>
        <?php } ?>
      </table>
    </div>
    
    <?php } else { ?>
    <div class="empty"><?php echo $text_empty; ?></div>
    <?php } ?>
  </div>
  
  <!--SPLIT FOR PRODUCT PAGE-->
  <div class="mini-cart-total orig-cart">
      <table>
        <?php foreach ($totals as $total) { ?>
        <tr>
          <td class="left<?php echo($total==end($totals) ? ' last' : ''); ?>"><?php echo $total['title']; ?></td>
          <td class="right<?php echo($total==end($totals) ? ' last' : ''); ?>"><?php echo $total['text']; ?></td>
        </tr>
        <?php } ?>
      </table>
    </div>
    <div class="checkout"><a class="button_black" href="<?php echo $cart; ?>"><span><?php echo $text_cart; ?></span></a>  <a class="button_pink" href="<?php echo $checkout; ?>"><span><?php echo $text_checkout; ?></span></a>
	
    <!-- <a id="ppx" href="http://dev.phonepartsusa.com/index.php?route=payment/paypal_express/SetExpressCheckout"><img src="catalog/view/theme/default/image/EC-button.gif" alt="Paypal Express"></a> -->
  

</div>
  <!--SPLIT FOR PRODUCT PAGE-->
	</div>
  </div>
  
</div>

<script type="text/javascript">
$(document).ready(function() {
$('#cart-total2').html('<?php echo $_SESSION['thistextitems'];?>');
  
	if(getWidthBrowser() < 959) {
		$('#cart > .heading a').live('click', function() {
			if($('#cart').hasClass('my-active')){
				$('#cart').removeClass('active');
				$('#cart').removeClass('my-active');
			} else {
				$('#cart').addClass('my-active');
			}
		});
	}
});
</script>