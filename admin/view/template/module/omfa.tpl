<?php echo $header; ?>
<div id="content">
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	<?php if ($error_warning) { ?>
	<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	<?php if ($success) { ?>
	<div class="success"><?php echo $success; ?></div>
	<?php } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="view/image/setting.png" alt="" /> <?php echo $heading_title; ?></h1>
			<div class="buttons">
				<a id="button-save" class="button"><?php echo $button_save; ?></a>
				<a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="http://braiv.zendesk.com" target="_blank"><?php echo $text_help; ?></a>
			</div>
		</div>
		<div class="content" style="overflow: hidden">
			<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<input type="hidden" id="store_id" name="store_id" value="<?php echo $store_id; ?>" />
				<div id="tab-omframework">
					<div id="tabs_omframework" class="htabs">
						<a href="#tab-omframework-general"><?php echo $tab_omframework_general; ?></a>
						<a href="#tab-omframework-customization"><?php echo $tab_omframework_customization; ?></a>
						<a href="#tab-omframework-adv-customization"><?php echo $tab_omframework_adv_customization; ?></a>
						<a href="#tab-omframework-patcher"><?php echo $tab_omframework_patcher; ?></a>
						<a href="#tab-omframework-modules"><?php echo $tab_omframework_modules; ?></a>
					</div>

					<div id="tab-omframework-general">
						<table class="form">
							<tr>
								<td valign=top><?php echo $entry_mobile_theme; ?></td>
								<td>
									<?php if (defined('VERSION') && (version_compare(VERSION, '1.5.5', '<') == true)) { ?>

									<select name="config_mobile_theme">
										<?php foreach ($mobile_themes as $template) { ?>
										<option value="<?php echo $template; ?>"><?php echo $template; ?></option>
										<?php } ?>
									</select>

									<?php } else { ?>

									<select name="config_mobile_theme" onchange="$('#mobile_template').load('index.php?route=setting/setting/template&token=<?php echo $token; ?>&template=' + encodeURIComponent(this.value));">
										<?php foreach ($mobile_themes as $template) { ?>
										<?php if ($template == $config_mobile_theme) { ?>
										<option value="<?php echo $template; ?>" selected="selected"><?php echo $template; ?></option>
										<?php } else { ?>
										<option value="<?php echo $template; ?>"><?php echo $template; ?></option>
										<?php } ?>
										<?php } ?>
									</select>

									<?php } ?>
								</td>
							</tr>
							<tr>
								<td></td>
								<td id="mobile_template"></td>
							</tr>

							<tr>
								<td valign=top><?php echo $entry_mobile_disable_site_for; ?></td>
								<td>
									<input type="checkbox" name="config_mobile_tablets" value="1" <?php if ($config_mobile_tablets){echo 'checked';}; ?> size="3" />
								</td>
							</tr>

							<tr>
								<td valign=top><?php echo $entry_mobile_disable_omf; ?></td>
								<td>
									<input type="checkbox" name="config_mobile_disable_omf" value="1" <?php if ($config_mobile_disable_omf){echo 'checked';}; ?> size="3" />
								</td>
							</tr>

							<tr>
								<td valign=top><?php echo $entry_wishlist_activation; ?>:</td>
								<td>
									<input type="checkbox" name="config_wishlist_disabled" value="1" <?php if ($config_wishlist_disabled){echo 'checked';}; ?> size="3" />
								</td>
							</tr>
							<tr>
								<td valign=top><?php echo $entry_mobile_disable_addtocart_outofstock; ?>:</td>
								<td>
									<input type="checkbox" name="config_disable_addtocart_outofstock" value="1" <?php if ($config_disable_addtocart_outofstock){echo 'checked';}; ?> size="3" />
								</td>
							</tr>
							<tr>
								<td><?php echo $entry_mobile_frontpage_cat_list; ?></td>
								<td><?php echo $entry_mobile_count; ?> &nbsp;&nbsp;<input type="text" name="config_mobile_front_page_cat_list" value="<?php echo $config_mobile_front_page_cat_list; ?>" size="3" /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="config_mobile_display_top_cats" value="1" <?php if ($config_mobile_display_top_cats){echo 'checked';}; ?>> <?php echo $entry_mobile_display_top_cats; ?></td>
							</tr>
						</table>
					</div>

					<div id="tab-omframework-customization">
						<table class="form">
							<tr>
								<td valign=top><?php echo $entry_mobile_logo; ?></td>
								<td><div class="image"><img src="<?php echo $mobile_logo; ?>" alt="" id="thumb-mobile-logo" />
										<input type="hidden" name="config_mobile_logo" value="<?php echo $config_mobile_logo; ?>" id="config_mobile_logo" />
										<br />
										<a onclick="image_upload('config_mobile_logo', 'thumb-mobile-logo');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb-mobile-logo').attr('src', '<?php echo $no_image; ?>'); $('#config_mobile_logo').attr('value', '');"><?php echo $text_clear; ?></a></div></td>
							</tr>

							<tr>
								<td><span class="required">*</span> <?php echo $entry_mobile_logo_size; ?></td>
								 <td><input type="text" name="config_mobile_logo_size" value="<?php echo $config_mobile_logo_size; ?>" size="3" /> %</td>
							</tr>

							<tr>
								<td><?php echo $entry_mobile_enable_fullwidth_images; ?></td>
								<td>
									<input type="checkbox" name="config_mobile_custom_product_listing" value="1" <?php if ($config_mobile_custom_product_listing){echo 'checked';}; ?> size="3" /> <?php echo $entry_mobile_product_listing; ?><br>
									<input type="checkbox" name="config_mobile_custom_category_description" value="1" <?php if ($config_mobile_custom_category_description){echo 'checked';}; ?> size="3" /> <?php echo $entry_mobile_category_description; ?>
								</td>
							</tr>

							<tr>
								<td valign="top" height=30><?php echo $entry_mobile_custom_styles; ?></td>
								<td><textarea rows="10" cols="100" name="config_mobile_custom_styles"><?php echo $config_mobile_custom_styles; ?></textarea></td>
							</tr>
						</table>
					</div>

					<div id="tab-omframework-adv-customization">
			            <table class="form">
			            	<tr>
			            		<td valign=top><?php echo $entry_mobile_custom_gradients; ?></td>
			            		<td><input type="checkbox" name="config_mobile_custom_gradients" value="1" <?php if ($config_mobile_custom_gradients) { echo 'checked'; } ?> size="3"/></td>
			            	</tr>
							<tr>
								<td valign="top"><?php echo $entry_mobile_colors; ?></td>
								<td><?php echo $entry_mobile_eyedropper_instruction; ?></td>
							</tr>
							<tr class="eyedropper">
								<td>	
									<table>
										<tr>
											<td valign="top" height=30><strong><?php echo $entry_mobile_colors_body; ?></strong></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_background; ?></td>
										</tr>
										<tr>
											<td class="color active"># <input type="text" class="color-picker" name="config_mobile_colors_body_background" size="6" value="<?php echo $config_mobile_colors_body_background; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_text; ?></td>
										</tr>
										<tr>
											<td class="color"># <input type="text" class="color-picker black" name="config_mobile_colors_body_text" size="6" value="<?php echo $config_mobile_colors_body_text; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
									</table>
									<br/><br/>
									<table>
										<tr>
											<td valign="top" height=30><strong><?php echo $entry_mobile_colors_link; ?></strong></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_link_unvisited; ?></td>
										</tr>
										<tr>
											<td class="color"># <input type="text" class="color-picker" name="config_mobile_colors_link_unvisited" size="6" value="<?php echo $config_mobile_colors_link_unvisited; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_link_visited; ?></td>
										</tr>
										<tr>
											<td class="color"># <input type="text" class="color-picker black" name="config_mobile_colors_link_visited" size="6" value="<?php echo $config_mobile_colors_link_visited; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_link_hover; ?></td>
										</tr>
										<tr>
											<td class="color"># <input type="text" class="color-picker black" name="config_mobile_colors_link_hover" size="6" value="<?php echo $config_mobile_colors_link_hover; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_link_selected; ?></td>
										</tr>
										<tr>
											<td class="color"># <input type="text" class="color-picker black" name="config_mobile_colors_link_selected" size="6" value="<?php echo $config_mobile_colors_link_selected; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
									</table>
									<br/><br/>
									<table>
										<tr>
											<td valign="top" height=30><strong><?php echo $entry_mobile_colors_header; ?></strong></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_background; ?></td>
										</tr>
										<tr>
											<td class="color"># <input type="text" class="color-picker" name="config_mobile_colors_header_background" size="6" value="<?php echo $config_mobile_colors_header_background; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_text; ?></td>
										</tr>
										<tr>
											<td class="color"># <input type="text" class="color-picker black" name="config_mobile_colors_header_text" size="6" value="<?php echo $config_mobile_colors_header_text; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
									</table>
									<br/><br/>
									<table>
										<tr>
											<td valign="top" height=30><strong><?php echo $entry_mobile_colors_primary_nav; ?></strong></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_background; ?></td>
										</tr>
										<tr>
											<td class="color"># <input type="text" class="color-picker" name="config_mobile_colors_primary_nav_background" size="6" value="<?php echo $config_mobile_colors_primary_nav_background; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_text; ?></td>
										</tr>
										<tr>
											<td class="color"># <input type="text" class="color-picker black" name="config_mobile_colors_primary_nav_text" size="6" value="<?php echo $config_mobile_colors_primary_nav_text; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
									</table>
									<br/><br/>
									<table>
										<tr>
											<td valign="top" height=30><strong><?php echo $entry_mobile_colors_secondary_nav; ?></strong></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_background; ?></td>
										</tr>
										<tr>
											<td class="color"># <input type="text" class="color-picker" name="config_mobile_colors_secondary_nav_background" size="6" value="<?php echo $config_mobile_colors_secondary_nav_background; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_text; ?></td>
										</tr>
										<tr>
											<td class="color"># <input type="text" class="color-picker black" name="config_mobile_colors_secondary_nav_text" size="6" value="<?php echo $config_mobile_colors_secondary_nav_text; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
									</table>
									<br/><br/>
									<table>
										<tr>
											<td valign="top" height=30><strong><?php echo $entry_mobile_colors_button; ?></strong></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_background; ?></td>
										</tr>
										<tr>
											<td class="color"># <input type="text" class="color-picker" name="config_mobile_colors_button_background" size="6" value="<?php echo $config_mobile_colors_button_background; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_text; ?></td>
										</tr>
										<tr>
											<td class="color"># <input type="text" class="color-picker black" name="config_mobile_colors_button_text" size="6" value="<?php echo $config_mobile_colors_button_text; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
									</table>
									<br/><br/>
									<table>
										<tr>
											<td valign="top" height=30><strong><?php echo $entry_mobile_colors_input; ?></strong></td>
										</tr>
										<tr>
											<td><?php echo $entry_mobile_colors_background; ?></td>
										</tr>
										<tr>
											<td class="color"># <input type="text" class="color-picker" name="config_mobile_colors_input_background" size="6" value="<?php echo $config_mobile_colors_input_background; ?>"><button type="button"><img src="view/image/omfa/eyedropper.png" /></button></td>
										</tr>
									</table>
									<br/>
								</td>
								<td class="screenshot-wrapper">
									<div id="screenshot"></div>
								</td>
							</tr>
							<tr>
								<td></td>
								<td style="border-top: 1px dotted #CCCCCC;">
									<a id="button-gen" class="button"><?php echo $button_generate;?></a><?php echo " $text_gen_instruction"; ?>
								</td>
							</tr>
						</table>
					</div>

					<div id="tab-omframework-patcher">
						<table class="form">
							<tr>
								<td valign=top><?php echo $entry_patches; ?></td>
								<td>
									<table>
										<?php foreach ($patches as $patch_sysname => $patch) { ?>
										<tr>
											<td style="width: 20px;">
												<input type="checkbox" name="patches[]" id="patch-<?php echo $patch_sysname; ?>" value="<?php echo $patch_sysname; ?>" <?php if (in_array($patch_sysname, $active_patches)) echo ' checked="checked"'; ?> />
											</td>
											<td valign=top><label for="patch-<?php echo $patch_sysname; ?>"><?php echo $patch['name']; ?></label></td>
										</tr>
										<?php } ?>
									</table>
								</td>
							</tr>
						</table>
					</div>

					<div id="tab-omframework-modules">
						<table class="form">
							<tr>
								<td valign=top><?php echo $entry_modules; ?></td>
								<td>
									<table>
										<?php foreach ($modules as $module_sysname => $module) { ?>
										<tr>
											<td style="width: 20px;">
												<input type="checkbox" name="config_modules_<?php echo $module_sysname; ?>" value="1" size="3" />
											</td>
											<td valign=top><label for="module-<?php echo $module_sysname; ?>"><?php echo $module['name']; ?></label></td>
										</tr>
										<?php } ?>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</form>
		</div>
	</div>

<link type="text/css" rel="stylesheet" href="view/javascript/colorpicker/jquery.miniColors.css" />
<script type="text/javascript" src="view/javascript/colorpicker/jquery.miniColors.js" ></script>
<script type="text/javascript" src="view/javascript/html2canvas/html2canvas.js" ></script>

<script type="text/javascript"><!--
$(document).ready( function() {
	// Enabling miniColors
	$('.color-picker').miniColors({
		letterCase: 'uppercase'
	});

	loadStoreSettings(<?php echo $store_id; ?>, '');
});

function image_upload(field, thumb) {
	$('#dialog').remove();

	$('#content').prepend('\
		<div id="dialog" style="padding: 3px 0px 0px 0px;">\
			<iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto">\
			</iframe>\
		</div>');

	$('#dialog').dialog({
		title: '<?php echo $text_image_manager; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).val()),
					<?php if (defined('VERSION') && (version_compare(VERSION, '1.5.2', '<') == true)) { ?>
					data: {
						image: $('#' + field).val()
					},
					type: 'POST',
					<?php } ?>
					dataType: 'text',
					beforeSend: function () {
						$('#' + thumb).attr('src','view/image/loading.gif');
					},
					success: function(data) {
						$('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
					}
				});
			}
		},
		bgiframe: false,
		width: 800,
		height: 400,
		resizable: false,
		modal: false
	});
}

function populate(json) {
	$('input[type=\'checkbox\']').not('[name="patches[]"]').removeAttr('checked');

	$.each(json, function(key, value) {
		if (key == 'mobile_logo') {
			$('#thumb-mobile-logo').attr('src',json['mobile_logo']);
		} else if (key != 'new_store' ) {
			var $formElement = $('[name="'+key+'"]');
			if ($formElement.get(0).tagName == 'INPUT')	{
				switch($formElement.attr("type")) {
					case "text" :
					case "hidden":
					  $formElement.attr("value", value);
				  break;

				  case "checkbox":
					  if(value > 0) $formElement.attr("checked","checked");
				  break;
				}
			} else if ($formElement.get(0).tagName == 'SELECT')	{
				var found = false;

				for (var i in $formElement.get(0).options) {
					if ($formElement.get(0).options[i].value == value) {
						$formElement.get(0).selectedIndex = i;
						found = true;
						break;
					}
				}

				if ( ! found && key == 'config_mobile_theme') {// if new theme is created, add it to the options and load it
					$formElement.append('<option selected="selected" value="'+value+'">'+value+'</option>');
					$formElement.trigger("change");
				}
			} else if ($formElement.get(0).tagName == 'TEXTAREA') {
				$formElement.val(value);
			}
		}
	});

	var theme = $('select[name=\'config_mobile_theme\']').attr('value');

	$('#mobile_template').load('index.php?route=setting/setting/template&token=<?php echo $token; ?>&template=' + encodeURIComponent(theme));

	if (theme.indexOf("-min") != -1) {
		$('[name=\'config_mobile_custom_gradients\']').parent().parent().hide();
		$('[name=\'config_mobile_custom_gradients\']').attr("disabled", "disabled");
	} else {
		$('[name=\'config_mobile_custom_gradients\']').removeAttr("disabled");
		$('[name=\'config_mobile_custom_gradients\']').parent().parent().show();
	}

	$('#screenshot').load('index.php?route=module/omfa/getScreenShot&token=<?php echo $token; ?>');

	$('.color-picker').each(function() {
		$(this).trigger('keyup.miniColors');
	});
}

function jsonify(formSet, mobile_logo) {
	var a = formSet.serializeArray()
	var json = {};

	for (var i in a) {
	if (a[i].name == 'patches[]') continue; // skip patches
		json[a[i].name] = a[i].value;
	}

	json['mobile_logo'] = mobile_logo;

	json['config_mobile_custom_styles'] = json['config_mobile_custom_styles'].replace(/[\r\n\t]/g,'');

	return json;
}

function loadStoreSettings(store_id, store_name) {
//console.log("loadStoreSettings store_id: "+ store_id +"\n store_name: "+ store_name);
	$.ajax({
		type:"GET",
		url: 'index.php?route=module/omfa/load&token=<?php echo $token; ?>&store_id=' + encodeURIComponent(store_id),
		dataType: 'json',
		beforeSend: function() {},
		success: function(json) {
			$('#dialog').dialog('close');
			$('#store_name').remove();
			$('#store_id').val(store_id);
			if (store_name != '') {
				$('h1').append('<span id="store_name">&ensp;&ndash;&ensp;' + store_name + '</span>');
			}
			
			populate(json);
		}
	});
}

function generateNewImage() {
	$('.error, .success').remove();
	$('.box').before('<div id="warning" class="attention"><img src="view/image/loading.gif" alt="" /> <?php echo "$text_generate"; ?></div>');
	window.location.hash = '';
	window.location.hash = '#warning';
	var homePage = window.open("<?php echo HTTP_CATALOG; ?>index.php?view=desktop");
	window.focus();
	if (homePage) {
		$(homePage.document).ready(function () {
			setTimeout(function () {
				if (!homePage.closed) {
					homePage.scrollTo(0,0);
					html2canvas(homePage.document.body, {
		    			onrendered: function(canvas) {
		    				$.ajax({
		    					type: "post",
		    					url: "index.php?route=module/omfa/saveScreenShot&token=<?php echo $token; ?>",
		    					dataType: 'json',
		    					data: { data: canvas.toDataURL("image/png") },
								success: function(json) {
									if (json.success) {
										homePage.close();
										$('#screenshot').load('index.php?route=module/omfa/getScreenShot&token=<?php echo $token; ?>');
										$('.attention').remove();
										$('.box').before('<div class="success" style="display: none;">' + json['success'] + '</div>');
										$('.success').fadeIn('slow');
										$('.error').remove();
									} else if (json.error) {
										$('.attention, .error').remove();
										$('.box').before('<div class="warning error" style="display: none;">' + json['error'] + '<?php echo " $text_gen_instruction"; ?></div>');
										$('.error').fadeIn('slow');
									}
								},
								complete: function() {
									setTimeout(function () {
										$('.success').fadeOut('slow').remove();
									}, 8000);
								},
								error: function(xhr, ajaxOptions, thrownError) {
									alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
								}
							});
		    			}
					});
				} else {
					$('.attention, .error').remove();
					$('.box').before('<div class="warning error" style="display: none;"><?php echo "$error_gen $text_gen_instruction"; ?></div>');
					$('.error').fadeIn('slow');
				}
			}, 10000);
		});
	} <?php /* else {
		$('.attention, .error').remove();
		$('.box').before('<div class="warning error"><?php echo $error_popups; ?></div>');
		$('.error').fadeIn('slow');
	}*/	?>
}

$('#button-save').click(function() {

	$.ajax({
		type:"POST",
		url: 'index.php?route=module/omfa/save&token=<?php echo $token; ?>&store_id='+ encodeURIComponent($("#store_id").val()),
		dataType: 'json',
		data: $('input[type=\'text\'][disabled!=\'disabled\'], input[type=\'hidden\'], input[type=\'checkbox\']:checked, select, textarea, .image img.attr(\'src\')'),
		beforeSend: function() {
			$('.box').before('<div class="attention"><img src="view/image/loading.gif" alt="" /></div>');
		},
		success: function(json) {
			if (json.success) {
				$('.attention').remove();
				$('.box').before('<div class="success" style="display: none;">' + json['success'] + '</div>');
				$('.success').fadeIn('slow');
				$('.error').remove();
			} else if(json.error) {
				$('.attention, .error').remove();
				$('.box').before('<div class="warning error" style="display: none;">' + json['error'] + '</div>');
				$('.error').fadeIn('slow');
			}
		},
		complete: function() {
			setTimeout(function () {
				$('.success').fadeOut('slow').remove();
			}, 8000);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#screenshot').on('click', '.button', generateNewImage);

$('#button-gen').click(generateNewImage);

$(".color").click(function() {
	$(".color").each(function() {
		$(this).removeClass("active");
	});
	$(this).addClass("active");
});

$(".color-picker").miniColors({
	change: function(hex) {
		$('[name=' + $(this).attr('name') + ']').miniColors('value', [hex]);
	}
});

$("#screenshot").on("mousedown", "img", function(event) {
	getColor(event,	this, $(".active .color-picker"), 1);
});

$('#tabs_omframework a').tabs();

$('[name=\'config_mobile_theme\']').change(function() {

	$('#mobile_template').load('index.php?route=setting/setting/template&token=<?php echo $token; ?>&template=' + encodeURIComponent(this.value));

	if (this.value.indexOf("-min") != -1) {
		$('[name=\'config_mobile_custom_gradients\']').parent().parent().hide();
		$('[name=\'config_mobile_custom_gradients\']').attr("disabled", "disabled");
	} else {
		$('[name=\'config_mobile_custom_gradients\']').removeAttr("disabled");
		$('[name=\'config_mobile_custom_gradients\']').parent().parent().show();
	}

	$.ajax({
		type:"GET",
		url: 'index.php?route=module/omfa/loadTheme&token=<?php echo $token; ?>&theme=' + encodeURIComponent(this.value),
		dataType: 'json',
		success: function(json) {
			$('input[type=\'checkbox\'][name^="config_mobile_custom_"]').removeAttr('checked');
			$.each(json, function(key, value) {
				var $formElement = $('[name="'+key+'"]');
				if ($formElement.get(0).tagName == 'INPUT')
				{
					switch($formElement.attr("type")) {
						case "text" :
						$formElement.attr("value", value);
						break;
						case "checkbox":
						$formElement.attr("checked","checked");
						break;
					}
				} else if ($formElement.get(0).tagName == 'TEXTAREA') {
					$formElement.val(value);
				}
			});

			$('.color-picker').each(function() {
				$(this).trigger('keyup.miniColors');
			});
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

<?php if (defined('VERSION') && (version_compare(VERSION, '1.5.2', '<') == true)) { ?>
$("a.button").wrapInner("<span>"); // fix for v. 1.5.1 buttons
<?php } ?>
--></script>

<script type="text/javascript"><!--

/* ColorPicker 0.9 - ColorPicking on images */

function getColor(e,img,resObj,range,scriptPath) {
  var req = false;
  if (window.XMLHttpRequest) req = new XMLHttpRequest();
  else if (window.ActiveXObject) {
    try { req = new ActiveXObject('Msxml2.XMLHTTP');}
    catch (e) {
      try { req = new ActiveXObject('Microsoft.XMLHTTP');}
      catch (e) {}
    }
  }
  if (!req) return false;
  else {
    req.open('GET',(scriptPath || '')+
                   'index.php?route=module/omfa/getColor&token=<?php echo $token; ?>&coords='+getMousePos(e,getOrigin(img))+
                   '&range='+range+'&img='+img.src, true);
    req.setRequestHeader('X-Referer', document.location);
    req.send(null);
    req.onreadystatechange = function(){
      if(req.readyState == 4) if(req.status == 200) {
        //resObj.value = req.responseText;
        //resObj.style.backgroundColor = req.responseText;
        var res = req.responseText;
        res = res.toUpperCase();
        resObj.miniColors('value', res);
      }
    };
  }
}
  

function getMousePos (e,relXY) {
  if(!e) var e = window.event;
  if(typeof e.pageX === 'number') return [e.pageX - relXY[0], e.pageY - relXY[1]];
  else return [e.clientX + document.body.scrollLeft +
               document.documentElement.scrollLeft - relXY[0],
               e.clientY + document.body.scrollTop +
               document.documentElement.scrollTop - relXY[1]];
}
  
function getOrigin(obj) {
  var parent=box=null, pos=[];
  
  this.getStyle = function (prop) {
    if (obj.currentStyle) return obj.currentStyle[prop];
    else if (window.getComputedStyle) return
        document.defaultView.getComputedStyle(obj,null).getPropertyValue(prop);
  }
  
  if (obj.parentNode === null || this.getStyle('display') == 'none') return false;
  if (obj.getBoundingClientRect) { // IE
    box = obj.getBoundingClientRect();
    var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
    var scrollLeft = document.documentElement.scrollLeft
                     || document.body.scrollLeft;
  
    return [Math.round(box.left) + scrollLeft, Math.round(box.top) + scrollTop];
  } else if (document.getBoxObjectFor) { // gecko
    box = document.getBoxObjectFor(obj);
    pos = [box.x, box.y];
  } else { // safari/opera
    pos = [obj.offsetLeft, obj.offsetTop];
    parent = obj.offsetParent;
    if (parent != obj) {
      while (parent) {
        pos[0] += parent.offsetLeft;
        pos[1] += parent.offsetTop;
        parent = parent.offsetParent;
      }
    }
    var ua = navigator.userAgent.toLowerCase();
    if (ua.indexOf('opera') != -1 ||
       (ua.indexOf('safari') != -1 &&
        this.getStyle('position') == 'absolute')) {
          pos[1] -= document.body.offsetTop;
    };
  }
  if (obj.parentNode) parent = obj.parentNode;
  else parent = null;
  while (parent && parent.tagName != 'BODY' && parent.tagName != 'HTML') {
    pos[0] -= parent.scrollLeft;
    pos[1] -= parent.scrollTop;
    if (parent.parentNode) parent = parent.parentNode;
    else parent = null;
  }
  return pos;
}
--></script>

<style>
	.active {
		background: #BABABA;
		padding-left: 2px;
		padding-bottom: 3px;
	}

	#tab-omframework-adv-customization .eyedropper table {
		width: 140px;
	}

	.screenshot-wrapper {
		position: relative;
	}

	#screenshot {
		max-width: 98%;
		max-height: 98%;
		overflow: auto;
		position: absolute;
		top: 10px;
		left: 10px;
	}

	@-moz-document url-prefix() {
	    .screenshot-wrapper {
	    	position: absolute;
	    	width: 75%;
	    	height: 100%;
	    	border: 0 !important;
	    }

	    #screenshot {
			width: auto;
			position: static;
			max-height: 198%;
		}
	}

	#screenshot img {
	 	cursor:url(view/image/omfa/eyedropper-cursor.png), auto;
	 	border: 1px solid #EEEEEE;
	}

	#screenshot div {
		 margin-top: 10px;
	}

	button {
		cursor: pointer;
		width: 24px;
		padding-top: 3px;
		padding-bottom: 0px;
		padding-left: 4px;
		position: relative;
		top: 3px;
	}
</style>

<?php echo $footer; ?>