<?php
require_once("auth.php");

if($_SESSION['login_as'] != 'admin'){
	$_SESSION['message'] = 'You dont have permission to manage users.';
	header("Location:$host_path/sales.php");
	exit;
}

$mode = $_GET['mode'];
if($mode == 'edit'){
	$id = (int)$_GET['id'];
	$row = $db->func_query_first("select * from inv_model_mt where model_id = '$id'");
}

if($_POST['add']){
	unset($_POST['add']);
	
	
	//echo "<pre>";
	//print_r($_POST);exit;
		$array = array();
		//$array = $_POST;
		
		$array['manufacturer_id']= $_POST['manufacturer_id'];
		$array['device']= $_POST['device'];
		$array['variant_exists']= $_POST['variant_exist'];
		$array['model_type_id'] = $_POST['model_type'];
		$array['model_connection_id'] = $_POST['model_connection'];
		$array['length'] = $_POST['length'];
		$array['height'] = $_POST['height'];
		$array['width'] = $_POST['width'];
		
		$array['date_added'] = date('Y-m-d h:i:s');
		
		
		
		if($id){
			$db->func_array2update("inv_model_mt",$array,"model_id = '$id'");
			
			
			
			
		}
		else{
		$id=	$db->func_array2insert("inv_model_mt",$array);
		}
		
		$db->db_exec("DELETE FROM inv_model_dt WHERE model_id='".$id."'");
		
		if($_POST['variant_exist']==0)
		{
			$array = array();
			
			$array['sub_model']=$_POST['model_dt1'][0]['sub_model'];
			$array['model_id']=$id;
			
			$array['order_no']=1;
			$sub_model_id = $db->func_array2insert("inv_model_dt",$array);
			
			
			$data = array();
			$data['sub_model_id']= $sub_model_id;
			$data['carrier_id']= $_POST['model_dt1'][0]['carrier'];
			$data['order_no']= 1;
			$db->func_array2insert("inv_model_carrier",$data);
		}
		else
		{
			
			$i=1;
			foreach($_POST['model_dt'] as $model)
			{
				$array = array();
			$array['sub_model']=$model['sub_model'];
			$array['model_id']=$id;
			
			$array['order_no']=$i;
			
			$sub_model_id = $db->func_array2insert("inv_model_dt",$array);
			
			
			$j=1;
			foreach($model['carrier'] as $model_carrier){
				
				
				$data = array();
			$data['sub_model_id']= $sub_model_id;
			$data['carrier_id']= $model_carrier;
			$data['order_no']= $j;
			$db->func_array2insert("inv_model_carrier",$data);
				$j++;
			}
			
			
			
			
			$i++;
			
			}
			
		}
		
		header("Location:model_list.php");
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
						 	 " <td align='center'><input type='text' name='model_dt["+current_row+"][sub_model]'  /></td>"+
							 "<td align='center'><select name='model_dt["+current_row+"][carrier][]'><option value=''>Please select</option><?php
							  $carriers = $db->func_query("SELECT * FROM inv_carrier ORDER BY name");
							  
							  foreach($carriers as $carrier)
							  {
								?><option value='<?php echo $carrier['id'];?>' ><?php echo addslashes($carrier['name']);?></option><?php  
								  
							  }
							  
							  ?></select> <a href='javascript://' onClick='addCarrier(this)'>+</a></td>"+
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
		function OpenPopup(url)
	{
	window.open (url+'&window=1', "mywindow","location=1,status=1,scrollbars=1, width=600,height=1000");	
		
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
			 	<h2>Add Model</h2>
			    <table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			         <tr>
			             <td>Manufacturer</td>
			         	 <td>
                         <?php
						 $manufacturers = $db->func_query("SELECT * FROM inv_manufacturer WHERE status=1");
						 
						 ?>
                         <select id="manufacturer_id" name="manufacturer_id" required>
                         <option value="">Please Select</option>
						 <?php
                         foreach($manufacturers as $rec)
						 {
							?>
                            <option value="<?php echo $rec['manufacturer_id'];?>" <?php  if($row['manufacturer_id']==$rec['manufacturer_id']) echo 'selected';?>><?php echo $rec['name'];?></option>
                            <?php 
							 
						 }
						 ?>
                         
                         </select>
                         </td>
			         </tr>
                     
                     
                     
                     <tr>
			             <td>Model Type</td>
			         	 <td>
                         <?php
						 $model_type = $db->func_query("SELECT * FROM inv_model_type WHERE status=1");
						 
						 ?>
                         <select id="model_type" name="model_type" required>
                         <option value="">Please Select</option>
						 <?php
                         foreach($model_type as $rec)
						 {
							?>
                            <option value="<?php echo $rec['model_type_id'];?>" <?php  if($row['model_type_id']==$rec['model_type_id']) echo 'selected';?>><?php echo $rec['name'];?></option>
                            <?php 
							 
						 }
						 ?>
                         
                         </select> <a href="javascript:void(0);" onclick="OpenPopup('model_type.php?mode=new')">Add New</a>
                         </td>
			         </tr>
                     
                     
                     <tr>
                     <td>Device Name</td>
                     <td><input type="text" name="device" value="<?php echo @$row['device'];?>" required /></td>
                     </tr>
			         
			         
			         
			         
			         
			         <tr>
			             <td>Variant Exist?</td>
			         	 <td><input type="checkbox" name="variant_exist" onchange="variant(this)" value="1" <?php if($row['variant_exists']):?> checked="checked" <?php endif;?> /></td>
			         </tr>
			         
			         
			         
			         
			    </table>
                
                <br />
                
                <table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
                <tr>
                <td colspan="2" style="font-weight:bold;text-align:center">About this Model</td>
                </tr>
			         <tr>
			             <td>USB Connection</td>
			         	 <td>
                         <?php
						 $model_connection = $db->func_query("SELECT * FROM inv_model_connection WHERE status=1");
						 
						 ?>
                         <select id="model_connection" name="model_connection" required>
                         <option value="">Please Select</option>
						 <?php
                         foreach($model_connection as $rec)
						 {
							?>
                            <option value="<?php echo $rec['model_connection_id'];?>" <?php  if($row['model_connection_id']==$rec['model_connection_id']) echo 'selected';?>><?php echo $rec['name'];?></option>
                            <?php 
							 
						 }
						 ?>
                         
                         </select> <a href="javascript:void(0);" onclick="OpenPopup('model_connection.php?mode=new')">Add New</a>
                         </td>
			         </tr>
                     
                     
                     
                     
                     
                     
                     <tr>
                     <td colspan="2" style="font-weight:bold;text-align:center">Phone Dimensions</td>
                     
                     </tr>
			         <tr>
                     <td>Length</td>
                    <td><input type="text" name="length" value="<?php echo @$row['length'];?>" required /></td>
                     </tr>
                     
                     <tr>
                     <td>Height</td>
                    <td><input type="text" name="height" value="<?php echo @$row['height'];?>" required /></td>
                     </tr>
                     
                     <tr>
                     <td>Width</td>
                    <td><input type="text" name="width" value="<?php echo @$row['width'];?>" required /></td>
                     </tr>
			         
			         
			         
			         
			         
			         
			         
			         
			         
			    </table>
                <br /><br />
                
                <?php
				
				$row2 = $db->func_query_first("select * from inv_model_dt where model_id = '$id'");
				?>
                <table border="1" width="60%" cellpadding="5" cellspacing="0" align="center" id="non_variants" style="<?php if($row['variant_exists']==1) { ?>display:none <?php } ?>">
					 <tr>	
					 	 <th>#</th>
					 	 <th>Model</th>
					 	 <th>Carrier</th>
					 	 <!--<th>
					 	 	 <a href="javascript://" onclick="addRow();">Add Row</a>
					 	 </th>-->
					 </tr>	
					 
						 <tr>
						 	 <td>1</td>
						 	 <td align="center"><input type="text" name="model_dt1[0][sub_model]" value="<?php echo $row2['sub_model'];?>" /></td>
							  <td align="center"><select name="model_dt1[0][carrier]">
                              <option value="">Please select</option>
                              <?php
							  $carriers = $db->func_query("SELECT * FROM inv_carrier ORDER BY name");
							  $carrier_row = $db->func_query_first("SELECT * FROM inv_model_carrier WHERE sub_model_id='".$row2['sub_model_id']."'");
							  foreach($carriers as $carrier)
							  {
								?>
                                <option value="<?php echo $carrier['id'];?>" <?php if($carrier_row['carrier_id']==$carrier['id']) echo 'selected'; ?>><?php echo $carrier['name'];?></option>
                                <?php  
								  
							  }
							  
							  ?>
                              </select>
                              </td>
						
						 </tr>
					
				</table>
                
                
                
                <table border="1" width="60%" cellpadding="5" cellspacing="0" align="center" id="variants" style="<?php if($row['variant_exists']==0) { ?>display:none <?php } ?>">
					 <tr>	
					 	 <th>#</th>
					 	 <th>Sub Model</th>
					 	 <th>Carrier</th>
					 	 <th>
					 	 	 <a href="javascript://" onclick="addRow();">Add Row</a>
					 	 </th>
					 </tr>	
					 
						<?php
						if($row)
						{
							$rows_variant = $db->func_query("SELECT * FROM inv_model_dt WHERE model_id='".$id."' ORDER BY order_no");
							$i=0;
							foreach($rows_variant as $row_variant)
							{
								
								$carrier_rows = $db->func_query("SELECT * FROM inv_model_carrier WHERE sub_model_id='".$row_variant['sub_model_id']."' ORDER BY order_no");
							?>
                            
                             <tr>
						 	 <td><?php echo $i+1;?></td>
						 	 <td align="center"><input type="text" name="model_dt[<?php echo $i;?>][sub_model]" value="<?php echo $row_variant['sub_model'];?>" /></td>
							  <td align="center">
                              
                              <?php
							  
							  foreach($carrier_rows as $carrier_row)
							  {
							  ?>
                              
                              <select name="model_dt[<?php echo $i;?>][carrier][]">
                              <option value="">Please select</option>
                              <?php
							  $carriers = $db->func_query("select * from inv_carrier ORDER BY name");
							  
							  foreach($carriers as $carrier)
							  {
								?>
                                <option value="<?php echo $carrier['id'];?>" <?php if($carrier_row['carrier_id']==$carrier['id']) echo 'selected'; ?>><?php echo $carrier['name'];?></option>
                                <?php  
								  
							  }
							  
							  ?>
                              </select> <a href='javascript://' onClick='addCarrier(this)'>+</a> <br /><br />
                              <?php 
							  }
							  ?>
                              </td>
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
 