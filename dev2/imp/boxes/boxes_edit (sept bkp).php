<?php

include_once '../auth.php';
include_once '../inc/functions.php';
include_once '../inc/split_page_results.php';


$return = $_GET['return'].".php";

$box_id = (int)$_GET['box_id'];
if(!$box_id){
	$_SESSION['message'] = "Box is not found.";
	header("Location:$host_path/boxes/$return");
	exit;
}


if ($_POST['getAjax']) {
	$json['rejected_shipments'] = $db->func_query('SELECT * FROM inv_rejected_shipments WHERE status != "Completed"');
	$json['product_reason'] = array();
	foreach ($_POST['products'] as $value) {
		$json['product_reason'][$value['reject_id']] = $db->func_query("SELECT a.* FROM inv_rj_reasons AS a INNER JOIN oc_product AS b ON (a.classification_id = b.classification_id) WHERE sku = '". $value['sku'] ."'");
	}
	echo json_encode($json);
	exit;
}
if($_POST['MoveLBB']){
	
	if(count($_POST['reject_ids']) > 0){
		foreach($_POST['reject_ids'] as $reject_id){
			moveItemToLBB($reject_id);
		}
		
		$_SESSION['message'] = "Items moved to LBB.";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=".$_GET['return']);
		exit;
	} else {
		$_SESSION['message'] = "Select at least one sku to move to NTR.";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=".$_GET['return']);
		exit;
	}
}

if($_POST['Transfer']){
	if(count($_POST['reject_ids']) > 0){
		foreach($_POST['reject_ids'] as $reject_id){
			$inv_return_shipment_box_items = array();
			$inv_return_shipment_box_items['return_shipment_box_id'] = $_POST['new_box_id'];
			$db->func_array2update("inv_return_shipment_box_items",$inv_return_shipment_box_items,"return_item_id = '$reject_id'");
			addBoxMoveLog ($box_id, $_POST['new_box_id'], $reject_id);
		}
		
		$_SESSION['message'] = "Return Items are moved to another box.";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=".$_GET['return']);
		exit;
	}	
	else{
		$_SESSION['message'] = "Select at least one sku to move to delete.";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=".$_GET['return']);
		exit;
	}	
}

if($_POST['moveNTR']){
	if(count($_POST['reject_ids']) > 0){
		foreach($_POST['reject_ids'] as $reject_id){
			$box_id = $db->func_query_first_cell('SELECT `return_shipment_box_id` FROM `inv_return_shipment_box_items` WHERE return_item_id = "'. $reject_id .'"');
			$nID = moveItemToBox($reject_id, $_POST[$reject_id], 'NTRBox');
			addBoxMoveLog ($box_id, $nID, $reject_id);
			unset($nID, $box_id);
		}
		
		$_SESSION['message'] = "Items moved to NTR.";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id");
		exit;
	} else {
		$_SESSION['message'] = "Select at least one sku to move to NTR.";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id");
		exit;
	}	
}

if($_POST['print']){ 
	$reject_ids = implode(",",$_POST['reject_ids']);
	if ($reject_ids) {
		header("Location:$host_path/print_shipment.php?ids=$reject_ids");
	} else {
		$_SESSION['message'] = "Please Select Items to print.";
	}
	exit;
}
elseif($_POST['delete']){
	if(count($_POST['reject_ids']) > 0){
		foreach($_POST['reject_ids'] as $reject_id){
			$db->db_exec ( "delete from inv_return_shipment_box_items where return_item_id = '$reject_id' and return_shipment_box_id = '$box_id' " );
		}
		
		$_SESSION['message'] = "Items deleted successfully.";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=".$_GET['return']);
		exit;
	}	
	else{
		$_SESSION['message'] = "Select at least one sku to move to delete.";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=".$_GET['return']);
		exit;
	}	
}
elseif($_POST['MoveRTS']){
	if(count($_POST['reject_ids']) > 0){
		foreach($_POST['reject_ids'] as $reject_id){
			$nID = moveItemToBox($reject_id , $_POST[$reject_id] , 'RTSBox');
			addBoxMoveLog ($box_id, $nID, $reject_id);
			unset($nID);
		}
		
		$_SESSION['message'] = "Items moved to RTS.";
		header("Location:$host_path/boxes/return_to_stock.php?");
		exit;
	}	
	else{
		$_SESSION['message'] = "Select at least one sku to move to NTR.";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=".$_GET['return']);
		exit;
	}	
}
elseif($_POST['MoveNTR']){
	if(count($_POST['reject_ids']) > 0){
		foreach($_POST['reject_ids'] as $reject_id){
			$nID = moveItemToBox($reject_id , $_POST[$reject_id] , 'NTRBox');
			addBoxMoveLog ($box_id, $nID, $reject_id);
			unset($nID);
		}
		$_SESSION['message'] = "Items Moved to NTR";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id");
		exit;
	}	
	else{
		$_SESSION['message'] = "Select at least one sku to move to NTR.";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id");
		exit;
	}
}

if($_POST['MoveReject']){
	if(count($_POST['reject_ids']) > 0){
		foreach($_POST['reject_ids'] as $reject_id){
			returnMoveToRJ($reject_id, $_POST['reject_reason'][$reject_id], $_POST['shipment_id'][$reject_id]);
		}
		
		$_SESSION['message'] = "Items Sent to Reject.";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id");
		exit;
	} else {
		$_SESSION['message'] = "Select at least one sku to Send to Reject.";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id");
		exit;
	}	
}
//save shipment
elseif($_POST['save'] || $_POST['Complete']){ 
	$shipment = array();
	$shipment['box_number'] = $db->func_escape_string($_POST['box_number']);
	$shipment['status'] = 'Issued';
	
	$checkExist = $db->func_query_first_cell("select id from inv_return_shipment_boxes where id != '$box_id' and box_number = '".$shipment['box_number']."'");
	if($checkExist){
		$_SESSION['message'] = "This package number is assigned to another shipment.";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=$return");
		exit;
	}
	else{
		$db->func_array2update("inv_return_shipment_boxes",$shipment,"id = '$box_id'");
		$_SESSION['message'] = "Box is updated";
	}
	
	//now update shipment item reject reason
	$reject_item_ids = $_POST['reject_item_ids'];
	foreach($reject_item_ids as $id => $reject_id){
		$text = $db->func_escape_string($_POST['reason'][$reject_id]);
		$claim_no = $db->func_escape_string($_POST['claim_no'][$reject_id]);
		$carrier_refund_amount = $db->func_escape_string($_POST['carrier_refund_amount'][$reject_id]);
		$reject_id = $db->func_escape_string($reject_id);
		

		$db->db_exec("update inv_return_shipment_box_items SET reason = '$text', return_item_id = '$reject_id' where id = '$id'");
		
		if($claim_no)
		{
			$db->db_exec("update inv_return_shipment_box_items SET claim_no='$claim_no',claim_user_id='".$_SESSION['user_id']."',claim_date_modified='".date('Y-m-d H:i:s')."'  where id = '$id'");

		}if($carrier_refund_amount)
		{
			$db->db_exec("update inv_return_shipment_box_items SET  carrier_refund_amount='$carrier_refund_amount',refund_user_id='".$_SESSION['user_id']."',refund_date_modified='".date('Y-m-d H:i:s')."'  where id = '$id'");

		}
	}
	
	if($_POST['Complete'] && $_SESSION['edit_received_shipment']){
		if(!$shipment['box_number']){
			$_SESSION['message'] = "Box number is required.";
			header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=$return");
			exit;
		}
		
		$db->db_exec("update inv_return_shipment_boxes SET status = 'Completed'  where id = '$box_id'");
		$_SESSION['message'] = "Box status is Completed";
	}
	
	header("Location:$host_path/boxes/$return");
	exit;
}



$box_detail = $db->func_query_first("select * from inv_return_shipment_boxes where id = '$box_id'");

if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}

if($page < 1){
	$page = 1;
}

$parameters = "box_id=$box_id";

$max_page_links = 10;
$num_rows = 500;
$start = ($page - 1)*$num_rows;

$inv_query  = "select si.* , s.box_number, s.box_type from inv_return_shipment_box_items si inner join inv_return_shipment_boxes s on (si.return_shipment_box_id  = s.id)
where s.id = '$box_id' order by si.id DESC";

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "boxes_edit.php",$page);
$products   = $db->func_query($splitPage->sql_query);

/*foreach($products as $index => $product){
	$_query = "select ((pc.raw_cost + pc.shipping_fee) / pc.ex_rate) from inv_product_costs pc where pc.sku = '".$product['product_sku']."' order by pc.id DESC";
	$products[$index]['item_cost'] = round($db->func_query_first_cell($_query),2);
}*/

$boxes = $db->func_query("select id , box_number from inv_return_shipment_boxes where id != '$box_id' and status<>'Completed' and box_type not in ('NotTestedBox','NTRBox') order by box_type");
$boxes1 =$db->func_query("SELECT id, box_number from inv_return_shipment_boxes where id != '$box_id' and status<>'Completed' and  box_type  in ('NotTestedBox') order by id desc limit 1  ");
$boxes2 =$db->func_query("SELECT id, box_number from inv_return_shipment_boxes where id != '$box_id' and status<>'Completed' and  box_type  in ('NTRBox') order by id desc limit 1  ");
$boxes = array_merge($boxes,$boxes1,$boxes2);
$Boxnames = array(
	'CIB' => 'Customer Issue Box',
	'ItemIssueBox' => 'Item Issue Box',
	'NotTestedBox' => 'Not Tested Box',
	'RTSBox' => 'Return to Stock Box',
	'NTRBox' => 'Need to Repair',
	'SDBox' => 'Shipping Damage Box',
	);
	?>
	<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Add / Edit Returns Shipment</title>
		
		<script type="text/javascript" src="<?php echo $host_path;?>js/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo $host_path;?>fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo $host_path;?>/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		
		<script type="text/javascript">
			$(document).ready(function() {
				$('.fancybox').fancybox({ width: '400px', height : '200px' , autoCenter : true , autoSize : true });
				$('.fancybox2').fancybox({ width: '500px', height : '500px' , autoCenter : true , autoSize : true });
			});
		</script>	
	</head>
	<body>
		<?php include_once '../inc/header.php';?>
		<h2 align="center"><?php echo $Boxnames[$products[0]['box_type']]; ?></h2>
		<?php if(@$_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<div align="center">
			<form method="post" action="">
				<br />
				<div>
					<table>
						<tr>
							<td>Box Number:</td>
							<td>
								<input type="text" name="box_number" value="<?php echo $box_detail['box_number'];?>" required />
							</td>
							<td>
								<?php if($box_detail['status'] != 'Completed'):?>
									<input type="submit" name="save" value="Save" />
								<?php endif;?>
							</td>
							<td>&nbsp;</td>
							<?php if($_GET['return'] != 'shipping_damage'):?>
								<td>
									Select Box:
									<select name="new_box_id" id="new_box_id" style="width:150px;">
										<option value="">Select One</option>
										<?php foreach($boxes as $box):?>
											<option value="<?php echo $box['id']; ?>"><?php echo $box['box_number']; ?></option>
										<?php endforeach;?>
									</select>
								</td>
								<td>
									<input type="submit" name="Transfer" value="Transfer" onclick="if(!$('#new_box_id').val()){ alert('Please select one BOX.'); return false;}" />
								</td>
							<?php endif; ?>
						</tr>
					</table>
				</div>
				
				<div align="center">
					<br />
					<a class="fancyboxX3 fancybox.iframe" href="<?php echo $host_path;?>/popupfiles/returns_box_skuadd.php?return_shipment_box_id=<?php echo $box_id?>">Add Item</a>
					
					<input type="submit" name="print" value="Print" />
					
					<input type="submit" name="MoveNTR" value="Move To NTR" />

					<input type="button" onclick="selectRejected();" name="MoveReject" value="Send to rejects" />

					<input type="submit" name="MoveLBB" value="Move to LBB" />

					<?php if($_SESSION['login_as'] == 'admin'):?>
						<input type="submit" name="delete" value="Delete" onclick="if(!confirm('Are you sure?')){ return false; }" />
					<?php endif;?>
					
					

					
					
					<?php if($_GET['return'] == 'not_tested'):?>
						<input type="submit" name="MoveRTS" value="Move to RTS" />
					<?php endif;?>
					
					<?php if($box_detail['status'] != 'Completed' && $_SESSION['edit_received_shipment']):?>
						<button type="submit" name="Complete" value="Complete" onclick="if(!confirm('Are you sure?')){ return false; }">
							Save And Close Box
						</button>
					<?php endif;?>
					
					<br /><br />
				</div>
				
				<div>	
					<?php if($products):?>
						<table style="width:100% !important;" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
							<thead>
								<tr>
									<th width="6%">#</th>
									<th width="6%">Added</th>
									<th width="6%">Order ID / Shipment</th>
									<th width="6%">RMA #</th>
									<th width="6%">Return ID</th>
									<th width="6%">SKU</th>
									<th width="6%">Title</th>
									<?php if($_SESSION['boxes_cost']):?>
										<th width="6%">Cost</th>
										<th width="6%">Price</th>
										<?php if($_GET['return'] == 'shipping_damage') { ?>
										<th width="6%">Shipping Cost</th>
										<?php } ?>	
									<?php endif;?>	

									<th width="6%">Customer Refunded?</th>
									<?php if($_GET['return'] != 'customer_issue_box') { ?>
									<th width="6%">Claim #</th>
									<th width="6%">Carrier Refund Amount</th>
									<?php } ?>
									<th width="6%">Source</th>
									
									
									
									<th width="6%">Reason</th>
									<th width="16%" colspan="4">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php $i = $splitPage->display_i_count();
								$count = 1; 
								foreach($products as $product):

									$is_refunded = $db->func_query_first_cell("SELECT b.decision from inv_returns a ,inv_return_items b WHERE a.id=b.return_id AND a.rma_number='".$product['rma_number']."' and b.sku='".$product['product_sku']."' ");
								
								if($is_refunded=='Issue Refund')
								{
									$is_customer_refunded = 'Yes';
								}
								else
								{
									$is_refunded = $db->func_query_first_cell("SELECT b.action from inv_returns a ,inv_return_decision b WHERE a.id=b.return_id AND a.rma_number='".$product['rma_number']."' and b.sku='".$product['product_sku']."' ");
									

									if($is_refunded=='Issue Refund')
									{
										$is_customer_refunded = 'Yes';
									}
									else
									{
										
										$is_customer_refunded = 'No';
									}
								}

								?>
								<tr class="return_product return_<?php echo $product['return_item_id'];?>" data-sku="<?php echo $product['product_sku'];?>" data-return-id="<?php echo $product['return_item_id'];?>">
									<td>
										<input type="checkbox" name="reject_ids[]" value="<?php echo $product['return_item_id'];?>" />
									</td>
									
									<td align="center"><?php echo americanDate($product['date_added']);?></td>
									<?php
									$price = 0.00;
									if($product['shipment_id'])
									{

										?>
										<td align="center"><?php echo linkToShipment($product['shipment_id'],$host_path,$db->func_query_first_cell("SELECT package_number from inv_shipments where id='".(int)$product['shipment_id']."'")); ?></td>
										<?php
									}
									else
									{
										$price = $db->func_query_first_cell("SELECT product_unit FROM inv_orders_items WHERE order_id='".$product['order_id']."' AND product_sku='".$product['product_sku']."'")
										?>
										<td align="center">
											<a href="<?php echo $host_path;?>viewOrderDetail.php?order=<?php echo $product['order_id'];?>"><?php echo $product['order_id'];?></a>
										</td>
										<?php
									}
									?>
									<td align="center">
										<a href="<?php echo $host_path;?>return_detail.php?rma_number=<?php echo $product['rma_number'];?>"><?php echo $product['rma_number'];?></a>
									</td>

									<td align="center">
										<input name="reject_item_ids[<?php echo $product['id']?>]" value="<?php echo $product['return_item_id'];?>" required />
									</td>
									
									<td align="center"><a href="<?php echo $host_path;?>product/<?php echo $product['product_sku'];?>"><?php echo $product['product_sku'];?></a></td>
									<td align="center"><?php echo getItemName($product['product_sku']);?></td>

									<?php if($_SESSION['boxes_cost']):?>
										<td align="center">
											$<?php echo $product['cost'];?>
										</td>

										<td align="center">$<?=number_format($price,2);?></td>

										<?php if($_GET['return'] == 'shipping_damage') { ?>
										<td align="center">$<?=number_format($db->func_query_first_cell('SELECT shipping_cost FROM inv_orders_details WHERE order_id = "'. $product['order_id'] .'"'),2);?></td>
										<?php } ?>	
									<?php endif;?>
									<td align="center"><?=$is_customer_refunded;?></td>

									<?php if($_GET['return'] != 'customer_issue_box') { ?>

									<?php
									if($product['claim_no']!='')
									{
										?>
										<td align="center"><?=$product['claim_no'];?> / <?=get_username($product['claim_user_id']);?><br>
											<small style="font-size:9px"><?=americanDate($product['claim_date_modified']);?></small>
										</td>
										<?php
									}
									else
									{
										?>
										<td>
											<input  type="text" name="claim_no[<?php echo $product['return_item_id']?>]" value="" />

										</td>
										<?php

									}
									?>
									
									<?php
									if($product['carrier_refund_amount']!='' and $product['carrier_refund_amount']!='0.00' )
									{
										?>
										<td align="center"><?=$product['carrier_refund_amount'];?> / <?=get_username($product['refund_user_id']);?><br>
											<small style="font-size:9px"><?=americanDate($product['refund_date_modified']);?></small>
										</td>
										<?php
									}
									else
									{
										?>
										<td>
											<input  type="text" name="carrier_refund_amount[<?php echo $product['return_item_id']?>]" value="0.00" size="5" />

										</td>
										<?php

									}
									?>
									<?php } ?>
									
									<td align="center"><?php echo $product['source'];?></td>
									
									
									
									
									
									<td align="center">
										<input type="hidden" value="<?php echo $product['product_sku'];?>" name="<?php echo $product['return_item_id'];?>" />
										<input  type="text" name="reason[<?php echo $product['return_item_id']?>]" value="<?php echo $product['reason']?>" />
									</td>
									
									<td align="center">
										<a class="fancybox2 fancybox.iframe" href="<?php echo $host_path;?>popupfiles/move_box_item.php?box_id=<?php echo $product['return_shipment_box_id'];?>&reject_id=<?php echo $product['return_item_id']?>">Transfer</a>
										
									</td>
									<td><a href="javascript:void(0)" onclick="moveItem('MoveNTR', this);">Move To NTR</a></td>
									<td><a href="javascript:void(0)" onclick="moveItem('MoveLBB', this);">Move To LBB</a></td>
							       <td><a href="javascript:void(0)" onclick="moveItem('MoveReject', this);">Rejects</a></td>



								</tr>
								<?php $i++; endforeach; ?>
								
								<tr>
									<td colspan="3" align="left">
										<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
									</td>
									
									<td colspan="8" align="right">
										<?php  echo $splitPage->display_links(10,$parameters); ?>
									</td>
								</tr>
							</tbody>   
						</table>   
						
						<div align="center">
							<br />
							<input type="submit" name="print" value="Print" />

							<input type="submit" name="MoveNTR" value="Move To NTR" />

							<input type="button" onclick="selectRejected();" name="MoveReject" value="Send to rejects" />

							<input type="submit" name="MoveLBB" value="Move to LBB" />
							
							<?php if($_SESSION['login_as'] == 'admin'):?>
								<input type="submit" name="delete" value="Delete" onclick="if(!confirm('Are you sure?')){ return false; }" />
							<?php endif;?>
							
							

								
							
							<?php if($_GET['return'] == 'not_tested'):?>
								<input type="submit" name="MoveRTS" value="Move to RTS" />
							<?php endif;?>
							
							<?php if($box_detail['status'] != 'Completed' && $_SESSION['edit_received_shipment']):?>
								<button type="submit" name="Complete" value="Complete" onclick="if(!confirm('Are you sure?')){ return false; }">
									Save And Close Box
								</button>
							<?php endif;?>
						</div>
						
					<?php endif;?>
				</div>
				<div class="blackPage" style="display: none;">
				<div class="whitePage">
					<div class="form list">
						<!-- <select name="shipment_id" id="vendor_shipment_id">
							<option value="">--Create New--</option>
							<?php if ($rejected_shipments) { ?>
							<?php foreach ($rejected_shipments as $key => $row) { ?>
							<option value="<?php echo $row['id']; ?>"><?php echo $row['package_number']; ?></option>
							<?php } ?>
							<?php } ?>
						</select> -->
					</div>
					<div class="form">
						<input type="submit" name="MoveReject" value="Submit" />
						<input class="button" type="button" value="Cancel" onclick="$('.blackPage').hide();" />
					</div>
				</div>
			</div>
			</form>
		</div>  

<script type="text/javascript">
		function moveItem(box, t) {
			var pCont = $(t).parent().parent();
			pCont.find('input[type=checkbox]').prop('checked', true);
			$('input[name="' + box + '"]').first().trigger('click');
		}


		function selectRejected () {
			var product_sku = [];
			var i = 0;
			$('.return_product').each(function(index, el) {
				if ($(el).find('input[type="checkbox"]').prop('checked')) {
					product_sku.push(i);
					product_sku[i] = {};
					product_sku[i]['sku'] = $(el).attr('data-sku');
					product_sku[i]['reject_id'] = $(el).attr('data-return-id');
					i++;
				}
				
			});
			$.ajax({
				url: '',
				type: 'POST',
				dataType: 'json',
				data: {products: product_sku, getAjax: 'yes'},
			})
			.always(function(json) {
				if (product_sku) {
					var html = '<table border="1" style="border-collapse:collapse;" width="94%" cellspacing="0" align="center" cellpadding="3">';
					for (var key in json['product_reason']) {
						html += '<tr>';
						html += '<td>' + key + '</td>';
						html += '<td>';
						html += '<select required="" name="shipment_id['+ key +']">';
						html += '<option value="">Please Select</option>';
						for (var i = json['rejected_shipments'].length - 1; i >= 0; i--) {
							html += '<option value="'+ json['rejected_shipments'][i]['id'] +'">'+ json['rejected_shipments'][i]['package_number'] +'</option>';
						}
						html += '</select>';
						html += '</td>';
						html += '<td>';
						html += '<select required="" name="reject_reason['+ key +']">';
						html += '<option value="">Please Select</option>';
						for (var i = json['product_reason'][key].length - 1; i >= 0; i--) {
							html += '<option value="'+ json['product_reason'][key][i]['id'] +'">'+ json['product_reason'][key][i]['name'] +'</option>';
						}
						html += '</select>';
						html += '</td>';
						html += '</tr>';
					}
					html += '</table>';
					$('.whitePage .form.list').html(html);
					$('.blackPage').show();
				}
			});
}

		</script>


	</body>
	</html>