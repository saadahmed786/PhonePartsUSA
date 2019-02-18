<?php
require_once("auth.php");
require_once("inc/functions.php");
$table = "`inv_canned_message`";
if ($_GET['cat']) {
	$catId = $_GET['cat'];
} else {
	$catId = 1;
}
if ($_GET['edit']) {
	$canned_message_id = $_GET['edit'];
	$messageData = $db->func_query_first('SELECT * FROM '. $table .' WHERE `canned_message_id` = "'. $canned_message_id .'"');
	$catId = $messageData['catagory'];
	if (!$messageData) {
		$_SESSION['message'] = "Message Not Found";
		header("Location:canned_messages_manage.php");
		exit;
	}
}
if ($_POST['update']) {
	if ($_POST != 'Canned') {
		$db->func_array2update($table, array('type' => 'Canned'), '`type` = "'. $_POST['type'] .'" AND `catagory` = "'. $catId .'"');
	}
	unset($_POST['update']);
	$_POST['date_modified'] = date('Y-m-d H:i:s');

	if ($_FILES['image']['tmp_name']) {

		$name = explode(".", $_FILES['image']['name']);

		$destination = $path . "files/canned_" . $canned_message_id . ".png";
		$file = $_FILES['image']['tmp_name'];

		move_uploaded_file($file, $destination);
	}

	$db->func_array2update($table, $_POST, '`canned_message_id` = "'. $canned_message_id .'"');
	$_SESSION['message'] = "Message Updated";
	header("Location:canned_messages_manage.php");
	exit;
}
if ($_POST['add']) {
	if ($_POST != 'Canned') {
		$db->func_array2update($table, array('type' => 'Canned'), '`type` = "'. $_POST['type'] .'" AND `catagory` = "'. $catId .'"');
	}
	unset($_POST['add']);
	$_POST['added_by'] = $_SESSION['user_id'];
	$_POST['date_added'] = date('Y-m-d H:i:s');
	$_POST['date_modified'] = date('Y-m-d H:i:s');
	$id = $db->func_array2insert($table, $_POST);
	if ($id) {

		if ($_FILES['image']['tmp_name']) {

			$name = explode(".", $_FILES['image']['name']);

			$destination = $path . "files/canned_" . $id . ".png";
			$file = $_FILES['image']['tmp_name'];

			move_uploaded_file($file, $destination);
		}

		$_SESSION['message'] = "Message Added";
		header("Location:canned_messages_manage.php");
		exit;
	} else {
		$_SESSION['message'] = "Error";
	}
}
$shortcodes = array();
foreach ($db->func_query("SELECT `name` FROM `inv_canned_shortcode` WHERE `catagory` = '$catId' AND `type` = 'code'") as $data) {
	$shortcodes[] = '{{' . $data['name'] . '}}';
}
$types = array();
foreach ($db->func_query("SELECT `name` FROM `inv_canned_shortcode` WHERE `catagory` = '$catId' AND `type` = 'type'") as $data) {
	$types[] = $data['name'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Canned Messages | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="js/jquery.min.js"></script>

	<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

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
			<h2><?= (isset($canned_message_id)) ? 'Edit': 'Add';?> Canned Messages</h2>
			<br />
			<br />
			<table align="center" border="1" width="60%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr>
					<td>Name</td>
					<td><input required="" type="text" onkeyup="checkWhiteSpace(this);" style="width:100%;" name="name" value="<?= ($canned_message_id) ? $messageData['name']: '';?>" /></td>
				</tr>
				<tr>
					<td>Email Subject</td>
					<td><input required="" type="text" onkeyup="checkWhiteSpace(this);" style="width:100%;" name="subject" value="<?= ($canned_message_id) ? $messageData['subject']: '';?>" /></td>
					<input type="hidden" name="catagory" value="<?= $catId;?>" />
				</tr>
				<tr>
					<td>Email Header</td>
					<td><input type="text" onkeyup="checkWhiteSpace(this);" required="" style="width:100%;" name="title" value="<?= ($canned_message_id) ? $messageData['title']: '';?>" /></td>
				</tr>
				<tr>
					<td>Short Codes</td>
					<td>
						<select id="shortcodes">
							<option value="">Select to Add into Message </option>
							<?php foreach ($shortcodes as $code) { ?>
							<option value="<?= $code ?>"><?= ucfirst(str_replace('_', ' ', $code)); ?></option>
							<?php } ?>
						</select>
						<a class="fancybox3 fancybox.iframe button" href="shortcodes.php?category=<?= $catId; ?>">Short Codes</a>
						<script type="text/javascript">
							$('#shortcodes').change(function () {
								//var shortcode = '<span contenteditable="false">' + $(this).val() + '</span>';
								var shortcode = CKEDITOR.dom.element.createFromHtml('<span contenteditable="false">' + $(this).val() + '</span>');
								if (shortcode) {
									//CKEDITOR.instances['message'].insertText(shortcode);
									CKEDITOR.instances.message.insertElement(shortcode);
									$('#shortcodes').val('');
								}
							});
						</script>
					</td>
				</tr>
				<tr>
					<td>Canned Message</td>
					<td>
						<textarea name="message" id="message" rows="10" cols="80">
							<?= ($canned_message_id) ? $messageData['message']: '';?>
						</textarea>
						<script>
							CKEDITOR.replace( 'message' );
						</script>
					</td>
				</tr>
				<tr>
					<td>
						Canned Image <small>150 x 150</small>
					</td>
					<td>
						<input type="file" name="image" id="file" accept="image/x-png"/>
						<script type="text/javascript" charset="utf-8" async defer>
							var _URL = window.URL || window.webkitURL;

							$("#file").change(function(e) {
								var file, img;


								if ((file = this.files[0])) {
									img = new Image();
									img.onload = function() {
										if (this.width != 150 && this.height != 150) {
											alert('Invalid Image 150x150 is allowed');
											$("#file").val('');
										}
									};
									img.onerror = function() {
										alert( "not a valid file: " + file.type);
									};
									img.src = _URL.createObjectURL(file);


								}

							});
						</script>
						<?php $src = $path .'files/canned_' . $canned_message_id . ".png"; ?>
						<?= (file_exists($src))? '<img src="'. $host_path .'files/canned_' . $canned_message_id . '.png?'.time().'" />': ''; ?>
					</td>
				</tr>
				<tr>
					<td>
						Type
					</td>
					<td>
						<select name="type">
							<?php foreach ($types as $type) { ?>
							<option value="<?= $type ?>" <?= ($messageData['type'] == $type)? 'selected="selected"': '';?>><?= $type; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input class="button" type="submit" name="<?= (isset($canned_message_id)) ? 'update': 'add';?>" value="<?= (isset($canned_message_id)) ? 'Update': 'Submit';?>" /></td>
				</tr>
			</table>
		</form>
	</div>
</body>