<?php
require_once("auth.php");
include_once 'inc/functions.php';
$sku = $_GET['sku'];
$wh = ' AND a.order_status != "Canceled" ';
$inv_query = 'select b.product_qty as quantity,(b.product_price - b.promotion_discount) as price,b.order_id,a.order_date as date_added,po_business_id FROM inv_orders a,inv_orders_items b where a.order_id=b.order_id ' . $wh . ' and b.product_sku="'.$sku.'" ORDER BY a.order_date DESC';
$results = $db->func_query($inv_query);
//testObject($results);exit;
?>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="include/table_sorter.css" rel="stylesheet" type="text/css" />
        
        
    <script type="text/javascript" src="js/jquery.min.js"></script>
    
        
        <title>Product Sales Report</title>
    </head>
<div><?php include_once 'inc/header.php';?></div><br><br>
<h2 align="center">(<?php echo $sku;?>) Sales Report</h2>
      
    <div>
      <table width="80%" cellspacing="0" cellpadding="5px" border="1" align="center">
        <thead>
            <tr style="background-color:#e5e5e5;">
              <th >SN</th>
              <th >Date</th>
              <th >Order ID</th>
              <th >Qty</th>
            <th >Sale Price</th> 
          </tr>
        </thead>
        <?php $i = 0; $total_cost=0;
         foreach($results as $itemSaleHistory)
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
               <?php echo $itemSaleHistory['quantity'];?>
             </td>
             <td align="center">
               $<?php echo number_format($itemSaleHistory['price'],2);?>
             </td>
           </tr>
           <?php $i++; $total_cost = $total_cost+$itemSaleHistory['price'];
           }
         ?> 
         <tr >
           <td colspan="4" align="right">
             <strong>Total:</strong>
           </td>
           <td align="center">
             $<?php echo number_format($total_cost,2);?>
           </td>
         </tr>
      </table>
    </div>
  </body>
  </html>