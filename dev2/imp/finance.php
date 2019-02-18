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
$num_rows = 100;
$start = ($page - 1)*$num_rows;

$vendor_query='';
if ($_SESSION['group']=='Vendor') {
  $vendor_query = ' and vendor = ' . $_SESSION['user_id'];
}

if($_GET['number']){
	$number = $db->func_escape_string(trim($_GET['number']));	
	$inv_query  = "select * from inv_shipments where package_number like '%$number%' $vendor_query order by date_issued DESC";
}
else{
	$inv_query  = "select * from inv_shipments where 1=1 $vendor_query order by date_issued DESC";
}
$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "finance.php",$page);
$shipments  = $db->func_query($splitPage->sql_query);
foreach($shipments as $index => $shipment){
	$_temp = $db->func_query("select (qty_shipped * unit_price) as shipped_total ,  (qty_received * unit_price) as received_total 
   from inv_shipment_items where shipment_id = '".$shipment['id']."'");
  $_shipped_total = 0.00;
  $_received_total = 0.00;

  foreach($_temp as $_t)
  {
    $_shipped_total = $_shipped_total + $_t['shipped_total'];
    $_received_total = $_received_total + $_t['received_total'];
    //   if($shipment['package_number']=='1192711715')
    // {
    // echo $_t['shipped_total']."<br>";
    // }
  }

  $shipments[$index]['extra'] = array('shipped_total'=>$_shipped_total,'received_total'=>$_received_total); 

  $SQL = "select sum(qty_rejected) as rejects ,  sum(qty_rejected * unit_price) as reject_total 
  from inv_rejected_shipment_items rsi inner join inv_shipment_items si on 
  (rsi.shipment_id = si.shipment_id and rsi.product_sku = si.product_sku)
  where rsi.shipment_id = '".$shipment['id']."'";
  $shipments[$index]['extra2'] = $db->func_query_first($SQL); 
}
//print_r($shipments); exit;
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>Finance</title>

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

  <div align="center">
   <form method="get">
    Package Number:
    <input type="text" name="number" value="<?php echo $_GET['number'];?>" />
    <input type="submit" name="search" value="Search" />
  </form>
</div>

<?php if($shipments):?>
 <table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
  <thead>
    <tr>
      <th>#</th>
      <th>Ship Date</th>
      <th>Shipment Number</th>
      <th>Ex Rate</th>
      <th>Shipping Cost</th>
      <th>Shipped Total</th>
      <th>Received Total</th>
      <th>Rejects</th>
      <th>Reject Total</th>
    </tr>
  </thead>
  <tbody>
   <?php $i = $splitPage->display_i_count(); $shipped_total = 0; $received_total = 0; $shipping_cost = 0;
   foreach($shipments as $shipment): if($shipment['ex_rate'] == 0) { $shipment['ex_rate'] = 1;}?>

   <tr id="<?php echo $shipment['id'];?>">
    <td align="center"><?php echo $i; ?></td>

    <td align="center"><?php echo date('m-d-Y H:i A',strtotime($shipment['date_issued']));?></td>

    <td align="center">
      <a href="view_shipment.php?shipment_id=<?php echo $shipment['id'];?>">
       <?php echo $shipment['package_number'];?>
     </a>
   </td>

   <td align="center"><?php echo $shipment['ex_rate'];?></td>

   <td align="center">$<?php echo number_format($shipment['shipping_cost'] / $shipment['ex_rate'],2);?></td>

   <td align="center">$<?php echo number_format($shipment['extra']['shipped_total'] / $shipment['ex_rate'],2);?></td>

   <td align="center">$<?php echo number_format($shipment['extra']['received_total'] / $shipment['ex_rate'],2);?></td>

   <td align="center"><?php echo $shipment['extra2']['rejects'];?></td>

   <td align="center">$<?php echo number_format($shipment['extra2']['reject_total'] / $shipment['ex_rate'],2);?></td>
 </tr>
 <?php 
 $shipping_cost += round($shipment['shipping_cost'] / $shipment['ex_rate'],2);
 $shipped_total += round($shipment['extra']['shipped_total'] / $shipment['ex_rate'],2);
 $received_total += round($shipment['extra']['received_total'] / $shipment['ex_rate'],2);
 $rejects += $shipment['extra2']['rejects'];
 $rejects_total += round($shipment['extra2']['reject_total'] / $shipment['ex_rate'],2);
 ?>     
 <?php $i++; endforeach; ?>

 <tr>
  <td></td>
  <td></td>
  <td align="center">Total:</td>
  <td></td>

  <td align="center"><b>$<?php echo $shipping_cost;?></b></td>
  <td align="center"><b>$<?php echo $shipped_total;?></b></td>
  <td align="center"><b>$<?php echo $received_total;?></b></td>
  <td align="center"><b><?php echo $rejects;?></b></td>
  <td align="center"><b>$<?php echo $rejects_total;?></b></td>
</tr>

<tr>
 <td colspan="4" align="left">
   <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
 </td>

 <td colspan="5" align="right">
   <?php  echo $splitPage->display_links(10,$parameters); ?>
 </td>
</tr>
</tbody>   
</table>   
<br />
<?php else : ?> 
  <p>
   <label style="color: red; margin-left: 600px;">Shipments is not exist.</label>
 </p>     
<?php endif;?>
</body>
</html>        