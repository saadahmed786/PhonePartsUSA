<?php
require_once("auth.php");
$sku = $_POST['sku'];

$rec = $db->func_query_first("SELECT attribute_group_id FROM inv_product_skus WHERE id='".$sku."'");

$rec = explode(",",$rec['attribute_group_id']);
$xx = "";
foreach($rec as $row)
{
	if($row!='')
	{
		$xx.=$row.",";
		$attrib_group = $db->func_query_first("SELECT * FROM inv_attribute_group WHERE id='".$row."'");	
		
		?>
        
        <strong><?php echo $attrib_group['name'];?> </strong><br />
        
        <?php
		
	}
	
}

trim($xx,",");

?>
<script>
callAttribs('<?php echo $xx;?>');

</script>
