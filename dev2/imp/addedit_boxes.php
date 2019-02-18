<?php

include_once '../auth.php';
include_once '../inc/functions.php';
include_once '../inc/split_page_results.php';
$pageViewLink = 'addedit_boxes.php?shipment_id=' . $_GET['shipment_id'];
$shipment_id = (int)$_GET['shipment_id'];
if(!$shipment_id){
	$shipment_id = $db->func_query_first_cell("select id from inv_buyback_boxes where status != 'Completed'");
}

if(!$shipment_id){
	//$_SESSION['message'] = "No new sku added in rejected list";
	header("Location:box_shipments.php");
	exit;
}

$shipments = $db->func_query("SELECT * FROM inv_shipments WHERE lbb_shipment = '$shipment_id'");

foreach ($shipments as $key => $shipment) {
	$itemsx = $db->func_query("SELECT * FROM inv_shipment_items WHERE is_lbb = '1' AND shipment_id = '". $shipment['id'] ."'");
	$items = array();

	foreach ($itemsx as $ix => $itemx) {
		$items[$itemx['product_sku']] = $itemx;
	}

	$shipments[$key]['items'] = $items;
	unset($items);
}

$shipment_detail = $db->func_query_first("SELECT * from inv_buyback_boxes where id = '$shipment_id'");

if ($_POST['action'] == 'getBoxs') {
	$shipments = $db->func_query("SELECT * FROM `inv_buyback_boxes` WHERE id != '$shipment_id'");
	$json['data'] = '<div class="blackPage">';
	$json['data'] .= '<div class="whitePage">';
	$json['data'] .= '<div class="form">';
	$json['data'] .= '<select id="mergeBoxId">';
	$json['data'] .= '<option value="">--Select--</option>';
	if ($shipments) {
		foreach ($shipments as $key => $row) {
			$json['data'] .= '<option value="'. $row['id'] .'">'. (($row['package_number'])? $row['package_number']: 'No Package #') .'</option>';
		}
	}
	$json['data'] .= '</select>';
	$json['data'] .= '</div>';
	$json['data'] .= '<div class="form">';
	
	$json['data'] .= '<input class="button" type="button" class="shipmentBtn" value="Add" onclick="mergeBox();" />';
	$json['data'] .= '<input class="button" type="button" value="Cancel" onclick="$(\'.blackPage\').remove();" />';

	$json['data'] .= '</div>';
	$json['data'] .= '</div>';
	$json['data'] .= '</div>';

	echo json_encode($json);
	exit;
}


if ($_POST['action'] == 'mergeBox') {
	$id = (int)$_POST['id'];
	$to = (int)$_POST['to'];
	$tables = array(
		 'inv_shipments' =>'lbb_shipment',
		 'inv_buyback_box_items' => 'shipment_id',
		 'inv_buyback_manual_box_items' => 'shipment_id',
		  'inv_buyback_shipment_box_comments'=>'buyback_shipment_box_id',
		
		);
	if ($id && $to) {
		foreach ($tables as $table => $key) {
			$db->db_exec("UPDATE $table SET $key = '$to' WHERE $key = '$id'");
		}
		$_SESSION['message'] = 'Box Merged';
	} else {
		$_SESSION['message'] = 'Wrong Box ID';
	}


	echo json_encode($json['to'] = $to);
	exit;
}

if ($_POST['action'] == 'getShipment') {
	$shipments = $db->func_query('SELECT * FROM `inv_shipments` WHERE vendor_po_id = "'. $_SESSION['user_id'] .'" AND is_lbb = 1 AND status = "Pending"');
	$json['data'] = '<div class="blackPage">';
	$json['data'] .= '<div class="whitePage">';
	$json['data'] .= '<div class="form">';

	$json['data'] .= '<select id="vendor_shipment_id">';
	$json['data'] .= '<option value="">--Create New--</option>';
	if ($shipments) {
		foreach ($shipments as $key => $row) {
			$json['data'] .= '<option value="'. $row['id'] .'">'. $row['package_number'] .'</option>';
		}
	}
	$json['data'] .= '</select>';
	$json['data'] .= '</div>';
	$json['data'] .= '<div class="form">';
	
	$json['data'] .= '<input class="button" type="button" class="shipmentBtn" value="Add" onclick="addShipment();" />';
	$json['data'] .= '<input class="button" type="button" value="Cancel" onclick="$(\'.blackPage\').remove();" />';

	$json['data'] .= '</div>';
	$json['data'] .= '</div>';
	$json['data'] .= '</div>';

	echo json_encode($json);
	exit;
}

if ($_POST['action'] == 'addShipment') {
	//print_r($_POST);
	$shipment = array();
	if ($_POST['shipment_id']) {
		$shipment['id'] = $_POST['shipment_id'];
	}
	foreach ($_POST['product'] as $productx) {
		$item = array();
		if ($productx['update'] && $productx['qty_shipped']) {
			unset($productx['update']);
			$item = $productx;
		}

		if ($item) {
			$log = 'Shipment #: ' . linkToShipment($shipment['id'], $host_path, $shipment['package_number']) . ' is Updated From '. linkToVPO($details['id'], $host_path, $vendor_po_id);
			if (!isset($shipment['id'])) {
				$shipment = array(
					'package_number' => 'lbb' . rand(),
					'status' => 'Pending',					
					'is_lbb' => 1,
					'lbb_shipment' => $shipment_id,
					'vendor_po_id' => $_SESSION['user_id'],
					'date_added' => date('Y-m-d H:i:s')
					);
				$shipment['id'] = $db->func_array2insert("inv_shipments", $shipment);
				$log = 'Shipment #: ' . linkToShipment($shipment['id'], $host_path, $shipment['package_number']) . ' is Created From '. linkToVPO($details['id'], $host_path, $vendor_po_id);
			}

			$shipment_item = $db->func_query_first('SELECT * FROM `inv_shipment_items` WHERE `shipment_id` = "'. $shipment['id'] .'" AND product_sku = "'. $item['product_sku'] .'" ');
			if ($shipment_item) {
				$db->db_exec('UPDATE inv_shipment_items SET qty_shipped = (qty_shipped + '. $item['qty_shipped'] .') WHERE id = "'. $shipment_item['id'] .'"');
				$plog .= "<br><br> Product " . linkToProduct($item['product_sku']) . ' Updated';
				$plog .= "<br> Shipped " . $item['qty_shipped'] . " items Added";
				$shipping_item_id = $shipment_item['id'];
			} else {
				$array = array(
					'shipment_id'	=> $shipment['id'],
					'product_id'	=> $item['product_id'],
					'product_name'	=> $item['product_name'],
					'is_lbb'		=> '1',
					'product_sku'	=> $item['product_sku'],
					'qty_shipped'	=> $item['qty_shipped']
					);
				$shipping_item_id = $db->func_array2insert("inv_shipment_items", $array);
				$plog .= "<br><br> Product " . linkToProduct($item['product_sku']) . ' Added';
				$plog .= "<br> Shipped " . $item['toShip'] . " items Added";
				unset($array);
			}
		}
	}
	actionLog($log . $plog);
	exit;
}

if($_POST['print']){ 
	$reject_ids = implode(",",$_POST['reject_ids']);
	header("Location:print_shipment.php?ids=$reject_ids");
	exit;
}
if(isset($_POST['addcomment']))
{
	$data = array();
	$data['id'] = $shipment_id;
	$data['comment'] = $_POST['comment'];
	$msg = addComment('buyback_shipment_box',$data);
	$_SESSION['message'] = $msg;
	header("Location: addedit_boxes.php?shipment_id=".$shipment_id);
	exit;
}

//save shipment
if($_POST['save'] || $_POST['RejectComplete']){ 
	$shipment = array();
	$shipment['package_number'] = $db->func_escape_string($_POST['package_number']);
	
	$shipment['status'] = 'Issued';
	$shipment['date_issued'] = date('Y-m-d H:i:s');
	
	if(isset($_POST['outbound_tracking']))
	{
		$shipment['outbound_tracking'] = $db->func_escape_string($_POST['outbound_tracking']);	
		$shipment['inbound_tracking'] = $db->func_escape_string($_POST['inbound_tracking']);

	}
	if(isset($_POST['outbound_shipping_cost']))
	{
		$shipment['outbound_shipping_cost'] = (float)$_POST['outbound_shipping_cost'];
		$shipment['inbound_shipping_cost'] = (float)$_POST['inbound_shipping_cost'];
	}


	$checkExist = $db->func_query_first_cell("SELECT id from inv_buyback_boxes where id != '$shipment_id' 
		and package_number = '".$shipment['package_number']."'");
	if($checkExist){
		$_SESSION['message'] = "This package number is assigned to another shipment.";
		header("Location:addedit_boxes.php?shipment_id=$shipment_id");
		exit;
	}
	else{
		$db->func_array2update("inv_buyback_boxes",$shipment,"id = '$shipment_id'");
		$_SESSION['message'] = "Shipment is updated";
	}
	
	//now update shipment item reject reason
	$reasons = $_POST['reason'];
	
	foreach($reasons as $id => $reason){
		
		$text = $db->func_escape_string($reason);
		
		$db->db_exec("update inv_buyback_box_items SET  reason = '$text' where id = '$id'");
	}
	if(isset($_POST['list']))
	{
		foreach($_POST['list'] as $_buyback_product =>$key)
		{


			$db->db_exec("UPDATE inv_buyback_box_items SET working_qty='".(int)$key['working_qty']."',refurb_cost='".(float)$key['refurb_cost']."',non_working_qty='".(int)$key['non_working_qty']."',non_working_lcd_cost='".(float)$key['non_working_lcd_cost']."' WHERE buyback_product_id='".$_buyback_product."' AND shipment_id='".$shipment_id."'");
		}
		$db->db_exec("update inv_buyback_boxes SET status = 'Completed'  where id = '$shipment_id'");


	}

	if(isset($_POST['manual_list']))
	{
		foreach($_POST['manual_list'] as $_buyback_product =>$key)
		{


			$db->db_exec("UPDATE inv_buyback_manual_box_items SET working_qty='".(int)$key['working_qty']."',refurb_cost='".(float)$key['refurb_cost']."',non_working_qty='".(int)$key['non_working_qty']."',non_working_lcd_cost='".(float)$key['non_working_lcd_cost']."' WHERE sku='".$_buyback_product."' AND shipment_id='".$shipment_id."'");
		}
		$db->db_exec("UPDATE inv_buyback_boxes SET status = 'Completed'  where id = '$shipment_id'");


	}



	if($_POST['RejectComplete'] && $_SESSION['edit_received_shipment']){
		if(!$shipment['package_number']){
			$_SESSION['message'] = "Package number is required.";
			header("Location:addedit_boxes.php?shipment_id=$shipment_id");
			exit;
		}
		$log = '<a href="'.$host_path.'buyback/addedit_boxes.php?shipment_id='.$shipment_id.'">'.$shipment_detail['package_number'].'</a> Buyback Shipment Box has been Closed';
		actionLog($log);
		$db->db_exec("update inv_buyback_boxes SET status = 'Completed'  where id = '$shipment_id'");
		$_SESSION['message'] = "Shipment status is Completed";
	}
	
	header("Location:box_shipments.php");
	exit;
}


if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}

if($page < 1){
	$page = 1;
}

$parameters = "shipment_id=$shipment_id";

$max_page_links = 10;
$num_rows = 500;
$start = ($page - 1)*$num_rows;

$inv_query  = "select si.* , s.package_number from inv_buyback_box_items si inner join inv_buyback_boxes s on (si.shipment_id = s.id)
where si.shipment_id = '$shipment_id' order by shipment_id";

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "addedit_boxes.php",$page);
$products   = $db->func_query($splitPage->sql_query);

$products1 = $db->func_query("SELECT sum(a.oem_received) as oem_received,sum(a.non_oem_received) as non_oem_received,a.buyback_product_id FROM inv_buyback_box_items a,oc_buyback_products b WHERE a.buyback_product_id=b.buyback_product_id and  a.shipment_id='".$shipment_id."' GROUP BY b.sku");
$products2 = $db->func_query("SELECT a.*,b.sku,b.description,b.oem_price,b.non_oem_price,sum(a.oem_received) as oem_received,sum(a.non_oem_received) as non_oem_received FROM inv_buyback_box_items a,oc_buyback_products b WHERE a.buyback_product_id=b.buyback_product_id and  a.shipment_id='".$shipment_id."' GROUP BY b.sku");
$manual_products2 = $db->func_query("SELECT a.*,b.sku,b.description,sum(a.oem_qty) as oem_received,sum(a.non_oem_qty) as non_oem_received FROM inv_buyback_manual_box_items a,inv_buy_back b WHERE a.sku=b.sku and  a.shipment_id='".$shipment_id."' GROUP BY a.sku");

$manual_items = $db->func_query("SELECT * FROM inv_buyback_manual_box_items WHERE shipment_id='$shipment_id'");

// Total OEM / Non-OEM Count
$oem_total = 0;
$non_oem_total = 0;
foreach ($products as $i => $product) {
	$oem_total+=$product['oem_received'];
	$non_oem_total+=$product['non_oem_received'];
}
foreach($manual_items as $manual)
{
	$oem_total+=$manual['oem_qty'];
	$non_oem_total+=$manual['non_oem_qty'];
}

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Add / Edit Shipment  Box</title>

	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

	<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox({ width: '400px', height : '200px' , autoCenter : true , autoSize : true });
			$('.fancybox2').fancybox({ width: '500px', height : '500px' , autoCenter : true , autoSize : true });
		});
	</script>	
</head>
<body>
	<?php include_once '../inc/header.php';?>

	<?php if(@$_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php endif;?>

	<div align="center">
		<form method="post" action="">
			<br />
			<div style="width: 80%;">
				Shipment Number:
				<input type="text" name="package_number" value="<?php echo $shipment_detail['package_number'];?>" required />

				<?php if($shipment_detail['status'] != 'Completed'):?>
					<input type="submit" name="save" value="Save" />
				<?php endif;?>
				<input style="float: right;" class="button" type="button" class="shipmentBtn" value="Merge Shipment" onclick="selectGetBoxs();" />
				<br /><br />
			</div>
			<?php
			if($shipment_detail['status']!='Completed' and $_SESSION['login_as']=='admin')
			{
				?>
				<a class="fancybox2 fancybox.iframe" href="<?=$host_path;?>buyback/add_manual_lcd.php?shipment_id=<?=$shipment_id;?>">Add Manual LCD</a>
				<?php

			}
			?>

			<table width="80%" cellspacing="0" cellpadding="5px" border="0" align="center" style="border-collapse:collapse;">

				<tr>
					<td align="right">
						Outbound Tracking: </td><td><input type="text" name="outbound_tracking" value="<?=$shipment_detail['outbound_tracking'];?>"> </td><td align="right">Inbound Tracking: </td><td><input type="text" name="inbound_tracking" value="<?=$shipment_detail['inbound_tracking'];?>"></td>
					</tr>
					<?php
					if($_SESSION['login_as']=='admin' || $_SESSION['boxes_cost']==1)
					{
						?>
						<tr>
							<td align="right">
								Shipping Cost: </td><td><input type="text" name="outbound_shipping_cost" value="<?=$shipment_detail['outbound_shipping_cost'];?>"> </td><td align="right">Shipping Cost: </td><td><input type="text" name="inbound_shipping_cost" value="<?=$shipment_detail['inbound_shipping_cost'];?>"></td>
							</tr>
							<tr>

								<td colspan="4">


									<?php
									$sum = array();
									$total_products = array();
									foreach($products as $product)
									{
										$sku = $db->func_query_first("SELECT * FROM oc_buyback_products WHERE buyback_product_id='".$product['buyback_product_id']."'");
										if(isset($sum['oem'][$sku['buyback_id']][$sku['sku']]))
										{
											$sum['oem'][$sku['buyback_id']][$sku['sku']]+=$product['oem_received'];

										}	
										else
										{
											$sum['oem'][$sku['buyback_id']][$sku['sku']]=$product['oem_received'];

										}	

										if(isset($sum['non_oem'][$sku['buyback_id']][$sku['sku']]))
										{
											$sum['non_oem'][$sku['buyback_id']][$sku['sku']]+=$product['non_oem_received'];

										}	
										else
										{
											$sum['non_oem'][$sku['buyback_id']][$sku['sku']]=$product['non_oem_received'];

										}
										$total_products[$sku['sku']] = $sku['description'];			   
									}
	        	//echo "<pre>";
	        	//print_r($total_products);
									?>
									<table width="90%" cellpadding="5px" cellspacing="0" border="1" align="center" style="border-collapse:collapse;display:none">
										<tr>
											<th>OEM</th>
											<?php
											foreach($sum['oem'] as $buyback_id =>$buyback)
											{
												$lbb_number = $db->func_query_first_cell("select shipment_number from oc_buyback where buyback_id='".(int)$buyback_id."'")
												?>
												<th><?=linkToLbbShipment($lbb_number,$host_path);?></th>
												<?php
											}
											?>
										</tr>
										<?php

										foreach($total_products as $sku=>$description)
										{
											?>
											<tr>
												<td><strong><?=$sku.'-'.$description;?></strong></td>
												<?php
												foreach($sum['oem'] as $buyback_id=>$buyback)
												{
													?>
													<td align="center"><?=(int)$buyback[$sku];?></td>
													<?php
												}
												?>
											</tr>
											<?php
										}
										?>

									</table>


									<table width="90%" cellpadding="5px" cellspacing="0" border="1" align="center" style="border-collapse:collapse;display:none">
										<tr>
											<th>NON-OEM</th>
											<?php
											foreach($sum['non_oem'] as $buyback_id =>$buyback)
											{
												$lbb_number = $db->func_query_first_cell("select shipment_number from oc_buyback where buyback_id='".(int)$buyback_id."'")
												?>
												<th><?=linkToLbbShipment($lbb_number,$host_path);?></th>
												<?php
											}
											?>
										</tr>
										<?php

										foreach($total_products as $sku=>$description)
										{
											?>
											<tr>
												<td><strong><?=$sku.'-'.$description;?></strong></td>
												<?php
												foreach($sum['non_oem'] as $buyback_id=>$buyback)
												{
													?>
													<td align="center"><?=(int)$buyback[$sku];?></td>
													<?php
												}
												?>
											</tr>
											<?php
										}
										?>

									</table>

									<table width="90%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
										<tr>
											<th>SKU</th>
											<th>Working Qty</th>
											<th>Total Cost</th>
											<th>Refurb Cost</th>
											<th>Non Working Qty</th>
											<th>Non Working LCD(s) Cost</th>
											<th> Cost Per Working LCD</th>

										</tr>
										<?php
										$z=0;
										foreach($products2 as $product)
										{
											$total_lcds = $oem_total+$non_oem_total;
											$total_lcd_cost = ((float)$product['oem_price'] * (int)$product['oem_received']) + ((float)$product['non_oem_price'] * (int)$product['non_oem_received']);


	        		// Forumla by Saad Ahmed (http://prntscr.com/94res2)
											$cost = ($shipment_detail['inbound_shipping_cost']+$shipment_detail['outbound_shipping_cost']) / $total_lcds;

											$cost = $cost + ($product['working_qty']*$product['refurb_cost']);
											$cost = $cost + ((float)$total_lcd_cost - (float)$product['non_working_lcd_cost']); 
											if($cost)
											{
												$cost = $cost / ($product['oem_received']+$product['non_oem_received']);
											}
											else
											{
												$cost = 0.00;
											}
											?>
											<tr>
												<td><?=$product['sku'].' - '.$product['description'];?></td>
												<td align="center"><input type="text" name="list[<?=$product['buyback_product_id'];?>][working_qty]" value="<?=$product['working_qty'];?>" size="4" ></td>
												<td align="center">$<?=number_format($total_lcd_cost,2);?></td>
												<td align="center"><input type="text" name="list[<?=$product['buyback_product_id'];?>][refurb_cost]" value="<?=$product['refurb_cost'];?>" size="6" ></td>

												<td align="center"><input type="text" name="list[<?=$product['buyback_product_id'];?>][non_working_qty]" value="<?=$product['non_working_qty'];?>" size="4" ></td>
												<td align="center"><input type="text" name="list[<?=$product['buyback_product_id'];?>][non_working_lcd_cost]" value="<?=$product['non_working_lcd_cost'];?>" size="4" ></td>
												<td>$<?=number_format($cost,2);?></td>

											</tr>
											<?php
											$z++;
										}

										$z=0;
										foreach($manual_products2 as $product)
										{
											$total_lcds = $oem_total+$non_oem_total;
											$total_lcd_cost = ((float)$product['oem_price'] * (int)$product['oem_received']) + ((float)$product['non_oem_price'] * (int)$product['non_oem_received']);


	        		// Forumla by Saad Ahmed (http://prntscr.com/94res2)
											$cost = ($shipment_detail['inbound_shipping_cost']+$shipment_detail['outbound_shipping_cost']) / $total_lcds;

											$cost = $cost + ($product['working_qty']*$product['refurb_cost']);
											$cost = $cost + ((float)$total_lcd_cost - (float)$product['non_working_lcd_cost']); 
											if($cost)
											{
												$cost = $cost / ($product['oem_received']+$product['non_oem_received']);
											}
											else
											{
												$cost = 0.00;
											}
											?>
											<tr>
												<td><?=$product['sku'].' - '.$product['description'];?></td>
												<td align="center"><input type="text" name="list[<?=$product['sku'];?>][working_qty]" value="<?=$product['working_qty'];?>" size="4" ></td>
												<td align="center">$<?=number_format($total_lcd_cost,2);?></td>
												<td align="center"><input type="text" name="list[<?=$product['sku'];?>][refurb_cost]" value="<?=$product['refurb_cost'];?>" size="6" ></td>

												<td align="center"><input type="text" name="list[<?=$product['sku'];?>][non_working_qty]" value="<?=$product['non_working_qty'];?>" size="4" ></td>
												<td align="center"><input type="text" name="list[<?=$product['sku'];?>][non_working_lcd_cost]" value="<?=$product['non_working_lcd_cost'];?>" size="4" ></td>
												<td>$<?=number_format($cost,2);?></td>

											</tr>
											<?php
											$z++;
										}
										?>
									</table>
								</td>
							</tr>

							<?php
						}
						?>

					</table>
					<hr>

					<div>	
						<?php if($products):?>
							<?php


							?>

							<table width="30%" cellspacing="0" cellpadding="5px" border="0" align="center" style="border-collapse:collapse;">
								<tr>
									<th>OEM Total: <?=$oem_total;?></th>
									<th>Non-OEM Total: <?=$non_oem_total;?></th>
								</tr>
							</table>
							<?php
							if($shipment_detail['status']=='Completed'):
								?>
							<br>

							<table width="40%" class="addToShipment" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
								<thead>
									<tr>
										<th>#</th>

										<th>SKU</th>
										<?php if ($_SESSION['login_as'] == 'admin') : ?>
											<th>OEM</th>
											<th>Non-OEM</th>
										<?php endif; ?>
										<th>Package</th>
										<th>Needed</th>
										<th>Total</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$c_oem = 0;
									$c_non_oem = 0;

									foreach ($products1 as $i => $product) {
										$sku = $db->func_query_first("SELECT sku,description FROM oc_buyback_products WHERE buyback_product_id='".$product['buyback_product_id']."'");
										?>
										<tr>
											<?php if ($_SESSION['add_lbb_shipment']) : ?>
												<td class="select">
													<input type="checkbox" onchange="selectx(this)" value="<?php echo $sku['sku']; ?>">
												</td>
											<?php else: ?>
												<td align="center"><?=$i+1;?></td>
											<?php endif; ?>
											<td align=""><?=$sku['sku'];?> - <?=$sku['description'];?><input type="hidden" name="product[<?php echo $sku['sku']; ?>][product_sku]" value="<?=$sku['sku'];?>"><input type="hidden" name="product[<?php echo $sku['sku']; ?>][product_id]" value="<?=$product['buyback_product_id'];?>"><input type="hidden" name="product[<?php echo $sku['sku']; ?>][product_name]" value="<?=$sku['description'];?>"></td>
											<?php if ($_SESSION['login_as'] == 'admin') : ?>
												<td align="center"><?=$product['oem_received'];?></td>
												<td align="center"><?=$product['non_oem_received'];?></td>
											<?php endif; ?>
											<td align="center">
												<?php $totalShipped = 0;?>
												<?php foreach ($shipments as $key => $shipment) { ?>
												<?php if ($shipment['items'][$sku['sku']]) { ?>
												<?php echo linkToShipment($shipment['id'], $host_path, $shipment['items'][$sku['sku']]['qty_shipped'] . '-' . $shipment['package_number'], ' target="_blank"'); ?>
												<?php $totalShipped += $shipment['items'][$sku['sku']]['qty_shipped']; ?>
												<br>
												<?php } ?>
												<?php } ?>
											</td>
											<td align="center"><?= ($product['non_oem_received'] + $product['oem_received']) - $totalShipped; ?><br><input type="number" style="width: 70px;" min="0" value="0" onchange="$(this).parent().parent().find('input[type=checkbox]').prop('checked', true).trigger('change');" name="product[<?php echo $sku['sku']; ?>][qty_shipped]"></td>
											<td align="center"><?=$product['non_oem_received'] + $product['oem_received'];?></td>
										</tr>
										<?php
									}
									?>


								</tbody>

							</table>
							<?php if ($_SESSION['add_lbb_shipment']) : ?>
								<table width="30%" cellspacing="0" cellpadding="5px" border="0" align="center" style="border-collapse:collapse;">
									<tr>
										<th><input class="button" type="button" class="shipmentBtn" value="Add to Shipment" onclick="selectGetShipment();" /></th>
									</tr>
								</table>
							<?php endif; ?>
							<?php
							endif;
							?>


							<br><br>

							<table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
								<thead>
									<tr>
										<th>#</th>
										<th>Buyback Shipment #</th>
										<th>SKU</th>
										<?php if ($_SESSION['login_as'] == 'admin') : ?>
											<th>OEM</th>
											<th>Non-OEM</th>
										<?php endif; ?>
										<th>Notes</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php $i = $splitPage->display_i_count();
									$count = 1; 
									$shipment_id = $products[0]['shipment_id'];
									$total_oem = 0;
									$total_non_oem = 0;
									foreach($products as $product):
										if($shipment_id != $product['shipment_id']){
											$count = 1; 
											$shipment_id = $product['shipment_id'];
										}
										$total_oem+=(int)$product['oem_received'];
										$total_non_oem+=(int)$product['non_oem_received'];

										$sku = $db->func_query_first("SELECT * FROM oc_buyback_products WHERE buyback_product_id='".$product['buyback_product_id']."'");
										$buyback_number = $db->func_query_first_cell("SELECT shipment_number FROM oc_buyback WHERE buyback_id='".$sku['buyback_id']."'");
										?>
										<?php $reason = $product['reason']; ?>

										<tr>
											<td>
												<input type="checkbox" name="reject_ids[]" value="<?php echo $product['buyback_product_id'];?>" />
											</td>

											<td align="center">
												<a href="<?php $host_path;?>shipment_detail.php?shipment=<?php echo $buyback_number;?>"><?php echo $buyback_number;?></a>
											</td>

											<td align="center">
												<?php echo $sku['sku'];?>
											</td>
											<?php if ($_SESSION['login_as'] == 'admin') : ?>
												<td align="center">
													<?php echo $product['oem_received'];?> x $<?=number_format($sku['oem_price'],2);?>
												</td>

												<td align="center">
													<?php echo $product['non_oem_received'];?> x $<?=number_format($sku['non_oem_price'],2);?>
												</td>
											<?php endif; ?>
											<td align="center">

												<input type="text" name="reason[<?php echo $product['id']?>]" value="<?php echo $reason; ?>" />
											</td>
											<td align="center">
												<a class="fancybox2 fancybox.iframe" href="<?php echo $host_path;?>buyback/move_box_item.php?id=<?php echo $product['id'];?>&shipment_id=<?php echo $product['shipment_id']?>&manual=0">Transfer</a>
											</td>
										</tr>

										<?php 

										$i++; endforeach; ?>
										<?php

										foreach($manual_items as $manual)
										{
											$sku = $db->func_query_first("SELECT sku,description FROM inv_buy_back WHERE sku='".$manual['sku']."'");
											$total_oem+=$manual['oem_qty'];
											$total_non_oem+=$manual['non_oem_qty'];
											?>
											<tr>
												<td>

												</td>

												<td align="center">
													-
												</td>

												<td align="center">
													<?php echo $sku['sku'];?>
												</td>

												<td align="center">
													<?php echo $manual['oem_qty'];?> x $<?=number_format($manual['oem_price'],2);?>
												</td>

												<td align="center">
													<?php echo $manual['non_oem_qty'];?> x $<?=number_format($manual['non_oem_price'],2);?>
												</td>

												<td align="center">

													<?=$db->func_query_first_cell("SELECT name FROM inv_buyback_shipment_reasons WHERE id='".$manual['reason']."'");?>
												</td>
												<td align="center">
													<a class="fancybox2 fancybox.iframe" href="<?php echo $host_path;?>buyback/move_box_item.php?id=<?php echo $manual['id'];?>&shipment_id=<?php echo $product['shipment_id']?>&manual=1">Transfer</a>
												</td>
											</tr>
											<?php
										}
										?>
										<tr>
											<th colspan="3" align="right">Total: </th>
											<th align="center"><?=number_format($total_oem);?></th>
											<th align="center"><?=number_format($total_non_oem);?></th>
											<th colspan="2"></th>
										</tr>
										<tr>
											<td colspan="4" align="left">
												<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
											</td>

											<td colspan="3" align="right">
												<?php  echo $splitPage->display_links(10,$parameters); ?>
											</td>
										</tr>
									</tbody>   
								</table>   



								<div align="center">
									<br />
									<input type="submit" name="print" value="Print" style="display:none" />
									<input type="submit" name="save" value="Save" />
									<?php if($shipment_detail['status'] != 'Completed' && $_SESSION['edit_received_shipment']):?>


										<button type="submit" name="RejectComplete" value="Complete Shipment" onclick="if(!confirm('Are you sure?')){ return false; }">
											Save And Complete Shipment
										</button>
									<?php endif;?>
								</div>

							<?php endif;?>
						</div>	
					</form>

					<table width="80%" border="0" cellpadding="5" cellspacing="0" style="border-collapse:collapse">
						<tr>
							<td>
								<form method="post" action="">
									<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
										<tr>
											<td>

												<b>Comment</b>
											</td>
											<td>
												<textarea rows="5" cols="30" name="comment" required></textarea>


											</td>
										</tr>

										<tr>
											<td colspan="2" align="center"> <input type="submit" class="button" name="addcomment" value="Add Comment" />	</td>
										</tr> 	   
									</table>
								</form>
							</td>
							<td valign="top">

								<table width="90%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
									<tr>
										<th>Date</th>
										<th>Comment</th>


										<th>Added By</th>


									</tr>
									<?php
									$comments = $db->func_query("SELECT * FROM inv_buyback_shipment_box_comments WHERE buyback_shipment_box_id='".$shipment_id."'");
									foreach($comments as $comment)
									{
										?>
										<tr>
											<td><?php echo americanDate($comment['date_added']);?></td>
											<td><?php echo $comment['comment'];?></td>


											<td><?php echo get_username($comment['user_id']);?></td>

										</tr>
										<?php 

									}
									?> 

								</table>

							</td>
						</tr>
					</table>
				</div>
				<script>
					function selectx (t) {

						if ($(t).prop('checked') == true && !$('input[data-pr='+ $(t).val() +']').val()) {
							$(t).parent().parent().append('<input data-pr="'+ $(t).val() +'" name="product['+ $(t).val() +'][update]" value="1" type="hidden">');
						} else if ($(t).prop('checked') == false) {
							$(t).parent().parent().find('input[data-pr='+ $(t).val() +']').remove();
						}

					}
					function selectGetShipment() {
						$('.shipmentBtn').attr('disabled', 'disabled');
						$.ajax({
							url: '<?php echo $pageViewLink; ?>',
							type: 'POST',
							dataType: 'json',
							data: {'action': 'getShipment'}
						})
						.always(function(json) {
							$('body').append(json['data']);
							$('.shipmentBtn').removeAttr('disabled');
						});
					}

					function selectGetBoxs() {
						$('.shipmentBtn').attr('disabled', 'disabled');
						$.ajax({
							url: '<?php echo $pageViewLink; ?>',
							type: 'POST',
							dataType: 'json',
							data: {'action': 'getBoxs'}
						})
						.always(function(json) {
							$('body').append(json['data']);
							$('.shipmentBtn').removeAttr('disabled');
						});
					}

					function mergeBox () {

						if (!$('#mergeBoxId').val()) {
							alert('Please Select Box');
							return false;
						}

						$('.shipmentBtn').attr('disabled', 'disabled');
						$.ajax({
							url: '<?php echo $pageViewLink; ?>',
							type: 'POST',
							dataType: 'json',
							data: {'action':'mergeBox', 'to': $('#mergeBoxId').val(), 'id':'<?php echo $shipment_id;?>'}
						})
						.always(function() {
							window.location.replace("addedit_boxes.php?shipment_id=" + $('#mergeBoxId').val());
						});

					}

					function addShipment () {

						$('.shipmentBtn').attr('disabled', 'disabled');
						var products = [];
						$('.select').each(function() {
							var cBox = $(this).find('input[type=checkbox]');
							if (cBox.is(':checked')) {
								products.push(cBox.val());
							}
						});
						$('.addToShipment').append('<input type="hidden" name="action" value="addShipment" />');
						$('.addToShipment').append('<input type="hidden" name="shipment_id" value="'+ $('#vendor_shipment_id').val() +'" />');
						$.ajax({
							url: '<?php echo $pageViewLink; ?>',
							type: 'POST',
							dataType: 'json',
							data: $('.addToShipment'+' :input')
						})
						.always(function() {
							window.location.reload();
						});

					}
				</script>
			</body>
			</html>