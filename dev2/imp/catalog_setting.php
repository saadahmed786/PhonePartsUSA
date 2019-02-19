<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'catalog_setting';
$pageName = 'Catalog Setting';
$pageLink = 'catalog_setting.php';
$pageSetting = false;
$table = '`catalog_setting`';
$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
if(!$_SESSION[$perission]){
	echo 'You dont have permission to manage '. $pageName .'.';
	exit;
}

$data = $db->func_query("SELECT * FROM $table");
$reqData =  array();
foreach ($data as $value) {
	$reqData[$value['setting_name']] = $value['setting_value'];
}


if ($_POST['add']) {
	$reqData = $_POST;
	unset($_POST['add']);

	foreach ($_POST as $setting_name => $setting_value) {
		$check = $db->func_query_first("SELECT * FROM $table WHERE setting_name = '$setting_name'");
		$array = array('setting_value' => $setting_value, 'setting_name' => $setting_name);
		if ($check) {
			$db->func_array2update($table, $array, "setting_name = '$setting_name'");
		} else {
			$db->func_array2insert($table, $array);
		}
	}

	$_SESSION['message'] = 'Catalog Setting Updated';

	header("Location:" . $pageLink);
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script>
		$(document).ready(function(e) {
			$('.fancybox3').fancybox({ width: '90%' , autoCenter : true , autoSize : true });
		});

		function checkWhiteSpace (t) {
			if ($(t).val() == ' ') {
				$(t).val('');
			}
		}

		function checkYoutubeId (t) {
			$.ajax({
				url: 'product.php',
				type: 'POST',
				dataType: 'json',
				data: {checkYoutubeId: $(t).val()},
				beforeSend: function () {
					$('input.button').attr('disabled', 'disabled');
				}
			})
			.always(function(json) {
				if (json['error']) {
					alert(json['msg']);
					$(t).val('');
				}
				$('input.button').removeAttr('disabled');
			});
		}

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
		<form action="" method="post" enctype="multipart/form-data">
			<h2>Edit <?= $pageName; ?></h2>
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>Youtube Link</td>
					<td>
						<input type="text" name="youtube_link" value="<?= $reqData['youtube_link']; ?>" onchange="checkYoutubeId(this);" onkeyup="checkWhiteSpace(this);" />
					</td>
				</tr>
				<tr>
					<td>Instructions</td>
					<td>
						<textarea name="instructions" id="instructions" cols="40" rows="8" style="width: 99%"><?= $reqData['instructions']; ?></textarea>
						<script>
							CKEDITOR.replace( 'instructions' );
						</script>
					</td>
				</tr>
				<tr>
					<td>Hover NEW</td>
					<td>
						<input type="text" name="hover_text_new" value="<?= $reqData['hover_text_new']; ?>" onkeyup="checkWhiteSpace(this);" />
					</td>
				</tr>
				<tr>
					<td>Hover A</td>
					<td>
						<input type="text" name="hover_text_a" value="<?= $reqData['hover_text_a']; ?>" onkeyup="checkWhiteSpace(this);" />
					</td>
				</tr>
				<tr>
					<td>Hover B</td>
					<td>
						<input type="text" name="hover_text_b" value="<?= $reqData['hover_text_b']; ?>" onkeyup="checkWhiteSpace(this);" />
					</td>
				</tr>
				<tr>
					<td>Hover C</td>
					<td>
						<input type="text" name="hover_text_c" value="<?= $reqData['hover_text_c']; ?>" onkeyup="checkWhiteSpace(this);" />
					</td>
				</tr>
				<tr>
					<td>Image size (200x200)</td>
					<td>
						<input type="text" name="image_size" value="<?= $reqData['image_size']; ?>" onkeyup="checkWhiteSpace(this);" />
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input class="button" type="submit" name="add" value="Submit" /><a class="button" style="margin-left:10px;" href="<?= $pageLink; ?>">Back</a></td>
				</tr>
			</table>
		</form>
	</div>
</body>