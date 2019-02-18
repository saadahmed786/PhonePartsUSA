<?php
require_once("auth.php");


$mode = $_GET['mode'];
if($mode == 'edit'){
	$id = (int)$_GET['id'];
	$row = $db->func_query_first("select * from inv_manufacturer where manufacturer_id = '$id'");
	$row['keyword'] = $db->func_query_first_cell("SELECT keyword FROM oc_url_alias WHERE query='catalog_manufacturer_id=".$id."'");
}

if($_POST['add']){
	unset($_POST['add']);
	
	$allowed = array('png', 'jpeg', 'jpg');
	$uploaded = 0;
	if ($_FILES['upFile']['tmp_name']) {
		$uniqid = uniqid();
		$name = explode(".", $_FILES['upFile']['name']);
		$ext = end($name);
		$fileName = $uniqid . '-' . $_POST['email'] . '.' . $ext;
		$dir = 'images/manufacturer/';
		$destination = $path . $dir . $fileName;
		$file = $_FILES['upFile']['tmp_name'];
		if (in_array($ext, $allowed)) {
			if (move_uploaded_file($file, $destination)) {
				$message .= 'Logo Uploaded <br>';
				$uploaded = 1;
			}
		} else {
			$message .= 'Logo Not Uploaded <br>';
		}
	}

	$_POST['image'] = ($uploaded)? $dir . $fileName : '';
	$_keyword = $_POST['keyword'];
	unset($_POST['keyword']);
	$array = array();
	$array = $_POST;
	$array['date_added'] = date('Y-m-d h:i:s');



	if($id){
		$db->func_array2update("inv_manufacturer",$array,"manufacturer_id = '$id'");
	}
	else{
		$db->func_array2insert("inv_manufacturer",$array);
	}
	$_POST['keyword'] = $_keyword;
	$check_query = $db->func_query_first("SELECT * FROM oc_url_alias where keyword='".strtolower($db->func_escape_string($_POST['keyword']))."'");
	if($check_query)
	{
	    $_POST['keyword']  = $_POST['keyword'].'-';
	}
	$db->db_exec("INSERT INTO oc_url_alias  SET query='catalog_manufacturer_id=".$id."',keyword='".strtolower($db->func_escape_string($_POST['keyword']))."'");
	
	$_SESSION['message'] = $message . "Manufacturer Updated";
	header("Location:manufacturer_list.php");
	exit;
	
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Add/Edit Manufacturer</title>
	<style type="text/css" media="all">
		.upMain {
			position: relative;
		}
		.ui.blue.button.upMain {
			display: inline-block;
		}
	</style>
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

		<form action="" method="post" enctype="multipart/form-data">
			<a href="devices_new_settings.php" title="back">Back</a> <h2>Add Manufacturer</h2>
			<table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>Name</td>
					<td><input type="text" name="name" value="<?php echo @$row['name'];?>" required /></td>
				</tr>

				<tr>
					<td>Logo</td>
					<td>
						<label class="ui blue button upMain" style="color: #fff;" for="mainimageup">
							<input onchange="validateFileUp(this);" type="file" style="opacity: 0; position: absolute; width: 100%; height: 23px; top: 0; left: 0;" name="upFile" accept="image/jpeg,image/png">
							Upload New
						</label>
						<div id="imagePrv" style="display: inline-block;">
							<?php if ($row['image']) { ?>
							<a class="fancybox2 fancybox.iframe" href="<?php echo $host_path . $row['image']; ?>" target="_blank">
								<img width="100px" src="<?php echo $host_path . $row['image']; ?>" alt="" />
							</a>
							<?php } ?>
						</div>
					</td>
				</tr>
				
				<tr>
					<td>SEO URL <small>(No spaces, only dashes)</small></td>
					<td><input type="text" name="keyword" value="<?php  echo @$row['keyword']; ?>" required /></td>
				</tr>

				<tr>
					<td>Description</td>
					<td><textarea name="description" rows="4"><?php echo @$row['description'];?></textarea></td>
				</tr>

				<tr>
					<td>Active</td>
					<td><input type="checkbox" name="status" value="1" <?php if($row['status']):?> checked="checked" <?php endif;?> /></td>
				</tr>

				<tr>
					<td colspan="2">
						<input type="submit" name="add" value="Submit" />
					</td>
				</tr>
			</table>
		</form>
	</div>
	<script>
		function validateFileUp (t) {
			var file = $(t).val().split(".");
			var ext = file.pop();
			var allowed = ['png', 'jpeg', 'jpg'];
			if ($.inArray(ext, allowed) >= 0) {
				if ($(t)[0].files[0]) {

					var reader = new FileReader();
					var src = '';
					reader.onload = function (e) {
						var error = false;
						var image = new Image();
						image.src = e.target.result;

						image.onload = function () {
							if (image.height != image.width) {
								error = true;
							}
						}
						console.log(error);
						if (error) {
							alert('This File width and height is not same');
							return false;
						}
						src = e.target.result;
						var data = '<a class="fancybox2 fancybox.iframe" href="'+ src +'" target="_blank">'
						+ '<img width="100px" src="'+ src +'" alt="" />'
						+ '</a>';
						$('#imagePrv').find('a').remove();
						$('#imagePrv').prepend(data);
					}

					reader.readAsDataURL($(t)[0].files[0]);
				}
			} else {
				alert('This File is not Allowed');
			}
		}
	</script>
</body>
</html>