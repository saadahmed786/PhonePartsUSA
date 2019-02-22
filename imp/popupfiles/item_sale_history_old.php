<?php
require_once("../auth.php");
include_once '../inc/split_page_results.php';
include_once '../inc/functions.php';
$parameters  = $_SERVER['QUERY_STRING'];
$sku = $_GET['sku'];
$wh = '';
if ($_GET['product'] == '1') {
  $wh = ' AND a.order_status != "Canceled" ';
}
$conditions = base64_decode($_GET['conditions']);
if(!$conditions)
{
  $condition_sql = ' 1 = 1 '  ;
   
}
else
{
  $condition_sql = str_replace(","," AND ",$conditions);  
  
}

  //$inv_query = 'Select b.quantity,b.price,b.order_id,a.date_added from oc_order a,oc_order_product b where a.order_id=b.order_id AND ( a.order_status_id IN( 15 , 24 , 3 , 16 , 7 , 21 , 11) ) and b.`model` = "' . $sku . '" ORDER BY b.order_id DESC';
$inv_query = 'select b.product_qty as quantity,a.order_status,(b.product_price - b.promotion_discount) as price,b.order_id,a.order_date as date_added,po_business_id FROM inv_orders a,inv_orders_items b where a.order_id=b.order_id ' . $wh . ' and b.product_sku="'.$sku.'" ORDER BY a.order_date DESC';
  if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}

$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1)*$num_rows;

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "item_sale_history_old.php",$page);
;
$orders = $db->func_query($splitPage->sql_query);

?>

       

 <table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">
                                    <thead>
                                        <tr style="background-color:#e5e5e5;">
                                            <th >SN</th>
                                            <th >Date</th>
                                            <th >Order ID</th>
                                            <th >Qty</th>
                                            
                                            <th >Sale Price</th>
                                            
                                           
                                            
                                        </tr>
                                    </thead>
                                    <?php $i = $splitPage->display_i_count();
                                      foreach($orders as $itemSaleHistory)
                    {
                      if($itemSaleHistory['po_business_id'])
                      {
                        $is_fba_customer = (int)$db->func_query_first_cell("SELECT is_fbb FROM inv_po_customers WHERE id='".(int)$itemSaleHistory['po_business_id']."'");
                        if($is_fba_customer)
                        {
                          continue;
                        }
                      }
                    ?>
                    <tr>
                                    <td align="center">
                                        <?= ($i); ?>
                                    </td>
                                    <td align="center">
                                        <?= americanDate($itemSaleHistory['date_added']); ?>
                                    </td>
                                    <td align="center">
                                  <?php echo linkToOrder($itemSaleHistory['order_id'],$host_path,'target="_parent"'); ?>
                                    </td>
                                    <td align="center">
                                    <?php if ($itemSaleHistory['order_status'] == 'Estimate') {
                                      echo "E ";
                                    } ?>
                                       <?php echo $itemSaleHistory['quantity'];?>
                                    </td>
                                    <td align="center">
                                       $<?php echo number_format($itemSaleHistory['price'],2);?>
                                    </td>
                                </tr>
                                       <?php $i++;  ?>
                                      <?php
                    }
                    ?>
                      <tr>
                       <td colspan="3" align="left">
                           <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
                       </td>
                       
                       <td colspan="2" align="right">
                          <?php echo $splitPage->display_links(10,$parameters);?>
                       </td>
                    </tr>
                    </table>
                    </body>
                    </html>