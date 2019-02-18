					<div class="tab-pane" id="tab-bundles">
						<script type="text/javascript"><!--
							function activate_bundle(bundle, plugin, confirmed) {
								confirmed = (typeof confirmed === "undefined") ? false : confirmed;
								$.ajax({
									url: '<?php echo str_replace('&amp;', '&', $bundle_activate) ?>',
									type: 'post',
									data: 'bundle=' + bundle + '&plugin=' + plugin + ((confirmed) ? '&confirmed=true' : ''),
									dataType: 'json',
									success: function(json) {
										$('.success, .warning, .attention, information, .error').remove();

										if (json['error']) {
											if (json['error']['option']) {
												for (i in json['error']['option']) {
													$('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
												}
											}
										}
										if (json['prompt']) {
											$.msgbox(json['prompt'], {
												type: "confirm", buttons : [
													{type: "submit", value: "<?php echo $text_yes; ?>"},
													{type: "cancel", value: "<?php echo $text_cancel; ?>"}
													]
											}, function(result) {
												if (result !== false) {
													activate_bundle(bundle,plugin,true);
												}
											});
										}
										if (json['success']) {
											$('#' + plugin + '-install').removeClass('btn-install-small').html('<?php echo $text_installed; ?>').addClass('btn-no-small').removeAttr("onclick").unbind("click");
											$('#' + plugin + '-disable').removeClass('btn-no-small').html('<?php echo $text_disable; ?>').addClass('btn-disable-small').attr('onclick', 'disable_bundle(\'' + bundle + '\', \'' + plugin + '\')');

											$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '</div>');
											$('.success').fadeIn('slow');
											$('html, body').animate({ scrollTop: 0 }, 'slow');
											$('.success').delay(1500).fadeOut('slow');
										}
									}
								});
							}
						//--></script>
						<script type="text/javascript"><!--
							function disable_bundle(bundle, plugin) {
								$.ajax({
									url: '<?php echo str_replace('&amp;', '&', $bundle_disable) ?>',
									type: 'post',
									data: 'bundle=' + bundle + '&plugin=' + plugin,
									dataType: 'json',
									success: function(json) {
										$('.success, .warning, .attention, information, .error').remove();

										if (json['error']) {
										}

										if (json['success']) {
											$('#' + plugin + '-install').removeClass('btn-no-small').html('<?php echo $text_activate; ?>').addClass('btn-install-small').attr('onclick', 'activate_bundle(\'' + bundle + '\', \'' + plugin + '\')');
											$('#' + plugin + '-disable').removeClass('btn-disable-small').html('<?php echo $text_not_installed; ?>').addClass('btn-no-small').removeAttr("onclick").unbind("click");

											$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '</div>');
											$('.success').fadeIn('slow');
											$('html, body').animate({ scrollTop: 0 }, 'slow');
											$('.success').delay(1500).fadeOut('slow');
										}
									}
								});
							}
						//--></script>
							<?php $bundle_count = 0; ?>
							<?php foreach($bundles as $bundle) { ?>
								<div class="bundle" <?php echo ($bundle_count) ? 'style="padding-top:25px;"' : ''; ?>>
									<div class="bundleimg">
										<img class="shadow" src="<?php echo $bundle['thumbnail'] ?>">
										<div class="bundlemoreinfo" style="padding-top:10px; padding-left: 7px;">
											<strong>Developed By:</strong><br /><?php echo $bundle['author']; ?>
										</div>
										<div class="bundlemoreinfo" style="padding-top:10px; padding-left: 7px;">
											<strong>Additional Information:</strong><br /><a href="<?php echo $bundle['url']; ?>" target="_blank" title="View This Extension On The OpenCart MarketPlace">OpenCart MarketPlace</a>
										</div>
									</div>
									<div class="bundletext">
										<h2><?php echo $bundle['title'] ?></h2>
										<p><?php echo $bundle['description'] ?></p>
										<?php foreach($bundle['plugins'] as $plugin) { ?>
										<div class="plugin">
											<span><?php echo $plugin['title']; ?></span>
											<div style="float:right; width:200px;">
												<div style="float:right; width:100px;"><span id="<?php echo $plugin['id']; ?>-disable" onclick="<?php echo ($plugin['active']) ? 'disable_bundle(\'' . $bundle['filename'] . '\', \'' . $plugin['id'] . '\');' : ''; ?>" class="<?php echo ($plugin['active']) ? 'btn-disable-small' : 'btn-no-small'; ?>" href=""><?php echo ($plugin['active']) ? $text_disable : $text_not_installed; ?></span></div>
												<div style="float:right;"><span id="<?php echo $plugin['id']; ?>-install" onclick="<?php echo (!$plugin['active']) ? 'activate_bundle(\'' . $bundle['filename'] . '\', \'' . $plugin['id'] . '\');' : ''; ?>" class="<?php echo ($plugin['active']) ? 'btn-no-small' : 'btn-install-small'; ?>" href=""><?php echo ($plugin['active']) ? $text_installed : $text_activate; ?></span></div>
											</div>
										</div>
										<?php } ?>
									</div>
								</div>
								<div style="clear: both;"></div>
								<?php $bundle_count++; ?>
							<?php } ?>

						</div>