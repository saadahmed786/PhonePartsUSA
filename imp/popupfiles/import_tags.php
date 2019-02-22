<?php

include '../auth.php';
include '../inc/functions.php';

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
	$headings = array(
		'SKU',
		'Mobile Sentrix',
		'Fixez',
		'Mengtor',
		'Mobile Defenders',
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
					$products[$i][$heading[$j]] = $db->func_escape_string(trim($row[$j]));
				}
			}
			$i++;
		}
		
		$log = "Product Has been imported via CSV ";
		$bits .= '';
		$updateProduct = array();
		foreach($products as $product){
			if ($product['SKU']) {
				$product_id = $db->func_query_first_cell("SELECT product_id from oc_product where sku='".$product['SKU']."'");
				if($product_id)
				{
					$db->db_exec("DELETE FROM oc_product_tag WHERE product_id='".$product_id."'");
  			$_product_tags = explode(",", $product['Tag']);
  			foreach($_product_tags as $_tag)
  			{
  				$_insert = array();
  				$_insert['product_id'] = $product_id;
  				$_insert['language_id'] = 1;
  				$_insert['tag'] = $db->func_escape_string(trim($_tag));

  				$db->func_array2insert("oc_product_tag", $_insert);
  			}
				}
			}
		}

		
		$log = "Product Tag(s) Has been imported via CSV";

		if(!$_SESSION['message']){
			$_SESSION['message'] = 'Products Tag(s) uploaded successfully.';
			actionLog($log);
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
	<title>Import Products</title>
</head>
<body>
	<div class="div-fixed">
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<div align="center">
			<a href="<?php echo $host_path;?>/csvfiles/import_tags.csv">Download Sample</a>
			<form method="post" action="" enctype="multipart/form-data">
				<table cellpadding="10" cellspacing="0">
					<tr>
						<td>File:</td>
						<td colspan="3">
							<input type="file" name="products" required value="" />
						</td>
					</tr>
					<tr>
						<td align="center" colspan="4">
							<input type="submit" name="upload" value="Upload" />
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</body>
</html>  	 