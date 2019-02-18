<?php

require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';


if((int)$_GET['box_id'] and $_GET['action'] == 'delete' && $_SESSION['delete_shipment']){
    $box_id = (int)$_GET['box_id'];
    $db->db_exec("delete from inv_return_shipment_boxes where id = '$box_id'");

    $_SESSION['message'] = "Shipment is deleted";
    header("Location:manage_returns_boxes.php");
    exit;
}

$page = (int)$_GET['page'];
if(!$page){
	$page = 1;
}

$where = array();
if($_GET['box_number']){
	$box_number = $db->func_escape_string($_GET['box_number']);
	$where[] = " box_number = '$box_number' ";
	$parameters[] = "box_number=$box_number";
}

if($where){
	$where = implode(" AND ",$where);
}
else{
	$where = ' 1 = 1';
}

$_query = "select * from inv_return_shipment_boxes where $where order by date_added desc";
$count_query = "select count(id) as total from inv_return_shipment_boxes where $where";

$splitPage   = new splitPageResults($db , $_query , 25 , "manage_returns_boxes.php",$page ,  $count_query);
$rma_returns = $db->func_query($splitPage->sql_query);

foreach($rma_returns as $index => $rma_return){
	$rma_returns[$index]['items'] = $db->func_query("select * from inv_return_shipment_box_items where return_shipment_box_id = '".$rma_return['id']."'"); 
}

if($parameters){
	$parameters = implode("&",$parameters);
}
else{
	$parameters = '';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>Manage rma returns boxes</title>
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
							Box Number: <?php echo createField("box_number","box_number","text",$_GET['box_number']);?>				        
				        </td>
				    </tr>	
				 </table>
				 
				 <br />
				 <input type="submit" name="search" value="Search" class="button" />
			</form>
	   </div>			
	
	   <div>	
		    <table class="data" border="1" style="border-collapse:collapse;" width="94%" cellspacing="0" align="center" cellpadding="3">
		        <tr style="background:#e5e5e5;">
    				<th style="width:50px;">#</th>
    				<th>Date Created</th>
    				<th>Box Number</th>
    				<th>Box Type</th>
    				<th>SKU * QTY</th>
    				<th>Status</th>
    				<th>Action</th>
    		   </tr>
			   <?php foreach($rma_returns as $k => $rma_return):?>
			   		<tr>
					   <td style="width:50px;"><?php echo $k+1;?></td>			   		
			   		   <td><?php echo $rma_return['date_added'];?></td>
			   		   <td>
			   		   	   	<?php echo $rma_return['box_number'];?>
			   		   </td>
			   		   <td><?php echo $rma_return['box_type'];?></td>
			   		   <td>
			   		   		<?php foreach($rma_return['items'] as $item):?>
			   		   			<?php echo $item['product_sku']. " * ". $item['quantity']?> <br />
			   		   		<?php endforeach;?>
			   		   </td>
			   		   <td><?php echo $rma_return['status'];?></td>
			   		   <td>
			   		       <?php if(($rma_return['status'] == 'Issued' && $_SESSION['edit_pending_shipment']) || $_SESSION['login_as'] == 'admin'):?>	
                              		<a href="returns_boxes_edit.php?box_id=<?php echo $rma_return['id']?>">Edit</a>
                           <?php endif;?>	
                          
                           <?php if($_SESSION['delete_shipment']):?>
                          		|
                          		<a href="manage_returns_boxes.php?action=delete&box_id=<?php echo $rma_return['id']?>" onclick="if(!confirm('Are you sure ?')){ return false; }">Delete</a>
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