<?php //echo '<pre>'; print_r($data); exit; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div id="notification2"></div>
<?php if ($payment_methods) { ?>
<p><?php echo $text_payment_method; ?></p>
<table class="radio">
  <?php foreach ($payment_methods as $payment_method) { ?>
  <tr class="highlight">
    <td><?php if ($payment_method['code'] == $code || !$code) { ?>
      <?php $code = $payment_method['code']; ?>
      <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" />
      <?php } else { ?>
      <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" />
      <?php } ?></td>
      <td><label for="<?php echo $payment_method['code']; ?>"><?php echo $payment_method['title']; ?></label></td>
    </tr>
    <?php } ?>
  </table>
  <br />
  <?php } ?>
  <?php if ($is_po) { ?>
  <div id="po_no" style="margin-right:80px;margin-top:10px;height:60px;margin-bottom:20px;">
    <strong>Enter PO #:</strong><br><br>
    <input type="text" name="po_no" required="" value="<?= $po_no; ?>" />
  </div>
  <br><br>
  <?php } ?>
  <span><?php echo $text_comments; ?></span>
  <textarea name="comment" rows="8" style="width: 98%;"><?php echo $comment; ?></textarea>
  <br />
  <br />
  <?php if ($text_agree) { ?>
  <div class="buttons">
    <div class="left">
      <?php if ($agree) { ?>
      <input type="checkbox" name="agree" value="1" checked="checked" />
      <?php } else { ?>
      <input type="checkbox" name="agree" value="1" />
      <?php } ?>&nbsp;<?php echo $text_agree; ?><br/><br/><br/>
      <span class="button_pink"><input type="button" value="<?php echo $button_continue; ?>" id="button-payment-method" class="button" /></span>
    </div>
  </div>
  <?php } else { ?>
  <div class="buttons">
    <div class="left">
      <span class="button_pink"><input type="button" value="<?php echo $button_continue; ?>" id="button-payment-method" class="button" /></span>
    </div>
  </div>
  <?php } ?>
  <script type="text/javascript"><!--
    function get_Width_Height() {
     var array = new Array();
     if(getWidthBrowser() > 767){
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
  $('.colorbox').colorbox({
   width: get_Width_Height()[0],
   height: get_Width_Height()[1]
 });
 //--></script> 