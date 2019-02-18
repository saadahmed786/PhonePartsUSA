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

echo $inv_query;

//Using Split Page Class to make pagination
$splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);

//Getting All Messages

$rows = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type="text/javascript" src="js/jquery.min.js"></script>

	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	<link rel="stylesheet" type="text/css" href="include/xtable.css" media="screen" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script>
		$(document).ready(function (e) {
			$('.fancybox3').fancybox({width: '90%', 'height': 800, autoCenter: true, autoSize: false});
		});

	</script>
	
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
				<td colspan="8" align="center"><input class="button" type="submit" name="filter" value="Search"></td>
				</tr>
			</table>
		</form>
		<?php if ($pageCreateLink) { ?>
		<p><a href="<?php echo $host_path . $pageCreateLink; ?>">Add <?= $pageName; ?></a></p>
		<?php } ?>
		<table  class="xtable"   align="center" style="width:98%;margin-top: 30px;">
			<thead>
				<tr>
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
</body>