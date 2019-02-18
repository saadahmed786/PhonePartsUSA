<?php
require_once("auth.php");
require_once("inc/functions.php");
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'customers';
$pageName = 'Privileges Type';
$pageLink = 'addedit_privileges_types.php';
$pageCreateLink = false;
$pageSetting = false;
$table = '`inv_privilege_type`';

if (!$_SESSION[$perission]) {
	exit;
}

//Deleteing Record
if ($_GET['delete']) {
	$delete = $_GET['delete'];
	$db->db_exec("delete from $table where `" . $_GET['column'] . "` = '" . (int) $delete . "'");
	echo json_encode(array('success' => 1, 'message' => 'Deleted'));
	exit;
}
if ($_POST['update']) {
	$column = $_POST['column'];
	unset($_POST['update'], $_POST['column']);
	$db->func_array2update($table, $_POST, '`'. $column .'` = "'. $_POST[$column] .'"');
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

$rows = $db->func_query("SELECT privilege_type_id as id, name, type FROM $table order by privilege_type_id DESC");
$types = array(
	array('id' => 'single', 'name' => 'Single'),
	array('id' => 'multiple', 'name' => 'Multiple'),
	);
if ($_POST['getRows']) {
	echo json_encode($rows);
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= $pageName; ?>s | PhonePartsUSA</title>
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
		<h2><?= $pageName; ?>s</h2>
		<table align="center" border="0" width="100%" cellpadding="10" cellspacing="0" style="border:0px solid #585858;border-collapse:collapse;">
			<tr>
				<td width="50%">
					<div class="contain_list">
						<table id="list" align="center" border="0" width="100%" cellpadding="10" cellspacing="0" style="border:0px solid #585858;border-collapse:collapse;">
							<?php foreach ($rows as $k => $row) { ?>
							<tr type-id="<?php echo $row['type']; ?>" data-id="<?php echo $row['id']; ?>">
								<td><?php echo $row['name']; ?></td>
							</tr>
							<?php } ?>
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
							<td>
							<select id="type" column-name="type" style="width: 100%;">
								<option value="">Select Type</option>
							<?php foreach ($types as $value) { ?>
								<option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
							<?php } ?>
							</select>
							</td>
						</tr>
						<tr>
							<td>
								<input style="width: 100%;" type="text" name="name" value="" onkeyup="checkWhiteSpace(this);" />
								<input type="hidden" column-name="privilege_type_id" name="id" />
							</td>
						</tr>
						<tr>
							<td><input class="button" id="delete" style="display: none;" type="button" value="Delete" /> <input class="button" id="addupdate" type="button" value="Add" /></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<script type="text/javascript">
		function loadReasons () {
			$.ajax({
				url: '<?php echo $pageLink; ?>',
				type: 'POST',
				dataType: 'json',
				data: {getRows: true},
			})
			.always(function(json) {
				var html = '';
				for (var i = 0; i < json.length; i++) {
					html += '<tr type-id="' + json[i]['type'] + '" data-id="' + json[i]['id'] + '"><td>' + json[i]['name'] + '</td></tr>';
				}
				$('#list').html(html);
			});
		}

		function resetForm () {
			$('input[name=name]').val('');
			$('input[name=id]').val('');
			$('#addupdate').val('Add');
			$('#type').val('');
			$('.active').removeClass('active');
		}

		$('#list').on('click', 'tr', function(event) {
			event.preventDefault();
			$('.active').removeClass('active');
			$(this).addClass('active');
			$('#type').val($(this).attr('type-id'));
			$('input[name=name]').val($(this).find('td').text());
			$('input[name=id]').val($(this).attr('data-id'));
			$('#addupdate').val('Update');
			$('#delete').show();
		});

		$('#addupdate').click(function(event) {
			$('.message').remove();
			var name = $('input[name=name]').val();
			var typeContainer = $('#type');
			var type = typeContainer.val();
			var idContainer = $('input[name=id]');
			var id = idContainer.val();
			if (!name || !type) {
				var message = '<div class="message" align="center">';
				message += '<font color="red">Please fill in data first</font>';
				message += '</div>';
				$('#main').prepend(message);
				return false;
			}
			var data = {}
			data['name'] = name;
			data[typeContainer.attr('column-name')] = type;
			if (id) {
				data[idContainer.attr('column-name')] = id;
				data['column'] = idContainer.attr('column-name');
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
				$('#main').prepend(message);
				loadReasons();
			});			
			
		});

		$('#delete').click(function(event) {
			var idContainer = $('input[name=id]');
			var id = idContainer.val();

			$.ajax({
				url: '<?php echo $pageLink; ?>?delete='+ id + '&column='+idContainer.attr('column-name'),
				type: 'GET',
				dataType: 'json',
				beforeSend: function () {
					$('#delete').attr('disabled', 'disabled');
				},
			})
			.always(function(json) {
				var message = '<div class="message" align="center">';
				if (json['success']) {
					resetForm();
					message += '<font color="green">'+ json['message'] +'</font>';
				}
				$('#delete').removeAttr('disabled').hide();
				message += '</div>';
				$('.message').remove();
				$('#main').prepend(message);
				$('tr[data-id="'+ id +'"]').remove();
			});			
			
		});
	</script>
</body>
</html>