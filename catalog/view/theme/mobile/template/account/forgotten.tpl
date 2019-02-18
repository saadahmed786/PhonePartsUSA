<?php echo $header; ?>
 <div class="box">
  <div class="top">
    <h1><?php echo $heading_title; ?></h1>
  </div>
  <div class="middle">
    <?php if ($error) { ?>
    <div class="warning"><?php echo $error; ?></div>
    <?php } ?>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="forgotten">
      <p><?php echo $text_email; ?></p>
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_your_email; ?></b>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
        <table>
          <tr>
            <td width="150"><?php echo $entry_email; ?></td>
            <td><input type="text" name="email" /></td>
          </tr>
        </table>
      </div>
      <div class="buttons">
        <table>
          <tr>
            <td align="left"><a href="<?php echo $back; ?>" class="button"><span><?php echo $button_back; ?></span></a></td>
            <td align="right"><input type="submit" value="<?php echo $button_continue; ?>" /></td>
          </tr>
        </table>
      </div>
    </form>
 </div>
</div>
<div class="bottom">&nbsp;</div>
<?php echo $column_left; ?><?php echo $column_right; ?>
<?php echo $footer; ?> 