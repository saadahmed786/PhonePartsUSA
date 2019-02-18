<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';
page_permission('purchasing_metrics');
if(!isset($_GET['hide_header']))
{
  unset($_SESSION['hide_header']);
}
if($_POST['action']=='load_cash_funds')
{
   $where='';
  if($_POST['filter_transaction_id']!='')
  {
    $where.=" and a.order_id='".$_POST['filter_transaction_id']."'";
  }
  if($_POST['filter_deposit_start']!='')
  {
    $where.=" and (date(a.deposited_date) between '".$_POST['filter_deposit_start']."' and '".$_POST['filter_deposit_end']."') ";
  }
  if($_POST['filter_received_start']!='')
  {
    $where.=" and (date(a.order_date) between '".$_POST['filter_received_start']."' and '".$_POST['filter_received_end']."') ";
  }
  if(!$where)
  {
    $where = " and date(a.deposited_date)='".date('Y-m-d')."'";
  }
  // echo "SELECT * FROM inv_transactions WHERE deposit_id<>0 $where  order by deposit_date desc";exit;
  $rows = $db->func_query("SELECT a.order_date,a.order_id,a.email,a.deposited_date,a.net_amount FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,4))='cash' $where  and a.deposit_id=0 ORDER BY a.`deposited_date` desc");
$html='';
  foreach($rows as $row)
  {
    $html.='<tr>';
    $html.='<td>'.americanDate($row['deposited_date'],false).'</td>';
    $html.='<td>-</td>';
    $html.='<td>'.americanDate($row['order_date']).'</td>';
    
    $html.='<td>'.linkToOrder($row['order_id']).'</td>';
    $html.='<td>'.linkToProfile($row['email']).'</td>';
    
    $html.='<td>$'.number_format($row['net_amount'],2).'</td>';
   
    $html.='</tr>';
  }
  if(!$rows)
  {
    $html.='<tr><td colspan="6" align="center">No Record Found</td></tr>';
  }

  echo json_encode(array('success'=>$html));exit;
}

if($_POST['action']=='load_behalf_funds')
{
   $where='';
  if($_POST['filter_transaction_id']!='')
  {
    $where.=" and a.order_id='".$_POST['filter_transaction_id']."'";
  }
  if($_POST['filter_deposit_start']!='')
  {
    $where.=" and (date(a.deposited_date) between '".$_POST['filter_deposit_start']."' and '".$_POST['filter_deposit_end']."') ";
  }
  if($_POST['filter_received_start']!='')
  {
    $where.=" and (date(a.order_date) between '".$_POST['filter_received_start']."' and '".$_POST['filter_received_end']."') ";
  }
  if(!$where)
  {
    $where = " and date(a.deposited_date)='".date('Y-m-d')."'";
  }
  // echo "SELECT * FROM inv_transactions WHERE deposit_id<>0 $where  order by deposit_date desc";exit;
  $rows = $db->func_query("SELECT a.order_date,a.order_id,a.email,a.deposited_date,a.net_amount FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,6))='behalf' $where  and a.deposit_id=0 ORDER BY a.`deposited_date` desc");
$html='';
  foreach($rows as $row)
  {
    $html.='<tr>';
    $html.='<td>'.americanDate($row['deposited_date'],false).'</td>';
    $html.='<td>-</td>';
    $html.='<td>'.americanDate($row['order_date']).'</td>';
    
    $html.='<td>'.linkToOrder($row['order_id']).'</td>';
    $html.='<td>'.linkToProfile($row['email']).'</td>';
    
    $html.='<td>$'.number_format($row['gross'],2).'</td>';
    $html.='<td>$'.number_format($row['payment_fee'],2).'</td>';
    $html.='<td>$'.number_format($row['net_amount'],2).'</td>';
   
    $html.='</tr>';
  }
  if(!$rows)
  {
    $html.='<tr><td colspan="8" align="center">No Record Found</td></tr>';
  }

  echo json_encode(array('success'=>$html));exit;
}

if($_POST['action']=='load_check_funds')
{
   $where='';
  if($_POST['filter_transaction_id']!='')
  {
    $where.=" and a.order_id='".$_POST['filter_transaction_id']."'";
  }
  if($_POST['filter_deposit_start']!='')
  {
    $where.=" and (date(a.deposited_date) between '".$_POST['filter_deposit_start']."' and '".$_POST['filter_deposit_end']."') ";
  }
  if($_POST['filter_received_start']!='')
  {
    $where.=" and (date(a.order_date) between '".$_POST['filter_received_start']."' and '".$_POST['filter_received_end']."') ";
  }
  if(!$where)
  {
    $where = " and date(a.deposited_date)='".date('Y-m-d')."'";
  }
  // echo "SELECT * FROM inv_transactions WHERE deposit_id<>0 $where  order by deposit_date desc";exit;
  $rows = $db->func_query("SELECT a.order_date,a.order_id,a.email,a.deposited_date,a.net_amount FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,5))='check' $where  and a.deposit_id=0 ORDER BY a.`deposited_date` desc");
$html='';
  foreach($rows as $row)
  {
    $html.='<tr>';
    $html.='<td>'.americanDate($row['deposited_date'],false).'</td>';
    $html.='<td>-</td>';
    $html.='<td>'.americanDate($row['order_date']).'</td>';
    
    $html.='<td>'.linkToOrder($row['order_id']).'</td>';
    $html.='<td>'.linkToProfile($row['email']).'</td>';
    
    $html.='<td>$'.number_format($row['gross'],2).'</td>';
    $html.='<td>$'.number_format($row['payment_fee'],2).'</td>';
    $html.='<td>$'.number_format($row['net_amount'],2).'</td>';
   
    $html.='</tr>';
  }
  if(!$rows)
  {
    $html.='<tr><td colspan="8" align="center">No Record Found</td></tr>';
  }

  echo json_encode(array('success'=>$html));exit;
}

if($_POST['action']=='load_wire_funds')
{
   $where='';
  if($_POST['filter_transaction_id']!='')
  {
    $where.=" and a.order_id='".$_POST['filter_transaction_id']."'";
  }
  if($_POST['filter_deposit_start']!='')
  {
    $where.=" and (date(a.deposited_date) between '".$_POST['filter_deposit_start']."' and '".$_POST['filter_deposit_end']."') ";
  }
  if($_POST['filter_received_start']!='')
  {
    $where.=" and (date(a.order_date) between '".$_POST['filter_received_start']."' and '".$_POST['filter_received_end']."') ";
  }
  if(!$where)
  {
    $where = " and date(a.deposited_date)='".date('Y-m-d')."'";
  }
  // echo "SELECT * FROM inv_transactions WHERE deposit_id<>0 $where  order by deposit_date desc";exit;
  $rows = $db->func_query("SELECT a.order_date,a.order_id,a.email,a.deposited_date,a.net_amount FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,4)) in ('wire','bank') $where  and a.deposit_id=0 ORDER BY a.`deposited_date` desc");
$html='';
  foreach($rows as $row)
  {
    $html.='<tr>';
    $html.='<td>'.americanDate($row['deposited_date'],false).'</td>';
    $html.='<td>-</td>';
    $html.='<td>'.americanDate($row['order_date']).'</td>';
    
    $html.='<td>'.linkToOrder($row['order_id']).'</td>';
    $html.='<td>'.linkToProfile($row['email']).'</td>';
    
    $html.='<td>$'.number_format($row['gross'],2).'</td>';
    $html.='<td>$'.number_format($row['payment_fee'],2).'</td>';
    $html.='<td>$'.number_format($row['net_amount'],2).'</td>';
   
    $html.='</tr>';
  }
  if(!$rows)
  {
    $html.='<tr><td colspan="8" align="center">No Record Found</td></tr>';
  }

  echo json_encode(array('success'=>$html));exit;
}


if($_POST['action']=='load_deposited_funds')
{
  $where='';
  if($_POST['filter_transaction_id']!='')
  {
    $where.=" and transaction_id='".$_POST['filter_transaction_id']."'";
  }
  if($_POST['filter_deposit_start']!='')
  {
    $where.=" and (date(deposit_date) between '".$_POST['filter_deposit_start']."' and '".$_POST['filter_deposit_end']."') ";
  }
  if($_POST['filter_received_start']!='')
  {
    $where.=" and (date(order_date) between '".$_POST['filter_received_start']."' and '".$_POST['filter_received_end']."') ";
  }
  if(!$where)
  {
    $where = " and date(deposit_date)='".date('Y-m-d')."'";
  }
  // echo "SELECT * FROM inv_transactions WHERE deposit_id<>0 $where  order by deposit_date desc";exit;
  $rows = $db->func_query("SELECT * FROM inv_transactions WHERE deposit_id<>0 $where  order by deposit_date desc");
$html='';
  foreach($rows as $row)
  {
    $html.='<tr>';
    $html.='<td>'.americanDate($row['deposit_date']).'</td>';
    $html.='<td>'.$db->func_query_first_cell("select name from inv_deposits where deposit_id='".$row['deposit_id']."'").'</td>';
    $html.='<td>'.americanDate($row['order_date']).'</td>';
    $html.='<td>'.($row['transaction_id']).'</td>';
    $html.='<td>'.linkToOrder($row['order_id']).'</td>';
    $html.='<td>'.($row['email']).'</td>';
    $html.='<td>$'.number_format($row['amount'],2).'</td>';
    $html.='<td>$'.number_format($row['transaction_fee'],2).'</td>';
    $html.='<td>$'.number_format($row['net_amount'],2).'</td>';
   
    $html.='</tr>';
  }
  if(!$rows)
  {
    $html.='<tr><td colspan="9" align="center">No Record Found</td></tr>';
  }

  echo json_encode(array('success'=>$html));exit;

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
      $order_data = $db->func_query_first("SELECT a.order_date,a.order_id,a.email,(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount,(select sum(value) from oc_order_total d where d.order_id=a.order_id and d.code='business_fee' ) as payment_fee,b.payment_method,e.payment_method,e.cash_split FROM inv_orders a,inv_orders_details b,oc_order e WHERE a.order_id=b.order_id and e.order_id=a.order_id and a.order_id='".$transaction_id."'");

      if($order_data['cash_split']>0.00)
      {
        $order_data['net_amount'] = $order_data['cash_split'];
      }
      $db->db_exec("UPDATE inv_orders SET deposit_id='".(int)$_POST['deposit_id']."',gross_amount='".$order_data['net_amount']."',net_amount='".$order_data['net_amount']."', deposited_date='".date("Y-m-d")."', deposited_by='".$_SESSION['user_id']."' where order_id='".$transaction_id."'");

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

$start = ($page - 1) * $num_rows;
 $inv_query = ("SELECT * FROM inv_transactions WHERE payment_status='Completed' AND DATE(order_date) BETWEEN '".$_GET['filter_transaction_start']."' AND '".$_GET['filter_transaction_end']."'  and order_status='Completed' and deposit_id=0 ORDER BY `order_date` desc");

$splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);

// echo $page;exit;

// echo $splitPage->sql_query;exit;
$rows = $db->func_query($splitPage->sql_query);

// cash query
 $inv_query = ("SELECT a.order_date,a.order_id,a.email,(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount,(select sum(value) from oc_order_total d where d.order_id=a.order_id and d.code='business_fee' ) as payment_fee,b.payment_method,e.payment_method,e.cash_split FROM inv_orders a,inv_orders_details b,oc_order e WHERE a.order_id=b.order_id and e.order_id=a.order_id and lower(left(e.payment_method,4))='cash' and lower(left(b.payment_method,4))='cash' AND DATE(a.order_date) BETWEEN '".$_GET['filter_transaction_start']."' AND '".$_GET['filter_transaction_end']."' and lower(a.order_status) in ('shipped') and a.deposit_id=0 ORDER BY a.`order_date` desc");
// echo $inv_query;exit;


$splitPage2 = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);


$cashs = $db->func_query($splitPage2->sql_query);




// behalf query
// cash query
 $inv_query = ("SELECT a.order_date,a.order_id,a.email,(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount,0.00 as payment_fee,b.payment_method FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,6))='behalf' AND DATE(a.order_date) BETWEEN '".$_GET['filter_transaction_start']."' AND '".$_GET['filter_transaction_end']."' and lower(a.order_status) in ('shipped') and a.deposit_id=0 ORDER BY a.`order_date` desc");
// echo $inv_query;exit;


$splitPage3 = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);


$behalfs = $db->func_query($splitPage3->sql_query);



// behalf query
// cash query
 $inv_query = ("SELECT a.order_date,a.order_id,a.email,(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount,0.00 as payment_fee,b.payment_method FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,5))='check' AND DATE(a.order_date) BETWEEN '".$_GET['filter_transaction_start']."' AND '".$_GET['filter_transaction_end']."' and lower(a.order_status) in ('shipped') and a.deposit_id=0 ORDER BY a.`order_date` desc");
// echo $inv_query;exit;


$splitPage4 = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);


$checks = $db->func_query($splitPage4->sql_query);


$inv_query = ("SELECT a.order_date,a.order_id,a.email,(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount,0.00 as payment_fee,b.payment_method FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,4)) in ('bank','wire') AND DATE(a.order_date) BETWEEN '".$_GET['filter_transaction_start']."' AND '".$_GET['filter_transaction_end']."' and lower(a.order_status) in ('shipped') and a.deposit_id=0 ORDER BY a.`order_date` desc");
// echo $inv_query;exit;


$splitPage5 = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);


$wires = $db->func_query($splitPage5->sql_query);


if(isset($_POST['type']) && $_POST['type']=='balance_due')
{

$result2 =   $cache->get('credit_management.balance_due');
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

                      $balance = $result['order_price'] - $result['paid_price'];

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
      a.sub_total + a.tax + a.shipping_amount
    ) - (
      a.paid_price + 
      (SELECT 
        COALESCE(SUM(c.amount) * (- 1), 0) 
      FROM
        oc_voucher_history c 
      WHERE a.order_id = c.order_id)
    )
  ) < 0 
  
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
$html = '
  <strong>Undeposited Funds</strong>
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th colspan="3" align="center">'.$paypal_undeposited_count.' Transactions, Amount $'.number_format($paypal_undeposited_amount,2).'</th>
                      </tr>
                      <tr>
                      <td>PayPal</td>
                      <td>'.$paypal_undeposited_count.'</td>
                      <td>$'.number_format($paypal_undeposited_amount,2).'</td>

                      </tr>
                       <tr>
                      <td>Cash/Check</td>
                      <td>0</td>
                      <td>$0.00</td>

                      </tr>
                       <tr>
                      <td>Bank Wire/ Behalf</td>
                      <td>0</td>
                      <td>$0.00</td>

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
  <title>Payments &amp; Credit Management</title>

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
    
    <h2 align="center">Payments &amp; Credit Management</h2>
   

   <div align="center" class="tabMenu" >
      <input type="button" class="toogleTab" data-tab="tabPayments" value="Payments Dashboard">
      <input type="button" class="toogleTab" data-tab="tabCash" value="Cash Payments">
      <input type="button" class="toogleTab" data-tab="tabPaypal" value="Paypal Accounting">
      <input type="button" class="toogleTab" data-tab="tabBehalf" value="Behalf Accounting">
      <input type="button" class="toogleTab" data-tab="tabCheck" value="Check Accounting">
      <input type="button" class="toogleTab" data-tab="tabWire" value="Wire Transfers">
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
    <div id="tabCash" class="makeTabs">
     <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
      <tbody>

        <tr>
          
          

          <td colspan="5" width="60%" valign="top">

          <form id="deposit_to_frm">
 <div style="text-align: left;margin-top:15px;margin-left:15px">

 <select name="deposit_id" style="padding:8px 11px">
 <option value="">Select Deposit #</option>
 <?php
 $open_deposits = $db->func_query("SELECT * FROM inv_deposits WHERE deposit_type='cash' and status='open' order by deposit_date desc");
 foreach($open_deposits as $open_deposit)
 {
  ?>
  <option value="<?php echo $open_deposit['deposit_id'];?>"><?php echo $open_deposit['name'];?></option>
  <?php
 }
 ?>

</select>
  &nbsp;<input type="button" class="button" value="Add to Deposit" onclick="addToDeposit('Cash');"></div>
           <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            <tr>
                <td colspan="8" align="center">Date: <input type="text" data-type="date" style="padding:8px 11px" id="filter_transaction_start" value="<?php echo $_GET['filter_transaction_start'];?>"> to <input type="text" data-type="date" style="padding:8px 11px" class="" id="filter_transaction_end" value="<?php echo $_GET['filter_transaction_end'];?>">
                  <input type="button" value="Find" onclick="window.location='credit_management_new.php?tab=cash&filter_transaction_start='+$('#tabCash #filter_transaction_start').val()+'&filter_transaction_end='+$('#tabCash #filter_transaction_end').val()" class="button">
                </td>
            </tr>
            <tr>

            <td colspan="2" style="font-size:40px;font-weight: bold" align="center" class="undeposited_amount">0.00</td>
            <td colspan="6" align="center"><h3>Undeposited Cash Transactions</h3><small><i>Undeposited Amount: $<?php echo number_format($db->func_query_first_cell("SELECT SUM(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,4))='cash' and lower(a.order_status) in ('shipped') and a.deposit_id=0")  ,2);?></i></small></td>
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
            <?php
           
            foreach($cashs as $row)
            {
              if($row['cash_split']>0.00)
              {
                $row['net_amount'] = $row['cash_split'];
              }
              ?>
              <tr class="list_items">
              <td><input type="checkbox" name="transaction_id[]" class="undeposited_checkbox" value="<?php echo $row['order_id'];?>" data-value="<?php echo $row['net_amount']+$row['payment_fee'];?>"></td>
              <td><?php echo americanDate($row['order_date']);?></td>
              
              <td><?php echo ($row['order_id']?linkToOrder($row['order_id']):$row['order_id']);?></td>
              <td><?php echo linkToProfile($row['email']) .($row['net_amount']<0?' <span style="color:red;font-weight:bold;font-size:30px">&rarr;</span>':' <span style="color:green;font-weight:bold;font-size:30px">&larr;</span>');?></td>
              <td><?php echo '$'.number_format($row['net_amount'],2);?></td>
              <td><?php echo '$'.number_format($row['payment_fee'],2);?></td>
              <td><?php echo '$'.number_format($row['net_amount']+$row['payment_fee'],2);?></td>
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
            $parameters .= '&tab=cash';
            ?>
            <td colspan="8">
              <em><?php echo $splitPage2->display_count("Displaying %s to %s of (%s)");?></em>
              <div class="pagination" style="float:right">
                <?php echo $splitPage2->display_links(10,$parameters);?>
              </div>
            </td>
          </tr>
        </tfoot>

            </table>
            </form>


          </td>

          <td colspan="3" width="40%" valign="top" >
          <div style="text-align: right;margin-top:15px;margin-right:15px"><a class="fancybox2 fancybox.iframe button" href="<?php echo $host_path;?>popupfiles/add_bank_deposit.php?type=cash">Add Cash Deposit</a></div>
  <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            <tr>
            
            <td colspan="6" align="center"><h3>Cash Deposits</h3></td>
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
            $deposits = $db->func_query("SELECT * FROM inv_deposits where deposit_type='cash' group by name ORDER BY deposit_date desc");
            foreach($deposits as $deposit)
            {
              $diff = $db->func_query_first_cell("SELECT SUM(net_amount) from inv_orders where deposit_id='".$deposit['deposit_id']."'");
              ?>
              <tr>
              <td><?php echo date('m/d/Y',strtotime($deposit['deposit_date']));?></td>
              <td><a class="fancyboxX3 fancybox.iframe" href="<?php echo $host_path;?>popupfiles/deposit_transactions.php?deposit_id=<?php echo $deposit['deposit_id'];?>&deposit_type=cash"><?php echo $deposit['name'];?></a></td>
              <td><?php echo $db->func_query_first_cell("SELECT COUNT(*) from inv_orders where deposit_id='".$deposit['deposit_id']."'");?></td>
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
            <form id="deposited_cash_frm">
            <div align="center" style="font-weight:bold">Ref ID: <input type="text" style="padding:8px 11px" name="filter_transaction_id" value=""> Deposit Date: <input type="text" data-type="date" style="padding:8px 11px" name="filter_deposit_start"> to <input type="text" data-type="date" style="padding:8px 11px" name="filter_deposit_end"> Received Date: <input type="text" data-type="date" style="padding:8px 11px" name="filter_received_start"> to <input type="text" data-type="date" style="padding:8px 11px" name="filter_received_end"><br><br><input type="button" value="Find" onclick="depositedCashFunds();" class="button"></div>
            <input type="hidden" name="action" value="load_cash_funds">

            </form>


            <table width="90%" cellpadding="0" cellspacing="0" class="xtable" id="deposited_cash_table">
            
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
          </table>
    </div>

    <!-- end cash tab -->


    <!-- tab behalf -->
    <div id="tabBehalf" class="makeTabs">
     <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
      <tbody>

        <tr>
          
          

          <td colspan="5" width="60%" valign="top">

          <form id="deposit_to_frm">
 <div style="text-align: left;margin-top:15px;margin-left:15px">

 <select name="deposit_id" style="padding:8px 11px">
 <option value="">Select Deposit #</option>
 <?php
 $open_deposits = $db->func_query("SELECT * FROM inv_deposits WHERE deposit_type='behalf' and status='open' order by deposit_date desc");
 foreach($open_deposits as $open_deposit)
 {
  ?>
  <option value="<?php echo $open_deposit['deposit_id'];?>"><?php echo $open_deposit['name'];?></option>
  <?php
 }
 ?>

</select>
  &nbsp;<input type="button" class="button" value="Add to Deposit" onclick="addToDeposit('Behalf');"></div>
           <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            <tr>
                <td colspan="8" align="center">Date: <input type="text" data-type="date" style="padding:8px 11px" id="filter_transaction_start" value="<?php echo $_GET['filter_transaction_start'];?>"> to <input type="text" data-type="date" style="padding:8px 11px" class="" id="filter_transaction_end" value="<?php echo $_GET['filter_transaction_end'];?>">
                  <input type="button" value="Find" onclick="window.location='credit_management_new.php?tab=behalf&filter_transaction_start='+$('#tabBehalf #filter_transaction_start').val()+'&filter_transaction_end='+$('#tabBehalf #filter_transaction_end').val()" class="button">
                </td>
            </tr>
            <tr>

            <td colspan="2" style="font-size:40px;font-weight: bold" align="center" class="undeposited_amount">0.00</td>
            <td colspan="6" align="center"><h3>Undeposited Behalf Transactions</h3><small><i>Undeposited Amount: $<?php echo number_format($db->func_query_first_cell("SELECT SUM(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,6))='behalf' and lower(a.order_status) in ('shipped') and a.deposit_id=0")  ,2);?></i></small></td>
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
            <?php
           
            foreach($behalfs as $row)
            {
              
              ?>
              <tr class="list_items">
              <td><input type="checkbox" name="transaction_id[]" class="undeposited_checkbox" value="<?php echo $row['order_id'];?>" data-value="<?php echo $row['net_amount'];?>"></td>
              <td><?php echo americanDate($row['order_date']);?></td>
              
              <td><?php echo ($row['order_id']?linkToOrder($row['order_id']):$row['order_id']);?></td>
              <td><?php echo linkToProfile($row['email']) .($row['net_amount']<0?' <span style="color:red;font-weight:bold;font-size:30px">&rarr;</span>':' <span style="color:green;font-weight:bold;font-size:30px">&larr;</span>');?></td>
              <td><?php echo '$'.number_format($row['net_amount'],2);?></td>
              <td><?php echo '$'.number_format($row['payment_fee'],2);?></td>
              <td><?php echo '$'.number_format($row['net_amount']+$row['payment_fee'],2);?></td>
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
            $parameters .= '&tab=behalf';
            ?>
            <td colspan="8">
              <em><?php echo $splitPage3->display_count("Displaying %s to %s of (%s)");?></em>
              <div class="pagination" style="float:right">
                <?php echo $splitPage3->display_links(10,$parameters);?>
              </div>
            </td>
          </tr>
        </tfoot>

            </table>
            </form>


          </td>

          <td colspan="3" width="40%" valign="top" >
          <div style="text-align: right;margin-top:15px;margin-right:15px"><a class="fancybox2 fancybox.iframe button" href="<?php echo $host_path;?>popupfiles/add_bank_deposit.php?type=behalf">Add Behalf Deposit</a></div>
  <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            <tr>
            
            <td colspan="6" align="center"><h3>Behalf Deposits</h3></td>
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
            $deposits = $db->func_query("SELECT * FROM inv_deposits where deposit_type='behalf' group by name ORDER BY deposit_date desc");
            foreach($deposits as $deposit)
            {
              $diff = $db->func_query_first_cell("SELECT SUM(net_amount) from inv_orders where deposit_id='".$deposit['deposit_id']."'");
              ?>
              <tr>
              <td><?php echo date('m/d/Y',strtotime($deposit['deposit_date']));?></td>
              <td><a class="fancyboxX3 fancybox.iframe" href="<?php echo $host_path;?>popupfiles/deposit_transactions.php?deposit_id=<?php echo $deposit['deposit_id'];?>&deposit_type=behalf"><?php echo $deposit['name'];?></a></td>
              <td><?php echo $db->func_query_first_cell("SELECT COUNT(*) from inv_orders where deposit_id='".$deposit['deposit_id']."'");?></td>
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
            <form id="deposited_behalf_frm">
            <div align="center" style="font-weight:bold">Ref ID: <input type="text" style="padding:8px 11px" name="filter_transaction_id" value=""> Deposit Date: <input type="text" data-type="date" style="padding:8px 11px" name="filter_deposit_start"> to <input type="text" data-type="date" style="padding:8px 11px" name="filter_deposit_end"> Received Date: <input type="text" data-type="date" style="padding:8px 11px" name="filter_received_start"> to <input type="text" data-type="date" style="padding:8px 11px" name="filter_received_end"><br><br><input type="button" value="Find" onclick="depositedBehalfFunds();" class="button"></div>
            <input type="hidden" name="action" value="load_behalf_funds">

            </form>


            <table width="90%" cellpadding="0" cellspacing="0" class="xtable" id="deposited_behalf_table">
            
            <thead>
            <tr>
            <th>Date</th>
            <th>Deposit #</th>
            <th>Received Date</th>
            <th>Ref ID</th>
            <th>Sender/Recipient</th>
            <th colspan="3">Amount</th>
            </tr> 
            <tr>
            <th colspan="5"></th>
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

    <!-- end behalf tab -->

    <!-- tab check -->
    <div id="tabCheck" class="makeTabs">
     <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
      <tbody>

        <tr>
          
          

          <td colspan="5" width="60%" valign="top">

          <form id="deposit_to_frm">
 <div style="text-align: left;margin-top:15px;margin-left:15px">

 <select name="deposit_id" style="padding:8px 11px">
 <option value="">Select Deposit #</option>
 <?php
 $open_deposits = $db->func_query("SELECT * FROM inv_deposits WHERE deposit_type='check' and status='open' order by deposit_date desc");
 foreach($open_deposits as $open_deposit)
 {
  ?>
  <option value="<?php echo $open_deposit['deposit_id'];?>"><?php echo $open_deposit['name'];?></option>
  <?php
 }
 ?>

</select>
  &nbsp;<input type="button" class="button" value="Add to Deposit" onclick="addToDeposit('Check');"></div>
           <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            <tr>
                <td colspan="8" align="center">Date: <input type="text" data-type="date" style="padding:8px 11px" id="filter_transaction_start" value="<?php echo $_GET['filter_transaction_start'];?>"> to <input type="text" data-type="date" style="padding:8px 11px" class="" id="filter_transaction_end" value="<?php echo $_GET['filter_transaction_end'];?>">
                  <input type="button" value="Find" onclick="window.location='credit_management_new.php?tab=check&filter_transaction_start='+$('#tabCheck #filter_transaction_start').val()+'&filter_transaction_end='+$('#tabCheck #filter_transaction_end').val()" class="button">
                </td>
            </tr>
            <tr>

            <td colspan="2" style="font-size:40px;font-weight: bold" align="center" class="undeposited_amount">0.00</td>
            <td colspan="6" align="center"><h3>Undeposited Check Transactions</h3><small><i>Undeposited Amount: $<?php echo number_format($db->func_query_first_cell("SELECT SUM(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,5))='check' and lower(a.order_status) in ('shipped') and a.deposit_id=0")  ,2);?></i></small></td>
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
            <?php
           
            foreach($checks as $row)
            {
              
              ?>
              <tr class="list_items">
              <td><input type="checkbox" name="transaction_id[]" class="undeposited_checkbox" value="<?php echo $row['order_id'];?>" data-value="<?php echo $row['net_amount'];?>"></td>
              <td><?php echo americanDate($row['order_date']);?></td>
              
              <td><?php echo ($row['order_id']?linkToOrder($row['order_id']):$row['order_id']);?></td>
              <td><?php echo linkToProfile($row['email']) .($row['net_amount']<0?' <span style="color:red;font-weight:bold;font-size:30px">&rarr;</span>':' <span style="color:green;font-weight:bold;font-size:30px">&larr;</span>');?></td>
              <td><?php echo '$'.number_format($row['net_amount'],2);?></td>
              <td><?php echo '$'.number_format($row['payment_fee'],2);?></td>
              <td><?php echo '$'.number_format($row['net_amount']+$row['payment_fee'],2);?></td>
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
            $parameters .= '&tab=check';
            ?>
            <td colspan="8">
              <em><?php echo $splitPage4->display_count("Displaying %s to %s of (%s)");?></em>
              <div class="pagination" style="float:right">
                <?php echo $splitPage4->display_links(10,$parameters);?>
              </div>
            </td>
          </tr>
        </tfoot>

            </table>
            </form>


          </td>

          <td colspan="3" width="40%" valign="top" >
          <div style="text-align: right;margin-top:15px;margin-right:15px"><a class="fancybox2 fancybox.iframe button" href="<?php echo $host_path;?>popupfiles/add_bank_deposit.php?type=check">Add Check Deposit</a></div>
  <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            <tr>
            
            <td colspan="6" align="center"><h3>Check Deposits</h3></td>
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
            $deposits = $db->func_query("SELECT * FROM inv_deposits where deposit_type='check' group by name ORDER BY deposit_date desc");
            foreach($deposits as $deposit)
            {
              $diff = $db->func_query_first_cell("SELECT SUM(net_amount) from inv_orders where deposit_id='".$deposit['deposit_id']."'");
              ?>
              <tr>
              <td><?php echo date('m/d/Y',strtotime($deposit['deposit_date']));?></td>
              <td><a class="fancyboxX3 fancybox.iframe" href="<?php echo $host_path;?>popupfiles/deposit_transactions.php?deposit_id=<?php echo $deposit['deposit_id'];?>&deposit_type=check"><?php echo $deposit['name'];?></a></td>
              <td><?php echo $db->func_query_first_cell("SELECT COUNT(*) from inv_orders where deposit_id='".$deposit['deposit_id']."'");?></td>
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
            <form id="deposited_check_frm">
            <div align="center" style="font-weight:bold">Ref ID: <input type="text" style="padding:8px 11px" name="filter_transaction_id" value=""> Deposit Date: <input type="text" data-type="date" style="padding:8px 11px" name="filter_deposit_start"> to <input type="text" data-type="date" style="padding:8px 11px" name="filter_deposit_end"> Received Date: <input type="text" data-type="date" style="padding:8px 11px" name="filter_received_start"> to <input type="text" data-type="date" style="padding:8px 11px" name="filter_received_end"><br><br><input type="button" value="Find" onclick="depositedCheckFunds();" class="button"></div>
            <input type="hidden" name="action" value="load_check_funds">

            </form>


            <table width="90%" cellpadding="0" cellspacing="0" class="xtable" id="deposited_check_table">
            
            <thead>
            <tr>
            <th>Date</th>
            <th>Deposit #</th>
            <th>Received Date</th>
            <th>Ref ID</th>
            <th>Sender/Recipient</th>
            <th colspan="3">Amount</th>
            </tr> 
            <tr>
            <th colspan="5"></th>
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

    <!-- end check tab -->


    <!-- tab wire -->
    <?php
    $data_type='wire';
    ?>
    <div id="tab<?php echo ucfirst($data_type);?>" class="makeTabs">
     <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
      <tbody>

        <tr>
          
          

          <td colspan="5" width="60%" valign="top">

          <form id="deposit_to_frm">
 <div style="text-align: left;margin-top:15px;margin-left:15px">

 <select name="deposit_id" style="padding:8px 11px">
 <option value="">Select Deposit #</option>
 <?php
 $open_deposits = $db->func_query("SELECT * FROM inv_deposits WHERE deposit_type='".$data_type."' and status='open' order by deposit_date desc");
 foreach($open_deposits as $open_deposit)
 {
  ?>
  <option value="<?php echo $open_deposit['deposit_id'];?>"><?php echo $open_deposit['name'];?></option>
  <?php
 }
 ?>

</select>
  &nbsp;<input type="button" class="button" value="Add to Deposit" onclick="addToDeposit('<?php echo ucfirst($data_type);?>');"></div>
           <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            <tr>
                <td colspan="8" align="center">Date: <input type="text" data-type="date" style="padding:8px 11px" id="filter_transaction_start" value="<?php echo $_GET['filter_transaction_start'];?>"> to <input type="text" data-type="date" style="padding:8px 11px" class="" id="filter_transaction_end" value="<?php echo $_GET['filter_transaction_end'];?>">
                  <input type="button" value="Find" onclick="window.location='credit_management_new.php?tab=<?php echo $data_type;?>&filter_transaction_start='+$('#tab<?php echo ucfirst($data_type);?> #filter_transaction_start').val()+'&filter_transaction_end='+$('#tab<?php echo ucfirst($data_type);?> #filter_transaction_end').val()" class="button">
                </td>
            </tr>
            <tr>

            <td colspan="2" style="font-size:40px;font-weight: bold" align="center" class="undeposited_amount">0.00</td>
            <td colspan="6" align="center"><h3>Undeposited <?php echo ucfirst($data_type);?> Transactions</h3><small><i>Undeposited Amount: $<?php echo number_format($db->func_query_first_cell("SELECT SUM(a.sub_total+a.shipping_amount+a.tax)+(select COALESCE(sum(c.amount),0) from oc_voucher_history c where c.order_id=a.order_id) as net_amount FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id  and lower(left(b.payment_method,4)) in ('wire','bank') and lower(a.order_status) in ('shipped') and a.deposit_id=0")  ,2);?></i></small></td>
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
            <?php
           
            foreach($wires as $row)
            {
              
              ?>
              <tr class="list_items">
              <td><input type="checkbox" name="transaction_id[]" class="undeposited_checkbox" value="<?php echo $row['order_id'];?>" data-value="<?php echo $row['net_amount'];?>"></td>
              <td><?php echo americanDate($row['order_date']);?></td>
              
              <td><?php echo ($row['order_id']?linkToOrder($row['order_id']):$row['order_id']);?></td>
              <td><?php echo linkToProfile($row['email']) .($row['net_amount']<0?' <span style="color:red;font-weight:bold;font-size:30px">&rarr;</span>':' <span style="color:green;font-weight:bold;font-size:30px">&larr;</span>');?></td>
              <td><?php echo '$'.number_format($row['net_amount'],2);?></td>
              <td><?php echo '$'.number_format($row['payment_fee'],2);?></td>
              <td><?php echo '$'.number_format($row['net_amount']+$row['payment_fee'],2);?></td>
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
            $parameters .= '&tab='.$data_type;
            ?>
            <td colspan="8">
              <em><?php echo $splitPage5->display_count("Displaying %s to %s of (%s)");?></em>
              <div class="pagination" style="float:right">
                <?php echo $splitPage5->display_links(10,$parameters);?>
              </div>
            </td>
          </tr>
        </tfoot>

            </table>
            </form>


          </td>

          <td colspan="3" width="40%" valign="top" >
          <div style="text-align: right;margin-top:15px;margin-right:15px"><a class="fancybox2 fancybox.iframe button" href="<?php echo $host_path;?>popupfiles/add_bank_deposit.php?type=<?php echo $data_type;?>">Add <?php echo ucfirst($data_type);?> Deposit</a></div>
  <table width="90%" cellpadding="0" cellspacing="0" class="xtable">
            <tr>
            
            <td colspan="6" align="center"><h3><?php echo ucfirst($data_type);?> Deposits</h3></td>
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
            $deposits = $db->func_query("SELECT * FROM inv_deposits where deposit_type='".$data_type."' group by name ORDER BY deposit_date desc");
            foreach($deposits as $deposit)
            {
              $diff = $db->func_query_first_cell("SELECT SUM(net_amount) from inv_orders where deposit_id='".$deposit['deposit_id']."'");
              ?>
              <tr>
              <td><?php echo date('m/d/Y',strtotime($deposit['deposit_date']));?></td>
              <td><a class="fancyboxX3 fancybox.iframe" href="<?php echo $host_path;?>popupfiles/deposit_transactions.php?deposit_id=<?php echo $deposit['deposit_id'];?>&deposit_type=<?php echo $data_type;?>"><?php echo $deposit['name'];?></a></td>
              <td><?php echo $db->func_query_first_cell("SELECT COUNT(*) from inv_orders where deposit_id='".$deposit['deposit_id']."'");?></td>
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
            <form id="deposited_<?php echo $data_type;?>_frm">
            <div align="center" style="font-weight:bold">Ref ID: <input type="text" style="padding:8px 11px" name="filter_transaction_id" value=""> Deposit Date: <input type="text" data-type="date" style="padding:8px 11px" name="filter_deposit_start"> to <input type="text" data-type="date" style="padding:8px 11px" name="filter_deposit_end"> Received Date: <input type="text" data-type="date" style="padding:8px 11px" name="filter_received_start"> to <input type="text" data-type="date" style="padding:8px 11px" name="filter_received_end"><br><br><input type="button" value="Find" onclick="depositedWireFunds();" class="button"></div>
            <input type="hidden" name="action" value="load_<?php echo $data_type;?>_funds">

            </form>


            <table width="90%" cellpadding="0" cellspacing="0" class="xtable" id="deposited_<?php echo $data_type;?>_table">
            
            <thead>
            <tr>
            <th>Date</th>
            <th>Deposit #</th>
            <th>Received Date</th>
            <th>Ref ID</th>
            <th>Sender/Recipient</th>
            <th colspan="3">Amount</th>
            </tr> 
            <tr>
            <th colspan="5"></th>
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

    <!-- end check tab -->

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
              <td><input type="checkbox" name="transaction_id[]" class="undeposited_checkbox" value="<?php echo $row['id'];?>" data-value="<?php echo $row['net_amount'];?>"></td>
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
              <td><a class="fancyboxX3 fancybox.iframe" href="<?php echo $host_path;?>popupfiles/deposit_transactions.php?deposit_id=<?php echo $deposit['deposit_id'];?>"><?php echo $deposit['name'];?></a></td>
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
  </html>
  <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
  <script type="text/javascript" src="js/newmultiselect.js"></script>

  <script>

   $(function () {
            $('#undeposited_table').multiSelect({
               actcls: 'highlightx',
               selector: 'tbody .list_items',
               except: ['form'],
               callback: function (items) {
                  traverseCheckboxes('#undeposited_table', '.undeposited_checkbox');
               }
            });
         })


  function checkAll(obj)
  {
    // console.log('here');
    // console.log($(obj).is(":checked"));
    $(obj).parent().parent().parent().parent().find('.undeposited_checkbox').prop('checked',$(obj).is(":checked"));
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
    // console.log($(this).val());
    // console.log($(this).is(":checked"));
    $this = $(this).parent().parent().parent().parent().parent();
    var amount = 0.00;
    $this.find('.undeposited_checkbox').each(function(){
      if(this.checked)
      {
        amount+=parseFloat($(this).attr('data-value'));
      }
    })

     $this.find('.undeposited_amount').html(amount);
    // if($(this).is(":checked"))
    // {
    //   $this.find('.undeposited_amount').html((parseFloat($this.find('.undeposited_amount').html()) + parseFloat($(this).attr('data-value'))).toFixed(2));
    // }
    // else
    // {
      
    //   $this.find('.undeposited_amount').html((parseFloat($this.find('.undeposited_amount').html()) - parseFloat($(this).attr('data-value'))).toFixed(2));
    // }
  });
  $(document).ready(function(){
loadData('balance_due');
depositedFunds();
depositedCashFunds();
depositedBehalfFunds();
depositedCheckFunds();
depositedWireFunds();

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
      if($('tab'+type).find('select[name=deposit_id]').val()=='')
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
  </script>