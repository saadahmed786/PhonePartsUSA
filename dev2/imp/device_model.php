<?php

require_once("auth.php");
require_once("inc/functions.php");
page_permission("device_model");
$table = "inv_d_model";
$page = 'device_model.php';
$title = "Device Model";
$mode = $_GET['mode'];

if($mode == 'edit'){
	$id = (int)$_GET['id'];
	$result = $db->func_query_first("select * from $table where id = '$id'");
}
if($mode=='delete')
{
	$id = (int)$_GET['id'];
	$db->db_exec("DELETE FROM $table WHERE id='".$id."'");	
	$_SESSION['message'] = "Record Deleted";
	header("Location: $page");
	exit;
}

if($_POST['add']){
	unset($_POST['add']);
	
	$array = array();
	$array = $_POST;
	$array['user_id'] = $_SESSION['user_id'];
	$array['date_added'] = date('Y-m-d H:i:s');
	if($id){
		
		$db->func_array2update($table,$array,"id = '$id'");
	}
	else{
		
		$id = $db->func_array2insert($table,$array);
	}
	
	header("Location: $page");
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title><?=$title;?></title>
		<script type="text/javascript" src="js/jquery.min.js"></script>
		
	
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
			 
			 <form action="" method="post" enctype="multipart/form-data">
			 	<h2><a href="devices_settings.php" title="back">&laquo;</a> <?=$title;?></h2>
			    <table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			         
			         <?php
					 
					 $rows = $db->func_query("SELECT * FROM inv_d_type WHERE status=1 ORDER BY name");
					 ?>
			       <tr>
			             <td>Device Type:</td>
			         	 <td><select name="device_type_id" required >
                         <option value="">Please Select</option>
                         <?php
						 foreach($rows as $row)
						 {
						 ?>
                         <option value="<?=$row['id'];?>" <?=($row['id']==$result['device_type_id']?'selected':'');?> ><?=$row['name'];?></option>
                         <?php
						 
						 }
						 
						 ?>
                         </select>
                         </td>
			         </tr>  
                     
                       <?php
					 
					 $rows = $db->func_query("SELECT * FROM inv_d_manufacturer WHERE status=1 ORDER BY name");
					 ?>
			       <tr>
			             <td>Manufacturer:</td>
			         	 <td><select name="manufacturer_id" required >
                         <option value="">Please Select</option>
                         <?php
						 foreach($rows as $row)
						 {
						 ?>
                         <option value="<?=$row['id'];?>" <?=($row['id']==$result['manufacturer_id']?'selected':'');?> ><?=$row['name'];?></option>
                         <?php
						 
						 }
						 
						 ?>
                         </select>
                         </td>
			         </tr>  
                     
                      <?php
					 
					 $rows = $db->func_query("SELECT * FROM inv_d_carrier WHERE status=1 ORDER BY name");
					 ?>
			       <tr>
			             <td>Carrier:</td>
			         	 <td><select name="carrier_id" required >
                         <option value="">Please Select</option>
                         <?php
						 foreach($rows as $row)
						 {
						 ?>
                         <option value="<?=$row['id'];?>" <?=($row['id']==$result['carrier_id']?'selected':'');?> ><?=$row['name'];?></option>
                         <?php
						 
						 }
						 
						 ?>
                         </select>
                         </td>
			         </tr>  
                       
			         
			        <tr>
			             <td>Name:</td>
			         	 <td><input type="text" name="name" value="<?php echo @$result['name'];?>" required /></td>
			         </tr>
                     
                     <tr>
			             <td>Status:</td>
			         	 <td>
                         <select name="status">
                         <option value="1" <?php if($result['status']==1) echo 'selected';?>>Enabled</option>
                         <option value="0" <?php if($result['status']==0) echo 'selected';?>>Disabled</option>
                         </select>
                         </td>
			         </tr>
                     
                     
                      
			         
			         
			         
			         
			         
			      
			         
			         
			         
			         
			         
			         <tr>
			             <td colspan="2" align="center">
			             	 <input type="submit" name="add" value="Submit" />
			             </td>
			         </tr>
			    </table>
			 </form>
		 </div>
         
         <div style="margin-top:20px">
         <?php
		 $lists = $db->func_query("SELECT * FROM $table ORDER BY name");
		 
		 ?>	
		   <table class="data" border="1" style="border-collapse:collapse;" width="80%" cellspacing="0" align="center" cellpadding="5">
		   	   <tr style="background:#e5e5e5;">
					<th style="width:50px;">#</th>
					
				<th align="center">Type</th>
                <th align="center">Manufacturer</th>
                <th align="center">Carrier</th>
					<th align="center">Name</th>
                    <th align="center">Status</th>
                    
                    <th align="center">Created By</th>
					<th align="center">Action</th>
					
                    
			   </tr>
				<?php
				$i=1;
                foreach($lists as $list):
				
                ?>
                <tr>
                <td align="center"><?=$i;?></td>
           <td align="center"><?=$db->func_query_first_cell("SELECT name FROM inv_d_type WHERE id='".$list['device_type_id']."'");?></td>
           <td align="center"><?=$db->func_query_first_cell("SELECT name FROM inv_d_manufacturer WHERE id='".$list['manufacturer_id']."'");?></td>
           <td align="center"><?=$db->func_query_first_cell("SELECT name FROM inv_d_carrier WHERE id='".$list['carrier_id']."'");?></td>
                <td align="center"><?=(strlen($list['name'])>100?substr($list['name']).'...':$list['name']);?></td>
                
                
                <td align="center"><?=($list['status']==0?'Disabled':'Enabled');?></td>
                <td align="center"><?=get_username($list['created_by']);?></td>
                <td align="center"><a href="<?=$page;?>?mode=edit&id=<?=$list['id'];?>">Edit</a> | <a href="javascript:void(0);" onclick="if(confirm('Are you sure to delete this entry?')){window.location='<?=$page;?>?mode=delete&id=<?=$list['id'];?>'}">Delete</a></td>
                </tr>
                <?php
				$i++;
                endforeach;
                ?>
		   </table>
		   
		   
      </div>
         
	</body>
</html>			 		 