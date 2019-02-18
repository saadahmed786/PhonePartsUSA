<?php
//==============================================================================
// Form Builder v154.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================
?>

<?php echo $header; ?>
<style type="text/css">
	.green {
		color: #080 !important;
	}
	.red {
		color: #B00 !important;
	}
	.status {
		cursor: pointer;
		font-size: 24px;
	}
	.list tbody tr:nth-child(odd) td {
		background: #FFF;
	}
	.list tbody tr:nth-child(even) td {
		background: #E8F4FF;
	}
	.list thead td {
		height: 24px;
	}
	.list .center {
		width: 50px;
	}
</style>
<div id="content">
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	<div class="box">
		<div class="heading">
			<h1 style="padding: 10px 2px 0"><img src="view/image/<?php echo $type; ?>.png" alt="" style="vertical-align: middle" /> <?php echo $heading_title; ?></h1>
			<div class="buttons">
				<a class="button" href="index.php?route=<?php echo $type . '/' . $name . '/edit&token=' . $token; ?>"><?php echo $button_create_new_form; ?></a>
			</div>
		</div>
		<div class="content">
			<table class="list">
			<thead>
				<tr>
					<td class="center"><?php echo $column_status; ?></td>
					<td class="left"><?php echo $column_name; ?></td>
					<td class="center"><?php echo $column_edit; ?></td>
					<td class="center"><?php echo $column_report; ?></td>
					<td class="center"><?php echo $column_copy; ?></td>
					<td class="center"><?php echo $column_delete; ?></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($forms as $form) { ?>
					<tr id="<?php echo $form['form_id']; ?>">
						<td onclick="toggleEntry($(this))" class="center status <?php echo ($form['status']) ? 'green">&#10004;' : 'red">&#10008;'; ?></td>
						<td class="left"><a href="index.php?route=<?php echo $type . '/' . $name . '/edit&form_id=' . $form['form_id'] . '&token=' . $token; ?>"><?php $form_name = unserialize($form['name']); echo $form_name[$this->config->get('config_admin_language')]; ?></a></td>
						<td class="center"><a href="index.php?route=<?php echo $type . '/' . $name . '/edit&form_id=' . $form['form_id'] . '&token=' . $token; ?>"><img src="view/image/review.png" /></a></td>
						<td class="center"><a href="index.php?route=<?php echo $type . '/' . $name . '/report&form_id=' . $form['form_id'] . '&token=' . $token; ?>"><img src="view/image/report.png" /></a></td>
						<td class="center"><a onclick="copyRow($(this))"><img src="view/image/category.png" /></a></td>
						<td class="center"><a onclick="deleteRow($(this))"><img src="view/image/error.png" /></a></td>
					</tr>
				<?php } ?>
				<?php if (empty($form)) { ?>
					<tr><td colspan="6" class="center"><a class="button" href="index.php?route=<?php echo $type . '/' . $name . '/edit&token=' . $token; ?>"><?php echo $button_create_new_form; ?></a></td></tr>
				<?php } ?>
			</tbody>
			</table>
			<?php echo $copyright; ?>
		</div>
	</div>
</div>
<script type="text/javascript"><!--
	var table = 'form';
	
	function toggleEntry(element) {
		var enabled = element.hasClass('green');
		element.removeClass('green red').html('<img src="view/image/loading.gif" />');
		$.ajax({
			type: 'POST',
			url: 'index.php?route=<?php echo $type; ?>/<?php echo $name; ?>/toggleEntry&token=<?php echo $token; ?>',
			data: {table: table, id: element.parent().attr('id'), new_value: (enabled ? 0 : 1)},
			success: function(data) {
				if (!data) {
					alert('<?php echo $standard_error; ?>');
					enabled = !enabled;
				}
				element.addClass(enabled ? 'red' : 'green').html(enabled ? '&#10008;' : '&#10004;');
			}
		});
	}
	
	function copyRow(element) {
		element.html('<img src="view/image/loading.gif" />').removeAttr('onclick');
		$.ajax({
			type: 'POST',
			url: 'index.php?route=<?php echo $type; ?>/<?php echo $name; ?>/copyRow&token=<?php echo $token; ?>',
			data: {table: table, id: element.parent().parent().attr('id')},
			success: function(data) {
				if (!data) {
					alert('<?php echo $standard_error; ?>');
					element.html('<img src="view/image/category.png" />').attr('onclick', 'copyRow($(this))');
				} else {
					location = location;
				}
			}
		});
	}
	
	function deleteRow(element) {
		var bg = element.parent().parent().find('td').css('background');
		element.parent().parent().find('td').css('background', '#FEE');
		if (confirm('<?php echo $text_confirm; ?>')) {
			element.html('<img src="view/image/loading.gif" />').removeAttr('onclick');
			$.ajax({
				type: 'POST',
				url: 'index.php?route=<?php echo $type; ?>/<?php echo $name; ?>/deleteRow&token=<?php echo $token; ?>',
				data: {table: table, id: element.parent().parent().attr('id')},
				success: function(data) {
					if (!data) {
						alert('<?php echo $standard_error; ?>');
						element.parent().parent().find('td').css('background', bg);
						element.html('<img src="view/image/error.png" />').attr('onclick', 'deleteRow($(this))');
					} else {
						element.parent().parent().remove();
					}
				}
			});
		} else {
			element.parent().parent().find('td').css('background', bg);			
		}
	}
//--></script>
<?php echo $footer; ?>