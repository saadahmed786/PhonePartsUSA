<?php

require_once("auth.php");
require_once("inc/functions.php");
page_permission("product_classification");
$page = 'product_classification.php';
$mode = $_GET['mode'];

if($mode == 'edit'){
	$id = (int)$_GET['id'];
	$result = $db->func_query_first("select * from inv_classification where id = '$id'");
}
if($mode=='delete')
{
	$id = (int)$_GET['id'];
	$db->db_exec("DELETE FROM inv_classification WHERE id='".$id."'");	
	$_SESSION['message'] = "Record Deleted";
	header("Location: $page");
	exit;
}

if($_POST['add']){
	unset($_POST['add']);
	$array = array();
	$array = $_POST;
	$array['user_id'] = $_SESSION['user_id'];
	$array['date_added'] = date('Y-m-d H:i:s');
	if($id){
		
		$db->func_array2update("inv_classification",$array,"id = '$id'");
	}
	else{
		
		$id = $db->func_array2insert("inv_classification",$array);
	}
	
	header("Location: $page");
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Product Classification</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>

	<style type="text/css">
		.cart{
			position:absolute;
			top:10%;
			right:13%;
			text-decoration:underline;
			cursor:pointer;
		}
		.ajax-dropdown {
			width: 100%;
			position: relative;
		}
		.ajax-dropdown table {
			width: 100%;
			position: absolute;
			top: 5px;
			-webkit-transform: translate(-50%, 0%);
			-moz-transform: translate(-50%, 0%);
			-ms-transform: translate(-50%, 0%);
			-o-transform: translate(-50%, 0%);
			transform: translate(-50%, 0%);
			left: 50%;
		}
		.ajax-dropdown tbody tr {
			background-color: #fff;
			border-bottom: 1px solid #000;
			padding:5px;
		}
		.ajax-dropdown tbody td {
			padding:5px;
		}

		.ajax-dropdown tbody tr:hover {
			background-color: #999;
			color: #fff;
		}
	</style>
</head>
<body>
	<div align="center">
		<div align="center"> 
			<?php include_once 'inc/header.php';?>
		</div>

		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>

		<form action="" method="post" enctype="multipart/form-data">
			<h2>Product Classification</h2>
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">



				<tr>
					<td>Name:</td>
					<td><input type="text" name="name" value="<?php echo @$result['name'];?>" required /></td>
				</tr>
				<tr>
					<td>Main Category:</td>
					<td>
						<select name="main_class_id" required="">
							<option value="">--Select--</option>
							<?php foreach ($db->func_query('SELECT * FROM inv_main_classification WHERE status = 1') as $val) { ?>
							<option value="<?php echo $val['id']; ?>" <?php echo (($result['main_class_id'] == $val['id'])? 'selected="selected"': '');?>><?php echo $val['name']; ?></option>
							<?php }?>
						</select>
						<a href="popupfiles/main_classification.php" class="fancybox3 fancybox.iframe button" style="">Main Classification</a>
					</td>
				</tr>
				<tr>
					<td>Main Class:</td>
					<td><input type="text" name="main_class" value="<?php echo @$result['main_class'];?>" /></td>
				</tr>

				<tr>
					<td>Sort Order:</td>
					<td>
					<input type="text" onkeyup="allowNum(this)" min="0" name="sort" value="<?php echo ($result['sort'])? $result['sort']: '0'; ?>" />
					Next Sort Order <b><?php echo $db->func_query_first_cell('SELECT MAX(`sort`) FROM `inv_classification`') + 1; ?></b>
					</td>
				</tr>

				<tr>
					<td>Status:</td>
					<td>
						<select name="status">
							<option value="1" <?php if($result['status']==1) echo 'selected';?>>Enabled</option>
							<option value="0" <?php if($result['status']==0) echo 'selected';?>>Disabled</option>
						</select>
					</td>
				</tr>

				<tr>
					<td colspan="2" align="center">
						<input type="submit" name="add" value="Submit" />
					</td>
				</tr>

				<tr>
					<td colspan="2">
						Reload Page after adding Main Classification
					</td>
				</tr>
			</table>
		</form>
	</div>

	<div style="margin-top:20px">
		<?php
		$lists = $db->func_query("SELECT * FROM inv_classification ORDER BY name");

		?>	
		<table class="data" border="1" style="border-collapse:collapse;" width="80%" cellspacing="0" align="center" cellpadding="5">
			<tr style="background:#e5e5e5;">
				<th style="width:50px;">#</th>

				<th align="center">Main Category</th>
				<th align="center">Main Class</th>
				<th align="center">Classification</th>
				<th align="center">Date Added</th>
				<th align="center">Status</th>

				<th align="center">Created By</th>
				<th align="center">Sort</th>
				<th align="center">Action</th>


			</tr>
			<?php
			$i=1;
			foreach($lists as $list):
				
				?>
			<tr>
				<td align="center"><?=$i;?></td>

				<td align="center"><?= $db->func_query_first_cell('SELECT `name` FROM inv_main_classification WHERE id = "'. $list['main_class_id'] .'"'); ?></td>
				<td align="center"><?=$list['main_class'];?></td>

				<td align="center"><?=(strlen($list['name'])>100?substr($list['name']).'...':$list['name']);?></td>

				<td align="center"><?=americanDate($list['date_added']);?></td>
				<td align="center"><?=($list['status']==0?'Disabled':'Enabled');?></td>
				<td align="center"><?=get_username($list['created_by']);?></td>
				<td align="center"><?= $list['sort']; ?></td>
				<td align="center"><a href="<?=$page;?>?mode=edit&id=<?=$list['id'];?>">Edit</a> | <a href="javascript:void(0);" onclick="if(confirm('Are you sure to delete this entry?')){window.location='<?=$page;?>?mode=delete&id=<?=$list['id'];?>'}">Delete</a></td>
			</tr>
			<?php
			$i++;
			endforeach;
			?>
		</table>


	</div>
	<script>
		function allowNum (t) {
			var input = $(t).val();
			var valid = input.substring(0, input.length - 1);
			if (isNaN(input)) {
				if (!valid) {
					valid = 0;
				}
				$(t).val(valid);
			}
		}
		function addSelect (data) {
			$('#type').val(data);
			$('.ajax-dropdown').html('');
		}
	</script>
</body>
</html>