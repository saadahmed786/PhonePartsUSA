<?php

include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';

if(!$_SESSION['email']){
    header("Location:index.php");
    exit;
}

$shipment_id = (int)$_GET['shipment_id'];

//save shipment
if($_POST['save'] || $_POST['IssueShipment'] || $_POST['ReceiveIt']){
	$shipment = array();
	$shipment['package_number'] = $db->func_escape_string($_POST['package_number']);
	
	if($_SESSION['display_exrate']){
		$shipment['ex_rate'] = $db->func_escape_string($_POST['ex_rate']);
	}
	
	if($_SESSION['display_cost']){
		$shipment['shipping_cost'] = $db->func_escape_string($_POST['shipping_cost']);
	}
	
	if($shipment_id){
		$db->func_array2update("inv_shipments",$shipment,"id = '$shipment_id'");
		$_SESSION['message'] = "Shipment is updated";
	}
	else{
		$checkExist = $db->func_query_first_cell("select id from inv_shipments where package_number = '".$shipment['package_number']."'");
		if($checkExist){
			$_SESSION['message'] = "This package number is assigned to another shipment.";
			header("Location:addedit_shipment.php");
			exit;
		}
		else{
			$shipment['status'] = 'Pending';
			$shipment['date_added'] = date('Y-m-d H:i:s');
		
			$shipment_id = $db->func_array2insert("inv_shipments",$shipment);
			$_SESSION['message'] = "Shipment is created";
		}
	}
	
	if($shipment_id){
		$shipment_item_ids = array();
		foreach($_POST['products'] as $product){
			$shipment_item = array();
			$shipment_item['product_id']  = $product['product_id'];
			$shipment_item['product_sku'] = $product['model'];
			if($_SESSION['display_cost']){
				$shipment_item['unit_price']  = $product['price'];
			}
			
			if($_SESSION['edit_pending_shipment']){
				$shipment_item['qty_shipped']  = $product['qty'];
			}
			
			if($_SESSION['edit_received_shipment']){
				$shipment_item['qty_received']  = $product['qty_received'];
			}
			
			$shipment_item['shipment_id'] = $shipment_id;
			
			$checkExist = $db->func_query_first_cell("select id from inv_shipment_items where shipment_id = '$shipment_id' and product_sku = '".$product['model']."'");
			if($checkExist){
				$db->func_array2update("inv_shipment_items",$shipment_item,"id = '$checkExist'");
				$shipment_item_ids[] = $checkExist;
			}
			else{
				$shipment_item_ids[] = $db->func_array2insert("inv_shipment_items",$shipment_item);
			}
			
			$SKU   = $db->func_escape_string($product['model']);
			$raw_cost = $db->func_escape_string($product['price']);
			$ex_rate  = $db->func_escape_string($_POST['ex_rate']);
			
			$qty = ($product['qty_received']) ? $product['qty_received'] : $shipment_item['qty_shipped'];
			if($qty <= 0){
				$qty = 1;
			}
			
			$shipping_fee = $shipment['shipping_cost'] / $qty;
			
			//addUpdateProductCost($SKU , $raw_cost , $ex_rate , $shipping_fee);
		}
		
		//check for new products
		foreach($_POST['new_products'] as $product){
			$shipment_item = array();
			$shipment_item['product_id']  = 0;
			$shipment_item['product_name'] = $product['title'];
			
			if($product['sku_type']){
				$last_id = getProductSkuLastID($product['sku_type']);
				$shipment_item['product_sku'] = getSKUFromLastId($product['sku_type'] , $last_id);
			}
			else{
				$shipment_item['product_sku'] = $product['model'];
			}
			
			if($_SESSION['edit_pending_shipment']){
				$shipment_item['qty_shipped']  = $product['qty'];
			}
			
			if($_SESSION['edit_received_shipment']){
				$shipment_item['qty_received']  = $product['qty_received'];
			}
			
			if($_SESSION['display_cost']){
				$shipment_item['unit_price']  = $product['price'];
			}
			
			$shipment_item['shipment_id'] = $shipment_id;
			$shipment_item['is_new']  = 1;
			
			$checkExist = $db->func_query_first_cell("select id from inv_shipment_items where shipment_id = '$shipment_id' and product_name = '".$product['title']."'");
			if($checkExist){
				$db->func_array2update("inv_shipment_items",$shipment_item,"id = '$checkExist'");
				$shipment_item_ids[] = $checkExist;
			}
			else{
				$shipment_item_ids[] = $db->func_array2insert("inv_shipment_items",$shipment_item);
			}
			
			if($shipment_item['product_sku']){
				createSKU($shipment_item['product_sku'] , $product['title'] , '' , $product['price'] , '' , 1);
				
				$SKU   = $db->func_escape_string($shipment_item['product_sku']);
				$raw_cost = $db->func_escape_string($product['price']);
				$ex_rate  = $db->func_escape_string($_POST['ex_rate']);
				
				//addUpdateProductCost($SKU , $raw_cost , $ex_rate);
			}
		}
		
		//delete extrs rows
		if($shipment_item_ids){
			$shipment_item_ids = implode(",",$shipment_item_ids);
			$db->db_exec("delete from inv_shipment_items where shipment_id = '$shipment_id' and not id in ($shipment_item_ids)");
		}
	}
	
	if($_POST['IssueShipment'] && $_SESSION['edit_pending_shipment']){
		$db->db_exec("update inv_shipments SET status = 'Issued' , date_issued = '".date('Y-m-d H:i:s')."' where id = '$shipment_id'");
		
		$_SESSION['message'] = "Shipment status is Issued";
		header("Location:shipments.php");
		exit;
	}
	
	if($_POST['ReceiveIt'] && $_SESSION['edit_received_shipment']){
		$db->db_exec("update inv_shipments SET status = 'Received' , date_received = '".date('Y-m-d H:i:s')."'  where id = '$shipment_id'");
		$_SESSION['message'] = "Shipment status is Received";
		header("Location:shipments.php");
		exit;
	}
	
	unset($_SESSION['list']);
	unset($_SESSION['newlist']);
	header("Location:shipments.php");
	exit;
}

$shipment_detail = array();
if($shipment_id){
	$shipment_detail = $db->func_query_first("select * from inv_shipments where id = '$shipment_id'");
	$list = array();
	$newlist = array();
	
	$shipment_items = $db->func_query("select * from inv_shipment_items where shipment_id = '$shipment_id' and is_new = 0","product_id");
	foreach($shipment_items as $product_id => $shipment_item){
		$list[$product_id] = array("qty"=>$shipment_item['qty_shipped'],
								   "qty_received" => $shipment_item['qty_received'],		
								   "price"=>$shipment_item['unit_price']);
	}
	unset($shipment_items);
	
	$shipment_items = $db->func_query("select * from inv_shipment_items where shipment_id = '$shipment_id' and is_new = 1");
	foreach($shipment_items as $product_id => $shipment_item){
		$newlist[] = array("title"=>$shipment_item['product_name'],
						   "qty"=>$shipment_item['qty_shipped'],
						   'sku' => $shipment_item['product_sku'],
						   "qty_received" => $shipment_item['qty_received'],
						   "price"=>$shipment_item['unit_price']);
	}
	unset($shipment_items);
}
else{
	$list = $_SESSION['list'];
	$newlist = $_SESSION['newlist'];
}

if(count($list) == 0){
	$_SESSION['message'] = "Please add at leaat 1 product to list to create a shipment";
	header("Location:sales.php");
	exit;
}

if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}

if($page < 1){
    $page = 1;
}

$parameters = "shipment_id=$shipment_id";

$max_page_links = 10;
$num_rows = 500;
$start = ($page - 1)*$num_rows;

$product_ids = implode(",",array_keys($list));

$inv_query   = "select p.product_id , p.model, p.quantity, p.status, p.mps , p.image , pd.name from 
				oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) 
				where p.product_id in ($product_ids)";

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "addedit_shipment.php",$page);
$products   = $db->func_query($splitPage->sql_query);

$product_skus = $db->func_query("select sku from inv_product_skus");
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Add / Edit Shipment</title>
        
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/jquery.lazyload.min.js"></script>
		<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		 
		<script type="text/javascript">
			$(document).ready(function() {
				$('.fancybox2').fancybox({ width: '500px', height : '200px' , autoCenter : true , autoSize : false });
				$('.fancybox3').fancybox({ width: '700px', height : '500px' , autoCenter : true , autoSize : false });

				$("img.lazy").lazyload({
				    effect : "fadeIn"
				});
			});

			function removeFromList(product_id , is_new){
				jQuery.ajax({
					url: 'inc/ajax.php?action=removeFromList&product_id='+product_id+'&is_new='+is_new,
					success: function(data){
						re = new RegExp(/Error.*?/gi);
                    	if(re.test(data)){
						    alert("Product is not removed from list, try again");
					    }
					    else{
					    	//alert("Product is removed from list");
					    	jQuery(".row_"+product_id).remove();
					    	jQuery("#row_"+product_id).remove();
					    }

                    	updateCart();
					}
				});
			}

			function updateCart(){
				jQuery.ajax({
					url: 'list_items.php',
					success: function(data){
						jQuery('#cart_items').html(data);
					}
				});
			}
		</script>	
		<style type="text/css">
			.cart{
				position:absolute;
				top:15%;
				right:15%;
				text-decoration:underline;
				cursor:pointer;
			}
		</style>
    </head>
    <body>
        <?php include_once 'inc/header.php';?>

        <?php if(@$_SESSION['message']):?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
                </div>
        <?php endif;?>
        
        <div class="cart" id="cart_items" align="right">
        	 <?php if(!$shipment_id):?>
        	 		<?php //include_once 'list_items.php';?>
        	 <?php endif;?>			
        </div>
        
        <div align="center">
	        <form method="post" action="">
	        	<br />
	        	<div>
	        		<?php if($_SESSION['display_exrate']):?>
	        			Ex. Rate:
	        			<input type="text" name="ex_rate" value="<?php echo $shipment_detail['ex_rate'];?>" required />
	        		<?php endif;?>	
	        		
	        		&nbsp;
	        		
	        		Shipment Number:
	        		<input type="text" name="package_number" value="<?php echo $shipment_detail['package_number'];?>" required />
	        		
	        		&nbsp;
	        		<?php if($_SESSION['display_cost']):?>
	        				Shipping Cost:
	        				<input type="text" name="shipping_cost" value="<?php echo $shipment_detail['shipping_cost'];?>"  />
	        		<?php endif;?>		
	        	
	        		<input type="submit" name="save" value="Save" />
	        	</div>
	        	
	        	<br />
	        	<a href="addsku.php?shipment_id=<?php echo $shipment_id?>" class="fancybox2 fancybox.iframe">Add SKU</a>
	        	&nbsp;
	        	|
	        	&nbsp;
	        	<a href="add_newitem.php?shipment_id=<?php echo $shipment_id?>" class="fancybox2 fancybox.iframe">Add New Item</a>
	        	
	        	<br /><br />
	        	
	        	<div>	
			        <?php if($products):?>
			             <table width="90%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
			                <thead>
			                    <tr>
			                        <th>#</th>
			                        <th>Image</th>
			                        <th>Name</th>
			                        <th>SKU</th>
                                     
			                        <th>Qty Shipped</th>
			                        <th>Qty Received</th>
			                        
			                        <?php if($_SESSION['display_cost']):?>
			                        	
                                        <th>Date</th>
                                        <th>Cost</th>
                                        <th>Price</th>
			                        <?php endif;?>
			                        
			                        <th></th>
			                   </tr>
			               </thead>
			               <tbody>
			                 <?php $i = $splitPage->display_i_count();
			           		     foreach($products as $product):?>
			                       <tr class="row_<?php echo $product['product_id'];?>">
			                          <td align="center"><?php echo $i; ?></td>
			                          
			                          <td align="center">
			                          		<a href="http://cdn.phonepartsusa.com/image/<?php echo $product['image'];?>" class="fancybox2 fancybox.iframe">
			                          			<img class="lazy" src="" data-original="http://cdn.phonepartsusa.com/image/<?php echo $product['image'];?>" height="50" width="50" alt="" />
			                          		</a>	
			                          </td>
			                          
			                          <td align="center" width="200px">
			                          		<?php echo $product['name'];?>
			                          		
			                          		<br />
                          					<?php $issue_count = $db->func_query_first_cell("select count(id) from inv_product_issues where product_sku = '".$product['model']."'")?>
                          					<a class="fancybox3 fancybox.iframe" href="popupfiles/product_issues.php?product_sku=<?php echo $product['model'];?>"><?php echo $issue_count?> of item issues</a>
			                          </td>
			                                                
			                          <td align="center">
			                          	  <a href="product/<?php echo $product['model'];?>"><?php echo $product['model'];?></a>
			                          	  <input type="hidden" name="products[<?php echo $i?>][model]" value="<?php echo $product['model'];?>" />	
			                          	  <input type="hidden" name="products[<?php echo $i?>][product_id]" value="<?php echo $product['product_id'];?>" />			
			                          </td>
			                          
			                          <td align="center">
			                          		<?php if($_SESSION['edit_pending_shipment']):?>
			                          				<input required type="text" name="products[<?php echo $i?>][qty]" value="<?php echo $list[$product['product_id']]['qty']?>" />
			                          		<?php else:?>
			                          				<?php echo $list[$product['product_id']]['qty']?>
			                          		<?php endif;?>			
			                          </td>
			                          
			                          <td align="center">
			                          		<?php if($_SESSION['edit_received_shipment']):?>
			                          				<input type="text" name="products[<?php echo $i?>][qty_received]" value="<?php echo $list[$product['product_id']]['qty_received']?>" />
			                          		<?php else:?>
			                          				<?php echo $list[$product['product_id']]['qty_received']?>
			                          		<?php endif;?>			
			                          </td>
			                          
			                          <?php if($_SESSION['display_cost']):?>
			                          		<?php
											$cost_query = $db->func_query_first("SELECT * FROM inv_product_costs WHERE sku='".$product['model']."'");
											
											?>
                                            	<td align="center"> <?php echo ($cost_query['raw_cost']?date('m/d/Y',strtotime($cost_query['cost_date'])):'');?></td>
                                                
                                                	<td align="center"> <?php echo $cost_query['raw_cost'];?></td>
                                                    
                                            <td align="center"><input type="text" name="products[<?php echo $i?>][price]" value="<?php echo $list[$product['product_id']]['price']?>" /></td>
			                          <?php endif;?>		
			                          
			                          <td align="center">
			                          	   <a href="javascript://" onclick="removeFromList(<?php echo $product['product_id'];?>);">X</a>
			                          </td>
			                       </tr>
			                  <?php $i++; endforeach; ?>
			                  
			                  <?php foreach($newlist as $new_item_id => $newItem):?>
			                  
			                  		<tr id="row_<?php echo $new_item_id;?>">
				                          <td align="center"><?php echo $i; ?></td>
				                          
				                          <td align="center">
				                          	  <?php $image = getItemImage($newItem['sku']);?>
				                          	  <a href="http://cdn.phonepartsusa.com/image/<?php echo $image;?>" class="fancybox2 fancybox.iframe">
			                          			  <img class="lazy" src="" data-original="http://cdn.phonepartsusa.com/image/<?php echo $image;?>" height="50" width="50" alt="" />
			                          		  </a>	
				                          </td>
				                          
				                          <?php $name = getItemName($newItem['sku']);?>
				                          <td align="center" width="200px">
				                          		<?php echo ($name) ? $name : $newItem['title']; ?>
				                          		
				                          		<br />
                          						<?php $issue_count = $db->func_query_first_cell("select count(id) from inv_product_issues where product_sku = '".$newItem['sku']."'")?>
                          						<a class="fancybox3 fancybox.iframe" href="popupfiles/product_issues.php?product_sku=<?php echo $newItem['sku'];?>"><?php echo $issue_count?> of item issues</a>
				                          </td>
				                                                
				                          <td align="center">
				                          	  <?php if($newItem['sku']):?>
				                          	  		<input type="text" name="new_products[<?php echo $i?>][model]" value="<?php echo $newItem['sku'];?>" />
				                          	  <?php else:?>
				                          	  		<select name="new_products[<?php echo $i?>][sku_type]">
				                          	  			 <option value="">Select SKU Type</option>
				                          	  			 <?php foreach($product_skus as $product_sku):?>
															 <option value="<?php echo $product_sku['sku'];?>"><?php echo $product_sku['sku'];?></option>
				                          	  			 <?php endforeach;?>
				                          	  		</select>
				                          	  <?php endif;?>		
				                          	  <input type="hidden" name="new_products[<?php echo $i?>][title]" value="<?php echo $newItem['title'];?>" />	
				                          </td>
				                          
				                          <td align="center">
				                          		<?php if($_SESSION['edit_pending_shipment']):?>
				                          				<input required type="text" name="new_products[<?php echo $i?>][qty]" value="<?php echo $newItem['qty']?>" />
				                          		<?php else:?>
				                          				<?php echo $newItem['qty']?>
				                          		<?php endif;?>			
				                          </td>
			                          
				                          <td align="center">
				                          		<?php if($_SESSION['edit_received_shipment']):?>
				                          				<input type="text" name="new_products[<?php echo $i?>][qty_received]" value="<?php echo $newItem['qty_received']?>" />
				                          		<?php else:?>
				                          				<?php echo $newItem['qty_received']?>
				                          		<?php endif;?>			
				                          </td>
			                          
				                          <?php if($_SESSION['display_cost']):?>
				                          		<td align="center"><input type="text" name="new_products[<?php echo $i?>][price]" value="<?php echo $newItem['price']?>" /></td>
				                          <?php endif;?>		
				                          
				                          <td align="center">
				                          	   <a href="javascript://" onclick="removeFromList(<?php echo $new_item_id;?> , '1');">X</a>
				                          </td>
				                    </tr>
			                  
			                  <?php $i++; endforeach;?>
			                      
			                  <tr>
			                  	  <td colspan="5" align="left">
				                      <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
			                      </td>
			                      
			                      <td colspan="5" align="right">
				                      <?php  echo $splitPage->display_links(10,$parameters); ?>
			                      </td>
			                  </tr>
			             </tbody>   
			         </table>   
			         
			         <br />
			         
			         <?php if($shipment_id AND $shipment_detail['status'] == 'Pending' && $_SESSION['edit_pending_shipment']):?>
			         		<div align="center" style="margin-right:10%;margin-top:5px;">
			         			<button type="submit" name="IssueShipment" value="IssueShipment" onclick="if(!confirm('Are you sure?')){ return false; }">
                              	 	Save And Issue Shipment
                              	</button>
			         		</div>
			         <?php endif;?>
			         
			         <?php if($shipment_id AND $shipment_detail['status'] == 'Issued' && $_SESSION['edit_received_shipment']):?>
			         		<div align="center" style="margin-right:10%;margin-top:5px;">
			         			<button type="submit" name="ReceiveIt" value="ReceiveIt" onclick="if(!confirm('Are you sure?')){ return false; }">
                              	 	Save And Receive It
                              	</button>
			         		</div>
			         <?php endif;?>
			         
			         <br /><br />
			         
			       <?php endif;?>
		    </form>
		</div>             
   </body>
</html>        