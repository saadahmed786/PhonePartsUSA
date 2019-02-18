<?php

include_once 'auth.php';
include_once 'inc/functions.php';
$action = $_GET['action'];
$id = (int)$_GET['id'];

if($_POST['kit_sku'] && $action){
	$kit_sku = array();
	$kit_sku['kit_sku']    = $_POST['kit_sku'];
	$kit_sku['linked_sku'] = $_POST['linked_sku'];
	$kit_sku['need_sync']  = 1;
	$kit_sku['dateofmodifcation'] = date('Y-m-d H:i:s');
	
	$error = false;
	//check all linked sku are exist or not?
	$linked_skus = explode(",",$kit_sku['linked_sku']);
	foreach($linked_skus as $linked_sku){
		$isExist = $db->func_query_first_cell("select product_id from oc_product where sku = '$linked_sku' or model = '$linked_sku'");
		if(!$isExist){
			$error = true;
			$_SESSION['message'] = "Linked sku $linked_sku is not exist. Please add to opencart and then try to add kit sku."; 
		}
	}
	
	if($error == false){
		if($id){
			$db->func_array2update("inv_kit_skus", $kit_sku, "id = '$id'");
			$_SESSION['message'] = "KIT sku updated successfully.";
		}
		else{
			$db->func_array2insert("inv_kit_skus", $kit_sku);
			$_SESSION['message'] = "KIT sku added successfully.";
		}
	}

	$log = 'Kit SKU '. (($action == 'new')? 'created': 'updatd') .' for ' . linkToProduct($kit_sku['kit_sku']);
    actionLog($log);
	
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}

if($action == 'edit' AND $id){
	$kit_sku = $db->func_query_first("Select * from inv_kit_skus where id = '$id'");
}

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>ADD / Edit KIT SKU</title>
    </head>
    
    <body>
    	<form method="post" action="">
    		<table>
    			 <tr>
    			 	<td>KIT SKU</td>
    			 	<td>
    			 		<input type="text" name="kit_sku" value="<?php echo @$kit_sku['kit_sku']?>" required />
    			 	</td>	
    			 </tr>
    			 
    			 <tr>
    			 	<td>Linked SKU<br/>(enter skus with comma seperted)</td>
    			 	<td>
    			 		<textarea rows="3" cols="50" name="linked_sku" required><?php echo @$kit_sku['linked_sku']?></textarea>
    			 	</td>	
    			 </tr>
    		
    			 <tr>
    			 	<td>
						<input type="submit" name="add" value="Submit" />    			 	
    			 	</td>
    			 </tr>
    		</table>
    	</form>	
    </body>
</html>        