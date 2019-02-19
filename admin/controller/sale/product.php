<?php
require_once("auth.php");
include_once 'inc/functions.php';

$product_sku = $db->func_escape_string($_GET['sku']);

$_query = "Select p.sku , p.image , p.price , p.product_id, p.is_main_sku, p.quantity from oc_product p where p.sku = '$product_sku'";
$product = $db->func_query_first($_query);

$product['vendor_code'] = 'China Office';

$product_id = $product['product_id'];
/*if($product['pricing_rule'] == 'Manual'){
	$customer_groups_data = $db->func_query("select * from oc_product_discount where product_id = '".$product_id."'","customer_group_id","quantity");
}
else{*/
	$customer_groups_data = $db->func_query("select * from oc_product_discount where product_id = '".$product_id."'","customer_group_id","quantity");
	//$customer_groups_data = $db->func_query("select * from inv_product_discount where product_id = '".$product_id."'","customer_group_id","quantity");
//} commented by zaman

$product_price = $db->func_query_first_cell("select price from oc_product_discount where product_id = '$product_id' AND customer_group_id = '8' and quantity = 1");

$product_prices = $db->func_query_first("select * from inv_product_prices where product_sku = '$product_sku'");

if($_POST['update']){
	$date  = date('Y-m-d');
	$SKU   = $db->func_escape_string($product_sku);
	$raw_cost = $db->func_escape_string($_POST['raw_cost']);
	$ex_rate  = $db->func_escape_string($_POST['ex_rate']);
	
	addUpdateProductCost($SKU , $raw_cost , $ex_rate , $_POST['shipping_fee']);
	
	//update discount prices
	if($product['is_main_sku'] == '1'){
		
		/*
		Commented by Zaman
		if($_POST['pricing_rule'] == 'Manual' && $_POST['discount_fixed']){
			foreach($_POST['discount_fixed'] as $group_id => $data){
				foreach($data as $quantity => $price){
					if($price > 0 && $quantity > 0){
						if(isset($customer_groups_data[$group_id][$quantity])){
							//$db->db_exec("update oc_product_discount SET price = '$price' where product_id = '$product_id' and customer_group_id = '$group_id' and quantity = '$quantity'");
						}
						else{
							//$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '$product_id' , customer_group_id = '$group_id' , quantity = '$quantity' , price = '$price'");
						}
					}
				}
			}
		}
		elseif($_POST['pricing_rule'] == 'CostBased' && $_POST['discount_markup']){
			$customer_groups_discount_data = $db->func_query("select * from oc_product_discount where product_id = '".$product_id."'","customer_group_id","quantity");
			
			$vendor_code   = $product['vendor_code'];
			$last_cost = $db->func_query_first_cell("select (current_cost+shipping_fee) as cost from inv_product_costs where vendor_code = '$vendor_code' AND sku = '$product_sku' order by cost_date DESC limit 1");
		
			foreach($_POST['discount_markup'] as $group_id => $data){
				foreach($data as $quantity => $markup){
					if($quantity > 0 && $markup > 0){
						if(isset($customer_groups_data[$group_id][$quantity])){
							$db->db_exec("update inv_product_discount SET markup = '$markup' where product_id = '$product_id' and customer_group_id = '$group_id' and quantity = '$quantity'");
						}
						else{
							$db->db_exec("insert into inv_product_discount SET product_id = '$product_id' , customer_group_id = '$group_id' , quantity = '$quantity' , markup = '$markup'");
						}
						
						//now need to update discount table also
						$price = number_format(($last_cost + (($last_cost*$markup)/100)),4);
						
						if(isset($customer_groups_discount_data[$group_id][$quantity])){
							//$db->db_exec("update oc_product_discount SET price = '$price' where product_id = '$product_id' and customer_group_id = '$group_id' and quantity = '$quantity'");
						}
						else{
							//$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '$product_id' , customer_group_id = '$group_id' , quantity = '$quantity' , price = '$price'");
						}
					}
				}
			}
		}
		
		*/
	}
	
	//update prices
	$product_prices_row = array();
	$product_prices_row['ebay'] = $_POST['ebay'];
	$product_prices_row['amazon'] = $_POST['amazon'];
	$product_prices_row['channel_advisor'] = $_POST['channel_advisor'];
	$product_prices_row['channel_advisor1'] = $_POST['channel_advisor1'];
	$product_prices_row['channel_advisor2'] = $_POST['channel_advisor2'];
	$product_prices_row['bigcommerce'] = $_POST['bigcommerce'];
	$product_prices_row['bigcommerce_retail'] = $_POST['bigcommerce_retail'];
	$product_prices_row['bonanza'] = $_POST['bonanza'];
	$product_prices_row['wish'] = $_POST['wish'];
	$product_prices_row['open_sky'] = $_POST['open_sky'];
	$product_prices_row['date_modified'] = date('Y-m-d H:i:s');
	
	if($product_prices){
		$db->func_array2update("inv_product_prices", $product_prices_row," product_sku = '$product_sku'");
	}
	else{
		$product_prices_row['product_sku'] = $product_sku;
		$db->func_array2insert("inv_product_prices", $product_prices_row);
	}
	foreach($_POST['downgrades'] as $grade => $key)
	{
		
		//foreach($key as $key_val => $val)
		//{
			//echo $key_val."<Br>".$val."<br>";exit;
				if($key['sku'] and $key['price'])
				{
				$db->db_exec("UPDATE oc_product SET price='".(float)$key['price']."' WHERE sku='".$key['sku']."' AND item_grade='".$grade."' AND main_sku = '$product_sku'");	
				}
			
		//}
	}
	if($_POST['discount_fixed'])
	{
	$customer_groups_discount_data = $db->func_query("select * from oc_product_discount where product_id = '".$product_id."'","customer_group_id","quantity");
	
		foreach($_POST['discount_fixed'] as $group_id => $data){
				foreach($data as $quantity => $price){
					if($quantity > 0 && $price > 0){
						if(isset($customer_groups_discount_data[$group_id][$quantity])){
							$db->db_exec("update oc_product_discount SET price = '".(float)$price."' where product_id = '$product_id' and customer_group_id = '$group_id' and quantity = '$quantity'");
							
						}
						else{
							$db->db_exec("insert into oc_product_discount SET product_id = '$product_id' , customer_group_id = '$group_id' , quantity = '$quantity' , price = '".(float)$price."'");
						}
						
						
					}
				}
			}	
		
		
	}
	
	
	header("Location:".$host_path."product/$product_sku");
    exit;
}

$_query = "Select pc.user_id , u.name , pc.current_cost, pc.raw_cost , pc.ex_rate, pc.cost_date , 
		   pc.shipping_fee, pc.vendor_code from 
		   oc_product p left join inv_product_costs pc on (p.sku = pc.sku) 
		   left join inv_users u on (u.id = pc.user_id) where p.sku = '$product_sku' order by pc.id DESC";
$product_costs = $db->func_query($_query);

if(strlen($product['sku']) == 0){
   $_SESSION['message'] = 'Product is not exist';
   header("Location:".$host_path."products.php");
   exit;	
}

//$vendors = $db->func_query("select id,name,code from inv_vendors");
$downgrade_data = $db->func_query("select sku , item_grade , price , quantity from oc_product where main_sku = '$product_sku'","item_grade");

$product_issues = $db->func_query("select group_concat(id) as product_issue_id , item_issue , count(item_issue) as total , date_added from inv_product_issues where product_sku = '$product_sku' group by item_issue");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Product Detail</title>
		<script type="text/javascript" src="<?php echo $host_path?>/js/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo $host_path?>/fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo $host_path?>/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('.fancybox').fancybox({ width: '400px', height : '200px' , autoCenter : true , autoSize : false });
				jQuery('.fancybox2').fancybox({ width: '500px', height : '500px' , autoCenter : true , autoSize : false });
			});
		
		    function updatePrice(type){
			    price    = parseFloat(jQuery("#price_old_"+type).val());
			    markdown = parseFloat(jQuery("#markdown_"+type).val());

			    if(markdown > 0){
				    newPrice = price - ((price*markdown)/100); 
				    newPrice = newPrice.toFixed(2);
				}
			    else{
			    	newPrice = price; 
				    newPrice = newPrice.toFixed(2);
			    }

			    jQuery("#price_"+type).html(newPrice);
			    jQuery("#price_new_"+type).val(newPrice); 
		    }

		    function calculatePrice(node , cost, group_id , qty){
			    markup = parseFloat(jQuery(node).val());
			    cost   = parseFloat(cost);
			    
			    newPrice = cost + ((cost * markup) / 100);
			    newPrice = newPrice.toFixed(2);
			    
			    tdNode = jQuery(node).parents();
			    jQuery(tdNode).find(".costVal_"+group_id+"_"+qty).html(newPrice);
			}

			function getProductPrice(element , type){
				 jQuery(element).val('Please wait...');

				 url = '<?php echo $host_path.'getPrice.php'; ?>';
				 jQuery.ajax({
					  url: url,
					  data: {market : type , product_sku : '<?php echo $product_sku;?>'},
					  success: function(data){
						  jQuery('#'+type).val(data);
				      },
				      complete: function(){
				    	  jQuery(element).val('Get Price');
				      }
			     });
			}

			function updateProductPrice(element , type){
				 price = $('#'+type).val();
				 jQuery(element).val('Please wait...');

				 url = '<?php echo $host_path.'updatePrice.php'; ?>';
				 jQuery.ajax({
					  url: url,
					  data: {market : type , product_sku : '<?php echo $product_sku;?>', product_price : price},
					  success: function(data){
						  alert(data);
						  
						  alert('Price updated');
				      },
				      complete: function(){
				    	  jQuery(element).val('Update Price');
				      }
			     });
			}

			function getPrices(){
				var markets = new Array('channel_advisor','channel_advisor1','channel_advisor2','bigcommerce','bigcommerce_retail','bonanza','wish');
				for(i=0;i<markets.length;i++){
					element = jQuery('#'+markets[i]).next();
					getProductPrice(element , markets[i]);
				}
			}
		</script>
	</head>
	<body>
		<div align="center"> 
		   <?php include_once 'inc/header.php';?>
		</div>
		
		 <?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		 <?php endif;?>
		 
		 <div align="center">
		    <form method="post" action="">
		 	   <table width="1000px" cellpadding="10" border="1" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
		 	 	  <tr>
		 	 	  	  <td width="300px;">	
		 	 	  	  	  <div>
		 	 	  	  	  	  <h3 align="center"><?php echo $product['sku'] ." ($". number_format($product_price,2) . ") "; ?></h3>
		 	 	  	  	  	  <p align="center">Qty: <?php echo $product['quantity'];?></p>
		 	 	  	  	  	  
		 	 	  	  	  	  <br />
		 	 	  	  	  	  <div align="center">
		 	 	  	  	  	  	   <a href="http://phonepartsusa.com/image/<?php echo $product['image']; ?>" target="_blank"><img width="180" src="http://phonepartsusa.com/image/<?php echo $product['image']; ?>" alt="<?php echo $product['sku']; ?>" /></a>
		 	 	  	  	  	  </div>	
		 	 	  	  	  </div> 	  	
		 	 	  	  </td>
		 	 	  	  <td valign="top" width="600px">
		 	 	  	  	  <?php if($_SESSION['edit_cost']):?>
			 	 	  	  	  <div style="float:left">
		 	 	  	  	  	     <table cellpadding="5" cellspacing="0" style="float:left" >
		 	 	  	  	  	     	 <caption>Update Price</caption>
		 	 	  	  	  	     	 <tr>
								 	 	<td>Raw Cost:</td>
								 	 	<td><input type="text" name="raw_cost" value="" /></td>
								 	 </tr>
								 	 
								 	 <tr>
								 	 	<td>Ex. Rate:</td>
								 	 	<td><input type="text" name="ex_rate" value="" /></td>
								 	 </tr>
								 	 
								 	 <tr>
								 	 	<td>Shipping Fee:</td>
								 	 	<td><input type="text" name="shipping_fee" value="<?php echo $product_costs[0]['shipping_fee']; ?>" /></td>
								 	 </tr>
							 	 </table>
                                 
								 
			 	 	  	  	  </div>
						  <?php endif;?>	
                          <?php
								 // Average True Cost
								 $avg_count = 1;
								 
								 foreach($product_costs as $avg_cost)
								 {
									if($avg_cost['raw_cost'])
									{ 
									 if($avg_count>3) break;
									 $average_true_cost += ($avg_cost['raw_cost']+$avg_cost['shipping_fee'])/$avg_cost['ex_rate'];
									 
									 $avg_count++;
									}
								 }
								 
								 ?>
                                 <?php if($_SESSION['display_cost'] and $avg_count-1):?>
                                 <table cellpadding="5" cellspacing="0" style="float:right">
                                 <tr>
                                 <td style="font-weight:bold">
                                 Average True Cost / (<?php echo $avg_count-1;?> Entries):
                                 </td>
                                 <td style="font-weight:bold">$<?=number_format($average_true_cost/($avg_count-1),2);?></td>
                                 </tr>
                                 
                                 
                                 </table>
                                 <?php
								 endif;
								 ?>		 	 	  	  	  
		 	 	  	  	  
		 	 	  	  	  <br />
		 	 	  	  	  
		 	 	  	  	  <?php $last_cost = 0;?>
		 	 	  	  	  <table width="100%" border="1" cellpadding="5" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
		 	 	  	  	  	  <tr style="background-color:#e5e5e5;">
		 	 	  	  	  	  	  <td>Date</td>
		 	 	  	  	  	  	  
		 	 	  	  	  	  	  <?php if($_SESSION['display_cost']):?>
		 	 	  	  	  	  	  		<td>Raw Cost</td>
		 	 	  	  	  	  	  		<td>Ex Rate</td>
		 	 	  	  	  	  	  		<td>Shipping Fee</td>
		 	 	  	  	  	  	  		<td>True Cost</td>
		 	 	  	  	  	  	  <?php endif;?>		
		 	 	  	  	  	  </tr>
		 	 	  	  	  	  
		 	 	  	  	  	  <?php if($product_costs[0]['raw_cost']): $i = 0;?>
			 	 	  	  	  	  <?php foreach($product_costs as $cost):?>
			 	 	  	  	  	  		<tr>
				 	 	  	  	  	  	   <td>
				 	 	  	  	  	  	   	   <?php 
				 	 	  	  	  	  	   	   		$_query = "select s.id from inv_shipments s left join inv_shipment_items si on (s.id = si.shipment_id) 
				 	 	  	  	  	  	   	   				   where date_completed like '%".date("Y-m-d",strtotime($cost['cost_date']))."%' and si.product_sku = '$product_sku'
				 	 	  	  	  	  	   	   				   order by date_completed DESC";
				 	 	  	  	  	  	   	   		$shipment_id = $db->func_query_first_cell($_query);
				 	 	  	  	  	  	   	   ?>
				 	 	  	  	  	  	   	   <?php if($shipment_id):?>
					 	 	  	  	  	  	  	   <a href="<?php echo $host_path;?>view_shipment.php?shipment_id=<?php echo $shipment_id;?>">
					 	 	  	  	  	  	  	   		<?php echo date('m/d/Y' , strtotime($cost['cost_date']))?>
					 	 	  	  	  	  	  	   </a>
					 	 	  	  	  	  	   <?php else:?>
					 	 	  	  	  	  	   		<?php echo date('m/d/Y' , strtotime($cost['cost_date']))?>
					 	 	  	  	  	  	   <?php endif;?>	 	   
				 	 	  	  	  	  	   </td>
				 	 	  	  	  	  	   
				 	 	  	  	  	  	   <?php if($_SESSION['display_cost']):?>
				 	 	  	  	  	  	   		<td><?php echo $cost['raw_cost']?></td>
				 	 	  	  	  	  	   		<td><?php echo ($cost['ex_rate'] > 0) ? $cost['ex_rate'] : '';?></td>
				 	 	  	  	  	  	   		<td><?php echo $cost['shipping_fee']?></td>
				 	 	  	  	  	  	   		<td>$<?php echo $current_cost = number_format(($cost['raw_cost']+$cost['shipping_fee'])/$cost['ex_rate'],2);?></td>
				 	 	  	  	  	  	   <?php endif;?>		
				 	 	  	  	  	   </tr>
			 	 	  	  	  	  <?php if($i == 0){ $last_cost = $current_cost; } $i++; endforeach;?>
							 <?php endif;?>			 	 	  	  	  	  
		 	 	  	  	  </table>
		 	 	  	  </td>
		 	 	  </tr>
		 	 </table>
		 	 
		 	 <br /><br />
		 	 
		 	 <table width="1000px" cellpadding="10" border="1" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
		 	 	 <tr>
		 	 	 	<td>
					 	 <?php $downGrades = array('Grade A'=>'Grade A','Grade B'=>'Grade B','Grade C'=>'Grade C');?>
		 	 
					 	 <?php if($product['is_main_sku'] == '1'):?>
						 	 <table border="1" cellpadding="5" cellspacing="0">
						 	 	 <?php foreach($downGrades as $key => $value):?>
							 		 <tr>
										 <td><?php echo $key . "--". $downgrade_data[$value]['quantity'];?></td>
										 
										 <td><input type="text" size="15" name="downgrades[<?php echo $value;?>][sku]" value="<?php echo $downgrade_data[$value]['sku'];?>" /></td>
										 
										 <td><input type="text" size="10" name="downgrades[<?php echo $value;?>][price]" value="<?php echo $downgrade_data[$value]['price'];?>" /></td>
							 		 </tr>
							 	 <?php endforeach;?>		 
						 	 </table>
						 <?php endif;?> 
		 	 	 	</td>
		 	 	 	
		 	 	 	<td align="right">
		 	 	 	 	 <?php if($product['is_main_sku'] == '1'):?>
						 	 <?php $customer_groups = $db->func_query("select g.customer_group_id , gd.name from oc_customer_group g inner join oc_customer_group_description gd on (g.customer_group_id = gd.customer_group_id) where gd.name in ('Default','Local','Wholesale Small') order by field(gd.name,'Default','Local','Wholesale Small') limit 3");?>
							 <table border="1" cellpadding="5" cellspacing="0" width="600px;">
							 	<tr>
							 	   <th>Customer Group</th>
							 	   <th colspan="2">QTY 1</th>
							 	   <th colspan="2">QTY 3</th>
							 	   <th colspan="2">QTY 10</th>
							 	</tr>
							 	
							 	<?php //if($product['pricing_rule'] == 'Manual'): Commented by Zami?>
								 	<?php foreach($customer_groups as $group):?>
								 		<tr>
								 			<td><?php echo $group['name'];?></td>
								 			<td align="center" colspan="2"><input type="text" name="discount_fixed[<?php echo $group['customer_group_id']?>][1]" value="<?php echo number_format($customer_groups_data[$group['customer_group_id']][1]['price'],2)?>" size="10" /></td>
								 			<td align="center" colspan="2"><input type="text" name="discount_fixed[<?php echo $group['customer_group_id']?>][3]" value="<?php echo number_format($customer_groups_data[$group['customer_group_id']][3]['price'],2)?>" size="10" /></td>
								 			<td align="center" colspan="2"><input type="text" name="discount_fixed[<?php echo $group['customer_group_id']?>][10]" value="<?php echo number_format($customer_groups_data[$group['customer_group_id']][10]['price'],2)?>" size="10" /></td>
								 		</tr>
								 	<?php endforeach;?>
								 	
								<?php /*else:?>
									<?php foreach($customer_groups as $group):?>
								 		<tr>
								 			<td><?php echo $group['name'];?></td>
								 			<td align="center"><input type="text" onchange="calculatePrice(this,'<?php echo $last_cost;?>',<?php echo $group['customer_group_id'];?>,1);" name="discount_markup[<?php echo $group['customer_group_id']?>][1]" value="<?php echo $customer_groups_data[$group['customer_group_id']][1]['markup']?>" size="10" /></td>
								 			<td width="50" class="costVal_<?php echo $group['customer_group_id'];?>_1">$<?php echo number_format($last_cost + ($customer_groups_data[$group['customer_group_id']][1]['markup']*$last_cost)/100,2);?></td>
								 			
								 			<td align="center"><input type="text" onchange="calculatePrice(this,'<?php echo $last_cost;?>',<?php echo $group['customer_group_id'];?>,3);" name="discount_markup[<?php echo $group['customer_group_id']?>][3]" value="<?php echo $customer_groups_data[$group['customer_group_id']][3]['markup']?>" size="10" /></td>
								 			<td width="50" class="costVal_<?php echo $group['customer_group_id'];?>_3">$<?php echo number_format($last_cost + ($customer_groups_data[$group['customer_group_id']][3]['markup']*$last_cost)/100,2);?></td>
								 			
								 			<td align="center"><input type="text" onchange="calculatePrice(this,'<?php echo $last_cost;?>',<?php echo $group['customer_group_id'];?>,10);" name="discount_markup[<?php echo $group['customer_group_id']?>][10]" value="<?php echo $customer_groups_data[$group['customer_group_id']][10]['markup']?>" size="10" /></td>
								 			<td width="50" class="costVal_<?php echo $group['customer_group_id'];?>_10">$<?php echo number_format($last_cost + ($customer_groups_data[$group['customer_group_id']][10]['markup']*$last_cost)/100,2);?></td>
								 		</tr>
								 	<?php endforeach;?>
								 	
								<?php endif; */?>	 	
							 </table>
						 <?php endif;?>
		 	 	 	</td>
		 	 	 </tr>	
		 	 </table>
			  	 
			 <br/><br/>
			 <div>
			 	 <table width="60%" cellpadding="5" cellspacing="0" border="1">
			 	 	 <tr>
			 	 	 	 <td>eBay Price:</td>
			 	 	 	 <td><?php echo ($product_prices['ebay_fetchdate'] > 0) ? $product_prices['ebay_fetchdate'] : 'NA';?></td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="ebay" id="ebay" value="<?php echo $product_prices['ebay']?>" />
			 	 	 	 	 <input type="button" onclick="getProductPrice(this , 'ebay')" name="getPrice" value="Get Price" />
			 	 	 	 	 <input type="button" onclick="getProductPrice(this , 'ebay')" name="getPrice" value="Update Price" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td>Amazon Price:</td>
			 	 	 	 <td><?php echo ($product_prices['amazon_fetchdate'] > 0) ? $product_prices['amazon_fetchdate'] : 'NA';?></td>
			 	 	 	 <td> 
			 	 	 	 	 <input type="text" name="amazon" id="amazon" value="<?php echo $product_prices['amazon']?>" />
			 	 	 	 	 <input type="button" onclick="getProductPrice(this , 'amazon')" name="getPrice" value="Get Price" />
			 	 	 	 	 <input type="button" onclick="updateProductPrice(this , 'amazon')" name="getPrice" value="Update Price" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td>Channel Advisor Price:</td>
			 	 	 	 <td><?php echo ($product_prices['channel_advisor_fetchdate'] > 0) ? $product_prices['channel_advisor_fetchdate'] : 'NA';?></td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="channel_advisor" id="channel_advisor" value="<?php echo $product_prices['channel_advisor']?>" />
			 	 	 	 	 <input type="button" onclick="getProductPrice(this , 'channel_advisor')" name="getPrice" value="Get Price" />
			 	 	 	 	 <input type="button" onclick="updateProductPrice(this , 'channel_advisor')" name="getPrice" value="Update Price" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 
			 	 	 <tr>
			 	 	 	 <td>Channel Advisor US1 Price:</td>
			 	 	 	 <td><?php echo ($product_prices['channel_advisor1_fetchdate'] > 0) ? $product_prices['channel_advisor1_fetchdate'] : 'NA';?></td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="channel_advisor1" id="channel_advisor1" value="<?php echo $product_prices['channel_advisor1']?>" />
			 	 	 	 	 <input type="button" onclick="getProductPrice(this , 'channel_advisor1')" name="getPrice" value="Get Price" />
			 	 	 	 	 <input type="button" onclick="updateProductPrice(this , 'channel_advisor1')" name="getPrice" value="Update Price" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 
			 	 	 <tr>
			 	 	 	 <td>Channel Advisor US2 Price:</td>
			 	 	 	 <td><?php echo ($product_prices['channel_advisor2_fetchdate'] > 0) ? $product_prices['channel_advisor2_fetchdate'] : 'NA';?></td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="channel_advisor2" id="channel_advisor2" value="<?php echo $product_prices['channel_advisor2']?>" />
			 	 	 	 	 <input type="button" onclick="getProductPrice(this , 'channel_advisor2')" name="getPrice" value="Get Price" />
			 	 	 	 	 <input type="button" onclick="updateProductPrice(this , 'channel_advisor2')" name="getPrice" value="Update Price" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td>Bigcommerce Price:</td>
			 	 	 	 <td><?php echo ($product_prices['bigcommerce_fetchdate'] > 0) ? $product_prices['bigcommerce_fetchdate'] : 'NA';?></td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="bigcommerce" id="bigcommerce" value="<?php echo $product_prices['bigcommerce']?>" />
			 	 	 	 	 <input type="button" onclick="getProductPrice(this , 'bigcommerce')" name="getPrice" value="Get Price" />
			 	 	 	 	 <input type="button" onclick="updateProductPrice(this , 'bigcommerce')" name="getPrice" value="Update Price" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td>Bigcommerce Retail Price:</td>
			 	 	 	 <td><?php echo ($product_prices['bigcommerce_retail_fetchdate'] > 0) ? $product_prices['bigcommerce_retail_fetchdate'] : 'NA';?></td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="bigcommerce_retail" id="bigcommerce_retail" value="<?php echo $product_prices['bigcommerce_retail']?>" />
			 	 	 	 	 <input type="button" onclick="getProductPrice(this , 'bigcommerce_retail')" name="getPrice" value="Get Price" />
			 	 	 	 	 <input type="button" onclick="updateProductPrice(this , 'bigcommerce_retail')" name="getPrice" value="Update Price" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td>Bonanza Price:</td>
			 	 	 	 <td><?php echo ($product_prices['bonanza_fetchdate'] > 0) ? $product_prices['bonanza_fetchdate'] : 'NA';?></td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="bonanza" id="bonanza" value="<?php echo $product_prices['bonanza']?>" />
			 	 	 	 	 <input type="button" onclick="getProductPrice(this , 'bonanza')" name="getPrice" value="Get Price" />
			 	 	 	 	 <input type="button" onclick="updateProductPrice(this , 'bonanza')" name="getPrice" value="Update Price" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td>Wish Price:</td>
			 	 	 	 <td><?php echo ($product_prices['wish_fetchdate'] > 0) ? $product_prices['wish_fetchdate'] : 'NA';?></td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="wish" id="wish" value="<?php echo $product_prices['wish']?>" />
			 	 	 	 	 <input type="button" onclick="getProductPrice(this , 'wish')" name="getPrice" value="Get Price" />
			 	 	 	 	 <input type="button" onclick="updateProductPrice(this , 'wish')" name="getPrice" value="Update Price" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 	 <tr>
			 	 	 	 <td>OpenSky Price:</td>
			 	 	 	 <td><?php echo ($product_prices['open_sky_fetchdate'] > 0) ? $product_prices['open_sky_fetchdate'] : 'NA';?></td>
			 	 	 	 <td>
			 	 	 	 	 <input type="text" name="open_sky" id="open_sky" value="<?php echo $product_prices['open_sky']?>" />
			 	 	 	 	 <input type="button" onclick="getProductPrice(this , 'open_sky')" name="getPrice" value="Get Price" />
			 	 	 	 	 <input type="button" onclick="updateProductPrice(this , 'open_sky')" name="getPrice" value="Update Price" />
			 	 	 	 </td>
			 	 	 </tr>
			 	 </table>
			 	  <br />
			 	 <input type="button" name="fetchAll" value="Get All Prices" onclick="getPrices()"; />
			 </div>  
             
             <div style="margin-top:10px">
             <?php
			 $gadgetfix_scrape = $db->func_query_first("SELECT * FROM inv_product_scrape_prices WHERE sku='".$product_sku."' AND scrape_site='gadgetfix'");
			 $ebay_scrape = $db->func_query_first("SELECT * FROM inv_product_scrape_prices WHERE sku='".$product_sku."' AND scrape_site='ebay'");
			 
			 ?>
             <table border="1" cellpadding="5" cellspacing="0" width="60%;">
             
             <tr>
             <td>Gadget Fix</td>
             <td><?php echo ($gadgetfix_scrape['date_updated']?date('m/d/Y h:ia',strtotime($gadgetfix_scrape['date_updated'])):'N/A');?></td>
             <td><input type="text" id="gadgetfix_url" style="width:230px" value="<?php echo $gadgetfix_scrape['url'];?>" /></td>
             <td><input type="text" id="gadgetfix_price" style="width:70px" readonly value="<?php  echo (float)$gadgetfix_scrape['price'];?>" /></td>
             <td align="center"><input type="button" value="Fetch" onclick="fetchScrapePrice(this,'gadgetfix')" /><input type="button" value="Update" onclick="updateScrapePrice(this,'gadgetfix')" /></td>
             </tr>
             
                <tr>
             <td>Ebay</td>
             <td><?php echo ($ebay_scrape['date_updated']?date('m/d/Y h:ia',strtotime($ebay_scrape['date_updated'])):'N/A');?></td>
             <td><input type="text" id="ebay_url" style="width:230px" value="<?php echo $ebay_scrape['url'];?>" /></td>
             <td><input type="text" id="ebay_price" style="width:70px" readonly value="<?php  echo (float)$ebay_scrape['price'];?>" /></td>
             <td align="center"><input type="button" value="Fetch" onclick="fetchScrapePrice(this,'ebay')" /><input type="button" value="Update" onclick="updateScrapePrice(this,'ebay')" /></td>
             </tr>
             
             
             
             </table>
             
             </div>
			 
			 <?php if(isset($product_issues) and count($product_issues) > 0):?>
				 <div align="center">
					<table border="1" cellpadding="5" cellspacing="0" width="60%;">
						<tr>
							<td>Item Issue</td>
							<td>Thumbnail</td>
							<td>Occurance</td>
						</tr>
						<?php $base_path = "../images/";?>
						<?php foreach($product_issues as $product_issue):?>
								<tr>
									<td><?php echo $product_issue['item_issue'];?></td>
									
									<td>
										<?php 
											$product_issue_id = $product_issue['product_issue_id'];
											$product_issue_images = $db->func_query("select * from inv_product_issue_images where product_issue_id IN ($product_issue_id)");
											if(strtotime($product_issue['date_added']) < mktime(0,0,0,03,14,2015)){
												$base_path = "../../qc/serviceFiles/";
											}
										?>
										<ul style="list-style-type:none;">
											<?php foreach($product_issue_images as $product_issue_image):?>
											
												<li style="display:inline;">
													 <a class="fancybox2 fancybox.iframe" href="<?php echo $base_path;?><?php echo $product_issue_image['image_path']?>">
													 
													 	<img src="<?php echo $base_path;?><?php echo $product_issue_image['image_path']?>" width="50" height="50" />
													 	
													 </a>
												</li>
												
											<?php endforeach;?>
										</ul>
									</td>
									
									<td><?php echo $product_issue['total'];?></td>
								</tr>
						<?php endforeach;?>
					</table>
				</div>	
			 <?php endif;?> 	 
			 <br />
			 
			 <?php if($product['is_main_sku'] == '0'):?>
			 	<div align="left" style="width:950px;">
			 		<h3 align="left">Sales Price: $<?php echo number_format($product['price'],2);?></h3>
			 	</div>	
		 	 <?php endif;?>
		 	 <br clear="all" />
		 	 
		 	 <input type="submit" name="update" class="button" value="Update" />
		  </form>
		  
		  <br clear="all" />
		  <h2><a href="<?php echo $host_path;?>products.php">Go Back</a></h2>
	 </div>		 
  </body>
</html>
<script>
function fetchScrapePrice(obj,type)
{
	var scrape_url = $('#'+type+'_url').val();
	var url = '<?php echo $host_path;?>scrape_'+type+'_price.php'; 
	
	if(scrape_url=='')
	{
	alert("Please provide a valid url");
	return false;	
		
	}
	
	$(obj).val('Please wait...');
	 $.ajax({
					  url: url,
					  type:"POST",
					  data: {scrape_url:encodeURIComponent(scrape_url),action:'fetch'},
					  dataType:"json",
					  success: function(json){
						  $('#'+type+'_price').val(json['success']);
				      },
				      complete: function(){
				    	  $(obj).val('Fetch');
				      }
			     });
	
	
}
function updateScrapePrice(obj,type)
{
	var scrape_url = $('#'+type+'_url').val();
	var scrape_price = $('#'+type+'_price').val();
	var url = '<?php echo $host_path;?>scrape_'+type+'_price.php'; 
	
	if(scrape_price=='0' || scrape_price=='' || scrape_price=='0.00' )
	{
	alert("Please provide a valid price");
	return false;	
		
	}
	
	$(obj).val('Please wait...');
	 $.ajax({
					  url: url,
					  type:"POST",
					  data: {scrape_url:encodeURIComponent(scrape_url),scrape_price:scrape_price,sku:'<?php echo $product_sku;?>',action:'update'},
					  dataType:"json",
					  success: function(json){
						 if(json['success'])
						 {
								alert(json['success']); 
						 }
				      },
				      complete: function(){
				    	  $(obj).val('Update');
				      }
			     });
	
	
}

</script>