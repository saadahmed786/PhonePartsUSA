<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
//Deleteing Record

if ($_GET['delete']) {
	$delete = $_GET['delete'];
	$db->db_exec("delete from inv_canned_message where canned_message_id = '" . (int) $delete . "'");
	header("Location:canned_messages_manage.php");
	exit;
}
// Getting Page information
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

if ($_GET['keyword']) {
	$keyword = $_GET['keyword'];
	$filter[] = "LCASE(`subject`) LIKE LCASE('%$keyword%') OR LCASE(`title`) LIKE LCASE('%$keyword%')";
}
if ($_GET['type']) {
	$filtertype = $_GET['type'];
	$filter[] = "`type` = '$filtertype'";
}

if ($filter) {
	$where = 'WHERE ' . implode( ' AND ', $filter);
}
//Writing query 
$inv_query = "SELECT * FROM `inv_canned_message` $where";

//Using Split Page Class to make pagination
$splitPage = new splitPageResults($db, $inv_query, $num_rows, "canned_messages_manage.php", $page);

//Getting All Messages
$canned_messages = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Canned Messages | PhonePartsUSA</title>
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
		<h2>Manage Canned Messages</h2>
		<form action="" method="get">
			<table>
				<tr>
					<td><input type="text" name="keyword" value="<?= (isset($_GET['keyword']))? $_GET['keyword']: '';?>" /></td>
					<td>
						<select name="type">
							<?php 
							$types = array();
							foreach ($db->func_query("SELECT `name` FROM `inv_canned_shortcode` WHERE `type` = 'type'") as $data) {
								$types[] = $data['name'];
							}
							?>
							<?php foreach ($types as $type) { ?>
							<option value="<?= $type ?>" <?= ($_GET['type'] == $type)? 'selected="selected"': '';?>><?= $type; ?></option>
							<?php } ?>
						</select>
					</td>
					<td><input class="button" type="submit" name="submit" value="Search"/></td>
				</tr>
			</table>
		</form>
		<p><a href="<?php echo $host_path ?>canned_messages_create.php?cat=1">Add Message for Orders</a></p>
		<p><a href="<?php echo $host_path ?>canned_messages_create.php?cat=2">Add Message for RMA</a></p>
		<p><a href="<?php echo $host_path ?>canned_messages_create.php?cat=3">Add Message for LBB</a></p>
		<p><a href="<?php echo $host_path ?>canned_messages_create.php?cat=4">Add Message for Customer Profile</a></p>
		<table width="90%" cellpadding="10" border="1"  align="center">
			<thead>
				<tr>
					<th width="3%">
						#
					</th>
					<th width="10%">
						Name
					</th>
					<th width="20%">
						Header
					</th>
					<th width="25%">
						Subject
					</th>
					<th width="12%">
						Type
					</th>
					<th width="8%">
						Catagory
					</th>
					<th width="12%">
						Date
					</th>
					<th colspan="2" width="10%">
						Action
					</th>
				</tr>
			</thead>
			<tbody>
				<!-- Showing All REcord -->
				<?php foreach ($canned_messages as $i => $message) { ?>
				<?php
				switch ($message['catagory']) {
					case '1':
					$message['catagory'] = 'Order';
					break;
					case '2':
					$message['catagory'] = 'RMA';
					break;
					case '3':
					$message['catagory'] = 'LBB';
					break;
					case '4':
					$message['catagory'] = 'Customer Profile';
					break;
				}
				?>
				<tr>
					<td>
						<?= ($i + 1); ?>
					</td>
					<td>
						<?= $message['name']; ?>
					</td>
					<td>
						<?= $message['title']; ?>
					</td>
					<td>
						<?= $message['subject']; ?>
					</td>
					<td>
						<?= $message['type']; ?>
					</td>
					<td>
						<?= $message['catagory']; ?>
					</td>
					<td>
						<?= americanDate($message['date_added']); ?>
					</td>
					<td>
						<a href="<?= $host_path . 'canned_messages_create.php?edit=' . $message['canned_message_id'];?>">Edit</a>
					</td>
					<td>
						<a href="<?= $host_path . 'canned_messages_manage.php?delete=' . $message['canned_message_id'];?>">Delete</a>
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