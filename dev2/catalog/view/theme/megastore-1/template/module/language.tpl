<?php if (count($languages) > 1) { ?>
<a href="javascript:void(0);"><?php echo $text_language; ?></a>
 <ul id="language">
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
 
    <?php foreach ($languages as $language) { ?>
    <li><a href="javascript:void(0);"  onclick="$('input[name=\'language_code\']').attr('value', '<?php echo $language['code']; ?>'); $(this).parent().parent().submit();"><img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
    <?php } ?>
    <input type="hidden" name="language_code" value="" />
    <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
  
</form>
</ul>
<?php } ?>

