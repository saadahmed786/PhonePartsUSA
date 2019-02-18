<?php echo $header; ?>
 <div class="box">
  <div class="top">
    <h1><?php echo $heading_title; ?></h1>
  </div>
  <div id="category" class="middle">
    <?php if ($success) { ?>
    <div class="success"><?php echo $success; ?></div>
    <?php } ?>
    <p><b><?php echo $text_my_account; ?></b></p>
    <ul>
      <li><a href="<?php echo $information; ?>"><?php echo $text_information; ?></a></li>
      <li><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
      <li><a href="<?php echo $address; ?>"><?php echo $text_address; ?></a></li>
    </ul>
    <p><b><?php echo $text_my_orders; ?></b></p>
    <ul>
      <li><a href="<?php echo $history; ?>"><?php echo $text_history; ?></a></li>
      <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
    </ul>
    <p><b><?php echo $text_my_newsletter; ?></b></p>
    <ul>
      <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
    </ul>
  </div>
</div>
<div class="bottom">&nbsp;</div>
<?php echo $column_left; ?><?php echo $column_right; ?>
<?php echo $footer; ?> 