<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<style type="text/css">
button { margin-left: 10px; }
.btn-no-small { -moz-box-shadow:inset 0px 1px 0px 0px #ffffff; -webkit-box-shadow:inset 0px 1px 0px 0px #ffffff; box-shadow:inset 0px 1px 0px 0px #ffffff; background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ededed), color-stop(1, #dfdfdf) ); background:-moz-linear-gradient( center top, #ededed 5%, #dfdfdf 100% ); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ededed', endColorstr='#dfdfdf'); background-color:#ededed; -moz-border-radius:4px; -webkit-border-radius:4px; border-radius:4px; border:1px solid #dcdcdc; display:inline-block; color:#777777; font-family:arial; font-size:12px; font-weight:normal; padding:0px 7px; text-decoration:none; text-shadow:1px 1px 0px #ffffff; }
.btn-no-small:hover { background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #dfdfdf), color-stop(1, #ededed) ); background:-moz-linear-gradient( center top, #dfdfdf 5%, #ededed 100% ); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#dfdfdf', endColorstr='#ededed'); background-color:#dfdfdf; }
.btn-no-small:active { position:relative; top:1px; }


.btn-install-small { -moz-box-shadow:inset 0px 1px 0px 0px #a4e271; -webkit-box-shadow:inset 0px 1px 0px 0px #a4e271; box-shadow:inset 0px 1px 0px 0px #a4e271; background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #89c403), color-stop(1, #77a809) ); background:-moz-linear-gradient( center top, #89c403 5%, #77a809 100% ); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#89c403', endColorstr='#77a809'); background-color:#89c403; -moz-border-radius:4px; -webkit-border-radius:4px; border-radius:4px; border:1px solid #74b807; display:inline-block; color:#ffffff; font-family:arial; font-size:12px; font-weight:normal; padding:0px 7px; text-decoration:none; text-shadow:1px 1px 0px #528009; }
.btn-install-small:hover { cursor: pointer; background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #77a809), color-stop(1, #89c403) ); background:-moz-linear-gradient( center top, #77a809 5%, #89c403 100% ); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#77a809', endColorstr='#89c403'); background-color:#77a809; }
.btn-install-small:active { cursor: pointer; position:relative; top:1px; }

.btn-disable-small { -moz-box-shadow:inset 0px 1px 0px 0px #f5978e; -webkit-box-shadow:inset 0px 1px 0px 0px #f5978e; box-shadow:inset 0px 1px 0px 0px #f5978e; background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #f24537), color-stop(1, #c62d1f) ); background:-moz-linear-gradient( center top, #f24537 5%, #c62d1f 100% ); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#f24537', endColorstr='#c62d1f'); background-color:#f24537; -moz-border-radius:4px; -webkit-border-radius:4px; border-radius:4px; border:1px solid #d02718; display:inline-block; color:#ffffff; font-family:arial; font-size:12px; font-weight:normal; padding:0px 7px; text-decoration:none; text-shadow:1px 1px 0px #810e05; }
.btn-disable-small:hover { cursor: pointer; background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #c62d1f), color-stop(1, #f24537) ); background:-moz-linear-gradient( center top, #c62d1f 5%, #f24537 100% ); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#c62d1f', endColorstr='#f24537'); background-color:#c62d1f; }
.btn-disable-small:active { cursor: pointer; position:relative; top:1px; }

.btn-advanced { -moz-box-shadow:inset 0px 1px 0px 0px #ffffff; -webkit-box-shadow:inset 0px 1px 0px 0px #ffffff; box-shadow:inset 0px 1px 0px 0px #ffffff; background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ededed), color-stop(1, #dfdfdf) ); background:-moz-linear-gradient( center top, #ededed 5%, #dfdfdf 100% ); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ededed', endColorstr='#dfdfdf'); background-color:#ededed; -moz-border-radius:3px; -webkit-border-radius:3px; border-radius:3px; border:1px solid #dcdcdc; display:inline-block; color:#777777; font-family:arial; font-size:11px; font-weight:bold; padding:6px 12px; text-decoration:none; text-shadow:1px 1px 0px #ffffff; }
.btn-advanced:hover { cursor: pointer; background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #dfdfdf), color-stop(1, #ededed) ); background:-moz-linear-gradient( center top, #dfdfdf 5%, #ededed 100% ); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#dfdfdf', endColorstr='#ededed'); background-color:#dfdfdf; }
.btn-advanced:active { cursor: pointer; position:relative; top:1px; }

.bundletext { float: left; width: 630px; }
.bundleimg { float:left; padding-left:15px; padding-top: 5px; width: 230px;}
.plugin { border-bottom: 1px dotted #000000; padding: 3px 5px; line-height:20px; font-weight:bold; }
.plugin div span { margin-left: 15px; }
.bundletext .last { border-bottom: 0px; }

.shadow { -moz-box-shadow: 3px 3px 4px #999; -webkit-box-shadow: 3px 3px 4px #999; box-shadow: 3px 3px 4px #999; -ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#999999')"; : progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#999999'); }

.onoffswitch { position: relative; width: 60px; -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none; }
.onoffswitch-checkbox { display: none; }
.onoffswitch-label { display: block; overflow: hidden; cursor: pointer; border: 2px solid #999999; border-radius: 12px; }
.onoffswitch-inner { display: block; width: 200%; margin-left: -100%; -moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s; -o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s; }
.onoffswitch-inner:before, .onoffswitch-inner:after { display: block; float: left; width: 50%; height: 20px; padding: 0; line-height: 20px; font-size: 11px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; }
.onoffswitch-inner:before { content: "ON"; padding-left: 11px; background-color: #2FCCFF; color: #FFFFFF; }
.onoffswitch-inner:after { content: "OFF"; padding-right: 11px; background-color: #EEEEEE; color: #999999; text-align: right; }
.onoffswitch-switch { display: block; width: 9px; margin: 5.5px; background: #FFFFFF; border: 2px solid #999999; border-radius: 12px; position: absolute; top: 0; bottom: 0; right: 36px; -moz-transition: all 0.3s ease-in 0s; -webkit-transition: all 0.3s ease-in 0s; -o-transition: all 0.3s ease-in 0s; transition: all 0.3s ease-in 0s; }
.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner { margin-left: 0; }
.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch { right: 0px; }

</style>
<script type="text/javascript">
	function feature_toggle(feature) {
		$('#container_' + $(feature).data('vqmod')).hide();
		$('#loading_' + $(feature).data('vqmod')).fadeIn('slow');

		$.ajax({
			url: '<?php echo HTTPS_SERVER; ?>index.php?route=<?php echo OPTIMIZATION_PREFIX; ?>/settings/feature&token=<?php echo $token; ?>',
			type: 'POST',
			dataType: 'JSON',
			data: 'vqmod=' + $(feature).data('vqmod') + '&token=<?php echo $token; ?>',
			success: function(json) {
				$('#loading_' + $(feature).data('vqmod')).hide();
				$('#container_' + $(feature).data('vqmod')).fadeIn('slow');
			},
			error: function(xhr, status, error) {
				$('#loading_' + $(feature).data('vqmod')).hide();
				$('#container_' + $(feature).data('vqmod')).fadeIn('slow');
				$('#notification').html('<div class="warning" style="display: none;">' + json['error']['message'] + '</div>');
				$('.warning').fadeIn('slow');
				$('html, body').animate({ scrollTop: 0 }, 'slow');
				$('.warning').delay(1500).fadeOut('slow');
			}
		});
	
	}
</script>
<?php $vqmod_path = DIR_SYSTEM . '../wx/core/'; ?>
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-setting" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
				<h1><?php echo $heading_title; ?></h1>
				<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
				</ul>
		</div>
	</div>
	<div class="container-fluid">
		<?php if ($error_warning) { ?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<?php if ($success) { ?>
		<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
    
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-setting" class="form-horizontal">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab-ips" data-toggle="tab"><?php echo $tab_ips; ?></a></li>
						<li><a href="#tab-analyzer" data-toggle="tab"><?php echo $tab_analyzer; ?></a></li>
						
						<li><a href="#tab-cache" data-toggle="tab"><?php echo $tab_cache; ?></a></li>
						<?php if (file_exists(DIR_TEMPLATE . OPTIMIZATION_PREFIX . "/advanced_search.tpl")) { ?>
						<li><a href="#tab-search" data-toggle="tab"><?php echo $tab_search; ?></a></li>
						<?php } ?>
						<li><a href="#tab-cdn" data-toggle="tab"><?php echo $tab_cdn; ?></a></li>
						<?php if (file_exists(DIR_TEMPLATE . OPTIMIZATION_PREFIX . "/bundled_features.tpl")) { ?>
						<li><a href="#tab-bundles" data-toggle="tab"><?php echo $tab_bundles; ?></a></li>
						<?php } ?>
						<li><a href="#tab-changelog" data-toggle="tab"><?php echo $tab_changelog; ?></a></li>
						<li<?php echo (!$new_optimizer_version) ? ' style="display:none;"' : ''; ?>><a href="#tab-update" data-toggle="tab"><?php echo $tab_update; ?></a></li>
					</ul>

				<div class="tab-content">

					<div class="tab-pane active" id="tab-ips">
					
						<fieldset>
							<legend><?php echo $heading_minify_javascript; ?></legend>
						
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input-name"><?php echo $entry_minify_javascript; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" name="optimizer_minify_javascript" value="1" <?php echo ($optimizer_minify_javascript) ? 'checked="checked"' : ''; ?> /> <?php echo $text_yes; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="optimizer_minify_javascript" value="0" <?php echo (!$optimizer_minify_javascript) ? 'checked="checked"' : ''; ?> /> <?php echo $text_no; ?>
									</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input-name"><?php echo $entry_javascript_jquery; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" onchange="jquery_version_display_toggle();" name="optimizer_javascript_jquery" value="1" <?php echo ($optimizer_javascript_jquery) ? 'checked="checked"' : ''; ?> /> <?php echo $text_yes; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" onchange="jquery_version_display_toggle();" name="optimizer_javascript_jquery" value="0" <?php echo (!$optimizer_javascript_jquery) ? 'checked="checked"' : ''; ?> /> <?php echo $text_no; ?>
									</label>
								</div>
							</div>
							<div class="form-group" id="optimizer_javascript_jquery_version" style="display:none;">
								<label class="col-sm-2 control-label" for="input-name"><?php echo $entry_javascript_jquery_version; ?></label>
								<div class="col-sm-10">
									<input type="text" name="optimizer_javascript_jquery_version" value="<?php echo $optimizer_javascript_jquery_version; ?>" class="form-control" />
								</div>
							</div>			
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input-name"><?php echo $entry_javascript_jqueryui; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" onchange="jqueryui_version_display_toggle();" name="optimizer_javascript_jqueryui" value="1" <?php echo ($optimizer_javascript_jqueryui) ? 'checked="checked"' : ''; ?> /> <?php echo $text_yes; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" onchange="jqueryui_version_display_toggle();" name="optimizer_javascript_jqueryui" value="0" <?php echo (!$optimizer_javascript_jqueryui) ? 'checked="checked"' : ''; ?> /> <?php echo $text_no; ?>
									</label>
								</div>
							</div>
							<div class="form-group" id="optimizer_javascript_jqueryui_version" style="display:none;">
								<label class="col-sm-2 control-label" for="input-name"><?php echo $entry_javascript_jquery_version; ?></label>
								<div class="col-sm-10">
									<input type="text" name="optimizer_javascript_jqueryui_version" value="<?php echo $optimizer_javascript_jqueryui_version; ?>" class="form-control" />
								</div>
							</div>			
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input-name"><?php echo $entry_javascript_defer; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" name="optimizer_javascript_defer" value="1" <?php echo ($optimizer_javascript_defer) ? 'checked="checked"' : ''; ?> /> <?php echo $text_yes; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="optimizer_javascript_defer" value="0" <?php echo (!$optimizer_javascript_defer) ? 'checked="checked"' : ''; ?> /> <?php echo $text_no; ?>
									</label>
								</div>
							</div>
							
							<script type="text/javascript"><!--
								function jqueryui_version_display_toggle() {
									if ($('input:radio[name=optimizer_javascript_jqueryui]:checked').val() == 1) {
										$('#optimizer_javascript_jqueryui_version').show();
									} else {
										$('#optimizer_javascript_jqueryui_version').hide();
									}
									
								}
								function jquery_version_display_toggle() {
									if ($('input:radio[name=optimizer_javascript_jquery]:checked').val() == 1) {
										$('#optimizer_javascript_jquery_version').show();
									} else {
										$('#optimizer_javascript_jquery_version').hide();
									}
									
								}
								jqueryui_version_display_toggle();
								jquery_version_display_toggle();
							// --></script>
						</fieldset>
			
						<fieldset>
							<legend><?php echo $heading_minify_css; ?></legend>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_minify_css"><?php echo $entry_minify_css; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" name="optimizer_minify_css" value="1" <?php echo ($optimizer_minify_css) ? 'checked="checked"' : ''; ?> /> <?php echo $text_yes; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="optimizer_minify_css" value="0" <?php echo (!$optimizer_minify_css) ? 'checked="checked"' : ''; ?> /> <?php echo $text_no; ?>
									</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_minify_encode_images"><?php echo $entry_minify_encode_images; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" name="optimizer_minify_encode_images" value="1" <?php echo ($optimizer_minify_encode_images) ? 'checked="checked"' : ''; ?> /> <?php echo $text_yes; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="optimizer_minify_encode_images" value="0" <?php echo (!$optimizer_minify_encode_images) ? 'checked="checked"' : ''; ?> /> <?php echo $text_no; ?>
									</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_minify_image_size"><?php echo $entry_minify_image_size; ?></label>
								<div class="col-sm-10">
									<select name="optimizer_minify_image_size">
										<?php for ($i = 1; $i <= 10; $i++) { ?>
										<option value="<?php echo ($i * 1024); ?>" <?php echo (($i * 1024) == $optimizer_minify_image_size) ? 'selected="selected"' : ''; ?>><?php echo $i; ?> KB<?php echo ($i == 3) ? ' ' . $text_default : ''; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_minify_image_occurs"><?php echo $entry_minify_image_occurs; ?></label>
								<div class="col-sm-10">
									<select name="optimizer_minify_image_occurs">
										<?php for ($i = 1; $i <= 10; $i++) { ?>
										<option value="<?php echo $i; ?>" <?php echo ($i == $optimizer_minify_image_occurs) ? 'selected="selected"' : ''; ?>><?php echo $i; ?> <?php echo ($i == 1) ? $text_time : $text_times; ?><?php echo ($i == 3) ? ' ' . $text_default : ''; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</fieldset>
			
			
						<fieldset>
							<legend><?php echo $heading_minify_settings; ?></legend>
							
							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_seo_cache"><?php echo $entry_minify_html; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" name="optimizer_minify_html" value="1" <?php echo ($optimizer_minify_html) ? 'checked="checked"' : ''; ?> /> <?php echo $text_yes; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="optimizer_minify_html" value="0" <?php echo (!$optimizer_minify_html) ? 'checked="checked"' : ''; ?> /> <?php echo $text_no; ?>
									</label>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_minify_encode_url"><?php echo $entry_minify_encode_url; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" name="optimizer_minify_encode_url" value="1" <?php echo ($optimizer_minify_encode_url) ? 'checked="checked"' : ''; ?> /> <?php echo $text_yes; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="optimizer_minify_encode_url" value="0" <?php echo (!$optimizer_minify_encode_url) ? 'checked="checked"' : ''; ?> /> <?php echo $text_no; ?>
									</label>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_minify_storage"><?php echo $entry_minify_storage; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" name="optimizer_minify_storage" value="1" <?php echo ($optimizer_minify_storage) ? 'checked="checked"' : ''; ?> /> <?php echo $text_in_memory_apc; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="optimizer_minify_storage" value="0" <?php echo (!$optimizer_minify_storage) ? 'checked="checked"' : ''; ?> /> <?php echo $text_file_system; ?>
									</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_minify_advanced"><?php echo $entry_minify_advanced; ?></label>
								<div class="col-sm-10">
									<span  class="btn-advanced" onclick="$('#optimize-advanced').slideToggle('slow'); if ($(this).html() == '<?php echo $text_show_advanced; ?>') { $(this).html('<?php echo $text_hide_advanced; ?>'); } else { $(this).html('<?php echo $text_show_advanced; ?>'); }"><?php echo $text_show_advanced; ?></span>
									<div id="optimize-advanced" style="display:none;">
										<div class="form-group">
											<label class="col-sm-2 control-label" for="optimizer_minify_logging"><?php echo $entry_minify_logging; ?></label>
											<div class="col-sm-10">
												<label class="radio-inline">
													<input type="radio" name="optimizer_minify_logging" value="1" <?php echo ($optimizer_minify_logging) ? 'checked="checked"' : ''; ?> /> <?php echo $text_on; ?>
												</label>
												<label class="radio-inline">
													<input type="radio" name="optimizer_minify_logging" value="0" <?php echo (!$optimizer_minify_logging) ? 'checked="checked"' : ''; ?> /> <?php echo $text_off . ' ' . $text_default; ?>
												</label>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label" for="optimizer_minify_debug_mode"><?php echo $entry_minify_debug_mode; ?></label>
											<div class="col-sm-10">
												<label class="radio-inline">
													<input type="radio" name="optimizer_minify_debug_mode" value="1" <?php echo ($optimizer_minify_debug_mode) ? 'checked="checked"' : ''; ?> /> <?php echo $text_on; ?>
												</label>
												<label class="radio-inline">
													<input type="radio" name="optimizer_minify_debug_mode" value="0" <?php echo (!$optimizer_minify_debug_mode) ? 'checked="checked"' : ''; ?> /> <?php echo $text_off . ' ' . $text_default; ?>
												</label>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label" for="optimizer_minify_max_age"><?php echo $entry_minify_max_age; ?></label>
											<div class="col-sm-10">
												<select name="optimizer_minify_max_age">
													<?php for ($i = 1; $i <= 100; $i++) { ?>
													<option value="<?php echo ($i * 86400); ?>" <?php echo (($i * 86400) == $optimizer_minify_max_age) ? 'selected="selected"' : ''; ?>><?php echo $i; ?> <?php echo ($i == 1) ? $text_day : $text_days; ?><?php echo ($i == 10) ? ' ' . $text_default : ''; ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label" for="optimizer_minify_file_path"><?php echo $entry_minify_file_path; ?></label>
											<div class="col-sm-10">
												<input type="text" name="optimizer_minify_file_path" value="<?php echo $optimizer_minify_file_path; ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label" for="optimizer_minify_file_locking"><?php echo $entry_minify_file_locking; ?></label>
											<div class="col-sm-10">
												<label class="radio-inline">
													<input type="radio" name="optimizer_minify_file_locking" value="1" <?php echo ($optimizer_minify_file_locking) ? 'checked="checked"' : ''; ?> /> <?php echo $text_on . ' ' . $text_default; ?>
												</label>
												<label class="radio-inline">
													<input type="radio" name="optimizer_minify_file_locking" value="0" <?php echo (!$optimizer_minify_file_locking) ? 'checked="checked"' : ''; ?> /> <?php echo $text_off; ?>
												</label>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label" for="optimizer_js_excludes"><?php echo $entry_js_excludes; ?></label>
											<div class="col-sm-10">
												<textarea name="optimizer_js_excludes" cols="40"><?php echo $optimizer_js_excludes; ?></textarea>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label" for="optimizer_css_excludes"><?php echo $entry_css_excludes; ?></label>
											<div class="col-sm-10">
												<textarea name="optimizer_css_excludes" cols="40"><?php echo $optimizer_css_excludes; ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
						</fieldset>

						<fieldset>
							<legend><?php echo $heading_data_caching; ?></legend>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_category_counts"><?php echo $entry_category_counts; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" name="optimizer_category_counts" value="1" <?php echo ($optimizer_category_counts) ? 'checked="checked"' : ''; ?> /> <?php echo $text_enabled; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="optimizer_category_counts" value="0" <?php echo (!$optimizer_category_counts) ? 'checked="checked"' : ''; ?> /> <?php echo $text_disabled; ?>
									</label>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label" for="cron_status"><?php echo $entry_cron_status; ?></label>
								<div class="col-sm-10">
									<span class="help" id="cron_status"><?php echo $optimizer_cron_status; ?></span><input type="hidden" name="optimizer_cron_status" value="<?php echo $optimizer_cron_status; ?>" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_memory_cache"><?php echo $entry_memory_cache; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" name="optimizer_memory_cache" value="1" <?php echo ($optimizer_memory_cache) ? 'checked="checked"' : ''; ?> /> <?php echo $text_in_memory_apc; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="optimizer_memory_cache" value="0" <?php echo (!$optimizer_memory_cache) ? 'checked="checked"' : ''; ?> /> <?php echo $text_file_system; ?>
									</label>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label" for="category_module"><?php echo $entry_sc_category_module; ?></label>
								<div class="col-sm-10">
									<div class="onoffswitch" id="container_sc_category_module">
										<input type="checkbox" name="sc_category_module" class="onoffswitch-checkbox" id="sc_category_module" onclick="feature_toggle(this);" data-vqmod="sc_category_module" <?php echo ($features['sc_category_module']) ? "checked" : ""; ?>>
										<label class="onoffswitch-label" for="sc_category_module">
											<span class="onoffswitch-inner"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>
									<div id="loading_sc_category_module" style="display:none;"><img src="data:image/gif;base64,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" /></div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_seo_cache"><?php echo $entry_sc_header_navigation; ?></label>
								<div class="col-sm-10">
									<div class="onoffswitch" id="container_sc_navigation">
										<input type="checkbox" name="sc_navigation" class="onoffswitch-checkbox" id="navigation" onclick="feature_toggle(this);" data-vqmod="sc_navigation" <?php echo ($features['sc_navigation']) ? "checked" : ""; ?>>
										<label class="onoffswitch-label" for="sc_navigation">
											<span class="onoffswitch-inner"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>
									<div id="loading_sc_navigation" style="display:none;"><img src="data:image/gif;base64,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" /></div>
								</div>
							</div>
						</fieldset>

					</div>
					
					<div class="tab-pane" id="tab-analyzer">
						<fieldset>
							<div class="form-group" style="margin-left: 30px; max-width: 1000px;">
							<p>
							The performance analyzer allows you to review a sub-render breakdown (of widgets / modules / extensions / etc), that are being included on page.  
							Each stating their own render times in milliseconds and number of database queries executed.  This data can be extremly useful for web owners &amp; developers
							to determine where the performance caveats exist on their website.  When used in combination with our block level caching system that is able to cache and re-serve
							these sub-renders you quickly end up with a page that performs a great deal faster.  That said, some general knowledge and common sense is required on what can or should 
							be cached; By example the "common/header" render will contain important page data like page title, meta data, shopping cart, (and so on), and as such, cannot be
							cached page to page;  On the otherhand, something like "module/featured" that is commonly used on the main index page, often can be. <br />
							</p><p>
							The performance analyzer included uses FirePHP to display information into the development console of your web browser.  In Chrome this will require the FirePHP4Chrome Extension
							in Firefox it requires the FireBug extension, and FirePHP extension.
							</p>
							<p>
								Related links for browser toolkits:<br />
								<a href="http://getfirebug.com" target="_blank"><img width="160" src="http://www.hunterbm.com/assets/increase_page_speed/firebug.png"></a>
								<a href="http://www.firephp.org" target="_blank"><img style="margin-left: 30px;" width="160" src="http://www.hunterbm.com/assets/increase_page_speed/firephp.png"></a>
								<a href="https://chrome.google.com/webstore/detail/firephp4chrome/gpgbmonepdpnacijbbdijfbecmgoojma" target="_blank"><img style="margin-left: 30px;" width="160" src="http://www.hunterbm.com/assets/increase_page_speed/firephp_chrome.png"></a>
								<br />
							</p>
							<p>
								Video Intro on Performance Analyzer:<br />
								<iframe width="640" height="360" src="https://www.youtube.com/embed/W8N4LKQxPvY?rel=0" frameborder="0" allowfullscreen></iframe>
							</p>							
							</div>
							<!-- <legend><?php echo $heading_speed_analyzer ?></legend> -->
							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_performance_analyzer"><?php echo $entry_firebug_analyzer; ?></label>
								<div class="col-sm-10">
									<div class="onoffswitch" id="container_optimizer_performance_analyzer">
										<input type="checkbox" name="optimizer_performance_analyzer" class="onoffswitch-checkbox" id="optimizer_performance_analyzer" onclick="feature_toggle(this);" data-vqmod="performance_analyzer" <?php echo ($features['performance_analyzer']) ? "checked" : ""; ?>>
										<label class="onoffswitch-label" for="optimizer_performance_analyzer">
											<span class="onoffswitch-inner"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>
									<div id="loading_optimizer_performance_analyzer" style="display:none;"><img src="data:image/gif;base64,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" /></div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_firebug_queries"><?php echo $entry_firebug_queries; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" name="optimizer_firebug_queries" value="1" <?php echo ($optimizer_firebug_queries) ? 'checked="checked"' : ''; ?> /> <?php echo $text_enabled; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="optimizer_firebug_queries" value="0" <?php echo (!$optimizer_firebug_queries) ? 'checked="checked"' : ''; ?> /> <?php echo $text_disabled; ?>
									</label>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_firebug_admin"><?php echo $entry_firebug_admin; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" name="optimizer_firebug_admin" value="1" <?php echo ($optimizer_firebug_admin) ? 'checked="checked"' : ''; ?> /> <?php echo $text_enabled; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="optimizer_firebug_admin" value="0" <?php echo (!$optimizer_firebug_admin) ? 'checked="checked"' : ''; ?> /> <?php echo $text_disabled; ?>
									</label>
								</div>
							</div>
						</fieldset>
					
					</div>
					
					<div class="tab-pane" id="tab-cache">
						
						<?php 
						if (OPTIMIZATION_PREFIX == 'wx' && file_exists(DIR_TEMPLATE . OPTIMIZATION_PREFIX . "/page_caching.tpl")) {
							include_once(DIR_TEMPLATE . OPTIMIZATION_PREFIX . "/page_caching.tpl");
						} ?>						
						<fieldset>
							<legend><?php echo $heading_sql_caching; ?></legend>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="category_module"><?php echo $entry_query_caching_category; ?></label>
								<div class="col-sm-10">
									<div class="onoffswitch" id="container_query_caching_category">
										<input type="checkbox" name="query_caching_category" class="onoffswitch-checkbox" id="query_caching_category" onclick="feature_toggle(this);" data-vqmod="query_caching_category" <?php echo ($features['query_caching_category']) ? "checked" : ""; ?>>
										<label class="onoffswitch-label" for="query_caching_category">
											<span class="onoffswitch-inner"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>
									<div id="loading_query_caching_category" style="display:none;"><img src="data:image/gif;base64,R0lGODlhgAAPAPIAAP///wAAAMbGxrKyskJCQgAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAgAAPAAAD5wiyC/6sPRfFpPGqfKv2HTeBowiZGLORq1lJqfuW7Gud9YzLud3zQNVOGCO2jDZaEHZk+nRFJ7R5i1apSuQ0OZT+nleuNetdhrfob1kLXrvPariZLGfPuz66Hr8f8/9+gVh4YoOChYhpd4eKdgwDkJEDE5KRlJWTD5iZDpuXlZ+SoZaamKOQp5wAm56loK6isKSdprKotqqttK+7sb2zq6y8wcO6xL7HwMbLtb+3zrnNycKp1bjW0NjT0cXSzMLK3uLd5Mjf5uPo5eDa5+Hrz9vt6e/qosO/GvjJ+sj5F/sC+uMHcCCoBAAh+QQJCgAAACwAAAAAgAAPAAAD/wi0C/4ixgeloM5erDHonOWBFFlJoxiiTFtqWwa/Jhx/86nKdc7vuJ6mxaABbUaUTvljBo++pxO5nFQFxMY1aW12pV+q9yYGk6NlW5bAPQuh7yl6Hg/TLeu2fssf7/19Zn9meYFpd3J1bnCMiY0RhYCSgoaIdoqDhxoFnJ0FFAOhogOgo6GlpqijqqKspw+mrw6xpLCxrrWzsZ6duL62qcCrwq3EsgC0v7rBy8PNorycysi3xrnUzNjO2sXPx8nW07TRn+Hm3tfg6OLV6+fc37vR7Nnq8Ont9/Tb9v3yvPu66Xvnr16+gvwO3gKIIdszDw65Qdz2sCFFiRYFVmQFIAEBACH5BAkKAAAALAAAAACAAA8AAAP/CLQL/qw9J2qd1AoM9MYeF4KaWJKWmaJXxEyulI3zWa/39Xh6/vkT3q/DC/JiBFjMSCM2hUybUwrdFa3Pqw+pdEVxU3AViKVqwz30cKzmQpZl8ZlNn9uzeLPH7eCrv2l1eXKDgXd6Gn5+goiEjYaFa4eOFopwZJh/cZCPkpGAnhoFo6QFE6WkEwOrrAOqrauvsLKttKy2sQ+wuQ67rrq7uAOoo6fEwsjAs8q1zLfOvAC+yb3B0MPHD8Sm19TS1tXL4c3jz+XR093X28ao3unnv/Hv4N/i9uT45vqr7NrZ89QFHMhPXkF69+AV9OeA4UGBDwkqnFiPYsJg7jBktMXhD165jvk+YvCoD+Q+kRwTAAAh+QQJCgAAACwAAAAAgAAPAAAD/wi0C/6sPRfJdCLnC/S+nsCFo1dq5zeRoFlJ1Du91hOq3b3qNo/5OdZPGDT1QrSZDLIcGp2o47MYheJuImmVer0lmRVlWNslYndm4Jmctba5gm9sPI+gp2v3fZuH78t4Xk0Kg3J+bH9vfYtqjWlIhZF0h3qIlpWYlJpYhp2DjI+BoXyOoqYaBamqBROrqq2urA8DtLUDE7a1uLm3s7y7ucC2wrq+wca2sbIOyrCuxLTQvQ680wDV0tnIxdS/27TND+HMsdrdx+fD39bY6+bX3um14wD09O3y0e77+ezx8OgAqutnr5w4g/3e4RPIjaG+hPwc+stV8NlBixAzSlT4bxqhx46/MF5MxUGkPA4BT15IyRDlwG0uG55MAAAh+QQJCgAAACwAAAAAgAAPAAAD/wi0C/6sPRfJpPECwbnu3gUKH1h2ZziNKVlJWDW9FvSuI/nkusPjrF0OaBIGfTna7GaTNTPGIvK4GUZRV1WV+ssKlE/G0hmDTqVbdPeMZWvX6XacAy6LwzAF092b9+GAVnxEcjx1emSIZop3g16Eb4J+kH+ShnuMeYeHgVyWn56hakmYm6WYnaOihaCqrh0FsbIFE7Oytba0D7m6DgO/wAMTwcDDxMIPx8i+x8bEzsHQwLy4ttWz17fJzdvP3dHfxeG/0uTjywDK1Lu52bHuvenczN704Pbi+Ob66MrlA+scBAQwcKC/c/8SIlzI71/BduysRcTGUF49i/cw5tO4jytjv3keH0oUCJHkSI8KG1Y8qLIlypMm312ASZCiNA0X8eHMqPNCTo07iyUAACH5BAkKAAAALAAAAACAAA8AAAP/CLQL/qw9F8mk8ap8hffaB3ZiWJKfmaJgJWHV5FqQK9uPuDr6yPeTniAIzBV/utktVmPCOE8GUTc9Ia0AYXWXPXaTuOhr4yRDzVIjVY3VsrnuK7ynbJ7rYlp+6/u2vXF+c2tyHnhoY4eKYYJ9gY+AkYSNAotllneMkJObf5ySIphpe3ajiHqUfENvjqCDniIFsrMFE7Sztre1D7q7Dr0TA8LDA8HEwsbHycTLw83ID8fCwLy6ubfXtNm40dLPxd3K4czjzuXQDtID1L/W1djv2vHc6d7n4PXi+eT75v3oANSxAzCwoLt28P7hC2hP4beH974ZTEjwYEWKA9VBdBixLSNHhRPlIRR5kWTGhgz1peS30l9LgBojUhzpa56GmSVr9tOgcueFni15styZAAAh+QQJCgAAACwAAAAAgAAPAAAD/wi0C/6sPRfJpPGqfKsWIPiFwhia4kWWKrl5UGXFMFa/nJ0Da+r0rF9vAiQOH0DZTMeYKJ0y6O2JPApXRmxVe3VtSVSmRLzENWm7MM+65ra93dNXHgep71H0mSzdFec+b3SCgX91AnhTeXx6Y2aOhoRBkllwlICIi49liWmaapGhbKJuSZ+niqmeN6SWrYOvIAWztAUTtbS3uLYPu7wOvrq4EwPFxgPEx8XJyszHzsbQxcG9u8K117nVw9vYD8rL3+DSyOLN5s/oxtTA1t3a7dzx3vPwAODlDvjk/Orh+uDYARBI0F29WdkQ+st3b9zCfgDPRTxWUN5AgxctVqTXUDNix3QToz0cGXIaxo32UCo8+OujyJIM95F0+Y8mMov1NODMuPKdTo4hNXgMemGoS6HPEgAAIfkECQoAAAAsAAAAAIAADwAAA/8ItAv+rD0XyaTxqnyr9pcgitpIhmaZouMGYq/LwbPMTJVE34/Z9j7BJCgE+obBnAWSwzWZMaUz+nQQkUfjyhrEmqTQGnins5XH5iU3u94Crtpfe4SuV9NT8R0Nn5/8RYBedHuFVId6iDyCcX9vXY2Bjz52imeGiZmLk259nHKfjkSVmpeWanhhm56skIyABbGyBROzsrW2tA+5ug68uLbAsxMDxcYDxMfFycrMx87Gv7u5wrfTwdfD2da+1A/Ky9/g0OEO4MjiytLd2Oza7twA6/Le8LHk6Obj6c/8xvjzAtaj147gO4Px5p3Dx9BfOQDnBBaUeJBiwoELHeaDuE8uXzONFu9tE2mvF0KSJ00q7Mjxo8d+L/9pRKihILyaB29esEnzgkt/Gn7GDPosAQAh+QQJCgAAACwAAAAAgAAPAAAD/wi0C/6sPRfJpPGqfKv2HTcJJKmV5oUKJ7qBGPyKMzNVUkzjFoSPK9YjKHQQgSve7eeTKZs7ps4GpRqDSNcQu01Kazlwbxp+ksfipezY1V5X2ZI5XS1/5/j7l/12A/h/QXlOeoSGUYdWgXBtJXEpfXKFiJSKg5V2a1yRkIt+RJeWk6KJmZhogKmbniUFrq8FE7CvsrOxD7a3Drm1s72wv7QPA8TFAxPGxcjJx8PMvLi2wa7TugDQu9LRvtvAzsnL4N/G4cbY19rZ3Ore7MLu1N3v6OsAzM0O9+XK48Xn/+notRM4D2C9c/r6Edu3UOEAgwMhFgwoMR48awnzMWOIzyfeM4ogD4aMOHJivYwexWlUmZJcPXcaXhKMORDmBZkyWa5suE8DuAQAIfkECQoAAAAsAAAAAIAADwAAA/8ItAv+rD0XyaTxqnyr9h03gZNgmtqJXqqwka8YM2NlQXYN2ze254/WyiF0BYU8nSyJ+zmXQB8UViwJrS2mlNacerlbSbg3E5fJ1WMLq9KeleB3N+6uR+XEq1rFPtmfdHd/X2aDcWl5a3t+go2AhY6EZIZmiACWRZSTkYGPm55wlXqJfIsmBaipBROqqaytqw+wsQ6zr623qrmusrATA8DBA7/CwMTFtr24yrrMvLW+zqi709K0AMkOxcYP28Pd29nY0dDL5c3nz+Pm6+jt6uLex8LzweL35O/V6fv61/js4m2rx01buHwA3SWEh7BhwHzywBUjOGBhP4v/HCrUyJAbXUSDEyXSY5dOA8l3Jt2VvHCypUoAIetpmJgAACH5BAkKAAAALAAAAACAAA8AAAP/CLQL/qw9F8mk8ap8q/YdN4Gj+AgoqqVqJWHkFrsW5Jbzbee8yaaTH4qGMxF3Rh0s2WMUnUioQygICo9LqYzJ1WK3XiX4Na5Nhdbfdy1mN8nuLlxMTbPi4be5/Jzr+3tfdSdXbYZ/UX5ygYeLdkCEao15jomMiFmKlFqDZz8FoKEFE6KhpKWjD6ipDqunpa+isaaqqLOgEwO6uwO5vLqutbDCssS0rbbGuMqsAMHIw9DFDr+6vr/PzsnSx9rR3tPg3dnk2+LL1NXXvOXf7eHv4+bx6OfN1b0P+PTN/Lf98wK6ExgO37pd/pj9W6iwIbd6CdP9OmjtGzcNFsVhDHfxDELGjxw1Xpg4kheABAAh+QQJCgAAACwAAAAAgAAPAAAD/wi0C/6sPRfJpPGqfKv2HTeBowiZjqCqG9malYS5sXXScYnvcP6swJqux2MMjTeiEjlbyl5MAHAlTEarzasv+8RCu9uvjTuWTgXedFhdBLfLbGf5jF7b30e3PA+/739ncVp4VnqDf2R8ioBTgoaPfYSJhZGIYhN0BZqbBROcm56fnQ+iow6loZ+pnKugpKKtmrGmAAO2twOor6q7rL2up7C/ssO0usG8yL7KwLW4tscA0dPCzMTWxtXS2tTJ297P0Nzj3t3L3+fmzerX6M3hueTp8uv07ezZ5fa08Piz/8UAYhPo7t6+CfDcafDGbOG5hhcYKoz4cGIrh80cPAOQAAAh+QQJCgAAACwAAAAAgAAPAAAD5wi0C/6sPRfJpPGqfKv2HTeBowiZGLORq1lJqfuW7Gud9YzLud3zQNVOGCO2jDZaEHZk+nRFJ7R5i1apSuQ0OZT+nleuNetdhrfob1kLXrvPariZLGfPuz66Hr8f8/9+gVh4YoOChYhpd4eKdgwFkJEFE5KRlJWTD5iZDpuXlZ+SoZaamKOQp5wAm56loK6isKSdprKotqqttK+7sb2zq6y8wcO6xL7HwMbLtb+3zrnNycKp1bjW0NjT0cXSzMLK3uLd5Mjf5uPo5eDa5+Hrz9vt6e/qosO/GvjJ+sj5F/sC+uMHcCCoBAA7AAAAAAAAAAAA" /></div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="category_module"><?php echo $entry_query_caching_product; ?></label>
								<div class="col-sm-10">
									<div class="onoffswitch" id="container_query_caching_product">
										<input type="checkbox" name="query_caching_product" class="onoffswitch-checkbox" id="query_caching_product" onclick="feature_toggle(this);" data-vqmod="query_caching_product" <?php echo ($features['query_caching_product']) ? "checked" : ""; ?>>
										<label class="onoffswitch-label" for="query_caching_product">
											<span class="onoffswitch-inner"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>
									<div id="loading_query_caching_product" style="display:none;"><img src="data:image/gif;base64,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" /></div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label" for="category_module"><?php echo $entry_query_caching_seo; ?></label>
								<div class="col-sm-10">
									<div class="onoffswitch" id="container_query_caching_seo">
										<input type="checkbox" name="query_caching_seo" class="onoffswitch-checkbox" id="query_caching_seo" onclick="feature_toggle(this);" data-vqmod="query_caching_seo" <?php echo ($features['query_caching_seo']) ? "checked" : ""; ?>>
										<label class="onoffswitch-label" for="query_caching_seo">
											<span class="onoffswitch-inner"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>
									<div id="loading_query_caching_seo" style="display:none;"><img src="data:image/gif;base64,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" /></div>
								</div>
							</div>							
						</fieldset>
						<fieldset>
							<legend><?php echo $heading_cache_block; ?></legend>
							
							<div class="form-group">
								<label class="col-sm-2 control-label" for="optimizer_page_cached_expires"><?php echo $entry_page_expires; ?></label>
								<div class="col-sm-10">
									<select name="optimizer_page_cached_expires">
										<option value="3600"   <?php echo ($optimizer_page_cached_expires == 3600)   ? 'selected="selected"' : ''; ?>>1 Hour</option>
										<option value="21600"  <?php echo ($optimizer_page_cached_expires == 21600)  ? 'selected="selected"' : ''; ?>>6 Hours</option>
										<option value="86400"  <?php echo ($optimizer_page_cached_expires == 86400)  ? 'selected="selected"' : ''; ?>>1 Day</option>
										<option value="172800" <?php echo ($optimizer_page_cached_expires == 172800) ? 'selected="selected"' : ''; ?>>2 Days</option>
										<option value="259200" <?php echo ($optimizer_page_cached_expires == 259200) ? 'selected="selected"' : ''; ?>>3 Days</option>
										<option value="604800" <?php echo ($optimizer_page_cached_expires == 604800) ? 'selected="selected"' : ''; ?>>7 Days</option>
									</select>
								</div>
							</div>							
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $help_cached_routes; ?>"><?php echo $entry_cached_routes; ?></span></label>
								<div class="col-sm-10" style="max-width: 640px;">
									<div id="cached-routes" class="well well-sm" style="height: 150px; overflow: auto;">
										<?php foreach ($optimizer_page_cached_routes as $cached_route) { ?>
										<div id="config-cached-routes"><i class="fa fa-minus-circle"></i> <?php echo $cached_route; ?>
											<input type="hidden" name="optimizer_page_cached_routes[]" value="<?php echo $cached_route; ?>" />
										</div>
										<?php } ?>
									</div>
									<label>Add a Route:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="new-cached-route" value="" />
								</div>
								<script type="text/javascript"><!--
								$('input[name=\'new-cached-route\']').keypress(function(e) {
									if (e.which == 13) {
										$('#cached-routes').append('<div id="config-cached-routes' + '22' + '"><i class="fa fa-minus-circle"></i> ' + $('input[name=\'new-cached-route\']').val() + '<input type="hidden" name="optimizer_page_cached_routes[]" value="' + $('input[name=\'new-cached-route\']').val() + '" /></div>');
										$('input[name=\'new-cached-route\']').val('');
										return false;
									}
								});
								$('#cached-routes').delegate('.fa-minus-circle', 'click', function() {
									$(this).parent().remove();
								});					
								//--></script>
							</div>
						</fieldset>
					</div>

					<?php 
					if (file_exists(DIR_TEMPLATE . OPTIMIZATION_PREFIX . "/advanced_search.tpl")) {
						include_once(DIR_TEMPLATE . OPTIMIZATION_PREFIX . "/advanced_search.tpl");
					} ?>
					
					<div class="tab-pane" id="tab-cdn">
						<script type="text/javascript">
						<!--
						function validate_cdn() {
							$('#btn_cdn_validate').hide();
							$('#loading_cdn_validate').fadeIn('slow');
							$.ajax({
								url: '//api.wxhosting.com/public/cdn/validate.json',
								type: 'POST',
								dataType: 'JSON',
								data: 'key=' + $("input[name=wx_cdn_api_key]").val() + '&token=' + $("input[name=wx_cdn_api_token]").val() ,
								success: function(json) {
									$('.success, .warning, .attention, information, .error').remove();
									if (json['success']['code'] == 200) {
										//Form Values
										$("input[name=wx_cdn_http]").val(json['http_url']);
										$("input[name=wx_cdn_https]").val(json['https_url']);
										$("input[name=wx_cdn_api_valid]").val('1');
										$("input[name=wx_cdn_account]").val(json['id'] + ' (' + json['name'] + ')');
										$("input[name=wx_cdn_https_status]").val('<?php echo $text_cdn_https_shared; ?>');
										//Display Values
										$("#wx_cdn_http").html(json['http_url']);
										$("#wx_cdn_account").html(json['id'] + ' (' + json['name'] + ')');
										$("#wx_cdn_https_status").html('<?php echo $text_cdn_https_shared; ?>');


										//Effects
										$('#validate-cdn').slideUp('slow');
										$('#cdn-settings').slideDown('slow');

										$('#notification').html('<div class="success" style="display: none;">' + json['success']['message'] + '</div>');
										$('.success').fadeIn('slow');
										$('html, body').animate({ scrollTop: 0 }, 'slow');
										$('.success').delay(1500).fadeOut('slow');
									}
									$('#loading_cdn_validate').hide();
									$('#btn_cdn_validate').fadeIn('slow');
								},
							error: function(xhr, status, error) {
								$('#btn_cdn_validate').show();
								$('#loading_cdn_validate').hide();
								clear();
								var json = $.parseJSON(xhr['responseText']);
								$("input[name=wx_cdn_http]").val('');
								$("input[name=wx_cdn_https]").val('');
								$("input[name=wx_cdn_api_valid]").val('0');
								$('#notification').html('<div class="warning" style="display: none;">' + json['error']['message'] + '</div>');
								$('.warning').fadeIn('slow');
								$('html, body').animate({ scrollTop: 0 }, 'slow');
								$('.warning').delay(1500).fadeOut('slow');
								}
							});
						}
						function get_cdn_settings() {
							$('#btn_cdn_get_settings').hide();
							$('#loading_cdn_get_settings').fadeIn('slow');
							$.ajax({
								url: '//api.wxhosting.com/public/cdn/validate.json',
								type: 'POST',
								dataType: 'JSON',
								data: 'key=' + $("input[name=wx_cdn_api_key]").val() + '&token=' + $("input[name=wx_cdn_api_token]").val() ,
								success: function(json) {
									$('.success, .warning, .attention, information, .error').remove();
									if (json['success']) {
										$("input:radio[name=wx_cdn_compress]").filter('[value=' + json['zone']['compress'] + ']').attr('checked', true);
										$("input:radio[name=wx_cdn_backend_compress]").filter('[value=' + json['zone']['backend_compress'] + ']').attr('checked', true);
										$("input:radio[name=wx_cdn_queries]").filter('[value=' + json['zone']['queries'] + ']').attr('checked', true);
										$("input:radio[name=wx_cdn_ignore_setcookie_header]").filter('[value=' + json['zone']['ignore_setcookie_header'] + ']').attr('checked', true);
										$("input:radio[name=wx_cdn_ignore_cache_control]").filter('[value=' + json['zone']['ignore_cache_control'] + ']').attr('checked', true);
										$("input:radio[name=wx_cdn_use_stale]").filter('[value=' + json['zone']['use_stale'] + ']').attr('checked', true);
										$("input:radio[name=wx_cdn_proxy_cache_lock]").filter('[value=' + json['zone']['proxy_cache_lock'] + ']').attr('checked', true);
										$("select[name=wx_cdn_cache_valid]").val(json['zone']['cache_valid']);
										$("select[name=wx_cdn_expires]").val(json['zone']['expires']);
										$("input[name=wx_cdn_set_host_header]").val(json['zone']['set_host_header']);
										$("input:radio[name=wx_cdn_canonical_link_headers]").filter('[value=' + json['zone']['canonical_link_headers'] + ']').attr('checked', true);

										$('#advanced_cdn_settings').slideDown('slow');
									}
									if (json['error']) {
										$('#notification').html('<div class="warning" style="display: none;">' + json['error'] + '</div>');
										$('.warning').fadeIn('slow');
										$('html, body').animate({ scrollTop: 0 }, 'slow');
										$('.warning').delay(1500).fadeOut('slow');
									}
									$('#loading_cdn_get_settings').hide();
								}
							});
						}
						function update_cdn_settings() {
							$('#btn_cdn_update_settings').hide();
							$('#loading_cdn_update_settings').fadeIn('slow');
							$.ajax({
								url: '//api.wxhosting.com/public/cdn/update.json',
								type: 'post',
								data: 'key=' + $("input[name=wx_cdn_api_key]").val()
										+ '&token=' + $("input[name=wx_cdn_api_token]").val()
										+ '&compress=' + $("input[name=wx_cdn_compress]:checked").val()
										+ '&backend_compress=' + $("input[name=wx_cdn_backend_compress]:checked").val()
										+ '&queries=' + $("input[name=wx_cdn_queries]:checked").val()
										+ '&ignore_setcookie_header=' + $("input[name=wx_cdn_ignore_setcookie_header]:checked").val()
										+ '&ignore_cache_control=' + $("input[name=wx_cdn_ignore_cache_control]:checked").val()
										+ '&use_stale=' + $("input[name=wx_cdn_use_stale]:checked").val()
										+ '&proxy_cache_lock=' + $("input[name=wx_cdn_proxy_cache_lock]:checked").val()
										+ '&cache_valid=' + $("select[name=wx_cdn_cache_valid]").val()
										+ '&expires=' + $("select[name=wx_cdn_expires]").val()
										+ '&set_host_header=' + $("input[name=wx_cdn_set_host_header]").val()
										+ '&canonical_link_headers=' + $("input[name=wx_cdn_canonical_link_headers]").val(),

								dataType: 'json',
								success: function(json) {
									$('.success, .warning, .attention, information, .error').remove();
									if (json['success']) {
										$('#notification').html('<div class="success" style="display: none;">' + json['success']['message'] + '</div>');
										$('.success').fadeIn('slow');
										$('html, body').animate({ scrollTop: 0 }, 'slow');
										$('.success').delay(1500).fadeOut('slow');
									}
									if (json['error']) {
										$('#notification').html('<div class="warning" style="display: none;">' + json['error'] + '</div>');
										$('.warning').fadeIn('slow');
										$('html, body').animate({ scrollTop: 0 }, 'slow');
										$('.warning').delay(1500).fadeOut('slow');
									}
									$('#loading_cdn_update_settings').hide();
									$('#btn_cdn_update_settings').fadeIn('slow');
									$('#advanced_cdn_settings').slideUp('slow');
									$('#btn_cdn_get_settings').fadeIn('slow');
								}
							});
						}
						// -->
						</script>
	
						<div id="validate-cdn" <?php echo ($wx_cdn_api_valid) ? 'style="display:none;"' : ''; ?>>
							<fieldset>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="wx_cdn_api_key"><?php echo $entry_cdn_api_key; ?></label>
									<div class="col-sm-10">
										<input onchange="$('input[name=wx_cdn_api_valid]').val('0');" type="text" name="wx_cdn_api_key" size="80" value="<?php echo $wx_cdn_api_key; ?>" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="wx_cdn_api_token"><?php echo $entry_cdn_api_token; ?></label>
									<div class="col-sm-10">
										<input onchange="$('input[name=wx_cdn_api_valid]').val('0');" type="text" name="wx_cdn_api_token" size="80" value="<?php echo $wx_cdn_api_token; ?>" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="wx_page_search"><?php echo $entry_cdn_validation; ?></label>
									<div class="col-sm-10">
										<span id="btn_cdn_validate" class="btn-advanced" onclick="validate_cdn();"><?php echo $text_validate_cdn; ?></span>
										<div id="loading_cdn_validate" style="display:none;"><img src="data:image/gif;base64,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" /></div>
										<input type="hidden" name="wx_cdn_api_valid" value="<?php echo $wx_cdn_api_valid; ?>" />
										<input type="hidden" name="wx_cdn_http" value="<?php echo $wx_cdn_http; ?>" />
										<input type="hidden" name="wx_cdn_https" value="<?php echo $wx_cdn_https; ?>" />
										<input type="hidden" name="wx_cdn_account" value="<?php echo $wx_cdn_account; ?>" />
										<input type="hidden" name="wx_cdn_https_status" value="<?php echo $wx_cdn_https_status; ?>" />
									</div>
								</div>

							</fieldset>
						</div>

						<div id="cdn-settings" <?php echo (!$wx_cdn_api_valid) ? 'style="display:none;"' : ''; ?>>
							<fieldset>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="wx_cdn_status"><?php echo $entry_cdn_status; ?></label>
									<div class="col-sm-10">
										<input type="radio" name="wx_cdn_status" value="1" <?php echo ($wx_cdn_status) ? 'checked="checked"' : ''; ?> /><?php echo $text_enabled; ?>
										<input type="radio" name="wx_cdn_status" value="0" <?php echo (!$wx_cdn_status) ? 'checked="checked"' : ''; ?> /><?php echo $text_disabled; ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="wx_cdn_status"><?php echo $entry_cdn_account; ?></label>
									<div class="col-sm-10">
										<span id="wx_cdn_account" class="help"><?php echo $wx_cdn_account; ?></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="wx_cdn_status"><?php echo $entry_cdn_http; ?></label>
									<div class="col-sm-10">
										<span id="wx_cdn_http" class="help"><?php echo $wx_cdn_http; ?></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="wx_cdn_status"><?php echo $entry_cdn_https; ?></label>
									<div class="col-sm-10">
										<span id="wx_cdn_https_status" class="help"><?php echo $wx_cdn_https_status; ?></span>
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-2 control-label" for="wx_cdn_images"><?php echo $entry_cdn_images; ?></label>
									<div class="col-sm-10">
										<input type="radio" name="wx_cdn_images" value="1" <?php echo ($wx_cdn_images) ? 'checked="checked"' : ''; ?> /><?php echo $text_yes; ?>
										<input type="radio" name="wx_cdn_images" value="0" <?php echo (!$wx_cdn_images) ? 'checked="checked"' : ''; ?> /><?php echo $text_no; ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="wx_cdn_css"><?php echo $entry_cdn_css; ?></label>
									<div class="col-sm-10">
										<input type="radio" name="wx_cdn_css" value="1" <?php echo ($wx_cdn_css) ? 'checked="checked"' : ''; ?> /><?php echo $text_yes; ?>
										<input type="radio" name="wx_cdn_css" value="0" <?php echo (!$wx_cdn_css) ? 'checked="checked"' : ''; ?> /><?php echo $text_no; ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="wx_cdn_js"><?php echo $entry_cdn_js; ?></label>
									<div class="col-sm-10">
										<input type="radio" name="wx_cdn_js" value="1" <?php echo ($wx_cdn_js) ? 'checked="checked"' : ''; ?> /><?php echo $text_yes; ?>
										<input type="radio" name="wx_cdn_js" value="0" <?php echo (!$wx_cdn_js) ? 'checked="checked"' : ''; ?> /><?php echo $text_no; ?>
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $entry_cdn_network_settings; ?></label>
									<div class="col-sm-10">
										<span id="btn_cdn_get_settings" class="btn-advanced" onclick="get_cdn_settings();"><?php echo $text_cdn_get_settings; ?></span>
										<div id="loading_cdn_get_settings" style="display:none;"><img src="data:image/gif;base64,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" /></div>
										<div id="advanced_cdn_settings" style="display:none;">
										
											<div class="form-group">
												<label class="col-sm-2 control-label" for="wx_cdn_compress"><?php echo $entry_cdn_compress; ?></label>
												<div class="col-sm-10">
													<input type="radio" name="wx_cdn_compress" value="1" /><?php echo $text_yes; ?>
													<input type="radio" name="wx_cdn_compress" value="0" /><?php echo $text_no; ?>
												</div>
											</div>	
											
											<div class="form-group">
												<label class="col-sm-2 control-label" for="wx_cdn_backend_compress"><?php echo $entry_cdn_backend_compress; ?></label>
												<div class="col-sm-10">
													<input type="radio" name="wx_cdn_backend_compress" value="1" /><?php echo $text_yes; ?>
													<input type="radio" name="wx_cdn_backend_compress" value="0" /><?php echo $text_no; ?>
												</div>
											</div>	
											
											<div class="form-group">
												<label class="col-sm-2 control-label" for="wx_cdn_queries"><?php echo $entry_cdn_queries; ?></label>
												<div class="col-sm-10">
													<input type="radio" name="wx_cdn_queries" value="1" /><?php echo $text_yes; ?>
													<input type="radio" name="wx_cdn_queries" value="0" /><?php echo $text_no; ?>
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-sm-2 control-label" for="wx_cdn_backend_compress"><?php echo $entry_cdn_cache_valid; ?></label>
												<div class="col-sm-10">
													<select name="wx_cdn_cache_valid">
														<option value=""><?php echo $text_cdn_no_override; ?></option>
														<option value="1d">1 <?php echo $text_day; ?></option>
														<option value="7d">7 <?php echo $text_days; ?></option>
														<option value="1M">1 <?php echo $text_month; ?></option>
														<option value="12M">12 <?php echo $text_months; ?></option>
													</select>
												</div>
											</div>	
											
											<div class="form-group">
												<label class="col-sm-2 control-label" for="wx_cdn_backend_compress"><?php echo $entry_cdn_ignore_setcookie_header; ?></label>
												<div class="col-sm-10">
													<input type="radio" name="wx_cdn_ignore_setcookie_header" value="1" /><?php echo $text_yes; ?>
													<input type="radio" name="wx_cdn_ignore_setcookie_header" value="0" /><?php echo $text_no; ?>
												</div>
											</div>	
											
											<div class="form-group">
												<label class="col-sm-2 control-label" for="wx_cdn_backend_compress"><?php echo $entry_cdn_ignore_cache_control; ?></label>
												<div class="col-sm-10">
													<input type="radio" name="wx_cdn_ignore_cache_control" value="1" /><?php echo $text_yes; ?>
													<input type="radio" name="wx_cdn_ignore_cache_control" value="0" /><?php echo $text_no; ?>
												</div>
											</div>	
											
											<div class="form-group">
												<label class="col-sm-2 control-label" for="wx_cdn_backend_compress"><?php echo $entry_cdn_use_stale; ?></label>
												<div class="col-sm-10">
													<input type="radio" name="wx_cdn_use_stale" value="1" /><?php echo $text_yes; ?>
													<input type="radio" name="wx_cdn_use_stale" value="0" /><?php echo $text_no; ?>
												</div>
											</div>	
	
											<div class="form-group">
												<label class="col-sm-2 control-label" for="wx_cdn_backend_compress"><?php echo $entry_cdn_proxy_cache_lock; ?></label>
												<div class="col-sm-10">
													<input type="radio" name="wx_cdn_proxy_cache_lock" value="1" /><?php echo $text_yes; ?>
													<input type="radio" name="wx_cdn_proxy_cache_lock" value="0" /><?php echo $text_no; ?>
												</div>
											</div>	
											
											<div class="form-group">
												<label class="col-sm-2 control-label" for="wx_cdn_backend_compress"><?php echo $entry_cdn_canonical_link_headers; ?></label>
												<div class="col-sm-10">
													<input type="radio" name="wx_cdn_canonical_link_headers" value="1" /><?php echo $text_yes; ?>
													<input type="radio" name="wx_cdn_canonical_link_headers" value="0" /><?php echo $text_no; ?>
												</div>
											</div>
												
											<div class="form-group">
												<label class="col-sm-2 control-label" for="wx_cdn_backend_compress"><?php echo $entry_cdn_expires; ?></label>
												<div class="col-sm-10">
													<select name="wx_cdn_expires">
														<option value=""><?php echo $text_cdn_no_override; ?></option>
														<option value="1d">1 <?php echo $text_day; ?></option>
														<option value="7d">7 <?php echo $text_days; ?></option>
														<option value="1M">1 <?php echo $text_month; ?></option>
														<option value="12M">12 <?php echo $text_months; ?></option>
													</select>
												</div>
											</div>
												
											<div class="form-group">
												<label class="col-sm-2 control-label" for="wx_cdn_backend_compress"><?php echo $entry_cdn_set_host_header; ?></label>
												<div class="col-sm-10">
													<input type="input" name="wx_cdn_set_host_header" value="" />
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-2 control-label" for="wx_cdn_backend_compress"><?php echo $entry_cdn_canonical_link_headers; ?></label>
												<div class="col-sm-10">
													<input type="radio" name="wx_cdn_canonical_link_headers" value="1" /><?php echo $text_yes; ?>
													<input type="radio" name="wx_cdn_canonical_link_headers" value="0" /><?php echo $text_no; ?>
												</div>
											</div>
											<div class="form-group">
													<div style="width:450px;">
														<span class="help">NOTE: CDN Network Settings are retrieved/updated live on our CDN network and not stored within your OpenCart installation.  In order for Network Setting changes to take effect you must click the update button below.  It may take up to 3 minutes for settings changes to be applied across our global network.</span>
													</div>
													<br />
													<span id="btn_cdn_update_settings" class="btn-advanced" onclick="update_cdn_settings();"><?php echo $text_cdn_update_settings; ?></span>
													<div id="loading_cdn_update_settings" style="display:none;"><img src="data:image/gif;base64,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" /></div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="wx_cdn_backend_compress"><?php echo $entry_cdn_api_change; ?></label>
									<div class="col-sm-10">
										<span class="btn-advanced" onclick="$('#cdn-settings').slideUp('slow'); $('#validate-cdn').slideDown('slow');"><?php echo $text_cdn_change_api; ?></span>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
					
					<?php 
					if (file_exists(DIR_TEMPLATE . OPTIMIZATION_PREFIX . "/bundled_features.tpl")) {
						include_once(DIR_TEMPLATE . OPTIMIZATION_PREFIX . "/bundled_features.tpl");
					} ?>
					
					<div class="tab-pane" id="tab-changelog">

						<script type="text/javascript">
							function get_changelog() {
								$('#update_container').show();
								document.getElementById('change_log').src = '<?php echo HTTPS_SERVER; ?>index.php?route=<?php echo OPTIMIZATION_PREFIX; ?>/update/changelog&token=<?php echo $token; ?>';
							}
						</script>
						<div id="install-update">
							<fieldset>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo "Changelog"; ?></label>
									<div class="col-sm-10">
										<span id="btn_install_update" class="btn-advanced" onclick="get_changelog();"><?php echo $text_view_changelog; ?></span>
									</div>
								</div>
								<div class="form-group" id="update_container">
									<label class="col-sm-2 control-label"><?php echo "View Log"; ?></label>
									<div class="col-sm-10">
										<iframe id="change_log" name="change_log" src="" width="80%" frameborder="0" height="300"></iframe>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
					
					<div class="tab-pane" id="tab-update">

						<script type="text/javascript">
							function install_update() {
								$('#update_container').show();
								document.getElementById('update_log').src = '<?php echo HTTPS_SERVER; ?>index.php?route=<?php echo OPTIMIZATION_PREFIX; ?>/update/install&token=<?php echo $token; ?>';
							}
						</script>
						<div id="install-update">
							<fieldset>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $entry_current_version; ?></label>
									<div class="col-sm-10">
										<?php echo $current_version; ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $entry_latest_version; ?></label>
									<div class="col-sm-10">
										<?php echo $latest_version; ?>
									</div>
								</div>
							
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo "Install"; ?></label>
									<div class="col-sm-10">
										<span id="btn_install_update" class="btn-advanced" onclick="install_update();"><?php echo $text_install_update; ?></span>
										<div id="loading_install_update" style="display:none;"><img src="data:image/gif;base64,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" /></div>
									</div>
								</div>
								<div class="form-group" id="update_container">
									<label class="col-sm-2 control-label"><?php echo "Update Log"; ?></label>
									<div class="col-sm-10">
										<iframe id="update_log" name="update_log" src="" width="80%" frameborder="0" height="300"></iframe>
									</div>
								</div>
							</fieldset>
						</div>					
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php if (VERSION < 2) { ?>
<style type="text/css">
.col-sm-2  { width: 16.6667%; }
.col-sm-10 { width: 70%; }
.col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 { float: left; }
.col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 { min-height: 1px; padding-left: 15px; padding-right: 15px; position: relative; }

.page-header { border-bottom: medium none; margin: 15px 0; padding: 0; vertical-align: middle; }
.page-header h1 { color: #848484; display: inline-block; font-family: "Open Sans",sans-serif; font-size: 30px; font-weight: 400; margin-top: 0px; margin-bottom: 15px; }
.pull-right { float: right; }
.breadcrumb {  background: rgba(0, 0, 0, 0) none repeat scroll 0 0;  display: inline-block;  margin: 0;  padding: 0 10px;  border-radius: 3px;   list-style: outside none none; }
.breadcrumb > li + li::before {  color: #cccccc;  content: "/ ";   padding: 0 5px; }
.breadcrumb > li { display: inline-block; }
.breadcrumb li a {   color: #999999;   font-size: 11px;   margin: 0;   padding: 0;   text-decoration: none; }
.breadcrumb li:last-child a { color: #1e91cf; }

.panel-heading { border-bottom: 1px solid transparent; border-top-left-radius: 2px; border-top-right-radius: 2px; padding: 12px 20px; position: relative; }
.panel-heading h3 { margin-top: 0px; margin-bottom: 0px; }
.panel-default .panel-heading { background: #fcfcfc none repeat scroll 0 0; border-color: #e8e8e8; color: #595959; }
.panel-default { -moz-border-bottom-colors: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; border-color: #bfbfbf #e8e8e8 #e8e8e8; border-image: none; border-style: solid; border-width: 2px 1px 1px; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05); }
.panel-body { padding: 15px; }

.nav { display: table; list-style: outside none none; margin-bottom: 0; padding-left: 0; width: 100%; }
.nav-tabs > li { float: left; margin-bottom: -1px; }
.nav > li { display: block; position: relative; }
.nav { list-style: outside none none; }
.nav-tabs { margin-bottom: 25px; }
.nav-tabs { border-bottom: 1px solid #dddddd;  }
.nav > li > a { display: block; padding: 10px 15px; position: relative; }
.nav-tabs > li > a { border-radius: 2px 2px 0 0; color: #666; text-decoration: none; border: 1px solid transparent; border-radius: 3px 3px 0 0; line-height: 1.42857; margin-right: 2px; }
.nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus { -moz-border-bottom-colors: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; background-color: #ffffff; border-color: #dddddd #dddddd transparent; border-image: none; border-style: solid; border-width: 1px; color: #333; font-weight: bold; cursor: default; }

.tab-content > .tab-pane { display: none; }
.tab-content > .active   { display: block; }

legend { -moz-border-bottom-colors: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; border-color: -moz-use-text-color -moz-use-text-color #e5e5e5; border-image: none; border-style: none none solid; border-width: 0 0 1px; color: #333333; display: block; font-size: 18px; line-height: inherit; margin-bottom: 17px; padding: 0; width: 100%; }

fieldset { border: 0 none; margin: 0; min-width: 0; padding: 0; }
fieldset legend { padding-bottom: 5px; }
.form-horizontal .control-label { margin-bottom: 0; padding-top: 9px; text-align: right; }
.form-horizontal .form-group {
  margin-left: -15px;
  margin-right: -15px;
}

.form-group { display: table; width: 100%; margin-bottom: 15px; }
.clearfix::before, .clearfix::after, .dl-horizontal dd::before, .dl-horizontal dd::after, .container::before, .container::after, .container-fluid::before, .container-fluid::after, .row::before, .row::after, .form-horizontal .form-group::before, .form-horizontal .form-group::after, .btn-toolbar::before, .btn-toolbar::after, .btn-group-vertical > .btn-group::before, .btn-group-vertical > .btn-group::after, .nav::before, .nav::after, .navbar::before, .navbar::after, .navbar-header::before, .navbar-header::after, .navbar-collapse::before, .navbar-collapse::after, .pager::before, .pager::after, .panel-body::before, .panel-body::after, .modal-footer::before, .modal-footer::after { content: " "; display: table; }


a.btn { text-decoration: none; }
.btn { -moz-user-select: none; background-image: none; border: 1px solid transparent; border-radius: 3px; cursor: pointer; display: inline-block; font-size: 12px; font-weight: normal; line-height: 1.42857; margin-bottom: 0; padding: 8px 13px; text-align: center; vertical-align: middle; white-space: nowrap; }
.btn-primary { background-color: #1e91cf; border-color: #1978ab; color: #ffffff; }
.btn-default { background-color: #ffffff; border-color: #cccccc; color: #555555; }

.alert { border: 1px solid transparent; border-radius: 3px; margin-bottom: 17px; padding: 10px; }
.alert-success { background-color: #ecf3e6; border-color: #dfebd5; color: #8fbb6c; }
button.close {
  background: transparent none repeat scroll 0 0;
  border: 0 none;
  cursor: pointer;
  padding: 0;
}
.close {
  color: #000;
  float: right;
  font-size: 18px;
  font-weight: bold;
  line-height: 1;
  opacity: 0.2;
  text-shadow: 0 1px 0 #fff;
}
.well {
    background-color: #f5f5f5;
    border: 1px solid #e3e3e3;
    border-radius: 3px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05) inset;
    margin-bottom: 20px;
    min-height: 20px;
    padding: 19px;
}
.well-sm {
    border-radius: 2px;
    padding: 9px;
}
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-migrate-1.4.0.min.js"></script>
<script type="text/javascript" src="view/javascript/jquery/superfish/js/superfish.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
<?php }?>



<?php echo $footer; ?>