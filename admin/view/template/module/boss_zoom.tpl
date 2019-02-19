<?php
$style = '<style type="text/css">
    .bztable {margin:20px 0; width:100%; border:1px solid #dfdfdf; }
    .bztable tr {vertical-align:middle; border-bottom:1px solid #dfdfdf; padding:15px 5px; font-weight:bold; background:#fff; text-align:left; }
    .bztable th, .bztable td {vertical-align:middle; border-bottom:1px solid #dfdfdf; padding:10px 5px; background:#fff; }
    .bztable tr.black td, .bztable tr.black th {background:#f9f9f9; }
    .bztable tr.last td, .bztable tr.last th{border:none; }
	.bztable tbody  tr td img{margin-right:10px; vertical-align: middle !important;display:inline-block;}
	.bztable tbody tr td input, .bztable tbody tr td select, .bztable tbody tr td span{margin: 0 10px 0 10px; vertical-align: middle}
	.bztable tbody tr td span{margin: 0 0 0 10px; vertical-align: middle}
    .bztitle {font-size: 1.5em;font-weight: normal;margin: 1.7em 0 1em 0;}
	.bz_error{color: red; font-size: 12px; font-weight: normal;}
</style>';
$header = str_replace('</head>',$style.'</head>',$header);
echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
		<table class="bztable" style="display:none">
		<tr>
			<td class="left"><select name="boss_zoom_module[0][layout_id]">
                <option value="2">product</option>
			</select></td>
			<td class="left"><select name="boss_zoom_module[0][position]">
                <option value="content_bottom" >content bottom</option>
			</select></td>
			<td class="left"><input type="text" name="boss_zoom_module[0][sort_order]" value="<?php echo $modules[0]['sort_order']; ?>" size="3" /></td>
		</tr>
		</table>
		<table class="bztable">
          <tbody>
			<tr class="last">
				<th width="30%"><?php echo $text_enabled_disabled; ?></th>
				<td width="70%"><select name="boss_zoom_module[0][status]">
                  <?php if ($modules[0]['status']) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select></td>
			</tr>
          </tbody>
        </table>
		<h3 class="bztitle"><?php echo $text_information; ?></h3>
		<table class="bztable">
          <tbody>
			<tr>
				<th width="30%" ><?php echo $text_image_Thumb; ?></th>
				<td width="70%" ><input type="text" name="boss_zoom_module[0][thumb_image_width]" value="<?php echo $modules[0]['thumb_image_width']; ?>" size="3"/>  x  <input type="text" name="boss_zoom_module[0][thumb_image_heigth]" value="<?php echo $modules[0]['thumb_image_heigth']; ?>" size="3" />
				<?php if (isset($error_thumb_image)) { ?>
                <span class="bz_error"><?php echo $error_thumb_image; ?></span>
                <?php } ?>
				</td>
			</tr>
			<tr class="black">
				<th width="30%"><?php echo $text_image_Addition; ?></th>
				<td width="70%"><input type="text" name="boss_zoom_module[0][addition_image_width]" value="<?php echo $modules[0]['addition_image_width']; ?>" size="3" />  x  <input type="text" name="boss_zoom_module[0][addition_image_heigth]" value="<?php echo $modules[0]['addition_image_heigth']; ?>" size="3" />
				<?php if (isset($error_addition_image)) { ?>
                <span class="bz_error"><?php echo $error_addition_image; ?></span>
                <?php } ?></td>
			</tr>
			<tr>
				<th width="30%" ><?php echo $text_image_Zoom; ?></th>
				<td width="70%" ><input type="text" name="boss_zoom_module[0][zoom_image_width]" value="<?php echo $modules[0]['zoom_image_width']; ?>" size="3" />  x  <input type="text" name="boss_zoom_module[0][zoom_image_heigth]" value="<?php echo $modules[0]['zoom_image_heigth']; ?>" size="3" />
				<?php if (isset($error_zoom_image)) { ?>
                <span class="bz_error"><?php echo $error_zoom_image; ?></span>
                <?php } ?></td>
			</tr>
			<tr class="black">
				<th width="30%" ><?php echo $text_area_Zoom; ?></th>
				<td width="70%" ><input type="text" name="boss_zoom_module[0][zoom_area_width]" value="<?php echo $modules[0]['zoom_area_width']; ?>" size="3" />  x  <input type="text" name="boss_zoom_module[0][zoom_area_heigth]" value="<?php echo $modules[0]['zoom_area_heigth']; ?>" size="3" /><?php echo $text_auto_size_area; ?>
				</td>
			</tr>
			<tr>
				<th width="30%"><?php echo $text_area_position; ?></th>
				<td width="70%">
					<!--
					<input type="radio" name="boss_zoom_module[0][position_zoom_area]" value="top" <?php if($modules[0]['position_zoom_area'] == 'top') echo 'checked="checked"'; ?>   ><img title="top" alt="top" src="view/image/boss_zoom/top.gif" />
					-->
					<input type="radio" name="boss_zoom_module[0][position_zoom_area]" value="right"  <?php if($modules[0]['position_zoom_area'] == 'right') echo 'checked="checked"'; ?> ><img title="right" alt="right" src="view/image/boss_zoom/right.gif" />
					<!--
					<input type="radio" name="boss_zoom_module[0][position_zoom_area]" value="bottom"  <?php if($modules[0]['position_zoom_area'] == 'bottom') echo 'checked="checked"'; ?> ><img title="bottom" alt="bottom" src="view/image/boss_zoom/bottom.gif" />
					<input type="radio" name="boss_zoom_module[0][position_zoom_area]" value="left"  <?php if($modules[0]['position_zoom_area'] == 'left') echo 'checked="checked"'; ?> ><img title="left" alt="left" src="view/image/boss_zoom/left.gif" />
					-->
					<input id="inside" type="radio" name="boss_zoom_module[0][position_zoom_area]" value="inside"  <?php if($modules[0]['position_zoom_area'] == 'inside') echo 'checked="checked"'; ?> ><?php echo $text_Inner; ?>	
				</td>
			</tr>
			<tr class="black">
				<th width="30%"><?php echo $text_distance; ?></th>
				<td width="70%"><span><?php echo $text_AdjustX; ?></span><input id="adjustX" type="text" name="boss_zoom_module[0][adjustX]" value="<?php echo $modules[0]['adjustX']; ?>" size="3" /><span> <?php echo $text_AdjustY; ?></span><input id="adjustY" type="text" name="boss_zoom_module[0][adjustY]" value="<?php echo $modules[0]['adjustY']; ?>" size="3" /></td>
			</tr>
			<tr>
				<th width="30%"><?php echo $text_title; ?></th>
				<td width="70%"><span><?php echo $text_enable; ?></span><input type="radio" name="boss_zoom_module[0][title_image]" value="true" <?php if($modules[0]['title_image'] == 'true') echo 'checked="checked"'; ?>   ><span><?php echo $text_disable; ?></span><input type="radio" name="boss_zoom_module[0][title_image]" value="false"  <?php if($modules[0]['title_image'] == 'false') echo 'checked="checked"'; ?> ><span><?php echo $text_title_opacity; ?></span><input type="text" name="boss_zoom_module[0][title_opacity]" value="<?php echo $modules[0]['title_opacity']; ?>" size="3" /></td>
			</tr>
			<tr class="black">
				<th width="30%"><?php echo $text_Tint; ?></th>
				<td width="70%"><span><?php echo $text_Tint; ?>:</span><input type="text" name="boss_zoom_module[0][tint]" value="<?php echo $modules[0]['tint']; ?>" size="10" /><span><?php echo $text_Tint_opacity; ?> </span>
				<input type="text" name="boss_zoom_module[0][tintOpacity]" value="<?php echo $modules[0]['tintOpacity']; ?>" size="3" /></td>
			</tr>
			<tr>
				<th width="30%"><?php echo $text_softFocus; ?></th>
				<td width="70%"><span><?php echo $text_enable; ?></span><input type="radio" name="boss_zoom_module[0][softfocus]" value="true" <?php if($modules[0]['softfocus'] == 'true') echo 'checked="checked"'; ?> ><span><?php echo $text_disable; ?></span><input type="radio" name="boss_zoom_module[0][softfocus]" value="false"  <?php if($modules[0]['softfocus'] == 'false') echo 'checked="checked"'; ?> ></td>
			</tr>
			<tr class="black">
				<th width="30%"><?php echo $text_Opacity_lens; ?></th>
				<td width="70%"><input type="text" name="boss_zoom_module[0][lensOpacity]" value="<?php echo $modules[0]['lensOpacity']; ?>" size="3" /></td>
			</tr>
			<tr class="last">
				<th width="30%"><?php echo $text_Smooth; ?></th>
				<td width="70%"><input type="text" name="boss_zoom_module[0][smoothMove]" value="<?php echo $modules[0]['smoothMove']; ?>" size="3" /></td>
			</tr>
          </tbody>
        </table>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
	$("#inside").bind('click', function(e){
		$('#adjustX').val("0"); 
		$('#adjustY').val("0");
	});
</script>
<?php echo $footer; ?>