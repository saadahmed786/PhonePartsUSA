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

		while ($data = fgetcsv($handle,1000,",","'")) {
		
        if (trim($data[0])!='') {
          $db->db_exec("UPDATE oc_product SET status=0,date_modified='".date("Y-m-d H:i:s")."' WHERE model='".$db->func_escape_string($data[0])."' or sku='".$db->func_escape_string($data[0])."'");
        }
		
		
		$k++;
    } 

		if(!$_SESSION['message']){
			$_SESSION['message'] = 'Products Disabled successfully.';
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
	 <title>Disable Products By SKU</title>
  </head>
  <body>
  	  <div class="div-fixed">
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<div align="center">
			 
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