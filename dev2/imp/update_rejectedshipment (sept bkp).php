<?php

include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';

include_once 'auth.php';

$shipment_id = (int)$_GET['shipment_id'];
if(!$shipment_id){
	$shipment_id = $db->func_query_first_cell("select id from inv_rejected_shipments where status != 'Completed'");
}

if(!$shipment_id){
	$_SESSION['message'] = "No new sku added in rejected list";
	header("Location:rejected_shipments.php");
	exit;
}

//save shipment
if($_POST['save']) { 
	
	$allowed = array('png', 'jpeg', 'jpg');
	//now update shipment item reject reason
	$reasons = $_POST['reason'];
	$shipment_id = $_POST['shipment_id'];
	foreach($reasons as $id => $reason){
		$text = $reason[0];
		$fileN = '';
		if ($_FILES['image']['tmp_name'][$id]) {
			$uniqid = uniqid();
			$name = explode(".", $_FILES['image']['name'][$id]);
			$ext = end($name);
			$fileName = $uniqid . '-' . $id . '.' . $ext;
			$destination = $path . 'files/' . $fileName;
			$file = $_FILES['image']['tmp_name'][$id];
			if (in_array($ext, $allowed)) {
				if (move_uploaded_file($file, $destination)) {
					$fileN = $fileName;
				}
			}
		}
		$ship_id = $shipment_id[$id];

		$db->db_exec("UPDATE inv_rejected_shipment_items SET rejected_shipment_id = '$ship_id', reject_reason = '$text', image = '$fileN' where id = '$id'");

		$item = $db->func_query_first("SELECT * FROM inv_rejected_shipment_items WHERE id = '$id'");
		$shipmentName = $db->func_query_first_cell( "select package_number from inv_rejected_shipments where id = '$ship_id'" );
		printLabel($item['reject_item_id'], $item['product_sku'], $shipmentName, $db->func_query_first_cell( "select name from inv_rj_reasons where id = '". $item['reject_reason'] ."'" ), $item['shipment_id'], $_POST['printerid']);

		logRejectItem($_POST['reject_item_id'][$id], 'Moved to ' . $shipmentName);
	}

	if ($_POST['ntr_reason']) {
		$reasons = $_POST['ntr_reason'];
		foreach($reasons as $id => $reason){
			$reason = $db->func_escape_string($reason);

			$fileN = '';
			if ($_FILES['image']['tmp_name'][$id]) {
				$uniqid = uniqid();
				$name = explode(".", $_FILES['image']['name'][$id]);
				$ext = end($name);
				$fileName = $uniqid . '-' . $id . '.' . $ext;
				$destination = $path . 'files/' . $fileName;
				$file = $_FILES['image']['tmp_name'][$id];
				if (in_array($ext, $allowed)) {
					if (move_uploaded_file($file, $destination)) {
						$fileN = $fileName;
					}
				}
			}

			$db->db_exec("UPDATE inv_return_shipment_box_items SET  reason = '$reason', image = '$fileN' WHERE id = '$id'");

			$item = $db->func_query_first("SELECT * FROM inv_return_shipment_box_items WHERE id = '$id'");
			$shipmentName = $db->func_query_first_cell( "select box_number from inv_return_shipment_boxes where id = '". $item['return_shipment_box_id'] ."'" );
			printLabel($item['return_item_id'], $item['product_sku'], $shipmentName, $item['reason'], $item['shipment_id'], $_POST['printerid']);
		}
	}
	
	header("Location:rejected_shipments.php");
	exit;

}

$ship_id = (int)$_GET['id'];

$shipment_detail = $db->func_query_first("select * from inv_rejected_shipments where id = '$shipment_id'");

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

$inv_query  = "select si.* , s.package_number from inv_rejected_shipment_items si inner join inv_shipments s on (si.shipment_id = s.id)
where rejected_shipment_id = '$shipment_id' and shipment_id = '$ship_id' order by shipment_id";

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "addedit_rejectedshipment.php",$page);
$products   = $db->func_query($splitPage->sql_query);

$ntr_ids = str_replace(',', "','", $_GET['ntr']);
$ntrItems = array();
if ($ntr_ids) {
	$ntrItems = $db->func_query("SELECT * FROM inv_return_shipment_box_items WHERE id in ('$ntr_ids')");	
}

$rejected_shipments = $db->func_query('SELECT * FROM inv_rejected_shipments WHERE vendor = "'. $shipment_detail['vendor'] .'" AND status != "Completed"');

$printers = array(
	array('id' => '157967', 'value' => 'QC1'),
	array('id' => '157973', 'value' => 'QC2'),
	array('id' => '157982', 'value' => 'Receiving'),
	array('id' => '136097', 'value' => 'Storefront')
	);

	?>
	<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Add / Edit Rejected Shipment</title>

		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

		<script type="text/javascript">
			$(document).ready(function() {
				$('.fancybox').fancybox({ width: '400px', height : '200px' , autoCenter : true , autoSize : true });
				$('.fancybox2').fancybox({ width: '500px', height : '500px' , autoCenter : true , autoSize : true });
			});
		</script>	
	</head>
	<body>
		<?php include_once 'inc/header.php';?>

		<?php if(@$_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>

		<div align="center">
			<form method="post" action="" enctype="multipart/form-data">
				<br />
				<p>
					Printer:
					<select required="" name="printerid" id="printerid">
						<option value="">Select One</option>
						<?php foreach ($printers as $printer): ?>
							<option value="<?php echo $printer['id'] ?>" <?php if ($printer['id'] == $return_item['printer']): ?> selected="selected" <?php endif; ?>>
								<?php echo $printer['value'] ?>
							</option>
						<?php endforeach; ?>
					</select>
				</p>
				<br />
				<br />
				<h2>Update reject item reasons</h2>
				<div>	
					<?php if($products):?>
						<table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
							<thead>
								<tr>
									<th>Reject ID</th>
									<th>SKU</th>
									<th>Vendor</th>
									<th>Shipments</th>
									<th>Reason</th>
								</tr>
							</thead>
							<tbody>
								<?php $i = $splitPage->display_i_count();
								$count = 1; 
								$shipment_id = $products[0]['shipment_id'];
								?>
								<?php foreach($products as $product): ?>
									<?php
									if($shipment_id != $product['shipment_id']) {
										$count = 1; 
										$shipment_id = $product['shipment_id'];
									}
									?>

									<?php for($j=0;$j<$product['qty_rejected'];$j++): ?>
										<tr>
											<td align="center">
												<input type="hidden" value="<?php echo $product['reject_item_id'];?>" name="reject_item_id[<?php echo $product['id']?>]"/>
												<?php echo $product['reject_item_id'];?>
											</td>

											<td align="center">
												<?php echo $product['product_sku'];?>
											</td>

											<td align="center">
												<?php echo str_replace(',', '<br>', $db->func_query_first_cell('SELECT GROUP_CONCAT(name) FROM inv_product_vendors as a inner join inv_users as b ON (a.vendor = b.id) WHERE product_sku = "'. $product['product_sku'] .'"')); ?>
											</td>

											<td align="center">
												<select name="shipment_id[<?php echo $product['id']?>]">
													<?php foreach ($rejected_shipments as $rejected_shipment) {?>
													<option <?php echo (($rejected_shipment['id'] == $product['rejected_shipment_id']) ? 'selected="selected"': ''); ?> value="<?php echo $rejected_shipment['id']; ?>"><?php echo $rejected_shipment['package_number']; ?></option>
													<?php }?>
												</select>
											</td>

											<td align="center">
												<?php $reasons = $db->func_query("SELECT * FROM inv_rj_reasons WHERE classification_id = (SELECT classification_id FROM oc_product where model = '". $product['product_sku'] ."')")?>
												<select required="" name="reason[<?php echo $product['id']?>][<?php echo $j?>]">
													<option value=''>Select</option>
													<?php foreach ($reasons as $reason) { ?>
													<option <?php echo ($product['reject_reason'] == $reason['id'])? 'selected="selected"': ''; ?> value="<?php echo $reason['id']; ?>"><?php echo $reason['name'] ?></option>
													<?php } ?>
												</select>
												<!-- <input required type="text" name="reason[<?php echo $product['id']?>][<?php echo $j?>]" value="<?php echo $product['reject_reason']?>" /> -->
												<label class="ui blue button upMain" style="color: #fff;" for="mainimageup" onclick="">
													<input type="file" style="opacity: 0; position: absolute; width: 100%; height: 23px; top: 0; left: 0;" name="image[<?php echo $product['id']?>]" id="mainimageup" accept="image/jpeg,image/png">
													Upload Image
												</label>
											</td>
										</tr>
										<?php $count++; ?>
									<?php endfor;?>       

									<?php $i++; ?> 
								<?php endforeach; ?>

							</tbody>   
						</table>   

					<?php endif;?>
					<?php if($ntrItems):?>
						<br><br>
						<h2>Update NTR item reasons</h2>
						<table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
							<thead>
								<tr>
									<th>Return ID</th>
									<th>SKU</th>
									<th>Reason</th>
								</tr>
							</thead>
							<tbody>

								<?php foreach($ntrItems as $product): ?>
									<tr>
										<td align="center">
											<?php echo $product['return_item_id'];?>
										</td>

										<td align="center">
											<?php echo $product['product_sku'];?>
										</td>

										<td align="center">
											<input required type="text" name="ntr_reason[<?php echo $product['id']?>]" value="<?php echo $product['reason']; ?>" />
											<label class="ui blue button upMain" style="color: #fff;" for="mainimageup" onclick="">
												<input type="file" style="opacity: 0; position: absolute; width: 100%; height: 23px; top: 0; left: 0;" name="image[<?php echo $product['id']?>]" id="mainimageup" accept="image/jpeg,image/png">
												Upload Image
											</label>
										</td>
									</tr>
									<?php $count++; ?>
								<?php endforeach; ?>

									<!-- <tr>
										<td colspan="2" align="left">
											<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
										</td>

										<td colspan="3" align="right">
											<?php  echo $splitPage->display_links(10,$parameters); ?>
										</td>
									</tr> -->
								</tbody>   
							</table>   


						<?php endif;?>
						<div align="center">
							<br />
							<input type="submit" name="save" value="Save" />
						</div>
					</div>	
				</form>
			</div>             
		</body>
		</html>        