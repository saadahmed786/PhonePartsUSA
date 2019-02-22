<?php

require_once("auth.php");
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';

$page = (int)$_GET['page'];
if(!$page){
	$page = 1;
}

$where = array();
if($_GET['rma_number']){
	$rma_number = $db->func_escape_string(trim($_GET['rma_number']));
	$where[] = " LCASE(rma_number) = LCASE('$rma_number') ";
	$parameters[] = "rma_number=$rma_number";
}

if($where){
	$where = implode(" AND ",$where);
}
else{
	$where = ' 1 = 1';
}

$_query = "select o.* , u.name as user_name from inv_customer_return_orders o left join inv_users u on (o.user_id = u.id) where $where order by date_added desc";

$splitPage   = new splitPageResults($db , $_query , 25 , "customer_damage_shipments.php",$page ,  $count_query);
$rma_returns = $db->func_query($splitPage->sql_query);

foreach($rma_returns as $index => $rma_return){
	$rma_returns[$index]['items'] = $db->func_query("select * from inv_customer_return_order_items where customer_return_order_id  = '".$rma_return['id']."'"); 
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
	 <title>Customer Damage Shipments</title>
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
		 .red td{ box-shadow:1px 2px 5px #990000;}
	 </style>
  </head>
  <body>
		<div align="center"> 
		   <?php include_once 'inc/header.php';?>
		</div>
		 <h2 align="center">Customer Damage Shipments</h2>
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
    				<th>Order ID</th>
    				<th>Date Created</th>
    				<th>RMA #</th>
    				<th>Items</th>
    				<th>Tracking Number</th>
    				<th>User Name</th>
    				<th>Order Status</th>
    				<th>Shipstation Added</th>
    		   </tr>
			   <?php foreach($rma_returns as $k => $rma_return):?>
			   		<tr>
					   <td style="width:50px;"><?php echo $k+1;?></td>		
					   <td>
			   		   	   	<a href="viewOrderDetail.php?order=<?php echo $rma_return['order_id'];?>"><?php echo $rma_return['order_id'];?></a>
			   		   </td>	   		
			   		   <td><?php echo americanDate($rma_return['date_added']);?></td>
			   		   <td>
			   		   	   	<a href="return_detail.php?rma_number=<?php echo $rma_return['rma_number'];?>"><?php echo $rma_return['rma_number'];?></a>
			   		   </td>
			   		   <td>
			   		   		<?php foreach($rma_return['items'] as $item):?>
			   		   			<?php echo $item['product_sku']. " * ". $item['product_qty']?> <br />
			   		   		<?php endforeach;?>
			   		   </td>
			   		   <td><?php echo $rma_return['tracking_number'];?></td>
			   		   <td><?php echo (!$rma_return['user_name']) ? 'Admin' : $rma_return['user_name'];?></td>
			   		   <td><?php echo $rma_return['order_status'];?></td>
			   		   <td>
			   		        <?php echo ($rma_return['shipstation_added']) ? 'Yes' : 'No';?>
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