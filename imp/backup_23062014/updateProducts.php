<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("config.php");
include_once("amazon/update_qty.php");
include_once("ebay/eb_updateQty.php") ;

$productUpdateArray = json_decode($_REQUEST['productUpdateArray'],true);

$token = $db->func_query_first_cell("Select config_value from configuration where config_key = 'USER_TOKEN'");
if(!$token){
    exit;
}

//$message = print_r($productUpdateArray,true);
//mail("vipin.garg12@gmail.com","Request IMP",$message);

$response = array();

if(is_array($productUpdateArray) and count($productUpdateArray) > 0){
    foreach($productUpdateArray as $product){
        $SKU = $product['sku'];
        $Qty = $product['qty'];
        
        $db->db_exec("Update oc_product SET quantity = '$Qty' , date_modified = '".date('Y-m-d H:i:s')."' where model = '$SKU' OR sku = '$SKU'");
    }
    
    $response[] = updateInventory($productUpdateArray);
    
    //$response[] = updateEbayQty($token,$productUpdateArray);
}

$data .= "Response- ".print_r($response,true) . "\n";

if($data){
    $message = "Hi Admin , <br />";
    $message .= "Update All Sync Report <br />";

    $message .= $data;

    $message .= "<br /><br /> Thanks, <br /> Phonepartsusa Team";

    $headers = "From:no-reply@phonepartsusa.com\r\nFromName:phonepartsusa\r\nContent-type:text/html;charset=utf-8;";
    mail("vipin.garg12@gmail.com","Update All Sync Report",$message,$headers);

    mail("saadahmed786@gmail.com","Update All Sync Report",$message,$headers);
}

error_log($data , 3 , "log/inventory.log");

echo "success";