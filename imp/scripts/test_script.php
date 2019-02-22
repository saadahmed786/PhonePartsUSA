<?php
require_once("../config.php");
require_once("../inc/functions.php");

$deposited = $db->func_query_first_cell("select sum(amount) from inv_deposits where deposit_type='card'");

					$accounts = array();
					$accounts['description'] = 'Opening Balance Card Bank Deposits';
					$accounts['debit'] = $deposited;
					$accounts['credit'] = 0.00;
					$accounts['order_id'] = '';
					$accounts['customer_email'] = '';
					$accounts['type']='cash_on_hand';
					$accounts['contra_account_code'] = 'card';
					$accounts['date_added'] = date('Y-m-d 00:00:00');

					add_accounting_voucher($accounts); // debit entry


					$accounts = array();
					$accounts['description'] = 'Opening Balance Card Bank Deposits';
					$accounts['debit'] = 0.00;
					$accounts['credit'] = $deposited;
					$accounts['order_id'] = '';
					$accounts['customer_email'] = '';
					$accounts['type']='card';
					$accounts['contra_account_code'] = 'cash_on_hand';
					$accounts['date_added'] = date('Y-m-d 00:00:00');

					add_accounting_voucher($accounts); // credit entry


echo 1;
/*
$rows = $db->func_query("select * from inv_deposits where is_mapped=1 and voided=0 and date(ship_date)>='2018-12-21' order by 1 desc");
$total = 0;
foreach($rows as $row)
{

	
	
	if($check)
	{
		//continue;
	}

	$order_detail = getOrder($row['order_id']);
	if(!$order_detail)
	{
		continue;
	}
			
				$debit_account = 'shipping_expense';
				$credit_account = $row['carrier_code'];
				$description = stripDashes($row['service_code']).' Shipping #'.$row['order_id'];
				
			
					
					$row['shipping_cost'] = ($row['shipping_cost']<0?$row['shipping_cost']*(-1):$row['shipping_cost']);
					$total+=$row['shipping_cost'];
					$accounts = array();
					$accounts['description'] = $description;
					$accounts['debit'] = $row['shipping_cost'];
					$accounts['credit'] = 0.00;
					$accounts['order_id'] = $row['tracking_number'];
					// $accounts['customer_email'] = $order_detail['email'];
					$accounts['type']=$debit_account;
					$accounts['contra_account_code'] = $credit_account;
					$accounts['date_added'] = $row['date_added'];

					add_accounting_voucher($accounts); // store credit applied


					$accounts = array();
					$accounts['description'] = $description;
					$accounts['debit'] = 0.00;
					$accounts['credit'] = $row['shipping_cost'];
					$accounts['order_id'] = $row['tracking_number'];
					// $accounts['customer_email'] = $order_detail['email'];
					$accounts['type']=$credit_account;
					$accounts['contra_account_code'] = $debit_account;
					$accounts['date_added'] = $row['date_added'];

					add_accounting_voucher($accounts); // store credit applied

				
					




					

	//}

}
echo $total;*/
?>