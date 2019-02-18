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
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
         <tr>
            <td>Account</td>
            <td>
              <select name="behalf_account">
              <option value="mockup" <?php echo ($behalf_account=='mockup'?'selected':'');?>>Mockup</option>
              <option value="sandbox" <?php echo ($behalf_account=='sandbox'?'selected':'');?>>Sandbox</option>
              <option value="production" <?php echo ($behalf_account=='production'?'selected':'');?>>Production</option>
              </select>

            </td>
          </tr>  

           <tr>
            <td>Client Token (Mockup)</td>
            <td><input type="text" name="behalf_client_token_mockup" value="<?php echo $behalf_client_token_mockup; ?>" /></td>
          </tr>

          <tr>
            <td>Client Token (Sandbox)</td>
            <td><input type="text" name="behalf_client_token_sandbox" value="<?php echo $behalf_client_token_sandbox; ?>" /></td>
          </tr>

          <tr>
            <td>Client Token (Production)</td>
            <td><input type="text" name="behalf_client_token" value="<?php echo $behalf_client_token; ?>" /></td>
          </tr>     
          <tr>
            <td>Email</td>
            <td><input type="text" name="behalf_server_email" value="<?php echo $behalf_server_email; ?>" /></td>
          </tr> 

          <tr>
            <td>Password</td>
            <td><input type="password" name="behalf_server_password" value="<?php echo $behalf_server_password; ?>" /></td>
          </tr>      
 
          <tr>
            <td><?php echo $entry_total; ?></td>
            <td><input type="text" name="behalf_total" value="<?php echo $behalf_total; ?>" /></td>
          </tr>   


          <tr>
            <td><?php echo $entry_order_status; ?></td>
            <td><select name="behalf_order_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $behalf_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_geo_zone; ?></td>
            <td><select name="behalf_geo_zone_id">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $behalf_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="behalf_status">
                <?php if ($behalf_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_sort_order; ?></td>
            <td><input type="text" name="behalf_sort_order" value="<?php echo $behalf_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?> 