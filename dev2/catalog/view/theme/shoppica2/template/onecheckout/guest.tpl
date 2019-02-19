<div id="guest-cpanle" class="divclear">
<?php if ($shipping_required) { ?>
  <?php if ($shipping_address) { ?>
  <input type="checkbox" name="shipping_address" value="1" id="shipping" checked="checked" />
  <?php } else { ?>
  <input type="checkbox" name="shipping_address" value="1" id="shipping" />
  <?php } ?>
  <?php echo $entry_shipping; ?>
  <br />
  <br />
<?php } else { ?>
  <input type="checkbox" name="shipping_address" value="1" id="shipping" checked="checked" style="display:none;" />
<?php } ?>
</div>