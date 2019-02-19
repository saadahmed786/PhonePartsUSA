<?php

include 'auth.php';

/* $_query = "Select p.product_id,p.sku, p.quantity, p.price,p.retail_price ,  pd.name , pc.raw_cost , pc.current_cost , pc.prev_cost , pc.cost_date, pc.ex_rate , pc.shipping_fee
  from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id)
  left join (select * from inv_product_costs order by id DESC) pc on (p.sku = pc.sku) where p.is_main_sku = 1 GROUP BY pc.sku"; */
$_query = "Select p.product_id,p.sku, p.quantity, p.price,p.retail_price ,  pd.name ,p.is_kit,p.vendor,p.classification_id,p.sub_classification_id,p.status
		   from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id)
		    where p.is_main_sku = 1";
$products = $db->func_query($_query);

$filename = "products-" . date("Y-m-d") . ".csv";
$fp = fopen($filename, "w");

if ($_SESSION['display_cost']) {
    //With sub class
    //$headers = array("SKU","ItemName","Qty","Vendor","Price Date","Raw Cost","Raw Cost(USD)","Exchange Rate","Shipping Fee","True Cost","Avg. Cost","Price","Retail Price","Class","Sub Class","Default Qty 1","Default Qty 3","Default Qty 10","Local Qty 1","Local Qty 3","Local Qty 10","WS Qty 1","WS Qty 3","WS Qty 10","Grade A","Grade B","Grade C","Kit SKU","Kit Price","Gadget Fix URL","Gadget Fix Price","Ebay URL","Ebay Price","Ebay 2 URL","Ebay 2 Price","Ebay 3 URL","Ebay 3 Price","Ebay 4 URL","Ebay 4 Price","Mengtor URL","Mengtor Price","Mobile Defenders URL","Mobile Defenders Price","Status","Manufacturer","Device","Model / Sub-Model","Attributes");
    //Without sub class
    $headers = array("SKU", "ItemName", "Qty", "Vendor", "Price Date", "Raw Cost", "Raw Cost(USD)", "Exchange Rate", "Shipping Fee", "True Cost", "Avg. Cost", "Price", "Retail Price", "Class", "Default Qty 1", "Default Qty 3", "Default Qty 10", "Local Qty 1", "Local Qty 3", "Local Qty 10", "WS Qty 1", "WS Qty 3", "WS Qty 10", "Grade A", "Grade B", "Grade C", "Kit SKU", "Kit Price", "Gadget Fix URL", "Gadget Fix Price", "Ebay URL", "Ebay Price", "Ebay 2 URL", "Ebay 2 Price", "Ebay 3 URL", "Ebay 3 Price", "Ebay 4 URL", "Ebay 4 Price", "Mengtor URL", "Mengtor Price", "Mobile Defenders URL", "Mobile Defenders Price", "Status", "Manufacturer", "Device", "Model / Sub-Model", "Attributes");
} else {
    //With sub class
    //$headers = array("SKU","ItemName","Qty","Vendor","Class","Sub Class","Default Qty 1","Default Qty 3","Default Qty 10","Local Qty 1","Local Qty 3","Local Qty 10","WS Qty 1","WS Qty 3","WS Qty 10","Status");
    //Without sub class
    $headers = array("SKU", "ItemName", "Qty", "Vendor", "Class", "Default Qty 1", "Default Qty 3", "Default Qty 10", "Local Qty 1", "Local Qty 3", "Local Qty 10", "WS Qty 1", "WS Qty 3", "WS Qty 10", "Status");
}

fputcsv($fp, $headers);

foreach ($products as $product) {

    $cost_query = $db->func_query_first("select * from inv_product_costs WHERE sku='" . $product['sku'] . "' ORDER BY id DESC limit 1");
    $product['raw_cost'] = $cost_query['raw_cost'];
    $product['current_cost'] = $cost_query['current_cost'];
    $product['prev_cost'] = $cost_query['prev_cost'];
    $product['cost_date'] = $cost_query['cost_date'];
    $product['ex_rate'] = $cost_query['ex_rate'];
    $product['shipping_fee'] = $cost_query['shipping_fee'];

    $device_product_id = $db->func_query_first_cell("SELECT device_product_id as id FROM inv_device_product WHERE sku='" . $product['sku'] . "'");


    $manufacturer = array();
    $device = array();
    $model = array();
    $attribs = array();
    if ($device_product_id['id']) {
        $_temps = $db->func_query("SELECT DISTINCT manufacturer_id,device_manufacturer_id FROM inv_device_manufacturer WHERE device_product_id='" . $device_product_id['id'] . "'");
        foreach ($_temps as $_temp) {
            $manufacturer[] = $db->func_query_first_cell("SELECT name FROM inv_manufacturer WHERE manufacturer_id='" . $_temp['manufacturer_id'] . "'");

            $_xx = $db->func_query("SELECT DISTINCT
				b.device_id
FROM
    `inv_device_manufacturer` a
    INNER JOIN `inv_device_device` b 
        ON (a.`device_manufacturer_id` = b.`device_manufacturer_id`)
		
		WHERE a.device_product_id='" . $device_product_id['id'] . "' AND a.manufacturer_id='" . $_temp['manufacturer_id'] . "'
		");
            foreach ($_xx as $_x) {

                $device[] = $db->func_query_first_cell("SELECT device FROM inv_model_mt WHERE model_id='" . $_x['device_id'] . "'");


                $_xxx = $db->func_query("SELECT DISTINCT 
mc.`id`,d.`sub_model`,d.`model_id`,d.`sub_model_id`,mc.`carrier_id`,c.`name`
FROM
    `inv_model_dt` d
    INNER JOIN `inv_model_carrier` mc
        ON (d.`sub_model_id` = mc.`sub_model_id`)
    INNER JOIN `inv_carrier` c
        ON (mc.`carrier_id` = c.`id`)
		
		WHERE d.model_id =" . $_x['device_id'] . "
		");

                foreach ($_xxx as $_xxxx) {
                    $model[] = $_xxxx['sub_model'] . ' (' . $_xxxx['name'] . ')';

                    $_aa = $db->func_query("SELECT DISTINCT attrib_id FROM inv_device_attrib WHERE device_model_id='" . $_xxxx['model_id'] . "'");

                    foreach ($_aa as $_a) {
                        $attribs[] = $db->func_query_first_cell("SELECT name FROM inv_attr WHERE id='" . $_a['attrib_id'] . "'");
                    } // END ATTRIB loop
                } // end model loop
            } // end device loop
        } // end manufacturer loop
    } // end device if
    $attribs = array_unique($attribs);


    $downgrade_data = $db->func_query("select sku , item_grade , price from oc_product where main_sku = '" . $product['sku'] . "'", "item_grade");
    if ($product['is_kit'] == 0) {
        $kit_data = $db->func_query_first("SELECT sku,price FROM oc_product WHERE is_kit=1 AND sku='" . $product['sku'] . "K'");
    }
    if ($product['ex_rate'] == 0) {
        $product['ex_rate'] = 1;
    }

    $class = $db->func_query_first_cell("SELECT name FROM inv_classification WHERE id='" . (int) $product['classification_id'] . "'");
    $sub_class = $db->func_query_first_cell("SELECT name FROM inv_sub_classification WHERE id='" . (int) $product['sub_classification_id'] . "'");

    $Default1 = number_format((float) $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=8 AND product_id='" . (int) $product['product_id'] . "' AND quantity=1"), 2);
    $Default3 = number_format((float) $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=8 AND product_id='" . (int) $product['product_id'] . "' AND quantity=3"), 2);
    $Default10 = number_format((float) $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=8 AND product_id='" . (int) $product['product_id'] . "' AND quantity=10"), 2);

    $Local1 = number_format((float) $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=10 AND product_id='" . (int) $product['product_id'] . "' AND quantity=1"), 2);
    $Local3 = number_format((float) $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=10 AND product_id='" . (int) $product['product_id'] . "' AND quantity=3"), 2);
    $Local10 = number_format((float) $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=10 AND product_id='" . (int) $product['product_id'] . "' AND quantity=10"), 2);

    $Wholesale1 = number_format((float) $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=6 AND product_id='" . (int) $product['product_id'] . "' AND quantity=1"), 2);
    $Wholesale3 = number_format((float) $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=6 AND product_id='" . (int) $product['product_id'] . "' AND quantity=3"), 2);
    $Wholesale10 = number_format((float) $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE customer_group_id=6 AND product_id='" . (int) $product['product_id'] . "' AND quantity=10"), 2);

    if ($_SESSION['display_cost']) {
        $cost = number_format(($product['raw_cost'] + $product['shipping_fee']) / $product['ex_rate'], 2);
        /* if($downgrade_data){ commented by zaman
          $rowData = array($product['sku'] , $product['name'] , $product['quantity'] , $product['raw_cost'] , $product['current_cost'], $product['shipping_fee'], $cost, $downgrade_data['Grade A']['sku'],$downgrade_data['Grade B']['sku'],$downgrade_data['Grade C']['sku']);
          }
          else{ */

        $scrape_prices = $db->func_query("SELECT url,price,scrape_site FROM inv_product_scrape_prices WHERE sku='" . $product['sku'] . "'");
        $s_price = array();
        if ($scrape_prices) {
            foreach ($scrape_prices as $scrape_price) {
                $s_price[$scrape_price['scrape_site']] = array(
                    'url' => $scrape_price['url'],
                    'price' => $scrape_price['price']);
            }
        }

        $avg_cost_rows = $db->func_query("SELECT * FROM inv_product_costs WHERE sku='" . $product['sku'] . "' ORDER BY cost_date DESC LIMIT 3");
        $avg_cost = 0.00;
        $avg_count = 1;
        foreach ($avg_cost_rows as $avg_cost_row) {
            $avg_cost+=($avg_cost_row['raw_cost'] + $avg_cost_row['shipping_fee']) / $avg_cost_row['ex_rate'];

            $avg_count++;
        }
        if ($avg_count > 1) {
            $avg_cost = $avg_cost / ($avg_count - 1);
            //$avg_cost = $avg_count-1;
        }

        //With sub class
        //$rowData = array($product['sku'], $product['name'], $product['quantity'], $product['vendor'], ($product['cost_date'] ? date('m/d/Y', strtotime($product['cost_date'])) : ''), $product['raw_cost'], $product['current_cost'], $product['ex_rate'], $product['shipping_fee'], $cost, number_format($avg_cost, 2), $product['price'], $product['retail_price'], $class, $sub_class, $Default1, $Default3, $Default10, $Local1, $Local3, $Local10, $Wholesale1, $Wholesale3, $Wholesale10, $downgrade_data['Grade A']['price'], $downgrade_data['Grade B']['price'], $downgrade_data['Grade C']['price'], ($kit_data ? $kit_data['sku'] : ''), ($kit_data ? $kit_data['price'] : ''), $s_price['gadgetfix']['url'], $s_price['gadgetfix']['price'], $s_price['ebay']['url'], $s_price['ebay']['price'], $s_price['ebay_2']['url'], $s_price['ebay_2']['price'], $s_price['ebay_3']['url'], $s_price['ebay_3']['price'], $s_price['ebay_4']['url'], $s_price['ebay_4']['price'], $s_price['mengtor']['url'], $s_price['mengtor']['price'], $s_price['mobile_defenders']['url'], $s_price['mobile_defenders']['price'], ($product['status'] == 0 ? 'Disable' : 'Enable'), implode(",", $manufacturer), implode(",", $device), implode(",", $model), implode(",", $attribs));
        //Without sub class
        $rowData = array($product['sku'], $product['name'], $product['quantity'], $product['vendor'], ($product['cost_date'] ? date('m/d/Y', strtotime($product['cost_date'])) : ''), $product['raw_cost'], $product['current_cost'], $product['ex_rate'], $product['shipping_fee'], $cost, number_format($avg_cost, 2), $product['price'], $product['retail_price'], $class, $Default1, $Default3, $Default10, $Local1, $Local3, $Local10, $Wholesale1, $Wholesale3, $Wholesale10, $downgrade_data['Grade A']['price'], $downgrade_data['Grade B']['price'], $downgrade_data['Grade C']['price'], ($kit_data ? $kit_data['sku'] : ''), ($kit_data ? $kit_data['price'] : ''), $s_price['gadgetfix']['url'], $s_price['gadgetfix']['price'], $s_price['ebay']['url'], $s_price['ebay']['price'], $s_price['ebay_2']['url'], $s_price['ebay_2']['price'], $s_price['ebay_3']['url'], $s_price['ebay_3']['price'], $s_price['ebay_4']['url'], $s_price['ebay_4']['price'], $s_price['mengtor']['url'], $s_price['mengtor']['price'], $s_price['mobile_defenders']['url'], $s_price['mobile_defenders']['price'], ($product['status'] == 0 ? 'Disable' : 'Enable'), implode(",", $manufacturer), implode(",", $device), implode(",", $model), implode(",", $attribs));

        //}
    } else {
        /* if($downgrade_data){ commented by zaman
          $rowData = array($product['sku'] , $product['name'] , $product['quantity'] , $downgrade_data['Grade A']['sku'], $downgrade_data['Grade B']['sku'], $downgrade_data['Grade C']['sku']);
          }
          else{ */
        //With sub class
        //$rowData = array($product['sku'], $product['name'], $product['quantity'], $product['vendor'], $class, $sub_class, $Default1, $Default3, $Default10, $Local1, $Local3, $Local10, $Wholesale1, $Wholesale3, $Wholesale10, ($product['status'] == 0 ? 'Disable' : 'Enable'));
        //Without sub class
        $rowData = array($product['sku'], $product['name'], $product['quantity'], $product['vendor'], $class, $Default1, $Default3, $Default10, $Local1, $Local3, $Local10, $Wholesale1, $Wholesale3, $Wholesale10, ($product['status'] == 0 ? 'Disable' : 'Enable'));
        //}
    }

    fputcsv($fp, $rowData);
}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filename);
@unlink($filename);
