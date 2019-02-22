<?php

include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';
$st = $db->func_query_first("Select count(id) from inv_shipment_comments where shipment_id = '".$shipment."'");
if ($_POST['action'] == 'load-comments') {
	$comments = $db->func_query("SELECT * FROM inv_shipment_comments WHERE shipment_id='".$_POST['shipment_id']."'");
	
	$json['data'] = '<table border="0">';
	$json['data'] .= '<thead>';
	$json['data'] .= '<tr><th width="40%">Name</th><th>Comment</th></tr>';
	$json['data'] .= '</thead>';
	$json['data'] .= '<tbody>';
	if ($comments) {
		foreach($comments as $comment) {
			$name = get_username($comment['user_id']);
			$usercomment = $comment['comment'];
			$json['data'] .= '<tr><td>' . $name . ' </td> <td>' . $usercomment . '</td></tr>';
		}
	} else {
		$json['data'] .= '<tr><td colspan="6">No Comments found</td></tr>';
	}
	$json['data'] .= '</tbody>';
	$json['data'] .= '</table>';

	echo json_encode($json);
	exit;
}

$is_vendor = false;

if($_SESSION['login_as'] != 'admin')
{
	$user_det = $db->func_query_first("SELECT * FROM inv_users WHERE id='".(int)$_SESSION['user_id']."'");
	if($user_det['group_id']==1){	
		$is_vendor = true;
	}
	else{
		$is_vendor = false;	
	}
}

if((int)$_GET['id'] and $_GET['action'] == 'ignore'){
	$shipment_id = (int)$_GET['id'];
	$db->db_exec("update inv_shipments SET ignored = '0' where id = '$shipment_id'");

	$log = 'Shipment #: ' . linkToShipment($shipment_id, $host_path, $_GET['no']) . ' is Ignored';
	actionLog($log);

	$_SESSION['message'] = "Shipment status is ignored";
	header("Location:shipments.php");
	exit;
}

if((int)$_GET['id'] and $_GET['action'] == 'issue' && $_SESSION['edit_pending_shipment']){
	$shipment_id = (int)$_GET['id'];
	$check = $db->func_query_first("SELECT * from inv_shipments WHERE id='".$shipment_id."'");
	
	if($check['ex_rate']<=0.00)
	{
		
		$_SESSION['message'] ='Shipment cannot be issued because the exchange rate is not provided';
		header("Location:shipments.php");
		exit;
	}
	elseif(strlen($check['tracking_number'])<=4 || $check['carrier']=='' || $check['shipping_cost']<0.00 )
	{
		$_SESSION['message'] ='Shipment cannot be issued because of invalid tracking or shipping cost';
		header("Location:shipments.php");
		exit;
	}
	

	$db->db_exec("update inv_shipments SET status = 'Issued' , date_issued = '".date('Y-m-d H:i:s')."' where id = '$shipment_id'");

	$log = 'Shipment #: ' . linkToShipment($shipment_id, $host_path, $_GET['no']) . ' is Issued';
	actionLog($log);

	$_SESSION['message'] = "Shipment status is Issued";
	header("Location:shipments.php");
	exit;
}

if((int)$_GET['id'] and $_GET['action'] == 'receive' && $_SESSION['edit_received_shipment']){
	$shipment_id = (int)$_GET['id'];
	$checkError = $db->func_query_first_cell("select count(id) from inv_shipment_items 
		where shipment_id = '$shipment_id' AND (qty_received is null OR qty_received = '') ");
	if($checkError > 0){
		$_SESSION['message'] = "You have not entered all received qty for each item.";
		header("Location:addedit_shipment.php?shipment_id=$shipment_id");
	}
	else{
		$db->db_exec("update inv_shipments SET status = 'Received' , date_received = '".date('Y-m-d H:i:s')."'  where id = '$shipment_id'");
		$_SESSION['message'] = "Shipment status is Received";
		header("Location:shipments.php");
	}
	
	exit;
}

if((int)$_GET['shipment_id'] and $_GET['action'] == 'delete' && $_SESSION['delete_shipment']){
	$shipment_id = (int)$_GET['shipment_id'];
	$db->db_exec("delete from inv_shipments where id = '$shipment_id'");
	
	$_SESSION['message'] = "Shipment is deleted";
	header("Location:shipments.php");
	exit;
}

if((int)$_GET['shipment_id'] and $_GET['action'] == 'complete' && $_SESSION['qc_shipment']){
	$shipment_id = (int)$_GET['shipment_id'];
	$db->db_exec("update inv_shipments SET status = 'Completed' , date_completed = '".date('Y-m-d H:i:s')."'  where id = '$shipment_id'");
	
	$_SESSION['message'] = "Shipment status is Completed";
	header("Location:shipments.php");
	exit;
}

if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}
if($page < 1){
	$page = 1;
}

$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1)*$num_rows;

$shipment_ids = array();
$record_found = true;
$parameters = array();
if(@$_GET['sku']){
	$product_sku = $db->func_escape_string($_GET['sku']);
	$product_sku = trim($product_sku);
	$parameters[] = "sku=$product_sku";
	$shipment_result = $db->func_query("select shipment_id from inv_shipment_items where product_sku = '$product_sku' AND rejected_product = '0'","shipment_id");
	if($shipment_result){
		$shipment_ids = array_keys($shipment_result);
	}
	else
	{
		$shipment_ids = array(0);

	}
}



if($_GET['number'] || $shipment_ids || $_GET['vendor'] || $_GET['shipment_type']){
	$where = array();
	if($shipment_ids){
		$where[] = " id IN (".implode(",", $shipment_ids).") ";
	}
	

	
	$number = $db->func_escape_string(trim($_GET['number']));	
	if($number){
		$where[] = " package_number like '%$number%' ";
		$parameters[] = "number=$number";
	}
	
	$shipment_type = $db->func_escape_string(trim($_GET['shipment_type']));	
	if($shipment_type){
		if ($shipment_type == 'vendor_shipment') {
			$where[] = " package_number NOT LIKE '%GFS%' AND package_number NOT LIKE '%lbb%' AND package_number NOT LIKE '%LBB%'";
			$parameters[] = "shipment_type=$shipment_type";
		} else {	
			$where[] = " package_number like '%$shipment_type%' ";
			$parameters[] = "shipment_type=$shipment_type";
		}
	}

	$tracking_number = $db->func_escape_string(trim($_GET['tracking_number']));	
	if($tracking_number){
		$where[] = " tracking_number like '%$tracking_number%' ";
		$parameters[] = "tracking_number=$tracking_number";
	}
	
	$vendor = (int)$_GET['vendor'];
	if($vendor){
		$where[] = " vendor = '$vendor' ";
		$parameters[] = "vendor=$vendor";
	}
	
	if($is_vendor){
		$where[] = " vendor = '".(int)$_SESSION['user_id']."' ";
	}
	
	$where = implode(" AND ", $where);
	$inv_query  = "select * from inv_shipments where $where order by date_added DESC";
}
else{
	$tracking_number = $db->func_escape_string(trim($_GET['tracking_number']));
	if($is_vendor){
		$inv_query  = "select * from inv_shipments where vendor = '".(int)$_SESSION['user_id']."' order by date_added DESC";
	}else if($tracking_number){
		$inv_query  = "select * from inv_shipments where tracking_number like '%".$tracking_number."%' order by date_added DESC";
	}else{
		$inv_query  = "select * from inv_shipments order by date_added DESC";
	}
}

$parameters = implode("&", $parameters);

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "shipments.php",$page);
$shipments  = $db->func_query($splitPage->sql_query);
$reorder_setting = $db->func_query_first("select * from inv_reorder_settings");

$vendors = $db->func_query("select id , name as value from inv_users where group_id = 1 order by lower(name) asc");
$shipment_type =array(
      array('id' => 'vendor_shipment', 'value' => 'Vendor Shipment'),
      array('id' => 'lbb', 'value' => 'LBB'),
      array('id' => 'gfs', 'value' => 'GFS Return')
      );
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Shipments</title>

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

	<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox({ width: '680px' , autoCenter : true , autoSize : true });
		});
	</script>	
</head>
<body>
	<?php include_once 'inc/header.php';?>
	<style type="text/css">
	.ajax-dropdown table {
		background: #000;
		color: #fff;
	}
	.ajax-dropdown tbody tr {
		background: #000;
	}
	</style>
	<?php if(@$_SESSION['message']):?>
		<div align="center"><br />
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
	<?php endif;?>

	<div align="center">
		<a href="sales.php">Add New Shipment</a>
	</div>	

	<br />

	<div align="center">
		<form method="get">
			<table>
				<tr>
					<td>Tracking #</td>
					<td>
						<input type="text" name="tracking_number" value="<?php echo $_GET['tracking_number'];?>" />
					</td>
					<td></td>
					<td>SKU:</td>
					<td>
						<input type="text" name="sku" value="<?php echo $_GET['sku'];?>" />
					</td>
					<td></td>

					<td>Shipment No.:</td>
					<td><input type="text" name="number" value="<?php echo $_GET['number'];?>" /></td>
					<td></td>

					<?php if(!$is_vendor):?>
						<td>Vendor:</td>
						<td>
							<?php echo createField("vendor", "vendor" , "select" , $_GET['vendor'] , $vendors);?>
						</td>
					<?php endif;?>
					<td></td>
					<td>Shipment Type:</td>
						<td>
							<?php echo createField("shipment_type", "shipment_type" , "select" , $_GET['shipment_type'] , $shipment_type);?>
						</td>
					<td></td>

					<td><input type="submit" name="search" value="Search" /></td>
				</tr>
			</table>
		</form>
	</div>

	<?php if($shipments):?>
		<table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
			<thead>
				<tr>
					<th>#</th>
					<th>Purchase Order #</th>

					<?php if(!$is_vendor): ?>
						<th>Rating</th>
						<th>OOS</th>
					<?php endif; ?>

					<th>Status</th>
					<th>Completed</th>
					<th>Issued</th>
					<th>FB Added</th>
					<th>New Item</th>
					<th>Ignored</th>
					<th>Action</th>
					<th>Package Tracking</th>
				</tr>
			</thead>
			<tbody>
				<?php $i = $splitPage->display_i_count();
				foreach($shipments as $shipment):?>

				<?php if(!$is_vendor){ 
					$_items = $db->func_query("SELECT * FROM inv_shipment_items WHERE shipment_id='".$shipment['id']."' AND is_new=0");
					//print_r($shipment['id']);
					//exit;

					$new_item = $db->func_query("SELECT is_new FROM inv_shipment_items WHERE shipment_id='".$shipment['id']."'");


					$score = 0;
					$it  = 0;
					$oos = 0;

					//print_r($_items);
					//exit;

					foreach($_items as $sitem){
						$_product=$db->func_query_first("SELECT mps,quantity FROM oc_product WHERE sku='".$sitem['product_sku']."'");

						//print_r($_product);
						//exit;

						$rop = getRop($_product['mps'] , $reorder_setting['lead_time'] , $reorder_setting['qc_time'] , $reorder_setting['safety_stock']);

						if($_product['quantity']<=0){
							$oos++;  
						}

						$score+=getScore((int)$rop);
						$it++;
					}

					if($it==0) $it=1;
					$rating = $score/$it;
				} 
				?>

				<tr id="<?php echo $shipment['id'];?>">
					<td align="center"><?php echo $i; ?></td>

					<td align="center"><?php echo $shipment['package_number'];?></td>

					<?php if(!$is_vendor):?>
						<?php if($shipment['status'] == 'Received' or $shipment['status'] == 'Issued' or $shipment['status'] == 'Pending'):?>
							<td align="center" ><?php echo round($rating,2);?></td>
						<?php else: ?>
							<td align="center"> - </td>
						<?php  endif; ?>

						<?php if($shipment['status'] == 'Received' or $shipment['status'] == 'Issued' or $shipment['status'] == 'Pending'):?>
							<td align="center" style="color:red"><?php echo $oos;?></td>
						<?php else: ?>
							<td align="center"> - </td>
						<?php endif;?>		
					<?php endif;?>

					<td align="center"><?php echo $shipment['status'];?></td>

					<td align="center"><?php echo ($shipment['date_qc']!='0000-00-00 00:00:00'?americanDate($shipment['date_qc']):'');?></td>

					<?php if($shipment['status'] != 'Pending'):?>						                                                  
						<td align="center"><?php echo americanDate($shipment['date_issued']);?></td>
					<?php else:?>
						<td>&nbsp;</td>
					<?php endif;?>		

					<td align="center"><?php echo ($shipment['fb_added'] == 1) ? 'Yes' : 'No';?></td>

					<td align="center"><?php echo ($shipment['is_new']?'X':'-');?></td>

					<td align="center">
						<?php if($shipment['ignored'] == 1):?>
							<a href="shipments.php?no=<?php echo $shipment['package_number']; ?>&action=ignore&id=<?php echo $shipment['id']?>" onclick="if(!confirm('Are you sure?')){ return false; }">Upload Again</a>
						<?php endif;?>	
					</td>

					<td align="center" class="showorder">

					<?php
		
					?>

						<!-- <a target="_parent" data-tooltip="<?php foreach($comments as $comment) {echo get_username($comment['user_id']). ' : '.$comment['comment']. "" ; }?>" href="#">Comments</a>					| -->
						<a href="javascript:void(0);" onmouseout="$('.load-comments-<?php echo $shipment['id'];?>' ).toggle();" onmouseover="loadComments('<?php echo $shipment['id'];?>');">Comments(<?php echo $count =  $db->func_query_first_cell("select count(id) from inv_shipment_comments where shipment_id='".$shipment['id']."';");?>)</a>
						
						<?php if(($shipment['status'] == 'Pending' && $_SESSION['edit_pending_shipment']) || ($shipment['status'] != 'Completed' || $_SESSION['login_as'] == 'admin')):	
						
						
						
						?>

						<!-- <a class="fancyboxX3 fancybox.iframe" href="shipment_comments.php?shipment_id=<?php echo $shipment['id']?>"  >Comments</a> -->
						|
						<a href="addedit_shipment.php?shipment_id=<?php echo $shipment['id']?>">
							Edit
						</a>
						|
					<?php endif;?>

					<?php if($shipment['status'] == 'Issued' && $_SESSION['edit_received_shipment'] == '1'):?>
						<a href="addedit_shipment.php?shipment_id=<?php echo $shipment['id']?>">Receive Shipment</a>
						|
					<?php endif;?>	

					<?php if($shipment['status'] == 'Pending' && $_SESSION['edit_pending_shipment']):?>
						<a href="shipments.php?no=<?php echo $shipment['package_number']; ?>&action=issue&id=<?php echo $shipment['id']?>" onclick="if(!confirm('Are you sure?')){ return false; }">
							Issue Shipment
						</a>
						|
					<?php endif;?>	

					<?php if($shipment['status'] == 'Received' && $_SESSION['qc_shipment']):?>
						<a href="shipment_qc.php?shipment_id=<?php echo $shipment['id']?>">
							QC Shipment
						</a>
						|
					<?php endif;?>

					<a href="view_shipment.php?shipment_id=<?php echo $shipment['id']?>">View</a>
					|
					<a href="download.php?action=shipment&shipment_id=<?php echo $shipment['id']?>">Download</a>

					<?php if($_SESSION['delete_shipment']):?>
						|
						<a href="shipments.php?action=delete&shipment_id=<?php echo $shipment['id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>
					<?php endif;?>


					<?php if($shipment['status'] == 'Completed'):?>
						|
						<a target="_blank" href="print.php?action=shipment&shipment_id=<?php echo $shipment['id']?>">Print</a>
					<?php endif;?>

					<?php if($_SESSION['user_id'] == '0'):?>
						|
						<a target="_blank" href="shipment_pdf.php?action=view&shipment_id=<?php echo $shipment['id']?>">Download PDF</a>
					<?php endif;?>
					<div class="load-comments-<?php echo $shipment['id'];?> ajax-dropdown" style="display: none;"></div>
				</td>
				<td>
					<?php
					if($shipment['is_tracker_updated']==0){
						echo 'Not Synced';
					}
					else
					{
						$tracker_id = $db->func_query_first_cell("SELECT tracker_id FROM inv_tracker where shipment_id='".$shipment['id']."'");
						$last_update = $db->func_query_first_cell("SELECT message FROM inv_tracker_status WHERE tracker_id='".$tracker_id."' order by id desc limit 1");
						echo $last_update;
					}
					?>
				</td>
			</tr>
			<?php $i++; endforeach; ?>

			<tr>
				<td colspan="3" align="left">
					<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
				</td>

				<td colspan="7" align="right">
					<?php  echo $splitPage->display_links(10,$parameters); ?>
				</td>
			</tr>
		</tbody>   
	</table>   
<?php else : ?> 
	<p>
		<label style="color: red; margin-left: 600px;">Shipments is not exist.</label>
	</p>     
<?php endif;?>
<br /><br />

<script type="text/javascript">
function loadComments (shipment_id) {
	$('.load-comments-' + shipment_id ).toggle();
	if (!($('.load-comments-' + shipment_id ).text())) {
			$.ajax({
				url: 'shipments.php',
				type: 'post',
				dataType: 'json',
				data: {shipment_id: shipment_id, action: 'load-comments'},
			})
			.always(function(json) {
				$('.load-comments-' + shipment_id ).html(json['data']);
			});
		}

	}
</script>
</body>
</html>        