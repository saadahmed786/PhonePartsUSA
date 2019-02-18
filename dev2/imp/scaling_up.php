<?php
ini_set('memory_limit', '4096M');
ini_set('max_execution_time', 150); //300 seconds = 5 minutes
include_once 'auth.php';
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
page_permission('sales_dashboard');
$order_status_query_array = array('processed','shipped','completed','unshipped');

if(!isset($_GET['hide_header']))
{
  unset($_SESSION['hide_header']);
}

if(isset($_POST['action']) and $_POST['action']=='ajax')
{
	$type = $_POST['type'];
	$month = $_POST['month'];
	$year = $_POST['year'];
	switch($type)
	{
		case 'items_ordered':
		$query="SELECT SUM(b.product_qty) from inv_orders_items b,inv_orders a where a.order_id=b.order_id and LOWER(a.order_status) IN (". "'" . implode("','", $order_status_query_array) . "'".") and month(a.order_date)='".$month."' and year(a.order_date)='".$year."'";
		break;

		case 'orders_shipped':
		$query = "SELECT COUNT(*) from inv_orders a where  LOWER(a.order_status) IN ('shipped') and month(a.order_date)='".$month."' and year(a.order_date)='".$year."'";
		break;

		case 'orders_local':
		$query="SELECT COUNT(*) from oc_order a where LOWER(a.order_status_id) IN (3,15) and a.shipping_code='multiflatrate.multiflatrate_0' and month(a.date_added)='".$month."' and year(a.date_added)='".$year."'";
		break;

		case 'items_local':
		$query="SELECT SUM(b.quantity) from oc_order_product b,oc_order a where a.order_id=b.order_id and   LOWER(a.order_status_id) IN (3,15) and a.shipping_code='multiflatrate.multiflatrate_0' and month(a.date_added)='".$month."' and year(a.date_added)='".$year."'";
		break;

		case 'avg_item':
		$query="SELECT AVG(b.quantity) from oc_order_product b,oc_order a where a.order_id=b.order_id and   LOWER(a.order_status_id) IN (3,15) and month(a.date_added)='".$month."' and year(a.date_added)='".$year."'";
		break;

		case 'lbb_items':
		$query="SELECT SUM(b.total_received)from oc_buyback_products b,oc_buyback a where a.buyback_id=b.buyback_id and lower(a.status)<>'awaiting' and month(a.date_received)='".$month."' and year(a.date_received)='".$year."'  and  b.data_type='customer'";
		break;

		case 'purchased_items':
		$query="SELECT sum(b.qty_received) from inv_shipment_items b, inv_shipments a where a.id=b.shipment_id and LOWER(a.status)='completed'  and month(a.date_completed)='".$month."' and year(a.date_completed)='".$year."' ";
		break;

		case 'active_customers':
		$query="SELECT COUNT(DISTINCT LOWER(TRIM(a.email))) from inv_orders a where  LOWER(a.order_status) IN (". "'" . implode("','", $order_status_query_array) . "'".") and month(a.order_date)='".$month."' and year(a.order_date)='".$year."'";
		break;

		case 'returned_items':
		$query="SELECT sum(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and LOWER(a.rma_status) <>'awaiting'  and month(a.date_qc)='".$month."' and year(a.date_qc)='".$year."' ";
		break;

    case 'total_item_issue':
    $query="SELECT sum(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and LOWER(a.rma_status) <>'awaiting' and b.item_condition in ('Item Issue','Item Issue - RTV','Not Tested')  and month(a.date_qc)='".$month."' and year(a.date_qc)='".$year."' ";
    break;

     case 'mail_item_issue':
    $query="SELECT sum(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and LOWER(a.rma_status) <>'awaiting' and b.item_condition in ('Item Issue','Item Issue - RTV','Not Tested') and a.source='mail'  and month(a.date_qc)='".$month."' and year(a.date_qc)='".$year."' ";
    break;

     case 'local_item_issue':
    $query="SELECT sum(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and LOWER(a.rma_status) <>'awaiting' and b.item_condition in ('Item Issue','Item Issue - RTV','Not Tested') and a.source='storefront'  and month(a.date_qc)='".$month."' and year(a.date_qc)='".$year."' ";
    break;



     case 'total_customer_damage':
    $query="SELECT sum(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and LOWER(a.rma_status) <>'awaiting' and b.item_condition in ('Customer Damage','Not PPUSA Part')  and month(a.date_qc)='".$month."' and year(a.date_qc)='".$year."' ";
    break;

     case 'mail_customer_damage':
    $query="SELECT sum(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and LOWER(a.rma_status) <>'awaiting' and b.item_condition in ('Customer Damage','Not PPUSA Part') and a.source='mail'  and month(a.date_qc)='".$month."' and year(a.date_qc)='".$year."' ";
    break;

     case 'local_customer_damage':
    $query="SELECT sum(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and LOWER(a.rma_status) <>'awaiting' and b.item_condition in ('Customer Damage','Not PPUSA Part') and a.source='storefront'  and month(a.date_qc)='".$month."' and year(a.date_qc)='".$year."' ";
    break;


    case 'total_shipping_damage':
    $query="SELECT sum(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and LOWER(a.rma_status) <>'awaiting' and b.item_condition ='Shipping Damage'  and month(a.date_qc)='".$month."' and year(a.date_qc)='".$year."' ";
    break;

     case 'mail_shipping_damage':
    $query="SELECT sum(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and LOWER(a.rma_status) <>'awaiting' and b.item_condition ='Shipping Damage' and a.source='mail'  and month(a.date_qc)='".$month."' and year(a.date_qc)='".$year."' ";
    break;

     case 'local_shipping_damage':
    $query="SELECT sum(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and LOWER(a.rma_status) <>'awaiting' and b.item_condition ='Shipping Damage' and a.source='storefront'  and month(a.date_qc)='".$month."' and year(a.date_qc)='".$year."' ";
    break;



		default:
		echo 'error';exit;
		break;

	}

	$cache_name = 'scaling_up.'.$type.'.'.$month.$year;

	$row = $cache->get($cache_name);
	if(!$row)
	{
		$row = $db->func_query_first_cell($query);
		$cache->set($cache_name,$row);
	}
	echo (float)$row;exit;
}





?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

  <script src="js/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo $host_path; ?>include/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
  <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

  <link rel="stylesheet" type="text/css" href="include/xtable.css" media="screen" />

  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
  <link rel="stylesheet" type="text/css" href="http://phonepartsusa.com/catalog/view/theme/ppusa2.0/stylesheet/font-awesome.css">

  <title>Scaling Up Metrics</title>
  <style>
    .emoji
    {
      cursor:pointer;
      opacity: 0.4;
      filter: alpha(opacity=40);

    }

    .emoji:hover{
     opacity: 0.8;
     filter: alpha(opacity=80); 
   }
   .emoji-selected{
    opacity: 1;
    filter: alpha(opacity=100); 
  }
  .emoji-selected:hover{
    opacity: 1;
    filter: alpha(opacity=100); 
  }
  table.xtable
  {
    text-shadow: none;
  }
  .popover-content {
    height: 280px;  
    width: 200px;  
  }

  textarea.popover-textarea {
   border: 0px;   
   margin: 0px; 
   margin-top:5px;
   width: 100%;
   height: 170px;
   padding: 0px;  
   box-shadow: none;
   border: 1px solid #ddd;
 }

 .popover-footer {
  margin: 0;
  padding: 8px 14px;
  font-size: 14px;
  font-weight: 400;
  line-height: 18px;
  background-color: #F7F7F7;
  border-bottom: 1px solid #EBEBEB;
  border-radius: 5px 5px 0 0;
}

</style>


</head>
<body>
  <?php if (!$_SESSION['hide_header']) { ?>
  <div align="center"> 
    <?php } else { ?>
    <div style="display: none;" align="center">
      <?php } ?>
      <?php include_once 'inc/header.php';?>
    </div>



    <br />

    <br />

    <h2 align="center">Scaling Up Metrics</h2>




    <form name="order" action="" method="get">
      <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
        <tbody>
          <tr>



            <td align="center" colspan="2">
          

           
             <label for="filter_date">Date Range</label>
             <select name="filter_date">
               <option value="-3 months" <?php if($_GET['filter_date']=='-3 months') echo 'selected=""'; ?> >3 Months</option>
               <option value="-6 months" <?php if(!isset($_GET['filter_date'])) echo 'selected=""';?> <?php if($_GET['filter_date']=='-6 months') echo 'selected=""'; ?>>6 Months</option>
               <option value="-12 months" <?php if($_GET['filter_date']=='-12 months') echo 'selected=""'; ?>>12 Months</option>

            </select>
          




          <input type="submit" value="Search" class="btn btn-primary" name="submit" style="margin: 10px 0 0 60px"></td>
        </tr>
        
        
        <tr>
          
            <td colspan="2">
          
              <table width="100%" class="xtable" cellspacing="0"   align="center">
              
                <thead>
                  <tr style="background-color:#e5e5e5;">
                    <th width="20%"></th>
                    
                      <?php
                        $start    = new DateTime(($_GET['filter_date']?$_GET['filter_date']:'-6 months'));

// print_r( $start );exit;
$end      = new DateTime();
$interval = new DateInterval('P1M');
$period   = new DatePeriod($start, $interval, $end);
foreach($period as $date)
{
                      ?>
                    <th><?php echo $date->format('M');?>-<?php echo $date->format('y');?></th>
                    <?php
                  }
                  ?>
                 

                  </tr>
                 
                </thead>
                <tbody>

                     <tr>
                      <td>Items Sold</td>
                      <?php
                        foreach($period as $date){
                        	?>
                        	<td align="center" class="load_data" data-traversed="false" data-type="items_ordered" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                        	<?php
                        }
                        ?>

                  </tr>

                   <tr>
                      <td>Orders Shipped</td>
                       <?php
                        foreach($period as $date){
                        	?>
                        	<td align="center" class="load_data" data-traversed="false" data-type="orders_shipped" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                        	<?php
                        }
                        ?>

                  </tr>

                   <tr>
                   <td>Order by Local Customers</td>
                       <?php
                        foreach($period as $date){
                        	?>
                        	<td align="center" class="load_data" data-traversed="false" data-type="orders_local" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                        	<?php
                        }
                        ?>

                  </tr>

                    <tr>
                      <td>Items by Local Customers</td>
                      <?php
                        foreach($period as $date){
                        	?>
                        	<td align="center" class="load_data" data-traversed="false" data-type="items_local" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                        	<?php
                        }
                        ?>

                  </tr>

                  <tr>
                      <td>Avg. Item Per Order</td>
                      <?php
                        foreach($period as $date){
                        	?>
                        	<td align="center" class="load_data" data-traversed="false" data-type="avg_item" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                        	<?php
                        }
                        ?>
                     

                  </tr>


                  <tr>
                      <td>LBB Items</td>
                      <?php
                        foreach($period as $date){
                        	?>
                        	<td align="center" class="load_data" data-traversed="false" data-type="lbb_items" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                        	<?php
                        }
                        ?>

                  </tr>


                   <tr>
                      <td>Items Purchased</td>
                      <?php
                        foreach($period as $date){
                        	?>
                        	<td align="center" class="load_data" data-traversed="false" data-type="purchased_items" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                        	<?php
                        }
                        ?>

                  </tr>


                  <tr>
                      <td>Active Customers</td>
                      <?php
                        foreach($period as $date){
                        	?>
                        	<td align="center" class="load_data" data-traversed="false" data-type="active_customers" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                        	<?php
                        }
                        ?>

                  </tr>

                   <tr>
                      <td>Items Returned</td>
                      <?php
                        foreach($period as $date){
                        	?>
                        	<td align="center" class="load_data" data-traversed="false" data-type="returned_items" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                        	<?php
                        }
                        ?>

                  </tr>

                   <tr>
                      <td>Item Issue / Not Returned (Total)</td>
                      <?php
                        foreach($period as $date){
                          ?>
                          <td align="center" class="load_data" data-traversed="false" data-type="total_item_issue" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                          <?php
                        }
                        ?>

                  </tr>

                    <tr>
                      <td>Item Issue / Not Returned (Mail)</td>
                      <?php
                        foreach($period as $date){
                          ?>
                          <td align="center" class="load_data" data-traversed="false" data-type="mail_item_issue" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                          <?php
                        }
                        ?>

                  </tr>

                    <tr>
                      <td>Item Issue / Not Returned (Local)</td>
                      <?php
                        foreach($period as $date){
                          ?>
                          <td align="center" class="load_data" data-traversed="false" data-type="local_item_issue" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                          <?php
                        }
                        ?>

                  </tr>

                   <tr>
                      <td>Customer Damange / Not PPUSA Part (Total)</td>
                      <?php
                        foreach($period as $date){
                          ?>
                          <td align="center" class="load_data" data-traversed="false" data-type="total_customer_damage" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                          <?php
                        }
                        ?>

                  </tr>


                    <tr>
                      <td>Customer Damange / Not PPUSA Part (Mail)</td>
                      <?php
                        foreach($period as $date){
                          ?>
                          <td align="center" class="load_data" data-traversed="false" data-type="mail_customer_damage" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                          <?php
                        }
                        ?>

                  </tr>

                    <tr>
                      <td>Customer Damange / Not PPUSA Part (Local)</td>
                      <?php
                        foreach($period as $date){
                          ?>
                          <td align="center" class="load_data" data-traversed="false" data-type="local_customer_damage" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                          <?php
                        }
                        ?>

                  </tr>


                   <tr>
                      <td>Shipping Damage (Total)</td>
                      <?php
                        foreach($period as $date){
                          ?>
                          <td align="center" class="load_data" data-traversed="false" data-type="total_shipping_damage" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                          <?php
                        }
                        ?>

                  </tr>


                  <tr>
                      <td>Shipping Damage (Mail)</td>
                      <?php
                        foreach($period as $date){
                          ?>
                          <td align="center" class="load_data" data-traversed="false" data-type="mail_shipping_damage" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                          <?php
                        }
                        ?>

                  </tr>

                  <tr>
                      <td>Shipping Damage (Local)</td>
                      <?php
                        foreach($period as $date){
                          ?>
                          <td align="center" class="load_data" data-traversed="false" data-type="local_shipping_damage" data-month="<?php echo $date->format('m');?>"  data-year="<?php echo $date->format('Y');?>" >-</td>
                          <?php
                        }
                        ?>

                  </tr>

                


                </tbody>
              

                                     </table>
                                   </td>  

                              
                              </tr>

                            
                           </tbody>
                         </table>
                       </form>
                     </body>
                     </html>


                     <script>

                     $(document).ready(function(e)
                     {
                     	loadData();
                     });

                     function loadData()
                     {
                     	$obj = $('.load_data[data-traversed=false]:first');
                     	if($obj.length==0)
                     	{
                     		return false;
                     	}

                     	$.ajax({
                          url: 'scaling_up.php',
                          type: 'post',
                          data: {action:'ajax',type:$obj.attr('data-type'),month:$obj.attr('data-month'),year:$obj.attr('data-year')},
                          dataType: 'html',
                          beforeSend: function() {

                          },  
                          complete: function() {

                          },      
                          success: function(html) {
                            if(html=='error')
                            {
                            	alert('error, please debug');
                            }
                            else
                            {
                            	$obj.html(html);
                            	$obj.attr('data-traversed','true');
                            	loadData();
                            }



                          }
                        }); 

                     }

                     </script>
                    