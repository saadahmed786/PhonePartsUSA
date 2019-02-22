<?php
ini_set("memory_limit",-1);
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';

$inv_orders = array(
  'APL-003-1452',
  'APL-003-1660',
  'APL-003-1294',
  'APL-002-6438',
  'APL-002-6372',
  'APL-002-6373',
  'ACC-ZRT-474',
  'APL-003-1020',
  'APL-003-1110',
  'APL-003-1530',
  'APL-003-1434',
  'APL-003-0330',
  'APL-003-0282',
  'APL-002-1292',
  'APL-003-1494',
  'APL-003-0996',
  'APL-003-1110',
  'APL-003-1340',
  'APL-003-1338',
  'APL-003-1336',
  'BTS810014',
  'ACC-ZRT-049',
  'ACC-ZRT-048',
  'FLX-SAM-1371',
  'FLX-SAM-1350',
  'FLX-SAM-1394',
  'APL-003-1635',
  'APL-003-1633',
  'APL-003-1530',
  'APL-003-0234',
  'APL-003-0228',
  'APL-002-1292',
  'APL-003-1482',
  'APL-003-1378'
);


$filename = 'fishbowl.csv';
$handle   = fopen("$filename", "r");

		$heading  = fgetcsv($handle);
		$fb = array();

		$i = 0;
		while(!feof($handle)){
			$row = fgetcsv($handle);
			for($j=0;$j<count($heading);$j++){
				if($row[$j]){
					$fb[$i][$heading[$j]] = trim($row[$j]);
				}
			}
			$i++;
		}
		$i = 1;
		$array = array();
		foreach($fb as $fishbowl)
		{
			if(in_array(trim($fishbowl['Sku']), $inv_orders))
			{
				//echo $i.'--';
				//echo $fishbowl['Sku'];
	//				$array[] = $fishbowl['Sku'];
				//echo '--'.$fishbowl['Qty'];
				//echo "<br>";

				$db->db_exec("UPDATE oc_product SET quantity='".(int)$fishbowl['Qty']."' where model='".$fishbowl['Sku']."'");
				$i++;
			}

		}
		
		// foreach($inv_orders as $data)
		// {
			
		// 	if(in_array($data,$array ))
		// 	{
		// 		echo $data."Found <br>";
		// 	}
		// 	else
		// 	{
		// 		echo $data."Not Found<br>";
		// 	}
		// }
		echo 'success';exit;
		exit;
?>