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

        <!-- start image -->  
        <table id="images" class="list">
          <thead>
            <tr>
              <td class="left"><?php echo $nivo_caption; ?></td>
              <td class="left"><?php echo $nivo_URL; ?></td>
              <td class="left"><?php echo $nivo_image ?></td>
               <td class="left">Sort Order</td>
              <td></td>
            </tr>
          </thead>
          <?php $image_row = 0; ?>
          <?php
          
          ?>
          <?php foreach ($images as $image) { ?>
          <tbody id="image-row<?php echo $image_row; ?>">
            <tr>	 
              <td class="left"><?php foreach ($languages as $language) { ?>
				  <textarea  style="width:90%; height:60px;" name="image_sliders[<?php echo $image_row; ?>][description][<?php echo $language['language_id']; ?>]" ><?php echo isset($image['description'][$language['language_id']]) ? $image['description'][$language['language_id']] : ''; ?></textarea>
				  <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />
				<?php } ?></td>
              <td class="left"><input type="text" name="image_sliders[<?php echo $image_row; ?>][link]" value="<?php echo  isset($image['link']) ? $image['link'] : ''; ?>" /></td>
              <td class="left"><div class="image">
                  <img src="<?php echo $image['thumb']; ?>" alt="" id="thumb<?php echo $image_row; ?>" />
                  <input type="hidden" name="image_sliders[<?php echo $image_row; ?>][image]" value="<?php echo $image['image']; ?>" id="image<?php echo $image_row; ?>"  />
                  <br />
                  <a onclick="image_upload('image<?php echo $image_row; ?>', 'thumb<?php echo $image_row; ?>');"><?php echo $text_browse; ?>
                  </a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb<?php echo $image_row; ?>').attr('src', '<?php echo $no_image; ?>'); $('#image<?php echo $image_row; ?>').attr('value', '');"><?php echo $text_clear; ?></a>
                </div></td>
                
                 <td class="left"><input type="text" name="image_sliders[<?php echo $image_row; ?>][sort]" value="<?php echo  isset($image['sort']) ? $image['sort'] : '1'; ?>" /></td>
              <td class="left"><a onclick="$('#image-row<?php echo $image_row; ?>').remove();" class="button"><?php echo $button_remove; ?></a></td>
            </tr>
          </tbody>
          <?php $image_row++; ?>
          <?php } ?>
          <tfoot>
            <tr>
              <td colspan="3"></td>
              <td class="left"><a onclick="addImage();" class="button"><?php echo $button_add_image; ?></a></td>
            </tr>
          </tfoot>
        </table>	 
        <!-- end image --> 

        <table id="module" class="list">
          <thead>
            <tr>
              <td class="left"><?php echo $entry_image; ?></td>
              <td class="left"><?php echo $entry_layout; ?></td>
              <td class="left"><?php echo $entry_position; ?></td>
              <td class="left"><?php echo $entry_status; ?></td>
              <td class="right"><?php echo $entry_sort_order; ?></td>
              <td></td>
            </tr>
          </thead>
          <?php $module_row = 0; ?>
          <?php foreach ($modules as $module) { ?>
          <tbody id="module-row<?php echo $module_row; ?>">
            <tr>
              <td class="left"><input type="text" name="boss_slideshow_module[<?php echo $module_row; ?>][image_width]" value="<?php echo $module['image_width']; ?>" size="3" />
                <input type="text" name="boss_slideshow_module[<?php echo $module_row; ?>][image_height]" value="<?php echo $module['image_height']; ?>" size="3" />
                <?php if (isset($error_image[$module_row])) { ?>
                <span class="error"><?php echo $error_image[$module_row]; ?></span>
                <?php } ?></td>
              <td class="left"><select name="boss_slideshow_module[<?php echo $module_row; ?>][layout_id]">
                  <?php foreach ($layouts as $layout) { ?>
                  <?php if ($layout['layout_id'] == $module['layout_id']) { ?>
                  <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
              <td class="left"><select name="boss_slideshow_module[<?php echo $module_row; ?>][position]">
				  <?php if ($module['position'] == 'header_bottom') { ?>
                  <option value="header_bottom" selected="selected"><?php echo $text_header_bottom; ?></option>
                  <?php } else { ?>
                  <option value="header_bottom"><?php echo $text_header_bottom; ?></option>
                  <?php } ?>
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
                </select></td>
              <td class="left"><select name="boss_slideshow_module[<?php echo $module_row; ?>][status]">
                  <?php if ($module['status']) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select></td>
              <td class="right"><input type="text" name="boss_slideshow_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $module['sort_order']; ?>" size="3" /></td>
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
</div>
  <script type="text/javascript"><!--
    var image_row = <?php echo $image_row; ?>;

    function addImage() {
      html  = '<tbody id="image-row' + image_row + '">';
      html += '<tr>';
      html += '<td class="left">';
	  <?php foreach ($languages as $language) { ?>
		html += '<textarea  style="width:90%; height:60px;" name="image_sliders[' + image_row + '][description][<?php echo  $language['language_id'];?>]" ></textarea>';
		html += '<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />'
	  <?php } ?>
	  html += '</td>';
      html += '<td class="left"><input type="text" name="image_sliders[' + image_row + '][link]" value="" /></td>';	
      html += '<td class="left"><div class="image"><img src="<?php echo $no_image; ?>" alt="" id="thumb' + image_row + '" /><input type="hidden" name="image_sliders[' + image_row + '][image]" value="" id="image' + image_row + '" /><br /><a onclick="image_upload(\'image' + image_row + '\', \'thumb' + image_row + '\');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$(\'#thumb' + image_row + '\').attr(\'src\', \'<?php echo $no_image; ?>\'); $(\'#image' + image_row + '\').attr(\'value\', \'\');"><?php echo $text_clear; ?></a></div></td>';
      html += '<td class="left"><a onclick="$(\'#image-row' + image_row  + '\').remove();" class="button"><?php echo $button_remove; ?></a></td>';
      html += '</tr>';
      html += '</tbody>'; 
	
      $('#images tfoot').before(html);
	  
      image_row++;
    }
    //--></script>
  <script type="text/javascript"><!--
    function image_upload(field, thumb) {
      $('#dialog').remove();
	
      $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
      $('#dialog').dialog({
        title: '<?php echo $text_image_manager; ?>',
        close: function (event, ui) {
          if ($('#' + field).attr('value')) {
            $.ajax({
              url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).attr('value')),
              dataType: 'text',
              success: function(data) {
                $('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
              }
            });
          }
        },	
        bgiframe: false,
        width: 700,
        height: 400,
        resizable: false,
        modal: false
      });
    };
    //--></script> 

  <script type="text/javascript"><!--
    var module_row = <?php echo $module_row; ?>;

    function addModule() {	
      html  = '<tbody id="module-row' + module_row + '">';
      html += '  <tr>';
      html += '    <td class="left"><input type="text" name="boss_slideshow_module[' + module_row + '][image_width]" value="980" size="3" /> <input type="text" name="boss_slideshow_module[' + module_row + '][image_height]" value="280" size="3" /></td>';	
      html += '    <td class="left"><select name="boss_slideshow_module[' + module_row + '][layout_id]">';
        <?php foreach ($layouts as $layout) { ?>
          html += '<option value="<?php echo $layout['layout_id']; ?>"><?php echo addslashes($layout['name']); ?></option>';
        <?php } ?>
      html += '    </select></td>';
      html += '    <td class="left"><select name="boss_slideshow_module[' + module_row + '][position]">';
      html += '      <option value="content_top"><?php echo $text_content_top; ?></option>';
      html += '      <option value="content_bottom"><?php echo $text_content_bottom; ?></option>';
      html += '    </select></td>';
      html += '    <td class="left"><select name="boss_slideshow_module[' + module_row + '][status]">';
      html += '      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
      html += '      <option value="0"><?php echo $text_disabled; ?></option>';
      html += '    </select></td>';
      html += '    <td class="right"><input type="text" name="boss_slideshow_module[' + module_row + '][sort_order]" value="" size="3" /></td>';
      html += '    <td class="left"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="button"><?php echo $button_remove; ?></a></td>';
      html += '  </tr>';
      html += '</tbody>';
	
      $('#module tfoot').before(html);
	
      module_row++;
    }
    //--></script> 

<?php echo $footer; ?>
