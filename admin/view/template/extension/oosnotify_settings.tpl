<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/setting.png" alt="" /> <?php echo $heading_title_setting; ?></h1>
	  <div class="buttons"><form action="" method="post"><input type="submit" name="save" value="<?php echo $button_save; ?>" class="button" ><a href="<?php echo $href;?>" class="button">Reports</a></div>
    </div>
    <div class="content">
     <div class="content">
    <div class="vtabs">
    <a href="#tab-installation"><?php echo $installation; ?></a>
        <a href="#tab-store-email"><?php echo $email_to_store; ?></a>
        <?php foreach ($languages as $language) { ?>
        	<a href="#tab-store-customer-<?php echo $language['language_id'];?>"><?php echo $email_to_customer.' ('.$language['name'].') '; ?></a>
        
        <?php } ?>
    </div>
    <div id="tab-installation" class="vtabs-content">
    <?php
     if ($counturl == 0){
        	echo '<form action="" method="POST">Primary Store URL <span class="help">( Example: http://www.storename.com/ )</span> <input type="text" name="url"><input type="submit" name="inserturl" value="SAVE"></form><br><br>';
        }
    	 echo $installed; 
        ?>
        
    </div>
    <div id="tab-store-email" class="vtabs-content">
    	<table class="form">
              <tr>
              	<td><?php echo $entry_store_subject; ?></td>
                <td><input type="text" name="store_subject" size="100" value="<?php echo $store_subject; ?>" ></td>
              </tr>
              <tr>
              	<td><?php echo $entry_store_body; ?></td>
                <td><textarea name="store_body" cols="80" rows="8" ><?php echo $store_body; ?></textarea></td>
              </tr>
      </table>
    </div>
    
    <?php foreach ($languages as $language) { ?>
     <div id="tab-store-customer-<?php echo $language['language_id'];?>" class="vtabs-content">
     	<table class="form">
              <tr>
              	<td><?php echo $entry_customer_subject; ?></td>
                <td><input type="text" name="customer_subject_<?php echo $language['language_id'];?>" size="100" value="<?php echo $this->config->get('oosn_customer_mail_sub'.$language['language_id']);?>"></td>
              </tr>
              <tr>
              	<td><?php echo $entry_customer_body; ?></td>
                <td><textarea name="customer_body_<?php echo $language['language_id'];?>" id="description<?php echo $language['language_id'];?>" cols="80" rows="8" ><?php echo $this->config->get('oosn_customer_mail_body'.$language['language_id']); ?></textarea></td>
              </tr>

      </table>
     	
     </div>
    <?php } ?>
    </div></form>
  </div>
</div>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript"><!--
CKEDITOR.replace('description1', {
 filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
//--></script>

<script type="text/javascript"><!--
CKEDITOR.replace('description2', {
 filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
//--></script>

<script type="text/javascript"><!--
CKEDITOR.replace('description3', {
 filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
//--></script>

<script type="text/javascript"><!--
CKEDITOR.replace('description4', {
 filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
//--></script>
<script type="text/javascript"><!--
CKEDITOR.replace('description5', {
 filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
//--></script>
<script type="text/javascript"><!--
CKEDITOR.replace('description6', {
 filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
//--></script>
<script type="text/javascript"><!--
CKEDITOR.replace('description7', {
 filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
 filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
//--></script>
 <script type="text/javascript"><!--
$('.vtabs a').tabs();
//--></script>
<?php echo $footer; ?>