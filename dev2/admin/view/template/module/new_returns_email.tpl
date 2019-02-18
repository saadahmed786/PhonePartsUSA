<?php
//==============================================================================
// New Returns E-mail v155.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================
?>

<?php echo $header; ?>
<style type="text/css">
	#shortcodes td {
		border: none;
		color: #666;
		font-size: 11px;
		line-height: 1.5;
		vertical-align: top;
	}
	textarea {
		font-family: monospace;
		height: 200px;
		width: 600px;
	}
	input[type="text"] {
		width: 600px;
	}
	.ui-dialog {
		position: fixed;
	}
</style>
<div id="content">
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	<?php if ($error_warning) { ?><div class="warning"><?php echo $error_warning; ?></div><?php } ?>
	<?php if ($success) { ?><div class="success"><?php echo $success; ?></div><?php } ?>
	<div class="box">
		<div class="heading">
			<h1 style="padding: 10px 2px 0"><img src="view/image/<?php echo $type; ?>.png" alt="" style="vertical-align: middle" /> <?php echo $heading_title; ?></h1>
			<div class="buttons">
				<a class="button" onclick="$('#form').submit()"><?php echo $button_save_exit; ?></a>
				<a class="button" onclick="save()"><?php echo $button_save_keep_editing; ?></a>
				<a class="button" onclick="location = '<?php echo $exit; ?>'"><?php echo $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form action="" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td><?php echo $entry_status; ?></td>
						<td><input type="button" style="float: right" onclick="toggleEditors()" value="<?php echo $button_toggle_ckeditors; ?>" />
							<select name="<?php echo $name; ?>_status">
								<?php $status = (isset(${$name.'_status'})) ? ${$name.'_status'} : 1; ?>
								<option value="1" <?php if ($status) echo 'selected="selected"'; ?>><?php echo $text_enabled; ?></option>
								<option value="0" <?php if (!$status) echo 'selected="selected"'; ?>><?php echo $text_disabled; ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2"><table id="shortcodes"><?php echo $text_shortcodes; ?></table></td>
					</tr>
					<tr>
						<td><?php echo $entry_admin_email; ?></td>
						<td><input type="text" name="<?php echo $name; ?>_admin_email" value="<?php echo (isset(${$name.'_admin_email'})) ? ${$name.'_admin_email'} : $this->config->get('config_email'); ?>" /></td>
					</tr>
					<tr>
						<td><?php echo $entry_admin_subject; ?></td>
						<td><table>
								<?php foreach ($languages as $language) { ?>
									<tr>
										<td><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></td>
										<td><input type="text" name="<?php echo $name; ?>_admin_subject_<?php echo $language['code']; ?>" value="<?php echo (!empty(${$name.'_admin_subject_'.$language['code']})) ? ${$name.'_admin_subject_'.$language['code']} : $text_admin_subject; ?>" /></td>
									</tr>
								<?php } ?>
							</table>
						</td>
					</tr>
					<tr>
						<td><?php echo $entry_admin_message; ?></td>
						<td><table>
								<?php foreach ($languages as $language) { ?>
									<tr>
										<td><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></td>
										<td><textarea name="<?php echo $name; ?>_admin_message_<?php echo $language['code']; ?>"><?php echo (!empty(${$name.'_admin_message_'.$language['code']})) ? ${$name.'_admin_message_'.$language['code']} : $text_admin_message; ?></textarea></td>
									</tr>
								<?php } ?>
							</table>
						</td>
					</tr>
					<tr>
						<td><?php echo $entry_email_customer; ?></td>
						<td><select name="<?php echo $name; ?>_customer_email">
								<?php $customer_email = (isset(${$name.'_customer_email'})) ? ${$name.'_customer_email'} : 1; ?>
								<option value="1" <?php if ($customer_email) echo 'selected="selected"'; ?>><?php echo $text_yes; ?></option>
								<option value="0" <?php if (!$customer_email) echo 'selected="selected"'; ?>><?php echo $text_no; ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $entry_customer_subject; ?></td>
						<td><table>
								<?php foreach ($languages as $language) { ?>
									<tr>
										<td><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></td>
										<td><input type="text" name="<?php echo $name; ?>_customer_subject_<?php echo $language['code']; ?>" value="<?php echo (!empty(${$name.'_customer_subject_'.$language['code']})) ? ${$name.'_customer_subject_'.$language['code']} : $text_customer_subject; ?>" /></td>
									</tr>
								<?php } ?>
							</table>
						</td>
					</tr>
					<tr>
						<td><?php echo $entry_customer_message; ?></td>
						<td><table>
								<?php foreach ($languages as $language) { ?>
									<tr>
										<td><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></td>
										<td><textarea name="<?php echo $name; ?>_customer_message_<?php echo $language['code']; ?>"><?php echo (!empty(${$name.'_customer_message_'.$language['code']})) ? ${$name.'_customer_message_'.$language['code']} : $text_customer_message; ?></textarea></td>
									</tr>
								<?php } ?>
							</table>
						</td>
					</tr>
				</table>
			</form>
			<?php echo $copyright; ?>
		</div>
	</div>
</div>
<script type="text/javascript" src="view/javascript/jquery/ui/external/jquery.cookie.js"></script>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript"><!--
	function save() {
		$('<div></div>').dialog({
			title: '<?php echo $text_saving; ?>',
			closeOnEscape: false,
			draggable: false,
			modal: true,
			resizable: false,
			open: function(event, ui) {
				$('.ui-dialog').css('padding', '0px');
				$('.ui-dialog-content').hide();
				$('.ui-dialog-titlebar-close').hide();
			}
		}).dialog('open');
		
		for (name in CKEDITOR.instances) CKEDITOR.instances[name].updateElement();
		
		$.ajax({
			type: 'POST',
			url: 'index.php?route=<?php echo $type; ?>/<?php echo $name; ?>/save&token=<?php echo $token; ?>',
			data: $('#form :input'),
			success: function(success) {
				if (success) {
					$('.ui-dialog-content').dialog('option', 'title', '<?php echo $text_saved; ?>');
					setTimeout(function(){
						$('.ui-dialog-content').dialog('close');
					}, 1000);
				} else {
					$('.ui-dialog-content').dialog('option', 'title',  '<?php echo $standard_error; ?>');
					setTimeout(function(){
						$('.ui-dialog-content').dialog('close');
					}, 2000);
				}
			}
		});
	}
	
	$(document).ready(function(){
		if ($.cookie('ckeditor') != 'disabled') {
			toggleEditors();
		}
	});
	
	function toggleEditors() {
		if ($.isEmptyObject(CKEDITOR.instances)) {
			$.cookie('ckeditor', 'enabled', {expires: 365});
			CKEDITOR.replaceAll(function(textarea, config) {
				config.height = '200px';
				config.width = '600px';
				config.resize_enabled = true;
				config.filebrowserBrowseUrl = 'index.php?route=common/filemanager&token=<?php echo $token; ?>';
				config.filebrowserImageBrowseUrl = 'index.php?route=common/filemanager&token=<?php echo $token; ?>';
				config.filebrowserFlashBrowseUrl = 'index.php?route=common/filemanager&token=<?php echo $token; ?>';
				config.filebrowserUploadUrl = 'index.php?route=common/filemanager&token=<?php echo $token; ?>';
				config.filebrowserImageUploadUrl = 'index.php?route=common/filemanager&token=<?php echo $token; ?>';
				config.filebrowserFlashUploadUrl = 'index.php?route=common/filemanager&token=<?php echo $token; ?>';
			});
		} else {
			$.cookie('ckeditor', 'disabled', {expires: 365});
			for (name in CKEDITOR.instances) CKEDITOR.instances[name].destroy();
		}
	}
//--></script>
<?php echo $footer; ?>