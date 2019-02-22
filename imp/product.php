<?php
// print_r($_GET);exit;
if (isset($_POST['checkYoutubeId'])) {
	if ($_POST['checkYoutubeId']) {
		include_once 'config.php';
		include_once 'inc/functions.php';
		if (!checkYoutubeId($_POST['checkYoutubeId'])) {
			$json['error'] = 1;
			$json['msg'] = 'Invalid Youtube ID';
		} else {
			$json['success'] = 1;
			$json['msg'] = 'Youtube ID is Valid';
		}
		echo json_encode($json);
		exit;
	}
}

require_once("auth.php");
include_once 'inc/functions.php';
include_once 'inc/shopify.php';

if(isset($_GET['debug']) && $_GET['debug']=1)
{
	$_SESSION['product_qty_update_tmp'] =1;
	// echo 'here';exit;
}

if(isset($_GET['debug']) && $_GET['debug']=0)
{
	unset($_SESSION['product_qty_update_tmp']);
}

if(isset($_POST['action']) and $_POST['action']=='load_ledger')
{
	$_sku = $db->func_escape_string($_POST['sku']);
	$ledger_filter = $db->func_escape_string($_POST['fiter']);
	$ledger_where = ' 1=1 ';
	switch($ledger_filter)
	{
		case 'picked':
			$ledger_where = "o.description='Marked as Picked.'";
		break;
		case 'packed':
			$ledger_where = "o.description='Marked as Packed.'";
		break;
		case 'shipped':
			$ledger_where = "o.description='Order has been Shipped.'";
		break;
		case 'shipment':
			$ledger_where = "o.description='Shipment QC &rarr; Completed'";
		break;



		case 'adjustment':
			$ledger_where = "o.description in ('Quantity has rolled back.', 'Stock Adjustment has been made.','Stock Adjustment (Cycle Count).','Stock Adjustment (Add).','Stock Adjustment (Remove).','Stock Adjustment (RTV).','Bulk Cycle Count') ";
		break;

	}
	// echo $ledger_filter.'--'.$ledger_where;exit;
	// echo "SELECT distinct t1.order_id FROM ( SELECT o.* FROM inv_product_ledger o WHERE trim(lower(o.sku))='".trim(strtolower($_sku))."' ORDER BY o.date_added DESC ) as t1 where date_added BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND NOW() and $ledger_where   order by date_added desc limit 200";exit;

	// $journals = $db->func_query("SELECT distinct t1.order_id FROM ( SELECT o.* FROM inv_product_ledger o WHERE trim(lower(o.sku))='".trim(strtolower($_sku))."' ORDER BY o.date_added DESC ) as t1 where date_added BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND NOW() and $ledger_where   order by date_added desc limit 200");
	// echo "SELECT distinct o.order_id FROM inv_product_ledger o WHERE trim(lower(o.sku))='".trim(strtolower($_sku))."' and o.date_added BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND NOW() and $ledger_where order by o.date_added desc limit 200";exit;
	$journals1 = $db->func_query("SELECT distinct o.order_id,o.date_added FROM inv_product_ledger o WHERE trim(lower(o.sku))='".trim(strtolower($_sku))."' and o.date_added BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND NOW() and o.order_id<>'' and $ledger_where  group by o.order_id order by o.date_added desc limit 200");

	$journals2 = $db->func_query("SELECT o.order_id,o.date_added FROM inv_product_ledger o WHERE trim(lower(o.sku))='".trim(strtolower($_sku))."' and o.date_added BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND NOW() and o.order_id='' and $ledger_where order by o.date_added desc limit 200");
	$journals = array_merge($journals1,$journals2);
	// print_r($journals);exit;

	// sorting 
	$date = array();
foreach ($journals as $key => $row)
{
    $date[$key] = $row['date_added'];
}
array_multisort($date, SORT_DESC, $journals);

	// end sorting
	$counter = 0;
					$journal = array();
					foreach($journals as $key => $jour)
					{
						if($jour['order_id']!='')
						{
							$histories = $db->func_query("SELECT * FROM inv_product_ledger WHERE order_id='".$jour['order_id']."' AND  trim(lower(sku))='".strtolower(trim($_sku))."'  ORDER BY date_added DESC");
							
						}
						else
						{
							$histories = $db->func_query("SELECT * FROM inv_product_ledger WHERE order_id='' AND  trim(lower(sku))='".strtolower(trim($_sku))."'  ORDER BY date_added DESC limit ".$counter.",1");
							$counter++;
						}
							$data = array();
							
							foreach($histories as $j => $history)
							{
								
									$data[$j]=$history;

									if($j==0)
								{
									$initial_qty = $history['quantity'];
									$int_index = $j;
									$data[$int_index]['rowspan']=1;
								}
								else
								{
									if($history['quantity']==$initial_qty)
									{
										
										$data[$int_index]['rowspan']+=1;
										// $data[$j]['rowspan']=1;
									}
									else
									{
										$initial_qty = $history['quantity'];
										$int_index = $j;
										$data[$int_index]['rowspan']=1;
									}
								}


							}

							$journal[$key]['order_id'] = $jour['order_id'];
							$journal[$key]['data'] = $data;

					}

					$loop = 0;
					$html='';
				foreach($journal as $i=> $journ)
				{
					if($loop==0 and $journ['order_id']!='')
					{
						$journ['not_picked'] = $inv_data['not_picked'];
						$journ['picked'] = $inv_data['picked'];
						$journ['packed'] = $inv_data['packed'];
					}
					$allocated_qty =  (int)$journ['not_picked'] + (int)$journ['picked'] + (int)$journ['packed'] ;
					$allocated_qty = (int)$allocated_qty - (int)$journ['on_hold'];
					
					
					$html.='<tr>';
					
					
					 $order_id = $journ['order_id'];
					

					$html.='<td '.  (count($journ['data'])>0?' rowspan="'.count($journ['data']).'"':'') .'>';
					
						if($order_id){
					
					$html.='<span class="tag '.($journ['action']=='rollback'?'red-bg':'blue-bg').'">'.(trim(substr($journ['data'][0]['description'], 0,8))=='Shipment'?linkToShipment($order_id,$host_path,'Shipment # '.$order_id):linkToOrder($order_id,$host_path)) .'</span>';
					
				}
				
						
					$html.='</td>';
						$kk = 0;
						// print_r($journ['data']);exit;
						foreach($journ['data'] as $_data)
						{

					
					
					$html.='<td>'.date('m/d/Y h:i:sa',strtotime($_data['date_added'])).'</td>
					
					
					<td>'.($_data['description']).($_data['notes']?'<br><i>'.$_data['notes'].'</i>':'').'</td>';

					if(isset($_data['rowspan']))
					{
					$html.='<td align="center" rowspan="'.$_data['rowspan'].'" >';

	$sign = '-';
	
					switch(strtolower($_data['description'])){
						case 'shipped &rarr; canceled':
						case 'stock adjustment has been made.':
						case 'stock adjustment.':
						case 'stock adjustment.':
						case 'processed &rarr; canceled':
						case 'on hold &rarr; canceled':
						case 'shipped &rarr; canceled':
						case 'shipment qc &rarr; completed':
						case 'stock adjustment (cycle count).':
						case 'quantity has rolled back.':
						case 'stock adjustment (add).':
						case 'stock adjustment (rtv).':
						case 'bulk cycle count':
						
						$sign='+';
						break;
						default:
						$sign = '-';
						break;
					}

					if($_data['quantity']<0)
					{
						$sign='-';
					}
					if($_data['quantity']==0)
					{
						$sign='';
					}

					
					$html.='<span class="tag '.($sign=='-'?'red-bg':'blue-bg').'">
					'.$sign.($_data['quantity']<0?$_data['quantity']*(-1):$_data['quantity']).'</span></td>';
				}
					
					
					$html.='<td>'.get_username($_data['user_id']).'</td>
				
					</tr>';

					$kk++;
				}
				
					$html.='</tr>';
					
					$loop++;
				}
				
				echo json_encode(array('html'=>$html));



	exit;
}
if (isset($_POST['vendors'])) {
	if ($_POST['vendors']) {
		$query = $db->func_query("SELECT * FROM inv_product_vendors WHERE product_sku='".$_POST['sku']."'");
		foreach($query as $q)
		{
			$check = $db->func_query_first_cell("SELECT vendor FROM inv_product_vendors_log WHERE vendor='".$q['vendor']."' and product_sku='".$q['product_sku']."'");
			if(!$check)
			{
				$array = array();
				$array['product_sku'] = $_POST['sku'];
				$array['vendor'] = $q['vendor'];
				$db->func_array2insert("inv_product_vendors_log", $array);
				unset($array);
			}
		}
		$db->func_query('DELETE FROM `inv_product_vendors` WHERE `product_sku` = "'. $_POST['sku'] .'"');
		foreach ($_POST['vendors'] as $key => $val) {
			$array = array();
			$array['product_sku'] = $_POST['sku'];
			$array['vendor'] = $val;
			$db->func_array2insert("inv_product_vendors", $array);
			unset($array);
		}
	}
	echo json_encode(array('msg' => 'Vendor Updated'));
	exit;
}
if ($_POST['remove']) {
	$update = $db->db_exec('UPDATE oc_product set ignore_up = "'. (int)$_POST['value'] .'" WHERE product_id = "'. (int)($_POST['id']) .'"');
	$sk = $db->func_query_first('SELECT * FROM oc_product where product_id = "'. (int)($_POST['id']) .'"');
	$log = linkToProduct($sk['sku']) . (($_POST['value'])? 'added to':'removed From') . ' Ignore List';
	actionLog($log);
	$json = array('success' => 1);
	echo json_encode($json);
	exit;
}
if ($_POST['action'] == 'removeImage') {
	if ($_POST['type'] == 'main') {
		$db->db_exec('UPDATE oc_product SET image = "data/image-coming-soon.jpg" WHERE product_id = "'. (int)$_POST['id'] .'"');
	} else if ($_POST['type'] == 'sub') {
		$db->db_exec('DELETE from oc_product_image WHERE product_image_id = "'. (int)$_POST['id'] .'"');
	}
	echo json_encode(array('success' => 1));
	exit;
}
if ($_POST['action'] == 'uploadSubFile') {
	$allowed = array('png', 'jpeg', 'jpg');
	$product = $db->func_query_first('SELECT * FROM oc_product a, oc_product_description b WHERE a.product_id = b.product_id AND a.product_id = "'. (int)$_POST['id'] .'"');
	if ($_POST['altimg']) {
		$updateImageAlt = $_POST['altimg'];
		$updateSortOrder = $_POST['sort_order'];
		foreach ($updateImageAlt as $product_image_id => $altimg) {
			if ($_FILES['upFile']['tmp_name'][$product_image_id]) {
				$uniqid = uniqid();
				$name = explode(".", $_FILES['upFile']['name'][$product_image_id]);
				$ext = end($name);
				$fileName = $uniqid . '-' . changeImageName($product['sku'] . '-' . $product['name']) . '.' . $ext;
				$dir = makeImgDir($product['sku'], $path);
				$destination = str_replace('/imp', '', $path) . 'image/data/' . $dir . $fileName;
				$file = $_FILES['upFile']['tmp_name'][$product_image_id];
				if (in_array($ext, $allowed)) {
					if (move_uploaded_file($file, $destination)) {
						$db->db_exec('UPDATE oc_product_image SET image = "'. 'data/' . $dir .$fileName .'", altimg = "'. $altimg .'", sort_order = "'. $updateSortOrder[$product_image_id] .'" WHERE product_image_id = "'. $product_image_id .'"');
					}
				}
			} else {
				$db->db_exec('UPDATE oc_product_image SET altimg = "'. $altimg .'", sort_order = "'. $updateSortOrder[$product_image_id] .'" WHERE product_image_id = "'. $product_image_id .'"');
			}
		}
	}
	if ($_POST['newaltimg']) {
		$newImageAlt = $_POST['newaltimg'];
		$newSortOrder = $_POST['newsort_order'];
		foreach ($newImageAlt as $product_image_id => $altimg) {
			if ($_FILES['newFile']['tmp_name'][$product_image_id]) {
				$uniqid = uniqid();
				$name = explode(".", $_FILES['newFile']['name'][$product_image_id]);
				$ext = end($name);
				$fileName = $uniqid . '-' . changeImageName($product['sku'] . '-' . $product['name']) . '.' . $ext;
				$dir = makeImgDir($product['sku'], $path);
				$destination = str_replace('/imp', '', $path) . 'image/data/' . $dir . $fileName;
				$file = $_FILES['newFile']['tmp_name'][$product_image_id];
				if (in_array($ext, $allowed)) {
					if (move_uploaded_file($file, $destination)) {
						$db->db_exec('INSERT INTO oc_product_image SET image = "'. 'data/' . $dir .$fileName .'", product_id = "'. (int)$_POST['id'] .'", altimg = "'. $altimg .'", sort_order = "'. $newSortOrder[$product_image_id] .'"');
					}
				}
			} else {
				//$array['error'] = 1;
				$array['msg'] = ' (' . $_FILES['newFile']['name'][$product_image_id] . ')';
			}
		}
	}
	$log = 'Images updated for ' . linkToProduct($product['sku']);
	actionLog($log);
	if ($array['error']) {
		$array['msg'] = 'Error in File(s) ' . $array['msg'];
	} else {
		$array = array('success'=> 1);
	}
	echo json_encode($array);
	exit;
}
if ($_POST['action'] == 'uploadFile') {
	$allowed = array('png', 'jpeg', 'jpg');
	$product = $db->func_query_first('SELECT * FROM oc_product a, oc_product_description b WHERE a.product_id = b.product_id AND a.product_id = "'. (int)$_POST['id'] .'"');
	if ($_FILES['file']['tmp_name']) {
		$uniqid = uniqid();
		$name = explode(".", $_FILES['file']['name']);
		$ext = end($name);
		$fileName = $uniqid . '-' . changeImageName($product['sku'] . '-' . $product['name']) . '.' . $ext;
		$dir = makeImgDir($product['sku'], $path);
		$destination = str_replace('/imp', '', $path) . 'image/data/' . $dir . $fileName;
		$file = $_FILES['file']['tmp_name'];
		if (in_array($ext, $allowed)) {
			if (move_uploaded_file($file, $destination)) {
				if ($_POST['type'] == 'main') {
					$db->db_exec('UPDATE oc_product SET image = "'. 'data/' . $dir .$fileName .'" WHERE product_id = "'. (int)$_POST['id'] .'"');
				}
				if ($_POST['type'] == 'sub') {
					$db->db_exec('INSERT INTO oc_product_image SET image = "'. 'data/' . $dir .$fileName .'", product_id = "'. (int)$_POST['id'] .'"');
				}
				$log = 'Images updated for ' . linkToProduct($product['sku']);
				actionLog($log);
				$array = array('success'=> 1);
				echo json_encode($array);
				exit;
			}
		} else {
			$array = array('error'=> 1, 'msg' => 'This file is now allowed');
			echo json_encode($array);
			exit;
		}
	} else {
		$array = array('error'=> 1, 'msg' => 'Please Select File');
		echo json_encode($array);
		exit;
	}
	exit;
}
$product_sku = $db->func_escape_string($_GET['sku']);
if($_POST['action']=='update_cost')
{
	$date = date('Y-m-d');
	$SKU = $db->func_escape_string($product_sku);
	//print_r($_POST['raw_cost']);exit;
	$raw_cost = $db->func_escape_string($_POST['raw_cost']);
	$ex_rate = $db->func_escape_string($_POST['ex_rate']);
	$update_with = $db->func_escape_string($_POST['update_with']);
	$downgrades_a_sku = $db->func_escape_string($_POST['downgrades_a_sku']);
	// $downgrades_a_price = (float)($_POST['downgrades_a_price']);
	$downgrades_b_sku = $db->func_escape_string($_POST['downgrades_b_sku']);
	$downgrades_b_price = $db->func_escape_string($_POST['downgrades_b_price']);
	$downgrades_c_sku = $db->func_escape_string($_POST['downgrades_c_sku']);
	$downgrades_c_price = $db->func_escape_string($_POST['downgrades_c_price']);
	// print_r($downgrades_a_sku);exit;	
	// echo( json_encode(array((float)$_POST['downgrades_a_price'])) );exit;
	$old_Price = getTrueCost($SKU);
	$xCost = $db->func_query_first("SELECT * FROM inv_product_costs WHERE sku = '$SKU' order by cost_date DESC");
	$old_raw_cost = $xCost['raw_cost'];
	$old_shipFee = $xCost['shipping_fee'];
	// echo "update oc_product set sku='".$downgrades_a_sku."' , price='".$_POST['downgrades_a_price']."' where item_grade='Grade A' AND main_sku='".$product_sku."' ";exit;
	$array = array();
	$array['sku'] =$downgrades_a_sku; 
	$array['price'] =$_POST['downgrades_a_price'];
	$db->func_array2update('oc_product',$array," item_grade='Grade A' AND main_sku='".$product_sku."'");
	// $db->db_exec("update oc_product set sku='".$downgrades_a_sku."' , price='".$_POST['downgrades_a_price']."' where item_grade='Grade A' AND main_sku='".$product_sku."' ");
	$db->db_exec("update oc_product set sku='".$downgrades_b_sku."' , price='".$downgrades_b_price."' where item_grade='Grade B' AND main_sku='".$product_sku."' ");
	$db->db_exec("update oc_product set sku='".$downgrades_c_sku."' , price='".$downgrades_c_price."' where item_grade='Grade C' AND main_sku='".$product_sku."' ");
	if($update_with=='cost' || $update_with=='both')
	{
		addUpdateProductCost($SKU, (float)$raw_cost, (float)$ex_rate, (float)$_POST['shipping_fee']);	
	}
	if($update_with=='price' || $update_with=='both')
	{
		updateProductPrice($SKU,(float)$raw_cost,(float)$ex_rate,(float)$_POST['shipping_fee']);	
	}
	$new_Price = getTrueCost($SKU);
	if ($old_Price != $new_Price) {
		$log .= '<br>True Cost From: ' . $old_Price . ' To: ' . $new_Price."<br>";
	}
	if ($old_raw_cost != $raw_cost) {
		$log .= '<br>Raw Cost From: ' . $old_raw_cost . ' To: ' . $raw_cost."<br>";
	}
	if ($old_shipFee != $_POST['shipping_fee']) {
		$log .= '<br>Shipping Fee From: ' . $old_shipFee . ' To: ' . $_POST['shipping_fee']."<br>";
	}
	if ($log) {
		$log = 'Cost/Price updated for ' . linkToProduct($SKU). "<br>" . $log;
		actionLog($log);
	}
	$json= array();
	$json['success'] = "Changes Modified";
	echo json_encode($json);exit;
}
$_query = "SELECT pd.title, pd.meta_keyword,pd.meta_description, p.sku , p.is_csv_added,p.is_shopify,p.is_ebay,p.discontinue,p.is_shopify_uploaded, p.video , p.ignore_up , p.image , p.price ,p.sale_price,p.bulk_price,p.shopify_compare_price,p.shopify_price, p.product_id, p.is_main_sku, p.quantity,p.is_kit,p.classification_id,sub_classification_id,vendor,p.special_discount,p.status,pd.name,pd.description,p.weight,p.visibility,p.show_on_top from oc_product p, oc_product_description pd where p.product_id=pd.product_id and p.sku = '$product_sku'";

$product = $db->func_query_first($_query);


$product['weight_oz'] =  $product['weight'] * 16;
$product['keyword'] = $db->func_query_first_cell("SELECT keyword FROM oc_url_alias WHERE query='product_id=".(int)$product['product_id']."'");
$outstock_date = $db->func_query_first_cell("select outstock_date from inv_product_inout_stocks where product_sku = '" . $product['sku'] . "' order by date_modified desc limit 1");
$product['vendor_code'] = 'China Office';
$product_id = $product['product_id'];
$product_tags = $db->func_query_first_cell("select group_concat(tag) as tags  from oc_product_tag where product_id = '$product_id' group by product_id");
$product['replacement_for1'] = $db->func_query_first_cell("SELECT name FROM oc_product_to_field WHERE product_id='".$product_id."' and additional_product_id=2 ");
$product['replacement_for2'] = $db->func_query_first_cell("SELECT name FROM oc_product_to_field WHERE product_id='".$product_id."' and additional_product_id=3 ");
$product['is_blowout'] = $db->func_query_first_cell("SELECT is_blowout FROM inv_device_product WHERE sku='".$product['sku']."' ");
$product_categories = array();
$_product_categories = $db->func_query("SELECT category_id FROM oc_product_to_category WHERE product_id='".$product_id."'");
foreach($_product_categories as $_pc)
{
	$product_categories[] = $_pc['category_id'];
}
/* if($product['pricing_rule'] == 'Manual'){
  $customer_groups_data = $db->func_query("select * from oc_product_discount where product_id = '".$product_id."'","customer_group_id","quantity");
  }
  else{ */
  	$customer_groups_data = $db->func_query("select * from oc_product_discount where product_id = '" . $product_id . "'", "customer_group_id", "quantity");
//$customer_groups_data = $db->func_query("select * from inv_product_discount where product_id = '".$product_id."'","customer_group_id","quantity");
//} commented by zaman
  	$product_price = $db->func_query_first_cell("select price from oc_product_discount where product_id = '$product_id' AND customer_group_id = '8' and quantity = 1");
  	$product_prices = $db->func_query_first("select * from inv_product_prices where product_sku = '$product_sku'");
  	if ($_POST['update']) {
  		$is_csv = $db->func_escape_string($_POST['is_csv_added']);
  		$is_shopify = $db->func_escape_string($_POST['is_shopify']);
  		$is_ebay = $db->func_escape_string($_POST['is_ebay']);
  		$discontinue = $db->func_escape_string($_POST['discontinue']);
  		$is_shopify_uploaded = $db->func_escape_string($_POST['is_shopify_uploaded']);
  		$is_blowout = (int)$_POST['is_blowout'];

  		$db->db_exec("update oc_product SET is_shopify = '$is_shopify',discontinue = '$discontinue', is_shopify_uploaded = '$is_shopify_uploaded' , is_csv_added = '$is_csv',is_ebay='".(int)$is_ebay."' where product_id = '$product_id'");

  		$db->db_exec("UPDATE inv_device_product SET is_blowout='".(int)$is_blowout."' WHERE TRIM(LOWER(sku))='".trim(strtolower($product_sku))."'");
  		$date = date('Y-m-d');
  		$SKU = $db->func_escape_string($product_sku);
  		$raw_cost = $db->func_escape_string($_POST['raw_cost']);
  		$ex_rate = $db->func_escape_string($_POST['ex_rate']);
   // addUpdateProductCost($SKU, $raw_cost, $ex_rate, $_POST['shipping_fee']);
	//update discount prices
  		$product_tags = $db->func_escape_string($_POST['product_tags']);
  		if($product_tags)
  		{
  			$db->db_exec("DELETE FROM oc_product_tag WHERE product_id='".$product_id."'");
  			$_product_tags = explode(",", $product_tags);
  			foreach($_product_tags as $_tag)
  			{
  				$_insert = array();
  				$_insert['product_id'] = $product_id;
  				$_insert['language_id'] = 1;
  				$_insert['tag'] = $db->func_escape_string(trim($_tag));
  				$db->func_array2insert("oc_product_tag", $_insert);
  			}
  		}
  		if ($product['is_main_sku'] == '1') {
		/*
		  Commented by Zaman
		  if($_POST['pricing_rule'] == 'Manual' && $_POST['discount_fixed']){
		  foreach($_POST['discount_fixed'] as $group_id => $data){
		  foreach($data as $quantity => $price){
		  if($price > 0 && $quantity > 0){
		  if(isset($customer_groups_data[$group_id][$quantity])){
		  //$db->db_exec("update oc_product_discount SET price = '$price' where product_id = '$product_id' and customer_group_id = '$group_id' and quantity = '$quantity'");
		  }
		  else{
		  //$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '$product_id' , customer_group_id = '$group_id' , quantity = '$quantity' , price = '$price'");
		  }
		  }
		  }
		  }
		  }
		  elseif($_POST['pricing_rule'] == 'CostBased' && $_POST['discount_markup']){
		  $customer_groups_discount_data = $db->func_query("select * from oc_product_discount where product_id = '".$product_id."'","customer_group_id","quantity");
		  $vendor_code   = $product['vendor_code'];
		  $last_cost = $db->func_query_first_cell("select (current_cost+shipping_fee) as cost from inv_product_costs where vendor_code = '$vendor_code' AND sku = '$product_sku' order by cost_date DESC limit 1");
		  foreach($_POST['discount_markup'] as $group_id => $data){
		  foreach($data as $quantity => $markup){
		  if($quantity > 0 && $markup > 0){
		  if(isset($customer_groups_data[$group_id][$quantity])){
		  $db->db_exec("update inv_product_discount SET markup = '$markup' where product_id = '$product_id' and customer_group_id = '$group_id' and quantity = '$quantity'");
		  }
		  else{
		  $db->db_exec("insert into inv_product_discount SET product_id = '$product_id' , customer_group_id = '$group_id' , quantity = '$quantity' , markup = '$markup'");
		  }
		  //now need to update discount table also
		  $price = number_format(($last_cost + (($last_cost*$markup)/100)),4);
		  if(isset($customer_groups_discount_data[$group_id][$quantity])){
		  //$db->db_exec("update oc_product_discount SET price = '$price' where product_id = '$product_id' and customer_group_id = '$group_id' and quantity = '$quantity'");
		  }
		  else{
		  //$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '$product_id' , customer_group_id = '$group_id' , quantity = '$quantity' , price = '$price'");
		  }
		  }
		  }
		  }
		  }
		 */
		}
		//update Competitor URLS
		
		foreach ($_POST['scraped_url'] as $key => $url) {
			$url_update = array();
			$url_update['url'] = $url;
			$url_update['type'] = $key;
			$url_update['sku'] = $product_sku;
			$check = $db->func_query_first("SELECT * FROM inv_product_price_scrap where sku ='$product_sku' and type='$key'");
			if($check)
			{
			$db->func_array2update("inv_product_price_scrap", $url_update, " sku = '$product_sku' AND `type` = '$key'");
			if ($check['url'] != $url) {
					$db->func_query("UPDATE inv_product_price_scrap set is_new = '1' where sku ='$product_sku' and type='$key'");
				}
			}
			else
			{
				$db->func_array2insert("inv_product_price_scrap", $url_update);
				$db->func_query("UPDATE inv_product_price_scrap set is_new = '1' where sku ='$product_sku' and type='$key'");
			}
		}
	//update prices
		$product_prices_row = array();
		$product_prices_row['ebay'] = $_POST['ebay'];
		$product_prices_row['amazon'] = $_POST['amazon'];
		$product_prices_row['channel_advisor'] = $_POST['channel_advisor'];
		$product_prices_row['channel_advisor1'] = $_POST['channel_advisor1'];
		$product_prices_row['channel_advisor2'] = $_POST['channel_advisor2'];
		$product_prices_row['bigcommerce'] = $_POST['bigcommerce'];
		$product_prices_row['bigcommerce_retail'] = $_POST['bigcommerce_retail'];
		$product_prices_row['bonanza'] = $_POST['bonanza'];
		$product_prices_row['wish'] = $_POST['wish'];
		$product_prices_row['open_sky'] = $_POST['open_sky'];
		$product_prices_row['date_modified'] = date('Y-m-d H:i:s');
		if ($product_prices) {
			$db->func_array2update("inv_product_prices", $product_prices_row, " product_sku = '$product_sku'");
			// foreach ($product_prices_row as $key => $val) {
			// 	if ($val != $product_prices[$key] && $key != 'date_modified') {
			// 		$log .= '<br>' . str_replace('_', ' ', $key) . ' From: ' . $product_prices[$key] . ' To: ' . $val."<br>";
			// 	}
			// }
		} else {
			$product_prices_row['product_sku'] = $product_sku;
			$db->func_array2insert("inv_product_prices", $product_prices_row);
			// foreach ($product_prices_row as $key => $val) {
			// 	if ($key != 'date_modified') {
			// 		$log .= '<br>' . str_replace('_', ' ', $key) . ' To: ' . $val."<br>";
			// 	}
			// }
		}
		if($_SESSION['product_pricing']){
			$old_grades = $db->func_query('SELECT sku, price, item_grade FROM oc_product WHERE main_sku = "'. $product_sku .'"');
			foreach ($_POST['downgrades'] as $grade => $key) {
		//foreach($key as $key_val => $val)
		//{
		//echo $key_val."<Br>".$val."<br>";exit;
				if ($key['sku'] and $key['price']) {
					$db->db_exec("UPDATE oc_product SET price='" . (float) $key['price'] . "' WHERE sku='" . $key['sku'] . "' AND item_grade='" . $grade . "' AND main_sku = '$product_sku'");
				}
				foreach ($old_grades as $val) {
					if ($val['sku'] == $key['sku'] && $val['item_grade'] == $grade && $val['price'] != $key['price']) {
						$log .= '<br>' . $grade . ' From: ' . $val['price'] . ' To: ' . $key['price']."<Br>";
					}
				}
		//}
			}
		}
		if($_SESSION['product_pricing']) {
			$old_item_price = $db->func_query_first_cell('SELECT price FROM oc_product WHERE sku = "'. $product_sku .'"');
			if ($_POST['price'] != '') {
				$db->db_exec('UPDATE `oc_product` SET price="'. (float) $_POST['price'] .'" WHERE sku="'. $product_sku .'"');
				if ($old_item_price != $_POST['price']) {
					$log .= '<br>Orignal Price' . ' From: ' . $old_item_price . ' To: ' . $_POST['price']."<br>";
				}
			}
            $price_csv_header  = array();
			$price_csv_header[] = 'Date Updated';
			$price_csv_header[] = 'User';
			$price_csv_header[] = 'SKU';
			$price_csv_header[] = 'Item Name';
			$price_csv_header[] = 'Price Type';
			$price_csv_header[] = 'Old Price';
			$price_csv_header[] = 'New Price';
			$price_csv_row  = array();
			
			$price_update = false;
			$iterator = 0;
			if ($_POST['sale_price'] != '') {
                $check = $db->func_query_first_cell('SELECT sale_price from `oc_product` WHERE sku="'. $product_sku .'"');
				if ((float)$_POST['sale_price'] != (float)$check) {
					$price_update = true;
					$price_csv_row[$iterator][] = date('Y-m-d H:i:s');
					$price_csv_row[$iterator][] = get_username($_SESSION['user_id']);
					$price_csv_row[$iterator][] = $product_sku;
					$price_csv_row[$iterator][] = $_POST['product_title'];
					$price_csv_row[$iterator][] = 'Sale';
					$price_csv_row[$iterator][] = '$'.number_format($check,2);
					$price_csv_row[$iterator][] = '$'.number_format($_POST['sale_price'],2);
					$iterator ++;	

					$db->db_exec("INSERT INTO inv_product_price_change_history SET user_id='".$_SESSION['user_id']."',sku='".$product_sku."',price_type='Sale',old_price='".(float)$check."',new_price='".(float)$_POST['sale_price']."',date_added='".date('Y-m-d H:i:s')."'");


				}
				$db->db_exec('UPDATE `oc_product` SET sale_price="'. (float) $_POST['sale_price'] .'" WHERE sku="'. $product_sku .'"');
			} else if ($_POST['sale_price'] == ''){
			    $check = $db->func_query_first_cell('SELECT sale_price from `oc_product` WHERE sku="'. $product_sku .'"');
				if (0.0000 != (float)$check) {
					$price_update = true;
					$price_csv_row[$iterator][] = date('Y-m-d H:i:s');
					$price_csv_row[$iterator][] = get_username($_SESSION['user_id']);
					$price_csv_row[$iterator][] = $product_sku;
					$price_csv_row[$iterator][] = $_POST['product_title'];
					$price_csv_row[$iterator][] = 'Sale';
					$price_csv_row[$iterator][] = '$'.number_format($check,2);
					$price_csv_row[$iterator][] = '$0.00';	
					$iterator++;


					$db->db_exec("INSERT INTO inv_product_price_change_history SET user_id='".$_SESSION['user_id']."',sku='".$product_sku."',price_type='Sale',old_price='".(float)$check."',new_price='0.00',date_added='".date('Y-m-d H:i:s')."'");


				}
				$db->db_exec('UPDATE `oc_product` SET sale_price="0.0000" WHERE sku="'. $product_sku .'"');
			}
			if ($_POST['bulk_price'] != '') {
                $check = $db->func_query_first_cell('SELECT bulk_price from `oc_product` WHERE sku="'. $product_sku .'"');
				if ((float)$_POST['bulk_price'] != (float)$check) {
					$price_update = true;
					$price_csv_row[$iterator][] = date('Y-m-d H:i:s');
					$price_csv_row[$iterator][] = get_username($_SESSION['user_id']);
					$price_csv_row[$iterator][] = $product_sku;
					$price_csv_row[$iterator][] = $_POST['product_title'];
					$price_csv_row[$iterator][] = 'Bulk';
					$price_csv_row[$iterator][] = '$'.number_format($check,2);
					$price_csv_row[$iterator][] = '$'.number_format($_POST['bulk_price'],2);
					$iterator ++;	

					$db->db_exec("INSERT INTO inv_product_price_change_history SET user_id='".$_SESSION['user_id']."',sku='".$product_sku."',price_type='Bulk',old_price='".(float)$check."',new_price='".(float)$_POST['bulk_price']."',date_added='".date('Y-m-d H:i:s')."'");

				}
				$db->db_exec('UPDATE `oc_product` SET bulk_price="'. (float) $_POST['bulk_price'] .'" WHERE sku="'. $product_sku .'"');
			} else if ($_POST['bulk_price'] == ''){
			    $check = $db->func_query_first_cell('SELECT bulk_price from `oc_product` WHERE sku="'. $product_sku .'"');
				if (0.0000 != (float)$check) {
					$price_update = true;
					$price_csv_row[$iterator][] = date('Y-m-d H:i:s');
					$price_csv_row[$iterator][] = get_username($_SESSION['user_id']);
					$price_csv_row[$iterator][] = $product_sku;
					$price_csv_row[$iterator][] = $_POST['product_title'];
					$price_csv_row[$iterator][] = 'Bulk';
					$price_csv_row[$iterator][] = '$'.number_format($check,2);
					$price_csv_row[$iterator][] = '$0.00';	
					$iterator++;

					$db->db_exec("INSERT INTO inv_product_price_change_history SET user_id='".$_SESSION['user_id']."',sku='".$product_sku."',price_type='Bulk',old_price='".(float)$check."',new_price='0.00',date_added='".date('Y-m-d H:i:s')."'");
				}
				$db->db_exec('UPDATE `oc_product` SET bulk_price="0.0000" WHERE sku="'. $product_sku .'"');
			}
			if ($_POST['shopify_price'] != '') {
				$db->db_exec('UPDATE `oc_product` SET shopify_price="'. (float) $_POST['shopify_price'] .'" WHERE sku="'. $product_sku .'"');
				$shopify_product_id =  $db->func_query_first_cell("SELECT shopify_product_id FROM inv_shopify WHERE sku='$product_sku'");
				if ($shopify_product_id && $_POST['shopify_price'] != '0.0000') {
						updateShopifyProductPrice($shopify_product_id,(float)$_POST['shopify_price'],(float)$_POST['shopify_compare_price']);
				}
			} else if ($_POST['shopify_price'] == ''){
				$db->db_exec('UPDATE `oc_product` SET shopify_price="0.0000" WHERE sku="'. $product_sku .'"');
			}
			if ($_POST['shopify_compare_price'] != '') {
				$db->db_exec('UPDATE `oc_product` SET shopify_compare_price="'. (float) $_POST['shopify_compare_price'] .'" WHERE sku="'. $product_sku .'"');
				$shopify_product_id =  $db->func_query_first_cell("SELECT shopify_product_id FROM inv_shopify WHERE sku='$product_sku'");
				if ($shopify_product_id && $_POST['shopify_price'] != '0.0000') {
						updateShopifyProductPrice($shopify_product_id,(float)$_POST['shopify_price'],(float)$_POST['shopify_compare_price']);
				}
			} else if ($_POST['shopify_compare_price'] == ''){
				$db->db_exec('UPDATE `oc_product` SET shopify_compare_price="0.0000" WHERE sku="'. $product_sku .'"');
			}
		}
		if($_SESSION['product_pricing']) {
			$group_prices = $db->func_query('SELECT `price`, opd.`customer_group_id`, `quantity`, `name` FROM oc_product_discount opd, `oc_customer_group` ocg WHERE opd.`customer_group_id` = ocg.`customer_group_id` AND `product_id` = "'. $product_id .'"');
			if ($_POST['discount_fixed']) {
				$customer_groups_discount_data = $db->func_query("select * from oc_product_discount where product_id = '" . $product_id . "'", "customer_group_id", "quantity");
				foreach ($_POST['discount_fixed'] as $group_id => $data) {
					foreach ($data as $quantity => $price) {
						if ($quantity > 0 && $price > 0) {
							if (isset($customer_groups_discount_data[$group_id][$quantity])) {
							    $group_name = $db->func_query_first_cell('SELECT name FROM oc_customer_group_description WHERE customer_group_id = "'. $group_id .'"');
								if ($group_name == 'Wholesale Small') {
									$group_name = 'Bronze';
								}
								$old_price = $db->func_query_first_cell('SELECT price FROM oc_product_discount WHERE customer_group_id = "'. $group_id .'" AND quantity = "'. $quantity .'" AND product_id = "'. $product_id .'"');
								if ( (float)$old_price !=  (float)$price) {
									$price_update = true;
									$price_csv_row[$iterator][] = date('Y-m-d H:i:s');
									$price_csv_row[$iterator][] = get_username($_SESSION['user_id']);
									$price_csv_row[$iterator][] = $product_sku;
									$price_csv_row[$iterator][] = $_POST['product_title'];
									$price_csv_row[$iterator][] = $group_name.' '.$quantity;
									$price_csv_row[$iterator][] = '$'.number_format($old_price,2);
									$price_csv_row[$iterator][] = '$'.number_format($price,2);


									$db->db_exec("INSERT INTO inv_product_price_change_history SET user_id='".$_SESSION['user_id']."',sku='".$product_sku."',price_type='".$group_name.' '.$quantity."',old_price='".(float)$old_price."',new_price='".(float)$price."',date_added='".date('Y-m-d H:i:s')."'");
									
									
									$iterator++;
								}
								$db->db_exec("update oc_product_discount SET price = '" . (float) $price . "' where product_id = '$product_id' and customer_group_id = '$group_id' and quantity = '$quantity'");
								foreach ($group_prices as $val) {
									if ($val['customer_group_id'] == $group_id && $val['quantity'] == $quantity && $val['price'] != $price) {
										$log .= '<br> Group ' . $val['name'] . ' Quantity ' . $val['quantity'] . ' Price From: ' . round($val['price'], 2) . ' To: ' . round($price,2)."<br>";
									}
								}
							} else {
							    $group_name = $db->func_query_first_cell('SELECT name FROM oc_customer_group_description WHERE customer_group_id = "'. $group_id .'"');
								if ($group_name == 'Wholesale Small') {
									$group_name = 'Bronze';
								} 
								$old_price = 0.00;
								if ( $old_price !=  $price) {
									$price_update = true;
									$price_csv_row[$iterator][] = date('Y-m-d H:i:s');
									$price_csv_row[$iterator][] = get_username($_SESSION['user_id']);
									$price_csv_row[$iterator][] = $product_sku;
									$price_csv_row[$iterator][] = $_POST['product_title'];
									$price_csv_row[$iterator][] = $group_name.' '.$quantity;
									$price_csv_row[$iterator][] = '$'.number_format($old_price,2);
									$price_csv_row[$iterator][] = '$'.number_format($price,2);


									$db->db_exec("INSERT INTO inv_product_price_change_history SET user_id='".$_SESSION['user_id']."',sku='".$product_sku."',price_type='".$group_name.' '.$quantity."',old_price='".(float)$old_price."',new_price='".(float)$price."',date_added='".date('Y-m-d H:i:s')."'");
									
									
									$iterator++;
								}
								$db->db_exec("insert into oc_product_discount SET product_id = '$product_id' , customer_group_id = '$group_id' , quantity = '$quantity' , price = '" . (float) $price . "'");
								$log .= '<br>'. $db->func_query_first_cell('SELECT name FROM oc_customer_group WHERE customer_group_id = "'. $group_id .'"') .' To: ' . $key['price']."<br>";
							}
						}
					}
				}
			}
			if ($price_update && $product['status']==1) {
				$filename = "price_report/PriceUpdateReport-".date("Y-m-d").".csv";
				if (!file_exists($_SERVER['DOCUMENT_ROOT']."/price_report/PriceUpdateReport-".date("Y-m-d").".csv")) {
					
					$file = fopen($filename,"w");
					fputcsv($file , $price_csv_header,',');
				} else {
					$file = fopen($filename,"a");
				}
				foreach ($price_csv_row as $row) {
					fputcsv($file , $row,',');
				}
				fclose($file);
			}
		}
		if ($_POST['kit_sku'] and $_POST['kit_price']) {
			$db->db_exec("UPDATE oc_product SET price='" . (float) $_POST['kit_price'] . "' WHERE sku='" . $db->func_escape_string($_POST['kit_sku']) . "'");
			$log .= '<br>Kit Sku To: ' . $_POST['kit_price'];
		}
		if (isset($_POST['product_visibility']) ) {
			$db->db_exec("UPDATE oc_product SET visibility='" . (int) $_POST['product_visibility'] . "' WHERE sku='" . $product_sku  . "'");
			$log .= '<br>Kit Sku To: ' . $_POST['kit_price'];
		}
		if($_SESSION['product_description']) {
			$old_record = $db->func_query_first('SELECT * FROM oc_product WHERE product_id ="' . (int) $product_id . '"');
			$db->db_exec("UPDATE oc_product SET classification_id='" . (int) $_POST['classification_id'] . "',vendor='" . $db->func_escape_string($_POST['vendor']) . "',status='".(int)$_POST['product_status']."',weight='".(float)$_POST['weight']."',weight_class_id=5,show_on_top='".(int)$_POST['show_on_top']."' WHERE product_id='" . (int) $product_id . "'");
			if ($old_record['status'] != $_POST['product_status']) {
				$log .= '<br>Status Changed to: ' . (($_POST['product_status'])? 'Enabled': 'Disabled');
			}
			if ($_POST['classification_id']) {
				$log .= '<br>Classification changed to: '. $db->func_query_first_cell('SELECT `name` FROM `inv_classification` WHERE `id` = "'. (int) $_POST['classification_id'] .'"');
			}
		}
		$old_record = $db->func_query_first("SELECT video,sku,image,quantity FROM oc_product WHERE product_id='$product_id'");
		$old_desc = $db->func_query_first("SELECT name,description FROM oc_product_description WHERE product_id='$product_id'");
		if ($_SESSION['product_qty_update']) {
			if ($old_record['quantity'] != $_POST['quantity']) {
				$shopify_product_id =  $db->func_query_first_cell("SELECT shopify_product_id FROM inv_shopify WHERE sku='$product_sku'");
				if ($shopify_product_id) {
					updateShopifyProductQty($shopify_product_id,$db->func_escape_string($_POST['quantity']));
				}
				$log .= '<br> Product Quantity Updated';
				makeLedger('',array($product_sku=>(int)$_POST['quantity']),$_SESSION['user_id'],'adjustment');
			}
			$db->func_query("UPDATE oc_product SET fb_qty_up='0', quantity='".$db->func_escape_string($_POST['quantity'])."' WHERE product_id='".$product_id."'");
			
		}
		if($_SESSION['product_description']) {
			//$path = '../../../../image/';
			$path = str_replace('/imp', '', $path).'/image/';
			if ($old_desc['name'] != $_POST['product_title']) {
				$log .= '<br> Product Title Updated';
			}
			if ($old_record['video'] != $_POST['video']) {
				$log .= '<br> Product Youtube Video Updated';
			}
			if ($old_desc['description'] != $_POST['product_description']) {
				$log .= '<br> Product Description Updated';
			}
			$db->func_query("UPDATE oc_product_description SET name='".$db->func_escape_string($_POST['product_title'])."', description='".$db->func_escape_string($_POST['product_description'])."',title='".$db->func_escape_string($_POST['page_title'])."',meta_keyword='".$db->func_escape_string($_POST['meta_keyword'])."',meta_description='".$db->func_escape_string($_POST['meta_description'])."' WHERE product_id='".$product_id."'");
			$db->func_query("UPDATE oc_product SET video='".$db->func_escape_string($_POST['video'])."' WHERE product_id='".$product_id."'");
			$db->db_exec("DELETE FROM oc_product_to_field WHERE product_id='".$product_id."' and additional_product_id in (2,3)");
			$db->db_exec("INSERT INTO oc_product_to_field SET product_id='".$product_id."',additional_product_id=2,language_id=1,name='".$db->func_escape_string($_POST['replacement_for1'])."'");
			$db->db_exec("INSERT INTO oc_product_to_field SET product_id='".$product_id."',additional_product_id=3,language_id=1,name='".$db->func_escape_string($_POST['replacement_for2'])."'");
			$folder_name = substr($old_record['sku'],0,7);
			$extension = explode(".",$old_record['image']);
			$extension = end($extension);
			$image_path = explode("/",$old_record['image']);
			
			unset($image_path[count($image_path)-1]);
			$image_path = implode("/", $image_path);
			//echo $image_path;exit;
			$image_name = $old_record['sku'].'-'.changeImageName($_POST['product_title']).'-1.'.$extension;
			$image_path = $image_path.'/'.$image_name;
			//echo $image_path;exit;
			if($old_record['image']) {
				$file = rename($path.$old_record['image'],$path.$image_path);
				//var_dump($file);exit;
				$db->db_exec("UPDATE oc_product set image='$image_path' WHERE product_id='$product_id'");
				$_sub_images = $db->func_query("SELECT product_image_id,image FROM oc_product_image WHERE product_id='$product_id' ORDER by product_image_id ");
				$_i=2;
				foreach($_sub_images as $sub) {
					$extension = explode(".",$sub['image']);
					$extension = end($extension);
					$image_path_sub = explode("/",$sub['image']);
					unset($image_path_sub[count($image_path_sub)-1]);
					$image_path_sub = implode("/", $image_path_sub);
					//echo $image_path_sub;exit;
					$image_name = $old_record['sku'].'-'.changeImageName($_POST['product_title']).'-'.$_i.'.'.$extension;
					$image_path_sub = $image_path_sub.'/'.$image_name;
					if(file_exists($path.$sub['image']))
					{
						$file = rename($path.$sub['image'],$path.$image_path_sub);
					}
					else
					{
						$image_path_sub = $image_path;
					}
					//var_dump($file);exit;
					$db->db_exec("UPDATE oc_product_image set image='$image_path_sub' WHERE product_image_id='".$sub['product_image_id']."'");
					$_i++;
				}
			}
			$db->db_exec("DELETE FROM oc_product_to_category WHERE product_id='".$product_id."'");
			foreach($_POST['product_to_category'] as $k =>$val) {
				$db->db_exec("INSERT INTO oc_product_to_category SET product_id='".(int)$product_id."',category_id='".(int)$val."'");
			}
			if($_POST['canonical_url'])
			{
				$db->db_exec("DELETE FROM oc_url_alias WHERE query = 'product_id=" . (int)$product_id. "'");
				$db->db_exec("INSERT INTO oc_url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $db->func_escape_string($_POST['canonical_url']) . "'");
			}
		}

		if($_POST['location'])
		{
			$db->db_exec("DELETE FROM oc_location_stock where product_id='".(int)$product_id."'");
			foreach($_POST['location'] as $location )
			{
				$db->db_exec("INSERT INTO oc_location_stock set location_id='".$location."',product_id='".$product_id."',product_option_value_id=0,quantity=0");
			}
		}
		if ($log) {
			$log = 'Product updated for ' . linkToProduct($product_sku)."<br>" . $log;
			actionLog($log);
		}
		header("Location:" . $host_path . "product/$product_sku");
		exit;
	}
	$_query = "Select pc.user_id , u.name , pc.current_cost, pc.raw_cost , pc.ex_rate, pc.cost_date , 
	pc.shipping_fee, pc.vendor_code,pc.avg_cost from 
	oc_product p left join inv_product_costs pc on (p.sku = pc.sku) 
	left join inv_users u on (u.id = pc.user_id) where p.sku = '$product_sku' order by pc.id DESC";
	$product_costs = $db->func_query($_query);
	if (strlen($product['sku']) == 0) {
		$_SESSION['message'] = 'Product is not exist';
		header("Location:" . $host_path . "products.php");
		exit;
	}
	//$vendors = $db->func_query("select id,name,code from inv_vendors");
	$downgrade_data = $db->func_query("select sku , item_grade , price , quantity from oc_product where main_sku = '$product_sku'", "item_grade");
	$product_issues = $db->func_query("select group_concat(id) as product_issue_id , item_issue , count(item_issue) as total , date_added from inv_product_issues where product_sku = '$product_sku' group by item_issue");
	$manufacturers = $db->func_query("select * from inv_manufacturer WHERE status=1 order by name asc");
	$sku_types = $db->func_query("SELECT * from inv_product_skus");
	$inv_data = getInventoryDetail($product_sku);
	
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Product Detail</title>
		<script type="text/javascript" src="<?php echo $host_path ?>/js/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo $host_path;?>ckeditor/ckeditor.js"></script>
		<script type="text/javascript" src="<?php echo $host_path ?>/fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo $host_path ?>/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		<link rel="stylesheet" type="text/css" href="<?php echo $host_path;?>include/xtable.css" media="screen" />
		<script type="text/javascript">
			jQuery(document).ready(function () {
				jQuery('.fancybox').fancybox({width: '400px', height: '200px', autoCenter: true, autoSize: false});
				jQuery('.fancybox2').fancybox({width: '500px', height: '500px', autoCenter: true, autoSize: false});
			});
			function allowInt (t) {
				var re = /^-?[0-9]+$/;
				var input = $(t).val();
				var valid = input.substring(0, input.length - 1);
				if (!re.test(input)) {
					$(t).val(valid);
				}
			}
			function allowFloat (t) {
				var input = $(t).val();
				var valid = input.substring(0, input.length - 1);
				if (isNaN(input) || input == valid+' ') {
					$(t).val(valid);
				}
			}
			function checkWhiteSpace (t) {
				if ($(t).val() == ' ') {
					$(t).val('');
				}
			}
			function changeWeight(obj)
			{
				var conversion = 'oz';
				var weight_oz = 0.0000;
				var weight_lb = 0.0000;
				if($(obj).attr('data-attr')=='oz')
				{
					var conversion = 'lb';
				}
				
				
				if(conversion =='oz')
				{
					var weight_lb = $('#weight_lb').val();
					
					if(weight_lb=='') weight_lb = 0.0000;
					weight_oz = parseFloat(weight_lb) * 16;
					weight_oz = weight_oz.toFixed(4);
					$('#weight_oz').val(weight_oz);
				}
				else
				{
					var weight_oz = $('#weight_oz').val();
					if(weight_oz=='') weight_oz = 0.0000;
					weight_lb = parseFloat(weight_oz) / 16;
					weight_lb = weight_lb.toFixed(4);
					$('#weight_lb').val(weight_lb);
				}
			}
			function updatePrice(type) {
				price = parseFloat(jQuery("#price_old_" + type).val());
				markdown = parseFloat(jQuery("#markdown_" + type).val());
				if (markdown > 0) {
					newPrice = price - ((price * markdown) / 100);
					newPrice = newPrice.toFixed(2);
				}
				else {
					newPrice = price;
					newPrice = newPrice.toFixed(2);
				}
				jQuery("#price_" + type).html(newPrice);
				jQuery("#price_new_" + type).val(newPrice);
			}
			function calculatePrice(node, cost, group_id, qty) {
				markup = parseFloat(jQuery(node).val());
				cost = parseFloat(cost);
				newPrice = cost + ((cost * markup) / 100);
				newPrice = newPrice.toFixed(2);
				tdNode = jQuery(node).parents();
				jQuery(tdNode).find(".costVal_" + group_id + "_" + qty).html(newPrice);
			}
			function getProductPrice(element, type) {
				jQuery(element).val('Please wait...');
				url = '<?php echo $host_path . 'getPrice.php'; ?>';
				jQuery.ajax({
					url: url,
					data: {market: type, product_sku: '<?php echo $product_sku; ?>'},
					success: function (data) {
						jQuery('#' + type).val(data);
					},
					complete: function () {
						jQuery(element).val('Get Price');
					}
				});
			}
			function updateProductPrice(element, type) {
				price = $('#' + type).val();
				jQuery(element).val('Please wait...');
				url = '<?php echo $host_path . 'updatePrice.php'; ?>';
				jQuery.ajax({
					url: url,
					data: {market: type, product_sku: '<?php echo $product_sku; ?>', product_price: price},
					success: function (data) {
						alert(data);
						alert('Price updated');
					},
					complete: function () {
						jQuery(element).val('Update Price');
					}
				});
			}
			function getPrices() {
				var markets = new Array('channel_advisor', 'channel_advisor1', 'channel_advisor2', 'bigcommerce', 'bigcommerce_retail', 'bonanza', 'wish');
				for (i = 0; i < markets.length; i++) {
					element = jQuery('#' + markets[i]).next();
					getProductPrice(element, markets[i]);
				}
			}
		//Adding to Category
		function AddThis(Obj) {
			var Value = $.trim($(Obj).parent().text());
			$(Obj).closest('div').hide(500);
			$('#category_box_2').append('<div class="odd" style="background-color:#dbeec4"><input type="checkbox" onchange="RemoveThis(this)" value="'+Obj.value+'" checked> '+Value+'</div>');
		}
		function RemoveThis(Obj) {
			var Value = $(Obj).val();
			$checkbox = $("#category_box_1 input[value='"+Value+"']");
			$checkbox.attr('checked',false);
			$checkbox.closest('div').show(500);
			$(Obj).closest('div').hide(500);
		}
		function LoopThrough() {
			$("#category_box_1 input").each(function(index, element) {
				if($(element).is(':checked'))
				{
					$(element).closest('div').css('display','none');
					AddThis(element); 
				}
			});
		}
		function LoopThroughOut() {
			$("#category_box_2 input").each(function(index, element) {
				if($(element).is(':checked') && $(element).closest('div').is(':visible') )
				{
					$(element).closest('div').css('display','none');
					RemoveThis(element);  
				}
			});
		}
		$(document).ready(function(e) {
			LoopThrough();
		});
	</script>
	<style type="text/css">
	.tag a{
		color:#FFF !important;
	}
		.hiddenOverFlow:hover {overflow-y: scroll !important;}
		.even-class{
			background-color:#C7C3C3;	
		}
		.odd-class{
			background-color:#FFFFFF;	
		}
		.sub_images
		{
			margin: 0;
			padding: 0;
			list-style-type: none;
			text-align: center;
		}
		.sub_images li { display: inline; }
		.sub_images li a
		{
			text-decoration: none;
			padding: .2em 1em;
			color: #fff;
			background-color: #036;
		}
		.sub_images li a:hover
		{
			color: #fff;
			background-color: #369;
		}
	</style>
	<style type="text/css">
		.main_image {
			display: inline-block;
			position: relative;
			min-height: 100px;
		}
		#table1 tr select.multiple {
			min-height: 500px;
		}
		.main_image a {
			display: block;
			width: 500px;
		}
		.upMain {
			position: relative;
		}
		.ui.blue.button.upMain {
			display: inline-block;
		}
		.subImages {
			position: relative;
			margin-top: 20px;
			text-align: center;
		}
		.subImages thead {
			height: 50px;
			background-color: #f9f9f9;
		}
		.subImage a {
			display: block;
			padding: 20px;
		}
		.subImage input[type=text] {
			border: #ccc 1px solid;
			padding: 5px;
			width: 90%;
			border-radius: 3px;
		}
		.subImage input[type=text]:active, .subImage input[type=text]:hover, .subImage input[type=text]:focus {
			box-shadow: 0px 0px 3px 0px #999;
		}
		.subImage .removeImage {
			display:inline-block;
			background: #f00;
			width: 15px;
			line-height: 15px;
			font-size: 10px;
			color: #fff;
			padding: 5px;
			margin: 0px;
			border-radius: 100%;
			border: none;
			text-align: center;
		}
	</style>
	<!-- Adding Product Clasification -->
	<script>
		$(document).ready(function (e) {
			$('.fancybox3').fancybox({width: '980px', 'height': 800, autoCenter: true, autoSize: false});
		});
		function populateDevice($i)
		{
			$('#div_sku_type' + $i).hide();
			$('#div_model' + $i).html('');
			var manufacturers = $('#manufacturer' + $i).val();
			var product_id = $('#product_id' + $i).val();
			$.ajax({
				url: "../devices_new.php",
				type: "POST",
				data: {manufacturers: manufacturers, action: 'ajax', type: 'device', i: $i, product_id: product_id},
				success: function (data) {
					$('#div_device' + $i).html(data);
					populateModel($i);
				}
			});
		}
		function editDevice($i, $device_ids)
		{
			var manufacturers = $('#manufacturer' + $i).val();
			$.ajax({
				url: "../devices_new.php",
				type: "POST",
				data: {manufacturers: manufacturers, action: 'ajax', type: 'device', i: $i, device_ids: $device_ids},
				success: function (data) {
					$('#div_device' + $i).html(data);
				}
			});
		}
		function populateModel($i)
		{
			$('#div_sku_type' + $i).hide();
			var manufacturers = $('#manufacturer' + $i).val();
			var product_id = $('#product_id' + $i).val();
			var models = $('#device' + $i).val();
			var device_ids = $('#xdevice_id' + $i).val();
			$.ajax({
				url: "../devices_new.php",
				type: "POST",
				data: {models: models, action: 'ajax', type: 'model', i: $i, device_ids: device_ids, manufacturer_ids: manufacturers, product_id: product_id},
				success: function (data) {
					$('#div_model' + $i).html(data);
					populateAttributes($i);
					$('#div_sku_type' + $i).show();
				}
			});
		}
		function editModel($i, $model_ids)
		{
			var models = $('#device' + $i).val();
			$.ajax({
				url: "../devices_new.php",
				type: "POST",
				data: {models: models, action: 'ajax', type: 'model', i: $i, model_ids: $model_ids},
				success: function (data) {
					$('#div_model' + $i).html(data);
					populateAttributes2($i);
				}
			});
		}
		function populateAttributes($i)
		{
			var classification_type = $('#classification' + $i).val();
			var attribs = $('#attrib_ids' + $i).val();
			var text_fields = $('#attrib_fields' + $i).val();
			$.ajax({
				url: "../devices_new.php",
				type: "POST",
				data: {classification_type: classification_type, action: 'ajax', type: 'attribs', i: $i, attribs: attribs, text_fields: text_fields},
				success: function (data) {
					$('#div_attribs' + $i).html(data);
				}
			});
		}
		function populateAttributes2($i)
		{
			var sku_type = $('#sku_type' + $i).val();
			var attribs = $('#attrib_ids' + $i).val();
			var text_fields = $('#attrib_fields' + $i).val();
            //var model_ids = $('#model'+$i).val();
            $.ajax({
            	url: "../devices_new.php",
            	type: "POST",
            	data: {sku_type: sku_type, action: 'ajax', type: 'attribs', i: $i, model_id: $('#model' + $i).val(), attribs: attribs, text_fields: text_fields, model_ids: model_ids},
            	success: function (data) {
            		$('#div_attribs' + $i).html(data);
            	}
            });
          }
          function submitThis(i, device_ids, model_ids)
          {
          	opt = (typeof opt === 'undefined') ? '' : opt;
          	var checked1 = []
          	var checked2 = [];
          	var index = 0;
          	$('#tr_' + i + ' input[name=attrib\\[\\]]').each(function (index)
          	{
          		if ($(this).prop('checked') == true)
          		{
          			checked1.push(parseInt($(this).val()));
          			checked2.push($('input[name=text_value\\[\\]]:eq(' + index + ')').val());
          		}
          		index++;
          	});
          	var classification = $('#classification' + i).val();
          	var manufacturer = $('#manufacturer' + i).val();
          	var device = $('#device' + i).val();
          	var model = $('#model' + i).val()
          	if (!device)
          	{
          		device = device_ids.split(",");
          	}
          	if (!model)
          	{
          		model = model_ids.split(",");
          	}
          	if (!manufacturer)
          	{
          		alert('Please select manufacturer');
          		return false;
          	}
          	if (!device)
          	{
          		alert('Please select device');
          		return false;
          	}
          	if (!model)
          	{
          		alert('Please select model');
          		return false;
          	}
          	$.ajax({
          		url: "../devices_new.php",
          		type: "POST",
          		data: {sku: encodeURIComponent($('#sku' + i).val()), name: encodeURIComponent($('#name' + i).val()), classification: classification, manufacturer: manufacturer, device: device, model: model, attrib: checked1, text_value: checked2, add: 'save'},
          		success: function (data) {
          			if (opt == '')
          			{
          				alert(data);
          			}
                        //location.reload(true);
                      }
                    });
          }
          function verifyThis(device_product_id, opt)
          {
          	opt = (typeof opt === 'undefined') ? '' : opt;
          	$.ajax({
          		url: "../devices_new.php",
          		type: "POST",
          		data: {device_product_id: device_product_id, action: 'ajax', type: 'verify'},
          		success: function (data) {
          			if (opt == '')
          			{
          				alert(data);
          				location.reload();
          			}
          		}
          	});
          }
          function toggleCheck(obj)
          {
          	$('.checkboxes').prop('checked', obj.checked);
          	traverseCheckboxes();
          }
          function traverseCheckboxes()
          {
          	var Val = '';
          	$('.checkboxes').each(function (index, element) {
          		$(element).parent().parent().removeClass('highlight');
          		if ($(element).is(":checked"))
          		{
          			Val += $(element).val() + ',';
          			$(element).parent().parent().addClass('highlight');
          		}
          	});
          	$('#selected_items').val(Val);
          }
          function mapSelected()
          {
          	var $items = $('#selected_items').val();
          	if ($items == '')
          	{
          		alert('Please select atleast 1 device before mapping');
          		return false;
          	}
          	$('a#map-selected-anchor').attr('href', 'map_device.php?items=' + $items);
          	$('a#map-selected-anchor').click();
          }
        </script>
        <!-- Adding Product Clasification -->
      </head>
      <body>
      	<div align="center" <?php echo (isset($_GET['is_popup'])?'style="display:none"':'');?>> 
      		<?php include_once 'inc/header.php'; ?>
      	</div>
      	<?php if ($_SESSION['message']): ?>
      		<div align="center"><br />
      			<font color="red"><?php
      				echo $_SESSION['message'];
      				unset($_SESSION['message']);
      				?><br /></font>
      			</div>
      		<?php endif; ?>
      		<?php $outStockHistory = $db->func_query('SELECT * FROM `inv_product_inout_stocks` WHERE `product_sku` = "' . $product_sku . '" ORDER BY `id` DESC'); ?>
      		<div align="center">
      			<div class="tabMenu"  <?php echo (isset($_GET['is_popup'])?'style="display:none"':'');?>>
      				<?php if($_SESSION['product_description']) { ?> <input type="button" class="toogleTab" data-tab="tabDetails" value="Product Information"> <?php } ?>
      				<input type="button" class="toogleTab" data-tab="tabImages" value="Images">
      				<input type="button" class="toogleTab" data-tab="tabCost" value="Cost">
      				

      				<input type="button" class="toogleTab" data-tab="tabSaleHistory" value="Sale History">
      				<?php if ($outStockHistory) { ?> <input type="button" class="toogleTab" data-tab="tabStockHistory" value="Stock Data"> <?php } ?>
      				<input type="button" class="toogleTab" data-tab="tabSummary" value="Return Data">
      				<?php if($_SESSION['product_competitive_pricing']) { ?> <input type="button" class="toogleTab" data-tab="tabThirdParty" value="Competitor Pricing"> <?php } ?>
      				<?php if (isset($product_issues) and count($product_issues) > 0) { ?> <input type="button" class="toogleTab" data-tab="tabProductIssue" value="Product Issue"> <?php } ?>
      				<input type="button" class="toogleTab" data-tab="tabRJNTR" value="Shipments">
      				<input type="button" class="toogleTab" data-tab="tabVendorPO" value="Vendor PO">
      				<input type="button" class="toogleTab" data-tab="tabLedger" id='tab_ledger' value="Item Ledger"> 
      				<input type="button" class="toogleTab" data-tab="tabClass" value="Classification">
      				<?php if ($_SESSION['edit_vendor']): ?>
      					<input type="button" class="toogleTab" data-tab="tabVendor" value="Vendor">
      				<?php endif; ?>
      				<!-- <input type="button" class="toogleTab" data-tab="tabNTR" value="NTR">
      				<input type="button" class="toogleTab" data-tab="tabRJ" value="RJ"> -->
      				<!-- <input type="button" class="toogleTab" data-tab="tabRepair" id='tab_repair' value="Repair Guide"> -->
      				<!-- <input type="button" class="toogleTab" data-tab="tabQuestions" id='tab_questions' value="Product Q/A"> -->
      			</div>
      			<form method="post" action="">
      				<div class="tabHolder">
      					<h3 align="center"><?php echo $product['sku']; ?></h3><br>
      					<h3 align="center"><?php echo $product['name']; ?></h3>

      					<?php
      						if ($_SESSION['product_qty_update']) 
      						{
      								
      									?>
      										
      										<a href="<?php echo $host_path;?>/popupfiles/add_remove_inventory.php?sku=<?php echo $product['sku'];?>" style="display:inline-block;margin-top:5px;margin-bottom:5px" class="button fancybox2 fancybox.iframe">Add/Remove Inventory</a> <a href="<?php echo $host_path;?>/popupfiles/cycle_count.php?sku=<?php echo $product['sku'];?>" style="display:inline-block;margin-top:5px;margin-bottom:5px" class="button button-danger fancybox2 fancybox.iframe">Cycle Count</a>
      									<?php
      								
      						}

      					?>
      					<div >
													<strong style="font-size:14px"> On Shelf: <?php echo $inv_data['on_shelf'];?> </strong> <br>
													<strong style="font-size:14px"> Available for Sale: <?php echo $inv_data['available'];?> </strong>
													</div>
      					
      					<div id="loading">
      						<h2>Loading...</h2>
      					</div>
      					<div id="tabImages" class="makeTabs">
      						<table width="80%">
      							<tbody>
      								<tr>
      									<td width="100%">	
      										<div>
      											<h3 align="center"><?php echo $product['sku'] ; ?></h3>
      											<p align="center">Qty: <?php echo $product['quantity']; ?></p>
      											<br />
      											<div align="center">
      												<div class="main_image">
      													<?php if ($product['image']) { ?>
      													<a class="fancybox2 fancybox.iframe" href="<?= noImage($product['image'], $host_path, $path,0); ?>" target="_blank">
      														<img width="100%" src="<?= noImage($product['image'], $host_path, $path,0); ?>" alt="<?php echo $product['sku']; ?>" />
      													</a><br />
      													<!-- <a class="blue button removeImage" href="javascript:void(0);" onclick="removeImage('<?= $product['product_id']; ?>','main', this);">x</a> -->
      													<?php } ?>
      													<label class="ui blue button upMain" style="color: #fff;" for="mainimageup" onclick="">
      														<input type="file" style="opacity: 0; position: absolute; width: 100%; height: 23px; top: 0; left: 0;" onchange="uploadFile(this, 'main', '<?= $product['product_id']; ?>')" name="image" id="mainimageup" accept="image/jpeg,image/png">
      														Upload Main Image
      													</label>
      												</div>
      												<br />
      												<div class="subImages">
      													<table width="100%" cellpadding="10" border="1">
      														<thead>
      															<tr>
      																<th width="20%">
      																	Image
      																</th>
      																<th width="50%">
      																	Title
      																</th>
      																<th width="10%">
      																	Sort
      																</th>
      																<th width="15%">
      																	Remove / <a href="javascript:void(0);" onclick="addNewImage()">Add New</a>
      																</th>
      															</tr>
      														</thead>
      														<tbody>
      															<?php $sub_images = $db->func_query("SELECT * FROM oc_product_image WHERE product_id='".$product['product_id']."'"); ?>
      															<?php foreach($sub_images as $sub_image) { ?>
      															<tr class="subImage">
      																<td align="center">
      																	<a class="fancybox2 fancybox.iframe" href="<?= noImage($sub_image['image'], $host_path, $path,0); ?>" target="_blank">
      																		<img width="100%" src="<?= noImage($sub_image['image'], $host_path, $path,0); ?>" alt="" />
      																	</a>
      																	<label class="ui blue button upMain" style="color: #fff;" for="mainimageup">
      																		<input onchange="validateFileUp(this);" type="file" style="opacity: 0; position: absolute; width: 100%; height: 23px; top: 0; left: 0;" name="upFile[<?= $sub_image['product_image_id']; ?>]" accept="image/jpeg,image/png">
      																		Upload New
      																	</label>
      																</td>
      																<td>
      																	<input type="text" name="altimg[<?= $sub_image['product_image_id']; ?>]" value="<?= $sub_image['altimg']; ?>" placeholder="Enter Title">
      																</td>
      																<td>
      																	<input type="text" name="sort_order[<?= $sub_image['product_image_id']; ?>]" value="<?= $sub_image['sort_order']; ?>" placeholder="Sort">
      																</td>
      																<td align="center">
      																	<a class="removeImage" href="javascript:void(0);" onclick="removeImage('<?= $sub_image['product_image_id']; ?>', 'sub', this);">x</a>
      																</td>
      															</tr>
      															<?php } ?>
      														</tbody>
      													</table>
      												</div>
      												<br><br>
      												<input type="button" class="ui blue button" onclick="uploadSubFile('<?= $product['product_id']; ?>')" name="Uplaod" id="subimageup" value="Upload Sub Images">
      											</div>	
      										</div> 	  	
      									</td>
      								</tr>
      								<script type="text/javascript">
      									function removeImage (id, action, t) {
      										if(!confirm('Are you sure want to remove Image?'))
      										{
      											return false;
      										}
      										$.ajax({
      											url: 'product.php',
      											type: 'POST',
      											dataType: 'json',
      											data: {'id': id, 'action': 'removeImage', 'type': action},
      											success: function(json){
      												if (action == 'main') {
      													window.location.reload();
      												} else {
      													$(t).parent().parent().remove();
      												}
      											}
      										});
      									}
      									function uploadFile (e, action, product_id) {
      										var file = $(e).val().split(".");
      										var ext = file.pop();
      										var allowed = ['png', 'jpeg', 'jpg'];
      										if ($.inArray(ext, allowed) >= 0) {
      											var formData = new FormData();
      											formData.append('file', $(e)[0].files[0]);
      											formData.append('id', product_id);
      											formData.append('action', 'uploadFile');
      											formData.append('type', action);
      											$.ajax({
      												url: "product.php",
      												type: "POST",
      												data:  formData,
      												dataType: "json",
      												contentType: false,
      												cache: false,
      												processData:false,
      												success: function(json){
      													if (json['success']) {
      														window.location.reload();
      													}
      													if (json['error']) {
      														alert(json['msg']);
      													}
      												},
      												error: function(){}           
      											});
      										} else {
      											alert('This File is not Allowed');
      										}
      									}
      									function validateFileUp (t) {
      										var file = $(t).val().split(".");
      										var ext = file.pop();
      										var allowed = ['png', 'jpeg', 'jpg'];
      										if ($.inArray(ext, allowed) >= 0) {
      											if ($(t)[0].files[0]) {
      												var reader = new FileReader();
      												var src = '';
      												reader.onload = function (e) {
      													src = e.target.result;
      													var data = '<a class="fancybox2 fancybox.iframe" href="'+ src +'" target="_blank">'
      													+ '<img width="100%" src="'+ src +'" alt="" />'
      													+ '</a>';
      													$(t).parent().parent().find('a').remove();
      													$(t).parent().parent().prepend(data);
      												}
      												reader.readAsDataURL($(t)[0].files[0]);
      											}
      										} else {
      											alert('This File is not Allowed');
      										}
      									}
      									function addNewImage () {
      										var data = '<tr class="subImage">'
      										+ '<td align="center">'
      										+ '<label class="ui blue button upMain" style="color: #fff;" for="mainimageup">'
      										+ '<input onChange="validateFileUp(this);" type="file" style="opacity: 0; position: absolute; width: 100%; height: 23px; top: 0; left: 0;" name="newFile[]" accept="image/jpeg,image/png">'
      										+ 'Upload New'
      										+ '</label>'
      										+ '</td>'
      										+ '<td>'
      										+ '<input type="text" name="newaltimg[]" value="" placeholder="Enter Title">'
      										+ '</td>'
      										+ '<td>'
      										+ '<input type="text" name="newsort_order[]" value="" placeholder="Sort">'
      										+ '</td>'
      										+ '<td align="center">'
      										+ '<a class="removeImage" href="javascript:void(0);" onclick="$(this).parent().parent().remove();">x</a>'
      										+ '</td>'
      										+ '</tr>';
      										$('.subImages tbody').append(data);
      									}
      									function uploadSubFile (product_id) {
      										var formData = new FormData();
      										$('.subImages input[type=file]').each(function() {
      											formData.append($(this).attr('name'), $(this)[0].files[0]);
      										});
      										$('.subImages input[type=text]').each(function() {
      											formData.append($(this).attr('name'), $(this).val());
      										});
      										formData.append('id', product_id);
      										formData.append('action', 'uploadSubFile');
      										$.ajax({
      											url: "product.php",
      											type: "POST",
      											data:  formData,
      											dataType: "json",
      											contentType: false,
      											cache: false,
      											processData:false,
      											success: function(json){
      												if (json['success']) {
      													window.location.reload();
      												}
      												if (json['error']) {
      													alert(json['msg']);
      													window.location.reload();
      												}
      											},
      											error: function(){}           
      										});
      									}
      								</script>
      							</tbody>
      						</table>
      					</div>
      					<input type="hidden" id="is_csv_added" name="is_csv_added" value="<?php echo  $product['is_csv_added'];?>">
      					<input type="hidden" id="is_shopify" name="is_shopify" value="<?php echo  $product['is_shopify'];?>">

      					<input type="hidden" id="is_ebay" name="is_ebay" value="<?php echo  $product['is_ebay'];?>">

      					<input type="hidden" id="discontinue" name="discontinue" value="<?php echo  $product['discontinue'];?>">
      					<input type="hidden" id="is_shopify_uploaded" name="is_shopify_uploaded" value="<?php echo  $product['is_shopify_uploaded'];?>">
      					<div id="tabCost" class="makeTabs">
      						<table width="80%">
      							<tbody>
      								<tr>
      									<td width="100%">
      										<?php if ($_SESSION['edit_cost']): ?>
      											<div style="float:left">
      												<table cellpadding="5" cellspacing="0" style="float:left;width:100%" >
      													<caption>Update Price</caption>
      													<tr>
      														<td>Raw Cost:</td>
      														<td><input type="text" name="raw_cost" value="<?php echo $product_costs[0]['raw_cost']; ?>" /></td>
      													</tr>
      													<tr>
      														<td>Ex. Rate:</td>
      														<td><input type="text" name="ex_rate" value="<?php echo $product_costs[0]['ex_rate']; ?>" /></td>
      													</tr>
      													<tr>
      														<td>Shipping Fee:</td>
      														<td><input type="text" name="shipping_fee" value="<?php echo $product_costs[0]['shipping_fee']; ?>" /></td>
      													</tr>
      													<tr>
      														<td align="center" colspan="2"><input type="radio" name="update_with" value="cost" /> Cost Only <input type="radio" name="update_with" value="price" /> Price Only <input type="radio" name="update_with" value="both" checked /> Update Cost &amp; Price <input type="button" class="button" value="Update" id="update_costing_btn" onclick="updateCosting()" /></td>
      													</tr>
      												</table>
      											</div>
      											<script>
      												function updateCosting() {
      													var raw_cost = $('input[name=raw_cost]').val();
      													var ex_rate = 	$('input[name=ex_rate]').val();
      													var shipping_fee = $('input[name=shipping_fee]').val();
      													var update_with = $('input[name=update_with]:checked').val();
      													var downgrades_a_sku =$('input[name^=downgrades]:eq(0)').val();
      													var downgrades_a_price =$('input[name^=downgrades]:eq(1)').val();
      													var downgrades_b_sku =$('input[name^=downgrades]:eq(2)').val();
      													var downgrades_b_price =$('input[name^=downgrades]:eq(3)').val();
      													var downgrades_c_sku =$('input[name^=downgrades]:eq(4)').val();
      													var downgrades_c_price =$('input[name^=downgrades]:eq(5)').val();
      													var url = '<?php echo $host_path; ?>product.php?sku=<?php echo $product_sku;?>';
      													if(raw_cost=='' || ex_rate=='' || shipping_fee=='')
      													{
      														alert('Please provide a valid information first');
      														return false;	
      													}
      													$('#update_costing_btn').attr('disabled','disabled');
      													$.ajax({
      														url: url,
      														type: "POST",
      														data: {action:"update_cost",raw_cost:raw_cost,ex_rate:ex_rate,shipping_fee:shipping_fee,update_with:update_with,downgrades_a_sku:downgrades_a_sku,downgrades_a_price:downgrades_a_price,downgrades_b_sku:downgrades_b_sku,downgrades_b_price:downgrades_b_price,downgrades_c_sku:downgrades_c_sku,downgrades_c_price:downgrades_c_price },
      														dataType: "json",
      														success: function (json) {
      															if (json['success'])
      															{
      																alert(json['success']);
      																location.reload(true);
      															}
      														},
      														complete: function () {
      														}
      													});
      												}
      											</script>
      										<?php endif; ?>	
      										<?php
									// Average True Cost
      										$avg_count = 1;
      										foreach ($product_costs as $avg_cost) {
      											if ($avg_cost['raw_cost']) {
      												if ($avg_count > 3)
      													break;
      												$average_true_cost += ($avg_cost['raw_cost'] + $avg_cost['shipping_fee']) / $avg_cost['ex_rate'];
      												$avg_count++;
      											}
      										}
      										$_average_true_cost  = $db->func_query_first_cell("SELECT cost FROM inv_avg_cost WHERE sku='".$product['sku']."'");
      										?>
      										<table cellpadding="5" cellspacing="0" style="float:right">
      											<?php if ($_SESSION['display_cost'] and $avg_count - 1): ?>
      												<tr>
      													<td style="font-weight:bold">
      														Average True Cost / (<?php echo $avg_count - 1; ?> Entries):
      													</td>
      													<td style="font-weight:bold">$<?= number_format($average_true_cost / ($avg_count - 1), 2); ?></td>
      												</tr>
      												<?php if ($product['is_main_sku'] == '0'): ?>
      													<tr>
      														<td>
      															Sales Price: 
      														</td>
      														<td>
      															$<?php echo number_format($product['price'], 2); ?>
      														</td>
      													</tr>
      												<?php endif; ?>
      												<?php if (!$product['quantity'] && $outstock_date) { ?>
      												<tr>
      													<td colspan="2" align="center" style="color:red;">
      														<?php echo "Out Of Stock Since: <br/> " . americanDate($outstock_date); ?>
      													</td>
      												</tr>
      												<?php } ?>
      											<?php endif; ?>
      											<tr>
      												<td>
      													<label for="ignore_up">
      														<input id="ignore_up" <?= ($product['ignore_up'])? 'checked=""': ''; ?> type="checkbox" onchange="ignoreProduct(this, '<?= $product['product_id'] ?>');">Ignore product for price update
      													</label>
      													<!-- <strong>  Special Discount:</strong> <input type="text" name="special_discount" value="<?php echo $product['special_discount'];?>" style="width:60px" /> %-->
      												</td>
      											</tr>
      										</table>
      										<script type="text/javascript">
      											function ignoreProduct (t, product_id) {
      												var val = '0';
      												if ($(t).is(":checked")) {
      													val = '1';
      												}
      												$.ajax({
      													url: 'product.php',
      													type: 'POST',
      													dataType: 'json',
      													data: {'id': product_id, 'value': val, 'remove': 'remove'},
      													success: function(json){
      														if (json['success']) {
      															alert('Updated');
      														}
      													}
      												});
      											}
      										</script>
      										<br />
      										<?php $last_cost = 0; ?>
      										<div class="hiddenOverFlow" style="clear:both;max-height:300px; overflow-y: hidden; width:49%;float:left;min-height:300px">
      											<h2>Price History</h2>
      											<table width="100%" border="1" cellpadding="5" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
      												<tr style="background-color:#e5e5e5;">
      													<td>Date / User</td>
      													<?php if ($_SESSION['display_cost']): ?>
      														<td>Raw Cost</td>
      														<td>Ex Rate</td>
      														<td>Shipping Fee</td>
      														<td>Landed Cost</td>
      														<td>Avg Cost</td>
      													<?php endif; ?>		
      												</tr>
      												<?php if ($product_costs[0]['raw_cost']): $i = 0; ?>
      													<?php foreach ($product_costs as $cost): ?>
      														<tr>
      															<td>
      																<?php
      																$_query = "select s.id, s.`date_received`, s.`shipping_cost` from inv_shipments s left join inv_shipment_items si on (s.id = si.shipment_id) 
      																where date_completed like '%" . date("Y-m-d", strtotime($cost['cost_date'])) . "%' and si.product_sku = '$product_sku'
      																order by date_completed DESC";
      																$shipment = $db->func_query_first($_query);
      																?>
      																<?php if ($shipment): ?>
      																	<a href="<?php echo $host_path; ?>view_shipment.php?shipment_id=<?php echo $shipment['id']; ?>">
      																		<?php echo americanDate($shipment['date_received']) ?>
      																	</a>
      																<?php else: ?>
      																	<?php
      																	if($cost['user_id'])
      																	{
      																		echo americanDate($cost['cost_date']).' / '.get_username($cost['user_id']);
      																	}
      																	else
      																	{
      																	 echo americanDate($cost['cost_date']);
      																	}
      																	?>
      																<?php endif; ?>	 	   
      															</td>
      															<?php if ($_SESSION['display_cost']): ?>
      																<td><?php echo $cost['raw_cost'] ?></td>
      																<td><?php echo ($cost['ex_rate'] > 0) ? $cost['ex_rate'] : ''; ?></td>
      																<td><?php echo $cost['shipping_fee'] ?></td>
      																<td>$<?php echo $current_cost = number_format(($cost['raw_cost'] + $cost['shipping_fee']) / $cost['ex_rate'], 2); ?></td>
      																<!-- <td>$<?php echo (float)$_average_true_cost;?></td> -->
      																<td><?php echo ($cost['avg_cost']>0.00?'$'.number_format($cost['avg_cost'],2):'N/A');?>
      																
      															<?php endif; ?>		
      														</tr>
      														<?php
      														if ($i == 0) {
      															$last_cost = $current_cost;
      														} $i++; ?>
      													<?php endforeach; ?>
      												<?php endif; ?>			 	 	  	  	  	  
      											</table>
      										</div>
      										<div class="hiddenOverFlow" style="max-height:300px; overflow-y: hidden; width:49%;float:right;min-height:300px">
      											<h2>Price Change History</h2>
      											<table width="100%"  class=""   align="center" cellpadding="5" cellspacing="0" border="0" style="border:1px solid #585858;border-collapse:collapse;">
			<thead>
				<tr style="background-color:#e5e5e5;">
				<th>Date Updated</th>
				<th>User</th>
				<th>Price Type</th>
				<th>Old Price</th>
				<th>New Price</th>
				<th>Diff</th>


				</tr>
				</thead>
				<tbody>
				<?php
				$price_changes = $db->func_query("SELECT * FROM inv_product_price_change_history WHERE trim(lower(sku))='".strtolower(trim($product_sku))."' ORDER BY date_added DESC limit 50");

				foreach($price_changes as $price_change)
				{
					?>
					<tr>
					<td><?php echo americanDate($price_change['date_added']);?></td>
					<td><?php echo get_username($price_change['user_id']);?></td>
					<td><?php echo $price_change['price_type'];?></td>
					<td><?php echo '$'.number_format($price_change['old_price'],2);?></td>
					<td><?php echo '$'.number_format($price_change['new_price'],2);?></td>
					<td><?php echo number_format((($price_change['new_price']-$price_change['old_price'])/$price_change['old_price'])*100,2).'%';?></td>

					</tr>
					<?php
				}

				?>
				</tbody>
				</table>
      											</div>
      										<br style="clear:both" /><br />
      										<table width="100%" cellpadding="10" border="1" cellspacing="0" style="clear:both;border:1px solid #585858;border-collapse:collapse;">
      											<tr>
      												<td valign="top">
      													<?php
      													$kit_check = $db->func_query_first("SELECT sku,is_kit,price,quantity FROM oc_product WHERE sku='" . $product['sku'] . "K' AND is_kit=1");
      													if ($kit_check):
      														?>
      													<h2>Kit</h2>
      													<table border="1" cellpadding="5" width="100%" cellspacing="0" style="margin-bottom:5px">
      														<tr>
      															<td style="font-weight:bold">SKU -- Qty:</td>
      															<td><?php echo $kit_check['sku'] . "--" . $kit_check['quantity']; ?></td>
      														</tr>
      														<tr>
      															<td style="font-weight:bold">Price:</td>			
      															<td>
      																<input type="text" size="10" name="kit_price" value="<?php echo $kit_check['price']; ?>" />
      																<input type="hidden" name="kit_sku" value="<?= $kit_check['sku']; ?>" />
      															</td>
      														</tr>
      													</table>
      													<?php
      													endif;
      													?>
      													<?php $downGrades = array('Grade A' => 'Grade A', 'Grade B' => 'Grade B', 'Grade C' => 'Grade C'); ?>
      													<?php if ($product['is_main_sku'] == '1'): ?>
      														<h2>Grades</h2>
      														<table border="1" cellpadding="5" cellspacing="0" width="100%">
      															<?php foreach ($downGrades as $key => $value): ?>
      																<tr>
      																	<td><?php echo $key . "--" . $downgrade_data[$value]['quantity']; ?></td>
      																	<td><input type="text" size="15" name="downgrades[<?php echo $value; ?>][sku]" value="<?php echo $downgrade_data[$value]['sku']; ?>" /></td>
      																	<?php if ($_SESSION['product_pricing']): ?>
      																		<td><input type="text" size="10" name="downgrades[<?php echo $value; ?>][price]" value="<?php echo number_format($downgrade_data[$value]['price'], 2); ?>" /></td>
      																	<?php endif; ?>
      																</tr>
      															<?php endforeach; ?>		 
      														</table>
      													<?php endif; ?> 
      												</td>
      												<td align="left">
      													<?php // if($product['is_main_sku'] == '1'):  ?>
      													<?php $customer_groups = $db->func_query("select g.customer_group_id , gd.name from oc_customer_group g inner join oc_customer_group_description gd on (g.customer_group_id = gd.customer_group_id) where gd.name in ('Default','Local','Wholesale Small','Silver','Gold','Platinum','Diamond') order by field(gd.name,'Default','Local','Wholesale Small','Silver','Gold','Platinum','Diamond') limit 7"); ?>
      													<?php if ($_SESSION['product_pricing']): ?>
      														<table border="1" cellpadding="5" cellspacing="0" width="100%">
      															<tr>
      																
      																	<td style="font-weight:bold">Default Price:</td>
      																	<td><input required="" onkeyup="allowFloat(this);" style="width: 50px;" type="text" name="price" value="<?=$product['price'];?>"/>
      																	<br>
      																	<?php
      																	$p = $product['price'];
      																	//$c = $product_costs[0]['raw_cost'];
      																	$c = ($product_costs[0]['raw_cost'] + $product_costs[0]['shipping_fee']) / $product_costs[0]['ex_rate'];
      																	$numerator = $p-$c;
      																	echo '<strong>'.number_format(($numerator/$c)*100,2).'%</strong>';
      																	?>
      																	</td>
      																	<td style="font-weight:bold">Sale Price:</td>
      																	<td align="center"><input onkeyup="allowFloat(this);" style="width: 50px;" type="text" name="sale_price" value="<?=$product['sale_price'];?>"/>
      																	<br>
      																	<?php
      																	$p = $product['sale_price'];
      																	//$c = $product_costs[0]['raw_cost'];
      																	$c = ($product_costs[0]['raw_cost'] + $product_costs[0]['shipping_fee']) / $product_costs[0]['ex_rate'];
      																	$numerator = $p-$c;
      																	echo '<strong>'.number_format(($numerator/$c)*100,2).'%</strong>';
      																	?>
      																	</td>
      																	<td style="font-weight:bold">Bulk Price:</td>
      																	<td><input onkeyup="allowFloat(this);" style="width: 50px;" type="text" name="bulk_price" value="<?=$product['bulk_price'];?>"/></td>
      																
      																
      																	<td style="font-weight:bold">Shopify Price:</td>
      																	<td><input onkeyup="allowFloat(this);" style="width: 50px;" type="text" name="shopify_price" value="<?=$product['shopify_price'];?>"/></td>
      																	<td style="font-weight:bold">Shopify Compare At Price:</td>
      																	<td><input onkeyup="allowFloat(this);" style="width: 50px;" type="text" name="shopify_compare_price" value="<?=$product['shopify_compare_price'];?>"/></td>
      																
      															</tr>
      														</table>
      														<table border="1" cellpadding="5" cellspacing="0" width="100%">
      															<tr>
      																<th>Customer Group</th>
      																<th colspan="2">QTY 1</th>
      																<th colspan="2">QTY 3</th>
      																<th colspan="2">QTY 10</th>
      															</tr>
      															<?php //if($product['pricing_rule'] == 'Manual'): Commented by Zami ?>
      															<?php $count=1; ?>
      															<?php foreach ($customer_groups as $group): ?>
      																<?php
      																if($group['name']=='Wholesale Small')
      																{
      																	$group['name'] = 'Bronze';
      																}
      																?>
      																<tr>
      																	<td><?php echo $group['name']; ?></td>
      																	<?php if($count==1){ ?>
      																	<td align="center" colspan="2"><input type="text" name="discount_fixed[<?php echo $group['customer_group_id'] ?>][1]" value="<?php echo number_format($product['price'], 2) ?>" size="10" /></td>
      																	<?php $count=$count+1;?>
      																	<?php }?>
      																	<?php if($count>2){?>
      																	<td align="center" colspan="2"><input type="text" name="discount_fixed[<?php echo $group['customer_group_id'] ?>][1]" value="<?php echo number_format($customer_groups_data[$group['customer_group_id']][1]['price'], 2) ?>" size="10" /></td>
      																	<?php }?>
      																	<?php $count=$count+1;?>
      																	<td align="center" colspan="2"><input type="text" name="discount_fixed[<?php echo $group['customer_group_id'] ?>][3]" value="<?php echo number_format($customer_groups_data[$group['customer_group_id']][3]['price'], 2) ?>" size="10" /></td>
      																	<td align="center" colspan="2"><input type="text" name="discount_fixed[<?php echo $group['customer_group_id'] ?>][10]" value="<?php echo number_format($customer_groups_data[$group['customer_group_id']][10]['price'], 2) ?>" size="10" /></td>
      																</tr>
      															<?php endforeach; ?>
      														</table>
      													<?php endif;  ?>
      												</td>
      											</tr>	
      										</table>
      									</td>
      								</tr>
      							</tbody>
      						</table>
      					</div>
      					
      					<?php if($_SESSION['product_description']) { ?>
      					<div id="tabDetails" class="makeTabs">
      						<table width="80%">
      							<tbody>
      								<tr>
      									<td width="100%">
      										<table width="100%" cellpadding="10" border="0" cellspacing="0">
      											<tr>
      												<td width="15%"><strong>Title:</strong></td>
      												<td width="85%">
      													<input type="text" style="width:99%" name="product_title" onkeyup="checkWhiteSpace(this);" value="<?php echo $product['name'];?>" required />
      												</td>
      											</tr>
      											<tr>
      												<td><strong>Page Title:</strong></td>
      												<td>
      													<input type="text" style="width:99%" name="page_title" onkeyup="checkWhiteSpace(this);" value="<?php echo $product['title'];?>" />
      												</td>
      											</tr>
      											<tr>
      												<td><strong>Meta Keyword:</strong></td>
      												<td>
      													<input type="text" style="width:99%" name="meta_keyword" onkeyup="checkWhiteSpace(this);" value="<?php echo $product['meta_keyword'];?>" />
      												</td>
      											</tr>
      											<tr>
      												<td><strong>Meta Description:</strong></td>
      												<td>
      													<input type="text" style="width:99%" name="meta_description" onkeyup="checkWhiteSpace(this);" value="<?php echo $product['meta_description'];?>" />
      												</td>
      											</tr>
      											<tr>
      												<td><strong>Replacement For:</strong></td>
      												<td>
      													<input type="text" style="width:59%" name="replacement_for1" onkeyup="checkWhiteSpace(this);" value="<?php echo $product['replacement_for1'];?>" />
      												</td>
      											</tr>
      											<tr>
      												<td><strong>Replacement For:</strong></td>
      												<td>
      													<input type="text" style="width:59%" name="replacement_for2" onkeyup="checkWhiteSpace(this);" value="<?php echo $product['replacement_for2'];?>" />
      												</td>
      											</tr>
      											<tr>
      												<td width="10%"><strong>Youtube ID:</strong></td>
      												<td width="90%">
      													<input type="text" style="width:150px" name="video" onkeyup="checkWhiteSpace(this);" onchange="checkYoutubeId(this);" value="<?php echo $product['video'];?>" />
      													<br>
      													<span> (sample) See URL in top-right then you see black code after v= *** http://www.youtube.com/watch?v=BA7fdSkp8ds enter code is BA7fdSkp8ds </span>
      													<script>
      														function checkYoutubeId (t) {
      															$.ajax({
      																url: 'product.php',
      																type: 'POST',
      																dataType: 'json',
      																data: {checkYoutubeId: $(t).val()},
      															})
      															.always(function(json) {
      																if (json['error']) {
      																	alert(json['msg']);
      																	$(t).val('');
      																}
      															});
      														}
      													</script>
      												</td>
      											</tr>
											<!-- <tr>
												<td width="10%"><strong>Classification:</strong></td>
												<td width="90%">
													<?php $classification = $db->func_query("SELECT * FROM inv_classification WHERE status=1 ORDER BY name"); ?>
													<select name="classification_id" style="width:200px">
														<option value="">Select Classifcation</option>
														<?php foreach ($classification as $class) { ?>
														<option value="<?php echo $class['id']; ?>" <?php if ($class['id'] == $product['classification_id']) echo 'selected'; ?>><?= $class['name']; ?></option>
														<?php } ?>
													</select>
												</td>
											</tr> -->
											<tr>
												<td width="10%"><strong>Vendor:</strong></td>
												<td width="90%">
													<?php $vendors = $db->func_query("SELECT * FROM inv_users WHERE status=1 and group_id=1 ORDER BY lower(name)"); ?>
													<select name="vendor" style="width:200px">
														<option value="">Select Vendor</option>
														<?php foreach ($vendors as $vendor) { ?>
														<option value="<?php echo strtolower($vendor['name']); ?>" <?php if (strtolower($vendor['name']) == strtolower($product['vendor'])) echo 'selected'; ?>><?= $vendor['name']; ?> </option>
														<?php } ?>
													</select>
												</td>
											</tr>
											<tr>
												<td><strong>Status:</strong></td>
												<td>
													<select name="product_status" style="width:150px">
														<option value="1" <?php echo ($product['status']==1?'selected':'');?>>Enable</option>
														<option value="0" <?php echo ($product['status']==0?'selected':'');?>>Disable</option>
													</select>
												</td>
											</tr>
											<tr>
												<td><strong>Show on Website:</strong></td>
												<td>
													<select name="product_visibility" style="width:150px">
														<option value="1" <?php echo ($product['visibility']==1?'selected':'');?>>Show</option>
														<option value="0" <?php echo (isset($product['visibility']) && $product['visibility']==0)?'selected':'';?>>Hide</option>
													</select>
												</td>
											</tr>
											<?php if($_SESSION['login_as'] == 'admin' || $_SESSION['export_to_shopify']) { ?>
											<tr>
												<td><strong>Sync to Shopify:</strong></td>
											<?php if ($product['is_shopify'] == '1') { ?>
												<td><input type="checkbox"  checked="checked" onchange="shopifyChecker()" id="shopifyCheck" ></td>
												<?php } else if ($product['is_shopify'] == '0'){ ?>
												<td><input type="checkbox" onchange="shopifyChecker()" id="shopifyCheck" ></td>
												<?php } ?>
											</tr>

											<tr>
												<td><strong>Sync to eBay:</strong></td>
											<?php if ($product['is_ebay'] == '1') { ?>
												<td><input type="checkbox"  checked="checked" onchange="eBayChecker()" id="ebayCheck" ></td>
												<?php } else if ($product['is_ebay'] == '0'){ ?>
												<td><input type="checkbox" onchange="eBayChecker()" id="ebayCheck" ></td>
												<?php } ?>
											</tr>


											<tr>
												<td><strong>Discontinue?:</strong></td>
											<?php if ($product['discontinue'] == '1') { ?>
												<td><input type="checkbox"  checked="checked" onchange="discontinueChecker()" id="discontinueCheck" ></td>
												<?php } else if ($product['discontinue'] == '0'){ ?>
												<td><input type="checkbox" onchange="discontinueChecker()" id="discontinueCheck" ></td>
												<?php } ?>
											</tr>
											<tr>
												<td><strong>Shopify Uploaded?</strong></td>
											<?php if ($product['is_shopify_uploaded'] == '1') { ?>
												<td><input type="checkbox"  checked="checked" onchange="shopifyUploadChecker()" id="shopifyUploadCheck" ></td>
												<?php } else if ($product['is_shopify_uploaded'] == '0'){ ?>
												<td><input type="checkbox" onchange="shopifyUploadChecker()" id="shopifyUploadCheck" ></td>
												<?php } ?>
											</tr>
											<?php } ?>
											<tr>
												<td><strong>Show on Top?</strong></td>
											<?php if ($product['show_on_top'] == '1') { ?>
												<td><input type="checkbox"  checked="checked" name="show_on_top" value="1" ></td>
												<?php } else if ($product['show_on_top'] == '0'){ ?>
												<td><input type="checkbox" name="show_on_top" value="1" ></td>
												<?php } ?>
											</tr>

											<tr>
												<td><strong>Is Blowout Product?</strong></td>
											<td><input type="checkbox" <?php echo ($product['is_blowout']?'checked':'');?> name="is_blowout" value="1"></td>
											</tr>
											<tr>
												<td><strong>Canonical URL:</strong></td>
												<td>
													<input type="text" style="width:150px" name="canonical_url" onkeyup="checkWhiteSpace(this);"  value="<?php echo $product['keyword'];?>" />
												</td>
											</tr>
											<tr>
												<td><strong>Location(s):</strong></td>
												<td>
													
												<select name="location[]" multiple="" style="width:59%">
												<?php
												$locations = $db->func_query("SELECT * FROM oc_location ORDER BY code");

												$_location = $db->func_query("SELECT location_id from oc_location_stock where product_id='".$product['product_id']."'");
												$_locations = array();
												foreach($_location as $_loc)
												{
													$_locations[] = $_loc['location_id'];
												}

												foreach($locations as $location)
												{
													?>
													<option  <?php echo (in_array($location['location_id'], $_locations)?'selected':'');?> value="<?php echo $location['location_id'];?>"><?php echo $location['code'];?></option>
													<?php
												}
												?>
												</select>
													
												</td>
											</tr>
											<tr>
												<td><strong>Weight:</strong></td>
												<td>
													<input type="text" style="width:50px" name="weight" id="weight_lb" onkeyup="checkWhiteSpace(this);changeWeight(this);" data-attr="lb"  value="<?php echo round($product['weight'],4);?>" /> lb <strong>OR</strong> <input type="text" style="width:50px" id="weight_oz" onkeyup="checkWhiteSpace(this);changeWeight(this);" data-attr="oz"  value="<?php echo round($product['weight_oz'],4);?>" /> oz
													<br>
													<span> (Enter lb OR oz, not both) </span>
												</td>
											</tr>
											<tr style="display: none">
												<td><strong>On Hand:</strong></td>
												<td>
												<div style="float: left">
													<?php if ($_SESSION['product_qty_update']) { ?>
													<input type="text" onkeyup="allowInt(this);" value="<?php echo $product['quantity']; ?>" name="quantity" />
													<?php } else { ?>
													<?=$product['quantity'];?>
													<?php } ?>
													</div>
													
												</td>
											</tr>
											<tr>
												<td><strong>Product Tags:</strong><br><small>(Seperated by Commas)</small></td>
												<td>
													
													<input type="text" value="<?php echo $product_tags; ?>" name="product_tags" style="width:59%" onkeyup="checkWhiteSpace(this);" />
													
												</td>
											</tr>


											<tr>
												<td colspan="2"><textarea name="product_description"><?php echo stripslashes($product['description']);?></textarea></td>
											</tr>

											<tr>
												<td colspan="2">
													<div class="hiddenOverFlow" style="max-height:300px; height: 300px; overflow-y: hidden; width:48%; display: inline-block;" id="category_box_1">
														<?php foreach(getCategories() as $category) { ?>
														<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
														<div class="<?php echo $class; ?>">
															<?php if (in_array($category['category_id'], $product_categories)) { ?>
															<input type="checkbox" name="product_to_category[]" value="<?php echo $category['category_id']; ?>" checked="checked" onChange="AddThis(this)" />
															<?php echo $category['name']; ?>
															<?php } else { ?>
															<input type="checkbox" name="product_to_category[]" value="<?php echo $category['category_id']; ?>" onChange="AddThis(this,'<?php echo $category['name'];?>')"  />
															<?php echo $category['name']; ?>
															<?php } ?>
														</div>
														<?php } ?>
													</div>
													<div class="hiddenOverFlow" style="max-height:300px; height: 300px; overflow-y: hidden; width:48%;  display: inline-block;" id="category_box_2">
													</div>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<script>
									CKEDITOR.replace( 'product_description' );
								</script>
							</tbody>
						</table>
					</div>
					<?php } ?>
					<div id="tabSummary" class="makeTabs">
					<h3>Return Data</h3>
						<table width="95%">
							<tbody>
								<tr>
									<td align="center" width="100%">
										<iframe src="<?php echo $host_path; ?>popupfiles/view_item_wise_summary.php?show_return=1&sku=<?php echo $product_sku; ?>&conditions=" style="border: 0px solid black; height: 600px; width: 1300px;"></iframe>
									</td>
								</tr>
							</tbody>
						</table><br>
					<!--<h3>RJ Data</h3>
					
						<?php $rjs = $db->func_query("SELECT `date_received`, `vendor`, `shipment_id`, `package_number`, `rejected_shipment_id`, `reject_reason`, `image` FROM inv_shipments s INNER JOIN inv_rejected_shipment_items st ON (s.id = st.shipment_id) WHERE fb_added != 1 AND product_sku = '$product_sku'"); ?>
						<table width="75%" cellpadding="5" cellspacing="0" border="1" align="center">
							<thead>
								<tr>
									<th>
										Date Received
									</th>
									<th>
										Vendor
									</th>
									<th>
										Shipment ID
									</th>
									<th>
										RJ Shipment ID
									</th>
									<th>
										Reason
									</th>
									<th>
										Image
									</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($rjs as $row) : ?>
									<tr>
										<td>
											<?php echo americanDate($row['date_received']); ?>
										</td>
										<td>
											<?php echo get_username($row['vendor']); ?>
										</td>
										<td>
											<?php echo linkToShipment($row['shipment_id'], $host_path, $row['package_number'], ' target="_blank" '); ?>
										</td>
										<td>
											<?php echo $db->func_query_first_cell("SELECT package_number FROM inv_rejected_shipments WHERE id = '" . $row['rejected_shipment_id'] . "'"); ?>
										</td>
										<td>
											<?php echo $row['reject_reason']; ?>
										</td>
										<td>
											<a class="fancybox-thumb" rel="fancybox-thumbrj" href="<?php echo $host_path; ?>files/<?php echo $row['image'];?>">
												<img src="<?php echo $host_path; ?>files/<?php echo $row['image'];?>" width="100" alt="">
											</a>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table><br>
						<h3>NTR Data</h3>
						<?php $ntrs = $db->func_query("SELECT `date_received`, `vendor`, `shipment_id`, `package_number`, `reason`, `image` FROM inv_shipments s INNER JOIN inv_return_shipment_box_items st ON (s.id = st.shipment_id) WHERE fb_added != 1 AND product_sku = '$product_sku'"); ?>
						<table width="75%" cellpadding="5" cellspacing="0" border="1" align="center">
							<thead>
								<tr>
									<th>
										Date Received
									</th>
									<th>
										Vendor
									</th>
									<th>
										Shipment ID
									</th>
									<th>
										Reason
									</th>
									<th>
										Image
									</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($ntrs as $row) : ?>
									<tr>
										<td>
											<?php echo americanDate($row['date_received']); ?>
										</td>
										<td>
											<?php echo get_username($row['vendor']); ?>
										</td>
										<td>
											<?php echo linkToShipment($row['shipment_id'], $host_path, $row['package_number'], ' target="_blank" '); ?>
										</td>
										<td>
											<?php echo $row['reason']; ?>
										</td>
										<td>
											<a class="fancybox-thumb" rel="fancybox-thumbntr" href="<?php echo $host_path; ?>files/<?php echo $row['image'];?>">
												<img src="<?php echo $host_path; ?>files/<?php echo $row['image'];?>" width="100" alt="">
											</a>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>-->
					<br>
					</div>
					<div id="tabVendorPO" class="makeTabs">
					<h3>Vendor PO</h3>
						<?php $vpos = $db->func_query("SELECT a.`id`,a.`vendor_po_id`, a.`vendor`, a.`status`, a.`date_added`,b.req_qty,b.qty_shipped FROM inv_vendor_po a  INNER JOIN inv_vendor_po_items b ON (a.vendor_po_id = b.vendor_po_id) WHERE b.sku = '$product_sku' order by a.date_added desc"); ?>
						<table width="75%" cellpadding="5" cellspacing="0" border="1" align="center">
							<thead>
								<tr>
									<th>
										Date Added
									</th>
									<th>
										Vendor
									</th>
									<th>
										Vendor PO ID
									</th>
									<th>
										Req / Shipped
									</th>
									<th>
										Status
									</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($vpos as $row) : ?>
									<tr>
										<td>
											<?php echo americanDate($row['date_added']); ?>
										</td>
										<td>
											<?php echo get_username($row['vendor']); ?>
										</td>
										<td>
											<?php echo linkToVPO($row['id'], $host_path, $row['vendor_po_id']); ?>
										</td>

										<td>
											<?php echo $row['req_qty'].'/'.$row['qty_shipped']; ?>
										</td>

										<td>
											<?php echo ucfirst(($row['status']=='shipped'?'completed':$row['status'])); ?>
										</td>
										
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table><br>
					</div>
					<div id="tabLedger" class="makeTabs">
						<!-- <table width="30%" border="1" cellpadding="5" cellspacing="0">
								<tr>
										<th width="45%">On Hand</th>
										<th width="45%"></th>
										<th width="10%"><?php echo $inv_data['on_hand'];?></th>
								</tr>
								<tr>
								<th>On Hold - Reserved </th>
								<th></th>
								<th><?php echo (int)$inv_data['on_hold'];?></th>
								</tr>
								
								<tr>
										<th>Allocated</th>
										<th></th>
										<th><?php echo (int)$inv_data['allocated']  ;?></th>
								</tr>
								
								<tr>
										<th></th>
										<th>Not Picked</th>
										<th><?php echo (int)$inv_data['not_picked'];?></th>
								</tr>
								<tr>
										<th></th>
										<th>Picked</th>
										<th><?php echo (int)$inv_data['picked'];?></th>
								</tr>
								<tr>
										<th></th>
										<th>Packed</th>
										<th><?php echo (int)$inv_data['packed'];?></th>
								</tr>
								<tr>
										<th>On Shelf</th>
										<th></th>
										<th><?php echo (int)$inv_data['on_hand'] - (int)$inv_data['picked'] - (int)$inv_data['packed'];?></th>
								</tr>
								<tr>
										<th>Available for Sale</th>
										<th></th>
										<th><?php echo (int)$inv_data['available'] ;?></th>
								</tr>
						</table> -->
							<table width="30%" border="1" cellpadding="8" cellspacing="0">
									<tr style="background-color: #D9D9D9">
											<td>On Hand</td>
											<td></td>
											<td></td>
											<td><?php echo (int)$inv_data['on_hand'];?></td>
									</tr>
									<tr style="background-color: #D9D9D9">
											<td>Pre-Filled</td>
											<td></td>
											<td></td>
											<td>
											<?php
											if($inv_data['prefill']>0)
											{
												$prefill_shipment_detail = $db->func_query_first("SELECT * FROM inv_shipments where id='".$inv_data['prefill_shipment']."'");
												if($prefill_shipment_detail)
												{


											 echo  linkToShipment($prefill_shipment_detail['id'], $host_path, $inv_data['prefill'], 'data-tooltip="'.$prefill_shipment_detail['package_number'].'"');
											}
											else
											{
												echo $inv_data['prefill'];
											}
											}
											else
											{
												echo $inv_data['prefill'];
											}
											?>
											 </td>
									</tr>
									<tr >
											<td></td>
											<td>On Shelf</td>
											<td><?php echo (int)$inv_data['on_hand'] - (int)$inv_data['picked'] - (int)$inv_data['packed'];?></td>
											<td></td>
									</tr>
									<tr >
											<td></td>
											<td>Adjustments</td>
											<td><?php echo (int)$inv_data['adjustment'];?></td>
											<td></td>
									</tr>
									<tr >
											<td></td>
											<td>Picked/Packed</td>
											<td><?php echo (int)$inv_data['picked'] + (int)$inv_data['packed'] ;?></td>
											<td></td>
									</tr>
									<tr style="background-color: #D9D9D9">
											<td>Allocated</td>
											<td></td>
											<td></td>
											<td><?php echo (int)$inv_data['allocated'];?></td>
									</tr>
									<tr >
											<td></td>
											<td>On Hold - Reserved</td>
											<td><?php echo (int)$inv_data['on_hold'] ;?></td>
											<td></td>
									</tr>
									<tr >
											<td></td>
											<td>Not Picked</td>
											<td><?php echo (int)$inv_data['not_picked'];?></td>
											<td></td>
									</tr>
									<tr >
											<td></td>
											<td>Picked</td>
											<td><?php echo (int)$inv_data['picked'] ;?></td>
											<td></td>
									</tr>
									<tr >
											<td></td>
											<td>Packed</td>
											<td><?php echo (int)$inv_data['packed'] ;?></td>
											<td></td>
									</tr>
									<tr style="background-color: #D9D9D9">
											<td>Available for Sale</td>
											<td></td>
											<td></td>
											<td><?php echo (int)$inv_data['available'];?></td>
									</tr>
							</table>
					
					<br><br>
					<strong>Warehouse Status Filter: <select id="ledger_filter" onchange="loadLedger();">
						<option value="">Show All</option>
						<option value="picked">Picked</option>	
						<option value="packed">Packed</option>	
						<option value="shipped">Shipped</option>	
						<option value="shipment">Shipment</option>	
						<option value="adjustment">Adjusted</option>	
					</select>
					</strong>
					<?php
					if($_SESSION['inventory_movement_report'])
					{
					?>
					<div style="text-align:right;margin-right:3%">
					<a class="button button-danger fancybox" href="#item_movement_csv">SKU Movement CSV</a>
					</div>
					<?php
				}
				?>
					<table  class="xtable" id="ledger_table"   align="center" cellpadding="0" cellspacing="0" border="0" style="width:95%;">
			<thead>
				<tr>
					<th width="10%">Order ID/Shipment No.</th>
				<th width="20%">Date / Time</th>
					
					
					<th width="21%">Event</th>
					<th width="2%">QTY</th>
					
					<!-- <th width="5%"><a  href="#">In Hand</th> -->
					<th width="10%">User</th>
					<!--<th width="10%" align="center" colspan="2">On Hand</th>
					
					<th colspan="4" align="center" width="20%">Allocated</th>
					<th width="7%">On Shelf</th>
					<th width="7%">Available</th>-->
					
				</tr>
				<!--
				<tr>
						<th colspan="5"> </th>
						<th style="background-color:#000;color:#FFF">On Shelf</th>
						<th>Adjustment</th>
						
						<th style="background-color:#000;color:#FFF" width="">On-Hold</th>
						<th style="background-color:#000;color:#FFF" width="">Not Picked</th>
						<th style="background-color:#000;color:#FFF" width="">Picked</th>
						<th style="background-color:#000;color:#FFF" width="">Packed</th>
						<th colspan="2"></th>
						
				</tr>-->
				<!-- </tr> -->
			</thead>
			<tbody>
				
				
				
			</tbody>
			
		</table>
					</div>
					<div id="tabRJNTR" class="makeTabs" >
						<table width="80%">
							<tbody>
								<tr>
									<td width="100%">
										<?php //$rj_ntrs = $db->func_query("SELECT * from inv_shipment_items si inner join inv_shipment_qc sq on (si.shipment_id = sq.shipment_id AND si.product_sku = sq.product_sku) where sq.product_sku = '$product_sku'  order by sq.date_modified desc ");
										$rj_ntrs = $db->func_query("SELECT shipment_id, sum(qty_shipped) as qty_shipped,sum(qty_received) as qty_received FROM inv_shipment_items WHERE product_sku='".$product_sku."'  group by product_sku, shipment_id order by shipment_id desc ");
										?>
										<table width="80%" cellpadding="5" cellspacing="0" border="1">
											<tr>
												<th>Date Completed</th>
												<th>Shipment #</th>
												<th>Vendor</th>
												<th>QTY Ordered</th>
												<th>QTY Received</th>
												<th>Rejected</th>
												<th>Reason</th>
												<th>NTR</th>
												<th>Reason</th>
												<th>Status</th>
											</tr>
											<?php
											if($rj_ntrs)
											{
												foreach($rj_ntrs as $_row)
												{
													$shipment_info = $db->func_query_first("SELECT * FROM inv_shipments WHERE id='".$_row['shipment_id']."' ");
													if(empty($shipment_info)){
														continue;
													}
													$shipment_qc = $db->func_query_first("SELECT * FROM inv_shipment_qc WHERE shipment_id='".$_row['shipment_id']."' and product_sku='".$product_sku."'")
													?>
													<tr>
														<td><?= americanDate($shipment_info['date_completed']); ?></td>
														<td>
															<a href="<?=$host_path;?>view_shipment.php?shipment_id=<?=$_row['shipment_id'];?>">
																<?= $shipment_info['package_number']; ?>
															</a>
														</td>
														<td><?php echo get_username($shipment_info['vendor']); ?></td>
														<td><?= $_row['qty_shipped']; ?></td>
														<td><?= $_row['qty_received']; ?></td>
														<td><?=(int)$shipment_qc['rejected'];?></td>
														<td><?=$shipment_qc['rejected_reason'];?></td>
														<td><?=$shipment_qc['ntr'];?></td>
														<td><?=$shipment_qc['ntr_reason'];?></td>
														<td><?=$shipment_info['status'];?></td>
													</tr>
													<?php
												}
											}
											else
											{
												?>
												<tr>
													<td colspan="5" align="center">No Record Found</td>
												</tr>
												<?php
											}
											?>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div id="tabRepair" class="makeTabs" >
						<div id='add_form'>
							<div >
								<label style="margin-right: 35px;">Step Name:</label><input placeholder="Step Name" type="text" id='step' />
							</div>
							<br>
							<div >
								<label style="margin-right: 61px;">Order :</label><input placeholder="Order Number " type="text" id='order_number' />
							</div>
							<br>
							<div>
								<label style="margin-right: 7px;">Step Description:</label><textarea id='description' type="text"></textarea>
							</div>
							<div style="margin-top: 15px;margin-left: 155px;">
								<label>File: </label><input id='step_image' type="file" name="image" />
							</div>
							<div  style="margin-top: 15px;margin-bottom: 25px;">
								<input value="Save" onClick="saveSteps()" type="button" id="save_repair_steps" />
							</div>
						</div>
						<div id="repair_table">
							<?php 
							$repair_guides = $db->func_query("select * from inv_product_repair_guide where product_sku = '".$product["sku"]."'");
							?>
							<table border="1" style="margin-bottom: 25px;">
								<thead>
									<th>ID</th>
									<th> Order </th>
									<th>Product Sku</th>
									<th>Product Title</th>
									<th>Step</th>
									<th>Description</th>
									<th>Image</th>
									<th>Action</th>
									<th>Action</th>
								</thead>
								<tbody>
									<?php 			foreach ($repair_guides as $key => $value) { ?>
									<tr>
										<td><?php echo $value['id'] ?> </td>
										<td><?php echo $value['order_number'] ?> </td>
										<td><?php echo $value['product_sku'] ?> </td>
										<td><?php echo $value['model']?> </td>
										<td><?php echo $value['step_name'] ?> </td>
										<td><?php echo $value['step_description'] ?> </td>
										<td><?php echo $value['image_path'] ?> </td>
										<td><button data-id='<?php echo $value['id'] ?>' data-step='<?php echo $value['step_name'] ?>' data-description='<?php echo $value['step_description']?>' data-order='<?php echo $value['order_number']?>' data-image='<?php echo $value['image_path']?>' type="button" onClick="editStep()"> Edit </button>    </td>
										<td><button  onClick="deleteStep('<?php echo $value['id'] ?>','<?php echo $value['product_sku'] ?>')" value="Delete" type="button"> Delete </button></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
							
						</div>
						
						<script type="text/javascript">
							function addMoreSteps()
							{
								$('#add_form').show();
								$('#repair_table').hide();
							}
							function saveSteps(id){
								var formData = new FormData();
								formData.append('image', $('#step_image')[0].files[0]);
								formData.append('step', $('#step').val());
								formData.append('description', $('#description').val());
								formData.append('product_sku','<?php echo $product["sku"]; ?>');
								formData.append('product_title','<?php echo $product["name"];  ?>');
								formData.append('order_number',$('#order_number').val());
								if(id)
								{
									formData.append('id',id);
								}
								$.ajax({
									url: '/imp/repair_guide.php',
									type: 'POST',
									data: formData,
									contentType: false,
									enctype: 'multipart/form-data',
									processData: false,
									success: function (response) {
										if(response != 'false')
										{
											$('#step').val('');
											$('#description').val('');
											$('#step_image').val('');
											$('#order_number').val('');
											$('#save_repair_steps').attr('onClick','saveSteps()');
											$('#repair_table').html(response);
											$('#repair_table').focus();
										}
										else
										{
											alert("some value is not filled properly");
										}
									}
								});
							}
							function editStep()
							{
								$('#step').focus();
								$('#description').val($(this.event.target).data('description'));
								$('#step').val($(this.event.target).data('step'));
								$('#order_number').val($(this.event.target).data('order'));
								var id = $(this.event.target).data('id')
								$('#save_repair_steps').attr('onClick','saveSteps("'+id+'")');
							}
							function deleteStep(id,p_id)
							{
								var formData = new FormData();
								formData.append('delete_id', id);
								formData.append('product_sku', p_id);
								$.ajax({
									url: '/imp/aneeb.php',
									type: 'POST',
									data: formData,
									contentType: false,
									enctype: 'multipart/form-data',
									processData: false,
									success: function (response) {
										$('#repair_table').html(response);
									}
								});
							}
						</script>
					</div>
					<div id="tabQuestions" class="makeTabs">
						<?php 
						$product_questions = $db->func_query("select * from oc_product_question where product_sku = '".$product["sku"]."'");
						?>
						<!-- display: none; -->
						<input type="hidden" id="question_id">
						<div id="product_answer" style="display: none;">
							<textarea type="text" style="" id="full_answer"></textarea> 
							<br><br>
							<button type="button" onclick="saveAnswer()" >Add</button>
						</div>
						<div id="q_table"> 
							<table  border="1" style="margin-bottom: 25px;margin-top: 25px;">
								<thead>
									<th>ID</th>
									<th> Product ID </th>
									<th>Product Sku</th>
									<th>Product Title</th>
									<th>Question</th>
									<th>Answer</th>
									<th>Date</th>
								</thead>
								<tbody>
									<?php 	foreach ($product_questions as $key => $value) { ?>
									<tr>
										<td><?php echo $value['id'] ?> </td>
										<td><?php echo $value['product_id'] ?> </td>
										<td><?php echo $value['product_sku'] ?> </td>
										<td><?php echo $value['product_title']?> </td>
										<td style="width: 220px;"><?php echo $value['question'] ?> </td>
										<?php if($value['answer']){ ?>
										<td style="width: 220px;"><?php echo $value['answer'] ?> </td>
										<?php } else { ?>
										<td><button type="button" onclick="addAnswer('<?php echo $value['id'] ?>')">Add Answer</button> </td>
										<?php }?>
										<td><?php echo $value['question_date'] ?> </td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<script type="text/javascript">
							function addAnswer(id)
							{
								$('#product_answer').show();
								$('#product_answer').focus();
								$('#question_id').val(id);
							}
							function saveAnswer()
							{
								var formData = new FormData();
								formData.append('answer', $('#full_answer').val());
								formData.append('product_sku','<?php echo $product["sku"]; ?>');
								formData.append('id',$('#question_id').val());
								$('#full_answer').val('');
								$.ajax({
									url: '/imp/questions.php',
									type: 'POST',
									data: formData,
									contentType: false,
									enctype: 'multipart/form-data',
									processData: false,
									success: function (response) {
										if(response != 'false')
										{
											$('#product_answer').hide();
											$('#q_table').html(response);
											$('#q_table').focus();
										}
										else
										{
											alert("some value is not filled properly");
										}
									}
								});
							}
						</script>
					</div>
					
					<div id="tabClass" class="makeTabs">
						<table border="1" width="98%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;" id="table1">
							<tr style="background-color:#e7e7e7;font-weight:bold">
								<td>Class</td>
								<td>Manufacturer</td>
								<td>Device</td>
								<td>Model / Sub Model</td>
								<td style="display:none">SKU Type</td>
								<td>Attributes</td>
								<td align="center">Action</td>
							</tr>
							<tr id="tr_0" class="list_items">
								<td>
									<input type="hidden" name="classification_id" value="<?php echo $product['classification_id']; ?>" />
									<select name="classification[]" id="classification0" onchange="populateDevice(0); populateModel(0); $('input[name=classification_id]').val($(this).val());">
										<option>Select Class</option>
										<?php
										$man_query1 = $db->func_query_first("SELECT * FROM inv_device_product WHERE sku='" . $product['sku'] . "'");
										foreach ($classification as $class) {
											$classSelect = $db->func_query_first("SELECT * FROM inv_device_class WHERE device_product_id='" . $man_query1['device_product_id'] . "' AND class_id='" . $class['id'] . "'");
											?>
											<option value="<?php echo $class['id']; ?>" <?= ($class['id'] == $product['classification_id']) ? 'selected="selected"' : ''; ?>>
												<?= $class['name']; ?>
											</option>
											<?php
										}
										?>
									</select>
								</td>
								<td>
									<select name="manufacturer[]" id="manufacturer0" multiple="multiple" class="multiple" onchange="populateDevice(0)">
										<?php
										$xmanu_id = '';
										$device_did = '';
										$device_did2 = '';
										$device_model = '';
										$device_model2 = '';
										foreach ($manufacturers as $manufacturer): ?>
										<?php $man_query2 = $db->func_query_first("SELECT * FROM inv_device_manufacturer WHERE device_product_id='" . $man_query1['device_product_id'] . "' AND manufacturer_id='" . $manufacturer['manufacturer_id'] . "'");
										?>
										<option value="<?php echo $manufacturer['manufacturer_id']; ?>" <?php
											if ($man_query2) {
												echo 'selected';
												$xmanu_id.=$man_query2['device_manufacturer_id'] . ',';
											}
											?>><?php echo $manufacturer['name']; ?></option>
										<?php endforeach; ?>
										<?php $xmanu_id = rtrim($xmanu_id, ","); ?>
									</select>
									<a href="javascript:void(0);" style="float:left" onclick="clearIt('0', 'manufacturer')">Clear </a>
									<input type="hidden" id="product_id0" value="<?php echo $man_query1['device_product_id']; ?>" />
									<input type="hidden" id="manufacturer_ids0" value="<?php echo $xmanu_id; ?>" />
								</td>
								<td>
									<div id="div_device0">
										<?php
										if ($xmanu_id) {
											$man_query3 = $db->func_query("SELECT * FROM inv_device_device WHERE device_manufacturer_id IN ($xmanu_id)");
											foreach ($man_query3 as $query) {
												$device_did.=$query['device_device_id'] . ',';
												$device_did2.=$query['device_id'] . ',';
												echo getResult("SELECT device FROM inv_model_mt WHERE model_id='" . $query['device_id'] . "'") . "<br>";
											}
											$device_did = rtrim($device_did, ",");
											?>
											<?php
										}
										?>
									</div>
									<a href="javascript:void(0);" style="float:left" onclick="clearIt('0', 'device')">Clear </a>
									<a href="javascript:void(0);" style="float:right" onclick="editDevice('0', '<?php echo $device_did2; ?>')">Edit</a>
								</td>
								<td>
									<div id="div_model0"><?php
										if ($device_did) {
											$man_query4 = $db->func_query("SELECT * FROM inv_device_model WHERE device_device_id IN ($device_did)");
											foreach ($man_query4 as $query) {
												$device_model.=$query['device_model_id'] . ',';
												$device_model2.=$query['model_id'] . ',';
												$resultx = getResult("SELECT sub_model_id FROM inv_model_carrier WHERE id='" . $query['model_id'] . "'");
                                            //echo $resultx;
												echo getResult("SELECT sub_model FROM inv_model_dt WHERE sub_model_id='" . $resultx . "'") . "<br>";
											}
											$device_model = rtrim($device_model, ",");
											$device_model2 = rtrim($device_model2, ",");
										}
										?>
									</div>
									<a href="javascript:void(0);" style="float:left" onclick="clearIt('0', 'model')">Clear </a>
									<a href="javascript:void(0);" style="float:right" onclick="editModel('0', '<?php echo $device_model2; ?>')">Edit</a>
								</td>
								<td style="display:none">
									<div id="div_sku_type0" style="display:none">
										<select name="sku_type" id="sku_type0" onchange="populateAttributes(0)">
											<?php foreach ($sku_types as $sku_type) { ?>
											<option value="<?php echo $sku_type['id']; ?>" <?php
												if ($my_sku == $sku_type['sku']) {
													$sku_type_id = $sku_type['id'];
													echo 'selected';
												}
												?>><?php echo $sku_type['sku']; ?>
											</option>
											<?php } ?>
										</select>
									</div>
								</td>
								<td>
									<div id="div_attribs0"><?php
										if ($device_model) {
											$man_query4 = $db->func_query("SELECT DISTINCT attrib_id,text_value FROM inv_device_attrib WHERE device_model_id IN ($device_model)");
											$_attrib = array();
											$_attrib_parent = array();
											foreach ($man_query4 as $attribs) {
												$attribute_row = $db->func_query_first("SELECT a.name,b.name as group_name,a.attribute_group_id,a.is_text
													FROM
													`inv_attr` a
													INNER JOIN `inv_attribute_group` b
													ON (a.`attribute_group_id` = b.`id`) where a.id='" . $attribs['attrib_id'] . "'");
												if ($attribs == 0) {
													echo 'No Attrib' . "<br>";
												}
												if (!in_array($attribute_row['attribute_group_id'], $_attrib_parent)) {
													$_attrib_parent[] = $attribute_row['attribute_group_id'];
													echo "<strong>" . $attribute_row['group_name'] . "</strong><br>";
												}
												$_attrib[] = $attribs['attrib_id'];
												$__attrib[] = $attribs['text_value'];
												if ($attribute_row['is_text'] == 0) {
													echo $attribute_row['name'] . "<br>";
												} else {
													echo $attribute_row['name'] . ": " . $attribs['text_value'] . " <br>";
												}
											}
											$device_model = rtrim($device_model, ",");
										}
										?>
									</div>
									<input type="hidden" id="attrib_ids0" value="<?php echo implode(",", $_attrib); ?>" />
									<input type="hidden" id="attrib_fields0" value="<?php echo implode(",", $__attrib); ?>" />
									<input type="hidden" id="temp_did0" value="<?php echo $device_did; ?>"/>
									<input type="hidden" id="temp_model0" value="<?php echo $device_model2; ?>" />
								</td>
								<td  align="center">
									<input type="button" class="button" name="add" value="Update" onclick="submitThis(0, '<?php echo $device_did2; ?>', '<?php echo $device_model2; ?>')" />
									<input type="hidden" id="sku0" value="<?php echo $product['sku']; ?>" />
									<script>
										<?php
										if ($xmanu_id and $man_query1['verified'] == 0) {
											?>
											populateDevice(0);
											<?php
										}
										?>
									</script>
								</td>
							</tr>
						</table>
					</div>
					<?php if($_SESSION['product_competitive_pricing']): ?>
						<div id="tabThirdParty" class="makeTabs">
						<?php if($_SESSION['login_as'] == 'admin') {
								if ($product['is_csv_added'] == '1') { ?>
							<input type="checkbox"  checked="checked" onchange="csvChecker()" id="csvCheck" >Include item in CSV Reports
						<?php } else if ($product['is_csv_added'] == '0'){ ?>
							<input type="checkbox" onchange="csvChecker()" id="csvCheck" >Include item in CSV Reports
						<?php } 
						}?>
							<div style="display: none;">
								<table width="60%" cellpadding="5" cellspacing="0" border="1">
									<tr>
										<td>eBay Price:</td>
										<td><?php echo ($product_prices['ebay_fetchdate'] > 0) ? americanDate($product_prices['ebay_fetchdate']) : 'NA'; ?></td>
										<td><?php echo $product_prices['ebay']; ?></td>
										<td>
											<input type="text" name="ebay_new" id="ebay_new" value="<?php echo $product_prices['ebay_new'] ?>" />
											<!-- <input type="button" onclick="getProductPrice(this , 'ebay')" name="getPrice" value="Get Price" /> -->
											<input type="button" onclick="getProductPrice(this, 'ebay_new')" name="getPrice" value="Update Price" />
										</td>
									</tr>
									<tr>
										<td>Amazon Price:</td>
										<td><?php echo ($product_prices['amazon_fetchdate'] > 0) ? americanDate($product_prices['amazon_fetchdate']) : 'NA'; ?></td>
										<td><?php echo $product_prices['amazon']; ?></td>
										<td> 
											<input type="text" name="amazon_new" id="amazon_new" value="<?php echo $product_prices['amazon_new'] ?>" />
											<!--<input type="button" onclick="getProductPrice(this , 'amazon')" name="getPrice" value="Get Price" /> -->
											<input type="button" onclick="updateProductPrice(this, 'amazon_new')" name="getPrice" value="Update Price" />
										</td>
									</tr>
									<tr>
										<td>Channel Advisor MM Price:</td>
										<td><?php echo ($product_prices['channel_advisor_fetchdate'] > 0) ? americanDate($product_prices['channel_advisor_fetchdate']) : 'NA'; ?></td>
										<td><?php echo $product_prices['channel_advisor']; ?></td>
										<td>
											<input type="text" name="channel_advisor_new" id="channel_advisor_new" value="<?php echo $product_prices['channel_advisor_new'] ?>" />
											<!--<input type="button" onclick="getProductPrice(this , 'channel_advisor')" name="getPrice" value="Get Price" />-->
											<input type="button" onclick="updateProductPrice(this, 'channel_advisor_new')" name="getPrice" value="Update Price" />
										</td>
									</tr>
									<tr>
										<td>Channel Advisor US1 Price:</td>
										<td><?php echo ($product_prices['channel_advisor1_fetchdate'] > 0) ? americanDate($product_prices['channel_advisor1_fetchdate']) : 'NA'; ?></td>
										<td><?php echo $product_prices['channel_advisor1']; ?></td>
										<td>
											<input type="text" name="channel_advisor1_new" id="channel_advisor1_new" value="<?php echo $product_prices['channel_advisor1_new'] ?>" />
											<!--<input type="button" onclick="getProductPrice(this , 'channel_advisor1')" name="getPrice" value="Get Price" />-->
											<input type="button" onclick="updateProductPrice(this, 'channel_advisor1_new')" name="getPrice" value="Update Price" />
										</td>
									</tr>
									<tr>
										<td>Channel Advisor US2 Price:</td>
										<td><?php echo ($product_prices['channel_advisor2_fetchdate'] > 0) ? americanDate($product_prices['channel_advisor2_fetchdate']) : 'NA'; ?></td>
										<td><?php echo $product_prices['channel_advisor2']; ?></td>
										<td>
											<input type="text" name="channel_advisor2_new" id="channel_advisor2_new" value="<?php echo $product_prices['channel_advisor2_new'] ?>" />
											<!--<input type="button" onclick="getProductPrice(this , 'channel_advisor2')" name="getPrice" value="Get Price" />-->
											<input type="button" onclick="updateProductPrice(this, 'channel_advisor2_new')" name="getPrice" value="Update Price" />
										</td>
									</tr>
									<tr>
										<td>Bigcommerce Price:</td>
										<td><?php echo ($product_prices['bigcommerce_fetchdate'] > 0) ? americanDate($product_prices['bigcommerce_fetchdate']) : 'NA'; ?></td>
										<td><?php echo $product_prices['bigcommerce']; ?></td>
										<td>
											<input type="text" name="bigcommerce_new" id="bigcommerce_new" value="<?php echo $product_prices['bigcommerce_new'] ?>" />
											<!--<input type="button" onclick="getProductPrice(this , 'bigcommerce')" name="getPrice" value="Get Price" />-->
											<input type="button" onclick="updateProductPrice(this, 'bigcommerce_new')" name="getPrice" value="Update Price" />
										</td>
									</tr>
									<tr>
										<td>Bigcommerce Retail Price:</td>
										<td><?php echo ($product_prices['bigcommerce_retail_fetchdate'] > 0) ? americanDate($product_prices['bigcommerce_retail_fetchdate']) : 'NA'; ?></td>
										<td><?php echo $product_prices['bigcommerce_retail']; ?></td>
										<td>
											<input type="text" name="bigcommerce_retail_new" id="bigcommerce_retail_new" value="<?php echo $product_prices['bigcommerce_retail_new'] ?>" />
											<!--<input type="button" onclick="getProductPrice(this , 'bigcommerce_retail')" name="getPrice" value="Get Price" />-->
											<input type="button" onclick="updateProductPrice(this, 'bigcommerce_retail_new')" name="getPrice" value="Update Price" />
										</td>
									</tr>
									<tr>
										<td>Bonanza Price:</td>
										<td><?php echo ($product_prices['bonanza_fetchdate'] > 0) ? americanDate($product_prices['bonanza_fetchdate']) : 'NA'; ?></td>
										<td><?php echo $product_prices['bonanza']; ?></td>
										<td>
											<input type="text" name="bonanza_new" id="bonanza_new" value="<?php echo $product_prices['bonanza_new'] ?>" />
											<!--<input type="button" onclick="getProductPrice(this , 'bonanza')" name="getPrice" value="Get Price" />-->
											<input type="button" onclick="updateProductPrice(this, 'bonanza_new')" name="getPrice" value="Update Price" />
										</td>
									</tr>
									<tr>
										<td>Wish Price:</td>
										<td><?php echo ($product_prices['wish_fetchdate'] > 0) ? americanDate($product_prices['wish_fetchdate']) : 'NA'; ?></td>
										<td><?php echo $product_prices['wish']; ?></td>
										<td>
											<input type="text" name="wish_new" id="wish_new" value="<?php echo $product_prices['wish_new'] ?>" />
											<!--<input type="button" onclick="getProductPrice(this , 'wish')" name="getPrice" value="Get Price" />-->
											<input type="button" onclick="updateProductPrice(this, 'wish_new')" name="getPrice" value="Update Price" />
										</td>
									</tr>
									<tr>
										<td>OpenSky Price:</td>
										<td><?php echo ($product_prices['open_sky_fetchdate'] > 0) ? americanDate($product_prices['open_sky_fetchdate']) : 'NA'; ?></td>
										<td><?php echo $product_prices['open_sky']; ?></td>
										<td>
											<input type="text" name="open_sky_new" id="open_sky_new" value="<?php echo $product_prices['open_sky_new'] ?>" />
											<!--<input type="button" onclick="getProductPrice(this , 'open_sky')" name="getPrice" value="Get Price" />-->
											<input type="button" onclick="updateProductPrice(this, 'open_sky_new')" name="getPrice" value="Update Price" />
										</td>
									</tr>
								</table>
								<!--  <input type="button" name="fetchAll" value="Get All Prices" onclick="getPrices()"; />-->
							</div>  
							<div style="margin-top:10px;display: none;">
								<?php $scrapping_sites = array('gadgetfix', 'ebay', 'ebay_2', 'ebay_3', 'ebay_4', 'mengtor', 'mobile_defenders'); ?>
								<table border="1" cellpadding="5" cellspacing="0" width="60%;">
									<?php foreach ($scrapping_sites as $site) { ?>
									<?php $scraper = $db->func_query_first("SELECT * FROM inv_product_scrape_prices WHERE sku='" . $product_sku . "' AND scrape_site='" . $site . "'"); ?>
									<tr>
										<td><?php echo ucfirst(str_replace("_", " ", $site)); ?></td>
										<td><?php echo ($scraper['date_updated'] ? americanDate($scraper['date_updated']) : 'N/A'); ?></td>
										<td><input type="text" id="<?php echo $site; ?>_url" style="width:230px" value="<?php echo $scraper['url']; ?>" /></td>
										<td><input type="text" id="<?php echo $site; ?>_price" style="width:70px" readonly value="<?php echo (float) $scraper['price']; ?>" /></td>
										<td align="center"><input type="button" value="Fetch &amp; Store" onclick="fetchScrapePrice(this, '<?php echo $site; ?>')" /></td>
									</tr>
									<?php } ?>
								</table>
							</div>
							<br>
							<div style="margin-top:10px">
							
								<?php $scrapping_sites = array('mobile_sentrix', 'fixez', 'mengtor', 'mobile_defenders','etrade_supply','maya_cellular','lcd_loop','parts_4_cells','cell_parts_hub'); ?>
								<table border="1" cellpadding="5" cellspacing="0" width="80%;">
									<thead>
										<tr>
											<th>Competitor</th>
											<th>Old Price</th>
											<th>Current Price</th>
											<th>% Change</th>
											<th>Out of Stock</th>
											<th>URL</th>
										</tr>
									</thead>
									<?php foreach ($scrapping_sites as $site) { ?>
									<?php $price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $product_sku . "' AND type = '$site' order by added DESC limit 1"); ?>
									<?php
									$scraper = $db->func_query_first("SELECT * FROM inv_product_price_scrap WHERE sku='" . $product_sku . "' AND `type`='" . $site . "' and url<>''"); ?>
									<?php $change = number_format($price['price'] / $price['old_price'] * 100, 2); ?>
									<?php if ($change < 100.00 && $change > 0.00) {
										$change = '-' . (100 - $change);
									} else if ($change == 0.00) {
										$change = 100 - $change;
									} else {
										$change = '+' . ($change - 100);
									}
									if((float)$price['old_price']==0.00)
									{
										$change = 0.00;
									}
									
									?>
									<?php //$change = ($change < 100.00 && $change > 0.00) ? '-' . (100 - $change): (($change == 0.00)? 100 - $change: '+' . (100 - $change)); ?>
									<tr>
										<td align="center"><?php echo ucwords(str_replace('_', ' ', $site)); ?></td>
										<td align="center" style="width: 150px;"><input readOnly style="width: 90px; border:none" onclick="this.select();" value="<?php echo number_format($price['old_price'], 2); ?>" /></td>
										<td align="center" style="width: 150px;"><input readOnly id="<?php echo $site;?>_new_price" style="width: 90px; border:none" onclick="this.select();" value="<?php echo number_format($price['price'], 2); ?>" /><br>
										<input align="center" readOnly style="width: 110px;border:none;" value="<?php echo americanDate($price['added']); ?>"></td>
										<td align="center"><?php echo $change; ?>%</td>
										<td align="center"><?php echo ($price['out_of_stock'])? 'Yes': 'No'; ?></td>
										<td align="center"><input style="width: 480px;" type="text" name="scraped_url[<?php echo $site;?>]" value="<?php echo $scraper['url']; ?>">
											<br><a href="<?php echo $scraper['url']; ?>" target="_blank" >Open Link</a>
										</td>
									</tr>
									<?php } ?>
								</table>
								<br><center><a class="button" href="javascript:void();"  onclick="fetchNewCompetitorPricing(this)">Fetch Competitor Pricing</a></center>
							</div>
						</div>
					<?php endif; ?>
					<?php if (isset($product_issues) and count($product_issues) > 0): ?>
						<div id="tabProductIssue" class="makeTabs">
							<div align="center">
								<table border="1" cellpadding="5" cellspacing="0" width="60%;">
									<tr>
										<td>Item Issue</td>
										<td>Thumbnail</td>
										<td>Occurance</td>
									</tr>
									<?php $base_path = "../images/"; ?>
									<?php foreach ($product_issues as $product_issue): ?>
										<tr>
											<td><?php echo $product_issue['item_issue']; ?></td>
											<td>
												<?php
												$product_issue_id = $product_issue['product_issue_id'];
												$product_issue_images = $db->func_query("select * from inv_product_issue_images where product_issue_id IN ($product_issue_id)");
												if (strtotime($product_issue['date_added']) < mktime(0, 0, 0, 03, 14, 2015)) {
													$base_path = "../../qc/serviceFiles/";
												}
												?>
												<ul style="list-style-type:none;">
													<?php foreach ($product_issue_images as $product_issue_image): ?>
														<li style="display:inline;">
															<a class="fancybox2 fancybox.iframe" href="<?php echo $base_path; ?><?php echo $product_issue_image['image_path'] ?>">
																<img src="<?php echo $base_path; ?><?php echo $product_issue_image['image_path'] ?>" width="50" height="50" />
															</a>
														</li>
													<?php endforeach; ?>
												</ul>
											</td>
											<td><?php echo $product_issue['total']; ?></td>
										</tr>
									<?php endforeach; ?>
								</table>
							</div>
						</div>	
					<?php endif; ?>
					<?php if ($outStockHistory) { ?> 
					<div id="tabStockHistory" class="makeTabs">
						<h2 style="color: red;">Out of Stock History</h2>
						<div class="hiddenOverFlow" style="max-height:300px; overflow-y: hidden;">
							<table width="80%" cellpadding="5" cellspacing="0" border="1" align="center">
								<tr>
									<th>
										Sr #
									</th>
									<th>
										In Stock Date
									</th>
									<th>
										Out Stock Date
									</th>
									<th>
										Last Updated
									</th>
								</tr>
								<?php foreach ($outStockHistory as $i => $outStock) { ?>
								<tr>
									<td align="center">
										<?= ($i + 1); ?>
									</td>
									<td align="center">
										<?= americanDate($outStock['instock_date']); ?>
									</td>
									<td align="center">
										<?= americanDate($outStock['outstock_date']); ?>
									</td>
									<td align="center">
										<?= americanDate($outStock['date_modified']); ?>
									</td>
								</tr>
								<?php } ?>
							</table>
						</div>
					</div>
					<?php } ?>
					<div id="tabSaleHistory" class="makeTabs">
						<table width="80%">
							<tbody>
								<tr>
									<td width="100%">
										<iframe src="<?php echo $host_path; ?>popupfiles/item_sale_history.php?product=1&sku=<?php echo $product_sku; ?>" style="border: 1px solid black; height: 600px; width: 1000px;"></iframe>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div id="tabVendor" class="makeTabs">
						<table width="80%">
							<tbody>
								<tr>
									<td width="50%" style="vertical-align:top">
										<?php $vendorsName = $db->func_query('SELECT `iu`.`name`, `iu`.`id` FROM `inv_users` AS `iu` WHERE `iu`.`group_id` = 1 order by lower(iu.name)');?>
										<?php $vendors = $db->func_query('SELECT `vendor` FROM `inv_product_vendors` WHERE `product_sku` = "'. $product_sku .'"'); ?>
										<h1>Current / Default Vendors</h1>
										<select id="vendors" multiple="" style="display: inline-block; width: 300px; height: 400px;">
											<?php foreach ($vendorsName as $key => $vendor): ?>
												<option value="<?php echo $vendor['id']; ?>" <?php echo (!(array_search($vendorsName[$key]['id'], array_column($vendors, 'vendor')) === false))? 'selected="selected"': '';?>><?php echo $vendor['name']; ?></option>
											<?php endforeach; ?>
										</select>
									</td>
									<td style="vertical-align:top"><h1>Old Vendors</h1>
										<?php $old_vendors = $db->func_query('SELECT `vendor` FROM `inv_product_vendors_log` WHERE `product_sku` = "'. $product_sku .'"'); ?>
										<?php foreach ($vendorsName as $key => $vendor): 
										if(!(array_search($vendorsName[$key]['id'], array_column($old_vendors, 'vendor')) === false))
										{
											if((array_search($vendorsName[$key]['id'], array_column($vendors, 'vendor')) === false))
											{
												echo "<strong>* ".$vendor['name']."</strong><br>";
											}
										}
										endforeach;
										?>
									</td>
								</tr>
								<tr>
									<td style="text-align: center;">
										<input type="button" onclick="updateVendor();" value="Update Vendors">
										<script type="text/javascript">
											function updateVendor () {
												$.ajax({
													url: 'product.php',
													type: 'POST',
													dataType: 'json',
													data: {vendors: $('#vendors').val(), sku: '<?php echo $product_sku; ?>'},
													success: function (json) {
														alert(json['msg']);
													}
												});
											}
										</script>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
				</div>
				<br><br><br>
				<input type="submit" name="update" class="button" value="Update" />
			</form>
			<h2><a href="<?php echo $host_path; ?>products.php">Go Back</a></h2>
		</div>

		 	<div id="item_movement_csv" style="display:none;text-align:center">
		   	<h1 style="font-size:17px">SKU Movement CSV -  <?php echo $product_sku;?></h1>
		   	<form id="item_movement_csv_form" method="GET" action="<?php echo $host_path;?>inventory_movement_report.php" enctype="multipart/form-data">
		   	<?php
		   
		   	?>
		   	<table width="100%" cellpadding="5" cellspacing="5">
		   
		    <tr>
		    	<td><strong>Date Start:</strong></td>
                <td>
                  
                  <input type="date" name="filter_date_range_start" value="" style="width: 90%;margin-right:2px; "  >
                  </td>
                  </tr>
                  <tr>
                  <td><strong>Date End:</strong></td>
                  <td>
                  <input type="date" name="filter_date_range_end" value="" style="width: 90%;margin-right:2px; "  >
                </td>
              </tr>

		   	<tr>	
		   	<td colspan="2">&nbsp;</td>
		   	</tr>
		   	<tr>
		   	<td colspan="2" align="center">

		   	<input type="button" onclick="$('#item_movement_csv_form').submit()" value="Download Now" class="button" style="padding: 5px; 15px; font-size: 12px;"></td>
		   	</tr>
		   	</table>
		   		<input type="hidden" name="sku" value="<?php echo $product_sku;?>">
		   		<input type="hidden" name="search" value="csv">
		   	</form>
		   	</div>


		<script type="text/javascript">
			$(document).ready(function() {
				<?php
				if(isset($_GET['competitor']))
				{
					?>
					$('input[data-tab="tabThirdParty"]').trigger('click');
					<?php
				}
				?>
				$("[rel='fancybox-thumbrj']").fancybox({
					helpers : {
						thumbs : true
					}
				});
			});
			$(document).ready(function() {
				$("[rel='fancybox-thumbntr']").fancybox({
					helpers : {
						thumbs : true
					}
				});
			});
		</script>
		<script>
			function fetchScrapePrice(obj, type)
			{
				var scrape_url = $('#' + type + '_url').val();
				var url = '<?php echo $host_path; ?>scrape_price.php';
				if (scrape_url == '')
				{
					alert("Please provide a valid url");
					return false;
				}
				$(obj).val('Please wait...');
				$.ajax({
					url: url,
					type: "POST",
					data: {scrape_url: encodeURIComponent(scrape_url), action: 'fetch', type: type, sku: '<?php echo $product_sku; ?>'},
					dataType: "json",
					success: function (json) {
						if (json['error'])
						{
							alert(json['error']);
						}
						else if (json['success'])
						{
							$('#' + type + '_price').val(json['success']);
						}
					},
					complete: function () {
						$(obj).val('Fetch &amp; Store');
					}
				});
			}
			function fetchNewCompetitorPricing(obj)
			{
				$(obj).html('Please wait...');
				$.ajax({
					url: '<?php echo $host_path;?>crons/cron_price_scraper.php',
					type: "POST",
					data: {sku: '<?php echo $product_sku; ?>'},
					dataType: "json",
					success: function (json) {
						if (json)
						{
							var loop = json.length;
							for (var i = 0; i < loop; i++) {
								console.log(json[i]['price']);
								$('#'+json[i]['site']+'_new_price').val(json[i]['price']);
							// nav += '<a href="" onclick="loadNav(this, \''+ json['next'] +'\', \''+ classID +'\', \''+ json['nav'][i].id +'\');" data-submodelid="' + json['nav'][i].id + '" class="list-group-item" data-toggle="collapse">'+ json['nav'][i].name +'</a>';
						}
					}
				},
				complete: function () {
					$(obj).html('Pricing Fetched');
				}
			});
				return false;
			}
			function updateScrapePrice(obj, type)
			{
				var scrape_url = $('#' + type + '_url').val();
				var scrape_price = $('#' + type + '_price').val();
				var url = '<?php echo $host_path; ?>scrape_price.php';
				if (scrape_price == '0' || scrape_price == '' || scrape_price == '0.00')
				{
					alert("Please provide a valid price");
					return false;
				}
				$(obj).val('Please wait...');
				$.ajax({
					url: url,
					type: "POST",
					data: {scrape_url: encodeURIComponent(scrape_url), scrape_price: scrape_price, sku: '<?php echo $product_sku; ?>', action: 'update', type: type},
					dataType: "json",
					success: function (json) {
						if (json['success'])
						{
							alert(json['success']);
						}
					},
					complete: function () {
						$(obj).val('Update');
					}
				});
			}
			function csvChecker(){
				if ($('#csvCheck').prop('checked') == true) {
					$('#is_csv_added').val(1);
				} else {
					$('#is_csv_added').val(0);
				}
			}
			function shopifyChecker(){
				if ($('#shopifyCheck').prop('checked') == true) {
					$('#is_shopify').val(1);
				} else {
					$('#is_shopify').val(0);
				}
			}

			function eBayChecker(){
				if ($('#ebayCheck').prop('checked') == true) {
					$('#is_ebay').val(1);
				} else {
					$('#is_ebay').val(0);
				}
			}


			function discontinueChecker(){
				if ($('#discontinueCheck').prop('checked') == true) {
					$('#discontinue').val(1);
				} else {
					$('#discontinue').val(0);
				}
			}
			function shopifyUploadChecker(){
				if ($('#shopifyUploadCheck').prop('checked') == true) {
					$('#is_shopify_uploaded').val(1);
				} else {
					$('#is_shopify_uploaded').val(0);
				}
			}
			function loadLedger()
			{
				var ledger_filter = $('#ledger_filter').val();
				$.ajax({
				url: "product.php",
				type: "POST",
				data: {action:'load_ledger',fiter:ledger_filter,sku:'<?php echo $product_sku;?>'},
				dataType:'json',
				
				success: function (json) {
					$('#ledger_table tbody').html(json['html']);
					
				}
			});
			}

			$(document).ready(function(){
				loadLedger();
			});
			
		</script>
	</body>
	</html>
