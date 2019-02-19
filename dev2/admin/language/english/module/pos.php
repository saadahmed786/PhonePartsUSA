<?php
/*
 * This file contains the english version of any static text required by your module in the admin area.
 * If you want to translate your module to another language, the idea is that you can just replace the
 * right hand column below with the changed language, rather than modifying every file in your module.
 * 
 * We will call these language strings through in the controller to make them available in the view. 
 * 
 * 
 */
// Heading Goes here:
$_['heading_title']    = 'Point Of Sale';


// Text
$_['text_module']         = 'Modules';
$_['text_success']        = 'Success: You have modified module POS!';
$_['text_order_sucess']   = 'Order was modified successfully.';
$_['text_customer_success'] = 'Customer was modified successfully.';
$_['text_order_payment_type']      = 'Order Payment Type';
$_['text_action'] = 'Action';
$_['text_type_already_exist'] = 'Order type already exists.';
$_['text_vqmod_not_installed'] = 'The VQMOD is required for module POS but it is not detected, plesae make sure VQMOD is installed and try again.';

$_['text_payment_type_setting'] = 'Order Payment Type Settings';
// add for Openbay Integration begin
$_['text_openbay_setting'] = 'OpenBay Integration Settings';
$_['text_openbay_enable'] = 'Enable OpenBay Integration';
// add for Openbay Integration end
$_['text_display_setting'] = 'POS Page Display Settings';
$_['text_display_once_login'] = 'Display Point Of Sale main page once logged in.';
$_['column_exclude'] = 'Excluded groups: ';
$_['text_select_all'] = 'Select All';
$_['text_unselect_all'] = 'Unselect All';
$_['text_not_available'] = 'Extension Not Available';
$_['text_autocomplete'] = 'Auto complete';

$_['text_terminal'] = 'Terminal';
$_['text_register_mode'] = 'Register Mode';
$_['text_date_added'] = 'Added';
$_['text_date_modified'] = 'Modified';
$_['text_customer'] = 'Customer';
$_['text_product_quantity'] = 'Qty';
$_['text_items_in_cart']  = 'Items in cart';
$_['text_amount_due']  = 'Amount Due';
$_['text_change']  = 'Change';
$_['text_payment_zero_amount']  = 'The payment amount can not be zero.';
$_['text_quantity_zero']  = 'The quantity can not be zero or negative.';
$_['text_comments'] = 'Comments';
$_['text_select'] = 'Select';
$_['text_add_product_prompt'] = 'Click here to add product';
$_['text_no_store'] = 'No store found, please config a store before use the POS system.';
$_['text_no_product'] = 'The product to be added is not found.';
$_['text_del_payment_confirm'] = 'The payment deletion cannot be undone. Are you sure you want to delete the selected payment?';

$_['text_product_name'] = 'Name';
$_['text_product_upc'] = 'UPC';
$_['text_order_ready'] = 'Order is ready for processing.';
$_['text_load_order'] = 'loading ...';
$_['text_filter_order_list'] = 'filtering ...';
$_['text_load_order_list'] = 'loading order list ...';
$_['text_no_order_selected'] = 'No order is selected. Select an order to delete.';
$_['text_confirm_delete_order'] = 'Deletion of orders cannot be undo. Are you sure you want to delete the selected orders?';
$_['text_customer_no_address'] = 'The customer does not have a default address.';

$_['text_week_0'] = 'Sun';
$_['text_week_1'] = 'Mon';
$_['text_week_2'] = 'Tue';
$_['text_week_3'] = 'Wed';
$_['text_week_4'] = 'Thu';
$_['text_week_5'] = 'Fri';
$_['text_week_6'] = 'Sat';

$_['text_month_1'] = 'Jan';
$_['text_month_2'] = 'Feb';
$_['text_month_3'] = 'Mar';
$_['text_month_4'] = 'Apr';
$_['text_month_5'] = 'May';
$_['text_month_6'] = 'Jun';
$_['text_month_7'] = 'Jul';
$_['text_month_8'] = 'Aug';
$_['text_month_9'] = 'Sep';
$_['text_month_10'] = 'Oct';
$_['text_month_11'] = 'Nov';
$_['text_month_12'] = 'Dec';

$_['tab_product_search'] = 'Search';
$_['tab_product_browse'] = 'Browse';
$_['tab_product_details'] = 'Details';
$_['tab_order_shipping'] = 'Shipping';
$_['tab_order_payments'] = 'Payments';
$_['tab_order_customer'] = 'Customer';

$_['column_payment_type']  = 'Type';
$_['column_payment_amount']  = 'Amount';
$_['column_payment_note']  = 'Comment';
$_['column_payment_action']  = 'Action';
$_['column_attr_name']  = 'Name';
$_['column_attr_value']  = 'Value';
$_['entry_thumb'] = 'Thumb Image';

// Button
$_['button_save']  = 'Save';
$_['button_cancel']  = 'Cancel';
$_['button_add_type']  = 'Add';
$_['button_remove']  = 'Remove';

$_['button_add_payment']  = 'Add';

$_['button_existing_order'] = 'Existing Orders'; 
$_['button_new_order'] = 'New Order'; 
$_['button_complete_order'] = 'Complete Order';
$_['button_print_invoice'] = 'Print Invoice';
$_['button_full_screen'] = 'Hide Header';
$_['button_normal_screen'] = 'Show Header';
$_['button_discount'] = 'Discount Manager';
$_['button_cut'] = 'Split Order';
$_['button_delete'] = 'Delete';
$_['button_add_product'] = 'Add Product';
$_['entry_product'] = 'Product Name:';

// add for SKU begin
$_['entry_sku'] = 'Stock Keeping Unit (SKU):';
$_['text_no_product_for_sku'] = 'No product can be found for SKU # ';
// add for SKU end
// add for UPC begin
// add for (update) UPC/EAN support begin
// $_['entry_upc'] = 'Universal Product Code (UPC):';
// $_['text_no_product_for_upc'] = 'No product can be found for UPC # ';
$_['entry_upc'] = 'Universal Product Code (UPC) /<br/>European Article Number (EAN):';
$_['text_no_product_for_upc'] = 'No product can be found for UPC/EAN # ';
// add for (update) UPC/EAN support end
// add for UPC end
// add for MPN begin
$_['entry_mpn'] = 'Manufacturer Part Number (MPN):';
$_['text_no_product_for_mpn'] = 'No product can be found for MPN # ';
// add for MPN end

// add for Print begin
$_['print_title'] = 'Receipt';
$_['column_desc'] = 'Description';
$_['column_qty'] = 'Qty';
$_['column_type'] = 'Type';
$_['column_amount'] = 'Amount';
$_['column_note'] = 'Note';
$_['user_info'] = 'Your cashier was %s';
$_['entry_term_n_cond'] = 'Term &amp; Condition: ';
$_['term_n_cond_default'] = '***NO REFUND OR EXCHANGES FOR OPEN PRODUCTS***';
$_['print_wait_title'] = 'Printing';
$_['print_wait_message'] = 'Printing, please wait ...';
$_['print_sign_message'] = 'Printing signature page, please wait ...';
$_['print_receipt_message'] = 'Printing receipt, please wait ...';
$_['text_print_setting'] = 'Receipt Print Settings';
$_['entry_print_log'] = 'Logo on receipt: ';
$_['entry_print_width'] = 'Receipt printer paper width: ';
$_['text_print_browse'] = 'Browse ...';
$_['text_print_image_manager'] = 'Receipt Print Image Manager';
$_['text_p_complete'] = 'Print receipt once the order is complete';
$_['text_p_payment'] = 'Print receipt once the full payment is made';
// add for Print end
// add for Invoice Print begin
$_['print_invoice_message'] = 'Printing invoice, please wait ...';
// add for Invoice Print end
// add for Discount begin
$_['tab_order_discount'] = 'Discount';
$_['text_discount_title'] = 'Discount Manager';
$_['text_discount_message'] = 'Specify the discount for the order';
$_['text_discount_type_amount'] = "Fixed Amount";
$_['text_discount_type_percentage'] = "Percentage";
$_['text_discount_subtotal'] = 'of sub total';
$_['text_discount_total'] = 'of total';
$_['text_discount'] = "Discount";
$_['text_discounted'] = "Discounted";
$_['text_discounted_title'] = "Discounted Amount";
$_['button_discount'] = "Apply Discount";
$_['error_discount_order_not_exist'] = 'Discount cannot applied as the order (id: %s) does not exist.';
$_['error_discount_not_installed'] = 'Discount total is not installed or enabled.';
$_['text_apply_discount'] = 'applying discount to order ...';
// add for Maximum Discount begin
$_['text_max_discount_setting'] = 'Discount Limit Settings';
$_['column_group'] = 'Group';
$_['column_discount_limit'] = 'Discount Limit';
$_['entry_max_discount_fixed'] = 'Fixed Discount Limit:';
$_['entry_max_discount_percentage'] = 'Discount Percentage Limit (%):';
// add for Maximum Discount begin
// add for Discount end
// add for Inplace Pricing begin
$_['text_inplace_pricing_setting'] = 'Inplace Pricing Settings';
$_['text_inplace_pricing_enable'] = 'Enable inplace pricing';
// add for Inplace Pricing end
// add for hiding Delete begin
$_['text_hide_delete_setting'] = 'Hiding Delete Order Settings';
$_['text_hide_delete_enable'] = 'Hide delete order button';
// add for hiding Delete end
// add for Hiding Order Status begin
$_['text_hide_order_status_setting'] = 'Hiding Order by Status Settings';
$_['text_hide_order_status_message'] = 'Choose the status of which orders should be hidden';
// add for Hiding Order Status end
// add for User as Affiliate begin
$_['text_user_affi_setting'] = 'User and Affiliate Mapping';
$_['column_ua_user'] = 'User';
$_['column_ua_affiliate'] = 'Affiliate';
$_['column_ua_action'] = 'Action';
// add for User as Affiliate end
// add for Default Customer begin
$_['text_customer_setting'] = 'Default Customer Settings';
$_['text_customer_system'] = 'System built-in customer';
$_['text_customer_custom'] = 'User defined customer';
$_['text_customer_existing'] = 'Existing customer';
$_['text_customer_info'] = 'General Information';
$_['text_address_info'] = 'Address Details';
// add for Default Customer end
// add for Manufacturer Product begin
$_['entry_manufacturer'] = 'Choose Manufacturer:';
// add for Manufacturer Product end
// add for Blank Page begin
$_['text_order_blank'] = 'Create a new order or select an existing order to start';
// add for Blank Page end
// add for Purchase Order Payment begin
$_['purchase_order'] = 'Purchase Order';
$_['text_purchase_order_number'] = 'PO Number';
// add for Purchase Order Payment end
// add for edit order address begin
$_['text_order_shipping_address'] = 'Shipping Address';
$_['text_order_payment_address'] = 'Payment Address';
$_['button_edit_address'] = 'Order Shipping & Payment Address';
$_['entry_order_address'] = 'Choose Address:';
$_['entry_shipping_method'] = 'Shipping Method:';
// add for edit order address end
// add for Quotation begin
$_['text_order_quote'] = 'Choose Order or Quote';
$_['text_new_order'] = 'New Order';
$_['text_new_quote'] = 'New Quote';
$_['text_existing_quotes'] = 'Existing Quotes';
$_['text_convert_to_order'] = 'Convert to Order';
$_['column_quote_id'] = 'Quote ID';
$_['text_list_order'] = 'Order List';
$_['text_list_quote'] = 'Quote List';
$_['text_quote_status_setting'] = 'Quote Status Settings';
$_['column_quote_status_name'] = 'Quote Status Name';
$_['column_quote_status_action'] = 'Action';
$_['button_rename'] = 'Rename';
$_['text_rename'] = 'Rename the quote status:';
$_['text_quote_status_already_exist'] = 'The given status already exists!';
$_['text_quote_status_in_use'] = 'The quote status is still in use. Check the quotes with following ids: %s';
$_['text_quote_ready'] = 'Quote is ready for processing.';
$_['text_quote_sucess']   = 'Quote was modified successfully.';
$_['text_quote_converted']   = 'The quote was converted to an order successfully.';
$_['text_confirm_complete']   = 'Are you sure you want to complete this order?';
$_['text_confirm_convert']   = 'Are you sure you want to convert this quote to an order?';
// add for Quotation end
// add for Empty order control begin
$_['text_status_initial']   = 'Initial';
$_['text_status_deleted']   = 'Deleted';
$_['text_empty_order_control_setting']   = 'Empty Order Control Settings';
$_['text_empty_order_control_delete_setting']   = 'Automatically delete the following selected types of orders when POS is launched';
$_['text_delete_order_with_no_products']   = 'Orders with no products added';
$_['text_delete_order_with_inital_status']   = 'Orders in \'Initial\' status';
$_['text_delete_order_with_deleted_status']   = 'Orders in \'Deleted\' status';
$_['text_empty_order_control_action']   = 'Select the types of orders and click Delete to delete orders';
// add for Empty order control end
// add for Quick sale begin
$_['tab_product_quick_sale']   = 'Quick Sale';
$_['text_quick_sale']   = 'Add Non-stock Product(s)';
$_['entry_quick_sale_name']   = 'Name:';
$_['entry_quick_sale_model']   = 'Model:';
$_['entry_quick_sale_price']   = 'Price:';
$_['entry_quick_sale_tax']   = 'Tax Class:';
$_['text_quick_sale_include_tax']   = 'Price entered includes tax';
$_['text_quick_sale_shipping']   = 'Requires shipping';
// add for Quick sale end
// add for Browse begin
$_['text_top_category_name']   = '[Top]';
$_['text_remaining'] = 'Remaining';
// add for Browse end
// add for Cash type begin
$_['text_cash_type_setting']   = 'Cash Type Settings';
$_['column_cash_type']   = 'Type';
$_['column_cash_image']   = 'Image';
$_['column_cash_value']   = 'Value';
$_['column_cash_action']   = 'Action';
$_['text_cash_type_note']   = 'Note';
$_['text_cash_type_coin']   = 'Coin';
// add for Cash type end
// add for UPC/SKU/MPN begin
$_['text_scan_type_setting']   = 'Barcode Scan Type Settings';
$_['text_scan_type_upc']   = 'UPC';
$_['text_scan_type_sku']   = 'SKU';
$_['text_scan_type_mpn']   = 'MPN';
// add for UPC/SKU/MPN end

$_['tab_settings_payment_type'] = 'Payment';
$_['tab_settings_options'] = 'Options';
$_['tab_settings_order'] = 'Order';
$_['tab_settings_authorizenet'] = 'Authorize.Net';
$_['tab_settings_receipt'] = 'Receipt';
$_['tab_settings_customer'] = 'Customer';
$_['tab_settings_discount'] = 'Discount';
$_['tab_settings_affiliate'] = 'Affiliate';
$_['tab_settings_quote'] = 'Quote';
$_['tab_settings_location'] = 'Location';
$_['tab_settings_table_management'] = 'Table Management';
$_['tab_settings_product_sn'] = 'Product Serial No';
$_['tab_settings_commission'] = 'Product Commission';
// add for Send notification begin
$_['text_new_subject']          = '%s - Order %s';
$_['text_new_greeting']         = 'Your order has been processed.';
$_['text_new_link']             = 'To view your order click on the link below:';
$_['text_new_order_detail']     = 'Order Details';
$_['text_new_instruction']      = 'Instructions';
$_['text_new_order_id']         = 'Order ID:';
$_['text_new_date_added']       = 'Date Added:';
$_['text_new_order_status']     = 'Order Status:';
$_['text_new_payment_method']   = 'Payment Method:';
$_['text_new_shipping_method']  = 'Shipping Method:';
$_['text_new_email']  			= 'Email:';
$_['text_new_telephone']  		= 'Telephone:';
$_['text_new_ip']  				= 'IP Address:';
$_['text_new_payment_address']  = 'Payment Address';
$_['text_new_shipping_address'] = 'Shipping Address';
$_['text_new_products']         = 'Products';
$_['text_new_product']          = 'Product';
$_['text_new_model']            = 'Model';
$_['text_new_quantity']         = 'Quantity';
$_['text_new_price']            = 'Price';
$_['text_new_order_total']      = 'Order Totals';	
$_['text_new_total']            = 'Total';	
$_['text_new_download']         = 'You can click on the link below to access your downloadable products:';
$_['text_new_comment']          = 'The comments for your order are:';
$_['text_new_footer']           = 'Please reply to this email if you have any questions.';
$_['text_new_powered']          = 'Powered By <a href="http://www.pos4opencart.com">Pos4Opencart</a>.';
// add for Send notification end
// add for location based stock begin
$_['text_location_setting'] = 'Location Settings';
$_['column_location_code'] = 'Code';
$_['column_location_name'] = 'Name';
$_['column_location_desc'] = 'Description';
$_['column_location_action'] = 'Action';
$_['text_location_stock_enable'] = 'Enable location based stock control';
$_['text_location_already_exist'] = 'The given location code already exists!';
// add for location based stock end
// add for table management begin
$_['text_table_management_setting'] = 'Table Management';
$_['text_table_management_enable'] = 'Enable table management';
$_['entry_table_layout'] = 'Upload your table layout image:';
$_['text_table_layout'] = 'Upload';
$_['entry_table_name'] = 'ID:';
$_['entry_table_desc'] = 'Description:';
$_['button_set_table'] = 'Set';
$_['button_delete_table'] = 'Delete';
$_['text_table_name_empty'] = 'The table ID cannot be empty!';
$_['text_table_name_exists'] = 'The table ID already exists. Please change the ID and try it again.';
$_['entry_table_number'] = 'Number of tables:';
$_['button_table_set_number'] = 'Set Table Number';
$_['column_table_id'] = 'Table ID';
$_['column_table_desc'] = 'Table Description';
$_['column_table_action'] = 'Action';
$_['button_table_modify'] = 'Modify';
$_['button_table_remove'] = 'Delete';
// add for table management end
// add for Complete Status begin
$_['text_complete_status_setting'] = 'Complete Status Settings';
$_['entry_complete_status'] = 'Complete Status:';
// add for Complete Status end
// add for Rounding begin
$_['text_rounding_setting'] = 'Rounding Settings';
$_['text_rounding_enable'] = 'Enable Rounding';
$_['text_rounding_5c'] = 'Rounding to 5 cents (0-2=>0;3-7=>5;8-9=>10)';
$_['text_rounding_10c'] = 'Rounding to 10 cents (0-4=>0;5-9=>10)';
$_['text_rounding_50c'] = 'Rounding to 50 cents (0-49=>0;50-99=>50)';
// add for Rounding end
// add for till control begin
$_['text_till_control_setting'] = 'Till Control Settings';
$_['text_till_control_enable'] = 'Enable Till Control';
$_['entry_till_control_key'] = 'Till control key:';
$_['button_test'] = 'Test';
$_['text_till_full_payment_enable'] = 'Open till when pay cash';
// add for till control end
// add for serial no begin
$_['text_add_serial_no_setting'] = 'Add Product Serial No';
$_['entry_sn'] = 'Serial No:';
$_['button_sn_save'] = 'Save Serial No';
$_['text_list_serial_no_setting'] = 'Search Serial No';
$_['column_sn_product_name'] = 'Product Name';
$_['column_sn_product_sn'] = 'Serial No';
$_['column_sn_product_status'] = 'Status';
$_['column_action'] = 'Action';
$_['text_sn_sold'] = 'Sold';
$_['text_sn_in_store'] = 'In Store';
$_['button_search'] = 'Search';
$_['text_duplicated_sn'] = "The following SNs always exist:\n%s";
$_['text_add_sn_success'] = "%s product SNs have been added successfully!";
$_['text_sold_info'] = '<br/>Order ID: %s';
$_['entry_product_sn'] = 'Product Serial No:';
// add for serial no end
// add for Status Change Notification begin
$_['text_notification_setting'] = 'Status Change Notification Settings';
$_['text_notification_enable'] = 'Send email notification when order status changes';
// add for Status Change Notification end
// add for commission begin
$_['text_commission_setting'] = 'Product Commission Settings';
$_['text_commission_enable'] = 'Enable product commission';
$_['text_set_commission'] = 'Commission Setting';
$_['entry_commission_fixed'] = 'Fixed amount:';
$_['entry_commission_percentage'] = 'Percentage:';
$_['text_commission_percentage_base'] = '% above';
$_['button_commission_save'] = 'Save Commission';
$_['text_list_commission_setting'] = 'Search Product Commission';
$_['column_commission_product_name'] = 'Product Name';
$_['column_commission_commission'] = 'Commission';
$_['column_commission_action'] = 'Action';
$_['button_commission_search'] = 'Search';
// add for commission end
// add for model begin
$_['entry_model'] = 'Product Model:';
// add for model end
?>