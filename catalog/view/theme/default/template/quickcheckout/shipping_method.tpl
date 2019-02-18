
<?php $config = $this->config->get('quickcheckout'); ?>
<!-- Quick Checkout quickcheckout/shipping_method.tpl -->

<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>

<?php if ($shipping_methods) { ?>

<div class="box box-border">
<div class="box-heading <?php if (!$config['shipping_method_methods_display']) {  echo 'hide';  } ?>"><?php echo $text_shipping_method; ?></div>
<div class="box-content">
<div class="<?php if (!$config['shipping_method_methods_display']) {  echo 'hide';  } ?>">

<?php if($config['shipping_method_methods_select']){ ?>
<select name="shipping_method" class="large-field shipping-method-select" >
    <?php foreach ($shipping_methods as $shipping_method) { ?>
    <?php foreach ($shipping_method['quote'] as $quote) { ?>
            <?php if ($quote['code'] == $code || !$code) { ?>
            <?php $code = $quote['code']; ?>
            <option  value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" selected="selected" ><?php echo $quote['title']; ?> <?php echo $quote['text']; ?></option>
            <?php } else { ?>
            <option  value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" ><?php echo $quote['title']; ?> <?php echo $quote['text']; ?></option>
            <?php } ?>
            <?php } ?>
        <?php } ?>
</select> 
<?php } else { ?> 

<table class="radio">
  <?php foreach ($shipping_methods as $shipping_method) { ?>
  <tr>
	<td colspan="3"><div class="h3 <?php if(!$config['shipping_method_title_display']){ echo 'hide'; } ?>"><?php echo $shipping_method['title']; ?></div></td>
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
    <td style="text-align: right;"><label for="<?php echo $quote['code']; ?>"><?php echo $quote['text']; ?></label></td>
  </tr>
  <?php } ?>
  <?php } else { ?>
  <tr>
	<td colspan="3"><div class="error alert alert-error"><?php echo $shipping_method['error']; ?></div></td>
  </tr>
  <?php } ?>
  <?php } ?>
</table>

<?php } ?>

	<div id="date_input" class="<?php if(!$config['shipping_method_date_display']){ echo 'hide'; } ?>">
      <label for="date"><?php if($config['text_shipping_date'][$this->config->get('config_language_id')]){ echo $config['text_shipping_date'][$this->config->get('config_language_id')]; }else{ echo 'Choose delivery date:'; } ?></label>
      <input type="text" name="date" class="date" id="date" rows="8"  value="<?php echo $date; ?>" />
    </div>

	<div id="comment_input" class="<?php if(!$config['shipping_method_comment_display']){ echo 'hide'; } ?>">
	<label class="comment"><?php echo $text_comments; ?></label>
	<textarea name="comment" rows="8" style="width: 98%;"><?php echo $comment; ?></textarea>
	</div>
	
</div>
<div class="clear"></div>
</div>
</div>

<?php if(isset($config['shipping_method_date_picker']) && $config['shipping_method_date_picker']){ ?> 
<script>
$('.date').datepicker({dateFormat: 'yy-mm-dd'});
</script>
<?php } ?>

<?php } ?>





