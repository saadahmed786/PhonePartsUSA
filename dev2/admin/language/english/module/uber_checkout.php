<?php
//-----------------------------------------
// Author: Qphoria@gmail.com
// Web: http://www.OpenCartGuru.com/
//-----------------------------------------

// Heading
$_['heading_title']   					= 'Uber Checkout (Q)';
// Text
$_['text_module']      					= 'Module';
$_['text_success']     					= 'Success: You have modified the module settings!';
$_['text_select']      					= 'select';
$_['text_radio']      					= 'radio';
$_['text_required']      				= 'Required';
$_['text_optional']      				= 'Optional';
$_['text_hidden']      					= 'Hidden';
$_['text_normal']      					= 'Normal';
$_['text_popup']      					= 'Popup';

$_['tab_general']      					= 'General';
$_['tab_address']      					= 'Address Form';

// Entry
$_['entry_status']    					= 'Status:<br/><span class="help">Enable/Disable the checkout system</span>';
$_['entry_style']    					= 'Style:<br/><span class="help">Choose Normal if you are using a standardized theme and want it to load as a standard page. Choose Popup if using a non-standard theme (like Shoppica and other Themeforest themes) and having theming problems or if you just want checkout as a popup.</span>';
$_['entry_payment_style']   			= 'Payment Choice Style:<br/><span class="help">Show select or radio style choices</span>';
$_['entry_shipping_style']  			= 'Shipping Choice Style:<br/><span class="help">Show select or radio style choices</span>';
$_['entry_payment_update_total']    	= 'Payment Reloads Total:<br/><span class="help">When changing the payment choice, should the totals reload? This is only needed if using a mod that adds a fee or discount based on the payment choice. Otherwise leave disabled to avoid unnecessary ajax updates.</span>';
$_['entry_comment_update_total']    	= 'Comment Reloads Total:<br/><span class="help">When updating the comment, should the totals reload? This is only needed if using a mod that adds a fee or discount based on the existence of comments. Otherwise leave disabled to avoid unnecessary ajax updates.</span>';
$_['entry_shipping_update_payment']    	= 'Shipping Reloads Payment:<br/><span class="help">When changing the shipping choice, should the payment options reload? This is only needed if using a mod that alters payment options. Otherwise leave disabled to avoid unnecessary ajax updates.</span>';
$_['entry_no_ship_address']    			= 'Disable Shipping Address:<br/><span class="help">If you do not want to allow shipping address to be different from the billing address, Set this to yes to disable the shipping address fields. This will force the billing address as the shipping address.</span>';
$_['entry_login']    					= 'Simplified Login/Register:<br/><span class="help">Enable if you want the simplified Login/Registration option. Disabled if you only want to use Uber Checkout for the Shipping/Payment/Confirmation page and use stock registration and login pages.</span>';
$_['entry_captcha']    					= 'Use Captcha for registration:<br/><span class="help">During registration, Should a <a href="http://en.wikipedia.org/wiki/CAPTCHA" target="_blank">captcha</a> be required?</span>';
$_['entry_newsletter_default']    		= 'Newsletter Default:<br/><span class="help">During registration, Should newsletter option default to Yes or No.</span>';

$_['entry_address_firstname']    		= 'Firstname:<br/><span class="help">Show Firstname on registration</span>';
$_['entry_address_lastname']    		= 'Lastname:<br/><span class="help">Show Lastname on registration</span>';
$_['entry_address_company']    			= 'Company:<br/><span class="help">Show Company on registration.</span>';
$_['entry_address_address_1']    		= 'Address 1:<br/><span class="help">Show Address 1 on registration</span>';
$_['entry_address_address_2']    		= 'Address 2:<br/><span class="help">Show Address 2 on registration</span>';
$_['entry_address_city']    			= 'City:<br/><span class="help">Show City on registration</span>';
$_['entry_address_telephone']    		= 'Telephone:<br/><span class="help">Show Telephone on registration</span>';
$_['entry_address_fax']    				= 'Fax:<br/><span class="help">Show Fax on registration</span>';
if (version_compare(VERSION, '1.5.2.2', '>=')) {
$_['help_company']    					= '<span class="help">Note: Company ID and Tax ID fields are controlled by the "Admin->Sales->Customer->Customer Group" menu</span>';
} else {
$_['help_company']    					= '';
}
?>
