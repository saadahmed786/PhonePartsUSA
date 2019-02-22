<?php
require_once("../auth.php");
include_once '../inc/split_page_results.php';
include_once '../inc/functions.php';
$page = (int)$_GET['page'];
if(!$page){
	$page = 1;
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
		header("Location:$host_path/boxes/need_to_repair.php");
		exit;
	} else {
		$_SESSION['message'] = "Select at least one sku to move to NTR.";
		header("Location:$host_path/boxes/need_to_repair.php");
		exit;
	}
}
if($_POST['MoveRTS']){
	if(count($_POST['reject_ids']) > 0){
		foreach($_POST['reject_ids'] as $reject_id){
			$box_id = $db->func_query_first_cell('SELECT `return_shipment_box_id` FROM `inv_return_shipment_box_items` WHERE return_item_id = "'. $reject_id .'"');
			$nID = moveItemToBox($reject_id, $_POST[$reject_id], 'RTSBox');
			addBoxMoveLog ($box_id, $nID, $reject_id);
			unset($nID, $box_id);
		}
		
		$_SESSION['message'] = "Items moved to RTS.";
		header("Location:$host_path/boxes/need_to_repair.php");
		exit;
	} else {
		$_SESSION['message'] = "Select at least one sku to move to NTR.";
		header("Location:$host_path/boxes/need_to_repair.php");
		exit;
	}	
}
if($_POST['MoveReject']){
	if(count($_POST['reject_ids']) > 0){
		foreach($_POST['reject_ids'] as $reject_id){
			returnMoveToRJ($reject_id, $_POST['reject_reason'][$reject_id], $_POST['shipment_id'][$reject_id]);
		}
		
		$_SESSION['message'] = "Items Sent to Reject.";
		header("Location:$host_path/boxes/need_to_repair.php");
		exit;
	} else {
		$_SESSION['message'] = "Select at least one sku to Send to Reject.";
		header("Location:$host_path/boxes/need_to_repair.php");
		exit;
	}	
}
elseif($_POST['save']){
	//now update shipment item reject reason
	$reject_item_ids = $_POST['hidden_reject_ids'];
	
	foreach($reject_item_ids as $id => $reject_id){
		$text = $db->func_escape_string($_POST['reason'][$reject_id]);
		$reject_id = $db->func_escape_string($reject_id);
		// echo "update inv_return_shipment_box_items SET reason = '$text' , return_item_id = '$reject_id' where id = '$id'"."<br>";
		$db->db_exec("update inv_return_shipment_box_items SET reason = '$text' , return_item_id = '$reject_id' where id = '$id'");
	}
	// exit;
	
	$_SESSION['message'] = "Items changes are saved.";
	header("Location:$host_path/boxes/need_to_repair.php");
	exit;
}
elseif($_POST['delete']){
	if(count($_POST['reject_ids']) > 0){
		foreach($_POST['reject_ids'] as $reject_id){
			$db->db_exec ( "delete from inv_return_shipment_box_items where return_item_id = '$reject_id'" );
		}
		
		$_SESSION['message'] = "Items deleted successfully.";
		header("Location:$host_path/boxes/need_to_repair.php");
		exit;
	}	
	else{
		$_SESSION['message'] = "Select at least one sku to move to delete.";
		header("Location:$host_path/boxes/need_to_repair.php");
		exit;
	}	
}
$where = array();
if($_GET['rma_number']){
	$rma_number = $db->func_escape_string(trim($_GET['rma_number']));
	$where[] = " LCASE(rma_number) = LCASE('$rma_number') ";
	$parameters[] = "rma_number=$rma_number";
}
if($_GET['order_id']){
	$order_id = $db->func_escape_string(trim($_GET['order_id']));
	$where[] = " order_id = '$order_id' ";
	$parameters[] = "order_id=$order_id";
}
if($where){
	$where = implode(" AND ",$where);
}
else{
	$where = ' 1 = 1';
}
$_query = "select si.* from inv_return_shipment_box_items si inner join inv_return_shipment_boxes s on (s.id = si.return_shipment_box_id)
where $where and box_type = 'NTRBox' order by date_added desc";
// $splitPage = new splitPageResults($db , $_query , 25 , "need_to_repair.php",$page ,  $count_query);
// $ntr_items = $db->func_query($splitPage->sql_query);
$ntr_items = $db->func_query($_query);
foreach($ntr_items as $index => $ntr_item){
	if($ntr_item['shipment_id']){
		$ntr_items[$index]['shipment_number'] = $db->func_query_first_cell("select package_number from inv_shipments where id = '".$ntr_item['shipment_id']."'");
	}
	
	$_query = "select ((pc.raw_cost + pc.shipping_fee) / pc.ex_rate) from inv_product_costs pc where pc.sku = '".$ntr_item['product_sku']."' order by pc.id DESC";
	$ntr_items[$index]['item_cost'] = round($db->func_query_first_cell($_query),2);
}
if($parameters){
	$parameters = implode("&",$parameters);
}
else{
	$parameters = '';
}
//check if there is any box open
$inv_return_shipment_box_id = $db->func_query_first_cell("select id from inv_return_shipment_boxes where box_number LIKE '%NTRBox%' and status = 'Issued'");
if(!$inv_return_shipment_box_id){
	$return_shipment_boxes_insert = array ();
	$return_shipment_boxes_insert ['box_number'] = getReturnBoxNumber ( 0, 'NTRBox' );
	$return_shipment_boxes_insert ['box_type']   = 'NTRBox';
	$return_shipment_boxes_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
	$inv_return_shipment_box_id = $db->func_array2insert ( "inv_return_shipment_boxes", $return_shipment_boxes_insert );
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Needs to Repair Items</title>
	<script type="text/javascript" src="<?php echo $host_path;?>js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $host_path;?>fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $host_path;?>/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	
	<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox({ width: '400px', height : '200px' , autoCenter : true , autoSize : true });
			$('.fancybox2').fancybox({ width: '500px', height : '500px' , autoCenter : true , autoSize : true });
		});
	</script>
	<style type="text/css">
		.data td,.data th{
			border: 1px solid #e8e8e8;
			text-align:center;
			width: 150px;
		}
		.div-fixed{
			position:fixed;
			top:0px;
			left:8px;
			background:#fff;
			width:98.8%; 
		}
		.red td{ box-shadow:1px 2px 5px #990000;}
	</style>
</head>
<body>
	<div align="center"> 
		<?php include_once '../inc/header.php';?>
	</div>
	<h2 align="center">Need To Repair Items</h2>
	<?php if($_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php else:?>
		<br /><br /> 
	<?php endif;?>
	
	<div align="center">
		<a class="fancybox3 fancybox.iframe" href="<?php echo $host_path;?>/popupfiles/returns_box_skuadd.php?return_shipment_box_id=<?php echo $inv_return_shipment_box_id?>">Add Item</a>
		<br /><br /> 
	</div>	
	<div align="center">
		<form action="" method="get">
			<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
				<tr>
					<td>
						RMA Number: <?php echo createField("rma_number","rma_number","text",$_GET['rma_number']);?>				        
					</td>
					
					<td>
						Order ID: <?php echo createField("order_id","order_id","text",$_GET['order_id']);?>				        
					</td>
					
					<td>
						<input type="submit" name="search" value="Search" class="button" />
					</td>
				</tr>	
			</table>
			<br />
		</form>
	</div>			
	
	<div>	
		<form action="" method="post">
			<br><br>
			<div align="center">
				<input type="submit" name="save" value="Save" />
				<input type="submit" name="MoveRTS" value="Move to RTS" />
				<input type="submit" name="MoveLBB" value="Move to LBB" />
				<input type="button" onclick="selectRejected();" name="MoveReject" value="Send to Rejects" />
				
				<?php if($_SESSION['login_as'] == 'admin'):?>
					<input type="submit" name="delete" value="Delete" onclick="if(!confirm('Are you sure?')){ return false; }" />
				<?php endif;?>
			</div>
			<br><br>
			<table class="data" border="1" style="border-collapse:collapse;" width="94%" cellspacing="0" align="center" cellpadding="3">
				<thead>
					<tr style="background:#e5e5e5;">
						<th style="width:50px;">#</th>
						<th>Date Added</th>
						<th>Order ID / Shipment ID</th>
						<th>RMA</th>
						<th>Return ID</th>
						<th>SKU</th>
						<th>Reason</th>
						<?php if($_SESSION['boxes_cost']):?>
							<th>Cost</th>
						<?php endif;?>	
						<th>Date Added</th>
						<th colspan="3">Action</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($ntr_items as $k => $ntr_item):?>
						<?php $ntr_item['cost'] = ($ntr_item['order_id'])? $db->func_query_first_cell('select product_price from inv_orders_items where order_id = "'. $ntr_item['order_id'] .'" AND product_sku = "'. $ntr_item['product_sku'] .'"'):$ntr_item['cost'];?>
						<tr class="return_product return_<?php echo $ntr_item['return_item_id'];?>" data-sku="<?php echo $ntr_item['product_sku'];?>" data-return-id="<?php echo $ntr_item['return_item_id'];?>">
							<td style="width:50px;">
								<input type="checkbox" name="reject_ids[]" value="<?php echo $ntr_item['return_item_id'];?>" />
								<input type="hidden" name="hidden_reject_ids[<?php echo $ntr_item['id'];?>]" value="<?php echo $ntr_item['return_item_id'];?>">
								<?php echo $k+1;?>
							</td>			   		
							<td><?php echo americanDate($ntr_item['date_added']); ?></td>
							<td><?php echo ($ntr_item['shipment_id'])? linkToShipment($ntr_item['shipment_id'], $host_path, $ntr_item['shipment_number']): linkToOrder($ntr_item['order_id'], $host_path); ?></td>
							<td><?php echo linkToRma($ntr_item['rma_number'], $host_path);?></td>
							<td align="center"><?php echo $ntr_item['return_item_id'];?></td>
							<td><?php echo linkToProduct($ntr_item['product_sku'], $host_path, ' target="_blank"');?></td>
							<td>
								<input type="hidden" value="<?php echo $ntr_item['product_sku'];?>" name="<?php echo $ntr_item['return_item_id'];?>" />
								<input type="text"  name="reason[<?php echo $ntr_item['return_item_id']?>]" value="<?php echo $ntr_item['reason']?>" />
							</td>
							<?php if($_SESSION['boxes_cost']):?>
								<td>
									$<?php echo number_format($ntr_item['cost'],2);?>
								</td>
							<?php endif;?>
							<td><?php echo americanDate($ntr_item['date_added']);?></td>
							<td><a href="javascript:void(0)" onclick="moveItem('MoveRTS', this);">Move To RTS</a></td>
							<td><a href="javascript:void(0)" onclick="moveItem('MoveLBB', this);">Move To LBB</a></td>
							<td><a href="javascript:void(0)" onclick="moveItem('MoveReject', this);">Reject</a></td>
						</tr>
					<?php endforeach;?>
				</tbody>
			</table>
			
			<br /><br />
			
			<div align="center">
				<input type="submit" name="save" value="Save" />
				<input type="submit" name="MoveRTS" value="Move to RTS" />
				<input type="submit" name="MoveLBB" value="Move to LBB" />
				<input type="button" onclick="selectRejected();" name="MoveReject" value="Send to Rejects" />
				
				<?php if($_SESSION['login_as'] == 'admin'):?>
					<input type="submit" name="delete" value="Delete" onclick="if(!confirm('Are you sure?')){ return false; }" />
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
		
		<br /><br />
		<table class="footer" border="0" style="border-collapse:collapse;" width="95%" align="center" cellpadding="3">
			<tr>
				<td colspan="7" align="left">
					<?php // echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
				</td>
				
				<td colspan="6" align="right">
					<?php // echo $splitPage->display_links(10,$parameters);?>
				</td>
			</tr>
		</table>
		<br />
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