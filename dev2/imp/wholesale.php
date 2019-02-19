<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$table = '`oc_wholesale_account`';
$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
//Deleteing Record
if ($_GET['delete']) {
	$delete = $_GET['delete'];

	$user = $db->func_query_first("SELECT * FROM $table WHERE id = '" . (int)$delete . "'");

	$log = 'Whole Sale Request was deleted ' . linkToProfile(strtolower($user['email']));
	actionLog($log);

	$db->db_exec("delete from $table where id = '" . (int)$delete . "'");
	header("Location:wholesale.php");
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
if ($_GET['keyword']) {
	$keyword = trim($_GET['keyword']);
	$where = " WHERE LCASE(`email`) LIKE LCASE('%$keyword%') OR LCASE(`personal_email`) LIKE LCASE('%$keyword%')";
}
//Writing query 
$inv_query = "SELECT * FROM ". $table . $where. "order by date_added desc";

//Using Split Page Class to make pagination
$splitPage = new splitPageResults($db, $inv_query, $num_rows, "wholesale.php", $page);

//Getting All Messages
$accounts = $db->func_query($splitPage->sql_query);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Business Application Submissions | PhonePartsUSA</title>
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
		<h2>Business Application Submissions</h2>
		<form action="" method="get">
			<table>
				<tr>
					<td><input type="text" name="keyword"/></td>
					<td><input class="button" type="submit" name="submit" value="Search"/></td>
					<td><a class="button" href="wholesale_setting.php">Setting</td>
				</tr>
			</table>
		</form>
		<table width="90%" cellpadding="10" border="1"  align="center">
			<thead>
				<tr>
					<th width="3%">
						#
					</th>
					<th width="25%">
						Name
					</th>
					<th width="25%">
						Personal Email
					</th>
					<th width="25%">
						Email
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
				<?php foreach ($accounts as $i => $account) { ?>
				<tr>
					<td>
						<?= ($i + 1); ?>
					</td>
					<td>
						<?= $account['first_name'] . ' ' . $account['last_name']; ?>
					</td>
					<td>
						<?= linkToProfile($account['personal_email']); ?>
					</td>
					<td>
						<?= $account['email']; ?>
					</td>
					<td>
						<?= americanDate($account['date_added']); ?>
					</td>
					<td>
						<a href="<?= $host_path . 'wholesale_request.php?id=' . $account['id'];?>">View</a>
					</td>
					<td>
						<a href="<?= $host_path . 'wholesale.php?delete=' . $account['id'];?>">Delete</a>
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