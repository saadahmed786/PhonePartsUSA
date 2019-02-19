<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("config.php");
include_once("ebay/eb_updateQty.php") ;
include_once("amazon/update_qty.php");
include_once("web/update_qty.php");

$token = $db->func_query_first_cell("Select config_value from configuration where config_key = 'USER_TOKEN'");

if(!$token){
    exit;
}

$orders = $db->func_query("select ot.* , o.store_type from inv_orders_items ot left join inv_orders o on (ot.order_id = o.order_id) where o.status = 'open' and o.fishbowl_uploaded = 1");

$webArray  = array();
$ebayArray = array();
$amazoneArray = array();
$orderIds = array();

foreach($orders as $order){
    $storeType = $order['store_type'];
    $orderId   = $order['order_id'];
    $sold_qty  = $order['product_qty'];
    $product_model  = $order['product_sku'];

    $orderIds[] = "'" . $orderId . "'";
    $product_data = $db->func_query_first("Select product_id , quantity from oc_product where model = '$product_model' OR sku = '$product_model'");
    if(!$product_data){
        continue;
    }

    $updateQty = $product_data['quantity'];
    if($updateQty <= 0){
        $updateQty = 0;
    }

    $ebayArray[] = array('sku' => $product_model , 'qty' => $updateQty);
    $amazoneArray[] = array('sku' => $product_model , 'qty' => $updateQty);
}

//update ebay qty
if(count($ebayArray) > 0){
    $ebay_response = updateEbayQty($token,$ebayArray);
}

//update amazon qty
if(count($amazoneArray) > 0){
    $amazon_response = updateInventory($amazoneArray);
}

$data = "Amazon Update - ".print_r($amazoneArray,true) . "\n <br />";
$data .= "eBay Update - ".print_r($ebayArray,true) . "\n <br />";
$data .= "Order IDs- ".print_r($orderIds,true) . "\n <br />";

$data .= "---------------------------------------------------------------- <br />";
$data .= "eBay Response- ".print_r($ebay_response,true) . "\n <br />";
$data .= "Amazon Response- ".print_r($amazon_response,true) . "\n <br />";

if($data){
    $message = "Hi Admin , <br />";
    $message .= "Inventory Sync Report <br />";

    $message .= $data;

    $message .= "<br /><br /> Thanks, <br /> Phonepartsusa Team";

    $headers = "From:no-reply@phonepartsusa.com\r\nFromName:phonepartsusa\r\nContent-type:text/html;charset=utf-8;";
    //mail("vipin.garg12@gmail.com","Inventory Sync Report",$message,$headers);
    mail("saadahmed786@gmail.com","Inventory Sync Report",$message,$headers);
}

error_log($data , 3 , "log/inventory.log");

if($orderIds){
    $orderIdStr = implode("," , $orderIds);
    $db->db_exec("update inv_orders set status = 'closed' where  order_id  IN ($orderIdStr)");
}

if($_REQUEST['m'] == 1){
    $_SESSION['message'] = "Invenyory sync successfully";
    header("Location:$host_path/order.php");
}

echo "success";

?>