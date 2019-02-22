<?php
include_once '../auth.php';
include_once '../inc/functions.php';
$sku = $_GET['sku'];

// $inv_data = getInventoryDetail($sku);
$count = $db->func_query_first_cell("SELECT quantity from oc_product where trim(lower(sku))='".$db->func_escape_string(trim(strtolower($sku)))."'");
$vendor_id = $db->func_query_first_cell("SELECT vendor from oc_product where trim(lower(sku))='".$db->func_escape_string(trim(strtolower($sku)))."'");
// print_r($_POST);
if($_POST['add']){
	
	$quantity  = (int)$_POST['quantity'];
	$comment = trim($db->func_escape_string($_POST['comment']));
	if($comment)
	{
		// echo 'here';exit;
		makeLedger('',array($sku=>(int)$quantity),$_SESSION['user_id'],'','Stock Adjustment (Add).',$comment);
		$db->db_exec("UPDATE oc_product SET quantity=quantity+'".$quantity."' where trim(lower(sku))='".trim(strtolower($sku))."'");
		$_SESSION['message'] = "Add Inventory is updated";
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
	}
	
	
}

if($_POST['remove']){
	
	$quantity  = (int)$_POST['quantity'];
	$comment = trim($db->func_escape_string($_POST['comment']));
	if($comment)
	{
		// echo 'here';exit;
		makeLedger('',array($sku=>(int)$quantity),$_SESSION['user_id'],'','Stock Adjustment (Remove).',$comment);
		$db->db_exec("UPDATE oc_product SET quantity=quantity-'".$quantity."' where trim(lower(sku))='".trim(strtolower($sku))."'");
		$_SESSION['message'] = "Remove Inventory is done";
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
	}
	
	
}

if($_POST['rtv']){
	
	
	$quantity  = (int)$_POST['quantity'];
	$comment = trim($db->func_escape_string($_POST['comment']));
	if($comment)
	{
		if ($_POST ['rtv_shipments']=='') {
								$rejcetedShipment = array();
								$rejcetedShipment ['package_number'] = 'RTV-' . rand();
								$rejcetedShipment ['vendor'] = $vendor_id;
								$rejcetedShipment ['status'] = 'Pending';
								$rejcetedShipment ['date_added'] = date ( 'Y-m-d H:i:s' );
								$rejcetedShipment ['user_id'] = $_SESSION['user_id'];
								$rtv_ship_num = $db->func_array2insert ( 'inv_rejected_shipments', $rejcetedShipment );
							} else {
								$rtv_ship_num = $db->func_query_first_cell('SELECT id FROM inv_rejected_shipments WHERE package_number = "'. $_POST ['rtv_shipments'] .'"');
							}

							for($i=1;$i<=$quantity;$i++)
							{


							$returns_po_item_insert = array ();
							$returns_po_item_insert ['rejected_shipment_id'] = $rtv_ship_num;
							$returns_po_item_insert ['product_sku'] = $sku;
							$returns_po_item_insert ['qty_rejected'] = 1;
							$returns_po_item_insert ['reject_reason'] = $db->func_query_first_cell("select id from inv_rj_reasons where name='".$_POST['rtv_reason']."' limit 1");
							
							$returns_po_item_insert ['cost'] = getTrueCost($sku);
							$returns_po_item_insert ['reject_item_id'] = getReturnItemId('RTVManual', $sku,1);
							$returns_po_item_insert ['order_id'] = '';
							$returns_po_item_insert ['production_date'] = $_POST ['production_date'];
							
								$returns_po_item_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
								$returns_po_item_insert ['original_box_added'] = date ( 'Y-m-d H:i:s' );
							
							
							$returns_po_item_insert ['rma_number'] = 'RTVManual';
							$source = 'RC';
							
							$db->func_array2insert ( "inv_rejected_shipment_items", $returns_po_item_insert);
						}



		// echo 'here';exit;
		makeLedger($rtv_ship_num,array($sku=>(int)$quantity),$_SESSION['user_id'],'','Stock Adjustment (RTV).',$comment);
		$db->db_exec("UPDATE oc_product SET quantity=quantity-'".$quantity."' where trim(lower(sku))='".trim(strtolower($sku))."'");
		$_SESSION['message'] = "RTV Inventory is done";
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
	}
	
	
}

?>
<!DOCTYPE html>
<html>
<title>Left</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
   <link href='http://imp.phonepartsusa.com/include/date-picker/css/bootstrap-datetimepicker.min.css' rel='stylesheet' />
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
 <script src="http://imp.phonepartsusa.com/js/moment.min.js"></script>
<script src='<?php echo $host_path;?>/include/date-picker/js/bootstrap-datetimepicker.min.js'></script>

<body>

<div class="w3-sidebar w3-bar-block w3-card w3-animate-left" style="display:none" id="mySidebar">
  <button class="w3-bar-item w3-button w3-large"
  onclick="w3_close()">Close &times;</button>
  <button class="w3-bar-item w3-button tablink" onclick="openCity(event, 'Add')">Add</button>
  <button class="w3-bar-item w3-button tablink" onclick="openCity(event, 'Remove')">Remove</button>
  <button class="w3-bar-item w3-button tablink" onclick="openCity(event, 'RTV')">RTV</button>
</div>

<div id="main">

<div class="w3-teal">
  <button id="openNav" class="w3-button w3-teal w3-xlarge" onclick="w3_open()">&#9776;</button>
  <div class="w3-container">
    <h2>Add/Remove Inventory</h2>
  </div>
</div>


<div class="w3-container">
<div id="Add" class="w3-container adjustment" style="display:none">
    <h2>Add Found Inventory</h2>
    <form method="post">
					<table width="100%" align="left" style="font-weight:bold">
						<tr>
							<td>On Shelf:</td>
							<td><?php echo $count;?></td>
						</tr>
							<tr>
							<td>Quantity (Add):</td>
							<td><input type="number" value="0" name="quantity" required="" style="width:90%"></td>
						</tr>

						
							<tr>
							<td>Comment:</td>
							<td><textarea name="comment" required="" style="width:90%;height:100px"></textarea></td>
						</tr>
						<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="add"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
					<input type="hidden" name="sku" value="<?php echo $sku;?>">
					</form>
  </div>

  <div id="Remove" class="w3-container adjustment" style="display:none">
    <h2>Remove Missing Inventory</h2>
    <form method="post">
					<table width="100%" align="left" style="font-weight:bold">
						<tr>
							<td>On Shelf:</td>
							<td><?php echo $count;?></td>
						</tr>
							<tr>
							<td>Quantity (Remove):</td>
							<td><input type="number" value="0" name="quantity" required="" style="width:90%"></td>
						</tr>

						
							<tr>
							<td>Comment:</td>
							<td><textarea name="comment" required="" style="width:90%;height:100px"></textarea></td>
						</tr>
						<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="remove"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
					<input type="hidden" name="sku" value="<?php echo $sku;?>">
					</form>
  </div>

  <div id="RTV" class="w3-container adjustment" style="display:none">
    <h2>RTV Inventory</h2>
    <form method="post">
					<table width="100%" align="left" style="font-weight:bold">
						<tr>
							<td>On Shelf:</td>
							<td><?php echo $count;?></td>
						</tr>
							<tr>
							<td>Quantity (RTV):</td>
							<td><input type="number" value="0" name="quantity" required="" style="width:90%"></td>
						</tr>

						<tr>
							<td>RTV Reason:</td>
							<td><select name="rtv_reason" style="width:90%" required="">
							<option value="">Select</option>
							<?php
							$reasons = $db->func_query("SELECT * FROM inv_rj_reasons WHERE classification_id = (SELECT classification_id FROM oc_product where model = '". $sku ."' limit 1)"); 
							foreach($reasons as $reason)
							{
							?>
							<option value="<?php echo $reason['name'];?>"><?php echo $reason['name'];?></option>
							<?php
							}
							?>
							</select>
							</td>
						</tr>
						<tr>
							<td>Shipments:</td>
							<td><select name="rtv_shipments" style="width:90%" >
							
							<?php
							$reasons =  $db->func_query("SELECT * FROM inv_rejected_shipments where status = 'Pending' AND vendor = '".$vendor_id."'");
							foreach($reasons as $reason)
							{
							?>
							<option value="<?php echo $reason['package_number'];?>"><?php echo $reason['package_number'];?></option>
							<?php
							}
							?>
							<option value="">Create New Box</option>
							</select>
							</td>
						</tr>

							<tr>
							<td>Production Date</td>
							<td><input type="text" data-type="monthyear" name="production_date" style="width:90%;position: relative;" value="0000-00"></td>

							</tr>

						
							<tr>
							<td>Comment:</td>
							<td><textarea name="comment" required="" style="width:90%;height:100px"></textarea></td>
						</tr>
						<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="rtv"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
					<input type="hidden" name="sku" value="<?php echo $sku;?>">
					</form>
  </div>
</div>

</div>

<script>
$(document).ready(function(){
	  $('input[data-type=monthyear]').datetimepicker({
                format: 'Y-MM'
            });
})
function w3_open() {
  document.getElementById("main").style.marginLeft = "25%";
  document.getElementById("mySidebar").style.width = "25%";
  document.getElementById("mySidebar").style.display = "block";
  document.getElementById("openNav").style.display = 'none';
}
function w3_close() {
  document.getElementById("main").style.marginLeft = "0%";
  document.getElementById("mySidebar").style.display = "none";
  document.getElementById("openNav").style.display = "inline-block";
}
</script>


<script>
function openCity(evt, cityName) {
  var i, x, tablinks;
  x = document.getElementsByClassName("adjustment");
  for (i = 0; i < x.length; i++) {
     x[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < x.length; i++) {
      // tablinks[i].className = tablinks[i].className.replace(" w3-red", ""); 
  }
  document.getElementById(cityName).style.display = "block";
  // evt.currentTarget.className += " w3-red";
}
</script>


</body>
</html>
