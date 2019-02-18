<?php
// Heading  
$_['heading_title']     = 'Credit Reasoning';

// Text
$_['text_send']         = 'Send';
$_['text_success']      = 'Success: You have modified credit reason!';
$_['text_sent']         = 'Success: Credit Reason e-mail has been sent!';
$_['text_wait']         = 'Please Wait!';

// Column
$_['column_name']       = 'Credit Name';
$_['column_code']       = 'Code';
$_['column_message']    = 'Canned Message';
$_['column_to']         = 'To';
$_['column_theme']      = 'Theme';
$_['column_amount']     = 'Amount';
$_['column_status']     = 'Status';
$_['column_order_id']   = 'Order ID';
$_['column_customer']   = 'Customer';
$_['column_date_added'] = 'Date Added';
$_['column_action']     = 'Action';

// Entry
$_['entry_code']        = 'Code:<br /><span class="help">The code of Credit Reasoning.</span>';
$_['entry_name']   = 'Reason Name:';
$_['entry_from_email']  = 'From E-Mail:';
$_['entry_to_name']     = 'To Name:';
$_['entry_to_email']    = 'To E-Mail:';
$_['entry_theme']       = 'Theme:';
$_['entry_message']     = 'Message:';
$_['entry_amount']      = 'Amount:';
$_['entry_status']      = 'Status:';

// Error
$_['error_permission']  = 'Warning: You do not have permission to modify credit reason!';
$_['error_exists']      = 'Warning: Reason code is already in use!';
$_['error_code']        = 'Code must be between 1 and 3 characters!';
$_['error_to_name']     = 'Recipient\'s Name must be between 1 and 64 characters!';
$_['error_name']   		= 'Your Name must be between 3 and 50 characters!';
$_['error_email']       = 'E-Mail Address does not appear to be valid!';
$_['error_amount']      = 'Amount must be greater than or equal to 1!';
$_['error_order']       = 'Warning: This voucher cannot be deleted as it is part of an <a href="%s">order</a>!';


$_['entry_template_shortcode']         = '<b>SHORT CODE</b><table class="list"><tr><td>{firstname}</td><td>the name of the client</td></tr><tr><td>{lastname}</td><td>the last name of the client</td></tr><tr><td>{delivery_address}</td><td>delivery address</td></tr><tr><td>{shipping_address}</td><td>alias to delivery address</td></tr><tr><td>{payment_address}</td><td>payment address</td></tr><tr><td>{order_date}</td><td>date of order</td></tr><tr><td><b>{product:start}</b><br />{product_image}<br />{product_name}<br />{product_model}<br />{product_quantity}<br />{product_price}<br />{product_price_gross}<br />{product_sku}<br />{product_upc}<br />{product_tax}<br />{product_attribute}<br />{product_option}<br />{product_total}<br />{product_total_gross}<br /><b>{product:stop}</b></td><td>purchased products</td></tr><tr><td><b>{total:start}</b><br />{total_title}<br />{total_value}<br /><b>{total:stop}</b></td><td>order totals</td></tr><tr><td><b>{voucher:start}</b><br />{voucher_description}<br />{voucher_amount}<br /><b>{voucher:stop}</b></td><td>vouchers</td></tr><tr><td>{special}</td><td>in promotion</td></tr><tr><td>{date}</td><td>date of sending notice</td></tr><tr><td>{payment}</td><td>method of payment</td></tr><tr><td>{shipment}</td><td>the method of shipment</td></tr><tr><td>{order_id}</td><td>order number</td></tr><tr><td>{invoice_number}</td><td>the invoice number</td></tr><tr><td>{total}</td><td>the total for the order</td></tr><tr><td>{order_href}</td><td>link to order</td></tr><tr><td>{comment}</td><td>additional comments added in the history</td></tr><tr><td>{promo}</td><td>promo text</td></tr><tr><td>{telephone}</td><td>telephone to the customer</td></tr><tr><td>{sub_total}</td><td>the sub-total for the order</td></tr><tr><td>{shipping_cost}</td><td>shipping cost</td></tr><tr><td>{email}</td><td>client email</td></tr><tr><td>{client_comment}</td><td>comment which a customer can fill in at checkout</td></tr><tr><td><b>{tax:start}</b><br />{tax_title}<br />{tax_value}<br /><b>{tax:stop}</b></td><td>taxes</td></tr><tr><td>{tax_amount}</td><td>amount of all taxes</td></tr><tr><td>{carrier}</td><td>carrier name<br />(if you use extension Package Tracking Service)</td></tr><tr><td>{tracking_number}</td><td>tracking number<br />(if you use extension Package Tracking Service)</td></tr><tr><td>{carrier_href}</td><td>href to traking number<br />(if you use extension Package Tracking Service)</td></tr></table>';
$_['entry_template_default_shortcode'] = '<b>SHORT CODE</b><table class="list"><tr><td>{firstname}</td><td>the name of the client</td></tr><tr><td>{lastname}</td><td>the last name of the client</td></tr><tr><td>{payment_address}</td><td>payment address</td></tr><tr><td>{shipping_address}</td><td>shipping address</td></tr><tr><td>{order_date}</td><td>date of order</td></tr><tr><td><b>{product:start}</b><br />{product_image}<br />{product_name}<br />{product_model}<br />{product_quantity}<br />{product_price}<br />{product_price_gross}<br />{product_sku}<br />{product_upc}<br />{product_tax}<br />{product_attribute}<br />{product_option}<br />{product_total}<br />{product_total_gross}<br /><b>{product:stop}</b></td><td>purchased products</td></tr><tr><td><b>{voucher:start}</b><br />{voucher_description}<br />{voucher_amount}<br /><b>{voucher:stop}</b></td><td>vouchers</td></tr><tr><td><b>{total:start}</b><br />{total_title}<br />{total_value}<br /><b>{total:stop}</b></td><td>order totals</td></tr><tr><td>{special}</td><td>in promotion</td></tr><tr><td>{date}</td><td>date of sending notice</td></tr><tr><td>{payment}</td><td>method of payment</td></tr><tr><td>{shipment}</td><td>the method of shipment</td></tr><tr><td>{download}</td><td>link to downloads</td></tr><tr><td>{order_id}</td><td>order number</td></tr><tr><td>{order_href}</td><td>link to order</td></tr><tr><td>{comment}</td><td>additional comments added in the history</td></tr><tr><td>{status_name}</td><td>status name</td></tr><tr><td>{store_name}</td><td>store name</td></tr><tr><td>{ip}</td><td>client IP</td></tr><tr><td>{email}</td><td>client email</td></tr><tr><td>{telephone}</td><td>telephone to the customer</td></tr><tr><td>{store_url}</td><td>store url</td></tr><tr><td>{logo}</td><td>store logo</td></tr><tr><td>{promo}</td><td>promo text</td></tr><tr><td>{total}</td><td>the total for the order</td></tr><tr><td>{sub_total}</td><td>the sub-total for the order</td></tr><tr><td>{shipping_cost}</td><td>shipping cost</td></tr><tr><td>{client_comment}</td><td>comment which a customer can fill in at checkout</td></tr><tr><td><b>{tax:start}</b><br />{tax_title}<br />{tax_value}<br /><b>{tax:stop}</b></td><td>taxes</td></tr><tr><td>{tax_amount}</td><td>amount of all taxes</td></tr></table>';


?>
