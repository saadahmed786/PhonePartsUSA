<?php
require_once("auth.php");
/*$sku = $_POST['sku'];
$sku_type = $_POST['sku_type'];
$sku = $sku_type.'-'.$sku;
$product_title = $db->func_query_first("SELECT
b.name,b.product_id
FROM
    `oc_product` a
    INNER JOIN `oc_product_description` b
        ON (a.`product_id` = b.`product_id`)
		
		WHERE a.sku='$sku'
		");*/
		
		
		$sku_type_id = $_POST['sku']; 
		$sku_type = $db->func_query_first("SELECT * FROM inv_product_skus WHERE id='".$sku_type_id."'");
		$sku_type = $sku_type['sku'];
		$sku_exist = (isset($_POST['sku_exist'])?1:0);
		
		if($sku_exist==0) $_POST['product_sku']=='';
		
		if($_POST['product_sku'])
		{
		$sku = $sku_type.'-'.$_POST['product_sku'];
		}
		else
		{
		$sku = '';	
		}
		
		$attribs = $_POST['zattrib'];
		
		if($sku)
		{
			$product = $db->func_query_first("SELECT
b.name,b.product_id
FROM
    `oc_product` a
    INNER JOIN `oc_product_description` b
        ON (a.`product_id` = b.`product_id`)
		
		WHERE a.sku='$sku'
		");
		$product_title = $product['name'];
		}
		else
		{
			
			$product_title='';
		}
		
		
if($product_title)
{
		$_SESSION['device_parts'][$sku] = array(
												'sku'=>$sku,
												'sku_type_id' => $sku_type_id,
												'sku_type'=>$sku_type,
												'product_title'=>$product_title,
												'sku_exist'=>$sku_exist,
												'attribs'=>$attribs
											);
		
}
			
		
		
		
?>

<div align="center">
			 	  <table border="1" width="900px;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			 	  	 <tr style="background-color:#e7e7e7;">
			 	  	 	 
			 	  	 	 <td>SKU Type</td>
			 	  	 	 <td>SKU</td>
                         <td>Item Title</td>
                         <td>Attributes</td>
                     
                         
			 	  	 </tr>
                    <?php
					foreach($_SESSION['device_parts'] as $sku => $parts)
					{
						
						?>
                        
                         <tr >
			 	  	 	 
			 	  	 	 <td><?php echo $parts['sku_type'];?></td>
			 	  	 	 <td><?php echo $parts['sku'];?></td>
                         <td><?php echo $parts['product_title']; ?></td>
                         <td><?php
                         foreach($parts['attribs'] as $attrib)
						 {
							$attrib_info = $db->func_query_first("SELECT * FROM inv_attr WHERE id='".$attrib."'");
							
							$attrib_group = $db->func_query_first("SELECT * FROM inv_attribute_group WHERE id='".$attrib_info['attribute_group_id']."'");
							
							echo "<strong>".$attrib_group['name'].':</strong> '.$attrib_info['name']."<br>";
							 
						 }
						 
						 ?></td>
                     
                         
			 	  	 </tr>
                        <?php	
						
					}
					
					?>
			 	  	 
			 	  	 <tr>
                     
                     <td colspan="4" align="center"> <input type="submit" name="add" value="Save" /></td>
                     </tr>
			 	  </table>
		     </div>
