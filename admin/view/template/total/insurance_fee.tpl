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
          <td><?php echo $entry_insurance_fee_percentage; ?></td>
          <td><input type="text" name="insurance_fee_percentage" value="<?php echo $insurance_fee_percentage; ?>" /></td>
        </tr>
		<tr>
          <td><?php echo $entry_insurance_fee_fixed_fee; ?></td>
          <td><input type="text" name="insurance_fee_fixed_fee" value="<?php echo $insurance_fee_fixed_fee; ?>" /></td>
        </tr>
		<tr>
          <td><?php echo $entry_insurance_fee_selection; ?></td>
          <td>
		  	<?php if ($insurance_fee_selection==0 || !$insurance_fee_selection) { ?>
		  	<label><input type="radio" name="insurance_fee_selection" value="0" checked="checked" />&nbsp;<?php echo $text_insurance_fee_offer; ?></label><br />
			<?php } else { ?>
			<label><input type="radio" name="insurance_fee_selection" value="0" />&nbsp;<?php echo $text_insurance_fee_offer; ?></label><br />
			<?php } ?>
			<?php if ($insurance_fee_selection==1) { ?>
  			<label><input type="radio" name="insurance_fee_selection" value="1" checked="checked" />&nbsp;<?php echo $text_insurance_fee_opt; ?></label><br />
			<?php } else { ?>
			<label><input type="radio" name="insurance_fee_selection" value="1" />&nbsp;<?php echo $text_insurance_fee_opt; ?></label><br />
			<?php } ?>
			<?php if ($insurance_fee_selection==2) { ?>
  			<label><input type="radio" name="insurance_fee_selection" value="2" checked="checked" />&nbsp;<?php echo $text_insurance_fee_required; ?></label><br />
			<?php } else { ?>
			<label><input type="radio" name="insurance_fee_selection" value="2" />&nbsp;<?php echo $text_insurance_fee_required; ?></label><br />
			<?php } ?>
		  </td>
        </tr>
		<tr>
          <td><?php echo $entry_insurance_fee_geo_zone; ?></td>
          <td><select name="insurance_fee_geo_zone_id">
              <option value="0"><?php echo $text_all_zones; ?></option>
              <?php foreach ($geo_zones as $geo_zone) { ?>
              <?php if ($geo_zone['geo_zone_id'] == $insurance_fee_geo_zone_id) { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_insurance_fee_status; ?></td>
          <td><select name="insurance_fee_status">
              <?php if ($insurance_fee_status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_insurance_fee_sort_order; ?></td>
          <td><input type="text" name="insurance_fee_sort_order" value="<?php echo $insurance_fee_sort_order; ?>" size="1" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php echo $footer; ?>