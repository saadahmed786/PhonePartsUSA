<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
//Deleteing Record
/*
if ($_GET['delete']) {
	$delete = $_GET['delete'];
	$db->db_exec("delete from inv_canned_message where canned_message_id = '" . (int) $delete . "'");
	header("Location:vouchers_manage.php");
	exit;
}
*/

// Getting Page information
page_permission("vouchers");
if (isset($_GET['page'])) {
	$page = intval($_GET['page']);
}

if ($page < 1) {
	$page = 1;
}
//Setting PAgination Limits
$max_page_links = 10;
$num_rows = 30;
$start = ($page - 1) * $num_rows;
//Setting Search prameters
$where = '';
$filter = array();

$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);

if ($_GET['code']) {
	$code = trim($_GET['code']);
	$filter[] = "LCASE(`ov`.`code`) LIKE LCASE('%$code%')";
}
if ($_GET['to']) {
	$filterto = trim($_GET['to']);
	$filter[] = "LCASE(`ov`.`to_email`) LIKE LCASE('%$filterto%')";
}
if ($_GET['status']  != '') {
	$filterstatus = $_GET['status'];
	$filter[] = "`ov`.`status` = '$filterstatus'";
}
if(!isset($_GET['date_start']))
{
	//$_GET['date_start'] = date('Y-m-d',strtotime('-1 day'));
	//$_GET['date_end'] = date('Y-m-d');
}


if(isset($_GET['date_start']) && $_GET['date_start']!='')
{
	$filter[] = "date(`ov`.`date_added`) between '".$_GET['date_start']."' and '".$_GET['date_end']."'";	
}



if ($filter) {
	$where = 'WHERE ' . implode( ' AND ', $filter);
}
$orderby = ' ORDER BY date_added desc';
if ($_GET['orderby']) {
	$orderby = ' ORDER BY ' . $_GET['orderby'];
}
//Writing query 
$inv_query = 'SELECT 
`ov`.*
FROM
`oc_voucher` AS `ov`

 ' .$where . $orderby;

 if(isset($_GET['debug']))
 {
 	echo $inv_query;
 }
//Using Split Page Class to make pagination
$splitPage = new splitPageResults($db, $inv_query, $num_rows, "vouchers_manage.php", $page);

//Getting All Messages
$vouchers = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Vouchers | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script>
		$(document).ready(function(e) {
			$('.fancybox3').fancybox({ width: '90%' , autoCenter : true , autoSize : true });
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
		<h2>Manage Vouchers</h2>
		<form action="" method="get">
			<table>
				<tr>
					<td><input type="text" name="code" value="<?= (isset($_GET['code']))? $_GET['code']: '';?>" placeholder="Code" /></td>
					<td><input type="text" name="to" value="<?= (isset($_GET['to']))? $_GET['to']: '';?>" placeholder="Email" /></td>
					<td><input type="date" name="date_start" class="datepicker" value="<?php echo $_GET['date_start'];?>"></td>
					<td><input type="date" name="date_end" class="datepicker" value="<?php echo $_GET['date_end'];?>"></td>
					<td>
						<select name="status">
							<?php $options = array('1' => 'Enabled', '0' => 'Disabled'); ?>
							<option value="">Status</option>
							<?php foreach ($options as $key => $value) { ?>
							<option value="<?= $key; ?>" <?= ($_GET['status'] == $key && $_GET['status'] != '')? 'selected="selected"': ''; ?>><?= $value; ?></option>
							<?php } ?>
						</select>
					</td>
					<td><input class="button" type="submit" name="submit" value="Search"/></td>
					<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php if ($_SESSION['vouchers_update'] || $_SESSION['login_as'] == 'admin') { ?><a href="<?php echo $host_path ?>voucher_reasons.php" class="button" >Edit Reasons</a>
		<?php } ?>
					</td>
				</tr>
			</table>
		</form>
		<p><a href="<?php echo $host_path ?>vouchers_create.php">Create Voucher</a></p><br>

		<?php

		if(isset($_GET['debug']))
		{
		
			$total_issued = $db->func_query_first_cell("SELECT SUM(amount) from oc_voucher where status=1 and date(date_added)='".$_GET['date_start']."'");

			$total_used = $db->func_query_first_cell("SELECT SUM(b.amount) from oc_voucher_history b,oc_voucher a where a.voucher_id=b.voucher_id and date(b.date_added)='".$_GET['date_start']."'");
			$total_used = $total_used*(-1);
			?>
			<table width="20%" border="1" cellpadding="8" cellspacing="0" style="">
									<tr style="font-weight: bold">
											<td>Total Issued</td>
											<td><?php echo '$'.number_format($total_issued,2);?></td>
											<td align="center">-</td>
											
											
									</tr>

									<tr style="font-weight: bold">
											<td>Total Used</td>
											<td align="center">-</td>
											<td><?php echo '$'.number_format($total_used,2);?></td>
											
											
									</tr>

									<tr style="font-weight: bold">
											<td>Balance</td>
											<td colspan="2"><?php echo '$'.number_format($total_issued-$total_used,2);?></td>
											
											
											
									</tr>
									</table>
									<br>
			<?php
		}
		?>
		
		<table width="90%" cellpadding="10" border="1" style="border-collapse: collapse;border:1px solid #ddd"  align="center">
			<thead>
				<tr>
					<th width="2%">#</th>
					<th width="10%">Code</th>
					<th width="25%">To</th>
					<th width="9%"><a href="vouchers_manage.php?orderby=<?= ($_GET['orderby'] == 'amount desc')? 'amount asc': 'amount desc'; ?>">Amount</a></th>
					<th width="9%">Balance</th>
					<th width="10%">Name</th>
					<th width="10%">User</th>
					<th width="8%">Source</th>
					<th width="8%">PPUSA</th>
					<th width="7%"><a href="vouchers_manage.php?orderby=<?= ($_GET['orderby'] == 'status desc')? 'status asc': 'status desc'; ?>">Status</a></th>
					<th width="12%"><a href="vouchers_manage.php?orderby=<?= ($_GET['orderby'] == 'date_added asc')? 'date_added desc': 'date_added asc'; ?>">Date Added</a></th>
					<th width="8%">Action</th>
				</tr>
			</thead>
			<tbody>
				<!-- Showing All REcord -->
				<?php foreach ($vouchers as $i => $voucher) { ?>
				<?php
				$voucher_detail = $db->func_query_first("SELECT * FROM inv_voucher_details WHERE voucher_id='".$voucher['voucher_id']."' ORDER BY id DESC");
				$user_name='admin';
				if ($voucher['user_id']) {
					$user_name = get_username($voucher['user_id']);
				}
				if($voucher_detail['user_id'])
				{
				$user_name = get_username($voucher_detail['user_id']);
			}
			//	$user_name = get_username($voucher_detail['user_id']);
			
				if($voucher_detail['oc_user_id'])
				{
				$user_name = $db->func_query_first_cell("SELECT username FROM oc_user WHERE user_id='".$voucher_detail['oc_user_id']."'");
				}
				
				?>
				<?php $balance = ((float) $voucher['amount']) + ((float) $db->func_query_first_cell("SELECT SUM(amount) FROM oc_voucher_history WHERE voucher_id='".$voucher['voucher_id']."'")); ?>
				<tr>
					<td><?= ($i) + 1 ?></td>
					<td>
						<?= $voucher['code'];?>
					</td>
					<td>
						<?= linkToProfile($voucher['to_email'], $host_path);?>
					</td>
					<td>
						$<?= number_format($voucher['amount'], 2);?>
					</td>
					<td>
						$<?= number_format($balance, 2);?>
					</td>
					<td>
						<?= $voucher['from_name'];?>
					</td>
					<td>
						<?= $user_name;?>
					</td>
					<td>
						<?= ($voucher_detail['is_lbb'])? 'BuyBack': '';?>
						<?= ($voucher_detail['is_rma'])? 'RMA': '';?>
						<?= ($voucher_detail['is_order_cancellation'])? 'Cancellation': '';?>
						<?= ($voucher_detail['is_pos'])? 'POS': '';?>
						<?= ($voucher_detail['is_manual'])? 'Order(Item Removed)': '';
						if ($voucher_detail['is_manual']) {
							$sku = explode(',', $voucher_detail['item_detail']);?>
						<br>
						<?php echo $sku[0];
						}
						 ?>
					</td>
					<td>
						<?= ($voucher_detail['is_pos'])? 'YES': 'N/A';?>
					</td>
					<td>
						<?= ($voucher['status'] == '1')? 'Enabled': 'Disabled';?>
					</td>
					<td>
						<?= americanDate($voucher['date_added']);?>
					</td>
					<td>
						<a href="<?= $host_path . 'vouchers_create.php?reason_id='.$voucher['reason_id'].'&edit=' . $voucher['voucher_id'] . '&status='.$voucher['status'];?>">Edit</a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<br /><br />
		<table class="footer" border="0" style="border-collapse:collapse;" width="95%" align="center" cellpadding="3">
			<tr>
				<td colspan="7" align="left">
					<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
				</td>

				<td colspan="6" align="right">
					<?php echo $splitPage->display_links(10,$parameters);?>
				</td>
			</tr>
		</table>
		<br />
	</div>
</body>