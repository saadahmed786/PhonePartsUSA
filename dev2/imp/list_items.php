<?php

include_once 'config.php';

if(!$_SESSION['email']){
    header("Location:index.php");
}

$results = array();

$product_ids = implode(",",array_keys($_SESSION['list']));
if($product_ids){
	$inv_query  = "select p.product_id , p.model from oc_product p where p.product_id in ($product_ids)";
	$results = $db->func_query($inv_query);
}

if(count($results) == 0){
	echo "<p>No products added to list</p>";
}
else{
?>

<a onclick="jQuery('#cart_data').toggle();">Total <span id="total"><?php echo count($_SESSION['list'])?></span> Items</a>
        	 
<div id="cart_data" style="z-index: 10000; display: none;background:#e5e5e5;">
	<table border="1" cellpadding="5" cellspacing="0" width="200px" style="border-collapse:collapse;">
		<tr>
			<th>Model</th>
			<th>Qty</th>
			<th></th>
		</tr>
		<?php foreach($results as $listItem):?>
			<tr id="row_<?php echo $listItem['product_id'];?>">
				<td><?php echo $listItem['model']?></td>
				
				<td><?php echo $_SESSION['list'][$listItem['product_id']]['qty']?></td>
				
				<td><a href="javascript://" onclick="removeFromList(<?php echo $listItem['product_id'];?>);">X</a></td>
			</tr>
		<?php endforeach;?>
	</table>
</div>
<?php }?>