<?php
include_once '../auth.php';
include_once '../inc/functions.php';
$_date= $_GET['date'];
$chart_group = $_GET['chart_group'];
$user_id = $_GET['user_id'];
$_date = explode("-", $_date);
  $date = $_date[1].''.$_date[0];
$reason = $_GET['reason'];
if($reason=='Return')
{
  $query = "select a.*,sum(d.amount) as used,d.date_added as date_used,d.order_id from oc_voucher a LEFT JOIN inv_voucher_details b ON (a.voucher_id=b.voucher_id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where   (b.is_rma=1 or b.is_pos=1) ";
}
elseif($reason=='LBB')
{
  $query = "select a.*,sum(d.amount) as used,d.date_added as date_used,d.order_id from oc_voucher a LEFT JOIN inv_voucher_details b ON (a.voucher_id=b.voucher_id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where b.is_lbb=1 ";
}
elseif($reason=='Order Cancellation')
{
  $query = "select a.*,sum(d.amount) as used,d.date_added as date_used,d.order_id from oc_voucher a LEFT JOIN inv_voucher_details b ON (a.voucher_id=b.voucher_id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where b.is_order_cancellation=1 ";
}
elseif($reason=='Manual')
{
  $query = "select a.*,sum(d.amount) as used,d.date_added as date_used,d.order_id from oc_voucher a LEFT JOIN inv_voucher_details b ON (a.voucher_id=b.voucher_id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where b.is_manual=1 ";
}
else
{
  $query = "select  a.*,sum(d.amount) as used,d.date_added as date_used,d.order_id from oc_voucher a LEFT JOIN inv_voucher_reasons b ON (a.reason_id=b.id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where SUBSTRING_INDEX(SUBSTRING_INDEX(b.reason, '-', 1), ',', -1)='".$reason."' ";
}




  $month = $_date[0];
  $year = $_date[1];

 
if($chart_group=='Weeks')
      {
    // $vouchers = $db->func_query("select a.*,sum(d.amount) as used,d.date_added as date_used,d.order_id from oc_voucher a LEFT JOIN inv_voucher_reasons b ON (a.reason_id=b.id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where c.user_id='".$user_id."' and yearweek(d.date_added,3)='".$date."' and a.status=1 group by a.voucher_id  ");
        $where = " and c.user_id='".$user_id."' and yearweek(d.date_added,3)='".$date."' and a.status=1 group by a.voucher_id";




      }
      elseif($chart_group=='Months')
      {
           // $vouchers = $db->func_query("select a.*,,sum(d.amount) as used,d.date_added as date_used,d.order_id from oc_voucher a LEFT JOIN inv_voucher_reasons b ON (a.reason_id=b.id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where c.user_id='".$user_id."' and month(d.date_added)='".$month."'  and a.status=1 and year(d.date_added)='".$year."' group by a.voucher_id   ");


        $where = " and c.user_id='".$user_id."' and month(d.date_added)='".$month."'  and a.status=1 and year(d.date_added)='".$year."' group by a.voucher_id";


      }
// echo $query.$where;exit;
      $vouchers = $db->func_query($query.$where);
    

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <script src="../js/jquery.min.js"></script>
  <script src="../js/chart.bundle.js"></script>
  <script type="text/javascript" src="<?php echo $host_path; ?>include/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
  <link rel="stylesheet" type="text/css" href="../include/xtable.css" media="screen" />
  <link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
  <link rel="stylesheet" type="text/css" href="http://phonepartsusa.com/catalog/view/theme/ppusa2.0/stylesheet/font-awesome.css">
  <title>Reason Classified Vouchers</title>

</head>
<body>
  <div style="display: none;" align="center">
      <?php include_once '../inc/header.php';?>
    </div>
    <h3 align="center"><?php echo $reason;?> Used Vouchers (<?php echo $_GET['date'];?>)</h3>

    <table width="100%" style="border: 0px solid #585858;"  align="center">
      <tbody>

        <tr>
          <td  valign="center" width="100%" >
              <table width="100%" class="tablesorter xtable" border="1" cellspacing="10" cellpadding="20" align="center">
                <thead>
                  <tr style="background: linear-gradient(#777, #444);color: #fff">
                    <th>#</th>
                    <th>Code</th>
                    <th>Reference</th>
                    <th>To</th>
                    <th>Total</th>
                    <!-- <th>Used</th> -->
                    <th>Balance</th>
                    

                    
                    <th>User</th>
                    <th>Source</th>
                    <th>PPUSA</th>
                    
                    <th>Date Used</th>
                  </tr>
                </thead>
                  <tbody>
                    <?php
                    $total_used=0.00;
                    foreach ($vouchers as $i => $voucher) { ?>
                    <?php
                    $voucher_detail = $db->func_query_first("SELECT * FROM inv_voucher_details WHERE voucher_id='".$voucher['voucher_id']."' ORDER BY id DESC");
                    $user_name='admin';
                    if ($voucher['user_id']) {
                      $user_name = get_username($voucher['user_id']);
                    }
                    if($voucher_detail['user_id'])
                    {
                      $user_name = get_username($voucher_detail['user_id']);
                    }

                    if($voucher_detail['oc_user_id'])
                    {
                      $user_name = $db->func_query_first_cell("SELECT username FROM oc_user WHERE user_id='".$voucher_detail['oc_user_id']."'");
                    }

                    ?>
                    <?php $balance = ((float) $voucher['amount']) + ((float) $db->func_query_first_cell("SELECT SUM(amount) FROM oc_voucher_history WHERE voucher_id='".$voucher['voucher_id']."'")); ?>
                    <tr>
                    <td><?= ($i) + 1 ?></td>
                      <td>
                        
                      <a target="_blank" href="<?= $host_path . 'vouchers_create.php?reason_id='.$voucher['reason_id'].'&edit=' . $voucher['voucher_id'] . '&status='.$voucher['status'];?>"><?= $voucher['code'];?></a>
                      </td>
                      <td>
                      <?php echo linkToOrder($voucher['order_id'],$host_path,'target="_blank"');?>
                      </td>
                      <td>
                        <?= linkToProfile($voucher['to_email'], $host_path,'','_blank');?>
                      </td>

                      <td>
                        $<?= number_format($voucher['amount'], 2);?>
                      </td>
                  
                      <!--  <td>
                       <?php
                       $total_used+=$voucher['used'];
                       ?>
                        $<?= number_format($voucher['used'], 2);?>
                      </td>  -->

                           <td>
                        $<?= number_format($balance, 2);?>
                      </td>

                      
                      <td>
                        <?= $user_name;?>
                      </td>
                      <td>
                        <?= ($voucher_detail['is_lbb'])? 'BuyBack': '';?>
                        <?= ($voucher_detail['is_rma'])? 'RMA': '';?>
                        <?= ($voucher_detail['is_order_cancellation'])? 'Cancellation': '';?>
                        <?= ($voucher_detail['is_pos'])? 'POS': '';?>
                        <?= ($voucher_detail['is_manual'])? 'Order(Item Removed)': '';
                        if ($voucher_detail['is_manual']) {
                          $sku = explode(',', $voucher_detail['item_detail']);?>
                          <br>
                          <?php echo $sku[0];
                        }
                        ?>
                      </td>
                      <td>
                        <?= ($voucher_detail['is_pos'])? 'YES': 'N/A';?>
                      </td>
                     
                      <td>
                        <?= americanDate($voucher['date_used']);?>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
              </table>
            
            </td>
          </tr>
      </tbody>
    </table>

  </body>
  <script type="text/javascript" src="../js/jquery.tablesorter.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function(e) {
     $(".tablesorter").tablesorter(); 
   });
  </script>
  </html>
