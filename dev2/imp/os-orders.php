<?php
//require_once("auth.php");

require_once("config.php");

include_once 'inc/split_page_results.php';

/*if($_SESSION['login_as'] != 'admin'){
	echo 'You dont have permission to manage Orders.';
	exit;
}*/



if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}

$max_page_links = 10;
$num_rows = 20;
$start = ($page - 1)*$num_rows;
$email = $_GET['email'];
$_query = "SELECT * from oc_order WHERE email='".$email."' ORDER BY order_id DESC";

$splitPage  = new splitPageResults($db , $_query , $num_rows , "model_list.php",$page);
$rows = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Orders List</title>
        <script type="text/javascript" src="js/jquery.min.js"></script>
	</head>
	<body>
		<div align="center">
			<div align="center" style="display:none"> 
			   <?php include_once 'inc/header.php';?>
			</div>
			
			 <?php if($_SESSION['message']):?>
				<div align="center"><br />
					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
				</div>
			 <?php endif;?>
			 
			 <br clear="all" />
			 
	        
	         
	         <br clear="all" /><br clear="all" />
	                      	  
			 <div align="center">
			 	  <table border="1" width="900px;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			 	  	 <tr style="background-color:#e7e7e7;">
			 	  	 	
                         <td>Order ID</td>
			 	  	 	 <td>Products</td>
			 	  	 	 <td>Return Code</td>
                         <td>Process</td>
                         <td>Date Added</td>
                        
                         
			 	  	 	 
			 	  	 	 
			 	  	 	 <td colspan="2" align="center">Action</td>
			 	  	 </tr>
			 	  	 
			 	  	 <?php 
					 $k=0;
					 foreach($rows as $row):
					 
					 $products = $db->func_query("SELECT * FROM oc_order_product WHERE order_id='".(int)$row['order_id']."'");
					 
					 ?>
			 	  	 	<tr id="tr-<?php echo $k;?>">
                       
				 	  	 	 <td><?php echo $row['order_id']; ?> <input id="order_id<?php echo $k;?>" type="hidden" value="<?php echo $row['order_id'];?>" /></td>
				 	  	 	 
				 	  	 	 <td><?php
							 $j=0;
                             foreach($products as $product)
							 {
								 $returnCheck=$db->func_query_first("SELECT
a.rma_number
FROM
    `inv_returns` a
    INNER JOIN `inv_return_items` b
        ON (a.`id` = b.`return_id`)
        WHERE a.`order_id` ='".(int)$row['order_id']."' AND b.`sku`='".$product['model']."'");
		
								 if($returnCheck)
								 {
									 
									 echo 'RMA # '.$returnCheck['rma_number']."<br>";
								 }
								 else
								 {
								echo '<input type="checkbox" class="product_ids'.$k.'" onChange="updateProducts(this,'.$k.')" id="product_id'.$k.$j.'" value="'.$product['model'].'"> ' .$product['model'].' * '.$product['quantity']."<br>";
								 }
							 }
							 
							 ?></td>
                            
				 	  	 	 <td>
							 <input type="hidden" id="products<?php echo $k;?>" value="" />
							 
							 <select id="return_code<?php echo $k;?>">
                             <?php
							 $reasons = $db->func_query("SELECT * FROM inv_reasons");
							 foreach($reasons as $reason)
							 {
								?>
                                <option value="<?php echo $reason['title'];?>"><?php echo $reason['title'];?></option>
                                <?php 
								 
							 }
							 
							 ?>
                             
                             </select>
                             
                             </td>
				 	  	 	 <td><select id="how_to_process<?php echo $k;?>">
                             <option value="Exchange">Exchange</option>
                             <option value="Refund">Refund</option>
                             
                             </select>
                             </td>
                             <td><?php echo date('d-m-Y h:i:s',strtotime($row['date_added'])); ?></td>
				 	  	 	 
				 	  	 	 
				 	  	 	
				 	  	 	 
				 	  	 	 
				 	  	 	 <td><a href="javascript:void(0);" onclick="generateRMA(<?php echo $k;?>)">RMA Return</a></td>
				 	  	 	 
				 	  	 	 <td><a href="#">Issue Revision</a></td>
			 	  	   </tr>
			 	  	 <?php 
					 $k++;
					 endforeach;?>
			 	  	 
			 	  	 <tr>
	                      <td colspan="3" align="left">
	                         <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
	                      </td>
	                      
	                      <td align="center" colspan="2" >
	                      	  <form method="get">
	                      	  	  Page: <input type="text" name="page" value="<?php echo $page;?>" size="3" maxlength="3" />
	                      	  	  <input type="submit" name="Go" value="Go" />
	                      	  </form>
	                      </td>
	                      
	                      <td align="right" colspan="2">
	                      		<?php echo $splitPage->display_links(10,$parameters);?>
	                      </td>
	                 </tr>
			 	  </table>
		     </div>		 
		</div>		     
    </body>
</html>
<script>

function updateProducts(obj,$k)
{
	var val="";
	$('.product_ids'+$k).each(function(index, element) {
		
        if(element.checked==true)
		{
			
			val+=$(element).val()+',';
			
		}
    });
	$('#products'+$k).val(val);
	
}
function generateRMA($i)
{
	if(!confirm('Are you sure want to generate RMA?'))
	{
	return false;	
	}
var order_id = $('#order_id'+$i).val();
var products = $('#products'+$i).val();
var return_code = $('#return_code'+$i).val();
var how_to_process = $('#how_to_process'+$i).val();
if(products=='')
{
alert('Please select items');
return false;	
	
}
 $.ajax({
                url: "xgen_rma.php",
				type:"POST",
                data: {order_id: order_id,products:products,returnc:encodeURIComponent(return_code),process:encodeURIComponent(how_to_process),email:encodeURIComponent('<?php echo $_GET['email'];?>')},
                success: function(data) {
					
					
					$('#tr-'+$i).fadeOut();
					}
            });	
	
}
</script>