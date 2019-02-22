<?php
include_once 'auth.php';
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
// echo "<Pre>";
// print_r($_SESSION);exit;
page_permission('sales_dashboard');

if(!isset($_GET['hide_header']))
{
  unset($_SESSION['hide_header']);
}

$sql_condition = " 1 > 1";

$order_status_query_array = array('processed','shipped','completed','unshipped');

// Getting Page information
if (isset($_GET['page'])) {
  $page = intval($_GET['page']);
}

if ($page < 1) {
  $page = 1;
}

if($_GET['action']=='export_csv')
{

  

if($_SESSION['is_sales_agent']=='1')
{
  $username = get_username($_SESSION['user_id']);
    // $orders_query = "and a.sales_user='".(int)$_SESSION['user_id']."'";
  $sql_condition = " a.user_id='".(int)$_SESSION['user_id']."'";

}
if($_SESSION['login_as']=='admin' or $_SESSION['is_sales_manager']==1)
{
if(isset($_GET['user_id']) && $_GET['user_id']!='')
{
  $username = get_username($_GET['user_id']);
  $sql_condition = " a.user_id='".(int)$_GET['user_id']."'";
  }
}


$filename = $username.' Accounts '.date('m-d-y').'.csv';
$fp = fopen($filename, "w");
$headers = array("Sales Agent","Customer Name","Email","Address","City","State","Zip","Potential", "Last Order Amount","Last Order Date");
fputcsv($fp, $headers,',');






$rows = $db->func_query("SELECT a.user_id, a.firstname,a.lastname,a.email,b.total,a.account_potential as avg_weekly_total,a.last_contacted,a.city,a.state,a.zone_id,a.address1,a.zip from inv_customers a, inv_customer_data_summary b where trim(LOWER(a.email))=trim(lower(b.email)) and b.type='order' and $sql_condition  group by a.email order by concat(a.firstname,' ',a.lastname)");


foreach($rows as $row)
{
  $row['state'] = $db->func_query_first_cell("SELECT name FROM oc_zone WHERE zone_id='".(int)$row['zone_id']."'");
    $last_ordered = $db->func_query_first("SELECT order_id,order_date,order_price FROM inv_orders where LOWER(TRIM(email))='".strtolower(trim($row['email']))."' and LOWER(order_status) IN ('shipped','processed','completed','paid','awaiting fulfillment') ORDER BY order_date desc limit 1");

  $rowData = array(get_username($row['user_id']),$row['firstname'].' '.$row['lastname'],$row['email'],$row['address1'],$row['city'],$row['state'],$row['zip'],$row['avg_weekly_total'],$last_ordered['order_price'],americanDate($last_ordered['order_date']));
    fputcsv($fp, $rowData,',');

 

    
  }
fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);

exit;

}


if(isset($_POST['type']) and $_POST['type']=='load_voucher_data')
{
  $user_id = (int)$_POST['user_id'];
  $chart_weeks = (int)$_POST['chart_weeks'];
  $chart_group = $_POST['chart_group'];
  $_date = $_POST['date'];
  if(!$_date)
  {
    if($chart_group=='Months')
    {
      $_date = date('m-Y');
    }
    else
    {
      $_date = date('W-Y');
    }
  }
  $_date = explode("-", $_date);
  $date = $_date[1].''.$_date[0];


  $month = $_date[0];
  $year = $_date[1];
  
  $html='';
  $json = array();
  if($user_id)
  {

    $rows = $cache->get('sales_dashboard.load_voucher_data.'.$user_id.'.'.$chart_weeks.'.'.$chart_group.'.'.$date);
    $rows2 = $cache->get('sales_dashboard.load_voucher_data2.'.$user_id.'.'.$chart_weeks.'.'.$chart_group.'.'.$date);
    if(!$rows)
    {


       if($chart_group=='Weeks')
      {
    $rows = $db->func_query("select sum(d.amount) as amount,b.id as reason_id,SUBSTRING_INDEX(SUBSTRING_INDEX(b.reason, '-', 1), ',', -1) as reason from oc_voucher a LEFT JOIN inv_voucher_reasons b ON (a.reason_id=b.id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where c.user_id='".$user_id."' and yearweek(d.date_added,3)='".$date."' and a.status=1 and b.id is not null   group by SUBSTRING_INDEX(SUBSTRING_INDEX(b.reason, '-', 1), ',', -1)");


    $rows2 = $db->func_query("select a.reason_id, sum(d.amount) as amount,b.is_rma,b.is_lbb,b.is_order_cancellation,b.is_pos,b.is_manual from oc_voucher a LEFT JOIN inv_voucher_details b ON (a.voucher_id=b.voucher_id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where c.user_id='".$user_id."' and yearweek(d.date_added,3)='".$date."' and a.reason_id=0 and a.status=1 group by a.voucher_id");




      }
      elseif($chart_group=='Months')
      {
           $rows = $db->func_query("select sum(d.amount) as amount,b.id as reason_id,SUBSTRING_INDEX(SUBSTRING_INDEX(b.reason, '-', 1), ',', -1) as reason from oc_voucher a LEFT JOIN inv_voucher_reasons b ON (a.reason_id=b.id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where c.user_id='".$user_id."' and month(d.date_added)='".$month."' and b.id is not null and a.status=1 and year(d.date_added)='".$year."'   group by SUBSTRING_INDEX(SUBSTRING_INDEX(b.reason, '-', 1), ',', -1)");

           $rows2 = $db->func_query("select a.reason_id, sum(d.amount) as amount,b.is_rma,b.is_lbb,b.is_order_cancellation,b.is_pos,b.is_manual from oc_voucher a LEFT JOIN inv_voucher_details b ON (a.voucher_id=b.voucher_id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where c.user_id='".$user_id."' and month(d.date_added)='".$month."' and a.reason_id=0 and a.status=1 and year(d.date_added)='".$year."' group by a.voucher_id");


      }
      else
      {
          // $rows = $db->func_query("SELECT SUM(b.total) AS total,b.date FROM inv_customer_data_summary b,inv_customers a where lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' group by quarter(b.date),year(b.date) order by b.date desc limit  ".$chart_weeks); 
      }

      $cache->set('sales_dashboard.load_voucher_data.'.$user_id.'.'.$chart_weeks.'.'.$chart_group.'.'.$date,$rows);
      $cache->set('sales_dashboard.load_voucher_data2.'.$user_id.'.'.$chart_weeks.'.'.$chart_group.'.'.$date,$rows2);


    }
    $_rows2 = array();
    // print_r($rows2);exit;
    foreach($rows2 as $row)
    {
      if($row['is_rma'] or $row['is_pos'])
      {
        $_rows2['Return']= $_rows2['Return']+($row['amount']*(-1));
      }
      elseif($row['is_lbb'])
      {
        $_rows2['LBB']= $_rows2['LBB']+($row['amount']*(-1));
      }
      elseif($row['is_order_cancellation'])
      {
        $_rows2['Order Cancellation']= $_rows2['Order Cancellation']+($row['amount']*(-1));
      }
      elseif($row['is_pos'])
      {
          // $_rows2['POS']= $_rows2['POS']+($row['amount']*(-1));
          $_rows2['POS']= $_rows2['POS']+($row['amount']*(-1));
      }
      elseif($row['is_manual'])
      {
        $_rows2['Manual']= $_rows2['Manual']+($row['amount']*(-1));
      }
      else
      {
        $_rows2['Not Defined']= $_rows2['Not Defined']+($row['amount']*(-1));
      }

    }
    // print_r($rows);exit;
    
    $html.='
    <span style="font-weight:bold;font-size:13px">'.$month.'-'.$year.' </span>
    <table width="100%" class="xtable tablesorter" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
        <thead>
        <tr>
        <th width="5%"></th>
        <th width="50%">Main Reason</th>
        <th width="45%">Total</th>

        </tr>
        </thead>
        <tbody>
        ';
        $voucher_total = 0.00;

        foreach($_rows2 as $reason=> $amount)
          {
            $voucher_total = $voucher_total + $amount;
           
            $html.='<tr style="font-weight:bold">';
            $html.='<td width="5%"></td>';
            $html.='<td><a href="javascript:void(0);" onClick="showPopup(\''.$month.'-'.$year.'\',\''.$reason.'\')">'.($reason).'</td>';
            $html.='<td>$'.number_format($amount,2).'</td>';

            $html.='</tr>';

    //         if($reason=='RMA')
    //         {

    //         if($chart_group=='Weeks')
    //   {
    // $subs = $db->func_query("select right(a.code,1) as code, a.reason_id, sum(d.amount) as amount,b.is_rma,b.is_lbb,b.is_order_cancellation,b.is_pos,b.is_manual from oc_voucher a LEFT JOIN inv_voucher_details b ON (a.voucher_id=b.voucher_id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where c.user_id='".$user_id."' and yearweek(d.date_added,3)='".$date."' and a.reason_id=0 and a.status=1 and b.is_rma=1  GROUP BY right(a.code,1)");
    //   }
    //   elseif($chart_group=='Months')
    //   {
    //       $subs = $db->func_query("select right(a.code,1) as code, a.reason_id, sum(d.amount) as amount,b.is_rma,b.is_lbb,b.is_order_cancellation,b.is_pos,b.is_manual from oc_voucher a LEFT JOIN inv_voucher_details b ON (a.voucher_id=b.voucher_id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where c.user_id='".$user_id."' and month(d.date_added)='".$month."' and year(d.date_added)='".$year."' and a.reason_id=0 and a.status=1 and b.is_rma=1  GROUP BY right(a.code,1)");
    //   }


    //          foreach($subs as $sub)
    //   {
    //     $html.='<tr style="display:none" class="row_return">';
    //         $html.='<td ></td>';
    //         $html.='<td>'.($sub['code']=='R'?'Return':'FulFillment').'</td>';
    //         $html.='<td>$'.number_format($sub['amount']*(-1),2).'</td>';

    //         $html.='</tr>';
    //   }

    // }



          }


          

          foreach($rows as $row)
          {
            $voucher_total = $voucher_total + ($row['amount']*(-1));
           
            $html.='<tr style="font-weight:bold">';
            $html.='<td width="5%">'.($row['reason']?'<img style="cursor:pointer" onClick="$(\'.row_'.$row['reason_id'].'\').toggle();" src="'.$host_path.'images/plus.png" height="18" width="18">':'').'</td>';
            $html.='<td><a href="javascript:void(0);" onClick="showPopup(\''.$month.'-'.$year.'\',\''.$row['reason'].'\')">'.($row['reason']).'</td>';
            $html.='<td>$'.number_format($row['amount']*(-1),2).'</td>';

            $html.='</tr>';

            if($chart_group=='Weeks')
      {
    $subs = $db->func_query("select sum(d.amount) as amount,b.id as reason_id,SUBSTRING_INDEX(SUBSTRING_INDEX(b.reason, '-', 2), '-', -1) as reason from oc_voucher a LEFT JOIN inv_voucher_reasons b ON (a.reason_id=b.id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where c.user_id='".$user_id."' and a.status=1 and SUBSTRING_INDEX(SUBSTRING_INDEX(b.reason, '-', 1), '-', -1)='".$row['reason']."' and yearweek(d.date_added,3)='".$date."'   group by SUBSTRING_INDEX(SUBSTRING_INDEX(b.reason, '-', 2), '-', -1)");
      }
      elseif($chart_group=='Months')
      {
           $subs = $db->func_query("select sum(d.amount) as amount,b.id as reason_id,SUBSTRING_INDEX(SUBSTRING_INDEX(b.reason, '-', 2), '-', -1) as reason from oc_voucher a LEFT JOIN inv_voucher_reasons b ON (a.reason_id=b.id) INNER JOIN inv_customers c ON ( a.to_email=c.email) INNER JOIN oc_voucher_history d ON ( a.voucher_id=d.voucher_id) where c.user_id='".$user_id."' and a.status=1 and SUBSTRING_INDEX(SUBSTRING_INDEX(b.reason, '-', 1), '-', -1)='".$row['reason']."' and month(d.date_added)='".$month."' and year(d.date_added)='".$year."'   group by SUBSTRING_INDEX(SUBSTRING_INDEX(b.reason, '-', 2), ',', -1)");
      }
      foreach($subs as $sub)
      {
        $html.='<tr style="display:none" class="row_'.$row['reason_id'].'">';
            $html.='<td ></td>';
            $html.='<td>'.$sub['reason'].'</td>';
            $html.='<td>$'.number_format($sub['amount']*(-1),2).'</td>';

            $html.='</tr>';
      }

        
          }
           $html.='<tr>';
            $html.='<td colspan="2"></td>';
            
            $html.='<td style="font-weight:bold">$'.number_format($voucher_total,2).'</td>';

            $html.='</tr>';

        $html.='</tbody>
        </table>
        ';
  }
  echo $html;exit;
}


if(isset($_POST['type']) and $_POST['type']=='load_weekly_data')
{

  $user_id = (int)$_POST['user_id'];
  $chart_weeks = (int)$_POST['chart_weeks'];
  $chart_group = $_POST['chart_group'];

  $json = array();
  if($user_id)
  {

    $rows = $cache->get('sales_dashboard.weekly_chart.'.$user_id.'.'.$chart_weeks.'.'.$chart_group);
    if(!$rows)
    {

      if($chart_group=='Weeks')
      {
    $rows = $db->func_query("SELECT SUM(b.total) AS total,b.date FROM inv_customer_data_summary b,inv_customers a where lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' group by yearweek(b.date,3) order by b.date desc limit  ".$chart_weeks);
      }
      elseif($chart_group=='Months')
      {
           $rows = $db->func_query("SELECT SUM(b.total) AS total,b.date FROM inv_customer_data_summary b,inv_customers a where lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' group by month(b.date),year(b.date) order by b.date desc limit  ".$chart_weeks);
      }
      else
      {
          $rows = $db->func_query("SELECT SUM(b.total) AS total,b.date FROM inv_customer_data_summary b,inv_customers a where lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' group by quarter(b.date),year(b.date) order by b.date desc limit  ".$chart_weeks); 
      }

    $cache->set('sales_dashboard.weekly_chart.'.$user_id.'.'.$chart_weeks.'.'.$chart_group,$rows);
  }
    
  
  for($i=count($rows)-1;$i>=0;$i--)
  {
    if($chart_group=='Weeks')
    {
      $_date = date('W-Y',strtotime($rows[$i]['date']));
    }
    elseif($chart_group=='Months')
    {
     $_date =  date('m-Y',strtotime($rows[$i]['date']));
    }
    else
    {
    $_date = date('m-Y',strtotime($rows[$i]['date'])) ;
    }
    $json[] = array(
      'total'=>$rows[$i]['total'],
      'date'=>$_date,

      );
  }
}
  echo json_encode($json);exit;
}

if(isset($_POST['type']) and $_POST['type']=='load_quarter_data')
{

  $user_id = (int)$_POST['user_id'];
  $quarter_group = $_POST['quarter_group'];

  $json = array();
  if($user_id)
  {

    //$rows = $cache->get('sales_dashboard.quarterly_chart.'.$user_id.'.'.$quarter_group);
    if(!$rows)
    {

        if ($quarter_group == '1') {
          
           $rows = $db->func_query("SELECT SUM(b.total) AS total,b.date FROM inv_customer_data_summary b,inv_customers a where  date(b.date)>=date(a.sales_assigned_date) and lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' and YEAR(b.date) = '".date('Y')."' and MONTH(b.date) BETWEEN 1 AND 3 group by yearweek(b.date) order by b.date desc ");

           $rows_2 = $db->func_query("SELECT SUM(b.total) AS total,b.date,MONTH(b.date) as month FROM inv_customer_data_summary b,inv_customers a where  date(b.date)>=date(a.sales_assigned_date) and lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' and YEAR(b.date) = '".date('Y')."' and MONTH(b.date) BETWEEN 1 AND 3 group by month(b.date),year(b.date) order by b.date asc ");

           $vouchers = $db->func_query("SELECT sum(c.`amount`) as total, MONTH(c.date_added) as month FROM `oc_voucher` b LEFT OUTER JOIN `oc_voucher_history` c ON b.`voucher_id` = c.`voucher_id` INNER JOIN inv_customers a on (b.to_email=a.email) where date(c.date_added)>=date(a.sales_assigned_date) and lower(b.code) NOT LIKE '%lbb%' and YEAR(c.date_added) = '".date('Y')."' and MONTH(c.date_added) BETWEEN 1 AND 3 AND a.user_id='".$user_id."' and b.status=1 group by month(c.date_added),year(c.date_added) order by c.date_added asc ");
        } else if ($quarter_group == '2') {
          
          $rows = $db->func_query("SELECT SUM(b.total) AS total,b.date FROM inv_customer_data_summary b,inv_customers a where date(b.date)>=date(a.sales_assigned_date) and lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' and YEAR(b.date) = '".date('Y')."' and MONTH(b.date) BETWEEN 4 AND 6 group by yearweek(b.date) order by b.date desc ");

          $rows_2 = $db->func_query("SELECT SUM(b.total) AS total,b.date,MONTH(b.date) as month FROM inv_customer_data_summary b,inv_customers a where date(b.date)>=date(a.sales_assigned_date) and lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' and YEAR(b.date) = '".date('Y')."' and MONTH(b.date) BETWEEN 4 AND 6 group by month(b.date),year(b.date) order by b.date asc ");

          $vouchers = $db->func_query("SELECT sum(c.`amount`) as total, MONTH(c.date_added) as month FROM `oc_voucher` b LEFT OUTER JOIN `oc_voucher_history` c ON b.`voucher_id` = c.`voucher_id` INNER JOIN inv_customers a on (b.to_email=a.email) where date(c.date_added)>=date(a.sales_assigned_date) and lower(b.code) NOT LIKE '%lbb%' and YEAR(c.date_added) = '".date('Y')."' and MONTH(c.date_added) BETWEEN 4 AND 6 AND a.user_id='".$user_id."' and b.status=1 group by month(c.date_added),year(c.date_added) order by c.date_added asc ");
        } else if ($quarter_group == '3') {
         
          $rows = $db->func_query("SELECT SUM(b.total) AS total,b.date FROM inv_customer_data_summary b,inv_customers a where date(b.date)>=date(a.sales_assigned_date) and lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' and YEAR(b.date) = '".date('Y')."' and MONTH(b.date) BETWEEN 7 AND 9 group by yearweek(b.date) order by b.date desc ");

          $rows_2 = $db->func_query("SELECT SUM(b.total) AS total,b.date,MONTH(b.date) as month FROM inv_customer_data_summary b,inv_customers a where date(b.date)>=date(a.sales_assigned_date) and lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' and YEAR(b.date) = '".date('Y')."' and MONTH(b.date) BETWEEN 7 AND 9 group by month(b.date),year(b.date) order by b.date asc ");

          $vouchers = $db->func_query("SELECT sum(c.`amount`) as total, MONTH(c.date_added) as month FROM `oc_voucher` b LEFT OUTER JOIN `oc_voucher_history` c ON b.`voucher_id` = c.`voucher_id` INNER JOIN inv_customers a on (b.to_email=a.email) where date(c.date_added)>=date(a.sales_assigned_date) and lower(b.code) NOT LIKE '%lbb%' and YEAR(c.date_added) = '".date('Y')."' and MONTH(c.date_added) BETWEEN 7 AND 9 AND a.user_id='".$user_id."' and b.status=1 group by month(c.date_added),year(c.date_added) order by c.date_added asc ");
        } else {
          
           $rows = $db->func_query("SELECT SUM(b.total) AS total,b.date FROM inv_customer_data_summary b,inv_customers a where date(b.date)>=date(a.sales_assigned_date) and lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' and YEAR(b.date) = '".date('Y')."' and MONTH(b.date) BETWEEN 10 AND 12 group by yearweek(b.date) order by b.date desc ");

            $rows_2 = $db->func_query("SELECT SUM(b.total) AS total,b.date,MONTH(b.date) as month FROM inv_customer_data_summary b,inv_customers a where date(b.date)>=date(a.sales_assigned_date) and lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' and YEAR(b.date) = '".date('Y')."' and MONTH(b.date) BETWEEN 10 AND 12 group by month(b.date),year(b.date) order by b.date asc ");

            $vouchers = $db->func_query("SELECT sum(c.`amount`) as total, MONTH(c.date_added) as month FROM `oc_voucher` b LEFT OUTER JOIN `oc_voucher_history` c ON b.`voucher_id` = c.`voucher_id` INNER JOIN inv_customers a on (b.to_email=a.email) where date(c.date_added)>=date(a.sales_assigned_date) and lower(b.code) NOT LIKE '%lbb%' and YEAR(c.date_added) = '".date('Y')."' and MONTH(c.date_added) BETWEEN 10 AND 12 AND a.user_id='".$user_id."' and b.status=1 group by month(c.date_added),year(c.date_added) order by c.date_added asc ");
        }


    //$cache->set('sales_dashboard.quarterly_chart.'.$user_id.'.'.$quarter_group);
  }
  //testObject($rows) ;exit;
  $user_data = $db->func_query_first("SELECT commission,commission_date from inv_users where id = '".$user_id."'");
  $commission = $user_data['commission'];
  $commission_date = $user_data['commission_date'];


  $commission = $commission/100;
  $table_html = '';
  // $sum_of_sale = 0.00;
  foreach ($rows_2 as $key => $data) {
    
    $dateObj   = DateTime::createFromFormat('!m', $data['month']);
    $monthName = $dateObj->format('F');

    $net_commission = $net;
    $_commission = $net_commission*$commission;
    if($commission_date!='0000-00-00')
    {
      // $commission_total = $db->func_query_first_cell("SELECT SUM(b.total) AS total FROM inv_customer_data_summary b,inv_customers a where lower(trim(a.email))=lower(trim(b.email)) and date(b.date)>=date(a.sales_assigned_date)  and b.type='order' and a.user_id='".$user_id."' and YEAR(b.date) = '".date('Y')."' and MONTH(b.date) ='".$data['month']."' and date(b.date)>='".$commission_date."'  order by b.date asc");
  
      //  $commission_voucher = $db->func_query_first_cell("SELECT sum(c.`amount`) as total FROM `oc_voucher` b LEFT OUTER JOIN `oc_voucher_history` c ON b.`voucher_id` = c.`voucher_id` INNER JOIN inv_customers a on (b.to_email=a.email) where lower(b.code) NOT LIKE '%lbb%' and YEAR(c.date_added) = '".date('Y')."' and MONTH(c.date_added)='".$data['month']."' and date(c.date_added)>='".$commission_date."'  and date(c.date_added)>=date(a.sales_assigned_date) AND a.user_id='".$user_id."' and b.status=1  ");

      //  $net_commission = $commission_total+$commission_voucher;

      $c_data = $db->func_query_first("SELECT SUM(sale_amount) as total_amount,sum(voucher_amount) as commission_voucher,sum(commission) as commission from inv_user_commission WHERE MONTH(date_updated)='".$data['month']."' and year(date_updated)='".date('Y')."' and user_id='".$user_id."'");

      $c_detail = $db->func_query("SELECT sum(sale_amount) as total_amount,sum(voucher_amount) as commission_voucher,sum(commission) as commission,yearweek(date_updated,3) as date_updated from inv_user_commission WHERE MONTH(date_updated)='".$data['month']."' and year(date_updated)='".date('Y')."' and user_id='".$user_id."' and commission<>0.00  group by yearweek(date_updated,3) order by date_updated ");
      // echo "SELECT sum(sale_amount) as total_amount,sum(voucher_amount) as commission_voucher,sum(commission) as commission,yearweek(date_updated,3) as date_updated from inv_user_commission WHERE MONTH(date_updated)='".$data['month']."' and year(date_updated)='".date('Y')."' and user_id='".$user_id."' and commission<>0.00  group by yearweek(date_updated,3) order by date_updated ";exit;

      $data['total'] = $c_data['total_amount'];
      $vouchers[$key]['total'] = $c_data['commission_voucher'];

      // $_commission = $db->func_query_first_cell("select sum(commission) as commission from inv_user_commission WHERE month(date_updated,3)='".$data['month']."' and year(date_updated)='".date('Y')."' and user_id='".$user_id."' and commission<>0.00");
      $_commission = $c_data['commission'];
      
    }
    // print_r($c_detail);exit;
    $net = $data['total'] + $vouchers[$key]['total'];
    // $sum_of_sale = $sum_of_sale + (float)$data['total'];
    $table_html .= '<tr style="font-weight:bold">
    <td width="5%">'.($c_detail?'<img style="cursor:pointer" onClick="$(\'.row_'.$monthName.'-'.date('y').'\').toggle();" src="'.$host_path.'images/plus.png" height="18" width="18">':'').'</td>
    <td align="center">'.$monthName.'-'.date('y').'</td>
    <td align="center">$'.number_format($data['total'],2).'</td>
    <td align="center">$'.number_format($vouchers[$key]['total']*-1,2).'</td>
    <td align="center">$'.number_format($net,2).'</td>
    <td align="center">$'.number_format($_commission,2).'</td>
    </tr>';

     foreach($c_detail as $detail)
  {
    // echo $monthName;
    $table_html.='<tr class="row_'.$monthName.'-'.date('y').'" style="display:none">
    <td></td>
    <td><a href="javascript:void(0);" onClick="showCommissionDetail(\''.$detail['date_updated'].'\')">'.$detail['date_updated'].'</a></td>
    <td align="center">$'.number_format($detail['total_amount'],2).'</td>
    <td align="center">$'.number_format($detail['commission_voucher']*-1,2).'</td>
    <td align="center">$'.number_format($detail['total_amount']+$detail['commission_voucher'],2).'</td>
    <td align="center">$'.number_format($detail['commission'],2).'</td>

    </tr>';
  }
  }
  // echo $monthName.'-';print_r($c_detail);exit;
 
  // exit;
  // echo $table_html;exit;
    
  $sum_of_sale = 0;
  for($i=count($rows)-1;$i>=0;$i--)
  {
      $_date = date('W-Y',strtotime($rows[$i]['date']));
    $sum_of_sale = $sum_of_sale + (float)$rows[$i]['total'];
    $json[] = array(
      'total'=>$rows[$i]['total'],
      'date'=>$_date,

      );
  }
  $json[0]['sum_of_sale'] =  $sum_of_sale;
  $json[0]['table_html'] =  $table_html;
  $json[0]['target_sale'] =  $db->func_query_first_cell("SELECT quarter_target from inv_users where id='".$user_id."'");
  $json[0]['commission_date'] = ($commission_date=='0000-00-00'?'N/A':americanDate($commission_date,false));

}
  echo json_encode($json);exit;
}


if(isset($_POST['type']) and $_POST['type']=='load_weekly_data2')
{

  $user_id = (int)$_POST['user_id'];
  $chart_weeks = (int)$_POST['chart_weeks'];
  $chart_group = $_POST['chart_group'];

  $json = array();
  if($user_id)
  {

    $rows = $cache->get('sales_dashboard.weekly_chart2.'.$user_id.'.'.$chart_weeks.'.'.$chart_group);
    if(!$rows)
    {

      if($chart_group=='Weeks')
      {
    $rows = $db->func_query("SELECT c.date_added as date ,sum(c.`amount`) total FROM `oc_voucher` b LEFT OUTER JOIN `oc_voucher_history` c ON b.`voucher_id` = c.`voucher_id` INNER JOIN inv_customers a on (b.to_email=a.email) where a.user_id='".$user_id."' and b.status=1 group by yearweek(c.date_added,3) having total<>0 order by c.date_added desc limit  ".$chart_weeks);
      }
      elseif($chart_group=='Months')
      {
           $rows = $db->func_query("SELECT c.date_added as date ,sum(c.`amount`) total FROM `oc_voucher` b LEFT OUTER JOIN `oc_voucher_history` c ON b.`voucher_id` = c.`voucher_id` INNER JOIN inv_customers a on (b.to_email=a.email) where a.user_id='".$user_id."' and b.status=1 group by month(c.date_added),year(c.date_added) having total<>0 order by c.date_added desc limit  ".$chart_weeks);
      }
      else
      {
          $rows = $db->func_query("SELECT c.date_added as date ,sum(c.`amount`) total FROM `oc_voucher` b LEFT OUTER JOIN `oc_voucher_history` c ON b.`voucher_id` = c.`voucher_id` INNER JOIN inv_customers a on (b.to_email=a.email) where a.user_id='".$user_id."' and b.status=1 group by quarter(c.date_added),year(c.date_added) order by c.date_added desc limit  ".$chart_weeks); 
      }

    $cache->set('sales_dashboard.weekly_chart2.'.$user_id.'.'.$chart_weeks.'.'.$chart_group,$rows);
  }
    
  
  for($i=count($rows)-1;$i>=0;$i--)
  {
    if($chart_group=='Weeks')
    {
      $_date = date('W-Y',strtotime($rows[$i]['date'])).'';
    }
    elseif($chart_group=='Months')
    {
     $_date =  date('m-Y',strtotime($rows[$i]['date'])).'';
    }
    else
    {
    $_date = date('m-Y',strtotime($rows[$i]['date'])).'' ;
    }
    $json[] = array(
      'total'=>$rows[$i]['total']*(-1),
      'date'=>$_date,

      );
  }
}
  echo json_encode($json);exit;
}

if(isset($_POST['type']) && ($_POST['type'])=='add_comment')
{
  $email = urldecode($_POST['email']);
  $comment = urldecode($_POST['comment']);
  $comment_type=$_POST['comment_type'];
  $customer_mood=$_POST['customer_mood'];
  $customer_id = $db->func_query_first_cell("SELECT id from inv_customers where email='".$email."'");
  $json = array();
  if($customer_id)
  {

    $db->db_exec("INSERT INTO inv_customer_comments SET customer_mood='".$customer_mood."', comment_type='".$comment_type."', comments='".$db->func_escape_string($comment)."',customer_id='".(int)$customer_id."',user_id='".$_SESSION['user_id']."',date_added='".date('Y-m-d H:i:s')."',email='".$email."'");

   // echo "UPDATE inv_customers SET customer_mood='".$customer_mood."' WHERE id='".(int)$customer_id."'";exit;
      $db->db_exec("UPDATE inv_customers SET customer_mood='".$customer_mood."' WHERE id='".(int)$customer_id."'");
    
    $json['success'] =1;
  }
  else
  {
    $json['error'] =1;
  }
  echo json_encode($json);exit;

}


if(isset($_POST['type']) && ($_POST['type'])=='update_date')
{
  $email = urldecode($_POST['email']);
  $date = date('Y-m-d H:i:s');
  
  $customer_id = $db->func_query_first_cell("SELECT id from inv_customers where email='".$email."'");
  $json = array();
  if($customer_id)
  {

  
   // echo "UPDATE inv_customers SET customer_mood='".$customer_mood."' WHERE id='".(int)$customer_id."'";exit;
      $db->db_exec("UPDATE inv_customers SET last_contacted='".$date."' WHERE id='".(int)$customer_id."'");
    $cache->delete('sales_dashboard');
    $json['success'] =americanDate($date,false);
  }
  else
  {
    $json['error'] =1;
  }
  echo json_encode($json);exit;

}

if(isset($_POST['type']) && ($_POST['type'])=='load_comments')
{
  $email = urldecode($_POST['email']);
  $customer_id = $db->func_query_first_cell("SELECT id from inv_customers where email='".$email."'");
  $html = '<div style="height:100%;overflow-y:scroll;width:100%;">';
  $html.='  <table width="100%" border="1" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
                  <thead><tr>
                    <th>Date</th>
                    <th>Comment</th>
                    <th>Type</th>
                    <th>Mood</th>
                    <th>User</th>

                      </tr>
                      </thead>
                      <tbody>
                    ';
  if($customer_id)
  {
    $comments = $db->func_query("SELECT * FROM inv_customer_comments WHERE customer_id='".(int)$customer_id."' or email='".$email."' order by date_added desc");

      foreach($comments as $comment)
      {
          $html.='<tr>

            <td>'.date('m/d/Y', strtotime($comment['date_added'])).'</td>
            <td>'.$comment['comments'].'</td>
            <td>'.$comment['comment_type'].'</td>
            <td align="center">'.($comment['customer_mood']?'<img style="width:24px" src="images/emoji/'.$comment['customer_mood'].'.png">':'-').'</td>
             <td>'.get_username($comment['user_id']).'</td>
          </tr>';
      }
  }
  $html.='
  </tbody>
    </table>
  </div>';
  echo $html;exit;
}

// $parameters = '&page='.$page;

function getWeeksMonthsYears($start_date,$end_date,$type='Weeks')
{

  $return = array();
  $startTime = strtotime($start_date);
  $endTime = strtotime($end_date);


  switch($type)
  {
    case 'Days':
    $_x = 'd';
    $type =  'day';
    break;
    case 'Weeks':
    $_x = 'W';
    $type =  'week';
    break;
    case 'Months':
    $_x = 'm';
    $type =  'month';
    break;
    case 'Years':
    $_x = 'Y';
    $type =  'year';
    break;
    default:
    $_x = 'W';
    $type='week';
    break;

  }

  while ($startTime < $endTime) {
// echo date('Y-m-d',$startTime )."<br>";  
    $return[] = date('m/d/Y',$startTime ); 

    $startTime += strtotime('+1 '.$type, 0);
    
  }
// exit;
// print_r($return);exit;
  return $return;
}

if(!isset($_GET['group_by']))
{
  $group_by = 'Weeks';
  $_GET['group_by'] = $group_by;
}

if(!isset($_GET['filter_date_start']))
{

  $_GET['filter_date_start'] = date('Y-m-d',strtotime('-7 days'));
  $_GET['filter_date_end'] = date('Y-m-d');
}
$parameters.='&filter_date_start='.$_GET['filter_date_start'];
$parameters.='&filter_date_end='.$_GET['filter_date_end'];
$parameters.='&group_by='.$_GET['group_by'];
  // $orders_query = "and (a.sales_user='0' or a.sales_user=NULL)";
$orders_query='';

if($_SESSION['is_sales_agent']=='1')
{
    // $orders_query = "and a.sales_user='".(int)$_SESSION['user_id']."'";
  $sql_condition = " a.user_id='".(int)$_SESSION['user_id']."'";

}

if(isset($_GET['user_id']) && $_GET['user_id']!='')
{
  $sql_condition = " a.user_id='".(int)$_GET['user_id']."'";
  $parameters.="&user_id=".$_GET['user_id'];
  if($_GET['user_id']!='')
  {

       // $orders_query = "and a.sales_user='".(int)$_GET['user_id']."'";
  }
}




$inv_query= "select a.id,a.firstname,a.lastname,a.email,a.customer_group,a.telephone,(SELECT SUM(b.total) FROM inv_customer_data_summary b WHERE    trim(LOWER(a.email))=trim(lower(b.email)) and type='order' and date(b.date) >= '".$_GET['filter_date_start']."' AND date(b.date)<='".$_GET['filter_date_end']."') as total_purchased,(SELECT SUM(c.total) FROM inv_customer_data_summary c WHERE    trim(LOWER(a.email))=trim(lower(c.email)) and c.type in('replacement','return_refund','return_store_credit') and date(c.date) >= '".$_GET['filter_date_start']."' AND date(c.date)<='".$_GET['filter_date_end']."') as total_returned from inv_customers a where $sql_condition";
// echo $inv_query;exit;
$max_page_links = 10;
$num_rows = 100;
$start = ($page - 1)*$num_rows;

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "sales_dashboard_new.php",$page);
$inv_orders = $db->func_query($splitPage->sql_query);

if($inv_query)
{



  $lastWeekNumber = date( 'YW', strtotime( 'last week' ) );
  $last2Week = date( 'YW', strtotime( '-2 week' ) );
  $last3Week = date( 'YW', strtotime( '-3 week' ) );
  $last4Week = date( 'YW', strtotime( '-4 week' ) );
  $last5Week = date( 'YW', strtotime( '-5 week' ) );
  $last6Week = date( 'YW', strtotime( '-6 week' ) );
  // echo $last3Week;exit;
  $currentWeek = date('YW');

  
  $cache_data = md5($sql_condition);
  $customers1 = $cache->get('sales_dashboard.customers1.'.$cache_data);
// echo "SELECT a.firstname,a.lastname,a.email,b.total,a.account_potential,a.longitude,a.latitude as avg_weekly_total,a.last_contacted,a.city,a.state from inv_customers a, inv_customer_data_summary b where trim(LOWER(a.email))=trim(lower(b.email)) and b.type='order' and $sql_condition and yearweek(b.date,3)='".$currentWeek."' group by a.email";
  if(!$customers1)
  {

  // $customers1 = $db->func_query("SELECT b.id, a.id,a.firstname,a.lastname,a.email,(select avg(d.total) from inv_customer_data_summary d where d.email=a.email and d.type='order') as avg_weekly_total from inv_customers a, inv_customer_data_summary b where trim(LOWER(a.email))=trim(lower(b.email)) and b.type='order' and $sql_condition and yearweek(b.date,3)='".$lastWeekNumber."' and b.email not in (select c.email from inv_customer_data_summary c where a.email=c.email and yearweek(c.date,3)='".$currentWeek."')   group by a.email");
  $customers1 = $db->func_query("SELECT a.firstname,a.lastname,a.email,sum(b.total) as total,a.account_potential+c.account_potential as avg_weekly_total,a.longitude,a.latitude ,a.last_contacted,a.city,a.state from inv_customers a inner join inv_customer_data_summary b on (trim(LOWER(a.email))=trim(lower(b.email)))
    left join inv_customers c
    on (a.id=c.parent_id)
    where a.parent_id=0 and  b.type='order'and $sql_condition and yearweek(b.date,3)='".$currentWeek."' group by a.email");

  $cache->set('sales_dashboard.customers1.'.$cache_data,$customers1);
}

$customers2 = $cache->get('sales_dashboard.customers2.'.$cache_data);
  if(!$customers2)
  {
  // $customers2 = $db->func_query("SELECT b.id, a.id,a.firstname,a.lastname,a.email,(select avg(d.total) from inv_customer_data_summary d where d.email=a.email and d.type='order') as avg_weekly_total,max(b.date) as last_ordered from inv_customers a, inv_customer_data_summary b where trim(LOWER(a.email))=trim(lower(b.email)) and b.type='order' and $sql_condition and yearweek(b.date,3)='".$last2Week."' and b.email not in (select c.email from inv_customer_data_summary c where a.email=c.email and yearweek(c.date,3) in ('".$currentWeek."','".$lastWeekNumber."'))   group by a.email");

     $customers2 = $db->func_query("SELECT a.firstname,a.lastname,a.email,sum(b.total) as total,a.account_potential+c.account_potential as avg_weekly_total,a.last_contacted,a.city,a.state,a.longitude,a.latitude from inv_customers a inner join inv_customer_data_summary b on trim(LOWER(a.email))=trim(lower(b.email)) left join inv_customers c on (a.id=c.parent_id) where a.parent_id=0 and b.type='order' and $sql_condition and yearweek(b.date,3)='".$lastWeekNumber."' and a.email not in (select c.email from inv_customer_data_summary c where a.email=c.email and yearweek(c.date,3)='".$currentWeek."') group by a.email");


  $cache->set('sales_dashboard.customers2.'.$cache_data,$customers2);
}

$customers3 = $cache->get('sales_dashboard.customers3.'.$cache_data);

  if(!$customers3)
  {

// $customers3 = $db->func_query("SELECT b.id, a.id,a.firstname,a.lastname,a.email,(select avg(d.total) from inv_customer_data_summary d where d.email=a.email and d.type='order') as avg_weekly_total,max(b.date) as last_ordered from inv_customers a, inv_customer_data_summary b where trim(LOWER(a.email))=trim(lower(b.email)) and b.type='order' and $sql_condition and yearweek(b.date,3) in ('".$last3Week."','".$last4Week."','".$last5Week."','".$last6Week."') and b.email not in (select c.email from inv_customer_data_summary c where a.email=c.email and yearweek(c.date,3) in ('".$currentWeek."','".$lastWeekNumber."','".$last2Week."'))   group by a.email");
$customers3 = $db->func_query("SELECT b.id, a.id,a.firstname,a.lastname,a.email,COALESCE(a.account_potential+d.account_potential,0) as avg_weekly_total,max(b.date) as last_ordered,a.company,a.city,a.state,a.last_contacted,a.longitude,a.latitude from inv_customers a
inner join
 inv_customer_data_summary b on trim(LOWER(a.email))=trim(lower(b.email))
 left join inv_customers d
on (a.id=d.parent_id)   
  where  b.type='order' and $sql_condition  and b.email not in (select c.email from inv_customer_data_summary c where a.email=c.email and yearweek(c.date,3) in ('".$currentWeek."','".$lastWeekNumber."'))   group by a.email");

  $cache->set('sales_dashboard.customers3.'.$cache_data,$customers3);
}


$vouchers = $cache->get('sales_dashboard.vouchers.'.$cache_data);
if(!$vouchers)
{
  $vouchers = $db->func_query("SELECT distinct b.voucher_id, a.firstname,a.lastname,a.email,a.account_potential,(b.amount) as total, COALESCE((b.`amount`) + sum(c.`amount`),b.amount) balance FROM `oc_voucher` b LEFT OUTER JOIN `oc_voucher_history` c ON b.`voucher_id` = c.`voucher_id` INNER JOIN inv_customers a on (b.to_email=a.email) where $sql_condition and b.status=1
    AND b.date_added between '".date('Y-m-d',strtotime('-6 months'))."' and '".date('Y-m-d')."' 
    group by b.voucher_id having balance>0");


  $cache->set('sales_dashboard.vouchers.'.$cache_data,$vouchers);
}

$potentials = $cache->get('sales_dashboard.potentials.'.$cache_data);
if(!$potentials)
{
  $potentials = $db->func_query("select a.firstname,a.lastname,a.email,a.city,(select name from oc_zone where zone_id=a.zone_id) as state,(a.account_potential) as account_potential,sum(b.total) as total from inv_customers a left join inv_customer_data_summary b on a.email=b.email and yearweek(b.date,3)='".$currentWeek."'  where $sql_condition group by a.email");

  $cache->set('sales_dashboard.potentials.'.$cache_data,$potentials);
}


$last_potentials = $cache->get('sales_dashboard.last_potentials.'.$cache_data);
if(!$last_potentials)
{
  $last_potentials = $db->func_query("select a.firstname,a.lastname,a.email,a.city,(select name from oc_zone where zone_id=a.zone_id) as state,(a.account_potential) as account_potential,sum(b.total) as total from inv_customers a left join inv_customer_data_summary b on a.email=b.email and yearweek(b.date,3)='".$lastWeekNumber."'  where $sql_condition group by a.email");

  $cache->set('sales_dashboard.last_potentials.'.$cache_data,$last_potentials);
}


$potential_sale = 0.00;
        $potential_potential = 0.00;
        foreach($potentials as $potential)
        {
          $potential_sale+=(float)$potential['total'];
          $potential_potential+=(float)$potential['account_potential'];

          }

          $last_week_potential_sale = 0.00;
        $last_week_potential_potential = 0.00;
        foreach($last_potentials as $potential)
        {
          $last_week_potential_sale+=(float)$potential['total'];
          $last_week_potential_potential+=(float)$potential['account_potential'];

          }

$lbb_vouchers = $cache->get('sales_dashboard.lbb_vouchers.'.$cache_data);
if(!$lbb_vouchers)
{
  //$lbb_vouchers = $db->func_query("select a.firstname,a.lastname,a.email,a.city,(select name from oc_zone where zone_id=a.zone_id) as state,a.account_potential,(select c.amount from inv_buyback_payments c where c.buyback_id=b.buyback_id limit 1) as total,sum(d.total_received) as total_sent,sum(d.total_qc_received) as total_accepted from inv_customers a,oc_buyback b,oc_buyback_products d where a.email=b.email and b.buyback_id=d.buyback_id and $sql_condition  and lower(b.status) in ('in qc','completed') group by a.email");

	$lbb_vouchers = $db->func_query("select a.firstname,a.lastname,a.email,a.city,(select name from oc_zone where zone_id=a.zone_id) as state,a.account_potential,(select c.amount from inv_buyback_payments c where c.buyback_id=b.buyback_id limit 1) as total,sum(d.total_received) as total_sent,sum(e.oem_qty_a+e.oem_qty_b+e.oem_qty_c+e.oem_qty_d+e.non_oem_qty_a+e.non_oem_qty_b+e.non_oem_qty_c+e.non_oem_qty_d) as total_accepted from inv_customers a,oc_buyback b,oc_buyback_products d,inv_buyback_shipments e where a.email=b.email and b.buyback_id=d.buyback_id and d.buyback_product_id = e.buyback_product_id  and $sql_condition and lower(b.status) in ('in qc','completed') group by a.email");

  $cache->set('sales_dashboard.lbb_vouchers.'.$cache_data,$lbb_vouchers);
}

$vouchers_total = 0.00;
$vouchers_new = array();
foreach($vouchers as $_voucher)
{
  $codes = $db->func_query("SELECT distinct b.voucher_id, b.code, COALESCE((b.`amount`) + sum(c.`amount`),b.amount) balance FROM `oc_voucher` b LEFT OUTER JOIN `oc_voucher_history` c ON b.`voucher_id` = c.`voucher_id`  where b.to_email='".$_voucher['email']."' and  b.status=1
    AND b.date_added between '".date('Y-m-d',strtotime('-6 months'))."' and '".date('Y-m-d')."' 
    group by b.voucher_id having balance>0");
  $_codes = '';
  foreach($codes as $code)
  {
    $_codes = $_codes.linkToVoucher($code['voucher_id'], $host_path, $code['code'], 'target="_blank"').', ';
  }
  $_codes = rtrim($_codes,', ');
  $vouchers_new[$_voucher['email']]['firstname'] = $_voucher['firstname'];
  $vouchers_new[$_voucher['email']]['lastname'] = $_voucher['lastname'];
  $vouchers_new[$_voucher['email']]['email'] = $_voucher['email'];
  
  $vouchers_new[$_voucher['email']]['codes'] = $_codes;


  $vouchers_new[$_voucher['email']]['account_potential'] = $_voucher['account_potential'];
  $vouchers_new[$_voucher['email']]['total'] += $_voucher['total'];
  $vouchers_new[$_voucher['email']]['balance'] += $_voucher['balance'];

  $vouchers_total+=$_voucher['balance'];
}
$target = $db->func_query_first_cell("SELECT weekly_target FROM inv_users WHERE id='".($_GET['user_id']?$_GET['user_id']:$_SESSION['user_id'])."'");
  $target_achieved = $db->func_query_first_cell("SELECT SUM(b.total) as target_achieved from inv_customers a, inv_customer_data_summary b where trim(LOWER(a.email))=trim(lower(b.email)) and b.type='order' and $sql_condition and yearweek(b.date,3)='".$currentWeek."' ");

$perc_target = ($target_achieved / $target) * 100;
  
}


?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

  <script src="js/jquery.min.js"></script>
  <script src="js/chart.bundle.js"></script>
  <script type="text/javascript" src="<?php echo $host_path; ?>include/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
  <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

  <link rel="stylesheet" type="text/css" href="include/xtable.css" media="screen" />

  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
  <link rel="stylesheet" type="text/css" href="http://phonepartsusa.com/catalog/view/theme/ppusa2.0/stylesheet/font-awesome.css">

  

  <title>Sales Dashboard</title>
  <style>
    .c-graph-card {
    	text-align: left;
    
    font-family:"Source Sans Pro,sans-serif" !important;
    margin: 0 0 1.875rem;
    border: 1px solid #e6eaee;
    border-radius: 4px;
    background-color: #fff;
    overflow: hidden;
    height:170px;
}

.c-graph-card__content {
	font-family: Source Sans Pro,sans-serif;
	color:#FFF;
    padding: 1.875rem 1.875rem 0;
}
.c-graph-card__title {
    margin: 0;
    font-size: 18px;
    line-height: 27px
}
.c-graph-card__date {
	line-height: 18px;
    margin: 0 0 10px;
    color: #FFF;
    font-size: 12px;
}
.c-graph-card__number {
	line-height: 40px
    margin: 0;
    color: #FFF;
    font-size: 40px;
    font-weight: 300;
}
.c-graph-card__status {
	line-height:21px;
    margin: 0;
    color: #FFF;
    font-size: 14px;
}

    .emoji
    {
      cursor:pointer;
      opacity: 0.4;
      filter: alpha(opacity=40);

    }

    .emoji:hover{
     opacity: 0.8;
     filter: alpha(opacity=80); 
   }
   .emoji-selected{
    opacity: 1;
    filter: alpha(opacity=100); 
  }
  .emoji-selected:hover{
    opacity: 1;
    filter: alpha(opacity=100); 
  }
  table.xtable
  {
    text-shadow: none;
  }
  .popover-content {
    height: 280px;  
    width: 200px;  
  }

  textarea.popover-textarea {
   border: 0px;   
   margin: 0px; 
   margin-top:5px;
   width: 100%;
   height: 170px;
   padding: 0px;  
   box-shadow: none;
   border: 1px solid #ddd;
 }

 .popover-footer {
  margin: 0;
  padding: 8px 14px;
  font-size: 14px;
  font-weight: 400;
  line-height: 18px;
  background-color: #F7F7F7;
  border-bottom: 1px solid #EBEBEB;
  border-radius: 5px 5px 0 0;
}
.to_show{
	/*display: none !important;*/
}
</style>

<script src="http://maps.google.com/maps/api/js?key=AIzaSyARnAGsdBJnIPbiMqyw8cypDKiFCUfYI3A" type="text/javascript"></script>
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

    <br />

    <br />

    

<div align="center" class="tabMenu" >
      <input type="button" class="toogleTab" data-tab="tabFirst" value="Sales Dashboard">
      <input type="button" class="toogleTab" data-tab="tabVoucher" value="Vouchers">
      <input type="button" class="toogleTab" data-tab="tabTargets" value="Sales Targets">

      <input type="button" class="toogleTab to_show" onclick="agentDashboardTab()" data-tab="tabDashboard" value="Agent Dashboard">

      </div>

      <div class="tabHolder">
      <div id="tabFirst" class="makeTabs">
      <h2 align="center">Sales Dashboard</h2>


    <form name="order" action="" method="get">
      <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
        <tbody>
          <tr>
              <td align="center" colspan="8">


            <?php
            if($_SESSION['login_as']=='admin' or $_SESSION['is_sales_manager']==1)
            {
              $agents = $db->func_query("select id,name from inv_users WHERE is_sales_agent=1 and status=1 ");
              ?>
             
                <label for="start_date">Agent:</label>
                <select name="user_id">
                  <option value="">Select Agent</option>
                  <?php
                  foreach($agents as $agent)
                  {
                    ?>
                    <option value="<?=$agent['id'];?>" <?php if($_GET['user_id']==$agent['id']) echo 'selected';?>><?=$agent['name'];?></option>
                    <?php
                  } 

                  ?>
                </select>
              
              <?php
            }
            ?>

            
             <?php
             $group_bys = array('Weeks','Months','Quarters');
             ?>
             <label for="group_by">Period</label>
             <select name="group_by" onchange="changeChartGroupValues(this.value);">
               <?php
               foreach($group_bys as $key => $group_by)
               {
                ?>
                <option value="<?=$group_by;?>" <?php if($_GET['group_by']==$group_by) echo 'selected';?>><?=$group_by;?></option>
                <?php
              }

              ?>
            </select>
            <?php

               // $chart_weeks = array('12'=>'12 Weeks','24'=>'24 Weeks','36'=>'36 Weeks','52'=>'52 Weeks','76'=>'76 Weeks');
            ?>

             <label for="chart_weeks">Group By</label>
             <select name="chart_weeks" class="chart_weeks">
              <?php
                foreach($chart_weeks as $_week => $chart_week)
                {
                  ?>
                    <option value="<?php echo $_week;?>" <?php echo ($_GET['chart_weeks']==$_week?'selected':'');?>><?php echo $chart_week;?></option>
                  <?php
                }
              ?>
            </select>
            <input type="hidden" name="tab" value="tabFirst">

            <input type="submit" value="Update" class="btn btn-primary" name="submit" > 

            <input type="button" value="Export CSV" class="btn btn-danger" style="margin-left:15px" onclick="window.location='sales_dashboard_new.php?action=export_csv&user_id=<?php

                          if($_SESSION['is_sales_agent']==1 && !isset($_GET['user_id']))
                          {
                            echo $_SESSION['user_id'];
                          }
                          else
                          {
                            echo $_GET['user_id'];
                          }

                          ?>'" > 
              <br>
              <br>
               <div class="progress" style="width:50%;height:12px;margin-bottom:5px">
  <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo (int)$perc_target;?>"
  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo round($perc_target,2);?>%">
    <span class="sr-only"><?php echo (int)$perc_target;?>% Complete</span>
  </div>
</div>Target: <?php echo '$'.number_format($target_achieved,2);?> / <?php echo '$'.number_format($target,2);?>
            
          </td>




          
        </tr>
        <tr>
            <td colspan="8" align="center">
                <div id="map" style="width: 70%; height: 400px;"></div>
            </td>
        </tr>
        <tr>
        <td colspan="8" align="center">
            <div id="container" style="float:left;width: 45%;margin-right:10px;">
              <canvas id="canvas"></canvas>
          </div>

  <div  style="height:320px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;width:45%;float:right">
    
        <span style="font-weight:bold;font-size:13px">Customer Available Vouchers (Total <?php echo '$'.number_format($vouchers_total,2);?>) </span>
         <table width="90%" class="xtable tablesorter" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
        <thead>
        <tr>
        <th width="50%">Customer</th>
        <th>Vouchers</th>
        <th>Balance</th>

        </tr>
        </thead>
        <tbody>
        <?php
        foreach($vouchers_new as $_email => $voucher)
        {
          ?>
          <tr>
            <td><?php echo $voucher['firstname']. ' '.$voucher['lastname'].getEmoji($voucher['email'],'14',$host_path);;?><br><span style="font-size:10px"><?php echo linkToProfile($voucher['email'], $host_path,'','_blank');?></span></td>
          <td>
          <?php echo $voucher['codes'];?>
          </td>
          <td>
          <?php
          echo '$'.number_format($voucher['balance'],2);
          ?>
          </td>

          </tr>
          <?php
        }
        ?>
        </tbody>
        </table>

  </div>
        <table width="100%">
        <tr>
        <td width="33%">
        <?php
        if($inv_orders)
        {
        ?>
        <div style="height:320px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
        <span style="font-weight:bold;font-size:13px">ORDERED CURRENT WEEK <?php echo date('W-Y');?></span>
        <table width="100%" class="xtable tablesorter" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
        <thead>

        <tr>
        <th>Last Contact</th>
        <th width="50%">Customer</th>
        <th>Potential</th>
        <th>L. Order</th>

        </tr>
        </thead>
        <tbody>
        <?php
        foreach($customers1 as $_customer1)
        {
          ?>
          <tr>
           <td align="center" ><a  data-tooltip="Double Click to Update Contact Date" onclick="updateLastContactDate('<?php echo $_customer1['email'];?>',$(this))" href="javascript:void(0);"><?php echo americanDate($_customer1['last_contacted'],false);?></a></td>
          <td><?php echo $_customer1['firstname']. ' '.$_customer1['lastname'].getEmoji($_customer1['email'],'14',$host_path);;?> (<?php echo '$'.$_customer1['total'];?>)<br><span style="font-size:10px"><?php echo linkToProfile($_customer1['email'], $host_path,'','_blank');?></span>


             <?php
          if($_customer1['city'])
          {
            ?>
            <br><?php echo $_customer1['city'].', '.$_customer1['state'];?>
            <?php
          }
          ?>
          </td>
          <td>
          <?php echo '$'.number_format($_customer1['avg_weekly_total'],2);?>
          </td>

           <?php
          $last_ordered = $db->func_query_first("SELECT order_id,order_date,order_price FROM inv_orders where LOWER(TRIM(email))='".strtolower(trim($_customer1['email']))."' and LOWER(order_status) IN ('shipped','processed','completed','paid','awaiting fulfillment') ORDER BY order_date desc limit 1");
          ?>
          <?php
          if($last_ordered)
          {
            ?>
            <td>

            <a href="<?php echo $host_path;?>/viewOrderDetail.php?order=<?php echo $last_ordered['order_id'] ;?>" target="_blank" data-tooltip="Order Total: <?php echo '$'.number_format($last_ordered['order_price'],2);?>">
            <?php echo date('m/d/Y',strtotime($last_ordered['order_date']));?>
            </a>

            </td>
            <?php
          }
          else
          {
            ?>
            <td align="center">-</td>
            <?php
          }
          ?>
         


          </tr>
          <?php
        }
        ?>


        </tbody>
        </table>
        </div>
        <?php
      }
      ?>
        </td>

         <td width="33%">

        <?php
        if($inv_orders)
        {
        ?>
        <div style="height:320px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
        <span style="font-weight:bold;font-size:13px">ORDERED LAST WEEK </span>
        <table width="100%" class="xtable tablesorter" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
        <thead>
        <tr>
        <th>Last Contact</th>
        <th width="50%">Customer</th>
        <th>Potential</th>
        <th>L. Order</th>

        </tr>
        </thead>
        <tbody>

         <?php
        foreach($customers2 as $_customer1)
        {
          ?>
          <tr>
           <td align="center" ><a  data-tooltip="Double Click to Update Contact Date" onclick="updateLastContactDate('<?php echo $_customer1['email'];?>',$(this))" href="javascript:void(0);"><?php echo americanDate($_customer1['last_contacted'],false);?></a></td>
          <td><?php echo $_customer1['firstname']. ' '.$_customer1['lastname'].getEmoji($_customer1['email'],'14',$host_path);;?> (<?php echo '$'.$_customer1['total'];?>)<br><span style="font-size:10px"><?php echo linkToProfile($_customer1['email'], $host_path,'','_blank');?></span>

 <?php
          if($_customer1['city'])
          {
            ?>
            <br><?php echo $_customer1['city'].', '.$_customer1['state'];?>
            <?php
          }
          ?>

          </td>
          <td>
          <?php echo '$'.number_format($_customer1['avg_weekly_total'],2);?>
          </td>
          
            <?php
          $last_ordered = $db->func_query_first("SELECT order_id,order_date,order_price FROM inv_orders where LOWER(TRIM(email))='".strtolower(trim($_customer1['email']))."' and LOWER(order_status) IN ('shipped','processed','completed','paid','awaiting fulfillment') ORDER BY order_date desc limit 1");
          ?>
          <?php
          if($last_ordered)
          {
            ?>
            <td>

            <a href="<?php echo $host_path;?>/viewOrderDetail.php?order=<?php echo $last_ordered['order_id'] ;?>" target="_blank" data-tooltip="Order Total: <?php echo '$'.number_format($last_ordered['order_price'],2);?>">
            <?php echo date('m/d/Y',strtotime($last_ordered['order_date']));?>
            </a>

            </td>
            <?php
          }
          else
          {
            ?>
            <td align="center">-</td>
            <?php
          }
          ?>
          
          </tr>
          <?php
        }
        ?>


        </tbody>
        </table>
        </div>
        <?php
      }
      ?>
        </td>


         <td width="33%">

        <?php
        if($inv_orders)
        {
        ?>
        <div style="height:320px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
        <span style="font-weight:bold;font-size:13px">ORDERED 2+ WEEKS AGO</span>
        <table width="100%" class="xtable tablesorter" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
        <thead>
        <tr>
        <th>Last Contact</th>
        <th width="50%">Customer</th>
        <th>Potential</th>
        <th>L. Order</th>

        </tr>
        </thead>
        <tbody>

         <?php
        foreach($customers3 as $_customer1)
        {
          ?>
          <tr>
          <td align="center" ><a  data-tooltip="Double Click to Update Contact Date" onclick="updateLastContactDate('<?php echo $_customer1['email'];?>',$(this))" href="javascript:void(0);"><?php echo americanDate($_customer1['last_contacted'],false);?></a></td>
          <td><?php echo $_customer1['firstname']. ' '.$_customer1['lastname'].getEmoji($_customer1['email'],'14',$host_path);;?>
              <!-- <i title="Add Comment" class="fa fa-comments" style="cursor:pointer;" data-email="<?php echo $_customer1['email'];?>"  data-toggle="popover" data-trigger="focus"  tabindex="0"></i> -->
          <?php
          if($_customer1['company'])
          {
            ?>
            <br><?php echo $_customer1['company'];?>
            <?php
          }
          ?>
          <br><span style="font-size:10px"><?php echo linkToProfile($_customer1['email'], $host_path,'','_blank');?></span>

             <?php
          if($_customer1['city'])
          {
            ?>
            <br><?php echo $_customer1['city'].', '.$_customer1['state'];?>
            <?php
          }
          ?>

          </td>
          <td>
          <?php echo '$'.number_format($_customer1['avg_weekly_total'],2);?>
          </td>
          <?php
          $last_ordered = $db->func_query_first("SELECT order_id,order_date FROM inv_orders where LOWER(TRIM(email))='".strtolower(trim($_customer1['email']))."' and LOWER(order_status) IN ('shipped','processed','completed','paid','awaiting fulfillment') ORDER BY order_date desc limit 1");
          ?>
            <?php
          $last_ordered = $db->func_query_first("SELECT order_id,order_date,order_price FROM inv_orders where LOWER(TRIM(email))='".strtolower(trim($_customer1['email']))."' and LOWER(order_status) IN ('shipped','processed','completed','paid','awaiting fulfillment') ORDER BY order_date desc limit 1");
          ?>
          <?php
          if($last_ordered)
          {
            ?>
            <td>

            <a href="<?php echo $host_path;?>/viewOrderDetail.php?order=<?php echo $last_ordered['order_id'] ;?>" target="_blank" data-tooltip="Order Total: <?php echo '$'.number_format($last_ordered['order_price'],2);?>">
            <?php echo date('m/d/Y',strtotime($last_ordered['order_date']));?>
            </a>

            </td>
            <?php
          }
          else
          {
            ?>
            <td align="center">-</td>
            <?php
          }
          ?>
          
          </tr>
          <?php
        }
        ?>


        </tbody>
        </table>
        </div>
        <?php
      }
      ?>
        </td>

        </tr>
        </table>
        </td>
        </tr>
        <tr>
        <td colspan="8" align="center">
        	<div class="row" style="margin-top:12px">
        	<div class="col-xs-4">
        		<div class="c-graph-card" data-mh="graph-cards" style="background-color:#009688;">
                            <div class="c-graph-card__content">
                                <h3 class="c-graph-card__title">Total Sale</h3>
                                <p class="c-graph-card__date">In Current Week</p>
                                <h4 class="c-graph-card__number">$<?php echo number_format($potential_sale,2);?></h4>
                                <p class="c-graph-card__status">Last Week customers ordered $<?php echo number_format($last_week_potential_sale,2);?> </p>
                            </div>
                            
                           
                        </div>
        	</div>

        	<div class="col-xs-4">
        		<div class="c-graph-card" data-mh="graph-cards" style="background-color:#F44336;">
                            <div class="c-graph-card__content">
                                <h3 class="c-graph-card__title">Total Potential</h3>
                                <p class="c-graph-card__date">In Current Week</p>
                                <h4 class="c-graph-card__number">$<?php echo number_format($potential_potential,2);?></h4>
                                <p class="c-graph-card__status">Last Week Potential Total $<?php echo number_format($last_week_potential_potential,2);?> </p>
                            </div>
                            
                           
                        </div>
        	</div>


        	<div class="col-xs-4">
        		<div class="c-graph-card" data-mh="graph-cards" style="background-color:#9C27B0;">
                            <div class="c-graph-card__content">
                                <h3 class="c-graph-card__title">Total Difference</h3>
                                <p class="c-graph-card__date">In Current Week</p>
                                <h4 class="c-graph-card__number">$<?php echo number_format($potential_sale - $potential_potential,2);?></h4>
                                <p class="c-graph-card__status">Last Week Difference was $<?php echo number_format($last_week_potential_potential - $last_week_potential_sale,2);?> </p>
                            </div>
                            
                           
                        </div>
        	</div>

        	</div>
       <!--  <div id="container" style="float:left;width: 90%;margin-right:10px;">
    <canvas id="potential_canvas"></canvas>
  </div> -->
  </td>
    
        </tr>
        <tr>
        <td colspan="4">
          <!-- <div  style="float:left;width: 90%;margin-right:10px;">
    <canvas id="lbb_canvas"></canvas>
  </div> -->

   <div style="height:320px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
        <span style="font-weight:bold;font-size:13px">Weekly Account Potential vs Sale</span>
        <table width="100%" class="xtable tablesorter" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
        <thead>

        <tr>
        <th width="70%">Customer</th>
        <th>Potential</th>
        <th>Weeks Sale</th>
        <th>Diff</th>

        </tr>
        </thead>
        <tbody>
        <?php
        
        foreach($potentials as $potential)
        {
        	if($potential['total']==0.00) continue;
         
          ?>
          
          <tr>
          <td><?php echo $potential['firstname']. ' '.$potential['lastname'].getEmoji($potential['email'],'14',$host_path);;?> <br><span style="font-size:10px"><?php echo linkToProfile($potential['email'], $host_path,'','_blank');?></span></td>
          <td>
          <?php echo '$'.number_format($potential['account_potential'],2);?>
          </td>
          <td><?php echo '$'.number_format($potential['total'],2);?></td>
          <td><?php echo '$'.number_format(($potential['account_potential'] - $potential['total']),2);?></td>



          </tr>
          <?php
        }

        ?>

        </tbody>
        </table>
        </div>

        </td>
        <td colspan="4">
          
           <div style="height:320px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
        <span style="font-weight:bold;font-size:13px">LBB Vouchers Issued </span>
        <table width="100%" class="xtable tablesorter" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
        <thead>

        <tr>
        <th width="70%">Customer</th>
        <th>#LCD Sent</th>
        <th>#LCD Cr.</th>
        <th>Amount Cr.</th>

        </tr>
        </thead>
        <tbody>
        <?php
        $lbb_total_sent = 0;
        $lbb_total_accepted = 0;

        foreach($lbb_vouchers as $lbb_voucher)
        {

          $lbb_total_sent += (float)$lbb_voucher['total_sent'];
        $lbb_total_accepted += (float)$lbb_voucher['total_accepted'];;
          ?>
          
          <tr>
          <td><?php echo $lbb_voucher['firstname']. ' '.$lbb_voucher['lastname'].getEmoji($lbb_voucher['email'],'14',$host_path);;?> <br><span style="font-size:10px"><?php echo linkToProfile($lbb_voucher['email'], $host_path,'','_blank');?></span></td>
          <td>
          <?php echo ($lbb_voucher['total_sent']);?>
          </td>
          <td><?php echo round($lbb_voucher['total_accepted']);?></td>
          <td><?php echo '$'.number_format( $lbb_voucher['total'],2);?></td>



          </tr>
          <?php
        }

        ?>

        </tbody>
        </table>
        </div>

        </td>
        </tr>
        <tr>
          
            <td colspan="8">
            <table width="90%" class="" cellspacing="0" align="center" style="margin-top:10px">

            <tr>
            <td align="center">

            <!--  <label for="filter_date_start">Date Start</label>
             <input type="text" name="filter_date_start" class="datepicker" value="<?php echo @$_GET['filter_date_start'];?>">
             <label for="filter_date_end">Date End</label>
             <input type="text" name="filter_date_end" class="datepicker" value="<?php echo @$_GET['filter_date_end'];?>">

             <input type="submit" value="Update" class="btn btn-primary" name="submit" > -->


            </td>
            
            </tr>
            </table>
              
                                   </td>  

                                 
                              </tr>

                             
                           </tbody>
                         </table>
                       </form>
                       </div>
                       <div id="tabVoucher" class="makeTabs">
                        <h2 align="center">Vouchers Used By Customers</h2>
                        <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
        <tbody>
          <tr>
              <td align="center" colspan="8">
                <form name="order" action="" method="get">

            <?php
            if($_SESSION['login_as']=='admin' or $_SESSION['is_sales_manager']==1)
            {
              $agents = $db->func_query("select id,name from inv_users WHERE is_sales_agent=1 and status=1 ");
              ?>
             
                <label for="start_date">Agent:</label>
                <select name="user_id">
                  <option value="">Select Agent</option>
                  <?php
                  foreach($agents as $agent)
                  {
                    ?>
                    <option value="<?=$agent['id'];?>" <?php if($_GET['user_id']==$agent['id']) echo 'selected';?>><?=$agent['name'];?></option>
                    <?php
                  } 

                  ?>
                </select>
              
              <?php
            }
            ?>

            
             <?php
             $group_bys = array('Weeks','Months');
             ?>
             <label for="group_by">Period</label>
             <select name="group_by" onchange="changeChartGroupValues(this.value);">
               <?php
               foreach($group_bys as $key => $group_by)
               {
                ?>
                <option value="<?=$group_by;?>" <?php if($_GET['group_by']==$group_by) echo 'selected';?>><?=$group_by;?></option>
                <?php
              }

              ?>
            </select>
            <?php

               // $chart_weeks = array('12'=>'12 Weeks','24'=>'24 Weeks','36'=>'36 Weeks','52'=>'52 Weeks','76'=>'76 Weeks');
            ?>

             <label for="chart_weeks">Group By</label>
             <select name="chart_weeks" class="chart_weeks">
              <?php
                foreach($chart_weeks as $_week => $chart_week)
                {
                  ?>
                    <option value="<?php echo $_week;?>" <?php echo ($_GET['chart_weeks']==$_week?'selected':'');?>><?php echo $chart_week;?></option>
                  <?php
                }
              ?>
            </select>
            <input type="hidden" name="tab" value="tabVoucher">
            <input type="submit" value="Update" class="btn btn-primary" name="submit" > 
            </form>
             
            
          </td>




          
        </tr>
        <tr>
        <td colspan="4" valign="top" width="50%">
            <div id="container" style="float:left;width: 90%;margin-right:10px;">
    <canvas id="canvas_vouchers"></canvas>
  </div>
        </td>
        <td colspan="4" id="voucher_details_table" valign="top" align="center">

        </td>

        </tr>
        </tbody>

        </table>

                       </div>
                       <div id="tabTargets" class="makeTabs">
                        <h2 align="center">Sales Targets & Goals</h2>
                        <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
        <tbody>
          <tr>
              <td align="center" colspan="8">
                <form name="order" action="" method="get">

            <?php
            if($_SESSION['login_as']=='admin' or $_SESSION['is_sales_manager']==1)
            {
              $agents = $db->func_query("select id,name from inv_users WHERE is_sales_agent=1 and status=1 ");
              ?>
             
                <label for="start_date">Agent:</label>
                <select name="user_id">
                  <option value="">Select Agent</option>
                  <?php
                  foreach($agents as $agent)
                  {
                    ?>
                    <option value="<?=$agent['id'];?>" <?php if($_GET['user_id']==$agent['id']) echo 'selected';?>><?=$agent['name'];?></option>
                    <?php
                  } 

                  ?>
                </select>
              
              <?php
            }
            ?>

             <label for="quarter_by">Quarter</label>
             <select name="quarter_by">
               <option value="1" <?php if($_GET['quarter_by'] == 1){echo "selected";} ?>>1st Quarter</option>
               <option value="2" <?php if (date("n")<4 ) echo "disabled";  if($_GET['quarter_by'] == 2){ echo "selected";} ?>>2nd Quarter</option>
               <option value="3" <?php if (date("n")<7 ) echo "disabled";  if($_GET['quarter_by'] == 3){ echo "selected";} ?>>3rd Quarter</option>
               <option value="4" <?php if (date("n")<10 ) echo "disabled"; if($_GET['quarter_by'] == 4){ echo "selected";} ?>>4th Quarter</option>
            </select>
            <input type="hidden" name="tab" value="tabTargets">
            <input type="submit" value="Update" class="btn btn-primary" name="submit" > 
            </form>
             
            
          </td>




          
        </tr>
        <tr>
        <td colspan="4" valign="top" width="35%">
            <div id="container" style="float:left;width: 100% !important;">
        <canvas id="targets_canvas_bar"></canvas>
          </div>
        </td>
        <td colspan="2" style="width: 50% !important;" valign="top" align="center">
        <div id="container" align="center" style="float:left;width: 90%;">
        <canvas id= "targets_canvas"></canvas>
        </div>
        </td>
        <td colspan="2" valign="top" align="center">
          <div style="text-align: center;font-weight:bold">Commission Affects From: <span id="commission_date">N/A</span></div>
          <table class="xtable tablesorter" cellspacing="0" id="targets_table" align="center" style="margin-top:3px;line-height: 12px">
            <thead>
              <tr>
              <th ></th>
                <th align="center">Monthly</th>
                <th align="center">Total Sale</th>
                <th align="center">Vouchers Used</th>
                <th align="center">Net</th>
                <th align="center">Commission</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </td>

        </tr>
        </tbody>

        </table>

                       </div>

                       <div id="tabDashboard" class="makeTabs">


                       </div>
                       </div>
                         <script type="text/javascript">
                         var locations = [
                          <?php
                            foreach($customers1 as $_customer1)
                            {
                              if($_customer1['latitude'])
                              {
                              	   
          $last_ordered = $db->func_query_first("SELECT order_id,order_date,order_price FROM inv_orders where LOWER(TRIM(email))='".strtolower(trim($_customer1['email']))."' and LOWER(order_status) IN ('shipped','processed','completed','paid','awaiting fulfillment') ORDER BY order_date desc limit 1");

                                  echo "['".str_replace("'", "", $_customer1['firstname'].' '.$_customer1['lastname'])."', ".$_customer1['latitude'].", ".$_customer1['longitude'].",'".'$'.number_format($_customer1['avg_weekly_total'],2)."','".americanDate($last_ordered['order_date'],false)."','".linkToProfile($_customer1['email'], $host_path,'','_blank')."'],";
                              }
                            }
                          ?>

                          <?php
                            foreach($customers2 as $_customer1)
                            {
                              if($_customer1['latitude'])
                              {
                                 $last_ordered = $db->func_query_first("SELECT order_id,order_date,order_price FROM inv_orders where LOWER(TRIM(email))='".strtolower(trim($_customer1['email']))."' and LOWER(order_status) IN ('shipped','processed','completed','paid','awaiting fulfillment') ORDER BY order_date desc limit 1");

                                  echo "['".str_replace("'", "", $_customer1['firstname'].' '.$_customer1['lastname'])."', ".$_customer1['latitude'].", ".$_customer1['longitude'].",'".'$'.number_format($_customer1['avg_weekly_total'],2)."','".americanDate($last_ordered['order_date'],false)."','".linkToProfile($_customer1['email'], $host_path,'','_blank')."'],";
                              }
                            }
                          ?>

                          <?php
                            foreach($customers3 as $_customer1)
                            {
                              if($_customer1['latitude'])
                              {
                                $last_ordered = $db->func_query_first("SELECT order_id,order_date,order_price FROM inv_orders where LOWER(TRIM(email))='".strtolower(trim($_customer1['email']))."' and LOWER(order_status) IN ('shipped','processed','completed','paid','awaiting fulfillment') ORDER BY order_date desc limit 1");

                                   echo "['".str_replace("'", "", $_customer1['firstname'].' '.$_customer1['lastname'])."', ".$_customer1['latitude'].", ".$_customer1['longitude'].",'".'$'.number_format($_customer1['avg_weekly_total'],2)."','".americanDate($last_ordered['order_date'],false)."','".linkToProfile($_customer1['email'], $host_path,'','_blank')."'],";
                              }
                            }
                          ?>
                         ];
    // var locations = [
    //   ['Bondi Beach', -33.890542, 151.274856, 4],
    //   ['Coogee Beach', -33.923036, 151.259052, 5],
    //   ['Cronulla Beach', -34.028249, 151.157507, 3],
    //   ['Manly Beach', -33.80010128657071, 151.28747820854187, 2],
    //   ['Maroubra Beach', -33.950198, 151.259302, 1]
    // ];

    var map = new google.maps.Map(document.getElementById('map'), {
      <?php
        if($_GET['user_id']=='48' or $_SESSION['user_id']=='48')
        {
      ?>
      zoom: 13,
      center: new google.maps.LatLng(36.1699412, -115.1398296),
      <?php
    }
    else
    {
      ?>
      zoom: 4,
      center: new google.maps.LatLng(37.09024, -95.712891),
        <?php
    }

      ?>
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;
      // console.log('Locations: '+locations.length);
    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map,
        title: locations[i][0]
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent("<div style='text-align:left'><strong>"+locations[i][0]+"</strong><br>"+locations[i][5]+", "+locations[i][3]+", "+locations[i][4]+'</div>');
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
  </script>
                     </body>
                     </html>
                     <script>

                      $(document).ready(function(){
                        $('[data-toggle="popover"]').popover({

                          trigger : 'click',  
                          placement : 'right', 
                          html: 'true', 
                          content : '<table  style="width:770px;height:100%">'+
                          '<tr>'+
                          '<td class="comment_data" align="left" width="75%" style="vertical-align:top"><img src="images/loading.gif" style="width:32px"></td>'+

                          '<td align="left" style="vertical-align:top" ><select class="popover-select" style="margin-bottom:5px"><option value="">Comment Reason</option> <option value="Sales Call">Sales Call</option><option value="Complaint">Complaint</option><option value="Incident">Incident</option></select><br><div><img src="images/emoji/sad.png" class="emoji" data-mood="sad" onClick="changeMood(this)"> <img src="images/emoji/confused.png" class="emoji" data-mood="confused" onClick="changeMood(this)"> <input type="hidden" class="popover-hidden" value=""> <img src="images/emoji/happy.png" data-mood="happy" onClick="changeMood(this)" class="emoji"></div><textarea class="popover-textarea"></textarea></td>'+
                          '</tr>'+


                          '</table>'+
                          '',
                          template: '<div class="popover" style="min-width:800px"><div class="arrow"></div>'+
                          '<h3 class="popover-title"></h3><div class="popover-content">'+
                          '</div><div class="popover-footer"><button type="button" class="btn btn-primary popover-submit">'+
                          '<i class="fa fa-check"></i></button>&nbsp;'+
                          '<button type="button" class="btn btn-default popover-cancel">'+
                          '<i class=" fa fa-times icon-remove"></i></button></div></div>' 
                        })
                        .on('shown.bs.popover', function() {

    //hide any visible comment-popover
    // alert('here');
    $("[data-toggle=popover]").not(this).popover('hide');
    var $this = $(this);
    // console.log($this.attr('data-email'));
    //attach link text
    $('.popover-select').val('');
    $('.popover-textarea').val('').focus();
    $('.popover-hidden').val('');
    $('.popover-submit').removeClass('disabled')
    loadCommentData($this.attr('data-email'));
    //close on cancel
    $('.popover-cancel').click(function() {
      $this.popover('hide');
    });
    //update link text on submit
    $('.popover-submit').one('click',function(e) {
        // $this.text($('.popover-textarea').val());
          // alert('here');
        addCustomerComment($this.attr('data-email'),$('.popover-textarea').val(),$('.popover-select').val(),$('.popover-hidden').val());
        $this.popover('hide');

        //loadCommentData($this.attr('data-email'));
        // e.preventDefault();
      });
  }); 
                      });
                      function changeMood(obj){
                        var mood = $(obj).attr('data-mood');
                        $(obj).parent().find('.popover-hidden').val(mood);
                        $('.emoji-selected').removeClass('emoji-selected');
                        $(obj).addClass('emoji-selected');
                      }
                      function loadCommentData(email)
                      {
                        $.ajax({
                          url: 'sales_dashboard_new.php',
                          type: 'post',
                          data: {type:'load_comments',email:encodeURIComponent(email),},
                          dataType: 'html',
                          beforeSend: function() {

                          },  
                          complete: function() {

                          },      
                          success: function(html) {
                            $('.comment_data').html(html)



                          }
                        });   

                      }
                      function addCustomerComment(email,comment,comment_type,customer_mood)
                      {
                        $.ajax({
                          url: 'sales_dashboard_new.php',
                          type: 'post',
                          data: {type:'add_comment',email:encodeURIComponent(email),comment:encodeURIComponent(comment),comment_type:comment_type,customer_mood:customer_mood},
                          dataType: 'json',
                          beforeSend: function() {
                           $('.popover-submit').addClass('disabled');
                         },  
                         complete: function() {
                          $('.popover-submit').removeClass('disabled');
                        },      
                        success: function(json) {

                         if(json['success'])
                         {
       // alert('Comment added successfully');
     }
     else
     {
      alert('Error adding comment');
    }


  }
});   


                      }

                    </script>

                    <script>
                    window.chartColors = {
  red: 'rgb(255, 99, 132)',
  orange: 'rgb(255, 159, 64)',
  yellow: 'rgb(255, 205, 86)',
  green: 'rgb(75, 192, 192)',
  blue: 'rgb(51, 102, 204)',
  purple: 'rgb(153, 102, 255)',
  grey: 'rgb(201, 203, 207)'
};

    
    var color = Chart.helpers.color;
    var barChartData = {
      labels: [],
      datasets: []

    };

    var barChartData2 = {
      labels: [],
      datasets: []

    };
    var barChartDatax = {
      labels: [],
      datasets: []

    };

    window.onload = function() {
      
    };
      $(document).ready(function(e){

        setTimeout(function(){

             $.ajax({
                          url: 'sales_dashboard_new.php',
                          type: 'post',
                          data: {type:'load_weekly_data',chart_group:$('select[name=group_by]').val(),chart_weeks:$('select[name=chart_weeks]').val(),user_id:'<?php

                          if($_SESSION['is_sales_agent']==1 && !isset($_GET['user_id']))
                          {
                            echo $_SESSION['user_id'];
                          }
                          else
                          {
                            echo $_GET['user_id'];
                          }

                          ?>'},
                          dataType: 'json',
                          beforeSend: function() {

                          },  
                          complete: function() {

                          },      
                          success: function(json) {


                            var newDataset = {
        label: 'Sales',
        backgroundColor: window.chartColors.blue,
        borderColor: window.chartColors.blue,
        borderWidth: 1,
        data: []
      };



                              var labels = json.map(function(item) {
   //  console.log(item.date);                           
   barChartData.labels.push(item.date);
    
  });

                         for (var index = 0; index < barChartData.labels.length; ++index) {
        newDataset.data.push(json[index]['total']);
      }

      barChartData.datasets.push(newDataset);

                            
var ctx = document.getElementById('canvas').getContext('2d');
      window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
          responsive: true,
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: $('select[name=chart_weeks]:eq(0) option:selected').text()+' Data'
          }
        }
      });



      // bar2



                        // window.myBar.update(); 



                          }
                        }); 

             //
             $.ajax({
              url: 'sales_dashboard_new.php',
              type: 'post',
              data: {type:'load_quarter_data',quarter_group:$('select[name=quarter_by]').val(),user_id:'<?php

              if($_SESSION['is_sales_agent']==1 && !isset($_GET['user_id']))
              {
                echo $_SESSION['user_id'];
              }
              else
              {
                echo $_GET['user_id'];
              }

              ?>'},
              dataType: 'json',
              beforeSend: function() {

              },  
              complete: function() {

              },      
              success: function(json) {


                var newDatasetx = {
                  label: 'Sales Vs Target Sales',
                  backgroundColor: window.chartColors.blue,
                  borderColor: window.chartColors.blue,
                  borderWidth: 1,
                  data: []
                };



                var labels = json.map(function(item) {
   //  console.log(item.date);                           
   barChartDatax.labels.push(item.date);

 });
                for (var x = 0; x < barChartDatax.labels.length; ++x) {
                  newDatasetx.data.push(json[x]['total']);
                }

                barChartDatax.datasets.push(newDatasetx);


                var ctx = document.getElementById('targets_canvas_bar').getContext('2d');
                window.myBar = new Chart(ctx, {
                  type: 'bar',
                  data: barChartDatax,
                  options: {
                    responsive: true,
                    legend: {
                      position: 'top',
                    },
                    title: {
                      display: true,
                      text: 'Weekly Goals'
                    }
                  }
                });
                var config3 = {
                  type: 'pie',
                  data: {
                    datasets: [{
                      data: [
                      json[0]['target_sale'],
                      json[0]['sum_of_sale'],

                      ],
                      backgroundColor: [
                      window.chartColors.orange,

                      window.chartColors.green,
                      ],
                      label: 'Quarterly Goal'
                    }],
                    labels: [
                    '# Quarterly Target',
                    '# Target Achieved'
                    ]
                  },
                  options: {
                    responsive: true
                  }
                };
                var ctx3 = document.getElementById('targets_canvas').getContext('2d');
                var myPie3 = new Chart(ctx3, config3);
                $('#commission_date').html(json[0]['commission_date']);
                $('#targets_table tbody').html(json[0]['table_html']);
                $('#targets_table').tablesorter({
        textExtraction: function(node){ 
            // for numbers formattted like €1.000,50 e.g. Italian
            // return $(node).text().replace(/[.$£€]/g,'').replace(/,/g,'.');

            // for numbers formattted like $1,000.50 e.g. English
            return $(node).text().replace(/[,$£€]/g,'');
         }
    });

      // bar2



                        // window.myBar.update(); 



                      }
                    });


             $.ajax({
                          url: 'sales_dashboard_new.php',
                          type: 'post',
                          data: {type:'load_weekly_data2',chart_group:$('select[name=group_by]').val(),chart_weeks:$('select[name=chart_weeks]').val(),user_id:'<?php

                          if($_SESSION['is_sales_agent']==1 && !isset($_GET['user_id']))
                          {
                            echo $_SESSION['user_id'];
                          }
                          else
                          {
                            echo $_GET['user_id'];
                          }

                          ?>'},
                          dataType: 'json',
                          beforeSend: function() {

                          },  
                          complete: function() {

                          },      
                          success: function(json) {


                            var newDataset = {
        label: 'Vouchers Used',
        backgroundColor: window.chartColors.blue,
        borderColor: window.chartColors.blue,
        borderWidth: 1,
        data: []
      };



                              var labels = json.map(function(item) {
   //  console.log(item.date);                           
   barChartData2.labels.push(item.date);
    
  });

                         for (var index = 0; index < barChartData2.labels.length; ++index) {
        newDataset.data.push(json[index]['total']);
      }

      barChartData2.datasets.push(newDataset);

                            
var ctx = document.getElementById('canvas_vouchers').getContext('2d');
      window.myBar2 = new Chart(ctx, {
        type: 'bar',
        data: barChartData2,
        options: {
          responsive: true,
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: $('select[name=chart_weeks]:eq(0) option:selected').text()+' Data'
          }
        }
      });



      // bar2



                        // window.myBar.update(); 



                          }
                        }); 


          

          }, 2000);


       



      });



      var config = {
      type: 'pie',
      data: {
        datasets: [{
          data: [
            <?php echo $potential_sale;?>,
            <?php echo $potential_potential;?>,
            
          ],
          backgroundColor: [
            window.chartColors.red,
            
            window.chartColors.blue,
          ],
          label: 'Potential vs Sale'
        }],
        labels: [
          'Weeks Sale',
          'Potential'
        ]
      },
      options: {
        responsive: true
      }
    };

    


    var config2 = {
      type: 'pie',
      data: {
        datasets: [{
          data: [
            <?php echo $lbb_total_sent;?>,
            <?php echo $lbb_total_accepted;?>,
            
          ],
          backgroundColor: [
            window.chartColors.orange,
            
            window.chartColors.green,
          ],
          label: 'LBB Vouchers'
        }],
        labels: [
          '# LCD Sent',
          '# LCD Accepted'
        ]
      },
      options: {
        responsive: true
      }
    };
    

    window.onload = function() {

      //var ctx = document.getElementById('potential_canvas').getContext('2d');
      //window.myPie = new Chart(ctx, config);

      //var ctx2 = document.getElementById('lbb_canvas').getContext('2d');
      //var myPie2 = new Chart(ctx2, config2);

       
    };

      function updateLastContactDate(email,obj)
      {
        if(!confirm('Are you sure want to update the contacted date?'))
        {
          return false;
        }
        $.ajax({
                          url: 'sales_dashboard_new.php',
                          type: 'post',
                          data: {type:'update_date',email:encodeURIComponent(email)},
                          dataType: 'json',
                          beforeSend: function() {
                            $(obj).html('...');
                          // $('.popover-submit').addClass('disabled');
                         },  
                         complete: function() {
                          //$('.popover-submit').removeClass('disabled');
                        },      
                        success: function(json) {

                         if(json['success'])
                         {
                          $(obj).html(json['success']);
       // alert('Comment added successfully');
     }
     else
     {
      alert('Error adding comment');
    }


  }
});   
      }

      function changeChartGroupValues(objValue)
      {
        // var objValue = $('select[name=group_by] option:selected').val();
        var html='';
          if(objValue=='Weeks')
          {
              html='<option value="12" <?php echo ($_GET['chart_weeks']==12?'selected':'');?> >12 Weeks</option>';
              html+='<option value="24" <?php echo ($_GET['chart_weeks']==24?'selected':'');?>>24 Weeks</option>';
              html+='<option value="36" <?php echo ($_GET['chart_weeks']==36?'selected':'');?>>36 Weeks</option>';
              html+='<option value="52" <?php echo ($_GET['chart_weeks']==52?'selected':'');?>>52 Weeks</option>';
              html+='<option value="76" <?php echo ($_GET['chart_weeks']==76?'selected':'');?>>76 Weeks</option>';
          }
          else if(objValue=='Months')
          {
               html='<option value="3" <?php echo ($_GET['chart_weeks']==3?'selected':'');?>>3 Months</option>';
              html+='<option value="6" <?php echo ($_GET['chart_weeks']==6?'selected':'');?>>6 Months</option>';
              html+='<option value="12" <?php echo ($_GET['chart_weeks']==12?'selected':'');?>>12 Months</option>';
              html+='<option value="18" <?php echo ($_GET['chart_weeks']==18?'selected':'');?>>18 Months</option>';
              html+='<option value="24" <?php echo ($_GET['chart_weeks']==24?'selected':'');?>>24 Months</option>';
          }
          else
          {
             html='<option value="4" <?php echo ($_GET['chart_weeks']==4?'selected':'');?>>4 Quarters</option>';
              html+='<option value="8" <?php echo ($_GET['chart_weeks']==8?'selected':'');?>>8 Quarters</option>';
              
          }

          $('.chart_weeks').html(html);
      }


    

  

   

  
  </script>

 <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>

         <script>

     $(document).ready(function(e) {

             $(".tablesorter").tablesorter({
        textExtraction: function(node){ 
            // for numbers formattted like €1.000,50 e.g. Italian
            // return $(node).text().replace(/[.$£€]/g,'').replace(/,/g,'.');

            // for numbers formattted like $1,000.50 e.g. English
            return $(node).text().replace(/[,$£€]/g,'');
         }
    }); 
             $('select[name=group_by').trigger('change');
             <?php
             if(isset($_GET['tab']))
             {
              ?>
              $('input[data-tab=<?php echo $_GET['tab'];?>]').trigger('click');
              <?php
             }
             ?>


             document.getElementById("canvas_vouchers").onclick = function(evt) {
      var activePoints = window.myBar2.getElementsAtEvent(evt);
      if (activePoints[0]) {
        var chartData = activePoints[0]['_chart'].config.data;
        var idx = activePoints[0]['_index'];

        var label = chartData.labels[idx];
        var value = chartData.datasets[0].data[idx];


        loadVoucherData(label);
        //showPopup(label);
        // var url = "http://example.com/?label=" + label + "&value=" + value;
        // console.log(url);
        // alert(url);
      }
    };

   loadVoucherData('');


        });

     function loadVoucherData(label)
     {

       $.ajax({
                          url: 'sales_dashboard_new.php',
                          type: 'post',
                          data: {type:'load_voucher_data',date:label,chart_group:$('select[name=group_by]').val(),chart_weeks:$('select[name=chart_weeks]').val(),user_id:'<?php

                          if($_SESSION['is_sales_agent']==1 && !isset($_GET['user_id']))
                          {
                            echo $_SESSION['user_id'];
                          }
                          else
                          {
                            echo $_GET['user_id'];
                          }

                          ?>'},
                          dataType: 'html',
                          beforeSend: function() {

                          },  
                          complete: function() {

                          },      
                          success: function(html) {

                            $('#voucher_details_table').html(html);
                          }

                        });
     }

     function showPopup(label,reason)
     {
       $.fancybox({
        href: '<?php echo $host_path;?>popupfiles/used_vouchers_history.php?reason='+reason+'&date='+label+'&chart_group='+$('select[name=group_by]').val()+'&user_id=<?php

                          if($_SESSION['is_sales_agent']==1 && !isset($_GET['user_id']))
                          {
                            echo $_SESSION['user_id'];
                          }
                          else
                          {
                            echo $_GET['user_id'];
                          }

                          ?>', 
        modal: false,
        width: '1100px', autoCenter: true, autoSize: false,
        type: 'iframe'
    });
     }

      function showCommissionDetail(date)
     {
       $.fancybox({
        href: '<?php echo $host_path;?>popupfiles/commission_detail.php?date='+date+'&user_id=<?php

                          if($_SESSION['is_sales_agent']==1 && !isset($_GET['user_id']))
                          {
                            echo $_SESSION['user_id'];
                          }
                          else
                          {
                            echo $_GET['user_id'];
                          }

                          ?>', 
        modal: false,
        width: '1100px', autoCenter: true, autoSize: false,
        type: 'iframe'
    });
     }

     function agentDashboardTab(){

		// if($.trim($("#tabDashboard").html())=='') {
			var html;
			html = '<iframe style="width:95%;height:1000px" src="agent_dashboard.php?hide_header=1"></iframe>';
			$('#tabDashboard').html(html);
		// } 
	}
     </script>