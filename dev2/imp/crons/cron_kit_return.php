<?php
require_once("../config.php");
require_once("../inc/functions.php");
$rows = $db->func_query("SELECT b.* FROM inv_return_items b,inv_returns a WHERE a.id=b.return_id AND a.store_type='web' AND a.rma_status='Awaiting' AND b.sku IN (SELECT kit_sku FROM inv_kit_skus)");
//mail("xaman.riaz@gmail.com","Kit Return",var_dump($rows));
foreach($rows as $row)
{
	$db->db_exec("DELETE FROM inv_return_items WHERE id='".$row['id']."'");
	unset($row['id']);
	$kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '".$row['sku']."'");
	if($kit_skus){
		$data = array();
		$data = $row;
		$kit_skus_array = explode(",",$kit_skus['linked_sku']);
		$zz = 0;
		foreach($kit_skus_array as $kit_skus_row){
		$data['sku'] = $kit_skus_row;	
		$data['title'] = $db->func_query_first_cell("SELECT b.name FROM oc_product_description b, oc_product a WHERE a.product_id=b.product_id AND a.model='".$kit_skus_row."' ");
		if($zz>0)
		{
		$data['price'] = 0.00;	
		}
			$db->func_array2insert("inv_return_items",$data);
			$zz++;
		}	
	
	
	}
}
?>
