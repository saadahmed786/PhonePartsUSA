<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';

$perission = false;
$pageName = 'Admin User';
$pageLink = 'admin_users.php';
$pageCreateLink = 'admin_users_edit.php';
$pageSetting = false;
$table = '`admin`';
$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
if(!$_SESSION['super_admin']){
	echo 'You dont have permission to manage users.';
	exit;
}
//Deleteing Record
if ($_GET['delete']) {
	if ($_SESSION['super_admin']) {
		$delete = $_GET['delete'];
		$user = $db->func_query_first('SELECT * FROM ' . $table . ' WHERE id = "'. (int) $delete .'"');
		$log = 'Admin User "' . $user['name'] . '" Deleted';
		actionLog($log);

		$db->db_exec("delete from $table where id = '" . (int) $delete . "'");
		$_SESSION['message'] = $pageName . ' Deleted';
		header("Location:" . $pageLink);
		exit;
	}
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

// Search Setup
$where = '';

if ($_GET['submit'] == 'Search') {
	unset($_GET['submit']);
	$filter = array();
	foreach ($_GET as $key => $value) {
		if ($value) {
			$filter[] = 'LCASE('. str_replace('`_`', '`.`', $key) .') LIKE LCASE("'. $value .'")';
		}
	}
	if ($filter) {
		$where = ' WHERE ' . implode(' AND ', $filter);
	}
}
$orderby = ' ORDER BY `a`.`id` ASC';

//Writing query 
$inv_query = 'SELECT * FROM '. $table .' AS `a` ' . $where . $orderby;

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
			<table>
				<tr>
					<td><input type="text" name="`a`_`name`" value="<?= (isset($_GET['`a`_`name`']))? $_GET['`a`_`name`']: '';?>" placeholder="Order id" /></td>
					<td><input type="text" name="`a`_`email`" value="<?= (isset($_GET['`a`_`email`']))? $_GET['`a`_`email`']: '';?>" placeholder="email" /></td>
					<td><input class="button" type="submit" name="submit" value="Search"/></td>
					<?php if ($pageSetting) { ?>
					<td><a href="<?= $pageSetting; ?>" class="fancybox3 fancybox.iframe button" style="">Settings</a></td>
					<?php } ?>
				</tr>
			</table>
		</form>
		<p><a href="<?php echo $host_path . $pageCreateLink; ?>">Add <?= $pageName; ?></a></p>
		<table width="90%" cellpadding="10" border="1"  align="center">
			<thead>
				<tr>
					<th width="2%">ID</th>
					<th>Name</th>
					<th>Username</th>
					<th>Password</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<!-- Showing All REcord -->
				<?php foreach ($rows as $i => $row) { ?>
				<tr>
					<td><?php echo $row['id']; ?></td>
					<td><?php echo $row['name']; ?></td>
					<td><?php echo $row['email']; ?></td>
					<td><?php echo $row['password']; ?></td>
					<td><?php echo $row['status']; ?></td>
					<td><?php echo '<a href="'. $pageLink .'?delete='. $row['id'] .'">Delete</a>' . ' <a href="'. $pageCreateLink .'?id='. $row['id'] .'">Edit</a>';?></td>
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