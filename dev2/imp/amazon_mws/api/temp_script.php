<?php
include_once("../../config.php");
include_once("../../inc/functions.php");
require('../includes/classes.php'); //autoload classes, not needed if composer is being used
//date_default_timezone_set("America/Los_Angeles");
date_default_timezone_set("UTC");
set_time_limit(0);
ini_set("memory_limit", "20000M");
$merchantInfo = $db->func_query_first("Select * from amazon_credential where id=2");
    if(!@$merchantInfo){
        echo "No merchant exist";
        exit;
    }
    $startDate = $merchantInfo['last_cron_date'];
    $prefix = $merchantInfo['prefix'];
    $amazon_credential_id = $merchantInfo['id'];
    $majorLastDate = '2015-11-01 20:32:00';
    
require_once '../amazon-config.php';
$rows = $db->func_query("SELECT * 
FROM inv_orders
WHERE store_type LIKE  '%amazon%'
AND MONTH(order_date) =  '01' AND YEAR(order_date)='2016'");
foreach($rows as $row)
{
  
     $orderItems = getOrderLineItems($row['order_id']);
            foreach($orderItems as $orderItem){
                $sku  = $orderItem['SellerSKU'];
                $order_item_id = $orderItem['OrderItemId'];
                $qty  = $orderItem['QuantityOrdered'];
                $order_price = $orderItem['ItemPrice']['Amount'];
                $true_cost = getTrueCost($sku);
                $product_unit = 0.00;
                $promotion_discount = 0.00;
                 if($orderItem['PromotionDiscount']){
                    $promotion_discount = $orderItem['PromotionDiscount']['Amount'];
                }
                
                if($promotion_discount<=0.00) continue;


                $order_price = $order_price - $promotion_discount;
                
                $item_sku = $db->func_escape_string($sku);
                $kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$item_sku'");
                if($kit_skus){
                    $kit_skus_array = explode(",",$kit_skus['linked_sku']);
                    for($_i=1;$_i<=$qty;$_i++)
                        {
                        $zz = 0;
                    
                        foreach($kit_skus_array as $kit_skus_row){
                        $kit_skus_row = $kit_skus_row;
                        $true_cost = getTrueCost($kit_skus_row);
                        $_qty =  1;
                        $promotion_discount = $order_price / $qty;
                        $product_unit = $product_unit;
                        $order_price = $product_unit;

                        if($zz > 0){
                            $true_cost = 0.00;
                        $_qty =  1;
                        $product_unit = 0.00;
                        $order_price = 0.00;
                        $promotion_discount = 0.00;
                        }
                        
                     //   $db->db_exec("UPDATE inv_orders_items SET promotion_discount='".$promotion_discount."' WHERE product_sku='".$kit_skus_row."' and order_id='".$row['order_id']."' and order_item_id='".$order_item_id."'");
                       echo "UPDATE inv_orders_items SET promotion_discount='".$promotion_discount."' WHERE product_sku='".$kit_skus_row."' and order_id='".$row['order_id']."' and order_item_id='".$order_item_id."'"."<br>";
                    
                        $zz++;
                    }
                }

                    //mark kit sku need_sync on all marketplaces
                   
                }
                else{
                  //$db->db_exec("UPDATE inv_orders_items SET promotion_discount='".$promotion_discount."' WHERE product_sku='".$item_sku."' and order_id='".$row['order_id']."' and order_item_id='".$order_item_id."'");
               echo "UPDATE inv_orders_items SET promotion_discount='".$promotion_discount."' WHERE product_sku='".$item_sku."' and order_id='".$row['order_id']."' and order_item_id='".$order_item_id."'"; 
                }
                echo "-----------------------------------------------<br>";
                //echo $row['order_id']."<br>";
            }
}
function getOrderLineItems($order_id)
{
   // require('../includes/classes.php');
    global $db;
     try {
        $amz = new AmazonOrderItemList("myStore"); //store name matches the array key in the config file
       // $amz->setLimits('Modified', $startDate); //accepts either specific timestamps or relative times 
       // $amz->setFulfillmentChannelFilter("MFN"); //no Amazon-fulfilled orders
      $amz->setOrderId($order_id);
        $amz->setUseToken(); //tells the object to automatically use tokens right away
        $amz->fetchItems(); //this is what actually sends the request
        return $amz->getItems();
    } catch (Exception $ex) {
        echo 'There was a problem with the Amazon library. Error: '.$ex->getMessage();
    }
}

?>
