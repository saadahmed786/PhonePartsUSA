<?php

require_once("config.php");
include_once 'inc/functions.php';
$success = false;

if(isset($_GET['popup']))
{
	$popup_param = 'popup=1';
}
if(isset($_POST['action']) and $_POST['action']=='populateItem')
{
	$json = array();
	$item_id = trim($_POST['item_id']);
	$check_query = $db->func_query_first("SELECT * FROM inv_orders_items WHERE order_item_id='".$db->func_escape_string($item_id)."'");
	if($check_query)
	{
		$json['success'] = 1;
		$json['product_sku'] = $check_query['product_sku']; 
		$json['product_price'] = round($check_query['product_price']/$check_query['product_qty'],2);
		$json['description'] = $db->func_query_first_cell("select name from oc_product_description where product_id = (select product_id from oc_product where sku = '" . $check_query['product_sku'] . "' limit 1)");
	}
	else
	{
		$json['error'] = 'Unable to find the item, please check againl';	
	}
	
	echo json_encode($json);
	exit;
	
}
if(isset($_POST['save']) || isset($_POST['create'])){
	if(count($_POST['return_item']) > 0){ 
		//print "<pre>";
		///print_R($_POST); exit;

		$check_sales_agent = $db->func_query_first_cell("SELECT user_id from inv_customers where trim(email)='".trim($db->func_escape_string($_POST['email']))."'");
		if($check_sales_agent)
		{
			$sales_agent = $check_sales_agent;
		}
		else
		{
			$sales_agent = 0;
		}
		
		$returns = array();
		$returns['email'] = $db->func_escape_string($_POST['email']);
		$returns['order_id']   = $db->func_escape_string($_POST['order_id']);
		$returns['date_added'] = date('Y-m-d H:i:s');
		$returns['store_type'] = $db->func_escape_string($_POST['store_type']);
		$returns['source'] = $db->func_escape_string($_POST['source']);
		$returns['sales_user'] = $sales_agent;
		if($_POST['create']){
			$returns['rma_status'] = 'Awaiting';
		}
		else{
			$returns['rma_status'] = 'Received';
		}
		
		$rma_number = getRMANumber($returns['store_type']);
		$returns['rma_number'] = $rma_number;
		$return_id = $db->func_array2insert("inv_returns",$returns);
	
		//insert rma items
		foreach($_POST['return_item'] as $product_sku => $items){
			foreach($items as $i => $item){
				$return_items = array();
				$return_items['sku']   = $product_sku;
				$return_items['title'] = $_POST['data'][$product_sku][$i]['title'];
				$return_items['price'] = (float)$_POST['data'][$product_sku][$i]['price'];
				$return_items['quantity']    = 1;
				$return_items['return_code'] = $_POST['data'][$product_sku][$i]['return_code'];
				$return_items['return_id'] = $return_id;
				$db->func_array2insert("inv_return_items",$return_items);
			}
		}
		
		$total_new_items = count($_POST['item_id']);
		for($z=0;$z<$total_new_items;$z++)
		{
			if($_POST['item_sku'][$z]!='')
			{
				$return_items = array();
				$return_items['sku']   = $_POST['item_sku'][$z];
				$return_items['title'] =  $_POST['item_title'][$z];
				$return_items['price'] = (float)$_POST['item_price'][$z];
				$return_items['quantity']    = 1;
				$return_items['return_code'] = $_POST['item_return_reason'][$z];
				$return_items['return_id'] = $return_id;
				$db->func_array2insert("inv_return_items",$return_items);
				
			}
			
		}
		
		if($_POST['comments']){
			$addcomment = array();
			$addcomment['comment_date'] = date('Y-m-d H:i:s');
			$addcomment['user_id']   = $_SESSION['user_id'];
			$addcomment['comments']  = $db->func_escape_string($_POST['comments']);
			$addcomment['return_id'] = $return_id;
			
			$db->func_array2insert("inv_return_comments",$addcomment);
		}
			
		$_SESSION['message'] = "RMA # $rma_number is generated successfully.";	
		actionLog("RMA # " . linkToRma($rma_number) . " is generated.");
		
		if(isset($_GET['popup']))
		{
			echo '<h4>RMA # '.$rma_number.' has been generated';
		}
		else
		{
			header("Location:manage_returns.php");
		}
		exit;
	}
	else{
		$_SESSION['message'] = "Please select at least 1 item to return.";	
	}
}

if(isset($_GET['order_id'])){
	$order_id = $db->func_escape_string($_GET['order_id']);
	
	//check rma exist
	$returnExist = $db->func_query_first_cell("select rma_number from inv_returns where order_id = '$order_id'");
	if($returnExist){	
		$_SESSION['message'] .= "<br /> A return is already exist for this orderID. RMA# is $returnExist. <a href='return_detail.php?rma_number=$returnExist'>Click here</a> to view it.";
	}
	
	$checkExist = $db->func_query_first("select * from inv_orders o inner join inv_orders_details od on (o.order_id = od.order_id) where o.order_id = '$order_id'");
	if(!$checkExist){
		$_SESSION['message'] = "Order id and email combination is not exist. Please enter correct details and try again.";
		header("Location:returns.php");
		exit;
	}
	else{
		$success = true;
		$rma_number = getRMANumber($checkExist['store_type']);
		$order_products = $db->func_query("select * from inv_orders_items where order_id = '$order_id'");
	}
}

$reasons = $db->func_query("select title from inv_reasons","title");
if($reasons){
	$reasons = array_keys($reasons);
}
else{
	$reasons = array('R1. Do Not Return','R2. Change of Mind','R3. Non-Functional','R4. Item Not As Described','R5.Received Wrong Item');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	 <title>Return products</title>
	 <script type="text/javascript" src="js/jquery.min.js"></script>
	 
	 <style type="text/css">
	 	table td{text-align:center;}
	 </style>
	 <link href="<?php echo $host_path;?>/css/style.css" rel="stylesheet" type="text/css" />
     
     
     <script>
	 function populateItem(obj,i)
	 {
		 var str  = $(obj).val();
		 $('#item_sku_'+i).val('');
		 $('#title_'+i+' input').val(''); 
						$('#title_'+i+' span').html(''); 
						
					 	$('#item_sku_'+i).val('');
						$('#item_price_'+i).val('');
		 if(str.length < 5){
			 alert('Please provide a valid Item ID');
			 return false;
		 }
		 $.ajax({
                 url: "returns_create.php",
                 type:"POST",
                 data: {action: 'populateItem',item_id:str},
				 dataType:"json",
                 success: function(json) {
                 if(json['error'])
				 {
					 
					 alert(json['error']);
					 return false;
                 }
				 if(json['success'])
				 {
						$('#title_'+i+' input').val(json['description']); 
						$('#title_'+i+' span').html(json['description']); 
						
					 	$('#item_sku_'+i).val(json['product_sku']);
						$('#item_price_'+i).val(json['product_price']);
				 }
				 }
             });
	 }
	 </script>
  </head>
  <body>
     <div align="center">
     	<div <?php echo (isset($_GET['popup'])?'style="display:none"':''); ?>>
        <?php include_once 'inc/header.php';?>
     </div>
     	
		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>
		
		<h2>Customer Products Returns</h2>
		
		<form action="" method="post">
			<table border="1" cellpadding="10" cellspacing="0" width="95%">
				<tr>
				   <td>
					  <table cellpadding="5">
						    <tr>	
						    	<td><b>Full Name:</b></td>
						    	<td><?php echo $checkExist['first_name']. " ". $checkExist['last_name'];?></td>
						    	<td></td>
						    </tr>
						    
						    <tr>	
						    	<td><b>Address 1:</b></td>
						    	<td><?php echo $checkExist['address1']?></td>
						    	<td></td>
						    </tr>
						    
						    <tr>	
						    	<td><b>Address 2:</b></td>
						    	<td><?php echo $checkExist['address2'];?></td>
						    	<td></td>
						    </tr>
						    
						     <tr>	
						    	<td>City: <?php echo $checkExist['city'];?></td>
						    	<td>State: <?php echo $checkExist['state'];?></td>
						    	<td>Zip: <?php echo $checkExist['zip'];?></td>
						    </tr>
					  </table>	    
			    	</td>
			    	
			    	<td>
			    	   <table cellpadding="5">
			    	       <tr>
								<td><b><?php echo $checkExist['order_id'];?></b></td>
								<td>|</td>
								<td><b><?php echo $rma_number;?></b></td>	    	       
			    	       </tr>
			    	       
			    	       <tr>
								<td><b><?php echo $checkExist['order_date'];?></b></td>
								<td>|</td>
								<td><b><?php echo date('Y-m-d H:i:s');?></b></td>	    	       
			    	       </tr>
			    	       
			    	       <tr>
								<td><b>Status:</b></td>
								<td></td>	  
								<td><?php echo $checkExist['rma_status'];?></td>	    
			    	       </tr>	
			    	   </table>
			    	</td>
			     </tr> 	
			</table>
			
			<p>
			   Return Source:
			   <select name="source">
			   		<option value="mail">Mail</option>
			   		<option value="manual" <?php if($rma_return['source'] == 'manual'):?> selected="selected" <?php endif;?>>Manual</option>
			   		<option value="storefront" <?php if($rma_return['source'] == 'storefront'):?> selected="selected" <?php endif;?>>Storefront</option>
			   </select>
		    </p>
			
			<h2>Order Products</h2>
			<table border="1" cellpadding="10" cellspacing="0" width="95%">
				 <tr>
				 	  <th></th>
				      <th>SKU</th>
				      <th>Title</th>
				      <th>QTY</th>
				      <th>Return Code</th>
				 </tr>
				 
				 <?php foreach($order_products as $order_product):?>
				 	<?php $product_name = $db->func_query_first_cell("select name from oc_product_description where product_id = (select product_id from oc_product where sku = '".$order_product['product_sku']."' limit 1)");?>
				 	
				 	<?php for($i=0; $i<$order_product['product_qty'];$i++):?>
                    
                    <?php
					if($order_product['product_unit']>0)
					{
						$_price = $order_product['product_unit'];	
					}
					else
					{
						$_price = round($order_product['product_price'] / $order_product['product_qty'],4);
						
					}
					?>
						 	<tr>
						 		<td><input type="checkbox" name="return_item[<?php echo $order_product['product_sku'];?>][<?php echo $i;?>]" value="1" /></td>
						 		
						        <td><?php echo $order_product['product_sku'];?></td>
						      
						        <td><?php echo $product_name;?></td>
						      
						        <td>1</td>
						      
						        <td>
							      	<select name="data[<?php echo $order_product['product_sku'];?>][<?php echo $i;?>][return_code]">
							      		<option value="">Select One</option>
							      		<?php foreach($reasons as $reason):?>
							      			<option value="<?php echo $reason; ?>"><?php echo $reason; ?></option>
							      		<?php endforeach;?>
							      	</select>
							   </td>
						   </tr>
						   
						   <input type="hidden" name="data[<?php echo $order_product['product_sku'];?>][<?php echo $i;?>][price]" value="<?php echo $_price;?>" />
						   
						   <input type="hidden" name="data[<?php echo $order_product['product_sku'];?>][<?php echo $i;?>][title]" value="<?php echo $product_name?>" />
					<?php endfor;?>		 
					  
				 <?php endforeach;?>
			</table>
            <br />
            <br />
            <table border="1" cellpadding="10" cellspacing="0" width="800">
				 <tr>
				 	  <th></th>
				      <th>Item ID</th>
				      <th>Title</th>
				      <th>QTY</th>
				      <th>Return Code</th>
				 </tr>
                 <?php
				 for($z=0;$z<=25;$z++)
				 {
					?>
                    <tr>
                    <td><?=$z+1;?></td>
                    <td><input type="text" name="item_id[]" id="item_id_<?=$z;?>" onblur="populateItem(this,<?=$z;?>);" /></td>
                      <td id="title_<?=$z;?>"><input type="hidden" name="item_title[]" id="item_title_<?=$z;?>" /><span></span></td>
                      <td>1</td>
                      
                      <td>
							      	<select name="item_return_reason[]" id="item_return_reason_<?=$z;?>">
							      		<option value="">Select One</option>
							      		<?php foreach($reasons as $reason):?>
							      			<option value="<?php echo $reason; ?>"><?php echo $reason; ?></option>
							      		<?php endforeach;?>
							      	</select>
                                    
                                    <input type="hidden" name="item_sku[]" id="item_sku_<?=$z;?>" />
                                    <input type="hidden" name="item_price[]" id="item_price_<?=$z;?>" />
							   </td>
                    </tr>
                    <?php 
					 
				 }
				 ?>
                 
                 </table>
			
			<br />
			
		  	<table border="1" cellpadding="10">
		  		  <tr>
		  		  	  <td>Comment:</td>
		  			  <td>
		  			  	  <textarea rows="5" cols="50" name="comments"></textarea>
		  			  </td>
		  		  </tr>
		  	</table>
		  	<input type="hidden" name="return_number" value="<?php echo $rma_number?>" />
			
			<br /><br />
			<input type="hidden" name="email" value="<?php echo $checkExist['email']?>" />
			<input type="hidden" name="order_id" value="<?php echo $checkExist['order_id']?>" />
			<input type="hidden" name="store_type" value="<?php echo $checkExist['store_type']?>" />
			
			<a class="linkbutton" href="returns.php?<?=$popup_param;?>">Back</a>
			<input type="submit" name="create" value="Create" onclick="if(!confirm('Are you sure?')){ return false; }" class="button" />
			
			<input type="submit" name="save" value="Received" onclick="if(!confirm('Are you sure?')){ return false; }" class="button" />
		</form>		
     </div>		
  </body>
</html>