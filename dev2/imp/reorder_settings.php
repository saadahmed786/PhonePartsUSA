<?php

include_once 'auth.php';

if($_POST['update']){
	$reorder_setting = array();
	$reorder_setting['lead_time']     = $_POST['lead_time'];
	$reorder_setting['qc_time']       = $_POST['qc_time'];
	$reorder_setting['safety_stock']  = $_POST['safety_stock'];
	$reorder_setting['additional_days']  = $_POST['additional_days'];
	$reorder_setting['dateofmodification'] = date('Y-m-d H:i:s');
	
	$db->func_array2update("inv_reorder_settings", $reorder_setting, "id = '1'");
	$_SESSION['message'] = "Settings Saved successfully.";
	
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}

$reorder_setting = $db->func_query_first("select * from inv_reorder_settings");
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>ReOrder Setting</title>
    </head>
    
    <body>
    	<form method="post" action="">
    		<table>
    			 <tr>
    			 	<td>Lead Time (In Days)</td>
    			 	<td>
    			 		<input type="number" name="lead_time" value="<?php echo @$reorder_setting['lead_time']?>" required />
    			 	</td>	
    			 </tr>
    			 
    			 <tr style="display:none">
    			 	<td>QC Time (In Days)</td>
    			 	<td>
    			 		<input type="number" name="qc_time" value="<?php echo @$reorder_setting['qc_time']?>" required />
    			 	</td>	
    			 </tr>
    			 
    			 <tr>
    			 	<td>Safety Stock</td>
    			 	<td>
    			 		<input type="number" name="safety_stock" value="<?php echo @$reorder_setting['safety_stock']?>" required />
    			 	</td>	
    			 </tr>
    			 
    			 <tr style="display:none">
    			 	<td>Additinal Days</td>
    			 	<td>
    			 		<input type="number" name="additional_days" value="<?php echo @$reorder_setting['additional_days']?>" required />
    			 	</td>	
    			 </tr>
    		
    			 <tr>
    			 	<td align="center" colspan="2">
    			 		<br />
						<input type="submit" name="update" value="Update" />    	
						<br />		 	
    			 	</td>
    			 </tr>
    		</table>
    	</form>	
    </body>
</html>        