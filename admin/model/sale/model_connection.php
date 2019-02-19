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
	$row = $db->func_query_first("select * from inv_model_connection where model_connection_id = '$id'");
}

if($_POST['add']){
	unset($_POST['add']);
	
	
	
		$array = array();
		$array = $_POST;
		$array['date_added'] = date('Y-m-d h:i:s');
		
		
		
		if($id){
			$db->func_array2update("inv_model_connection",$array,"model_connection_id= '$id'");
		}
		else{
			$db->func_array2insert("inv_model_connection",$array);
		}
		
		header("Location:usb_conn_list.php");
		exit;
	
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Add/Edit Model Connection</title>
	</head>
	<body>
		<div align="center">
			<div align="center"> 
			   <?php 
			   if(!isset($_GET['window']))
			   {
			   include_once 'inc/header.php';
			   }
			   ?>
			</div>
			
			 <?php if($_SESSION['message']):?>
				<div align="center"><br />
					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
				</div>
			 <?php endif;?>
			 
			 <form action="" method="post">
			 	<h2>Add Model Connection</h2>
			    <table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			         <tr>
			             <td>Name</td>
			         	 <td><input type="text" name="name" value="<?php echo @$row['name'];?>" required /></td>
			         </tr>
			         
			         
			         
			         
			         
			         <tr>
			             <td>Active</td>
			         	 <td><input type="checkbox" name="status" value="1" <?php if($row['status']):?> checked="checked" <?php endif;?> /></td>
			         </tr>
			         
			         
			         
			         <tr>
			             <td colspan="2">
			             	 <input type="submit" name="add" value="Submit" />
			             </td>
			         </tr>
			    </table>
			 </form>
		 </div>
	</body>
</html>			 		 