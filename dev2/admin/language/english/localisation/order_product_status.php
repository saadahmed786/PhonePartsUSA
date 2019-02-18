<?php
// Heading
$_['heading_title']    = 'Order Product Status';

// Text
$_['text_success']     = 'Success: You have modified order product statuses!';

// Column
$_['column_name']      = 'Order Status Name';
$_['column_action']    = 'Action';

// Entry
$_['entry_name']                  = 'Order Status Name:';
$_['entry_order_status_internal'] = 'Internal Order Status:<span class="help">The internal order status that should be applied to an order when it reaches \'Complete\' Status.</span>';
$_['entry_order_status']          = 'Public Order Status:<span class="help">The public order status that should be applied to an order when it reaches \'Complete\' Status.</span>';
$_['entry_days_delay']            = 'Days Delayed:<span class="help">The number of days delayed an order should be delayed it\'s for shipment date.</span>';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify order product statuses!';
$_['error_name']       = 'Order Status Name must be between 3 and 32 characters!';
$_['error_default']    = 'Warning: This order status cannot be deleted as it is currently assigned as the default store order status!';
$_['error_download']   = 'Warning: This order status cannot be deleted as it is currently assigned as the default download status!';
$_['error_store']      = 'Warning: This order status cannot be deleted as it is currently assigned to %s stores!';
$_['error_order']      = 'Warning: This order status cannot be deleted as it is currently assigned to %s orders!';
?>