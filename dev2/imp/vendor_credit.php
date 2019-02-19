<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';

$pageLink = 'inventory_movement_report.php';

page_permission('inventory_movement_report');

if(!isset($_GET['hide_header']))
{
  unset($_SESSION['hide_header']);
}

// Getting Page information
if (isset($_GET['page'])) {
  $page = intval($_GET['page']);
}
if ($page < 1) {
  $page = 1;
}

 $start = (int)($page-1)*50;
  $end = 50;



$parameters = '&page='.$page;

$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
$parameters = str_replace('sort=' . $_GET['sort'], '', $parameters);
$parameters = str_replace('&order_by=' . $_GET['order_by'], '', $parameters);

$data = array();
$inv_query="SELECT b.name,b.id as vendor_id,sum(a.amount) as total_amount,max(a.date_added) as date_added FROM inv_users b left join inv_vendor_credit_data a on (a.vendor_id=b.id) where b.group_id=1  and b.status=1 group by b.id  order by b.name";

$rows = $db->func_query($inv_query);

// print_r($data);exit;data
  



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
  <title>Vendor Credit Page</title>
  <style>
  #summary table th{
    font-size:16px;
  }
  </style>
  <script>
   jQuery(document).ready(function () {
        // jQuery('.fancybox').fancybox({width: '400px', height: '200px', autoCenter: true, autoSize: false});
        jQuery('.fancybox2').fancybox({width: '500px', height: '500px', autoCenter: true, autoSize: false});
      });
  </script>
</head>

<body>
  <?php if (!$_SESSION['hide_header']) { ?>
  <div align="center">
    <?php } else { ?>
    <div style="display: none;" align="center">
      <?php } ?>
      <?php include_once 'inc/header.php';?>
    </div>
    <?php if(@$_SESSION['message']):?>
      <div align="center">
        <br />
        <font color="red">
          <?php echo $_SESSION['message']; unset($_SESSION['message']);?>
          <br />
        </font>
      </div>
    <?php endif;?>
    <h2 align="center">Vendor Credit Page</h2>
    <table width="96%">
      <tr>
        <td style="vertical-align: top;width: 25%" align="center" >
          
        </td>
        <td style="vertical-align: top;">
       <div style="text-align: right">
        <a href="<?php echo $host_path;?>/popupfiles/vendor_credit_reason.php" style="display:inline-block;margin-top:5px;margin-bottom:5px;margin-right:5px" class="button button-danger fancybox2 fancybox.iframe">Credit Reason</a>
       </div>   


        <table cellpadding="0" class="xtable" cellspacing="0" style="border:1px solid #ddd;clear:both">
        
        <tr>
         <th>Vendor</th>
        <th>Credit Available</th>
        <th>Last Updated</th>
        
        </tr>
        <tbody>
        <?php
        $total_credit = 0.00;
        foreach($rows as $row)
        {
          $total_credit+=$row['total_amount'];
          ?>
          <tr>
          <td><a href="<?php echo $host_path;?>vendor_ledger.php?vendor_id=<?php echo $row['vendor_id'];?>"><?php echo $row['name'];?></a></td>
          <td><?php echo '$'.number_format($row['total_amount'],2);?></td>
          <td><?php echo americanDate($row['date_added']);?></td>
         
          </tr>
          <?php
        }
        ?>
       
        </tbody>

        <tfoot>
                  <tr>
            
            <td>
              
              <strong>Total Vendor Credit:</strong>
            </td>
            <td><strong><?php echo '$'.number_format($total_credit,2);?></strong></td>
            <td></td>
          </tr>
          </tfoot>
     
        </table>

        </td>
      </tr>
    </table>
  </body>

  </html>
  <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
  