<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';

$pageLink = 'conversion_report.php';

page_permission('conversion_report');

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

$extra_query ='';
$date_start = date('Y-m-d',strtotime('-7 days'));
$date_end = date('Y-m-d');
if($_GET['filter_date']!='')
{
  if($_GET['filter_date']=='last month')
  {
    $date_start= date("Y-m-j", strtotime("first day of previous month"));
$date_end =  date("Y-m-j", strtotime("last day of previous month"));
  }
  elseif($_GET['filter_date']=='custom')
  {
    $date_start = date('Y-m-d',strtotime($_GET['filter_date_range_start']));
  $date_end = date('Y-m-d',strtotime($_GET['filter_date_range_end']));
  }
  else
  {
    $date_start = date('Y-m-d',strtotime($_GET['filter_date']));
    $date_end = date('Y-m-d');
  }
}


if(isset($_GET['filter_sales_agent']) && $_GET['filter_sales_agent']!='')
{
  $extra_query.=" AND a.user_id='".(int)$_GET['filter_sales_agent']."'";
}
if(isset($_GET['test']))
  {
    print_r($_GET);
  }

if(isset($_GET['filter_busienss_type']) && $_GET['filter_busienss_type']!='')
{

  $_temp = '"'.implode('","', $_GET['filter_busienss_type']).'"';
  $extra_query.=' AND b.source in ('.$_temp.')';
}
if(isset($_GET['filter_source']) && $_GET['filter_source']!='')
{
  $extra_query.=" and ((select k.source from oc_customer_source k where k.email=a.email and k.type='hear' limit 1)='".$db->func_escape_string($_GET['filter_source'])."')";
}
$date_query ='';
 $date_query=" and date(a.date_added) between '".$date_start."' and '".$date_end."'";
if(isset($_GET['filter_type']))
{
  if($_GET['filter_type']=='New')
  {
    $date_query=" and date(a.date_added) between '".$date_start."' and '".$date_end."'";
  }
  elseif($_GET['filter_type']=='Winback')
  {
     $date_query=" and date(d.order_date) between '".$date_start."' and '".$date_end."' and (select date(dd.order_date) from inv_orders dd where dd.email=a.email and lower(dd.order_status) in ('processed','shipped','completed') order by order_date desc limit 1,1)<='".date('Y-m-d', strtotime("-2 months", strtotime($date_end)))."' ";
  

  }
  else
  {
     $date_query=" and (date(a.date_added) between '".$date_start."' and '".$date_end."' or (date(d.order_date) between '".$date_start."' and '".$date_end."' and (select date(dd.order_date) from inv_orders dd where dd.email=a.email and lower(dd.order_status) in ('processed','shipped','completed') order by order_date desc limit 1,1)<='".date('Y-m-d', strtotime("-2 months", strtotime($date_end)))."' )) ";
  }
}

$sort = $_GET['sort'];
$order_by = $_GET['order_by'];
$sort_array  = array('account','business_type','source','email_count','calls_count','order_date','no_of_orders','total_ordered');
if(!in_array($sort, $sort_array))
{
  $sort = $sort_array[0];
  $order_by = 'desc';
}

if($sort=='account')
{
  $sort = "CONCAT(a.firstname,' ',a.lastname) ";
}
elseif($sort=='business_type')
{
 $sort = "b.source"; 
}
elseif($sort=='source')
{
 $sort = "listen"; 
}
elseif($sort=='email_count')
{
 $sort = "SUBSTRING_INDEX(a.freshsales_contact_data, '-', 1)"; 
}
elseif($sort=='calls_count')
{
 $sort = "SUBSTRING_INDEX(SUBSTRING_INDEX(a.freshsales_contact_data, '-', 2), '-',-1)"; 
}
elseif($sort=='order_date')
{
 $sort = "d.order_date"; 
}
elseif($sort=='no_of_orders')
{
 $sort = "count_order"; 
}
elseif($sort=='total_ordered')
{
 $sort = "total_ordered"; 
}
$orderby = ' ORDER BY '.$sort.' '.$order_by;
if($order_by=='asc') $order_by='desc'; else $order_by = 'asc';


   // $inv_query =("select distinct (select k.source from oc_customer_source k where k.email=a.email and k.type='hear' limit 1) as listen, a.freshsales_contact_data, a.last_order, a.user_id, a.firstname,a.lastname,a.email,a.company,b.source,(select count(*) from inv_orders c where a.email=c.email and lower(order_status) in ('processed','shipped','completed')) as count_order,(select sum(c.sub_total+c.tax+c.shipping_amount) from inv_orders c where a.email=c.email and lower(c.order_status) in ('processed','shipped','completed')) as total_ordered,d.order_id,d.email,d.order_status,d.order_date  from inv_customers a,oc_customer_source b,inv_orders d where (a.email)=(b.email) and d.email=a.email and b.type='business_type' $date_query and lower(d.order_status) in ('processed','shipped','completed') $extra_query group by a.email $orderby");

$inv_query="select distinct a.date_added, (select k.source from oc_customer_source k where k.email=a.email and k.type='hear' limit 1) as listen, a.freshsales_contact_data, (select c.order_date from inv_orders c where a.email=c.email and lower(order_status) in ('on hold','processed','shipped','completed') order by c.order_date desc limit 1) as last_order, a.user_id, a.firstname,a.lastname,a.email,a.company,b.source,(select count(*) from inv_orders c where a.email=c.email and lower(order_status) in ('on hold','processed','shipped','completed')) as count_order,(select sum(c.sub_total+c.tax+c.shipping_amount) from inv_orders c where a.email=c.email and lower(c.order_status) in ('processed','shipped','completed')) as total_ordered,d.order_id,d.order_status,d.order_date from inv_customers a

inner join 
oc_customer_source b
on (a.email=b.email)
left join
inv_orders d 
on(a.email=d.email)
left join
inv_customers t2
on (a.id=t2.parent_id)
where a.parent_id=0 and   b.type='business_type' $date_query $extra_query  group by a.email $orderby";
// echo $inv_query;
if(isset($_GET['export_csv']) && $_GET['export_csv']==1)
{
  	$filename = 'conversion_report.csv';
	$fp = fopen($filename, "w");
	$headers = array("Lead Date","Account","Company","Email","Sales Agent","Business Type","Source","Email Count","Calls Count","Call Duration","Last Order Date","# of Orders","Total Ordered");
	fputcsv($fp, $headers);

	$rows = $db->func_query($inv_query);

	foreach($rows as $row)
	{
		 $contact_details = explode("-", $row['freshsales_contact_data']);
         $email_count = (int)$contact_details[0];
         $calls_count = $contact_details[1]+$contact_details[2];
         $calls_recorded = gmdate("H:i:s", $contact_details[3]);

         $rowData = array(americanDate($row['date_added']),$row['firstname'].' '.$row['lastname'],$row['company'],$row['email'],get_username($row['user_id']),$row['source'],$row['listen'],(int)$email_count,$calls_count,$calls_recorded,americanDate($row['last_order']),$row['count_order'],$row['total_ordered']);
         fputcsv($fp, $rowData);

	}
	fclose($fp);
	header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);
exit;

}

   $splitPage = new splitPageResults($db, $inv_query, $end, $pageLink, $page);
//Getting All Messages
$data = $db->func_query($splitPage->sql_query);



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
  <title>Conversion Report</title>
  <style>
  #summary table th{
    font-size:16px;
  }
  </style>
  <script>
 
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
    <h2 align="center">Conversion Report</h2>
    <table width="96%">
      <tr>
        <td style="vertical-align: top;width: 25%" align="center" >
          <br>
          <br>
          <font style="font-size:
          x-large;">Search Filters</font>
          <form  id="frmorder" action="" method="get">
            <table width="90%" cellpadding="3" cellspacing="3" border="0" align="center">
              <tr>
                <td>Type
                  <br>
                  <select style="width:100%" name="filter_type">
                    <option <?php echo ($_GET['filter_type']=='New'?'selected':'');?>>New</option>
                    <option <?php echo ($_GET['filter_type']=='Winback'?'selected':'');?>>Winback</option>
                    <option <?php echo ($_GET['filter_type']=='Both'?'selected':'');?>>Both</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>Date Range
                  <br>
                  <select style="width:100%" name="filter_date">
                    <option value="-7 days" <?php echo ($_GET['filter_date']=='-7 days'?'selected':'');?>>7 Days</option>
                    <option value="-30 days" <?php echo ($_GET['filter_date']=='-30 days'?'selected':'');?>>30 Days</option>
                    <option value="last month" <?php echo ($_GET['filter_date']=='last month'?'selected':'');?>>Last Month</option>
                    <option value="custom" <?php echo ($_GET['filter_date']=='custom'?'selected':'');?>>Custom Date</option>
                  </select>
                  <br>
                  or
                  <br>
                  <input type="date" name="filter_date_range_start" value="<?php echo $_GET['filter_date_range_start'];?>" style="width: 45%;margin-right:2px; "  >
                  <input type="date" name="filter_date_range_end" value="<?php echo $_GET['filter_date_range_end'];?>" style="width: 45%;margin-right:2px; "  >
                </td>
              </tr>
              <tr>
                <td>Sales Agent
                <br>
                <select style="width:100%" name="filter_sales_agent">
                  <option value="">All</option>
                  <?php
                  $agents = $db->func_query("SELECT * FROM inv_users WHERE is_sales_agent=1 order by name");
                  foreach($agents as $agent){
                    ?>
                      <option value="<?php echo $agent['id'];?>" <?php echo ($_GET['filter_sales_agent']==$agent['id']?'selected':'');?>><?php echo $agent['name'];?></option>
                    <?php
                  }
                  ?>
                </select>
                </td>
              </tr>

               <tr>
                <td>Business Type
                <br>
                <select style="width:100%" name="filter_busienss_type[]" multiple="" size="10">
                  
                  <?php
                  $rows = $db->func_query("SELECT DISTINCT source FROM oc_customer_source WHERE type='business_type' order by source");
                  foreach($rows as $row){
                    ?>
                      <option <?php echo ((in_array($row['source'], $_GET['filter_busienss_type']))?'selected':'');?>><?php echo $row['source'];?></option>
                    <?php
                  }
                  ?>
                </select>

                
                </td>
              </tr>

               <tr>
                <td>Source
                <br>
                <select style="width:100%" name="filter_source">
                  <option value="">All</option>
                  <?php
                  $rows = $db->func_query("SELECT DISTINCT source FROM oc_customer_source WHERE type='hear' order by source");
                  foreach($rows as $row){
                    ?>
                      <option <?php echo ($_GET['filter_source']==$row['source']?'selected':'');?> ><?php echo $row['source'];?></option>
                    <?php
                  }
                  ?>
                </select>
                </td>
              </tr>

            </table>
            <br>
            <input type="button" onClick="$('input[name=export_csv]').val(0);$('#frmorder').submit();" name="search" style="width:130px" value="Search" class="button" /> &nbsp&nbsp&nbsp&nbsp
            <?php
            if($_SESSION['login_as']=='admin')
            {
              ?>
              <input type="button" name="" value="Export CSV" onClick="$('input[name=export_csv]').val(1);$('#frmorder').submit();" class="button button-danger"  />
              <?php
            }
            ?>
            <input type="hidden" name="export_csv" value="0">
          </form>
        </td>
        <td style="vertical-align: top;">
          <div id="summary" style="text-align: center;">
          <table width="100%">

          <tr>
          
          <th id="total_account">Total Accounts: <span></span></th>
          <th id="avg_order_size">Avg Order Size: <span></span></th>
          <th id="avg_contacts">Avg Contacts: <span></span></th>
          

          </tr>
          <tr>
          
          <th id="total_orders">Total # Orders: <span></span></th>
          <th ></th>
          <th > <span></span></th>
          

          </tr>
          </table>
          
              </div>


        <table cellpadding="0" class="xtable" cellspacing="0" style="border:1px solid #ddd;clear:both">
        <tr>
        <th><a <?=($sort=='account'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=account&order_by=<?=$order_by;?>&<?=$parameters;?>">Account</a></th>
        <th>Sales Agent</th>
        <th><a <?=($sort=='business_type'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=business_type&order_by=<?=$order_by;?>&<?=$parameters;?>">Type</a></th>
        <th> <a <?=($sort=='source'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=source&order_by=<?=$order_by;?>&<?=$parameters;?>">Source</th>
        <th> <a <?=($sort=='email_count'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=email_count&order_by=<?=$order_by;?>&<?=$parameters;?>">Emails</th>
        <th> <a <?=($sort=='calls_count'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=calls_count&order_by=<?=$order_by;?>&<?=$parameters;?>">Calls</th>
        <th><a <?=($sort=='order_date'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=order_date&order_by=<?=$order_by;?>&<?=$parameters;?>">Order Date</a></th>
        <th><a <?=($sort=='no_of_orders'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=no_of_orders&order_by=<?=$order_by;?>&<?=$parameters;?>"># of Orders</a></th>
        <th><a <?=($sort=='total_ordered'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=total_ordered&order_by=<?=$order_by;?>&<?=$parameters;?>">Total Ordered</a></th>
        </tr>
        <tbody>
        <?php

      $i=0;
        foreach($data as $row)
        {
         $contact_details = explode("-", $row['freshsales_contact_data']);
         $email_count = (int)$contact_details[0];
         $calls_count = $contact_details[1]+$contact_details[2];
         $calls_recorded = gmdate("H:i:s", $contact_details[3]) ;; 
          ?>

          <tr>
          <td><?php echo $row['firstname'].' '.$row['lastname'];?>
          <?php
          if($row['company'])
          {
          ?>
          <br><?php echo $row['company'];?>
          <?php
        }
        ?>
          <br><?php echo linkToProfile($row['email']);?></td>
          <td><?php

          $agent =  get_username($row['user_id']);
          if(!$agent)
          {
            $agent = 'N/A';
          }
          echo $agent;
           ?></td>
          <td><?php echo $row['source'];?></td>
          <!-- <td><?php //echo $db->func_query_first_cell("SELECT source FROM oc_customer_source WHERE email='".$row['email']."' and type='hear'");?></td> -->
          <td><?php echo $row['listen'];?></td>
          <td><?php echo (int)$email_count;?></td>
          <td><span data-tooltip="Total Call Time: <?php echo $calls_recorded;?>" class="tag <?php echo ($calls_count==0?'red':'blue');?>-bg"><?php echo $calls_count;?></span></td>
          <td><?php echo americanDate($row['last_order']);?></td>
          <td><?php echo $row['count_order'];?></td>
          <td><?php echo '$'.number_format($row['total_ordered'],2);?></td>
          </tr>
          <?php
          $i++;
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
  