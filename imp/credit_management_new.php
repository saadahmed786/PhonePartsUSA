<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';
page_permission('credit_management_report');
if(!isset($_GET['hide_header']))
{
  unset($_SESSION['hide_header']);
}

if(!isset($_GET['filter_transaction_start']) or $_GET['filter_transaction_start']=='' )
{
  $_GET['filter_transaction_start'] = date('Y-m-d');
  $_GET['filter_transaction_end'] = date('Y-m-d');
}

if(isset($_POST['action']) && $_POST['action']=='fetch_credits')
{
  $filter_date_start = $_POST['filter_date_start'];
  $filter_date_end = $_POST['filter_date_end'];
  $filter_voucher_code = $_POST['filter_voucher_code'];
  $filter_user = $_POST['filter_user'];
  if(!$filter_date_start)
  {
    $filter_date_start = date('Y-m-d',strtotime('-7 days'));
    $filter_date_end = date('Y-m-d');
  }
  else
  {
        $filter_date_start = date('Y-m-d',strtotime($filter_date_start));
    $filter_date_end = date('Y-m-d',strtotime($filter_date_end));
  }

    $where = array();
    $where[] = '1 = 1';

    if($filter_voucher_code)
    {
      $where[] = "b.code='".$db->func_escape_string($filter_voucher_code)."'";
    }

    if($filter_user)
    {
      $where[] = "a.user_id='".(int)$filter_user."'";
    }

    $where_query = implode(" AND ", $where);

    $html='';

      // echo "SELECT a.* FROM inv_vouchers a,oc_voucher b where a.voucher_id=b.voucher_id and $where_query  and a.method='store_credit' and date(a.date_added) between '$filter_date_start' and '$filter_date_end'";exit;
    $credit_data = $db->func_query("SELECT a.* FROM inv_vouchers a,oc_voucher b where a.voucher_id=b.voucher_id and $where_query  and a.method='store_credit' and date(a.date_added) between '$filter_date_start' and '$filter_date_end'");
    $_credit = array();
    $balance = $db->func_query_first_cell("SELECT sum(a.amount) FROM inv_vouchers a,oc_voucher b where a.voucher_id=b.voucher_id and a.method='store_credit' and $where_query and date(a.date_added)<'$filter_date_start'");

    $_credit[0]['date_added'] = date('Y-m-d H:i:s',$filter_date_start);
    $_credit[0]['description'] = 'Opening Balance';
    $_credit[0]['amount']=$balance;
    $_credit[0]['balance']=$balance;
    $_credit[0]['voucher_id']=0;
    $_credit[0]['order_id']=0;
    $_credit[0]['reference']='';
    $_credit[0]['user_id']=0;
    $i=1;
        foreach($credit_data as $row)
        {
          $_credit[$i] = $row;
          $balance+=$row['amount'];
          $_credit[$i]['balance'] = $balance;

          $i++;
          }

    
    $html.='
    <br>
      <table width="50%" class="xtable" cellpadding="10" style="border: 0px solid #585858;"  border="1" align="center">
          <tr>
              <td><strong>Date Start:</strong></td>
              <td><input type="date" id="filter_date_start"   value="'.$filter_date_start.'"></td>
              <td><strong>Date End:</strong></td>
              <td><input type="date" id="filter_date_end" value="'.$filter_date_end.'"></td>
              <td><strong>Code:</strong></td>
              <td><input type="text" id="filter_voucher_code" value="'.$filter_voucher_code.'"></td>

              <td><strong>User:</strong></td>
              <td><select id="filter_user">
                  <option value="">Please Select</option>';

                  $users= $db->func_query("SELECT * from inv_users where status=1 and group_id<>1 order by lower(name) asc");
                  foreach($users as $user)
                  {
                      $html.='<option value="'.$user['id'].'" '.($user['id']==$filter_user?'selected':'').'>'.$user['name'].'</option>';
                  }
              $html.='</select></td>
          </tr>
          <tr>
              <td colspan="8" align="center"><input type="button" class="button" value="Filter" onClick="fetch_credits();"></td>
          </tr>

      </table>
    <table width="98%" class="xtable" cellpadding="10" style="border: 0px solid #585858;"  align="center">
      <thead>
          <tr>
          <th>Date/Time</th>
          <th>Description</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Balance</th>
          <th>Voucher</th>
          <th>Ref</th>
          <th>User</th>

          </tr>

      </thead>
      <tbody>';
      
      for($j=$i-1; $j>=0; $j--)
      {


       
       $html.='<tr>
        <td>'.americanDate($_credit[$j]['date_added']).'</td>
        <td>'.$_credit[$j]['description'].'</td>';
        
          if($_credit[$j]['amount']<=0)
          {
            
              $html.='<td style="color:green">$'.number_format($_credit[$j]['amount']*(-1),2).'</td>
              <td></td>';
            
          }
          else
          {
            
              $html.='<td></td>
              <td style="color:red">($'.number_format($_credit[$j]['amount'],2).')</td>';
            
          }
        
        if($_credit[$j]['balance']<0)
        {
          
          $html.='<td style="color:green">$'.number_format($_credit[$j]['balance']*(-1),2).'</td>';
          
        }
        else
        {
          
          $html.='<td style="color:red">($'.number_format($_credit[$j]['balance'],2).')</td>';

          
        }
        
        $html.='<td>'.$_credit[$j]['reference'].'</td>';
        
        if($_credit[$j]['order_id']=='')
        {
          $details = $db->func_query_first("SELECT order_id,rma_number,is_lbb,is_rma from inv_voucher_details where voucher_id='".$_credit[$j]['voucher_id']."'");
          $_type='';
          if($details['is_rma'])
          {
            $_type = linkToRma($details['rma_number'], $host_path);
          }
          elseif($details['is_lbb'])
          {
            $_type = linkToLbbShipment($details['order_id'], $host_path);
          }
          else
          {
           $_type = linkToOrder($details['order_id'], $host_path);

          }
         
          $html.='<td>'.$_type.'</td>';

        }
        else
        {
          
          $html.='<td>'.linkToOrder($_credit[$j]['order_id'], $host_path).'</td>';

          
        }
        
        $html.='<td>'.get_username($_credit[$j]['user_id']).'</td>
        </tr>';
        
      }
      
       $html.='</tbody>
        </table>';

        echo $html;


exit;
}
if(isset($_POST['action']) && $_POST['action']=='fetch_vouchers')
{
  $type = $_POST['type'];
  $page = (int)$_POST['page'];
  if(!$page) $page = 1;
  $limit = 50;
  $start = ($page-1)*$limit;
  $end = ($page*$limit);


 
if($_POST['filter_transaction_start']==$_POST['filter_transaction_end'] and $_POST['filter_transaction_start']==date('Y-m-d'))
{
  $order_date_where='';
}
else
{
  $order_date_where ="  AND DATE(date_added) BETWEEN '".$_POST['filter_transaction_start']."' AND '".$_POST['filter_transaction_end']."'";
}


  $html='<table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
      <tbody>

        <tr>
          
          

          <td colspan="5" width="60%" valign="top">

          <form id="deposit_to_frm">
 <div style="text-align: left;margin-top:15px;margin-left:15px">

 <select name="deposit_id" style="padding:8px 11px">
 <option value="">Select Deposit #</option>';
 
 $open_deposits = $db->func_query("SELECT * FROM inv_deposits WHERE deposit_type='".$type."' and status='open' order by deposit_date desc");
 foreach($open_deposits as $open_deposit)
 {
  
  $html.='<option value="'.$open_deposit['deposit_id'].'">'.$open_deposit['name'].'</option>';
}

  

$html.='</select>
  &nbsp;<input type="button" class="button" value="Add to Deposit" onclick="addToDeposit(\''.$type.'\');"></div>
           <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            <tr>
                <td colspan="8" align="center">Date: <input type="date" data-type="date" style="padding:8px 11px" id="filter_transaction_start" value="'.$_GET['filter_transaction_start'].'"> to <input type="date" data-type="date" style="padding:8px 11px" class="" id="filter_transaction_end" value="'.$_GET['filter_transaction_end'].'">
                  <input type="button" value="Find" onclick="" class="button">
                </td>
            </tr>
            <tr>

            <td colspan="2" style="font-size:40px;font-weight: bold" align="center" class="undeposited_amount">0.00</td>
            <td colspan="6" align="center"><h3>Undeposited '.map_payment_method($type).' Transactions</h3><small><i>Undeposited Amount: $'.

            
             number_format($db->func_query_first_cell("SELECT SUM(amount) as net_amount FROM inv_vouchers WHERE method='".$type."' and deposit_id=0")  ,2).'</i></small></td>
            </tr>

            </table>

            <table width="90%" cellpadding="0" cellspacing="0" class="xtable" id="undeposited_table">
            
            <thead>
            <tr>
            <td align="center"><input type="checkbox" onchange="checkAll(this);"></td>
            <th>Date Rec.</th>
            
            <th>Ref ID</th>
            <th>Sender/Recipient</th>
            <th colspan="3">Amt Received/Sent</th>
            </tr> 
            <tr>
            <th colspan="4"></th>
            <th>Gross</th>
            <th>Fee</th>
            <th>Net</th>
            </tr>
            </thead>
            <tbody>
            ';
           $rows = $db->func_query("SELECT * FROM inv_vouchers WHERE method='".$type."' $order_date_where order by date_added desc limit $start,$end");
            foreach($rows as $row)
            {
              $customer_email = $db->func_query_first_cell("SELECT email from inv_orders where order_id='".$row['order_id']."'");
              
              $html.='<tr class="list_items">
              <td><input type="checkbox" name="transaction_id[]" class="undeposited_checkbox" value="'.$row['id'].'" data-value="'.$row['amount'].'">

              <input type="hidden" name="net_amount['.$row['order_id'].']" value="'.$row['amount'].'">
              </td>
              <td>'.americanDate($row['date_added']).' </td>
              
              <td>'.($row['order_id']?linkToOrder($row['order_id']):$row['order_id']).'</td>
              <td>'.linkToProfile($customer_email) .($row['amount']<0?' <span style="color:red;font-weight:bold;font-size:30px">&rarr;</span>':' <span style="color:green;font-weight:bold;font-size:30px">&larr;</span>').'</td>
              <td>$'.number_format($row['amount'],2).'</td>
              <td>$'.number_format($row['fee'],2).'</td>
              <td>$'.number_format($row['amount']+$row['fee'],2).'</td>
              </tr>';
            }
            $html.='
            </tbody>

        </tfoot>

            </table>
            </form>


          </td>

          <td colspan="3" width="40%" valign="top" >
          <div style="text-align: right;margin-top:15px;margin-right:15px"><a class="fancybox2 fancybox.iframe button" href="'. $host_path.'popupfiles/add_bank_deposit.php?type='.$type.'">Add '.map_payment_method($type).' Deposit</a></div>
  <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            <tr>
            
            <td colspan="6" align="center"><h3>'.map_payment_method($type).' Deposits</h3></td>
            </tr>

            </table>

            <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            
            <thead>
            <tr>
            <th>Date</th>
            <th>Deposit #</th>
            <th># of Trans</th>
            <th>Diff</th>
            <th>Status</th>
            </tr> 
            
            </thead>
            <tbody>';
            $deposits = $db->func_query("SELECT * FROM inv_deposits where deposit_type='".$type."' group by name ORDER BY deposit_date desc");
            foreach($deposits as $deposit)
            {
              $diff = $db->func_query_first_cell("SELECT SUM(net_amount) from inv_orders where deposit_id='".$deposit['deposit_id']."'");
              
              $html.='<tr>
              <td>'.date('m/d/Y',strtotime($deposit['deposit_date'])).'</td>
              <td><a class="fancyboxXZ fancybox.iframe" href="'.$host_path.'popupfiles/deposit_transactions.php?deposit_id='.$deposit['deposit_id'].'&deposit_type='.$type.'">'.$deposit['name'].'</a></td>
              <td>'.$db->func_query_first_cell("SELECT COUNT(*) from inv_vouchers where deposit_id='".$deposit['deposit_id']."'").'</td>
              <td>$'.
                number_format( (float)$deposit['amount']-(float)$diff ,2).'</td>
              <td><span class="tag '.($deposit['status']=='closed'?'red':'blue').'-bg">'.$deposit['status'].'</span></td>

              </tr>';
              
            }
            
            $html.='</tbody>
            </table>
          </td>

          </tr>
          <tr>
          <td colspan="8">

              <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            <tr>
            
            <td colspan="8" align="center"><h3>Deposited Funds</h3></td>
            </tr>

            </table>
            <form id="deposited_cash_frm">
            <div align="center" style="font-weight:bold">Ref ID: <input type="text" style="padding:8px 11px" name="filter_transaction_id" value=""> Deposit Date: <input type="text" data-type="date" style="padding:8px 11px" name="filter_deposit_start"> to <input type="text" data-type="date" style="padding:8px 11px" name="filter_deposit_end"> Received Date: <input type="text" data-type="date" style="padding:8px 11px" name="filter_received_start"> to <input type="text" data-type="date" style="padding:8px 11px" name="filter_received_end"><br><br><input type="button" value="Find" onclick="depositedFunds(\''.$type.'\');" class="button"></div>
            <input type="hidden" name="action" value="load_funds">

            </form>


            <table width="90%" cellpadding="0" cellspacing="0" class="xtable" id="deposited_table">
            
            <thead>
            <tr>
            <th>Date</th>
            <th>Deposit #</th>
            <th>Received Date</th>
            <th>Ref ID</th>
            <th>Sender/Recipient</th>
            <th>Amount</th>
            </tr> 
           
            
            </thead>
            <tbody>

            </tbody>
            </table>



          </td>
          </tr>
          </tbody>
          </table>';
          echo $html;exit;
}



if(isset($_POST['deposit_id']))
{
  $json = array();
  $transaction_ids = $_POST['transaction_id'];
  if(empty($transaction_ids))
  {
    $json['error']='Please select Payment Transactions with this Deposit #';
  }
  else
  {
    $total = 0.00;
    foreach($transaction_ids as $transaction_id)
    {
      
      $total+=(float)$db->func_query_first_cell("SELECT net_amount from inv_transactions where id='".$transaction_id."'");
      $db->db_exec("UPDATE inv_transactions SET deposit_id='".(int)$_POST['deposit_id']."',deposit_date='".date("Y-m-d H:i:s")."',deposited_by='".$_SESSION['user_id']."' where id='".$transaction_id."'");


      // for cash, bankwire, behalf and orders
      //$order_data = $db->func_query_first("SELECT a.order_date,a.order_id,a.email,(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount,(select COALESCE(sum(value),0) from oc_order_total d where cast(d.order_id as char(50))=a.order_id and d.code='business_fee' ) as payment_fee,b.payment_method FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and a.order_id='".$transaction_id."'");
      $net_amount = $_POST['net_amount'][$transaction_id];
      $order_data['net_amount'] = (float)$net_amount;
      if(isset($_POST['remove_tax']))
      {
        if((float)$order_data['net_amount']<0)
        {
        $db->db_exec("UPDATE inv_orders SET tax_deposit_id='".(int)$_POST['deposit_id']."', tax_deposited_date='".date("Y-m-d")."', tax_deposited_by='".$_SESSION['user_id']."',deposited_tax_amount='".$order_data['net_amount']."' where order_id='".$transaction_id."'");
          
        }
        else
        {
           $db->db_exec("UPDATE inv_orders SET refund_tax_deposit_id='".(int)$_POST['deposit_id']."', refund_tax_deposited_date='".date("Y-m-d")."', refund_tax_deposited_by='".$_SESSION['user_id']."',refund_deposited_tax_amount='".$order_data['net_amount']."' where order_id='".$transaction_id."'");
        }
      }
      else
      {

      $db->db_exec("UPDATE inv_orders SET deposit_id='".(int)$_POST['deposit_id']."',gross_amount='".$order_data['net_amount']."',net_amount='".$order_data['net_amount']."', deposited_date='".date("Y-m-d")."', deposited_by='".$_SESSION['user_id']."' where order_id='".$transaction_id."'");
      }

    }
    // echo $total;exit;
    // $db->db_exec("UPDATE inv_deposits set amount=amount+".(float)$total." where deposit_id='".(int)$_POST['deposit_id']."'");

    $json['success'] = 'Transactions are mapped against the Deposit No. successfully';

  }
  echo json_encode($json);exit;
}

$parameters = '&page='.$page;

if (isset($_GET['page'])) {
  $page = intval($_GET['page']);
}
if ($page < 1) {
  $page = 1;
}

$max_page_links = 10;
$num_rows = 20;
if(!isset($_GET['filter_transaction_start']) or $_GET['filter_transaction_start']=='' )
{
  $_GET['filter_transaction_start'] = date('Y-m-d');
  $_GET['filter_transaction_end'] = date('Y-m-d');
}

if($_GET['filter_transaction_start']==$_GET['filter_transaction_end'] and $_GET['filter_transaction_start']==date('Y-m-d'))
{
  $order_date_where='';
}
else
{
  $order_date_where ="  AND DATE(a.order_date) BETWEEN '".$_GET['filter_transaction_start']."' AND '".$_GET['filter_transaction_end']."'";
}

$start = ($page - 1) * $num_rows;
 $inv_query = ("SELECT * FROM inv_transactions WHERE payment_status in('Completed','Refunded') AND DATE(order_date) BETWEEN '".$_GET['filter_transaction_start']."' AND '".$_GET['filter_transaction_end']."'  and order_status='Completed' and deposit_id=0 ORDER BY `order_date` desc");

$splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);

// echo $page;exit;

// echo $splitPage->sql_query;exit;
$rows = $db->func_query($splitPage->sql_query);

// cash query
 // $inv_query = ("SELECT a.order_date,a.order_id,a.email,(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount,(select sum(value) from oc_order_total d where d.order_id=a.order_id and d.code='business_fee' ) as payment_fee,b.payment_method,e.payment_method,e.cash_split FROM inv_orders a,inv_orders_details b,oc_order e WHERE a.order_id=b.order_id and e.order_id=a.order_id and lower(left(e.payment_method,4))='cash' and lower(left(b.payment_method,4))='cash' AND DATE(a.order_date) BETWEEN '".$_GET['filter_transaction_start']."' AND '".$_GET['filter_transaction_end']."' and a.gross_amount>0 and a.deposit_id=0 and a.undeposited_by>0 ORDER BY a.`order_date` desc");
 // $inv_query = ("SELECT a.undeposited_date, a.order_date,a.order_id,a.email,a.paid_price as net_amount,b.payment_method,a.order_status,(select pos_date from oc_order c where a.order_id=c.order_id) as date_received FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and lower(a.order_status)<>'canceled' and (lower(a.order_status)='shipped' or a.gross_amount>0) $order_date_where and lower(left(b.payment_method,4))='cash' and a.paid_price>0 ORDER BY a.`order_date` desc");
 $inv_query = ("SELECT * from
(
SELECT a.undeposited_date, a.order_date,a.order_id,a.email,a.paid_price as net_amount,b.payment_method,a.order_status,(select pos_date from oc_order c where a.order_id=c.order_id) as date_received FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and lower(a.order_status)<>'canceled' and (lower(a.order_status)='shipped' or a.gross_amount>0)  and lower(left(b.payment_method,4))='cash' and a.paid_price>0
union ALL
select '' as undeposited_date,date_added as order_date,order_id,email,(cash_paid-change_due) as net_amount,'Cash or Credit at Store Pick-Up' as payment_method,'Shipped' as order_status,pos_date as date_received from oc_order where order_status_id=3 and payment_method='PayPal' and cash_paid>0.00 and paypal_paid>0.00
) a
 WHERE 1=1 $order_date_where
order by 2 desc");
// echo $inv_query;exit;


$splitPage2 = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);


$cashs = $db->func_query($splitPage2->sql_query);


$inv_query = ("SELECT * from ( SELECT a.undeposited_date, a.order_date,a.order_id,a.email,a.tax*(-1) as net_amount,a.tax*(-1) as gross_amount,b.payment_method,a.order_status,(select pos_date from oc_order c where a.order_id=c.order_id) as date_received FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and lower(a.order_status)<>'canceled' and a.tax>0 and refund_tax=0 and a.paid_price>0 and (a.tax_deposit_id=0 or a.refund_tax_deposit_id=0)

  union ALL
  SELECT a.undeposited_date, a.order_date,a.order_id,a.email,a.refund_tax*(-1) as net_amount,a.refund_tax*(-1) as gross_amount,b.payment_method,a.order_status,(select pos_date from oc_order c where a.order_id=c.order_id) as date_received FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and lower(a.order_status)<>'canceled' and a.tax=0 and refund_tax>0 and a.paid_price>0 and (a.tax_deposit_id=0 or a.refund_tax_deposit_id=0)


  union ALL SELECT a.undeposited_date, a.refund_tax_date as order_date,a.order_id,a.email,a.refund_tax as net_amount,a.refund_tax as gross_amount,b.payment_method,a.order_status,(select pos_date from oc_order c where a.order_id=c.order_id) as date_received FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and lower(a.order_status)<>'canceled' and refund_tax>0 and a.paid_price>0 and (a.tax_deposit_id=0 or a.refund_tax_deposit_id=0)


  ) a WHERE 1=1 $order_date_where   order by 2 desc");
// echo $inv_query;exit;


$splitPage_tax = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);


$taxes = $db->func_query($splitPage_tax->sql_query);




// behalf query
// cash query
 $inv_query = ("SELECT a.order_date,a.order_id,a.email,a.gross_amount,a.payment_fee,a.net_amount,b.payment_method FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and lower(a.order_status)<>'canceled' and lower(left(b.payment_method,6))='behalf' $order_date_where and a.gross_amount>0 and a.deposit_id=0 and a.undeposited_by>0 ORDER BY a.`undeposited_date` desc");
// echo $inv_query;exit;


$splitPage3 = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);


$behalfs = $db->func_query($splitPage3->sql_query);



// behalf query
// cash query
 $inv_query = ("SELECT a.order_date,a.order_id,a.email,a.gross_amount,a.payment_fee,a.net_amount,b.payment_method FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and lower(a.order_status)<>'canceled' and lower(left(b.payment_method,5))='check' $order_date_where and a.gross_amount>0 and a.deposit_id=0 and a.undeposited_by>0 ORDER BY a.`undeposited_date` desc");
// echo $inv_query;exit;


$splitPage4 = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);


$checks = $db->func_query($splitPage4->sql_query);


 $inv_query = ("SELECT a.order_date,a.order_id,a.email,a.gross_amount,a.payment_fee,a.net_amount,b.payment_method FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and lower(a.order_status)<>'canceled' and lower(left(b.payment_method,4)) in ('bank','wire') $order_date_where and a.gross_amount>0 and a.deposit_id=0 and a.undeposited_by>0 ORDER BY a.`undeposited_date` desc");
// echo $inv_query;exit;


$splitPage5 = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);


$wires = $db->func_query($splitPage5->sql_query);


 /*$inv_query = ("SELECT 
  * 
FROM
  (SELECT 
    a.order_date,
    a.order_id,
    a.email,
    a.gross_amount,
    a.payment_fee,
    a.net_amount,
    b.payment_method,
    a.deposit_id,
   COALESCE(a.payment_source,'') as payment_source
  FROM
    inv_orders a,
    inv_orders_details b 
  WHERE a.order_id = b.order_id 
    AND LOWER(LEFT(b.payment_method, 4)) IN ('card') 
    AND a.gross_amount > 0 
    AND a.deposit_id = 0 
    AND a.undeposited_by > 0 
    AND LOWER(a.order_status) <> 'canceled' 
    
  UNION
  ALL 
  SELECT 
    a.pos_date AS order_date,
    a.order_id,
    a.email,
    a.total AS gross_amount,
    0.00,
    a.total AS net_amount,
    a.payment_method,
    b.deposit_id ,
   COALESCE(b.payment_source,'') as payment_source
  FROM
    oc_order a,
    inv_orders b 
  WHERE a.order_id = b.order_id 
    and LOWER(a.payment_method) IN ('card') 
    AND a.order_status_id <> 7 
    
  UNION
  ALL 
  SELECT 
    a.pos_date AS order_date,
    a.order_id,
    a.email,
    a.card_paid AS gross_amount,
    0.00,
    a.card_paid AS net_amount,
    a.payment_method,
    b.deposit_id,
    COALESCE(b.payment_source,'') as payment_source

  FROM
    oc_order a,
    inv_orders b 
  WHERE a.order_id = b.order_id 
    and LOWER(a.payment_method) IN ('cash,card') 
    AND a.order_status_id <> 7
   

    ) a 
WHERE gross_amount > 0 
  and deposit_id = 0
  and lower(payment_source)<>'replacement'
  $order_date_where
 
ORDER BY order_date DESC ");*/
// echo $inv_query;exit;


$inv_query = ("SELECT 
  * 
FROM
  (SELECT 
    a.order_date,
    a.order_id,
    a.email,
    a.gross_amount,
    a.payment_fee,
    a.net_amount,
    b.payment_method,
    a.deposit_id,
   COALESCE(a.payment_source,'') as payment_source
  FROM
    inv_orders a,
    inv_orders_details b 
  WHERE a.order_id = b.order_id 
    AND LOWER(LEFT(b.payment_method, 4)) IN ('card') 
    AND a.gross_amount > 0 
    AND a.deposit_id = 0 
    AND a.undeposited_by > 0 
    AND LOWER(a.order_status) <> 'canceled' 
    
  UNION
  ALL 
  SELECT 
    a.pos_date AS order_date,
    a.order_id,
    a.email,
    a.total AS gross_amount,
    0.00,
    a.total AS net_amount,
    a.payment_method,
    b.deposit_id ,
   COALESCE(b.payment_source,'') as payment_source
  FROM
    oc_order a,
    inv_orders b 
  WHERE a.order_id = b.order_id 
    and LOWER(a.payment_method) IN ('card') 
    AND a.order_status_id <> 7 
    
  UNION
  ALL 
  SELECT 
    a.pos_date AS order_date,
    a.order_id,
    a.email,
    a.card_paid AS gross_amount,
    0.00,
    a.card_paid AS net_amount,
    a.payment_method,
    b.deposit_id,
    COALESCE(b.payment_source,'') as payment_source

  FROM
    oc_order a,
    inv_orders b 
  WHERE a.order_id = b.order_id 
    and LOWER(a.payment_method) IN ('cash,card') 
    AND a.order_status_id <> 7
   
   
   UNION
  ALL 
  SELECT 
    a.pos_date AS order_date,
    a.order_id,
    a.email,
    a.card_paid AS gross_amount,
    0.00,
    a.card_paid AS net_amount,
    a.payment_method,
    b.deposit_id ,
   COALESCE(b.payment_source,'') as payment_source
  FROM
    oc_order a,
    inv_orders b 
  WHERE a.order_id = b.order_id 
    and LOWER(a.payment_method) IN ('paypal') 
    AND a.order_status_id = '3' 
    AND paypal_paid>0
    AND card_paid>0
   
   

    ) a 
WHERE gross_amount > 0 
  and deposit_id = 0
  and lower(payment_source)<>'replacement'
  $order_date_where
 
ORDER BY order_date DESC");

if(isset($_GET['debug']))
{
  echo $inv_query;
}
$splitPage6 = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);


$cards = $db->func_query($splitPage6->sql_query);


if(isset($_POST['type']) && $_POST['type']=='balance_due')
{

$result2 =   $cache->get('credit_management.balance_due');
if(!$result2)
{
   /*$result2 = $db->func_query("SELECT 

  a.id,
  b.first_name,
  b.last_name,
  a.email,
  a.order_id,
  a.order_status,
  
  a.sub_total + a.tax + a.shipping_amount AS order_price,
  a.paid_price,
  (SELECT 
    COALESCE(SUM(c.amount), 0) * (- 1) 
  FROM
    oc_voucher_history c 
  WHERE a.order_id = c.order_id) AS vouchers_amount,
  (SELECT 
    COALESCE(SUM(k.amount), 0) 
  FROM
    inv_transactions k 
  WHERE k.order_id = a.order_id) AS mapped_amount 
FROM
  inv_orders a,
  inv_orders_details b 
WHERE a.order_id = b.order_id 
  AND b.payment_method NOT IN (
    'Cash or Credit at Store Pick-Up',
    'Behalf',
    'Check',
    'Wire Transfer'
  ) 
  AND b.shipping_method <> 'Local Las Vegas Store Pickup - 9:30am-4:30pm' 
  AND LOWER(a.order_status) IN (
    'processed',
    'shipped',
    'on hold',
    'completed'
  ) 
  AND (
    (
      a.sub_total + a.tax + a.shipping_amount
    ) - (
      a.paid_price + 
      (SELECT 
        COALESCE(SUM(c.amount) * (- 1), 0) 
      FROM
        oc_voucher_history c 
      WHERE a.order_id = c.order_id)
    )
  ) > 0 
  
  and ((
    a.sub_total + a.tax + a.shipping_amount
  ) - (
    (select 
      COALESCE(sum(k.amount), 0) 
    from
      inv_transactions k 
    where a.order_id = k.order_id) + 
    (select 
      COALESCE(sum(c.amount) * (- 1), 0) 
    from
      oc_voucher_history c 
    where a.order_id = c.order_id)
  )) > 0
   
  AND a.store_type IN ('web', 'po_business') 
  AND COALESCE(a.payment_source, '') <> 'Replacement' 
  AND DATE(a.order_date) >= '".date('Y-m-d',strtotime('-6 month'))."' 
ORDER BY 1 DESC");*/

$result2 = $db->func_query("SELECT 

  a.id,
  b.first_name,
  b.last_name,
  a.email,
  a.order_id,
  a.order_status,
  
  a.sub_total + a.tax + a.shipping_amount AS order_price,
  a.paid_price,
  (SELECT 
    COALESCE(SUM(c.amount), 0) * (- 1) 
  FROM
    oc_voucher_history c 
  WHERE a.order_id = c.order_id) AS vouchers_amount,
  (SELECT 
    COALESCE(SUM(k.amount), 0) 
  FROM
    inv_transactions k 
  WHERE k.order_id = a.order_id) AS mapped_amount 
FROM
  inv_orders a,
  inv_orders_details b 
WHERE a.order_id = b.order_id 
  
  AND b.shipping_method <> 'Local Las Vegas Store Pickup - 9:30am-4:30pm' 
  AND LOWER(a.order_status) IN (
    'processed',
    'shipped',
    'on hold',
    'completed'
  ) 
  AND (
    (
      a.sub_total + a.tax + a.shipping_amount
    ) - (
      a.paid_price + 
      (SELECT 
        COALESCE(SUM(c.amount) * (- 1), 0) 
      FROM
        oc_voucher_history c 
      WHERE a.order_id = c.order_id)
    )
  ) > 0 
  
  and ((
    a.sub_total + a.tax + a.shipping_amount
  ) - (
    (select 
      COALESCE(sum(k.amount), 0) 
    from
      inv_transactions k 
    where a.order_id = k.order_id) + 
    (select 
      COALESCE(sum(c.amount) * (- 1), 0) 
    from
      oc_voucher_history c 
    where a.order_id = c.order_id)
  )) > 0
   
  AND a.store_type IN ('web', 'po_business') 
  AND COALESCE(a.payment_source, '') <> 'Replacement' 
  AND DATE(a.order_date) >= '".date('Y-m-d',strtotime('-6 month'))."' 
ORDER BY 1 DESC");
  $cache->set('credit_management.balance_due',$result2);
}

// testObject($result2);

  $html = '
  <strong>Balance Due</strong>
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Order ID</th>
                          <th>Customer</th>
                          <th>Status</th>
                          <th>Balance</th>
                          

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  foreach($result2 as $result)
                  {
                   
                    if(round($result['paid_price']+$result['vouchers_amount'])==round($result['order_price']))
                    {
                      continue;
                    }

                     if($result['paid_price']<0.00)
                    {
                      $result['paid_price'] = $result['paid_price']*(-1);
                    }

                    if(round($result['paid_price']+$result['mapped_amount'])==round($result['order_price']))
                    {
                      continue;
                    }

                      $balance = $result['order_price'] - ($result['paid_price']+$result['vouchers_amount']);

                    $html.='<tr>
                    <td>'.linkToOrder($result['order_id'],$host_path).'</td>
                    <td>'.linkToProfile($result['email'],$host_path).'</td>
                    <td>'.$result['order_status'].'</td>
                    <td>$'.number_format($balance,2).'</td>
                    
                    </tr>
                    ';
                  }
                

                 $html.=' </tbody>
                  
              </table>
';
echo $html;exit;
}


if(isset($_POST['type']) && $_POST['type']=='overpaid')
{

$result2 =   $cache->get('credit_management.overpaid');
if(!$result2)
{
   $result2 = $db->func_query("SELECT 
  a.id,
  b.first_name,
  b.last_name,
  a.email,
  a.order_id,
  a.order_status,
  
  a.sub_total + a.tax + a.shipping_amount AS order_price,
  a.paid_price,
  (SELECT 
    COALESCE(SUM(c.amount), 0) * (- 1) 
  FROM
    oc_voucher_history c 
  WHERE a.order_id = c.order_id) AS vouchers_amount,
  (SELECT 
    COALESCE(SUM(k.amount), 0) 
  FROM
    inv_transactions k 
  WHERE k.order_id = a.order_id) AS mapped_amount 
FROM
  inv_orders a,
  inv_orders_details b 
WHERE a.order_id = b.order_id 
  AND b.payment_method NOT IN (
    'Cash or Credit at Store Pick-Up',
    'Behalf'
  ) 
  AND b.shipping_method <> 'Local Las Vegas Store Pickup - 9:30am-4:30pm' 
  AND LOWER(a.order_status) IN (
    'processed',
    'shipped',
    'on hold',
    'completed'
  ) 
  AND (
    (
      a.sub_total + a.tax + a.shipping_amount+a.voucher_issued_amount
    ) - (
      a.paid_price + a.refunded_amount +
      (SELECT 
        COALESCE(SUM(c.amount) * (- 1), 0) 
      FROM
        oc_voucher_history c 
      WHERE a.order_id = c.order_id)
    )
  ) < 0 
  
  and ((
    a.sub_total + a.tax + a.shipping_amount+a.voucher_issued_amount
  ) - (
    (select 
      COALESCE(sum(k.amount), 0) 
    from
      inv_transactions k 
    where a.order_id = k.order_id) + a.refunded_amount+ 
    (select 
      COALESCE(sum(c.amount) * (- 1), 0) 
    from
      oc_voucher_history c 
    where a.order_id = c.order_id)
  )) < 0
   
  AND a.store_type IN ('web', 'po_business') 
  AND COALESCE(a.payment_source, '') <> 'Replacement' 
  AND DATE(a.order_date) >= '".date('Y-m-d',strtotime('-6 month'))."' 
ORDER BY 1 DESC");
  $cache->set('credit_management.overpaid',$result2);
}

// testObject($result2);

  $html = '
  <strong>Overpaid</strong>
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Order ID</th>
                          <th>Customer</th>
                          <th>Status</th>
                          <th>Balance</th>
                          

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  foreach($result2 as $result)
                  {
                   
                    if(round($result['paid_price']+$result['vouchers_amount'])==round($result['order_price']))
                    {
                      continue;
                    }

                     if($result['paid_price']<0.00)
                    {
                      $result['paid_price'] = $result['paid_price']*(-1);
                    }

                    if(round($result['paid_price']+$result['mapped_amount'])==round($result['order_price']))
                    {
                      continue;
                    }

                      $balance = $result['paid_price'] - $result['order_price'];

                    $html.='<tr>
                    <td>'.linkToOrder($result['order_id'],$host_path).'</td>
                    <td>'.linkToProfile($result['email'],$host_path).'</td>
                    <td>'.$result['order_status'].'</td>
                    <td>$'.number_format($balance,2).'</td>
                    
                    </tr>
                    ';
                  }
                

                 $html.=' </tbody>
                  
              </table>
';
echo $html;exit;
}

if(isset($_POST['type']) && $_POST['type']=='unmapped_transactions')
{

  $result2 =   $cache->get('credit_management.unmapped_transactions');
if(!$result2)
{

   $result2 = $db->func_query("SELECT * FROM inv_transactions WHERE payment_status='Completed' and order_status='Completed' and is_mapped = '0' and date(order_date)>='".date('Y-m-d',strtotime('-3 month'))."' ORDER BY `order_date` desc");
  $cache->set('credit_management.unmapped_transactions',$result2);
}

// testObject($result2);

  $html = '
  <strong>Unmapped Transactions</strong>
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Date</th>
                          <th>Source/Transaction</th>
                          <th>Map</th>
                          <th>Sender/Receiver</th>
                          <th>Amount</th>
                          

                          ';


                      
                          
                          $html.='
                      
                      </tr>
                  </thead>
                  <tbody>';
                  $transaction_category =   $cache->get('credit_management.transaction_category');
if(!$transaction_category)
{
                  $transaction_category = $db->func_query("select * from inv_voucher_reasons where main_category<>'' and reason<>'' order by 
                    lower(concat(main_category,' - ',reason))");
                  $cache->set('credit_management.transaction_category',$transaction_category);
                }
                  foreach($result2 as $result)
                  {
                   
                  
                    $html.='<tr>
                    <td>'.americanDate($result['order_date']).'</td>
                    <td>'.($result['order_source']=='Unknown'?'PayPal':$result['order_source']).'/'.$result['transaction_id'].'</td>
                    <td align="center"><input type="text" class="mapbox" placeholder="Order ID, LBB, RMA" style="width:40%">
                    <select name="transaction_category" class="transaction_category" style="width:40%">
                        <option value="">Select</option>';
                          foreach($transaction_category as $transaction)
                          {
                            $html.='<option value="'.$transaction['id'].'">'.$transaction['main_category'].' - '.$transaction['reason'].'</option>';
                          }
                    $html.='</select>
                    <button class="button" onClick="MapBox(this,\''.$row['transaction_id'].'\')">Map</button>
                    </td>
                    <td>'.linkToProfile($result['email'],$host_path).'</td>
                    <td><span class="tag '.($result['amount']>0?'blue-bg':'red-bg').'">$'.number_format($result['amount'],2).'</span></td>
                    
                    </tr>
                    ';
                  }
                

                 $html.=' </tbody>
                  
              </table>
';
echo $html;exit;
}

if(isset($_POST['type']) && $_POST['type']=='undeposited_funds')
{
$paypal_undeposited_amount = $db->func_query_first_cell("SELECT SUM(net_amount) from inv_transactions where deposit_id=0");
$paypal_undeposited_count = $db->func_query_first_cell("SELECT count(*) from inv_transactions where deposit_id=0");

$cash_undeposited_amount = $db->func_query_first_cell("SELECT SUM(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,4))='cash' and lower(a.order_status) in ('shipped') and a.deposit_id=0");
$cash_undeposited_count = $db->func_query_first_cell("SELECT count(*) FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,4))='cash' and lower(a.order_status) in ('shipped') and a.deposit_id=0");

$check_undeposited_amount = $db->func_query_first_cell("SELECT SUM(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,5))='check' and lower(a.order_status) in ('shipped') and a.deposit_id=0");
$check_undeposited_count = $db->func_query_first_cell("SELECT count(*) FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,5))='check' and lower(a.order_status) in ('shipped') and a.deposit_id=0");

$behalf_undeposited_amount = $db->func_query_first_cell("SELECT SUM(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,6))='behalf' and lower(a.order_status) in ('shipped') and a.deposit_id=0");
$behalf_undeposited_count = $db->func_query_first_cell("SELECT count(*) FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,6))='behalf' and lower(a.order_status) in ('shipped') and a.deposit_id=0");

$wire_undeposited_amount = $db->func_query_first_cell("SELECT SUM(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,4)) in ('wire','bank') and lower(a.order_status) in ('shipped') and a.deposit_id=0");
$wire_undeposited_count = $db->func_query_first_cell("SELECT count(*) FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,4)) in ('wire','bank')  and lower(a.order_status) in ('shipped') and a.deposit_id=0");


$html = '
  <strong>Undeposited Funds</strong>
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th colspan="3" align="center">'.($paypal_undeposited_count+$cash_undeposited_count+$check_undeposited_count+$behalf_undeposited_count+$wire_undeposited_count).' Transactions, Amount $'.number_format($paypal_undeposited_amount+$cash_undeposited_amount+$check_undeposited_amount+$behalf_undeposited_amount+$behalf_undeposited_amount,2).'</th>
                      </tr>
                      <tr>
                      <td>PayPal</td>
                      <td>'.$paypal_undeposited_count.'</td>
                      <td>$'.number_format($paypal_undeposited_amount,2).'</td>

                      </tr>
                       <tr>
                      <td>Cash/Check</td>
                      <td>'.($cash_undeposited_count+$check_undeposited_count).'</td>
                     <td>$'.number_format($cash_undeposited_amount+$check_undeposited_amount,2).'</td>

                      </tr>
                       <tr>
                      <td>Bank Wire/ Behalf</td>
                      <td>'.($wire_undeposited_count+$behalf_undeposited_count).'</td>
                      <td>$'.number_format($wire_undeposited_amount+$behalf_undeposited_amount,2).'</td>

                      </tr>

                  </thead>
                  <tbody>';


echo $html;exit;
}




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
  <title>Payment Management</title>
<style>
#xcontent{width: 100%;
      height: 100%;
      top: 0px;
      left: 0px;
      position: fixed;
      display: block;
      opacity: 0.8;
      background-color: #000;
      z-index: 99;}
</style>
</head>
<body>
<div id="xcontent" style="display:none"><div style="color:#fff;
      top:40%;
      position:fixed;
      left:40%;
      font-weight:bold;font-size:25px"><img src="https://phonepartsusa.com/catalog/view/theme/default/image/loader_white.gif" /><span style="margin-left: 11%;
      margin-top: 33%;
      position: absolute;
      width: 201px;">Please wait...</span></div></div>
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
    
    <h2 align="center">Payment Management</h2>
   

   <div align="center" class="tabMenu" >
      <input type="button" class="toogleTab" data-tab="tabPayments" onclick="$('#tab').val('');" value="Payments Dashboard">
      <input type="button" class="toogleTab" data-tab="tabPaypal" onclick="$('#tab').val('paypal');" value="Paypal Accounting">
      <input type="button" class="toogleTab" data-tab="tabcash" onclick="$('#tab').val('cash');fetch_vouchers('cash');" value="Cash Payments">
      <input type="button" class="toogleTab" data-tab="tabcard" onclick="$('#tab').val('card');fetch_vouchers('card');" value="Card Accounting">
      <input type="button" class="toogleTab" data-tab="tabcheck" onclick="$('#tab').val('check');fetch_vouchers('check');" value="Check Accounting">
      <input type="button" class="toogleTab" data-tab="tabwire" onclick="$('#tab').val('wire');fetch_vouchers('wire');" value="Wire Transfers">
      <input type="button" class="toogleTab" data-tab="tabcod" onclick="$('#tab').val('cod');fetch_vouchers('cod');" value="Cash on Delivery">
      <input type="button" class="toogleTab" data-tab="tabbehalf" onclick="$('#tab').val('behalf');fetch_vouchers('behalf');" value="Behalf Accounting">
      <input type="button" class="toogleTab" data-tab="tabtax" onclick="$('#tab').val('tax');" value="Tax Management">
      <input type="button" class="toogleTab" data-tab="tabstore_credit" onclick="$('#tab').val('store_credit');fetch_credits();" value="Store Credit Management">
   <!--    <input type="button" class="toogleTab" data-tab="tabTargets" value="Sales Targets">

      <input type="button" class="toogleTab to_show" onclick="agentDashboardTab()" data-tab="tabDashboard" value="Agent Dashboard">
 -->
      </div>

       <div class="tabHolder">
      <div id="tabPayments" class="makeTabs">
      <!-- <h2 align="center">Payments Dashboard</h2> -->
    <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
      <tbody>

        <tr>
          
          

          <td colspan="4" width="50%" valign="top">
          <div id="balance_due" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>
           <td colspan="4" width="50%" valign="top">
          <div id="overpaid" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>
        </tr>
        <tr>
            <td colspan="8"><hr></td>
        </tr>

<tr>
          
          

          <td colspan="8"  valign="top">
          <div id="unmapped_transactions" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>

            
          </tr>

          <tr>
            <td colspan="8"><hr></td>
        </tr>

          <tr>
              <td colspan="4" width="40%" valign="top">
          <div id="undeposited_funds" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>
          <td colspan="4"></td>
          </tr>

      
    

        

      </tbody>
    </table>
    </div>
    <!-- tab cash -->
    <div id="tabcash" class="makeTabs">

    </div>

    <!-- end cash tab -->


    <!-- tab behalf -->
    <div id="tabcod" class="makeTabs">
    
    </div>

    <!-- end behalf tab -->

    <!-- tab behalf -->
    <div id="tabbehalf" class="makeTabs">
    
    </div>

    <!-- end behalf tab -->

    <!-- tab card -->
    <div id="tabcard" class="makeTabs">
    
    </div>

    <!-- end behalf tab -->



    <!-- tab check -->
    <div id="tabcheck" class="makeTabs">
     
    </div>

    <!-- end check tab -->


    <!-- tab wire -->
   
    <div id="tabwire" class="makeTabs">
    
    </div>

    <!-- end check tab -->


    <!-- tab wire -->
  
    <div id="tabtax" class="makeTabs">
    

    </div>

    <!-- end tax tab -->
    <!-- tab wire -->
  
    <div id="tabstore_credit" class="makeTabs">
    
    </div>

    <!-- end tax tab -->

    <!-- tab paypal starts -->

    <div id="tabPaypal" class="makeTabs">
     <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
      <tbody>

        <tr>
          
          

          <td colspan="5" width="60%" valign="top">

          <form id="deposit_to_frm">
 <div style="text-align: left;margin-top:15px;margin-left:15px">

 <select name="deposit_id" style="padding:8px 11px">
 <option value="">Select Deposit #</option>
 <?php
 $open_deposits = $db->func_query("SELECT * FROM inv_deposits WHERE status='open' and deposit_type='paypal' order by deposit_date desc");
 foreach($open_deposits as $open_deposit)
 {
  ?>
  <option value="<?php echo $open_deposit['deposit_id'];?>"><?php echo $open_deposit['name'];?></option>
  <?php
 }
 ?>

</select>
  &nbsp;<input type="button" class="button" value="Add to Deposit" onclick="addToDeposit('Paypal');"></div>
           <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            <tr>
                <td colspan="8" align="center">Date: <input type="text" data-type="date" style="padding:8px 11px" id="filter_transaction_start" value="<?php echo $_GET['filter_transaction_start'];?>"> to <input type="text" data-type="date" style="padding:8px 11px" id="filter_transaction_end" value="<?php echo $_GET['filter_transaction_end'];?>">
                  <input type="button" value="Find" onclick="window.location='credit_management_new.php?tab=paypal&filter_transaction_start='+$('#tabPaypal #filter_transaction_start').val()+'&filter_transaction_end='+$('#tabPaypal #filter_transaction_end').val()" class="button">
                </td>
            </tr>
            <tr>

            <td colspan="2" style="font-size:40px;font-weight: bold" align="center" class="undeposited_amount">0.00</td>
            <td colspan="6" align="center"><h3>Undeposited Paypal Transactions</h3><small><i>Undeposited Amount: $<?php echo number_format($db->func_query_first_cell("SELECT SUM(net_amount) from inv_transactions where deposit_id=0"),2);?></i></small></td>
            </tr>

            </table>

            <table width="90%" cellpadding="0" cellspacing="0" class="xtable" id="undeposited_table">
            
            <thead>
            <tr>
            <td align="center"><input type="checkbox" onchange="checkAll(this);"></td>
            <th>Date Rec.</th>
            <th>Transaction #</th>
            <th>Ref ID</th>
            <th>Sender/Recipient</th>
            <th colspan="3">Amt Received/Sent</th>
            </tr> 
            <tr>
            <th colspan="5"></th>
            <th>Gross</th>
            <th>Fee</th>
            <th>Net</th>
            </tr>
            </thead>
            <tbody>
            <?php
           
            foreach($rows as $row)
            {
              ?>
              <tr class="list_items">
              <td><input type="checkbox" name="transaction_id[]" class="undeposited_checkbox" value="<?php echo $row['id'];?>" data-value="<?php echo $row['net_amount'];?>">

              <input type="hidden" name="net_amount[<?php echo $row['id'];?>]" value="<?php echo $row['net_amount'];?>">
              </td>
              <td><?php echo americanDate($row['order_date']);?></td>
              <td><?php echo $row['transaction_id'];?></td>
              <td><?php echo ($row['order_id']?linkToOrder($row['order_id']):$row['order_id']);?></td>
              <td><?php echo $row['email'] .($row['net_amount']<0?' <span style="color:red;font-weight:bold;font-size:30px">&rarr;</span>':' <span style="color:green;font-weight:bold;font-size:30px">&larr;</span>');?></td>
              <td><?php echo '$'.number_format($row['amount'],2);?></td>
              <td><?php echo '$'.number_format($row['transaction_fee'],2);?></td>
              <td><?php echo '$'.number_format($row['net_amount'],2);?></td>
              </tr>
              <?php
            }
            ?>
            </tbody>

            <tfoot>
          <tr>
            <?php
            $parameters = str_replace(array('page='.$_GET['page'],'tab='.$_GET['tab']), '', $_SERVER['QUERY_STRING']);
            // $parameters = str_replace('&tab=' . $_GET['tab'], '', $_SERVER['QUERY_STRING']);
            $parameters .= '&tab=paypal';
            ?>
            <td colspan="8">
              <em><?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?></em>
              <div class="pagination" style="float:right">
                <?php echo $splitPage->display_links(10,$parameters);?>
              </div>
            </td>
          </tr>
        </tfoot>

            </table>
            </form>


          </td>

          <td colspan="3" width="40%" valign="top" >
          <div style="text-align: right;margin-top:15px;margin-right:15px"><a class="fancybox2 fancybox.iframe button" href="<?php echo $host_path;?>popupfiles/add_bank_deposit.php?type=paypal">Add Bank Deposit</a></div>
  <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            <tr>
            
            <td colspan="6" align="center"><h3>Bank Deposits</h3></td>
            </tr>

            </table>

            <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            
            <thead>
            <tr>
            <th>Date</th>
            <th>Deposit #</th>
            <th># of Trans</th>
            <th>Diff</th>
            <th>Status</th>
            </tr> 
            
            </thead>
            <tbody>
            <?php
            $deposits = $db->func_query("SELECT * FROM inv_deposits where deposit_type='paypal' group by name ORDER BY deposit_date desc");
            foreach($deposits as $deposit)
            {
              $diff = $db->func_query_first_cell("SELECT SUM(net_amount) from inv_transactions where deposit_id='".$deposit['deposit_id']."'");
              ?>
              <tr>
              <td><?php echo date('m/d/Y',strtotime($deposit['deposit_date']));?></td>
              <td><a class="fancyboxXZ fancybox.iframe" href="<?php echo $host_path;?>popupfiles/deposit_transactions.php?deposit_id=<?php echo $deposit['deposit_id'];?>"><?php echo $deposit['name'];?></a></td>
              <td><?php echo $db->func_query_first_cell("SELECT COUNT(*) from inv_transactions where deposit_id='".$deposit['deposit_id']."'");?></td>
              <td>
                <?php 
                echo '$'.number_format( (float)$deposit['amount']-(float)$diff ,2);
                ?>
              </td>
              <td><span class="tag <?php echo ($deposit['status']=='closed'?'red':'blue');?>-bg"><?php echo $deposit['status'];?></span></td>

              </tr>
              <?php
            }
            ?>
            </tbody>
            </table>
          </td>

          </tr>
          <tr>
          <td colspan="8">

              <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            <tr>
            
            <td colspan="8" align="center"><h3>Deposited Funds</h3></td>
            </tr>

            </table>
            <form id="deposited_frm">
            <div align="center" style="font-weight:bold">Transaction ID: <input type="text" style="padding:8px 11px" name="filter_transaction_id" value=""> Deposit Date: <input type="text" data-type="date" style="padding:8px 11px" name="filter_deposit_start"> to <input type="text" data-type="date" style="padding:8px 11px" name="filter_deposit_end"> Received Date: <input type="text" data-type="date" style="padding:8px 11px" name="filter_received_start"> to <input type="text" data-type="date" style="padding:8px 11px" name="filter_received_end"><br><br><input type="button" value="Find" onclick="depositedFunds();" class="button"></div>
            <input type="hidden" name="action" value="load_deposited_funds">

            </form>


            <table width="90%" cellpadding="0" cellspacing="0" class="xtable" id="deposited_funds_table">
            
            <thead>
            <tr>
            <th>Date</th>
            <th>Deposit #</th>
            <th>Received Date</th>
            <th>Transaction #</th>
            <th>Ref ID</th>
            <th>Sender/Recipient</th>
            <th colspan="3">Amt Received/Sent</th>
            </tr> 
            <tr>
            <th colspan="6"></th>
            <th>Gross</th>
            <th>Fee</th>
            <th>Net</th>
            </tr>
            
            </thead>
            <tbody>

            </tbody>
            </table>



          </td>
          </tr>
          </tbody>
          </table>
    </div>

    <!-- tab paypal ends -->
    </div>

  </body>
  <input type="hidden" id="temp_bit" value="0">
  <input type="hidden" id="tab" value="">
  </html>
  <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
  <script type="text/javascript" src="js/newmultiselect.js"></script>

  <script>

   $(function () {
            // $('#undeposited_table').multiSelect({
            //    actcls: 'highlightx',
            //    selector: 'tbody .list_items',
            //    except: ['form'],
            //    callback: function (items) {
            //       traverseCheckboxes('#undeposited_table', '.undeposited_checkbox');
            //    }
            // });
         })


  function checkAll(obj)
  {
    // console.log('here');
    // console.log($(obj).is(":checked"));
    $(obj).parent().parent().parent().parent().find('.undeposited_checkbox').prop('checked',$(obj).is(":checked"));
    var amount = 0.00;
    $(obj).parent().parent().parent().parent().find('.undeposited_checkbox').each(function () {
      $this = $(this).parent().parent().parent().parent().parent();
    
   
      if(this.checked)
      {
        amount+=parseFloat($(this).attr('data-value'));
      }

      

    

});

$(obj).parent().parent().parent().parent().parent().find('.undeposited_amount').html(amount.toFixed(2));


  }
  $(document).ready(function(){
    <?php
    if(isset($_GET['tab']))
    {
      ?>
      $('input[data-tab=tab<?php echo ucfirst($_GET['tab']);?>]').click();
      <?php
    }
    ?>


  })
  $(document).on('click','.undeposited_checkbox',function(e){
    console.log($(this).val());

    // console.log($(this).is(":checked"));
    $this = $(this).parent().parent().parent().parent().parent();
    var amount = 0.00;
    $this.find('.undeposited_checkbox').each(function(){
      if(this.checked)
      {
        amount+=parseFloat($(this).attr('data-value'));
      }
    })

     $this.find('.undeposited_amount').html(amount.toFixed(2));
    // if($(this).is(":checked"))
    // {
    //   $this.find('.undeposited_amount').html((parseFloat($this.find('.undeposited_amount').html()) + parseFloat($(this).attr('data-value'))).toFixed(2));
    // }
    // else
    // {
      
    //   $this.find('.undeposited_amount').html((parseFloat($this.find('.undeposited_amount').html()) - parseFloat($(this).attr('data-value'))).toFixed(2));

    // }
    // e.preventDefault();
  });
  $(document).ready(function(){
loadData('balance_due');
depositedFunds();
depositedCashFunds();
depositedBehalfFunds();
depositedCheckFunds();
depositedWireFunds();
depositedCardFunds();

  });

  function loadData(type)
  {
    $.ajax({
        url: 'credit_management_new.php',
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


          if(type=='balance_due')
          {

          loadData('overpaid');
          }

           if(type=='overpaid')
          {

          loadData('unmapped_transactions');
          }

          if(type=='unmapped_transactions')
          {

          loadData('undeposited_funds');
          }

         

            if(type=='undeposited_funds')
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

  function MapBox(obj,transaction_id)
    {
      var ref = $(obj).parent().parent().find('.mapbox');
      var ref_id = $(ref).val();
      if(jQuery.trim(ref_id)=='')
      {
        alert('Please provide a valid reference');
        return false;
      }

        if($(obj).parent().parent().find('.transaction_category').val()=='')
        {
          alert('Please select a valid transaction Transaction Category to map');
          return false;
        }
      if(!confirm('Are you sure to Map?'))
      {
        return false;
      }
      $.ajax({
        url: 'paypal_orders.php',
        type:"POST",
        dataType:"json",
        data:{'ref_id':ref_id,'action':'mapbox','transaction_id':transaction_id,'transaction_category':$(obj).parent().parent().find('.transaction_category').val()},
        beforeSend: function() {
          $(this).addClass('disabled');

          // $(ref).parent().find('.map_button').hide(200);
      //$(ref).parent().find('.map_wait').html('Please wait...').show(200);
    },    
    complete: function() {
      $(this).removeClass('disabled');
    
  },    
  success: function(json){
    if(json['error'])
    {
      alert(json['error']);
      // $(ref).parent().find('.map_button').show(200);
      return false;
    }
    if (json['success']) {
      $(ref).parent().hide(200);
                      }
                    }
                  });
      
    }

    function addToDeposit(type)
    {
      if($('#tab'+type+' select[name=deposit_id]').val()=='')
      {
        alert('Please select deposit #');
        return false;
      }

      var formData = $('#tab'+type).find('#deposit_to_frm').serialize();

        $.ajax({
        url: 'credit_management_new.php',
        type:"POST",
        dataType:"json",
        data:formData,
       
  success: function(json){
    if(json['error'])
    {
      alert(json['error']);
      // $(ref).parent().find('.map_button').show(200);
      return false;
    }
    if (json['success']) {
     alert(json['success']);
     window.location='<?php echo $host_path;?>credit_management_new.php?tab='+type.toLowerCase()+'&filter_transaction_start=<?php echo $_GET['filter_transaction_start'];?>&filter_transaction_end=<?php echo $_GET['filter_transaction_end'];?>&page=<?php echo ($_GET['page']?$_GET['page']:1);?>';
                      }
                    }
                  });
      
    }

    function depositedFunds()
    {
      

      var formData = $('#deposited_frm').serialize();

        $.ajax({
        url: 'credit_management_new.php',
        type:"POST",
        dataType:"json",
        data:formData,
         beforeSend: function() {
        
          $('#deposited_funds_table tbody').html('<tr><td colspan="9" align="center"><img class="loader" src="images/loading.gif" height"100" width="100" /></td></tr>');
    },    
    complete: function() {
      // $(document).removeClass('loader');

    
  },    
       
  success: function(json){
   
    if (json['success']) {
      $('#deposited_funds_table tbody').html(json['success']);
                      }
                    }
                  });
      
    }

    function depositedCashFunds()
    {
      

      var formData = $('#deposited_cash_frm').serialize();
        $.ajax({
        url: 'credit_management_new.php',
        type:"POST",
        dataType:"json",
        data:formData,
         beforeSend: function() {
        
          $('#deposited_cash_table tbody').html('<tr><td colspan="8" align="center"><img class="loader" src="images/loading.gif" height"100" width="100" /></td></tr>');
    },    
    complete: function() {
      // $(document).removeClass('loader');

    
  },    
       
  success: function(json){
   
    if (json['success']) {
      $('#deposited_cash_table tbody').html(json['success']);
                      }
                    }
                  });
      
    }


    function depositedBehalfFunds()
    {
      

      var formData = $('#deposited_behalf_frm').serialize();
        $.ajax({
        url: 'credit_management_new.php',
        type:"POST",
        dataType:"json",
        data:formData,
         beforeSend: function() {
        
          $('#deposited_behalf_table tbody').html('<tr><td colspan="8" align="center"><img class="loader" src="images/loading.gif" height"100" width="100" /></td></tr>');
    },    
    complete: function() {
      // $(document).removeClass('loader');

    
  },    
       
  success: function(json){
   
    if (json['success']) {
      $('#deposited_behalf_table tbody').html(json['success']);
                      }
                    }
                  });
      
    }

    function depositedCardFunds()
    {
      

      var formData = $('#deposited_card_frm').serialize();
        $.ajax({
        url: 'credit_management_new.php',
        type:"POST",
        dataType:"json",
        data:formData,
         beforeSend: function() {
        
          $('#deposited_card_table tbody').html('<tr><td colspan="8" align="center"><img class="loader" src="images/loading.gif" height"100" width="100" /></td></tr>');
    },    
    complete: function() {
      // $(document).removeClass('loader');

    
  },    
       
  success: function(json){
   
    if (json['success']) {
      $('#deposited_card_table tbody').html(json['success']);
                      }
                    }
                  });
      
    }

    function depositedCheckFunds()
    {
      

      var formData = $('#deposited_check_frm').serialize();
        $.ajax({
        url: 'credit_management_new.php',
        type:"POST",
        dataType:"json",
        data:formData,
         beforeSend: function() {
        
          $('#deposited_check_table tbody').html('<tr><td colspan="8" align="center"><img class="loader" src="images/loading.gif" height"100" width="100" /></td></tr>');
    },    
    complete: function() {
      // $(document).removeClass('loader');

    
  },    
       
  success: function(json){
   
    if (json['success']) {
      $('#deposited_check_table tbody').html(json['success']);
                      }
                    }
                  });
      
    }

    function depositedWireFunds()
    {
      

      var formData = $('#deposited_wire_frm').serialize();
        $.ajax({
        url: 'credit_management_new.php',
        type:"POST",
        dataType:"json",
        data:formData,
         beforeSend: function() {
        
          $('#deposited_wire_table tbody').html('<tr><td colspan="8" align="center"><img class="loader" src="images/loading.gif" height"100" width="100" /></td></tr>');
    },    
    complete: function() {
      // $(document).removeClass('loader');

    
  },    
       
  success: function(json){
   
    if (json['success']) {
      $('#deposited_wire_table tbody').html(json['success']);
                      }
                    }
                  });
      
    }

    jQuery(document).ready(function () {
        
        jQuery('.fancybox2').fancybox({width: '500px', height: '500px', autoCenter: true, autoSize: false});
      });

    $(".fancyboxXZ").fancybox({
    type:'iframe',width: '90%', autoCenter: true, autoSize: true,
    afterClose: function () { // USE THIS IT IS YOUR ANSWER THE KEY WORD IS "afterClose"
        
        window.location='<?php echo $host_path;?>credit_management_new.php?tab='+$('#tab').val()+'&filter_transaction_start=<?php echo $_GET['filter_transaction_start'];?>&filter_transaction_end=<?php echo $_GET['filter_transaction_end'];?>&page=<?php echo $_GET['page'];?>';
          
        
    }
});

    function fetch_vouchers(type)
    {

       $.ajax({
        url: 'credit_management_new.php',
        type: 'post',
        data: {type:type,'action':'fetch_vouchers',filter_transaction_start:'<?php echo $_GET['filter_transaction_start'];?>',filter_transaction_end:'<?php echo $_GET['filter_transaction_end'];?>',page:'<?php echo (int)$_GET['page'];?>'},
        dataType: 'html',
        beforeSend: function() {
         $('#xcontent').show();
        },  
        complete: function() {
          $('#xcontent').hide();
        },      
        success: function(html) {
          $('#tab'+type).html(html);
        }
      });
    }

    function fetch_credits()
    {

       $.ajax({
        url: 'credit_management_new.php',
        type: 'post',
        data: {'action':'fetch_credits',filter_date_start:$('#tabstore_credit #filter_date_start').val(),filter_date_end:$('#tabstore_credit #filter_date_end').val(),filter_voucher_code:$('#filter_voucher_code').val(),filter_user:$('#filter_user').val()},
        dataType: 'html',
        beforeSend: function() {
         $('#xcontent').show();
        },  
        complete: function() {
          $('#xcontent').hide();
        },      
        success: function(html) {
          $('#tabstore_credit').html(html);
        }
      });
    }
  </script>