<?php
require_once("auth.php");
$attribs = $_POST['attribs'];
$sku_id = $_POST['sku_id'];
$attribs = explode(",",$attribs);


foreach($attribs as $attrib)
{
$group_attribute = $db->func_query_first("SELECT a.* FROM inv_attribute_group a,inv_attr b WHERE a.id=b.attribute_group_id AND a.id='".$attrib."' ");	

echo "<h2>".$group_attribute['name']."</h2>";
$attributes = $db->func_query("SELECT * FROM inv_attr WHERE attribute_group_id='".$attrib."'");




foreach($attributes as $attribute)
{
	
?>
<input type="checkbox" name="zattrib[]" class="xattribs"  id="xattribs<?php echo $attribute['id'];?>" value="<?php echo $attribute['id'];?>" /> <label for="checkbox<?php echo $attribute['id'];?>"  ><strong><?php echo $attribute['name'];?> </strong></label><br />
<?php	
}

}

?>
