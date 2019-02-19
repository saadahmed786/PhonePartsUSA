<div id="checkout-shipping_method">
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>


<!--<div class="shipping_methods_new" style="float: left; margin-bottom:10px;">-->
	<div class="shippingMethod">
		<h2> Delivery Method</h2>
	</div>

<?php if ($shipping_methods) { ?>
  <?php foreach ($shipping_methods as $shipping_method) { ?>
	  
  <?php if (!$shipping_method['error']) { ?>
  <?php foreach ($shipping_method['quote'] as $quote) { ?>
 	<div class="local">
		<div class="local_orange">
		  	<?php if ($quote['code'] == $code || !$code) { ?>
			  <?php $code = $quote['code']; ?>
			  <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" checked="checked" style="position: initial !important;" />
			  <?php } else { ?>
			  <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>"  style="position: initial !important;"/>
			  <?php } ?>
			<h5><?php echo $quote['text']; ?></h5>
			<h4 class="shipping_label"><?php echo $quote['title']; ?></h4>
    		<!--<label class="shipping_label" for="<?php echo $quote['code']; ?>"><?php echo $quote['title']; ?></label>-->
		</div>
	</div>
  <?php } ?>
  <?php } else { ?>
  <div class="error"><?php echo $shipping_method['error']; ?></div>
   <?php } ?>
  <?php } ?>
<?php } ?>

<!--</div>-->
<br/><br/>
<input type="hidden" name="comment" value="" />
<div class="buttons" style="clear: both;">
  <div class="right"><input type="button" value="<?php echo $button_continue; ?>" id="button-shipping-method" class="button" data-theme="a" /></div>
</div>


<? /*
<?php if ($shipping_methods) { ?>
<fieldset data-role="controlgroup">
<p><?php echo $text_shipping_method; ?></p>
  <?php foreach ($shipping_methods as $shipping_method) { ?>
	  
  <?php if (!$shipping_method['error']) { ?>
 
  <?php foreach ($shipping_method['quote'] as $quote) { ?>
  	<?php if ($quote['code'] == $code || !$code) { ?>
      <?php $code = $quote['code']; ?>
      <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" checked="checked" />
      <?php } else { ?>
      <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" />
      <?php } ?>
    <label for="<?php echo $quote['code']; ?>"><?php echo $quote['title']; ?> - <?php echo $quote['text']; ?></label>
  <?php } ?>
  <?php } else { ?>
  <div class="error"><?php echo $shipping_method['error']; ?></div>
   <?php } ?>
  <?php } ?>
</fieldset>
<br />
<?php } ?>
<input type="hidden" name="comment" value="" />
<!--<b><?php echo $text_comments; ?></b>
<textarea name="comment" rows="8" style="width: 98%;"><?php echo $comment; ?></textarea>
<br />-->
<input type="button" value="<?php echo $button_continue; ?>" id="button-shipping-method" class="button" data-theme="a" />
*/ ?>

</div>
<script type="text/javascript"><!--
$('#checkout-shipping_method').page();
//$('.local_orange .ui-radio').removeClass('ui-radio);
//--></script> 
