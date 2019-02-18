<?php
// Heading
$_['heading_title']       			= 'Ajax Quick Checkout ';

// Text
$_['text_module']        		 	= 'Modules';
$_['text_success']      		 	= 'Success: You have modified module welcome!';
	
// Entry
$_['entry_status']       			= 'Status:';
$_['entry_sort_order']    			= 'Sort Order:';
$_['settings_yes']   				= 'Yes';
$_['settings_no']   				= 'No';
$_['settings_display']   			= 'Display';
$_['settings_require']   			= 'Require';
$_['settings_enable'] 				= 'Enable';
$_['settings_select'] 				= 'Dropdown Select';
$_['settings_image'] 				= 'Display images';
$_['settings_second_step'] 			= 'Require second step for payment<br /><small>In OpenCart there are two types of payment methods – some require an order_id and some don\'t. This creates an extra steps for those, that need the order_id, since it is created on the first step (like PayPal). But there are those, that don\'t need it (like cash on delivery). We went ahead and set it up for you so that you don\'t do it yourself, but if you have a custom payment method, you can require the second step yourself.</small>
';


// Checkout
$_['checkout_heading']   			= '<h1>Ajax Quick Checkout from <a href="http://www.opencart.com/index.php?route=extension/extension&filter_username=Dreamvention" target="_blank">Dreamvention</a> <small>Powerful things should be simple!</small></h1> ';
$_['checkout_intro']   				=  '
<div class="intro-block">
<strong>Customize your Ajax Quick Checkout</strong><br>
<ul>
<li>As you already know that the checkout has many fields. Most of them you actually do not need. <br />
Here you can check the ones you need and hide those, that are not necessary.
Display - option to show or hide a field. <br />
Require - option to validate the value or not.<br /><br />
<strong>Warning:</strong> Do not require those fields that you have not displayed. This will prevent customers from making a purchase.</li></ul></div>
<div class="intro-block">
<strong>Sort the fields and steps of the checkout page</strong><br>
<ul>
<li>simply drag-n-drop the fields where you want them to be.</li>
<li>Fields can be sorted inside one step.</li>
<li>Steps can be sorted between each other and also inside different columns</li>
<li>There can be 1 to 3 columns in your checkout page.</li>
<li>You can add module positions as steps. For this you will need to have <a href="http://www.opencart.com/index.php?route=extension/extension/info&extension_id=6916" target="_blank">Extra Positions 1.3.10 or above</a></li>
</ul>
</div>
<div class="intro-block">
<strong>Save and share settings.</strong>
<ul>
<li>
You can save your checkout settings by using the Bulk settings option. <br />
1. Check the checkbox for bulk settings<br />
2. Copy the settings data and save it to a text file.</li>
<li>Update your settings fast <br />
1. Check the checkbox for bulk settings<br />
2. Paste the settings data into the field, replacing the data inside it.<br />
3. Save it.</li>
</ul>
</div>
<br style="clear:both" />';
$_['checkout_intro_display']   		= 'Show/hide Intro &#8250;';
$_['checkout_quickcheckout']   		= 'Quick Checkout:<br>
<small>(Enable - turn all vqmod on. Display - allow the new checkout to show)</small>';
$_['checkout_debug']   				= 'Quick Checkout Debug mode:<br>
<small>(Debug shows debug pannel, session array and php Notices. For Developers only.)</small>';
$_['checkout_compatibility']   		= 'Force default compatibility:<br>
<small>(in case you have lots of modules, this may help solve most of the compatibility issues)</small>';
$_['checkout_defalt_option']   		= 'Select default option at checkout:<br>
<small>(set the default option. Still, if the customer already visited the checkout, opencart will keep his old option as default)</small>';
$_['checkout_defalt_option_register'] = 'register';
$_['checkout_defalt_option_guest']    = 'guest';
$_['checkout_display_options']    = 'Display options:<br>
<small>(You can hide some or all options from the checkout. It does not disable it - it just hides them)</small>';
$_['checkout_display_login_text']    = 'login';
$_['checkout_min_order']    = 'Set min order amount:<br>
<small>(and the massage in case it is not reached. 0 - no min purchase)</small>';
$_['checkout_min_order_tag']    = '<small>Use this tag in text on the right to inform about the min order amount</small>';
$_['checkout_display_only_register_options']    = 'Display options:<br>
<small>(Step 2 will display only for register users)</small>';
$_['checkout_display_only_register_text']    = 'only register';

$_['checkout_guest_step_1']   		= 'Guest Customer info';
$_['checkout_register_step_1']   	= 'Registrate Customer info';
$_['checkout_firstname']   			= 'Firstname:';
$_['checkout_lastname']   			= 'Lastname:';
$_['checkout_email']   				= 'Email:<br>
<small>(If the email is not displayed, it has to have a default email that  will be used on order creation)</small>';
$_['checkout_telephone']  	 		= 'Telephone:';
$_['checkout_fax']   				= 'Fax:';

$_['checkout_guest_step_2'] 		= 'Guest Payment address';
$_['checkout_register_step_2'] 		= 'Registrate Payment address';
$_['checkout_payment_address'] 		= 'Payment address:';
$_['checkout_company']    			= 'Company:';
$_['checkout_customer_group']    	= 'Customer Group (<a href="%s">opencart settings</a>):';
$_['checkout_company_id']    		= 'Company id:';
$_['checkout_tax_id']    			= 'Tax id:';
$_['checkout_address_1']  			= 'Address 1:';
$_['checkout_address_2']  			= 'Address 2:';
$_['checkout_city']   				= 'City:';
$_['checkout_postcode']   			= 'Postcode (<a href="%s">opencart settings</a>):';
$_['checkout_country']   			= 'Country:';
$_['checkout_zone']  				= 'Zone:';
$_['checkout_newsletter']   		= 'Newsletter:';
$_['checkout_password']   			= 'Password:';
$_['checkout_privacy_agree']   		= 'Agree to Privacy Policy:';

$_['checkout_guest_step_3'] 		= 'Guest Shipping address:';
$_['checkout_register_step_3'] 		= 'Registrate Shipping address:';
$_['checkout_shipping_address'] 	= 'Always display open:';
$_['checkout_shipping_address_enable'] 	= 'Enable Shipping address:';



$_['checkout_step_4']   					= 'Shipping';
$_['checkout_shippint_method']   			= 'Shipping Method:';
$_['checkout_shipping_method_methods']   	= 'Shipping methods:';
$_['checkout_shipping_method_title']  		= 'Shipping Method Title:';
$_['checkout_shipping_method_date']  		= 'Shipping Date:<br>
<small>(Show date input and set the title of the field)</small>';
$_['shipping_method_date_picker']  		= 'With Date picker';

$_['checkout_shipping_method_comment']  	= 'Shipping comment:';

$_['checkout_step_5']   				= 'Payment';
$_['checkout_payment_method']   		= 'Payment Method:';
$_['checkout_payment_method_methods']   = 'Payment methods:<br>
<small>(You can display them in list with icons or dropdown select style. Images are kept in the <a onclick="image_upload(\'image\', \'thumb\');">image folder/payment</a>)</small>';
$_['checkout_payment_method_comment']   = 'Payment comment:';
$_['checkout_payment_method_agree']   	= 'Agree to Conditions:';
$_['checkout_payment_method_methods_steps']   	= 'Set payment methods steps: <br><small>(unchecked - leave one step, checked -  require second step)</small>';


$_['checkout_step_6']   				= 'Confirm';
$_['checkout_confirm_images']   		= 'Show images in cart:';
$_['checkout_confirm_name']   			= 'Show name in cart:';
$_['checkout_confirm_model']   			= 'Show model in cart:';
$_['checkout_confirm_quantity']   		= 'Show quantity in cart:';
$_['checkout_confirm_price']   			= 'Show price in cart:';
$_['checkout_confirm_total']   			= 'Show total in cart:';
$_['confirm_coupon_display']   			= 'Show Coupon option:';
$_['confirm_voucher_display']   		= 'Show Voucher option:';
$_['confirm_2_step_cart_display']		= 'Show cart on second step of confirm:';


$_['checkout_design']  					= 'Design checkout';
$_['checkout_labels_float']				= 'Position labels:';
$_['checkout_labels_float_left']   		= 'Float left';
$_['checkout_labels_float_clear'] 		= 'Float above';
$_['checkout_force_default_style'] 		= 'Force default style <br /><small>If you have a custom theme and the checkout looks bad, try forcing the default style.</small>';


$_['checkout_design_cutomer_info']  		= 'Customer info';
$_['checkout_design_shipping_address']  	= 'Shipping address';
$_['checkout_design_shipping_method']  		= 'Shipping method';
$_['checkout_design_payment_method']  		= 'Payment method';
$_['checkout_design_confirm']  				= 'Confirm';
$_['checkout_design_extra1']  				= 'Checkout position 1';
$_['checkout_design_extra2']  				= 'Checkout position 2';
$_['checkout_design_extra3']  				= 'Checkout position 3';

$_['checkout_style']  					= 'Style your checkout:<br>
<small>(Style checkout by setting column width (in %), moving and sorting steps and adding custom CSS. If the checkout doesn\'t fit into your content width, just make the columns more narrow)</small>';
$_['checkout_style_css']  					= 'Add custom CSS styles:<br>
<small>(you can style your checkout by adding CSS styles here)</small>';

$_['positions_needed']  				= 'To turn on the Extra positions, you need to install <a href="http://www.opencart.com/index.php?route=extension/extension/info&extension_id=6916" target="_blank">Extra Positions 1.3.10 or above</a>';

$_['checkout_settings']  				= 'Bulk Settings:<br>
<small>(You can save and share settings by copy/pasting into the hidden field. You can also save your settings by copying them to a text file and using it in the future)</small>';

$_['checkout_settings_checkbox']        = 'Use bulk settings<br>
<small>(When checked, the settings set above will be ignored, and the settings in the field below will be uploaded)</small>';


// Error
$_['error_permission']   				= 'Warning: You do not have permission to modify module welcome!';
?>
