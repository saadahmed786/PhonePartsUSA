<?php echo $header; ?>
 <div class="box">
  <div class="top">
    <h1><?php echo $heading_title; ?></h1>
  </div>
  <div class="middle">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="newsletter">
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
        <table>
          <tr>
            <td width="150"><?php echo $entry_newsletter; ?></td>
            <td><?php if ($newsletter) { ?>
              <input type="radio" name="newsletter" value="1" checked="checked" />
              <?php echo $text_yes; ?>
              <input type="radio" name="newsletter" value="0" />
              <?php echo $text_no; ?>
              <?php } else { ?>
              <input type="radio" name="newsletter" value="1" />
              <?php echo $text_yes; ?>
              <input type="radio" name="newsletter" value="0" checked="checked" />
              <?php echo $text_no; ?>
              <?php } ?></td>
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