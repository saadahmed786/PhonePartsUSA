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
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $entry_partner; ?></td>
          <td><input type="text" name="pp_payflow_pro_partner" value="<?php echo $pp_payflow_pro_partner; ?>" />
            <?php if ($error_partner) { ?>
            <span class="error"><?php echo $error_partner; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_vendor; ?></td>
          <td><input type="text" name="pp_payflow_pro_vendor" value="<?php echo $pp_payflow_pro_vendor; ?>" />
            <?php if ($error_vendor) { ?>
            <span class="error"><?php echo $error_vendor; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_username; ?></td>
          <td><input type="text" name="pp_payflow_pro_username" value="<?php echo $pp_payflow_pro_username; ?>" />
            <?php if ($error_username) { ?>
            <span class="error"><?php echo $error_username; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_password; ?></td>
          <td><input type="password" name="pp_payflow_pro_password" value="<?php echo $pp_payflow_pro_password; ?>" onfocus="if (this.value==(this.defaultText||this.defaultValue)) this.value='';" onblur="if(this.value=='')this.value=this.defaultText||this.defaultValue;" autocomplete="off" />
            <?php if ($error_password) { ?>
            <span class="error"><?php echo $error_password; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><?php echo $entry_server; ?></td>
          <td><select name="pp_payflow_pro_server">
              <option value="T" <?php if ($pp_payflow_pro_server == "T")  echo 'selected="selected"'; ?>><?php echo $text_test; ?></option>
              <option value="L" <?php if ($pp_payflow_pro_server == "L")  echo 'selected="selected"'; ?>><?php echo $text_live; ?></option>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_transaction; ?></td>
          <td><select name="pp_payflow_pro_transaction">
              <option value="A" <?php if ($pp_payflow_pro_transaction == "A")  echo 'selected="selected"'; ?>><?php echo $text_authorization; ?></option>
              <option value="S" <?php if ($pp_payflow_pro_transaction == "S")  echo 'selected="selected"'; ?>><?php echo $text_sale; ?></option>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_timeout; ?></td>
          <td><input type="text" name="pp_payflow_pro_timeout" value="<?php echo $pp_payflow_pro_timeout; ?>" /></td>
            <?php if ($error_timeout) { ?>
            <span class="error"><?php echo $error_timeout; ?></span>
            <?php } ?></td>
        </tr>          
        <tr>
          <td><?php echo $entry_timeout_order_status; ?></td>
          <td><select name="pp_payflow_pro_timeout_order_status_id">
              <?php foreach ($order_statuses as $order_status) { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>" <?php if ($order_status['order_status_id'] == $pp_payflow_pro_timeout_order_status_id)  echo 'selected="selected"'; ?>><?php echo $order_status['name']; ?></option>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_fps_order_status; ?></td>
          <td><select name="pp_payflow_pro_fps_order_status_id">
              <?php foreach ($order_statuses as $order_status) { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>" <?php if ($order_status['order_status_id'] == $pp_payflow_pro_fps_order_status_id)  echo 'selected="selected"'; ?>><?php echo $order_status['name']; ?></option>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_order_status; ?></td>
          <td><select name="pp_payflow_pro_order_status_id">
              <?php foreach ($order_statuses as $order_status) { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>" <?php if ($order_status['order_status_id'] == $pp_payflow_pro_order_status_id)  echo 'selected="selected"'; ?>><?php echo $order_status['name']; ?></option>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_total; ?></td>
          <td><input type="text" name="pp_payflow_pro_total" value="<?php echo $pp_payflow_pro_total; ?>" /></td>
        </tr>          
        <tr>
          <td><?php echo $entry_comment1; ?></td>
          <td><input type="text" name="pp_payflow_pro_comment1" value="<?php echo $pp_payflow_pro_comment1; ?>" maxlength="128" size="50" /><br /><?php echo $entry_comment1_input; ?></td>
        </tr>
        <tr>
          <td><?php echo $entry_comment2; ?></td>
          <td><input type="text" name="pp_payflow_pro_comment2" value="<?php echo $pp_payflow_pro_comment2; ?>" maxlength="128" size="50" /><br /><?php echo $entry_comment2_input; ?></td>
        </tr>
        <tr>
          <td><?php echo $entry_invnum; ?></td>
          <td><input type="text" name="pp_payflow_pro_invnum" value="<?php echo $pp_payflow_pro_invnum; ?>" maxlength="9" /></td>
        </tr>
        <tr>
          <td><?php echo $entry_idprefix; ?></td>
          <td><input type="text" name="pp_payflow_pro_idprefix" value="<?php echo $pp_payflow_pro_idprefix; ?>" maxlength="16" /></td>
        </tr>
        <tr>
          <td><?php echo $entry_geo_zone; ?></td>
          <td><select name="pp_payflow_pro_geo_zone_id">
              <option value="0"><?php echo $text_all_zones; ?></option>
              <?php foreach ($geo_zones as $geo_zone) { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>" <?php if ($geo_zone['geo_zone_id'] == $pp_payflow_pro_geo_zone_id)  echo 'selected="selected"'; ?>><?php echo $geo_zone['name']; ?></option>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_status; ?></td>
          <td><select name="pp_payflow_pro_status">
              <option value="1" <?php if ($pp_payflow_pro_status)  echo 'selected="selected"'; ?>><?php echo $text_enabled; ?></option>
              <option value="0" <?php if (!$pp_payflow_pro_status)  echo 'selected="selected"'; ?>><?php echo $text_disabled; ?></option>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_sort_order; ?></td>
          <td><input type="text" name="pp_payflow_pro_sort_order" value="<?php echo $pp_payflow_pro_sort_order; ?>" size="1" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>
</div>
<?php echo $footer; ?>
