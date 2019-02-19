<?php
//-----------------------------------------
// Author: Qphoria@gmail.com
// Web: http://www.OpenCartGuru.com/
//-----------------------------------------

// Heading
$_['heading_title']      = 'Paypal Express New';

// Text
$_['text_payment']          = 'Payment';
$_['text_success']          = 'Success: You have modified the account details!';
$_['text_development']      = '<span style="color: green;">Ready</span>';
$_['text_mail']   		    = 'mail';
$_['text_log']   		    = 'log';
$_['text_left']             = 'Left';
$_['text_right']            = 'Right';
$_['text_column_left']      = 'Left';
$_['text_column_right']     = 'Right';
$_['text_content_top']      = 'Content Top';
$_['text_content_bottom']   = 'Content Bottom';
$_['text_login']            = 'Login';
$_['text_billing']          = 'Billing';
$_['text_sale']          	= 'Sale';
$_['text_auth']          	= 'Auth';

// Entry
$_['entry_status']          = 'Status:';
$_['entry_geo_zone']        = 'Geo Zone:';
$_['entry_order_status']    = 'Verified Order Status:<br/><span class="help">The order will be set to this status when payment is complete and the paypal member is <a href="https://www.paypal.com/va/cgi-bin/webscr?cmd=xpt/Marketing/securitycenter/buy/VerificationFAQ-outside" target="_blank">Paypal Verified</a>.</span>';
$_['entry_unverified_order_status']    = 'Unverified Order Status:<br/><span class="help">The order will be set to this status when payment is complete but the paypal member is not <a href="https://www.paypal.com/va/cgi-bin/webscr?cmd=xpt/Marketing/securitycenter/buy/VerificationFAQ-outside" target="_blank">Paypal Verified</a>.</span>';
$_['entry_apiuser']         = 'API Username:<br/><span class="help">You will need to create your Paypal API Access for this part. See the <a href="http://www.youtube.com/watch?v=TMP2llxOuKo" target="_blank">video tutorial</a>.</span>';
$_['entry_apipass']         = 'API Password:<br/><span class="help">You will need to create your Paypal API Access for this part. See the <a href="http://www.youtube.com/watch?v=TMP2llxOuKo" target="_blank">video tutorial</a>.</span>';
$_['entry_apisig']          = 'API Signature:<br/><span class="help">You will need to create your Paypal API Access for this part. See the <a href="http://www.youtube.com/watch?v=TMP2llxOuKo" target="_blank">video tutorial</a>.</span>';
$_['entry_logo']      		= 'Logo Image:<br /><span class="help">If you have SSL on your site, and want to use your main site logo leave this blank. Otherwise, if you are not using SSL on your site or want to use a custom logo, enter the secure url of your image here. If you have multiple stores and want to use a different logo for each store, add a * in the place where you want the store id to be auto-populated. For example: image/logo*.jpg. When using store 0 it will look for logo0.jpg, using store 1 will look for logo1.jpg, etc.</span>';
$_['entry_sort_order']      = 'Sort Order:';
$_['entry_test']            = 'Sandbox Mode:<br /><span class="help">If you are using your paypal sandbox account, set this to yes. <a href="https://developer.paypal.com/" target="_blank">more info</a></span>';
$_['entry_checkout_cart']   = 'Display Express Checkout button on "checkout/cart" page:';
$_['entry_module_cart']     = 'Display Express Checkout button on "module/cart" page:';
$_['entry_login']  		    = 'Display Express Checkout button on "login" page:';
$_['entry_module']          = 'Display Paypal Express button sidebox:<?br/><span class="help">Versions of OpenCart older than 1.4.7 may need this if the other buttons options are not displaying</span>';
$_['entry_checkout']        = 'Show On Normal Checkout:<br/><span class="help">This will show Paypal Express as a payment option during normal non-express checkout. Since Paypal Express is considered an "Express" checkout, it doesn\'t make a lot of sense to show it during normal checkout. But if you want it, then you can enable it here.</span>';
$_['entry_module_position'] = 'Paypal Express button sidebox position:';
$_['entry_debug']           = 'Debug Mode:<br/><span class="help">This will log the background messaging between the store and paypal to a file called "ppx_debug.txt" in your store system/logs folder in FTP for troubleshooting purposes.</span>';
$_['entry_account']         = 'Automatically Create Account:<br /><span class="help">Set to yes to automatically create an account for guests based on the customer paypal address. Passwords will be randomly generated and emailed to the customer. If the email already exists, it will just log the customer in</span>';
$_['entry_landing']         = 'Landing Page:<br /><span class="help">When sent to paypal, this determines if they see the Login page or Credit Card Billing page by default.</span>';
$_['entry_payment_action']	= 'Payment Action:<br /><span class="help">Sale = Payment is made immediately. Auth = The money is only "held" for 3 days and you must manually accept it through your paypal account.</span>';
$_['entry_help']            = 'Help:';

// Help
$_['help_debug']            = '<span style="color:red;">If you are having problems or errors, please enable this and try checkout again.<br/> Then copy the ppx_debug.txt from your system/logs directory into an email to the developer.</span>';
$_['help_logo']             = ' You can use <a href="http://sslpic.com">www.sslpic.com</a> for free secure image hosting. <br/> It must be secure (https) to avoid the IE warning about secure and insecure items.';
$_['help']				    = 'Paypal Express Checkout is included with all standard paypal accounts. Get your Paypal Express Checkout API details by following <a href="http://help.wildapricot.com/display/DOC/Requesting+PayPal+API+Signature">this guide</a>';

// Error
$_['error_permission']      = 'Warning: You do not have permission to modify this payment module!';
$_['error_apiuser']         = 'API Username Required!';
$_['error_apipass']         = 'API Password Required!';
$_['error_apisig']          = 'API Signature Required!';
?>