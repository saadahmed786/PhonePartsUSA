<?php

require_once("auth_dev.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';

if((int)$_GET['return_id'] and $_GET['action'] == 'delete' && $_SESSION['delete_shipment']){
	$return_id = (int)$_GET['return_id'];
	$db->db_exec("delete from inv_returns where id = '$return_id'");
	$db->db_exec("delete from inv_return_comments where return_id = '$return_id'");
	$db->db_exec("delete from inv_return_items where return_id = '$return_id'");
	
	$_SESSION['message'] = "RMA is deleted";
	header("Location:manage_returns.php");
	exit;
}

if((int)$_GET['return_id'] and $_GET['action'] == 'complete'){
	$return_id = (int)$_GET['return_id'];
	$db->db_exec("update inv_returns SET rma_status = 'Completed' , date_completed = '".date('Y-m-d H:i:s')."' where id = '$return_id'");
	
	$_SESSION['message'] = "RMA is completed now.";
	header("Location:manage_returns_dev.php");
	exit;
}

$page = (int)$_GET['page'];
if(!$page){
	$page = 1;
}

$where = array();
if($_GET['rma_number']){
	$rma_number = $db->func_escape_string($_GET['rma_number']);
	$where[] = " rma_number = '$rma_number' ";
	$parameters[] = "rma_number=$rma_number";
}

if($_GET['status']){
	$status = $db->func_escape_string($_GET['status']);
	$where[] = " status = '$status' ";
	$parameters[] = "status=$status";
}

if($where){
	$where = implode(" AND ",$where);
}
else{
	$where = ' 1 = 1';
}

$_query = "select * from inv_returns where $where order by date_added desc";
$count_query = "select count(id) as total from inv_returns where $where";

$splitPage   = new splitPageResults($db , $_query , 25 , "manage_returns_dev.php",$page ,  $count_query);
$rma_returns = $db->func_query($splitPage->sql_query);

foreach($rma_returns as $index => $rma_return){
	$rma_returns[$index]['extra_details'] = $db->func_query("select sku , quantity , price from inv_return_items where return_id = '".$rma_return['id']."'"); 
}

if($parameters){
	$parameters = implode("&",$parameters);
}
else{
	$parameters = '';
}

$RMA_STATUS[]  = array("id"=>"Awaiting","value"=>"Awaiting");
$RMA_STATUS[]  = array("id"=>"Received","value"=>"Received");
$RMA_STATUS[]  = array("id"=>"In QC","value"=>"In QC");
$RMA_STATUS[]  = array("id"=>"Completed","value"=>"Completed");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>Manage rma returns</title>
	 <script type="text/javascript" src="js/jquery.min.js"></script>
	 <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	 <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	 
	 <script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox({ width: '450px' , autoCenter : true , autoSize : true });
			$('.fancybox2').fancybox({ width: '680px' , autoCenter : true , autoSize : true });
		});
	 </script>	
	 <style type="text/css">
	 	.data td,.data th{
	 		 border: 1px solid #e8e8e8;
             text-align:center;
             width: 150px;
         }
         .div-fixed{
			 position:fixed;
			 top:0px;
			 left:8px;
			 background:#fff;
			 width:98.8%; 
		 }
		 .red td{ box-shadow:1px 2px 5px #990000}
	 </style>
  </head>
  <body>
		<div align="center"> 
		   <?php include_once 'inc/header.php';?>
		</div>
		 
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php else:?>
			<br /><br /> 
		<?php endif;?>
		
		<div align="center">
			<form action="" method="get">
				 <table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
				    <tr>
				        <td>
							RMA Number: <?php echo createField("rma_number","rma_number","text",$_GET['rma_number']);?>				        
				        </td>
				        
				        <td>
							Status: <?php echo createField("rma_status","rma_status","select",$_GET['rma_status'],$RMA_STATUS);?>				        
				        </td>
				    </tr>	
				 </table>
				 
				 <br />
				 <input type="submit" name="search" value="Search" class="button" />
			</form>
	   </div>			
	   <br />
	
	  <div>	
		   <table class="data" border="1" style="border-collapse:collapse;" width="94%" cellspacing="0" align="center" cellpadding="3">
		       <tr style="background:#e5e5e5;">
    				<th style="width:50px;">#</th>
    				<th>Date Returned</th>
    				<th>RMA Number</th>
    				<th>Email</th>
    				<th>Order ID</th>
    				<th>SKU * QTY</th>
    				<th>Amount</th>
    			    <th>Status</th>
    			    <th style="width:300px;">Action</th>
    		   </tr>
			   <?php foreach($rma_returns as $k => $rma_return):?>
			   		<tr>
					   <td style="width:50px;"><?php echo $k+1;?></td>			   		
			   		   <td><?php echo $rma_return['date_added'];?></td>
			   		   <td>
			   		   	   <a href="return_detail_dev.php?rma_number=<?php echo $rma_return['rma_number'];?>">
			   		   	   	   <?php echo $rma_return['rma_number'];?>
			   		   	   </a>
			   		   </td>
			   		   <td><?php echo $rma_return['email'];?></td>
			   		   <td><a href="viewOrderDetail.php?order=<?php echo $rma_return['order_id'];?>"><?php echo $rma_return['order_id'];?></a></td>
			   		   <td>
			   		   		<?php $amount = 0; foreach($rma_return['extra_details'] as $item):?>
			   		   			<?php echo $item['sku']. " * ". $item['quantity']?> <br />
			   		   			
			   		   			<?php $amount = $amount + $item['price'];?>
			   		   		<?php endforeach;?>
			   		   </td>
			   		   <td>$<?php echo $amount;?></td>
			   		   <td><?php echo $rma_return['rma_status'];?></td> 
			   		   
			   		   <td style="width:300px;">
			   		   		<?php if($rma_return['rma_status'] != 'Completed' && $_SESSION['return_decision']):?>
			   		   				<a href="return_detail_dev.php?rma_number=<?php echo $rma_return['rma_number'];?>">Edit</a>
			   		   		<?php endif;?>		
			   		   		
			   		   		<?php if($rma_return['rma_status'] == 'Received' && $_SESSION['qc_shipment']):?>
			   		   			| <a href="return_detail_dev.php?rma_number=<?php echo $rma_return['rma_number'];?>">QC</a> 
			   		   		<?php endif;?>	
			   		   		
			   		   		<?php if($_SESSION['return_decision'] && $rma_return['rma_status'] == 'In QC'):?>
			   		   				| <a href="return_detail_dev.php?rma_number=<?php echo $rma_return['rma_number'];?>">Mark Complete</a>
			   		   		<?php endif;?>
			   		   		
			   		   		<?php if($_SESSION['login_as'] == 'admin'):?>
			   		   				| <a onclick="if(!confirm('Are you sure?')){ return false; }" href="manage_returns_dev.php?return_id=<?php echo $rma_return['id'];?>&action=delete">Delete</a>
			   		   		<?php endif;?>		
			   		   </td>
			   		</tr>
			   <?php endforeach;?>
		   </table>
		   
		   <br /><br />
		   <table class="footer" border="0" style="border-collapse:collapse;" width="95%" align="center" cellpadding="3">
				 <tr>
		                 <td colspan="7" align="left">
		                       <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
		                 </td>
		                      
		                 <td colspan="6" align="right">
		                     <?php echo $splitPage->display_links(10,$parameters);?>
		                 </td>
		           </tr>
			</table>
			<br />
      </div>		
  </body>
</html>            			   