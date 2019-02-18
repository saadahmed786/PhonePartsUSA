<?php
require_once("auth.php");
$table = "inv_attribute_group";
$page = "attribute_group_list.php";

$mode = $_GET['mode'];
if($mode == 'edit'){
	$id = (int)$_GET['id'];
	$row = $db->func_query_first("select * from $table where id = '$id'");
}

if($_POST['add']){
	unset($_POST['add']);
	
	
	
		$array = array();
		$array['name'] = $_POST['name'];
		$array['attribute_type'] = $_POST['attribute_type'];
		$array['date_added'] = date('Y-m-d h:i:s');
		
		
		
		if($id) {
			$db->func_array2update($table,$array,"id = '$id'");
		} else {
			$exist_check = $db->func_query_first_cell("SELECT * FROM $table WHERE name='".$_POST['name']."'");
			if($exist_check)
			{
				$_SESSION['message'] = 'This Group already exists, please try with different name';
				header("Location:attribute_group.php");
				exit;	
				
			}
			$id = $db->func_array2insert($table,$array);
		}
		
		
		//$db->db_exec("DELETE FROM inv_attr WHERE attribute_group_id='".$id."'");
		
		
			
			$i=1;
			$temp_arr = array();
			foreach($_POST['attributes'] as $key => $attr)
			{
				if($attr['name'])
				{
					
					$array = array();
					$array['attribute_group_id']=$id;
					$array['name']=$attr['name'];
					$array['order_no']=$i;
					if(isset($_POST['attributes'][$key]['id']))
					{
						$temp_arr[] = $_POST['attributes'][$key]['id'];
						$db->func_array2update("inv_attr",$array,"id='".$_POST['attributes'][$key]['id']."'");	
					}
					else
					{
						
						$temp_arr[] = $db->func_array2insert("inv_attr",$array);	
					}
					$i++;
				}
			}
			
			if($temp_arr)
			{
			$db->db_exec("DELETE FROM inv_attr WHERE id NOT IN (".implode(",",$temp_arr).") AND attribute_group_id='".$id."'");	
				
			}
		
		header("Location:$page");
		exit;
	
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Add/Edit Attribute Group</title>
	</head>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script>
	 
	
	
	function addRow(){
	var current_row = $('#variants tr').length+1;	
		 	   var row = "<tr>"+
		 	   				 "<td>"+(current_row)+"</td>"+
						 	 " <td align='center'><input type='text' name='attributes["+current_row+"][name]'  /></td>"+
							 
							 "<td><a href='javascript://' onclick='$(this).parent().parent().remove();'>X</a></td>"+
						 "</tr>";
			   $("#variants").append(row);		
			   current_row++;	 
	 	   }
	
	</script>
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
			 <a href="devices_new_settings.php" title="back">Back</a>  	<h2>Add Attribute Group</h2>
			    <table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			         <tr>
			             <td>Name</td>
			         	 <td><input type="text" name="name" value="<?php echo @$row['name'];?>" required /></td>
			         </tr>
                      <tr>
			             <td>Type</td>
			         	 <td><select name="attribute_type" required>
                         <option value="general" <?php echo ($row['attribute_type']=='general'?'selected':'') ?>>General</option>
                        <option value="screw_driver" <?php echo ($row['attribute_type']=='screw_driver'?'selected':'') ?>>Screw Driver</option>
                         
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

							$xrows = $db->func_query("SELECT * FROM inv_attr WHERE attribute_group_id='".(int)$_GET['id']."' ORDER BY order_no");
							
							$i=0;
							foreach($xrows as $xrow)
							{
								
								
							?>
                            
                             <tr>
						 	 <td><?php echo $i+1;?></td>
						 	 <td align="center"><input type="text" name="attributes[<?php echo $i;?>][name]" value="<?php echo $xrow['name'];?>" /><input type="hidden" name="attributes[<?php echo $i;?>][id]" value="<?php echo $xrow['id'];?>" /></td>
							  
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
</html>			 		 