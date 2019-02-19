<?php

include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';

$shipment_id = (int)$_GET['shipment_id'];

//save shipment qc details
if($_POST['QCUpdate'] || $_POST['QcComplete']){
	$rejected = 0;
	
	$total_qty = 0;
	foreach($_POST['products'] as $product){
		$qc_shipment = array();
		$qc_shipment['shipment_id'] = $shipment_id;
		$qc_shipment['product_sku'] = $product['product_sku'];
		$qc_shipment['accept_all']  = ($product['accept_all']) ? 1 : 0;
		
		$qc_shipment['grade_a'] = $product['grade_a'];
		$qc_shipment['grade_a_qty'] = $product['grade_a_qty'];
		$qc_shipment['grade_b'] = $product['grade_b'];
		$qc_shipment['grade_b_qty'] = $product['grade_b_qty'];
		$qc_shipment['grade_c'] = $product['grade_c'];
		$qc_shipment['grade_c_qty'] = $product['grade_c_qty'];
		$qc_shipment['grade_d'] = $product['grade_d'];
		$qc_shipment['grade_d_qty'] = $product['grade_d_qty'];
		$qc_shipment['rejected'] = $product['rejected'];
		
		$qc_shipment['date_modified'] = date('Y-m-d H:i:s');
		$qc_shipment['user_id'] = $_SESSION['user_id'];
		
		$checkExist = $db->func_query_first_cell("select id from inv_shipment_qc where shipment_id = '$shipment_id' and product_sku = '".$product['product_sku']."'");
		if(!$checkExist){
			$db->func_array2insert("inv_shipment_qc",$qc_shipment);		
		}
		else{
			$db->func_array2update("inv_shipment_qc",$qc_shipment," id = '$checkExist'");			
		}		
		
		//now add rejected sku to shipments
		if($qc_shipment['rejected'] > 0){
			$rejected = 1;
			addToRejectedShipment($product['product_sku'], $product['rejected'] , $shipment_id);
		}
		else{
			removeFromRejectedShipment($product['product_sku'], $shipment_id);
		}
		
		$total_qty += $product['qty_received'];
	}
	
	$db->db_exec("update inv_shipments SET date_qc = '".date('Y-m-d H:i:s')."'  where id = '$shipment_id'");
	
	if($_POST['QcComplete'] && $_SESSION['qc_shipment']){
		$db->db_exec("update inv_shipments SET status = 'Completed' , date_completed = '".date('Y-m-d H:i:s')."'  where id = '$shipment_id'");
		
		$shipment_detail = $db->func_query_first("select * from inv_shipments where id = '$shipment_id'");
		$ex_rate  = $db->func_escape_string($shipment_detail['ex_rate']);
		
		if($total_qty <= 0){
			$total_qty = 1;
		}
		
		$item_shipping_cost = round($shipment_detail['shipping_cost'] / $total_qty,4);
		
		//save shipment cost
		foreach($_POST['products'] as $product){
			$previous = $db->func_query_first("SELECT * FROM inv_product_costs WHERE sku='$product_sku' ORDER BY id DESC LIMIT 1");
			$product_sku   = $db->func_escape_string($product['product_sku']);
			$product_price = $db->func_escape_string($product['product_price']);
			
			addUpdateProductCost($product_sku , $product_price , $ex_rate , $item_shipping_cost);
		
			if($previous['raw_cost']!=$product_price and $previous['ex_rate']!=$ex_rate and $previous['shipping_fee']!=$item_shipping_cost)
			{
				
					addToPriceChangeReport($shipment_id,$product_sku,$product_price,$ex_rate,$item_shipping_cost);
				
			}
		}
		
		$_SESSION['message'] = "Shipment status is Completed";
		if($rejected){
			$last_id = $db->func_query_first_cell("select id from inv_rejected_shipments where status != 'Completed'");
			header("Location:update_rejectedshipment.php?id=$shipment_id&shipment_id=$last_id");
			exit;
		}
		else{
			header("Location:shipments.php");
			exit;
		}
	}
	
	$_SESSION['message'] = "QC Shipment is updated";
	header("Location:shipment_qc.php?shipment_id=$shipment_id");
	exit;
}

$shipment_detail = array();
if($shipment_id){
	$shipment_detail = $db->func_query_first("select * from inv_shipments where id = '$shipment_id'");
	
	$shipment_query  = "select sq.* , si.product_sku , si.qty_received , si.unit_price
					   from inv_shipment_items si left join inv_shipment_qc sq on 
					   (si.product_sku = sq.product_sku and si.shipment_id = sq.shipment_id) 
					   where si.shipment_id = '$shipment_id' and si.product_sku != ''";
	$shipment_items  = $db->func_query($shipment_query);
	
	foreach($shipment_items as $index => $product){
		$shipment_items[$index]['grade_skus'] = $db->func_query("select model,item_grade from oc_product where main_sku = '".$product['product_sku']."'","item_grade");
	}
}

//print "<pre>";print_r($shipment_items); exit;

if(!$shipment_detail){
	 header("Location:shipments.php");
	 exit;
}
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>QC Shipment</title>
        <script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		 
		<script type="text/javascript">
			$(document).ready(function() {
				$('.fancybox').fancybox({ width: '400px', height : '200px' , autoSize : true });
				$('.fancybox3').fancybox({ width: '1200px', height : '800px' , autoCenter : true , autoSize : false });
			});

			function showDiv(val , product_sku){
				if(val){
					jQuery('.product_'+product_sku+'_row').hide();
				}
				else{
					jQuery('.product_'+product_sku+'_row').show();
				}
			}
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
	        		<h2>Shipment Number: <?php echo $shipment_detail['package_number'];?></h2>
	        	</div>
	        	
	        	<div>	
			        <?php if($shipment_items):?>
			             <table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
			                <thead>
			                    <tr>
			                        <th>#</th>
			                        <th>SKU</th>
			                        <th>QTY Received</th>
			                        <th>Accept All</th>
			                        <th>Grade A</th>
			                        <th>Grade B</th>
			                        <th>Grade C</th>
			                        <th>Grade D</th>
			                        <th>Reject</th>
			                   </tr>
			               </thead>
			               <tbody>
			                 <?php foreach($shipment_items as $i => $product):?>
			                        <tr class="row_<?php echo $product['product_sku'];?>">
			                          <td align="center"><?php echo $i+1; ?></td>
			                          
			                          <td align="center">
			                          	  <?php echo $product['product_sku'];?>
			                          	  <input type="hidden" name="products[<?php echo $i?>][product_sku]" value="<?php echo $product['product_sku'];?>" />	
			                          	  <input type="hidden" name="products[<?php echo $i?>][product_price]" value="<?php echo $product['unit_price'];?>" />
			                          	  <input type="hidden" name="products[<?php echo $i?>][qty_received]" value="<?php echo $product['qty_received'];?>" />
			                          	  
			                          	  <br />
                          				  <?php $issue_count = $db->func_query_first_cell("select count(id) from inv_product_issues where product_sku = '".$product['product_sku']."'")?>
                          				  <a class="fancybox3 fancybox.iframe" href="popupfiles/product_issues.php?product_sku=<?php echo $product['product_sku'];?>"><?php echo $issue_count?> of item issues</a>
			                          </td>
			                          
			                          <?php $qty_left = $product['qty_received'] - $product['grade_a_qty'] - $product['grade_b_qty'] - $product['grade_c_qty']; ?>
			                          
			                          <td align="center">
			                          		<?php if(!is_null($product['accept_all']) && $product['accept_all'] != 1):?>
			                          			
			                          			<p><?php echo $qty_left . " -- " . $product['product_sku'];?></p>
			                          			
			                          			<?php if($product['grade_a_qty'] > 0):?>
			                          					<p><?php echo $product['grade_a_qty'] . " -- " . $product['grade_a'];?></p>
			                          			<?php endif;?>	
			                          			
			                          			<?php if($product['grade_b_qty'] > 0):?>
			                          					<p><?php echo $product['grade_b_qty'] . " -- " . $product['grade_b'];?></p>
			                          			<?php endif;?>
			                          			
			                          			<?php if($product['grade_c_qty'] > 0):?>
			                          					<p><?php echo $product['grade_c_qty'] . " -- " . $product['grade_c'];?></p>
			                          			<?php endif;?>	
			                          			
			                          		<?php else:?>
			                          				<?php echo $product['qty_received'];?>
			                          		<?php endif;?>
			                          </td>
			                          
			                          <td align="center">
			                          	  <input type="checkbox" onclick="showDiv(this.checked,'<?php echo $product['product_sku']?>');" name="products[<?php echo $i?>][accept_all]" value="1" <?php if($product['accept_all'] == 1 || is_null($product['accept_all'])):?> checked="checked" <?php endif;?> />
			                          </td>
			                          
			                          <td align="center">
		                          	  		<div class="product_<?php echo $product['product_sku'];?>_row" <?php if($product['accept_all'] == 1 || is_null($product['accept_all'])):?> style="display:none;" <?php endif;?>>
			                          	  		<?php if(!$product['grade_a']){
			                          	  				 $product['grade_a'] = $product['grade_skus']['Grade A']['model'];
			                          	  			  }
			                          	  		?>
			                          	  		<?php if($product['grade_a']):?>
			                          	  				<input type="text" readonly="readonly" size="15" name="products[<?php echo $i?>][grade_a]" value="<?php echo $product['grade_a']?>" /> 
			                          	  		<?php else:?>
			                          	  				<a class="fancybox fancybox.iframe" href="create_sku.php?main_sku=<?php echo $product['product_sku']?>&grade=A">Create SKU</a>
			                          	  		<?php endif;?>	
			                          	  		<br />
			                          	  		
			                          	  		<br />
			                          	  		<input type="text" size="5" name="products[<?php echo $i?>][grade_a_qty]" value="<?php echo $product['grade_a_qty']?>" />
			                          	  	</div>	
			                          </td>
			                          
			                          <td align="center">
		                          	  		<div class="product_<?php echo $product['product_sku'];?>_row" <?php if($product['accept_all'] == 1 || is_null($product['accept_all'])):?> style="display:none;" <?php endif;?>>
			                          	  		<?php if(!$product['grade_b']){
			                          	  				 $product['grade_b'] = $product['grade_skus']['Grade B']['model'];
			                          	  			  }
			                          	  		?>
			                          	  		<?php if($product['grade_b']):?>
			                          	  				<input type="text" readonly="readonly" size="15" name="products[<?php echo $i?>][grade_b]" value="<?php echo $product['grade_b']?>" /> 
			                          	  		<?php else:?>
			                          	  				<a class="fancybox fancybox.iframe" href="create_sku.php?main_sku=<?php echo $product['product_sku']?>&grade=B">Create SKU</a>
			                          	  		<?php endif;?>	
			                          	  		<br />
			                          	  		
			                          	  		<br />
			                          	  		<input type="text" size="5" name="products[<?php echo $i?>][grade_b_qty]" value="<?php echo $product['grade_b_qty']?>" />
			                          	   </div>		
			                          </td>
			                          
			                          <td align="center">
		                          	  		<div class="product_<?php echo $product['product_sku'];?>_row" <?php if($product['accept_all'] == 1 || is_null($product['accept_all'])):?> style="display:none;" <?php endif;?>>
			                          	  		<?php if(!$product['grade_c']){
			                          	  				 $product['grade_c'] = $product['grade_skus']['Grade C']['model'];
			                          	  			  }
			                          	  		?>
			                          	  		<?php if($product['grade_c']):?>
			                          	  				<input type="text" readonly="readonly" size="15" name="products[<?php echo $i?>][grade_c]" value="<?php echo $product['grade_c']?>" /> 
			                          	  		<?php else:?>
			                          	  				<a class="fancybox fancybox.iframe" href="create_sku.php?main_sku=<?php echo $product['product_sku']?>&grade=C">Create SKU</a>
			                          	  		<?php endif;?>	
			                          	  		<br />
			                          	  		
			                          	  		<br />
			                          	  		<input type="text" size="5" name="products[<?php echo $i?>][grade_c_qty]" value="<?php echo $product['grade_c_qty']?>" />
			                          	  	</div>	
			                          </td>
			                          
			                          <td align="center">
		                          	  		<div class="product_<?php echo $product['product_sku'];?>_row" <?php if($product['accept_all'] == 1 || is_null($product['accept_all'])):?> style="display:none;" <?php endif;?>>
			                          	  		<?php if(!$product['grade_d']){
			                          	  				 $product['grade_d'] = $product['grade_skus']['Grade D']['model'];
			                          	  			  }
			                          	  		?>
			                          	  		<?php if($product['grade_d']):?>
			                          	  				<input type="text" readonly="readonly" size="15" name="products[<?php echo $i?>][grade_d]" value="<?php echo $product['grade_d']?>" /> 
			                          	  		<?php else:?>
			                          	  				<a class="fancybox fancybox.iframe" href="create_sku.php?main_sku=<?php echo $product['product_sku']?>&grade=D">Create SKU</a>
			                          	  		<?php endif;?>	
			                          	  		<br />
			                          	  		
			                          	  		<br />
			                          	  		<input type="text" size="5" name="products[<?php echo $i?>][grade_d_qty]" value="<?php echo $product['grade_d_qty']?>" />
			                          	  	</div>	
			                          </td>
			                          
			                          <td align="center">
			                          		<div class="product_<?php echo $product['product_sku'];?>_row" <?php if($product['accept_all'] == 1 || is_null($product['accept_all'])):?> style="display:none;" <?php endif;?>>
			                          	  		<input type="text" size="5" name="products[<?php echo $i?>][rejected]" value="<?php echo $product['rejected']?>" />
			                          		</div>
			                          </td>
			                       </tr>
			                  <?php $i++; endforeach; ?>
			             </tbody>   
			         </table>   
			         
			         <br />
			         <div align="center" style="margin-right:10%;margin-top:5px;">
			        		<?php if($shipment_detail['status'] != 'Completed' && $_SESSION['qc_shipment']):?>
			        			<input type="submit" name="QCUpdate" value="QC Update" />
			        			
                              	<button type="submit" name="QcComplete" value="QcComplete" onclick="if(!confirm('Are you sure?')){ return false; }">
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