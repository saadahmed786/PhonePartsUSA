<?php
//==============================================================================
// New Returns E-mail v155.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

$version = 'v155.2';

// Heading
$_['heading_title']				= 'New Returns E-mail';

// Buttons
$_['button_save_exit']			= 'Save & Exit';
$_['button_save_keep_editing']	= 'Save & Keep Editing';
$_['button_toggle_ckeditors']	= 'Toggle CKEditors';

// Entries
$_['entry_status']				= 'Status:';
$_['entry_admin_email']			= 'Admin E-mail Address(es):<br /><span class="help">Enter the e-mail address(es) notified when new returns are created. Separate multiple addresses by , (commas). Leave blank to send no admin e-mail.</span>';
$_['entry_admin_subject']		= 'Admin E-mail Subject:';
$_['entry_admin_message']		= 'Admin E-mail Message:';
$_['entry_email_customer']		= 'E-mail Customer:<br /><span class="help">Select whether to e-mail the customer a copy of their return request.</span>';
$_['entry_customer_subject']	= 'Customer E-mail Subject:';
$_['entry_customer_message']	= 'Customer E-mail Message:';

// Text
$_['text_saving']				= 'Saving...';
$_['text_saved']				= 'Saved!';
$_['text_shortcodes']			= '
	<tr><td colspan="4">For "Subject" and "Message" fields, use the following shortcodes to insert information about the return request:</td></tr>
	<tr><td>
			[store_name]<br />
			[store_url]<br />
			[store_owner]<br />
			[store_address]<br />
			[store_email]<br />
			[store_telephone]<br />
			[store_fax]<br />
		</td><td>
			[return_id]<br />
			[date_added]<br />
			[return_reason]<br />
			[return_status]<br />
			[comment]<br />
		</td><td>
			[order_id]<br />
			[date_ordered]<br />
			[product]<br />
			[model]<br />
			[quantity]<br />
			[opened]<br />
		</td><td>
			[customer_id]<br />
			[firstname]<br />
			[lastname]<br />
			[email]<br />
			[telephone]<br />
		</td>
	</tr>
	<tr><td colspan="4">You can also use any column from the "order" table by using a shortcode in the format [order_COLUMN], where COLUMN is the name of the column you want to pull from.</td></tr>
';
$_['text_admin_subject']		= '[store_name]: RMA #[return_id] Created';
$_['text_admin_message']		= '
	<p>You have received a return request:</p>
	<p><b>Return Info</b><br />RMA #: [return_id]<br />Date Added: [date_added]<br />Return Reason: [return_reason]<br />Return Status: [return_status]<br />Comment: [comment]</p>
	<p><b>Order Info</b><br />Order ID: [order_id]<br />Date Ordered: [date_ordered]<br />Product: [product] ([model])<br />Quantity: [quantity]<br />Opened: [opened]</p>
	<p><b>Customer Info</b><br />Customer ID: [customer_id]<br />First Name: [firstname]<br />Last Name: [lastname]<br />E-mail Address: [email]<br />Telephone: [telephone]</p>
';
$_['text_customer_subject']		= '[store_name]: RMA #[return_id] Created';
$_['text_customer_message']		= '
	<p>Thank you for submitting your return request. Your RMA # is [return_id]. We will respond to your request as soon as possible, and appreciate your patience as we work to resolve the issue.</p>
	<p>[store_name]<br />[store_url]</p>
	<p><b>Return Info</b><br />RMA #: [return_id]<br />Date Added: [date_added]<br />Return Reason: [return_reason]<br />Return Status: [return_status]<br />Comment: [comment]</p>
	<p><b>Order Info</b><br />Order ID: [order_id]<br />Date Ordered: [date_ordered]<br />Product: [product] ([model])<br />Quantity: [quantity]<br />Opened: [opened]</p>
	<p><b>Customer Info</b><br />First Name: [firstname]<br />Last Name: [lastname]<br />E-mail Address: [email]<br />Telephone: [telephone]</p>
';

// Copyright
$_['copyright']					= '<div style="text-align: center; margin: 15px" class="help">' . $_['heading_title'] . ' ' . $version . ' &copy; <a target="_blank" href="http://www.getclearthinking.com">Clear Thinking, LLC</a></div>';

// Standard Text
$_['standard_module']			= 'Modules';
$_['standard_shipping']			= 'Shipping';
$_['standard_payment']			= 'Payments';
$_['standard_total']			= 'Order Totals';
$_['standard_feed']				= 'Product Feeds';
$_['standard_success']			= 'Success: You have modified ' . $_['heading_title'] . '!';
$_['standard_error']			= 'Error: You do not have permission to modify this extension!';
?>