<?php

include 'auth.php';

/*$_query = "Select p.product_id,p.sku, p.quantity, p.price,p.retail_price ,  pd.name , pc.raw_cost , pc.current_cost , pc.prev_cost , pc.cost_date, pc.ex_rate , pc.shipping_fee
		   from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id)
		   left join (select * from inv_product_costs order by id DESC) pc on (p.sku = pc.sku) where p.is_main_sku = 1 GROUP BY pc.sku";*/
 $_query = "Select p.product_id,p.sku, p.quantity, p.price,p.retail_price ,  pd.name 
		   from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id)
		    where p.is_main_sku = 1";
$products = $db->func_query($_query);

$filename = "products-".date("Y-m-d").".csv";
$fp = fopen($filename,"w");

if($_SESSION['display_cost']){
	$headers = array("SKU","ItemName","Qty","Price Date","Raw Cost","Raw Cost(USD)","Exchange Rate","Shipping Fee","True Cost","Avg. Cost","Retail Price","Default Qty 1","Default Qty 3","Default Qty 10","Local Qty 1","Local Qty 3","Local Qty 10","WS Qty 1","WS Qty 3","WS Qty 10","Grade A","Grade B","Grade C");
}
else{
	$headers = array("SKU","ItemName","Qty","Default Qty 1","Default Qty 3","Default Qty 10","Local Qty 1","Local Qty 3","Local Qty 10","WS Qty 1","WS Qty 3","WS Qty 10","Gadget Fix URL","Gadget Fix Price","Ebay URL","Ebay Price");
}

fputcsv($fp , $headers);

foreach($products as $product){
	
	$cost_query = $db->func_query_first("select * from inv_product_costs WHERE sku='".$product['sku']."' ORDER BY id DESC limit 1");
	$product['raw_cost'] = $cost_query['raw_cost'];
	$product['current_cost'] = $cost_query['current_cost'];
	$product['prev_cost'] = $cost_query['prev_cost'];
	$product['cost_date'] = $cost_query['cost_date'];
	$product['ex_rate'] = $cost_query['ex_rate'];
	$product['shipping_fee'] = $cost_query['shipping_fee'];
	
	$downgrade_data = $db->func_query("select sku , item_grade , price from oc_product where main_sku = '".$product['sku']."'","item_grade");
	
	if($product['ex_rate'] == 0){
		$product['ex_rate'] = 1;
	}
	
$Default1 = number_format((float)$db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=8 AND product_id='".(int)$product['product_id']."' AND quantity=1"),2);
$Default3 = number_format((float)$db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=8 AND product_id='".(int)$product['product_id']."' AND quantity=3"),2);
$Default10 = number_format((float)$db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=8 AND product_id='".(int)$product['product_id']."' AND quantity=10"),2);

$Local1 = number_format((float)$db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=10 AND product_id='".(int)$product['product_id']."' AND quantity=1"),2);
$Local3 = number_format((float)$db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=10 AND product_id='".(int)$product['product_id']."' AND quantity=3"),2);
$Local10 = number_format((float)$db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=10 AND product_id='".(int)$product['product_id']."' AND quantity=10"),2);

$Wholesale1 = number_format((float)$db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=6 AND product_id='".(int)$product['product_id']."' AND quantity=1"),2);
$Wholesale3 = number_format((float)$db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=6 AND product_id='".(int)$product['product_id']."' AND quantity=3"),2);
$Wholesale10 = number_format((float)$db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=6 AND product_id='".(int)$product['product_id']."' AND quantity=10"),2);

	if($_SESSION['display_cost']){
		$cost = number_format(($product['raw_cost']+$product['shipping_fee']) / $product['ex_rate'] ,2);
		/*if($downgrade_data){ commented by zaman
			$rowData = array($product['sku'] , $product['name'] , $product['quantity'] , $product['raw_cost'] , $product['current_cost'], $product['shipping_fee'], $cost, $downgrade_data['Grade A']['sku'],$downgrade_data['Grade B']['sku'],$downgrade_data['Grade C']['sku']);
		}
		else{*/
		
		$gadgetfix_price = $db->func_query_first("SELECT url,price FROM inv_product_scrape_prices WHERE sku='".$product['sku']."' AND scrape_site='gadgetfix'");
		$ebay_price = $db->func_query_first("SELECT url,price FROM inv_product_scrape_prices WHERE sku='".$product['sku']."' AND scrape_site='gadgetfix'");
		
		$avg_cost_rows = $db->func_query("SELECT * FROM inv_product_costs WHERE sku='".$product['sku']."' ORDER BY cost_date DESC LIMIT 3");
		$avg_cost = 0.00;
		$avg_count = 1;
		foreach($avg_cost_rows as $avg_cost_row)
		{
		$avg_cost+=($avg_cost_row['raw_cost']+$avg_cost_row['shipping_fee']) / $avg_cost_row['ex_rate'];
		
		$avg_count++;
		}
		if($avg_count>1)
		{
		$avg_cost = $avg_cost/($avg_count-1);
		//$avg_cost = $avg_count-1;
		}
		
		
			$rowData = array($product['sku'] , $product['name'] , $product['quantity'],($product['cost_date']?date('m/d/Y',strtotime($product['cost_date'])):'') , $product['raw_cost'] , $product['current_cost'],$product['ex_rate'], $product['shipping_fee'], $cost,number_format($avg_cost,2),$product['retail_price'],$Default1,$Default3,$Default10,$Local1,$Local3,$Local10,$Wholesale1,$Wholesale3,$Wholesale10,$downgrade_data['Grade A']['price'],$downgrade_data['Grade B']['price'],$downgrade_data['Grade C']['price'],$gadgetfix_price['url'],$gadgetfix_price['price'],$gadgetfix_price['url'],$ebay_price['price']);
			
		//}
	}
	else{
		/*if($downgrade_data){ commented by zaman
			$rowData = array($product['sku'] , $product['name'] , $product['quantity'] , $downgrade_data['Grade A']['sku'], $downgrade_data['Grade B']['sku'], $downgrade_data['Grade C']['sku']);
		}
		else{*/
			$rowData = array($product['sku'] , $product['name'] , $product['quantity'],$Default1,$Default3,$Default10,$Local1,$Local3,$Local10,$Wholesale1,$Wholesale3,$Wholesale10);
		//}
	}

	fputcsv($fp , $rowData);
}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);