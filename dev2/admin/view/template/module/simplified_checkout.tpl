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
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
      	<tr>
            <td>* <?php echo $entry_status; ?></td>
            <td><select name="simplified_checkout_status">
                <?php if ($simplified_checkout_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td>* <?php echo $entry_dynamic_shipping; ?></td>
            <td>
            	<?php if ($simplified_checkout_dynamic_shipping) { ?>
            		<input type="radio" name="simplified_checkout_dynamic_shipping" value="0" id="hide_country_0" /> <label for="hide_country_0"><?php echo $text_no; ?></label>
					<input type="radio" name="simplified_checkout_dynamic_shipping" value="1" id="hide_country_1" checked="checked" /> <label for="hide_country_1"><?php echo $text_yes; ?></label>
				<?php } else { ?>
					<input type="radio" name="simplified_checkout_dynamic_shipping" value="0" id="hide_country_0" checked="checked" /> <label for="hide_country_0"><?php echo $text_no; ?></label>
					<input type="radio" name="simplified_checkout_dynamic_shipping" value="1" id="hide_country_1" /> <label for="hide_country_1"><?php echo $text_yes; ?></label>
				<?php } ?>
            </td>
          </tr>
          <tr>
            <td>* <?php echo $entry_show_coupon; ?></td>
            <td>
            	<?php if ($simplified_checkout_show_coupon) { ?>
            		<input type="radio" name="simplified_checkout_show_coupon" value="0" id="show_coupon_0" /> <label for="show_coupon_0"><?php echo $text_no; ?></label>
					<input type="radio" name="simplified_checkout_show_coupon" value="1" id="show_coupon_1" checked="checked" /> <label for="show_coupon_1"><?php echo $text_yes; ?></label>
				<?php } else { ?>
					<input type="radio" name="simplified_checkout_show_coupon" value="0" id="show_coupon_0" checked="checked" /> <label for="show_coupon_0"><?php echo $text_no; ?></label>
					<input type="radio" name="simplified_checkout_show_coupon" value="1" id="show_coupon_1" /> <label for="show_coupon_1"><?php echo $text_yes; ?></label>
				<?php } ?>
            </td>
          </tr>
          <tr>
            <td>* <?php echo $entry_hide_country; ?></td>
            <td>
            	
            	<?php if ($simplified_checkout_hide_country) { ?>
            		<input type="radio" name="simplified_checkout_hide_country" value="0" id="hide_country_0" /> <label for="hide_country_0"><?php echo $text_no; ?></label>
					<input type="radio" name="simplified_checkout_hide_country" value="1" id="hide_country_1" checked="checked" /> <label for="hide_country_1"><?php echo $text_yes; ?></label>
				<?php } else { ?>
					<input type="radio" name="simplified_checkout_hide_country" value="0" id="hide_country_0" checked="checked" /> <label for="hide_country_0"><?php echo $text_no; ?></label>
					<input type="radio" name="simplified_checkout_hide_country" value="1" id="hide_country_1" /> <label for="hide_country_1"><?php echo $text_yes; ?></label>
				<?php } ?>
            </td>
          </tr>
		  <tr>
            <td>** <?php echo $entry_fixed_country; ?></td>
            <td><select name="simplified_checkout_fixed_country" onchange="$('select[name=\'simplified_checkout_fixed_zone\']').load('../index.php?route=account/register/zone&country_id=' + this.value + '&zone_id=<?php //echo $zone_id; ?>');">
            		<option value="0"><?php echo $text_select; ?></option>
            	<?php foreach ($countries as $country) { ?>
              	  <?php if ($simplified_checkout_fixed_country == $country['country_id']) { ?>
                	<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
               	 <?php } else { ?>
               		 <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                <?php }
                } ?>
              </select> <span class="error"><?php echo $error_fixed_country; ?></span></td>
          </tr>
          <tr>
            <td>* <?php echo $entry_hide_zone; ?></td>
            <td>
            	
            	<?php if ($simplified_checkout_hide_zone) { ?>
            		<input type="radio" name="simplified_checkout_hide_zone" value="0" id="hide_zone_0" /> <label for="hide_zone_0"><?php echo $text_no; ?></label>
					<input type="radio" name="simplified_checkout_hide_zone" value="1" id="hide_zone_1" checked="checked" /> <label for="hide_zone_1"><?php echo $text_yes; ?></label>
				<?php } else { ?>
					<input type="radio" name="simplified_checkout_hide_zone" value="0" id="hide_zone_0" checked="checked" /> <label for="hide_zone_0"><?php echo $text_no; ?></label>
					<input type="radio" name="simplified_checkout_hide_zone" value="1" id="hide_zone_1" /> <label for="hide_zone_1"><?php echo $text_yes; ?></label>
				<?php } ?>
            </td>
          </tr>
          <tr>
            <td><?php echo $entry_fixed_zone; ?></td>
            <td><select name="simplified_checkout_fixed_zone">
            		<option value="0"><?php echo $text_select; ?></option>
            	<?php 
            	if ($zones) {
            		foreach ($zones as $zone) { 
              			if ($simplified_checkout_fixed_zone == $zone['zone_id']) { ?>
                	<option value="<?php echo $zone['zone_id']; ?>" selected="selected"><?php echo $zone['name']; ?></option>
               		 <?php } else { ?>
               		 <option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['name']; ?></option>
               		 <?php }
               		}
                } ?>
              </select></td>
          </tr>
          <tr>
            <td>* <?php echo $entry_hide_account_terms; ?></td>
            <td>
            	
            	<?php if ($simplified_checkout_hide_account_terms) { ?>
            		<input type="radio" name="simplified_checkout_hide_account_terms" value="0" id="hide_account_terms_0" /> <label for="hide_account_terms_0"><?php echo $text_no; ?></label>
					<input type="radio" name="simplified_checkout_hide_account_terms" value="1" id="hide_account_terms_1" checked="checked" /> <label for="hide_account_terms_1"><?php echo $text_yes; ?></label>
				<?php } else { ?>
					<input type="radio" name="simplified_checkout_hide_account_terms" value="0" id="hide_account_terms_0" checked="checked" /> <label for="hide_account_terms_0"><?php echo $text_no; ?></label>
					<input type="radio" name="simplified_checkout_hide_account_terms" value="1" id="hide_account_terms_1" /> <label for="hide_account_terms_1"><?php echo $text_yes; ?></label>
				<?php } ?>
            </td>
          </tr>
          <tr>
            <td><?php echo $entry_template; ?></td>
            <td><select name="simplified_checkout_template">
            	<option value="2column" selected="selected"><?php echo $text_2column; ?></option>
            <!--
            	<?php 
				if ($simplified_checkout_template == 'standard' || !$simplified_checkout_template) { ?>
                	<option value="standard" selected="selected"><?php echo $text_standard; ?></option>
               		<option value="2column"><?php echo $text_2column; ?></option>
               		<?php } else { ?>
               		<option value="standard"><?php echo $text_standard; ?></option>
               		<option value="2column" selected="selected"><?php echo $text_2column; ?></option>
              <?php } ?>
          -->
              </select></td>
          </tr>
        </table>
        <div><?php echo $text_help; ?></div>
      </form>
    </div>
  </div>
</div>


 
<?php echo $footer; ?>