<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/total.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
        <tr>
          <td><?php echo $entry_onecheckout_payment_fee_total; ?></td>
          <td><input type="text" name="onecheckout_payment_fee_total" value="<?php echo $onecheckout_payment_fee_total; ?>" /></td>
        </tr>
        <tr>
          <td><?php echo $entry_onecheckout_payment_fee_percentage; ?></td>
          <td><input type="text" name="onecheckout_payment_fee_percentage" value="<?php echo $onecheckout_payment_fee_percentage; ?>" /></td>
        </tr>
		<tr>
          <td><?php echo $entry_onecheckout_payment_fee_fixed_fee; ?></td>
          <td><input type="text" name="onecheckout_payment_fee_fixed_fee" value="<?php echo $onecheckout_payment_fee_fixed_fee; ?>" /></td>
        </tr>
		<tr>
	      <td><?php echo $entry_onecheckout_payment_fee_payment_type; ?></td>
          <td><div class="scrollbox">
          <?php $class = 'odd'; ?>
	          <?php foreach ($payment_types as $payment_type) { ?>
	          <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
				<div class="<?php echo $class; ?>">
				<?php if ($onecheckout_payment_fee_types) { ?>
				    <?php if (in_array($payment_type['paymentkey'],$onecheckout_payment_fee_types)) { ?>
						<input type="checkbox" name="onecheckout_payment_fee_types[]" value="<?php echo $payment_type['paymentkey']; ?>" checked="checked" />
						<?php echo $payment_type['name']; ?>
					<?php } else { ?> 
						<input type="checkbox" name="onecheckout_payment_fee_types[]" value="<?php echo $payment_type['paymentkey']; ?>" />
						<?php echo $payment_type['name']; ?>
					<?php } ?>
				<?php } else { ?>
					<input type="checkbox" name="onecheckout_payment_fee_types[]" value="<?php echo $payment_type['paymentkey']; ?>" />
					<?php echo $payment_type['name']; ?>
				<?php } ?>
                </div>			
              <?php } ?>
           </div></td>
        </tr>
		<tr>
          <td><?php echo $entry_onecheckout_payment_fee_geo_zone; ?></td>
          <td><select name="onecheckout_payment_fee_geo_zone_id">
              <option value="0"><?php echo $text_all_zones; ?></option>
              <?php foreach ($geo_zones as $geo_zone) { ?>
              <?php if ($geo_zone['geo_zone_id'] == $onecheckout_payment_fee_geo_zone_id) { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_onecheckout_payment_fee_tax; ?></td>
          <td><select name="onecheckout_payment_fee_tax_class_id">
              <option value="0"><?php echo $text_none; ?></option>
              <?php foreach ($tax_classes as $tax_class) { ?>
              <?php if ($tax_class['tax_class_id'] == $onecheckout_payment_fee_tax_class_id) { ?>
              <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_onecheckout_payment_fee_status; ?></td>
          <td><select name="onecheckout_payment_fee_status">
              <?php if ($onecheckout_payment_fee_status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_onecheckout_payment_fee_sort_order; ?></td>
          <td><input type="text" name="onecheckout_payment_fee_sort_order" value="<?php echo $onecheckout_payment_fee_sort_order; ?>" size="1" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php echo $footer; ?>