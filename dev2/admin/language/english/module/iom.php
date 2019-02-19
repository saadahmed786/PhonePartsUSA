<?php
// Heading
$_['heading_title']       = 'Improved Order Management';
$_['heading_inventory']   = 'Inventory Mananagement';

// Text
$_['text_module']         = 'Modules';
$_['text_success']        = 'Success: You have modified Improved Order Management Settings!';

// Entry
$_['entry_iom_inventory_auto_return']     = 'Auto Return Inventory:<span class="help">When an order is modified or updated, the following statuses should be considered cancelled and return any reserved inventory for sale.</span>';
$_['entry_iom_inventory_auto_reserve']    = 'Auto Reserve Inventory:<span class="help">When an order is placed the following order statuses should be considered a valid order and inventory should be reserved.</span>';
$_['entry_iom_inventory_ops_pending']     = 'Order Item Pending Status:<span class="help">The status given to an order line item which inventory reserve is pending.</span>';
$_['entry_iom_inventory_ops_backordered'] = 'Order Item Backorder Status:<span class="help">The status given to an order line item which inventory is unavailable/out of stock.</span>';
$_['entry_iom_inventory_ops_partialship'] = 'Order Item Partial-Ship Status:<span class="help">The status given to an order line item which has been partially shipped.</span>';
$_['entry_iom_inventory_ops_reserved']    = 'Order Item Reserved Status:<span class="help">The status given to an order line item which inventory has been reserved.</span>';
$_['entry_iom_inventory_ops_ordered']     = 'Order Item Ordered Status:<span class="help">The status given to an order line item which was out of stock, but has now been ordered and is pending arrival.</span>';
$_['entry_iom_inventory_ops_cancelled']   = 'Order Item Cancelled Status:<span class="help">The status given to an order line item which has been cancelled.</span>';
$_['entry_iom_inventory_ops_shipped']     = 'Order Item Shipped Status:<span class="help">The status given to an order line item which has been fully shipped to the customer.</span>';

$_['entry_iom_inventory_os_shipready']          = 'Order Status Ship Ready:<span class="help">The status given to an order when all inventory is locally available & ready for packing/shipment.</span>';
$_['entry_iom_inventory_os_inventoryrequired']  = 'Order Status Inventory Required:<span class="help">The status given to an order that requires inventory where the delay period cannot be determined.</span>';

// Error
$_['error_permission']    = 'Warning: You do not have permission to modify Improved Order Management!';
?>