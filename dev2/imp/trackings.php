<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'tracking_report';
$pageName = 'Order Trackings';
$pageLink = 'trackings.php';
$pageCreateLink = false;
$pageSetting = false;
page_permission($perission);
$table = '`inv_tracker`';

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
	$where[] = " s.order_id LIKE '%".$db->func_escape_string($_GET['filter_order_id'])."%'";
	$parameters.='&filter_order_id='.$_GET['filter_order_id'];
	}
	if($_GET['filter_order_date'])
	{
	$where[] = " DATE(s.ship_date) = '".date('Y-m-d',strtotime($_GET['filter_order_date']))."'";
	$parameters.='&filter_order_date='.$_GET['filter_order_date'];
	}


	if($_GET['filter_transaction_id'])
	{
	$where[] = " ts.tracker_id LIKE '%".$db->func_escape_string($_GET['filter_transaction_id'])."%'";
	$parameters.='&filter_transaction_id='.$_GET['filter_transaction_id'];
	}

	
	if(isset($_GET['filter_status']) and $_GET['filter_status']!='' )
	{
		$where[] = " ts.status = '".$db->func_escape_string($_GET['filter_status'])."'";
		$parameters.='&filter_status='.$_GET['filter_status'];
	}

	
	
}

if(!$where)
{
	$where[] = " 1 = 1 ";
}
$where = implode(" AND ", $where);

$sort = $_GET['sort'];
$order_by = $_GET['order_by'];

$sort_array  = array('s.ship_date','ts.tracker_id','ts.status','s.order_id');

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


if($order_by=='asc') $order_by='desc'; else $order_by = 'asc';


//Writing query 
$inv_query="select distinct s.*,ts.status,ts.message,ts.tracker_id from inv_shipstation_transactions s,inv_tracker t, inv_tracker_status ts
where ts.tracker_id=t.tracker_id and t.tracking_code=s.tracking_number and s.confirmation='delivery' and $where  group by ts.tracker_id order by ts.id desc
";

//echo $inv_query;

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
					<th>Transaction ID:</th>
					<td><input type="text" name="filter_transaction_id" value="<?=$_GET['filter_transaction_id'];?>"></td>
					<th>Order Date:</th>
					<td><input type="text" class="datepicker" readOnly name="filter_order_date" value="<?=$_GET['filter_order_date'];?>"></td>
					<th>Type:</th>
					<td>
					<?php
					$delivery_statuses = $db->func_query("SELECT DISTINCT status FROM inv_tracker_status")
					?>
					<select name="filter_status">
					<option value="">Please Select</option>
					<?php
					foreach($delivery_statuses as $status)
					{
						?>
						<option value="<?=$status['status'];?>" <?=($_GET['filter_status']==$status['status']?'selected':'');?>><?=$status['status'];?></option>
						<?php
					}
					?>

					</select>

					</td>
					
				</tr>
				<tr>
				<td colspan="7" align="center"><input class="button" type="submit" name="filter" value="Search"></td>
				</tr>
			</table>
		</form>
		<?php if ($pageCreateLink) { ?>
		<p><a href="<?php echo $host_path . $pageCreateLink; ?>">Add <?= $pageName; ?></a></p>
		<?php } ?>
		<table  class="xtable"   align="center" style="width:98%;margin-top: 30px;">
			<thead>
				<tr>
				<th width="7%"><a <?=($sort=='s.ship_date'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=s.ship_date&order_by=<?=$order_by;?>&<?=$parameters;?>">Date Shipped</a> </th>
					<th width="20%"><a <?=($sort=='s.order_id'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=s.order_id&order_by=<?=$order_by;?>&<?=$parameters;?>">Order ID</a></th>
					<th width="15%"><a <?=($sort=='ts.tracker_id'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=ts.tracker_id&order_by=<?=$order_by;?>&<?=$parameters;?>">Tracking ID</a></th>
					
					
					<th width="8%"><a href="#">Carrier</a></th>
					<th width="10%"><a  href="#">Shipping Method</a></th>
					<th width="40%"><a <?=($sort=='ts.status'?'class="'.$order_by.'"':'');?> href="<?=$pageLink;?>?sort=ts.status&order_by=<?=$order_by;?>&<?=$parameters;?>">Last Tracking</a></th>
					
				</tr>
			</thead>
			<tbody>
				<!-- Showing All REcord -->
				<?php
				foreach($rows as $i=> $row)
				{
					
					?>
					<tr>
					<?php
					$order_id = linkToOrder($row['order_id'],$host_path);
					 
					?>
					<td><?=date('m/d/Y',strtotime($row['ship_date']));?></td>
					<td><?=$order_id;?></td>
					<td><?=$row['tracker_id'];?></td>
					<td><?=stripDashes($row['carrier_code']);?></td>

					<td><?=stripDashes($row['service_code']);?></td>
					<?php
					if($_GET['filter_status']!='')
					{
						?>
					
					<td>(<?=$row['status'];?>) <?=($row['message']);?></td>
					<?php
				}
				else
				{
					$state=$db->func_query_first("SELECT status,message FROM inv_tracker_status WHERE tracker_id='".$row['tracker_id']."' order by id desc");
					$row['status'] = $state['status'];
					$row['message'] = $state['message'];
					?>
<td>(<?=$row['status'];?>) <?=($row['message']);?></td>
					<?php
				}
					?>
					
					
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
				<td colspan="6">
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