<?php
require_once("auth.php");
require_once("inc/functions.php");
// if (!$_SESSION['vouchers_update']) {
// 	echo "You dont have the permssion to access this page.";
// 	exit;
// }
$page = 'location_management.php';
$table = "`oc_location`";

if ($_POST['submit']) {
	$insert = array();
	$insert['code'] = $db->func_escape_string($_POST['code']);
	$insert['name'] = $db->func_escape_string($_POST['name']);
	$insert['description'] = $db->func_escape_string($_POST['description']);
	

	
	if ($_POST['update'] == '1') {
		$db->func_array2update($table,$insert,"location_id = '".$_POST['location_id']."'");
		$_SESSION['message'] = "Location Updated successfully.";
		header("Location:$page");
		exit;
	} else {
		$id = $db->func_array2insert($table, $insert);
		$_SESSION['message'] = "Location Added successfully.";
   		header("Location:$page");
   		exit;
	}
}
$detail = array();
if(isset($_GET['action']) && $_GET['action']=='edit')
{
	$detail = $db->func_query_first("SELECT * FROM $table WHERE location_id='".(int)$_GET['location_id']."'");
}
if((int)$_GET['location_id'] and $_GET['action'] == 'delete'){
	$check = $db->func_query_first_cell("SELECT location_stock_id from oc_location_stock where location_id='".(int)$_GET['location_id']."'");

	if($check)
	{
		$_SESSION['message'] = "Location is already assigned with some product.";
   		header("Location:$page");	
   		exit;
	}
	$db->db_exec("delete from $table where location_id = '".$_GET['location_id']."'");
	$_SESSION['message'] = "Location Deleted successfully.";
   		header("Location:$page");
   		exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Location Management</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
</head>
<body>
	<div align="center">
		<div align="center" style="display:none"> 
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
			<h2>Location Management</h2>
			<table align="center" border="1" width="40%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			 
				

				<tr>
					<th>Code</th>
					<td>
						<input required id="code" style="width: 300px ;" type="text" name="code" value="<?php echo $detail['code'];?>">
						<input type="hidden" id="update" name="update" value="<?php echo ($detail['location_id']?'1':'0');?>">
						<input type="hidden" id="location_id" name="location_id" value="<?php echo (int)$detail['location_id'];?>">
					</td>
				</tr>

				<tr>
					<th>Name</th>
					<td>
						<input required id="name" style="width: 300px ;" type="text" name="name" value="<?php echo $detail['name'];?>">
						
					</td>
				</tr>
				<tr>
					<th>Decsription</th>
					<td>
						<textarea required cols="40" rows="3" name="description" id="description" placeholder="Please enter description" ><?php echo $detail['description'];?></textarea>
					</td>
				</tr>
				<tr >
					<td align="center" colspan="2"><input class="button" type="submit" name="submit" value="Submit" /> <input type="button" class="button button-danger" onclick="window.location='<?php echo $page;?>'" value="New Entry"></td>
				</tr>
			</table>
		</form>
		<br><br>
		
		<table align="center" width="100%" >
		
		
			<tr>
			
			<td align="center"> <h2>Available Shelves</h2></td>
		</tr>
			<tr>
				
				
				<td>
				
				<table align="center" border="1" width="80%" cellpadding="5" cellspacing="0" >
					<thead>
				<tr>
				<th>ID</th>	
					<th>Code</th>
					<th>Name</th>
					
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php $i =1;
				$rows = $db->func_query("SELECT * FROM $table order by location_id desc");
				 foreach ($rows as $row) { ?>
				<tr>
				<td align="center"><?php echo $row['location_id']; ?></td>
					<td align="center">
					
					 <?php echo $row['code'] ?> </td>
					 <td align="center">
					 	<?php echo $row['name']; ?>
					 </td>
					 
					<td align="center">
<a href="<?php echo $host_path;?><?php echo $page;?>?action=edit&location_id=<?php echo $row['location_id'];?>" >Edit</a>
|
					<a href="<?php echo $page;?>?action=delete&location_id=<?php echo $row['location_id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>
					
					
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
