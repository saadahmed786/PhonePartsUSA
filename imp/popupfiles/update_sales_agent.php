<?php

include '../auth.php';
if($_SESSION['login_as']!='admin' && !$_SESSION['update_sales_agent'])
{
	echo 'You do not have sufficent permission to access this page';exit;
}
 $agents =$db->func_query("SELECT id,name,trim(lower(email)) as email FROM inv_users WHERE is_sales_agent=1");
 $agents_array = array();
 foreach($agents as $agent)
 {
 	$agents_array[$agent['email']] = $agent['id'];
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
$error_log = '';
// $success_log = '';
foreach($products as $product)
{
	if(isset($agents_array[$product['Agent Email']]))
	{

	$check_user = $db->func_query_first_cell("SELECT user_id from inv_customers where TRIM(LOWER(email))='".$db->func_escape_string(trim(strtolower($product['Customer Email'])))."'");
	$date_time = date('Y-m-d H:i:s');
	if($check_user!=(int)$agents_array[$product['Agent Email']])
	{	
		 $db->db_exec("UPDATE inv_customers SET user_id='".(int)$agents_array[$product['Agent Email']]."',sales_assigned_date='".$date_time."' WHERE TRIM(LOWER(email))='".$db->func_escape_string(trim(strtolower($product['Customer Email'])))."'");

	}
		//actionLog($product['Customer Email'].' sales agent updated to '.$product['Agent Email']);

		// $success_log.="<strong>".$product['Agent Email'].'</strong> agent updated against Customer <strong>'.$product['Customer Email']."</strong><br>";
	}
	else
	{
		$error_log.="<strong>".$product['Agent Email'].'</strong> agent not found in system against Customer <strong>'.$product['Customer Email']."</strong><br>";
	}
}


		if($error_log!=''){
			$_SESSION['message'] = 'There are some errors in the document: <br>'.$error_log;
		}
		else
		{
			$_SESSION['message'] = 'Sales Agent updated by Customers';	
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
	 <title>Update Products Qty By SKU</title>
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
			         	    <a href="<?=$host_path;?>csvfiles/update-sales-agent.csv">Download Sample CSV</a>
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