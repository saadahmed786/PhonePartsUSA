<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';

$pageLink = 'trial_balance.php';

//page_permission('trial_balance');

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

if(!isset($_GET['date']))
{
  $_GET['date'] = date('Y-m-d');
}

$parameters = '&page='.$page;

$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
$parameters = str_replace('sort=' . $_GET['sort'], '', $parameters);
$parameters = str_replace('&order_by=' . $_GET['order_by'], '', $parameters);

$data = array();
$inv_query="SELECT a.account_code,b.name,sum(a.debit)-sum(a.credit) as amount from inv_accounts_vouchers a,inv_charts b where a.account_code=b.main_code and date(a.date_added)<='".$_GET['date']."' group by a.account_code order by a.account_code";
// echo $inv_query;exit;
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
  <title>Trial Balance</title>
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
    <h2 align="center">Trial Balance</h2>
    <div align="center" style="font-weight: bold">As on <input type="date" name="date" value="<?php echo $_GET['date'];?>" onchange="window.location='<?php echo $host_path.$pageLink;?>?date='+this.value"></div>
    <table width="98%">
      <tr>
        
        <td style="vertical-align: top;" align="center">
      


        <table cellpadding="0" class="xtable" cellspacing="0" style="border:1px solid #ddd;clear:both" width="60%">
        
        <tr>
         <th>Code</th>
        <th>Account</th>
        <th>Debit</th>
        <th>Credit</th>
        <th>Action</th>
        
        </tr>
        <tbody>
        <?php
        $debit_balance = 0.00;
        $credit_balanace = 0.00;
        
        foreach($rows as $row)
        {

          if($row['amount']>0)
          {
            $debit_balance = $debit_balance + (float)$row['amount'];
          } 
          if($row['amount']<0)
          {
            $credit_balance = $credit_balance + ((float)$row['amount']*(-1)); 
          }
          
        ?>
        <tr>
        <td><?php echo $row['account_code'];?></td>
        <td><?php echo $row['name'];?></td>
        <td style="color:red"><?php echo ($row['amount']<0?'($'.number_format($row['amount']*(-1),2).')':'') ;?></td>
        <td><?php echo ($row['amount']>0?'$'.number_format($row['amount'],2):'') ;?></td>
        <td align="center">
        <a href="<?php echo $host_path;?>account_journal.php?code=<?php echo $row['account_code'];?>" data-tooltip="View Journal"><i class="fa fa-list fa-2x"></i></a>&nbsp;
        <a href="<?php echo $host_path;?>account_ledger.php?code=<?php echo $row['account_code'];?>" data-tooltip="View Ledger"><i class="fa fa-book fa-2x"></i></a>
        </td>
        

        </tr>
        <?php
      }
      ?>
       
        </tbody>

        <tfoot>
                  <tr>
            
            <td colspan="2">
              
              <strong>Total:</strong>
            </td>
            <td><strong><?php echo '$'.number_format($credit_balance,2);?></strong></td>
            <td><strong><?php echo '$'.number_format($debit_balance,2);?></strong></td>
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
  