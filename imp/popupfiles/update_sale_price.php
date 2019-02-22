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

	$type = $_FILES['products']['type'];
	if(in_array($type,$csv_mimetypes)){
		$filename = $_FILES['products']['tmp_name'];
		$handle   = fopen("$filename", "r");



		$price_filename = "price_report/PriceUpdateReport-".date("Y-m-d").".csv";
		$price_csv_header  = array();
		$price_csv_header[] = 'Date Updated';
		$price_csv_header[] = 'User';
		$price_csv_header[] = 'SKU';
		$price_csv_header[] = 'Item Name';
		$price_csv_header[] = 'Price Type';
		$price_csv_header[] = 'Old Price';
		$price_csv_header[] = 'New Price';

		$price_csv_row[]= array();

		if (!file_exists($_SERVER['DOCUMENT_ROOT']."/imp/price_report/PriceUpdateReport-".date("Y-m-d").".csv")) {

			$pfile = fopen($price_filename,"w");
			fputcsv($pfile , $price_csv_header,',');
		} else {
			$pfile = fopen($price_filename,"a");
		}

		while ($data = fgetcsv($handle,1000,",","'")) {

			if (trim($data[0])!='' && trim($data[1])!='' && trim($data[1])!='0') {

				$old = $db->func_query_first('SELECT sale_price,product_id from `oc_product` WHERE sku="'. $db->func_escape_string($data[0]) .'"');
				$title = $db->func_query_first_cell('SELECT name from `oc_product_description` WHERE product_id="'. $old['product_id'] .'"');
				$user = get_username($_SESSION['user_id']);
				$price_csv_row[] = date('Y-m-d H:i:s');
				$price_csv_row[] = get_username($_SESSION['user_id']);
				$price_csv_row[] = $db->func_escape_string($data[0]);
				$price_csv_row[] = $title;
				$price_csv_row[] = 'Sale';
				$price_csv_row[] = '$'.number_format($old['sale_price'],2);
				$price_csv_row[] = '$'.number_format($data[1],2);
				fputcsv($pfile , $price_csv_row,',');




				$db->db_exec("UPDATE oc_product SET sale_price='".(float)$data[1]."',date_modified='".date("Y-m-d H:i:s")."' WHERE model='".$db->func_escape_string($data[0])."' or sku='".$db->func_escape_string($data[0])."'");

				$db->db_exec("INSERT INTO inv_product_price_change_history SET user_id='".$_SESSION['user_id']."',sku='".$db->func_escape_string($data[0])."',price_type='Sale',old_price='".(float)$old['sale_price']."',new_price='".(float)$data[1]."',date_added='".date('Y-m-d H:i:s')."'");

			}
			
			
		$k++;
    } 
fclose($pfile);
		if(!$_SESSION['message']){
			$_SESSION['message'] = 'Products Sale Price Updated successfully.';
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
	 <title>Update Products Sale Price By SKU</title>
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
			         	    <input type="file" name="products" required value="" /><br>
			         	    <a href="<?=$host_path;?>csvfiles/update-sale-price.csv">Download Sample CSV</a>
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