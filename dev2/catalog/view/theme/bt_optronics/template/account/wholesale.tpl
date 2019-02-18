<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/stylesheet.css" />
<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/boss_add_cart.css" />
<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/skeleton.css" />
<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/responsive_menu.css" />
<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/responsive.css" />
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/colorbox/colorbox.css" media="screen" />
	<style type="text/css" media="screen">
		body {
			background: none;
		}
		.error {
			font-size: 12px;
		}
	</style>
</head>
<body>
	<form class="register" style="width: 500px;" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
		<h1 align="center"><?= ($account != '')? $account : $heading; ?></h1>
		<?= ($account != '')? '<p>Please Close the Popup and contiue registration.<p>' : ''; ?>
		<?php if ($account == '') { ?>
		<table width="500px">
			<tr>
				<td width="200px"><label>Email: </label></td>
				<td>
					<input type="text" name="email" value="<?php echo $email; ?>" />
					<?php if ($error_email) { ?>
					<span class="error"><?php echo $error_email; ?></span>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td>Password: </td>
				<td>
					<input type="password" name="password" value="<?php echo $password; ?>" />
					<?php if ($error_password) { ?>
					<span class="error"><?php echo $error_password; ?></span>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td>Confirm Password: </td>
				<td>
					<input type="password" name="confirm" value="<?php echo $confirm; ?>" />
					<?php if ($error_confirm) { ?>
					<span class="error"><?php echo $error_confirm; ?></span>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="submit" class="button" name="submit" value="Create Account" />
				</td>
			</tr>
		</table>
		<?php } ?>
	</form>
</body>
</html>