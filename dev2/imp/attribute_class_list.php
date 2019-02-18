<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';

if(isset($_GET['id']) and $_GET['action'] == 'delete'){
	
	$db->db_exec("UPDATE inv_classification SET attribute_group_id='' where id = '".(int)$_GET['id']."'");
	header("Location:attribute_class_list.php");
	exit;
}

if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}
if($page < 1){
	$page = 1;
}

$max_page_links = 10;
$num_rows = 100;
$start = ($page - 1)*$num_rows;

$_query = "SELECT * FROM `inv_classification` ORDER BY `id`";

$splitPage  = new splitPageResults($db , $_query , $num_rows , "attribute_list.php",$page);
$rows = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Manufacturer Listing</title>
</head>
<body>
	<div align="center">
		<div align="center" style="display:none"> 
			<?php include_once 'inc/header.php';?>
		</div>

		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>

		<br clear="all" />

		<a href="devices_new_settings.php" class="button" >Back</a>

		<br clear="all" /><br clear="all" />

		<div align="center">
			<table border="1" width="900px;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr style="background-color:#e7e7e7;">
					<td>Class</td>
					<td>Attributes</td>


					<td colspan="2" align="center">Action</td>
				</tr>

				<?php foreach($rows as $row):?>
					<tr>
						<td><?php echo $row['name']; ?></td>

						<td><?php echo ($row['attribute_group_id']) ? (int)count(explode(',', $row['attribute_group_id'])) : '0'; ?></td>

						<td><a href="attributes_class.php?id=<?php echo $row['id']; ?>&mode=edit">Edit</a></td>

						<td><a href="attribute_class_list.php?id=<?php echo $row['id']; ?>&action=delete" onclick="if(!confirm('Are you sure, You want to delete this attributes?')){ return false;}">Clear Attributes</a></td>
					</tr>
				<?php endforeach;?>

				<tr>
					<td align="left">
						<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
					</td>

					<td align="center" >
						<form method="get">
							Page: <input type="text" name="page" value="<?php echo $page;?>" size="3" maxlength="3" />
							<input type="submit" name="Go" value="Go" />
						</form>
					</td>

					<td align="right" colspan="2">
						<?php echo $splitPage->display_links(10,$parameters);?>
					</td>
				</tr>
			</table>
		</div>		 
	</div>		     
</body>
</html>