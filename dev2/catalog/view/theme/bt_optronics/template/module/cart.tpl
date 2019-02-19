<style>
.btn-cart{
	
	display:block;
	font-weight:bold;
  background: #ffffff;
  background-image: -webkit-linear-gradient(top, #ffffff, #dedede);
  background-image: -moz-linear-gradient(top, #ffffff, #dedede);
  background-image: -ms-linear-gradient(top, #ffffff, #dedede);
  background-image: -o-linear-gradient(top, #ffffff, #dedede);
  background-image: linear-gradient(to bottom, #ffffff, #dedede);
  -webkit-border-radius: 4;
  -moz-border-radius: 4;
  border-radius: 4px;
  /* font-family: Arial;*/
  color: #333;
  font-size: 13pt;
  padding: 5px 20px;
  border: solid #dedede 1px;
  text-decoration: none;
}
.btn-cart:hover {
  background: #efefef;
  background-image: -webkit-linear-gradient(top, #ffffff, #dedede);
  background-image: -moz-linear-gradient(top, #ffffff, #dedede);
  background-image: -ms-linear-gradient(top, #ffffff, #dedede);
  background-image: -o-linear-gradient(top, #ffffff, #dedede);
  background-image: linear-gradient(to bottom, #ffffff, #dedede);
  text-decoration: none;
  color:#333;
}
.btn-cart2{
  -webkit-border-radius: 4;
  -moz-border-radius: 4;
  border-radius: 4px;
  
  color: #fff !important;
  font-size: 13pt;
  background: #3498db;
  padding: 5px 20px;
  border:0;
}
.btn-cart2:hover {
  background: #036;
  color:#fff;
  background-image: -webkit-linear-gradient(top, #4d63b8, #4d63b8);
  background-image: -moz-linear-gradient(top, #4d63b8, #4d63b8);
  background-image: -ms-linear-gradient(top, #4d63b8, #4d63b8);
  background-image: -o-linear-gradient(top, #4d63b8, #4d63b8);
  background-image: linear-gradient(to bottom, #4d63b8, #4d63b8);
  text-decoration: none;
}
</style>
<div id="cart">
  <div class="heading">
    <h4><?php echo $heading_title; ?> - <span id="cart-total"><?php echo $text_items;?></span></h4></div>
    <div class="content">
      <div class="bg_content">
        <?php if ($products || $vouchers) { ?>
        <div class="mini-cart-info" style="margin:0px">
          <table>
            <?php foreach ($products as $product) { ?>
            <?php
            //Putting Signature in the End
            if ($product['model'] == "SIGN") {
              $sign = '<tr>';
              $sign .= '<td class="image">';
              $sign .= '<a href="javascript:void(0)"></a>';
              $sign .= '</td>';
              $sign .= '<td class="name">';
              $sign .= '<div class="name"><a href="javascript:void(0)" style="font-size: 12px;">'.$product['name'].'</a></div>';
              $sign .= '<div class="subs"></div>';
              $sign .= '<div class="total">'.$product['price'].'</div>';
              $sign .= '<div class="remove"></div>';
              $sign .= '</td></tr>';
              continue;
            }
            ?>
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
               <a onclick="(getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') ? location = 'index.php?route=checkout/cart&amp;remove=<?php echo $product['key']; ?>' : $('#cart').load('index.php?route=module/cart&amp;remove=<?php echo $product['key']; ?>')"><?php echo $button_remove; ?></a>
             </div>
           </td>

         </tr>
         <?php } ?>
         <!-- Putting Signature in the End -->
         <?php echo $sign; ?>
         <?php foreach ($vouchers as $voucher) { ?>
         <tr>
          <td class="image">
          </td>
          <td class="name">
           <div class="name"><span><?php echo $voucher['description']; ?></span></div>
           <div class="total"><?php echo $voucher['amount']; ?></div>
           <div class="quantity">x&nbsp;1</div>
           <div class="remove">
            <a onclick="(getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') ? location = 'index.php?route=checkout/cart&amp;remove=<?php echo $voucher['key']; ?>' : $('#cart').load('index.php?route=module/cart&amp;remove=<?php echo $voucher['key']; ?>' + ' #cart > *');');"><?php echo $button_remove; ?></a>
          </div>
        </td>
      </tr>
      <?php } ?>
    </table>
  </div>
  <div class="mini-cart-total">
    <table>
      <?php foreach ($totals as $total) { ?>
      <tr>
        <td class="left<?php echo($total==end($totals) ? ' last' : ''); ?>"><?php echo $total['title']; ?></td>
        <td class="right<?php echo($total==end($totals) ? ' last' : ''); ?>"><?php echo $total['text']; ?></td>
      </tr>
      <?php } ?>
    </table>
  </div>
  <div class="checkout"><a class="btn-cart" href="<?php echo $cart; ?>" style="float:left;margin-right:5px"><?php echo $text_cart; ?></a>  <a class="btn-cart btn-cart2" href="<?php echo $checkout; ?>" style="float:left;margin-right:5px"><?php echo $text_checkout; ?></a></div>

  <div class="box" style="margin-top:20px">


    <div class="box-content" style="text-align:center;">

      <a id="ppx" href="<?php echo $store_url . 'index.php?route=payment/paypal_express_new/SetExpressCheckout';?>"><img src="catalog/view/theme/default/image/EC-button.gif" alt="Paypal Express" /></a>
    </div>
  </div>

  <?php } else { ?>
  <div class="empty"><?php echo $text_empty; ?></div>
  <?php } ?>
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