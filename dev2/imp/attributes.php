<?php
require_once("auth.php");


$mode = $_GET['mode'];
if($mode == 'edit'){
	$id = (int)$_GET['id'];
	$rows = $db->func_query_first("select * from inv_product_skus where id = '$id'");
}

if($_POST['add']){
	unset($_POST['add']);
	$attribute = "";
	
	//$db->db_exec("delete from inv_attribute where product_sku_id = '".$id."'");
	for($i=0;$i<10;$i++)
	{
		
		
		if($_POST['attribute_group'.$i]!='')
		{
			$attribute.=$_POST['attribute_group'.$i].",";
			//$j=1;
			//foreach($_POST['attribute'][$i] as $attrib)
			//{
				
				//if($attrib!='')
				//{
					/*$data = array();
					
					$data['attribute_group_id']=$_POST['attribute_group'.$i];
					$data['product_sku_id']=$id;
					
					$data['order_no']=$j;
					
					$db->func_array2insert("inv_attribute",$data);*/
					
			//$j++;			
				//}
				
			
			//}
			
		
		}
		
	}
//	$attribute = trim($attribute,",");
		
		
		$array = array();
		$array['attribute_group_id']=$attribute;
		
			$db->func_array2update("inv_product_skus",$array,"id = '$id'");
		
		
		header("Location:attribute_list.php");
		exit;
	
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Add/Edit Attribute</title>
        <script type="text/javascript" src="js/jquery.min.js"></script>
        
        
        <script>
		
		  function addAttrib(obj)
		   {
				$parent = ($(obj).parent());   
				$clone = $parent.children('input:first').clone();
			
				$parent.append("<br />");
			   $parent.append($clone);
			
		   }
	
		</script>
	</head>
	<body>
		<div align="center">
			<div align="center" style="display:none"> 
			   <?php include_once 'inc/header.php';?>
			</div>
			
			 <?php if($_SESSION['message']):?>
				<div align="center"><br />
					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
				</div>
			 <?php endif;?>
			 
			 <form action="" method="post">
			 	<a href="attribute_list.php" title="back">Back</a> <h2>Add Attributes against <?php echo $rows['sku'];?> </h2>
			    
                
                <table border="1" width="30%" cellpadding="5" cellspacing="0" align="center" >
					 <tr>	
					 	 <th>#</th>
                         <th>Attribute Group</th>
					 	
					 	 
					
					 </tr>	
					 <?php
					$xattribs = explode(",",$rows['attribute_group_id']);
					
					 for($i=0;$i<10;$i++)
					 
					 {
					 ?>
						 <tr>
						 	 <td><?php echo $i+1;?></td>
                             <td><select name="attribute_group<?php echo $i;?>" style="width:400px">
                             <option value="">Please select</option>
                             <?php
							 $attribute_groups = $db->func_query("SELECT
DISTINCT b.id,b.name
FROM
    `inv_attr` a
    INNER JOIN `inv_attribute_group` b
        ON (a.`attribute_group_id` = b.`id`)");
							 foreach($attribute_groups as $attribute_group)
							 {
								 ?>
                                 <option value="<?php echo $attribute_group['id'];?>" <?php if($xattribs[$i]==$attribute_group['id']) echo 'selected'; ?>><?php echo $attribute_group['name'];?></option>
                                 <?php
								 
							 }
							 ?>
                             
                             </select> 
                             </td>
						 	 
							  
						 </tr>
					<?php
					 }
					 ?>
				</table>
                <br /><br />
                <input type="submit" name="add" class="button" value="Update" />
                
			 </form>
		 </div>
	</body>
</html>			 		 