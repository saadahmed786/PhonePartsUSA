<?php

require_once("auth_dev.php");
require_once("inc/functions.php");

$rma_number = $db->func_escape_string($_REQUEST['rma_number']);
if(!$rma_number){
	header("Location:$host_path/manage_returns_dev.php");
	exit;
}

if($_GET['action'] == 'remove' && $_GET['image_id']){
	$return_item_id = (int)$_GET['return_item_id'];
	$db->db_exec("delete from inv_return_item_images where return_item_id = '$return_item_id' and id = '".(int)$_GET['image_id']."'");
	header("Location:return_detail_dev.php?rma_number=$rma_number");
}

if(isset($_POST['addcomment'])){
	$addcomment = array();
	$addcomment['comment_date'] = date('Y-m-d H:i:s');
	$addcomment['user_id']   = $_SESSION['user_id'];
	$addcomment['comments']  = $db->func_escape_string($_POST['comments']);
	$addcomment['return_id'] = $_POST['return_id'];
	
	$db->func_array2insert("inv_return_comments",$addcomment);
	
	$_SESSION['message'] = "New comment is added.";
	header("Location:$host_path/return_detail_dev.php?rma_number=$rma_number");
	exit;
}

if(isset($_POST['save']) || isset($_POST['completed']) || isset($_POST['qcdone'])){
	//update sku if changed
	foreach($_POST['product_sku'] as $return_item_id => $product_sku){
		$return_items = array();
		
		if($product_sku != $_POST['new_sku'][$return_item_id]){
			$return_items['sku']   = $_POST['new_sku'][$return_item_id];
			$return_items['title'] = $db->func_query_first_cell("select name from oc_product_description where product_id = (select product_id from oc_product where sku = '".$_POST['new_sku'][$return_item_id]."')");
		}
		
		//$return_items['returnable'] = $_POST['returnable'][$return_item_id];
		$return_items['item_condition'] = $_POST['item_condition'][$return_item_id];
		$return_items['how_to_process'] = $_POST['how_to_process'][$return_item_id];
		$return_items['item_issue'] = $_POST['item_issue'][$return_item_id];
		
		if($_SESSION['return_decision']){
			$return_items['decision'] = $_POST['decision'][$return_item_id];
		}
		
		$return_id = $_POST['return_id'];
		$db->func_array2update("inv_return_items",$return_items,"id = '$return_item_id' AND return_id = '$return_id'");
	}
	
	if(isset($_POST['qcdone'])){
		$db->db_exec("update inv_returns SET rma_status = 'In QC' , date_qc = '".date('Y-m-d H:i:s')."' where rma_number = '$rma_number'");
		$_SESSION['message'] = "Rma verified from QC";
	}
	elseif(isset($_POST['completed'])){
		$db->db_exec("update inv_returns SET rma_status = 'Completed' , date_completed = '".date('Y-m-d H:i:s')."' where rma_number = '$rma_number'");
		$_SESSION['message'] = "Rma status is completed.";
	}
	else{
		$_SESSION['message'] = "Rma changes are saved.";
	}
	
	header("Location:$host_path/return_detail_dev.php?rma_number=$rma_number");
	exit;
}

$rma_return = $db->func_query_first("select r.* , o.order_date, od.first_name,od.last_name,od.address1,od.address2,
						od.city,od.state,od.zip from inv_returns r 
					    inner join inv_orders o on (r.order_id = o.order_id) 
					    inner join inv_orders_details od on (r.order_id = od.order_id)
					    where rma_number  = '$rma_number'");
						//print_r($rma_return);exit;
if(!$rma_return){
	
	header("Location:$host_path/manage_returns_dev.php");
	exit;
}

$return_items = $db->func_query("select * from inv_return_items where return_id = '".$rma_return['id']."'");
$comments = $db->func_query("select * from inv_return_comments c left join inv_users u on (c.user_id = u.id) where return_id = '".$rma_return['id']."'");

$item_conditions = array(array('id'=>'Good For Stock','value'=>'Good For Stock'), array('id'=>'Item Issue','value'=>'Item Issue'), array('id'=>'Customer Damage','value'=>'Customer Damage'));
$returnable_values = array(array('id'=>1,'value'=>'Yes'), array('id'=>0,'value'=>'No'));

$decisions = array(array('id'=>'Issue Credit','value'=>'Issue Credit'),
				   array('id'=>'Issue Refund','value'=>'Issue Refund'),
				   array('id'=>'Issue Replacement','value'=>'Issue Replacement')
		     );

$item_issues = $db->func_query("select * from inv_item_issues");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>Return Inventory Page</title>
	 <script type="text/javascript" src="js/jquery.min.js"></script>
	 <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	 <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	 
	 <script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox({ width: '450px' , autoCenter : true , autoSize : true });
			$('.fancybox2').fancybox({ width: '680px' , autoCenter : true , autoSize : true });
		});
	 </script>	
	 <script type="text/javascript">
	     function unlockBox(condition , order_product_id){
		    if(condition == 'Item Issue'){
			    jQuery("#item_issue_"+order_product_id).show();
			}
		    else{
		    	jQuery("#item_issue_"+order_product_id).hide();
		    }
		 }
	 </script>
  </head>
  <body>
  	  <div class="div-fixed">
		<div align="center"> 
		   <?php include_once 'inc/header.php';?>
		</div>
		
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<div align="center" style="width:80%;margin:0 auto;">
		   <form method="post" action="">
			    <h2>RMA Return Details</h2>
			  
			     <br /><br />
			  	 <table border="1" cellpadding="10" cellspacing="0" width="1000">
					<tr>
					   <td>
						  <table cellpadding="5">
						  		<caption>Shipping</caption>
							    <tr>	
							    	<td><b>Full Name:</b></td>
							    	<td><?php echo $rma_return['first_name']. " ". $rma_return['last_name'];?></td>
							    	<td></td>
							    </tr>
							    
							    <tr>	
							    	<td><b>Address 1:</b></td>
							    	<td><?php echo $rma_return['address1']?></td>
							    	<td></td>
							    </tr>
							    
							    <tr>	
							    	<td><b>Address 2:</b></td>
							    	<td><?php echo $rma_return['address2'];?></td>
							    	<td></td>
							    </tr>
							    
							    <tr>	
							    	<td>City: <?php echo $rma_return['city'];?></td>
							    	<td>State: <?php echo $rma_return['state'];?></td>
							    	<td>Zip: <?php echo $rma_return['zip'];?></td>
							    </tr>
						  </table>	    
				    	</td>
				    	
				    	<td>
						  <table cellpadding="5">
						  		<caption>Billing</caption>
							    <tr>	
							    	<td><b>Full Name:</b></td>
							    	<td><?php echo $rma_return['first_name']. " ". $rma_return['last_name'];?></td>
							    	<td></td>
							    </tr>
							    
							    <tr>	
							    	<td><b>Address 1:</b></td>
							    	<td><?php echo $rma_return['address1']?></td>
							    	<td></td>
							    </tr>
							    
							    <tr>	
							    	<td><b>Address 2:</b></td>
							    	<td><?php echo $rma_return['address2'];?></td>
							    	<td></td>
							    </tr>
							    
							    <tr>	
							    	<td>City: <?php echo $rma_return['city'];?></td>
							    	<td>State: <?php echo $rma_return['state'];?></td>
							    	<td>Zip: <?php echo $rma_return['zip'];?></td>
							    </tr>
						  </table>	    
				    	</td>
				    	
				    	<td>
				    	   <table cellpadding="5">
				    	       <tr>
									<td><b><?php echo $rma_return['order_id'];?></b></td>
									<td>|</td>
									<td><b><?php echo $rma_return['rma_number'];?></b></td>	    	       
				    	       </tr>
				    	       
				    	       <tr>
									<td><b><?php echo $rma_return['order_date'];?></b></td>
									<td>|</td>
									<td><b><?php echo $rma_return['date_added'];?></b></td>	    	       
				    	       </tr>
				    	       
				    	       <tr>
									<td><b>Status:</b></td>
									<td></td>	  
									<td><?php echo $rma_return['rma_status'];?></td>	    
				    	       </tr>	
				    	   </table>
				    	</td>
				     </tr> 	
				</table>
				
				<br /><br />
				<table border="1" cellpadding="10" cellspacing="0" width="1000">
					 <tr>
					      <th>SKU</th>
					      <th>Title</th>
					      <th>QTY</th>
					      <th>Return Reason</th>
					      
					      <?php if($_SESSION['manage_returns']):?>
					      		<th>How to Process</th>
					      		<th>Condition</th>
					      <?php endif;?>		
					      
					      <?php if($_SESSION['return_decision'] == '1' && ($rma_return['rma_status'] == 'In QC' || $rma_return['rma_status'] == 'Completed')):?>
					      		<th>Decision</th>
					      <?php endif;?>	
					      
					      <th>Images</th>	
					 </tr>
					 
					 <?php foreach($return_items as $return_item):?>
					 	<?php 
					 		$images = $db->func_query("select * from inv_return_item_images where return_item_id = '".$return_item['id']."'");
					 	?>
					 	<tr>
					      <td width="70">
					      	  <input type="hidden" name="product_sku[<?php echo $return_item['id'];?>]" value="<?php echo $return_item['sku']?>" />
					      	  
					      	  <input type="text" name="new_sku[<?php echo $return_item['id'];?>]" value="<?php echo $return_item['sku'];?>" />
					      </td>
					      
					      <td width="150"><?php echo $return_item['title'];?></td>
					      
					      <td><?php echo $return_item['quantity'];?></td>
					      
					      <td width="150"><?php echo $return_item['return_code'];?></td>
					      
					      <?php if($_SESSION['manage_returns']):?>
						      <td>
						      	  <input type="text" name="how_to_process[<?php echo $return_item['id'];?>]" value="<?php echo $return_item['how_to_process']?>" />
						      </td>
						      
						      <td>
						      	  <select name="item_condition[<?php echo $return_item['id'];?>]" required="true" onchange="unlockBox(this.value,<?php echo $return_item['id'];?>)">
						      	  	  <option value="">Select One</option>
						      	  	  
						      	  	  <?php foreach($item_conditions as $item_condition):?>
						      	  	  		<option value="<?php echo $item_condition['id']?>" <?php if($item_condition['id'] == $return_item['item_condition']):?> selected="selected" <?php endif;?>>
						      	  	  			<?php echo $item_condition['value']?>
						      	  	  		</option>
						      	  	  <?php endforeach;?>
						      	  </select>
						      	  
						      	  <br /><br />
						      	  <div id="item_issue_<?php echo $return_item['id'];?>" style="display:none;">
						      	  	 <select name="item_issue[<?php echo $return_item['id'];?>]" style="width:135px;">
						      	  	     <option value="">Select One</option>
						      	  	  
							      	  	  <?php foreach($item_issues as $item_issue):?>
							      	  	  		<option value="<?php echo $item_issue['id']?>" <?php if($item_issue['id'] == $item_issue['item_issue']):?> selected="selected" <?php endif;?>>
							      	  	  			<?php echo $item_issue['name']?>
							      	  	  		</option>
							      	  	  <?php endforeach;?>
						      	  	 </select>
						      	  </div>
						      </td>
						      
						      <?php if($_SESSION['return_decision'] == '1' && ($rma_return['rma_status'] == 'In QC' || $rma_return['rma_status'] == 'Completed')):?>
						      	<td>
						      		 <?php if($return_item['returnable'] == 1):?>
						      		 		<?php echo createField("decision[".$return_item['id']."]", "decision","select",$return_item['decision'],$decisions);?>
						      		 	
						      		 <?php else:?>
						      		 		<?php echo createField("decision[".$return_item['id']."]", "decision","select",$return_item['decision'],array(array('id'=>'Denied','value'=>'Denied')));?>
						      		 	
						      		 <?php endif;?>
						      	</td>
						      <?php endif;?>
						      
						      <td>
						      	  <a class="fancybox2 fancybox.iframe" href="popupfiles/return_item_image.php?return_item_id=<?php echo $return_item['id']; ?>">Upload</a>
						      	  
						      	  <br /><br />
						      	  <?php if($images):?>
										<table align="left">
											<tr>
												<?php foreach($images as $image):?>
													<td>
														 <a href="<?php echo str_ireplace("../", "", $image['image_path']);?>" class="fancybox2 fancybox.iframe">
														 	<img src="<?php echo str_ireplace("../", "", $image['thumb_path']);?>" width="25" height="25" />
														 </a>	
														 
														 <a onclick="if(!confirm('Are you sure?')){ return false; }" href="return_detail_dev.php?rma_number=<?php echo $rma_number?>&action=remove&image_id=<?php echo $image['id']?>&return_item_id=<?php echo $return_item['id']?>">X</a>
													</td>
												<?php endforeach;?>
											</tr>
										</table>
								  <?php endif;?>
						      </td>
						  <?php endif;?>     
					   </tr>
					 <?php endforeach;?>
				</table>
				
				<br /><br />
				<input type="hidden" name="order_id" value="<?php echo $rma_return['order_id']?>" />
				<input type="hidden" name="return_id" value="<?php echo $rma_return['id']?>" />
				<input type="hidden" name="return_number" value="<?php echo $rma_return['return_number']?>" />
				
				<?php if($rma_return['rma_status'] != 'Completed'):?>
						<input type="submit" name="save" value="Save" class="button" />
				<?php endif;?>
				
				<?php if($_SESSION['manage_returns'] && $rma_return['rma_status'] != 'In QC' && $rma_return['rma_status'] != 'Completed'):?>		
						<input type="submit" name="qcdone" value="Complete QC" class="button" />
				<?php endif;?>		
				
				<?php if($_SESSION['return_decision'] && $rma_return['rma_status'] == 'In QC'):?>		
						<input type="submit" name="completed" value="Complete Return" class="button" />
				<?php endif;?>		
		  </form> 	 
		  
		  <br /><br />
		  
		  <form method="post" action="">
		  	  <table border="1" cellpadding="10">
		  		  <tr>
		  			  <td>
		  			  	  <textarea rows="5" cols="50" name="comments" required></textarea>
		  			  </td>
		  		  </tr>
		  		  <tr>
		  		  	 <td align="center">
						 <input type="submit" class="button" name="addcomment" value="Add Comment" />	  		  	 
		  		  	 </td>
		  		  </tr>	
		  	  </table>
		  	  <input type="hidden" name="return_id" value="<?php echo $rma_return['id']?>" />
		  	  <input type="hidden" name="return_number" value="<?php echo $rma_return['return_number']?>" />
		  </form>
		  
		  <h2>Comment History</h2>
		  <table cellpadding="10" border="1" width="600">
		  	  <tr>
		  	  	  <th>Date</th>
		  	  	  <th>User</th>
		  	  	  <th>Comment</th>
		  	  </tr>
			  <?php foreach($comments as $comment):?>
			  		<tr>
			  	  	  <td><?php echo $comment['comment_date'];?></td>
			  	  	  <td><?php echo ($comment['user_id']) ? $comment['name'] : 'admin';?></td>
			  	  	  <td><?php echo $comment['comments'];?></td>
			  	    </tr>
			  <?php endforeach;?>
		  </table>		
		  <br /><br />  
	  </div>
   </div>		
 </body>
</html>