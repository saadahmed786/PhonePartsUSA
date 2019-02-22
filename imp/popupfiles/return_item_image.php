<?php

include_once '../auth.php';
include_once '../inc/functions.php';

set_time_limit(0);
ini_set("memory_limit", '2048M');

$return_item_id = (int)$_GET['return_item_id'];

if(@$_POST['add'] && $_FILES['image_path']['tmp_name'][0]){
	
	$count = count($_FILES['image_path']['tmp_name']);
	for($i=0; $i<$count; $i++){
		$uniqid = uniqid();
		$destination = "../images/returns/".$uniqid.".jpg";
		$destination_thumb = "../images/returns/".$uniqid."_thumb.jpg";
		
		if(move_uploaded_file($_FILES['image_path']['tmp_name'][$i], $destination)){
			resizeImage($destination , $destination_thumb , 50 , 50);
			
			resizeImage($destination , $destination , 500 , 500);
			
			$itemImage = array();
			$itemImage['image_path'] = $destination;
			$itemImage['thumb_path'] = $destination_thumb;
			$itemImage['date_added'] = date('Y-m-d H:i:s');
			$itemImage['user_id']  = $_SESSION['user_id'];
			$itemImage['return_item_id'] = $return_item_id;
			
			$db->func_array2insert("inv_return_item_images",$itemImage);
		}
	}
	
	$_SESSION['message'] = "Image is added.";
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}
?>
<html>
	<body>
		<div align="center">
			<?php if($message):?>
				<h5 align="center" style="color:red;"><?php echo $message;?></h5>
			<?php endif;?>
			
			<h3>Upload Item Images</h3>
			<form method="post" enctype="multipart/form-data" action="">
				<table>
					<tr>
						<td>Image:</td>
						<td><input type="file" name="image_path[]" multiple="multiple" value="" required /> <br /><br /></td>					
					</tr>
					
					<tr>
						<td colspan="2" align="center"><input type="submit" name="add" value="Submit" /></td>					
					</tr>
				</table>
			</form>		
			<br /><br />
		</div>	
	</body>
</html>