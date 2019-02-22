<?php
require_once("../auth.php");

if($_SESSION['login_as'] != 'admin'){
	$_SESSION['message'] = 'You dont have permission to manage issues.';
	header("Location:$host_path/home.php");
	exit;
}
if($_GET['popup']==1 && isset($_GET['popup']))
{
	$popup = 1;
}
else
{
	$popup = 0;
}
$mode = $_GET['mode'];
if($mode == 'edit'){
	$id = (int)$_GET['id'];
	$result = $db->func_query_first("select * from inv_reasonlist where id = '$id'");
}

if($_POST['add']){
	unset($_POST['add']);
	
	$name = $db->func_escape_string($_POST['name']);
	$isExist = $db->func_query_first("select id from inv_reasonlist where name = '$name'");
	if(!$isExist || $id){
		$input_arr = array();
		$input_arr = $_POST;
		$input_arr['date_modified'] = date('Y-m-d H:i:s');
		$input_arr['user_modified'] = $_SESSION['user_id'];
		if($id){
			$db->func_array2update("inv_reasonlist",$input_arr,"id = '$id'");
		}
		else{
			$input_arr['date_added'] = date('Y-m-d H:i:s');
			$input_arr['user_added'] = $_SESSION['user_id'];
			$db->func_array2insert("inv_reasonlist",$input_arr);
		}
		
		$_SESSION['message'] = "Issue title updated";
		echo "<script>window.close();parent.window.location.reload();</script>";
	}
	else{
		$message = "This issue is already exist";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Add/Edit Issue</title>
	</head>
	<body>
		<div  align="center">
			 <?php if($message):?>
				 <h5 align="center" style="color:red;"><?php echo $message;?></h5>
			 <?php endif;?>
			
			 <form action="" method="post">
			 	<h2>Issue Details</h2>
			    <table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			         <tr>
			             <td>Title</td>
			         	 <td><input type="text" size="40" name="name" value="<?php echo @$result['name'];?>" required /></td>
			         </tr>
			         
			         <tr>
			             <td colspan="2" align="center">
			             	 <input type="submit" name="add" value="Submit" />
			             </td>
			         </tr>
			    </table>
			 </form>
		 </div>
	</body>
</html>			 		 