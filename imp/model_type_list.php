<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';


if(isset($_GET['id']) and $_GET['action'] == 'delete'){
	
	$db->db_exec("delete from inv_model_type where model_type_id = '".(int)$_GET['id']."'");
	header("Location:model_type_list.php");
	exit;
}

if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}

$max_page_links = 10;
$num_rows = 20;
$start = ($page - 1)*$num_rows;

$_query = "SELECT * FROM inv_model_type";

$splitPage  = new splitPageResults($db , $_query , $num_rows , "model_type_list.php",$page);
$rows = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Model Type Listing Listing</title>
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
			 
			 <br clear="all" />
			 
	       <a href="devices_new_settings.php" title="back">&laquo;</a> |   <a href="model_type.php?mode=new" class="button">Add New</a>
	         
	         <br clear="all" /><br clear="all" />
	                      	  
			 <div align="center">
			 	  <table border="1" width="900px;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			 	  	 <tr style="background-color:#e7e7e7;">
			 	  	 	 <td>ID</td>
			 	  	 	 <td>Name</td>
			 	  	 	 <td>Date Added</td>
			 	  	 	 
			 	  	 	 <td>Status</td>
			 	  	 	 
			 	  	 	 <td colspan="2" align="center">Action</td>
			 	  	 </tr>
			 	  	 
			 	  	 <?php foreach($rows as $row):?>
			 	  	 	<tr>
				 	  	 	 <td><?php echo $row['model_type_id']; ?></td>
				 	  	 	 
				 	  	 	 <td><?php echo $row['name']; ?></td>
				 	  	 	 
				 	  	 	 <td><?php echo date('d-m-Y',strtotime($row['date_added'])); ?></td>
				 	  	 	 
				 	  	 	 <td><?php echo ($row['status']==1?'Enabled':'Disabled'); ?></td>
				 	  	 	
				 	  	 	 
				 	  	 	 
				 	  	 	 <td><a href="model_type.php?id=<?php echo $row['model_type_id']; ?>&mode=edit">Edit</a></td>
				 	  	 	 
				 	  	 	 <td><a href="model_type_list.php?id=<?php echo $row['model_type_id']; ?>&action=delete" onclick="if(!confirm('Are you sure, You want to delete this Model Type?')){ return false;}">Delete</a></td>
			 	  	   </tr>
			 	  	 <?php endforeach;?>
			 	  	 
			 	  	 <tr>
	                      <td colspan="2" align="left">
	                         <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
	                      </td>
	                      
	                      <td align="center" colspan="2" >
	                      	  <form method="get">
	                      	  	  Page: <input type="text" name="page" value="<?php echo $page;?>" size="3" maxlength="3" />
	                      	  	  <input type="submit" name="Go" value="Go" />
	                      	  </form>
	                      </td>
	                      
	                      <td align="right" colspan="2">
	                      		<?php echo $splitPage->display_links(10,$parameters);?>
	                      </td>
	                 </tr>
			 	  </table>
		     </div>		 
		</div>		     
    </body>
</html>