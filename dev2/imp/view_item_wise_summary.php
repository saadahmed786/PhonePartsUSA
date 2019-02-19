<?php
require_once("../auth.php");
include_once '../inc/split_page_results.php';
include_once '../inc/functions.php';
$parameters  = $_SERVER['QUERY_STRING'];
$sku = $_GET['sku'];
$conditions = base64_decode($_GET['conditions']);
if(!$conditions)
{
	$condition_sql = ' 1 = 1 '	;
	 
}
else
{
	$condition_sql = str_replace(","," AND ",$conditions);	
	
}

  $inv_query = "SELECT a.*,b.* FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id AND b.sku='$sku'  AND $condition_sql ORDER BY a.date_added DESC";

  if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}

$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1)*$num_rows;

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "view_item_wise_summary.php",$page);
;
$returns = $db->func_query($splitPage->sql_query);

?>

<div style="display:none"><?php include_once '../inc/header.php';?></div>
<h2><?php echo $sku;?> - <?php   echo $db->func_query_first_cell("SELECT
b.`name`
FROM
    `oc_product` a
    INNER JOIN `oc_product_description` b
        ON (a.`product_id` = b.`product_id`) WHERE a.sku='".$sku."'");?></h2>
        <?php
        getWeeklyReturnsBySKU($sku,$condition_sql);
        ?>

 <table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">
                                    <thead>
                                        <tr style="background-color:#e5e5e5;">
                                            <th>SN</th>
                                            <th>Order Date</th>
                                             <th>Date QC</th>
                                            <th>RMA #</th>
                                            <th>Order ID</th>
                                            
                                            <th>Email</th>
                                            <th>Store Type</th>
                                            <th>Status</th>
                                            <th>Source</th>
                                            <th>Price</th>
                                            <th>Return Reason</th>
                                            <th>Condition</th>
                                            <th>Decision</th>
                                            
                                        </tr>
                                    </thead>
                                    <?php $i = $splitPage->display_i_count();
                                      foreach($returns as $return)
									  {
										  $comments = $db->func_query("SELECT comments FROM inv_return_comments WHERE return_id='".$return['return_id']."' and sku='".$sku."'");
										  $item_comments = '';
										  foreach($comments as $comment)
										  {
											  $_comment = explode("-",$comment['comments']);
											  
												$item_comments.=stripslashes($_comment[3])."\n";
											  
										  }
										  
										  $orderDate = $db->func_query_first_cell("SELECT `order_date` FROM `inv_orders` WHERE `order_id` = '" . $return['order_id'] . "'");
									  ?>
									  <tr>
                                      <td align="center"><?=$i;?></td>
                                      <td align="center"><?=americanDate($orderDate);?></td>
                                      <td align="center"><?=americanDate($return['date_added']);?></td>
                                      <td align="center"><a href="javascript:void(0);" onClick="window.open('../return_detail.php?rma_number=<?php echo $return['rma_number']?>')"><?=$return['rma_number'];?></a></td>
                                      <td align="center"><a href="javascript:void(0);" onClick="window.open('../viewOrderDetail.php?order=<?php echo $return['order_id']?>')"><?=$return['order_id'];?></a></td>
                                      <td align="center"><?= linkToProfile($return['email'], $host_path) ;?></td>
                                      <td align="center"><?=$return['store_type'];?></td>
                                      <td align="center"><?=$return['rma_status'];?></td>
                                      <td align="center"><?=$return['source'];?></td>
                                      <td align="center"><?='$'.number_format($return['price'],2);?></td>
                                      
                                      <?php
									  if($item_comments)
									  {
									  ?>
                                      <td align="center"><a href="javascript:void(0);" data-tooltip="<?php echo $item_comments;?>"><?=$return['return_code'];?></a></td>
                                      <?php
									  }
									  else
									  {
										?>
                                         <td align="center"><?=$return['return_code'];?></td>
                                        <?php  
									  }
									  ?>
                                      <td align="center"><?=$return['item_condition'];?></td>
                                      <td align="center"><?=$return['decision'];?></td>
                                      
                                      
                                      
                                      
                                      
                                      </tr>
                                       <?php $i++;  ?>
                                      <?php
									  }
									  ?>
									    <tr>
                       <td colspan="6" align="left">
                           <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
                       </td>
                       
                       <td colspan="7" align="right">
                       		<?php echo $splitPage->display_links(10,$parameters);?>
                       </td>
                    </tr>
                    </table>
                    </body>
                    </html>