<?php
require_once("../auth.php");
include_once '../inc/split_page_results.php';

if($_SESSION['login_as'] != 'admin' || !$_SESSION['qc_shipment']){
	$_SESSION['message'] = 'You dont have permission to manage reasons.';
	header("Location:$host_path/home.php");
	exit;
}

if((int)$_GET['id'] && $_GET['action'] == 'delete'){
	$db->db_exec("delete from inv_reasons where id = '".(int)$_GET['id']."'");
	header("Location:$host_path/settings/reject_reasons.php");
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

$_query = "select * from inv_reasons order by id asc";

$splitPage  = new splitPageResults($db , $_query , $num_rows , "reject_reasons.php",$page);
$results = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Reject Reasons</title>
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
			<div align="center"> 
			   <?php include_once '../inc/header.php';?>
			</div>
			
			 <?php if($_SESSION['message']):?>
				<div align="center"><br />
					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
				</div>
			 <?php endif;?>
			 
			 <br clear="all" />
			 
	         <a class="fancybox fancybox.iframe" href="<?php echo "$host_path/settings/"; ?>reject_reason.php?mode=new">Add New</a>
	         
	         <br clear="all" /><br clear="all" />
	                      	  
			 <div align="center">
			 	  <table border="1" width="900px;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			 	  	 <tr style="background-color:#e7e7e7;">
			 	  	 	 <td>ID</td>
			 	  	 	 <td>Name</td>
			 	  	 	 <td colspan="2" align="center">Action</td>
			 	  	 </tr>
			 	  	 
			 	  	 <?php foreach($results as $result):?>
			 	  	 	<tr>
				 	  	 	 <td><?php echo $result['id']; ?></td>
				 	  	 	 
				 	  	 	 <td><?php echo $result['title']; ?></td>
				 	  	 	 
				 	  	 	 <td><a class="fancybox fancybox.iframe" href="<?php echo "$host_path/settings/"; ?>reject_reason.php?id=<?php echo $result['id']; ?>&mode=edit">Edit</a></td>
				 	  	 	 
				 	  	 	 <td><a href="<?php echo "$host_path/settings/"; ?>reject_reasons.php?id=<?php echo $result['id']; ?>&action=delete" onclick="if(!confirm('Are you sure, You want to delete this reason?')){ return false;}">Delete</a></td>
			 	  	   </tr>
			 	  	 <?php endforeach;?>
			 	  	 
			 	  	 <tr>
	                      <td colspan="2" align="left">
	                         <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
	                      </td>
	                      
	                      <td colspan="2" align="right">
	                      		<?php echo $splitPage->display_links(10,$parameters);?>
	                      </td>
	                 </tr>
			 	  </table>
		     </div>		 
		</div>		     
    </body>
</html>