<?php
require_once("../auth.php");
include_once '../inc/split_page_results.php';
include_once '../inc/functions.php';
$parameters  = $_SERVER['QUERY_STRING'];
$sku = $_GET['sku'];

?>

<div style="display:none"><?php include_once '../inc/header.php';?></div>
<h2><?php echo $sku;?> - <?php echo getItemName($sku);?></h2>
       
       <?php
      $avg_sale = $db->func_query_first_cell("SELECT AVG(b.product_qty) FROM inv_orders_items b, inv_orders a where a.order_id=b.order_id and b.product_sku='".$sku."' and lower(a.order_status) not in ('on hold','voided','canceled','cancelled') and a.order_date BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() ");
      $sale_5 = $db->func_query_first_cell("SELECT sum(b.product_qty) FROM inv_orders_items b, inv_orders a where a.order_id=b.order_id and b.product_sku='".$sku."' and lower(a.order_status) not in ('on hold','voided','canceled','cancelled') and a.order_date BETWEEN CURDATE() - INTERVAL 5 DAY AND CURDATE() ");
      $sale_15 = $db->func_query_first_cell("SELECT sum(b.product_qty) FROM inv_orders_items b, inv_orders a where a.order_id=b.order_id and b.product_sku='".$sku."' and lower(a.order_status) not in ('on hold','voided','canceled','cancelled') and a.order_date BETWEEN CURDATE() - INTERVAL 15 DAY AND CURDATE() ");

      $sale_30 = $db->func_query_first_cell("SELECT sum(b.product_qty) FROM inv_orders_items b, inv_orders a where a.order_id=b.order_id and b.product_sku='".$sku."' and lower(a.order_status) not in ('on hold','voided','canceled','cancelled') and a.order_date BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() ");
      $sale_60 = $db->func_query_first_cell("SELECT sum(b.product_qty) FROM inv_orders_items b, inv_orders a where a.order_id=b.order_id and b.product_sku='".$sku."' and lower(a.order_status) not in ('on hold','voided','canceled','cancelled') and a.order_date BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE() ");
      
      $return_5 = $db->func_query_first_cell("SELECT SUM(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and b.sku='".$sku."' and a.rma_status in ('Completed','In QC','QC Completed')  and a.date_qc BETWEEN CURDATE() - INTERVAL 5 DAY AND CURDATE()");
      $return_15 = $db->func_query_first_cell("SELECT SUM(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and b.sku='".$sku."' and a.rma_status in ('Completed','In QC','QC Completed')  and a.date_qc BETWEEN CURDATE() - INTERVAL 15 DAY AND CURDATE()");
      $return_30 = $db->func_query_first_cell("SELECT SUM(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and b.sku='".$sku."' and a.rma_status in ('Completed','In QC','QC Completed')  and a.date_qc BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()");
      $return_60= $db->func_query_first_cell("SELECT SUM(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and b.sku='".$sku."' and a.rma_status in ('Completed','In QC','QC Completed')  and a.date_qc BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE()");

       ?>
<div>

<table width="100%">
<tr>
<td width="50%" style="vertical-align: text-top;">
    <table width="100%">
    <tr>
        <td colspan="2"><strong>Sales (in Days)</strong></td>
        <td colspan="2"><strong>Returns (in Days)</strong></td>
    </tr>
    <tr>
        <td style="width: 150px;">Ave (Last 30 days):</td><td align="left"><?php echo (float)$db->func_query_first_cell("SELECT mps FROM oc_product WHERE model='".$sku."'");?></td>
        <td style="width: 150px;"></td><td align="left"></td>
    </tr>
    <tr>
      <td style="width: 150px;">Last 5: </td><td align="left"><?php echo (int)$sale_5;?></td>
        <td style="width: 150px;">Last 5:</td><td align="left"> <?php echo (int)$return_5;?></td>
    </tr>
    <tr>
      <td style="width: 150px;">Last 15: </td><td align="left"><?php echo (int)$sale_15;?></td>
      <td style="width: 150px;">Last 15: </td><td align="left"><?php echo (int)$return_15;?></td>
    </tr>
    <tr>
      <td style="width: 150px;">Last 30: </td><td align="left"><?php echo (int)$sale_30;?></td>
     <td style="width: 150px;">Last 30: </td><td align="left"><?php echo (int)$return_30;?></td>
    </tr>
    <tr>
      <td style="width: 150px;">Last 60: </td><td align="left"><?php echo (int)$sale_60;?></td>
      <td style="width: 150px;">Last 60: </td><td align="left"><?php echo (int)$return_60;?></td>
      
    </tr>
    
    </table>
    <br>
    <?php
      //$item_vendors = $db->func_query("SELECT distinct vendor FROM inv_product_vendors WHERE product_sku='".$sku."'");
      $item_vendors = $db->func_query("SELECT distinct a.vendor from inv_shipments a, inv_shipment_items b where b.shipment_id=a.id and b.product_sku='".$sku."' and a.status in ('Completed','QCd') UNION SELECT distinct c.vendor from inv_rejected_shipments c, inv_rejected_shipment_items d WHERE d.rejected_shipment_id=c.id AND d.product_sku='".$sku."' and c.status in ('Completed','QCd')");
    ?>
    <table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">
      <thead>
        <tr style="background-color:#e5e5e5;">
          <th>Vendor</th>
          <th>Purchased</th>
          <th>Returns</th>
          <!--<th>Item Issue</th>-->
          <th>Defect Rate</th>
        </tr>
      </thead>
      <tbody>
      <?php
        foreach($item_vendors as $vendor)
         {
          $purchased = $db->func_query_first_cell("SELECT SUM(b.qty_received) from inv_shipment_items b, inv_shipments a where a.id=b.shipment_id and b.product_sku='".$sku."' and a.status in ('Completed','In QC') and a.vendor='".$vendor['vendor']."'");
          $rj = $db->func_query_first_cell("SELECT SUM(b.qty_rejected) from inv_rejected_shipment_items b, inv_rejected_shipments a where a.id=b.rejected_shipment_id and b.product_sku='".$sku."' and a.vendor='".$vendor['vendor']."'");
            
            // New Item Issue By gohar
          $new_item_issue = $db->func_query_first_cell("SELECT COUNT(id) from inv_return_items where rtv_vendor_id='".$vendor['vendor']."' and sku='".$sku."' and lower(item_condition) in ('item issue','not tested')");
          
          //Old Item issue Commented by Gohar
          /*$item_issue_shipments = $db->func_query("SELECT distinct(s.id) from inv_rejected_shipments s inner join inv_rejected_shipment_items si on (s.id = si.rejected_shipment_id) where s.vendor='".$vendor['vendor']."' and si.product_sku='".$sku."'");
          $item_issue = 0;
          $result = array();
          foreach ($item_issue_shipments as $shipment_id) {
            $result[] = $db->func_query("SELECT si.* , s.package_number from inv_rejected_shipment_items si left join inv_shipments s on (si.shipment_id = s.id)
            where si.rejected_shipment_id ='".$shipment_id['id']."' and si.product_sku='".$sku."'");
          }
          foreach ($result as $res) {
            foreach ($res as $val) {
              if (!$val['package_number']) {
                $item_issue+= $val['qty_rejected'];
              }
            }
          }*/
          
          
          //$checker = $db->func_query("SELECT si.* , s.package_number from inv_rejected_shipment_items si left join inv_shipments s on (si.shipment_id = s.id)where si.rejected_shipment_id IN ('".$item_issue_shipments."') and si.product_sku='".$sku."'");
          //echo "SELECT SUM(b.qty_rejected) from inv_rejected_shipment_items b left join inv_shipments s on (b.shipment_id = s.id) where s.package_number = '' AND b.rejected_shipment_id IN ('".$item_issue_shipments."') and b.product_sku='".$sku."'";
          //echo "SELECT SUM(b.qty_rejected) from inv_rejected_shipment_items b left join inv_shipments s on (b.shipment_id = s.id), inv_rejected_shipments a where s.package_number = '' AND a.id=b.rejected_shipment_id and b.product_sku='".$sku."' and a.vendor='".$vendor['vendor']."' and a.date_issued BETWEEN CURDATE() - INTERVAL 360 DAY AND CURDATE()";

          //$item_issue = $db->func_query_first_cell("SELECT SUM(b.quantity) from inv_return_items b, inv_returns a where a.id=b.return_id and b.item_condition in ('Item Issue','Item Issue - RTV') and b.rtv_vendor_id = '".$vendor['vendor']."' and b.sku='".$sku."' and a.date_qc BETWEEN CURDATE() - INTERVAL 360 DAY AND CURDATE()");
          //$item_issue = $db->func_query_first_cell("SELECT SUM(b.quantity) from inv_return_shipment_box_items b inner join inv_return_shipment_boxes c on (b.return_shipment_box_id=c.id) inner join inv_shipments a on (a.id=b.shipment_id)  where b.shipment_id<>'0' and c.box_type='ItemIssueBox' and a.vendor = '".$vendor['vendor']."' and b.product_sku='".$sku."' and a.date_qc BETWEEN CURDATE() - INTERVAL 360 DAY AND CURDATE()");
         $defect_rate = (($rj+$new_item_issue) / $purchased ) * 100;
        ?>
        <tr>
         <td><?php echo get_username($vendor['vendor']);?></td>
         <td><?php echo (int)$purchased;?></td>
         <!-- <td><?php echo (int)$rj;?></td> -->
         <td><?php echo (int)$new_item_issue+(int)$rj;?></td>
         <td><?php echo number_format($defect_rate,2);?>%</td>
        </tr>          
        <?php
        }
        ?>
      </tbody>
    </table>
    <br><br>
    <div style="overflow-x:hidden;overflow-y: scroll;height:350px;">
    <table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">
      <thead>
        <tr style="background-color:#e5e5e5;">
          <th>Date Out Of Stock</th>
          <th>Date Restored</th>
          <th>Days Out Of Stock</th>
          <th>Lost Sales</th>
        </tr>
      </thead>
      <tbody>
      <?php $stock_records = $db->func_query("select * from inv_product_inout_stocks where product_sku = '".$sku."' order by outstock_date desc");

      foreach ($stock_records as $stock) { ?>
            <tr>
             <td align="center"><?php echo $stock['outstock_date'];?></td>
             <?php if ($stock['instock_date']!='0000-00-00 00:00:00') { ?>
                <td align="center"><?php echo $stock['instock_date'];?></td>
             <?php } else { ?>
               <td align="center">N/A</td>
             <?php }
             if ($stock['instock_date']!='0000-00-00 00:00:00') {
                $diff = strtotime($stock['instock_date']) - strtotime($stock['outstock_date']);
              } else {
                $now = date('Y-m-d'); // or your date as well
                // echo $now;exit;
                $diff = strtotime($now) - strtotime($stock['outstock_date']);
                //$diff = strtotime(date()) - strtotime($stock['outstock_date']);
              } ?>
             <td align="center"><?php echo number_format($diff / (60 * 60 * 24),2).' Days'; ?></td>
             <td align="center"><?php echo $avg_sale * floor($diff / (60 * 60 * 24)); ?></td>
            </tr>          
        <?php } ?>
      </tbody>
    </table>
    </div>
</td>
<td align="center" width="50%">
 <iframe style="width:90%;height:500px" src="<?php echo $host_path;?>popupfiles/item_sale_history_old.php?sku=<?=$_GET['sku'];?>&product=<?=$_GET['product'];?>&conditions=<?=$_GET['conditions'];?>"></iframe><br><br>
 <a class="button" target="_blank" href="<?php echo $host_path;?>sku_sale_report.php?sku=<?=$_GET['sku'];?>">View All Sale history</a>
 </td>
 </tr>
 </table>
                    </div>
                    </body>
                    </html>