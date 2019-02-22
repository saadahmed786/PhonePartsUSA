<?php
set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once 'auth.php';
include_once 'inc/split_page_results.php';

$report_type = $_GET['report_type'];
if(!in_array($report_type, array("weekly","monthly"))){
	$report_type = 'daily';
}
$parameters[] = 'report_type='.$report_type;

$condition = '';
if($_GET['product_sku']){
	$parameters[] = 'product_sku='.$_GET['product_sku'];
	$condition = ' where product_sku = "'.$_GET['product_sku'].'" ';
}

if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}

if($report_type == 'weekly'){
	$inv_query = "select date_format(sale_date,'%Y / Week: %V') as week , sale_date , product_sku , sum(quantity) as quantity from inv_product_sale_history $condition group by week , product_sku order by sale_date desc";
}
elseif($report_type == 'monthly'){
	$inv_query = "select date_format(sale_date,'%M - %Y') as month , sale_date , product_sku , sum(quantity) as quantity from inv_product_sale_history $condition group by month , product_sku order by sale_date desc";
}
else{
	$inv_query = "select sale_date , product_sku , quantity from inv_product_sale_history $condition group by sale_date , product_sku order by sale_date desc";
}

$max_page_links = 10;
$num_rows = 20;
$start = ($page - 1)*$num_rows;

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "sale_history.php",$page);
$inv_sales  = $db->func_query($splitPage->sql_query);

if($report_type == 'daily'){
	foreach($inv_sales as $index => $inv_sale){
		$inv_sales[$index]['orders'] =  $db->func_query("select order_id , quantity from inv_product_sale_history where product_sku = '".$inv_sale['product_sku']."' and sale_date = '".$inv_sale['sale_date']."'");
	}
}

$parameters = implode("&", $parameters);
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Sale History</title>
        <script type="text/javascript" src="js/jquery.min.js"></script>
    </head>
    <body>
        <?php include_once 'inc/header.php';?>

        <?php if(@$_SESSION['message']):?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
                </div>
        <?php endif;?>
        
        <h2 align="center">Sale History</h2>
        
        <form method="get">
        	<table align="center">
        		<tr>
        			 <td>Product SKU: </td>
        			 <td>
        			 	 <input type="text" name="product_sku" value="<?php echo $_GET['product_sku'];?>" />
        			 </td>
        			 <td>&nbsp;</td>
        			 
        			 <td>Report Type: </td>
        			 <td>
        			 	 <select name="report_type">
        			 	 	 <option value="daily">Daily</option>
        			 	 	 <option value="weekly" <?php if($report_type == 'weekly'):?> selected="selected" <?php endif;?>>Weekly</option>
        			 	 	 <option value="monthly" <?php if($report_type == 'monthly'):?> selected="selected" <?php endif;?>>Monthly</option>
        			 	 </select>
        			 </td>
        			 <td>
        			 	 <input type="submit" name="search" value="Search" />
        			 </td>
        		</tr>
        	</table>
        </form>
        
        <?php if($inv_sales):?>
                  <table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center">
                        <thead>
                              <tr>
                              	  <th>S. N.</th>
                 	              <th>Date</th>
                 	              <th>SKU</th>
                 	              <?php if($report_type == 'daily'):?>
                                  		<th>Order / QTY</th>
                                  <?php endif;?>		
                                  <th>Sold</th>
                               </tr>
                         </thead>
                         <?php $i = $splitPage->display_i_count();
                               foreach($inv_sales as $sale): $total = 0;?>
                                   <tr id="<?php echo $sale['id'];?>">
                                   	   <td align="center"><?php echo $i;?></td>
                                   
                                   	   <?php if($report_type == 'weekly'):?>
                                   	   			<td align="center"><?php echo $sale['week'];?></td>
                                   	   <?php elseif($report_type == 'monthly'):?>
                                   	   			<td align="center"><?php echo $sale['month'];?></td>
                                   	   <?php else:?>
                                   	   			<td align="center"><?php echo date("d M Y", strtotime($sale['sale_date']));?></td>
                                   	   <?php endif;?>	
                                   	   
                                       <td align="center">
                                       		<a href="<?php echo $host_path; ?>/product/<?php echo $sale['product_sku'];?>"><?php echo $sale['product_sku'];?></a>
                                       </td>
                                       
                                       <?php if($report_type == 'daily'):?>
	                                       <td align="center">
	                                      	 	<?php foreach($sale['orders'] as $order):?>
	                                       				<?php echo $order['order_id'] . " / ". $order['quantity']. "<br/>"; $total+= $order['quantity'];?>
	                                      	 	<?php endforeach;?>
	                                       </td>
	                                   <?php else:?>
	                                   		<?php $total = $sale['quantity'];?>    
                                       <?php endif;?>
                                       
                                       <td align="center"><?php echo $total;?></td>         
                                   </tr>
                         <?php $i++; endforeach; ?>
                   </table>
           <?php else : ?> 
                    <div align="center">
                         	No sale history found
                    </div>	
          <?php endif;?>
             
          <div align="center">       
                <br />
                <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");
                  	  print "&nbsp;";
                      echo $splitPage->display_links(10,$parameters);
                ?>
                <br /><br />
         </div>             
	  </div>   
  </body>
</html>