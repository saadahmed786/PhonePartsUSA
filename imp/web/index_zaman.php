<?php

date_default_timezone_set("America/Los_Angeles");

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'config.php';

//imp config file
include_once '../config.php';

include_once '../inc/functions.php';

//opencart config file
include_once '../../config.php';

$startDate = getLastCronDate();
$startDate = date('Y-m-d H:i:s', (strtotime($startDate)));

//connect opencart db and fetch orders
$conn2 = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die(mysql_error() . " opencart db connect error");





$orderArray = array();
$order_query = "Select * from oc_order where ( oc_order.order_status_id IN( 15 , 24 , 3 , 16 , 7 , 21 , 11) ) AND oc_order.date_modified >= '$startDate'";

$orderQuery = mysqli_query($conn2, $order_query) or die(mysqli_error($conn2) . $order_query);

$k = 0;
while ($order = mysqli_fetch_assoc($orderQuery)) {
    if ($order['order_status_id'] == 15) {
        $status = "Processed";
    } elseif ($order['order_status_id'] == 24) {
        $status = "Processed";
    } elseif ($order['order_status_id'] == 3) {
        $status = "Shipped";
    } elseif ($order['order_status_id'] == 16) {
        $status = "Voided";
    } elseif ($order['order_status_id'] == 11) {
        $status = "Refunded";
    } elseif ($order['order_status_id'] == 7) {
        $status = "Canceled";
    } elseif ($order['order_status_id'] == 21) {
        $status = "On Hold";
    } else {
        $status = "Completed";
        continue;
    }

    $shipping_cost_query = mysqli_query($conn2, "Select `value` from oc_order_total where `code` = 'shipping' and order_id = '" . $order['order_id'] . "'");
    $shipping_cost_result = mysqli_fetch_assoc($shipping_cost_query);
    $shipping_cost = $shipping_cost_result['value'];
    $ref_order_id = 0;
    if ($order['ref_order_id']) {
        $ref_order_id = $order['order_id'];
        $order['order_id'] = $order['ref_order_id'];
    }
	
if($order['payment_method'] == 'Cash at Store Pick-Up' or strtolower($order['payment_method'])=='terms' or strtolower($order['payment_method'])=='cash' )
{
    $_payment_source =  'Unpaid';
}
else if($order['payment_method']=='Replacement')
{
    $_payment_source = 'Replacement';
}
else
{
    $_payment_source = 'Paid';
}

    $orderArray[$k] = array('order_id' => $order['order_id'],
        'order_price' => $order['total'],
        'order_date' => $order['date_added'],
        'date_modified' => $order['date_modified'],
        'status' => $status,
        'firstname' => $order['firstname'],
        'email' => $order['email'],
        'lastname' => $order['lastname'],
        'telephone' => $order['telephone'],
        'add1' => $order['shipping_address_1'],
        'add2' => $order['shipping_address_2'],
        'city' => $order['shipping_city'],
        'state' => $order['shipping_zone'],
        'country' => $order['shipping_country'],
        'zip' => $order['shipping_postcode'],
        'bill_add1' => $order['payment_address_1'],
        'bill_add2' => $order['payment_address_2'],
        'bill_city' => $order['payment_city'],
        'bill_state' => $order['payment_zone'],
        'bill_country' => $order['payment_country'],
        'bill_zip' => $order['payment_postcode'],
        'payment_method' => $order['payment_method'],
        'payment_source' =>$_payment_source,
        'payment_code' => $order['payment_code'],
        'shipping_method' => $order['shipping_method'],
        'customer_po' => $order['po_no'],
        'shipping_cost' => $shipping_cost
    );
    if (!$ref_order_id) {
        $order_items_query = mysqli_query($conn2, "select * from oc_order_product Where oc_order_product.order_id = '" . $order['order_id'] . "'");
    } else {
        $order_items_query = mysqli_query($conn2, "select * from oc_order_product Where oc_order_product.order_id = '" . $ref_order_id . "'");
    }
    while ($order_items_row = mysqli_fetch_assoc($order_items_query)) {
        $orderArray[$k]['Items'][] = array('order_item_id' => $order['order_id'] . "-" . $order_items_row['product_id'],
            'product_sku' => $order_items_row['model'],
            'product_qty' => $order_items_row['quantity'],
            'product_true_cost' => getTrueCost($order_items_row['model']),
            'product_price' => $order_items_row['price'],
            'product_total' => $order_items_row['total'],
        );
    }

    $k++;
}

mysqli_close($conn2);

$db = new Database();

insertOrderinImp($orderArray);

function insertOrderinImp($orderArray) {
    global $db;

    if (!$orderArray) {
        $_SESSION['message'] = "No Orders found in open cart.";
        return;
    }

    foreach ($orderArray as $order) {
        $orderId = $order['order_id'];
        $orderTotal = $order['order_price'];
        $orderDate = $order['order_date'];
        $orderStatus = $order['status'];

        $email = $order['email'];
        $firstname = $db->func_escape_string($order['firstname']);
        $lastname = $db->func_escape_string($order['lastname']);
        $phone = $db->func_escape_string($order['telephone']);
        $add1 = $db->func_escape_string($order['add1']);
        $add2 = $db->func_escape_string($order['add2']);
        $city = $db->func_escape_string($order['city']);
        $state = $db->func_escape_string($order['state']);
        $country = $db->func_escape_string($order['country']);
        $zip = $db->func_escape_string($order['zip']);

        $bill_add1 = $db->func_escape_string($order['bill_add1']);
        $bill_add2 = $db->func_escape_string($order['bill_add2']);
        $bill_city = $db->func_escape_string($order['bill_city']);
        $bill_state = $db->func_escape_string($order['bill_state']);
        $bill_country = $db->func_escape_string($order['bill_country']);
        $bill_zip = $db->func_escape_string($order['bill_zip']);

        $paymentMethod = $db->func_escape_string($order['payment_method']);
		
        $payment_source = $db->func_escape_string($order['payment_source']);
        if($payment_source=='Paid')
        {
            $_paid_price = $orderTotal;
        }
        else
        {
            $_paid_price = 0.00;
        }
        $paymentCode = $db->func_escape_string($order['payment_code']);
        $shippingMethod = $db->func_escape_string($order['shipping_method']);
        $shipping_cost = $order['shipping_cost'];
		$store_type='web';
		$po_business_id='';

        $customer_po = $db->func_escape_string($order['customer_po']);
		if($paymentMethod=='Terms'){
		$orderStatus='Estimate';
		$store_type = 'po_business';	
		$po_business_id = $db->func_query_first_cell("SELECT id FROM inv_po_customers where LOWER(email)='".strtolower($email)."'");	
		}

        $customer_name = $db->func_escape_string($firstname . " " . $lastname);

        //if order is new
        $isExist = $db->func_query_first_cell("select id from inv_orders where order_id = '$orderId'");
        if (!$isExist) {
            if ($orderStatus == 'Voided' || $orderStatus == 'Canceled' || $orderStatus == 'Refunded') {
                $db->db_exec("insert into inv_return_orders (order_id,order_date,return_date,status,store_type,dateofmodification)
                             values ('$orderId','$orderDate','$orderDate','open','web','" . date('Y-m-d H:i:s') . "')");
            } else {
                $db->db_exec("insert into inv_orders (order_id,email,order_date,order_price,paid_price,order_status,payment_source,status,store_type,customer_name,dateofmodification,po_business_id, customer_po)
                             values ('$orderId','$email','$orderDate','$orderTotal','$_paid_price' ,'$orderStatus','$payment_source','open' , '$store_type','$customer_name','" . date('Y-m-d H:i:s') . "','".$po_business_id."', '". $customer_po ."') ");

                if ($paymentCode == 'pp_payflow_pro') {
                    $avs_record = $db->func_query_first("SELECT * from oc_payflow_admin_tools Where order_id='" . $orderId . "'");
                    if ($avs_record) {
                        if ($avs_record['avsaddr'] == 'Y') {
                            $is_address_verified = 'Confirmed';
                        } else {
                            $is_address_verified = 0;
                        }
                        $db->db_exec("UPDATE inv_orders SET avs_code='" . $avs_record['avszip'] . "',is_address_verified='" . $is_address_verified . "',payment_source='Payflow' WHERE order_id='" . $orderId . "'");
                    }
                }
            }

            $db->db_exec("insert into inv_orders_details (order_id,first_name,last_name,phone_number,address1,address2,city,state,country,zip,bill_address1,bill_address2,bill_city,bill_state,bill_country,bill_zip,payment_method,shipping_method,shipping_cost,dateofmodification)
                         values ('$orderId' , '$firstname' , '$lastname' , '$phone' , '$add1' , '$add2' ,'$city' , '$state' , '$country' , '$zip', '$bill_add1' , '$bill_add2' ,'$bill_city' , '$bill_state' , '$bill_country' , '$bill_zip' , '$paymentMethod', '$shippingMethod' ,'$shipping_cost' ,'" . date('Y-m-d H:i:s') . "')");

            foreach ($order['Items'] as $order_item) {
                $product_sku = $order_item['product_sku'];
                $product_price = $order_item['product_price'];
                $product_price = str_replace(',', '', (string) $product_price);
                $product_total = $order_item['product_total'];
                $product_total = str_replace(',', '', (string) $product_total);
                $product_cost = $order_item['product_true_cost'];

                $orderItemData = array();
                $orderItemData['order_id'] = $orderId;
                $orderItemData['order_item_id'] = $order_item['order_item_id'];
                $orderItemData['product_sku'] = $product_sku;
                $orderItemData['product_qty'] = (int) $order_item['product_qty'];
                $orderItemData['product_unit'] = $product_price;
                $orderItemData['product_true_cost'] = $product_cost;
                $orderItemData['product_price'] = $product_total;
                $orderItemData['dateofmodification'] = date('Y-m-d H:i:s');

                //check if SKU is KIT SKU
                $kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$product_sku'");
                if ($kit_skus) {
                    $kit_skus_array = explode(",", $kit_skus['linked_sku']);
                    $zz = 0;
                    foreach ($kit_skus_array as $kit_skus_row) {
                        $orderItemData['product_sku'] = $kit_skus_row;
                        $orderItemData['product_true_cost'] = getTrueCost($kit_skus_row);
                        if ($zz > 0) {
                            $orderItemData['product_unit'] = 0.00;
                            $orderItemData['product_price'] = 0.00;
                            $orderItemData['product_true_cost'] = 0.00;
                        }

                        $db->func_array2insert("inv_orders_items", $orderItemData);
                        $zz++;
                    }

                    //mark kit sku need_sync on all marketplaces
                    $db->db_exec("update inv_kit_skus SET need_sync = 1, dateofmodifcation = '" . date('Y-m-d H:i:s') . "' where kit_sku = '$product_sku'");
                } else {
                    $db->func_array2insert("inv_orders_items", $orderItemData);
                }
            }
        }
        //if order exist and updated
        else {
            if ($orderStatus == 'Voided' || $orderStatus == 'Canceled' || $orderStatus == 'Refunded') {
                $db->db_exec("update inv_orders SET order_status = '$orderStatus' where order_id = '$orderId'");

                $isReturnExist = $db->func_query_first_cell("select id from inv_return_orders where order_id = '$orderId'");
                if (!$isReturnExist) {
                    $returnDate = $order['date_modified'];
                    $db->db_exec("insert into inv_return_orders (order_id,email,order_price,order_date,return_date,status,store_type,dateofmodification)
                             values ('$orderId','$email','$orderTotal','$orderDate','$returnDate','open','web','" . date('Y-m-d H:i:s') . "')");

                    $db->db_exec("delete from inv_orders_details where order_id = '$orderId'");

                    $db->db_exec("insert into inv_orders_details (order_id,first_name,last_name,phone_number,address1,address2,city,state,country,zip,bill_address1,bill_address2,bill_city,bill_state,bill_country,bill_zip,payment_method,shipping_method,shipping_cost,dateofmodification)
                         values ('$orderId' , '$firstname' , '$lastname' , '$phone' , '$add1' , '$add2' ,'$city' , '$state' , '$country' , '$zip' , '$bill_add1' , '$bill_add2' ,'$bill_city' , '$bill_state' , '$bill_country' , '$bill_zip', '$paymentMethod' , '$shippingMethod', '$shipping_cost' ,'" . date('Y-m-d H:i:s') . "')");
                } else {
                    $db->db_exec("update inv_return_orders SET is_updated = 1 , ignored = 0 , dateofmodification = '" . date('Y-m-d H:i:s') . "' where order_id = '$orderId'");
                }
            } else {
                //$db->db_exec("update inv_orders SET is_updated = 1 , ignored = 0 , order_status = '$orderStatus' , dateofmodification = '".date('Y-m-d H:i:s')."' where order_id = '$orderId'");
            }

            $db->db_exec("delete from inv_orders_items where order_id = '$orderId'");

            foreach ($order['Items'] as $order_item) {
                $product_sku = $order_item['product_sku'];
                $product_price = $order_item['product_price'];
                $product_price = str_replace(',', '', (string) $product_price);
                $product_total = $order_item['product_total'];
                $product_total = str_replace(',', '', (string) $product_total);
                $product_cost = $order_item['product_true_cost'];

                $orderItemData = array();
                $orderItemData['order_id'] = $orderId;
                $orderItemData['order_item_id'] = $order_item['order_item_id'];
                $orderItemData['product_sku'] = $product_sku;
                $orderItemData['product_qty'] = (int) $order_item['product_qty'];
                $orderItemData['product_unit'] = $product_price;
                $orderItemData['product_true_cost'] = $product_cost;
                $orderItemData['product_price'] = $product_total;
                $orderItemData['dateofmodification'] = date('Y-m-d H:i:s');

                //check if SKU is KIT SKU
                $kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$product_sku'");
                if ($kit_skus) {
                    $kit_skus_array = explode(",", $kit_skus['linked_sku']);
                    $zz = 0;
                    foreach ($kit_skus_array as $kit_skus_row) {
                        $orderItemData['product_sku'] = $kit_skus_row;
                        $orderItemData['product_true_cost'] = getTrueCost($kit_skus_row);
                        if ($zz > 0) {
                            $orderItemData['product_unit'] = 0.00;
                            $orderItemData['product_price'] = 0.00;
                            $orderItemData['product_true_cost'] = 0.00;
                        }

                        $db->func_array2insert("inv_orders_items", $orderItemData);
                        $zz++;
                    }

                    //mark kit sku need_sync on all marketplaces
                    $db->db_exec("update inv_kit_skus SET need_sync = 1, dateofmodifcation = '" . date('Y-m-d H:i:s') . "' where kit_sku = '$product_sku'");
                } else {
                    $db->func_array2insert("inv_orders_items", $orderItemData);
                }
            }
        }
    }

    $cronDate = date('Y-m-d H:i:s');
    $db->db_exec("update configuration set config_value = '$cronDate' where config_key = 'WEB_LAST_CRON_TIME' ");

    return "Orders inserted successfully.";
}

function getLastCronDate() {
    global $db;

    $startDate = $db->func_query_first_cell("select config_value  from configuration where config_key = 'WEB_LAST_CRON_TIME'");
    if (!intval($startDate)) {
        $startDate = date('Y-m-d H:i:s');
    } else {
        return $startDate;
    }
}

if ($_REQUEST['m'] == 1) {
    $_SESSION['message'] = "Order imported successfully";
    header("Location:$host_path/order.php");
}

echo "success";
?>