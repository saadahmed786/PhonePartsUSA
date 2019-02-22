<?php

include 'config.php';
include_once 'inc/functions.php';
include_once 'product_catalog/load_catalog2.php';

$vpo_id = (int) $_GET['vpo_id'];
$vendor_po_id = $db->func_query_first_cell("SELECT vendor_po_id FROM inv_vendor_po WHERE id = '$vpo_id'");
$exchange_rate = $db->func_query_first_cell("SELECT ex_rate FROM inv_vendor_po WHERE id='".$vpo_id."'");
if(!$exchange_rate)
{
	$exchange_rate = 1.00;
}
$_query = "SELECT DISTINCT sku,new_cost FROM `inv_vendor_po_items` where vendor_po_id='".$vendor_po_id."'";
$products = $db->func_query($_query);

$headers = array("SKU","Title","Quantity","30 Days Item Sale","30 Days Item Return","Raw Cost","Ex Rate","Shipping Fee","True Cost","New Cost","D1","P1","Sale Price","MS","FZ","MG","MD","ETS","MC","LL","P4C","CPH");

$filename = $vendor_po_id."-Competitor-prices.csv";
$fp = fopen($filename, "w");
fputcsv($fp, $headers,',');

	if ($products) {
			
		foreach($products as $product){

			$costs = $catalog->getTrueCostRow($product['sku']);
			$detail = $db->func_query_first("Select p.product_id,p.sku, p.price,p.sale_price,p.quantity, pd.name from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) where sku='".$product['sku']."' ");
			$product['product_id'] = $detail['product_id'];
			$product['price'] = $detail['price'];
			$product['name'] = $detail['name'];
			$product['sale_price'] = $detail['sale_price'];
			$product['quantity'] = $detail['quantity'];
			$scrapping_sites = array('mobile_sentrix', 'fixez', 'mengtor', 'mobile_defenders','etrade_supply','maya_cellular','lcd_loop','parts_4_cells','cell_parts_hub');
			$price_values = array();
			foreach ($scrapping_sites as $site) {

				$price = $db->func_query_first("select price from inv_product_price_scrap_history ph where ph.sku = '" . $product['sku'] . "' AND ph.type = '$site' order by ph.added DESC limit 1");

				$price_values[] = $price;

			}
			$p1_price = $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=1633 AND product_id='" . (int) $product['product_id'] . "' AND quantity=1");
			$last_30_days_sale = getLast30DaysItemSale($product['sku']);
			$last_30_days_return = getLast30DaysItemReturns($product['sku']);

			$rowData = array($product['sku'] , $product['name'],$product['quantity'],$last_30_days_sale,$last_30_days_return, $costs['raw_cost'],$costs['ex_rate'],$costs['shipping_fee'],$costs['true_cost'],round($product['new_cost'],2),$product['price'],$p1_price,$product['sale_price'],$price_values[0]['price'],$price_values[1]['price'],$price_values[2]['price'],$price_values[3]['price'],$price_values[4]['price'],$price_values[5]['price'],$price_values[6]['price'],$price_values[7]['price'],$price_values[8]['price']);

				fputcsv($fp , $rowData,',');
		}
	}
	fclose($fp);
	header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filename);
@unlink($filename);