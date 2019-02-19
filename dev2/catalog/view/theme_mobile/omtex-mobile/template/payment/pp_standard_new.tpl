<?php if ($testmode) { ?>
<div class="warning"><?php echo $text_testmode; ?></div>
<?php } ?>

  <div class="buttons">
    <div class="right">
      <input type="button" value="<?php echo $button_confirm; ?>" class="button" data-theme="a" onclick="window.location='index.php?route=payment/paypal_express_new/SetExpressCheckout&is_pp_checkout=1'" />
    </div>
  </div>

