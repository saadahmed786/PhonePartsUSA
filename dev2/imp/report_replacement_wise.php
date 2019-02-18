<?php
include_once 'auth.php';
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
page_permission('replacement_wise_return_report');
$start_date = date('Y')."-".date('m')."-01";
$end_date = date('Y')."-".date('m')."-".date('t');
 $condition_sql = " (a.order_date BETWEEN '$start_date' and '$end_date') ";

if(isset($_REQUEST['submit'])){
    $inv_query   = '';
    

    $parameters  = $_SERVER['QUERY_STRING'];
	
	 $sku = $db->func_escape_string($_REQUEST['sku']);
   	$start_date = $db->func_escape_string($_REQUEST['start_date']);
    $end_date = $db->func_escape_string($_REQUEST['end_date']);
        

        if(@$sku){
            $conditions[] =  " LCASE(b.product_sku)=LCASE('".$sku."') ";
        }

        if(@$start_date && $end_date)
        {
          $conditions[] =  " (a.order_date BETWEEN '$start_date' and '$end_date') ";
        }

        
            $condition_sql = implode(" AND " , $conditions);
        
        
        if(!$condition_sql){
            $condition_sql = " (a.order_date BETWEEN '$start_date' and '$end_date') ";

        }
        
        
    
}
$_REQUEST['start_date'] = $start_date;
$_REQUEST['end_date'] = $end_date;
$inv_query = "SELECT b.product_sku,COUNT(b.product_sku) as count_sku,sum(b.product_price) as product_price,sum(b.product_true_cost*b.product_qty) as cost FROM inv_orders a,inv_orders_items b WHERE a.order_id=b.order_id  and lower(a.payment_source)='replacement'  AND $condition_sql  GROUP BY b.product_sku ORDER BY COUNT(b.product_sku) DESC";

//echo $inv_query;exit;
if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}

$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1)*$num_rows;

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "report_replacement_wise.php",$page);
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
        <title>Report Return Item Wise</title>
        
        
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
        
        <h2 align="center">Replacement Report</h2>
       
        <h3 align="center">
        <?php
        if($_SESSION['login_as']=='admin')
        {
        ?>
        
         <a href="pdf_replacement_wise.php?<?=$parameters;?>" target="_blank">Print PDF Report</a>
         <?php
       }
       ?>
         </h3>
        
        <form name="order" action="" method="get">
                <table width="90%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
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
                                            <th>SKU</th>
                                            <th>Item Name</th>
                                            <th># Of Replacements</th>
                                            <th>Amt Replacement</th>
                                            <th>Cost</th>
                                          <!--   <th>Shipping Cost</th> -->
                                        <!--     <th>Action</th> -->
                                            
                                        </tr>
                                    </thead>
                                    <?php $i = $splitPage->display_i_count();
                                      ?>
                                        <?php
										foreach($inv_orders as $return)
										{
                              // $orders = $db->func_query("SELECT a.order_id FROM inv_orders a,inv_orders_items b WHERE a.order_id=b.order_id  and lower(a.payment_source)='replacement' and b.product_sku='".$return['product_sku']."'  AND $condition_sql  GROUP BY a.order_id ");
                              // $shipping_cost = 0.00;
                              // $_order_ids = '';
                              // foreach($orders as $order)
                              // {
                              //   $shipping_cost += $db->func_query_first_cell("SELECT sum(shipping_cost)+sum(insurance_cost) FROM inv_shipstation_transactions WHERE order_id='".$order['order_id']."'");
                              //   $_order_ids.=$order['order_id'].',';
                              // }
                              // $_order_ids = rtrim($_order_ids,',');
										?>
                                            <tr id="<?php echo $return['product_sku'];?>">
                                                <td align="center"><?php echo $i; ?></td>
                                                
                                                <td align="center"><?php echo linkToProduct($return['product_sku'], $host_path);?></td>
                                               <td ><?php   echo getItemName($return['product_sku']);?></td>
        
        
        <td align="center">
        <?php echo $return['count_sku'];?>
        </td>
        
        <td align="center">
        $<?=number_format($return['product_price'],2);?>
        </td>
        
                                                
                                                 <td align="center">$<?php echo number_format(@$return['cost'],2);?></td>
                                         <!--    <td><?=$_order_ids;?></td>
 -->                                           <!--    <td align="center">$<?php echo number_format($shipping_cost,2);?></td>
                                                  -->
                                                
                                                <!-- <td align="center">
                                                  		<a href="<?php echo $host_path;?>/popupfiles/view_item_wise_summary.php?sku=<?php echo $return['sku']?>&start_date=<?=$_REQUEST['start_date'];?>&end_date=<?=$_REQUEST['end_date'];?>&conditions=<?php echo base64_encode(implode(",",$conditions)); ?>" class="fancybox3 fancybox.iframe">View Summary</a>
                                                </td> -->
                                            </tr>
                                            <?php $i++;  ?>
                                            <?php
										}
										?>
                                    
                                </table>
                            </td>  
                            
                        <?php else : ?> 
                        
                            <td colspan=4><label style="color: red; margin-left: 600px;">No Record Found</label></td>
                             
                        <?php endif;?>
                    </tr>
                    
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