<?php
//==============================================================================
// Form Builder v154.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

$version = 'v154.2';

// Heading
$_['heading_title']				= 'Form Builder';

// Form List
$_['button_create_new_form']	= 'Create New Form';
$_['column_status']				= 'Status';
$_['column_name']				= 'Name';
$_['column_edit']				= 'Edit';
$_['column_report']				= 'Report';
$_['column_copy']				= 'Copy';
$_['column_delete']				= 'Delete';
$_['text_confirm']				= 'This operation cannot be undone. Continue?';

// Form Report
$_['text_report']				= 'Report';
$_['button_show_blank']			= 'Show Blank Responses';
$_['button_hide_blank']			= 'Hide Blank Responses';
$_['tab_list']					= 'List';
$_['tab_summary']				= 'Summary';
$_['help_list']					= '
	<ul class="help">
		<li>Click the "Answered" box to toggle whether the inquiry has been answered.</li>
		<li>Click a customer name to view their information.</li>
		<li>Click an e-mail address to send an e-mail to the customer using your mail application.</li>
		<li>Click a file name to view it in your browser, or the "download" link to download the file.</li>
	</ul>
';
$_['column_answered']			= 'Answered';
$_['column_customer']			= 'Customer';
$_['column_date_added']			= 'Date Added';
$_['column_ip_address']			= 'IP Address';
$_['column_responses']			= 'Responses';
$_['text_guest']				= 'Guest';
$_['text_download']				= 'download';
$_['text_number_of_uploads']	= '<em># of uploads</em>';
$_['column_field_key']			= 'Field Key';
$_['column_response']			= 'Response';
$_['column_count']				= 'Count';

// Form Edit
$_['button_save_exit']			= 'Save & Exit';
$_['button_save_keep_editing']	= 'Save & Keep Editing';
$_['text_saving']				= 'Saving...';
$_['text_saved']				= 'Saved!';
$_['tab_general']				= 'General';
$_['tab_locations']				= 'Locations';
$_['tab_fields']				= 'Fields';
$_['tab_errors']				= 'Errors';
$_['tab_success']				= 'Success';
$_['tab_email']					= 'E-mail';

$_['entry_status']				= 'Status:';
$_['entry_form_name']			= 'Form Name:';
$_['entry_password_required']	= 'Password Required To Access:<span class="help">Leave blank to allow anyone to access the form.</span>';
$_['entry_enter_password']		= '"Enter Password" Message:<span class="help">If requiring a password, enter the message displayed to customers informing them they need to enter a password.</span>';
$_['entry_password_overlay']	= 'Password Overlay Opacity<span class="help">100% = completely opaque<br />0% = completely transparent</span>';

$_['help_locations']			= '
	<ul style="line-height: 1.5">
		<li><b>Module Box Class:</b> Enter your theme\'s CSS class for module boxes. This is <span style="font-family: monospace">box</span> in the default theme. Leave blank to not display a box.</li>
		<li><b>Module Heading Class:</b> Enter your theme\'s CSS class for module box headings. This is <span style="font-family: monospace">box-heading</span> in the default theme. Leave blank to not display a heading for the box.</li>
		<li><b>Module Content Class:</b> Enter your theme\'s CSS class for module box content. This is <span style="font-family: monospace">box-content</span> in the default theme. Leave blank if not using a module box.</li>
		<li><b>Hide CSS Selectors:</b> Optionally enter the CSS selectors to be hidden on the form page. Separate selectors by , (commas). This can be useful when displaying the form as its own page, so you can hide elements on the standard information page that you don\'t need. For example, to hide the usual "Continue" button that appears on information pages, which has <span style="font-family: monospace">class="buttons"</span>, you would enter <span style="font-family: monospace">.buttons</span>
	</ul>
';
$_['column_status']				= 'Status';
$_['column_display']			= 'Display';
$_['text_module_box_class']		= 'Module Box Class:';
$_['text_module_heading_class']	= 'Module Heading Class:';
$_['text_module_content_class']	= 'Module Content Class:';
$_['text_hide_css_selectors']	= 'Hide CSS Selectors:';
$_['column_stores']				= 'Stores';
$_['column_location']			= 'Location';
$_['text_layout']				= 'Layout:';
$_['text_position']				= 'Position:';
$_['text_sort_order']			= 'Sort Order:';
$_['button_create_form_page']	= 'Create Form Page';
$_['help_create_form_page']		= 'Creating a form page will perform the following operations:<ol><li>A new layout will be created specifically for the form page.</li><li>A new information page will be created for the form, using the form name.</li><li>A layout override will be applied to that information page using the new layout.</li><li>A module instance will be created and set for that layout, with the options set so the form appears as normal page content instead of a module.</li></ol>This means you can use that information page as the form page, which gives you the ability to set an SEO keyword for the page, and (for OpenCart versions 1.5.3 or later) choose whether to include the page in the bottom footer.';
$_['help_enter_seo_keyword']	= 'Enter the SEO keyword used for the information page:';
$_['help_display_in_footer']	= 'Display page link in bottom footer:';
$_['text_creating']				= '<p style="font-size: 16px; font-weight: bold; line-height: 5; text-align: center; color: #000">Creating...</p>';
$_['text_success']				= '<p style="font-size: 16px; font-weight: bold; line-height: 5; text-align: center; color: #080">Success!</p>';
$_['text_failed']				= '<p style="font-size: 16px; font-weight: bold; line-height: 5; text-align: center; color: #B00">Failed</p>';
$_['entry_nonstandard']			= 'Code for Non-Standard Locations:';
$_['help_nonstandard']			= 'To embed the form in a location that is not a standard module position, paste this code in the template file where you want the form to appear. If you want a box around the form, you will need to paste &lt;div&gt; elements around the code with the appropriate classes for your theme. You can view these classes in a file such as /catalog/view/theme/YOURTHEME/template/module/information.tpl. For example, in the default theme this would look like:';
$_['text_this_will_appear']		= 'This will appear after saving the form and reloading the page';

$_['button_toggle_ckeditors']	= 'Toggle CKEditors';
$_['text_global_settings']		= 'Global Settings';

$_['text_captcha']				= 'Captcha';
$_['text_checkboxes']			= 'Checkboxes';
$_['text_column_break']			= 'Column Break';
$_['text_date_time']			= 'Date / Time';
$_['text_email_address']		= 'E-mail Address';
$_['text_file_upload']			= 'File Upload';
$_['text_hidden_data']			= 'Hidden Data';
$_['text_html_block']			= 'HTML Block';
$_['text_radio_buttons']		= 'Radio Buttons';
$_['text_row_break']			= 'Row Break';
$_['text_select_dropdown']		= 'Select Dropdown';
$_['text_submit_button']		= 'Submit Button';
$_['text_text_input']			= 'Text Input';

$_['text_field_type_help']		= 'To add a field type, drag and drop it below';
$_['text_field_help']			= 'Drop field types here';
$_['text_help']					= 'Help';

$_['text_key']					= 'Key';
$_['text_required']				= 'Required';
$_['text_name']					= 'Name';
$_['text_choices']				= 'Choices';
$_['text_default_value']		= 'Default Value';
$_['text_s']					= '(s)';
$_['text_type']					= 'Type';
$_['text_date']					= 'Date';
$_['text_time']					= 'Time';
$_['text_date_and_time']		= 'Date & Time';
$_['text_include_confirmation']	= 'Include E-mail Confirmation Field';
$_['text_confirm_field_name']	= 'Confirm Field Name';
$_['text_file_size_limit']		= 'File Size Limit (KB)';
$_['text_allowed_extensions']	= 'Allowed File Extensions';
$_['text_display_in_email']		= 'Display in Customer\'s E-mail';
$_['text_data']					= 'Data';
$_['text_number_of_selections']	= 'Number of Selections Allowed';
$_['text_redirect_on_success']	= 'Redirect on Success';
$_['text_button_text']			= 'Button Text';
$_['text_success_message']		= 'Success Message';
$_['text_text']					= 'Text';
$_['text_password']				= 'Password';
$_['text_textarea']				= 'Textarea';
$_['text_min_length']			= 'Min Length';
$_['text_max_length']			= 'Max Length';
$_['text_allowed_characters']	= 'Allowed Characters';

$_['help_required']				= '<em><b>Required</b></em><br />Select whether the field requires a response.<br /><br />';
$_['help_key']					= '<em><b>Key</b></em><br />Enter a key for the field, used to track responses in the database. Only alphanumeric characters are allowed. The key will not be displayed to customers.<br /><br />';
$_['help_name']					= '<em><b><span style="color: #F00">*</span> Name</b></em><br />Enter the name for the field, displayed next to the field input. HTML is supported.<br /><br />';
$_['help_choices']				= '<em><b><span style="color: #F00">*</span> Choices</b></em><br />Enter the choices from which customers can select. Separate the choices by ; (semi-colons).<br /><br />';
$_['help_default_value']		= '<em><b><span style="color: #F00">*</span> Default Value</b></em><br />Optionally enter the default value that is displayed when the form loads.<br /><br />';
$_['help_default_values']		= '<em><b><span style="color: #F00">*</span> Default Value(s)</b></em><br />Optionally enter the default values that are selected when the form loads. Separate the defaults by ; (semi-colons).<br /><br />';
$_['help_date']					= '<em><b>Type</b></em><br />Select the type of field (date, time, or date & time).<br /><br />';
$_['help_email']				= '<em><b>Include E-mail Confirmation Field</b></em><br />Select whether to include an e-mail confirmation field after the e-mail field.<br /><br /><em><b><span style="color: #F00">*</span> Confirm Name Field</b></em><br />If including an e-mail confirmation field, enter the name displayed next to the field input. HTML is supported.<br /><br />';
$_['help_file']					= '<em><b>File Size Limit (KB)</b></em><br />Enter the maximum file size allowed in KB for files uploaded. (1 MB = 1024 KB)<br /><br /><em><b>Allowed File Extensions</b></em><br />Enter the allowed file extensions for files uploaded, separated by , (commas).<br /><br /> <em><b>Success Message</b></em><br />Enter the message displayed after the form is successfully submitted.<br /><br />';
$_['help_hidden']				= '<em><b>Display in Customer\'s E-mail</b></em><br />Select whether to display the hidden data in the customer\'s confirmation e-mail.<br /><br /><em><b><span style="color: #F00">*</span> Data</b></em><br />Enter the data for the hidden field.<br /><br />';
$_['help_html']					= '<span style="color: #F00">*</span> Enter the HTML code to be displayed. Click the "Toggle CKEditors" button in the top-right corner of the page to enable/disable the CKEditors.<br /><br />';
$_['help_select']				= '<em><b>Number of Selections Allowed</b></em><br />Enter the number of selections allowed. If greater than 1, the field will appear as a multiple-select box instead of a dropdown menu.<br /><br />';
$_['help_submit']				= '<em><b>Redirect on Success</b></em><br />Optionally enter the URL where the customer is redirected, after displaying the success message. Leave this field blank to remain on the same page.<br /><br /><span style="color: #F00">*</span> <em><b>Button Text</b></em><br />Enter the text for the submit button. HTML is supported<br /><br /><span style="color: #F00">*</span> <em><b>Success Message</b></em><br />Enter the message displayed after the form is successfully submitted. HTML is supported.<br /><br />';
$_['help_text']					= '<em><b>Type</b></em><br />Select the type of field (single line field or textarea field).<br /><br /><em><b>Min Length</b></em><br />Optionally set the minimum response length required.<br /><br /><em><b>Max Length</b></em><br />Optionally set the maximum response length allowed.<br /><br />';
$_['help_allowed_characters']	= '<em><b>Allowed Characters</b></em><br />Optionally enter the characters allowed as a response for the field. For example, to only allow numbers and hyphens, you would enter: 01234567890-<br /><br />';
$_['help_asterisk']				= '<a style="color: #036; display: block; font-style: italic; margin: 10px 0;" onclick="$(this).next().toggle()">What does the red asterisk <span style="color: #F00">*</span> mean?</a><div style="display: none">Fields marked with <span style="color: #F00">*</span> mean that you can enter a query string variable in brackets to pull its value from the URL. (This will work even if SEO URLs are enabled, but you\'ll need to know what the usual query string variables are.) For example, if you enter <span style="font-family: monospace">[path]</span> and the URL contains <span style="font-family: monospace">&path=2_7_13</span>, the value <span style="font-family: monospace">2_7_13</span> will be displayed.<br /><br />One special exception: if the URL contains the product_id query string variable, you can also enter any column name in the "product" or "product_description" tables prefixed with "product_", and it will pull that information from the database. For example, if the URL contains <span style="font-family: monospace">&product_id=5</span> and the product with the product_id of 5 has the name of "iPhone", you can enter <span style="font-family: monospace">[product_name]</span> to have "iPhone" automatically inserted in that field.</div>';

$_['entry_required_error']		= '"Required" Error Message:<span class="help">Enter the message displayed when the customer does not fill in all the required fields.</span>';
$_['text_required_error']		= 'Please fill in all required fields';
$_['entry_captcha_error']		= '"Captcha" Error Message:<span class="help">Enter the message displayed when the customer does not enter the correct captcha code.</span>';
$_['text_captcha_error']		= 'Verification code does not match the image';
$_['entry_invalid_email_error']	= '"Invalid E-mail" Error Message:<span class="help">Enter the message displayed when the customer does not use a valid e-mail address in an e-mail field.</span>';
$_['text_invalid_email_error']	= 'Please use a valid e-mail address format';
$_['entry_mismatch_error']		= '"E-mail Mismatch" Error Message:<span class="help">Enter the message displayed when an e-mail field and its confirmation field do not match.</span>';
$_['text_mismatch_error']		= 'E-mail address does not match confirmation';
$_['entry_minlength_error']		= '"Minimum Length" Error Message:<span class="help">Enter the message displayed when a response for a text field does not meet its minimum required length. Use [min] in place of the Min Length value.</span>';
$_['text_minlength_error']		= 'Please enter at least [min] characters';
$_['entry_file_name_error']		= '"File Name Length" Error Message:<span class="help">Enter the message displayed when the file name is less than 3 or greater than 128 characters.</span>';
$_['text_file_name_error']		= 'File name must be between 3 and 128 characters';
$_['entry_file_size_error']		= '"File Size" Error Message:<span class="help">Enter the message displayed when the file size is greater than a file upload field maximum file size.</span>';
$_['text_file_size_error']		= 'File size is too large';
$_['entry_file_ext_error']		= '"File Extension" Error Message:<span class="help">Enter the message displayed when the file extension does not match the allowed file extensions.</span>';
$_['text_file_ext_error']		= 'File extension is not allowed';
$_['entry_file_upload_error']	= '"File Upload" Error Message:<span class="help">Enter the message displayed for general file upload errors.</span>';
$_['text_file_upload_error']	= 'File upload error';

$_['help_email_shortcodes']		= '
	<table class="help" style="width: 500px">
		<tr><td colspan="4" style="line-height: 1.2">
			For "Subject" and "Message" fields, use a field key surrounded by square brackets [ and ] to insert the customer\'s response to that field,
			or use [form_responses] to insert a list of all form responses. You can also use the following shortcodes to insert other pieces of information:
		</td></tr>
		<tr><td>
			[store_name]<br />
			[store_url]<br />
			[store_owner]<br />
			[store_address]<br />
		</td><td>
			[store_email]<br />
			[store_telephone]<br />
			[store_fax]<br />
		</td><td>
			[current_date]<br />
			[current_time]<br />
			[form_name]<br />
			[form_responses]<br />
		</td></tr>
	</table>
';
$_['entry_admin_email']			= 'Admin E-mail Address(es):<span class="help">Enter the e-mail address(es) where form responses are sent, separated by , (commas).</span>';
$_['entry_admin_subject']		= 'Admin E-mail Subject:';
$_['text_admin_subject']		= '[store_name]: [form_name] response';
$_['entry_admin_message']		= 'Admin E-mail Message:';
$_['text_admin_message']		= '<p>You have received a response to your [form_name] form, with the following responses:</p>' . "\n\n" . '<p>[form_responses]</p>';
$_['entry_email_customer']		= 'E-mail Customer Their Responses:<span class="help">Select whether to e-mail the customer a copy of their responses, if the form includes an "E-mail Address" field type.</span>';
$_['entry_customer_subject']	= 'Customer E-mail Subject:';
$_['text_customer_subject']		= '[store_name]: [form_name] submitted';
$_['entry_customer_message']	= 'Customer E-mail Message:';
$_['text_customer_message']		= '<p>Thank you for your submission! We will respond to your inquiry as soon as possible. A copy of your responses is included below. Thanks again!</p>' . "\n\n" . '<p>[store_name]<br />[store_url]</p>' . "\n\n" . '<p>[form_responses]</p>';

// Copyright
$_['copyright']					= '<div style="text-align: center; margin: 15px" class="help">' . $_['heading_title'] . ' ' . $version . ' &copy; <a target="_blank" href="http://www.getclearthinking.com">Clear Thinking, LLC</a></div>';

// Standard Text
$_['standard_module']			= 'Modules';
$_['standard_shipping']			= 'Shipping';
$_['standard_payment']			= 'Payments';
$_['standard_total']			= 'Order Totals';
$_['standard_feed']				= 'Product Feeds';
$_['standard_success']			= 'Success: You have created a new form!';
$_['standard_error']			= 'Error: You do not have permission to perform that operation.';
$_['standard_left']				= 'Left';
$_['standard_right']			= 'Right';
$_['standard_content_top']		= 'Content Top';
$_['standard_content_bottom']	= 'Content Bottom';
$_['standard_column_left']		= 'Column Left';
$_['standard_column_right']		= 'Column Right';
?>