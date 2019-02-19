<?php

require_once("auth.php");
require_once("inc/functions.php");
page_permission("complaint_category");
$mode = $_GET['mode'];
if($mode == 'edit'){
	$issue_category_id = (int)$_GET['id'];
	$issue_result = $db->func_query_first("select * from inv_issues_category where issue_category_id = '$issue_category_id'");
}
if($mode=='delete')
{
	$issue_category_id = (int)$_GET['id'];
	$db->db_exec("DELETE FROM inv_issues_category WHERE issue_category_id='".$issue_category_id."'");	
	$_SESSION['message'] = "Record Deleted";
	header("Location: issues_complaint_category.php");
	exit;
}

if($_POST['add']){
	unset($_POST['add']);
	
	$array = array();
	$array = $_POST;
	$array['created_by'] = $_SESSION['user_id'];
	
	if($issue_category_id){
		$array['date_modified'] = date('Y-m-d H:i:s');
		$db->func_array2update("inv_issues_category",$array,"issue_category_id = '$issue_category_id'");
	}
	else{
		$array['date_added'] = date('Y-m-d H:i:s');
		$issue_category_id = $db->func_array2insert("inv_issues_category",$array);
	}

	$log = 'Complaint Category '. $_POST['name'] .' was '. (($mode == 'edit')? 'updated': 'Added');
		actionLog($log);
	
	header("Location:issues_complaint_category.php");
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Add Issue Category</title>
		<script type="text/javascript" src="js/jquery.min.js"></script>
		
	
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
			 
			 <form action="" method="post" enctype="multipart/form-data">
			 	<h2>Add Issue Category</h2>
			    <table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			         
			         
			         
			        <tr>
			             <td>Category:</td>
			         	 <td><input type="text" name="name" value="<?php echo @$issue_result['name'];?>" required /></td>
			         </tr>
                     
                      
			         
			         
			         
			         
			         
			         <tr>
			             <td>Description</td>
			         	 <td>
			         	 	 <textarea rows="3" cols="40" name="description"><?php echo @$issue_result['description']?></textarea>
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
		 $category_list = $db->func_query("SELECT * FROM inv_issues_category ORDER BY name");
		 
		 ?>	
		   <table class="data" border="1" style="border-collapse:collapse;" width="80%" cellspacing="0" align="center" cellpadding="5">
		   	   <tr style="background:#e5e5e5;">
					<th style="width:50px;">#</th>
					<th align="center">Category</th>
					<th align="center">Description</th>
					<th align="center">Date Added</th>
                    <th align="center">Created By</th>
					<th align="center">Action</th>
					
                    
			   </tr>
				<?php
				$i=1;
                foreach($category_list as $list):
				
                ?>
                <tr>
                <td align="center"><?=$i;?></td>
                <td align="center"><?=$list['name'];?></td>
                <td align="center"><?=(strlen($list['description'])>100?substr($list['description']).'...':$list['description']);?></td>
                <td align="center"><?=americanDate($list['date_added']);?></td>
                <td align="center"><?=get_username($list['created_by']);?></td>
                <td align="center"><a href="issues_complaint_category.php?mode=edit&id=<?=$list['issue_category_id'];?>">Edit</a> | <a href="javascript:void(0);" onclick="if(confirm('Are you sure to delete this entry?')){window.location='issues_complaint_category.php?mode=delete&id=<?=$list['issue_category_id'];?>'}">Delete</a></td>
                </tr>
                <?php
				$i++;
                endforeach;
                ?>
		   </table>
		   
		   
      </div>
         
	</body>
</html>			 		 