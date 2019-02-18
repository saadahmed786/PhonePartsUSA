<?php echo $header; ?>
 <div class="box">
  <div class="top">
    <h1><?php echo $heading_title; ?></h1>
  </div>
  <div class="middle">
    <?php if ($success) { ?>
    <div class="success"><?php echo $success; ?></div>
    <?php } ?>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <b style="margin-bottom: 2px; display: block;"><?php echo $text_address_book; ?></b>
    <?php foreach ($addresses as $result) { ?>
    <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
      <table>
        <tr>
          <td width="70%"><?php echo $result['address']; ?></td>
          <td><a href="<?php echo $result['update']; ?>" class="button"><span><?php echo $button_edit; ?></span></a>
          <a href="<?php echo $result['delete']; ?>" class="button"><span><?php echo $button_delete; ?></span></a></td>
        </tr>
      </table>
    </div>
    <?php } ?>
    <div class="buttons">
      <table>
        <tr>
          <td align="left"><a href="<?php echo $back; ?>" class="button"><span><?php echo $button_back; ?></span></a></td>
          <td align="right"><a href="<?php echo $insert; ?>" class="button"><span><?php echo $button_new_address; ?></span></a></td>
        </tr>
      </table>
  </div>
 </div>
</div>
<div class="bottom">&nbsp;</div>
<?php echo $column_left; ?><?php echo $column_right; ?>
<?php echo $footer; ?> 