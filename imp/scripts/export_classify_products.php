<?php
ini_set("memory_limit",-1);
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$products = $db->func_query("SELECT a.product_id, a.sku ,b.device_product_id,a.price FROM oc_product a, inv_device_product b where a.sku=b.sku and a.status=1");
$filename = 'classify.csv';
$fp = fopen($filename, "w");
$headers = array("SKU", "ItemName", "P1 Price","Class","Manufacturer","Model","Sub Model","Attribs");
fputcsv($fp, $headers,',');

foreach($products as $product)
{
	$row = array();
	$product_name = $db->func_query_first_cell("SELECT name FROM oc_product_description WHERE product_id='".$product['product_id']."'");
	$price = $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE product_id='".$product['product_id']."' and customer_group_id='1633' and quantity=1");
	$class = $db->func_query_first_cell("SELECT a.name FROM inv_classification a, inv_device_class b WHERE a.id=b.class_id and b.device_product_id='".$product['device_product_id']."'");
	
	$_manufacturers = $db->func_query("SELECT a.manufacturer_id,b.name,a.device_manufacturer_id FROM inv_device_manufacturer a,inv_manufacturer b WHERE a.manufacturer_id=b.manufacturer_id AND a.device_product_id='".$product['device_product_id']."'");
	$manufacturers = array();
	$manufacturer_ids = array();
	foreach($_manufacturers as $manufacturer)
	{
		$manufacturers[] = $manufacturer['name'];
		$manufacturer_ids[] = $manufacturer['device_manufacturer_id'];
	}
	$manufacturer_ids = implode(",", $manufacturer_ids);
	if($manufacturer_ids)
	{
		$_models = $db->func_query("select a.model_id, a.device,b.* from inv_model_mt a, inv_device_device b  WHERE a.model_id=b.device_id and  b.device_manufacturer_id in ($manufacturer_ids)");
		$model_name = array();
		$model_id = array();
		foreach($_models as $_model)
		{
			$model_name[] = $_model['device'];
			$model_id[] = $_model['model_id'];
		}
		$model = implode(",", $model_name);	
		$model_ids = implode(",", $model_id);

		if($model_ids)
		{
			$model_ids_a = array();
			$device_model_ids = array();
			foreach ($_manufacturers as $_xx) {
				

                    $_recs = $db->func_query("SELECT
                        distinct mo.model_id,
                        mo.device_model_id
                        FROM
                        `inv_device_manufacturer` m
                        INNER JOIN `inv_device_device` d
                        ON (m.`device_manufacturer_id` = d.`device_manufacturer_id`)
                        INNER JOIN `inv_device_model` mo
                        ON (d.`device_device_id` = mo.`device_device_id`)

                        WHERE m.manufacturer_id='" . $_xx['manufacturer_id'] . "' AND m.device_product_id='" . $product['device_product_id'] . "' AND d.device_id in ($model_ids)");
                    if($_recs){
                    	
                    }
                    
                    foreach ($_recs as $_rec) {

                        $model_ids_a[] = $_rec['model_id'];
                        $device_model_ids[] = $_rec['device_model_id'];
                    }
                }



			$sub_model_query = $db->func_query("SELECT
                mc.`id`,d.`sub_model`,d.`model_id`,d.`sub_model_id`,mc.`carrier_id`,c.`name`
                FROM
                `inv_model_dt` d
                LEFT JOIN `inv_model_carrier` mc
                ON (d.`sub_model_id` = mc.`sub_model_id`)
                LEFT JOIN `inv_carrier` c
                ON (mc.`carrier_id` = c.`id`)

                WHERE d.model_id in(" . $model_ids . ")
                ");
			$sub_model = array();
			foreach($sub_model_query as $sub)
			{
				if(in_array($sub['id'], $model_ids_a))
				{

				$sub_model[] = $sub['sub_model']. ' ('.$sub['name'].') ';
				}
			}
			$sub_model = implode(",", $sub_model);


			$attribs = array();
			if($device_model_ids)
			{
				

			$attrib_query = $db->func_query("SELECT distinct
               a.name,c.name as group_name from inv_attr a, inv_device_attrib b,inv_attribute_group c where a.id = b.attrib_id and c.id=a.attribute_group_id and  b.device_model_id in (".implode(",", $device_model_ids).")
                ");
			foreach($attrib_query as $attrib)
			{
				

				$attribs[$attrib['group_name']] = $attrib['name'];
				
			}
			// $attribs = implode(",", $attribs);
			$attribs = json_encode($attribs);
		}
			if($attribs)
			{
				// echo "SELECT
    //            a.name from inv_attr a, inv_device_attrib b where a.id = b.attrib_id and  b.device_model_id in (".$model_ids.")
    //             ";exit;
				// echo $attribs;exit;
			}
		}
	}

	$manufacturers = implode(",", $manufacturers);
	if(!$price)
	{
		$price = $product['price'];
	}
	$product_name = str_replace(",", " ", $product_name);
	$row = array($product['sku'],$product_name,round($price,2),$class,$manufacturers,$model,$sub_model,$attribs);
	fputcsv($fp, $row,',');
}
fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
readfile($filename);
@unlink($filename);


?>