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
       <table id="module" class="list">
       		<thead>
                <tr>
                  <td class="left"><?php echo $entry_layout; ?></td>
              	  <td class="left"><?php echo $entry_image; ?></td>
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
              <td class="left"><select name="featured_category_module[<?php echo $module_row; ?>][layout_id]">
                  <?php foreach ($layouts as $layout) { ?>
                  <?php if ($layout['layout_id'] == $module['layout_id']) { ?>
                  <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
               
              <td class="left">
              	<input type="text" name="featured_category_module[<?php echo $module_row; ?>][image_width]" value="<?php echo $module['image_width']; ?>" size="3" />
                <input type="text" name="featured_category_module[<?php echo $module_row; ?>][image_height]" value="<?php echo $module['image_height']; ?>" size="3" />
                </td>
              <td class="left"><select name="featured_category_module[<?php echo $module_row; ?>][position]">
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
              <td class="left"><select name="featured_category_module[<?php echo $module_row; ?>][status]">
                  <?php if ($module['status']) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select></td>
              <td class="right"><input type="text" name="featured_category_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $module['sort_order']; ?>" size="3" /></td>
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
       
       <table id="categories" class="list">
          <thead>
            <tr>
              <td class="left"><?php echo $text_cat; ?></td>
              <td class="left"><?php echo $text_image; ?></td>
              <td class="left"><?php echo $text_desc; ?></td>
              <td class="left"><?php echo $text_remove; ?></td>
            </tr>
          </thead>
          <?php $cat_row = 0; ?>
          <?php foreach ($featured_category as $izi) { ?>
          
          <tbody id="cat_row<?php echo $cat_row; ?>">
          	<tr>
            	<td class="left">
                	<?php		
						$categories = $this->model_catalog_category->getCategories(0);							
					?>
                        <select name="featured_category_cat[<?php echo $cat_row; ?>][catId]">
                        	<option></option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['category_id']; ?>" <?php if($izi['catId']==$cat['category_id']){ echo 'selected="selected"'; }?>><?php echo $cat['name']; ?></option>    	
                            <?php endforeach; ?>
                        </select>
                </td>
                <td class="left">
                		<div class="image<?php echo $cat_row; ?>">
							<?php if(isset($izi['catImage']) && !empty($izi['catImage'])): ?>
                                <img src="<?php echo HTTP_IMAGE . $izi['catImage']; ?>" alt="" id="thumb<?php echo $cat_row; ?>" width="100" height="100" />
                            <?php endif; ?>
                            <input type="hidden" name="featured_category_cat[<?php echo $cat_row; ?>][catImage]" value="<?php echo $izi['catImage']; ?>" id="image<?php echo $cat_row; ?>"  />
                            <br />
                            <a onclick="image_upload('image<?php echo $cat_row; ?>', 'thumb<?php echo $cat_row; ?>');"><?php echo $text_browse; ?></a>
                           
                        </div>
                </td>
                <td class="left">
                	<input name="featured_category_cat[<?php echo $cat_row; ?>][catDesc]" value="<?php if(isset($izi['catDesc'])){ echo $izi['catDesc']; } ?>" />
                </td>
                <td class="left"><a onclick="$('#cat_row<?php echo $cat_row; ?>').remove();" class="button"><?php echo $text_remove; ?></a></td>
            </tr>
          </tbody>
          <?php $cat_row++; ?>
          <?php } ?>
       
          <tfoot>
            <tr>
              <td colspan="3"></td>
              <td class="left"><a onclick="addCat();" class="button"><?php echo "Add New Category"; ?></a></td>
            </tr>
          </tfoot>
      </table>
      </form>
    </div>
  
  </div>
  
  
</div>
<script type="text/javascript"><!--
var module_row = <?php echo $module_row; ?>;

function addModule() {	
	html  = '<tbody id="module-row' + module_row + '">';
	html += '  <tr>';
	html += '    <td class="left"><select name="featured_category_module[' + module_row + '][layout_id]">';
	<?php foreach ($layouts as $layout) { ?>
	html += '      <option value="<?php echo $layout['layout_id']; ?>"><?php echo addslashes($layout['name']); ?></option>';
	<?php } ?>
	html += '    </select></td>';
	html += '<td class="left"><input type="text" name="featured_category_module[' + module_row + '][image_width]"  size="3" /><input type="text" name="featured_category_module[' + module_row + '][image_height]" size="3" />';
	html += '</td>';
	html += '    <td class="left"><select name="featured_category_module[' + module_row + '][position]">';
	html += '      <option value="content_top"><?php echo $text_content_top; ?></option>';
	html += '      <option value="content_bottom"><?php echo $text_content_bottom; ?></option>';
	html += '      <option value="column_left"><?php echo $text_column_left; ?></option>';
	html += '      <option value="column_right"><?php echo $text_column_right; ?></option>';
	html += '    </select></td>';
	html += '    <td class="left"><select name="featured_category_module[' + module_row + '][status]">';
    html += '      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
    html += '      <option value="0"><?php echo $text_disabled; ?></option>';
    html += '    </select></td>';
	html += '    <td class="right"><input type="text" name="featured_category_module[' + module_row + '][sort_order]" value="" size="3" /></td>';
	html += '    <td class="left"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="button"><?php echo $button_remove; ?></a></td>';
	html += '  </tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html);
	
	module_row++;
}
//--></script> 

<script type="text/javascript"><!--
var cat_row = <?php echo $cat_row; ?>;
function addCat(){
		html  ='<tbody id="cat_row' + cat_row + '">';
		html += '<tr>';
		html += '<td class="left">';
		<?php		
			$categories = $this->model_catalog_category->getCategories(0);							
		?>
		html += '<select name="featured_category_cat[' + cat_row + '][catId]">';
		html += '<option></option>';
		<?php foreach($categories as $cat): ?>
		html += '<option value="<?php echo $cat['category_id']; ?>"><?php echo $cat['name']; ?></option>';    	
		<?php endforeach; ?>
		html += '</select>';
		html += '</td>';
		html += '<td class="left">';
		html += '<div class="image' + cat_row + '">';
		html += '<input type="hidden" name="featured_category_cat[' + cat_row + '][catImage]" value="<?php echo $izi['catImage']; ?>" id="image' + cat_row + '"  />';
		html += '<br />';
		html += '<a onclick="image_upload(\'image' + cat_row + '\', \'thumb' + cat_row + '\');"><?php echo $text_browse; ?></a>';
		html += '</div>';
		html += '</td>';
		html += '<td class="left">';
		html += '<input cols="60" rows="5" name="featured_category_cat[' + cat_row + '][catDesc]" />';
		html += '</td>';
		html += '<td class="left"><a onclick="$(\'#cat_row' + cat_row + '\').remove();" class="button"><?php echo $text_remove; ?></a></td>';
		html += '</tr>';
		html += '</tbody>';
				
		$('#categories tfoot').before(html);
				
		cat_row++;
}
//--></script>

<script type="text/javascript">	
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
						$('.' + field).find('img').remove();
						$('.' + field).prepend('<img src="' + data + '" alt="" id="' + thumb + '" />');
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
</script>

<?php echo $footer; ?>