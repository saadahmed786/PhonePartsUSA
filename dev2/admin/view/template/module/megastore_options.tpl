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
      <table class="form">
     		 <tr>
               <td>Background Color</td>
               <td>
               		<input type="text" name="megastore_options[bgColor]" value="<?php if(isset($megastore_options['bgColor'])){ echo $megastore_options['bgColor']; } ?>" class="colorPicker" />
                    <span style="background:#<?php if(isset($megastore_options['bgColor'])){ echo $megastore_options['bgColor']; } ?>;"></span>
               </td>
           		<td>Background Pattern/Image (Upload a pattern/Image) <br /> <small style="color:red">Note:</small> <small>If you are going to use a pattern/image, the "Background Color" field must be blank.</small></td>
           		<td>
                		<div class="image">
                        <?php if($megastore_options['bgImage']): ?>
                        	<img src="<?php echo HTTP_IMAGE . $megastore_options['bgImage']; ?>" alt="" id="thumb" width="100" height="100" />
                        <?php endif; ?>
                  		<input type="hidden" name="megastore_options[bgImage]" value="<?php echo $megastore_options['bgImage']; ?>" id="image"  />
                      	<br />
                      	<a onclick="image_upload('image', 'thumb');"><?php echo $text_browse; ?></a>
                     	<a onclick="$('#image').attr('value','');$('#thumb').remove();"><?php echo $button_remove; ?></a>
               </td>             
           </tr>
      </table>
      <table class="form">      		
      		
      		<tr>
                <td>Primary Colour (Default Red):</td>
                        <td>
                           <input type="text" name="megastore_options[primary]" value="<?php if(isset($megastore_options['primary'])){ echo $megastore_options['primary']; } ?>" class="colorPicker" />
                           <span style="background:#<?php if(isset($megastore_options['primary'])){ echo $megastore_options['primary']; } ?>;"></span>
                    </td>
                    <td>Search and Top Menu "Background Colour": </td>
                        <td>
                            <input type="text" name="megastore_options[secondary]" value="<?php if(isset($megastore_options['secondary'])){ echo $megastore_options['secondary'];} ?>" class="colorPicker" /> 
                            <span style="background:#<?php echo $megastore_options['secondary'] ?>;"></span>
                        </td>
        	</tr>
            <tr>
                <td>Top Menu "Link" Colour: </td>
                    <td>
                       <input type="text" name="megastore_options[slideTop]" value="<?php if(isset($megastore_options['slideTop'])){ echo $megastore_options['slideTop']; } ?>" class="colorPicker" />
                       <span style="background:#<?php echo $megastore_options['slideTop']; ?>;"></span>
                </td>
                <td>Top Menu "Active Link" Colour: </td>
                    <td>
                       <input type="text" name="megastore_options[active]" value="<?php if(isset($megastore_options['active'])){ echo $megastore_options['active']; } ?>" class="colorPicker" />
                       <span style="background:#<?php echo $megastore_options['active']; ?>;"></span>
                </td>
                <td>Search & Category "Border" Colour:</td>
                    <td>
                       <input type="text" name="megastore_options[slideBottom]" value="<?php if(isset($megastore_options['slideBottom'])){ echo $megastore_options['slideBottom']; } ?>" class="colorPicker" />
                       <span style="background:#<?php echo $megastore_options['slideBottom']; ?>;"></span>
                </td>
            </tr>
           
            <tr>
            	<td>Slideshow Speed, e.g 5000. <br />(5000 = 5 Seconds)</td>
                <td><input type="text" name="megastore_options[slideSpeed]" value="<?php if(isset($megastore_options['slideSpeed'])){ echo $megastore_options['slideSpeed']; } ?>" /></td>
                <td>Slideshow Animation</td>
                <td>
                    <select name="megastore_options[slideAnim]">
                        <option value="slide" <?php if(isset($megastore_options['slideAnim']) && $megastore_options['slideAnim']=='slide'){ echo 'selected="selected"'; }?>>Slide</option>
                        <option value="fade" <?php if(isset($megastore_options['slideAnim']) && $megastore_options['slideAnim']=='fade'){ echo 'selected="selected"'; }?>>Fade</option>
                    </select>                
               </td>
            </tr>
           <tr>
            	<td>Show/Hide "Add to cart" button in module blocks:</td>
                <td>
                    <select name="megastore_options[atc]">
                    	<option></option>
                        <option value="Show" <?php if(isset($megastore_options['atc']) && $megastore_options['atc']=='Show'){ echo 'selected="selected"'; }?>>Show</option>
                        <option value="Hide" <?php if(isset($megastore_options['atc']) && $megastore_options['atc']=='Hide'){ echo 'selected="selected"'; }?>>Hide</option>
                    </select>                
               </td>
           </tr>
      </table>
      <table class="form">
      	<td>Header Image (Top Right):</td>
           		<td>
                		<div class="image1">
                        <?php if($megastore_options['headerImage']): ?>
                        	<img src="<?php echo HTTP_IMAGE . $megastore_options['headerImage']; ?>" alt="" id="thumb1" width="100" height="100" />
                        <?php endif; ?>
                  		<input type="hidden" name="megastore_options[headerImage]" value="<?php echo $megastore_options['headerImage']; ?>" id="image1"  />
                      	<br />
                      	<a onclick="image_upload('image1', 'thumb1');"><?php echo $text_browse; ?></a>
                        <a onclick="$('#image1').attr('value','');$('#thumb1').remove();"><?php echo $button_remove; ?></a>
                        </div><br />
                        Image Link
                        <input type="text" name="megastore_options[topImgHref]" value="<?php if($megastore_options['topImgHref']){ echo $megastore_options['topImgHref']; } else { echo "http://"; } ?>" />
               </td>
      </table>
      <table class="form">
      	<td>Footer Image 1</td>
        <td><div class="image2">
                        <?php if($megastore_options['footerImage']): ?>
                        	<img src="<?php echo HTTP_IMAGE . $megastore_options['footerImage']; ?>" alt="" id="thumb2" width="100" height="100" />
                        <?php endif; ?>
                  		<input type="hidden" name="megastore_options[footerImage]" value="<?php echo $megastore_options['footerImage']; ?>" id="image2"  />
                      	<br />
                      	<a onclick="image_upload('image2', 'thumb2');"><?php echo $text_browse; ?></a>
                        <a onclick="$('#image2').attr('value','');$('#thumb2').remove();"><?php echo $button_remove; ?></a>
                        </div><br />
                        Image Link
                        <input type="text" name="megastore_options[footerImgHref]" value="<?php if($megastore_options['footerImgHref']){ echo $megastore_options['footerImgHref']; } else { echo "http://"; } ?>" /></td>
        <td>Footer Image 2</td>
        <td><div class="image3">
                        <?php if($megastore_options['footerImage1']): ?>
                        	<img src="<?php echo HTTP_IMAGE . $megastore_options['footerImage1']; ?>" alt="" id="thumb3" width="100" height="100" />
                        <?php endif; ?>
                  		<input type="hidden" name="megastore_options[footerImage1]" value="<?php echo $megastore_options['footerImage1']; ?>" id="image3"  />
                      	<br />
                      	<a onclick="image_upload('image3', 'thumb3');"><?php echo $text_browse; ?></a>
                        <a onclick="$('#image3').attr('value','');$('#thumb3').remove();"><?php echo $button_remove; ?></a>
                        </div><br />
                        Image Link
                        <input type="text" name="megastore_options[footerImgHref1]" value="<?php if($megastore_options['footerImgHref1']){ echo $megastore_options['footerImgHref1']; } else { echo "http://"; } ?>" /></td>
        <td>Footer Image 3</td>
        <td><div class="image4">
                        <?php if($megastore_options['footerImage']): ?>
                        	<img src="<?php echo HTTP_IMAGE . $megastore_options['footerImage2']; ?>" alt="" id="thumb4" width="100" height="100" />
                        <?php endif; ?>
                  		<input type="hidden" name="megastore_options[footerImage2]" value="<?php echo $megastore_options['footerImage2']; ?>" id="image4"  />
                      	<br />
                      	<a onclick="image_upload('image4', 'thumb4');"><?php echo $text_browse; ?></a>
                        <a onclick="$('#image4').attr('value','');$('#thumb4').remove();"><?php echo $button_remove; ?></a>
                        </div><br />
                        Image Link
                        <input type="text" name="megastore_options[footerImgHref2]" value="<?php if($megastore_options['footerImgHref2']){ echo $megastore_options['footerImgHref2']; } else { echo "http://"; } ?>" /></td>
      </table>
    </div>
  
  </div>
  
  
</div>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script> 
<script type="text/javascript"><!--
$('input.colorPicker').ColorPicker({
	onSubmit: function(hsb, hex, rgb, el) {
		$(el).val(hex);
		$(el).ColorPickerHide();
		$(el).parent().find('span').css('background','#'+hex);	
	},
	onBeforeShow: function () {
		$(this).ColorPickerSetColor(this.value);
	}
})
.bind('keyup', function(){
	$(this).ColorPickerSetColor(this.value);	
});
--></script>
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