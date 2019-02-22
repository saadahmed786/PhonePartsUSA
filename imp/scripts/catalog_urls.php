<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$manufacturers = $db->func_query("SELECT * FROM inv_manufacturer");
foreach($manufacturers as $row)
{
	if($row['manufacturer_id'])
	{
		$keyword = strtolower($row['name']);
		$keyword = changeImageName($keyword);
		$db->func_query("DELETE FROM oc_url_alias WHERE query='catalog_manufacturer_id=".$row['manufacturer_id']."'");
		$db->func_query("INSERT INTO oc_url_alias SET query='catalog_manufacturer_id=".$row['manufacturer_id']."' , keyword='".$keyword."'");	

		$rows2 = $db->func_query("SELECT * FROM inv_model_mt WHERE manufacturer_id='".$row['manufacturer_id']."'");

		foreach($rows2 as $row2)
		{
			$keyword = strtolower($row2['device']);
			$keyword = changeImageName($keyword);
		$db->func_query("DELETE FROM oc_url_alias WHERE query='catalog_model_id=".$row2['model_id']."'");
		$db->func_query("INSERT INTO oc_url_alias SET query='catalog_model_id=".$row2['model_id']."' , keyword='".$keyword."'");
		}	
	}
}
echo 1;
?>