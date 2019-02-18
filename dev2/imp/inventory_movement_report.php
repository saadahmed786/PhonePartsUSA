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
$statuses = array('order has been shipped.','quantity has rolled back.','stock adjustment has been made.','processed &rarr; shipped','shipped &rarr; canceled','shipment QC &rarr; completed','stock adjustment.','stock adjustment (cycle count).','stock adjustment (add).','stock adjustment (remove).','stock adjustment (rtv).');

  $implode_query = "'" . implode("','", $statuses) . "'";
if(isset($_GET['filter_date_range_start']) && isset($_GET['filter_date_range_end']) )
{

$filter_query = array();
$filter_query[] = '1 = 1';
  if(isset($_GET['filter_description']) && $_GET['filter_description']!='')
  {
    $filter_query[] = "lower(description)='".$db->func_escape_string(str_replace("~", "&rarr;", $_GET['filter_description']))."'";
  }

  if(isset($_GET['filter_user']) && $_GET['filter_user']!='')
  {
    $filter_query[] = "user_id='".(int)$_GET['filter_user']."'";
  }

  $filter_query =  implode(" AND ", $filter_query);


  $inv_query="SELECT *,(select b.quantity from oc_product b where inv_product_ledger.sku=b.model limit 1) as new_qty FROM inv_product_ledger where lower(description) in (".$implode_query.") and




   date(date_added) between '".$_GET['filter_date_range_start']."' and '".$_GET['filter_date_range_end']."' ".(isset($_GET['sku'])?" and trim(lower(sku))='".trim(strtolower($_GET['sku']))."' ":"")." AND ".$filter_query."  order by date_added desc";
   if(isset($_GET['debug']))
   {
    echo $inv_query;
   }
   if($_GET['search']=='csv')
   {
    if(isset($_GET['sku']))
    {

      $filename = $_GET['sku'].'_movement-'.$_GET['filter_date_range_start'].'-'.$_GET['filter_date_range_end'].'.csv';
    }
    else
    {
      
      $filename = 'inventory_movement-'.$_GET['filter_date_range_start'].'-'.$_GET['filter_date_range_end'].'.csv';
    }
$fp = fopen($filename, "w");
$headers = array("Date","SKU","Qty Change","Previous Qty","New Qty", "Ref","Type","User","Notes");
fputcsv($fp, $headers,',');

foreach($db->func_query($inv_query) as $row)
{

    // switch(strtolower($row['description'])){
    //         case 'shipped &rarr; canceled':
    //         case 'stock adjustment has been made.':
    //         case 'stock adjustment.':
    //         case 'stock adjustment.':
    //         case 'stock adjustment (add).':
    //         case 'stock adjustment (cycle count).':
    //         case 'processed &rarr; canceled':
    //         case 'on hold &rarr; canceled':
    //         case 'shipped &rarr; canceled':
    //         case 'shipment qc &rarr; completed':
            
    //         $sign='+';
    //         break;
    //         default:
    //         $sign = '-';
    //         break;
    //       }
         
          switch(strtolower($row['description'])){
            
            case 'stock adjustment (add).':
             $qty_change = ($row['quantity']);
             $new_qty = $row['quantity']+$row['on_hand'];
    
            break;

            case 'stock adjustment (remove).':
            $qty_change = '-'.($row['quantity']);
             $new_qty = $row['on_hand']-$row['quantity'];                        
    
            break;

            case 'order has been shipped.':
            $qty_change = '-'.($row['quantity']);
             $new_qty = $row['on_hand']-$row['quantity'];                        
    
            break;

            case 'stock adjustment (rtv).':
            $qty_change = '-'.($row['quantity']);
             $new_qty = $row['on_hand']-$row['quantity'];                        
    
            break;

            case 'stock adjustment (cycle count).':
               $qty_change = ($row['quantity']-$row['on_hand']);
          $new_qty = $row['quantity'];
            break;

            default:
             $qty_change = ($row['quantity']-$row['on_hand']);
             $new_qty = $row['quantity'];

            break;


            default:
               $qty_change = ($row['quantity']-$row['on_hand']);
          $new_qty = $row['quantity'];
            break;
          }

          if(strtolower($row['description'])=='stock adjustment (cycle count).')
          {
            $check = (int)$row['on_hand'] - (int)$row['quantity'];
            if($check==0)
            {
              $sign = '';
            }
            elseif($check>0)
            {
              $sign = '-';
            }
            elseif($check<0)
            {
              $sign = '+';
            }
          }


  $rowData = array(americanDate($row['date_added']),$row['sku'],$qty_change,$row['on_hand'],$new_qty,$row['order_id'],$row['description'],get_username($row['user_id']),$row['notes']);
  fputcsv($fp, $rowData,',');
}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);
exit;

   }
   $splitPage = new splitPageResults($db, $inv_query, $end, $datapageLink, $page);
//Getting All Messages
$data = $db->func_query($splitPage->sql_query);

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
  <link rel="stylesheet" type="text/css" href="http://phonepartsusa.com/catalog/view/theme/ppusa2.0/stylesheet/font-awesome.css">
  <title>Inventory Movement Report</title>
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
    <h2 align="center">Inventory Movement Report</h2>
    <form  name="order" action="" method="get">
    <table width="96%">
      <tr>
        <td style="vertical-align: top;width: 25%" align="center" >
          <br>
          <br>
          <font style="font-size:
          x-large;">Search Filters</font>
          
            <table width="90%" cellpadding="3" cellspacing="3" border="0" align="center">
             
              <tr>
                <td>
                  
                  <input type="date" name="filter_date_range_start" value="<?php echo $_GET['filter_date_range_start'];?>" style="width: 90%;margin-right:2px; "  >
                  </td>
                  <td>
                  <input type="date" name="filter_date_range_end" value="<?php echo $_GET['filter_date_range_end'];?>" style="width: 90%;margin-right:2px; "  >
                </td>
              </tr>
              
            </table>
            <br>
            <input type="hidden" name="search" id="search" value="report">
            <input type="button"  id="" style="width:130px" value="View Report" class="button" onclick="$('#search').val('report');$('form[name=order]').submit();"  /> &nbsp&nbsp&nbsp&nbsp
           
              <input type="button" name="export_csv" value="Download CSV" class="button button-danger" onclick="$('#search').val('csv');$('form[name=order]').submit();"  />
              
          
        </td>
        <td style="vertical-align: top;">
          
        <div style="text-align: center">
        <select name="filter_description">
        <option value="">Transaction Type</option>
        <?php
        foreach($statuses as $status)
        {
          if($status=='stock adjustment (rtv).')
          {
            $new_status = 'Transferred to RTV';
          }
          else
          {
            $new_status = ucwords($status);
          }
          ?>
          <option value="<?php echo str_replace("&rarr;", "~", $status);?>" <?php echo ($_GET['filter_description']==str_replace("&rarr;", "~", $status)?'selected':'');?>><?php echo ($new_status);?></option>
          <?php
        }

        ?>
        </select>

        <select name="filter_user">
        <option value="">Select User</option>
        <?php
        $users = $db->func_query("SELECT distinct a.user_id,b.name FROM inv_product_ledger a,inv_users b WHERE a.user_id=b.id and lower(a.description) in (".$implode_query.") and b.status=1 order by lower(b.name) ");
        foreach($users as $user)
        {
          ?>
          <option value="<?php echo $user['user_id'];?>" <?php echo ($_GET['filter_user']==$user['user_id']?'selected':'');?>><?php echo ($user['name']);?></option>
          <?php
        }

        ?>
        </select>

        <input type="button"  id="" style="width:130px" value="Filter" class="button" onclick="$('#search').val('report');$('form[name=order]').submit();"  /> &nbsp&nbsp&nbsp&nbsp




        </div>

        <table cellpadding="0" class="xtable" cellspacing="0" style="border:1px solid #ddd;clear:both">
        <tr>
         <th>Date</th>
        <th>SKU</th>
        <th>Qty Change</th>
        <th>Previous Qty</th>
        <th>New Qty</th>
       
        <th>Ref</th>
        <th>Type</th>
        <th>User</th>
        <th>Notes</th>
        
        </tr>
        <tbody>
        <?php

        foreach($data as $row)
        {

          switch(strtolower($row['description'])){
            
            case 'stock adjustment (add).':
             $qty_change = ($row['quantity']);
             $new_qty = $row['quantity']+$row['on_hand'];
    
            break;

            case 'stock adjustment (remove).':
            $qty_change = '-'.($row['quantity']);
             $new_qty = $row['on_hand']-$row['quantity'];                        
    
            break;

             case 'order has been shipped.':
            $qty_change = '-'.($row['quantity']);
             $new_qty = $row['on_hand']-$row['quantity'];                        
    
            break;

            case 'stock adjustment (rtv).':
            $qty_change = '-'.($row['quantity']);
             $new_qty = $row['on_hand']-$row['quantity'];                        
    
            break;

            case 'stock adjustment (cycle count).':
               $qty_change = ($row['quantity']-$row['on_hand']);
          $new_qty = $row['quantity'];
            break;

            default:
             $qty_change = ($row['quantity']-$row['on_hand']);
             $new_qty = $row['quantity'];

            break;


            default:
               $qty_change = ($row['quantity']-$row['on_hand']);
          $new_qty = $row['quantity'];
            break;
          }
          // print_r($row);exit;
          ?>
          <tr>
          <td><?php echo americanDate($row['date_added']);?></td>
          <td><?php echo linkToProduct($row['sku'], $host_path);?></td>
          <td><?php echo ($qty_change);?></td>
          <td><?php echo $row['on_hand'];?></td>
          <td><?php echo $new_qty;?></td>
          <td><?php echo (trim(substr($row['description'], 0,9))=='Shipment'?linkToShipment($row['order_id'],$host_path,'Shipment '.$row['order_id']):(trim(substr($row['description'], -6))=='(RTV).'?'<a href="'.$host_path.'addedit_rejectedshipment.php?shipment_id='.$row['order_id'].'">RTV</a>':linkToOrder($row['order_id'],$host_path))) .'';?></td>
          <td><?php echo $row['description'];?></td>
          <td><?php echo get_username($row['user_id']);?></td>
          <td><?php echo $row['notes'];?></td>

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
    </form>
  </body>

  </html>
  <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
  