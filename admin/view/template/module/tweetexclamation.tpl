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
<div class="box">
  <div class="heading">
    <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
	<p><?php echo $entry_config_ckey; ?>
	<input type="text" name="tweetexclamation_ckey" value="<?php echo $tweetexclamation_ckey; ?>" class="gpinput" />
	</p>
	<p><?php echo $entry_config_csecret; ?>
	<input type="text" name="tweetexclamation_csecret" value="<?php echo $tweetexclamation_csecret; ?>" class="gpinput" />
	</p>
	<p><?php echo $entry_config_atoken; ?>
	<input type="text" name="tweetexclamation_atoken" value="<?php echo $tweetexclamation_atoken; ?>" class="gpinput" />
	</p>
	<p><?php echo $entry_config_asecret; ?>
	<input type="text" name="tweetexclamation_asecret" value="<?php echo $tweetexclamation_asecret; ?>" class="gpinput" />
	</p>
	<hr>
	<p><?php echo $text_instructions; ?></p>
      <table class="form">
        <tr>
          <td></td>
          <td></td>
        </tr>
      </table>
      <table id="module" class="list">
        <thead>
          <tr>
            <td class="left"><?php echo $entry_config; ?></td>
            <td class="left"><?php echo $entry_layout; ?></td>
            <td class="left"><?php echo $entry_position; ?></td>
            <td class="left"><?php echo $entry_status; ?></td>
            <td class="right"><?php echo $entry_sort_order; ?></td>
            <td></td>
          </tr>
        </thead>
        <?php $module_row = 0; ?>
        <?php foreach ($modules as $index=>$module) { ?>
        <tbody id="module-row<?php echo $module_row; ?>">
          <tr>
            <td class="right">
		<table class=config>
			<tr><td><?php echo $entry_config_title?></td><td><input type="text" name="tweetexclamation_module[<?php echo $module_row; ?>][config_title]" value="<?php echo $module['config_title']; ?>" size="10" /></td></tr>
			<tr><td><?php echo $entry_config_username ?></td><td><input type="text" name="tweetexclamation_module[<?php echo $module_row; ?>][config_username]" value="<?php echo $module['config_username']; ?>" size="10" />
<?php if (isset($error_modules[$index]) && isset($error_modules[$index]['config_username'])) { ?> <span class="error"><?php echo $error_modules[$index]['config_username']; ?></span> <?php } ?></td></tr>
			<tr><td><?php echo $entry_config_count ?></td><td><input type="text" name="tweetexclamation_module[<?php echo $module_row; ?>][config_count]" value="<?php echo $module['config_count']; ?>" size="3" /></td></tr>
			<tr><td><?php echo $entry_config_avatar_size?></td><td><input type="text" name="tweetexclamation_module[<?php echo $module_row; ?>][config_avatar_size]" value="<?php echo $module['config_avatar_size']; ?>" size="3" /></td></tr>
			<tr><td><?php echo $entry_config_template?></td><td><input type="text" name="tweetexclamation_module[<?php echo $module_row; ?>][config_template]" value="<?php echo $module['config_template']; ?>" size="10" /></td></tr>
			<tr><td><?php echo $entry_config_readmore?></td><td><input type="text" name="tweetexclamation_module[<?php echo $module_row; ?>][config_readmore]" value="<?php echo $module['config_readmore']; ?>" size="10" /></td></tr>
		</table>
		</td>
            <td class="left"><select name="tweetexclamation_module[<?php echo $module_row; ?>][layout_id]">
                <?php foreach ($layouts as $layout) { ?>
                <?php if ($layout['layout_id'] == $module['layout_id']) { ?>
                <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
            <td class="left"><select name="tweetexclamation_module[<?php echo $module_row; ?>][position]">
                <?php if ($module['position'] == 'content_top') { ?>
                <option value="content_top" selected="selected"><?php echo $text_content_top; ?></option>
                <?php } else { ?>
                <option value="content_top"><?php echo $text_content_top; ?></option>
                <?php } ?>  
                <?php if ($module['position'] == 'content_bottom') { ?>
                <option value="content_bottom" selected="selected"><?php echo $text_content_bottom; ?></option>
                <?php } else { ?>
                <option value="content_bottom"><?php echo $text_content_bottom; ?></option>
                <?php } ?>     
                <?php if ($module['position'] == 'column_left') { ?>
                <option value="column_left" selected="selected"><?php echo $text_column_left; ?></option>
                <?php } else { ?>
                <option value="column_left"><?php echo $text_column_left; ?></option>
                <?php } ?>
                <?php if ($module['position'] == 'column_right') { ?>
                <option value="column_right" selected="selected"><?php echo $text_column_right; ?></option>
                <?php } else { ?>
                <option value="column_right"><?php echo $text_column_right; ?></option>
                <?php } ?>
              </select></td>
            <td class="left"><select name="tweetexclamation_module[<?php echo $module_row; ?>][status]">
                <?php if ($module['status']) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
            <td class="right"><input type="text" name="tweetexclamation_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $module['sort_order']; ?>" size="3" /></td>
            <td class="left"><a onclick="$('#module-row<?php echo $module_row; ?>').remove();" class="button"><?php echo $button_remove; ?></a></td>
          </tr>
        </tbody>
        <?php $module_row++; ?>
        <?php } ?>
        <tfoot>
          <tr>
            <td colspan="5"></td>
            <td class="left"><a onclick="addModule();" class="button"><?php echo $button_add_module; ?></a></td>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>
</div>
<style>
table.config {width:100%; border:0;}
</style>
<script type="text/javascript"><!--
var module_row = <?php echo $module_row; ?>;

function addModule() {	
	html  = '<tbody id="module-row' + module_row + '">';
	html += '  <tr>';

	html += '   <td class="right"> <table class="config">';
	html += '     <tr><td><?php echo $entry_config_title?></td><td><input type="text" name="tweetexclamation_module[' + module_row + '][config_title]" size="10" value="<?php echo $entry_config_title_default ?>" /></td></tr>';
	html += '     <tr><td><?php echo $entry_config_username ?></td><td><input type="text" name="tweetexclamation_module[' + module_row + '][config_username]" size="10" value="" /></td></tr>';
	html += '     <tr><td><?php echo $entry_config_count ?></td><td><input type="text" name="tweetexclamation_module[' + module_row + '][config_count]" size="3" value="<?php echo $entry_config_count_default ?>" /></td></tr>';
	html += '     <tr><td><?php echo $entry_config_avatar_size?></td><td><input type="text" name="tweetexclamation_module[' + module_row + '][config_avatar_size]" size="3" value="<?php echo $entry_config_avatar_size_default ?>" /></td></tr>';
	html += '     <tr><td><?php echo $entry_config_template?></td><td><input type="text" name="tweetexclamation_module[' + module_row + '][config_template]" size="10" value="<?php echo $entry_config_template_default ?>" /></td></tr>';
	html += '     <tr><td><?php echo $entry_config_readmore?></td><td><input type="text" name="tweetexclamation_module[' + module_row + '][config_readmore]" size="10" value="<?php echo $entry_config_readmore_default ?>" /></td></tr>';
	html += '   </table> </td>';

	//html += '   <td class="right"> <table class="config"> <tr><td><?php echo $entry_config_count ?></td><td><input type="text" name="tweetexclamation_module[' + module_row + '][config_count]" size="3" /></td></tr> <tr><td><?php echo $entry_config_avatar_size?></td><td><input type="text" name="tweetexclamation_module[' + module_row + '][config_avatar_size]" size="3" /></td></tr> </table> </td>';
	html += '    <td class="left"><select name="tweetexclamation_module[' + module_row + '][layout_id]">';
	<?php foreach ($layouts as $layout) { ?>
	html += '      <option value="<?php echo $layout['layout_id']; ?>"><?php echo addslashes($layout['name']); ?></option>';
	<?php } ?>
	html += '    </select></td>';
	html += '    <td class="left"><select name="tweetexclamation_module[' + module_row + '][position]">';
	html += '      <option value="content_top"><?php echo $text_content_top; ?></option>';
	html += '      <option value="content_bottom"><?php echo $text_content_bottom; ?></option>';
	html += '      <option value="column_left"><?php echo $text_column_left; ?></option>';
	html += '      <option value="column_right"><?php echo $text_column_right; ?></option>';
	html += '    </select></td>';
	html += '    <td class="left"><select name="tweetexclamation_module[' + module_row + '][status]">';
	html += '      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
	html += '      <option value="-1"><?php echo $text_disabled; ?></option>';
	html += '    </select></td>';
	html += '    <td class="right"><input type="text" name="tweetexclamation_module[' + module_row + '][sort_order]" value="" size="3" /></td>';
	html += '    <td class="left"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="button"><?php echo $button_remove; ?></a></td>';
	html += '  </tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html);
	
	module_row++;
}
//--></script>
<?php echo $footer; ?>
