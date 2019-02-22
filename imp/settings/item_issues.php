<?php
require_once("../auth.php");
include_once '../inc/functions.php';
include_once '../inc/split_page_results.php';

if($_SESSION['login_as'] != 'admin' || !$_SESSION['issue_types']){
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
if((int)$_GET['id'] && $_GET['action'] == 'delete'){
	$db->db_exec("delete from inv_reasonlist where id = '".(int)$_GET['id']."'");
	header("Location:$host_path/settings/item_issues.php?popup=$popup");
	exit;
}

if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}

$max_page_links = 10;
if($popup==1)
{$num_rows=500;}
else
{$num_rows = 20;}

$start = ($page - 1)*$num_rows;

$_query = "select * from inv_reasonlist order by id asc";

$splitPage  = new splitPageResults($db , $_query , $num_rows , "item_issues.php",$page);
$results = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Item Issues</title>
		<script type="text/javascript" src="<?php echo $host_path;?>/js/jquery.min.js"></script>
		
		<script type="text/javascript" src="<?php echo $host_path;?>/fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo $host_path;?>/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		<script type="text/javascript">
			$(document).ready(function() {
				$('.fancybox').fancybox({ width: '450px' , height: '200px' , autoCenter : true , autoSize : false });
			});
		 </script>		
	</head>
	<body>
		<div align="center">
			<div align="center" <?php if($popup) echo 'style="display:none"';?>> 
			   <?php include_once '../inc/header.php';?>
			</div>
			
			 <?php if($_SESSION['message']):?>
				<div align="center"><br />
					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
				</div>
			 <?php endif;?>
			 
			 <br clear="all" />
			 
	         <a class="fancybox fancybox.iframe" href="<?php echo "$host_path/settings/"; ?>item_issue.php?mode=new&popup=<?=$popup;?>">Add New</a>
	         
	         <br clear="all" /><br clear="all" />
	                      	  
			 <div align="center">
			 	  <table border="1" width="900px;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			 	  	 <tr style="background-color:#e7e7e7;">
			 	  	 	 <td>ID</td>
			 	  	 	 <td>Name</td>
			 	  	 	  <td>Added</td>
			 	  	 	  <td>Modified</td>
			 	  	 	  
			 	  	 	 <td  align="center">Action</td>
			 	  	 </tr>
			 	  	 
			 	  	 <?php foreach($results as $result):?>
			 	  	 	<tr>
				 	  	 	 <td><?php echo $result['id']; ?></td>
				 	  	 	 
				 	  	 	 <td><?php echo $result['name']; ?></td>
				 	  	 	 
				 	  	 	 <td><?=americanDate($result['date_added']);?> -  <?=get_username($result['user_added']);?></td>
				 	  	 	 <td>
				 	  	 	 <?php
				 	  	 	 if($result['date_modified'])
				 	  	 	 {
				 	  	 	 ?>
				 	  	 	 <?=americanDate($result['date_modified']);?> -  <?=get_username($result['user_modified']);?>
				 	  	 	 <?php
				 	  	 	 }
				 	  	 	 else
				 	  	 	 {
				 	  	 	 echo 'Not Modified';
				 	  	 	 }
				 	  	 	 ?>
				 	  	 	 </td>
				 	  	 	 
				 	  	 	 <td><a class="fancybox fancybox.iframe" href="<?php echo "$host_path/settings/"; ?>item_issue.php?id=<?php echo $result['id']; ?>&mode=edit&popup=<?=$popup;?>">Edit</a> <?php if($_SESSION['login_as']=='admin'){ ?> | <a href="<?php echo "$host_path/settings/"; ?>item_issues.php?id=<?php echo $result['id']; ?>&action=delete&popup=<?=$popup;?>" onclick="if(!confirm('Are you sure, You want to delete this issue?')){ return false;}">Delete</a> <?php } ?></td>
			 	  	   </tr>
			 	  	 <?php endforeach;?>
			 	  	 
			 	  	 <tr>
	                      <td colspan="2" align="left">
	                         <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
	                      </td>
	                      
	                      <td colspan="3" align="right">
	                      		<?php echo $splitPage->display_links(10,$parameters);?>
	                      </td>
	                 </tr>
			 	  </table>
		     </div>		 
		</div>		     
    </body>
</html>