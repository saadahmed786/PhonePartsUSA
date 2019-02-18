<?php

include 'auth.php';
include 'inc/functions.php';
include 'shipstation/shipstation.php';

$ship = new ShipStation;

$filename = "products-" . date("Y-m-d") . ".csv";
$fp = fopen($filename, "w");
$headers = array('Order Number', 'Order Created Date', 'Order Date Paid', 'Order Total', 'Order Amount Paid', 'Order Tax Paid', 'Order Shipping Paid', 'Order Requested Shipping Service', 'Order Total Weight (oz)', 'Order Source', 'Order Notes from Buyer', 'Buyer Full Name', 'Buyer First Name', 'Buyer Last Name', 'Buyer Email', 'Buyer Phone', 'Recipient Full Name', 'Recipient First Name', 'Recipient Last Name', 'Recipient Phone', 'Recipient Company', 'Address Line 1', 'Address Line 2', 'Address Line 3', 'City', 'State', 'Postal Code', 'Country Code', 'Item SKU', 'Item Name', 'Item Quantity', 'Item Unit Price');

fputcsv($fp, $headers,',');

foreach ($_POST['orderIds'] as $order_id) {
    $order = $ship->getOrder($order_id);
    $order_id = explode('-', $order_id)[0];
    $orderInfo = $db->func_query_first('Select o.prefix, o.ppusa_sync, o.order_id,d.payment_method , o.ss_valid, o.order_date , o.email, o.order_price , o.store_type, o.order_status, o.fishbowl_uploaded,o.customer_name,
        o.match_status,o.bscheck, o.is_address_verified, o.avs_code,o.payment_source,d.address1,d.bill_address1,d.zip,d.bill_zip,o.transaction_fee
        from inv_orders o,inv_orders_details d where o.order_id=d.order_id AND o.order_id = "'. $order_id .'" group by o.order_id order by order_date DESC');
    $customer = $db->func_query_first('select * from inv_customers where email = "'. $orderInfo['email'] .'"');
    if (!$customer) {
        $customer = $db->func_query_first('select * from inv_po_customers where email = "'. $orderInfo['email'] .'"');
    }
    $recipientName = explode(' ', $order['shipTo']['name']);
    $sku = '';
    $name = '';
    $qty = '';
    $price = '';
    foreach ($order['items'] as $key => $product) {
        $sku .= '('. $product['sku'] .')' . (isset($items[($key + 1)])? ', ': '');
        $name .= '('. $product['name'] .')' . (isset($items[($key + 1)])? ', ': '');
        $qty .= '('. $product['quantity'] .')' . (isset($items[($key + 1)])? ', ': '');
        $price .= '('. $product['unitPrice'] .')' . (isset($items[($key + 1)])? ', ': '');
    }
    $rowData = array(
        $order['orderNumber'],
        americanDate($order['orderDate']),
        americanDate($order['paymentDate']),
        $order['orderTotal'],
        $order['amountPaid'],
        $order['taxAmount'],
        $order['shippingAmount'],
        $order['requestedShippingService'],
        $order['weight']['value'],
        'Order Notes from Buyer',
        $order['billTo']['name'],
        $customer['firstname'],
        $customer['lastname'],
        $order['customerEmail'],
        $order['billTo']['phone'],
        $order['shipTo']['name'],
        $recipientName[0],
        $recipientName[1] . (($recipientName[2])? ' ' . $recipientName[2]: ''),
        $order['shipTo']['phone'],
        $order['shipTo']['company'],
        $order['shipTo']['street1'],
        $order['shipTo']['street2'],
        $order['shipTo']['street3'],
        $order['shipTo']['city'],
        $order['shipTo']['state'],
        $order['shipTo']['postalCode'],
        $order['shipTo']['countyr'],
        $sku,
        $name,
        $qty,
        $price
        );
    fputcsv($fp, $rowData,',');
}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filename);
@unlink($filename);