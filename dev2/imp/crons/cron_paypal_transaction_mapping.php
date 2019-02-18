<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
$rows = $db->func_query("select * from inv_orders where is_manual=1 and paid_price=0.00");
foreach($rows as $row)
{
	$detail = $db->func_query("SELECT transaction_id FROM inv_transactions WHERE trim(order_id)='".$row['order_id']."'");
	if(!$detail)
	{
		$detail = $db->func_query("SELECT transaction_id FROM inv_transactions_multi WHERE order_id='".$row['order_id']."'");
	}
	
	if($detail)
	{
				foreach($detail as $det)
				{
					$detail2 = $db->func_query_first("SELECT * FROM inv_transactions WHERE transaction_id='".$det['transaction_id']."'");
					$check_for_paid = $db->func_query_first("SELECT paid_price FROM inv_orders WHERE order_id='".$$row['order_id']."'");
			if((float)$check_for_paid==0.00)
			{
				$db->db_exec("UPDATE inv_orders SET paid_price=".$detail2['amount']." where order_id='".$row['order_id']."'");	
			}
			
			}
			
	}
}
echo 1;
?>