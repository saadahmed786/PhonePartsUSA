<?php
require_once("auth.php");


if(isset($_POST['add']))
{
	$db->db_exec("delete from inv_device_parts where device_id = '".(int)$_GET['model_id']."' AND model_id='".(int)$_GET['sub_model_id']."' AND  carrier_id='".(int)$_GET['carrier_id']."'");
	
	
		foreach($_SESSION['device_parts'] as $parts)
		{
	$array = array();
	$array['device_id'] = $_GET['model_id'];
	$array['model_id'] = $_GET['sub_model_id'];
	$array['carrier_id'] = $_GET['carrier_id'];
	$array['sku_type_id']=$parts['sku_type_id'];
	$array['sku']=$parts['sku'];
		$xattrib = '';
	foreach($parts['attribs'] as $attrib)
	{
		
		$xattrib = $xattrib.','.$attrib;
	}
	
	$array['attributes'] = ltrim($xattrib,",");
	
	$db->func_array2insert("inv_device_parts",$array);
	
		}
	
}

unset($_SESSION['device_parts']);
$mode = $_GET['mode'];
if($mode == 'edit'){
	$model_id = (int)$_GET['model_id'];
	$sub_model_id = (int)$_GET['sub_model_id'];
	$carrier_id = (int)$_GET['carrier_id'];
	$row = $db->func_query_first("SELECT
a.*,
b.*,
d.id AS carrier_id,
d.`name` AS carrier_name,
e.name AS manufacturer
FROM
    `inv_model_mt` a
    INNER JOIN `inv_model_dt` b 
        ON (a.`model_id` = b.`model_id`)
    INNER JOIN `inv_model_carrier` c
        ON (b.`sub_model_id` = c.`sub_model_id`)
    INNER JOIN `inv_carrier` d
        ON (c.`carrier_id` = d.`id`)
         INNER JOIN `inv_manufacturer` e
        ON (a.`manufacturer_id` = e.`manufacturer_id`)
        
        
        WHERE a.`model_id`='$model_id' AND b.`sub_model_id`='$sub_model_id' AND d.`id`='$carrier_id'");
		$title = $row['manufacturer'].' '.$row['device'].' '.$row['sub_model'].' '.$row['carrier_name'];
		
		
		
	$records = $db->func_query("SELECT * FROM inv_device_parts WHERE device_id='".$model_id."' AND model_id='".$sub_model_id."' AND carrier_id='".$carrier_id."'");
}

if($records)
{
	foreach($records as $record)
	{
		$SKUType = $db->func_query_first("SELECT * FROM inv_product_skus WHERE id='".$record['sku_type_id']."'");
	
	$product = $db->func_query_first("SELECT
b.name,b.product_id
FROM
    `oc_product` a
    INNER JOIN `oc_product_description` b
        ON (a.`product_id` = b.`product_id`)
		
		WHERE a.sku='".$record['sku']."'
		");
		$product_title = $product['name'];
	
		$_SESSION['device_parts'][$record['sku']] = array(
												'sku'=>$record['sku'],
												'sku_type_id' => $record['sku_type_id'],
												'sku_type'=>$SKUType['sku'],
												'product_title'=>$product_title,
												'sku_exist'=>1,
												'attribs'=>explode(",",$record['attributes'])
											);
		
	}
		
	
	
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title><?php echo $title;?></title>
        
         <script type="text/javascript" src="js/jquery.min.js"></script>
        
        
        <script>
		
		
		
		function callAttribs(attribs)
		   {
			  
			
				            $.ajax({
                url: "ajax_attributes.php",
				type:"POST",
                data: {attribs: attribs,sku_id:$("select[name=sku]").val()},
                success: function(data) {
					
					
					$('#xattributes').html(data);
					}
            });
			$("#attribute_list").val(attribs);
		   }
		
		  function callGroupAttribs(obj)
		   {
				if(obj.value=='') return false;
				
				            $.ajax({
                url: "ajax_group_attribs.php",
				type:"POST",
                data: {sku: obj.value},
                success: function(data) {
					
					
					$('#group_attributes').html(data);
					}
            });
       

				
			
		   }
		   
		   function MapSKU()
		   {
			   var sku = $('#product_sku').val();
			   var sku_type = $('select[name=sku]').val();
			   if(sku=='')
			   {
				alert("Please provide SKU");
				return false;   
			   }
			   if(sku_type=='')
			   {
				alert("Please provide SKU Type");
				return false;   
			   }
			   
			   $.ajax({
                url: "ajax_map_sku.php",
				type:"POST",
                data: $('#myForm').serialize(),
                success: function(data) {
					
					
					$('#mapping').html(data);
					}
            }); 
			   
		   }
		  
	
		</script>
	</head>
	<body>
		<div align="center">
			<div align="center"> 
			   <?php include_once 'inc/header.php';?>
			</div>
			
			 <?php if($_SESSION['message']):?>
				<div align="center"><br />
					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
				</div>
			 <?php endif;?>
			 
			 <form id="myForm" action="" method="post">
			 	<h2><?php echo $title;?></h2>
			    <table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			         <tr>
			             <td>Add Part</td>
			         	 <td>
                         <select name="sku" required onChange="callGroupAttribs(this)" style="width:300px">
                         <option value="">Please select</option>
                         <?php
						 $skus = $db->func_query("SELECT * FROM inv_product_skus WHERE attribute_group_id<>'' ORDER BY sku");
						 foreach($skus as $sku)
						 {
							?>
                            <option value="<?php echo $sku['id'];?>"><?php echo $sku['sku'];?></option>
                            <?php 
							 
						 }
						 ?>
                         
                         
                         
                         
                         </select>
                         
                         </td>
			         </tr>
			         
			         
			         
			         
			         
			         <tr style="display:none">
			             <td>Group Attributes</td>
			         	 <td id="group_attributes"></td>
			         </tr>
                     
                    <tr>
			             <td>Attributes</td>
			         	 <td id="xattributes"></td>
			         </tr>
                     
                      <tr>
			             <td>SKU Exists</td>
			         	 <td ><input type="checkbox" name="sku_exist" value="1" onclick="if(this.checked==true){$('.product_sku').fadeIn();}else{$('.product_sku').fadeOut();}" /> <input type="text" class="product_sku" id="product_sku" name="product_sku"  style="display:none" /></td>
			         </tr>
			         
			         
			         
			         <tr>
			             <td colspan="2">
			             	 <input type="button"  value="Add" onclick="MapSKU();" />
			             </td>
			         </tr>
			    </table>
                <input id="attribute_list" type="hidden" /><input name="product_title" type="hidden" value="<?php echo $title;?>" />
                
                 <div id="mapping" style="margin-top:10px">
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
             
             </div>
			 </form>
             
            
		 </div>
         
	</body>
</html>			 		 