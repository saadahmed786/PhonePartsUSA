<?php
// Heading
$_['heading_title']      = 'PayPal Payflow Pro';

// Text
$_['text_payment']       = 'Payment';
$_['text_success']       = 'Success: You have modified the PayPal Payflow Pro account details!';
$_['text_pp_payflow_pro'] = '<a onclick="window.open(\'https://www.paypal.com/cgi-bin/webscr?cmd=_payflow-pro-overview-outside\');"><img src="view/image/payment/paypal.png" alt="PayPal Payflow Pro" title="PayPal Payflow Pro" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_live']          = 'Live';
$_['text_test']          = 'Test';
$_['text_sale']          = 'Sale';
$_['text_authorization'] = 'Authorization';

// Entry
$_['entry_partner']      = 'Partner:';
$_['entry_vendor']       = 'Merchant Login/Vendor:';
$_['entry_username']     = 'User:';
$_['entry_password']     = 'Password:<br /><span class="help">These fields are identical to the <a href="https://manager.paypal.com/" target="_blank">Payflow Pro Manager Login</a>.</span>';
$_['entry_server']       = 'Server:<br /><span class="help">Use the live or testing (sandbox) gateway server to process transactions?</span>';
$_['entry_transaction']  = 'Transaction Method:';
$_['entry_timeout']      = 'Timeout:<br /><span class="help">How long (in seconds) to wait for a response from the credit card processor after the customer clicks "confirm."</span>';
$_['entry_timeout_order_status']      = 'Timeout Order Status:<br /><span class="help">The status to give an order upon the credit card processor taking longer than the timeout time specified above to respond.</span>';
$_['entry_fps_order_status'] = 'FPS Order Status:<br /><span class="help">The status to give an order upon a Payflow Pro transaction that triggers the Fraud Protection Service and is placed under review in the Payflow Pro backend before further processing.</span>';
$_['entry_order_status'] = 'Successful Order Status:<br /><span class="help">The status to give an order upon a successful Payflow Pro transaction.</span>';
$_['entry_geo_zone']     = 'Geo Zone:<br /><span class="help">Optionally restrict this payment method to a specific Geo Zone.</span>';
$_['entry_total']        = 'Total:<br /><span class="help">The checkout total the order must reach before this payment method becomes active.</span>';
$_['entry_invnum']       = 'Merchant Invoice Number:<br /><span class="help">(Advanced) Optionally override all transactions with a static INVNUM (max length 9).<br /><a href="https://cms.paypal.com/cms_content/en_US/files/developer/PP_PayflowPro_Guide.pdf" target="_blank">See documentation</a></span>';
$_['entry_idprefix']     = 'X-VPS-Request-ID Prefix:<br /><span class="help">(Advanced) Optional prefix for all order IDs sent to the Server.<br /><a href="http://www.paypal.com/en_US/pdf/PayflowPro_HTTPS_Interface_Guide.pdf" target="_blank">See documentation</a></span>';
$_['entry_comment1']     = 'Comment 1:<br /><span class="help">(Advanced) Optional comment added to each transaction (max length 128).<br /><a href="http://www.paypal.com/en_US/pdf/PayflowPro_HTTPS_Interface_Guide.pdf" target="_blank">See documentation.</a></span>';
$_['entry_comment2']     = 'Comment 2:<br /><span class="help">(Advanced) Another comment line.</span>';
$_['entry_comment1_input']     = 'Available variables: {id}, {ip}, {total_models}, {total_products}, {cart}';
$_['entry_comment2_input']     = 'Available variables: {id}, {ip}, {total_models}, {total_products}, {cart}';
$_['entry_status']       = 'Status:';
$_['entry_sort_order']   = 'Sort Order:';

// Error
$_['error_permission']   = 'Warning: You do not have permission to modify the payment module settings for PayPal Payflow Pro!';
$_['error_partner']      = 'Partner Required!';
$_['error_vendor']       = 'Merchant Login/Vendor Required!';
$_['error_username']     = 'User Required!';
$_['error_password']     = 'Password Required!';
$_['error_timeout']      = 'Must be between 1 and 450 inclusive.';
?>
