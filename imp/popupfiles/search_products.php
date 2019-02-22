<?php
require_once("../auth.php");
include_once '../inc/functions.php';

$sku = $db->func_escape_string($_GET['query']);
$sku = strtolower($sku);
$where = " Where p.sku != '' and p.is_main_sku = 1 AND lower(p.sku) LIKE '%".$sku."%'";
 $_query = "Select p.sku ,  pd.name 
		   from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id)
		  
           $where group by p.sku order by p.sku LIMIT 25";
$rows= $db->func_query($_query);
$suggestion = array();
foreach($rows as $row)
{
		
	$suggestion[] = array("value"=>$db->func_escape_string($row['sku']." - ".utf8_encode($row['name'])),
						  "data"=>$row['sku']);
}
$return = array();
$return = array(
				"query"=>"Unit",
				"suggestions"=>$suggestion

);

echo json_encode($return);


?>