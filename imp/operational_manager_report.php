<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';

$pageLink = 'operational_manager_report.php';

page_permission('sales_dashboard');

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
$parameters = '&page='.$page;

$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);




$current_month= date('m');
$current_year = date('Y');
$last_month = date("m", strtotime("first day of previous month"));
$last_year = date("Y", strtotime("first day of previous month"));





  // echo getGenericQuery(20000,30000);exit;

$unpaid_orders = $cache->get('operational_manager_report.unpaid_orders');
if(!$unpaid_orders)
{

  $unpaid_orders = $db->func_query("SELECT distinct a.email, a.order_id,a.customer_name,a.order_status,a.sub_total+a.shipping_amount+a.tax as sub_total,a.order_price,a.paid_price FROM `inv_orders` a inner join inv_orders_details b on a.order_id=b.order_id left join inv_transactions c on a.order_id=b.order_id where a.transaction_id='' and b.payment_method not in('Replacement','Free Checkout') and a.payment_source<>'Replacement' and ( lower(b.payment_method) in ('paypal','paypal express','credit/debit card') and lower(a.order_status) in ('processed','completed','shipped') and c.order_id is null and b.shipping_method<>'Local Las Vegas Store Pickup - 9:30am-4:30pm' ) limit 100");
    
  $cache->set('operational_manager_report.unpaid_orders',$unpaid_orders);

}
  $html1 = '
  <strong>Unpaid Orders</strong>
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Order ID</th>
                          <th>Customer</th>
                          <th>Balance</th>
                          <th>Balance Due</th>
                          <th>Order Status</th>

                          ';


                         
                          
                          $html1.='
                          
                          
                      </tr>
                  </thead>
                  <tbody>';
                 foreach($unpaid_orders as $unpaid_order)
                 {
                  $html1.='<tr>';
                  $html1.='<td>'.linkToOrder($unpaid_order['order_id']).'</td>
                  <td>'.$unpaid_order['customer_name'].'<br>'.linkToProfile($unpaid_order['email']).'</td>
                  <td>$'.number_format($unpaid_order['sub_total'],2).'</td>
                  <td>$'.number_format($unpaid_order['sub_total']-$unpaid_order['paid_price'],2).'</td>
                  <td>'.$unpaid_order['order_status'].'</td>
                  </tr>
                  ';
                 }

                 $html1.=' </tbody>
                  
              </table>
';
// echo $html;exit;



 
  $start = (int)($page-1)*25;
  $end = 25;
  $date_start = $_GET['date_start'];
  $date_end = $_GET['date_end'];
  if(!$date_start)
  {
    $date_start = date('Y-m-d',strtotime('-7 days'));
    $date_end = date('Y-m-d');

  }



    $inv_query = ("select a.voucher_id, a.date_added,b.user_id,a.code,a.reason_id,a.amount,b.is_rma,b.is_lbb,b.is_order_cancellation,b.is_pos,b.is_manual from oc_voucher a,inv_voucher_details b where a.voucher_id=b.voucher_id and a.status=1  order by a.date_added desc ");


$splitPage = new splitPageResults($db, $inv_query, $end, $pageLink, $page);
//Getting All Messages
$rows = $db->func_query($splitPage->sql_query);


  $html2='<table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Date Issued</th>
                          <th>User Name</th>
                          <th>Code</th>
                          <th>Reason</th>
                          <th>Issued</th>
                          <th>Balance</th>
                          
                      </tr>
                  </thead>
                  <tbody>'; 
                  
                  foreach($rows as $row)
                  {

                   $voucher_reason = $db->func_query_first_cell("SELECT reason FROM inv_voucher_reasons where id='".$row['reason_id']."'");
                    if($voucher_reason=='')
                    {
                       if($row['is_rma'] or $row['is_pos'])
      {
        $voucher_reason= 'Return';
      }
      elseif($row['is_lbb'])
      {
        $voucher_reason= 'LBB';
      }
      elseif($row['is_order_cancellation'])
      {
        $voucher_reason= 'Order Cancellation';
      }
      elseif($row['is_pos'])
      {
          // $_rows2['POS']= $_rows2['POS']+($row['amount']*(-1));
          $voucher_reason= 'POS';
      }
      elseif($row['is_manual'])
      {
        $voucher_reason= 'Manual';
      }
      else
      {
        $voucher_reason= 'Not Defined';
      }

                    }
                    $balance = $db->func_query_first_cell("SELECT SUM(amount) FROM oc_voucher_history WHERE voucher_id='".$row['voucher_id']."'");
                    $html2.='<tr>';
                    $html2.='<td>'.americanDate($row['date_added']).'</td>';
                    
                    $html2.='<td>'.get_username($row['user_id']).'</td>';
                    $html2.='<td>'.linkToVoucher($row['voucher_id'],$host_path,$row['code'],'target="_blank"').'</td>';

                    $html2.='<td>'.$voucher_reason.'</td>';
                    $html2.='<td>$'.number_format($row['amount'],2).'</td>';
                    $html2.='<td>$'.number_format($row['amount']+$balance,2).'</td>';
                    $html2.'</tr>';
                  }

                  
                  $html2.='</tbody>
                  <tfoot>
                  <tr>
            
            <td colspan="11">
              <em>'.$splitPage->display_count("Displaying %s to %s of (%s)").'</em>
              <div class="pagination" style="float:right">
                '. $splitPage->display_links(10,$parameters).'
              </div>
            </td>
          </tr>
                  </table>';




    // $inv_query = ("select a.oc_user_id,a.sales_user,sum(b.price) as price,b.date_added,a.rma_number from inv_returns a, inv_return_decision b where a.id=b.return_id and b.action='Issue Refund' group by b.return_id order by b.id desc");
    $inv_query = ("select a.oc_user_id,a.sales_user,sum(b.price) as price,b.date_added,a.rma_number,c.item_condition,c.item_issue from inv_returns a, inv_return_decision b,inv_return_items c where a.id=b.return_id and a.id=c.return_id and b.sku=c.sku and b.action='Issue Refund' group by b.return_id order by b.id desc");


$splitPage = new splitPageResults($db, $inv_query, $end, $pageLink, $page);
//Getting All Messages
$rows = $db->func_query($splitPage->sql_query);
// print_r($rows);exit;

  $html3='<table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Date Issued</th>
                          <th>User Name</th>
                          <th>RMA #</th>
                          <th>Reason</th>
                          
                          <th>Amount</th>
                          
                          
                      </tr>
                  </thead>
                  <tbody>'; 
                  
                  foreach($rows as $row)
                  {
                    $username = 'N/A';
                    if($row['oc_user_id'])
                    {
                      $username = get_username($row['oc_user_id'],true);
                    }
                    elseif($row['sales_user'])
                    {
                      $username = get_username($row['oc_user_id']); 
                    }

                    // if($row['item_issue']=='')
                    // {
                    //   $reason = $row['item_condition'];
                    // }
                    // else
                    // {
                      $reason = $row['item_condition'];
                    // }
                   
                    $html3.='<tr>';
                    $html3.='<td>'.americanDate($row['date_added']).'</td>';
                    
                    $html3.='<td>'.$username.'</td>';
                    $html3.='<td>'.linkToRma($row['rma_number']).'</td>';
                    $html3.='<td>'.$reason.'</td>';
                    
                    $html3.='<td>$'.number_format($row['price'],2).'</td>';
                    
                    $html3.'</tr>';
                  }
                  
                  
                  $html3.='</tbody>
                  <tfoot>
                  <tr>
            
            <td colspan="11">
              <em>'.$splitPage->display_count("Displaying %s to %s of (%s)").'</em>
              <div class="pagination" style="float:right">
                '. $splitPage->display_links(10,$parameters).'
              </div>
            </td>
          </tr>
                  </tfoot>
                  </table>';






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
  <title>Operational Manager Report</title>

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
      <div align="center"><br />
        <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
      </div>
    <?php endif;?>
    
    <h2 align="center">Operational Manager Report</h2>
    <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
      <tbody>

        <tr>
          
          

          <td colspan="8" width="50%" valign="top">
          <div id="monthly_report" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">

          <?php echo $html1;?>
              </div>

          </td>
        </tr>
        <tr>
            <td colspan="8"><hr></td>
        </tr>
        <tr>
        <td colspan="4">
        <strong>Store Credit Issued</strong>

        <?php echo $html2;?>
       
        </td>
        <td colspan="4" valign="top">

<strong>Issue Refunds</strong>
<?php echo $html3;?>

        </td>

       
        </tr>
        <tr>
            <td colspan="8"><hr></td>
        </tr>



      </tbody>
    </table>

  </body>
  </html>
  <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
  <script>
  $(document).ready(function(){
//loadData('monthly_report');

  });

  function loadData(type)
  {
    $.ajax({
        url: 'sales_related_report.php',
        type: 'post',
        data: {type:type},
        dataType: 'html',
        beforeSend: function() {
          $('#'+type).html('<img src="images/loading.gif" height"100" width="100" />');
        },  
        complete: function() {
        },      
        success: function(html) {
          $('#'+type).html(html);


          if(type=='monthly_report')
          {

          loadData('80_sale');
          }

           if(type=='80_sale')
          {

          loadData('profitable_customers');
          }

           if(type=='profitable_customers')
          {

          loadData('least_profitable_customers');
          }

            if(type=='least_profitable_customers')
          {
            $('.xtable').tablesorter({
        textExtraction: function(node){ 
            // for numbers formattted like €1.000,50 e.g. Italian
            // return $(node).text().replace(/[.$£€]/g,'').replace(/,/g,'.');

            // for numbers formattted like $1,000.50 e.g. English
            return $(node).text().replace(/[,$£€]/g,'');
         }
    });
          //loadData('sales_agent');
          }
        }
      });
  }
  </script>