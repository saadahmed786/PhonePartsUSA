<?php
require_once("auth.php");

$gP = array(
	'POS' => array(
		'POS User' => 'pos_user', 
		'POS Manaer' => 'pos_manager', 
		'View Returned Orders' => 'pos_view_returned_items', 
		'Edit Order' => 'pos_can_edit_order', 
		'Generate RMA' => 'pos_can_process_rma', 
		'Issue Store Credit' => 'pos_can_issue_store_credit'
		),
	'Open Cart' => array(
		'View Returned Orders' => 'view_returned_items', 
		'Edit Order' => 'can_edit_order', 
		'Update Bulk Orders' => 'update_b_order', 
		'Create Order' => 'can_create_order', 
		'Process RMA' => 'can_process_rma', 
		'Issue Store Credit' => 'can_issue_store_credit'
		),
	);

if($_SESSION['login_as'] != 'admin'){	
	echo 'You dont have permission to manage group permissions.';
	exit;
}

if($_POST['updatePermissions']){
	$data = $_POST['data'];
	foreach($data as $group_id => $row){
		$updateRow = array();
		foreach ($gP as $key => $gPr) {
			foreach ($gPr as $gPrm) {
				$updateRow[$gPrm] = ($_POST['data'][$group_id][$gPrm]) ? 1 : 0;
			}
		}
		$db->func_array2update("oc_user",$updateRow," user_id = '$group_id' ");
	}
	
	$_SESSION['message'] = "Permissions updated successfully";
	header("Location:oc_users.php");
	exit;
}

$_query = "SELECT *, CONCAT(`firstname`, ' ', `lastname`) AS `name` FROM oc_user ";
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
					
					<div class="panal main">
						<div class="arc_heading main">
							<h2><a data-toggle="group-<?php echo $group['user_id']; ?>"><?php echo $group['name']; ?> (<?php echo $group['username']; ?>)</a></h2>
						</div>

						<div class="arc_data" id="group-<?php echo $group['user_id']; ?>">

							<?php foreach ($gP as $pGName => $pG) { ?>

							<div class="panal">
								<?php $dis = ''; ?>
								<?php $all = true; ?>
								<?php $total = count($pG); ?>
								<?php $selected = 0; ?>
								<?php foreach ($pG as $pName => $p) {

									$dis .= '<div class="form-input form-input'. str_replace(' ', '', strtolower($pGName)) . $group['user_id'] . '">';
									$dis .= '<label>';
									$dis .= '<input type="checkbox" name="data['. $group['user_id'] .']['. $p .']" value="1" ' . (($group[$p])? 'checked="checked"': '') . '/>' . $pName;
									$dis .= '</label>';
									$dis .= '</div>';
									if ($all == true) {
										$all = (!$group[$p])? false: true;
									}
									$selected += ($group[$p])? 1: 0;
								} ?>
								<div class="arc_heading">
									<h2><a data-toggle="pgroup-<?php echo str_replace(' ', '', strtolower($pGName)) . $group['user_id']; ?>"><?php echo $pGName; ?> <small><?php echo $selected . '/' . $total; ?> Selected</small></a><input type="checkbox" data-class="form-input<?php echo str_replace(' ', '', strtolower($pGName)) . $group['user_id']; ?>" onclick="selectAllCheck(this);" <?php if( $all ):?> checked="checked" <?php endif;?> /></h2>
								</div>
								<div class="arc_data" id="pgroup-<?php echo str_replace(' ', '', strtolower($pGName)) . $group['user_id']; ?>">
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