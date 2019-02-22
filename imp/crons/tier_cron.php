<?php
require_once("../config.php");
require_once("../inc/functions.php");

$skus = $db->func_query("SELECT sku FROM oc_product where status = '1' limit 1000");
foreach ($skus as $sku) {
$sale_60 = $db->func_query_first_cell("SELECT sum(b.product_qty) FROM inv_orders_items b, inv_orders a where a.order_id=b.order_id and b.product_sku='".$sku['sku']."' and lower(a.order_status) not in ('on hold','voided','canceled','cancelled') and a.order_date BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE() ");
	if ($sale_60>10) {
		$db->func_query("update oc_product SET tier = '1' WHERE sku='" . $sku['sku'] . "'");
	} else if($sale_60>0) {
		$db->func_query("update oc_product SET tier = '2' WHERE sku='" . $sku['sku'] . "'");
	} else {
		$db->func_query("update oc_product SET tier = '3' WHERE sku='" . $sku['sku'] . "'");
	}
}

$disabled = $db->func_query("SELECT a.*, (a.`amount` + SUM(b.`amount`)) balance FROM `oc_voucher` a LEFT OUTER JOIN `oc_voucher_history` b ON a.`voucher_id` = b.`voucher_id` where a.status=1 and date(a.date_added)>='2018-09-29' and date(a.date_added)<=DATE(NOW()) - INTERVAL 90 DAY group by a.voucher_id having (a.`amount` + SUM(b.`amount`))>0");

foreach($disabled as $dis)
{
	$db->db_exec("UPDATE oc_voucher SET status=0 where voucher_id='".$dis['voucher_id']."'");
	$vouch_id = addVoucher('','store_credit',($dis['balance']*(-1)),linkToVoucher($dis['voucher_id'],'', $dis['code']));
       		$db->db_exec("UPDATE inv_vouchers SET voucher_id='".(int)$dis['voucher_id']."',description='Disabled Credit' where id='".$vouch_id."'");


       				$accounts = array();
					$accounts['description'] = $dis['code'].' Expired (90 Days)';
					$accounts['debit'] = 0.00;
					$accounts['credit'] = $dis['price'];
					$accounts['customer_email'] = $dis['to_email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'store_credit';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // disabled store credit


					$accounts = array();
					$accounts['description'] = $dis['code'].' Expired (90 Days)';
					$accounts['credit'] = 0.00;
					$accounts['debit'] = $dis['price'];
					$accounts['customer_email'] = $dis['to_email'];
					$accounts['type']='store_credit';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // disabled store credit



}
echo "Success";
?>