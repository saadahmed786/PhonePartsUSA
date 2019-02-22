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
		'E-Trade Supply',
		'Maya Cellular',
		'LCD Loop',
		'Parts 4 Cells',
		'Cell Parts Hub',
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
		//print_r($products);exit;
		$log = "Product Has been imported via CSV ";
		$bits .= '';
		$updateProduct = array();
		foreach($products as $product){
			if ($product['SKU']) {
				if ($product['Mobile Sentrix']){
				$updateProduct[$product['SKU']]['mobile_sentrix'] = array(
					'sku' => $product['SKU'],
					'url' => $product['Mobile Sentrix'],
					'type' => 'mobile_sentrix',
					);}
				if ($product['Fixez']){
				$updateProduct[$product['SKU']]['fixez'] = array(
					'sku' => $product['SKU'],					
					'url' => $product['Fixez'],
					'type' => 'fixez',
					);}
				if ($product['Mengtor']){
				$updateProduct[$product['SKU']]['mengtor'] = array(
					'sku' => $product['SKU'],
					'url' => $product['Mengtor'],
					'type' => 'mengtor',
					);}
				if ($product['Mobile Defenders']){
				$updateProduct[$product['SKU']]['mobile_defenders'] = array(
					'sku' => $product['SKU'],
					'url' => $product['Mobile Defenders'],
					'type' => 'mobile_defenders',
					);}
				if ($product['E-Trade Supply']){
				$updateProduct[$product['SKU']]['etrade_supply'] = array(
					'sku' => $product['SKU'],
					'url' => $product['E-Trade Supply'],
					'type' => 'etrade_supply',
					);}
				if ($product['Maya Cellular']){
				$updateProduct[$product['SKU']]['maya_cellular'] = array(
					'sku' => $product['SKU'],
					'url' => $product['Maya Cellular'],
					'type' => 'maya_cellular',
					);}
				if ($product['LCD Loop']){
				$updateProduct[$product['SKU']]['lcd_loop'] = array(
					'sku' => $product['SKU'],
					'url' => $product['LCD Loop'],
					'type' => 'lcd_loop',
					);}
				if ($product['Parts 4 Cells']){
				$updateProduct[$product['SKU']]['parts_4_cells'] = array(
					'sku' => $product['SKU'],
					'url' => $product['Parts 4 Cells'],
					'type' => 'parts_4_cells',
					);}
				if ($product['Cell Parts Hub']){
				$updateProduct[$product['SKU']]['cell_parts_hub'] = array(
					'sku' => $product['SKU'],
					'url' => $product['Cell Parts Hub'],
					'type' => 'cell_parts_hub',
					);}
			}
		}
		//print_r($updateProduct);exit;
		foreach ($updateProduct as $sku => $urls) {
			foreach ($urls as $type => $product) {
				if ($db->func_query_first('SELECT * FROM `inv_product_price_scrap` WHERE sku = "'. $sku .'" AND type = "'. $type .'"')) {
					$db->func_array2update("inv_product_price_scrap",$product,'sku = "'. $sku .'" AND type = "'. $type .'"');
				} else {
					$db->func_array2insert("inv_product_price_scrap",$product);
				}
			}
		}

		$log = "Product URL(s) Has been imported via CSV";

		if(!$_SESSION['message']){
			$_SESSION['message'] = 'Products URL(s) uploaded successfully.';
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
			<a href="<?php echo $host_path;?>/csvfiles/scrap_product_urls.csv">Download Sample</a>
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