<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';

$pageLink = 'vendor_ledger.php';

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

 $start = (int)($page-1)*500;
  $end = 500;



$parameters = '&page='.$page;

$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
$parameters = str_replace('sort=' . $_GET['sort'], '', $parameters);
$parameters = str_replace('&order_by=' . $_GET['order_by'], '', $parameters);


$vendor_id = (int)$_GET['vendor_id'];
$vendor_details = $db->func_query_first("SELECT name FROM inv_users WHERE id='".$vendor_id."' and status=1 and group_id=1");
if(!$vendor_id && !$vendor_details)
{
  echo 'Not a valid vendor or inactive, please contact admin';exit;
}
$inv_query="SELECT distinct * FROM inv_users b inner join inv_vendor_credit_data a on (a.vendor_id=b.id) where b.group_id=1  and b.status=1 and b.id='".$vendor_id."'  order by a.date_added";

 $splitPage = new splitPageResults($db, $inv_query, $end, $datapageLink, $page);
//Getting All Messages
$rows = $db->func_query($splitPage->sql_query);

$balance = 0.00;
$_rows = array();
$i=0;
        foreach($rows as $row)
        {
          $_rows[$i] = $row;
          $balance+=$row['amount'];
          $_rows[$i]['balance'] = $balance;

          $i++;
          }

if(isset($_GET['debug']))
{

print_r($_rows);;

  
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
  
  
  <title>Vendor Ledger</title>
  <style>
  #summary table th{
    font-size:16px;
  }
  </style>
  <script>

  jQuery(document).ready(function () {
        jQuery('.fancybox').fancybox({width: '400px', height: '200px', autoCenter: true, autoSize: false});
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
    <h2 align="center">Vendor Ledger - <?php echo $vendor_details['name'];?></h2>
    <?php
    if(isset($_SESSION['create_vendor_credit_manual']) and $_SESSION['create_vendor_credit_manual']==1)
    {
    ?>
    <div style="text-align: right;margin-right: 5%">

      


    <a href="<?php echo $host_path;?>/popupfiles/vendor_credit_manual.php?vendor_id=<?php echo $vendor_id;?>" style="display:inline-block;margin-top:5px;margin-bottom:5px" class="button fancybox2 fancybox.iframe">Manual Vendor Credit</a>
    </div>
    <?php
  }
  ?>
    <table width="96%">
      <tr>
        
        <td style="vertical-align: top;">
          


        <table cellpadding="0" class="xtable" cellspacing="0" style="border:1px solid #ddd;clear:both">
        <tr>
         <th>Date</th>
        <th>Addition</th>
        <th>Removal</th>
        <th>Balance</th>
        <th>Ref</th>
        <th>Description</th>
        <th>Type</th>
        <th>User</th>
        
        </tr>
        <tbody>
        <?php
        $balance = 0.00;
        // foreach($rows as $row)
        for($j=$i-1; $j>=0; $j--)
        {
          // $balance+=$row['amount'];
          ?>
          <tr>
          <td><?php echo americanDate($_rows[$j]['date_added']);?></td>
          <?php
          if($_rows[$j]['amount']<0)
          {
            ?>
              <td></td>
              <td style="color:red">(<?php echo '$'.number_format($_rows[$j]['amount'],2);?>)</td>
            <?php
          }
          else
          {
            ?>

             <td style="color:green"><?php echo '$'.number_format($_rows[$j]['amount'],2);?></td>
              <td></td>

              
             
            <?php
          }

          ?>

           <td><?php echo '$'.number_format($_rows[$j]['balance'],2);?></td>
           <td><?php echo ($_rows[$j]['vendor_po_id']?'<a href="'.$host_path.'vendor_po_view.php?vpo_id='.$_rows[$j]['vendor_po_id'].'">'.$db->func_query_first_cell("SELECT vendor_po_id FROM inv_vendor_po WHERE id='".$_rows[$j]['vendor_po_id']."'").'</a>':'N/A');?></td>
           <td><?php echo $_rows[$j]['comment'];?></td>
           <td><?php echo ($_rows[$j]['credit_reason_id'])?$db->func_query_first_cell("SELECT reason from inv_vendor_credit_reasons where id='".$_rows[$j]['credit_reason_id']."'"):'N/A';?>
          <td><?php echo get_username($_rows[$j]['user_id']);?></td>
         
          </tr>
          <?php
        }
        ?>


       
        </tbody>

       <tfoot>
                  <tr>
            
            <td colspan="11">
              <em><?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?></em>
              <div class="pagination" style="float:right">
                <?php echo $splitPage->display_links(10,$parameters);?>
              </div>
            </td>
          </tr>
          </tfoot>
     
        </table>

        </td>
      </tr>
    </table>
  </body>

  </html>
  <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
  