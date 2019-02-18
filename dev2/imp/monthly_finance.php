<?php

include_once 'auth.php';
include_once 'inc/split_page_results.php';

if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}
$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
$max_page_links = 10;
$num_rows = 20;
$start = ($page - 1)*$num_rows;

$inv_query  = "select * , sum(shipping_cost) as shipping_cost_total , group_concat(id) as ids , date_format(date_issued,'%Y-%m') as month_date,avg(ex_rate) as e_rate 
			   from inv_shipments group by month_date having month_date != '00-00' order by date_issued DESC";

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "monthly_finance.php",$page);
$shipments  = $db->func_query($splitPage->sql_query);

foreach($shipments as $index => $shipment){
	if($shipment['ids']){
		$_temp = $db->func_query("select (qty_shipped * unit_price) as shipped_total ,  (qty_received * unit_price) as received_total 
				from inv_shipment_items where shipment_id IN (".$shipment['ids'].")");
    
		$_shipped_total = 0.00;
    $_received_total = 0.00;
    foreach($_temp as $_t)
    {
      $_shipped_total = $_shipped_total + $_t['shipped_total'];
      $_received_total = $_received_total + $_t['received_total'];
    }
		$shipments[$index]['extra'] = array('shipped_total'=>$_shipped_total,'received_total'=>$_received_total); 
		
		$SQL = "select sum(qty_rejected) as rejects ,  sum(qty_rejected * unit_price) as reject_total 
			from inv_rejected_shipment_items rsi inner join inv_shipment_items si on 
			(rsi.shipment_id = si.shipment_id and rsi.product_sku = si.product_sku)
			where rsi.shipment_id IN (".$shipment['ids'].")";
		$shipments[$index]['extra2'] = $db->func_query_first($SQL); 
	}
}

// echo "<pre>";
// print_r($shipments); exit;

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Monthly Finance</title>
        
        <script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		 
		<script type="text/javascript">
			$(document).ready(function() {
				$('.fancybox').fancybox({ width: '680px' , autoCenter : true , autoSize : true });
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
        
        <?php if($shipments):?>
             <table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Month</th>
                        <th>Shipping Cost Total</th>
                        <th>Shipped Total</th>
                        <th>Received Total</th>
                        <th>Total Cost</th>
                        <th>Rejects</th>
                        <th>Reject Total</th>
                   </tr>
               </thead>
               <tbody>
                 <?php $i = $splitPage->display_i_count();
           		     foreach($shipments as $shipment):?>
                   
                                            
                       <tr id="<?php echo $shipment['id'];?>">
                          <td align="center"><?php echo $i; ?></td>
                          
                          <td align="center"><?php echo $shipment['month_date'];?></td>
                                                
                          <td align="center">$<?php echo number_format($shipment['shipping_cost_total'] / $shipment['e_rate'],2);?></td>
                          
                          <td align="center">$<?php echo number_format($shipment['extra']['shipped_total'] / $shipment['e_rate'],2);?></td>
                          
                          <td align="center">$<?php echo number_format($shipment['extra']['received_total'] / $shipment['e_rate'],2);?></td>
                          
                          <?php $total = $shipment['extra']['received_total'] + $shipment['shipping_cost_total'];?>
                          <td align="center">$<?php echo number_format($total /$shipment['e_rate'] ,2);?></td>
                          
                          <td align="center"><?php echo $shipment['extra2']['rejects'];?></td>
                          
                          <td align="center">$<?php echo number_format($shipment['extra2']['reject_total'] / $shipment['e_rate'],2);?></td>
                        </tr>
                  <?php $i++; endforeach; ?>
                      
                  <tr>
                  	  <td colspan="4" align="left">
	                      <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
                      </td>
                      
                      <td colspan="4" align="right">
	                      <?php  echo $splitPage->display_links(10,$parameters); ?>
                      </td>
                  </tr>
               </tbody>   
            </table>   
        <?php else : ?> 
              <p>
                 <label style="color: red; margin-left: 600px;">Shipments is not exist.</label>
              </p>     
        <?php endif;?>
   </body>
</html>        