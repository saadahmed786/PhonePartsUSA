<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';
// echo 'here';exit;
$pageLink = 'account_journal.php';
$code = $_GET['code'];
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

if(!isset($_GET['start_date']))
{
  $_GET['start_date'] = date('Y-m-d',strtotime('-7 days'));
  $_GET['end_date'] = date('Y-m-d');

}

$parameters = '&page='.$page;

$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
$parameters = str_replace('sort=' . $_GET['sort'], '', $parameters);
$parameters = str_replace('&order_by=' . $_GET['order_by'], '', $parameters);

$data = array();
// $inv_query="SELECT a.account_code,b.name,sum(a.debit)-sum(a.credit) as amount from inv_accounts_vouchers a,inv_charts b where a.account_code=b.main_code and a.account_code='".$code."' and date(a.date_added) between '".$_GET['start_date']."' and '".$_GET['end_date']."' group by a.account_code having sum(a.debit)-sum(a.credit)<>0 order by a.date_added";
$inv_query="SELECT a.account_code,b.name,a.description,a.debit,a.credit,a.order_id,a.customer_email,a.user_id,a.date_added from inv_accounts_vouchers a,inv_charts b where a.account_code=b.main_code and a.account_code='".$code."' and date(a.date_added)>='".$_GET['start_date']."' and date(a.date_added) <= '".$_GET['end_date']."'  order by a.date_added";
// echo $inv_query;exit;
$rows = $db->func_query($inv_query);


    $data = array();
    $balance = $db->func_query_first_cell("SELECT sum(a.debit)-sum(a.credit) as amount from inv_accounts_vouchers a,inv_charts b where a.account_code=b.main_code and a.account_code='".$code."' and date(a.date_added) < '".$_GET['start_date']."'");

    $data[0]['date_added'] = date('Y-m-d H:i:s',strtotime('-1 day',strtotime($_GET['start_date'])));
    $data[0]['account_code'] = '';
    $data[0]['description'] = '<strong>Opening Balance</strong>';
    $data[0]['order_id']='';
    $data[0]['customer_email']='';
    $data[0]['user_id']=0;
    $data[0]['name'] = '<strong>Opening Balance</strong>';
    $data[0]['amount']=$balance;
    $data[0]['balance']=$balance;
    
    $i=1;
        foreach($rows as $row)
        {
          $data[$i] = $row;
          $balance+=($row['debit']-$row['credit']);
          $data[$i]['balance'] = $balance;

          $i++;
          }


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
  
  <title>Accounting Ledger</title>
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
    <h2 align="center">Accounting Ledger</h2>
    <div align="center" style="font-weight: bold">
    <form method="GET">
    From: <input type="date" name="start_date" value="<?php echo $_GET['start_date'];?>" > to  <input type="date" name="end_date" value="<?php echo $_GET['end_date'];?>" > 
    <input type="hidden" name="code" value="<?php echo $_GET['code'];?>">
    <input type="submit" value="Search" style="padding-top:3px;padding-bottom: 3px" class="button">

    </form>
    <table width="98%">
      <tr>
        
        <td style="vertical-align: top;" align="center">
      


        <table cellpadding="0" class="xtable" cellspacing="0" style="border:1px solid #ddd;clear:both" width="60%">
        
        <tr>
         <th>Date/Time</th>
        <th>Description</th>
        
        <th>Debit</th>
        <th>Credit</th>
        <th>Balance</th>
        <th>Ref</th>
        <th>User</th>
        
        </tr>
        <tbody>
        <?php
        
        
        for($j=$i-1; $j>=0; $j--)
        {

          
          
        ?>
        <tr>
        <td><?php echo americanDate($data[$j]['date_added']);?></td>
        <td><?php echo $data[$j]['description'];?></td>
        <td style="color:red"><?php echo ($data[$j]['credit']>0?'($'.number_format($data[$j]['credit'],2).')':'') ;?></td>
        <td><?php echo ($data[$j]['debit']>0?'$'.number_format($data[$j]['debit'],2):'') ;?></td>
        <td style="<?php echo ($data[$j]['balance']<0?'color:red':'');?>">
        <?php echo ($data[$j]['balance']<0?'($'.number_format($data[$j]['balance']*(-1),2).')':'$'.number_format($data[$j]['balance'])) ;?>
        </td>
        <td><?php echo linkToOrder($data[$j]['order_id']);?></td>
        <td><?php echo get_username($data[$j]['user_id']);?></td>
        

        </tr>
        <?php
      }
      ?>
       
        </tbody>

        <tfoot>
             
          </tfoot>
     
        </table>

        </td>
      </tr>
    </table>
  </body>

  </html>
  <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
  