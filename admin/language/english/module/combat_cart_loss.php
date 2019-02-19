<?php
    // Heading
    $_['heading_title']='Combat Cart-Loss';
    $_['text_module'] = 'Modules';

    //Table Titles
    $_['title_unconfirmed_orders']='Unconfirmed Orders';
    $_['title_confirmed_orders']='Confirmed Orders';
    $_['title_templates_list']='Templates';
    $_['title_settings']='Settings';
     $_['title_autoemail_settings']='Automated Customer Reminder';

    $_['title_order_id']='Order id';
    $_['title_order_customer']='Customer';
    $_['title_order_total']='Total (incl. shipping)';
    $_['title_order_added']='Added';
    $_['title_order_modified']='Modified';
    $_['title_order_contacted']='Email Sent';

    $_['title_yes']='Yes';
    $_['title_no']='No';
    $_['title_detail']='Details';
    $_['title_order_emails']='Emails';

    $_['title_date']='Date';
    $_['title_email_subject']='Subject';
    $_['title_email_from']='From Email';
    $_['title_email_message']='Message';

    $_['title_button_delete']='Delete';

    $_['message_no_orders']='No unconfirmed orders';
    $_['message_no_order']='Cannot find this order';


    $_['message_orders_deleted']='Orders deleted';
    $_['error_orders_not_deleted']='Orders not deleted';

    $_['title_send_mass_message']='Send mass message';

    $_['title_back_to_module']='< Back';

    //Unconfirmed order
    $_['order_details_title']='Order details';
    $_['message_no_products']='No products';
    $_['message_no_emails']='No emails';

    $_['order_title']='Order #';

    $_['title_product_name']='Product name';
    $_['title_product_model']='Product model';
    $_['title_product_quantity']='Quantity';
    $_['title_product_total']='Total';

    $_['title_customer_name']='Customer name';
    $_['title_customer_email']='Customer email';
    $_['title_customer_telephone']='Customer telephone';


    $_['title_message_to_customer']='Message to customer';
    $_['title_message_subject']='Subject';
    $_['title_message_from']='From';
    $_['title_button_send_message']='Send message';
    $_['error_message_not_sent']='Message not sent';
    $_['message_sent']='Message sent';

    $_['title_message_template']='Message template';
    $_['message_no_templates']='No templates';
    $_['message_default_template']='None';

    $_['question_delete_orders']='Are you sure you want delete these orders?';
    $_['question_delete_templates']='Are you sure you want delete these templates?';

    $_['error_no_orders_selected']='No orders selected. Please select at least one order';
    $_['error_no_carts_selected']='No carts selected.';

    //Templates
    $_['title_template_subject']='Template subject';
    $_['title_template_from']='From Email Address';
    $_['title_template_message']='Template message<br/><span class="help">You can use the following template variables:<br /> [customername], [customerfirstname], [customerlastname],
[cost],  [deliveryaddress], [products], [order] & [store]. Please see the documentation for further usage instructions<br/><br/>PLEASE DO NOT COPY & PASTE THESE VARIABLES. TYPE THEM IN MANUALLY TO AVOID ERRORS.<br/><br/></span>';
    $_['title_edit_template']='Edit';
    $_['error_templates_not_deleted']='Templates not deleted';
    $_['message_templates_deleted']='Templates deleted';
    $_['title_new_template']='New template';

    $_['message_no_template']='Template not found';
    $_['title_template_edit']='Edit template';

    $_['title_save_template']='Save template';

    $_['message_template_updated']='Template updated';
    $_['error_template_not_updated']='Templated not updated';

    $_['message_template_added']='Templated added';

    $_['error_check_template_fields']='Check template fields, all are required';

    $_['entry_admin_email'] = 'Send email to store owner every time a new unconfirmed order is logged:<br /><span class="help">This will enable instant notification to store owner about any order added to unconfirmed list.</span>';
    $_['entry_ccl_email_subject'] = 'Email Subject:<br /><span class="help">You can use the template variables [order] & [store] in your subject.</span>';
    $_['entry_ccl_email_message'] = 'Email Message:<br /><span class="help">You can use the following template variables:<br /> [customername]<br />[customerfirstname]<br />[customerlastname]<br />[cost]<br />[deliveryaddress]<br />[products]<br />[order]<br />[store]<br/>See the documentation for further usage instructions<br/><br/>PLEASE DO NOT COPY & PASTE THESE VARIABLES. TYPE THEM IN MANUALLY TO AVOID ERRORS.</span>';
    
    $_['entry_auto_email'] = 'Send Automated Email to Customer:<br /><span class="help">This will enable automated reminders to any customers that are added to the unconfirmed orders list.</span>';
    $_['entry_ccl_autoemail_subject'] = 'Email subject:<br /><span class="help">You can use the template variables [order] and [store] in your subject.</span>';
    $_['entry_ccl_autoemail_message'] = 'Email message:<br /><span class="help">You can use the following template variables:<br />[customername]<br />[customerfirstname]<br />[customerlastname]<br />[deliveryaddress]<br />[cost]<br />[products]<br />[order]<br />[store]<br/>See the documentation for further usage instructions<br/><br/>PLEASE DO NOT COPY & PASTE THESE VARIABLES. TYPE THEM IN MANUALLY TO AVOID ERRORS.</span>';
    
    $_['entry_auto_coupon_value'] = 'Coupon Discount';
    $_['entry_auto_coupon_total'] = 'Minimum Cart Value Coupon is Valid For';
    $_['entry_auto_coupon_duration'] = 'Days for which Coupon is Valid';
    
    //Mass messages
    $_['title_mass_message_window']='Please enter your message';
    $_['error_no_recipients']='Please select at least one recipient';
    $_['error_check_subject_message']='Please check subject and message to customer';
    $_['mass_messages_sent']='Messages successfully sent to customers';
    $_['mass_message_not_sent']='Messages not sent to customers';
?>