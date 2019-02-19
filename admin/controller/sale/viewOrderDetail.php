<?php
include_once 'auth.php';
unset($_SESSION['paid_order']);
if(isset($_POST['update'])){
	$orderID  = $db->func_escape_string($_GET['order']);
	$first_name = $db->func_escape_string($_POST['first_name']);
	$last_name  = $db->func_escape_string($_POST['last_name']);
	$order_status = $db->func_escape_string($_POST['order_status']);
	$po_payment_source = $db->func_escape_string($_POST['po_payment_source']);
	$po_payment_source_detail = $db->func_escape_string($_POST['po_payment_source_detail']);
	$po_payment_source_amount = (float)$db->func_escape_string($_POST['po_payment_source_amount']);
	
	$db->db_exec("update inv_orders_details SET first_name = '$first_name' , last_name = '$last_name' where order_id = '$orderID'");
	
	$db->db_exec("update inv_orders SET order_status='".$order_status."',po_payment_source='$po_payment_source',po_payment_source_detail='$po_payment_source_detail',po_payment_source_amount='$po_payment_source_amount' WHERE order_id='".$orderID."'");
	header("Location:viewOrderDetail.php?order=$orderID");
	exit;
}

if($_GET['action'] == 'delete' && (int)$_GET['fileid']){
	$fileid = (int)$_GET['fileid'];
	$orderID  = $db->func_escape_string($_GET['order']);
	
	$db->db_exec("Delete from inv_order_docs where id = '$fileid' and order_id = '$orderID'");
	
	header("Location:viewOrderDetail.php?order=$orderID");
	exit;
}

//add comments
if(isset($_POST['addcomment'])){
	$orderID  = $db->func_escape_string($_GET['order']);
	
	$addcomment = array();
	$addcomment['date_added'] = date('Y-m-d H:i:s');
	$addcomment['user_id']   = $_SESSION['user_id'];
	$addcomment['comment']   = $db->func_escape_string($_POST['comment']);
	$addcomment['order_id']  = $orderID;

	$order_history_id = $db->func_array2insert("oc_order_history",$addcomment);
	
	$order_mod_logs = array();
	$order_mod_logs['order_history_id'] = $order_history_id;
	$order_mod_logs['order_id'] = $orderID;
	$order_mod_logs['user_id']  = $_SESSION['user_id'];
	$order_mod_logs['date_modified']  = date('Y-m-d H:i:s');
	
	$db->func_array2insert("oc_order_mod_logs",$order_mod_logs);

	$_SESSION['message'] = "New comment is added.";
	header("Location:$host_path/viewOrderDetail.php?order=$orderID");
	exit;
}

//upload return item item images
if($_FILES['order_docs']['tmp_name']){
	$imageCount = 0;
	$orderID    = $db->func_escape_string($_GET['order']);
	
	$uniqid = uniqid();
	$name   = explode(".",$_FILES['order_docs']['name']);
	$ext    = end($name);
	
	$destination = $path."files/".$uniqid.".$ext";
	$file = $_FILES['order_docs']['tmp_name'];
	
	if(move_uploaded_file($file, $destination)){
		$orderDoc = array();
		$orderDoc['attachment_path'] = "files/".basename($destination);
		$orderDoc['type'] = $_FILES['order_docs']['type'];
		$orderDoc['size'] = $_FILES['order_docs']['size'];
		$orderDoc['description'] = $_POST['description'];
		$orderDoc['date_added']  = date('Y-m-d H:i:s');
		$orderDoc['order_id']    = $orderID;
			
		$db->func_array2insert("inv_order_docs",$orderDoc);
		$imageCount++;
	}

	if($imageCount > 0){
		$_SESSION['message'] = "attachments are added successfully.";
	}
	else{
		$_SESSION['message'] = "attachments are not added.";
	}
	header("Location:$host_path/viewOrderDetail.php?order=$orderID");
	exit;
}

if($_GET['order']){
	$orderID  = $db->func_escape_string($_GET['order']);
	
	$order = $db->func_query_first("select inv_orders.* , inv_orders_details.* from  inv_orders left join inv_orders_details on (inv_orders_details.order_id = inv_orders.order_id) where inv_orders.order_id = '$orderID' ");
	$order_items = $db->func_query("Select * from inv_orders_items where order_id = '$orderID' ");
	
	if(!$order){
	    $order = $db->func_query_first("select inv_return_orders.* , inv_orders_details.* from  inv_return_orders inner join inv_orders_details on (inv_orders_details.order_id = inv_return_orders.order_id) where inv_orders_details.order_id = '$orderID' ");
        $order_items = $db->func_query("Select * from inv_orders_items where order_id = '$orderID' ");
	}
	
	$comments = $db->func_query("select oh.* , u.name from oc_order_history oh left join oc_order_mod_logs om on (om.order_history_id = oh.order_history_id)
								 left join inv_users u on (u.id = om.user_id)  where oh.order_id = '$orderID'");
	
	$attachments = $db->func_query("select * from inv_order_docs where order_id = '$orderID'");
	
	$order_fraud = $db->func_query_first("select * from oc_order_fraud where order_id = '$orderID'");
}
else{
	exit;
}
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link href="include/calendar.css" rel="stylesheet" type="text/css" />
		<link href="include/calendar-blue.css" rel="stylesheet" type="text/css" />
		<script  type="text/javascript" src="include/calendar.js"></script>
		<script  type="text/javascript" src="include/calendar-en.js"></script>
		<script  type="text/javascript" src="include/calhelper.js"></script>
        <script type="text/javascript" src="js/jquery.min.js"></script>
         <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	 <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		<title>Order Detail</title>
        
        <script>
		
		 $(document).ready(function(e) {
            $('.fancybox3').fancybox({ width: '90%' , autoCenter : true , autoSize : true });
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
	
	<h2 align="center"> Order Details - Customer's Detail </h2>
	
	<div align="center">
		<?php if($order) :?>
        <?php
		if($order['store_type']=='po_business'){
		$is_po = true;
		}
		else
		{
			$is_po = false;	
		}
		?>
       
			<form method="post" action="" id="xfrm">
					<table cellpadding="5" style="border:1px solid #585858;" width="70%" border="1">
						 <tr>
						 	<th>Order ID : </th>
						 	<td> <?= $order['order_id']; ?> </td>
						 	
						 	<th>Order Date : </th>
						 	<td> <?=date('d-M-Y H:i:s' , strtotime($order['order_date'])); ?> </td>
						 </tr>
			             
			             <tr>
			                <th> Order Price</th>
			                <td> $<?=$order['order_price'] ?> </td>
			                
			                <th>Store Type </th>
			                <td> <?=$order['store_type'] ?> </td>
			             </tr>
			             
			             <tr>
			                <th>Sub Store Type </th>
			                <td> <?=$order['sub_store_type'] ?> </td>
			                
			                <th>Order Status</th>
			                <td> <span id="order_status_span"><?=$order['order_status'] ?></span> <?php if($is_po and strtolower($order['order_status']=='estimate')){
								
								?>
                                <input type="button" class="button"  value="Confirm Order" onclick="changeOrderStatus('Unshipped',this)" />
                                 <script>
							 function changeOrderStatus(status,obj)
							 {
								 if(!confirm('Are you sure?'))
								 {
									return false; 
								 }
								 else
								 {
									 $('#order_status_span').html('Unshipped');
									 $('#order_status').val('Unshipped');
									 $(obj).hide();
									 $('input[name=update]').click();
								 }
							 }
							 
							 </script>
                                <?php
								}  ?>
                                
                                <input type="hidden" name="order_status" id="order_status" value="<?php echo $order['order_status'];?>">
                                 </td>
			             </tr>
			             
			             <tr>
			                <th>Fishbowl Uploaded</th>
			                <td> <?=$order['fishbowl_uploaded'] ?> </td>
			             
			                <th>Customer Email </th>
			                <td> <?=$order['email'] ?> </td>
			             </tr>
			             
			             <tr>
			                <th>Payment Method : </th>
			                <td> <?=$order['payment_method'] ?> </td>
			             
			                <th>Shipping Method : </th>
			                <td> <?=$order['shipping_method'] ?> </td>
			             </tr>
			             
			             <tr>
			                <th>Shipping Cost : </th>
			                <td> $<?=$order['shipping_cost'] ?> </td>
			                
			                <th>Phone : </th>
						    <td> <?=$order['phone_number'] ?> </td>
			             </tr>
                         <?php
						 if($is_po):
						 ?>
                         <tr>
                         
                         <th colspan="4" align="center">--- PO DETAILS ---</td>
                         
                         </tr>
                         
                         <tr>
                         <th>Payment Source :</th>
                         <td><span id="po_payment_source_name"><?php echo ucfirst($order['po_payment_source']);?></span>
                         <th>Details :</th>
                         <td><span id="po_payment_source_detail_name"><?php echo $order['po_payment_source_detail'];?></span> (<span id="po_payment_source_amount_name"><?php  echo '$'.number_format($order['po_payment_source_amount'],2).''; ?></span>)</td>
                         
                         </tr>
                         <tr>
                         <td colspan="4" align="center"><a href="popupfiles/payment_status.php?order_id=<?=$orderID;?>" class="fancybox3 fancybox.iframe button" >Update</a>
                         
                          <input type="hidden" name="po_payment_source" id="po_payment_source" value="<?php echo $order['po_payment_source'];?>" />
                <input type="hidden" name="po_payment_source_detail" id="po_payment_source_detail" value="<?php echo $order['po_payment_source_detail'];?>" />
                <input type="hidden" name="po_payment_source_amount" id="po_payment_source_amount" value="<?php echo $order['po_payment_source_amount'];?>" />
                         </td>
                         </tr>
                         <?php
						 endif;
						 ?>
			      </table>
			      
			      <br />
			      <table cellpadding="5" style="border:1px solid #585858;" width="70%" border="1">
			      	  <tr>
			      	  	  <td width="60%">
			      	  	  	   <h3 align="center">Shipping</h3>
			      	  	  	   <table width="100%" border="0" align="left">
			      	  	  	   		<tr>
						                <th width="30%">Name : </th>
						                <td>
						                	<input type="text" name="first_name" size="15" value="<?php echo $order['first_name'];?>" /> 
						                	<input type="text" name="last_name" size="15" value="<?php echo $order['last_name'];?>" /> 
						                </td>
						             </tr>
						             
						             <tr>
						                <th>Address : </th>
						                <td> <?=$order['address1'] ." ".@$order['address2']?> </td>
						             </tr>
						             
						             <tr>
						                <th>City : </th>
						                <td> <?=$order['city'] ?> </td>
						             </tr>
						             
						              <tr>
						                <th>State : </th>
						                <td> <?=$order['state'] ?> </td>
						             </tr>
						             
						              <tr>
						                <th>Country : </th>
						                <td> <?=$order['country'] ?> </td>
						             </tr>
						             
						              <tr>
						                <th>Zip : </th>
						                <td> <?=$order['zip'] ?> </td>
						             </tr>
			      	  	  	   </table>
			      	  	  </td>
			      	  	  
			      	  	  <td width="40%">
			      	  	  	  <h3 align="center">Billing</h3>
			      	  	  	  <table width="100%" border="0" align="left">
			      	  	  	   		<tr>
						                <th>Name : </th>
						                <td>
						                	<?=$order['first_name'] ." ".@$order['last_name']?>
						                </td>
						             </tr>
						             
						             <tr>
						                <th>Address : </th>
						                <td> <?=$order['bill_address1'] ." ".@$order['bill_address2']?> </td>
						             </tr>
						             
						             <tr>
						                <th>City : </th>
						                <td> <?=$order['bill_city'] ?> </td>
						             </tr>
						             
						              <tr>
						                <th>State : </th>
						                <td> <?=$order['bill_state'] ?> </td>
						             </tr>
						             
						              <tr>
						                <th>Country : </th>
						                <td> <?=$order['bill_country'] ?> </td>
						             </tr>
						             
						              <tr>
						                <th>Zip : </th>
						                <td> <?=$order['bill_zip'] ?> </td>
						             </tr>
			      	  	  	   </table>
			      	  	  </td>
			      	  </tr>
					</table>
			        
			        <br />
			        
			        <table align="center" border="1" cellspacing="0" cellpadding="5" width="70%">
			             <tr>
			                  <td>Order Item ID</td>
			                  <td>Product SKU</td>
			                  <td>Product Qty</td>
			                  <td>Percentage Discount</td>
                              <td>Product Price</td>
			             </tr>
			             
			             <?php foreach($order_items as $order_item):?>
			                      <tr>
			                          <td><?php echo $order_item['order_item_id'];?></td>
			                          
			                          <td><?php echo $order_item['product_sku'];?></td>
			                          
			                          <td><?php echo $order_item['product_qty'];?></td>
                                      <td><?php echo $order_item['product_discount'];?>%</td>
			                          
			                          <td><?php echo $order_item['product_price'];?></td>
			                     </tr>
			             
			             <?php endforeach;?>
			        </table>
			        
			        <br />
                    
                     <div  id="aim_div">
                Charge Card <input type="checkbox" onchange="if(this.checked){$('input[name=update]').hide(500);$('#aim_table').fadeIn();}else{$('#aim_table').fadeOut();$('input[name=update]').show(500);}" id="charge_aim" /><br /><br />
                
                
                <?php
				
$months = array();
for ($i = 1; $i <= 12; $i++) {
			$months[] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)), 
				'value' => sprintf('%02d', $i)
			);
		}
		
		$today = getdate();

		$year_expire = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$year_expire[] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) 
			);
		}
				?>
  <table border="1" width="60%" cellpadding="5" cellspacing="0" align="center" id="aim_table" style="display:none">
    <tr>
      <td>Card Owner:</td>
      <td><input type="text" name="cc_owner" value="" /></td>
    </tr>
    <tr>
      <td>Card Number:</td>
      <td><input type="text" name="cc_number" value="" /></td>
    </tr>
    <tr>
      <td>Card Expiry Date:</td>
      <td><select name="cc_expire_date_month">
          <?php foreach ($months as $month) { ?>
          <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
          <?php } ?>
        </select>
        /
        <select name="cc_expire_date_year">
          <?php foreach ($year_expire as $year) { ?>
          <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
          <?php } ?>
        </select></td>
    </tr>
    <tr>
      <td>Card Security Code (CVV2):</td>
      <td><input type="text" name="cc_cvv2" value="" size="3" /></td>
    </tr>
    <tr>
    <td align="center" colspan="2">
    <input type="hidden" type="hidden" name="orders[order_id]" value="<?php echo $order['order_id'];?>">
    
    <input type="hidden" name="orders_details[first_name]" value='<?php echo $order['first_name'];?>'>
    <input type="hidden" name="orders_details[last_name]" value='<?php echo $order['last_name'];?>'>
    <input type="hidden" name="orders[email]" value='<?php echo $order['email'];?>'>
    <input type="hidden" name="orders_details[phone_number]" value='<?php echo $order['phone_number'];?>'>
    <input type="hidden" name="orders_details[address1]" value='<?php echo $order['address1'];?>'>
    <input type="hidden" name="orders_details[city]" value='<?php echo $order['city'];?>'>
    <input type="hidden" name="orders_details[state]" value='<?php echo $order['state'];?>'>
    <input type="hidden" name="orders_details[zip]" value='<?php echo $order['zip'];?>'>
    <input type="hidden" name="total" value='<?php echo $order['order_price'];?>'>
    
    <input type="button" class="button" value="Update" onclick="confirmAim();" id="confirm-btn"  /></td>
    
    </tr>
  </table>
</div>
                    
			        <div align="center">
			        	<input type="submit" name="update" value="Update Order" />
			        </div>	
	        </form>
		<?php else :?>
			<h4> No Order Found</h4>
		<?php endif ;?>
    </div>
    				
	<br /><br />    				
	<div align="center">
		<table width="70%">
			<tr>
				<td width="50%" valign="top">
					  <form method="post" action="">
					  	  <table border="1" cellpadding="10" width="90%">
					  		  <tr>
					  			  <td>
					  			  	  <textarea rows="5" cols="50" name="comment" required></textarea>
					  			  </td>
					  		  </tr>
					  		  <tr>
					  		  	 <td align="center">
									 <input type="submit" class="button" name="addcomment" value="Add Comment" />	  		  	 
					  		  	 </td>
					  		  </tr>	
					  	  </table>
					  	  <input type="hidden" name="order_id" value="<?php echo $orderID?>" />
					  </form>
					  
					  <h2>Comment History</h2>
					  <table border="1" cellpadding="10" width="90%">
					  	  <tr>
					  	  	  <th>Date</th>
					  	  	  <th>User</th>
					  	  	  <th>Comment</th>
					  	  </tr>
						  <?php foreach($comments as $comment):?>
						  		<tr>
						  	  	  <td><?php echo $comment['date_added'];?></td>
						  	  	  <td><?php echo ($comment['user_id']) ? $comment['name'] : 'admin';?></td>
						  	  	  <td>
						  	  	  	  <?php 
							  	  	  	  //parse usps , ups or fedex tracking number and make them as link
							  	  	  	  preg_match("/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", $comment['comment'] , $matches);
							  	  	  	  if($matches){
							  	  	  	  	  if(stristr($comment['comment'], "USPS")){
							  	  	  	  	  	   $comment['comment'] = preg_replace("/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", sprintf($usps_link,$matches[1],$matches[1]), $comment['comment']);
							  	  	  	  	  }
							  	  	  	  	  elseif(stristr($comment['comment'], "UPS")){
							  	  	  	  	  	   $comment['comment'] = preg_replace("/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", sprintf($ups_link,$matches[1],$matches[1]), $comment['comment']);
							  	  	  	  	  }
							  	  	  	  	  else{
							  	  	  	  	  	   $comment['comment'] = preg_replace("/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", sprintf($fedex_link,$matches[1],$matches[1]), $comment['comment']);
							  	  	  	  	  }
							  	  	  	  }
						  	  	  	  ?>
						  	  	  	  <?php echo $comment['comment'];?>
						  	  	  </td>
						  	    </tr>
						  <?php endforeach;?>
					  </table>		
				</td>
				<td width="50%" valign="top">
					<form method="post" action="" enctype="multipart/form-data">
					  	  <table border="1" cellpadding="10" width="100%">
					  		  <tr>
					  			  <td>
					  			  	  <input type="file" name="order_docs" required />
					  			  </td>
					  		  </tr>
					  		  <tr>
					  			  <td>
					  			  	  <textarea rows="2" cols="50" name="description" style="resize:none"></textarea>
					  			  </td>
					  		  </tr>
					  		  <tr>
					  		  	 <td align="center">
									 <input type="submit" class="button" name="upload" value="Upload" />	  		  	 
					  		  	 </td>
					  		  </tr>	
					  	  </table>
					  	  <input type="hidden" name="order_id" value="<?php echo $orderID?>" />
					  </form>
					  
					  <h2>Attachments</h2>
					  <table border="1" cellpadding="10" width="100%">
					  	  <tr>
					  	  	  <th>Date</th>
					  	  	  <th>File</th>
					  	  	  <th>Description</th>
					  	  	  <th>Action</th>
					  	  </tr>
						  <?php foreach($attachments as $attachment):?>
						  		<tr>
						  	  	  <td><?php echo $attachment['date_added'];?></td>
						  	  	  <td><?php echo $attachment['type'];?></td>
						  	  	  <td><?php echo $attachment['description'];?></td>
						  	  	  <td>
						  	  	  	  <a href="<?php echo $host_path ."". $attachment['attachment_path'];?>">download</a>
						  	  	  	  |
						  	  	  	  <a href="viewOrderDetail.php?action=delete&fileid=<?php echo $attachment['id']?>&order=<?php echo $orderID;?>" onclick="if(!confirm('Are you sure, You want to delete this file?')){ return false; }">delete</a>
						  	  	  </td>
						  	    </tr>
						  <?php endforeach;?>
					  </table>		
				</td>			
			</tr>
		</table>
	</div>
	
	<br /><br /> 
	<div align="center"> 
	   <table border="1" style="border-collapse:collapse;" width="70%" cellpadding="10">
	       <tr>
	       	  <th>Country Match</th>
	       	  <th>Distance</th>
	       	  <th>IP City</th>
	       	  <th>IP Region</th>
	       	  <th>ISP</th>
	       	  <th>IP Organization</th>
	       	  <th>IP User Type</th>
	       	  <th>IP Domain</th>
	       	  <th>IP Corporate Proxy</th>
	       	  <th>Anonymous Proxy</th>
		   </tr>
		   
		   <tr>       	  
			  <td><?php echo $order_fraud['country_match']?></td>
		      <td><?php echo $order_fraud['distance']?></td>	 
		      <td><?php echo $order_fraud['ip_city']?></td> 
		      <td><?php echo $order_fraud['ip_region']?></td>
		      <td><?php echo $order_fraud['ip_isp']?></td>
		      <td><?php echo $order_fraud['ip_org']?></td>
		      <td><?php echo $order_fraud['ip_user_type']?></td>
		      <td><?php echo $order_fraud['ip_domain']?></td>
		      <td><?php echo $order_fraud['ip_corporate_proxy']?></td> 
		      <td><?php echo $order_fraud['anonymous_proxy']?></td> 
		   </tr>
	   </table>
	</div>
	
	<div align="center"> 
         <br />
		 <a href="order.php" style="margin-left:20px;"> Back </a> 
     </div>	 
 </body>
</html>
<script>
		
		function confirmAim()
		   {
			   var status = true;
			
			$.ajax({
		url: 'ajax_aim_send.php',
		type: 'post',
		//data: {},
		data:$('#xfrm :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#confirm-btn').attr('disabled', true);
			$('#confirm-btn').val('Processing...');
		},
		complete: function() {
			$('#confirm-btn').attr('disabled', false);
			$('#confirm-btn').val('Update');
		},				
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
				
			}
			
			if (json['success']) {
				alert(json['success']);
				$('input[name=update]').click();
			}
		}
	});   
			   
		   }
		</script>