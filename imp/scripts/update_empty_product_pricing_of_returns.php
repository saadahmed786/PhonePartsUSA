<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
require_once("../config.php");
require_once("../inc/functions.php");
$returns = $db->func_query("SELECT * FROM inv_returns WHERE rma_status='In QC'");
foreach($returns as $return)
{
	$return_check = $db->func_query_first_cell("SELECT SUM(price) FROM inv_return_items WHERE return_id='".$return['id']."' ");	
	if($return_check>0)
	{
		continue;
	}
	else
	{
		$return_items = $db->func_query("SELECT id,sku FROM inv_return_items WHERE return_id='".$return['id']."' ");
		foreach($return_items as $return_item)
		{
			$order_item = $db->func_query_first("SELECT product_unit,product_price,product_qty FROM inv_orders_items WHERE order_id='".$return['order_id']."' AND product_sku='".$return_item['sku']."'");	
			
			
			$unit_price = $order_item['product_unit'];
			$product_price = $order_item['product_price'];
			
			if($unit_price>0)
			{
				$price = $unit_price;
			}
			else
			{
				$price = $product_price / $order_item['product_qty'];	
			}
			echo $return['rma_number']."<br>";
			print_r($order_item);
			echo "<br>";
			echo "SELECT product_unit,product_price,product_qty FROM inv_orders_items WHERE order_id='".$return['order_id']."' AND product_sku='".$return_item['sku']."'"."<br>";
			$db->db_exec("UPDATE inv_return_items SET price='".(float)$price."' WHERE id='".$return_item['id']."'");
			echo "Updated <br>=====================================================<br>";
			
		}
		
	}
	
}


$rows = $db->func_query("select * from inv_vendor_po where month(date_added)>='".date('m',strtotime('-1 month'))."' and year(date_added)>='".date('Y',strtotime('-1 month'))."'");
  foreach($rows as $row)
  {
    $vpo_id = (int)($row['id']);
    // $row = $db->func_query_first("SELECT * FROM inv_vendor_po WHERE id = '$vpo_id'");;
    $vendor_id = $row['vendor'];
    $vendor_po_id = $row['vendor_po_id'];


    $applied_credits = $db->func_query_first_cell("SELECT SUM(amount) from inv_vendor_credit_data where vendor_id='".$row['vendor']."' and vendor_po_id='".$row['id']."'");

    $shipment_data = $db->func_query_first("SELECT a.ex_rate, SUM(b.qty_received) as qty_received,sum(b.unit_price * b.qty_received) as unit_price,(select sum(c.shipping_cost) from inv_shipments c where c.vendor_po_id=a.vendor_po_id) as shipping_cost from inv_shipments a,inv_shipment_items b where a.id=b.shipment_id and a.vendor_po_id='".$vendor_po_id."'");

   
        $payment_status_new = 'No Payment Status';
   
     if((int)$shipment_data['qty_received']==0 && $row['amount_paid']+($applied_credits*(-1)))
        {
            $payment_status_new = 'Pre-Paid';
        }
     if((int)$shipment_data['qty_received']>0 && (round(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($row['amount_paid']-$applied_credits)+$row['amount_refunded']))==0)
        {
            $payment_status_new= 'Paid';
        }

        if((int)$shipment_data['qty_received']>0 && (round(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($row['amount_paid']-$applied_credits)+$row['amount_refunded']))>0)
        {
            $payment_status_new= 'Not Paid';
        }

        if((int)$shipment_data['qty_received']>0 && (round(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($row['amount_paid']-$applied_credits)+$row['amount_refunded']))<0)
        {
            $payment_status_new= 'Over-Paid';
        }
        if($row['payment_status_new']!=$payment_status_new)
        {
        echo $vendor_po_id."--$payment_status_new<br>";
            
       $db->db_exec("UPDATE inv_vendor_po SET payment_status_new='".$payment_status_new."' WHERE id='".$vpo_id."'");
        }

  }
?>