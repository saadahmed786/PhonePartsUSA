<?php echo $header; ?>
<style>
.center {
	background-color: #EFEFEF;
	padding: 7px;
	text-align: center;
	font-weight: bold;
}
</style>
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
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tabs" class="htabs">
          <?php foreach ($stores as $store) { ?>
          <a href="#tab-store-<?php echo $store['store_id']; ?>" id="store-<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></a>
          <?php } ?>
		</div>
        <?php foreach ($stores as $store) { ?>
        <div id="tab-store-<?php echo $store['store_id']; ?>">
		  <table style="width: 100%;">
			<thead>
			  <tr>
                <td class="center" width="50%"><?php echo $heading_normal; ?></td>
                <td class="center"><?php echo $heading_redirect; ?></td>
              </tr>
			</thead>
			<tbody>
			  <tr>
                <td valign="top">
				  <table class="form">
				    <tr>
				      <td><?php echo $entry_status; ?></td>
				      <td><select name="success_page[<?php echo $store['store_id']; ?>][success_page_status]">
				        <?php if (isset($module[$store['store_id']]['success_page_status']) && $module[$store['store_id']]['success_page_status'] == 1) { ?>
				        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
				        <option value="0"><?php echo $text_disabled; ?></option>
				        <?php } else { ?>
				        <option value="1"><?php echo $text_enabled; ?></option>
				        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
				        <?php } ?>
				      </select></td>
				    </tr>
					<tr>
				      <td><?php echo $entry_to_default; ?></td>
				      <td><select name="success_page[<?php echo $store['store_id']; ?>][success_page_to_default]">
				        <?php if (isset($module[$store['store_id']]['success_page_to_default']) && $module[$store['store_id']]['success_page_to_default'] == 1) { ?>
				        <option value="1" selected="selected"><?php echo $text_yes; ?></option>
				        <option value="0"><?php echo $text_no; ?></option>
				        <?php } else { ?>
				        <option value="1"><?php echo $text_yes; ?></option>
				        <option value="0" selected="selected"><?php echo $text_no; ?></option>
				        <?php } ?>
				      </select></td>
				    </tr>
					<tr>
				      <td><?php echo $entry_facebook; ?></td>
				      <td><select name="success_page[<?php echo $store['store_id']; ?>][success_page_facebook_status]">
				        <?php if (isset($module[$store['store_id']]['success_page_facebook_status']) && $module[$store['store_id']]['success_page_facebook_status'] == 1) { ?>
				        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
				        <option value="0"><?php echo $text_disabled; ?></option>
				        <?php } else { ?>
				        <option value="1"><?php echo $text_enabled; ?></option>
				        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
				        <?php } ?>
				        </select>
						<table class="form" style="display: inline; margin-left: 10px;">
						  <tr>
						    <td><?php echo $entry_facebook_profile; ?></td><td><input type="text" name="success_page[<?php echo $store['store_id']; ?>][success_page_facebook_profile]" value="<?php echo isset($module[$store['store_id']]['success_page_facebook_profile']) ? $module[$store['store_id']]['success_page_facebook_profile'] : ''; ?>" /></td>
						  </tr>
						  <tr>
						    <td><?php echo $entry_dimension; ?></td><td><input type="text" name="success_page[<?php echo $store['store_id']; ?>][success_page_facebook_width]" value="<?php echo isset($module[$store['store_id']]['success_page_facebook_width']) ? $module[$store['store_id']]['success_page_facebook_width'] : ''; ?>" size="5" /> x <input type="text" name="success_page[<?php echo $store['store_id']; ?>][success_page_facebook_height]" value="<?php echo isset($module[$store['store_id']]['success_page_facebook_height']) ? $module[$store['store_id']]['success_page_facebook_height'] : ''; ?>" size="5" /></td>
						  </tr>
						  <tr>
						    <td><?php echo $entry_facebook_border; ?></td><td><input type="text" name="success_page[<?php echo $store['store_id']; ?>][success_page_facebook_border]" value="<?php echo isset($module[$store['store_id']]['success_page_facebook_border']) ? $module[$store['store_id']]['success_page_facebook_border'] : ''; ?>" /></td>
						  </tr>
						</table>
					  </td>
				    </tr>
					<tr>
				  </table>
				</td>
                <td valign="top">
				  <table class="form">
				    <tr>
					  <td><?php echo $entry_status; ?></td>
					  <td><select name="success_page[<?php echo $store['store_id']; ?>][success_page_redirect_status]">
					    <?php if (isset($module[$store['store_id']]['success_page_redirect_status']) && $module[$store['store_id']]['success_page_redirect_status'] == 1) { ?>
					    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
					    <option value="0"><?php echo $text_disabled; ?></option>
					    <?php } else { ?>
					    <option value="1"><?php echo $text_enabled; ?></option>
					    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
					    <?php } ?>
					  </select></td>
					</tr>
					<tr>
					  <td><?php echo $entry_redirect; ?></td>
					  <td><input type="text" name="success_page[<?php echo $store['store_id']; ?>][success_page_redirect_url]" value="<?php echo isset($module[$store['store_id']]['success_page_redirect_url']) ? $module[$store['store_id']]['success_page_redirect_url'] : ''; ?>" size="40" /></td>
					</tr>
				  </table>
				</td>
              </tr>
			</tbody>
		  </table>
		  <div id="language-<?php echo $store['store_id']; ?>" class="htabs" style="position: relative;">
            <?php foreach ($languages as $language) { ?>
            <a href="#tab-language-<?php echo $store['store_id']; ?>-<?php echo $language['language_id']; ?>"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
            <?php } ?>
			<div style="position: absolute; top: 3px; right: 5px;">
			  <?php echo $entry_layout; ?> <select name="success_page[<?php echo $store['store_id']; ?>][success_page_layout_id]" onchange="loadTemplate(this, '<?php echo $store['store_id']; ?>');">
			  <option value="*"><?php echo $text_none; ?></option>
			  <?php foreach ($layouts as $key => $layout) { ?>
			  <?php if (isset($module[$store['store_id']]['success_page_layout_id']) && $key == $module[$store['store_id']]['success_page_layout_id']) { ?>
			  <option value="<?php echo $key; ?>" selected="selected"><?php echo $layout['name']; ?></option>
			  <?php } else { ?>
			  <option value="<?php echo $key; ?>"><?php echo $layout['name']; ?></option>
			  <?php } ?>
			  <?php } ?>
			  </select>
            </div>
		  </div><a id="shortcode">Shortcode</a>
          <?php foreach ($languages as $language) { ?>
          <div id="tab-language-<?php echo $store['store_id']; ?>-<?php echo $language['language_id']; ?>">
			<table class="form">
			  <tr><td><?php echo $entry_body; ?></td></tr>
			  <tr>
                <td><textarea name="success_page[<?php echo $store['store_id']; ?>][success_page_description][<?php echo $language['language_id']; ?>]" id="description-<?php echo $store['store_id']; ?>-<?php echo $language['language_id']; ?>"><?php echo isset($module[$store['store_id']]['success_page_description'][$language['language_id']]) ? $module[$store['store_id']]['success_page_description'][$language['language_id']] : ''; ?></textarea></td>
              </tr>
			  <tr><td><?php echo $entry_logged; ?></td></tr>
			  <tr>
                <td><textarea name="success_page[<?php echo $store['store_id']; ?>][success_page_is_logged][<?php echo $language['language_id']; ?>]" id="is_logged-<?php echo $store['store_id']; ?>-<?php echo $language['language_id']; ?>"><?php echo isset($module[$store['store_id']]['success_page_is_logged'][$language['language_id']]) ? $module[$store['store_id']]['success_page_is_logged'][$language['language_id']] : ''; ?></textarea></td>
              </tr>
            </table>
          </div>
          <?php } ?>
        </div>
        <?php } ?>
      </form>
    </div>
  </div>
  <div id="dialog" title="Shortcodes">
    <p><?php echo $text_shortcode; ?></p>
  </div>
</div>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script> 
<script type="text/javascript"><!--
<?php foreach ($stores as $store) { ?>
<?php foreach ($languages as $language) { ?>
CKEDITOR.replace('description-<?php echo $store['store_id']; ?>-<?php echo $language['language_id']; ?>', {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});

CKEDITOR.replace('is_logged-<?php echo $store['store_id']; ?>-<?php echo $language['language_id']; ?>', {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
<?php } ?>
<?php } ?>
//--></script>
<script type="text/javascript"><!--
function loadTemplate(element, store_id) {
	var language_id = $('#language-' + store_id + ' a').filter('.selected').attr('href').replace(/#tab-language-(\d+)-(\d+)/g, "$2");

	if (element.value != '*') {
		$('textarea[id=\'description-' + store_id + '-' + language_id + '\']').load('index.php?route=module/success_page/loadtemplate&id=' + element.value + '&token=<?php echo $token; ?>', function(){
			CKEDITOR.instances["description-" + store_id + "-" + language_id].setData($('textarea[id=\'description-' + store_id + '-' + language_id + '\']').text());
		});
	} else {
		CKEDITOR.instances["description-" + store_id + "-" + language_id].setData('');
	}
}

$("#dialog").dialog({autoOpen: false});

$("#shortcode").click(function() {
  $("#dialog").dialog("open");
});

$('#tabs a').tabs();
$('.vtabs a').tabs();
//--></script> 
<script type="text/javascript"><!--
<?php foreach ($stores as $store) { ?>
$('#language-<?php echo $store['store_id']; ?> a').tabs();
<?php } ?> 
//--></script> 
<?php echo $footer; ?>