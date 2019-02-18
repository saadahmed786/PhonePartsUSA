<?php

include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';

$box_id = (int)$_GET['box_id'];
if(!$box_id){
	$_SESSION['message'] = "Box is not found.";
	header("Location:manage_returns_boxes.php");
	exit;
}

if($_POST['print']){ 
	$reject_ids = implode(",",$_POST['reject_ids']);
	header("Location:print_shipment.php?ids=$reject_ids");
	exit;
}

//save shipment
if($_POST['save'] || $_POST['Complete']){ 
	$shipment = array();
	$shipment['box_number'] = $db->func_escape_string($_POST['box_number']);
	$shipment['status'] = 'Issued';
	
	$checkExist = $db->func_query_first_cell("select id from inv_return_shipment_boxes where id != '$box_id' and box_number = '".$shipment['box_number']."'");
	if($checkExist){
		$_SESSION['message'] = "This package number is assigned to another shipment.";
		header("Location:returns_boxes_edit.php?box_id=$box_id");
		exit;
	}
	else{
		$db->func_array2update("inv_return_shipment_boxes",$shipment,"id = '$box_id'");
		$_SESSION['message'] = "Shipment is updated";
	}
	
	//now update shipment item reject reason
	$reasons = $_POST['reason'];
	foreach($reasons as $id => $reason){
		$text = implode("<br/>",$reason);
		$text = $db->func_escape_string($text);
		
		$db->db_exec("update inv_return_shipment_box_items SET  reason = '$text' where id = '$id'");
	}
	
	if($_POST['Complete'] && $_SESSION['edit_received_shipment']){
		if(!$shipment['box_number']){
			$_SESSION['message'] = "Box number is required.";
			header("Location:returns_boxes_edit.php?box_id=$box_id");
			exit;
		}
		
		$db->db_exec("update inv_return_shipment_boxes SET status = 'Completed'  where id = '$box_id'");
		$_SESSION['message'] = "Shipment status is Completed";
	}
	
	header("Location:manage_returns_boxes.php");
	exit;
}

$box_detail = $db->func_query_first("select * from inv_return_shipment_boxes where id = '$box_id'");

if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}

if($page < 1){
    $page = 1;
}

$parameters = "box_id=$box_id";

$max_page_links = 10;
$num_rows = 500;
$start = ($page - 1)*$num_rows;

$inv_query  = "select si.* , s.box_number from inv_return_shipment_box_items si inner join inv_return_shipment_boxes s on (si.return_shipment_box_id  = s.id)
			   where s.id = '$box_id' order by s.id";

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "returns_boxes_edit.php",$page);
$products   = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Add / Edit Returns Shipment</title>
        
        <script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		 
		<script type="text/javascript">
			$(document).ready(function() {
				$('.fancybox').fancybox({ width: '400px', height : '200px' , autoCenter : true , autoSize : true });
				$('.fancybox2').fancybox({ width: '500px', height : '500px' , autoCenter : true , autoSize : true });
			});
		</script>	
    </head>
    <body>
        <?php include_once 'inc/header.php';?>

        <?php if(@$_SESSION['message']):?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
                </div>
        <?php endif;?>
        
        <div align="center">
	        <form method="post" action="">
	        	<br />
	        	<div>
	        		Box Number:
	        		<input type="text" name="box_number" value="<?php echo $box_detail['box_number'];?>" required />
	        		
	        		<?php if($box_detail['status'] != 'Completed'):?>
	        				<input type="submit" name="save" value="Save" />
	        		<?php endif;?>		
	        		<br /><br />
	        	</div>
	        	
	        	<a class="fancybox fancybox.iframe" href="popupfiles/returns_box_skuadd.php?return_shipment_box_id=<?php echo $box_id?>">Add Item</a>
	        	<div>	
			        <?php if($products):?>
			             <table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
			                <thead>
			                    <tr>
			                    	<th>#</th>
			                    	<th>Date Added</th>
			                        <th>Reject ID</th>
			                        <th>SKU</th>
			                        <th>Source</th>
			                        <th>Order ID</th>
			                        <th>RMA #</th>
			                        <th>Reason</th>
			                        <th>Action</th>
			                   </tr>
			               </thead>
			               <tbody>
			                 <?php $i = $splitPage->display_i_count();
			                       $count = 1; 
			                       $shipment_id = $products[0]['return_shipment_box_id'];
			                       
			           		       foreach($products as $product):
			           		           if($shipment_id != $product['return_shipment_box_id']){
			           		         	  $count = 1; 
			                       		  $shipment_id = $product['return_shipment_box_id'];
			           		           }
			           		         ?>
			           		         <?php $reason = explode("<br/>",$product['reason']); ?>
			           		     	 <?php for($j=0;$j<$product['quantity'];$j++):?>
					                       <tr>
					                       	  <td>
					                       	  	  <input type="checkbox" name="reject_ids[]" value="<?php echo $product['rma_number'] . "-". $product['product_sku']. "-".$count;?>" />
					                       	  </td>
					                       	  
					                       	  <td align="center"><?php echo $product['date_added'];?></td>
					                       	  
					                          <td align="center"><?php echo $product['rma_number'] . "-". $product['product_sku']. "-".$count;?></td>
					                          
					                          <td align="center"><?php echo $product['product_sku'];?></td>
					                          
					                          <td align="center"><?php echo $product['source'];?></td>
					                         
					                          <td align="center">
					                          	  <a href="viewOrderDetail.php?order=<?php echo $product['order_id'];?>"><?php echo $product['order_id'];?></a>
					                          </td>
					                          
					                          <td align="center">
					                          	  <a href="return_detail.php?rma_number=<?php echo $product['rma_number'];?>"><?php echo $product['rma_number'];?></a>
					                          </td>
					                          
					                          <td align="center">
					                          	   <input type="hidden" name="reject_item_id" value="<?php echo $product['rma_number'] . "-". $product['product_sku']. "-".$count;?>" />
					                          	   <input required type="text" name="reason[<?php echo $product['id']?>][<?php echo $j?>]" value="<?php echo $reason[$j]?>" />
					                          </td>
					                          
					                          <td>
					                          	  <a class="fancybox2 fancybox.iframe" href="popupfiles/move_box_item.php?box_id=<?php echo $product['return_shipment_box_id'];?>&sku=<?php echo $product['product_sku']?>&count=<?php echo $count;?>">Move</a>
					                          </td>
					                       </tr>
					               <?php $count++; endfor;?>       
					                
			                  <?php $i++; endforeach; ?>
			                  
			                  <tr>
			                  	  <td colspan="2" align="left">
				                      <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
			                      </td>
			                      
			                      <td colspan="3" align="right">
				                      <?php  echo $splitPage->display_links(10,$parameters); ?>
			                      </td>
			                  </tr>
			             </tbody>   
			         </table>   
			         
			         <div align="center">
			         	<br />
			         	<input type="submit" name="print" value="Print" />
			         	
			         	<?php if($box_detail['status'] != 'Completed' && $_SESSION['edit_received_shipment']):?>
                              <input type="submit" name="save" value="Save" />
                              
                              <button type="submit" name="Complete" value="Complete" onclick="if(!confirm('Are you sure?')){ return false; }">
                              	 	Save And Complete Shipment
                              	</button>
                        <?php endif;?>
			         </div>
			         
			       <?php endif;?>
		       </div>	
		    </form>
		</div>             
   </body>
</html>        