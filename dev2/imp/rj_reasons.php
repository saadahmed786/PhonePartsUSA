<?php
require_once("auth.php");
require_once("inc/functions.php");
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'rj_reasons_view';
$pageName = 'Rejected Reason';
$pageLink = 'rj_reasons.php';
$pageCreateLink = false;
$pageSetting = false;
$table = '`inv_rj_reasons`';

if (!$_SESSION[$perission]) {
	exit;
}

//Deleteing Record
if ($_GET['delete']) {
	$delete = $_GET['delete'];
	$db->db_exec("delete from $table where id = '" . (int) $delete . "'");
	$_SESSION['message'] = $pageName . ' Deleted';
	header("Location:" . $pageLink);
	exit;
}
if ($_GET['edit']) {
	$editId = $_GET['edit'];
	$editData = $db->func_query_first('SELECT * FROM '. $table .' WHERE `id` = "'. $editId .'"');
}
if ($_POST['update']) {
	unset($_POST['update']);
	$db->func_array2update($table, $_POST, '`id` = "'. $_POST['id'] .'"');
	echo json_encode(array('success' => 1, 'message' => $pageName . ' Updated'));
	exit;
}
if ($_POST['add']) {
	unset($_POST['add']);
	$id = $db->func_array2insert($table, $_POST);
	if ($id) {
		echo json_encode(array('success' => 1, 'message' => $pageName . ' Added'));
		exit;
	} else {
		echo json_encode(array('error' => 1, 'message' => 'Error try again'));
		exit;
	}
}

$reasons = $db->func_query("SELECT rj.*,c.name AS class_name FROM`inv_rj_reasons` rj INNER JOIN inv_classification c ON (rj.classification_id = c.id) ORDER BY c.`name` ASC , rj.`name` ASC");
$classification = $db->func_query("SELECT name as value, id FROM `inv_classification` order by sort");

if ($_POST['getReasons']) {
	echo json_encode($reasons);
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
		<h2><?= $pageName; ?></h2>
		<table align="center" border="0" width="100%" cellpadding="10" cellspacing="0" style="border:0px solid #585858;border-collapse:collapse;">
			<tr>
				<td width="50%">
					<div class="contain_list">
						<table id="list" align="center" border="0" width="100%" cellpadding="10" cellspacing="0" style="border:0px solid #585858;border-collapse:collapse;">
						</table>
					</div>
					<br>
					<div align="center">
						<input class="button" onclick="resetForm();" type="button" value="Add Reason" />
					</div>
				</td>
				<td width="50%">
					<table align="center" border="0" width="100%" cellpadding="10" cellspacing="0" style="border:0px solid #585858;border-collapse:collapse; text-align: right;">
						<tr>
							<td><?php echo createField("classification_id", "classification_id" , "select" , '', $classification, 'style="width: 100%;"');?></td>
						</tr>
						<tr>
							<td>
								<input style="width: 100%;" type="text" name="name" value="" onkeyup="checkWhiteSpace(this);" />
								<input type="hidden" name="id" />
							</td>
						</tr>
						<tr>
							<td><input class="button" id="addupdate" type="button" value="Add" /></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<script type="text/javascript">
	$(document).ready(function (){
		loadReasons();
	});
		function loadReasons () {
			$.ajax({
				url: '<?php echo $pageLink; ?>',
				type: 'POST',
				dataType: 'json',
				data: {getReasons: true},
			})
			.always(function(json) {
				var html = '<tr style="font-weight:bold"> <td> Product Class </td> <td>Reject Reason </td></tr>';
				for (var i = 0; i < json.length; i++) {
					html += '<tr class-id="' + ((json[i]['classification_id'])? json[i]['classification_id'] : '') + '" reason-id="' + json[i]['id'] + '"><td>' + json[i]['class_name'] + '</td><td class="name">' + json[i]['name'] + '</td></tr>';
				}
				$('#list').html(html);
			});
		}

		function resetForm () {
			$('input[name=name]').val('');
			$('input[name=id]').val('');
			$('#addupdate').val('Add');
			$('#classification_id').val('');
			$('.active').removeClass('active');
		}

		$('#list').on('click', 'tr', function(event) {
			event.preventDefault();
			$('.active').removeClass('active');
			$(this).addClass('active');
			$('#classification_id').val($(this).attr('class-id'));
			$('input[name=name]').val($(this).find('.name').text());
			$('input[name=id]').val($(this).attr('reason-id'));
			$('#addupdate').val('Update');
		});

		$('#addupdate').click(function(event) {
			var name = $('input[name=name]').val();
			var classification_id = $('#classification_id').val();
			var id = $('input[name=id]').val();
			if (!name) {
				return false;
			}
			var data = {}
			data['name'] = name;
			data['classification_id'] = classification_id;
			if (id) {
				data['id'] = id;
				data['update'] = true;
			} else {
				data['add'] = true;
			}

			$.ajax({
				url: '<?php echo $pageLink; ?>',
				type: 'POST',
				dataType: 'json',
				data: data,
				beforeSend: function () {
					$('#addupdate').attr('disabled', 'disabled');
				},
			})
			.always(function(json) {
				var message = '<div class="message" align="center">';
				if (json['success']) {
					resetForm();
					message += '<font color="green">'+ json['message'] +'</font>';
				}
				if (json['error']) {
					message += '<font color="red">'+ json['message'] +'</font>';
				}
				$('#addupdate').removeAttr('disabled');
				message += '</div>';
				$('.message').remove();
				$('#main').prepend(message);
				loadReasons();
			});			
			
		});
	</script>
</body>