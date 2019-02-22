<?php

include 'auth.php';
include 'inc/functions.php';
if($_POST['upload'] && $_FILES['products']['tmp_name']){
	$csv_mimetypes = array(
		'text/csv',
		'text/plain',
		'application/csv',
		'text/comma-separated-values',
		'application/excel',
		'application/vnd.ms-excel',
		'application/vnd.msexcel',
		'text/anytext',
		'application/octet-stream',
		'application/txt',
		);

	$type = $_FILES['products']['type'];
	if(in_array($type,$csv_mimetypes)){
		$filename = $_FILES['products']['tmp_name'];
		$handle   = fopen("$filename", "r");

		$heading  = fgetcsv($handle);
		$products = array();

		$i = 0;
		while(!feof($handle)){
			$row = fgetcsv($handle);
			for($j=0;$j<count($heading);$j++){
				if($row[$j]){
					$products[$i][$heading[$j]] = trim($row[$j]);
				}
			}
			$i++;
		}
		
		foreach($products as $product){
			$kit_sku = array();
			$kit_sku['kit_sku'] = $db->func_escape_string($product['Kit Sku']);
			
			$linked_array = array_values($product);
			$linked_array = array_slice($linked_array,1,sizeof($linked_array));
			$kit_sku['linked_sku'] = implode(",",$linked_array);
			$kit_sku['need_sync']  = 1;
			$kit_sku['dateofmodifcation'] = date('Y-m-d H:i:s');
			
			$error = false;
			//check all linked sku are exist or not?
			foreach($linked_array as $linked_sku){
				$isExist = $db->func_query_first_cell("select product_id from oc_product where sku = '$linked_sku' or model = '$linked_sku'");
				if(!$isExist){
					$error = true;
					$_SESSION['message'][] = "Linked sku $linked_sku is not exist. Please add to opencart and then try to add kit sku."; 
				}
			}
			
			if($error == false){
				$id = $db->func_query_first_cell("select id from inv_kit_skus where kit_sku = '".$kit_sku['kit_sku']."'");
				if($id){
					$db->func_array2update("inv_kit_skus", $kit_sku, "id = '$id'");
				}
				else{
					$db->func_array2insert("inv_kit_skus", $kit_sku);
				}
				$log = 'Kit SKU updated by File Upload for ' . linkToProduct($kit_sku['kit_sku']);
				actionLog($log);
			}
		}

		if(!$_SESSION['message']){
			$_SESSION['message'] = 'Kit Skus uploaded successfully.';
		}
		else{
			$_SESSION['message'] = implode("<br />",$_SESSION['message']);
		}
		
		echo "<script>window.close();parent.window.location.reload();</script>";
		exit;
	}
	else{
		$_SESSION['message'] = 'Uploaded file is not valid, try again';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Import Kit Skus</title>
</head>
<body>
	<div class="div-fixed">
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<div align="center">
			<a href="<?php echo $host_path;?>/csvfiles/kits_mapping.csv">Download Sample</a>

			<form method="post" action="" enctype="multipart/form-data">
				<table cellpadding="10" cellspacing="0">
					<tr>
						<td>File:</td>
						<td>
							<input type="file" name="products" required value="" />
						</td>
					</tr>

					<tr>
						<td align="center" colspan="2">
							<input type="submit" name="upload" value="Upload" />
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</body>
</html>  	 