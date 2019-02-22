<?php
include_once 'auth.php';
include_once 'inc/functions.php';
page_permission('sales_dashboard');
if(!isset($_GET['hide_header']))
{
  unset($_SESSION['hide_header']);
}

if(isset($_POST['action']) && $_POST['action']=='load_comment')
{
  global $db;
  $date = explode(".",$_POST['month_year']);
  $month = $date[0];
  $year = $date[0];
  $deno = (int)$_POST['deno'];
  switch($deno)
  {
    case 3000;
    $deno_end = 5000;
    break;

    case 5000;
    $deno_end = 7500;
    break;

    case 7500;
    $deno_end = 10000;
    break;

    case 10000;
    $deno_end = 15000;
    break;

    case 15000;
    $deno_end = 20000;
    break;

    case 20000;
    $deno_end = 30000;
    break;

    default:
    $deno_end = 50000;
    break;
  }
  $row = $db->func_query("SELECT customer_name,email from inv_orders where lower(order_status) in ('processed','shipped','completed') and email not like '%@marketplace.amazon%' and month(order_date)='".$month."' and year(order_date)='".$year."' and sum(sub_total+shipping_amount+tax)>'".$deno."' and sum(sub_total+shipping_amount+tax)<='.$deno_end.'");

  $html = '';
  foreach($rows as $row)
  {
    $html.=$row['customer_name'].' ('.$row['email'].')<br>';
  }
echo $html;exit;
}
function getGenericQuery($start=500,$end=1000)
{
  $query = "select count(x.rec_$start) rec_$start,x.monthx,x.yearx,x.emailx from ( select count(*) rec_$start,order_date,month(order_date) monthx,year(order_date) yearx,group_concat(email) as emailx from inv_orders where lower(order_status) in ('processed','shipped','completed') and email not like '%@marketplace.amazon%' group by lower(email),month(order_date),year(order_date) having sum(sub_total+shipping_amount+tax)>$start and sum(sub_total+shipping_amount+tax)<=$end order by order_date desc) x group by x.monthx,x.yearx order by x.order_date desc limit 13";
  return $query;
}

$current_month= date('m');
$current_year = date('Y');
$last_month = date("m", strtotime("first day of previous month"));
$last_year = date("Y", strtotime("first day of previous month"));

$territory_query="select a.territory,sum(b.order_price) current_month_total from oc_zone a,inv_orders b,inv_orders_details c where b.order_id=c.order_id and a.zone_id=c.zone_id and lower(order_status) in ('shipped','processed','completed') and month(b.order_date)='".$current_month."' and lower(b.email) not like '%@phonepartsusa.com%' and year(b.order_date)='".$current_year."' group by a.territory order by 2 desc ";
$territories = $cache->get('sales_related_report.territories.'.$current_month.$current_year);
if(!$territories)
{
$territories = $db->func_query($territory_query);
$cache->set('sales_related_report.territories.'.$current_month.$current_year,$territories);
}
$territory_query="select a.territory,sum(b.order_price) prev_month_total from oc_zone a,inv_orders b,inv_orders_details c where b.order_id=c.order_id and a.zone_id=c.zone_id and lower(b.email) not like '%@phonepartsusa.com%' and lower(order_status) in ('shipped','processed','completed') and month(b.order_date)='".$last_month."' and year(b.order_date)='".$last_year."' group by a.territory order by 1 ";
$territories2 = $cache->get('sales_related_report.territories.'.$last_month.$last_year);
if(!$territories2)
{
$territories2 = $db->func_query($territory_query);
$cache->set('sales_related_report.territories.'.$last_month.$last_year,$territories2);
}


$territory_query="select avg(x.current_month_total) as avg_year_total ,x.territory from ( select sum(b.order_price) current_month_total,a.territory from oc_zone a,inv_orders b,inv_orders_details c where b.order_id=c.order_id and a.zone_id=c.zone_id and lower(b.email) not like '%@phonepartsusa.com%' and lower(order_status) in ('shipped','processed','completed') AND date(b.order_date) >CURRENT_DATE() - INTERVAL 12 MONTH and a.territory<>'' group by a.territory, month(b.order_date),year(b.order_date) ) x group by x.territory
";
$territories3 = $cache->get('sales_related_report.territories2');
if(!$territories3)
{
$territories3 = $db->func_query($territory_query);
$cache->set('sales_related_report.territories2',$territories3);
}
$result = array();
foreach($territories as $row)
{
  $result[$row['territory']]['current_month_total'] = $row['current_month_total']; 
}
foreach($territories2 as $row)
{
  $result[$row['territory']]['prev_month_total'] = $row['prev_month_total']; 
}

foreach($territories3 as $row)
{
  $result[$row['territory']]['avg_year_total'] = $row['avg_year_total']; 
}


if(isset($_POST['type']) && $_POST['type']=='monthly_report')
{
  // echo getGenericQuery(20000,30000);exit;

$result2 = $cache->get('sales_related_report.monthly');
if(!$result2)
{
    $rows0 = $db->func_query(getGenericQuery(1,500));
    $rows1 = $db->func_query(getGenericQuery(500,1000));
    $rows2 = $db->func_query(getGenericQuery(1000,1500));
    $rows3 = $db->func_query(getGenericQuery(1500,2000));
    $rows4 = $db->func_query(getGenericQuery(2000,3000));
    $rows5 = $db->func_query(getGenericQuery(3000,5000));
    $rows6 = $db->func_query(getGenericQuery(5000,7500));
    $rows7 = $db->func_query(getGenericQuery(7500,10000));
    $rows8 = $db->func_query(getGenericQuery(10000,15000));
    $rows9 = $db->func_query(getGenericQuery(15000,20000));
    $rows10 = $db->func_query(getGenericQuery(20000,30000));
    $rows11 = $db->func_query(getGenericQuery(30000,100000));

    $result2 = array();
    
    foreach($rows0 as $_row)
    {
      $result2[$_row['monthx'].'.'.$_row['yearx']][] = $_row;
    }

    foreach($rows1 as $_row)
    {
      $result2[$_row['monthx'].'.'.$_row['yearx']][] = $_row;
    }

    foreach($rows2 as $_row)
    {
      $result2[$_row['monthx'].'.'.$_row['yearx']][] = $_row;
    }

    foreach($rows3 as $_row)
    {
      $result2[$_row['monthx'].'.'.$_row['yearx']][] = $_row;
    }

    foreach($rows4 as $_row)
    {
      $result2[$_row['monthx'].'.'.$_row['yearx']][] = $_row;
    }

    foreach($rows5 as $_row)
    {
      $result2[$_row['monthx'].'.'.$_row['yearx']][] = $_row;
    }

    foreach($rows6 as $_row)
    {
      $result2[$_row['monthx'].'.'.$_row['yearx']][] = $_row;
    }
    foreach($rows7 as $_row)
    {
      $result2[$_row['monthx'].'.'.$_row['yearx']][] = $_row;
    }

    foreach($rows8 as $_row)
    {
      $result2[$_row['monthx'].'.'.$_row['yearx']][] = $_row;
    }

    foreach($rows9 as $_row)
    {
      $result2[$_row['monthx'].'.'.$_row['yearx']][] = $_row;
    }

    foreach($rows10 as $_row)
    {
      $result2[$_row['monthx'].'.'.$_row['yearx']][] = $_row;
    }

    foreach($rows11 as $_row)
    {
      $result2[$_row['monthx'].'.'.$_row['yearx']][] = $_row;
    }
  
  $cache->set('sales_related_report.monthly',$result2);

}
// testObject($result2);
$denom = array('1','500','1000','1500','2000','3000','5000','7500','10000','15000','20000','30000');
  $html = '
  <strong>Customers Ordering Total</strong>
            <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th></th>';


                          foreach($denom as $deno)
                          {
                            if($deno!='1')
                            {

                            $html.='<th>'.$deno.'+'.'</th>';
                            }
                            else
                            {
                              $html.='<th><500</th>';
                            }
                            
                          }
                          
                          $html.='
                          
                          <th>Total</th>
                      </tr>
                  </thead>
                  <tbody>';
                 $k=1;
                 // testObject($result2);exit;
                 foreach($result2 as $key=> $result)
                 {
                  if($k>13) continue;
                  // testObject($result);
                  // echo $key;exit;

                  $date = explode(".", $key);

                  $m = date('M',strtotime($date[1].'-'.$date[0].'-01'));
                  $y = date('Y',strtotime($date[1].'-'.$date[0].'-01'));
                  $html.='
                  <tr>
                  <td style="font-weight: bold">'.$key.'</td>';
                  
                  $i=0;
                  $sum = 0;
                    foreach($denom as $deno)
                    {
                     
                     if(!isset($result[$i]['rec_'.$deno]))
                     {
                       $html.='<td>0</td>';
                      // $i++;
                      continue;
                     }
                     $td = '<td>';
                     if($deno>=3000)
                     {
                     	// $my_deno = '<a href="javascript:void(0)" data-tooltip="'.str_replace(",", "<br>", $result[$i]['emailx']).'">'.round($result[$i]['rec_'.$deno]).'</a>';
                     	$td='<td onmouseout="$(\'.load-comments-'.$k.$i.'\' ).hide();" onmouseover="loadComments(\''.$key.'\',\''.$deno.'\',\''.$k.'\',\''.$i.'\')">';
                     	$my_deno = '<a href="javascript:void(0)" >'.round($result[$i]['rec_'.$deno]).'</a>';

                     	$my_deno.='<div class="load-comments-'.$k.$i.' ajax-dropdown" style="display: none;"></div>';
                     }
                     else
                     {
                     	$my_deno = round($result[$i]['rec_'.$deno]);
                     }
                      $sum = (int)$sum + (int) $result[$i]['rec_'.$deno];
                      
                      $html.=$td.$my_deno.'</td>';
                    

                      
                    $i++;
                    }
                  
                  $html.='<td style="font-weight:bold">'.$sum.'</td>
                  </tr>';
                  
                  $k++;
                 }
                 

                 $html.=' </tbody>
                  
              </table>
';
echo $html;exit;
}

if(isset($_POST['type']) and $_POST['type']=='80_sale')
{

  $rows = $cache->get('sales_related_report.'.$_POST['type']);
  if(!$rows)
  {
  //$rows = $db->func_query("select  a.email,sum(a.order_price) as order_price1 from inv_orders a where a.order_date > CURRENT_DATE - INTERVAL 30 DAY  AND LOWER(a.order_status) in ('processed','shipped','completed') group by a.email having order_price1>500 order by order_price1 desc limit 100");

    $rows = $db->func_query("select  a.email,sum(a.sub_total+a.shipping_amount+a.tax) as order_price1 from inv_orders a
    	inner join inv_customers b
    	on (a.email=b.email)
    	left join inv_customers c
    	on (b.id=c.parent_id)

     where b.parent_id=0 and a.order_date > CURRENT_DATE - INTERVAL 30 DAY  AND LOWER(a.order_status) in ('processed','shipped','completed') and lower(a.email) not like '%@phonepartsusa.com%' group by a.email having order_price1>500 order by order_price1 desc  limit 60");


$cache->set('sales_related_report.'.$_POST['type'],$rows);
}


  $html.='<table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Customer</th>
                          <th>Amount</th>
                          
                      </tr>
                  </thead>
                  <tbody>'; 
                  $_shipping_cost=0.00;       
                  $total_80_sale = 0.00;
                  foreach($rows as $row)
                  {
                    $total_80_sale+=$row['order_price1'];
                    // $_shipping_cost  = $db->func_query_first_cell("select  sum(shipping_cost) from inv_orders a,inv_orders_details b where a.order_id=b.order_id  and a.order_date > CURRENT_DATE - INTERVAL 30 DAY  AND LOWER(a.order_status) in ('processed','shipped','completed') and lower(email)='".strtolower($row['email'])."'");
                    $html.='<tr>';
                    $html.='<td>'.linkToProfile($row['email']).'</td>
                    <td>$'.number_format($row['order_price1'],2).'</td>';
                    $html.'</tr>';
                  }

                  
                  $html.='</tbody>
                  <tfoot>
                  <td style="font-weight:bold" align="right">Total:</td>
                  <td style="font-weight:bold">$'.number_format($total_80_sale,2).'</td>
                  </tfoot>
                  </table>';


echo $html;exit;
}

if(isset($_POST['type']) and $_POST['type']=='profitable_customers')
{

  $rows = $cache->get('sales_related_report.'.$_POST['type']);
  if(!$rows)
  {
  // $rows = $db->func_query("select sum(b.shipping_amount) as shipping_amount, b.email,sum(c.product_price) order_price1,(sum(c.product_price) - sum(c.product_true_cost*c.product_qty)) as profit from inv_orders b,inv_orders_items c where  b.order_id=c.order_id and b.order_date > CURRENT_DATE - INTERVAL 90 DAY AND lower(b.order_status) in ('processed','shipped','completed') group by lower(b.email) having order_price1>500 order by profit desc limit 25");
    $rows = $db->func_query("select sum(b.shipping_amount) as shipping_amount, b.email,sum(b.sub_total) order_price1,(sum(b.sub_total-b.items_true_cost)) as profit from inv_orders b where  b.order_date > CURRENT_DATE - INTERVAL 90 DAY AND lower(b.order_status) in ('processed','shipped','completed') and lower(b.email) not like '%@phonepartsusa.com%' group by lower(b.email) having order_price1>500 order by profit desc limit 25");
$cache->set('sales_related_report.'.$_POST['type'],$rows);
}


  $html.='<table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Agent</th>
                          <th>Customer</th>
                          <th>Sale of Parts</th>
                          <th>Parts Profit</th>
                          <th>Shipping</th>
                          <th>Return Credit</th>
                          <th>Return Shipping</th>
                          <th>Apprc.</th>
                          <th>Net</th>
                          <th>%</th>
                          
                      </tr>
                  </thead>';        
                  foreach($rows as $row)
                  {


                    $_temp = $cache->get('sales_related_report.'.$_POST['type'].'.inner.'.md5($row['email']));
                    if(!$_temp)
                    {
                      $shipping_cost = $row['shipping_amount'];
                    // $shipping_cost = (float)$db->func_query_first_cell("SELECT SUM(b.shipping_cost) FROM inv_orders a inner join inv_orders_details b ON (a.order_id=b.order_id) where a.order_date > CURRENT_DATE - INTERVAL 90 DAY AND lower(a.order_status) in ('processed','shipped','completed') and  a.email='".$row['email']."'");  
                    $shipping_paid = (float)$db->func_query_first_cell("SELECT SUM(b.shipping_cost)+sum(b.insurance_cost) FROM inv_orders a left join inv_shipstation_transactions b ON (cast(a.order_id as char(50))=cast(b.order_id as char(50))) where a.order_date > CURRENT_DATE - INTERVAL 90 DAY AND lower(a.order_status) in ('processed','shipped','completed') and b.voided=0 and a.email='".$row['email']."'");

                    $return_credit = (float)$db->func_query_first_cell("SELECT SUM(amount) FROM oc_voucher where status=1 and to_email='".$row['email']."' AND date_added > CURRENT_DATE - INTERVAL 90 DAY");

                    $return_shipping = 0.00;

                  // $appr = (float)$db->func_query_first_cell("SELECT SUM(b.amount) FROM oc_order_voucher b,inv_orders a where cast(a.order_id as char(50))=cast(b.order_id as char(50)) and  a.email='".$row['email']."' and b.code not like '%LBB%' AND a.order_date > CURRENT_DATE - INTERVAL 90 DAY");
                  $appr = (float)$db->func_query_first_cell("SELECT sum(b.amount) FROM oc_order_voucher b,inv_orders a,oc_voucher c where cast(a.order_id as char(50))=cast(b.order_id as char(50)) and b.voucher_id=c.voucher_id and trim(lower(a.email))='".trim(strtolower($row['email']))."' and c.status=1 and c.reason_id in (6,18) AND a.order_date > CURRENT_DATE - INTERVAL 90 DAY");
                    $customer_details = $db->func_query_first("SELECT a.user_id,concat(a.firstname,' ',a.lastname) as name from inv_customers a WHERE lower(a.email)='".strtolower($row['email'])."'");
                  
                    $_temp = array(
                      'shipping_cost'=>$shipping_cost,
                      'shipping_paid'=>$shipping_paid,
                      'return_credit'=>$return_credit,
                      'return_shipping'=>$return_shipping,
                      'appr'=>$appr,
                      'customer_details'=>$customer_details

                      );
                    $cache->set('sales_related_report.'.$_POST['type'].'.inner.'.md5($row['email']),$_temp);

                  }

                    $net_amount = (float)$row['profit'] - (float)$_temp['shipping_paid'] - (float)$_temp['return_shipping'] - (float)$_temp['appr'];

                    $net_perc = (( $net_amount)/ ($row['order_price1']+$_temp['shipping_cost']))*100;

                    $html.='<tr>';
                    $html.='
                    <td>'.get_username($_temp['customer_details']['user_id']).'</td>
                    <td>'.$_temp['customer_details']['name'].'<br>'.linkToProfile($row['email']).'</td>
                    <td>$'.number_format($row['order_price1']+$_temp['shipping_cost'],2).'</td>
                    <td>$'.number_format( $row['profit'],2).'</td>
                    <td>-$'.number_format($_temp['shipping_paid'],2).'</td>
                    <td>$'.number_format($_temp['return_credit'],2).'</td>
                    <td>-$'.number_format($_temp['return_shipping'],2).'</td>
                    
                    <td>-$'.number_format($_temp['appr'],2).'</td>
                    <td>$'.number_format($net_amount,2).'</td>
                    <td>'.number_format($net_perc,2).'%</td>


                    ';
                    $html.'</tr>';
                  }

                  
                  $html.='<tbody>

                  </tbody>
                  </table>';


echo $html;exit;
}


if(isset($_POST['type']) and $_POST['type']=='least_profitable_customers')
{

  $rows = $cache->get('sales_related_report.'.$_POST['type']);
  if(!$rows)
  {
  // $rows = $db->func_query("select a.user_id, concat(a.firstname,' ',a.lastname) as name,a.email,sum(b.order_price) order_price1,sum(b.profit) as profit from inv_customers a,inv_orders b where a.email=b.email and b.order_date > CURRENT_DATE - INTERVAL 90 DAY AND lower(b.order_status) in ('processed','shipped','completed') group by a.email having order_price1>500 order by profit asc  limit 25");
    
    // $rows = $db->func_query("select sum(b.shipping_amount) as shipping_amount, b.email,sum(c.product_price) order_price1,(sum(c.product_price) - sum(c.product_true_cost*c.product_qty)) as profit from inv_orders b,inv_orders_items c where  b.order_id=c.order_id and b.order_date > CURRENT_DATE - INTERVAL 90 DAY AND lower(b.order_status) in ('processed','shipped','completed') group by lower(b.email) having order_price1>500 order by  profit asc limit 25");

     $rows = $db->func_query("select sum(b.shipping_amount) as shipping_amount, b.email,sum(b.sub_total) order_price1,(sum(b.sub_total-b.items_true_cost)) as profit from inv_orders b where  b.order_date > CURRENT_DATE - INTERVAL 90 DAY and lower(b.email) not like '%@phonepartsusa.com%' AND lower(b.order_status) in ('processed','shipped','completed') group by lower(b.email) having order_price1>500 order by profit asc limit 25");

$cache->set('sales_related_report.'.$_POST['type'],$rows);
}


  $html.='<table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Agent</th>
                          <th>Customer</th>
                          <th>Sale of Parts</th>
                          <th>Parts Profit</th>
                          <th>Shipping</th>
                          <th>Return Credit</th>
                          <th>Return Shipping</th>
                          <th>Apprc.</th>
                          <th>Net</th>
                          <th>%</th>
                          
                      </tr>
                  </thead>';        
                  foreach($rows as $row)
                  {


                    $_temp = $cache->get('sales_related_report.'.$_POST['type'].'.inner.'.md5($row['email']));
                    if(!$_temp)
                    {
                      $shipping_cost = $row['shipping_amount'];
                      // $shipping_cost = (float)$db->func_query_first_cell("SELECT SUM(b.shipping_cost) FROM inv_orders a inner join inv_orders_details b ON (a.order_id=b.order_id) where a.order_date > CURRENT_DATE - INTERVAL 90 DAY AND lower(a.order_status) in ('processed','shipped','completed') and  a.email='".$row['email']."'");  
                    $shipping_paid = (float)$db->func_query_first_cell("SELECT SUM(b.shipping_cost)+sum(b.insurance_cost) FROM inv_orders a left join inv_shipstation_transactions b ON (cast(a.order_id as char(50))=cast(b.order_id as char(50))) where a.order_date > CURRENT_DATE - INTERVAL 90 DAY AND lower(a.order_status) in ('processed','shipped','completed') and b.voided=0 and a.email='".$row['email']."'");

                    $return_credit = (float)$db->func_query_first_cell("SELECT SUM(amount) FROM oc_voucher where status=1 and to_email='".$row['email']."' AND date_added > CURRENT_DATE - INTERVAL 90 DAY");

                    $return_shipping = 0.00;

                        // $appr = (float)$db->func_query_first_cell("SELECT SUM(b.amount) FROM oc_order_voucher b,inv_orders a where cast(a.order_id as char(50))=cast(b.order_id as char(50)) and  a.email='".$row['email']."' and b.code not like '%LBB%' AND a.order_date > CURRENT_DATE - INTERVAL 90 DAY");
                     $appr = (float)$db->func_query_first_cell("SELECT sum(b.amount) FROM oc_order_voucher b,inv_orders a,oc_voucher c where cast(a.order_id as char(50))=cast(b.order_id as char(50)) and b.voucher_id=c.voucher_id and trim(lower(a.email))='".trim(strtolower($row['email']))."' and c.status=1 and c.reason_id in (6,18) AND a.order_date > CURRENT_DATE - INTERVAL 90 DAY");

                    $customer_details = $db->func_query_first("SELECT a.user_id,concat(a.firstname,' ',a.lastname) as name from inv_customers a WHERE lower(a.email)='".strtolower($row['email'])."'");
                  
                    $_temp = array(
                      'shipping_cost'=>$shipping_cost,
                      'shipping_paid'=>$shipping_paid,
                      'return_credit'=>$return_credit,
                      'return_shipping'=>$return_shipping,
                      'appr'=>$appr,
                      'customer_details'=>$customer_details

                      );
                    $cache->set('sales_related_report.'.$_POST['type'].'.inner.'.md5($row['email']),$_temp);

                  }

                         $net_amount =  (float)$row['profit'] - (float)$_temp['shipping_paid'] - (float)$_temp['return_shipping'] - (float)$_temp['appr'];

                    $net_perc = (( $net_amount)/ ($row['order_price1']+$_temp['shipping_cost']))*100;

                    $html.='<tr>';
                    $html.='
                    <td>'.get_username($_temp['customer_details']['user_id']).'</td>
                    <td>'.$_temp['customer_details']['name'].'<br>'.linkToProfile($row['email']).'</td>
                    <td>$'.number_format($row['order_price1']+$_temp['shipping_cost'],2).'</td>
                    <td>$'.number_format( $row['profit'],2).'</td>
                    <td>-$'.number_format($_temp['shipping_paid'],2).'</td>
                    <td>$'.number_format($_temp['return_credit'],2).'</td>
                    <td>-$'.number_format($_temp['return_shipping'],2).'</td>
                    
                    <td>-$'.number_format($_temp['appr'],2).'</td>
                    <td>$'.number_format($net_amount,2).'</td>
                    <td>'.number_format($net_perc,2).'%</td>


                    ';
                    $html.'</tr>';
                  }

                  
                  $html.='<tbody>

                  </tbody>
                  </table>';


echo $html;exit;
}

if(isset($_POST['type']) and $_POST['type']=='sales_agent')
{

  $rows = $cache->get('sales_related_report.'.$_POST['type']);
  if(!$rows)
  {
     // $rows = $db->func_query("select a.user_id, count(*) as no_of_customers,count(distinct a.email) as no_of_emails,b.email,sum(c.product_price) order_price1,sum(c.product_price) - sum(c.product_true_cost) as profit from inv_orders b,inv_orders_items c,inv_customers a where  b.order_id=c.order_id and lower(b.email)=lower(a.email) and a.user_id<>0 and b.order_date > CURRENT_DATE - INTERVAL 30 DAY AND lower(b.order_status) in ('processed','shipped','completed') group by lower(a.user_id) having order_price1>500 order by profit desc limit 25");
  $rows = $db->func_query("select a.user_id, count(*) as no_of_customers,count(distinct a.email) as no_of_emails,sum(b.order_price) order_price1,sum(b.profit) as profit from inv_customers a,inv_orders b where a.email=b.email and b.order_date > CURRENT_DATE - INTERVAL 30 DAY AND lower(b.order_status) in ('processed','shipped','completed') and a.user_id<>0 group by a.user_id having order_price1>500 order by profit desc  limit 25");
$cache->set('sales_related_report.'.$_POST['type'],$rows);
}


  $html.='<table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Agent</th>
                          <th># of Orders</th>
                          <th># of Emails</th>
                          <th># of Calls</th>
                          <th>Sale of Parts</th>
                          <th>Parts Profit</th>
                          <th>Shipping</th>
                          <th>Return Credit</th>
                          <th>Return Shipping</th>
                          <th>Apprc.</th>
                          <th>Net</th>
                          <th>%</th>
                          
                      </tr>
                  </thead>';        
                  foreach($rows as $row)
                  {


                    $_temp = $cache->get('sales_related_report.'.$_POST['type'].'.inner.'.md5($row['user_id']));
                    if(!$_temp)
                    {

                    $shipping_paid = (float)$db->func_query_first_cell("SELECT SUM(b.shipping_cost)+sum(b.insurance_cost) FROM inv_orders a, inv_shipstation_transactions b , inv_customers c  where cast(a.order_id as char(50))=cast(b.order_id as char(50)) and a.email=c.email and a.order_date > CURRENT_DATE - INTERVAL 90 DAY AND lower(a.order_status) in ('processed','shipped','completed') and b.voided=0 and c.user_id='".$row['user_id']."'");

                    $return_credit = (float)$db->func_query_first_cell("SELECT SUM(a.amount) FROM oc_voucher a,inv_customers b where a.to_email=b.email and a.status=1 and b.user_id='".$row['user_id']."' AND a.date_added > CURRENT_DATE - INTERVAL 90 DAY");

                    $return_shipping = 0.00;

                    $appr = (float)$db->func_query_first_cell("SELECT SUM(b.amount) FROM oc_order_voucher b,inv_orders a,inv_customers c where cast(a.order_id as char(50))=cast(b.order_id as char(50))  and a.email=c.email and  c.user_id='".$row['user_id']."' AND b.code not like '%LBB%' AND a.order_date > CURRENT_DATE - INTERVAL 90 DAY");

                    
                    $_temp = array(
                      'shipping_paid'=>$shipping_paid,
                      'return_credit'=>$return_credit,
                      'return_shipping'=>$return_shipping,
                      'appr'=>$appr,
                      

                      );
                    $cache->set('sales_related_report.'.$_POST['type'].'.inner.'.md5($row['user_id']),$_temp);

                  }

                    $net_amount = (float)$row['profit'] - (float)$_temp['appr'];

                    $net_perc = (($row['order_price1'] - $net_amount)/ ($row['order_price1'] - $net_amount))*100;

                    $html.='<tr>';
                    $html.='
                    <td>'.get_username($row['user_id']).'</td>
                    <td>'.(int)$row['no_of_customers'].'</td>
                    <td>'.(int)$row['no_of_emails'].'</td>
                    <td>0</td>
                    <td>$'.number_format($row['order_price1'],2).'</td>
                    <td>$'.number_format($row['profit'],2).'</td>
                    <td>$'.number_format($_temp['shipping_paid'],2).'</td>
                    <td>$'.number_format($_temp['return_credit'],2).'</td>
                    <td>$'.number_format($_temp['return_shipping'],2).'</td>
                    
                    <td>$'.number_format($_temp['appr'],2).'</td>
                    <td>$'.number_format($net_amount,2).'</td>
                    <td>'.number_format($net_perc,2).'</td>


                    ';
                    $html.'</tr>';
                  }

                  
                  $html.='<tbody>

                  </tbody>
                  </table>';


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
  <title>Sales Related Report</title>
<style>
.ajax-dropdown  {
		background: #000;
		color: #fff;
		position: absolute !important;
		z-index: 99;
		width: 400px !important;
	}
	

</style>
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
    
    <h2 align="center">Sales Related Report</h2>
    <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
      <tbody>

        <tr>
          
          

          <td colspan="8" width="50%" valign="top">
          <div id="monthly_report" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">


              </div>

          </td>
        </tr>
        <tr>
            <td colspan="8"><hr></td>
        </tr>
        <tr>
        <td colspan="4">
        <strong>Territory based Sales</strong>
          <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Territory</th>
                          <th>Current Month</th>
                          <th>Last Month</th>
                          <th>12 Months Ave</th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php
                  
                  foreach($result as $territory =>$row)
                  {

                    ?>
                    <tr>
                    <td><?php echo $territory;?></td>
                    <td><?php  echo '$'. number_format($row['current_month_total'],2);?></td>
                    <td><?php  echo '$'. number_format($row['prev_month_total'],2);?></td>
                    <td><?php  echo '$'. number_format($row['avg_year_total'],2);?></td>
                  
                    </tr>
                    <?php
                  }
                  ?>

                  </tbody>
                  
              </table>
        </td>
        <td colspan="4" valign="top">

<strong>80% of Sale (30 Days)</strong>
<div id="80_sale" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">

                  </div>

        </td>

       
        </tr>
        <tr>
            <td colspan="8"><hr></td>
        </tr>

        <tr>
        <td colspan="8">
          <strong>25 most Profitable Customers (3 Months)</strong>
<div id="profitable_customers" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">

                  </div>
        </td>
        

        </tr>

        <tr>
            <td colspan="8"><hr></td>
        </tr>
        <tr>
        <td colspan="8">
          <strong>25 Least Profitable Customers (3 Months)</strong>
<div id="least_profitable_customers" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">

                  </div>
        </td>
        </tr>

        <tr style="display:none">
        <td colspan="8" d><strong>Sales Agent Data</strong>
<div id="sales_agent" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">

                  </div></td>

        </tr>

        
        

      </tbody>
    </table>

  </body>
  </html>
  <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
  <script>
  $(document).ready(function(){
loadData('monthly_report');

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
  function loadComments(monthYear,deno,k,i)
  {
     $.ajax({
        url: 'sales_related_report_new.php',
        type: 'post',
        data: {action:'load_comment',month_year:$monthYear,deno:deno},
        dataType: 'html',
        beforeSend: function() {
          // $('#'+type).html('<img src="images/loading.gif" height"100" width="100" />');
        },  
        complete: function() {
        },      
        success: function(html) {
          $('.load-comments-'+k+i).html(html);
        }
      });
  }
  </script>