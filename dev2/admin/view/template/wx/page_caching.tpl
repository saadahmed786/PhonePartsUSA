						<fieldset>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="wx_page_caching"><?php echo $entry_page_caching; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" name="wx_page_caching" value="1" <?php echo ($wx_page_caching) ? 'checked="checked"' : ''; ?> /> <?php echo $text_on; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="wx_page_caching" value="0" <?php echo (!$wx_page_caching) ? 'checked="checked"' : ''; ?> /> <?php echo $text_off; ?>
									</label>
								</div>
							</div>
							<div class="form-group">
								<fieldset>
									<div class="form-group">
										<label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $help_excluded_routes; ?>"><?php echo $entry_excluded_routes; ?></span></label>
										<div class="col-sm-10"  style="max-width: 640px;">
											<div id="excluded-routes" class="well well-sm" style="height: 150px; overflow: auto;">
												<?php foreach ($wx_page_excluded_routes as $route) { ?>
												<div id="config-excluded-routes"><i class="fa fa-minus-circle"></i> <?php echo $route; ?>
													<input type="hidden" name="wx_page_excluded_routes[]" value="<?php echo $route; ?>" />
												</div>
												<?php } ?>
											</div>
											<label>Add a Route:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="new-excluded-route" value="" />
										</div>
										<script type="text/javascript"><!--
										$('input[name=\'new-excluded-route\']').keypress(function(e) {
											if (e.which == 13) {
												$('#excluded-routes').append('<div id="config-excluded-routes' + '22' + '"><i class="fa fa-minus-circle"></i> ' + $('input[name=\'new-excluded-route\']').val() + '<input type="hidden" name="wx_page_excluded_routes[]" value="' + $('input[name=\'new-excluded-route\']').val() + '" /></div>');
												$('input[name=\'new-excluded-route\']').val('');
												return false;
											}
										});
										$('#excluded-routes').delegate('.fa-minus-circle', 'click', function() {
											$(this).parent().remove();
										});					
										//--></script>
									</div>
									<div class="form-group" style="border-top: 0px;">
										<label class="col-sm-2 control-label" for="input-category">&nbsp;</label>
										<div class="col-sm-10"  style="max-width: 640px;">
										<p style="display:block">
										Routes containing the following text are automatically excluded and do not require manual definition above:<br />
										"*account*", "*compare*", "*shipping*", "*payment*", "*checkout*", "*total*", "*captcha*", "*feed*", "*recurring*"
										</p>
										</div>
									</div>
								</fieldset>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="wx_page_expires"><?php echo $entry_page_expires; ?></label>
								<div class="col-sm-10">
									<select name="wx_page_expires">
										<option value="0"      <?php echo ($wx_page_expires == 0)      ? 'selected="selected"' : ''; ?>>Never Expires</option>
										<option value="3600"   <?php echo ($wx_page_expires == 3600)   ? 'selected="selected"' : ''; ?>>1 Hour</option>
										<option value="21600"  <?php echo ($wx_page_expires == 21600)  ? 'selected="selected"' : ''; ?>>6 Hours</option>
										<option value="86400"  <?php echo ($wx_page_expires == 86400)  ? 'selected="selected"' : ''; ?>>1 Day</option>
										<option value="172800" <?php echo ($wx_page_expires == 172800) ? 'selected="selected"' : ''; ?>>2 Days</option>
										<option value="259200" <?php echo ($wx_page_expires == 259200) ? 'selected="selected"' : ''; ?>>3 Days</option>
										<option value="604800" <?php echo ($wx_page_expires == 604800) ? 'selected="selected"' : ''; ?>>7 Days</option>
									</select>
								</div>
							</div>
						</fieldset>
						
						<fieldset>
							<legend><?php echo $heading_cache_smart; ?></legend>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="smart_caching"><?php echo $entry_smart_caching; ?></label>
								<div class="col-sm-10">
									<div class="onoffswitch" id="container_smart_caching">
										<input type="checkbox" name="smart_caching" class="onoffswitch-checkbox" id="smart_caching" onclick="feature_toggle(this);" data-vqmod="smart_caching" <?php echo ($features['smart_caching']) ? "checked" : ""; ?>>
										<label class="onoffswitch-label" for="smart_caching">
											<span class="onoffswitch-inner"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>
									<div id="loading_smart_caching" style="display:none;"><img src="data:image/gif;base64,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" /></div>
								</div>
							</div>						
							<div class="form-group">
								<label class="col-sm-2 control-label" for="wx_page_search"><?php echo $entry_page_search; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" name="wx_page_search" value="1" <?php echo ($wx_page_search) ? 'checked="checked"' : ''; ?> /> <?php echo $text_yes; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="wx_page_search" value="0" <?php echo (!$wx_page_search) ? 'checked="checked"' : ''; ?> /> <?php echo $text_no; ?>
									</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="wx_page_sale"><?php echo $entry_page_sale; ?></label>
								<div class="col-sm-10">
									<label class="radio-inline">
										<input type="radio" name="wx_page_sale" value="1" <?php echo ($wx_page_sale) ? 'checked="checked"' : ''; ?> /> <?php echo $text_yes; ?>
									</label>
									<label class="radio-inline">
										<input type="radio" name="wx_page_sale" value="0" <?php echo (!$wx_page_sale) ? 'checked="checked"' : ''; ?> /> <?php echo $text_no; ?>
									</label>
								</div>
							</div>
						</fieldset>
						
						<fieldset>
							<legend><?php echo $heading_cache_smart; ?></legend>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="wx_page_expires"><?php echo $entry_page_desktop; ?></label>
								<div class="col-sm-10">
									<select name="wx_page_desktop">
										<option value="disabled" <?php echo ($wx_page_desktop == 'disabled') ? 'selected="selected"' : ''; ?>><?php echo $text_page_disabled; ?></option>
										<option value="desktop"  <?php echo ($wx_page_desktop == 'desktop')  ? 'selected="selected"' : ''; ?>><?php echo $text_page_desktop; ?></option>
										<option value="mobile"   <?php echo ($wx_page_desktop == 'mobile')   ? 'selected="selected"' : ''; ?>><?php echo $text_page_mobile; ?></option>
										<option value="tablet"   <?php echo ($wx_page_desktop == 'tablet')   ? 'selected="selected"' : ''; ?>><?php echo $text_page_tablet; ?></option>
										<option value="tv"       <?php echo ($wx_page_desktop == 'tv')       ? 'selected="selected"' : ''; ?>><?php echo $text_page_tv; ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="wx_page_expires"><?php echo $entry_page_mobile; ?></label>
								<div class="col-sm-10">
									<select name="wx_page_mobile">
										<option value="disabled" <?php echo ($wx_page_mobile == 'disabled') ? 'selected="selected"' : ''; ?>><?php echo $text_page_disabled; ?></option>
										<option value="desktop"  <?php echo ($wx_page_mobile == 'desktop')  ? 'selected="selected"' : ''; ?>><?php echo $text_page_desktop; ?></option>
										<option value="mobile"   <?php echo ($wx_page_mobile == 'mobile')   ? 'selected="selected"' : ''; ?>><?php echo $text_page_mobile; ?></option>
										<option value="tablet"   <?php echo ($wx_page_mobile == 'tablet')   ? 'selected="selected"' : ''; ?>><?php echo $text_page_tablet; ?></option>
										<option value="tv"       <?php echo ($wx_page_mobile == 'tv')       ? 'selected="selected"' : ''; ?>><?php echo $text_page_tv; ?></option>
									</select>
								</div>
							</div>							
							<div class="form-group">
								<label class="col-sm-2 control-label" for="wx_page_expires"><?php echo $entry_page_tablet; ?></label>
								<div class="col-sm-10">
									<select name="wx_page_tablet">
										<option value="disabled" <?php echo ($wx_page_tablet == 'disabled') ? 'selected="selected"' : ''; ?>><?php echo $text_page_disabled; ?></option>
										<option value="desktop"  <?php echo ($wx_page_tablet == 'desktop')  ? 'selected="selected"' : ''; ?>><?php echo $text_page_desktop; ?></option>
										<option value="mobile"   <?php echo ($wx_page_tablet == 'mobile')   ? 'selected="selected"' : ''; ?>><?php echo $text_page_mobile; ?></option>
										<option value="tablet"   <?php echo ($wx_page_tablet == 'tablet')   ? 'selected="selected"' : ''; ?>><?php echo $text_page_tablet; ?></option>
										<option value="tv"       <?php echo ($wx_page_tablet == 'tv')       ? 'selected="selected"' : ''; ?>><?php echo $text_page_tv; ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="wx_page_expires"><?php echo $entry_page_tv; ?></label>
								<div class="col-sm-10">
									<select name="wx_page_tv">
										<option value="disabled" <?php echo ($wx_page_tv == 'disabled') ? 'selected="selected"' : ''; ?>><?php echo $text_page_disabled; ?></option>
										<option value="desktop"  <?php echo ($wx_page_tv == 'desktop')  ? 'selected="selected"' : ''; ?>><?php echo $text_page_desktop; ?></option>
										<option value="mobile"   <?php echo ($wx_page_tv == 'mobile')   ? 'selected="selected"' : ''; ?>><?php echo $text_page_mobile; ?></option>
										<option value="tablet"   <?php echo ($wx_page_tv == 'tablet')   ? 'selected="selected"' : ''; ?>><?php echo $text_page_tablet; ?></option>
										<option value="tv"       <?php echo ($wx_page_tv == 'tv')       ? 'selected="selected"' : ''; ?>><?php echo $text_page_tv; ?></option>
									</select>
								</div>
							</div>
						</fieldset>