<?php
include_once 'auth.php';
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
$pageLink = 'stock_inout_report.php';

page_permission('stock_inout_report');
$start_date = date('Y-m-d',strtotime('-1 days'));
$end_date = date('Y-m-d');
 $condition_sql = " (cron_date BETWEEN '$start_date' and '$end_date') ";

if(isset($_REQUEST['submit'])){
    $inv_query   = '';
    

    // $parameters  = $_SERVER['QUERY_STRING'];
	
	 $sku = $db->func_escape_string($_REQUEST['sku']);
   	$start_date = $db->func_escape_string($_REQUEST['start_date']);
    $end_date = $db->func_escape_string($_REQUEST['end_date']);
        
        $parameters='submit=1';
        if(@$sku){
            $conditions[] =  " LCASE(sku)=LCASE('".$sku."') ";
            $parameters.='&sku='.$sku;
        }

        if(@$start_date && $end_date)
        {
          $conditions[] =  " (cron_date BETWEEN '$start_date' and '$end_date') ";
          $parameters.='&start_date='.$start_date.'&end_date='.$end_date;
        }

        
            $condition_sql = implode(" AND " , $conditions);
        
        
        if(!$condition_sql){
            $condition_sql = " (cron_date BETWEEN '$start_date' and '$end_date') ";

        }
        
        
    
}
$_REQUEST['start_date'] = $start_date;
$_REQUEST['end_date'] = $end_date;

$sort = $_GET['sort'];
$order_by = $_GET['order_by'];

$sort_array  = array('sku','total_sold','qty_received','qty_sold','avg_cost','avg_price','profit');

    if(!in_array($sort, $sort_array))
{
    $sort = $sort_array[0];
    $order_by = 'asc';
}
$orderby = ' ORDER BY `'.$sort.'` '.$order_by;
if($order_by=='asc') $order_by='desc'; else $order_by = 'asc';
$inv_query = "SELECT sku,sum(total_sold) as total_sold,SUM(qty_received) as qty_received,sum(qty_sold) as qty_sold, avg(avg_cost) as avg_cost,avg(avg_price) as avg_price, (avg(avg_price)*sum(qty_sold)) - (avg(avg_cost)*sum(qty_sold)) as profit from inv_inout_report WHERE sku<>'SIGN'  AND $condition_sql   GROUP BY sku ".$orderby;

// echo $inv_query;
if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}

$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1)*$num_rows;

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "stock_inout_report.php",$page);
$inv_orders = $db->func_query($splitPage->sql_query);


?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" href="include/jquery-ui.css">
		<script src="js/jquery.min.js"></script>
        <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	 <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		<script src="js/jquery-ui.js"></script>
        <title>Stock In / Out Report</title>
        
        
    </head>
    <body>
        <?php include_once 'inc/header.php';?>

        <?php if(@$_SESSION['message']):?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
                </div>
        <?php endif;?>
        
        <br />
        
        <br />
        
        <h2 align="center">Stock In / Out Report</h2>
       
        <h3 align="center">
        <?php
        if($_SESSION['login_as']=='admin')
        {
        ?>
        
         <a href="pdf_stock_inout_report.php?<?=$parameters;?>" target="_blank">Print PDF Report</a>
         <?php
       }
       ?>
         </h3>
        
        <form name="order" action="" method="get">
                <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
                <tbody>
                    <tr>
                        

                        
                        
                        <td colspan="3" align="center">
                            <label for="start_date">SKU:</label>
                            <input type="text" name="sku" value="<?php echo @$_REQUEST['sku'];?>" />
                       <label for="start_date">Start / End Date</label>
                          <input style="width:140px" type="text" placeholder="Start Date" name="start_date" value="<?php echo $_REQUEST['start_date'];?>" class="datepicker" readOnly>
<input style="width:140px" type="text" placeholder="End Date" name="end_date" value="<?php echo $_REQUEST['end_date'];?>" class="datepicker" readOnly>
                        <input type="submit" value="Search" name="submit" style="margin: 10px 0 0 60px"></td>
                    </tr>

                    <tr>
                        <?php if($inv_orders):?>
                            <td colspan=8>
                                <table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">
                                    <thead>
                                        <tr style="background-color:#e5e5e5;">
                                            <th>SN</th>
                                            <th>Date</th>
                                            <th><a href="<?=$pageLink;?>?sort=sku&order_by=<?=$order_by;?>&<?=$parameters;?>">SKU</a></th>
                                            <th>Item Name</th>
                                            <th>End Qty</th>
                                            <th><a href="<?=$pageLink;?>?sort=qty_received&order_by=<?=$order_by;?>&<?=$parameters;?>">Qty Received (Shipment)</a></th>
                                            <th><a href="<?=$pageLink;?>?sort=qty_sold&order_by=<?=$order_by;?>&<?=$parameters;?>">Qty Sold</a></th>
                                            <th><a href="<?=$pageLink;?>?sort=avg_cost&order_by=<?=$order_by;?>&<?=$parameters;?>">Avg Cost</a></th>
                                            <th><a href="<?=$pageLink;?>?sort=avg_price&order_by=<?=$order_by;?>&<?=$parameters;?>">Avg Price</a></th>
                                            <th><a href="<?=$pageLink;?>?sort=total_sold&order_by=<?=$order_by;?>&<?=$parameters;?>">Total Sold Price</a></th>
                                            <th><a href="<?=$pageLink;?>?sort=profit&order_by=<?=$order_by;?>&<?=$parameters;?>">Profit</a></th>
                                            <th>Out Stock</th>
                                            
                                        </tr>
                                    </thead>
                                    <?php $i = $splitPage->display_i_count();
                                      ?>
                                        <?php
										foreach($inv_orders as $return)
										{
                        // $profit = ($return['avg_price']*$return['qty_sold']) - ($return['avg_cost']*$return['qty_sold']);  
										$profit = 	$return['profit'];
										?>
                                                <tr id="<?php echo $return['sku'];?>" style="background-color:#dcdcdc;font-weight:bold">
                                                <td align="center"><?php echo $i; ?></td>
                                                <td align="center"></td>
                                                <td align="center"><?php echo linkToProduct($return['sku'], $host_path);?></td>
                                               <td ><?php   echo getItemName($return['sku']);?></td>
        
        
        <td align="center">
        <?php $_qty =  $db->func_query_first_cell("SELECT quantity FROM oc_product WHERE model='".$return['sku']."'");

        $outstock_date = $db->func_query_first_cell("select outstock_date from inv_product_inout_stocks where product_sku = '" . $return['sku'] . "' order by date_modified desc limit 1");
        echo $_qty;
        ?>
        </td>
        <td align="center">
        <?php echo $return['qty_received'];?>
        </td>
        <td align="center">
        <?php echo $return['qty_sold'];?>
        </td>

        
        <td align="center">
        $<?=number_format($return['avg_cost'],2);?>
        </td>
         <td align="center">
        $<?=number_format($return['avg_price'],2);?>
        </td>

         <td align="center">
        $<?=number_format($return['total_sold'],2);?>
        </td>
         <td align="center">
        $<?=number_format($profit,2);?>
        </td>

        <td align="center">
       <?php if (!$_qty && $outstock_date) { ?>

       <?php
       echo '<span style="color:red">' .americanDate($outstock_date)."</span>";
   }
   else
   {
    echo '-';
   }
   ?>
        </td>
        
                                              
                                            </tr>
                                            <?php
                                            $rows = $db->func_query("SELECT cron_date,sku,total_sold,qty_received,qty_sold,avg_cost,avg_price,current_qty from inv_inout_report WHERE sku='".$return['sku']."'  AND $condition_sql    ORDER BY cron_date desc");
                                            
                                            foreach($rows as $row)
                                            {
                                                $profit = ($row['avg_price']*$row['qty_sold']) - ($row['avg_cost']*$row['qty_sold']);  
                                                // $profit = $row['profit'];                                                ?>
                                                <tr>
                                                <td align="center"></td>
                                                <td align="center"><?=date('m/d/Y',strtotime($row['cron_date']));?></td>
                                                <td align="center">-</td>
                                                <td align="center">-</td>
                                                <td align="center"><?php echo $row['current_qty'];?></td>
                                                <td align="center"><?php echo $row['qty_received'];?></td>
                                                <td align="center"><?php echo $row['qty_sold'];?></td>
                                                <td align="center"><?php echo $row['avg_cost'];?></td>
                                                <td align="center"><?php echo $row['avg_price'];?></td>
                                                <td align="center"><?php echo $row['total_sold'];?></td>
                                                <td align="center"><?php echo number_format($profit,2);?></td>
                                                <td align="center">-</td>

                                                
                                                </tr>
                                                <?php
                                            }


                                            ?>
                                            <?php $i++;  ?>
                                            <?php
										}
										?>
                                    
                                </table>
                            </td>  
                            
                        <?php else : ?> 
                        
                            <td colspan="11"><label style="color: red; margin-left: 600px;">No Record Found</label></td>
                             
                        <?php endif;?>
                    </tr>
             <?php
    $parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
?>
                    
                    <tr>
                       <td colspan="5" align="left">
                           <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
                       </td>
                       
                       <td colspan="6" align="right">
                       		<?php echo $splitPage->display_links(10,$parameters);?>
                       </td>
                    </tr>
             </tbody>
        </table>
    </form>
</body>
</html>