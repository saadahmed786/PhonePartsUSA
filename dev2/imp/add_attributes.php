<?php
require_once("auth.php");


$mode = $_GET['mode'];
if($mode == 'edit'){
	$id = (int)$_GET['id'];
	$row = $db->func_query_first("select * from inv_attr where attribute_group_id = '$id'");
}

if($_POST['add']){
	unset($_POST['add']);
	
	
	
			
			
			
		
		
		$db->db_exec("DELETE FROM inv_attr WHERE attribute_group_id='".$_POST['attribute_group_id']."'");
		
		
			
			$i=1;
			foreach($_POST['attributes'] as $attr)
			{
				$array = array();
			$array['attribute_group_id']=$_POST['attribute_group_id'];
			$array['name']=$attr['name'];
			
			$array['order_no']=$i;
			
		 $db->func_array2insert("inv_attr",$array);
			
			
	
			
			
			
			
			$i++;
			
			}
			
		
		
		header("Location:add_attr_list.php");
		exit;
	
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Add/Edit Model</title>
         <script type="text/javascript" src="js/jquery.min.js"></script>
         
         <script>
		 var current_row = $('#variants tr').length+1;
	
	
	function addRow(){
		 	   var row = "<tr>"+
		 	   				 "<td>"+(current_row)+"</td>"+
						 	 " <td align='center'><input type='text' name='attributes["+current_row+"][name]'  /></td>"+
							 
							 "<td><a href='javascript://' onclick='$(this).parent().parent().remove();'>X</a></td>"+
						 "</tr>";
			   $("#variants").append(row);		
			   current_row++;	 
	 	   }
		   
		   function addCarrier(obj)
		   {
				$parent = ($(obj).parent());   
				$clone = $parent.children('select:first').clone();
			
				$parent.append("<br /><br />");
			   $parent.append($clone);
			
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
			 
			 <form action="" method="post">
			 	<h2>Add Attribute</h2>
			    <table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			         <tr>
			             <td>Attribute Group</td>
			         	 <td>
                         <?php
						 $groups = $db->func_query("SELECT * FROM inv_attribute_group");
						 
						 ?>
                         <select id="attribute_group_id" name="attribute_group_id" required>
                         <option value="">Please Select</option>
						 <?php
                         foreach($groups as $rec)
						 {
							?>
                            <option value="<?php echo $rec['id'];?>" <?php  if($row['attribute_group_id']==$rec['id']) echo 'selected';?>><?php echo $rec['name'];?></option>
                            <?php 
							 
						 }
						 ?>
                         
                         </select>
                         </td>
			         </tr>
                     
			         
			         
			         
			         
			         
			         
			         
			         
			         
			         
			    </table>
                
                <br /><br />
                
             
                
                
                
                <table border="1" width="60%" cellpadding="5" cellspacing="0" align="center" id="variants" style="">
					 <tr>	
					 	 <th>#</th>
					 	 <th>Attribute Name</th>
					 	 
					 	 <th>
					 	 	 <a href="javascript://" onclick="addRow();">Add Row</a>
					 	 </th>
					 </tr>	
					 
						<?php
						if($row)
						{
							$xrows = $db->func_query("SELECT * FROM inv_attr WHERE attribute_group_id='".$id."' ORDER BY order_no");
							$i=0;
							foreach($xrows as $xrow)
							{
								
								
							?>
                            
                             <tr>
						 	 <td><?php echo $i+1;?></td>
						 	 <td align="center"><input type="text" name="attributes[<?php echo $i;?>][name]" value="<?php echo $xrow['name'];?>" /></td>
							  
							 <td><a href='javascript://' onclick='$(this).parent().parent().remove();'>X</a></td>
						 </tr>
                            <?php	
								$i++;
							}
							
						}
						
						?>
					
				</table>
                <br /><br />
              <div style="text-align:center">  <input type="submit" name="add" value="Submit" /></div>
			 </form>
		 </div>
	</body>
    <script>
	
	
	       
	function variant(obj)
	{
		if(obj.checked==true)
		{
			$('#non_variants').fadeOut();
			$('#variants').fadeIn();	
			
		}
		else
		{
			$('#non_variants').fadeIn();
			$('#variants').fadeOut();	
			
		}
		
	}
	
	
	
	</script>
</html>			 		
 