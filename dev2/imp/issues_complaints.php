<?php

require_once("auth.php");
include_once("inc/functions.php");
include_once 'inc/split_page_results.php';
page_permission('complaints');
if((int)$_GET['id'] && $_GET['action'] == 'delete'){
	$db->db_exec("delete from inv_issues_complaints where id = '".(int)$_GET['id']."'");
	$log = 'Complaint '. linkToComplaint($issue_id) .' has been deleted';
	actionLog($log);
	add_issue_history(0,'Issue # '.(int)$_GET['id'].' is deleted');
	header("Location:issues_complaints.php");
	exit;
}

$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);

if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}

if($page < 1){
    $page = 1;
}

$max_page_links = 10;
$num_rows = 20;
$start = ($page - 1)*$num_rows;

$_query = "Select * from inv_issues_complaints order by id";

$splitPage  = new splitPageResults($db , $_query , $num_rows , "issues_complaints.php",$page);
$results = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Customer Issue / Complaint</title>
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
			 
			 <br clear="all" />
			 
	         <a href="issues_complaint_add.php?mode=new">Add New</a>
	         
	         <br clear="all" /><br clear="all" />
	                      	  
			 <div align="center">
			 	  <table border="1" width="90%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			 	  	 <tr style="background-color:#e7e7e7;">
			 	  	 	 <td>Date</td>
			 	  	 	 <td>SKU</td>
			 	  	 	 <td>Item Title</td>
			 	  	 	 <td>Revision Issue</td>
			 	  	 	 <td>Complaint Issue</td>
			 	  	 	 <td>Notes</td>
			 	  	 	 <td>Assigned To</td>
			 	  	 	 <td>Status</td>
                         <td>Priority</td>
                         

			 	  	 	 <td colspan="3" align="center">Action</td>
			 	  	 </tr>
			 	  	 
			 	  	 <?php foreach($results as $result):?>
			 	  	 	<tr>
				 	  	 	 <td><?php echo americanDate($result['date_added']); ?></td>
				 	  	 	 
				 	  	 	 <td><?php echo $result['sku']; ?></td>
				 	  	 	 
				 	  	 	 <td><?php echo $result['item_title']; ?></td>
				 	  	 	 
				 	  	 	 <td><?php echo $result['revision_issue']; ?></td>
				 	  	 	
				 	  	 	 <td><?php echo $result['item_issue']; ?></td>
				 	  	 	 
				 	  	 	 <td><?php echo $result['notes']; ?></td>
				 	  	 	 
				 	  	 	 <td>
				 	  	 	 	<?php $user_ids =  $db->func_query("SELECT user_id FROM inv_issue_assigned WHERE issue_id='".(int)$result['id']."'");		
									 
									 foreach($user_ids as $xu)
									 {
										echo get_username($xu['user_id'])."<br>";	 
										 
									 }
									 
									 
									 ?>
				 	  	 	 </td>
				 	  	 	 
				 	  	 	 <td align="center"><?php 
							 echo get_issue_status_tag($result['status']);
							 
							 ?> </td><td align="center"> <?php
							 
							 switch($result['priority'])
							 {
								case 3:
								$priority = "<span class='tag red-bg'>High</span>";
								break; 
								case 2:
								$priority = "<span class='tag blue-bg'>Medium</span>";
								break; 
								case 1:
								$priority = "<span class='tag orange-bg'>Low</span>";
								break; 
								default:
								$priority = "Not Defined";
								break;
								 
								 
							 }
							echo $priority;
							  ?></td>
				 	  	 	 
				 	  	 	 <td><a href="issues_complaint_add.php?id=<?php echo $result['id']; ?>&mode=edit">Edit</a></td>
				 	  	 	 
				 	  	 	 <td><a href="issues_complaint_view.php?id=<?php echo $result['id']; ?>">View</a></td>
				 	  	 	 
				 	  	 	 <td><a href="issues_complaints.php?id=<?php echo $result['id']; ?>&action=delete" onclick="if(!confirm('Are you sure, You want to delete this issue?')){ return false;}">Delete</a></td>
			 	  	   </tr>
			 	  	 <?php endforeach;?>
			 	  	 
			 	  	 <tr>
	                      <td colspan="5" align="left">
	                         <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
	                      </td>
	                      
	                      <td colspan="5" align="right">
	                      		<?php echo $splitPage->display_links(10,$parameters);?>
	                      </td>
	                 </tr>
			 	  </table>
		     </div>		 
		</div>		     
    </body>
</html>