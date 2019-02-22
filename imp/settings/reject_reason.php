<?php
require_once("../auth.php");

if($_SESSION['login_as'] != 'admin'){
	$_SESSION['message'] = 'You dont have permission to manage reasons.';
	header("Location:$host_path/home.php");
	exit;
}

$mode = $_GET['mode'];
if($mode == 'edit'){
	$id = (int)$_GET['id'];
	$result = $db->func_query_first("select * from inv_reasons where id = '$id'");
}

if($_POST['add']){
	unset($_POST['add']);
	
	$name = $db->func_escape_string($_POST['title']);
	$isExist = $db->func_query_first("select id from inv_reasons where title = '$name'");
	if(!$isExist || $id){
		$input_arr = array();
		$input_arr = $_POST;
		$input_arr['date_modified'] = date('Y-m-d H:i:s');
		
		if($id){
			$db->func_array2update("inv_reasons",$input_arr,"id = '$id'");
		}
		else{
			$db->func_array2insert("inv_reasons",$input_arr);
		}
		
		$_SESSION['message'] = "Reason title updated";
		echo "<script>window.close();parent.window.location.reload();</script>";
	}
	else{
		$message = "This reason is already exist";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Add/Edit Reason</title>
	</head>
	<body>
		<div  align="center">
			 <?php if($message):?>
				 <h5 align="center" style="color:red;"><?php echo $message;?></h5>
			 <?php endif;?>
			
			 <form action="" method="post">
			 	<h2>Reason Details</h2>
			    <table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			         <tr>
			             <td>Title</td>
			         	 <td><input type="text" size="40" name="title" value="<?php echo @$result['title'];?>" required /></td>
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