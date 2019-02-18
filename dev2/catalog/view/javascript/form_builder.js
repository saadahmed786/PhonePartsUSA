//==============================================================================
// Form Builder v154.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

$(window).load(function(){
	$('#form-password-overlay').width($('#form-password-overlay').parent().width());
	$('#form-password-overlay').height($('#form-password-overlay').next().next().innerHeight());
	$('#form-password-box').css('margin-left', ($('#form-password-box').parent().width() - $('#form-password-box').innerWidth()) / 2);
});

$(document).ready(function(){
	$('.form-date').datepicker({
		dateFormat: 'yy-mm-dd'
	});	
	$('.form-time').timepicker({
		timeFormat: 'h:mm tt',
		ampm: true
	});
	$('.form-datetime').datetimepicker({
		dateFormat: 'yy-mm-dd',
		timeFormat: 'h:mm tt',
		ampm: true,
		separator: ' @ '
	});
	
	$('.form-fileupload').each(function(){
		element = $(this);
		new AjaxUpload(element, {
			action: 'index.php?route=module/form_builder/upload',
			autoSubmit: true,
			data: {'filesize': element.prev().attr('title').split(';')[0], 'extensions': element.prev().attr('title').split(';')[1]},
			name: 'file',
			responseType: 'json',
			onSubmit: function(file, extension) {
				$('.form-file-error, .form-file-success').remove();
				element.before('<img id="loading" src="catalog/view/theme/default/image/loading.gif" alt="Loading" />');
				element.attr('disabled', 'disabled');
			},
			onComplete: function(file, json) {
				$('#loading').remove();
				element.removeAttr('disabled');
				if (json['success']) {
					element.prev().val(json['file']);
					element.before('<div class="form-file-success">' + element.prev().attr('title').split(';')[2] + ' &nbsp; <span style="color: #000">' + json['name'] + '</span></div>');
				}
				if (json['error']) {
					element.before('<div class="form-file-error">' + form_language[json['error']] + '</div>');
				}
			}
		});
	});
	
	$(document).keydown(function(event) {
		if ($('.ui-dialog').is(':visible') && event.which == 13) {
			event.preventDefault();
			$('.ui-dialog-buttonpane button').click();
		}
	});
});

function validatePassword(form_id) {
	$('#form-password-box a').after('<img id="loading" style="margin-left: 10px" src="catalog/view/theme/default/image/loading.gif" alt="Loading" />');
	$.ajax({
		url: 'index.php?route=module/form_builder/validatePassword&form_id=' + form_id + '&password=' + $('#form-password-box input').val(),
		success: function(data) {
			$('#loading').remove();
			if (data == 'success') {
				$('#form-password-overlay').hide('slow');
				$('#form-password-box').hide('slow');
			} else {
				$('#form-password-overlay').css('background', 'url("catalog/view/javascript/jquery/ui/themes/ui-lightness/images/ui-bg_diagonals-thick_18_b81900_40x40.png") 50% 50% repeat');
			}
		}
	});
}

function validateMin(element, min) {
	if (element.val().length && (element.val().length + 1) < min && !$('.ui-dialog').is(':visible')) {
		popupDialog(form_language['minlength'].replace('[min]', min), function(){ $('.ui-dialog-content').dialog('close'); element.focus(); });
		element.focus();
	}
}

function validateMaxAllowed(element, event, max, allowed) {
	if ($.inArray(event.which, [0, 8, 13]) != -1) return;
	if (max && (element.val().length + 1) > max && element[0].selectionStart == element[0].selectionEnd) {
		event.preventDefault();
	}
	if (allowed && allowed.indexOf(String.fromCharCode(event.which)) == -1) {
		event.preventDefault();
	}
}

function validatePaste(element, max, allowed) {
	setTimeout(function(){
		if (allowed) {
			var regex = new RegExp('[^' + allowed.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&') + ']', 'g');
			element.val(element.val().replace(regex, ''));
		}
		if (max && element.val().length > max) {
			element.val(element.val().substr(0, max));
		}
	}, 0);
}

function validateForm(form_id) {
	var errors = [];
	$('.form-required-bg').removeClass('form-required-bg');
	
	$('.form-required').each(function(){
		var element = $(this);
		element.find(':input').each(function(){
			if (!$(this).val()) element.addClass('form-required-bg');
		});
		element.find(':checkbox, :radio').each(function(){
			if ($(this).val()) element.removeClass('form-required-bg');
		});
	});
	if ($('.form-required-bg').length) {
		errors.push(form_language['required']);
	}
	
	var regex = /^[^@]+@[^@]+\.[a-zA-Z]{2,}$/i;
	$('.form-email').each(function(){
		if ($(this).val() || $(this).parent().find('.form-confirm').val()) {
			if (!regex.test($(this).val())) {
				$(this).parent().addClass('form-required-bg');
				errors.push(form_language['invalid_email']);
			}
			if ($(this).parent().find('.form-confirm').length && $(this).val() != $(this).parent().find('.form-confirm').val()) {
				$(this).parent().addClass('form-required-bg');
				errors.push(form_language['mismatch']);
			}
		}
	});
	
	var i = 0;
	$('.form-captcha').each(function(){
		var captcha = $(this);
		$.ajax({
			async: false,
			url: 'index.php?route=module/form_builder/validateCaptcha&key=form' + form_id + '_captcha' + i + '&value=' + $(this).val(),
			success: function(data) {
				if (data) {
					captcha.parent().addClass('form-required-bg');
					errors.push(form_language['captcha']);
				}
			}
		});
		i++;
	});
	
	return errors;
}

function submitForm(element, form_id, success, redirect) {
	popupDialog('<div class="progressbar" />', '');
	$('.progressbar').progressbar({value: 100});
	$('.ui-dialog-content').css('padding', '0');
	
	var errors = validateForm(form_id);
	
	if (errors.length) {
		$('.ui-dialog-content').dialog('close').css('padding', '15px');
		popupDialog('&bull; ' + errors.join('<br /><br />&bull; '), function(){ $('.ui-dialog-content').dialog('close'); });
	} else {
		$.ajax({
			type: 'POST',
			url: 'index.php?route=module/form_builder/submit&form_id=' + form_id,
			data: $('#form'+form_id+' :input').serialize(),
			success: function(data) {
				$('.ui-dialog-content').dialog('close').css('padding', '15px');
				if (data == 'success') {
					if (redirect) {
						popupDialog(success, function(){ location = redirect; });
					} else {
						popupDialog(success, function(){ $('.ui-dialog-content').dialog('close'); });
					}
				} else {
					popupDialog(data, function(){ $('.ui-dialog-content').dialog('close'); });
				}
			}
		});
	}
}

function popupDialog(contents, buttonFunction) {
	var buttonObject = {};
	if (buttonFunction) buttonObject[form_language['button_continue']] = buttonFunction;
	$('<div style="display: table-cell; padding: 15px;">' + contents + '</div>').dialog({
		modal: true,
		resizable: false,
		//closeOnEscape: false,
		buttons: buttonObject,
		create: function(event, ui) {
			$('.ui-dialog').css('position', 'fixed');
		},
		open: function(event, ui) {
			$('.ui-dialog :button').blur();
			$('.ui-dialog-titlebar').hide();
		}
	}).dialog('open');
}