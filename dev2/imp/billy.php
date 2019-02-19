<?php
include_once 'auth.php';
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
echo getOrderProfit(array('order_id'=>'PO498','store_type'=>'po_business','order_price'=>'1916.22','payment_source'=>''));

?>