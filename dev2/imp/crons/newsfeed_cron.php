<?php
require_once("../config.php");
require_once("../inc/functions.php");
$trackers = $db->func_query("SELECT * FROM inv_tracker order by datetime asc limit 30");
$paid_unshipped_orders = $db->func_query("SELECT * FROM inv_orders where payment_source='Paid' AND (order_status='Unshipped' OR order_status='Processed')order by id asc limit 30");
$hold_orders = $db->func_query("SELECT * FROM inv_orders  where LOWER(order_status)='on hold' order by id asc limit 30");
$shipment_comments = $db->func_query("SELECT a.*, b.name as username FROM inv_shipment_comments a inner Join inv_users b on a.user_id = b.id where user_id<>'0' order by a.id asc limit 30");
$buyback_comments = $db->func_query("SELECT a.*, b.shipment_number as lbb_name,c.name as username FROM inv_buyback_comments a inner Join oc_buyback b on a.buyback_id = b.buyback_id inner Join inv_users c on a.user_id = c.id where user_id<>'0' order by a.id asc limit 30");
$paypals = $db->func_query("SELECT * FROM inv_transactions WHERE is_mapped = '0' order by id desc limit 30");
$followed_orders = $db->func_query("SELECT a.*, b.name as username FROM inv_orders a inner Join inv_users b on a.followed_by = b.id WHERE is_followed = '1' order by id asc limit 30");
$followed_shipments = $db->func_query("SELECT a.*, b.name as username FROM inv_shipments a inner Join inv_users b on a.followed_by = b.id WHERE is_followed = '1' order by id asc limit 30");

$json = array();
$json['trackers'] = $trackers;	
$json['paid_unshipped_orders'] = $paid_unshipped_orders;	
$json['hold_orders'] = $hold_orders;
$json['shipment_comments'] = $shipment_comments;
$json['buyback_comments'] = $buyback_comments;
$json['paypals'] = $paypals;
$json['followed_orders'] = $followed_orders;
$json['followed_shipments'] = $followed_shipments;
echo json_encode($json);

?>