<?php
/**
 * Contains part of the Opencart Authorize.Net CIM Payment Module code.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to memiiso license.
 * Please see the LICENSE.txt file for more information.
 * All other rights reserved.
 *
 * @author     memiiso <gel.yine.gel@hotmail.com>
 * @copyright  2013-~ memiiso
 * @license    Commercial License. Please see the LICENSE.txt file
 */
?>
<?php echo $header; ?>
<style type="text/css">
input {width:400px;}
</style>
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
      <div class="buttons">
      <a id="showlicense" class="button"><span><?php echo $text_license; ?></span></a>
      <a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a>
      <a href="<?php echo $cancel; ?>" class="button"><span><?php echo $button_cancel; ?></span></a>
      </div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      
        <table class="form">
          <tr>
            <td style="width: 380px;"><span class="required">*</span> <?php echo $text_live.' '.$entry_login; ?></td>
            <td><input type="text" name="authorizenet_cim_live_login" value="<?php echo $authorizenet_cim_live_login; ?>" />
              <?php if ($error_login) { ?>
              <span class="error"><?php echo $error_login; ?></span>
              <?php } ?>
            </td>                 
          </tr>          
          <tr>
            <td ><span class="required">*</span> <?php echo $text_live.' '.$entry_key; ?></td>
            <td ><input type="text" name="authorizenet_cim_live_key" value="<?php echo $authorizenet_cim_live_key; ?>" />
              <?php if ($error_key) { ?>
              <span class="error"><?php echo $error_key; ?></span>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td><span class="required"></span> <?php echo $text_sandbox.' '.$entry_login; ?></td>
            <td><input type="text" name="authorizenet_cim_sandbox_login" value="<?php echo $authorizenet_cim_sandbox_login; ?>" />
              <?php if ($error_login) { ?>
              <span class="error"><?php echo $error_login; ?></span>
              <?php } ?>
            </td>   
           </tr>
           <tr>  
              <td ><span class="required"></span> <?php echo $text_sandbox.' '.$entry_key; ?></td>
              <td ><input type="text" name="authorizenet_cim_sandbox_key" value="<?php echo $authorizenet_cim_sandbox_key; ?>" />
              <?php if ($error_key) { ?>
              <span class="error"><?php echo $error_key; ?></span>
              <?php } ?>
              </td>  
           </tr>          
          <tr>
            <td ><?php echo $entry_hash; ?></td>
            <td ><input type="text" name="authorizenet_cim_hash" value="<?php echo $authorizenet_cim_hash; ?>" /></td>
          </tr>
          <tr>
            <td ><?php echo $entry_server; ?></td>
            <td ><select name="authorizenet_cim_server">
                <?php if ($authorizenet_cim_server == 'live') { ?>
                <option value="live" selected="selected"><?php echo $text_live; ?></option>
                <?php } else { ?>
                <option value="live"><?php echo $text_live; ?></option>
                <?php } ?>
                <?php if ($authorizenet_cim_server == 'sandbox') { ?>
                <option value="sandbox" selected="selected"><?php echo $text_sandbox; ?></option>
                <?php } else { ?>
                <option value="sandbox"><?php echo $text_sandbox; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td ><?php echo $entry_validation_mode; ?></td>
            <td ><select name="authorizenet_cim_validation_mode">
                <option value="none" <?php echo $authorizenet_cim_validation_mode == 'none' ? 'selected="selected"':'' ; ?>><?php echo $validation_mode_none; ?></option>
                <option value="testMode" <?php echo $authorizenet_cim_validation_mode == 'testMode' ? 'selected="selected"':'' ; ?>><?php echo $validation_mode_test; ?></option>
                <option value="liveMode" <?php echo $authorizenet_cim_validation_mode == 'liveMode' ? 'selected="selected"':'' ; ?>><?php echo $validation_mode_live; ?></option>
              </select>
              </td>
          </tr>
          <tr> 
            <td ><?php echo $entry_method; ?></td>
            <td >
            <select name="authorizenet_cim_method">
                <?php if ($authorizenet_cim_method == 'AuthCapture') { ?>
                <option value="AuthCapture" selected="selected"><?php echo $text_authorizeandcapture; ?></option>
                <?php } else { ?>
                <option value="AuthCapture"><?php echo $text_authorizeandcapture; ?></option>
                <?php } ?>
                <?php if ($authorizenet_cim_method == 'AuthOnly') { ?>
                <option value="AuthOnly" selected="selected"><?php echo $text_authorization; ?></option>
                <?php } else { ?>
                <option value="AuthOnly"><?php echo $text_authorization; ?></option>
                <?php } ?>
                <?php if ($authorizenet_cim_method == 'CaptureOnly') { ?>
                <option value="CaptureOnly" selected="selected"><?php echo $text_capture; ?></option>
                <?php } else { ?>
                <option value="CaptureOnly"><?php echo $text_capture; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td ><?php echo $entry_total; ?></td>
            <td ><input type="text" name="authorizenet_cim_total" value="<?php echo $authorizenet_cim_total; ?>" /></td>
          </tr>
          <tr>
            <td ><?php echo $entry_order_status.'<br><span class="help">'.$entry_order_status_info.'</span>'; ?></td>
            
            <td ><select name="authorizenet_cim_order_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $authorizenet_cim_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td ><?php echo $entry_geo_zone; ?></td>
            <td ><select name="authorizenet_cim_geo_zone_id">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $authorizenet_cim_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td ><?php echo $entry_status; ?></td>
            <td ><select name="authorizenet_cim_status">
                <?php if ($authorizenet_cim_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td ><?php echo $entry_sort_order; ?></td>
            <td ><input type="text" name="authorizenet_cim_sort_order" value="<?php echo $authorizenet_cim_sort_order; ?>" size="1" /></td>
          </tr>          
           <tr>
            <td ><?php echo $entry_cim_require_billing_adress; ?></td>
            <td >
                <input type="checkbox" name="authorizenet_cim_require_billing_adress" value="forcebillingadress" <?php echo ($authorizenet_cim_require_billing_adress=='forcebillingadress')  ? 'checked="checked"' : ''; ?> style="width: inherit;">
            </td>
          </tr>         
          <tr>
            <td ><?php echo $text_disable_bank_payment; ?></td>
            <td >
                <input type="checkbox" name="authorizenet_cim_disable_bank_payment" value="disable_bank_payment" <?php echo ($authorizenet_cim_disable_bank_payment=='disable_bank_payment')  ? 'checked="checked"' : ''; ?> style="width: inherit;">
            </td>
          </tr>  
          
          
          <tr>
          <td colspan="2" style="border-bottom: none;">
				 <div style="clear: both;">
				 <?php echo $this->language->get('text_held_rule_list_info'); ?>
				  </div></td>
          </tr>         
          <tr>
            <td ><?php echo $entry_cim_held_rule_list; ?></td>
            <td >            
            <?php if ($held_rule_list_error) { ?>
              <span class="error"><?php echo $held_rule_list_error; ?></span>
              <?php } ?>
            <textarea name="authorizenet_cim_held_rule_list" rows="10" cols="80" ><?php echo $authorizenet_cim_held_rule_list; ?></textarea> 
				<div style="float: right;">
				<?php echo $this->language->get('text_order_status_list'); ?><br>
				<?php foreach ($order_statuses as $order_status) { ?>
		                <?php echo $order_status['order_status_id'].':'.$order_status['name']; ?> <br>
	                <?php } ?>
				 </div>
            </td>
          </tr>
                               
           <tr>
            <td ><?php echo $entry_cim_held_notificatin_emails; ?></td>
            <td >
                <input type="text" name="authorizenet_cim_held_notificatin_emails" value="<?php echo $authorizenet_cim_held_notificatin_emails; ?>" />
            </td>
          </tr>
           <tr>
            <td ><?php echo $entry_cim_held_notify_customer; ?></td>
            <td >
                <input type="checkbox" name="authorizenet_cim_held_notify_customer" value="notifycustomeronhold" <?php echo ($authorizenet_cim_held_notify_customer=='notifycustomeronhold')  ? 'checked="checked"' : ''; ?> style="width: inherit;">
            </td>
          </tr>          
          <tr>
            <td ><?php echo $entry_enable_shipping_adress; ?></td>
            <td >
                <input type="checkbox" name="authorizenet_cim_use_shipping_address" value="usecimshippingaddress" <?php echo ($authorizenet_cim_use_shipping_address=='usecimshippingaddress')  ? 'checked="checked"' : ''; ?> style="width: inherit;">
            </td>
          </tr>         
          <tr>
            <td ><?php echo $entry_cim_fill_line_items; ?></td>
            <td >
                <input type="checkbox" name="authorizenet_cim_fill_line_items" value="filllineitems" <?php echo ($authorizenet_cim_fill_line_items=='filllineitems')  ? 'checked="checked"' : ''; ?> style="width: inherit;">
            </td>
          </tr>
          <tr>
            <td ><?php echo $entry_send_email_onerror; ?></td>
            <td >
                <input type="checkbox" name="authorizenet_cim_email_error" value="emailerror" <?php echo ($authorizenet_cim_email_error=='emailerror')  ? 'checked="checked"' : ''; ?> style="width: inherit;">
            </td>
          </tr>
          <tr>
            <td ><?php echo $entry_daily_log; ?></td>
            <td >
                <input type="checkbox" name="authorizenet_cim_daily_log" value="daily" <?php echo ($authorizenet_cim_daily_log=='daily')  ? 'checked="checked"' : ''; ?> style="width: inherit;">
            </td>
          </tr>
         <tr>
            <td ><?php echo $entry_delete_notfound_cimid; ?></td>
            <td >
                <input type="checkbox" name="authorizenet_cim_delete_notfound" value="delete" <?php echo ($authorizenet_cim_delete_notfound=='delete')  ? 'checked="checked"' : ''; ?> style="width: inherit;">
            </td>
          </tr>
         <tr>
            <td ><?php echo $entry_log_responses; ?></td>
            <td >
                <input type="checkbox" name="authorizenet_cim_debug_log" value="create" <?php echo ($authorizenet_cim_debug_log=='create')  ? 'checked="checked"' : ''; ?> style="width: inherit;">
            </td>
          </tr>                   
         <tr>
            <td ><?php echo $entry_save_use_jquerydialog; ?></td>
            <td >
                <input type="checkbox" name="authorizenet_cim_use_jquerydialog" value="usejquerdialog" <?php echo ($authorizenet_cim_use_jquerydialog=='usejquerdialog')  ? 'checked="checked"' : ''; ?> style="width: inherit;">
            </td>
          </tr>
          <tr>
            <td ></td>
            <td >   	
       			<input type="hidden" value="disable" name="authorizenet_cim_enable_cim_adress" />  
                <input type="hidden" value="notsave" name="authorizenet_cim_transaction_save_shippingaddress" />  
            </td>
          </tr>       
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?> 
<script type="text/javascript"><!--
$(document).ready(function(){
	$('#showlicense').click(function() {
		$('<div title="<?php echo $text_license_agreement; ?>"><p><?php echo str_replace( array( "\n", "\r" ), array( "<br>", "" ), $text_license_text ); ?></p></div>').dialog({resizable: true,width:'800',height:'450'});
		return false;
	});

});
//--></script>