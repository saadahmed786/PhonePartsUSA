<?php
ini_set("memory_limit",-1);
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$filename = 'customer_update.csv';
		$handle   = fopen("$filename", "r");

		$heading  = fgetcsv($handle);
		$customers = array();

		$i = 0;
		while(!feof($handle)){
			$row = fgetcsv($handle);
			for($j=0;$j<count($heading);$j++){
				if($row[$j]){
					$customers[$i][$heading[$j]] = trim($row[$j]);
				}
			}
			$i++;
		}
		$tier = array();
		$tier['Default'] = 8;
		$tier['Local'] = 10;
		$tier['Bronze'] = 6;
		$tier['Silver'] = 1631;
		$tier['Gold'] = 1632;
		$tier['Platinum'] = 1633;
		$tier['Diamond'] = 1634;

		$i = 0;
		foreach($customers as $customer)
		{
			$email = trim(strtolower($customer['Email']));
			if($email==''){
				continue;
			}
			$customer_group = $customer['Tier'];
			$customer_group_id = $tier[$customer_group];
			if($customer_group_id==''){
				$customer_group_id = 8;
			}
			$tax_exempt = ($customer['Tax']=='Yes'?1:0);

			$query = "UPDATE oc_customer SET customer_group_id='".(int)$customer_group_id."',dis_tax='".(int)$tax_exempt."' WHERE LOWER(TRIM(email))='".$email."'";
			$query_2 = "UPDATE inv_customers SET customer_group='".$db->func_escape_string($customer_group)."',dis_tax='".(int)$tax_exempt."' WHERE LOWER(TRIM(email))='".$email."'";
			$db->db_exec($query);
			$db->db_exec($query_2);

			if($i==1)
			{
			// echo $query."<br>";
			// echo $query_2."<br>";
			// exit;
		}
			$i++;	
		}
		echo 'success';
		// print_r($available);
?>