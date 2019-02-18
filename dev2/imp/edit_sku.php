<?php

include_once 'auth.php';
include_once 'inc/functions.php';

$product_sku = $db->func_escape_string($_GET['product_sku']);
$product_detail = $db->func_query_first("select p.product_id , p.sku , p.date_added, pd.name, p.price , p.status , p.image, p.is_main_sku from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) where sku = '$product_sku'");
if(!$product_detail){
	$_SESSION['message'] = "SKU not found";
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}

if($_POST['update']){
	print_r($_POST);
	exit;
	$skuUpdate = array();
	if($_SESSION['edit_cost']){
		$skuUpdate['price']   = $_POST['price'];
	}
	$skuUpdate['status']  = ($_POST['status']) ? 1 : 0;
	
	$image = '';
    if($_FILES['image']['tmp_name'] AND $_FILES['image']['error'] == 0){
    	$name = preg_replace("/[^a-zA-Z0-9 ]/is","",$_POST['name']);
    	$file_name = substr(str_ireplace(" ","-",strtolower($name)),0,80) . ".jpg";
    	
    	$image = "impskus/$file_name";
    	if(file_exists("../image/$image")){
    		$image = md5(microtime()) . '.jpg';
    	}
    	
    	if(move_uploaded_file($_FILES['image']['tmp_name'],"../image/$image")){
    		$skuUpdate['image']  = $image;
    	}
    }
    
    $db->func_array2update("oc_product",$skuUpdate," sku = '$product_sku'");
    if($_POST['name']){
    	$product_id = (int)$_POST['product_id'];
    	$name = $db->func_escape_string($_POST['name']);
    	$db->db_exec("update oc_product_description SET name = '$name' where product_id = '$product_id'");
    }
    
    if($product_detail['is_main_sku'] == 1){
		if($_POST['grade_a']){
	    	createGradeSku($product_sku, 'A');
	    }
	    
	 	if($_POST['grade_b']){
	    	createGradeSku($product_sku, 'B');
	    }
	    
	 	if($_POST['grade_c']){
	    	createGradeSku($product_sku, 'C');
	    }
	    
	    if($_POST['grade_d']){
	    	createGradeSku($product_sku, 'D');
	    }
    }
	$sk = $db->func_query_first('SELECT * FROM oc_product where product_id = "'. (int)($_POST['product_id']) .'"');
    $log = 'SKU Deleted '. linkToProduct($sk['sku']);
    actionLog($log);
	$_SESSION['message'] = "SKU updated";
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}

$downgrade_data = $db->func_query("select sku , item_grade , price from oc_product where main_sku = '$product_sku'","item_grade");
?>
<html>
	<head>
		<style>
		  * { font-family: Verdana, Geneva, sans-serif; font-size:11px; }
		</style>
		<link href="<?php echo $host_path?>/include/style.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<div align="center">
			<?php if($message):?>
				<h5 align="center" style="color:red;"><?php echo $message;?></h5>
			<?php endif;?>
			
			<form method="post" enctype="multipart/form-data">
				<input type="hidden" name="product_id" value="<?php echo $product_detail['product_id']?>" />
				
				<h3>Edit SKU</h3>
				<table border="1" width="90%" cellpadding="3" cellspacing="0">
					<tr>
						<td width="100">SKU</td>				
						<td align="left">
							<input type="text" name="name" value="<?php echo $product_detail['sku']?>" size="30" readonly="readonly" />
						</td>
					</tr>
					
					<tr>
						<td>Name</td>				
						<td align="left">
							<input type="text" name="name" value="<?php echo $product_detail['name']?>" size="30" required />
						</td>
					</tr>
					
					<tr>
						<td>Image</td>				
						<td>
							<input type="file" name="image" value="" />
						</td>
					</tr>
					
					<?php if($_SESSION['edit_cost']):?>
						<tr>
							<td>Price</td>				
							<td>
								<input type="number" name="price" step="any" value="<?php echo $product_detail['price']?>" required />
							</td>
						</tr>
					<?php endif;?>	
					
					<tr>
						<td>Status</td>				
						<td>
							<input type="checkbox" name="status" value="1" <?php if($product_detail['status'] == 1):?> checked="checked" <?php endif;?> />
						</td>
					</tr>
					
					<?php if($product_detail['is_main_sku'] == 1):?>
						<tr>
							<td>Grade A</td>				
							<td>
								<?php if($downgrade_data['Grade A']['sku']):?>
										<input type="text" name="grade_a_sku" value="<?php echo $downgrade_data['Grade A']['sku']?>" />
								<?php else:?>
										<input type="checkbox" name="grade_a" value="1" />
								<?php endif;?>		
							</td>
						</tr>
						
						<tr>
							<td>Grade B</td>				
							<td>
								<?php if($downgrade_data['Grade B']['sku']):?>
										<input type="text" name="grade_b_sku" value="<?php echo $downgrade_data['Grade B']['sku']?>" />
								<?php else:?>
										<input type="checkbox" name="grade_b" value="1" />
								<?php endif;?>	
							</td>
						</tr>
						
						<tr>
							<td>Grade C</td>				
							<td>
								<?php if($downgrade_data['Grade C']['sku']):?>
										<input type="text" name="grade_c_sku" value="<?php echo $downgrade_data['Grade C']['sku']?>" />
								<?php else:?>
										<input type="checkbox" name="grade_c" value="1" />
								<?php endif;?>	
							</td>
						</tr>
						
						<tr>
							<td>Grade D</td>				
							<td>
								<?php if($downgrade_data['Grade D']['sku']):?>
										<input type="text" name="grade_d_sku" value="<?php echo $downgrade_data['Grade D']['sku']?>" />
								<?php else:?>
										<input type="checkbox" name="grade_d" value="1" />
								<?php endif;?>	
							</td>
						</tr>
					<?php endif;?>
					
					<tr>
						<td colspan="2" align="center">
							<input type="submit" name="update" value="Submit" />
						</td>
					</tr>
				</table>
			</form>
		</div>	
	</body>
</html>