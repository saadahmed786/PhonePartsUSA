<?php



include '../auth.php';



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
		// print_r($products);exit;

		$i=0;

		foreach($products as $product)

		{

			// $product = explode(';', $product['SKU;Qty;Price']);
			// print_r($product);exit;

			// echo $product[0];exit;

		?>

        <script>

		if(!parent.document.getElementById('product_sku<?php echo $i;?>'))

		{

			parent.addRow();

		}

			

		parent.document.getElementById('product_sku<?php echo $i;?>').value = "<?php echo $product['SKU'];?>";

		parent.document.getElementById('product_qty<?php echo $i;?>').value = "<?php echo (float)$product['Qty'];?>";

		parent.document.getElementById('product_unit<?php echo $i;?>').value = "<?php echo (float)$product['Price'];?>";

		parent.duplicateSkuCheck(parent.document.getElementById('product_sku<?php echo $i;?>'),true);

		parent.updatePrice(parent.document.getElementById('product_sku<?php echo $i;?>'),'<?php echo (float)$product[2];?>');

		parent.updateOverPrice(parent.document.getElementById('product_unit<?php echo $i;?>'));

		

		</script>

        

        <?php	

			

			$i++;

		}

		

		//unset($_SESSION['order_products']);

		//$_SESSION['order_products'] = $products;

		if(!$_SESSION['message']){

			//$_SESSION['message'] = 'Items imported successfully.';

		}

		echo "<script>parent.$.fancybox.close();</script>";

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

			 <a href="<?php echo $host_path;?>/csvfiles/ProductsImport.csv">Download Sample</a>

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