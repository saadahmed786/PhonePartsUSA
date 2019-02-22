<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 1200); //300 seconds = 5 minutes
require_once("../auth.php");
//require_once("../inc/functions.php");

$products = $db->func_query("SELECT a.product_id,a.sku FROM oc_product a,oc_product_discount b WHERE a.product_id=b.product_id and   b.customer_group_id=8 AND b.quantity=1 and b.price>0 and a.status=1 ");
foreach($products as $product)
{
	updateProductPrice($product['sku']);
	}



	function updateProductPrice($sku,$raw_cost='',$ex_rate='',$shipping_fee='')
{
	global $db;
	$product = $db->func_query_first("SELECT product_id,sku FROM oc_product WHERE model='".$sku."'");

	if($raw_cost)
	{
		$cost['raw_cost'] =$raw_cost;
		$cost['ex_rate'] =$ex_rate;
		$cost['shipping_fee'] =$shipping_fee;

	}
	else
	{
		$cost = $db->func_query_first("SELECT  cost_date,raw_cost,ex_rate,shipping_fee FROM inv_product_costs WHERE sku='" . $product['sku'] . "' ORDER BY id DESC limit 1");		
	}
	$true_cost = ($cost['raw_cost'] + $cost['shipping_fee']) / $cost['ex_rate'];	


	$true_cost = round($true_cost, 2);
		if(!$true_cost) return false;
	$markup = $db->func_query_first("SELECT * FROM  inv_product_pricing WHERE  $true_cost BETWEEN COALESCE(`range_from`,$true_cost) AND COALESCE(`range_to`,$true_cost)");




	$sql = 'SELECT 
	iks.`kit_sku`, op.`price`
	FROM
	`inv_kit_skus` AS `iks`
	INNER JOIN
	`oc_product` AS `op` ON op.`sku` = iks.`kit_sku`
	WHERE
	iks.`kit_sku` = "' . $product['sku'] . 'K"
	';

	$kitSku = $db->func_query_first($sql);
                            // Setting kit sku Price if it exist;
	$kitSkuPrice = 0;
	if ($kitSku) {


		$kitSkuPrice = ($true_cost * $markup['markup_d1'])+$markup['kit_price'];

		$_temp_kit_sku = explode('.',(float)$kitSkuPrice);

		if((int)$_temp_kit_sku[1]==0)
		{
			$kitSkuPrice = $_temp_kit_sku[0].'.0000';	

		}
		else
		{

			$kitSkuPrice = $_temp_kit_sku[0].'.9500';	
		}
	}
	else
	{

		$kitSkuPrice = 0;


	}

	$markup_general = round($true_cost * $markup['markup_general'],4);
	$markup_special = round($true_cost * $markup['markup_special'],4);
	$markup_d1 = round($true_cost * $markup['markup_d1'],4);
	$markup_d3 = round($true_cost * $markup['markup_d3'],4);
	$markup_d10 = round($true_cost * $markup['markup_d10'],4);

	$markup_l1 = round($true_cost * $markup['markup_l1'],4);
	$markup_l3 = round($true_cost * $markup['markup_l3'],4);
	$markup_l10 = round($true_cost * $markup['markup_l10'],4);

	$markup_w1 = round($true_cost * $markup['markup_w1'],4);
	$markup_w3 = round($true_cost * $markup['markup_w3'],4);
	$markup_w10 = round($true_cost * $markup['markup_w10'],4);
	
	
	$markup_silver1 = round($true_cost * $markup['markup_silver1'],4);
	$markup_silver3 = round($true_cost * $markup['markup_silver3'],4);
	$markup_silver10 = round($true_cost * $markup['markup_silver10'],4);
	
	$markup_gold1 = round($true_cost * $markup['markup_gold1'],4);
	$markup_gold3 = round($true_cost * $markup['markup_gold3'],4);
	$markup_gold10 = round($true_cost * $markup['markup_gold10'],4);
	
	$markup_platinum1 = round($true_cost * $markup['markup_platinum1'],4);
	$markup_platinum3 = round($true_cost * $markup['markup_platinum3'],4);
	$markup_platinum10 = round($true_cost * $markup['markup_platinum10'],4);
	
	$markup_diamond1 = round($true_cost * $markup['markup_diamond1'],4);
	$markup_diamond3 = round($true_cost * $markup['markup_diamond3'],4);
	$markup_diamond10 = round($true_cost * $markup['markup_diamond10'],4);
	
	if(!$markup_general) return false;
	// if($true_cost ==0.00)
	// {
	// 	$markup_silver1 = $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id = '6' and quantity = '1' AND product_id='".(int)$product['product_id']."' ");
	// 	$markup_silver3 = $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id = '6' and quantity = '3' AND product_id='".(int)$product['product_id']."' ");
	// 	$markup_silver10 = $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id = '6' and quantity = '10' AND product_id='".(int)$product['product_id']."' ");


	// 	$markup_gold1 = $markup_silver1;
	// 	$markup_gold3 = $markup_silver3;
	// 	$markup_gold10 = $markup_silver10;

	// 	$markup_platinum1 = $markup_silver1;
	// 	$markup_platinum3 = $markup_silver3;
	// 	$markup_platinum10 = $markup_silver10;

	// 	$markup_diamond1 = $markup_silver1;
	// 	$markup_diamond3 = $markup_silver3;
	// 	$markup_diamond10 = $markup_silver10;
	// 		$db->db_exec("DELETE FROM oc_product_discount WHERE customer_group_id IN('1631','1632','1633','1634') AND product_id='".(int)$product['product_id']."'");
	// }


	$grade_a = round($true_cost * $markup['grade_a'],4);
	$grade_b = round($true_cost * $markup['grade_b'],4);
	$grade_c = round($true_cost * $markup['grade_c'],4);

if($true_cost and $true_cost>0)
{
	$db->db_exec("UPDATE oc_product SET price='" . (float) $markup_general . "',special_price='".(float)$markup_special."' WHERE product_id='" . (int) $product['product_id'] . "'");

	$db->db_exec("DELETE FROM oc_product_discount WHERE product_id='" . (int) $product['product_id'] . "'");

			// Default markups
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '8' , quantity = '1' , price = '" . (float) $markup_d1 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '8' , quantity = '3' , price = '" . (float) $markup_d3 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '8' , quantity = '10' , price = '" . (float) $markup_d10 . "'");

			   // Local Markups
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '10' , quantity = '1' , price = '" . (float) $markup_l1 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '10' , quantity = '3' , price = '" . (float) $markup_l3 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '10' , quantity = '10' , price = '" . (float) $markup_l10 . "'");

			   // Wholesale markups
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '6' , quantity = '1' , price = '" . (float) $markup_w1 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '6' , quantity = '3' , price = '" . (float) $markup_w3 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '6' , quantity = '10' , price = '" . (float) $markup_w10 . "'");

	
	   // Silver markups
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1631' , quantity = '1' , price = '" . (float) $markup_silver1 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1631' , quantity = '3' , price = '" . (float) $markup_silver3 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1631' , quantity = '10' , price = '" . (float) $markup_silver10 . "'");
	
	
	
	   // Gold markups
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1632' , quantity = '1' , price = '" . (float) $markup_gold1 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1632' , quantity = '3' , price = '" . (float) $markup_gold3 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1632' , quantity = '10' , price = '" . (float) $markup_gold10 . "'");
	
	
	   // Platinum markups
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1633' , quantity = '1' , price = '" . (float) $markup_platinum1 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1633' , quantity = '3' , price = '" . (float) $markup_platinum3 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1633' , quantity = '10' , price = '" . (float) $markup_platinum10 . "'");
	
	   // Diamond markups
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1634' , quantity = '1' , price = '" . (float) $markup_diamond1 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1634' , quantity = '3' , price = '" . (float) $markup_diamond3 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1634' , quantity = '10' , price = '" . (float) $markup_diamond10 . "'");
	
	
	
	
	
	
	

		// Grade A markup
	$db->db_exec('UPDATE oc_product SET  `price` = "' . (float)$grade_a . '" WHERE `main_sku` = "' . $product['sku'] . '" AND `item_grade` = "Grade A"');

		 // Grade B markup
	$db->db_exec('UPDATE oc_product SET  `price` = "' . (float)$grade_b . '" WHERE `main_sku` = "' . $product['sku'] . '" AND `item_grade` = "Grade B"');

		 // Grade C markup
	$db->db_exec('UPDATE oc_product SET  `price` = "' . (float)$grade_c . '" WHERE `main_sku` = "' . $product['sku'] . '" AND `item_grade` = "Grade C"');

	if($kitSkuPrice)
	{
		 // Kit Sku markup
		$db->db_exec('UPDATE oc_product SET  `price` = "' . (float)$kitSkuPrice . '" WHERE `sku` = "' . $product['sku'] . 'K"');
	}

	}

	
}
?>