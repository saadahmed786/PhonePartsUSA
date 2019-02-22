<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'shipstation_trackings';
$pageName = 'Shipping';
$pageLink = 'shipping_management.php';
$pageCreateLink = false;
$pageSetting = false;
page_permission($perission);
$table = '`inv_orders`';

if(isset($_POST['action']) && $_POST['action']=='update_payor_vendor')
{
	$id = (int)$_POST['id'];
	$payor = $_POST['payor'];

	$db->db_exec("UPDATE inv_shipments SET payor='".$payor."' where id='".$id."'");
	exit;
}
if(isset($_POST['action']) && $_POST['action']=='update_payment_status_vendor')
{
	$id = (int)$_POST['id'];
	$payment_status = $_POST['payment_status'];

	$db->db_exec("UPDATE inv_shipments SET payment_status='".$payment_status."' where id='".$id."'");
	exit;
}

if(isset($_POST['action']) && $_POST['action']=='update_shipment_no')
{
	$id = (int)$_POST['id'];
	$shipment_no = $_POST['shipment_no'];

	$db->db_exec("UPDATE inv_shipstation_transactions SET order_id='".$shipment_no."' where id='".$id."'");
	exit;
}

if(isset($_POST['action']) && $_POST['action']=='update_payor')
{
	$id = (int)$_POST['id'];
	$payor = $_POST['payor'];

	$db->db_exec("UPDATE inv_shipstation_transactions SET payor='".$payor."' where id='".$id."'");
	exit;
}


if(isset($_POST['action']) && $_POST['action']=='update_reason')
{
	$id = (int)$_POST['id'];
	$reason = $db->func_escape_string($_POST['reason']);

	$db->db_exec("UPDATE inv_shipstation_transactions SET reason='".$reason."' where id='".$id."'");
	exit;
}


	if( $_FILES['upload_csv']['tmp_name']){
	$csv_mimetypes = array(
        'text/csv',
        'text/plain',
        'application/csv',
        'text/comma-separated-values',
        'application/excel',
        'application/vnd.ms-excel',
        'application/vnd.msexcel',
        'text/anytext',
        'application/octet-stream',
        'application/txt',
	);

	$type = $_FILES['upload_csv']['type'];
	// echo $type;exit;
	if(in_array($type,$csv_mimetypes)){
		$filename = $_FILES['upload_csv']['tmp_name'];
		$handle   = fopen("$filename", "r");
		$k=0;
		while ($data = fgetcsv($handle,1000,",","'")) {
		if($k==0)
		{
			$k++;
			continue;
		}
		// print_r($data);exit;
		if(trim($data[2]))
		{
			$shipment_date = date('Y-m-d H:i:s',strtotime(trim($data[0])));
			$carrier = trim($db->func_escape_string($data[1]));
			$tracking_number = trim($db->func_escape_string($data[2]));
			$ref = trim($db->func_escape_string($data[3]));
			$billed_cost = (float)$data[4];

			$check = $db->func_query_first("SELECT * FROM inv_shipstation_transactions where tracking_number='".$tracking_number."' and tracking_number<>''");
			if($check)
			{
				$db->db_exec("UPDATE inv_shipstation_transactions SET billed_cost='".(float)$billed_cost."',confirmation='delivery' where id='".(int)$check['id']."'");


				if($check['order_id']!=$ref && $ref!='')
				{
						$db->db_exec("UPDATE inv_shipstation_transactions SET order_id='".$ref."',confirmation='delivery' where id='".(int)$check['id']."'");
				}
			}
			else
			{
				$db->db_exec("INSERT INTO inv_shipstation_transactions SET order_id='".$ref."',tracking_number='".$tracking_number."',shipping_cost='".$billed_cost."',billed_cost='".$billed_cost."',ship_date='".$shipment_date."',carrier_code='".strtolower($carrier)."',package_code='package',confirmation='delivery',weight='0',units='ounces',date_added='".date('Y-m-d H:i:s')."'");
			}
		}
		
		$k++;
    } 

		if(!$_SESSION['message']){
			$_SESSION['message'] = 'CSV Uploaded Successfully.';
		}
		echo "<script>window.location='shipping_management.php';</script>";
		exit;
	}
	else{
		$_SESSION['message'] = 'Uploaded file is not valid, try again';
		echo "<script>location.reload();</script>";exit;
	}
}


// Getting Page information
if (isset($_GET['page'])) {
	$page = intval($_GET['page']);
}

if ($page < 1) {
	$page = 1;
}

$parameters = '&page='.$page;
//Setting PAgination Limits
$max_page_links = 10;
$num_rows = 30;
$start = ($page - 1) * $num_rows;

// Search Setup
$where = array();

if ($_GET['filter'] == 'Search') {
	
	
	if($_GET['filter_order_id'])
	{
	$where[] = " order_id LIKE '%".$db->func_escape_string($_GET['filter_order_id'])."%'";
	$parameters.='&filter_order_id='.$_GET['filter_order_id'];
	}


	if($_GET['filter_transaction_id'])
	{
	$where[] = " tracking_number LIKE '%".$db->func_escape_string($_GET['filter_transaction_id'])."%'";
	$parameters.='&filter_transaction_id='.$_GET['filter_transaction_id'];
	}

	if($_GET['filter_order_date'])
	{
	$where[] = " DATE(ship_date) = '".date('Y-m-d',strtotime($_GET['filter_order_date']))."'";
	$parameters.='&filter_order_date='.$_GET['filter_order_date'];
	}

	if(isset($_GET['filter_orderType']) and $_GET['filter_orderType']!='' )
	{
		$where[] = " is_mapped = '".$db->func_escape_string($_GET['filter_orderType'])."'";
		$parameters.='&filter_orderType='.$_GET['filter_orderType'];
	}

	if(isset($_GET['filter_voided']) and $_GET['filter_voided']!='' )
	{
		$where[] = " voided = '".$db->func_escape_string($_GET['filter_voided'])."'";
		$parameters.='&filter_voided='.$_GET['filter_voided'];

	}

	
}

if(!$where)
{
	$where[] = " 1 = 1 ";
}
$where = implode(" AND ", $where);

$sort = $_GET['sort'];
$order_by = $_GET['order_by'];

$sort_array  = array('ship_date','order_id','tracking_number','shipment_id','shipping_charge','carrier_code','service_code');

	if(!in_array($sort, $sort_array))
{
	$sort = $sort_array[0];
	$order_by = 'desc';
}



// $parameters = str_replace(array_values($sort_array), '', $parameters);

// $parameters = str_replace('&sort=', '', $parameters);
// $parameters = str_replace('sort=', '', $parameters);
// $parameters = str_replace('&order_by=asc', '', $parameters);
// $parameters = str_replace('&order_by=desc', '', $parameters);	

// $parameters = rtrim($parameters,'&');

//$parameters.='&sort='.$sort;
//$parameters.='&order_by='.$order_by;





$orderby = ' ORDER BY `'.$sort.'` '.$order_by;
if($sort=='shipping_charge')
{
$orderby = ' ORDER BY shipping_cost+insurance_cost '.$order_by;

}

if($order_by=='asc') $order_by='desc'; else $order_by = 'asc';


//Writing query 
$inv_query = "SELECT * FROM inv_shipstation_transactions WHERE confirmation='delivery'  and $where
$orderby";

// echo $inv_query;


//Using Split Page Class to make pagination
$splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);
// print_r($splitPage);
//Getting All Messages

$rows = $db->func_query($splitPage->sql_query);





  
$rows_main = $db->func_query("select sum(c.shipping_cost) as shipping_cost,sum(a.shipping_cost) as shipping_paid,month(c.dateofmodification) as date_month,year(c.dateofmodification) as date_year from inv_orders_details c,inv_shipstation_transactions a where a.order_id=c.order_id and a.voided=0 and date(c.dateofmodification) between  '".date('Y-m-01',strtotime('-12 Month'))."' and '".date('Y-m-d')."' group by month(c.dateofmodification),year(c.dateofmodification) order by a.ship_date");


  $amount = 0;
  $json = array();

  $data = array();
  foreach($rows_main as $row)
  {
    $data[$row['date_month'].'-'.$row['date_year']]['Shipping Charged'] = array('shipping_cost'=>round($row['shipping_cost'],2));
    $data[$row['date_month'].'-'.$row['date_year']]['Shipping Cost'] = array('shipping_cost'=>round($row['shipping_paid'],2));

  }
// print_r($data);exit;

  $labels = array();
  foreach($data as $label => $_temps)
  {
    $labels[] = $label;
    
  }

// print_r($labels);exit;
  $labels = (array_unique($labels));
$labels2 = array('Shipping Charged','Shipping Cost');



 
$rows_main = $db->func_query("select sum(c.shipping_cost) as shipping_cost,sum(a.billed_cost) as shipping_paid,month(c.dateofmodification) as date_month,year(c.dateofmodification) as date_year from inv_orders_details c,inv_shipstation_transactions a where a.order_id=c.order_id and a.voided=0 and date(c.dateofmodification) between  '".date('Y-m-01',strtotime('-12 Month'))."' and '".date('Y-m-d')."' group by month(c.dateofmodification),year(c.dateofmodification) order by a.ship_date");


  $amount = 0;
  $json = array();

  $_data = array();
  foreach($rows_main as $row)
  {
    $_data[$row['date_month'].'-'.$row['date_year']]['Shipping Cost'] = array('shipping_cost'=>round($row['shipping_cost'],2));
    $_data[$row['date_month'].'-'.$row['date_year']]['Billed Cost'] = array('shipping_cost'=>round($row['shipping_paid'],2));

  }


  $_labels = array();
  foreach($_data as $label => $_temps)
  {
    $_labels[] = $label;
    
  }

  $_labels = (array_unique($_labels));

$_labels2 = array('Shipping Cost','Billed Cost');
// print_r($rows);$rows
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script src="js/jquery.min.js"></script>
  <script src="js/chart.bundle.js"></script>

	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	<link rel="stylesheet" type="text/css" href="include/xtable.css" media="screen" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script>
		$(document).ready(function (e) {
			$('.fancybox3').fancybox({width: '90%', 'height': 800, autoCenter: true, autoSize: false});
		});

	</script>
<style>
small {
    font-size: 8px;
    font-weight: bold;
}
</style>	
</head>
<body>
	<div align="center">
		<div align="center"> 
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if ($_SESSION['message']) { ?>
		<div align="center"><br />
			<font color="red">
				<?php
				echo $_SESSION['message'];
				unset($_SESSION['message']);
				?>
				<br />
			</font>
		</div>
		<?php } ?>
		<h2>Manage <?= $pageName; ?>s</h2>
		<div align="center" class="tabMenu" >
      <input type="button" class="toogleTab" data-tab="tabManagement" value="Management">
      <input type="button" class="toogleTab" data-tab="tabShipping" value="Shipping Dashboard">
      <!-- <input type="button" class="toogleTab" data-tab="tabCustomer" value="Customer Shipments"> -->
      <input type="button" class="toogleTab" data-tab="tabVendor" value="Vendor Shipments">
      <input type="button" class="toogleTab" data-tab="tabUnmapped" value="Un-Mapped Tracking">
      <input type="button" class="toogleTab" data-tab="tabCompare" value="Compare Chart">
      <input type="button" class="toogleTab" data-tab="tabCarrier" value="Upload Carrier File">
      
      </div>

      <div class="tabHolder">
      <div id="tabManagement" class="makeTabs">
		<form action="" method="get">
			<table width="70%" cellpadding="5">
				<tr>
					<th>Order ID:</th>
					<td><input type="text" name="filter_order_id" value="<?=$_GET['filter_order_id'];?>"></td>
					<th>Tracking ID:</th>
					<td><input type="text" name="filter_transaction_id" value="<?=$_GET['filter_transaction_id'];?>"></td>
					<th>Order Date:</th>
					<td><input type="text" class="datepicker" readOnly name="filter_order_date" value="<?=$_GET['filter_order_date'];?>"></td>
					<th>Type:</th>
					<td>
					<select name="filter_orderType">
					<option value="">Please Select</option>
					<option value="1" <?=($_GET['filter_orderType']=='1'?'selected':'');?>>Mapped</option>
					<option value="0" <?=($_GET['filter_orderType']=='0'?'selected':'');?>>Not Mapped</option>

					</select>

					</td>
					<td>
					<select name="filter_voided">
					<option value="">Please Select</option>
					<option value="0" <?=($_GET['filter_voided']=='0'?'selected':'');?>>Non-Voided</option>
					<option value="1" <?=($_GET['filter_voided']=='1'?'selected':'');?>>Voided</option>

					</select>

					</td>
				</tr>
				<tr>
				<td colspan="8" align="center"><input class="button" type="submit" name="filter" value="Search"> <input type="button" id="update_payment_status" class="button button-danger" value="Update Payment"></td>
				</tr>
			</table>
		</form>
		<?php if ($pageCreateLink) { ?>
		<p><a href="<?php echo $host_path . $pageCreateLink; ?>">Add <?= $pageName; ?></a></p>
		<?php } ?>
		<table  class="xtable"   align="center" style="width:98%;margin-top: 30px;">
			<thead>
				<tr>
				<td align="center"><input type="checkbox" onchange="checkAll(this);"></td>
				<th width="10%"><a <?=($sort=='tracking_number'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=tracking_number&order_by=<?=$order_by;?>&<?=$parameters;?>">Tracking #</a> </th>
					<th width="8%"><a <?=($sort=='order_id'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=order_id&order_by=<?=$order_by;?>&<?=$parameters;?>">Order ID</a></th>
					
					
					<th width="8%"><a <?=($sort=='shipment_id'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=shipment_id&order_by=<?=$order_by;?>&<?=$parameters;?>">Shipment ID</a></th>
					<th width="6%"><a  href="#">Shipping Charge</a></th>
					<th width="6%"><a <?=($sort=='shipping_charge'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=shipping_charge&order_by=<?=$order_by;?>&<?=$parameters;?>">Shipping Cost</a></th>
					<th width="6%"><a  href="#">Diff</a></th>

					

					
					<th width="8%"><a <?=($sort=='carrier_code'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=carrier_code&order_by=<?=$order_by;?>&<?=$parameters;?>">Carrier</a></th>
					 <th width="10%"><a <?=($sort=='service_code'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=service_code&order_by=<?=$order_by;?>&<?=$parameters;?>">Service</a></th>
					 <th width="10%"><a  href="#">Requested</a></th>
					<th width="15%">Status</th>
					<th width="13%"><a <?=($sort=='ship_date'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=ship_date&order_by=<?=$order_by;?>&<?=$parameters;?>">Date</a></th>
				</tr>
			</thead>
			<tbody>
				<!-- Showing All REcord -->
				<?php
				foreach($rows as $i=> $row)
				{
					$is_missing = true;
					$missing_check = getOrder($row['order_id']);
					if($missing_check['order_id'])
					{
						$is_missing = false;
					}
					
					if($is_missing)
					{
					
						$missing_check = getOrder('E'.$row['order_id']);	
						if($missing_check['order_id'])
						{
							$row['order_id'] = 'E'.$row['order_id'];
							$is_missing = false;

						}

					}

					if($is_missing)
					{
						
						$missing_check = getOrder('RL'.$row['order_id']);
						if($missing_check['order_id'])
						{
							$row['order_id'] = 'RL'.$row['order_id'];
							$is_missing = false;
						}

					}

					if($is_missing==false)
					{
						$db->func_query("UPDATE inv_shipstation_transactions SET is_mapped=1,order_id='".$row['order_id']."' WHERE id='".$row['id']."'");
					}
						
						if(isset($_GET['filter_orderType']) and $_GET['filter_orderType']==0)
						{
							if($is_missing==false)
							{
								// continue;
							}
						}
					

					?>
					<tr>
					<?php
					$order_id = linkToOrder($row['order_id'],$host_path);
					if($is_missing)
					{
						$order_id = $row['order_id'];
						
					}
					$shipping_paid = $row['shipping_cost']+$row['insurance_cost'];
					$diff =  $missing_check['shipping_cost'] - $shipping_paid; 
					?>
					<td><input type="checkbox" name="tracking_number[]" class="checkboxes" value="<?php echo $row['tracking_number'];?>" data-value=""></td>
					<td><?=$row['tracking_number'];?></td>
					<td><?=$order_id;?></td>
					<td><?=$row['shipment_id'];?></td>
					
					<td>$<?=number_format($missing_check['shipping_cost'],2);?></td>
					<td>$<?=number_format($shipping_paid,2);?></td>
					<td><span class="tag <?=($diff>=0?'green-bg':'red-bg');?> ">$<?=number_format($diff,2);?></span></td>
					
					<td><?=stripDashes($row['carrier_code']);?></td>

					<td><?=stripDashes($row['service_code']);?></td>
					<td><?=stripDashes($missing_check['shipping_method']);?></td>
					
					<td align="center"><span class="tag <?=($is_missing?'red-bg':'blue-bg');?>"><?=($is_missing?'Missing':'Mapped');?></span>
					<?php
					if($row['voided']==1)
					{
					?>
					 <span class="tag red-bg">Voided</span>
					 <?php
					}
					?>
					 </td>
					
					<td><?=americanDate($row['ship_date']);?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
			<tfoot>
<tr>
<?php
	$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
?>
				<td colspan="11">
					<em><?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?></em>
				<div class="pagination" style="float:right">
				<?php echo $splitPage->display_links(10,$parameters);?>
				</div>
				</td>

				
			</tr>
			</tfoot>
		</table>

		<br /><br />
		<table class="footer" border="0" style="border-collapse:collapse;" width="95%" align="center" cellpadding="3">
			
		</table>
		<br />
		</div>
		<div id="tabShipping" class="makeTabs">

				<div id="" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
					  <div id="items_below_rop" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
					  <h2>Voided - Awaiting Credit</h2>
            <table width="100%" class="xtable" cellspacing="0" align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                        <th class="header"></th>
                          <th class="header">Order#</th>
                          <th class="header">Tracking#</th>
                          <th class="header">Amount</th>
                          <th class="header">Shipping Paid</th>
                          <th class="header">Voided Amount</th>
                          <th class="header">Date Filed</th>
                          <th class="header">Reason</th>
                          

                          
                      
                      </tr>
                  </thead>

                  <tbody>
                  <?php
                  $inv_query = ("SELECT * FROM inv_shipstation_transactions where payment_status='Voided - Awaiting Credit' order by ship_date desc");
                  $splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);
// print_r($splitPage);
//Getting All Messages

$rows = $db->func_query($splitPage->sql_query);


                  foreach($rows as $row)
                  {
                  	?>
                  	<tr>
                  	<td></td>
                  	<td><?php echo linkToOrder($row['order_id']);?></td>
                  	<td><?php echo $row['tracking_number'];?></td>
                  	<td><?php echo '$'.number_format($db->func_query_first_cell("SELECT shipping_amount from inv_orders where order_id='".$row['order_id']."'"),2);?></td>
                  	<td><?php echo '$'.number_format($row['shipping_cost']+$row['insurance_cost'],2);?></td>
                  	<td><?php echo '$'.number_format($row['claim_voided_amount'],2);?></td>
                  	<td><?php echo americanDate($row['date_filed']);?></td>
                  	<td><input type="text" class="reason" data-id="<?php echo $row['id'];?>" value="<?php echo $row['reason'];?>"></td>

                  	</tr>
                  	<?php
                  }

                  ?>


                  </tbody>

                  <tfoot>
<tr>
<?php
	$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
?>
				<td colspan="11">
					<em><?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?></em>
				<div class="pagination" style="float:right">
				<?php echo $splitPage->display_links(10,$parameters);?>
				</div>
				</td>

				
			</tr>
			</tfoot>

                  </table>
              </div>

              </div>

              <div id="" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
					  <div id="items_below_rop" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
					  <h2>Voided - Received Credit</h2>
            <table width="100%" class="xtable" cellspacing="0" align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                        <th class="header"></th>
                          <th class="header">Order#</th>
                          <th class="header">Tracking#</th>
                          <th class="header">Amount</th>
                          <th class="header">Shipping Paid</th>
                          <th class="header">Voided Amount</th>
                          <th class="header">Date Filed</th>
                          <th class="header">Date Completed</th>
                          <th class="header">Reason</th>
                          

                          
                      
                      </tr>
                  </thead>

                  <tbody>
                  <?php
                  $inv_query = ("SELECT * FROM inv_shipstation_transactions where payment_status='Voided - Received Credit' order by ship_date desc");
                  $splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);
// print_r($splitPage);
//Getting All Messages

$rows = $db->func_query($splitPage->sql_query);


                  foreach($rows as $row)
                  {
                  	?>
                  	<tr>
                  	<td></td>
                  	<td><?php echo linkToOrder($row['order_id']);?></td>
                  	<td><?php echo $row['tracking_number'];?></td>
                  	<td><?php echo '$'.number_format($db->func_query_first_cell("SELECT shipping_amount from inv_orders where order_id='".$row['order_id']."'"),2);?></td>
                  	<td><?php echo '$'.number_format($row['shipping_cost']+$row['insurance_cost'],2);?></td>
                  	<td><?php echo '$'.number_format($row['claim_voided_amount'],2);?></td>
                  	<td><?php echo americanDate($row['date_filed']);?></td>
                  	<td><?php echo americanDate($row['date_claimed']);?></td>
                  	<td><input type="text" id="reason" value="<?php echo $row['reason'];?>"></td>

                  	</tr>
                  	<?php
                  }

                  ?>


                  </tbody>

                  <tfoot>
<tr>
<?php
	$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
?>
				<td colspan="11">
					<em><?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?></em>
				<div class="pagination" style="float:right">
				<?php echo $splitPage->display_links(10,$parameters);?>
				</div>
				</td>

				
			</tr>
			</tfoot>

                  </table>
              </div>

              </div>


              <div id="" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
					  <div id="items_below_rop" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">

<h2>Claim Filed</h2>
            <table width="100%" class="xtable" cellspacing="0" align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                        <th class="header"></th>
                          <th class="header">Order#</th>
                          <th class="header">Tracking#</th>
                          <th class="header">Amount</th>
                          <th class="header">Shipping Paid</th>
                          <th class="header">Claim Amount</th>
                          <th class="header">RMA#</th>
                          
                          <th class="header">Reason</th>
                          

                          
                      
                      </tr>
                  </thead>

                  <tbody>
                  <?php
                  $inv_query = ("SELECT * FROM inv_shipstation_transactions where payment_status='Claim Filed' order by ship_date desc");
                  $splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);
// print_r($splitPage);
//Getting All Messages

$rows = $db->func_query($splitPage->sql_query);


                  foreach($rows as $row)
                  {
                  	?>
                  	<tr>
                  	<td></td>
                  	<td><?php echo linkToOrder($row['order_id']);?></td>
                  	<td><?php echo $row['tracking_number'];?></td>
                  	<td><?php echo '$'.number_format($db->func_query_first_cell("SELECT shipping_amount from inv_orders where order_id='".$row['order_id']."'"),2);?></td>
                  	<td><?php echo '$'.number_format($row['shipping_cost']+$row['insurance_cost'],2);?></td>
                  	<td><?php echo '$'.number_format($row['claim_voided_amount'],2);?></td>
                  	<td><?php echo $row['rma_number'];?></td>
                  	<td><input type="text" id="reason" value="<?php echo $row['reason'];?>"></td>

                  	</tr>
                  	<?php
                  }

                  ?>


                  </tbody>

                  <tfoot>
<tr>
<?php
	$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
?>
				<td colspan="11">
					<em><?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?></em>
				<div class="pagination" style="float:right">
				<?php echo $splitPage->display_links(10,$parameters);?>
				</div>
				</td>

				
			</tr>
			</tfoot>

                  </table>
              </div>

              </div>

              <div id="" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
					  <div id="items_below_rop" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
<h2>Claim Granted/Refused</h2>
            <table width="100%" class="xtable" cellspacing="0" align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                        <th class="header"></th>
                          <th class="header">Order#</th>
                          <th class="header">Tracking#</th>
                          <th class="header">Amount</th>
                          <th class="header">Shipping Paid</th>
                          <th class="header">Claim Amount</th>
                          <th class="header">RMA#</th>
                          
                          <th class="header">Reason</th>
                          

                          
                      
                      </tr>
                  </thead>

                  <tbody>
                  <?php
                  $inv_query = ("SELECT * FROM inv_shipstation_transactions where payment_status in ('Claim Granted','Claim Refused') order by ship_date desc");
                  $splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);
// print_r($splitPage);
//Getting All Messages

$rows = $db->func_query($splitPage->sql_query);


                  foreach($rows as $row)
                  {
                  	?>
                  	<tr>
                  	<td><?php echo ($row['payment_status']=='Claim Granted'?'Granted':'Refused');?></td>
                  	<td><?php echo linkToOrder($row['order_id']);?></td>
                  	<td><?php echo $row['tracking_number'];?></td>
                  	<td><?php echo '$'.number_format($db->func_query_first_cell("SELECT shipping_amount from inv_orders where order_id='".$row['order_id']."'"),2);?></td>
                  	<td><?php echo '$'.number_format($row['shipping_cost']+$row['insurance_cost'],2);?></td>
                  	<td><?php echo '$'.number_format($row['claim_voided_amount'],2);?></td>
                  	<td><?php echo $row['rma_number'];?></td>
                  	<td><input type="text" id="reason" value="<?php echo $row['reason'];?>"></td>

                  	</tr>
                  	<?php
                  }

                  ?>


                  </tbody>

                  <tfoot>
<tr>
<?php
	$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
?>
				<td colspan="11">
					<em><?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?></em>
				<div class="pagination" style="float:right">
				<?php echo $splitPage->display_links(10,$parameters);?>
				</div>
				</td>

				
			</tr>
			</tfoot>

                  </table>
              </div>

              </div>


		</div>

		<div id="tabVendor" class="makeTabs">
				<table  class="xtable"   align="center" style="width:98%;margin-top: 30px;">
						<thead>
						<tr>
								<th>Shipment Date</th>
								<th>Shipment #</th>
								<th>Tracking</th>
								<th>Carrier</th>
								<th>Shipping Cost</th>
								<th>Payor</th>
								<th>Custom Duties</th>
								<th>Payment Status</th>
						</tr>
						</thead>
						<tbody>
								<?php
									$inv_query =("SELECT * FROM inv_shipments where status<>'Pending' and carrier<>'In House' order by date_issued desc ");

									$splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);
// print_r($splitPage);
//Getting All Messages

$vendor_shipments = $db->func_query($splitPage->sql_query);
									foreach($vendor_shipments as $vendor_shipment)
									{
										?>
											<tr>
													<td><?php echo americanDate($vendor_shipment['date_issued']);?></td>
													<td><?php echo linkToShipment($vendor_shipment['id'], $host_path, $vendor_shipment['package_number']); ?></td>
													<td><?php echo $vendor_shipment['tracking_number'];?>
													<td><?php echo $vendor_shipment['carrier'];?></td>
													<td>$<?php echo number_format($vendor_shipment['shipping_cost']/$vendor_shipment['ex_rate'],2);?><br><small>(<?php echo $vendor_shipment['shipping_cost'];?>)</small></td>
													<td><select data-id="<?php echo $vendor_shipment['id'];?>" id="payor_<?php echo $vendor_shipment['id'];?>" class="payorVendor">
																<option value="vendor" <?php echo ($vendor_shipment['payor']=='vendor'?'selected':'');?>>Vendor</option>
																<option value="phonepartsusa" <?php echo ($vendor_shipment['payor']=='phonepartsusa'?'selected':'');?>>PhonePartsUSA</option>

														</select></td>
													<td>$<?php echo number_format($vendor_shipment['customs_duties'],2);?></td>
													<td>
														<select data-id="<?php echo $vendor_shipment['id'];?>" class="paymentStatusVendor" id="payment_status_<?php echo $vendor_shipment['id'];?>">
																<option value="unpaid" <?php echo ($vendor_shipment['payment_status']=='unpaid'?'selected':'');?>>Unpaid</option>
																<option value="paid" <?php echo ($vendor_shipment['payment_status']=='paid'?'selected':'');?>>Paid</option>

														</select>

													</td>
											</tr>
										<?php
									}
								?>		
						</tbody>
						<tfoot>
<tr>
<?php
	$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
?>
				<td colspan="11">
					<em><?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?></em>
				<div class="pagination" style="float:right">
				<?php echo $splitPage->display_links(10,$parameters);?>
				</div>
				</td>

				
			</tr>
						</tfoot>
				</table>
		</div>

		<div id="tabUnmapped" class="makeTabs">
				<table  class="xtable"   align="center" style="width:98%;margin-top: 30px;">
						<thead>
						<tr>
								<th>Shipment Date</th>
								<th>Shipment #</th>
								<th>Tracking</th>
								<th>Carrier</th>
								<th>Shipping Method</th>
								<th>Shipping Cost</th>
								<th>Payor</th>
								
						</tr>
						</thead>
						<tbody>
								<?php
									$inv_query =("select * from inv_shipstation_transactions where order_id='' and shipment_id<>'' and voided=0 order by ship_date desc ");

									$splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);
// print_r($splitPage);
//Getting All Messages

$rows = $db->func_query($splitPage->sql_query);
									foreach($rows as $row)
									{
										?>
											<tr>
													<td><?php echo americanDate($row['ship_date']);?></td>
													<td><input type="text" class="shipmentNo" data-id="<?php echo $row['id'];?>" id="shipment_no_<?php echo $row['id'];?>"></td>
													<td><?php echo $row['tracking_number'];?>
													<td><?php echo $row['carrier_code'];?></td>
													<td><?php echo $row['service_code'];?></td>
													<td>$<?php echo number_format($row['shipping_cost']+$row['insurance_cost'],2);?></td>
													<td><select class="payor" data-id="<?php echo $row['id'];?>" id="payor_<?php echo $row['id'];?>">
																<option value="vendor" <?php echo ($row['payor']=='vendor'?'selected':'');?>>Vendor</option>
																<option value="phonepartsusa" <?php echo ($row['payor']=='phonepartsusa'?'selected':'');?>>PhonePartsUSA</option>

														</select></td>
													
											</tr>
										<?php
									}
								?>		
						</tbody>
						<tfoot>
<tr>
<?php
	$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
?>
				<td colspan="11">
					<em><?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?></em>
				<div class="pagination" style="float:right">
				<?php echo $splitPage->display_links(10,$parameters);?>
				</div>
				</td>

				
			</tr>
						</tfoot>
				</table>
		</div>


		<div id="tabCompare" class="makeTabs">
			 <!-- <h2>Shipping Comparison Chart</h2> -->

					   <div id="container" style="width: 75%;;">
              <canvas id="canvas"></canvas>
            </div>

             <div id="container" style="width: 75%;;">
              <canvas id="canvas2"></canvas>
            </div>

		</div>
		<div id="tabCarrier" class="makeTabs">

		<form action="" method="post" enctype="multipart/form-data" style="margin-top:100px;margin-bottom:100px">
		Upload CSV: <input type="file" name="upload_csv">
		<br><br>
		<small><a href="<?php echo $host_path;?>csvfiles/shipment_carrier.csv">Download Sample File</a></small><br><br>
		<input type="submit" class="button" value="Upload">
		</form>

		<div id="" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
					  <div id="items_below_rop" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
					  <h2>Unmapped Transactions</h2>
            <table width="100%" class="xtable" cellspacing="0" align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                  <tr>
								<th>Shipment Date</th>
								<th>Shipment #</th>
								<th>Tracking</th>
								<th>Carrier</th>
								<th>Shipping Method</th>
								<th>Shipping Cost</th>
								<th>Payor</th>
								
						</tr>
                  </thead>
                  <tbody>

                  <?php
                  $rows = $db->func_query("select * from inv_shipstation_transactions where shipment_id='' and voided=0 order by ship_date desc ");
                  foreach($rows as $row)
                  {
                  	?>
                  	<tr>
                  	<td><?php echo americanDate($row['ship_date']);?></td>
													<td><input type="text" id="shipment_no_<?php echo $row['id'];?>" value="<?php echo $row['order_id'];?>"></td>
													<td><?php echo $row['tracking_number'];?>
													<td><?php echo $row['carrier_code'];?></td>
													<td>-</td>
													<td>$<?php echo number_format($row['shipping_cost']+$row['insurance_cost'],2);?></td>
													<td><select class="payor" data-id="<?php echo $row['id'];?>" id="payor_<?php echo $row['id'];?>">
																<option value="vendor" <?php echo ($row['payor']=='vendor'?'selected':'');?>>Vendor</option>
																<option value="phonepartsusa" <?php echo ($row['payor']=='phonepartsusa'?'selected':'');?>>PhonePartsUSA</option>

														</select></td>
                  	</tr>
                  	<?php
                  }
                  ?>
                  </tbody>
                  </table>
                  </div>
                  </div>

                  <div id="" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
					  <div id="items_below_rop" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;">
					  <h2>Difference Amount</h2>
            <table width="100%" class="xtable" cellspacing="0" align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                  <tr>
								<th>Shipment Date</th>
								<th>Shipment #</th>
								<th>Tracking</th>
								<th>Carrier</th>
								<th>Shipping Method</th>
								<th>Shipping Cost</th>
								<th>Billed Cost</th>
								<th>Diff</th>
								
								
						</tr>
                  </thead>
                  <tbody>

                  <?php
                $inv_query =("select * from inv_shipstation_transactions where billed_cost<>0.00 and shipping_cost<>billed_cost and voided=0 order by ship_date desc ");

									$splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);
// print_r($splitPage);
//Getting All Messages

$rows = $db->func_query($splitPage->sql_query);
									
                  foreach($rows as $row)
                  {
                  	?>
                  	<tr>
                  	<td><?php echo americanDate($row['ship_date']);?></td>
													<td><input type="text" id="shipment_no_<?php echo $row['id'];?>" value="<?php echo $row['order_id'];?>"></td>
													<td><?php echo $row['tracking_number'];?>
													<td><?php echo $row['carrier_code'];?></td>
													<td><?php echo $row['service_code'];?></td>
													<td>$<?php echo number_format($row['shipping_cost']+$row['insurance_cost'],2);?></td>
													<td>$<?php echo number_format($row['billed_cost'],2);?></td>
													<td>$<?php echo number_format(($row['shipping_cost']+$row['insurance_cost']) - $row['billed_cost'],2);?></td>
                  	</tr>
                  	<?php
                  }
                  ?>
                  </tbody>
                  </table>
                  </div>
                  </div>
                  <br>
                  <?php
                  // if(isset($_GET['debug']))
                  // {
                  ?>

                  <!-- <div id="" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;"> -->
					  <!-- <div id="items_below_rop" style="height:317px;overflow-y:scroll;overflow-x: hidden;margin-top:5px;text-align: center;"> -->
					 
					  <!-- </div> -->
					  <!-- </div> -->
					  <?php
					// }
					?>

		</div>
		</div>
	</div>
</body>

<?php
$colors = array('Shipped Charged'=>'rgb(51, 102, 204)','Shipping Cost'=>'rgb(75, 192, 192)','Billed Cost'=>'rgb(255, 99, 132)');
?>
<script>
 var barChartData = {
      labels: [<?php echo "'" . implode("','", $labels) . "'";?>],
      datasets: [<?php
 $i=1;
 foreach($labels2 as $label2)
{ 

  ?>
      {
        label: '<?php echo ($label2);?>',
        backgroundColor:'<?php echo $colors[$label2];?>',
        stack: 'Stack <?php echo $i-1;?>',
        data: [
          <?php
          $a='';
           foreach($labels as $label)
          {
            
            foreach($data[$label][$label2] as $row)
            {
              if(!$row)
              {
                $row = 0.00;
              }
              $a.=round($row,2).',';

            }

          }
          echo rtrim($a,',');
          ?>

        ]
      },
      <?php
      // if($i==5) break;;
      
      $i++;
    
    }
    ?>]

    };



     var barChartData2 = {
      labels: [<?php echo "'" . implode("','", $_labels) . "'";?>],
      datasets: [<?php
 $i=1;
 foreach($_labels2 as $label2)
{ 

  ?>
      {
        label: '<?php echo ($label2);?>',
        backgroundColor:'<?php echo $colors[$label2];?>',
        stack: 'Stack <?php echo $i-1;?>',
        data: [
          <?php
          $a='';
           foreach($_labels as $label)
          {
            
            foreach($_data[$label][$label2] as $row)
            {
              if(!$row)
              {
                $row = 0.00;
              }
              $a.=round($row,2).',';

            }

          }
          echo rtrim($a,',');
          ?>

        ]
      },
      <?php
      // if($i==5) break;;
      
      $i++;
    
    }
    ?>]

    };


window.onload = function() {
      var ctx = document.getElementById('canvas').getContext('2d');
      
      window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
          responsive: true,
          title: {
            display: true,
            text: 'Shipping Charged vs Shipping Cost'
          },
          tooltips: {
            mode: 'index',
            intersect: false
          },
          scales: {
            xAxes: [{
              stacked: true,
            }],
            yAxes: [{
              stacked: true
            }]
          }
        }
      });
var ctx2 = document.getElementById('canvas2').getContext('2d');
       window.myBar = new Chart(ctx2, {
        type: 'bar',
        data: barChartData2,
        options: {
          responsive: true,
          title: {
            display: true,
            text: 'Shipping Cost vs Billed Cost'
          },
          tooltips: {
            mode: 'index',
            intersect: false
          },
          scales: {
            xAxes: [{
              stacked: true,
            }],
            yAxes: [{
              stacked: true
            }]
          }
        }
      });
    };









 function checkAll(obj)
  {
    $(obj).parent().parent().parent().parent().find('.checkboxes').prop('checked',$(obj).is(":checked"));
  }

  $(document).on('click','#update_payment_status',function(){
  	var checkedVals = $('.checkboxes:checkbox:checked').map(function() {
    return this.value;
}).get();

  	var tracking_numbers = checkedVals.join(",");
  	console.log(tracking_numbers);
  	$.fancybox({
        
        modal: false,
        href:'<?php echo $host_path;?>/popupfiles/update_shipping_payment_status.php?tracking_numbers='+tracking_numbers,
        // width:'90%',
        width: '90%', autoCenter: true, autoSize: true,
        type: 'iframe'
    });
  })

  $(document).on('change','.payorVendor',function(){


  	$.ajax({
				url: 'shipping_management.php',
				type: 'post',
				data:{action:'update_payor_vendor',id:$(this).attr('data-id'),payor:$(this).val()},
				dataType: 'json',
				complete: function () {
					
				}
				}).always(function(json) {
				
			});


  })

  $(document).on('change','.paymentStatusVendor',function(){


  	$.ajax({
				url: 'shipping_management.php',
				type: 'post',
				data:{action:'update_payment_status_vendor',id:$(this).attr('data-id'),payment_status:$(this).val()},
				dataType: 'json',
				complete: function () {
					
				}
				}).always(function(json) {
				
			});


  })


    $(document).on('change','.shipmentNo',function(){


  	$.ajax({
				url: 'shipping_management.php',
				type: 'post',
				data:{action:'update_shipment_no',id:$(this).attr('data-id'),shipment_no:$(this).val()},
				dataType: 'json',
				complete: function () {
					
				}
				}).always(function(json) {
				
			});


  })

    $(document).on('change','.payor',function(){


  	$.ajax({
				url: 'shipping_management.php',
				type: 'post',
				data:{action:'update_payor',id:$(this).attr('data-id'),payor:$(this).val()},
				dataType: 'json',
				complete: function () {
					
				}
				}).always(function(json) {
				
			});


  })


    $(document).on('change','.reason',function(){


  	$.ajax({
				url: 'shipping_management.php',
				type: 'post',
				data:{action:'update_reason',id:$(this).attr('data-id'),reason:$(this).val()},
				dataType: 'json',
				complete: function () {
					
				}
				}).always(function(json) {
				
			});


  })

</script>