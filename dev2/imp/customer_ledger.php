<?php
include 'auth.php';
include 'inc/functions.php';
function date_compare($a, $b)
{
    $t1 = strtotime($a['order_date']);
    $t2 = strtotime($b['order_date']);
    return $t1 - $t2;
}  


$email = $db->func_escape_string(base64_decode($_GET['email']));

$query = "SELECT 
`inv_orders`.*,
`inv_orders_details`.*,
`oc_order`.`ip`,
`oc_order`.`payment_method` 
FROM
`inv_orders` 
INNER JOIN `inv_orders_details`
ON `inv_orders`.`order_id` = `inv_orders_details`.`order_id`
LEFT OUTER JOIN `oc_order` 
ON `inv_orders`.`order_id` = `oc_order`.`order_id` 
WHERE `inv_orders`.`email` = '$email' 
ORDER BY inv_orders.order_date  ASC";
$orders = $db->func_query($query);

$k = 0;
            $temp_order = array();
            foreach($orders as $order)
            {
                $temp_order[$k]['order_id'] = $order['order_id'];
                $temp_order[$k]['shipping_cost'] = $order['shipping_cost'];
                $temp_order[$k]['store_type'] = $order['store_type'];
                $temp_order[$k]['order_status'] = $order['order_status'];
                $temp_order[$k]['paid_price'] = $order['paid_price'];
                $temp_order[$k]['payment_method'] = $order['payment_method'];
                $temp_order[$k]['order_date'] = $order['order_date'];
                $temp_order[$k]['manual'] = 0;
                $k++;
            }
            $applied_vouchers = $db->func_query("SELECT voucher_id, amount as paid_price,date_added as order_date from oc_voucher_history WHERE manual=1 and customer_email='".$customer_email."'");
            
            foreach($applied_vouchers as $av)
            {
                $temp_order[$k]['order_id'] = $av['voucher_id'];
                $temp_order[$k]['shipping_cost'] = 0;
                $temp_order[$k]['store_type'] = 'voucher';
                $temp_order[$k]['order_status'] = 'Applied';
                $temp_order[$k]['paid_price'] = $av['paid_price']*(-1);
                $temp_order[$k]['payment_method'] = 'Cash Voucher';
                $temp_order[$k]['order_date'] = $av['order_date'];
                $temp_order[$k]['manual'] = 1;
                $temp_order[$k]['code'] = $db->func_query_first_cell("SELECT code FROM oc_voucher WHERE voucher_id='".$av['voucher_id']."'");
                $k++;
            }
            
            usort($temp_order, "date_compare");

            

$filename = "ledger-".$email."-" . date("Y-m-d") . ".csv";
$fp = fopen($filename, "w");

if ($_SESSION['display_cost']) {
    
    $headers = array("#", "Added", "Order ID", "Status", "Tracking No.","Credit", "Debit", "Balance");
} else {
    //With sub class
    //$headers = array("SKU","ItemName","Qty","Vendor","Class","Sub Class","Default Qty 1","Default Qty 3","Default Qty 10","Local Qty 1","Local Qty 3","Local Qty 10","WS Qty 1","WS Qty 3","WS Qty 10","Status");
    //Without sub class
   $headers = array("#", "Added", "Order ID", "Status", "Tracking No.");
}

fputcsv($fp, $headers,',');

foreach($temp_order as $i => $order) { 
        

                $subtotalTotal = $db->func_query_first_cell('SELECT SUM(`product_price`) FROM `inv_orders_items` WHERE `order_id` = "' . $order['order_id'] . '"');
                
                $_tax = (float)$db->func_query_first_cell('SELECT SUM(`value`) FROM `oc_order_total` WHERE `order_id` = "'. $order['order_id'] .'" AND `code` = "tax"');

                $order_total = (float)($subtotalTotal + $order['shipping_cost'] + $_tax);   

                if($order['store_type']=='po_business')
                {
                    if(strtolower($order['order_status'])=='shipped' || strtolower($order['order_status'])=='unshipped' )
                    {
                        $debit =$order['paid_price'];
                        $credit = $order_total;
                    }
                    else
                    {
                        $debit=0.00;
                        $credit = 0.00;
                    }
                }
                else
                {
                    
                    if (preg_match("/^cash (.*)/i", strtolower($order['payment_method'])) > 0)
                    {
                        if(strtolower($order['order_status'])=='shipped')
                        {
                            $credit = $order_total;
                            $debit = $order_total;
                        }
                        else
                        {
                            $debit = $order['paid_price'];
                            $credit = $order_total;
                        }
                    }
                    else
                    {
                        $credit = $order_total;
                        $debit = $order_total;
                    }


                }


                if(strtolower($order['order_status'])=='canceled' or strtolower($order['order_status'])=='voided' or strtolower($order['order_status'])=='refunded' )
                {
                    $debit = 0.00;
                    $credit = 0.00;
                }
                $balance = $balance + ($credit - $debit);
                $total_debit+=$debit;
                $total_credit+=$credit;
                $payStatus = 0;
                if (strtolower($order['order_status']) == 'processed' || (strtolower($order['store_type'] == 'po_business') && (strtolower($order['order_status']) == 'shipped' || strtolower($order['order_status']) == 'unshipped'))) {
                    $payStatus = 1;
                }
                if($_SESSION['display_cost'])
                {
                $rowData = array($i+1,americanDate($order['order_date']),($order['manual']?$order['code']: $order['order_id']),$order['order_status'],($order['manual']?'':''.getTrackingNo($order['order_id'])),$credit,$debit,$balance);
                }
                else
                {
                 $rowData = array($i+1,americanDate($order['order_date']),($order['manual']?$order['code']: $order['order_id']),$order['order_status'],($order['manual']?'':''.getTrackingNo($order['order_id'])));   
                }
                fputcsv($fp, $rowData,',');
            }



fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filename);
@unlink($filename);
