<?php

include '../auth.php';
 function changeImageName($data)
	{
		
		 $string = str_replace(' ', '-', $data); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
		
	}
 function updateMainProductImage($data)
	{
		global $db;
		
		
		$sku = $data[0];
		
		$folder_name = substr($sku,0,7);
		
		$dir = 'data/'.$folder_name.'/'.$sku.'/';
		
		if (file_exists('../../image/'.$dir)==false) {
    mkdir('../../image/'.$dir, 0775, true);

}



$products = $db->func_query("SELECT a.*,b.name FROM oc_product a,oc_product_description b where a.product_id=b.product_id AND a.sku='".$sku."'");



		foreach($products as $product)
		{
			//$product_details = 	$this->getProduct($product['product_id']);
			$extension = explode(".",$data[1]);
			$extension = end($extension);
			//echo $extension;exit;
			$image = $dir.$sku.'-'.changeImageName($product['name']).'-1.'.$extension;
		 //echo $image;exit;
			if($data[1]!='')
			{
				
				
			if(copy('../../image/'.$data[1], '../../image/'.$image))
		{
			
		}
		else
		{
		file_put_contents('import_csv.log', PHP_EOL."Main Image:".$data[1].PHP_EOL."Renamed Image: ".$image.PHP_EOL."Date Time: ".date('d-m-Y h:i:s').PHP_EOL.PHP_EOL."****************************************", FILE_APPEND);	
		}
		
		
		$db->db_exec("UPDATE `oc_product` SET image='".$image."' WHERE product_id='".$product['product_id']."'");
		
			}
		
		
		
		
		/* Sub image Import */
		
		
		$dataArray[] = array('image' =>$data[2]);
			  $dataArray[] = array('image' =>$data[3]);
			  $dataArray[] = array('image' =>$data[4]);
			  $dataArray[] = array('image' =>$data[5]);
			  $dataArray[] = array('image' =>$data[6]);
			  $dataArray[] = array('image' =>$data[7]);
		
		
		
		
		if($dataArray[0]['image']!=''
		   or $dataArray[1]['image']!=''
		   or $dataArray[2]['image']!=''
		   or $dataArray[3]['image']!=''
		   or $dataArray[4]['image']!=''
		   or $dataArray[5]['image']!=''
		    )
		
		{
		
		
		$db->db_exec("DELETE FROM oc_product_image WHERE product_id = '" . (int)$product['product_id'] . "'");
		}
		
		$i=1;
		
		foreach($dataArray as $data1)
		{
		
		if($data1['image']!='')
		{
			$extension = explode(".",$data[1]);
			$extension = end($extension);
			$image = $dir.$sku.'-'.changeImageName($product['name']).'-'.($i+1).'.'.$extension;
			
			
			if(copy('../../image/'.$data1['image'], '../../image/'.$image)){
				
				
			}
			else
			{
			file_put_contents('import_csv.log', PHP_EOL."Sub Image:".$data1['image'].PHP_EOL."Renamed Image: ".$image.PHP_EOL."Date Time: ".date('d-m-Y h:i:s').PHP_EOL.PHP_EOL."****************************************", FILE_APPEND);	
			}
			
		
		$db->db_exec("INSERT INTO oc_product_image SET product_id = '" . (int)$product['product_id'] . "', image = '" . $db->func_escape_string(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . $i . "'");
		$i++;
		}
		}
		
		
	
		
		
		
		
		
		/* Sub Image ends */
			
			
		}
		
		
		
		
		
	}
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

		while ($data = fgetcsv($handle,1000,",","'")) {
		if($k>0)
		{
        if ($data[0]!='') {
          
		  
			updateMainProductImage($data);  
			  
		  
			  $dataArray = array();
			  
			 // $dataArray = $
			 /* $dataArray[] = array('image' =>$data[2]);
			  $dataArray[] = array('image' =>$data[3]);
			  $dataArray[] = array('image' =>$data[4]);
			  $dataArray[] = array('image' =>$data[5]);
			  $dataArray[] = array('image' =>$data[6]);
			  $dataArray[] = array('image' =>$data[7]);
			  
			  $this->model_catalog_product->updateSubProductImage($dataArray,$data[3],$data);*/
		  
		 
		  
        }
		else
		{
			
		file_put_contents('import_csv.log', PHP_EOL."Filename:".$_FILES['products']['name'].PHP_EOL."data: ".json_encode($data)."Date Time: ".date('d-m-Y h:i:s').PHP_EOL.PHP_EOL."****************************************", FILE_APPEND);	
		}
		}
		$k++;
    } 

		if(!$_SESSION['message']){
			$_SESSION['message'] = 'Products Images uploaded successfully.';
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
	 <title>Import Images</title>
  </head>
  <body>
  	  <div class="div-fixed">
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<div align="center">
			 <a href="<?php echo $host_path;?>../admin/csv_templates/1.csv">Download Sample</a>
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