<?php if ($testmode) { ?>
<div class="warning"><?php echo $text_testmode; ?></div>
<?php } ?>
<br>
<div class="buttons">
    <div class="center">
      <input type="submit" value="Pay Complete Order" class="btn4" onclick="window.location='index.php?route=payment/paypal_express_new/SetExpressCheckout&is_pp_checkout=1'"  />
    </div>
  </div>

<span style="font-size:11px">Pressing the link above will take you to Paypal to complete the payment porition of the order. 
After completeing the payment, you will be directed back to our website. An order confirmtion page
will be displayed showing the Order ID and Shipping Address.</span>