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
	$where[] = " LCASE(rma_number) like '%".strtolower($rma_number)."%' ";
	$parameters[] = "rma_number=$rma_number";
}

if($_GET['returnable']){
	$returnable = $db->func_escape_string($_GET['returnable']);
	$where[] = " returnable = '$returnable' ";
	$parameters[] = "returnable=$returnable";
}

if($_GET['condition']){
	$condition = $db->func_escape_string($_GET['condition']);
	$where[] = " item_condition = '$condition' ";
	$parameters[] = "condition=$condition";
}

if($_GET['decision']){
	$decision = $db->func_escape_string($_GET['decision']);
	$where[] = " decision = '$decision' ";
	$parameters[] = "decision=$decision";
}


if($where){
	$where = implode(" AND ",$where);
}
else{
	$where = ' 1 = 1';
}

$_query = "select rt.*,r.* from  inv_return_items rt inner join  inv_returns r on (rt.return_id = r.id)
		  where $where order by date_added desc";
$splitPage   = new splitPageResults($db , $_query , 25 , "returns_history.php",$page ,  $count_query);
$rma_returns = $db->func_query($splitPage->sql_query);

foreach($rma_returns as $i => $rma_return){
	$rma_returns[$i]['order_date'] = $db->func_query_first_cell("select order_date from inv_orders where order_id = '".$rma_return['order_id']."'");
}

if($parameters){
	$parameters = implode("&",$parameters);
}
else{
	$parameters = '';
}

$item_conditions = array(array('id'=>'Good For Stock','value'=>'Good For Stock'), array('id'=>'Damaged','value'=>'Damaged'));
$returnable_values = array(array('id'=>1,'value'=>'Yes'), array('id'=>0,'value'=>'No'));

$decisions = array(array('id'=>'Issue Credit','value'=>'Issue Credit'),
				   array('id'=>'Issue Refund','value'=>'Issue Refund'),
				   array('id'=>'Issue Replacement','value'=>'Issue Replacement')
		     );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>Returns History</title>
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
							Returnable: <?php echo createField("returnable","returnable","select",$_GET['returnable'],$returnable_values);?>				        
				        </td>
				        
				        <td>
							Condition: <?php echo createField("condition","condition","select",$_GET['condition'],$item_conditions);?>				        
				        </td>
				        
				        <td>
							Decision: <?php echo createField("decision","decision","select",$_GET['decision'],$decisions);?>				        
				        </td>
				    </tr>
				 </table>
				 <br />
				 <input type="submit" name="search" value="Search" class="button" />
			</form>
	   </div>			
	   <br />
	
	   <div>	
		   <table class="data" border="1" style="border-collapse:collapse;" width="94%" cellspacing="0" align="center" cellpadding="5">
		   	   <tr style="background:#e5e5e5;">
					<th style="width:50px;">#</th>
					<th>Order Date</th>
					<th>Order ID</th>
					<th>RMA #</th>
					<th>Returned Item</th>
					<th>Returnable</th>
					<th>Condition</th>
				    <th>Decision</th>
			   </tr>
			   <?php foreach($rma_returns as $k => $rma_return):?>
			   		<tr>
					   <td style="width:50px;"><?php echo $k+1;?></td>			   		
			   		   <td><?php echo americanDate($rma_return['order_date']);?></td>
			   		   <td><a href="viewOrderDetail.php?order=<?php echo $rma_return['order_id'];?>"><?php echo $rma_return['order_id'];?></a></td>
			   		   <td>
			   		   	   <a href="return_detail.php?rma_number=<?php echo $rma_return['rma_number'];?>">
			   		   	   	   <?php echo $rma_return['rma_number'];?>
			   		   	   </a>
			   		   </td>
			   		   <td><?php echo $rma_return['sku'];?></td>
			   		   <td><?php echo ($rma_return['returnable']) ? 'Yes' : 'No';?></td> 
			   		   <td><?php echo $rma_return['item_condition'];?></td> 
			   		   <td><?php echo $rma_return['decision'];?></td> 
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