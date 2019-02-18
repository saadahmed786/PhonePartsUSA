<div id="checkout-payment_method">
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="shipping_methods_new" style="float: left; margin-bottom: 10px;">
		<div class="paymentMethods">
			<h2> Payment Method</h2>
		</div>
<?php if ($payment_methods) { ?>
<?php foreach ($payment_methods as $payment_method) { ?>
		<div class="local">
			<div class="local_blue">
				<?php if ($payment_method['code'] == $code || !$code) { ?>

				  <?php $code = $payment_method['code']; ?>
				  <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" />
				  <?php } else { ?>
				  <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" />

				  <?php } ?>
					<h4><?php echo $payment_method['title']; ?></h4>
			</div>
		</div>
	<?php } ?>
<?php } ?>
		<div class="comments">
			<p>Order Comments</p>
			<textarea name="comment"><?php echo $comment; ?></textarea>
		</div>
		<input type="hidden" name="agree" value="1" id="agree"/>
<? /*
		<div class="local_blue">
		<?php if ($text_agree) { ?>

			<?php if ($agree) { ?>
			<input type="checkbox" name="agree" value="1" checked="checked" id="agree"/>
			<?php } else { ?>
			<input type="checkbox" name="agree" value="1" id="agree" />
			<?php } ?>
			<legend id="text_agree"><h6><?php echo $text_i_agree; ?></h6><?php echo $text_agree; ?></legend>
		<?php } else { ?>
		<?php } ?>
			<!--<input type="checkout">
			<h6>I have read and agree to the 
			Terms & Conditions</h6>-->
		</div>
*/ ?>
</div>
<br/><br/>
<div class="buttons" style="clear: both;">
  <div class="right"><input type="button" value="<?php echo $button_continue; ?>" id="button-payment-method" class="button" data-theme="a" /></div>
</div>
<? /*
<?php if ($payment_methods) { ?>
<fieldset data-role="controlgroup">
<legend><?php echo $text_payment_method; ?></legend>
<?php foreach ($payment_methods as $payment_method) { ?>
	<?php if ($payment_method['code'] == $code || !$code) { ?>
      <?php $code = $payment_method['code']; ?>
      <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" />
      <?php } else { ?>
      <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" />
      <?php } ?>
    <label for="<?php echo $payment_method['code']; ?>"><?php echo $payment_method['title']; ?></label>
	<?php } ?>
<?php } ?>
</fieldset>
<b><?php echo $text_comments; ?></b>
<textarea name="comment" rows="8" style="width: 98%;"><?php echo $comment; ?></textarea>
<br />
<br />
<?php if ($text_agree) { ?>

    <?php if ($agree) { ?>
    <input type="checkbox" name="agree" value="1" checked="checked" id="agree"/>
    <?php } else { ?>
    <input type="checkbox" name="agree" value="1" id="agree" />
    <?php } ?>
    <label for="agree"><?php echo $text_i_agree; ?></label>
	<legend id="text_agree"><?php echo $text_agree; ?></legend>
<input type="button" value="<?php echo $button_continue; ?>" id="button-payment-method" class="button" data-theme="a" />
<?php } else { ?>
<input type="button" value="<?php echo $button_continue; ?>" id="button-payment-method" class="button" data-theme="a" />
<?php } ?>
*/ ?>
</div>
<script type="text/javascript"><!--
$('#text_agree a').removeAttr('href');
$('#text_agree a').removeAttr('class');
$('#text_agree a').attr("href","#agree_page");
$('#checkout-payment_method').page();
$('.fancybox').fancybox({
	width: 640,
	height: 480,
	autoDimensions: false
});
//$('div.ui-radio').removeClass('ui-radio);
//--></script> 
