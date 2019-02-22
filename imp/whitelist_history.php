<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'whitelist_history';
$pageName = 'White List History';
$pageLink = 'whitelist_history.php';
$pageCreateLink = false;
$pageSetting = false;
$table = '`inv_whitelist_history`';
if (!$_SESSION[$perission]) {
	exit;
}
//Deleteing Record
/*
if ($_GET['delete']) {
	if ($_SESSION['user_id'] == 0) {
		$delete = $_GET['delete'];
		$db->db_exec("delete from $table where id = '" . (int) $delete . "'");
		$_SESSION['message'] = $pageName . ' Deleted';
		header("Location:" . $pageLink);
		exit;
	}
}
*/

// Getting Page information
if (isset($_GET['page'])) {
	$page = intval($_GET['page']);
}

if ($page < 1) {
	$page = 1;
}

$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
//Setting PAgination Limits
$max_page_links = 10;
$num_rows = 30;
$start = ($page - 1) * $num_rows;

// Search Setup
$where = '';

if ($_GET['submit'] == 'Search') {
	unset($_GET['submit']);
	$filter = array();
	foreach ($_GET as $key => $value) {

		if ($value) {
			if ($key == 'user') {
				$filter[] = '`'. $key .'` = "'. $value .'"';
			} else {
				$filter[] = 'LCASE('. $key .') LIKE LCASE("'. $value .'")';
			}
		}
	}
	if ($filter) {
		$where = ' WHERE ' . implode(' AND ', $filter);
	}
}

$orderby = ' ORDER BY `date_added` DESC';

//Writing query 
$inv_query = 'SELECT * FROM '. $table .' ' . $where . $orderby;

//Using Split Page Class to make pagination
$splitPage = new splitPageResults($db, $inv_query, $num_rows, $pageLink, $page);

//Getting All Messages
$whitelists = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type="text/javascript" src="js/jquery.min.js"></script>

	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script>
		$(document).ready(function (e) {
			$('.fancybox3').fancybox({width: '90%', 'height': 800, autoCenter: true, autoSize: false});
		});

	</script>
	
</head>
<body>
	<div align="center">
		<div align="center" style="display:none;"> 
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
		<h2>Manage <?= $pageName; ?></h2>
		<form action="" method="get">
			<table>
				<tr>
					<td><input type="text" name="details" value="<?= (isset($_GET['details']))? $_GET['details']: '';?>" placeholder="Order id / Email" /></td>
					<td>
						<select name="user">
							<option value="">--Select User--</option>
							<option value="0">Admin</option>
							<?php $users = $db->func_query('SELECT `id`, `name` FROM inv_users');?>
							<?php foreach ($users as $user) { ?>
							<option value="<?= $user['id']; ?>"><?= ucfirst($user['name']); ?></option>
							<?php } ?>
						</select>
					</td>
					<td><input class="button" type="submit" name="submit" value="Search"/></td>
					<?php if ($pageSetting) { ?>
					<td><a href="<?= $pageSetting; ?>" class="fancybox3 fancybox.iframe button" style="">Settings</a></td>
					<?php } ?>
				</tr>
			</table>
		</form>
		<?php if ($pageCreateLink) { ?>
		<p><a href="<?php echo $host_path . $pageCreateLink; ?>">Add <?= $pageName; ?></a></p>
		<?php } ?>
		<table width="90%" cellpadding="10" border="1"  align="center">
			<thead>
				<tr>
					<th width="2%">#</th>
					<th width="15%">Type</th>
					<th width="15%">User</th>
					<th width="30%">Order/Email</th>
					<th width="15%">Reason</th>
					<th width="15%">Date Added</th>
				</tr>
			</thead>
			<tbody>
				<!-- Showing All REcord -->
				<?php foreach ($whitelists as $i => $row) { ?>
				<?php $row['reason'] = $db->func_query_first_cell('SELECT name from inv_whitelist_reasons where id = "'. $row['reason'] .'"');?>
				<?php $row['details'] = ($row['type'] == 'order')? linkToOrder($row['details']): linkToProfile($row['details']); ?>
				<tr>
					<td><?= ($i) + 1; ?></td>
					<td><?= $row['type']; ?></td>
					<td><?= getAdmin($row['user']); ?></td>
					<td><?= $row['details']; ?></td>
					<td><?= ($row['reason'])? $row['reason']: 'N/A'; ?></td>
					<td><?= americanDate($row['date_added']); ?></td>
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