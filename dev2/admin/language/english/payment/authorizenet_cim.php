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

// Heading
$_['heading_title']      = 'Authorize.Net (CIM)';

// Text 
$_['text_payment']       = 'Payment';
$_['text_success']       = 'Success: You have modified Authorize.Net (CIM) account details!';
$_['text_authorizenet_cim']   = '<img src="view/image/payment/authorizenet.png" style="width:80px" alt="'.$_['heading_title'].'" title="'.$_['heading_title'].'" style="border: 1px solid #EEEEEE;" />';
$_['text_test']          = 'Test';
$_['text_sandbox']          = 'Sandbox';
$_['text_live']          = 'Live';
$_['text_authorization'] = 'Authorization';
$_['text_authorizeandcapture'] = 'Authorize and Capture';
$_['text_capture']       = 'Capture';

// Entry
$_['entry_login']        = 'Login ID:';
$_['entry_key']          = 'Transaction Key:';
$_['entry_hash']         = 'MD5 Hash:';
$_['entry_server']       = 'Transaction Server:';
$_['entry_mode']         = 'Transaction Mode:';
$_['entry_method']       = 'Transaction Method:';
$_['entry_total']        = 'Total:<br /><span class="help">The checkout total the order must reach before this payment method becomes active.</span>';
$_['entry_order_status'] = 'Order Status:';
$_['entry_order_status_info'] = 'Which status order shuld be after successful payment verified.';
$_['entry_geo_zone']     = 'Geo Zone:'; 
$_['entry_status']       = 'Status:';
$_['entry_sort_order']   = 'Sort Order:';
$_['entry_daily_log']   = 'Create Daily Log File:';
$_['entry_delete_notfound_cimid']   = 'Delete Not Found Cim Ids:';
$_['entry_enable_cim_adress']   = 'Add Shipping Adress To Cim Orders:';
$_['entry_log_responses']   = 'Log Authorize.Net Responses<br>(Adviced For Test Purpose only. Not adviced for production use):';
$_['entry_save_shiping_address']   = 'Save Shiping Adress to Cim On Checkout:';
$_['entry_save_use_jquerydialog']   = 'Use Jquery Dialog :';
$_['entry_validation_mode']   = 'Validation Mode';
$_['validation_mode_test']   = 'Test Mode';
$_['validation_mode_live']   = 'Live Mode';
$_['validation_mode_none']   = 'None(Default)';
$_['entry_send_email_onerror']   = 'Send Email On Error<br /><span class="help">Sends Email to store email if System gets Error To Connect CIM server or fetch the customer profile</span>';
$_['entry_enable_shipping_adress']   = 'Enable Shipping Adress:<br /><span class="help">Enables Creating Shipping Adress On Cim server and Adds Shipping Detail To CIM Order Transection request.</span>';
$_['entry_cim_fill_line_items']   = 'Fill Line Items:<br /><span class="help">Adds line items To CIM Order Transection request.</span>';


$_['entry_cim_require_billing_adress']   	= 'Force Billing Adress With Payment Details<br /><span class="help">Forces User To Enter Billing Adress For his/her Credir Card Or Bank account.</span>';
$_['entry_cim_held_notificatin_emails']   	= 'On Hold - Notification Emails:<br /><span class="help">If One of the up status code returns The email Alett will be sent To This Email Adresses(seperate with ",")</span>';
$_['entry_cim_held_notify_customer']   	= 'On Hold - Notify Customer:<br /><span class="help">Send Customer Email If Order Status changed to "On Hold - Order Status"</span>';
$_['entry_cim_held_rule_list']   		= 'On Hold - Rule List:<br /><span class="help">If One of the combination code returns The order status will change to defined order tatus</span>';
$_['text_held_rule_list_error']   		= 'You Have Error In your list. make sure each group have 4 sub setting and order status id is exist';
$_['text_held_rule_list_info']   		= '<b>Code:</b> RESPONSE CODE;RESPONSE REASON CODE;AVS RESPONSE CODE;ORDER STATUS ID|RESPONSE CODE;RESPONSE REASON CODE;AVS RESPONSE CODE;ORDER STATUS ID;<br>
<b>Ex:</b> 4;193;ALL;8|4;ALL;ALL;1 <br>
<b>Important Note:</b> Put More Generic Rules At the End of list.';
$_['text_order_status_list']   		= '<b>Order Status List:</b>';


// Error 
$_['error_permission']   = 'Warning: You do not have permission to modify payment Authorize.Net (SIM)!';
$_['error_login']        = 'Login ID Required!';
$_['error_key']          = 'Transaction Key Required!';

$_['text_disable_bank_payment']       = 'Diable Bank Payment';

?>