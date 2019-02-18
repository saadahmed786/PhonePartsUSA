<?php echo $header; ?>
<div id="content" class="ExcelPortContent">
    <!-- START BREADCRUMB -->
    <?php require_once(IMODULE_ADMIN_ROOT.'view/template/module/excelport/breadcrumb.php'); ?>
    <!-- END BREADCRUMB -->
    <!-- START FLASHMESSAGE -->
    <?php require_once(IMODULE_ADMIN_ROOT.'view/template/module/excelport/flashmessage.php'); ?>
    <!-- END FLASHMESSAGE -->
    <div class="box">
        <div class="heading">
        	<h1>
            	<img src="view/image/imodules.png" style="margin-top: -3px;" alt="" />
                <span class="ExcelPortsTitle"><?php echo $heading_title; ?></span>
                <?php 
                	$dirname = IMODULE_ADMIN_ROOT.'view/template/module/excelport/';
                    
                	$tab_files = scandir($dirname); 
                	$tabs = array();
                	foreach ($tab_files as $key => $file) {
                		if (strpos($file,'tab_') !== false) {
                			$tabs[] = array(
                            	'file' => $dirname.$file,
                				'name' => ucwords(str_replace('.php','',str_replace('_',' ',str_replace('tab_','',$file))))
                			);
               			} 
                	}
               		foreach ($tabs as $key => $tab) {
                		if ($tab['name'] == 'Support' && $key < count($tabs) - 1) {
                			$temp = $tabs[count($tabs) - 1];
                			$tabs[count($tabs) - 1] = $tab;
                			$tabs[$key] = $temp;
                			break;
                		}
                	}
                ?>
        		<ul class="ExcelPortAdminSuperMenu">
          		<?php foreach($tabs as $tab): ?>
            		<li><?php echo $tab['name']; ?></li>
          		<?php endforeach; ?>
        		</ul>
        	</h1>
			<div class="buttons">
            	<a onclick="$('#form').submit();" class="button submitButton ExcelPortSubmitButton"><?php echo $button_save; ?></a>
                <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a>
            </div>
		</div>
        <!-- START NOT ACTIVATED CHECK -->
        <?php require_once(IMODULE_ADMIN_ROOT.'view/template/module/excelport/notactivated.php'); ?>
        <!-- END NOT ACTIVATED CHECK -->
		<div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <ul class="ExcelPortAdminSuperWrappers">
                    <?php foreach($tabs as $tab): ?>
                    <li><?php require_once($tab['file']); ?></li>
                    <?php endforeach; ?>
                </ul>
                <input type="hidden" class="selectedTab" name="selectedTab" value="<?php echo (empty($_GET['tab'])) ? 0 : $_GET['tab'] ?>" />
                <input type="hidden" name="ExcelPort[Activated]" value="true"/>
            </form>
		</div>
	</div>
</div>

<div id="progress-dialog">
	<div id="progressbar"></div>
    <div id="progressinfo"></div>
    <button class="finishActionButton" style="display: none;"><!--<img src="./view/image/ExcelPort/ajax-loader-2.gif" class="loadingImage2"/>-->Abort</button>
</div>

<script type="text/javascript">

$('li[id^="toolbar-popup"]').remove();

var xhr;
var loopXHR;
var pageTitle = $('title').html();
var abort = false;
var sending = false;
var updateTimeout = null;
var loopXHR = null;
var site_url = null;
var lastMemory = 0;
var unidentifiedError = false;

var closeDialog = function() {
	$( "#progress-dialog" ).dialog('close');
	$( "#progress-dialog" ).dialog('destroy');
	$('#progressinfo').empty();
	$('#progressbar').progressbar({value:0});
	$('.finishActionButton').hide();
	initDialog();
}

$('.finishActionButton').click(function() {
	abort = true;
	loopXHR.abort();
	clearTimeout(updateTimeout);
	$(this).html('<img src="./view/image/ExcelPort/ajax-loader-2.gif" class="loadingImage2"/>');
	$(this).attr('disabled', 'disabled');
    $('#progressinfo').html('Aborting... Please wait...');
	$('title').html(pageTitle);
	if (!sending) closeDialog();
});

$(document).ajaxSend(function() {
	sending = true;	
});

$(document).ajaxStop(function() {
	sending = false;
	if (abort) {
		closeDialog();
	}
});

var dependable = 'input[name="ExcelPort[Export][DataType]"], input[name="ExcelPort[Import][DataType]"]';

$(dependable).each(function() {
	$(this).change(function() {
		$('*[data-depends-on]').each(function() {
			if ($($(this).attr('data-depends-on')).is(':checked') || $($(this).attr('data-depends-on')).is(':selected')) {
				$(this).slideDown(100);
			} else {
				$(this).hide();
			}
		});
	});
	$(this).trigger('change');
});

var initDialog = function () {
	$( "#progress-dialog" ).dialog({
		autoOpen: false,
		width: 680,
		show: "fade",
		modal: true,
		resizable: false,
		closeOnEscape: false,
		open: function(event, ui) {
			$(".ui-dialog-titlebar").hide();
			$(".ui-dialog").css({
				left: (($(document).width() - $(this).width())/2)+'px',
				top: (($(document).height() - $(this).height())/2)+'px'
			});
		}
	});
};

initDialog();

switch (location.protocol) {
	case 'https:': 
		site_url = '<?php echo dirname(HTTPS_SERVER); ?>';
		break;
	default:
		site_url = '<?php echo dirname(HTTP_SERVER); ?>';
		break;
}

if (document.location.href.indexOf('com_mijoshop') != -1) site_url += '/components/com_mijoshop/opencart';

var selectedTab = $('.selectedTab').val();
$('.ExcelPortAdminSuperMenu li').removeClass('selected').eq(selectedTab).addClass('selected');
$('.ExcelPortAdminSuperWrappers > li').hide().eq(selectedTab).show();

$(window).load(function() {
	var downloaded = false;
	var importing = false;
	var ajaxgenerate = <?php echo (empty($this->session->data['ajaxgenerate'])) ? 'false' : $this->session->data['ajaxgenerate']; unset($this->session->data['ajaxgenerate']); ?>;
	var ajaximport = <?php echo (empty($this->session->data['ajaximport']) || $hadError) ? 'false' : $this->session->data['ajaximport']; unset($this->session->data['ajaximport']); ?>;
	var token = '';
	var vars = window.location.search.split('&');
	var progressText = ['', ''];
	var progressRegime = ajaxgenerate ? 'Export' : 'Import';
	
	var conditions = <?php echo json_encode($conditions); ?>;
	var operations = <?php echo json_encode($operations); ?>;
	var enabled_conditions = <?php echo json_encode(!empty($data['ExcelPort']['Export']['Filters']) ? $data['ExcelPort']['Export']['Filters'] : array()); ?>;
	
	var conditionsIndexes = {};
	for (var i in conditions) {
		conditionsIndexes[i] = 0;
	}

	if ($('input[name="ExcelPort[' + progressRegime + '][DataType]"]:checked').val() == 'Products') {
		progressText = [
			'Products',
			'products'
		];	
	} else if ($('input[name="ExcelPort[' + progressRegime + '][DataType]"]:checked').val() == 'Categories') {
		progressText = [
			'Categories',
			'categories'
		];		
	} else if ($('input[name="ExcelPort[' + progressRegime + '][DataType]"]:checked').val() == 'Options') {
		progressText = [
			'Options',
			'options'
		];		
	} else if ($('input[name="ExcelPort[' + progressRegime + '][DataType]"]:checked').val() == 'Attributes') {
		progressText = [
			'Attributes and attribute groups',
			'attributes and attribute groups'
		];		
	} else if ($('input[name="ExcelPort[' + progressRegime + '][DataType]"]:checked').val() == 'Customers') {
		progressText = [
			'Customers',
			'customers'
		];		
	} else if ($('input[name="ExcelPort[' + progressRegime + '][DataType]"]:checked').val() == 'CustomerGroups') {
		progressText = [
			'Customer Groups',
			'customer groups'
		];		
	}
	
	for (var i = 0; i < vars.length; i++) {
		var parts = vars[i].split('=');
		if (parts[0] == 'token') token = parts[1];	
	}
	var timer = null;
	var seconds;
	
	var zeroPad = function (num, places) {
	  var zero = places - num.toString().length + 1;
	  return Array(+(zero > 0 && zero)).join("0") + num;
	}
	
	var progress = function(message, isError) {
		if (isError !== false) {
			$('#progressbar').progressbar({value: message.percent, disabled:false});
			if ((message.current === message.all && !importing && typeof(message.populateAll) == 'undefined') || message.finishedImport) {
				$('.finishActionButton').html('Finish');
				$('.finishActionButton').removeAttr('disabled');
				clearInterval(timer);
				clearTimeout(updateTimeout);
				loopXHR.abort();
				if (!downloaded) {
					$('#progressinfo').html('<?php echo $text_file_downloading; ?>');
					document.location.href = "index.php?token=" + token + "&route=module/excelport/download";
					downloaded = true;
				}
				if (importing) {
					$('#progressinfo').html('<?php echo $text_import_done; ?>'.replace('{COUNT}', message.current).replace('{TYPE}', progressText[1]));
				}
			} else if (importing) {
				if (message.current > 0) {
					var pps = Math.round((message.current)/seconds);
					$('#progressinfo').html('Importing. Please wait...<br />Reading from: ' + message.importingFile + '<br />' + progressText[0] + ' per second: ' + pps + "<br />Imported: " + message.current);
				} else {
					$('#progressbar').progressbar({value: 100, disabled:false});
					$('#progressinfo').html('<?php echo $text_preparing_data; ?>');	
				}
			} else {
				if (message.current > 0) {
					if (message.percent != 100) {
						var pps = message.current/seconds;
						var allSecondsRemaining = Math.round((message.all - message.current)/pps);
						var hoursRemaining =  zeroPad(Math.floor(allSecondsRemaining/3600), 2);
						var minutesRemaining = zeroPad(Math.floor((allSecondsRemaining%3600)/60), 2);
						var secondsRemaining = zeroPad(Math.floor((allSecondsRemaining%60)), 2);
						$('#progressinfo').html("Progress: " + message.percent + "%<br />" + message.current + " " + progressText[1] + " were " + (importing ? "imported" : "exported") + "...<br />" + Math.ceil(pps) + " " + progressText[1] + " per second<br />" + "Estimated time left: " + hoursRemaining + ':' + minutesRemaining + ':' + secondsRemaining);
					} else {
						$('#progressinfo').html('<?php echo $text_file_generating; ?>');	
					}
				} else {
					$('#progressinfo').html('<?php echo $text_preparing_data; ?>');		
				}
			}
		} else {
			$('.finishActionButton').html('Finish');
			$('.finishActionButton').removeAttr('disabled');
			$('#progressbar').progressbar({ disabled: true, value: 0 });
			$('#progressinfo').html(message);
			clearInterval(timer);
			clearTimeout(updateTimeout);
		}
	}
	
	var countSeconds = function() {
		seconds++;
	}
	
	var updateProgressBar = function(site_root, countinueChecking) {
		countinueChecking = typeof countinueChecking == 'undefined' ? true : countinueChecking;
		if (abort) return;
		loopXHR = $.ajax({
			url: site_root+'/<?php echo IMODULE_TEMP_FOLDER; ?>/excelport_progress.txt',
			type: 'GET',
			timeout: null,
			dataType: 'json',
			cache: false,
			success: function(returnData, textStatus, jqXHR) {
				if ($( "#progress-dialog" ).dialog('isOpen')) {
					if (returnData != null && returnData.error == false) {
						if (lastMemory == returnData.memory_get_usage && unidentifiedError) {
							var megabytes = Math.round(parseInt(returnData.memory_get_usage)/1048576);
							var errorMessage = 'Error: The server may be out of memory. Currently, the script is using ' + megabytes + ' MB';
							progress(errorMessage, false);
							return;
						} else {
							lastMemory = returnData.memory_get_usage;
						}
						progress(returnData, true);
						
						if (!importing) document.title = returnData.percent + '% ' + pageTitle;
						
						if ((returnData != null && returnData.current !== returnData.all && !importing) || (!returnData.finishedImport && importing)) {
							if (!countinueChecking) {
								return;
							}
							
							updateTimeout = setTimeout(function (){
								
								updateProgressBar(site_root);
							}, 1000);
						}
					} else {
						if (returnData != null) {
							progress(returnData.message, false);
							if (!countinueChecking || (returnData.current == returnData.all && !importing)) {
								return;
							}
							
							updateTimeout = setTimeout(function (){
								
								updateProgressBar(site_root);
							}, 1000);	
						} else {
							if (!countinueChecking) {
								return;
							}
							
							updateTimeout = setTimeout(function (){
								
								updateProgressBar(site_root);
							}, 1000);
						}
					}
				} else {
					
					clearTimeout(updateTimeout);
				}
			},
			error: function() {
				if (!countinueChecking) {
					
					return;
				}
				
				updateTimeout = setTimeout(function (){
					
					updateProgressBar(site_root);
				}, 1000);
			}
		});
	}
	
	var startAjaxGenerate = function(path, data) {
		downloaded = false;
		importing = false;
		unidentifiedError = false;
		if (abort) return;
		if (!$( "#progress-dialog" ).dialog('isOpen')) {
			$( "#progress-dialog" ).dialog( "open" );
			$('.loadingImage').show();	
			$('.finishActionButton').show();
		}
		if (timer == null) {
			seconds = 1;
			timer = setInterval(countSeconds, 1000);
		}
		
		xhr = $.ajax({
			url: path,
			data: data,
			async: true,
			type: 'POST',
			timeout: null,
			dataType: 'json',
			cache: false,
			statusCode: {
				500: function(){
					progress('Server error 500 has occured.', false);
				}
			},
			success: function(successData) {
				if (successData == null) {
					unidentifiedError = true;
				} else {
					if (successData.current < successData.all && successData.done) {
						startAjaxGenerate(path, data);	
					}
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				clearTimeout(updateTimeout);
				error = true;
				
				if (textStatus == 'timeout') {
					progress('A server timeout has occured.', false);
				} else if (textStatus == 'error') {
					console.log('A server error has occured.');	
				} else if (textStatus == 'parsererror') {
					progress(jqXHR.responseText.replace("<br />", ''), false);
				}
			}
		});
	}
	
	var startAjaxImport = function(path, data) {
		importing = true;
		downloaded = true;
		unidentifiedError = false;
		if (abort) return;
		if (!$( "#progress-dialog" ).dialog('isOpen')) {
			$( "#progress-dialog" ).dialog( "open" );
			$('.loadingImage').show();	
			$('.finishActionButton').show();
		}
		if (timer == null) {
			seconds = 1;
			timer = setInterval(countSeconds, 1000);
		}
		
		xhr = $.ajax({
			url: path,
			data: data,
			async: true,
			type: 'POST',
			timeout: null,
			dataType: 'json',
			cache: false,
			statusCode: {
				500: function(){
					progress('Server error 500 has occured.', false);
				}
			},
			success: function(successData) {
				if (successData == null) {
					unidentifiedError = true;
				} else {
					if (successData.error) {
						progress(successData.message, false);
					} else {
						if (successData.done && !successData.finishedImport) {
							startAjaxImport(path, data);	
						}
					}
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				clearTimeout(updateTimeout);
				error = true;
				
				if (textStatus == 'timeout') {
					progress('A server timeout has occured.', false);
				} else if (textStatus == 'error') {
					console.log('A server error has occured.');	
				} else if (textStatus == 'parsererror') {
					progress(jqXHR.responseText.replace("<br />", ''), false);
				}
			}
		});
	}
	
	var triggerFiltersDisplay = function() {
		var checked = $('input[name="ExcelPort[Export][DataType]"]:checked').val();
		
		if ($('#toggle_filter').val() == '1') {
			$('.dataTypeFilter').each(function(index, element) {
				if ($(element).attr('data-type') == checked) $(element).slideDown();
				else $(element).slideUp();
			});
		} else {
			$('.dataTypeFilter').slideUp();
		}
	}
	
	var getOperations = function(category, operation, index) {
		var html = '<select name="ExcelPort[Export][Filters][' + category + '][' + index + '][Condition]">';
		
		for (var i in operations) {
			html	+= '<option value="' + i + '"' + ((typeof(operation) != 'undefined' && operation == i) ? ' selected="selected"' : '') + '>' + operations[i].html + '</option>';
		}
		
		html    += '</select>';
		
		return html;
	}
	
	var refreshOperation = function(field, type, predefine) {
		$(field).children('option').attr('disabled', 'disabled');
		if (typeof(type) != 'undefined') {
			if (type == 'text') $(field).children('option[value^="text_"]').removeAttr('disabled');
			else $(field).children('option[value^="number_"]').removeAttr('disabled');
			
			if (predefine) {
				$(field).children('option').removeAttr('selected');
				$(field).children('option[disabled!="disabled"]:first').attr('selected', 'selected');
			}
		}
	}
	
	var getFields = function(category, field_name, index) {
		var html = '<select name="ExcelPort[Export][Filters][' + category + '][' + index + '][Field]">';
		for (var i in conditions[category]) {
			html += '<option value="' + i + '"' + ((typeof(field_name) != 'undefined' && i == field_name) ? ' selected="selected"' : '') + '>' + conditions[category][i].label + '</option>';
		}
		html    += '</select>';
		
		return html;
	}
	
	var addCondition = function(category, field_name, operation, value) {
		if (typeof(category) != 'undefined') {
			html  = '<tr>';
			html += '	<td>' + getFields(category, field_name, conditionsIndexes[category]) + '';
			html += '	' + getOperations(category, operation, conditionsIndexes[category]) +'';
			html += '	<?php echo $text_the_value; ?> <input type="text"' + ((typeof(value) != 'undefined') ? ' value="' + value + '"' : '') + ' name="ExcelPort[Export][Filters][' + category + '][' + conditionsIndexes[category] + '][Value]"></td>';
			html += '	<td class="right"><a class="btn btn-danger discardCondition"><i class="icon-trash icon-white"></i> <?php echo $button_discard_condition; ?></a></td>';
			html += '</tr>';
			
			$('.dataTypeFilter[data-type="' + category + '"] table tbody').append(html);
			if (typeof(field_name) != 'undefined') {
				refreshOperation('select[name="ExcelPort[Export][Filters][' + category + '][' + conditionsIndexes[category] + '][Condition]"]', conditions[category][field_name].type, false);
			} else {
				for (var j in conditions[category]) { refreshOperation('select[name="ExcelPort[Export][' + category + '][' + conditionsIndexes[category] + '][Condition]"]', conditions[category][j].type, true); break; }
			}
			$('select[name="ExcelPort[Export][Filters][' + category + '][' + conditionsIndexes[category] + '][Field]"]').change({index: conditionsIndexes[category]}, function(e, data) {
				refreshOperation('select[name="ExcelPort[Export][Filters][' + category + '][' + e.data.index + '][Condition]"]', conditions[category][$(this).val()].type, true);
			}).trigger('change');
			
			$('.discardCondition').unbind('click').click(function() {
				$(this).closest('tr').remove();
			});
			
			conditionsIndexes[category]++;
		}
	}
	
	for (var i in enabled_conditions) {
		var added = false;
		for (var j in enabled_conditions[i]) {
			if (typeof(enabled_conditions[i][j].Field) != 'undefined') {
				addCondition(i, enabled_conditions[i][j].Field, enabled_conditions[i][j].Condition, enabled_conditions[i][j].Value);
				added = true;
			}
		}
		if (!added) addCondition(i);
	}
	
	if (ajaxgenerate) {
		$('#generateLoading').show();
		updateProgressBar(site_url);
		
		var exportData = {
			ExcelPort : {
				Export : {
					DataType : $('input[name="ExcelPort[Export][DataType]"]:checked').val(),
					Store : $('input[name="ExcelPort[Export][Store]"]:checked').val(),
					Language : $('input[name="ExcelPort[Export][Language]"]:checked').val(),
					QuickExport : $('input[name="ExcelPort[Export][QuickExport]"]:checked').val(),
					Filter : $('input[name="ExcelPort[Export][Filter]"]').val(),
					Filters : {}
				},
				Settings : {
					ExportLimit : $('input[name="ExcelPort[Settings][ExportLimit]"]').val()
				}
			}
		};
		
		$('*[name^="ExcelPort[Export][Filters]"]').each(function (index, element) {
			var regex = /ExcelPort\[Export\]\[Filters\]\[(.*?)\]\[(.*?)\]\[(.*?)\]/gi;
			match = regex.exec($(element).attr('name'));
			if (match == null) {
				regex = /ExcelPort\[Export\]\[Filters\]\[(.*?)\]\[(.*?)\]/gi;
				match = regex.exec($(element).attr('name'));
				if (typeof(exportData.ExcelPort.Export.Filters[match[1]]) == 'undefined') exportData.ExcelPort.Export.Filters[match[1]] = {};
				exportData.ExcelPort.Export.Filters[match[1]][match[2]] = $(element).val();
			} else {
				if (typeof(exportData.ExcelPort.Export.Filters[match[1]]) == 'undefined') exportData.ExcelPort.Export.Filters[match[1]] = {};
				if (typeof(exportData.ExcelPort.Export.Filters[match[1]][match[2]]) == 'undefined') exportData.ExcelPort.Export.Filters[match[1]][match[2]] = {};
				exportData.ExcelPort.Export.Filters[match[1]][match[2]][match[3]] = $(element).val();
			}
		});
		
		startAjaxGenerate('index.php?token='+token+'&route=module/excelport/ajaxgenerate&_=' + (new Date()).getTime(), exportData);
	}
	
	if (ajaximport) {
		$('#generateLoading').show();
		updateProgressBar(site_url);
		startAjaxImport('index.php?token='+token+'&route=module/excelport/ajaximport&_=' + Date.now(), {
			ExcelPort : {
				Import : {
					DataType : $('input[name="ExcelPort[Import][DataType]"]:checked').val(),
					Language : $('input[name="ExcelPort[Import][Language]"]:checked').val(),
					Delete : $('input[name="ExcelPort[Import][Delete]"]:checked').val(),
					AddAsNew : $('input[name="ExcelPort[Import][AddAsNew]"]:checked').val()
				},
				Settings : {
					ImportLimit : $('input[name="ExcelPort[Settings][ImportLimit]"]').val()
				}
			}
		});
	}
	
	$('.ExcelPortAdminMenu li').click(function() {
		$('.ExcelPortAdminMenu li',$(this).parents('li')).removeClass('selected');
		$(this).addClass('selected');
		$($('.ExcelPortAdminWrappers li',$(this).parents('li')).hide().get($(this).index())).fadeIn(200);
	});
	
	$('.ExcelPortAdminSuperMenu li').click(function() {
		$('.ExcelPortAdminSuperMenu > li',$(this).parents('h1')).removeClass('selected');
		$(this).addClass('selected');
		$($('.ExcelPortAdminSuperWrappers > li',$(this).parents('#content')).hide().get($(this).index())).fadeIn(200);
		$('.selectedTab').val($(this).index());
	});
	
	$('.needMoreSize').click(function() {
		window.open(site_url + '/vendors/excelport/help_increase_size.php', '_blank', 'location=no,width=830,height=580,resizable=no');
	});
	
	$('.ExcelPortSubmitButton').click(function(e) {
		abort = false;
		var action = $(this).attr('data-action');
		if (action == 'import' && $('#checkboxDelete').is(':checked')) {
			if (!confirm('<?php echo $text_confirm_delete_other; ?>')) return;	
		}
		$('#form').attr('action',$('#form').attr('action').replace(/&submitAction=.*($|&)/g, ''));
		if (action != undefined && action != '') {
			$('#form').attr('action',$('#form').attr('action')+'&submitAction='+action);
		}
		$('#form').submit();
	});
	
	$('#filter_popover').popover({ trigger: 'hover' }).click(function() {
		$(this).toggleClass('active');
		if ($(this).hasClass('active')) {
			$('#toggle_filter').val('1');
		} else {
			$('#toggle_filter').val('0');
		}
		triggerFiltersDisplay();
	});
	
	$('input[name="ExcelPort[Export][DataType]"]').change(triggerFiltersDisplay);
	triggerFiltersDisplay();
	
	$('.addCondition').click(function() {
		addCondition($(this).closest('.dataTypeFilter').attr('data-type'));
	});
	
	$('a[data-toggle="tooltip"]').tooltip({placement:'right'});
});
</script>
<?php echo $footer; ?>
