<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
//Writing query 
$inv_query = "select * from inv_users where group_id = 1 order by lower(name)";
//Using Split Page Class to make pagination

//Getting All Messages
$accounts = $db->func_query($inv_query);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Vendor Accounts</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	
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
		<h2>Vendor Accounts</h2>
		<form action="" method="get">
			<table>
				<tr>
					<td><input type="text" name="keyword"/></td>
					<td><input class="button" type="submit" name="submit" value="Search"/></td>
					
				</tr>
			</table>
		</form>
		<br>
		<table width="50%" cellpadding="5" border="1"  align="center">
			<thead>
				<tr>
					<th width="3%">
						#
					</th>
					<th width="25%">
						Name
					</th>
					<th width="25%">
						Email
					</th>
					
					<!-- <th width="12%">
						Balance
					</th> -->
					<th colspan="2" width="10%">
						Action
					</th>
				</tr>
			</thead>
			<tbody>
				<!-- Showing All REcord -->
				<?php foreach ($accounts as $i => $account) { ?>
				<?php
				$shipments = $db->func_query("SELECT id,ex_rate,shipping_cost FROM inv_shipments WHERE vendor='".$account['id']."'");
$balance = 0.00;
				foreach($shipments as $index => $shipment){
	$SQL = "select   sum(qty_received * unit_price) as received_total 
			from inv_shipment_items where shipment_id = '".$shipment['id']."'";
	$shipment['extra'] = $db->func_query_first($SQL); 
	


	$balance = $balance + ($shipment['extra']['received_total'] / $shipment['ex_rate'])+ ($shipment['shipping_cost'] / $shipment['ex_rate']);

}
$credit = $db->func_query_first_cell("SELECT SUM(credit) FROM inv_accounts WHERE account_id='V".$account['id']."'");

$balance = $balance - $credit;

				?>
				<tr>
					<td>
						<?= ($i + 1); ?>
					</td>
					<td>
						<?= $account['name']; ?>
					</td>
					<td>
						<?= $account['email']; ?>
					</td>
					<!-- <td>
						$<?= number_format($balance,2); ?>
					</td> -->
										<td>
						<a href="<?= $host_path . 'vendor_account_detail.php?account_id=' . $account['id'];?>">View Detail</a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<br /><br />
		
	</div>
</body>