<?php

include_once 'auth.php';
include_once 'inc/functions.php';

$grade = $_GET['grade'];

$main_sku  = $db->func_escape_string($_GET['main_sku']);
$parts     = explode("-",$main_sku);
$part_type = $parts[0]."-".$parts[1];

$product_details = $db->func_query_first("select p.product_id , name , image, price , description, manufacturer_id  from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) where p.model = '$main_sku'");

$last_id = getProductSkuLastID($part_type);
$last_id = (int)$last_id;
$new_sku = getSKUFromLastId($part_type , $last_id);

if($_POST['add']){
	
	// Updated Price wrt Product Pricing
	$price = $product_details['price'];
	$costing = $db->func_query_first("SELECT * FROM inv_product_costs WHERE sku='".$main_sku."' ORDER BY id DESC LIMIT 1");
	if($costing)
	{
		
		
		
		
		$true_cost = ($costing['raw_cost'] + $costing['shipping_fee']) / $costing['ex_rate'];
		
		
		
		 $markup = $db->func_query_first("SELECT * FROM  inv_product_pricing WHERE  $true_cost BETWEEN COALESCE(`range_from`,$true_cost) AND COALESCE(`range_to`,$true_cost)");
		
	/*	switch($grade)
		{
			case 'A':
				$price = $true_cost*$markup['grade_a'];
			break;	
			case 'B':
				$price = $true_cost*$markup['grade_b'];
			break;	
			case 'C':
				$price = $true_cost*$markup['grade_c'];
			break;	
			
			default:
			$price = $price;
			break;
			
		}*/
	}
	
	
	// End Pricing
	
	$product_id = createSKU($new_sku , $product_details['name'] , $product_details['description'] , $price , $main_sku , 0, $grade , $product_details['image']);

	$db->db_exec("update oc_product SET location = 1 , manufacturer_id = '".$product_details['manufacturer_id']."' where sku = '$new_sku'");
	$db->db_exec("insert ignore into oc_product_to_store SET product_id = '$product_id' , store_id = 0");
	$db->db_exec("insert into oc_product_to_category (product_id , category_id) select $product_id , category_id from oc_product_to_category where product_id = '".$product_details['product_id']."'");
	
	$_SESSION['message'] = "SKU created";
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
}
?>
<html>
	<body>
		<div align="center">
			<?php if($message):?>
				<h5 align="center" style="color:red;"><?php echo $message;?></h5>
			<?php endif;?>
			
			<form method="post">
				<table width="100%">
					<tr>
						<td><b>Title:</b></td>
						<td><?php echo $product_details['name']. " - Grade $grade";?></td>					
					</tr>
					
					<tr>
						<td><b>SKU:</b></td>
						<td><?php echo $new_sku;?></td>					
					</tr>
					
					<tr>
						<td colspan="2" align="center"><input type="submit" name="add" value="Create" /></td>					
					</tr>
				</table>
			</form>		
		</div>	
	</body>
</html>