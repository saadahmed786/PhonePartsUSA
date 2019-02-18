<?php $config = $this->config->get('quickcheckout'); ?>
<!-- Quick Checkout quickcheckout/payment_method.tpl -->
<?php if ($error_warning) { ?>

<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($payment_methods) { ?>
<div class="box box-border" >
  <div class="box-heading <?php if (!$config['payment_method_methods_display']) {  echo 'hide';  } ?>"><?php echo $text_payment_method; ?></div>
  <div class="box-content ">
    <div class="<?php if (!$config['payment_method_methods_display']) {  echo 'hide';  } ?>">
      <?php if($config['payment_method_methods_select']){ ?>
      <select name="payment_method" class="large-field payment-method-select" >
        <?php foreach ($payment_methods as $payment_method) { ?>
        <?php if ($payment_method['code'] == $code || !$code) { ?>
        <?php $code = $payment_method['code']; ?>
        <option  value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" selected="selected" ><?php echo $payment_method['title']; ?></option>
        <?php } else { ?>
        <option  value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" ><?php echo $payment_method['title']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
      <?php }else{?>
      <?php foreach ($payment_methods as $payment_method) { ?>
      <div>
        <?php if ($payment_method['code'] == $code || !$code) { ?>
        <?php $code = $payment_method['code']; ?>
        <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" class="styled"  />
        <?php } else { ?>
        <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" class="styled"  />
        <?php } ?><label for="<?php echo $payment_method['code']; ?>"><?php if(file_exists(DIR_IMAGE.'data/payment/'.$payment_method['code'].'.png')) { ?><img class="payment-image <?php if (!$config['payment_method_methods_image']) {  echo 'hide';  } ?>" src="image/data/payment/<?php echo $payment_method['code']; ?>.png" /><?php } ?>
        <?php echo $payment_method['title']; ?></label>
        
      </div>
      <?php } ?>
      <?php } ?>
      <?php } ?>
    </div>
    <div id="comment_input" class="<?php if(!$config['payment_method_comment_display']){ echo 'hide'; } ?>">
      <label class="comment"><?php echo $text_comments; ?></label>
      <textarea name="comment" id="comment" rows="8"><?php echo $comment; ?></textarea>
    </div>
    <?php if ($text_agree) { ?>
    <div>
      <?php if ($config['payment_method_agree_display']) { ?>
      
      <?php if ($agree) { ?>
      <input type="checkbox" name="agree" value="1" checked="checked" class="styled"/>
      <?php } else { ?>
      <input type="checkbox" name="agree" value="1" class="styled"/>
      <?php } ?>
      <?php }else{ ?>
      <input type="checkbox" name="agree" value="1" checked="checked" class="hide"/>
      <?php } ?>
      <?php echo $text_agree; ?>
    </div>
    <div>
    </div>
    <?php } ?>
    <div class="clear"></div>
  </div>
</div>
<script type="text/javascript"><!--
$('.colorbox').colorbox({
	width: 640,
	height: 480
});
//--></script>
<script><!--
$(function(){

		if($.isFunction($.fn.uniform)){
        $(" .styled, input:radio.styled").uniform().removeClass('styled');
		}
      });
//--></script>