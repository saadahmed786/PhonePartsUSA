<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<!-- <div class="warning">We advise customers to select Fedex shipping methods to avoid any delivery delays during holidays.</div>
 -->
<?php if ($shipping_methods) { ?>
<p><?php echo $text_shipping_method; ?></p>
<table class="radio">
  <tr>
    <td><input type="checkbox" <?= ($sign_product_exist) ? 'checked="checked"': '';?> name="signProduct" id="addSign"/></td>
    <td><div style="float: left">Require Signature At Delivery <span style="margin-left: 10px;">$3.00</span></div>
      <div style="float: left; margin-left: 10px;"><span class="tooltip-mark">?</span> <span class="tooltip">Prevent yourself from non-delivery! (a very very rare event, but it does happen) Mailmen and women often leave packages on the porch, next to the door or common areas. Theft does occur! We do not take liability on missing or lost packages if shown as delivered in carrier tracking. We recommend all customers require signature for error-free delivery.</span></div>
    </td>
    <td></td>
  </tr>
  <script type="text/javascript">
$('#addSign').on('click', function() {
  if (this.checked) {
    //addToCart()
    $.ajax({
    url: 'index.php?route=checkout/cart/addSignature',
    type: 'post',
    data: 'product_id=<?= $sign_product;?>&quantity=1',
    dataType: 'json',
    success: function(json) {
      if (json['success']) {
        console.log('Signature Added');
      } 
    }
  });
  } else {
    $.ajax({
      url: 'index.php?route=checkout/cart&remove=<?= $sign_product;?>&sign=1',
      dataType: 'html',
      success: function(html) {
        console.log('Signature Removed');
      }
    });
  }
});
</script>
  <?php foreach ($shipping_methods as $shipping_method) { ?>
  <tr>
    <td colspan="2"><b><?php echo $shipping_method['title']; ?></b></td>
    <td><b>ETA (Business Days)</b></td>
    <td> </td>
  </tr>
  <?php if (!$shipping_method['error']) { ?>
  <?php foreach ($shipping_method['quote'] as $quote) { ?>
  <tr class="highlight">
    <td><?php if ($quote['code'] == $code || !$code) { ?>
      <?php $code = $quote['code']; ?>
      <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" checked="checked" />
      <?php } else { ?>
      <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" />
      <?php } ?></td>
      <td><label for="<?php echo $quote['code']; ?>"><?php echo $quote['title']; ?></label></td>
      <td><label for="<?php echo $quote['code']; ?>"><?php echo $quote['delivery_time']; ?></label></td>
      <td style="text-align: left;"><label for="<?php echo $quote['code']; ?>"><?php echo $quote['text']; ?></label></td>
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="4"><div class="error"><?php echo $shipping_method['error']; ?></div></td>
    </tr>
    <?php } ?>
    <?php } ?>
  </table>
  <br />
  <?php } ?>
  <span><?php echo $text_comments; ?></span>
  <textarea name="comment" rows="8" style="width: 98%;"><?php echo $comment; ?></textarea>
  <br />
  <br />
  <div class="buttons">
    <div class="left">
      <span class="button_pink"><input type="button" value="<?php echo $button_continue; ?>" id="button-shipping-method" class="button" /></span>
    </div>
  </div>
