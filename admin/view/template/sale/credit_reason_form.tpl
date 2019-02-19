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
      <h1><img src="view/image/order.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
		  <tr>
		    <td colspan="2">
			  
              
              
                <table class="form">
                  <tr>
                    <td><span class="required">*</span> <?php echo $entry_name; ?></td>
                    <td><input type="text" name="name" size="40" value="<?php echo $name;?>" />
					<?php if (isset($error_name)) { ?>
                    <span class="error"><?php echo $error_name; ?></span>
                    <?php } ?></td>
                  </tr>
                  <tr>
                    <td><span class="required">*</span> <?php echo $entry_code; ?></td>
                    <td><input type="text" name="code" size="3" value="<?php echo $code;?>" />
					<?php if (isset($error_code)) { ?>
                    <span class="error"><?php echo $error_code; ?></span>
                    <?php } ?></td>
                  </tr>
                  
                  
                  
                  <tr>
                    <td><span class="required">*</span> <?php echo $entry_message; ?></td>
                    <td><textarea cols="170" rows="14" name="message" id="message"><?php echo $message;?></textarea>
                    <?php if (isset($error_message)) { ?>
                    <span class="error"><?php echo $error_message; ?></span>
                    <?php } ?>
                    </td>
                  </tr>
                  
                  <tr>
              <td><?php echo $entry_status; ?></td>
              <td><select name="status">
                  <?php if ($status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select></td>
            </tr>
            
            
            
                </table>
              
		    
			<table style="width: 100%;"><tr><td style="text-align: right;"><?php echo $entry_template_shortcode; ?></td></tr></table>
		    </td>
		  </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script> 
<script type="text/javascript"><!--
CKEDITOR.on('instanceReady', function( ev ) {
	var blockTags = ['div','h1','h2','h3','h4','h5','h6','p','pre','li','blockquote','ul','ol',
  'table','thead','tbody','tfoot','td','th'];

	for (var i = 0; i < blockTags.length; i++) {
		ev.editor.dataProcessor.writer.setRules( blockTags[i], {
			indent : false,
			breakBeforeOpen : true,
			breakAfterOpen : false,
			breakBeforeClose : false,
			breakAfterClose : true
		});
	}
});

CKEDITOR.config.autoParagraph = false;
CKEDITOR.config.htmlEncodeOutput = false;


CKEDITOR.replace('message', {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});

//--></script> 
<script type="text/javascript"><!--
$('#languages a').tabs();
//--></script> 
<?php echo $footer; ?>