<?php
require_once("auth.php");
require_once("inc/functions.php");
if (!$_SESSION['vouchers_update']) {
	echo "You dont have the permssion to access this page.";
	exit;
}
$table = "`inv_voucher_reasons`";
//$rma_reasons = $db->func_query("Select * from $table where reason_type = 'RMA' order by id desc");
$order_reasons = $db->func_query("Select * from $table where reason_type = 'Order' order by reason asc");
$paypal_reasons = $db->func_query("Select * from $table where reason_type = 'Manual_Paypal' order by reason asc");
$manual_reasons = $db->func_query("Select * from $table   order by reason asc");
$update_reasons = $db->func_query("Select * from $table where reason_type = 'Update' order by reason asc");
if ($_POST['submit']) {
	$insert = array();
	$insert['main_category'] = $_POST['main_category'];
	$insert['location'] = implode("~", $_POST['location']);

	$insert['reason'] = $_POST['reason'];
	$insert['reason_type'] = $_POST['category'];
	$insert['description'] = $_POST['description'];
	//print_r($insert);exit;
	if ($_POST['update'] == '1') {
		$db->func_array2update($table,$insert,"id = '".$_POST['update_reason_id']."'");
		$_SESSION['message'] = "Reason Updated successfully.";
		header("Location:voucher_reasons.php");
		exit;
	} else {
		$id = $db->func_array2insert($table, $insert);
		$_SESSION['message'] = "Reason Added successfully.";
   		header("Location:voucher_reasons.php");
   		exit;
	}
}
$detail = array();
if(isset($_GET['action']) && $_GET['action']=='edit')
{
	$detail = $db->func_query_first("SELECT * FROM $table WHERE id='".(int)$_GET['reason_id']."'");
}
if((int)$_GET['reason_id'] and $_GET['action'] == 'delete'){
	$delete_reason = (int)$_GET['reason_id'];
	$db->db_exec("delete from $table where id = '$delete_reason'");
	$_SESSION['message'] = "Reason Deleted successfully.";
   		header("Location:voucher_reasons.php");
   		exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Reasons | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
</head>
<body>
	<div align="center">
		<div align="center"> 
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if ($_SESSION['message']) { ?>
		<div align="center"><br />
			<font color="red">
				<?php
				echo $_SESSION['message'];
				unset($_SESSION['message']);
				?>
				<br />
			</font>
		</div>
		<?php } ?>
		<form action="" method="post" enctype="multipart/form-data">
			<h2>Edit Reasons</h2>
			<table align="center" border="1" width="40%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			 
				<?php
				$main_categories = array('Cancelled Order/Items',
'Appreciation',
'Shipping Income',
'Warehouse',
'Error',
'Marketing',
'Cancelled Order/Items',
'Payment',
'Taxes',
'Purchasing',
'Return',
'Payment Dispute',
'Shipping Income',
'LBB',
'Product Income');
				?>
				<tr>
					<th style="">Transaction Category</th>
					<td style="">
						<select required  id="main_category" name="main_category">
							<option value="">Select</option>
							<?php
							foreach($main_categories as $main_category)
							{
								?>
								<option value="<?php echo $main_category;?>" <?php echo ($main_category==$detail['main_category']?'selected':''); ?>><?php echo $main_category;?></option>
								<?php
							}
							?>
							
						</select>
					</td>
				</tr>

				<tr>
				<th>Location</td>
				<td>
				<?php
				$_location = explode("~",$detail['location'] );
				$locations = array('Manual','Order','POS','RMA','Payment Mapping');
				?>
				<select required id="location" name="location[]" multiple="" size="5">
				<?php
				foreach($locations as $location)
							{
								?>
								<option <?php echo (in_array($location, $_location)?'selected':'');?>><?php echo $location;?></option>
								<?php

							}
							?>
				</select>
				</td>

				</tr>


				<tr>
					<th style="">Voucher</th>
					<td style="">
						<select  id="category" name="category">
							<option value="">Select</option>
							<option value="Order" <?php echo ($detail['reason_type']=='Order'?'selected':'');?>>Order Voucher Reason</option>
							<!-- <option value="Manual_Paypal">Manual PayPal Refund Reason</option> -->
							<option value="Manual" <?php echo ($detail['reason_type']=='Manual'?'selected':'');?>>Manual Voucher Reason</option>
							<option value="Update" <?php echo ($detail['reason_type']=='Update'?'selected':'');?>>Update Voucher Reason</option>
							
						</select>
					</td>
				</tr>
				<tr>
					<th>Reason</th>
					<td>
						<input required id="reason" style="width: 300px ;" type="text" name="reason" value="<?php echo $detail['reason'];?>">
						<input type="hidden" id="update" name="update" value="<?php echo ($detail['id']?'1':'0');?>">
						<input type="hidden" id="update_reason_id" name="update_reason_id" value="<?php echo (int)$detail['id'];?>">
					</td>
				</tr>
				<tr>
					<th>Decsription</th>
					<td>
						<textarea required cols="50" rows="3" name="description" id="description" placeholder="Please enter description" ><?php echo $detail['description'];?></textarea>
					</td>
				</tr>
				<tr >
					<td align="center" colspan="2"><input class="button" type="submit" name="submit" value="Submit" /> <input type="button" class="button button-danger" onclick="window.location='voucher_reasons.php'" value="New Entry"></td>
				</tr>
			</table>
		</form>
		<br><br>
		
		<table align="center" width="100%" >
		
		
		<tr style="display: none">
			<td align="center"> <h2>Order Voucher Reasons</h2></td>
			<td align="center"> <h2>Update Voucher Reasons</h2></td>
		</tr>
			<tr style="display: none">
				
				<td>
					
				<table align="center" border="1" width="80%" cellpadding="5" cellspacing="0" >
					<thead>
				<tr>
				<th>#</th>
					<th>Reason</th>
					<th>Description</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php $i =1; foreach ($order_reasons as $reason) { ?>
				<tr>
				<td align="center"><?php echo $i; ?></td>
					<td align="center">
					<input type="hidden" id="reason<?php echo $reason['id']; ?>" value= "<?php echo $reason['reason'];?>">
					<input type="hidden" id="description<?php echo $reason['id']; ?>" value= "<?php echo $reason['description'];?>">
					<input type="hidden" id="reason_category<?php echo $reason['id']; ?>" value="<?php echo $reason['reason_type'];?>">
					 <?php echo $reason['reason'] ?> </td>
					 <td align="center">
					 	<?php echo $reason['description']; ?>
					 </td>
					<td align="center"><a href="voucher_reasons.php?action=delete&reason_id=<?php echo $reason['id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>
					|
					<a href="<?php echo $host_path;?>voucher_reasons.php?action=edit&reason_id=<?php echo $reason['id'];?>" >Edit</a>
					</td>
				</tr>
				<?php $i++;} ?>
			</tbody>
				</table>
				</td>
				
				<td>
				
				<table align="center" border="1" width="80%" cellpadding="5" cellspacing="0" >
					<thead>
				<tr>
				<th>#</th>	
					<th>Reason</th>
					<th>Description</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php $i =1;
				 foreach ($update_reasons as $reason) { ?>
				<tr>
				<td align="center"><?php echo $i; ?></td>
					<td align="center">
					<input type="hidden" id="reason<?php echo $reason['id']; ?>" value= "<?php echo $reason['reason'];?>">
					<input type="hidden" id="description<?php echo $reason['id']; ?>" value= "<?php echo $reason['description'];?>">
					<input type="hidden" id="reason_category<?php echo $reason['id']; ?>" value="<?php echo $reason['reason_type'];?>">
					 <?php echo $reason['reason'] ?> </td>
					 <td align="center">
					 	<?php echo $reason['description']; ?>
					 </td>
					<td align="center"><a href="voucher_reasons.php?action=delete&reason_id=<?php echo $reason['id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>
					|
					<a href="<?php echo $host_path;?>voucher_reasons.php?action=edit&reason_id=<?php echo $reason['id'];?>" >Edit</a>
					</td>
				</tr>
			<?php $i++;
			 } ?>
			</tbody>
				</table>
				</td>
			</tr>
			<tr>
			<td align="center"> </td>
			<td align="center"> <h2>Reasons</h2></td>
		</tr>
			<tr>
				
				<td>
				<!-- 	
				<table align="center" border="1" width="80%" cellpadding="5" cellspacing="0" >
					<thead>
				<tr>
				<th>#</th>
					<th>Reason</th>
					<th>Description</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php $i =1; foreach ($paypal_reasons as $reason) { ?>
				<tr>
				<td align="center"><?php echo $i; ?></td>
					<td align="center">
					<input type="hidden" id="reason<?php echo $reason['id']; ?>" value= "<?php echo $reason['reason'];?>">
					<input type="hidden" id="description<?php echo $reason['id']; ?>" value= "<?php echo $reason['description'];?>">
					<input type="hidden" id="reason_category<?php echo $reason['id']; ?>" value="<?php echo $reason['reason_type'];?>">
					 <?php echo $reason['reason'] ?> </td>
					 <td align="center">
					 	<?php echo $reason['description']; ?>
					 </td>
					<td align="center"><a href="voucher_reasons.php?action=delete&reason_id=<?php echo $reason['id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>
					|
					<a href="javascript:void(0);" onclick="updateThis(<?php echo $reason['id']; ?>)">Edit</a>
					</td>
				</tr>
				<?php $i++;} ?>
			</tbody>
				</table> -->
				</td>
				<td>
				
				<table align="center" border="1" width="80%" cellpadding="5" cellspacing="0" >
					<thead>
				<tr>
				<th>#</th>	
					<th>Category</th>
					<th>Reason</th>
					<th>Locations</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php $i =1;
				 foreach ($manual_reasons as $reason) { ?>
				<tr>
				<td align="center"><?php echo $i; ?></td>
					<td align="center">
					
					 <?php echo $reason['main_category'] ?> </td>
					 <td align="center">
					 	<?php echo $reason['reason']; ?>
					 </td>
					 <td align="center"><?php echo str_replace("~", ", ", $reason['location']);?>
					<td align="center">
<a href="<?php echo $host_path;?>voucher_reasons.php?action=edit&reason_id=<?php echo $reason['id'];?>" >Edit</a>
|
					<a href="voucher_reasons.php?action=delete&reason_id=<?php echo $reason['id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>
					
					
					</td>
				</tr>
			<?php $i++;
			 } ?>
			</tbody>
				</table>
				</td>
			</tr>
		</table>
		<br>
	</div>
	<script type="text/javascript">
		function updateThis(id){
			$('#reason').val($('#reason'+id).val());
			$('#description').text($('#description'+id).val());
			$("#category option[value=" + $('#reason_category'+id).val() + "]").prop("selected",true);
			$("#update").val(1);
			$("#update_reason_id").val(id);			
		}
	</script>
</body>
