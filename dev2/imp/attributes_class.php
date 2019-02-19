<?php
require_once("auth.php");


$mode = $_GET['mode'];
if($mode == 'edit'){
	$id = (int)$_GET['id'];
	$rows = $db->func_query_first("select * from inv_classification where id = '$id'");
}

if($_POST['add']){
	unset($_POST['add']);

			$array['attribute_group_id']=implode(',', $_POST['attribute_group']);

			$db->func_array2update("inv_classification",$array,"id = '$id'");


			header("Location:attribute_class_list.php");
			exit;

		}


		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
			<title>Add/Edit Attribute</title>
			<script type="text/javascript" src="js/jquery.min.js"></script>


			<script>

				function addAttrib(obj)
				{
					$parent = ($(obj).parent());   
					$clone = $parent.children('input:first').clone();

					$parent.append("<br />");
					$parent.append($clone);

				}

			</script>
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

				<form action="" method="post">
					<a href="attribute_class_list.php" title="back">Back</a><br><br>
					<a href="attribute_group_list.php" class="button">Add Attribute</a>
					<h2>Add Attributes against Class </h2>


					<table border="1" width="30%" cellpadding="5" cellspacing="0" align="center" >
						<tr>	
							<th>Attribute Group</th>



						</tr>	
						<?php
						$xattribs = explode(",",$rows['attribute_group_id']);
						?>
						<tr>
							<td><select name="attribute_group[]" style="width:400px; height: 300px;" class="multiple" multiple="multiple">
								<option value="">Please select</option>
								<?php
								$attribute_groups = $db->func_query("SELECT
									DISTINCT b.id,b.name
									FROM
									`inv_attr` a
									INNER JOIN `inv_attribute_group` b
									ON (a.`attribute_group_id` = b.`id`)");
								foreach($attribute_groups as $attribute_group)
								{
									?>
									<option value="<?php echo $attribute_group['id'];?>" <?php if(in_array($attribute_group['id'], $xattribs)) echo 'selected'; ?>><?php echo $attribute_group['name'];?></option>
									<?php
								}
								?>

							</select> 
						</td>


					</tr>
				</table>
				<br /><br />
				<input type="submit" name="add" class="button" value="Update" />

			</form>
		</div>
	</body>
	</html>			 		 