<?php
require_once("auth.php");
require_once("inc/functions.php");
require_once("auth.php");
require_once("inc/functions.php");
$perission = false;
$pageName = 'Send Email';
$pageLink = 'sendemail.php';
$pageCreateLink = false;
$pageSetting = false;
$table = '`inv_customer_group_privilege`';

if (!$_SESSION[$perission] && $permission) {
	exit;
}
$cat_id = (int)$_GET['catid'];
$orderID = $_GET['order_id'];
$emailInfo = $_SESSION['email_info'][$orderID];
$adminInfo = array('name' => $_SESSION['user_name'], 'company_info' => $_SESSION['company_info'] );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?> | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script>

		function allowFloat (t) {
			var input = $(t).val();
			var valid = input.substring(0, input.length - 1);
			if (isNaN(input)) {
				$(t).val(valid);
			}
		}
		function checkWhiteSpace (t) {
			if ($(t).val() == ' ') {
				$(t).val('');
			}
		}

	</script>
	<style type="text/css">
		.contain_list {
			height: 300px;
			overflow-y: scroll;
			border: 2px solid #000;
		}
		.active {
			background-color: #ccc;
		}
	</style>
</head>
<body>
	<div id="main" align="center">
		<div align="center" style="display:none">
			<?php include_once 'inc/header.php';?>
		</div>
		<?php if($_SESSION['message']) { ?>
		<div align="center">
			<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
		</div>
		<?php } ?>
		<div align="center">
			<h3>Send Email</h3>
			<table width="70%" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">
				<form method="post" action="email_invoice.php?order_id=<?php echo $orderID; ?>&action=email" id="email_form">
					<tr>
						<td>Canned Message:</td>
						<td>
							<?php $canned_messages = $db->func_query('SELECT * FROM `inv_canned_message` WHERE `catagory` = "'. $cat_id .'" && type in ("Canned", "Invoice Email")'); ?>
							<select name="canned_id" id="canned_message">
								<option value=""> --- Custom --- </option>
								<?php foreach ($canned_messages as $canned_message) { ?>
								<option value="<?= $canned_message['canned_message_id']; ?>"><?= $canned_message['name']; ?></option>
								<?php } ?>                        
							</select>
							<input type="hidden" name="total_formatted" value="<?= $emailInfo['total_formatted']; ?>"/>
						</td>
					</tr>
					<tr>
						<td>Title</td>
						<td><input type="text" name="title" id="canned_title" value=""/></td>
					</tr>
					<!-- <tr>
						<td>To</td>
						<td><input type="text" name="email" readonly="" id="email" value="<?php echo $email; ?>"/></td>
					</tr> -->
					<tr>
						<td>Subject</td>
						<td><input type="text" name="subject" id="canned_subject" value=""/></td>
					</tr>
					<tr>
						<td>Message:</td>
						<td><textarea name="comment" id="comment" class="comment-box" cols="40" rows="8" style="width: 99%"></textarea></td>
						<script>
							CKEDITOR.replace( 'comment' );
						</script>
					</tr>
					<tr>
						<td></td>
						<td><label class="addsd" for="signature_check"><input type="checkbox" id="signature_check" /> Add Signature</label><label class="addsd" for="disclaimer_check"><input type="checkbox" id="disclaimer_check" /> Add Disclaimer</label></td>
					</tr>
					<tr>
						<td></td>
						<td><input class="button" name="sendemail" value="Send Email" type="submit"></td>
					</tr>
				</form>
			</table>
			<ul style="display:none;">
				<textarea id="disclaimer"><div contenteditable="false"><?= $db->func_query_first_cell('SELECT `signature` FROM `inv_signatures` WHERE `type` = 1'); ?></div></textarea>
				<?php $src = $path .'files/sign_' . $_SESSION['user_id'] . ".png"; ?>
				<textarea id="signature"><div contenteditable="false"><?= shortCodeReplace($adminInfo, $db->func_query_first_cell('SELECT `signature` FROM `inv_signatures` WHERE `user_id` = "'. $_SESSION['user_id'] .'" AND type = 0')); ?> <?= (file_exists($src))? '<img src="'. $host_path .'files/sign_' . $_SESSION['user_id'] . '.png?'. time() .'" />': ''; ?></div></textarea>
				<?php foreach ($canned_messages as $canned_message) { ?>
				<textarea id="canned_<?= $canned_message['canned_message_id']; ?>"><?= shortCodeReplace($emailInfo, $canned_message['message']); ?></textarea>
				<li id="title_<?= $canned_message['canned_message_id']; ?>"><?= shortCodeReplace($emailInfo, $canned_message['title']); ?></li>
				<li id="subject_<?= $canned_message['canned_message_id']; ?>"><?= shortCodeReplace($emailInfo, $canned_message['subject']); ?></li>
				<?php } ?>
			</ul>

			<script type="text/javascript">
				var canned_messages = [<?php echo implode(',', $messages)?>];
				var msgs = {};
				$(function() {
					$('#email_form').submit(function () {
						if ($('#canned_title').val() == '' || $('#canned_subject').val() == '') {
							alert('Please Enter Your Message');
							return false;
						}
					});
					$('#canned_message').change(function() {
						var id = $(this).val();
						var message = '';
						if(id > 0) {
							message = $('#canned_' + id).text();
						}
						message = message + '<div id="customeData">';
						if ($('#signature_check').is(':checked')) {
							message = message + $('#signature').text();
						}
						if ($('#disclaimer_check').is(':checked')) {
							message = message + $('#disclaimer').text();
						}
						message = message + '</div>';
						$('#canned_title').val($('#title_' + id).text());
						$('#canned_subject').val($('#subject_' + id).text());
						CKEDITOR.instances.comment.setData(message);
					});
					$('.addsd').click(function() {
						if (!CKEDITOR.instances.comment.document.getById('customeData')) {
							message = CKEDITOR.instances.comment.getData() + '<div id="customeData"></div>';
							CKEDITOR.instances.comment.setData(message);
						}
						var message = '';
						if ($('#signature_check').is(':checked')) {
							message = message + $('#signature').text();
						}
						if ($('#disclaimer_check').is(':checked')) {
							message = message + $('#disclaimer').text();
						}
						CKEDITOR.instances.comment.document.getById('customeData').setHtml(message);

					});
					$('#canned_message').keyup(function() {
						$(this).change();
					});
				});
			</script>
		</div>
	</div>
</body>
</html>