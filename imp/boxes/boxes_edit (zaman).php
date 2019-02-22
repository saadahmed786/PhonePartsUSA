<?php

include_once '../auth.php';
include_once '../inc/functions.php';
include_once '../inc/split_page_results.php';

$return = $_GET['return'].".php";

$box_id = (int)$_GET['box_id'];
if(!$box_id){
	$_SESSION['message'] = "Box is not found.";
	header("Location:$host_path/boxes/$return");
	exit;
}

if($_POST['Transfer']){
	if(count($_POST['reject_ids']) > 0){
   	   foreach($_POST['reject_ids'] as $reject_id){
   	   	  $inv_return_shipment_box_items = array();
		  $inv_return_shipment_box_items['return_shipment_box_id'] = $_POST['new_box_id'];
		  $db->func_array2update("inv_return_shipment_box_items",$inv_return_shipment_box_items,"return_item_id = '$reject_id'");
   	   }
   	   
   	   $_SESSION['message'] = "Return Items are moved to another box.";
	   header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=".$_GET['return']);
	   exit;
   }	
   else{
	   $_SESSION['message'] = "Select at least one sku to move to delete.";
	   header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=".$_GET['return']);
	   exit;
   }	
}

if($_POST['print']){ 
	$reject_ids = implode(",",$_POST['reject_ids']);
	header("Location:$host_path/print_shipment.php?ids=$reject_ids");
	exit;
}
elseif($_POST['delete']){
   if(count($_POST['reject_ids']) > 0){
   	   foreach($_POST['reject_ids'] as $reject_id){
   	   	   $db->db_exec ( "delete from inv_return_shipment_box_items where return_item_id = '$reject_id' and return_shipment_box_id = '$box_id' " );
   	   }
   	   
   	   $_SESSION['message'] = "Items deleted successfully.";
	   header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=".$_GET['return']);
	   exit;
   }	
   else{
	   $_SESSION['message'] = "Select at least one sku to move to delete.";
	   header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=".$_GET['return']);
	   exit;
   }	
}
elseif($_POST['MoveRTS']){
   if(count($_POST['reject_ids']) > 0){
   	   foreach($_POST['reject_ids'] as $reject_id){
   	   		moveItemToBox($reject_id , $_POST[$reject_id] , 'RTSBox');
   	   }
   	   
   	   $_SESSION['message'] = "Items moved to RTS.";
	   header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=".$_GET['return']);
	   exit;
   }	
   else{
	   $_SESSION['message'] = "Select at least one sku to move to NTR.";
	   header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=".$_GET['return']);
	   exit;
   }	
}
elseif($_POST['MoveNTR']){
   if(count($_POST['reject_ids']) > 0){
   	   foreach($_POST['reject_ids'] as $reject_id){
   	   	   moveItemToBox($reject_id , $_POST[$reject_id] , 'NTRBox');
   	   }
   }	
   else{
	   $_SESSION['message'] = "Select at least one sku to move to NTR.";
	   header("Location:addedit_rejectedshipment.php?shipment_id=$shipment_id");
	   exit;
   }	
}
//save shipment
elseif($_POST['save'] || $_POST['Complete']){ 
	$shipment = array();
	$shipment['box_number'] = $db->func_escape_string($_POST['box_number']);
	$shipment['status'] = 'Issued';
	
	$checkExist = $db->func_query_first_cell("select id from inv_return_shipment_boxes where id != '$box_id' and box_number = '".$shipment['box_number']."'");
	if($checkExist){
		$_SESSION['message'] = "This package number is assigned to another shipment.";
		header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=$return");
		exit;
	}
	else{
		$db->func_array2update("inv_return_shipment_boxes",$shipment,"id = '$box_id'");
		$_SESSION['message'] = "Box is updated";
	}
	
	//now update shipment item reject reason
	$reject_item_ids = $_POST['reject_item_ids'];
	foreach($reject_item_ids as $id => $reject_id){
		$text = $db->func_escape_string($_POST['reason'][$reject_id]);
		$reject_id = $db->func_escape_string($reject_id);
		
		$db->db_exec("update inv_return_shipment_box_items SET reason = '$text' , return_item_id = '$reject_id' where id = '$id'");
	}
	
	if($_POST['Complete'] && $_SESSION['edit_received_shipment']){
		if(!$shipment['box_number']){
			$_SESSION['message'] = "Box number is required.";
			header("Location:$host_path/boxes/boxes_edit.php?box_id=$box_id&return=$return");
			exit;
		}
		
		$db->db_exec("update inv_return_shipment_boxes SET status = 'Completed'  where id = '$box_id'");
		$_SESSION['message'] = "Box status is Completed";
	}
	
	header("Location:$host_path/boxes/$return");
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
			   where s.id = '$box_id' order by si.id DESC";

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "boxes_edit.php",$page);
$products   = $db->func_query($splitPage->sql_query);

/*foreach($products as $index => $product){
	$_query = "select ((pc.raw_cost + pc.shipping_fee) / pc.ex_rate) from inv_product_costs pc where pc.sku = '".$product['product_sku']."' order by pc.id DESC";
	$products[$index]['item_cost'] = round($db->func_query_first_cell($_query),2);
}*/

$boxes = $db->func_query("select id , box_number from inv_return_shipment_boxes where id != '$box_id' order by box_type");
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Add / Edit Returns Shipment</title>
        
        <script type="text/javascript" src="<?php echo $host_path;?>js/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo $host_path;?>fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo $host_path;?>/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		 
		<script type="text/javascript">
			$(document).ready(function() {
				$('.fancybox').fancybox({ width: '400px', height : '200px' , autoCenter : true , autoSize : true });
				$('.fancybox2').fancybox({ width: '500px', height : '500px' , autoCenter : true , autoSize : true });
			});
		</script>	
    </head>
    <body>
        <?php include_once '../inc/header.php';?>

        <?php if(@$_SESSION['message']):?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
                </div>
        <?php endif;?>
        
        <div align="center">
	        <form method="post" action="">
	        	<br />
	        	<div>
	        		<table>
	        			<tr>
	        				 <td>Box Number:</td>
	        				 <td>
	        				 	 <input type="text" name="box_number" value="<?php echo $box_detail['box_number'];?>" required />
	        				 </td>
	        				 <td>
	        				 	 <?php if($box_detail['status'] != 'Completed'):?>
				        				<input type="submit" name="save" value="Save" />
				        		 <?php endif;?>
	        				 </td>
	        				 <td>&nbsp;</td>
	        				 <td>
	        				     Select Box:
								 <select name="new_box_id" id="new_box_id" style="width:150px;">
							      		<option value="">Select One</option>
							      		<?php foreach($boxes as $box):?>
							      			<option value="<?php echo $box['id']; ?>"><?php echo $box['box_number']; ?></option>
							      		<?php endforeach;?>
							     </select>
	        				 </td>
	        				 <td>
	        				 	<input type="submit" name="Transfer" value="Transfer" onclick="if(!$('#new_box_id').val()){ alert('Please select one BOX.'); return false;}" />
	        				 </td>
	        			</tr>
	        		</table>
	        	</div>
	        	
	        	<div align="center">
		         	<br />
		         	<a class="fancybox fancybox.iframe" href="<?php echo $host_path;?>/popupfiles/returns_box_skuadd.php?return_shipment_box_id=<?php echo $box_id?>">Add Item</a>
		         	
		         	<input type="submit" name="print" value="Print" />
		         	
		         	<?php if($_SESSION['login_as'] == 'admin'):?>
	         			<input type="submit" name="delete" value="Delete" onclick="if(!confirm('Are you sure?')){ return false; }" />
	         		<?php endif;?>
	         		
	         		<?php if($_GET['return'] != 'return_to_stock'):?>
	         			<input type="submit" name="MoveNTR" value="Move to NTR" />
	         		<?php endif;?>		
	         		
	         		<?php if($_GET['return'] == 'not_tested'):?>
	         			<input type="submit" name="MoveRTS" value="Move to RTS" />
	         		<?php endif;?>
		         	
		         	<?php if($box_detail['status'] != 'Completed' && $_SESSION['edit_received_shipment']):?>
                           <button type="submit" name="Complete" value="Complete" onclick="if(!confirm('Are you sure?')){ return false; }">
                              	Save And Close Box
                           </button>
                    <?php endif;?>
                    
                    <br /><br />
		        </div>
	        	
	        	<div>	
			        <?php if($products):?>
			             <table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
			                <thead>
			                    <tr>
			                    	<th>#</th>
			                    	<th>Added</th>
			                        <th>Return ID</th>
			                        <th>SKU</th>
                                    <th>Title</th>
			                        <th>Source</th>
			                        <th>Order ID</th>
			                        <th>RMA #</th>
			                        <?php if($_SESSION['boxes_cost']):?>
			                        	<th>Cost</th>
			                        <?php endif;?>	
			                        <th>Reason</th>
			                        <th>Action</th>
			                   </tr>
			               </thead>
			               <tbody>
			                 <?php $i = $splitPage->display_i_count();
			                       $count = 1; 
			           		       foreach($products as $product):?>
					                       <tr>
					                       	  <td>
					                       	  	  <input type="checkbox" name="reject_ids[]" value="<?php echo $product['return_item_id'];?>" />
					                       	  </td>
					                       	  
					                       	  <td align="center"><?php echo americanDate($product['date_added']);?></td>
					                       	  
					                          <td align="center">
					                          	  <input name="reject_item_ids[<?php echo $product['id']?>]" value="<?php echo $product['return_item_id'];?>" required />
					                          </td>
					                          
					                          <td align="center"><a href="<?php echo $host_path;?>product/<?php echo $product['product_sku'];?>"><?php echo $product['product_sku'];?></a></td>
                                              <td align="center"><?php echo getItemName($product['product_sku']);?></td>
					                          
					                          <td align="center"><?php echo $product['source'];?></td>
					                         
					                          <td align="center">
					                          	  <a href="<?php echo $host_path;?>viewOrderDetail.php?order=<?php echo $product['order_id'];?>"><?php echo $product['order_id'];?></a>
					                          </td>
					                          
					                          <td align="center">
					                          	  <a href="<?php echo $host_path;?>return_detail.php?rma_number=<?php echo $product['rma_number'];?>"><?php echo $product['rma_number'];?></a>
					                          </td>
					                          
					                          <?php if($_SESSION['boxes_cost']):?>
					                         	 <td>
					                         	 	 $<?php echo $product['cost'];?>
					                         	 </td>
					                          <?php endif;?>
					                          
					                          <td align="center">
					                          	   <input type="hidden" value="<?php echo $product['product_sku'];?>" name="<?php echo $product['return_item_id'];?>" />
					                          	   <input  type="text" name="reason[<?php echo $product['return_item_id']?>]" value="<?php echo $product['reason']?>" />
					                          </td>
					                          
					                          <td align="center">
					                          	  <a class="fancybox2 fancybox.iframe" href="<?php echo $host_path;?>popupfiles/move_box_item.php?box_id=<?php echo $product['return_shipment_box_id'];?>&reject_id=<?php echo $product['return_item_id']?>">Transfer</a>
					                          </td>
					                       </tr>
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
			         	
			         	<?php if($_SESSION['login_as'] == 'admin'):?>
		         			<input type="submit" name="delete" value="Delete" onclick="if(!confirm('Are you sure?')){ return false; }" />
		         		<?php endif;?>
		         		
		         		<?php if($_GET['return'] != 'return_to_stock'):?>
		         			<input type="submit" name="MoveNTR" value="Move to NTR" />
		         		<?php endif;?>		
		         		
		         		<?php if($_GET['return'] == 'not_tested'):?>
		         			<input type="submit" name="MoveRTS" value="Move to RTS" />
		         		<?php endif;?>
			         	
			         	<?php if($box_detail['status'] != 'Completed' && $_SESSION['edit_received_shipment']):?>
	                           <button type="submit" name="Complete" value="Complete" onclick="if(!confirm('Are you sure?')){ return false; }">
	                              	Save And Close Box
	                           </button>
	                    <?php endif;?>
			        </div>
			         
			       <?php endif;?>
		       </div>	
		    </form>
		</div>             
   </body>
</html>        