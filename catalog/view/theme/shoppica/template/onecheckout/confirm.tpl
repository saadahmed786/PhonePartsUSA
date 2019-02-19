<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<div class="onecheckout-product">
  <table>
    <thead>
      <tr>
        <td class="name"><?php echo $column_name; ?></td>
        <td class="quantity"><?php echo $column_quantity; ?></td>
        <td class="price"><?php echo $column_price; ?></td>
        <td class="total"><?php echo $column_total; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $product) { ?>
      <tr>
        <td class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
          <?php foreach ($product['option'] as $option) { ?>
          <br />
          &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
          <?php } ?></td>
        <td class="quantity"><?php echo $product['quantity']; ?></td>
        <td class="price"><?php echo $product['price']; ?></td>
        <td class="total"><?php echo $product['total']; ?></td>
      </tr>
      <?php } ?>
      <?php foreach ($vouchers as $voucher) { ?>
      <tr>
        <td class="name"><?php echo $voucher['description']; ?></td>
        <td class="quantity">1</td>
        <td class="price"><?php echo $voucher['amount']; ?></td>
        <td class="total"><?php echo $voucher['amount']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php foreach ($totals as $total) { ?>
      <tr>
        <td colspan="2" class="price"><b><?php echo $total['title']; ?>:</b></td>
        <td colspan="2" class="total"><?php echo $total['text']; ?></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
</div>
<?php echo $cartmodule; ?>
<?php if ($onecheckout_survey_status && $this->config->get('onecheckout_survey_position')=='0') { ?>
<div class="cart-module">
  <div class="cart-heading active"><?php echo $survey_heading_title; ?></div>
  <div class="cart-content" style="display:block;">
    <select name="onecheckout_surver" onchange="$.post('index.php?route=onecheckout/confirm/savesurver',$('#confirm select[name=\'onecheckout_surver\']'));">
	<option value=""><?php echo $text_survey; ?></option>
   	<?php foreach ($survey_options as $option) { ?>
   	<?php if ($onecheckout_survey_option == $option) { ?>
    <option value="<?php echo $option; ?>" selected="selected"><?php echo $option; ?></option>
    <?php } else { ?>
    <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
    <?php } ?>
    <?php } ?>
	</select>
  </div>
</div>
<?php } ?>
<?php if ($text_agree) { ?>
<div class="onebuttons">  
    <?php if ($agree) { ?>
    <input type="checkbox" name="agree" value="1" checked="checked" />
    <?php } else { ?>
    <input type="checkbox" name="agree" value="1" />
    <?php } ?><?php echo $text_agree; ?><br /><br />
  <div class="right divclear">
	<a id="button-confirmorder" class="onebutton"><span><?php echo $button_confirm; ?></span></a>
  </div>
</div>
<?php } else { ?>
<div class="onebuttons">
  <div class="right"><a id="button-confirmorder" class="onebutton"><span><?php echo $button_confirm; ?></span></a></div>
</div>
<?php } ?>
<script type="text/javascript"><!--
$('.colorbox').colorbox({
	width: 640,
	height: 480
});
//--></script>