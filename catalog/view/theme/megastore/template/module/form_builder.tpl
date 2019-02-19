<?php
//==============================================================================
// Form Builder v154.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================
?>

<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/form_builder.css" />
<style type="text/css">
	<?php if (!empty($module['css'])) echo $module['css'] . ' { display: none; }'; ?>
	#form-password-overlay {
		opacity: <?php echo (int)$form['password']['opacity'] / 100; ?>;
	}
</style>

<div class="<?php echo $module['box']; ?>">
	<?php if (!empty($module['heading'])) { ?>
		<div class="<?php echo $module['heading'] . '">' . $form['name'][$language]; ?></div>
	<?php } ?>
	<?php if ($form['password']['password']) { ?>
		<div id="form-password-overlay"></div>
		<div id="form-password-box">
			<?php echo $form['password']['message'][$language]; ?><br />
			<input type="password" onkeyup="if (event.which == 13) validatePassword(<?php echo $form['form_id']; ?>)" /><br />
			<a class="button" onclick="validatePassword(<?php echo $form['form_id']; ?>)"><?php echo $this->language->get('button_continue'); ?></a>
		</div>
	<?php } ?>
	<div class="<?php echo $module['content']; ?>">
		
		<div id="form<?php echo $form['form_id']; ?>">
			<div class="form-table">
				<div class="form-cell">
				<?php foreach ($form['fields'] as $field) { ?>
					
					<?php if ($field['type'] == 'column') { ?>
						
						</div><div class="form-cell">
						
					<?php } elseif ($field['type'] == 'row') { ?>
						
						</div></div><div class="form-table"><div class="form-cell">
						
					<?php } elseif ($field['type'] == 'html') { ?>
						
						<?php echo $this->replaceShortcodes($field['html'][$language]); ?><br />
						
					<?php } else { ?>
						
						<?php if ($field['required']) { ?>
							<div class="form-required"><span class="required">*</span>
						<?php } elseif ($field['type'] != 'hidden') { ?>
							<div>
						<?php } ?>
						<?php if (isset($field['name']) && $field['type'] != 'hidden') { ?>
							<strong><?php echo $this->replaceShortcodes($field['name'][$language]); ?></strong><br />
						<?php } ?>
						
						<?php if ($field['type'] == 'captcha') { ?>
							
							<?php $captcha = (isset($captcha)) ? $captcha + 1 : 0; ?>
							<input type="text" value="" autocomplete="off" class="form-captcha" /><br />
							<div class="form-captcha-image" style="background: url('index.php?route=module/form_builder/captcha&key=form<?php echo $form['form_id']; ?>_captcha<?php echo $captcha; ?>') no-repeat 50% 50%"></div><br />
							
						<?php } elseif ($field['type'] == 'checkbox' || $field['type'] == 'radio') { ?>
							
							<?php $defaults = array_map(array($this, 'replaceShortcodes'), array_map('trim', explode(';', $field['default'][$language]))); ?>
							<?php foreach (array_map(array($this, 'replaceShortcodes'), array_map('trim', explode(';', $field['choices'][$language]))) as $choice) { ?>
								<label>
									<input type="<?php echo $field['type']; ?>" name="<?php echo $field['key'] . ($field['type'] == 'checkbox' ? '[]' : ''); ?>" style="cursor: pointer" onclick="$(this).val($(this).is(':checked') ? '<?php echo addslashes($choice); ?>' : '')" <?php echo (in_array($choice, $defaults)) ? 'checked="checked" value="' . $choice . '"' : 'value=""'; ?> />
									<?php echo $choice; ?>
								</label><br />
							<?php } ?>
							
						<?php } elseif ($field['type'] == 'date' || $field['type'] == 'time' || $field['type'] == 'datetime') { ?>
							
							<input type="text" name="<?php echo $field['key']; ?>" value="<?php echo $field['default'][$language]; ?>" class="form-<?php echo $field['type']; ?>" /><br />
							
						<?php } elseif ($field['type'] == 'email') { ?>
							
							<input type="text" name="<?php echo $field['key']; ?>" class="form-email" /><br />
							<?php if ($field['confirm']) { ?>
								<br />
								<?php if ($field['required']) { ?>
									<span class="required">*</span>
								<?php } ?>
								<strong><?php echo $field['confirm_name'][$language]; ?></strong><br />
								<input type="text" class="form-confirm" /><br />
							<?php } ?>
							
						<?php } elseif ($field['type'] == 'file') { ?>
							
							<input type="hidden" name="<?php echo $field['key']; ?>" title="<?php echo $field['filesize'] . ';' . $field['extensions'] . ';' . str_replace('"', '\"', $field['success'][$language]); ?>" />
							<input type="file" class="form-fileupload" /><br />
							<span class="form-file-help"><?php echo $field['extensions']; ?></span><br />
							
						<?php } elseif ($field['type'] == 'hidden') { ?>
							
							<input type="hidden" name="<?php echo $field['key']; ?>" value="<?php echo $this->replaceShortcodes($field['data'][$language]); ?>" />
							
						<?php } elseif ($field['type'] == 'select') { ?>
							
							<?php $defaults = array_map(array($this, 'replaceShortcodes'), array_map('trim', explode(';', $field['default'][$language]))); ?>
							<?php if ($field['selections'] > 1) { ?>
								<select name="<?php echo $field['key']; ?>[]" multiple="multiple" size="<?php echo (int)$field['selections']; ?>">
							<?php } else { ?>
								<select name="<?php echo $field['key']; ?>">
							<?php } ?>
								<?php foreach (array_map(array($this, 'replaceShortcodes'), array_map('trim', explode(';', $field['choices'][$language]))) as $choice) { ?>
									<option value="<?php echo $choice; ?>" <?php if (in_array($choice, $defaults)) echo 'selected="selected"'; ?>><?php echo $choice; ?></option>
								<?php } ?>
							</select>
							<br />
							
						<?php } elseif ($field['type'] == 'submit') { ?>
							
							<a onclick="submitForm($(this), <?php echo $form['form_id']; ?>, $(this).next().html(), '<?php echo (!empty($field['redirect']) && substr($field['redirect'], 0, 4) != 'http' ? 'http://' : '') . $field['redirect']; ?>')" class="button"><?php echo $this->replaceShortcodes($field['button'][$language]); ?></a>
							<div style="display: none"><?php echo $this->replaceShortcodes($field['success'][$language]); ?></div><br />
							
						<?php } elseif ($field['type'] == 'text' || $field['type'] == 'password') { ?>
							
							<?php $onblur = ($field['min_length']) ? 'onblur="validateMin($(this), ' . (int)$field['min_length'] . ')"' : ''; ?>
							<?php $onkeypress = ($field['max_length'] || $field['allowed'][$language]) ? 'onkeypress="validateMaxAllowed($(this), event, ' . (int)$field['max_length'] . ', \'' . addslashes($field['allowed'][$language]) . '\')"' : ''; ?>
							<?php $onpaste = ($field['max_length'] || $field['allowed'][$language]) ?  'onpaste="validatePaste($(this), ' . (int)$field['max_length'] . ', \'' . addslashes($field['allowed'][$language]) . '\')"' : ''; ?>
							<input type="<?php echo $field['type']; ?>" name="<?php echo $field['key']; ?>" <?php echo $onblur; ?> <?php echo $onkeypress; ?> <?php echo $onpaste; ?> value="<?php echo $this->replaceShortcodes($field['default'][$language]); ?>" /><br />
							
						<?php } elseif ($field['type'] == 'textarea') { ?>
							
							<?php $onblur = ($field['min_length']) ? 'onblur="validateMin($(this), ' . (int)$field['min_length'] . ')"' : ''; ?>
							<?php $onkeypress = ($field['max_length'] || $field['allowed'][$language]) ? 'onkeypress="validateMaxAllowed($(this), event, ' . (int)$field['max_length'] . ', \'' . addslashes($field['allowed'][$language]) . '\')"' : ''; ?>
							<?php $onpaste = ($field['max_length'] || $field['allowed'][$language]) ?  'onpaste="validatePaste($(this), ' . (int)$field['max_length'] . ', \'' . addslashes($field['allowed'][$language]) . '\')"' : ''; ?>
							<textarea name="<?php echo $field['key']; ?>" <?php echo $onblur; ?> <?php echo $onkeypress; ?> <?php echo $onpaste; ?>><?php echo $this->replaceShortcodes($field['default'][$language]); ?></textarea><br />
							
						<?php } ?>
						
						<?php if ($field['type'] != 'hidden') { ?>
							</div>
						<?php } ?>
						
					<?php } ?>
				<?php } ?>
				</div> <!-- table-cell -->
			</div> <!-- table -->
		</div>
	</div>
</div>

<?php
	$form_language = "{";
	foreach ($form['errors'] as $error_name => $error) {
		$form_language .= "'" . $error_name . "': '" . addslashes($error[$language]) . "', ";
	}
	$form_language .= "'button_continue': '" . $this->language->get('button_continue') . "'}";
?>
<script type="text/javascript"><!--
	var form_language = <?php echo $form_language; ?>
//--></script>
<?php if ($this->request->get['route'] != 'product/product') { ?>
	<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-timepicker-addon.js"></script>
<?php } ?>
<script type="text/javascript" src="catalog/view/javascript/jquery/ajaxupload.js"></script>
<script type="text/javascript" src="catalog/view/javascript/form_builder.js"></script>
