<?php
require_once("auth.php");

if($_SESSION['login_as'] != 'admin'){
	$_SESSION['message'] = 'You dont have permisson to manage group permissions.';
	echo 'You dont have permission to manage group permissions.';
	exit;
}

$permTypes = $db->func_query('SELECT * FROM inv_perm_type');
$gP = array();
foreach ($permTypes as $type) {
	$perms = $db->func_query('SELECT * FROM inv_perm WHERE perm_type_id = "'. $type['id'] .'"');
	$gP[$type['name']] = array();
	foreach ($perms as $perm) {
		 $gP[$type['name']][$perm['name']] = $perm['id'];
	}
}

if($_POST['updatePermissions']){
	$data = $_POST['data'];
	foreach($data as $group_id => $row){
		$db->func_query("DELETE FROM inv_group_perm WHERE group_id = '$group_id'");
		foreach ($row as $perm_id => $avail) {
			$array = array('group_id' => $group_id, 'perm_id' => $perm_id);
			$db->func_array2insert('inv_group_perm', $array);
		}
	}
	$_SESSION['message'] = "Permissions updated successfully";
	header("Location:groups.php");
	exit;
}

$_query = "SELECT *, id AS group_id FROM inv_groups WHERE lower(name) NOT IN ('super admin', 'programmer')";
$group_permissions = $db->func_query($_query);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Group Permissions</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<style>
		.panal{border:1px solid #ccc; border-radius:14px; margin:10px; text-align: left;}
		.panal.main{border:1px solid #cce9ff;}
		.arc_heading{padding:5px 10px; background: #ccc; border-radius:10px;}
		.arc_heading.main{background: #cce9ff;}
		.arc_heading a{ cursor: pointer;}
		.arc_data{margin:10px; display: none;}
		.form-input {width:20%; display: inline-block;}
	</style>
</head>
<body>
	<div align="center">
		<div align="center"> 
			<?php include_once 'inc/header.php';?>
		</div>

		<?php if($_SESSION['message']):?>
			<div align="center"><br />
				<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
			</div>
		<?php endif;?>

		<div align="center" style="width:90%;">

			<h2>Manage Permissions</h2>

			<form method="post" name="group" action="">
				<?php foreach($group_permissions as $group):?>
					<?php $group_id = $group['group_id']; ?>
					<div class="panal main">
						<div class="arc_heading main">
							<h2><a data-toggle="group-<?php echo $group['group_id']; ?>"><?php echo $group['name']; ?></a></h2>
						</div>

						<div class="arc_data" id="group-<?php echo $group['group_id']; ?>">

							<?php foreach ($gP as $pGName => $pG) { ?>

							<div class="panal">
								<?php $dis = ''; ?>
								<?php $all = true; ?>
								<?php $total = count($pG); ?>
								<?php $selected = 0; ?>
								<?php foreach ($pG as $pName => $p) {
									$varify = $db->func_query_first("SELECT * FROM inv_group_perm WHERE group_id = '$group_id' AND perm_id = '$p'");
									$dis .= '<div class="form-input form-input'. str_replace(' ', '', strtolower($pGName)) . $group['group_id'] . '">';
									$dis .= '<label>';
									$dis .= '<input type="checkbox" name="data['. $group['group_id'] .']['. $p .']" value="1" ' . (($varify)? 'checked="checked"': '') . '/>' . $pName;
									$dis .= '</label>';
									$dis .= '</div>';
									if ($all == true) {
										$all = (!$varify)? false: true;
									}
									$selected += ($varify)? 1: 0;
								} ?>
								<div class="arc_heading">
									<h2><a data-toggle="pgroup-<?php echo str_replace(' ', '', strtolower($pGName)) . $group['group_id']; ?>"><?php echo $pGName; ?> <small><?php echo $selected . '/' . $total; ?> Selected</small></a><input type="checkbox" data-class="form-input<?php echo str_replace(' ', '', strtolower($pGName)) . $group['group_id']; ?>" onclick="selectAllCheck(this);" <?php if( $all ):?> checked="checked" <?php endif;?> /></h2>
								</div>
								<div class="arc_data" id="pgroup-<?php echo str_replace(' ', '', strtolower($pGName)) . $group['group_id']; ?>">
									<?php echo $dis; ?>
								</div>
							</div>

							<?php } ?>

						</div>
					</div>

				<?php endforeach;?>
				<br />
				<input type="submit" name="updatePermissions" class="button" value="Update Permissions" />
			</form>
		</div>		 
	</div>
	<script>
		$('.arc_heading a').click(function() {
			$('#' + $(this).attr('data-toggle')).toggle('fast');
		});
	</script>
</body>
</html>